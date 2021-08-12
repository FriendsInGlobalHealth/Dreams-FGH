<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\form\ActiveForm;


use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\Utilizadores;
use app\models\ReferenciasDreams;

//05 11 2018 Actualizado em Pemba
use app\models\ReferenciasServicosReferidos;
use app\models\ServicosBeneficiados;
use app\models\Organizacoes;
//use app\models\Provincias;
use app\models\Distritos;
//use app\models\Beneficiarios;





use common\models\User;
use dektrium\user\models\Profile;
use kartik\widgets\DepDrop;

use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ReferenciasDreamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


//seleciona todos os utilizadores da sua provincia

if (isset(Yii::$app->user->identity->provin_code)&&Yii::$app->user->identity->provin_code>0)
{
$provs=User::find()->where(['provin_code'=>(int)Yii::$app->user->identity->provin_code])->asArray()->all();
$prov = ArrayHelper::getColumn($provs, 'id');

$dists=Distritos::find()->where(['province_code'=>(int)Yii::$app->user->identity->provin_code])->asArray()->all();
$dist=ArrayHelper::getColumn($dists, 'district_code');




$users=ReferenciasDreams::find()->where(['IN','criado_por',$prov])->andWhere(['=', 'status', 1])->asArray()->all();
$users2=ReferenciasDreams::find()->where(['IN','notificar_ao',$prov])->andWhere(['=', 'status', 1])->asArray()->all();
//added on 05 11 2018
$orgs=Organizacoes::find()->where(['IN','distrito_id',$dist])->where(['=', 'status', 1])->orderBy('parceria_id ASC')->asArray()->all();

} else {
$users=ReferenciasDreams::find()->asArray()->where(['=', 'status', 1])->all();
$users2=ReferenciasDreams::find()->asArray()->where(['=', 'status', 1])->all();
$orgs=Organizacoes::find()->where(['=', 'status', 1])->orderBy('parceria_id ASC')->asArray()->all();
}


$orgs=Organizacoes::find()->where(['=', 'status', 1])->orderBy('parceria_id ASC')->asArray()->all();
$org=ArrayHelper::getColumn($orgs, 'id');


$ids = ArrayHelper::getColumn($users, 'criado_por');
$notify_to = ArrayHelper::getColumn($users2, 'notificar_ao');


