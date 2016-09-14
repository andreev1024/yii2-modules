<?php

namespace andreev1024\piwik\controllers;

use andreev1024\piwik\models\TrackingCode;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * DefaultController for TrackingCode model
 */
class DefaultController extends Controller
{
    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'index',
                            'create',
                            'update',
                            'delete',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Return list with all tracking codes
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public function actionIndex()
    {
        $provider = new ArrayDataProvider([
            'allModels' => TrackingCode::getSettingsList(),
        ]);

        return $this->render('index', ['provider' => $provider]);
    }

    /**
     * Creates new Tracking code
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TrackingCode;
        $model->setScenario(TrackingCode::SCENARIO_CREATE);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Update existing Tracking code
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $scope
     *
     * @return string|\yii\web\Response
     */
    public function actionUpdate($scope)
    {
        $model = TrackingCode::findOne($scope);
        $model->setScenario(TrackingCode::SCENARIO_UPDATE);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Delete existing Tracking code
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $scope
     *
     * @return \yii\web\Response
     */
    public function actionDelete($scope)
    {
        TrackingCode::delete($scope);
        return $this->redirect(['index']);
    }
}
