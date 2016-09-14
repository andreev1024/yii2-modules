<?php
use yii\helpers\Html;

$s = $selectors;
?>
<div class="hpanel stats">
    <div class="panel-body list">
        <div class="panel-title"><?= $header ?></div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-xs-12"
                 id = '<?= $s['id']['container'] ?>'
                 data-options = '<?= $encryptedOptions ?>'>
                <?= $content; ?>
            </div>
        </div>
    </div>
</div>