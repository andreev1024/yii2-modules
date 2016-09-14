<?php
use yii\helpers\Html;
use reseed\reWidgets\rebox\ReBox;
/**
 * @var $this yii\web\View 
 * @var $model modules\cron\models\Cron 
 */

$this->title = Yii::$app->translate->t('add cron task');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->translate->t('settings cron'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-cron-create">
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
    
    <?php echo $this->render('_form', ['model' => $model]); ?>
    
    <?php ReBox::end(); ?>
</div>
