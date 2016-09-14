<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use yii\widgets\Pjax;
use yii\helpers\Url;

/**
 * @var $this yii\web\View 
 * @var $model modules\cron\models\Cron 
 * @var $form yii\widgets\ActiveForm
 */

?>
<div class="system-currency-form">
    <?php 
        $form = ActiveForm::begin(['id' => 'create_cron_form']); 
        echo $form->field($model, 'minutes'),
             $form->field($model, 'hours'),
             $form->field($model, 'days'),
             $form->field($model, 'months'),
             $form->field($model, 'week'),
             $form->field($model, 'command'),
             $form->field($model, 'active')->checkbox();
    ?>
    <div class="form-group"><?php echo Html::submitButton($model->isNewRecord ? Yii::$app->translate->t('create') : Yii::$app->translate->t('update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?></div>
    <?php ActiveForm::end(); ?>
</div>