<?php

namespace atuin\engine\widgets\grid;

use atuin\engine\helpers\Hooks;
use Yii;


/**
 * Class GridView
 * @package atuin\engine\widgets\grid
 * 
 * Kartik GridView extension that lets the use of Hooks to add new data columns into the tables.
 * 
 */
class GridView extends \kartik\grid\GridView
{


    public $export = FALSE;

    /**
     * Hook Name that will be called to get the extra GridView data that we will add into the table;
     * if not defined, nothing will be called.
     *
     * @var string
     */
    public $gridHookName;

    /**
     * Array of possible categories that will be used to translate the different column labels added from the Hooks
     *
     * @var array
     */
    public $i18Categories = ['admin'];


    public function init()
    {
        if (!is_null($this->gridHookName)) {
            $this->getHookData();
        }

        parent::init();
    }

    /**
     * Calls the hook defined in $gridHookName and adds the data returned
     * by its listeners into the GridView.
     * 
     * The return format of the Hook Listener Methods should be:
     * 
     *  [
     *      modelPrimaryKeyId   => [ "columnName" => "columnData"],
     *      12                  => [ "User Name" => "John Johnson"]
     *  ]
     * 
     * The column data should come already formatted because it's impossible to determine
     * which type of formatting should be used in each row.
     * 
     */
    protected function getHookData()
    {
        // 1 - Check if the last column it's an Action Column. If it is, then
        // we will pop the column and add it later.

        $actionColumn = NULL;

        if (is_array(end($this->columns)) && array_key_exists('class', end($this->columns)) 
            && end($this->columns)['class'] == 'yii\grid\ActionColumn') {
            $actionColumn = array_pop($this->columns);
        }

        // 2 - Reformat the Hook Data Columns in order to make them more easy to handle in the columns[] GridView Array

        // Getting the basic data to send it to the hook listeners 
        $models = array_values($this->dataProvider->getModels());

        // Calling the hook listeners
        $hookDataArrays = Hooks::triggerAction($this->gridHookName, $models);

        $reformatedDataArrays = [];

        foreach ($hookDataArrays as $hookData) {
            foreach ($hookData as $key => $rawData) {
                foreach ($rawData as $column => $data) {
                    $reformatedDataArrays[$column][$key] = $data;
                }
            }
        }


        // 3 - Foreach different column we will add it manually in the GridView 

        foreach ($reformatedDataArrays as $column => $dataColumn) {
            $this->columns[] = [
                'attribute' => $column,
                'label' => Yii::t('admin', $column),
                'value' =>
                    function ($model) use ($dataColumn) {
                        if (array_key_exists($model->id, $dataColumn)) {
                            return $dataColumn[$model->id];
                        }
                        return NULL;
                    },
            ];
        }

        // 3 - Add the Action Column in case it was defined for this GridView

        if (!is_null($actionColumn)) {
            $this->columns[] = $actionColumn;
        }

    }

}