<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\ComiteZonal;
use app\models\ComiteDistrital;
use kartik\select2\Select2;
use app\models\ComiteLocalidades;
use app\models\ComiteCirculos;
use app\models\ComiteCelulas;
use app\models\Us;
use app\models\ServicosBeneficiados;
use app\models\Distritos;
use yii\widgets\Pjax;

use kartik\grid\EditableColumn;
use app\models\ServicosDream;
use app\models\Utilizadores;
use app\models\Organizacoes;


// $this->title = 'Adolescentes e Jovens';
$this->params['breadcrumbs'][] = $this->title;

$indicatorID = $_GET['iID'];
$enrollmentTime = isset($_GET['eTime'])? str_replace('_', '-',$_GET['eTime']).' meses': "Todos";
$ageBand = isset($_GET['aBand'])? str_replace('_', '-',$_GET['aBand']).' anos': "Todas";

switch($indicatorID){
  case 0: 
          $this->title = 'Beneficiárias no Indicador AGYW_PREV';
          break;
  case 1: 
          $this->title = 'Beneficiárias que completaram o pacote primário completo do DREAMS mas nenhum serviço/intervenção adicional';
          break;
  case 2: 
          $this->title = 'Beneficiárias que completaram o pacote primário completo do DREAMS e pelo menos um serviço/intervenção secundário';
          break;
  case 3: 
          $this->title = 'Beneficiárias que completaram pelo menos um serviço/intervenção do DREAMS mas não o pacote primário completo';
          break;
  case 4: 
          $this->title = 'Beneficiárias que iniciaram um serviço/intervenção do DREAMS mas não o completaram';
          break;
  case 5: 
          $this->title = 'Beneficiárias que completaram uma intervenção baseada em evidências com foco na prevenção da violência';
          break;
  case 6: 
          $this->title = 'Beneficiárias que receberam apoio escolar para manter-se, progredir e/ou matricular-se na escola';
          break;
          break;
  case 7: 
          $this->title = 'Beneficiárias que receberam intervenções sobre abordagens sócio-económicas combinadas';
          break;        
};

  //contabilizar o numero de servicos Core por Beneficiario
  function core($k){

    $cors = ServicosDream::find()->where(['=','core_service',1])->distinct()->all();
    $coreServicos=0;
    foreach($cors as $cor) {
      $coreServicos = $coreServicos+ServicosBeneficiados::find()
      ->where(['=','beneficiario_id',intval($k)])
      ->andWhere(['=', 'servico_id', intval($cor->id)])
      ->andWhere(['=', 'status', 1])
      ->select('servico_id')->distinct()
      ->count();
    }
    return $coreServicos;
  }
  
?>
<div class="beneficiarios-index">

  <h2 align="center">
    <?= Html::img('@web/img/users/bandeira.jpg',['class' => 'img-default','width' => '75px','alt' => 'DREAMS']) ?>   <br>
    <br><?= Html::encode($this->title) ?>
  </h2>
  <h3 align="center">
    Tempo de Registo como Beneficiária DREAMS: <?= Html::encode($enrollmentTime) ?>
    <br>Faixa Etária: <?= Html::encode($ageBand) ?>
  </h3><br/>

  <?php
