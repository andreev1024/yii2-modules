<?php
/**
 *  @author Andreev <andreev1024@gmail.com>
 *  @since 2015-04-16
 *
 *  CSV import can import data from csv files to DB.
 *
 *  How it use:
 *
 *  <?= CsvImportWidget::widget([
 *      'options' => [
 *          'class' => [
 *              \common\models\Organization::className() => [
 *                  'fields' => [
 *                      'org_name',
 *                      'org_name_kana',
 *                      'org_name_latin',
 *                      'website',
 *                      'phone',
 *                      'fax',
 *                  ],
 *              ],
 *              \common\models\AddressOrg::className() => [
 *                  'fields' => [
 *                      'serial_number',
 *                      'country',
 *                      'country_short',
 *                      'area_level_1',
 *                      'area_level_2',
 *                      'locality',
 *                      'sublocality',
 *                      'street_number',
 *                      'premise',
 *                      'address_other',
 *                      'postal_code',
 *                      'route',
 *                      'floor',
 *                      'room',
 *                      'latitude',
 *                      'longitude',
 *                  ],
 *                  'fk' => [
 *                      [
 *                          'model' => \common\models\Organization::className(),
 *                          'field' => 'org_id'
 *                      ]
 *                  ]
 *              ]
 *          ],
 *      ]
 *  ]) ?>
 *
 *  In this example we have 2 models: Organization (parent model) and AddressOrg (child model).
 *  `Fields` defines allowed mapping fields. Attributes which don't specified here will not be saved.
 *  `fk` is array, which contain foreign keys options. This array can contain one or more arrays.
 *
 */
namespace andreev1024\csvImport\widgets;

use Yii;
use yii\base\Widget;

use \csvImport\models\CsvImport;

class CsvImportWidget extends Widget {
    //put your code here
    public $model;
    public $sampleFile;
    public $options;

    public function init(){
        parent::init();

        if(empty($this->model)){
            $this->model = new CsvImport();
        }

        if(empty($this->sampleFile)){
            $this->sampleFile = 'organization_example.csv';
        }
    }

    public function run(){
        return $this->render(
            "csvImport",
            [
                'csvImportModel' => $this->model,
                'csvSampleFile' => $this->sampleFile,
                'options' => $this->options,
            ]);
    }
}
