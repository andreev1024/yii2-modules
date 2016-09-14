<?php
$this->registerJs('
    $("#' . $selector['id']['modalTrigger'] . '").click(function(event){
        $("#' . $selector['id']['modal'] . '").modal("toggle");
    });
');
?>