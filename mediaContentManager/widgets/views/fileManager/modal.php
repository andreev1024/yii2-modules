<?php
use andreev1024\mediaContentManager\models\File;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$s = $selectors;

if ($modalBlock) {
    $this->beginBlock($modalBlock);
    echo ArrayHelper::getValue($this->blocks, $modalBlock);
}
?>

<div id = "<?= $s['id']['container'] ?>" data-options = '<?= Html::encode($encryptedOptions) ?>'>

    <?php
        Modal::begin([
            'toggleButton' => false,
            'id' => $s['id']['modal'],
            'header' => '<h3>' . $header . '</h3>',
            'size' => Modal::SIZE_LARGE
        ]);
        echo $content;
        Modal::end();
    ?>

</div>

<?php
if ($modalBlock) {
    $this->endBlock();
}

echo Html::button(
    Yii::$app->translate->t(
        'Choose ' . ($mode === File::PROCESSING_TYPE_IMAGE ? 'image' : 'file')
    ), [
        'id' => $s['id']['modalTrigger'],
    ]
);
