# Media Content Manager

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download).

Run migrations
`php yii migrate --migrationPath=@andreev1024/mediaContentManager/migrations`

## Configuration

Before module configuration you must configurate your S3 (CloudFront) storages.

Add module to `config/main.php` and configure `storages`
```
    ...
    'bootstrap' => [
        // ...
        'mediaContentManager',
        // ...
    ],
    'modules' => [
        // ...
        'mediaContent' => [
            'class' => \andreev1024\mediaContentManager\Module::className(),
            'key' => '123456789',   //key for FileManager encode/decode operation 
            'masterStorage' => \andreev1024\mediaContentManager\components\S3Wrapper::ID,
            'masterConfig' => [
                'credentials' => [
                    'key' => '123456789',
                    'secret' => '123456789',
                ],
                'access' => \andreev1024\mediaContentManager\components\BaseStorage::ACCESS_PUBLIC,
                'region' => 'ap-northeast-1',
                'version' => '2006-03-01',
                'cloudFront' => [
                    'privateKey' => __DIR__ . '/private-key.pem',
                    'keyPairId' => 'QWERTY',
                    'region' => 'ap-northeast-1',
                    'version' => '2015-04-17',
                    'domainName' => 'http://qwerty.cloudfront.net',
                    'policy' =>
                        '{
                       "Statement": [
                          {
                             "Resource":"http://qwerty.cloudfront.net/contents/*",
                             "Condition":{
                                "DateLessThan":{"AWS:EpochTime":' . strtotime("+1 year") . '}
                             }
                          }
                       ]
                    }'
                ]
            ],
            'masterStorageDirectory' => 'bucketABC',
            'slaveStorage' => \andreev1024\mediaContentManager\components\LocalStorage::ID,
            'slaveStorageDirectory' =>'@api/media',         //  !!! MUST BE WRITABLE
            'slaveConfig' => [
                'url' => 'http://api.app.net/media',     //  !!! MUST BE AVAILABLE FROM WEB
            ],
        ],
```      
## Usage
 
### How to save data:

```
use andreev1024\mediaContentManager\models\File;

$model = new File();
$model->setAttributes([
    'path' => 'contents/mail', // path to save in the storage
    'file' => UploadedFile::getInstanceByName('file'), // or 'path/to/local/file.ext'
]);
$model->save();
```

### How to obtain file url:

```
use andreev1024\mediaContentManager\models\File;

$model = File::findOne($id);
$url = $model->getUrl();
```

### How to obtain URL for the images (jpeg, jpg, png file formats)

```
use andreev1024\mediaContentManager\models\File;
use andreev1024\mediaContentManager\processors\ImageProcessor;

$model = File::findOne($id);
$url = $model->getUrl(ImageProcessor::SIZE_TYPE_SMALL);
```

### How to edit file info:

- via url `/mediaContentManager/files/update/{$file_id}`
- via model:

```
use andreev1024\mediaContentManager\models\File;

$model = File::findOne($id);
$model->setScenario(File::SCENARIO_UPDATE);
$model->setAttributes([
    'title' => $new_title,
    'description' => $new_description,
    'metadata' => [
        // ...
    ],
]);
$model->save();
```

### How to view file info:

По url: `/mediaContentManager/files/view/{$file_id}`

### How to use `FileBehavior`

Record.php
```
/**
 * Class Record
 *
 * @property int $id
 * @property int $file_id
 *
 * @property File $file
 */
class Record extends ActiveRecord
{
    public $attachment;

    public function behaviors()
    {
        return [
            'fileBehavior' => [
                'class' => FileBehavior::className(),
                'attribute' => 'attachment',
                'path' => 'contents/mail', // or closure
                'relationName' => 'file',
            ],
        ];
    }
    
    public function rules()
    {
        return [
            [['attachment'], 'file'],
        ];
    }
    
    public function getFile()
    {
        return $this->hasOne(File::className(), ['id' => 'file_id']);
    }
}
```

```
$model = new Record();
$model->setAttribute('attachment', UploadedFile::getInstance($model, 'attachment'));
$model->save();
```

### How to use `PivotFileBehavior`

RelRecordFile.php
```
/**
 * Class RelRecordFile
 * 
 * @property int $id
 * @property int $record_id
 * @property int $file_id
 */
class RelRecordFile extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%rel_record_file}}';
    }
}
```

