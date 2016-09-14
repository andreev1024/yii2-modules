<?php

/**
 * Model for CSVImport
 * @author Andreev <andreev1024@gmail.com>
 * @since 2015-04-16
 *
 * Note
 *     -    The process of transferring options:
 *             widget -> html(encrypt) -> controller
 *     -    Attributes in mapping consist of intersect model attributes and attributes from widget config;
 *     -    If you choose `skip` in mapping then this attribute (field) will be ignored;
 *     -    Titles (columns headers) in csv-files cannot be repeated
 *     -    If count($title) !== count($row) -> error
 *
 */

namespace andreev1024\csvImport\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class CsvImport extends \yii\base\Model
{
    /**
     * @var yii\web\UploadedFile
     */
    public $file;

    /**
     * Field delimiter
     * @var string
     */
    public $delimiter = ",";

    /**
     * field enclosure character
     * @var string
     */
    public $enclosure = '"';

    /**
     *  escape character
     * @var string
     */
    public $escape = "\\";

    /**
     * Limits the number of returned rows to the specified amount
     * @var integer
     */
    public $limit = 1000;

    /**
     * Array with errors
     * @var array
     */
    public $errors = [];

    /**
     * The tables involved in the import
     * @var [type]
     */
    private $tables;

    /**
     * Password for encrypting data.
     * This is necessary because options saved on client-side (in JS)
     * @var string
     */
    private static $password = 'andreev';

    public function attributeLabels()
    {
        return ['file' => Yii::$app->translate->t('CSV file import')];
    }

    public function rules()
    {
        return [
            ['file', 'file', 'maxSize' => 1024 * 1024, 'maxFiles' => 1, 'skipOnEmpty' => false,
                'mimeTypes' => [
                    'application/csv',
                    'application/excel',
                    'application/vnd.ms-excel',
                    'application/vnd.msexcel',
                    'application/octet-stream',
                    'text/plain',
                    'text/csv',
                    'text/comma-separated-values',
                ]
            ],
            [['delimiter', 'enclosure', 'escape'], 'safe'],
        ];
    }

    /**
     * Encode data
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @param   mixed $data
     * @return  string
     */
    public static function encode($data)
    {
        return base64_encode(
            mcrypt_encrypt(
                MCRYPT_RIJNDAEL_256,
                md5(static::$password),
                serialize($data),
                MCRYPT_MODE_CBC,
                md5(md5(static::$password))
            )
        );
    }

    /**
     * Decode data
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @param   string $data
     * @return  string
     */
    public static function decode($data)
    {
        return unserialize(
            rtrim(
                mcrypt_decrypt(
                    MCRYPT_RIJNDAEL_256,
                    md5(static::$password),
                    base64_decode($data),
                    MCRYPT_MODE_CBC,
                    md5(md5(static::$password))
                ),
                "\0"
            )
        );
    }

    /**
     * checks whether there are duplicates among the fields
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @param   array $fields
     * @return  array
     */
    public static function isDublicated(array $fields)
    {
        $buffer = [];
        $dublicated = [];
        foreach ($fields as $oneField) {
            if (isset($buffer[$oneField])) {
                $dublicated[] = $oneField;
            }

            $buffer[$oneField] = true;
        }
        return $dublicated;
    }

    /**
     * Return titles from csv-file
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @param   resource $handle
     * @return  array
     */
    public function getTitles($handle)
    {
        $titles = false;
        while ($result = fgetcsv($handle, 0, $this->delimiter, $this->enclosure, $this->escape)) {
            $result = array_filter($result, function ($val) {
                return trim($val) !== '' ? true : false;
            });

            if (! $result) {
                continue;
            } else {
                $titles = $result;
                break;
            }
        }
        return $titles;
    }

    /**
     * Validate and save data
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @param   array $data
     * @param   array $mapping
     * @param   array $options
     * @return  array
     */
    public function importCSV(array $data, array $mapping, array $options)
    {
        $title = null;
        $errors = [];
        $insertData = [];
        $models = [];
        $result = [
            'errors' => []
        ];

        foreach ($data as $rowNumber => $row) {
            $rowNumber++;

            //  skip empty rows
            if ($this->isRowEmpty($row)) {
                continue;
            }

            if (! $title) {
                $title = $row;
                continue;
            }

            if (count($title) !== count($row)) {
                $result['errors'][] = Yii::$app->translate->t('The number of columns is not equal to the number of titles');
                return $result;
            }

            $row = array_combine($title, $row);
            $processData = [];

            foreach ($row as $columnTitle => $value) {
                if (isset($mapping[$columnTitle])) {
                    $mappingData = $mapping[$columnTitle];
                    $modelClass = key($mappingData);
                    $attribute = reset($mappingData);
                    $processData[$modelClass][$attribute] = $value;
                }
            }

            //  skip empty rows after mapping
            if ($this->isRowEmpty($processData)) {
                continue;
            }
            
            foreach ($processData as $modelClass => $attributes) {
                if (! isset($models[$modelClass])) {
                    $models[$modelClass] = new $modelClass;
                }

                $model = $models[$modelClass];
                $this->flushModel($model);
                $model->setAttributes($attributes);
                $this->processUserPassword($model, $attributes);
                $model->validate(array_keys($attributes));

                if ($model->hasErrors()) {
                    $errors[] = $this->formatErrors($model->errors, $rowNumber);
                }

                if (! $errors) {
                    if (! isset($this->tables[$modelClass])) {
                        $this->tables[$modelClass] = $modelClass::tableName();
                    }

                    $insertData[$this->tables[$modelClass]]['fields'] = array_keys($attributes);
                    $insertData[$this->tables[$modelClass]]['data'][] = array_values($attributes);
                }
            }
        }

        if (! $result['errors'] = $errors) {
            $saveResult = $this->save($insertData, $options);
            $result['errors'] = array_merge($result['errors'], $saveResult['errors']);
        }

        return $result;
    }

