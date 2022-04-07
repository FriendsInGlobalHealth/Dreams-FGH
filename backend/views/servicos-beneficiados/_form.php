<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;


use kartik\widgets\DepDrop;
use \kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\widgets\DatePicker;
use kartik\form\ActiveForm;
use common\models\User;
use app\models\ServicosDream;
use app\models\Beneficiarios;
use app\models\Us;
use app\models\SubServicosDreams;
use app\models\TipoServicos;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $model app\models\ServicosBeneficiados */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servicos-beneficiados-form">

    <?php $form = ActiveForm::begin(); ?>
  <div class="row"> 
    <div class="col-lg-12"> 
      <?php 
      
       $subservicosLista =
       SubServicosDreams::find()
       ->where(['=','status',1])
       ->andwhere(['=','servico_id',$model->servico_id])
       ->all();

       $map = array();

       foreach ($subservicosLista as $subServico){
         if($subServico['mandatory']==1){
             array_push($map,['id'=>$subServico['id'],'name'=>($subServico['name']." *")]);
         }else{
             array_push($map,['id'=>$subServico['id'],'name'=>$subServico['name']]);
         }
       }

        //if(isset($_REQUEST['id'])) {$_REQUEST['m']=filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);}
        if(isset($_REQUEST['m'])&&$_REQUEST['m']>0) {

          $idb=filter_var($_REQUEST['m'], FILTER_SANITIZE_NUMBER_INT);
          $person = Beneficiarios::find()->where(['=','id',$idb])->all();

          $personMap = ArrayHelper::map($person,'member_id',function ($person, $defaultValue)
              { 
                if (isset(Yii::$app->user->identity->role)&&Yii::$app->user->identity->role==20) {
                  echo '<h2>'.$person->emp_firstname." ".$person->emp_middle_name." ".$person->emp_lastname.'</h2>';
                } else {  
                  echo '<h2>'.$person->distrito['cod_distrito'].'/'.$person->member_id.'</h2>';
                }
            });	
          
          echo $form->field($model, 'beneficiario_id')->hiddenInput(['value'=>$_REQUEST['m']])->label(false);
        } else {

          $person = Beneficiarios::find()->orderBy('emp_firstname ASC')->all();

          $personMap = ArrayHelper::map($person,'member_id',function ($person, $defaultValue)
                { return  $person->emp_firstname." ".$person->emp_middle_name." ".$person->emp_lastname; });

          if($model->isNewRecord) { }
          echo $form->field($model, 'beneficiario_id')->dropDownList(
          $personMap, ['prompt'=>'[--selecione o Beneficiario --]',
                  ]);

        }
      ?>
    </div>
  </div>
  <div class="row"> 

  <div class="col-lg-4">
    <?php 
        if (isset($_REQUEST['sid'])&&($_REQUEST['sid']>0)) {
          echo $form->field($model, 'tipo_servico_id')->widget(Select2::classname(), [
            'name' => 'kv-state-250',
            'data' =>(ArrayHelper::map(TipoServicos::find()->where(['=','id',$_REQUEST['ts']])->all(), 'id', 'name')),
            'options' => [
                            'placeholder' => '--Selecione o Tipo de Serviço--',
                            'id' => 'tipo_servico_id',
                            'value'=>$_REQUEST['ts']],
            'pluginOptions' => [
                            'allowClear' => true
                            ],    
            // 'disabled' => true
          ])->label('Área de Serviços');
        } else {
          echo $form->field($model, 'tipo_servico_id')->widget(Select2::classname(), [
            'name' => 'kv-state-250',
            'data' =>(ArrayHelper::map(TipoServicos::find()->where(['=','status',1])->all(), 'id', 'name')),
            'options' => [
                            'placeholder' => '--Selecione o Tipo de Serviço--',
                            'id' => 'tipo_servico_id'],
            'pluginOptions' => [
                            'allowClear' => true
                            ],
          ])->label('Área de Serviços');

        }
    ?>
    
  <div class="help-block"></div>
  </div>
    <div class="col-lg-4">
      <?php  
        if(!$model->isNewRecord) { 
         echo $form->field($model, 'servico_id')->widget(DepDrop::classname(), [
                              'data' =>(ArrayHelper::map(ServicosDream::find()->orderBy('name ASC')->where(['=','status',1])->all(), 'id', 'name')),
                                          'options' => [
                                            'id' =>'servico_id',
                                            'multiple'=>false,
                                            'placeholder' => '--Selecione o Serviço--'],
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                                            'pluginOptions' => [
                                                            'depends' => ['tipo_servico_id'],
                                                            'url' => Url::to(['listaservicos']),
                                                            'loadingText' => 'Lendo Area de Servicos ...',
                                                        ]
                                          ])->label('Serviço');     

    

        } else {


        if (isset($_REQUEST['ts'])&&($_REQUEST['ts']>0)) {
          echo $form->field($model, 'servico_id')->widget(DepDrop::classname(), [
            'data' =>(ArrayHelper::map(ServicosDream::find()->orderBy('name ASC')->where(['=','status',1])->andwhere(['=','servico_id',$model->tipo_servico_id])->all(), 'id', 'name')),
                        'options' => [
                          'id' =>'servico_id',
                          'multiple'=>false,
                          'placeholder' => '--Selecione o Serviço--'],
                          'type' => DepDrop::TYPE_SELECT2,
                          'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                          'pluginOptions' => [
                                          'depends' => ['tipo_servico_id'],
                                          'url' => Url::to(['listaservicos']),
                                          'loadingText' => 'Lendo Area de Servicos ...',
                                      ]
                        ])->label('Serviço');     
        } else {
          if (isset($_REQUEST['sid'])&&($_REQUEST['sid']>0)) {
            echo $form->field($model, 'servico_id')->widget(DepDrop::classname(), [
              'data' =>(ArrayHelper::map(ServicosDream::find()->orderBy('name ASC')->where(['=','id',$_REQUEST['sid']])->all(), 'id', 'name')),
              'options' => [
                'id' =>'servico_id',
                'multiple'=>false,
                'placeholder' => '--Selecione o Serviço--',
                'value'=> $_REQUEST['sid']],
                'type' => DepDrop::TYPE_SELECT2,
                'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                'pluginOptions' => [
                                'depends' => ['tipo_servico_id'],
                                'url' => Url::to(['listaservicos']),
                                'loadingText' => 'Lendo Area de Servicos ...',
                ],   
                // 'disabled' => true
              ])->label('Serviço'); 
          } else {
            echo $form->field($model, 'servico_id')->widget(DepDrop::classname(), [
              'options' => [
                'id' =>'servico_id',
                'multiple'=>false,
                'placeholder' => '--Selecione o Serviço--'],
                'type' => DepDrop::TYPE_SELECT2,
                'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                'pluginOptions' => [
                                'depends' => ['tipo_servico_id'],
                                'url' => Url::to(['listaservicos']),
                                'loadingText' => 'Lendo Area de Servicos ...',
                            ]
              ])->label('Serviço'); 
          }
        }//is New
      ?>

      <div class="help-block"></div>
    </div>    

    <div class="col-lg-4">   
      <?php 
          if (isset($_REQUEST['sid'])&&($_REQUEST['sid']>0)) {
            echo $form->field($model, 'sub_servico_id')->widget(DepDrop::classname(), [
                'data' =>(ArrayHelper::map(SubServicosDreams::find()->where(['=','status',1])->andwhere(['=','servico_id',$_REQUEST['sid']])->all(), 'id', 'name')),
                                            
                'options' => [
                  'id' =>'sub_servico_id',
                  'multiple'=>false,
                  'placeholder' => '--Selecione o Sub-Serviço/Intervenção--'],
                  'type' => DepDrop::TYPE_SELECT2,
                  'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                  'pluginOptions' => [
                                  'depends' => ['tipo_servico_id','servico_id'],
                                  'url' => Url::to(['listasubservicos']),
                                  'loadingText' => 'Lendo Area de Sub-Servicos ...',
                              ]
                ])->label('Sub-Serviço/Intervenção'); 
          } else {
            echo $form->field($model, 'sub_servico_id')->widget(DepDrop::classname(), [
                'data' =>(ArrayHelper::map($map, ['id'], ['name'])),
                'options' => [
                  'id' =>'sub_servico_id',
                  'multiple'=>false,
                  'placeholder' => '--Selecione o Sub-Serviço/Intervenção--'],
                  'type' => DepDrop::TYPE_SELECT2,
                  'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                  'pluginOptions' => [
                                  'depends' => ['tipo_servico_id','servico_id'],
                                  'url' => Url::to(['listasubservicos']),
                                  'loadingText' => 'Lendo Area de Sub-Servicos ...',
                              ]
                ])->label('Sub-Serviço/Intervenção');
            
          }
      ?>
    </div>	
  </div>

  <div class="row"> 

    <div class="col-lg-4"> <br>
      <?= $form->field($model, 'ponto_entrada')->radioButtonGroup([1 => 'US', 2 => 'CM', 3 => 'ES']); ?>
  </div> 

  <div class="col-lg-4"> 	
    <?php
      if (isset(Yii::$app->user->identity->provin_code)){
        echo $form->field($model, 'us_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Us::find()->orderBy('name ASC')->where(['provincia_id'=>(int)Yii::$app->user->identity->provin_code])->andWhere('status IS NOT NULL')->all(),'id','name'),
          'options' => ['placeholder' => '--Selecione o Ponto de Entrada (Localização)--'],
          'pluginOptions' => [
              'allowClear' => true
          ],
        ]);
      } else{
        echo  $form->field($model, 'us_id')->widget(Select2::classname(), [
          'name' => 'kv-state-250',
          'data' =>(ArrayHelper::map(Us::find()->orderBy('name ASC')->where('status IS NOT NULL')->all(), 'id', 'name')),
          'options' => [
                          'placeholder' => '--Selecione o Tipo de Serviço--',
                          'id' => 'us_id'],
          'pluginOptions' => [
                          'allowClear' => true
                          ],
        ]);
      };
    ?>
  </div> 
	
  <div class="col-lg-4"> 
    <?=
      $form->field($model, 'data_beneficio', [
        ])->widget(
          DatePicker::className(), [
              'language'=>'pt',
              'readonly' => true,
              'name' => 'dp_2',
              'type' => DatePicker::TYPE_COMPONENT_PREPEND,
              'pluginOptions' => [
                  'autoclose'=>true,
                  'startDate' => '01/01/2017',
                  'endDate' => "0d",
                  'format' => 'dd/mm/yyyy'
              ]
      ]);
    ?>
  </div>
  </div>

  <div class="row">

    <div class="col-lg-12"> 
      <?php $form->field($model, 'activista_id')->textInput() ?> 
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <?= $form->field($model, 'provedor')->textInput(['readonly'=>false,'placeholder' => 'Nome do Provedor do Serviço']) ?>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <?= $form->field($model, 'description')->textArea(['maxlength' => true, 'rows' => '3']) ?>
    </div> 
  </div>
 
  <div class="row">
    <div class="col-lg-12">
      <?php $form->field($model, 'status')->radio( ['1' => ' Activo', '0' => ' Cancelado']);?>
        
      <?= $form->field($model, 'status')->dropDownList([1 => 'Activo', 0 => 'Cancelado']); ?>		 
        
      <?php $form->field($model, 'status')->checkbox(['value' => 1])->label('Status'); ?>
      <?php $form->field($model, 'status')->radioButtonGroup(['1' => ' Activo', '0' => ' Cancelado']); ?>
    </div> 
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="form-group">

        <?php if(isset($_REQUEST['atender']) &&isset($_REQUEST['m'])&&($_REQUEST['m']>0)&&($_REQUEST['atender']==sha1(1)))   {?>
                  <?= Html::submitButton($model->isNewRecord ? 'Atender' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?php } else { ?>
                <?= Html::a('<i class="glyphicon glyphicon-backward"></i>', ['beneficiarios/' . $idb ], ['class' => 'btn btn-warning']) ?> 
                <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php }?> 
      </div>
    </div>
  </div>
		
  <?php ActiveForm::end(); ?>
</div>