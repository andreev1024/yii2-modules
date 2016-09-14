<?php

namespace andreev1024\mediaContentManager\controllers;

use andreev1024\mediaContentManager\models\UploadImageOption;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * Class DefaultController
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 *
 * @package andreev1024\mediaContentManager\controllers
 */
class DefaultController extends Controller
{
    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access public
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
