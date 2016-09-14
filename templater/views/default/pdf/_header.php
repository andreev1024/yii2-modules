<?php

use kartik\icons\Icon;
use yii\bootstrap\Modal;
use yii\helpers\Html;

Icon::map($this);

$this->beginBlock('modalFrame');

$header = '<h3>' . Yii::$app->translate->t('Variable List', 'app') . '</h3>';
Modal::begin([
    'toggleButton' => false,
    'header' => $header,
    'id' => $selector['id']['modal'],
    'size' => Modal::SIZE_LARGE,
]);

if (isset($variableList)) {
    echo $variableList;
}

Modal::end();

$this->endBlock();
?>

<div class="col-xs-12">
    <h4 class="text-info"><?= Html::encode(Yii::$app->translate->t('Template Info', 'app')); ?></h4>
    <ul>
        <li>
            <?= Yii::$app->translate->t('Customize your template using HTML, CSS and <b>twig variables and snippets</b>
                    inset information from an order by inserting Twig variables into your template.', 'app')
            ?>
        </li>
        <li>
            <?= Yii::$app->translate->t(
                'If you want to render variable with html, then you must use it like',
                'app'
            ) . ' <strong>{{$var|raw}}</strong>';
            ?>
        </li>
        <li>
            <?= Yii::$app->translate->t('In footer and header you can use some useful varibles:', 'app') ?>
            <ul>
                <li>
                    <?= '{PAGENO} - ' . Yii::$app->translate->t('Page number', 'app') . ';' ?>
                </li>
                <li>
                    <?= '{nbpg} - ' . Yii::$app->translate->t('Number of pages', 'app') . ';' ?>
                </li>
            </ul>
        </li>
        <?= Yii::$app->translate->t('If footer/header section containing 2 characters "|" - footer/header will be split into three strings and set as content for the left|centre|right parts of the footer/header (e.g. Chapter 1|{PAGENO}|Book Title).', 'app') . ' ' .
        Html::a(
            Yii::$app->translate->t('Read more...', 'app'),
            'http://mpdf1.com/manual/index.php?tid=151'
        );
        ?>
        <li>
            <?=Yii::$app->translate->t('use', 'app');?>
            <b>&lt;pagebreak /&gt;</b>
            <?=Yii::$app->translate->t('for page break', 'app');?>
            .
        </li>
    </ul>
</div>