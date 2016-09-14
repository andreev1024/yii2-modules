<?php
use common\models\Persona;
use kartik\grid\GridView;
use andreev1024\templater\models\Template;
use reseed\reWidgets\rebox\ReBox;
use yii\helpers\Html;


$this->title = Yii::$app->translate->t('templates', 'app');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="template-index">

    <?php
        ReBox::begin([
            'header' => [
                'options' => [
                    'title' => $this->title
                ],
                'icon' => [
                    'name' => 'table',
                ],
            ],
        ]);

        $panelContent[] = Html::a(
            Yii::$app->translate->t('create template', 'app'),
            ['choose-template-type'],
            ['class' => 'btn btn-success']);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'condensed' => true,
        'responsive' => false,
        'export' => false,
        'hover' => true,
        'panel' => [
            'heading'=>false,
            'type'=>'success',
            'before'=>join('', $panelContent),
            'after'=>false,
            'footer'=>''    //  pagination
        ],
        'columns' => [
            [
                'attribute' => 'entity',
                'filter' => Template::getEntityLabels(),
            ],
            [
                'attribute' => 'name',
                'filter' => false,
            ],
            [
                'attribute' => 'created_at',
                'filter' => false,
            ],
            [
                'attribute' => 'created_by',
                'filter' => false,
                'value' => function ($data) {
                    return Persona::getFullName($data->created_by);
                },
            ],
            [
                'attribute' => 'updated_at',
                'filter' => false,
            ],
            [
                'attribute' => 'updated_by',
                'filter' => false,
                'value' => function ($data) {
                    return Persona::getFullName($data->updated_by);
                },
            ],
            [
                'attribute' => 'flag_main',
                'class' => '\kartik\grid\BooleanColumn',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{delete}',
            ],
        ],
    ]); ?>

    <?php ReBox::end(); ?>
</div>