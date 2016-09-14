<?php
/**
 * @var \yii\web\View $this
 * @var \andreev1024\mediaContentManager\models\File $model
 */

$this->title = $model->original_name;
$this->params['breadcrumbs']= [
    ['label' => Yii::t('mediaContentManager', 'Media Content Manager'), 'url' => ['default/index']],
    ['label' => Yii::t('mediaContentManager', 'Files'), 'url' => ['files/index']],
    $this->title,
];
?>

<div class="file-view">

    <h1 class="page-header margin_btm_20"><?= \yii\helpers\Html::encode($this->title) ?></h1>

    <p>
        <?= \yii\helpers\Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= \yii\helpers\Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'original_name',
            'created_by' => ['label' => 'User ID', 'attribute' => 'created_by'],
            'created_at',
            [
                'attribute' => 'URL',
                'value' => $model->getUrl(),
            ],
        ],
    ]) ?>

</div>
