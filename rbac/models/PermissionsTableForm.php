<?php

namespace andreev1024\rbac\models;

use yii;
use yii\base\Model;
use yii\rbac\ManagerInterface;

/**
 * Class PermissionsTableForm
 * @author Alexandr Arofikin <sashaaro@gmail.com>
 * @author Andreev <andreev1024@gmail.com>
 */
class PermissionsTableForm extends Model
{

    const DEFAULT_GROUP = 'other';

    public $permissions;

    private $authManager;

    public function rules()
    {
        return [
            [['permissions'], 'safe']
        ];
    }

    /**
     * @param ManagerInterface $authManager
     */
    public function setAuthManager(ManagerInterface $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $authManager = $this->authManager;
        foreach ($this->permissions as $role => $permission) {
            $role = $authManager->getRole($role);
            foreach ($permission as $name => $value) {
                $permission = $authManager->getPermission($name);
                $relation = $authManager->hasChild($role, $permission);
                if ($value && !$relation) {
                    $authManager->addChild($role, $permission);
                } elseif (!$value && $relation) {
                    $authManager->removeChild($role, $permission);
                }
            }
        }

        return true;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-05-23
     * @access  public
     * @return  array Data for ArrayDataProvider. Used for permission table rendering.
     */
    public function getData()
    {
        $roles = $this->authManager->getRoles();
        $permissions = $this->authManager->getPermissions();
        $data = [];
        foreach ($roles as $role) {
            foreach ($permissions as $permission) {
                $data[$this->getGroup($permission->name)][$role->name][$permission->name] =
                    \Yii::$app->authManager->hasChild($role, $permission);
            }
        }

        $dataForProvider = [
            'pagination' => [
                'pageSize' => 50
            ]
        ];
        
        foreach ($data as $key => $value) {
            $dataForProvider['allModels'][] = [
                'groupName' => $key,
                'permissions' => $value
            ];
        }

        return $dataForProvider;
    }

    /**
     * Extract group name from permission
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-05-23
     * @access  public
     * @param   string $permission
     * @return  string
     */
    public function getGroup($permission)
    {
        if (($pos = strpos($permission, '.')) === false) {
            return Yii::$app->translate->t(static::DEFAULT_GROUP);
        }

        return substr($permission, 0, $pos);
    }
}
