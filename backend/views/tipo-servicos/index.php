<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TipoServicosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipo Servicos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipo-servicos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            'name',
            
            'description',
            'status',
        //    'criado_por',
            // 'actualizado_por',
            // 'criado_em',
            // 'actualizado_em',
            // 'user_location',
            // 'user_location2',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

       <p>
     
        <?= Html::a('<i class="fa fa-plus"></i> Novo Tipo de Serviço', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
