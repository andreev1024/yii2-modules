<?php

use kartik\grid\GridView;

$this->params['breadcrumbs'][] = ['url' => ['default/index'], 'label' => 'RBAC Module'];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
/** @var \yii\widgets\ActiveForm $form */
$form = \yii\widgets\ActiveForm::begin();

$parentModel = $model;
$gridColumns = [
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'groupName',
        'label' => Yii::$app->translate->t('Groups'),
        'vAlign' => 'middle',
    ],
    [
        'class' => 'kartik\grid\ExpandRowColumn',
        'detailRowCssClass' => GridView::TYPE_DEFAULT,
        'value' => function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function ($model, $key, $index, $column) use ($allRoles, $allPermissions, $parentModel) {
            return Yii::$app->controller->renderPartial('_tableDetails', [
                'model' => $model,
                'parentModel' => $parentModel,
                'allRoles' => $allRoles,
                'allPermissions' => $allPermissions,
            ]);
        },

    ],
];

echo GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider($model->getData()),
    'columns' => $gridColumns,
    'condensed' => true,
    'responsive' => true,
    'export' => false,
    'hover' => true,
]);

?>

<button type="submit" class="btn btn-success">Save</button>
<?php $form->end() ?>

<style>
    #permissions-table td:first-child{
        width: 200px;
    }
    #permissions-table .permission-description{
        font-size: 10px;
        font-family: Arial;
        font-style: oblique;
    }
</style>

<?php
$this->registerJs("
    var accessChangeColor = function(element) {
        element.closest('td').css('background-color', element.is(':checked') ? '#C1E6CD' : '#E6C2C1');
    }

    $('#permissions-table input[type=checkbox]').on('change', function(){
        accessChangeColor($(this));
    });

    $('input[type=checkbox].checker').on('change', function(){
        var
            role = $(this).data('role'),
            group = $(this).data('group'),
            checked = $(this).is(':checked'),
            elements = $('[data-role=' + role + ']').filter('[data-group=' + group + ']');

            elements.prop('checked', checked);
            elements.each(function(){
                accessChangeColor($(this));
            });
    });
")
?>