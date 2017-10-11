<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "test".
 *
 * @property integer $id
 * @property string $name
 * @property string $body
 */
class Test extends \yii\db\ActiveRecord
{
    const CREATE = 'create';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'body'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['body'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'body' => 'Body',
        ];
    }

    public function scenarios()
    {
        $scenarios=parent::scenarios();
        $scenarios[self::CREATE] = ['name'];
        return $scenarios;
    }
}
