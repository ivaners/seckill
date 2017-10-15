<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Seckill */

$this->title = 'Create Seckill';
$this->params['breadcrumbs'][] = ['label' => 'Seckills', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seckill-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
