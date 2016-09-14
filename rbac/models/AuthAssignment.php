<?php

namespace andreev1024\rbac\models;

use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%re_auth_assignment}}".
 *
 * @property string $item_name
 * @property string $user_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class AuthAssignment extends ActiveRecord
{
    const ITEM_NAME_OPERATOR = 'operator';
    const ITEM_NAME_MANAGER = 'manager';
    const ITEM_NAME_ADMIN = 'admin';

    public function behaviors()
    {
        return [
             TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 're_auth_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'targetClass' => \common\models\User::className(), 'targetAttribute' => 'id'],
            [['item_name', 'user_id'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name' => 'Item Name',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
