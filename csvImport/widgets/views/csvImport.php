<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\icons\Icon;
use yii\bootstrap\Modal;
use \csvImport\models\CsvImport;

Icon::map($this);

$prefix = 'csvi_';
$selector = [
    'id' => [
        'wrapper' => 'import',
        'response' => 'response',
        'modal' => 'modal',
        'uploadBtn' => 'uploadBtn',
        'mappingBtn' => 'mappingBtn',
        'collapse' => 'collapseElement'
    ],
];
foreach ($selector as &$selectorArr) {
    foreach ($selectorArr as &$oneSelector) {
        $oneSelector = $prefix . $oneSelector;
    }
}

Modal::begin([
    'header' => '<h2>Mapping</h2>',
    'toggleButton' => false,
    'id' => $selector['id']['modal'],
]);
Modal::end();
?>
<div id="<?=$selector['id']['wrapper']?>">
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<?= $form->field($csvImportModel, 'file')->fileInput()->label(false) ?>
</div>
<?= Html::button(Yii::$app->translate->t('Upload'), [
    'class' => 'btn btn-primary',
    'disabled' => true,
    'id' => $selector['id']['uploadBtn'],
])
?>

<?= Html::button(Yii::$app->translate->t('Mapping'), [
    'class' => 'btn btn-primary',
    'disabled' => true,
    'id' => $selector['id']['mappingBtn'],
    'data' => [
        'toggle' => 'modal',
        'target' => '#' . $selector['id']['modal'],
    ]
]) ?>

<?= ' ', Html::a(Icon::show('file') . ' ' . Yii::$app->translate->t('Download example csv file'), \yii::$app->request->baseUrl . '/uploads/' . $csvSampleFile, ['class' => 'btn'])
?>

<?= Html::button(Yii::$app->translate->t('Options'), [
    'class' => 'btn',
    'data' => [
        'toggle' => 'collapse',
        'target' => '#' . $selector['id']['collapse'],
    ],
    'aria-expanded' => 'false',
    'aria-controls' => $selector['id']['collapse'],
]) ?>

    <div class="collapse" id="<?= $selector['id']['collapse'] ?>">
        <div class="well row">
            <div class="col-md-1 col-xs-6">
                <?= $form->field($csvImportModel, 'delimiter') ?>
            </div>
            <div class="col-md-1 col-xs-6">
                <?= $form->field($csvImportModel, 'enclosure') ?>
            </div>
            <div class="col-md-1 col-xs-6">
                <?= $form->field($csvImportModel, 'escape') ?>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>

    <div id='<?= $selector['id']['response'] ?>'>
        <div class='help-block'></div>
    </div>

<?php
$this->registerJs(
    '   /**
    *   Send file and get mapping
    *   Load only one file (not multiple)
    */
    $("body").on("change", "#' . $selector['id']['wrapper'] . ' [type=file]", function(evt){

        if (! this.files.length) {
            return false;
        }

        var formdata = new FormData(),
            delimiter = $("[name=\'CsvImport[delimiter]\']"),
            enclosure = $("[name=\'CsvImport[enclosure]\']"),
            escape = $("[name=\'CsvImport[escape]\']")
            options = \'' . CsvImport::encode($options) . '\';

        formdata.append("file", this.files[0]);
        formdata.append("options", options);
        formdata.append(delimiter.attr("name"), delimiter.val());
        formdata.append(enclosure.attr("name"), enclosure.val());
        formdata.append(escape.attr("name"), escape.val());

        $.ajax({
            url: "' . Url::to(['/utility/csv-import/get-mapping']) . '",
            type: "POST",
            data: formdata,
            processData: false,
            contentType: false,
        })
        .done(function(data) {
            if (data.success) {
                $("#' . $selector['id']['modal'] . ' .modal-body").html(data.content);
                $("#' . $selector['id']['uploadBtn'] . '").attr("disabled", false);
                $("#' . $selector['id']['mappingBtn'] . '").attr("disabled", false);
                $("#' . $selector['id']['response'] . '").removeClass("has-error");
                $("#' . $selector['id']['response'] . ' .help-block").html("");
            } else {
                $("#' . $selector['id']['response'] . '").addClass("has-error");
                $("#' . $selector['id']['response'] . ' .help-block").html(data.content);
            }

        })
        .fail(function(data) {
            console.log("server error!");
        });

    });

    /**
    *   Send file and success/errors
    */
    $("#' . $selector['id']['uploadBtn'] . '").on("click", function(evt) {
        evt.preventDefault();
        evt.stopImmediatePropagation();

        var inputFile = $("#' . $selector['id']['wrapper'] . ' input[type=file]");
            inputFile = inputFile[0];

        if (! inputFile.files.length) {
            return false;
        }

        var formdata = new FormData(),
            delimiter = $("[name=\'CsvImport[delimiter]\']"),
            enclosure = $("[name=\'CsvImport[enclosure]\']"),
            escape = $("[name=\'CsvImport[escape]\']"),
            options = \'' . CsvImport::encode($options) . '\',
            mapping = [];


        $("#' . $selector['id']['modal'] . ' tr").each(function(){
            var item = {};
            item.field = $("option:selected" ,$(this)).val();
            if(item.field) {
                item.csv_field = $("[data-csv-field]" ,$(this)).data("csv-field");
                item.model = $("option:selected" ,$(this)).closest("optgroup").data("model");
                mapping.push(item);
            }
        });

        formdata.append("file", inputFile.files[0]);
        formdata.append("mapping", JSON.stringify(mapping));
        formdata.append(delimiter.attr("name"), delimiter.val());
        formdata.append(enclosure.attr("name"), enclosure.val());
        formdata.append(escape.attr("name"), escape.val());
        formdata.append("options", options);

        $.ajax({
            url: "' . Url::to(['/utility/csv-import/import']) . '",
            type: "POST",
            data: formdata,
            processData: false,
            contentType: false,
        })
        .done(function(data) {
            if (data.success) {
                $("#' . $selector['id']['response'] . '").removeClass("has-error");
                $("#' . $selector['id']['response'] . ' .help-block").html("' . Yii::$app->translate->t('CSV import accomplished') . '");
                window.location.reload();
            } else {
                $("#' . $selector['id']['response'] . '").addClass("has-error");
                $("#' . $selector['id']['response'] . ' .help-block").html(data.content);
            }
        })
        .fail(function(data) {
            console.log("server error!");
        });
    });

    /**
    *   search duplicates in mapping
    */
    $("#' . $selector['id']['modal'] . '").on("change", "select", function() {
        if(! $(this).val()) {
            return false;
        }

        var repeats = {};
        $("#' . $selector['id']['modal'] . ' tr").each(function(){
            var
                field = $("option:selected", $(this)).val(),
                table = $("option:selected", $(this)).closest("optgroup").data("model")
                key = table + "." + field;

            repeats[key] = (repeats[key])? repeats[key] + 1 : 1 ;
        });

        var dublicity = false;
        $("#' . $selector['id']['modal'] . ' tr").each(function(){
            var
                field = $("option:selected", $(this)).val(),
                table = $("option:selected", $(this)).closest("optgroup").data("model")
                key = table + "." + field;

            if(repeats[key] > 1) {
                $(".help-block", $(this)).html("' . Yii::$app->translate->t('fields are duplicated') . '");
                $(".help-block", $(this)).addClass("txt-danger");
                dublicity = true;
            } else {
                $(".help-block", $(this)).html("");
                $(".help-block", $(this)).removeClass("txt-danger");
            }

            $("#' . $selector['id']['uploadBtn'] . '").attr("disabled", dublicity);

        });
    });
');
?>
