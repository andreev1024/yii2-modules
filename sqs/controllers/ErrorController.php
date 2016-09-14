<?php

namespace andreev1024\sqs\controllers;

use andreev1024\sqs\models\SqsError;
use andreev1024\sqs\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Controller provides GUI for SQS Error handling.
 */
class ErrorController extends Controller
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
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Show error list.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SqsError();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Update model page.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['index']);
            }

        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Delete model page.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $id
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Delete multiple posts page.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return \yii\web\Response
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionBatchDelete()
    {
        if (($ids = Yii::$app->request->post('ids')) !== null) {
            $models = $this->findModel($ids);
            foreach ($models as $model) {
                $model->delete();
            }
            return $this->redirect(['index']);
        } else {
            throw new HttpException(400);
        }
    }

    /**
     * Add items to queue.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return \yii\web\Response
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionAddToQueue()
    {
        if (($ids = Yii::$app->request->post('ids')) !== null) {
            $models = $this->findModel($ids);
            $messagesByUrl = [];
            foreach ($models as $model) {
                $messagesByUrl[$model->queue_url][] = $model->message;
            }

            foreach ($messagesByUrl as $queueUrl => $messages) {
                Yii::$app->sqs->sendMessageBatch($messages, ['queues' => [$queueUrl]]);
            }

            if (isset($model)) {
                $model->deleteAll(['id' => $ids]);
            }

            return $this->redirect(['index']);
        }

        throw new HttpException(400);
    }

    /**
     * Finds the SqsError model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param mixed $id
     *
     * @return object|array
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (is_array($id)) {
            $model = SqsError::findAll($id);
        } else {
            $model = SqsError::findOne($id);
        }

        if (!$model) {
            throw new NotFoundHttpException(
                Yii::$app->translate->t('the requested page does not exist.', Module::$translateCategory)
            );
        } else {
            return $model;
        }
    }
}
