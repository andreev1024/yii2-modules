<?php
use andreev1024\templater\components\Config;
use reseed\reWidgets\rebox\ReBox;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = Yii::$app->translate->t('create Template', 'app');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->translate->t('templates', 'app'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$types = [];
foreach(Config::getTypes() as $type) {
    $types[$type] = Yii::$app->translate->t($type, 'app');
}

ReBox::begin([
    'header' => [
        'options' => [
            'class' => 'box-name',
            'title' => Html::encode(Yii::$app->translate->t('template type', 'app'))
        ],
        'icon' => [
            'name' => 'info-circle',
            'framework' => 'fa',
            'options' => [],
            'space' => true,
            'tag' => 'i'
        ],
    ],
]);
?>

<div>
    <?= Html::beginForm() ?>
    <div class="row ">
        <div class="col-xs-12 col-md-4 form-group">
            <?= Html::dropDownList(
                'type',
                null,
                $types,
                [
                    'prompt' => Yii::$app->translate->t('choose template type', 'app'),
                    'class' => 'form-control'
                ]
            ) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-4 form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?= Html::endForm(); ?>
</div>

<?php ReBox::end(); ?>