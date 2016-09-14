<?php

namespace andreev1024\mediaContentManager\widgets;

use andreev1024\mediaContentManager\models\File;
use andreev1024\mediaContentManager\Module;
use andreev1024\mediaContentManager\processors\ImageProcessor;
use andreev1024\mediaContentManager\widgets\assets\FileManagerAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\Widget;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This widget provide GUI for interaction with file system (AWS S3, local DB etc.)
 *
 * Note: if we don't have any files and directories in storage, then widget doesn't displays.
 *
 * Display
 *
 *      Modal
 *
 *          Widget content can be displayed in modal. For this you must set $displayInModal = true;
 *          If you want use external view block you must set $modalBlock = 'modelBlockName'.
 *
 *      Display mode
 *
 *          Files can be displayed like icons or simple list. You can configure it by $displayMode.
 *
 * Mode
 *
 *      We can display all files or only images. You can configure it by $mode.
 *
 * Action
 *
 *      When user clicked on file icon there triggered event. You can configure it by $onClickAction.
 *
 * E.g.
 *
 * <?= FileManager::widget([
 *      'displayMode' => FileManager::DISPLAY_MODE_LIST,
 *      'startDirectory' => 'contents/media/images',
 *      'modalId' => 'modal',
 *      'modalBlock' => 'modalFrame',
 *      'storage' => S3Wrapper::ID,
 *      'storageDirectory' => 'bucketABC',
 *      'onClickAction' => FileManager::ACTION_SHOW_POPOVER,
 *      'fileUploader' => true,
 *      'fileUploaderOptions' => [
 *          'formAction' => Url::to(['/site/default/index'])
 *      ]
 * ]) ?>
 *
 */
class FileManager extends Widget
{
    const DISPLAY_MODE_ICONS = 'icons';
    const DISPLAY_MODE_LIST = 'list';

    const ACTION_SHOW_POPOVER = 'showPopover';
    const ACTION_DEFAULT = 'default';

    /**
     * Widet modes.
     * @var string File::PROCESSING_TYPE_IMAGE|PROCESSING_TYPE_PLAIN etc.
     */
    public $mode = File::PROCESSING_TYPE_PLAIN;

    /**
     * @var string See description abowe.
     */
    public $displayMode = self::DISPLAY_MODE_LIST;

    /**
     * @var string See description abowe.
     */
    public $displayInModal = false;

    /**
     * @var string Storage ID.
     */
    public $storage;

    /**
     * Directory from which the widget will be displays files.
     * For S3 it's bucket, for local - directory.
     * @var string
     */
    public $storageDirectory;

    /**
     * The widget shows files from this directory.
     * @var string
     */
    public $startDirectory;


    /**
     * If widget uses slave storage $startDirectory
     * will be replaced $slaveStartDirectory
     * @var string
     */
    public $slaveStartDirectory;

    /**
     * If we try to get storage and our config not valid
     * and $enableSlave is true then widget try to get
     * the slave storage.
     * @var bool
     */
    public $enableSlave = true;

    /**
     * Utilize for transfer data between requests (AJAX).
     * Note: don't set this argument manually.
     * @var string
     */
    public $encryptedOptions;

    /**
     * Modal Id.
     * If $useOtherModal true then $modalId must be equal `other` modal Id
     * @var string
     */
    public $modalId = 'fm-modal'; //  if use Other Modal -> this id must be equal other model id

    /**
     * @var string Action, which will be executed when user clicks on `file`.
     */
    public $onClickAction = self::ACTION_DEFAULT;

    /**
     * Modal block Id. If $modalBlock false - Modal render without block.
     * @var string
     */
    public $modalBlock = 'modalFrame';

    /**
     * @var string Widget header.
     */
    public $header = 'file manager';

    /**
     * @var int The number of items per page.
     */
    public $pageSize = 10;

    /**
     * @var string File Manager Controller
     */
    public $controller = '/mediaContent/file-manager/index';

    /**
     * @var bool enable/disable FileUploader Widget
     */
    public $fileUploader = false;
    /**
     * @var array Options for FileUploader Widget
     */
    public $fileUploaderOptions = [];

    /**
     * @var string Data attribute for files
     */
    private $dataPrfx = 'info';

