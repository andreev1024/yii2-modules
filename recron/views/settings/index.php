<?php

use yii\helpers\Html;
use reseed\reWidgets\rebox\ReBox;
use kartik\grid\GridView;
use kartik\icons\Icon;

$this->title = Yii::$app->translate->t('settings cron');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-currency-index">
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
    ]); ?>

    <p>
        <?php
        echo Html::a(Yii::$app->translate->t('create {modelClass}', null, ['modelClass' => 'cron task']),['create'], ['class' => 'btn btn-success']),' ';
        echo Html::a(Yii::$app->translate->t('apply crontab'),['apply'], ['class' => 'btn btn-primary']);
        ?>
    </p>
    <div id="currency-view">
        <?php
        echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'contentOptions' => ['style' => 'width:40px' ],
                    ],
                    [
                        'class' => 'kartik\grid\EditableColumn',
                        'attribute' => 'minutes',
                        'value' => 'minutes',
                        'format' => 'raw'
                    ],
                    [
                        'class' => 'kartik\grid\EditableColumn',
                        'attribute' => 'hours',
                        'value' => 'hours',
                        'format' => 'raw'
                    ],
                    [
                        'class' => 'kartik\grid\EditableColumn',
                        'attribute' => 'days',
                        'value' => 'days',
                        'format' => 'raw'
                    ],
                    [
                        'class' => 'kartik\grid\EditableColumn',
                        'attribute' => 'months',
                        'value' => 'months',
                        'format' => 'raw'
                    ],
                    [
                        'class' => 'kartik\grid\EditableColumn',
                        'attribute' => 'week',
                        'value' => 'week',
                        'format' => 'raw'
                    ],
                    [
                        'class' => 'kartik\grid\EditableColumn',
                        'attribute' => 'command',
                        'value' => 'command',
                        'format' => 'raw'
                    ],
                    [
                        'class' => 'kartik\grid\EditableColumn',
                        'attribute' => 'active',
                        'value' => 'active',
                        'format' => 'boolean',
                        'editableOptions' => [
                            'inputType' => \kartik\editable\Editable::INPUT_CHECKBOX,
                        ],
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'header' => '',
                        'template' => '{update} {delete}',
                        'contentOptions' => ['style' => 'width:90px' ]
                    ],
                ],
                'id' => 'cronGrid',
                'containerOptions' => ['style' => 'min-height:600px;' ],
                'responsive' => true,
                'export' => false,
                'hover' => true,
                'rowOptions' => ['style' => 'background-color: #fff;'],
                'summaryOptions' => ['class' => 'margin_btm_10 font90' ]
            ]);
        ?>
    </div>

    <?php ReBox::end(); ?>
</div>