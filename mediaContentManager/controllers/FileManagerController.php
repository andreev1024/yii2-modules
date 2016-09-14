<?php

namespace andreev1024\mediaContentManager\controllers;

use andreev1024\filters\UrlParamsFilter;
use andreev1024\mediaContentManager\models\File;
use andreev1024\mediaContentManager\widgets\FileManager;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * FileManagerController for FileManager widget
 */
class FileManagerController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => UrlParamsFilter::className(),
                'only' => ['index'],
                'config' => [
                    'index' => [
                        'mode' => [
                            'values' => function($attribute) {
                                if (is_numeric($attribute)) {
                                    if (in_array((int)$attribute, File::getProcessingTypes())) {
                                        return true;
                                    }
                                }
                                return false;
                            }
                        ],
                    ]
                ]
            ],
        ];
    }

    /**
     * Return rendered data for FileManager.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $options
     * @param null|string $startDirectory
     * @param null|string $displayMode
     *
     * @return null|string
     * @throws BadRequestHttpException
     */
    public function actionIndex($options, $startDirectory = null, $displayMode = null)
    {
        if (Yii::$app->request->isAjax) {
            $fileManager = new FileManager([
                'encryptedOptions' => $options,
                'startDirectory' => $startDirectory,
                'displayMode' => $displayMode,
            ]);
            return $fileManager->getContent();
        }
        throw new BadRequestHttpException();
    }
}
