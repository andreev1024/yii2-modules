<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use andreev1024\rbac\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model andreev1024\rbac\models\AuthItemChild */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-child-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent')->dropDownList(\yii\helpers\ArrayHelper::map(AuthItem::find()->asArray()->all(), 'name', 'name'))//->)//->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'child')->dropDownList(\yii\helpers\ArrayHelper::map(AuthItem::find()->asArray()->all(), 'name', 'name'))//->)//textInput(['maxlength' => 64]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
