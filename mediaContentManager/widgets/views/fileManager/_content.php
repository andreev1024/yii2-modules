<?php
/*
 * @var $mode
 * @var $files
 * @var $pagination
 * @var $directoriesTree
 */

use kartik\icons\Icon;
use andreev1024\mediaContentManager\widgets\FileManager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use andreev1024\mediaContentManager\widgets\FileUploader;

Icon::map($this);

$s = $selectors;

function buildTree($params, $deep = 0)
{
    $padding = $deep * 5;
    $unactiveIcon = Icon::show('folder-close', [], 'bsg');
    $activeIcon = Icon::show('folder-open', [], 'bsg');
    $html = "<ul class = 'list-unstyled' style='padding-left: {$padding}px;'>";
    foreach($params['data'] as $key => $value) {
        $icon = $value['active'] ? $activeIcon : $unactiveIcon;
        $url = Url::to([
            $params['controllerUrl'],
            'startDirectory' => $value['path'],
            'displayMode' => $params['displayMode'],
            'page' => 1,
            'per-page' => $params['pageSize']
        ]);

        $dirClass = $params['selectors']['class']['directory'];
        $html .= "<li class='{$dirClass}' data-url='{$url}'>{$icon}<small>{$key}</small></li>";

        if (!empty($value['nasted'])) {
            $params['data'] = $value['nasted'];
            $html .= buildTree($params, ++$deep);
        }
    }
    $html .= '</ul>';
    return $html;
}

$buildTreeParams = [
    'data' => $directoriesTree,
    'controllerUrl' => $this->context->controller,
    'displayMode' => $displayMode,
    'pageSize' => $pageSize ? : Yii::$app->request->get($pagination->pageSizeParam),
    'selectors' => $s
];

$class = [$s['class']['filesContainer'], 'clearfix'];
if ($displayMode === FileManager::DISPLAY_MODE_ICONS) {
    $view = '_icons';
    $class[] = $s['class']['icons'];
} else {
    $view = '_list';
    $class[] = $s['class']['list'];
}
?>

<div class="row">
    <div class="col-md-3 clearfix">
        <div class="row">
            <div class="col-md-12 margin_btm_20 margin_top_20">
                <?= $this->render('_buttons',[
                    'displayMode' => $displayMode,
                    's' => $s,
                    'startDirectory' => $startDirectory,
                    'mode' => $mode,
                ]) ?>
            </div>
        </div>
        <?php if ($fileUploader): ?>
            <div class="row">
                <div class="col-md-12 margin_btm_20 margin_top_20">
                    <?= FileUploader::widget([
                        'formAction' => $fileUploaderOptions['formAction']
                    ]) ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="well">
                    <?= buildTree($buildTreeParams); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="<?= join(' ', $class) ?>">
            <?= $this->render($view, [
                    'files' => $files,
                    'mode' => $mode,
                    'selectors' => $s,
                    'dataPrfx' => $dataPrfx,
            ]); ?>
        </div>
        <?= LinkPager::widget(['pagination' => $pagination]) ?>
    </div>
</div>