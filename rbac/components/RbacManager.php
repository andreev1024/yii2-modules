<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 05.03.15
 * Time: 1:17
 */

namespace andreev1024\rbac\components;


use yii\rbac\DbManager;
use andreev1024\rbac\models\AuthItem;

class RbacManager extends DbManager
{
    /**
     * @inheritdoc
     */
    protected function getItem($name)
    {
        $item = parent::getItem($name);

        if(!$item){
            $authItem = new AuthItem();
            $authItem->name = $name;
            $authItem->type = AuthItem::TYPE_PERMISSION;
            $authItem->save();
        }

        return $item;
    }
} 