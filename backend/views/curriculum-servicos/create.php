<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\FaixaEtariaServico */

$this->title = Yii::t('app', 'Create Curriculum Serviços');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Curriculum Serviço'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="curriculum-servico-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>