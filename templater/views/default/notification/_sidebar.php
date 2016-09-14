<?php

use andreev1024\templater\models\NotificationTemplate;
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
        'data' => Template::getEntityLabelsByType(NotificationTemplate::TEMPLATE_TYPE),
        'options' => [
            'placeholder' => Yii::$app->translate->t('select entity ...', 'app'),
            'id' => $selector['id']['templateEntity']
        ],
    ]); ?>

    <div style="margin:15px 0px;">
        <?= Html::a(
            Yii::$app->translate->t( 'view the variable list', 'app') .
            '<i class="glyphicon glyphicon-log-in"></i>',
            '#',
            [
                'id' => $selector['id']['modalTrigger'],
                'class' => isset($variableList) ? '' : 'hidden',
            ]); ?>
    </div>

</div>