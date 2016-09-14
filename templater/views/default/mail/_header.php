<?php
use yii\bootstrap\Modal;

$this->beginBlock('modalFrame');
Modal::begin([
    'toggleButton' => false,
    'size' => Modal::SIZE_LARGE,
    'header' => '<h3>' . Yii::$app->translate->t('variable list', 'app') . '</h3>',
    'id' => $selector['id']['modal'],
]);

if (isset($variableList)) {
    echo $variableList;
}

Modal::end();
$this->endBlock();
?>

<div class="col-xs-12">
    <ul>
        <li>
            <?= Yii::$app->translate->t('How to fetch data by some condition?', 'app') ?>
        </li>
<pre>{% if order.status == "pending" %}
    Order has 'pending' status
{% elseif order.status == "confirm" %}
    Order has 'confirm' status
{% else %}
    Order doesn't have any status
{% endif %}</pre>
        <li>
            <?= Yii::$app->translate->t('How to format date?', 'app') ?>
        </li>
        <pre>{{order.date|date("Y-m-d H:i:s", "Asia/Tokyo")}}</pre>
    </ul>
</div>