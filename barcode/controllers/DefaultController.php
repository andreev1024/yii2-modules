<?php

namespace andreev1024\barcode\controllers;

/**
 * Controller for barcode rendering
 * @author Andreev <andreev1024@gmail.com>
 * @since 2015-05-21
 */

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use andreev1024\barcode\components\Barcode;

class DefaultController extends Controller
{
    public $defaultAction = 'view';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'view'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-variable-list' => ['get'],
                ],
            ],
        ];
    }

    /**
     * Render barcode
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-05-22
     * @access  public
     * @param   string $text
     * @param   string $size
     * @param   string $orientation
     * @param   string $code_type
     * @param   string $ext
     * @return  mixed
     */
    public function actionView($text, $size = null, $orientation = null, $code_type = null, $ext = null)
    {
        $params = [
            'text' => $text,
            'size' => $size,
            'orientation' => $orientation,
            'code_type' => $code_type
        ];
        $result = (new Barcode($params))->create()->view($ext);
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add($result['headers'][0], $result['headers'][1]);
        Yii::$app->response->content = $result['body'];
    }
}
