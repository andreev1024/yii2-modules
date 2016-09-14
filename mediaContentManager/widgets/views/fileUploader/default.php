<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
    'action' => $formAction
]);
?>

<?= $form->field($model, 'name')->input('text', ['placeholder'=>'file.txt'])->label(false) ?>
<?= $form->field($model, 'path')->input('text', ['placeholder'=>'contents/media/files'])->label(false) ?>
<?= $form->field($model, 'file')->fileInput()->label(false) ?>

<div class="form-group">
    <?= Html::submitButton('Submit', [
        'class' => 'btn btn-primary',
    ]) ?>
</div>

<?php ActiveForm::end(); ?>
