<?php
use conquer\codemirror\CodemirrorAsset;
use conquer\codemirror\CodemirrorWidget;
use yii\helpers\Html;
?>

<div class="message-form col-sm-9 col-xs-12 form-group">

    <?= $form->field($model, 'template')->widget(
        CodemirrorWidget::className(), [
        'assets' => [
            CodemirrorAsset::MODE_HTMLMIXED,
            CodemirrorAsset::MODE_XML,
            CodemirrorAsset::MODE_CSS,
            CodemirrorAsset::MODE_JAVASCRIPT,
            CodemirrorAsset::MODE_VBSCRIPT,
            CodemirrorAsset::MODE_HTTP,
        ],
        'settings' => [
            'lineNumbers' => true,
            'mode' => 'htmlmixed',
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton(
            !$model->isNewRecord ?
                Yii::$app->translate->t('update', 'app') :
                Yii::$app->translate->t('create', 'app'),
                [
                    'class' => $model->isNewRecord ?
                        'btn btn-success' :
                        'btn btn-primary'
                ]
        ) ?>
    </div>
</div>