    /**
     * @var Secure key for data encrypting.
     */
    private $key;

    /**
     * @var \andreev1024\mediaContentManager\components\BaseStorage;
     */
    private $storageInst;


    /**
     * Widget initialization.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $module = Module::getInstance();
        $this->key = $module->key;

        foreach (['storage', 'storageDirectory'] as $attribute) {
            if (is_null($this->$attribute)) {
                $this->$attribute = $module->{'master' . ucfirst($attribute)};
            }
        }

        if ($this->encryptedOptions) {
            $options = Yii::$app->security->decryptByKey(
                hex2bin($this->encryptedOptions),
                $this->key
            );

            foreach (Json::decode($options) as $key => $value) {
                $this->$key = $value;
            }
        }

        if (!in_array($this->mode, File::getProcessingTypes())) {
            throw new InvalidConfigException('Wrong value for `mode`');
        }

        if (!in_array($this->displayMode, static::getDisplayModeArray())) {
            throw new InvalidConfigException('Invalid `displayMode` attribute.');
        }

        if ($this->header) {
            $this->header = Yii::$app->translate->t($this->header);
        }

        $this->storageInst = $module->getStorage(
            $this->storage,
            $this->storageDirectory,
            $this->enableSlave
        );

        $storageIsSlave = $this->storage !== $this->storageInst->id &&
            $this->storageDirectory !== $this->storageInst->storageDirectory;

        if ($storageIsSlave && $this->enableSlave) {
            $this->storage = $this->storageInst->id;
            $this->storageDirectory = $this->storageInst->storageDirectory;
            $this->startDirectory = $this->slaveStartDirectory;
        }

        if (is_null($this->storage) || !$this->storageDirectory) {
            throw new InvalidConfigException('`storageDirectory` or `storage` is empty.');
        }

        if (!$this->encryptedOptions) {
            $options = [
                'mode' => $this->mode,
                'storage' => $this->storage,
                'storageDirectory' => $this->storageDirectory,
                'enableSlave' => false,
                'fileUploaderOptions' => $this->fileUploaderOptions
            ];
            
            $this->encryptedOptions = bin2hex(
                Yii::$app->security->encryptByKey(
                    Json::encode($options),
                    $this->key
                )
            );
        }
    }

    /**
     * Widget run.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return mixed
     */
    public function run()
    {
        $content = $this->getContent();
        
        if (!$content) {
            return null;
        }

        $this->registerClientScript();
        return $this->render($this->displayInModal ? 'modal' : 'default', [
            'mode' => $this->mode,
            'encryptedOptions' => $this->encryptedOptions,
            'displayMode' => $this->displayMode,
            'startDirectory' => $this->startDirectory,
            'header' => $this->header,
            'modalBlock' => $this->modalBlock,
            'modalId' => $this->modalId,
            'content' => $content,
            'selectors' => $this->getSelectors(),
        ]);
    }

