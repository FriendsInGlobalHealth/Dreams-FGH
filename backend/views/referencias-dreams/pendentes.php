<?php

  use yii\helpers\Html;
  use kartik\grid\GridView;
  use kartik\form\ActiveForm;
  use yii\bootstrap\Modal;

  use yii\widgets\Pjax;
  use yii\helpers\ArrayHelper;
  use \kartik\widgets\Select2;
  use app\models\Utilizadores;
  use app\models\ReferenciasDreams;
  use app\models\Us;

  //05 11 2018 Actualizado em Pemba
  use app\models\ReferenciasServicosReferidos;
  use app\models\ServicosBeneficiados;
  use app\models\Organizacoes;
  use app\models\Distritos;

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

    $referido_por = Profile::find()
      ->select('profile.*')
      ->distinct(true)
      ->innerjoin('user', '`profile`.`user_id` = `user`.`id`')
      ->innerjoin('app_dream_referencias', '`app_dream_referencias`.`criado_por` = `profile`.`user_id`')
      ->where(['user.provin_code' => Yii::$app->user->identity->provin_code])
      ->andwhere(['app_dream_referencias.status' => 1])
      ->andWhere(['<>','profile.name',''])
      ->orderBy('name ASC')
      ->all();

    $notificar_ao = Profile::find()
      ->select('profile.*')
      ->distinct(true)
      ->innerjoin('user', '`profile`.`user_id` = `user`.`id`')
      ->innerjoin('app_dream_referencias', '`app_dream_referencias`.`notificar_ao` = `profile`.`id`')
      ->where(['user.provin_code' => Yii::$app->user->identity->provin_code])
      ->andwhere(['app_dream_referencias.status' => 1])
      ->andWhere(['<>','profile.name',''])
      ->orderBy('name ASC')
      ->all();

    //added on 05 11 2018
    $orgs=Organizacoes::find()->where(['IN','distrito_id',$dist])->orderBy('parceria_id ASC')->asArray()->all();

  } else {

    $referido_por = Profile::find()
      ->select('profile.*')
      ->distinct(true)
      ->innerjoin('app_dream_referencias', '`app_dream_referencias`.`criado_por` = `profile`.`user_id`')
      ->where(['app_dream_referencias.status' => 1])
      ->andWhere(['<>','profile.name',''])
      ->orderBy('name ASC')
      ->all();

    $notificar_ao = Profile::find()
      ->select('profile.*')
      ->distinct(true)
      ->innerjoin('app_dream_referencias', '`app_dream_referencias`.`notificar_ao` = `profile`.`id`')
      ->where(['app_dream_referencias.status' => 1])
      ->andWhere(['<>','profile.name',''])
      ->orderBy('name ASC')
      ->all();

    $dists=Distritos::find()->orderBy('district_name ASC')->asArray()->all();
    $dist=ArrayHelper::getColumn($dists, 'district_code');

    $orgs=Organizacoes::find()->where(['=', 'status', 1])->orderBy('parceria_id ASC')->asArray()->all();

  }

  $org=ArrayHelper::getColumn($orgs, 'id');

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

      <?php $form = ActiveForm::begin([
        'options' => [
          'class' => 'referencias-pendentes-form'
      ]]); ?>

      <div class="panel panel-success">
        <div class="panel-heading"> 
          
          <b><span class="glyphicon glyphicon-check" aria-hidden="true"></span> Detalhes do cancelamento das Referencias</b>

        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-lg-6">  
              <div class="form-group required">

                <?=  
                
                  $form->field($model, 'cancel_reason')->widget(Select2::classname(),['data' => ['1' => 'Serviço não provido nos últimos 6 meses','2' => 'Beneficiária não encontrada','3' => 'Abandono','4' => 'Beneficiária recusou o serviço','5' => 'Outro Motivo'],
                    'options' => ['onchange' => 'var valor2 = this.value; 
                      if(valor2==5){
                        $("#other_reason").show(1000);
                        $("#SALVAR").attr("disabled", "disabled"); 
                      }
                      else{
                        $("#other_reason").hide(1000);
                        $("#SALVAR").prop("disabled", false);}
                      
                      if(valor2==="")
                      {
                        $("#SALVAR").attr("disabled", "disabled");
                      }', 
                    'placeholder' => '--Selecione Aqui--'],'pluginOptions' => ['allowClear' => true],]
                  ); 
                ?>

              </div>
            </div>
            <div class="col-lg-6" id="other_reason"> 
              <div class="form-group required">

                <?= $form->field($model, 'other_reason')->textArea(['maxlength' => true, 'rows' => '3','oninput'=>'validacao()'])?> 

              </div>
            </div>
          </div>              
          <div class="form-group pull-right">

            <?= Html::submitButton('SALVAR' , ['value'=>Url::to('pendentes'),'class' => 'btn btn-success','id'=>'SALVAR', 'disabled'=>true]) ?>
            <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-warning']) ?>
          
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php 
        Modal::begin([
            'header' => '<h4>Confirma o Cancelamento das Seguintes Referências?</h4>',
            'id' => 'modal',
            'size' => 'modal-lg',
        ]);
        echo "<div id='modalContent'></div>";
        Modal::end();
  ?>
          
  <table width="100%"   class="table table-bordered  table-condensed">
    <tr>
      <td bgcolor="#261657" bgcolor="" align="center">
        <font color ="#fff" size="+1"><b><span class="fa fa-exchange" aria-hidden="true"></span> 
          Selecione as Referências e Contra-Referências Pendentes por Cancelar</b>
        </font>
      </td>
    </tr>
    <tr>
      <td bgcolor="#808080" align="center">
        <font color="#fff" size="+1"><b> </b>

        </font>
      </td>
    </tr>
  </table>

  <?= GridView::widget([
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'toggleData'=>false,
      'columns' => [
          ['class' => 'yii\grid\SerialColumn'],
          [
            'class' => 'kartik\grid\CheckboxColumn',
            'headerOptions' => ['class' => 'kartik-sheet-style']
          ],

          [
            'attribute'=>'distrito_id',
            'format' => 'html',
            'value' => function ($model) {
              return isset($model->beneficiario)? $model->beneficiario->distrito['district_name'] : '';

            },
            'filter'=>ArrayHelper::map(
              $dists, 'district_code', 'district_name'
            ),
          ],

          [
            'attribute'=>'servico_id',
            'format' => 'html',
            'label'=>'Organização Referente',
            'value' => function ($model) {
              $organizacao = Organizacoes::find()
                ->select('app_dream_parceiros.*')
                ->innerjoin('user', '`user`.`parceiro_id` = `app_dream_parceiros`.`id`')
                ->innerjoin('app_dream_referencias', '`app_dream_referencias`.`criado_por` = `user`.`id`')
                ->where(['=','app_dream_referencias.id',$model->id])
                ->one();
                
              return isset($organizacao)? $organizacao->name: "";
            },
            'filter'=>ArrayHelper::map(
              Organizacoes::find()
                ->where(['IN','id',$org])
                ->andWhere(['<>','status','0'])
                ->orderBy('distrito_id ASC')
                ->all(), 'id', 'name'
            ),
          ],

          ['attribute'=> 'criado_em',
            'format' => 'html',
            'value' => function ($model) {

              return $model->criado_em;
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

			    ['attribute'=>'referido_por',
            'format' => 'html',
            'value' => function ($model) {
              return  $model->beneficiario_id>0 ?  '<font color="#cd2660"><b>'.$model->nreferente['name'].'</b></font>': "-";
            },
            'filter'=>ArrayHelper::map($referido_por, 'user_id', 'name'),
          ],

          ['format' => 'html',
		        'label'=>'Contacto',
            'value' => function ($model) {
              return  $model->beneficiario_id>0 ?  '<font color="#cd2660"><b>'.$model->referente['phone_number'].'</b></font>': "-";
            },
          ],

          ['attribute'=>'notificar_ao',
           'format' => 'html',
           'value' => function ($model) {
              return  $model->beneficiario_id>0 ?  '<font color="#cd2660"><b>'.$model->nreceptor['name'].'</b></font>': "-";
            },
            'filter'=>ArrayHelper::map($notificar_ao, 'id', 'name'),
          ],

          ['attribute'=>'refer_to',
            'label'=>'Ref. Para',
            'format' => 'html',
            'value' => function ($model) {
              return  $model->refer_to;
            },
          'filter'=>array("US"=>"US","CM"=>"CM","ES"=>"ES"),
        ],

        ['attribute'=>'projecto',
         'format' => 'html',
         'label'=>'Organização Referida',
         'value' => function ($model) {
            return  $model->organizacao['name'];
          },
         'filter'=>ArrayHelper::map(
            Organizacoes::find()
              ->where(['IN','id',$org])
              ->andWhere(['<>','status','0'])
              ->orderBy('distrito_id ASC')
              ->all(), 'id', 'name'
          ),
        ],

        [
          'attribute'=>'status',
          'format' => 'html',
          'label'=>'Ponto de Entrada para Referência',
          'value' => function ($model) {
            $us = Us::find()
              ->select('app_dream_us.*')
              ->innerjoin('user', '`user`.`us_id` = `app_dream_us`.`id`')
              ->innerjoin('profile', '`profile`.`user_id` = `user`.`id`')
              ->innerjoin('app_dream_referencias', '`app_dream_referencias`.`notificar_ao` = `profile`.`id`')
              ->where(['=','app_dream_referencias.id',$model->id])
              ->all();
            return count($us) > 0 ? $us[0]->name: "";
          },
          'filter'=>ArrayHelper::map(
            Us::find()
              ->where(['IN','distrito_id',$dist])
              ->andWhere(['<>','status','0'])
              ->orderBy('name ASC')
              ->all(), 'id', 'name'
          ),

        ],

        ['attribute'=>'status_ref',
          'format' => 'html',
          'value' => function ($model) {
            return  $model->status_ref==0? '<font color="red">Pendente</font>':'<font color="green"><b>Atendido</b></font>';
          },
          'filter'=>array("1"=>"Atendido","0"=>"Pendente"),
        ],
      ],
      'panel'=>[
        'type'=>GridView::TYPE_PRIMARY,
      ],
    ]); 
  ?>

  <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
  window.onload = function () {
    $(document).ready(function() {
      $("#cancel_reason").value = "";
      $("#other_reason").value = "";
      $("#other_reason").hide(1000);
    
      $('.referencias-pendentes-form').on('beforeSubmit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var formData = form.serialize();

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            success: function(data) {
              $("#modal").modal('show');
              $('#modalContent').html(data);
            },
            error: function() {
              console.log("Something went wrong");
            }
        });
      }).on('submit', function(e) {
        e.preventDefault();
        $('#SALVAR').attr('disabled', 'disabled');
      });


      
    });

  }
            
  function validacao(){
    var reason = document.getElementById("referenciasdreams-other_reason").value;
    if(reason===""){  
      $("#SALVAR").attr("disabled", "disabled");                     
    }else{
      $("#SALVAR").attr("disabled", false);
    }
  }

</script>