Record.php
```
use andreev1024\mediaContentManager\behaviors\PivotFileBehavior

/**
 * Class Record
 * 
 * @property int $id
 */
class Record extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%record}}';
    }
    
    public function behaviors()
    {
        return [
            'fileBehavior' => [
                'class' => PivotFileBehavior::className(),
                'pivotClassName' => RelRecordFile::className(),
                'pivotLink' => ['record_id' => 'id'],
                'link' => ['id' => 'file_id'],
            ],
        ];
    }
    
    public function getFileBehavior()
    {
        return $this->getBehavior('fileBehavior');
    }
}
```

```
$record = Record::findOne($id);
$files = $record->getFilesBehavior()->files;
foreach ($files as $file) {
    echo Html::link($file->original_name, $file->getUrl());
}
```

## API

### andreev1024\mediaContentManager\models\File

#### Properties:

- $id (readonly)
- $original_name (readonly) - original file name with extension
- $size (readonly) - size of the file in bytes
- $mime_type (readonly)
- $title - file title
- $description - file description
- $metadata - other info about the file
- $path (only when creating) - path to save in the storage
- $name (readonly) - file name in the storage
- $created_by
- $updated_by
- $created_at
- $updated_at

#### Methods:

- getUrl()
- getProcessor()

### andreev1024\mediaContentManager\processors\ImageProcessor

Used for images only(jpeg, jpg, png).

#### Constants:

- SIZE_TYPE_SMALL
- SIZE_TYPE_MEDIUM
- SIZE_TYPE_LARGE

#### Methods:

- getSizeTypes()
- getUrl($size_type = null)

### andreev1024\mediaContentManager\behaviors\PivotFileBehavior

used for Record - File relation

#### Properties

- $pivotClassName - class name of pivot model
- $pivotLink
- $pivotOnCondition
- $link

#### Methods:

- getPivot()
- getFiles()

How to upload images
=================

Example:
````
//1.  Controller:     

        public function actionImageUpload()
        {
            $fileAttribute = 'attachment';
            $output = [];
            $saveImage = function($attachment, $productId) {
                $model = new ProductImage();
                $model->setAttributes([
                    'product_id' => $productId,
                    'attachment' => $attachment,
                ]);
                $model->save(false);
            };
    
            if ($productId = Yii::$app->request->post('product_id')) {
                $model = new ProductImage(['product_id' => $productId]);
                if ($attachment = UploadedFile::getInstances($model, $fileAttribute)) {
                    $model->attachment = $attachment;
                    if ($model->validate()) {
                        if (is_array($model->attachment)) {
                            foreach ($model->attachment as $uploadFile) {
                                $saveImage($uploadFile, $productId);
                            }
                        } else {
                            $saveImage($model->attachment, $productId);
                        }
                    } else {
                        $errors = $model->getErrors($fileAttribute);
                        $errorMsg = reset($errors);
                        $output['error'] = $errorMsg;
                    }
                }
            }
            return json_encode($output);
        }
    
//2.  View

        echo FileInput::widget([
            'model' => new \modules\products\models\ProductImage,
            'attribute' => 'attachment[]',
            'options'=>[
                'multiple'=>true,
                'accept' => 'image/*'   //  req for img
            ],
            'pluginOptions' => [
                'showCaption' => false,
                'showRemove' => true,
                'showUpload' => true,
                'browseClass' => 'btn btn-primary',
                'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                'browseLabel' => Yii::$app->translate->t('select image'),
                'uploadUrl' => Url::to(['product/image-upload']),
                'uploadExtraData' => [
                    'product_id' => $model->id,
                ],
                //  Hide upload btn in thumbs. If you show this btn and use it you must know, that
                //  `filebatchuploadcomplete` event did't support with it. So, you must add additional
                //  event and prevent `pjax reload conflict`
                'fileActionSettings' => ['uploadClass' => 'hide'],
                'maxFileSize' => 10000, //  10 mB
                'maxFileCount' => 10,
                'previewFileType' => "image",   //  req for img
                'allowedFileTypes' => ["image"],    //  req for img
                //'uploadAsync' => false,
                //'allowedFileExtensions' => ["jpg", "gif", "png", "txt"]
            ],
            'pluginEvents' => [
                'filebatchuploadcomplete' => "function(event, files, extra) {
                    $.pjax.reload({container:'#container'});
                }"
            ]
        ]);
        
//3. Model

        public function behaviors()
        {
            return [
                'fileBehavior' => [
                    'class' => FileBehavior::className(),
                    'attribute' => 'attachment',
                    'path' => 'contents/media/images',
                    'relation' => 'file',
                ],
            ];
        }
        
        public function rules()
        {
            return [
                ...
                [
                    'attachment',
                    'image',
                    'extensions' => ['png', 'jpg', 'jpeg'],
                    'maxSize' => 1024*1024*10,   //  10 mb
                    'maxFiles' => 10,
                ],
                ...
            ];
        }
```
    