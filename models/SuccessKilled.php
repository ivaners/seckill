<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "success_killed".
 *
 * @property integer $seckill_id
 * @property integer $user_phone
 * @property integer $state
 * @property string $create_time
 */
class SuccessKilled extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'success_killed';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['seckill_id', 'user_phone'], 'required'],
            [['seckill_id', 'user_phone', 'state'], 'integer'],
            [['create_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'seckill_id' => 'Seckill ID',
            'user_phone' => 'User Phone',
            'state' => 'State',
            'create_time' => 'Create Time',
        ];
    }
}
