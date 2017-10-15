<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Seckill */

$this->title = 'Update Seckill: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Seckills', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->seckill_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="seckill-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
