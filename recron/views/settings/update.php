<?php
use yii\helpers\Html;

/**
 * @var $this yii\web\View 
 * @var $model modules\cron\models\Cron 
 */

$this->title = Yii::$app->translate->t('update {modelClass}: ',['modelClass' => 'settings cron']).' '. $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->translate->t('settings cron'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::$app->translate->t('update');
?>
<div class="system-currency-update">
    <legend><h3><?php echo Html::encode($this->title) ?></h3></legend>
    <?php echo $this->render('_form', ['model' => $model]) ?>
</div>
