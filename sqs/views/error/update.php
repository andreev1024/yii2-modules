<?php

use kartik\widgets\ActiveForm;
use reseed\reWidgets\rebox\ReBox;
use andreev1024\sqs\models\SqsError;
use andreev1024\sqs\Module;
use yii\helpers\Html;

$this->title = Yii::$app->translate->t('update', 'app');
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->translate->t('sqs errors', Module::$translateCategory),
    'url' => ['error/index']
];
$this->params['breadcrumbs'][] = ['label' => $model->id];

?>
<div class="error-update">
    <?php ReBox::begin([
        'header' => [
            'options' => [
                'class' => 'box-name',
                'title' => Html::encode($this->title)
            ],
            'icon' => [
                'name' => 'table',
                'framework' => 'fa',
                'options' => [],
                'space' => true,
                'tag' => 'i'
            ],
        ],
    ]);
    $form = ActiveForm::begin();
    ?>

    <?= $form->field($model, 'code')->staticInput() ?>
    <?= $form->field($model, 'error_message')->staticInput() ?>
    <?= $form->field($model, 'metadata')->staticInput() ?>
    <?= $form->field($model, 'created_at')->staticInput() ?>
    <?= $form->field($model, 'updated_at')->staticInput() ?>

    <?= $form->field($model, 'queue_url') ?>
    <?= $form->field($model, 'message')->textarea() ?>
    <?= $form->field($model, 'status')->dropDownList(SqsError::getStatusArray()) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::$app->translate->t('update', 'app'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php ReBox::end(); ?>
</div>
