 <?php

 use yii\helpers\Html;
 use yii\helpers\ArrayHelper;

$countAllAttributes = [];
foreach ($attributes as $class => $attr_arr) {
    $attributes[$class] = array_combine($attr_arr, $attr_arr);
    $countAllAttributes = array_merge($countAllAttributes, $attr_arr);
}

$countAllAttributes = array_count_values($countAllAttributes);

//  prepare options for group and replace class name to short class name
$groups = array_keys($attributes);
$optionsGroup = [];
foreach ($groups as $oneGroup) {
    $shortClass = explode('\\', $oneGroup);
    $shortClass = end($shortClass);
    $optionsGroup[$shortClass] = ['data' => ['model' => $oneGroup]];
    $attributes[$shortClass] = $attributes[$oneGroup];
    unset($attributes[$oneGroup]);
}

$this->registerCSS('
    .csvi-table th, .csvi-table td {
         border-top: none !important;
    }
');

?>

<table class="table csvi-table">
    <th><?= Yii::$app->translate->t('Csv fields') ?></th>
    <th><?= Yii::$app->translate->t('Attributes') ?></th>
    <th></th>
    <?php foreach ($titles as $oneTitle): ?>
    <tr>
        <td class="col-md-4 col-xs-1">
            <span data-csv-field="<?= $oneTitle ?>"><?= $oneTitle ?></span>
        </td>
        <td class="col-md-4 col-xs-1">
            <?= Html::dropDownList(
                '',
                //  if we have attributes with same names in a few models
                //  then the user must choose it manually
                (isset($countAllAttributes[$oneTitle]) and $countAllAttributes[$oneTitle] > 1) ? null : $oneTitle,
                $attributes,
                [
                    'prompt' => Yii::$app->translate->t('skip this field...'),
                    'groups' => $optionsGroup,
                ]
            ) ?>
        </td>
        <td class="col-md-4 col-xs-1 help-block">
        </td>
    </tr>
    <?php endforeach; ?>
</table>

