<?php
use yii\helpers\Url;

$sessionHandlerUrl = Url::to(['session-handler']);

$this->registerJs('
    $("#' . $selector['id']['modalTrigger'] . '").click(function(event){

        if( !$("#' . $selector['id']['templateEntity'] . '").val()){
            return false;
        }

        $("#' . $selector['id']['modal'] . '").modal("toggle");
    });

    $("#' . $selector['id']['showBarcode'] . '").change(function(event){
        if($(this).val() == 0){
            $("#' . $selector['id']['barcodeType'] . '").prop("disabled", "disabled");
        } else {
            $("#' . $selector['id']['barcodeType'] . '").prop("disabled", false);
        }
    });

    $(document).on("change", "#' . $selector['id']['templateEntity'] . '", function() {
        var value = $(this).val();
        $.general.sendPost({entityStateKey: value},"' . $sessionHandlerUrl . '", function(){
            $.pjax.reload("#pjaxVariablesContainer");
        });
    });

    var tabId = $("[data-toggle=\'tab\']").first().attr("href");
    $("#code-contents").appendTo(tabId);

    $("[data-toggle=\'tab\']").click(function(){
        tabId = $(this).attr("href");
        var lastChar = tabId.substr(tabId.length - 1);

        if($(tabId).children().length == 0){
            $("#code"+lastChar).removeClass("code-absolute");
            $("#code"+lastChar).appendTo(tabId);
        }
    });
');

$this->registerCss(<<<'CSS'
textarea#template-template{
    width:100%;
    border: 1px solid #eee;
}

.CodeMirror {
    height: 490px!important;
}

.code-relative{
    position: relative;
    z-index: 100;
}

.code-absolute {
    position: absolute;
    bottom: -15px;
    left: 0;
    z-index: 10;
}
CSS
);