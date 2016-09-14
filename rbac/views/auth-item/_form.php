<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use andreev1024\rbac\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model andreev1024\rbac\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'type')->radioList([
        AuthItem::TYPE_PERMISSION => 'Permission',
        AuthItem::TYPE_ROLE => 'Role',
    ])//->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
<?php /*
    <?= $form->field($model, 'rule_name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>
*/ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
