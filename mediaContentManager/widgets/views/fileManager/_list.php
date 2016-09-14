<?php
use kartik\icons\Icon;
use andreev1024\mediaContentManager\processors\ImageProcessor;
use yii\helpers\Html;

$s = $selectors;
?>

<ul class = 'list-unstyled'>
<?php
    $html = '';
    foreach ($files as $oneFile):
        $icon = isset($oneFile['isImage']) && $oneFile['isImage'] ? 'file-image-o' : 'file-o';
        $html .= '<li>';
        $html .= '<small>';
        $html .= Html::a(
            Icon::show($icon, [], Icon::FA) . ' ' . $oneFile['original_name'],
            '#',
            [
                'data' => [
                    $dataPrfx . '-name' => $oneFile['original_name'],
                    $dataPrfx . '-title' => is_null($oneFile['title']) ? '' : $oneFile['title'],
                    $dataPrfx . '-description' => is_null($oneFile['description']) ? '' : $oneFile['description'],
                    $dataPrfx . '-url' => (isset($oneFile['isImage']) && $oneFile['isImage']) ?
                        $oneFile['url'][ImageProcessor::SIZE_TYPE_LARGE] :
                        $oneFile['url'],
                    $dataPrfx . '-mime_type' => $oneFile['mime_type'],
                    $dataPrfx . '-id' => $oneFile['id'],
                ],
                'class' => $s['class']['fileName'] . ' ' . $s['class']['fileContainer'],
            ]
        );
        $html .= '</small>';
        $html .= '</li>';
    endforeach;
    ?>
    <?= $html ?>
</ul>