$this->title = Yii::t('app', 'Referências e Contra-Referências Pendentes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="referencias-dreams-index">
  <h2 align="center">
  <?= Html::img('@web/img/users/bandeira.jpg',['class' => 'img-default','width' => '75px','alt' => 'DREAMS']) ?>   <br>
  <br>Lista de <?= Html::encode($this->title) ?>
</h2>

    <div class="row">

      <div class="col-lg-6">
        <div class="panel panel-primary">
          <div class="panel-heading"> 
              <b><span class="glyphicon glyphicon-check" aria-hidden="true"></span> FILTROS</b>
          </div>
          <div class="panel-body">

            <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

          </div>
        </div>

      </div>
      <div class="col-lg-6">

      <?php $form = ActiveForm::begin(); ?>

          <div class="panel panel-success">
            <div class="panel-heading"> 
                <b><span class="glyphicon glyphicon-check" aria-hidden="true"></span> Detalhes do cancelamento das Referencias</b>
            </div>
            <div class="panel-body">


              <div class="row">
                <div class="col-lg-6">  
                  <?= $form->field($model, 'cancel_reason')->dropDownList( 
                      ['' => '', '1' => ' Serviço não provido nos últimos 6 meses', 
                        '2' => ' Beneficiária não encontrada',  
                        '3' => ' Abandono',  
                        '4' => ' Beneficiária recusou o serviço', 
                        '5' => ' Outro Motivo'
                      ]) ?>
                  

                </div>

                  <div class="col-lg-6"> <?= $form->field($model, 'other_reason')->textInput() ?></div>
              </div>
                               
                <div class="form-group pull-right">
                    <?= Html::submitButton('SALVAR' , ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
        </div>


      </div>
    </div>

          




    <table width="100%"   class="table table-bordered  table-condensed">
      <tr>
        <td   bgcolor="#261657" bgcolor="" align="center"><font color ="#fff" size="+1"><b>

          <span class="fa fa-exchange" aria-hidden="true"></span> Selecione as Referências e Contra-Referências Pendentes por Cancelar
            </b></font></td>
        </tr>
      <tr>
        <td   bgcolor="#808080" align="center">
          <font color="#fff" size="+1"><b>
          </b></font>    </td>
        </tr>
      </table>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

              ['attribute'=> 'id',
                'format' => 'raw',
                'label' => 'Select',
                'value' => function ($model) {
                
                  return Html::checkBox($model->id, false,[$model->id]);
                },
              ],	  


            ['attribute'=> 'criado_em',
          'format' => 'html',
          'value' => function ($model) {
            
          return $model->criado_em;
          // return Yii::$app->formatter->asDate($model->criado_em, 'yyyy-MM-dd');
          },
          ],		

			
            'nota_referencia',
        

            ['attribute'=> 'beneficiario_id',
            'format' => 'html',
            'label'=>'Código do Beneficiário',
            'value' => function ($model) {
            if(isset($model->beneficiario->distrito['cod_distrito'])&&$model->beneficiario->distrito['cod_distrito']>0) {
            return  $model->beneficiario_id>0 ?  '<font color="#cd2660">'.$model->beneficiario->distrito['cod_distrito'].'/'.$model->beneficiario['member_id'].'</font>': '-';
            }
            {return '-'.'/'.$model->beneficiario['member_id'];}
            },
            ],

          
			 [
            'attribute'=>'referido_por',
            'format' => 'html',
            'value' => function ($model) {
           return  $model->beneficiario_id>0 ?  '<font color="#cd2660"><b>'.$model->nreferente['name'].'</b></font>': "-";
           },
            'filter'=>ArrayHelper::map(
              Profile::find()
            ->where(['IN','user_id',$ids])
            ->andWhere(['<>','name',''])
            ->orderBy('name ASC')
            ->all(), 'user_id', 'name'
        ),
            ],

[
         
            'format' => 'html',
		'label'=>'Contacto',
            'value' => function ($model) {
           return  $model->beneficiario_id>0 ?  '<font color="#cd2660"><b>'.$model->referente['phone_number'].'</b></font>': "-";
           },
            ],

[
                 'attribute'=>'notificar_ao',
                 'format' => 'html',
                 'value' => function ($model) {
$utils=Profile::find()->where(['=','id',$model->notificar_ao])->all();
                  foreach ($utils as $util) {
                    return  $model->beneficiario_id>0 ?  '<font color="#cd2660"><b>'.$util->name.'</b></font>': "-";
                  }   

                },
                 'filter'=>ArrayHelper::map(
                   Profile::find()
                 ->where(['IN','user_id',$ids])
                 ->andWhere(['<>','name',''])
                 ->orderBy('name ASC')
                 ->all(), 'id', 'name'
             ),
                 ],

[
          'attribute'=>'refer_to',
          'label'=>'Ref. Para',
          'format' => 'html',
          'value' => function ($model) {
         return  $model->refer_to;
       },
         'filter'=>array("US"=>"US","CM"=>"CM","ES"=>"ES"),

       ],
 [
                 'attribute'=>'projecto',
                 'format' => 'html',
                 'value' => function ($model) {
                return  $model->organizacao['name'];
                },
                 'filter'=>ArrayHelper::map(
                   Organizacoes::find()
                 ->where(['IN','id',$org])
                 ->andWhere(['<>','status','0'])
                 ->orderBy('distrito_id ASC')
                 ->all(), 'id', 'name'
             ),],

  ['attribute'=> 'status_ref',
                    'format' => 'html',
                     'value' => function ($model) {
        $query = ReferenciasServicosReferidos::find()
            ->where(['=','referencia_id',$model->id])
            ->orderBy('id ASC')
            ->all();
          $servs=ArrayHelper::getColumn($query,'servico_id');
          $conta= ServicosBeneficiados::find()
            ->where(['=','beneficiario_id',$model->beneficiario_id])
            ->andWhere(['status' => 1])
            ->andWhere(['IN','servico_id', $servs])
            ->exists();
        if($conta>0) {

          // UPDATE
          $connection = Yii::$app->db;
          $connection->createCommand()
          ->update('app_dream_referencias', ['status_ref' => 1],['id'=>$model->id])
          ->execute();

          return '<font color="green"><b>Atendido</b></font>'; } else

          {return '<font color="red">Pendente</font>';}
       },
       'filter'=>array("1"=>"Atendido","0"=>"Pendente"),
                    ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    
    <?php ActiveForm::end(); ?>

</div>