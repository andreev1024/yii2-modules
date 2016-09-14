<?php
use kartik\icons\Icon;
use andreev1024\mediaContentManager\processors\ImageProcessor;
use yii\helpers\Html;

$s = $selectors;

$formattingFileName = function($fileName) {
    $maxStrLen = 42;
    if(strlen($fileName) > $maxStrLen) {
        $fileName = substr($fileName, 0, $maxStrLen)   . '...';
    }
    return wordwrap($fileName, 15, "<br />\n", true);
};

foreach ($files as $oneFile):?>

    <?= Html::beginTag('div', [
        'class' => $s['class']['fileContainer'] . ' col-md-2',
        'data' => [
            $dataPrfx . '-name' => $oneFile['original_name'],
            $dataPrfx . '-title' => is_null($oneFile['title']) ? '' : $oneFile['title'],
            $dataPrfx . '-description' => is_null($oneFile['description']) ? '' : $oneFile['description'],
            $dataPrfx . '-url' => (isset($oneFile['isImage']) && $oneFile['isImage']) ?
                $oneFile['url'][ImageProcessor::SIZE_TYPE_LARGE] :
                $oneFile['url'],
            $dataPrfx . '-mime_type' => $oneFile['mime_type'],
            $dataPrfx . '-id' => $oneFile['id'],
        ]
    ]); ?>

    <?php if (isset($oneFile['url'][ImageProcessor::SIZE_TYPE_SMALL])): ?>
        <img src="<?= $oneFile['url'][ImageProcessor::SIZE_TYPE_SMALL] ?>" class="<?= $s['class']['fileImg'] ?>">
    <?php else: ?>
        <?= Icon::show('file-o', [], Icon::FA); ?>
    <?php endif; ?>

    <p class="<?= $s['class']['fileName'] ?>">
        <small><?= $formattingFileName($oneFile['original_name']) ?></small>
    </p>

    <?= Html::endTag('div') ?>

<?php endforeach; ?>