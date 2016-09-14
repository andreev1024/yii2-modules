<?php
/**
 * @var \yii\web\View $this
 * @var \andreev1024\mediaContentManager\models\ImageOption[] $models
 * @var \yii\widgets\ActiveForm $form
 */

$this->title = Yii::t('mediaContentManager', 'Image Options');
$this->params['breadcrumbs'] = [
    ['label' => Yii::t('mediaContentManager', 'Media Content Manager'), 'url' => ['default/index']],
    $this->title,
];
?>

<h1 class="page-header margin_btm_20"><?= $this->title ?></h1>

<?php $form = \yii\widgets\ActiveForm::begin() ?>

<div class="image-options-from">
    <?php if (Yii::$app->getSession()->hasFlash('success')): ?>
        <div class="alert alert-success">
            <p><?= Yii::$app->getSession()->getFlash('success') ?></p>
        </div>
    <?php endif ?>

    <?php foreach ($models as $index => $model): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?= $model->getTypeLabel() ?>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, "[$index]width")->textInput() ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, "[$index]height")->textInput() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>

    <div class="form-group">
        <?= \yii\helpers\Html::submitButton(
            Yii::t('mediaContentManager', 'Save'),
            ['class' => 'btn btn-primary']
        ) ?>
        <?= \yii\helpers\Html::submitInput(
            Yii::t('mediaContentManager', 'Reset'),
            [
                'class' => 'btn btn-danger pull-right',
                'name' => 'reset',
            ]
        ) ?>
    </div>
</div>

<?php \yii\widgets\ActiveForm::end() ?>
