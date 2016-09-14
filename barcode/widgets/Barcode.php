<?php

/**
 * Barcode rendering widget
 * @author Andreev <andreev1024@gmail.com>
 * @since 2015-05-21
 */

namespace andreev1024\barcode\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class Barcode extends Widget
{
    /**
     * Params for view rendering
     * @var array
     */
    public $text;

    /**
     * @var integer
     */
    public $size;

    /**
     * @var string (horizontal|vertical)
     */
    public $orientation;

    /**
     * Can be code128, code39, code25, codabar
     * @var [type]
     */
    public $codeType;

    /**
     * Picture extension
     * @var string
     */
    public $ext = 'png';

    protected $urlParams;

    public function init()
    {
        parent::init();

        if (! $this->text) {
            throw new \Exception("Required attribute 'text' was skipped");
        }

        $urlParams = [
            '/barcode',
            'text' => (string)$this->text
        ];

        $this->size ? $urlParams['size'] = (int)$this->size : null;
        $this->orientation ? $urlParams['orientation'] = (string)$this->orientation : null;
        $this->codeType ? $urlParams['code_type'] = (string)$this->codeType : null;
        $this->ext ? $urlParams['ext'] = (string)$this->ext : null;
        $this->urlParams = $urlParams;
    }

    public function run()
    {
        return Html::img($this->urlParams);
    }
}
