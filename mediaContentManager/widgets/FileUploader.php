<?php

namespace andreev1024\mediaContentManager\widgets;

use andreev1024\mediaContentManager\models\File;
use Yii;
use yii\bootstrap\Widget;
use yii\web\UploadedFile;

/**
 * FileUploader
 *
 * This widget allows upload files in AWS S3 manually.
 *
 * Example
 *
 *   <?= FileUploader::widget([
 *      'formAction' => Url::to(['/site/default/index'])
 *   ]) ?>
 */
class FileUploader extends Widget
{
    /**
     * @var string Action for form (not required)
     */
    public $formAction = '';

    public function run()
    {
        $model = new File;
        
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->save()) {
                return Yii::$app->getResponse()->refresh();
            }
        }

        return $this->render('default', [
            'model' => $model,
            'formAction' => $this->formAction
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getViewPath()
    {
        return parent::getViewPath() . '/fileUploader';
    }
}