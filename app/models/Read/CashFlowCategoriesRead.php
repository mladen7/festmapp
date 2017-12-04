<?php
namespace Models\Read;

use Phalcon\Mvc\Model;

use Models\CashFlowCategory;
/**
 * Created by PhpStorm.
 * User: ProudSourceIT
 * Date: 12.5.2016
 * Time: 11:04
 */
class CashFlowCategoriesRead extends Model
{

    public $categoryList;

    public function executeRead()
    {
        $listOfCategories = array();

        $categories = CashFlowCategory::find();

        foreach ($categories as $cats) {
            $data['id'] = $cats->id;
            $data['name'] = $cats->name;
            $data['pic'] = $cats->pic;

            array_push($listOfCategories, $data);
        }

        if (sizeof($listOfCategories) != 0)
            $this->categoryList = $listOfCategories;
    }

    public function jsonSerialize()
    {
        return $this->categoryList;
    }

    public function isEmpty()
    {
        return (sizeof($this->categoryList) == 0);
    }
}