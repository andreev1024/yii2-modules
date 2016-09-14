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