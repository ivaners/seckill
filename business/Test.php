<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2017/10/8
 * Time: 08:30
 */

namespace app\business;


use yii\base\Model;
use app\models\Test as Tests;
class Test extends Model
{
    public $name;
    public $body;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = call_user_func([new Tests(),'rules']);
        return $rules;
    }

    public function save()
    {
        $model=Tests::findOne(1);
        $model->scenario='create';
        $model->attributes=$this->attributes;
        $model->save();
    }
}