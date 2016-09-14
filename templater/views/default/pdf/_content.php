<?php

use conquer\codemirror\CodemirrorAsset;
use conquer\codemirror\CodemirrorWidget;
use kartik\tabs\TabsX;
use yii\helpers\Html;

?>

<div class="col-sm-9 col-xs-12 form-group">
    <?php
    $items = [
        [
            'label' => Yii::$app->translate->t('Template', 'app'),
            'content' => '',
            'active' => true
        ],
        [
            'label' => Yii::$app->translate->t('Header', 'app'),
            'content' => ''
        ],
        [
            'label' => Yii::$app->translate->t('Footer', 'app'),
            'content' => ''
        ],
        [
            'label' => Yii::$app->translate->t('Css', 'app'),
            'content' => ''
        ]
    ];
    ?>

    <?= TabsX::widget([
        'items' => $items,
        'position' => TabsX::POS_ABOVE,
        'bordered' => true,
        'encodeLabels' => false
    ])
    ?>

    <div id="code-contents" class="code-relative">
        <div id="code0" class="code-relative">
            <?=
            $form->field($model, 'template')->widget(
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
                    'options' => ['rows' => 50]
                ]
            )->label(false)
            ?>
        </div>

        <div id="code1" class="code-absolute">
            <?=
            $form->field($model, 'header')->widget(
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
                        'mode' => 'htmlmixed'
                    ],
                ]
            )->label(false)
            ?>
        </div>

        <div id="code2" class="code-absolute">
            <?=
            $form->field($model, 'footer')->widget(
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
                ]
            )->label(false)
            ?>
        </div>

        <div id="code3" class="code-absolute">
            <?=
            $form->field($model, 'css')->widget(
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
                        'lineWiseCopyCut' => true
                    ],
                ]
            )->label(false)
            ?>
        </div>
    </div>
</div>


<row>
    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ?
                Yii::$app->translate->t('create', 'app') :
                Yii::$app->translate->t('update', 'app'),
            [
                'class' => $model->isNewRecord ?
                    'btn btn-success' :
                    'btn btn-primary'
            ]
        );?>
    </div>
</row>