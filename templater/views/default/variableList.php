<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
?>
<div class="row">
    <div class="col-xs-12 col-md-6 col-lg-6 form-group">
        <?=
        Html::textInput('searchBox', '', [
            'class' => 'form-control',
            'placeholder' => Yii::$app->translate->t('Enter search text', 'app')
        ])
        ?>
    </div>
</div>
<div>
    <?php
    Pjax::begin([
        'enablePushState' => false,
        'options' => [
            'id' => 'pjaxVariablesContainer'
        ]
    ]);
    echo GridView::widget([
        'options' => [
            'class' => 'variablesDescription'
        ],
        'dataProvider' => $dataProvider,
        'rowOptions' => function($model, $name) {
            return [
                'data-description' => $model['description']
            ];
        },
        'columns' => [
            [
                'attribute' => 'description',
                'header' => Yii::$app->translate->t('Variable description', 'app'),
            ],
            [
                'value' => function($model, $name) {
                    return isset($model['key']) ? $model['key'] : $name;
                },
                'header' => Yii::$app->translate->t('Variable name', 'app'),
            ],
            [
                'attribute' => 'origin',
                'header' => Yii::$app->translate->t('Origin', 'app')
            ]
        ]
    ]);
    Pjax::end();
    ?>
</div>
<?php
$js = <<<JS
    var rows = $('.variablesDescription tbody tr');
    $('input[name=searchBox]')[0].oninput = function(event) {
        var value = event.target.value.toLowerCase();
        rows.each(function(i,line) {

            if ( $(line).data('description').toLowerCase().search(value) > -1 ) {
                $(line).show();
            } else if ( typeof $(line).data('key') === 'string' && $(line).data('key').toLowerCase().search(value) > -1 ) {
                $(line).show();
            } else {
                $(line).hide();
            }
        });
    }
JS;

$this->registerJs($js, View::POS_END);

$js = <<<JS
    $(document).on('pjax:complete', function () {
        rows = $('.variablesDescription tbody tr');
        $('input[name=searchBox]').trigger('input')
    });
JS;

$this->registerJs($js);