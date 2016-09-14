<?php
/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

$this->title = Yii::t('mediaContentManager', 'Files');
$this->params['breadcrumbs'] = [
    ['label' => Yii::t('mediaContentManager', 'Media Content Manager'), 'url' => ['default/index']],
    $this->title,
];
?>

<div>
    <h1 class="page-header margin_btm_20"><?= $this->title ?></h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'original_name',
            'created_at',
            [
                'label' => Yii::t('mediaContentManager', 'URL'),
                'value' => function (\andreev1024\mediaContentManager\models\File $data) {
                    return $data->getUrl();
                }
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
            ],
        ],
    ]) ?>
</div>
