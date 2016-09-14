<?php

namespace andreev1024\mediaContentManager\controllers;

use andreev1024\mediaContentManager\models\File;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class FilesController
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 *
 * @package andreev1024\mediaContentManager\controllers
 */
class FilesController extends Controller
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
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access public
     *
     * @return string
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionIndex()
    {
        if (!\Yii::$app->getUser()->can('viewFiles')) {
            throw new ForbiddenHttpException();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => File::find(),
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access public
     *
     * @param string $id
     *
     * @return string
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->loadModel($id);

        if (!\Yii::$app->getUser()->can('viewFile', ['model' => $model])) {
            throw new ForbiddenHttpException();
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access public
     *
     * @param integer|null $id
     *
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id = null)
    {
        $model = $this->loadModel($id);
        $model->setScenario(File::SCENARIO_UPDATE);

        if (!\Yii::$app->getUser()->can('updateFile', ['model' => $model])) {
            throw new ForbiddenHttpException();
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('form', ['model' => $model]);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access public
     *
     * @param integer $id
     *
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = $this->loadModel($id);
        if (!\Yii::$app->getUser()->can('deleteFile', ['model' => $model])) {
            throw new ForbiddenHttpException();
        }
        (new \modules\products\models\ProductImage())->deleteAll([
            'file_id' => $model->id
        ]);
        $model->delete();

        $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.07
     * @access protected
     *
     * @param integer|null $id
     *
     * @return File
     * @throws NotFoundHttpException
     */
    protected function loadModel($id = null)
    {
        $model = !empty($id) ? File::findOne($id) : new File();
        if (empty($model)) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
