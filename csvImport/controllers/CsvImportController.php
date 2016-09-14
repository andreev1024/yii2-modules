<?php

/**
 * Controller for CSVImport
 * @author Andreev <andreev1024@gmail.com>
 * @since 2015-04-16
 */

namespace andreev1024\csvImport\controllers;

use Yii;

use yii\web\Controller;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use yii\web\Response;
use andreev1024\csvImport\models\CsvImport;
use yii\web\UploadedFile;

class CsvImportController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['error' ],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'get-mapping','import'
                        ],
                        'allow' => true,
                        'roles' => ['@' ],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-mapping' => ['post'],
                    'import' => ['post']
                ],
            ],

        ];
    }

    /**
     * Return rendered mapping view
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @return  array
     */
    public function actionGetMapping()
    {
        $model = new CsvImport;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = [
            'success' => false,
        ];

        $options = CsvImport::decode($this->checkRequiredAttr('options'));
        $titles = [];

        if (($model->file = UploadedFile::getInstanceByName('file')) &&
            $model->load(yii::$app->request->post()) &&
            $model->validate() &&
            (($handle = fopen($model->file->tempName, "r")))
        ) {
            if (! ($titles = $model->getTitles($handle))) {
                $response['content'] = Yii::$app->translate->t('Can not find columns titles in file');
                return $response;
            }

            if ($duplicated = CsvImport::isDublicated($titles)) {
                $response['content'] = Yii::$app->translate->t("Fields in your file are duplicated: </br>") .
                    $model->formatErrors($duplicated);
                return $response;
            }

            foreach ($options['class'] as $class => $optionsArr) {
                $allClassAttributes = array_keys((new $class)->attributes);
                $attributes[$class] = array_intersect($allClassAttributes, $optionsArr['fields']);
            }

            $response = [
                'success' => true,
                'content' => $this->renderAjax('_mapping', [
                    'attributes' => $attributes,
                    'titles' => $titles,
                ]),
            ];
        } else {
            $response['content'] = $model->getErrors();
        }

        return $response;
    }

    /**
     * Parse csv file, execute import process and return format response
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @return  [type] [description]
     */
    public function actionImport()
    {
        $model = new CsvImport;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = [
            'success' => false,
        ];

        $mapping = Json::decode($this->checkRequiredAttr('mapping'));
        $mapping = ArrayHelper::map($mapping, 'model', 'field', 'csv_field');
        $options = CsvImport::decode($this->checkRequiredAttr('options'));

        if (($model->file = UploadedFile::getInstanceByName('file')) &&
            $model->load(yii::$app->request->post()) &&
            $model->validate()
        ) {
            $parser = new \parseCSV();
            $parser->delimiter = $model->delimiter;
            $parser->enclosure = $model->enclosure;
            $parser->heading = false;
            $parser->limit = $model->limit;

            if ($parser->parse($model->file->tempName)) {
                $data = $model->importCSV($parser->data, $mapping, $options);
                if ($data['errors']) {
                    $response['content'] = $model->formatErrors($data['errors']);
                    return $response;
                } else {
                    $response['success'] = true;
                    return $response;
                }
            } else {
                $response['content'] = $parser->error_info;
                return $response;
            }
        } else {
            $response['content'] = $model->formatErrors($model->getErrors());
            return $response;
        }

        return $response;
    }

    /**
     * checks whether the param in the request
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @param   string $param
     * @return  string
     */
    public function checkRequiredAttr($param)
    {
        if (! $result = yii::$app->request->post($param)) {
            throw new \Exception("`{$param}` is required attribute");
        }
        return $result;
    }
}
