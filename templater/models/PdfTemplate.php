<?php

namespace andreev1024\templater\models;

use common\models\Language;
use kartik\mpdf\Pdf;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Model for pdf-type templates.
 */
class PdfTemplate extends Template
{
    const TEMPLATE_TYPE = 'pdf';

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    public $header;
    public $footer;
    public $format;
    public $orientation;
    public $show_barcode;
    public $barcode_type;
    public $css;
    public $language;
    public $status_id;
    public $title;

    /**
     * Returns arguments which will be pack into `settings` field.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public function getSettingsArguments()
    {
        return  [
            'header',
            'footer',
            'format',
            'orientation',
            'show_barcode',
            'barcode_type',
            'css',
            'language',
            'status_id',
            'title'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'title' => Yii::$app->translate->t('title', 'app'),
                'header' => Yii::$app->translate->t('header', 'app'),
                'footer' => Yii::$app->translate->t('footer', 'app'),
                'orientation' => Yii::$app->translate->t('orientation', 'app'),
                'format' => Yii::$app->translate->t('format', 'app'),
                'css' => Yii::$app->translate->t('css', 'app'),
                'show_barcode' => Yii::$app->translate->t('show barcode', 'app'),
                'barcode_type' => Yii::$app->translate->t('barcode type', 'app'),
                'language' => Yii::$app->translate->t('language', 'app'),
                'status_id' => Yii::$app->translate->t('status', 'app'),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['format', 'orientation', 'status_id', 'language', 'title'], 'required'],
            [['header', 'footer', 'css'], 'string'],
            ['orientation', 'in', 'range' => array_keys(static::getOrientationsLabel())],
            ['format', 'in', 'range' => array_keys(static::getFormatLabel())],
            ['show_barcode', 'in', 'range' => array_keys(static::getShowBarcodeLabel())],
            ['barcode_type', 'validateBarcodeType'],
            ['language', 'in', 'range' => array_keys(static::getLanguages())],
            ['status_id', 'in', 'range' => array_keys(static::getStatusLabel())],
            [['flag_main'], 'boolean'],
            ['flag_main', 'default', 'value' => Template::FLAG_NONE],
            ['flag_main', 'flagMainValidator'],
            [['title'], 'string', 'max' => 255],
        ]);
    }

    /**
     * Validates flag_main attribute.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $attribute
     * @param $params
     */
    public function flagMainValidator($attribute, $params)
    {
        if (
            (int)$this->$attribute === Template::FLAG_NONE &&
            !Template::getMainTemplate($this->entity, $this->id)
        ) {
            $this->addError(
                $attribute,
                Yii::$app->translate->t('Templates must have the main template. If you want to make another template the main then you must go into another template and enable main template checkbox.', 'app'));
        }
    }

    /**
     * @return array
     */
    public static function getStatusLabel()
    {
        return [
            static::STATUS_ENABLE => Yii::$app->translate->t('enable', 'app'),
            static::STATUS_DISABLE => Yii::$app->translate->t('disable', 'app'),
        ];
    }

    /**
     * @return array
     */
    public static function getOrientationsLabel()
    {
        return [
            Pdf::ORIENT_PORTRAIT => Yii::$app->translate->t('portrait', 'app'),
            Pdf::ORIENT_LANDSCAPE => Yii::$app->translate->t('landscape', 'app'),
        ];
    }

    /**
     * @return array
     */
    public static function getFormatLabel()
    {
        return [
            Pdf::FORMAT_A3 => 'A3',
            Pdf::FORMAT_A4 => 'A4',
            Pdf::FORMAT_LETTER => Yii::$app->translate->t('letter', 'app'),
            Pdf::FORMAT_LEGAL => Yii::$app->translate->t('legal', 'app'),
            Pdf::FORMAT_FOLIO => Yii::$app->translate->t('folio', 'app'),
            Pdf::FORMAT_LEDGER => Yii::$app->translate->t('ledger-l', 'app'),
            Pdf::FORMAT_TABLOID => Yii::$app->translate->t('tabloid', 'app'),
        ];
    }

    /**
     * @return array
     */
    public static function getBarcodeTypeLabel()
    {
        return [
            'ISBN' => 'EAN-13',
            'UPCA' => 'UPC-A',
            'UPCE' => 'UPC-E',
            'EAN8' => 'EAN-8',
            'IMB' => 'Intelligent Mail barcode',
            'RM4SCC' => 'Royal Mail 4-state Customer barcode',
            'KIX' => 'Royal Mail 4-state Customer barcode (Dutch)',
            'POSTNET' => 'POSTNET',
            'PLANET' => 'PLANET',
            'C128A' => 'Code 128',
            'EAN128A' => 'UCC/EAN-128 (GS1-128)',
            'C39' => 'Code 39 (Code 3 of 9)',
            'S25' => 'Standard 2 of 5',
            'I25' => 'Interleaved 2 of 5',
            'C93' => 'Code 93',
            'MSI' => 'MSI',
            'CODABAR' => 'CODABAR',
            'CODE11' => 'Code 11',
        ];
    }

    /**
     * @return array
     */
    public static function getShowBarcodeLabel()
    {
        return [
            static::STATUS_ENABLE => Yii::$app->translate->t('yes', 'app'),
            static::STATUS_DISABLE => Yii::$app->translate->t('no', 'app'),
        ];
    }

    /**
     * Barcode type validator
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-05-07
     * @access  public
     * @param   string $attribute
     * @param   array $params
     * @return  null
     */
    public function validateBarcodeType($attribute, $params)
    {
        $barcodeTypes = $this->getBarcodeTypeLabel();
        if (! isset($barcodeTypes[$this->$attribute])) {
            $this->addError($attribute, Yii::$app->translate->t('invalid barcode type', 'app'));
        }
    }

    /**
     * Get App languages
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-05-07
     * @access  public
     * @return  array
     */
    public static function getLanguages()
    {
        $languages = Language::find()
            ->where(['enable'=>1])
            ->all();

        return ArrayHelper::map($languages, 'code', 'title_en');
    }
}