    /**
     * Method returns rendered files and directory data.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return null|string
     */
    public function getContent()
    {
        $query = File::find()
            ->select('path')
            ->distinct()
            ->where([
                'storage_directory' => $this->storageDirectory,
                'storage' => $this->storage
            ])
            ->asArray();

        if ($this->mode === File::PROCESSING_TYPE_IMAGE) {
            $query->image();
        }

        $allDirectories = array_filter(
            ArrayHelper::getColumn(
                $query->all(),
                'path'
            )
        );

        $directoriesTree = [];
        $activeDirFound = false;
        foreach ($allDirectories as $directoryPath) {
            $directoryPathArr = explode('/', $directoryPath);
            $pathReference = &$directoriesTree;
            $onePathArr = [];

            foreach ($directoryPathArr as $key => $onePath) {

                $onePathArr[] = $onePath;

                if (!isset($pathReference[$onePath])) {
                    $path = implode('/', $onePathArr);
                    $pathReference[$onePath] = [
                        'path' => $path,
                        'nasted' => [],
                        'active' => $path === $this->startDirectory ? true : false,
                    ];

                    if ($pathReference[$onePath]['active']) {
                        $activeDirFound = true;
                    }
                }

                $pathReference = &$pathReference[$onePath]['nasted'];
            }
        }
        unset($pathReference);

        //  add root
        $directoriesTree = [
            'root' => [
                'path' => '',
                'nasted' => $directoriesTree,
                'active' => (!$this->startDirectory || !$activeDirFound) ? true : false,
            ]
        ];

        //  get files for current directory
        $query = File::find()->where([
            'path' => (!$this->startDirectory || !$activeDirFound) ? '' : $this->startDirectory,
            'storage_directory' => $this->storageDirectory
        ]);

        if($this->mode === File::PROCESSING_TYPE_IMAGE) {
            $query->image();
        }

        $get = Yii::$app->request->get();
        if (isset($get['options'])) {
            unset($get['options']);
        }

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => $this->pageSize,
            'params' => array_merge(
                $get,
                [
                    'displayMode' => $this->displayMode,
                    'startDirectory' => $this->startDirectory,
                ]
            ),
            'route' => $this->controller,
        ]);

        $files = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $sizeTypes = ImageProcessor::getSizeTypes();

        foreach ($files as &$oneFile) {

            if (File::isImageByMime($oneFile->mime_type)) {
                $urls = [];
                foreach ($sizeTypes as $oneSize) {
                    $urls[$oneSize] = $oneFile->getUrl($oneSize);
                }
                $oneFile = ArrayHelper::toArray($oneFile);
                $oneFile['url'] = $urls;
                $oneFile['isImage'] = true;
            } else {
                $url = $oneFile->getUrl();
                $oneFile = ArrayHelper::toArray($oneFile);
                $oneFile['url'] = $url;
            }
        }
        unset($oneFile);

        if (!$files && !$directoriesTree) {
            return null;
        }

        return $this->render('_content', [
            'startDirectory' => $this->startDirectory,
            'mode' => $this->mode,
            'displayMode' => $this->displayMode,
            'pagination' => $pagination,
            'pageSize' => $this->pageSize,
            'files' => $files,
            'directoriesTree' => $directoriesTree,
            'selectors' => $this->getSelectors(),
            'dataPrfx' => $this->dataPrfx,
            'fileUploaderOptions' => $this->fileUploaderOptions,
            'fileUploader' => $this->fileUploader,
        ]);
    }

    /**
     * Register client scripts.
     *
     * @author Andreev <andreev1024@gmail.com>
     */
    private function registerClientScript()
    {
        $view = $this->getView();
        FileManagerAsset::register($view);
        $selectors = Json::encode($this->getSelectors());
        $messages = Json::encode([
            'copyUrl' => Yii::$app->translate->t('copy Url'),
            'copyUrlAction' => Yii::$app->translate->t('Copy to clipboard (Ctrl+C or Cmd-C) and press Enter'),
        ]);

        $script = "
            var options = {
                selectors : {$selectors},
                dataPrfx : '{$this->dataPrfx}',
                displayInModal : '{$this->displayInModal}',
                onclickAction : '{$this->onClickAction}',
                messages: {$messages},
            };

            $.fileManager.method.init(options);
        ";

        $view->registerJs($script);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public function getDataPrfx()
    {
        return $this->dataPrfx;
    }

    /**
     * Returns array with display modes.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public static function getDisplayModeArray()
    {
        return [
            self::DISPLAY_MODE_ICONS => 'icons',
            self::DISPLAY_MODE_LIST => 'list',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getViewPath()
    {
        return parent::getViewPath() . '/fileManager';
    }

    /**
     * Return view selectors.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    private function getSelectors()
    {
        return [
            'id' => [
                'modal' => $this->modalId,
                'modalTrigger' => 'fm-modal-trigger',
                'container' => 'fm-container',
            ],
            'class' => [
                'directory' => 'fm-dir',
                'filesContainer' => 'fm-files-container',
                'fileContainer' => 'fm-file-container',
                'fileImg' => 'fm-file-img',
                'fileName' => 'fm-file-name',
                'fileInfo' => 'fm-file-info',
                'icons' => 'fm-icons',
                'list' => 'fm-list',
                'btnActive' => 'btn-success',
                'btnDefault' => 'btn-default',
                'ajax' => 'ajax',
                'copyUrl' => 'copy-url',
            ],
        ];
    }
}
