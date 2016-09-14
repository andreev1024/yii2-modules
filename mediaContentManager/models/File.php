<?php

namespace andreev1024\mediaContentManager\models;

use andreev1024\mediaContentManager\behaviors\ArrayFieldBehavior;
use andreev1024\mediaContentManager\components\BaseStorage;
use andreev1024\mediaContentManager\helpers\FileInfo;
use andreev1024\mediaContentManager\helpers\FileInfoInterface;
use andreev1024\mediaContentManager\helpers\UploadedFileInfo;
use andreev1024\mediaContentManager\models\scopes\FileQuery;
use andreev1024\mediaContentManager\Module;
use andreev1024\mediaContentManager\processors\Builder;
use andreev1024\mediaContentManager\processors\ImageProcessor;
use andreev1024\mediaContentManager\processors\Processor;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Class File
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 */
class File extends ActiveRecord
{
    const PROCESSING_TYPE_PLAIN = 0;
    const PROCESSING_TYPE_IMAGE = 1;

    const SCENARIO_UPDATE = 'update';
    const SCENARIO_STRING = 'string';

    /** @var string|UploadedFile */
    public $file;

    /**
     * @var bool Do some process async.
     *           For example, you can create thumbnails async (in background).
     */
    public $async = false;

    /** @var FileInfoInterface */
    private $fileInfo;

