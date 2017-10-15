<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "seckill".
 *
 * @property integer $seckill_id
 * @property string $name
 * @property integer $number
 * @property string $start_time
 * @property string $end_time
 * @property string $create_time
 */
class Seckill extends \yii\db\ActiveRecord
{
    private $_error;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seckill';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'number'], 'required'],
            [['number'], 'integer'],
            [['start_time', 'end_time', 'create_time'], 'safe'],
            [['name'], 'string', 'max' => 120],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'seckill_id' => 'Seckill ID',
            'name' => 'Name',
            'number' => 'Number',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'create_time' => 'Create Time',
        ];
    }

    /*
	 * 基于mysql验证库存信息
	 * @desc 高并发下会导致超卖
	 *
	 * @author liubin
	 * @date 2017-02-10
	*/
    public function order_check_mysql($gid)
    {


        $gid = intval($gid);

        /*
         * 1：$sql_forlock如果不加事务，不加写锁：
         * 超卖非常严重，就不说了
         *
         * 2：$sql_forlock如果不加事务，只加写锁：
         * 第一个会话读$sql_forlock时加写锁，第一个会话$sql_forlock查询结束会释放该行锁.
         * 第二个会话在第一个会话释放后读$sql_forlock的写锁时，会再次$sql_forlock查库存
         * 导致超卖现象产生
         *
        */
        $sql_forlock = 'select * from seckill where seckill_id = ' . $gid . ' limit 1 for update';
        //$sql_forlock	= 'select * from goods where id = '.$gid .' limit 1';
//        $result = $pdo->query($sql_forlock, PDO::FETCH_ASSOC);
//        $goodsInfo = $result->fetch();
        $goodsInfo = Yii::$app->db->createCommand($sql_forlock)->queryOne();

        if ($goodsInfo['number'] > 0) {
            //去库存
            $gid = $goodsInfo['seckill_id'];
            $sql_inventory = 'UPDATE seckill SET number = number - 1 WHERE seckill_id = ' . $gid;
//            $result = $this->_goodsModel->exect($sql_inventory);
            Yii::$app->db->createCommand($sql_inventory)->execute();
            //创订单

            Yii::$app->db->createCommand()->insert('orders', [
                'goods_id' => $gid,
                'uid' => 1,
                'addtime' => time()
            ])->execute();

            return '购买成功';
        }

        $this->_error = '库存不足';
        return $this->_error;
    }

    public function order_check_transaction($gid)
    {
        $gid = intval($gid);

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            $sql_forlock = 'select * from seckill where seckill_id = ' . $gid . ' limit 1 for update';

            $goodsInfo = $db->createCommand($sql_forlock)->queryOne();
            if ($goodsInfo['number'] > 0) {
                $gid = $goodsInfo['seckill_id'];
                $sql_inventory = 'UPDATE seckill SET number = number - 1 WHERE seckill_id = ' . $gid;
                $db->createCommand($sql_inventory)->execute();
                $db->createCommand()->insert('orders', [
                    'goods_id' => $gid,
                    'uid' => 1,
                    'addtime' => time()
                ])->execute();
                $transaction->commit();
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /*
	 * 基于redis队列验证库存信息
	 * @desc Redis是底层是单线程的,命令执行是原子操作,包括lpush,lpop等.高并发下不会导致超卖
	 *
	 * @author liubin
	 * @date 2017-02-10
	*/
    public function order_check_redis($gid)
    {
        $gid = intval($gid);
        $key = 'goods:' . $gid;
        $count = Yii::$app->redis->lpop($key);
        if (!$count) {
            $this->_error = '库存不足';
            return false;
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            $sql_inventory = 'UPDATE seckill SET number = number - 1 WHERE seckill_id = ' . $gid;
            $db->createCommand($sql_inventory)->execute();
            $db->createCommand()->insert('orders', [
                'goods_id' => $gid,
                'uid' => 1,
                'addtime' => time()
            ])->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            Yii::$app->redis->rpush($key,1);
            $transaction->rollBack();
            throw $e;
        }
    }
}
