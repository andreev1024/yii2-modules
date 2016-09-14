<?php

namespace andreev1024\rbac\controllers;

use andreev1024\rbac\models\PermissionsTableForm;
use yii\base\DynamicModel;
use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTable()
    {
        $model = new PermissionsTableForm();
        $model->setAuthManager(\Yii::$app->authManager);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->refresh();
        }

        $roles = \Yii::$app->authManager->getRoles();
        $permissions = \Yii::$app->authManager->getPermissions();

        return $this->render('table', [
            'allRoles' => $roles,
            'allPermissions' => $permissions,
            'model' => $model
        ]);
    }
}