    /**
     * Save data.
     * Use batch insert and transaction
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @param   array $insertData
     * @param   array $options
     * @return  array
     */
    public function save(array $insertData, array $options)
    {
        Yii::trace('User with id = ' . Yii::$app->user->identity->id . ' start saving data', __METHOD__);

        $result['errors'] = [];
        $tablesWithFk = [];
        $tables = [];
        $db = yii::$app->db;

        foreach ($options['class'] as $modelClass => $optionsArr) {
            if (isset($optionsArr['fk'])) {
                $tablesWithFk[] = [
                    'tableName' => $this->tables[$modelClass],
                    'fk' => $optionsArr['fk'],
                    'modelClass' => $modelClass,
                ];
            } else {
                $tables[] = [
                    'tableName' => $this->tables[$modelClass],
                    'modelClass' => $modelClass,
                ];
            }
        }
        
        $lastInsertId = [];
        $insert = function ($table, &$lastInsertId, $insertData) use ($db) {
            $db->createCommand()->batchInsert(
                $table['tableName'],
                $insertData[$table['tableName']]['fields'],
                $insertData[$table['tableName']]['data']
            )->execute();
            $lastInsertId[$table['modelClass']] = $db->getLastInsertID();
        };

        $transaction = $db->beginTransaction();
        try {
            //  insert data in tables without fk
            foreach ($tables as $oneTable) {
                $insert($oneTable, $lastInsertId, $insertData);
            }

            //  insert data in tables with fk
            if ($tablesWithFk) {
                $i = 0;
                $limit = pow(count($tablesWithFk), 2);
                while ($i < $limit) {
                    foreach ($tablesWithFk as $key => $oneTable) {
                        $canInsert = true;
                        $fkFields = [];
                        foreach ($oneTable['fk'] as $fkData) {
                            $fkFields[] = $fkData['field'];
                            if (! isset($lastInsertId[$fkData['model']])) {
                                $canInsert = false;
                                break;
                            }
                        }

                        if (! $canInsert) {
                            continue;
                        }

                        //  inject foreign keys fields names
                        $insertData[$oneTable['tableName']]['fields'] = array_merge(
                            $insertData[$oneTable['tableName']]['fields'],
                            $fkFields
                        );

                        //  inject foreign keys values
                        $li = 0;
                        foreach ($insertData[$oneTable['tableName']]['data'] as &$row) {
                            foreach ($oneTable['fk'] as $fkData) {
                                $row[] = $lastInsertId[$fkData['model']] + $li;
                            }
                            $li++;
                        }

                        $insert($oneTable, $lastInsertId, $insertData);
                        unset($tablesWithFk[$key]);
                    }
                    $i++;
                }

                if (! empty($tablesWithFk)) {
                    Yii::error('Import error. This tables ' . yii\helpers\VarDumper::dumpAsString($tablesWithFk) .
                        ' refer to some tables which not present in this import', __METHOD__);
                    $result['errors'][] = Yii::$app->translate->t('Import error. See log.');
                    throw new \Exception();
                }
            }

            Yii::trace('User with id = ' . Yii::$app->user->identity->id . ' successful finished saving data', __METHOD__);
            $transaction->commit();

        } catch (\Exception $e) {
            $result['errors'][] = Yii::$app->translate->t('Import error. See log.');
            Yii::error($e->getMessage(), __METHOD__);
            $transaction->rollBack();
        }

        return $result;
    }

    /**
     * Format errors for output
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @param   array $errors
     * @param   integer $rowNumber
     * @param   string $delimiter
     * @return  string
     */
    public function formatErrors($errors, $rowNumber = null, $delimiter = '</br>')
    {
        $message = [];
        $firstItem = reset($errors);

        if ($rowNumber) {
            $message[] = Yii::$app->translate->t('Error in line ') . $rowNumber . ':';
        }

        if (is_array($firstItem)) {
            foreach ($errors as $errorMsg) {
                $message = array_merge($message, $errorMsg);
            }
        } else {
            $message = array_merge($message, $errors);
        }

        return implode($delimiter, $message);
    }

    /**
     * Check whether row empty
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @param   array $row Can be one- or two- dimensional array
     * @return  boolean
     */
    public function isRowEmpty($row)
    {
        $firstItem = reset($row);
        if (is_array($firstItem)) {
            $rowCopy = $row;
            $row = [];
            foreach ($rowCopy as $modelClass => $attributes) {
                $row = array_merge($row, $attributes);
            }
        }

        return !(boolean)array_filter($row, function ($val) {
            return trim($val) !== '' ? true : false;
        });
    }

    /**
     * Flush model attributes
     * @author Andreev <andreev1024@gmail.com>
     * @version ver 1.0 added on 2015-04-16
     * @access  public
     * @param   object $model
     * @param   mixed $default
     * @return  null
     */
    public function flushModel($model, $default = null)
    {
        $attributes = array_keys($model->attributes);
        foreach ($attributes as $attr) {
            $model->$attr = $default;
        }
    }
    
    /**
     * Encode password if model is user
     * @access  public
     * @param   object $model
     * @param   array $attributes
     * @return  null
     */
    public function processUserPassword($model, &$attributes)
    {
        if(isset($model->password_hash)){
            $model->setPassword($model->password_hash);
            $attributes['password_hash'] = $model->password_hash;
        }
    }
}
