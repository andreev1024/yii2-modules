<?php
/**
 * @var \yii\web\View $this
 */

$this->title = Yii::t('mediaContentManager', 'Media Content Manager');
$this->params['breadcrumbs'] = [$this->title];
?>


<div class="file-view">

    <h1 class="page-header margin_btm_20"><?= $this->title ?></h1>

    <?= \yii\bootstrap\Nav::widget([
        'items' => [
            ['label' => Yii::t('mediaContentManager', 'Home'), 'url' => ['default/index']],
            ['label' => Yii::t('mediaContentManager', 'Files'), 'url' => ['files/index']],
            ['label' => Yii::t('mediaContentManager', 'Image Options'), 'url' => ['image-options/index']],
        ],
    ]) ?>

</div>
