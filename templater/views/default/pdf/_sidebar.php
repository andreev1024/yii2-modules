<?php

use andreev1024\templater\models\PdfTemplate;
use andreev1024\templater\models\Template;
use yii\helpers\Html;

?>

<div class="col-sm-3 col-xs-12">
    <?= $form->field($model, 'name')->textInput([
        'maxlength' => 255,
        'placeholder' => Yii::$app->translate->t('myTemplateName', 'app'),
    ]) ?>

    <?= $form->field($model, 'title')->textInput([
        'maxlength' => 255,
        'placeholder' => Yii::$app->translate->t("I'm a nice title for your document", 'app'),
    ]) ?>

    <?= $form->field($model, 'entity')->widget('\kartik\widgets\Select2', [
        'data' => Template::getEntityLabelsByType(PdfTemplate::TEMPLATE_TYPE),
        'options' => [
            'placeholder' => Yii::$app->translate->t('select entity ...', 'app'),
            'id' => $selector['id']['templateEntity']
        ],
    ]); ?>

    <?= Html::a(
        Yii::$app->translate->t('View the variable list', 'app') .
        '<i class="glyphicon glyphicon-log-in"></i>',
        '#',
        [
            'id' => $selector['id']['modalTrigger'],
            'class' => isset($variableList) ? '' : 'hidden',

        ]
    ); ?>

    <?= $form->field($model, 'language')->dropDownList(PdfTemplate::getLanguages()) ?>
    <?= $form->field($model, 'format')->dropDownList(PdfTemplate::getFormatlabel()) ?>
    <?= $form->field($model, 'orientation')->dropDownList(PdfTemplate::getOrientationsLabel()) ?>
    <?= $form->field($model, 'show_barcode')->dropDownList(PdfTemplate::getShowBarcodeLabel()) ?>
    <?= $form->field($model, 'barcode_type')->dropDownList(PdfTemplate::getBarcodeTypeLabel()) ?>
    <?= $form->field($model, 'status_id')->dropDownList(PdfTemplate::getStatusLabel()) ?>
    <?= $form->field($model, 'flag_main')->checkbox([
        'label' => Yii::$app->translate->t('main template', 'app')
    ]) ?>

    <div class="form-group">
        <?= \andreev1024\mediaContentManager\widgets\FileManager::widget() ?>
    </div>

</div>