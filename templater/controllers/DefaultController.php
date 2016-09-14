<?php

namespace andreev1024\templater\controllers;

use andreev1024\filters\UrlParamsFilter;
use andreev1024\templater\components\Config;
use andreev1024\templater\models\Template;
use andreev1024\templater\models\TemplateSearch;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Default module Controller.
 */
class DefaultController extends Controller
{
    const ENTITY_STATE_KEY = 'entityStateKey';

    public $messages = [
        'tryLater' => 'An error has occurred. Please try again later.',
    ];

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
                            'create',
                            'update',
                            'index',
                            'delete',
                            'session-handler',
                            'choose-template-type',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'urlParamsFilter' => [
               'class' => UrlParamsFilter::className(),
               'only' => ['create'],
               'method' => 'get',
               'config' => [
                   'create' => [
                       'type' => [
                           'values' => Config::getTypes()
                       ],
                       'entity' => [
                           'values' =>function($attribute) {
                               $type = Yii::$app->request->get('type');
                               $entities = Config::getEntityByType($type);
                               return in_array($attribute, array_keys($entities));
                           }
                       ],
                   ]
               ]
            ],
        ];
    }

    /**
     * Lists all Template models.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TemplateSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return mixed
     */
    public function actionChooseTemplateType()
    {
        if (Yii::$app->request->isPost && Yii::$app->request->post('type')) {
            $this->redirect(['create', 'type' => Yii::$app->request->post('type')]);
        }

        return $this->render('choose-type');
    }

    /**
     * Creates a new Template model.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $type
     *
     * @return string|Response
     * @throws \Exception
     */
    public function actionCreate($type, $entity = null)
    {
        if (!Yii::$app->request->isAjax) {
            Yii::$app->session->set(self::ENTITY_STATE_KEY, null);
        }

        $error = '';
        $modelClass = $this->getModelClassByType($type);
        $model = new $modelClass;

        if ($entity) {
            $model->entity = $entity;
        }

        $model->load(Yii::$app->request->post());
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->save();
                $transaction->commit();
                $redirectUrl = ['index'];
                if ($previousUrl = Url::previous('templater')) {
                    $redirectUrl = $previousUrl;
                    Url::remember(null, 'templater');
                }
                return $this->redirect($redirectUrl);
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->handleTransactionFail($e);
                $error = Yii::$app->translate->t($this->messages['tryLater'], 'app');
            }
        }

        $entity = Yii::$app->session->get(self::ENTITY_STATE_KEY);
        return $this->render('create', [
            'model' => $model,
            'type' => $type,
            'error' => $error,
            'variableList' => $this->getVariableList([
                 'entityName' => !empty($entity) ? $entity : null
            ])
        ]);

    }

    /**
     * Updates an existing Template model.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $id
     *
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->request->isAjax) {
            Yii::$app->session->set(self::ENTITY_STATE_KEY, null);
        }

        $error = '';
        $entity = Yii::$app->session->get(self::ENTITY_STATE_KEY);
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->save();
                $transaction->commit();
                $redirectUrl = ['index'];
                if ($previousUrl = Url::previous('templater')) {
                    $redirectUrl = $previousUrl;
                    Url::remember(null, 'templater');
                }
                return $this->redirect($redirectUrl);
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->handleTransactionFail($e);
                $error = Yii::$app->translate->t($this->messages['tryLater'], 'app');
            }
        }

        return $this->render('update', [
            'model' => $model,
            'type' => Config::getTypeByEntity($model->entity),
            'error' => $error,
            'variableList' => $this->getVariableList([
                'entityName' => isset($entity) ? $entity : $model->entity,
                'templateModel' => $model,
            ]),
        ]);

    }

    /**
     * Deletes an existing Template model.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $id
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Save group state in session
     *
     * @author  Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-02-15
     * @access  public
     * @return  mixed
     */
    public function actionSessionHandler()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $session = Yii::$app->session;

        if (!$session->isActive) {
            $session->open();
        }

        if (($data = Yii::$app->request->post(self::ENTITY_STATE_KEY)) !== null) {
            $session->set(self::ENTITY_STATE_KEY, $data);
            return ['success' => true];
        }

        return ['success' => false];
    }

    /**
     * Renders variable list.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param array $arguments
     *      string $entityName 'buyOrderContract', 'sellOrderContract', 'invoice' etc.
     *      Template $templateModel
     *
     * @return mixed
     * @throws \Exception
     */
    private function getVariableList(array $arguments = [])
    {
        $entityName = isset($arguments['entityName']) ? $arguments['entityName'] : null;
        $data = [];

        if ($entityName !== null) {
            $entity = Config::getEntity($entityName);
            if (empty($entity['class'])) {
                throw new \Exception('Wrong attribute');
            }

            $data = $entity['class']::getVariableDescription($arguments);
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $this->renderPartial('variableList', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Find model by Id.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        $model = Template::find()->where(['id' => $id])->asArray()->one();
        if ($model !== null) {
            $type = Config::getTypeByEntity($model['entity']);
            $modelClass = $this->getModelClassByType($type);
            return $modelClass::findOne($id);
        }

        throw new NotFoundHttpException(
            Yii::$app->translate->t('The requested page does not exist.', 'app')
        );
    }

    /**
     * Returns model class by type.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $type
     *
     * @return string
     */
    private function getModelClassByType($type)
    {
        return 'andreev1024\templater\models\\' . ucfirst($type) . 'Template';
    }

    /**
     * Handle transaction fail on CRUD opeartion.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $e Exception
     *
     * @return string
     */
    public function handleTransactionFail($e)
    {
        return Template::errorExceptionHandler($e);
    }
}
