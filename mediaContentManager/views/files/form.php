<?php
/**
 * @var \yii\web\View $this
 * @var \andreev1024\mediaContentManager\models\File $model
 * @var \yii\widgets\ActiveForm $form
 */

$this->title = Yii::t('mediaContentManager', 'Update an file');
$this->params['breadcrumbs'] = [
    ['label' => Yii::t('mediaContentManager', 'Media Content Manager'), 'url' => ['default/index']],
    $this->title,
];
?>

<div class="file-form">

    <?php $form = \yii\widgets\ActiveForm::begin() ?>

    <?= $form->errorSummary($model) ?>
    
    <?= $form->field($model, 'title')->textInput(['maxlength' => 45]) ?>
    <?= $form->field($model, 'description')->textarea(['cols' => 50, 'rows' => 6]) ?>
    
    <div class="form-group">
        <?= \yii\helpers\Html::submitButton(
            Yii::t('mediaContentManager', 'Update'),
            ['class' => 'btn btn-primary']
        ) ?>
    </div>

    <?php $form->end() ?>

</div>
