<?php

use reseed\reWidgets\realert\ReAlert;
use reseed\reWidgets\rebox\ReBox;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$selector = [
    'id' => [
        'modal' => 'modalVar',
        'modalFileManager' => 'modal',
        'modalTrigger' => 'ptModalTrigger',
        'showBarcode' => 'template-show_barcode',
        'barcodeType' => 'template-barcode_type',
        'templateEntity' => 'template-entity'
    ],
];

ReBox::begin([
    'header' => [
        'options' => [
            'title' => Yii::$app->translate->t($this->title, 'app'),
        ],
        'icon' => [
            'name' => 'info-circle',
        ],
    ],
]);

$form = ActiveForm::begin();

$data =  [
    'model' => $model,
    'title' => $this->title,
    'variableList' => $variableList,
    'selector' => $selector,
    'form' => $form
];?>
    <div class="row">
        <?= $this->render("./{$type}/_header",$data) ?>
    </div>
    <div class="row">
        <?= $this->render("./{$type}/_sidebar",$data) ?>
        <?= $this->render("./{$type}/_content",$data) ?>
    </div>
    <div class="row">
        <?= $this->render("./{$type}/_footer",$data) ?>
    </div>

<?php ActiveForm::end(); ?>
<?php ReBox::end();

echo ReAlert::widget();

$js = <<<JS
    var errorMessage = "{$error}";

    if (errorMessage) {
        new ReAlert({
            message: errorMessage,
            alert_type : "danger",
            width : "300px"
        }).show();
    }
JS;

$this->registerJs($js);