Pjax::begin(['enablePushState'=>false]); ?>
  <?php
    // Generate a bootstrap responsive striped table with row highlighted on hover
    echo GridView::widget([
        'dataProvider'=> $dataProvider,
        'filterModel' => $searchModel,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],
          [
            'class' => 'kartik\grid\ExpandRowColumn',
            'expandAllTitle' => 'Expand all',
            'collapseTitle' => 'Collapse all',
            'expandIcon'=>'<span class="glyphicon glyphicon-expand"></span>',
            'value' => function ($model, $key, $index, $column) {
              return GridView::ROW_COLLAPSED;
            },
            'detail'=>function ($model, $key, $index, $column) {
              return Yii::$app->controller->renderPartial('_expandagyw.php', ['model'=>$model]);
            },
            'detailOptions'=>[
              'class'=> 'kv-state-enable',
            ],
          ],
          [
            'attribute'=> 'member_id',
            'format' => 'html',
            'label'=>'Código do Beneficiário',
            'value' => function ($model) {
              return  $model->member_id>0 ?  '<font color="#cd2660"><b>'.$model->distrito['cod_distrito'].'/'.$model->member_id.'</b></font>': "-";
            },
          ],
          ['attribute' => 'emp_firstname',
            'label'=>'Nome do Beneficiário',
            'format' => 'raw',
            'value' => function ($model) {
              return  Yii::$app->user->identity->role==20 ?  $model->emp_firstname.' '.$model->emp_middle_name.' '.$model->emp_lastname: "<font color=#261657><b>DREAMS</b></font><span class='label label-success'><font size=+1>".intval($model->member_id)."</font></span>";
            },
          ],
          [
            'attribute'=>'emp_gender',
            'format' => 'raw',
            'filter'=>array("1"=>"M","2"=>"F"),
            'value' => function ($model) {
              return  $model->emp_gender==1 ? '<i class="fa fa-male"></i><span style="display:none !important">M</span>': '<i class="fa fa-female"></i><span style="display:none !important">F</span>';
            },
          ],

          [
            'attribute'=>'ponto_entrada',
            'format' => 'raw',
            'label'=>'PE',
            'filter'=>array("1"=>"US","2"=>"CM","3"=>"ES"),
            'value' => function ($model) {
              if($model->ponto_entrada==1) {return "US";} elseif($model->ponto_entrada==2){return "CM";} else {return "ES";}
            },
          ],
          [
            'attribute'=>'district_code',
            'label'=>'Distrito',
            'filter'=> ArrayHelper::map(Distritos::find()->orderBy('district_name ASC')->asArray()->all(), 'district_code', 'district_name'),
            'value'=>'distrito.district_name',
          ],

          [
            'attribute'=>'emp_birthday',
            'label'=>'Idade',
            'value' => function ($model) {
              if(!$model->emp_birthday==NULL) {
                $newDate = substr(date($model->emp_birthday, strtotime($model->emp_birthday)),-4);

                return  date("Y")-$newDate." anos";
              } else {
                return  $model->idade_anos." anos";
              }
            },
          ],
          [
            'label'=>'#Intervençoes',
            'format' => 'raw',
            'value' => function ($model) {
            $conta = ServicosBeneficiados::find()->where(['beneficiario_id' => $model->id])->distinct()->count();
            if($conta==0){return  '<span class="label label-danger"> <i class="fa fa-medkit"></i>&nbsp;['.$conta.']</span>';}
            elseif ($conta<5) {return  '<span class="label label-warning"> <i class="fa fa-medkit"></i>&nbsp;['.$conta.']</span>';}
            elseif ($conta<10) {return  '<span class="label label-info"> <i class="fa fa-medkit"></i>&nbsp;['.$conta.']</span>';}
            else {
              return  '<span class="label label-success"> <i class="fa fa-medkit"></i>&nbsp;['.$conta.']</span>';}
            },
          'filter'=>array("0"=>"0","5"=>"5"),
          ], 
          [
            'attribute'=>'parceiro_id',
            'label'=>'Org',
            'filter'=> ArrayHelper::map(Organizacoes::find()->orderBy('name ASC')->asArray()->all(), 'id', 'name'),
            'value' => function ($model) {
              $org = Organizacoes::find()->where(['id'=>$model->parceiro_id])->one();
              if($org != null){
                return $org->name;
              }
              return "-";
            },
          ],      
          [
            'attribute'=>'criado_em',
            'label'=>'Data de Registo',
            'format'=>'raw',
            'content'=>function($data){
              return Yii::$app->formatter->asDate($data['criado_em'], "php:Y-m-d");
            }
          ],
          [
            'label'=>'#',
            'format'=>'raw',
            'content'=>function($data){
              return '<a href="'.$data['member_id'].'.dreams"  > <i class="glyphicon glyphicon-eye-open icon-success"></i> </a>';
            }
          ],
        ],
        'pjax'=>Yii::$app->user->identity->role==20 ? true:false, // pjax is set to always true for this demo
        // set your toolbar
        'toolbar'=> [
          ['content'=>Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>'Reset List']).' '.
                      Html::a('<i class="glyphicon glyphicon-export"></i>', ['exportlist'], ['data'=>[  'method' => 'post',
                                                                                                        'params'=>['beneficiaries' => $beneficiaries]],
                                                                                              'data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>'Export List'])
                      
                  ],
              '{toggleData}',
          ],
    
          'panel'=>[
              'type'=>GridView::TYPE_PRIMARY,
            //  'heading'=>$heading,
          ],
          'persistResize'=>true,
    
          'hover'=>true
    ]);
  ?>
<?php Pjax::end(); ?>

<p>
  <?= Html::a('<i class="glyphicon glyphicon-home"></i>', ['site/index'], ['class' => 'btn btn-danger']) ?>
</p>


</div>


<script type="text/javascript">
window.onload = function () {

$(document).ready(function() {});

$.getScript('http://www.chartjs.org/assets/Chart.js',function(){

var data = [
<?php
$tcliservicos= ServicosDream::find()->where(['servico_id'=>1])->andWhere(['=', 'status', 1])->count();
$cliservicos= ServicosDream::find()->where(['servico_id'=>1])->andWhere(['=', 'status', 1])->all();
$cliServices=0;
foreach ($cliservicos as $core) {

  $cliServices = $cliServices+ServicosBeneficiados::find()
//   ->where(['=','beneficiario_id',$model->member_id])
   ->andWhere(['=', 'servico_id', $core->id])
   ->andWhere(['=', 'status', 1])
   ->select('servico_id')->distinct()
   ->count();

?>
,
{
   value: <?= $cliServices?>,
   color: "#F7464A"
}, {
   value: 50,
   color: "#E2EAE9"
}, {
   value: 100,
   color: "#D4CCC5"
}, {
   value: 40,
   color: "#949FB1"
}, {
   value: 120,
   color: "#4D5360"
}
<?php } ?>

]

var options = {
   animation: false
};

//Get the context of the canvas element we want to select
var c = $('#myChart');
var ct = c.get(0).getContext('2d');
var ctx = document.getElementById("myChart").getContext("2d");
/*************************************************************************/
myNewChart = new Chart(ct).Doughnut(data, options);

})



}
</script>
