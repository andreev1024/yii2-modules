<?php

namespace andreev1024\rbac\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%re_auth_item}}".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property string $created_at
 * @property string $updated_at
 */
class AuthItem extends \yii\db\ActiveRecord
{
    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 're_auth_item';
    }

    public function behaviors()
    {
        return [
             TimestampBehavior::className(),
        // BlameableBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type'], 'integer'],
            [['type'], 'in', 'range' => self::getTypeRange()],
            [['description', 'data'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'rule_name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'type' => 'Type',
            'description' => 'Description',
            'rule_name' => 'Rule Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    /**
     * @inheritdoc
     */
//    public function scenarios()
//    {
//        // bypass scenarios() implementation in the parent class
//        return Model::scenarios();
//    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'rule_name' => $this->rule_name,
            'data' => $this->data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
        ->andFilterWhere(['like', 'description', $this->description])
        ->andFilterWhere(['like', 'rule_name', $this->rule_name]);

        return $dataProvider;
    }

    public function getTypeOptions()
    {
        return array(
            self:: TYPE_ROLE => Yii::$app->translate->t('Role'),
            self:: TYPE_PERMISSION => Yii::$app->translate->t('Permission')
        );
    }
    /*
     * method to return an array of allowed numerical Gender values
     */

    public static function getTypeRange()
    {
        return array(
            self::TYPE_ROLE,
            self::TYPE_PERMISSION
        );
    }

    /**
     * @return string the text display for the current gender
     */
    public static function getTypeText($type)
    {
        switch ($type) {
            case 1:
                return Yii::$app->translate->t('Role');
                break;
            case 2:
                return Yii::$app->translate->t('Permission');
                break;
        }
    }

    public function getAuthRoleType($role)
    {
        $item = $this->findOne(['name' => $role]);
        return self::getTypeText($item->type);
    }

    public function findAllTree($arrRole, &$arrReturn = array(), $model = null)
    {
        //$arrReturn[] = $arrRole;
        $this->findAllParent($arrRole, $arrReturn, $model);
        $this->findAllParent(array($arrRole), $arrReturn);
    }

    public function findAllParent($role, &$arrReturn, $model)
    {
        $list = $model->findAll(['child' => $role]);
        if (!empty($list)) {
            foreach ($list as $item) {
                $arrReturn[] = $item->parent;
                $this->findAllParent($item->parent, $arrReturn, $model);
            }
        }
    }

    public function findAllChild($roleArr, &$arrReturn, $model)
    {
        while (!empty($roleArr)) {
            $item = array_shift($roleArr);
            if (!in_array($item, $arrReturn)) {
                $arrReturn[] = $item;
                $list = $model->findAll(['parent' => $item]);
                if (!empty($list)) {
                    foreach ($list as $temp) {
                        if (!in_array($temp->child, $arrReturn)) {
                            $roleArr[] = $temp->child;
                        }
                    }
                }
            }
        }
    }

    public function findChilds($role, $model)
    {
        $childs = $model->findAll(['parent' => $role]);
        $arr = array();
        if (!empty($childs)) {
            foreach ($childs as $item) {
                $temp = $this->findOne(['name' => $item->child]);
                $arr[] = $temp;
            }
        }
        return $arr;
    }

    public function checkParent(&$childs, $model)
    {
        foreach ($childs as $item) {
            $allChild = array();
            $this->findAllChild(array($item), $allChild, $model);
            $allChild = array_diff($allChild, array($item));
            $childs = array_diff($childs, $allChild);
        }
    }
}