    /** @var Processor */
    private $processor;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @since Ver 1.0 added on 2015.04.10
     * @access public
     *
     * @return array
     */
    public static function getProcessingTypes()
    {
        return [
            self::PROCESSING_TYPE_PLAIN,
            self::PROCESSING_TYPE_IMAGE,
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            'blameBehavior' => [
                'class' => BlameableBehavior::className(),
            ],
            'arrayFieldBehavior' => [
                'class' => ArrayFieldBehavior::className(),
                'attributes' => ['metadata'],
                'emptyEncodedValue' => null,
                'emptyDecodedValue' => [],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            [['path'], 'filter', 'filter' => function ($value) {
                return trim($value , '/');
            }],
            [['path'], 'default', 'value' => 'contents/media/files'],
            [['processing_type'], 'in', 'range' => self::getProcessingTypes()],
            [['title'], 'string', 'max' => 50],
            [['name'], 'validateFileExist', 'skipOnEmpty' => false],
            [['description', 'name'], 'string', 'max' => 255],
            [['title', 'description'], 'default', 'value' => null],
            [['metadata'], 'default', 'value' => []],
            ['storage', 'in', 'range' => array_keys(Module::getStorageArray())],
            ['storage_access', 'in', 'range' => array_values(BaseStorage::getStorageAccessArray())],
            ['storage_access', 'default', 'value' => BaseStorage::ACCESS_PUBLIC],
            ['storage_directory', 'string'],
            ['content', 'string'],
            ['extension', 'string', 'max' => 256],
        ];
    }

    /**
     * Validator check: do file with `name` and `path` already exist ?
     * @author Andreev <andreev1024@gmail.com>
     * @param $attribute
     * @param $params
     */
    public function validateFileExist($attribute, $params)
    {
        $fileExist = static::find()->where(['name' => $this->name, 'path' => $this->path])->one();
        if ($fileExist) {
            $this->addError(
                $attribute,
                Yii::$app->translate->t('A file with the same name already exist.', 'app')
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return array_merge(
            parent::scenarios(),
            [
                self::SCENARIO_UPDATE => ['title', 'description'],
                self::SCENARIO_STRING => [
                                            'path',
                                            'processing_type',
                                            'title',
                                            'name',
                                            'description',
                                            'metadata',
                                            'storage',
                                            'storage_access',
                                            'storage_directory',
                                            'content',
                                            'extension',
                                        ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->loadProcessor();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!empty($this->file)) {
            unset($this->content);
            $this->prepareFile();
        } 

        if (!empty($this->content)){
            $temp = tmpfile();
            fwrite($temp, $this->content);
            $metaDatas = stream_get_meta_data($temp);
            $this->file = $metaDatas['uri'];
            
            $this->prepareFile();
            
            fclose($temp);
        }

        return parent::beforeSave($insert);
    }

    /**
     * @author Doszhan Kalibek <doszhan777@gmail.com>
     * @version Ver 1.0 added on 2015.10.28
     * @access private
     */
    private function prepareFile()
    {
        $this->fillSystemData();
        $this->loadProcessor();
        $this->fillStorageData();
        $this->getProcessor()->upload($this->getFileInfo()->getFilename());
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
             /*  it comments because we wait our background job service solution
            if ($this->processor instanceof ImageProcessor && $this->async) {
                Yii::$app->gearmanHandler->addBackground('mediaContentManager.createThumbnails', [
                    'fileName' => $this->getFileNameById($this->id),
                    'fileUrl' => $this->getUrl(),
                ]);
            }
            */
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        $this->getProcessor()->delete();

        parent::afterDelete();
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access public
     *
     * @return Processor|ImageProcessor
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access public
     *
     * @return string
     */
    public function getUrl()
    {
        return call_user_func_array([$this->getProcessor(), 'getUrl'], func_get_args());
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access protected
     */
    protected function fillSystemData()
    {
        if ($this->name) {
            $this->original_name = $this->name;
        } else {
            $this->name = uniqid() . '.' . (!empty($this->content) ? $this->extension : $this->getFileInfo()->getExtension());
            $this->original_name = $this->getFileInfo()->getName();
        }

        $this->size = $this->getFileInfo()->getSize();
        $this->mime_type = $this->getFileInfo()->getType();
        $this->processing_type = $this->getProcessingTypeByMime($this->getFileInfo()->getType());
    }

    /**
     * Fills storage data.
     *
     * @author Andreev <andreev1024@gmail.com>
     */
    protected function fillStorageData()
    {
        $storage = $this->processor->getStorage();
        $this->storage_access = $storage->access;
        $this->storage_directory = $storage->storageDirectory;
        $this->storage = $storage->id;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access protected
     *
     * @param string $mimeType
     *
     * @return int
     */
    protected function getProcessingTypeByMime($mimeType)
    {
        if (static::isImageByMime($mimeType)) {
            return self::PROCESSING_TYPE_IMAGE;
        }

        return self::PROCESSING_TYPE_PLAIN;
    }

    /**
     * Checks, does the $mimeType of the image.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $mimeType
     *
     * @return int
     */
    public static function isImageByMime($mimeType)
    {
        return preg_match('/image\/(jpe?g|png)/i', $mimeType);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access protected
     *
     * @return FileInfoInterface
     */
    protected function getFileInfo()
    {
        if (empty($this->fileInfo)) {
            if ($this->file instanceof UploadedFile) {
                $this->fileInfo = new UploadedFileInfo($this->file);
            } else {
                $this->fileInfo = new FileInfo($this->file);
            }
        }

        return $this->fileInfo;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access private
     */
    private function loadProcessor()
    {
        $filename = $this->getFileName();
        $this->processor = Builder::build(
            $filename,
            $this->async,
            [
                'storage' => $this->storage,
                'storageDirectory' => $this->storage_directory,
                'useDefault' => $this->isNewRecord
            ],
            $this->processing_type
        );
    }

    /**
     * Return file name in Storage.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public function getFileName()
    {
        return $this->path . '/' . $this->name;
    }

    /**
     * Return file name in storage by Id.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $id
     * @param null $default
     *
     * @return string
     */
    public static function getFileNameById($id, $default = null)
    {
        if ($model = static::findOne($id)) {
            return $model->getFileName();
        }
    }

    /**
     * Init scope.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return FileQuery
     */
    public static function find()
    {
        return new FileQuery(get_called_class());
    }
}
