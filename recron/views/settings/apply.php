<?php

use yii\helpers\Html;
use reseed\reWidgets\rebox\ReBox;
use kartik\icons\Icon;

$this->title = Yii::$app->translate->t('apply crontab');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->translate->t('settings cron'), 'url' => ['index']];
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

        <div class="alert alert-<?php echo !$returnVar ? 'success' : 'danger'; ?>" role="alert">
            <b><?php echo !$returnVar ? 'Success!' : 'Errors!'; ?></b>
            <?php echo nl2br(implode(PHP_EOL, $output)); ?>
        </div>
        
    <?php ReBox::end(); ?>
</div>