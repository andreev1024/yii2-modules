<?php
use yii\helpers\Url;

$sessionHandlerUrl = Url::to(['session-handler']);

$this->registerJs('
    $("#' . $selector['id']['modalTrigger'] . '").click(function(event){
        $("#' . $selector['id']['modal'] . '").modal("toggle");
    });

    $(document).on("change", "#' . $selector['id']['templateEntity'] . '", function() {
        var value = $(this).val();
        $.general.sendPost({entityStateKey: value},"' . $sessionHandlerUrl . '", function(){
            $.pjax.reload("#pjaxVariablesContainer");
        });
    });
');
?>