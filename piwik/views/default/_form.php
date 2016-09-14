<?php
use andreev1024\piwik\models\TrackingCode;
use reseed\reWidgets\rebox\ReBox;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="tracking-code-index">
    <?php
    ReBox::begin([
        'header' => [
            'options' => [
                'class' => 'box-name',
                'title' => Html::encode($this->title),
            ],
            'icon' => [
                'name' => 'table',
                'framework' => 'fa',
                'options' => [],
                'space' => true,
                'tag' => 'i',
            ],
        ],
    ]);

    $form = ActiveForm::begin(); ?>

    <h4><?= Yii::$app->translate->t('General tracking options') ?>:</h4>
    <?= $form->field($model, 'status')->checkbox() ?>

    <?php if(isset($isUpdate) && $isUpdate): ?>
        <?= $form
            ->field($model, 'scope')
            ->hiddenInput()
            ->label(false);
        ?>
    <?php else: ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form
                    ->field($model, 'scope')
                    ->dropDownList(TrackingCode::getScopesArray())
                ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form
                ->field($model, 'trackerUrl')
                ->textInput(['placeholder' => 'subdomen.domen.net'])
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form
                ->field($model, 'siteId')
                ->textInput(['placeholder' => '1'])
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php
            $field = $form
                ->field($model, 'mainSiteDomen')
                ->textInput(['placeholder' => 'app.net']);
            $field->enableClientValidation = false;
            echo $field;
            ?>
        </div>
    </div>

    <?= $form
        ->field($model, 'trackVisitorsAcrossAllSubdomains')
        ->checkbox()
        ->hint(TrackingCode::getHint('trackVisitorsAcrossAllSubdomains'))
    ?>

    <?= $form
        ->field($model, 'prependTitle')
        ->checkbox()
        ->hint(TrackingCode::getHint('prependTitle'))
    ?>

    <?= $form
        ->field($model, 'notCountedAliasLink')
        ->checkbox()
        ->hint(TrackingCode::getHint('notCountedAliasLink'))
    ?>

    <?= $form
        ->field($model, 'disableCookies')
        ->checkbox()
        ->hint(TrackingCode::getHint('disableCookies'))
    ?>

    <h4><?= Yii::$app->translate->t('Image tracking options') ?>:</h4>
    <?= $form
        ->field($model, 'imageTracking')
        ->checkbox()
        ->hint(TrackingCode::getHint('imageTracking'))
    ?>

    <?= Html::submitButton(Yii::$app->translate->t('Save'), $options = ['class' => 'btn btn-success'] ) ?>

    <?php ActiveForm::end(); ?>
    <?php ReBox::end(); ?>
</div>