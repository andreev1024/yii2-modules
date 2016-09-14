<?php
use andreev1024\mediaContentManager\widgets\FileManager;
use yii\helpers\Url;

foreach (array_keys(FileManager::getDisplayModeArray()) as $const) {
    $url = array_merge(
        Yii::$app->request->get(),
        [
            $this->context->controller,
            'startDirectory' => $startDirectory,
            'displayMode' => $const,
        ]
    );

    if (isset($url['options'])) {
        unset($url['options']);
    }

    $btns[$const]['url'] = Url::to($url);

    $btns[$const]['classes'][] = $s['class']['ajax'];
    $btns[$const]['classes'][] = ($displayMode === $const) ?
        $s['class']['btnActive'] :
        $s['class']['btnDefault'];
    $btns[$const]['classes'] = implode(' ', $btns[$const]['classes']);
}

?>

<div class="btn-group" role="group">
    <?php foreach(array_keys(FileManager::getDisplayModeArray()) as $const): ?>
        <a href="<?= $btns[$const]['url'] ?>"
           class="btn btn-xs <?= $btns[$const]['classes'] ?>">
            <?= Yii::$app->translate->t($const) ?>
        </a>
    <?php endforeach; ?>
</div>