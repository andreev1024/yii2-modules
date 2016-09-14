<?php

namespace andreev1024\recron\controllers;

use yii;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use andreev1024\recron\models\Cron;

/**
 * Class SettingsController
 */
class SettingsController extends yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ]
        ];
    }

    public function actionIndex()
    {

        $dataProvider = new ActiveDataProvider(['query' => Cron::find()]);

        if (Yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $model = Cron::findOne($id);
            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['Cron']);
            $post['Cron'] = $posted;
            if ($model->load($post)) {
                $output = '';
                if (isset($posted['active'])) {
                    $output = Yii::$app->formatter->asBoolean($model->active);
                }

                $message = '';
                if (!$model->validate()) {
                    $message = $model->getErrors(current(array_keys($posted)));
                } else {
                    $model->save();
                }
                $out = Json::encode(['output' => $output, 'message' => $message]);
            }
            echo $out;
            return;
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * From create cron task
     * @param string $setCommand pre set command for task
     * @return mixed
     */
    public function actionCreate($setCommand = null)
    {
        $model = new Cron();

        if (!is_null($setCommand))
            $model->command = $setCommand;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionApply()
    {
        $crontabPath = (isset(Yii::$app->params['crontab.path']) ? Yii::$app->params['crontab.path'] : dirname(\Yii::$app->basePath)).'/yii cron/console/table';
        $output = [];
        $returnVar = 0;
        exec('export EDITOR="'.$crontabPath.'"; crontab -e', $output, $returnVar);
        return $this->render('apply', ['output' => $output, 'returnVar' => $returnVar]);
    }

    protected function findModel($id)
    {
        if (($model = Cron::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
