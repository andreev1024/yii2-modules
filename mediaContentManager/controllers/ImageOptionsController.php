<?php

namespace andreev1024\mediaContentManager\controllers;

use andreev1024\mediaContentManager\models\ImageOption;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\web\Controller;

class ImageOptionsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'accessControl' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['imageOptions'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.14
     * @access public
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        if (\Yii::$app->request->post('reset')) {
            ImageOption::deleteAll();
            \Yii::$app->getSession()->setFlash('success', 'Success reset');

            return $this->refresh();
        }

        $models = ImageOption::getAllOptions();
        if (Model::loadMultiple($models, \Yii::$app->request->post()) && Model::validateMultiple($models)) {
            foreach ($models as $model) {
                $model->save(false);
            }

            \Yii::$app->getSession()->setFlash('success', 'Success saved');

            return $this->refresh();
        }

        return $this->render('index', ['models' => $models]);
    }
}
