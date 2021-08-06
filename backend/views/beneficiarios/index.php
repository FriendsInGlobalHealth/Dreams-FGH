<?php

use yii\helpers\Html;
//use yii\grid\GridView;
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


/* @var $this yii\web\View */
/* @var $searchModel app\models\BeneficiariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Adolescentes e Jovens';
$this->params['breadcrumbs'][] = $this->title;
//echo Yii::$app->user->identity->IsUserAdmin;


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

     <br>Lista de <?= Html::encode($this->title) ?>


    </h2>
    <?php    $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
<?= Html::a('<i class="fa fa-6 fa-female" aria-hidden="true"></i> Lista de Beneficiários por Vulnerabilidade',['/beneficiarios-dreams'], ['class' => 'btn btn-success', 'disabled'=>false]) ?>

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

        //  'id',
       // 'emp_number',

       [
                               'class' => 'kartik\grid\ExpandRowColumn',
                               'expandAllTitle' => 'Expand all',
                               'collapseTitle' => 'Collapse all',
                               'expandIcon'=>'<span class="glyphicon glyphicon-expand"></span>',
                               'value' => function ($model, $key, $index, $column) {
                                       return GridView::ROW_COLLAPSED;
                               },
                               'detail'=>function ($model, $key, $index, $column) {
                                 return Yii::$app->controller->renderPartial('_expand.php', ['model'=>$model]);
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
   //	return $model->ponto_entrada;
      if($model->ponto_entrada==1) {return "US";} elseif($model->ponto_entrada==2){return "CM";} else {return "ES";}
   },
    ],
  /*   [
        'attribute'=>'us_id',
        'value'=>'us.name',
        'filter'=> ArrayHelper::map(Us::find()->orderBy('name ASC')->asArray()->all(), 'id', 'name'),

      ],*/
      [
        'attribute'=>'district_code',
        'label'=>'Distrito',
        /*'filter'=>array(
          ""=>"Todos",
          "1"=>"Cidade da Beira",
          "4"=>"Cidade de Xai Xai",
          "6"=>"Chokwé",
          "7"=>"Cidade de Quelimane",
          "8"=>"Nicoadala",
          "13"=>"Limpopo",
          "14"=>"Chongoene",
          "15"=>"Geral",
          "16"=>"Namaacha",
          "17"=>"Matutuine",
        ),*/
        'filter'=> ArrayHelper::map(Distritos::find()->orderBy('district_name ASC')->asArray()->all(), 'district_code', 'district_name'),
        'value'=>'distrito.district_name',
  /*      'value'=>function ($model) {
          if(!$model->district_code==NULL) {
            $District = $model->district_code;
            $queryDistrito = Distritos::findOne($District);
          
            return  $queryDistrito['district_name'];} else {
            return  " ";
          }
          },*/
      ],

     [
       'attribute'=>'emp_birthday',
       'label'=>'idade',
       'filter'=>array(
         ""=>"Todos",
         "2008"=>"10",
         "2007"=>"11",
         "2006"=>"12",
         "2005"=>"13",
         "2004"=>"14",
         "2003"=>"15",
         "2002"=>"16",
         "2001"=>"17",
         "2000"=>"18",
         "1999"=>"19",
         "1998"=>"20",
         "1997"=>"21",
         "1996"=>"22",
         "1995"=>"23",
         "1994"=>"24",
         "1993"=>"25",
         "1993"=>"26",
       ),
        'value' => function ($model) {
if(!$model->emp_birthday==NULL) {
    $newDate = substr(date($model->emp_birthday, strtotime($model->emp_birthday)),-4);

   return  date("Y")-$newDate." anos";} else {
return  $model->idade_anos." anos";
}
  },
    ],

[
  'label'=>'#Interv',
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
  'label'=>'#Prim',
  'format' => 'raw',
  'value' => function ($model) {
  $conta = ServicosBeneficiados::find()->where(['beneficiario_id' => $model->id])->distinct()->count();
if($conta==0){return  '<span class="label label-danger"> <i class="fa fa-medkit"></i>&nbsp;['.core( $model->id).']</span>';}
  elseif ($conta<3) {return  '<span class="label label-warning"> <i class="fa fa-medkit"></i>&nbsp;['.core( $model->id).']</span>';}
  elseif ($conta<5) {return  '<span class="label label-info"> <i class="fa fa-medkit"></i>&nbsp;['.core( $model->id).']</span>';}
  else {
    return  '<span class="label label-success"> <i class="fa fa-medkit"></i>&nbsp;['.core( $model->id).']</span>';}


  },
'filter'=>array("0"=>"0","5"=>"5"),
],

      // Remocao do campo Contacto na tabela de beneficiario. Removido por Gerzelio, pedido por @Munheze aos 30 de Janeiro de 2020 10:14
//[
// 'attribute'=>'emp_mobile',
// 'label'=>'Contacto',
// 'format'=>'raw',
// 'value' => function ($model) {
//return Yii::$app->user->identity->role>10? $model->emp_mobile: "--";
// },
//],

      
    [
       'attribute'=>'parceiro_id',
   'format' => 'raw',
   'label'=>'Org',
        'filter'=>array("1"=>"Jhpiego - Sofala",
		"2"=>"FHI - 360 Sofala",
		"3"=>"World Education Inc. - Sofala",
		"4"=>"World Vision - Cidade de Quelimane",
		"5"=>"FGH - Zambezia",
		"6"=>"N`WETI - Gaza",
		"8"=>"Associação Kugarissica da Munhava - OCB WEI - Beira",
		"9"=>"NAFEZA - OCB WEI Quelimane",
		"10"=>"ICAP - Nicoadala",
		"12"=>"Associação Comussanas - OCB WEI - Beira",
		"13"=>"AMME - OCB WEI Quelimane",
		"14"=>"Kukumbi OCB WEI - Nicoadala",
		"15"=>"World Education Inc. - Gaza",
		"16"=>"World Education Inc. - Zambezia",
		"17"=>"EGPAF - Gaza",
		"18"=>"CDC",
		"19"=>"Udeba-Lab - OCB WEI Limpopo",
		"20"=>"Associação AREPACHO - OCB WEI - Chongoene",
		"21"=>"Associação KUVUMBANA - OCB WEI Cidade Xai-Xai",
		"23"=>"Jhpiego - Gaza",
		"24"=>"Associação VUKOXA - OCB WEI - Chokwe",	
		"25"=>"Associação OCSIDA - OCB WEI - Chokw ",
		"26"=>"Associação ACTIVA - OCB WEI - Cidade de Xai-Xai",
		"27"=>"World Vision - Nicoadala",
		"28"=>"Conselho Cristão de Moçambique - OCB WEI - Beira",
		"29"=>"Direcção   o Provincial da Educa  o e Desenvolvimento Humano de Gaza",
		"30"=>"Rede CAME",
		"31"=>"FHI360 COVIDA Maputo Prov",
		"32"=>"Jhpiego - Zambezia",
		"34"=>"Peace Corps - Corpo da Paz - Chokwe",
		"35"=>"FGH Nicoadala",
		"36"=>"SOPROC - OCB WEI Beira",	
		"38"=>"Associação  o Tiyane Vavassate - OCB FHI360 CoVida - Matutuine",
		"39"=>"ASSEDUCO - OCB FHII360 COVDA Namaacha",
		"40"=>"SDSMAS Matutuine",
		"41"=>"Fundação ARIEL Glaser",
		"43"=>"N`WETI_Zambezia",
		"44"=>"USAID",		
		"45"=>"SDSMAS de Namaacha",  

    "46"=>"Nucleo Provincial do Combate ao SIDA da Zambézia",
"47"=>"ABT - ECHO",
"48"=>" ASSEDUCO - OCB FHII360 COVDA Matutuine",
"49"=>"N´WETI_Nicoadala",
"50"=>"N´WETI_Quelimane",
"51"=>"Dirreccao Provincial de Saude da Zambezia",
"52"=>"Jhpiego Maputo provincia",
"53"=>"FGH", 
"54"=>"ABTECHO_Chimoio",
"55"=>"ICAP - ERATI",
"56"=>"ICAP - Nampula",
"57"=>"FGH - Gile",
"58"=>"FGH _Ile",
"59"=>"FGH _Lugela",
"60"=>"FGH _ Maganja da Costa",
"61"=>"FGH _ Mocuba",
"62"=>"FGH _ Namacurra",
"63"=>"FGH _ Inhassunge",
"64"=>"FGH _ Mocubela",
"65"=>"FGH _ Pebane",
"66"=>"FGH _ Milange",
"68"=>"Fundacao Ariel Glaser_Cabo Delgado",
"69"=>"ABTECHO_ Caia",
"71"=>"EGPAF _ MAXIXE",
"72"=>"EGPAF _ INHAMBANE",
"73"=>"Fundacao Ariel Glaser_Maputo Provincia",
"74"=>"Fundacao Ariel Glaser_ Boane",
"75"=>"Fundacao Ariel Glaser_ Matola",
"76"=>"Fundacao Ariel Glaser_ Magude",
"77"=>"Fundacao Ariel Glaser_ Manhica",
"78"=>"Fundacao Ariel Glaser_ Marracuene",
"79"=>"Fundacao Ariel Glaser_ Moamba",
"80"=>"EGPAF _ Guija",
"81"=>"N'WETI _  Maganja da Costa",
"82"=>"N'WETI _  Milange",
"83"=>"N'WETI _  Mocuba",
"84"=>"N'WETI _  Namacurra",
"85"=>"N'WETI _  Inhassunge",
"86"=>"N'WETI _  Ile",
"87"=>"N'WETI _  Mocubela",
"88"=>"N'WETI _  Lugela",
"89"=>"N'WETI _  Pebane",
"90"=>"N'WETI _  Gile",
"91"=>"Associação SOPROC_Caia",
"92"=>"Nucleo Provincial de Combate ao HIV SIDA _Gaza",
"93"=>"ANDA_Chimoio",
"94"=>"Associação Coalizão FHI360Covida _Erati",
"95"=>"Associação Niiwanane FHI360Covida _ Nampula",
"96"=>"Associação Coalizão da Juventude MoçambicanaFHI360Covida_Matola",
"97"=>"Associação Coalizão da Juventude Moçambicana_FHI360Covida_Boane",
"98"=>"FHI360 Covida_Nampula",
"99"=>"FHI360 Covida_Erati",
"100"=>"DPS Nampula",
"101"=>"DPS Inhambane",
"102"=>"Associação_UNIDOS_NWETI_Maganja da Costa",
"103"=>"Associacao_KUKUMBI_ OCB _NWETI_Namacurra",
"104"=>"Kukumbi OCB NWETI - Nicoadala",
"105"=>"AMME - OCB NWETI_Quelimane",
"106"=>"Associação Ovarelelana_FHI360Covida_Nampula",
"107"=>"Kukumbi OCB NWETI - Mocuba",
"108"=>"Associação CARITAS - OCB _NWETI - Milange",
"109"=>"Associação Assodeli _OCB_NWETI_ Pebane",
"110"=>"Associação Kubatsirana_OCB_ANDA_Chimoio",
"111"=>"Associação Okuluvela_OCB_NWETI_Inhassunge",
"112"=>"Conselho Cristão de Moçambique_OCB_Beira",
"113"=>"EGPAF_Maxixe _Comunitario",
"114"=>"Conselho Provincial de Combate ao SIDA _INHAMBANE",
"115"=>"Associação Kugarissica_OCB_Beira",
"117"=>"ADPP_FHI360Covida_Marracuene",
"118"=>"ADPP_FHI360Covida_Manhica",
"119"=>"ADPP_FHI360Covida_Magude",
"120"=>"ADPP_FHI360Covida_Moamba",


"121"=>"Associação AMME_OCB Nweti_Ile",
"122"=>"Associação Olipa_FHI360Covida _ Pemba",
"123"=>"Conselho Cristão de Moçambique_OCB_Nweti _Mocubela",
"124"=>"COMUSANAS_SOFALA",
"125"=>"Fundacao Ariel Glaser_SDSMAS_Pemba",
"126"=>"Associação ADESCA_ OCB_ NWETI_Lugela",
"128"=>"FHI360 Covida_Pemba",
"129"=>"ADPP_MAPUTO PROVINCIA",
"130"=>"ASSEDUCO - OCB FHII360 COVIDA  MAPUTO PROVINCIA"),
	  

        'value' => function ($model) {
   //	return $model->parceiro_id;
    if($model->parceiro_id==1) {return "Jhpiego - Sofala";}
	elseif($model->parceiro_id==2){return "FHI - 360 Sofala";} 
	elseif($model->parceiro_id==3){return "World Education Inc. - Sofala";} 
	elseif($model->parceiro_id==4){return "World Vision - Cidade de Quelimane";}
	elseif($model->parceiro_id==5){return "FGH - Zambezia";}
	elseif($model->parceiro_id==6){return "N WETI - Gaza";}
	elseif($model->parceiro_id==8){return "Associação Kugarissica da Munhava - OCB WEI - Beira";}
	elseif($model->parceiro_id==9){return "NAFEZA - OCB WEI Quelimane";}
	elseif($model->parceiro_id==10){return "ICAP - Nicoadala";}
	elseif($model->parceiro_id==12){return "Associação Comussanas - OCB WEI - Beira";}
	elseif($model->parceiro_id==13){return "AMME - OCB WEI Quelimane";}
	elseif($model->parceiro_id==14){return "Kukumbi OCB WEI - Nicoadala";}
	elseif($model->parceiro_id==15){return "World Education Inc. - Gaza";}
	elseif($model->parceiro_id==16){return "World Education Inc. - Zambezia";}
	elseif($model->parceiro_id==17){return "EGPAF - Gaza";}
	elseif($model->parceiro_id==18){return "CDC";}
	elseif($model->parceiro_id==19){return "Udeba-Lab - OCB WEI Limpopo";}
	elseif($model->parceiro_id==20){return "Associação AREPACHO - OCB WEI - Chongoene";}
	elseif($model->parceiro_id==21){return "Associação KUVUMBANA - OCB WEI Cidade Xai-Xai";}
	elseif($model->parceiro_id==23){return "Jhpiego - Gaza";}
	elseif($model->parceiro_id==24){return "Associação VUKOXA - OCB WEI - Chokwea";}
	elseif($model->parceiro_id==25){return "Associação OCSIDA - OCB WEI - Chokw ";}
	elseif($model->parceiro_id==26){return "Associação ACTIVA - OCB WEI - Cidade de Xai-Xai";}
	elseif($model->parceiro_id==27){return "World Vision - Nicoadala";}
	elseif($model->parceiro_id==28){return "Conselho Cristão de Mo ambique - OCB WEI - Beira";}
	elseif($model->parceiro_id==29){return "Direcção Provincial da Educa  o e Desenvolvimento Humano de Gaza";}
	elseif($model->parceiro_id==30){return "Rede CAME";}
	elseif($model->parceiro_id==31){return "FHI360 COVIDA Maputo Prov";}
	elseif($model->parceiro_id==32){return "Jhpiego - Zambezia";}
	elseif($model->parceiro_id==34){return "Peace Corps - Corpo da Paz - Chokwe";}
	elseif($model->parceiro_id==35){return "FGH Nicoadala";}
	elseif($model->parceiro_id==36){return "SOPROC - OCB WEI Beira";}
	elseif($model->parceiro_id==38){return "Associação  o Tiyane Vavassate - OCB FHI360 CoVida - Matutuine";}
	elseif($model->parceiro_id==39){return "ASSEDUCO - OCB FHII360 COVDA Namaacha";}	
	elseif($model->parceiro_id==40){return "SDSMAS Matutuine";}
	elseif($model->parceiro_id==41){return "Fundação ARIEL Glaser";}
	elseif($model->parceiro_id==43){return "N`WETI_Zambezia";}
	elseif($model->parceiro_id==44){return "USAID";}
    elseif($model->parceiro_id==45){return "SDSMAS de Namaacha";} 
          
          elseif($model->parceiro_id==46){return "Nucleo Provincial do Combate ao SIDA da Zambézia";}
elseif($model->parceiro_id==47){return "ABT - ECHO";}
elseif($model->parceiro_id==48){return " ASSEDUCO - OCB FHII360 COVDA Matutuine";}
elseif($model->parceiro_id==49){return "N´WETI_Nicoadala";}
elseif($model->parceiro_id==50){return "N´WETI_Quelimane";}
elseif($model->parceiro_id==51){return "Dirreccao Provincial de Saude da Zambezia";}
elseif($model->parceiro_id==52){return "Jhpiego Maputo provincia";}
elseif($model->parceiro_id==53){return "FGH";} 
elseif($model->parceiro_id==54){return "ABTECHO_Chimoio";}
elseif($model->parceiro_id==55){return "ICAP - ERATI";}
elseif($model->parceiro_id==56){return "ICAP - Nampula";}
elseif($model->parceiro_id==57){return "FGH - Gile";}
elseif($model->parceiro_id==58){return "FGH _Ile";}
elseif($model->parceiro_id==59){return "FGH _Lugela";}
elseif($model->parceiro_id==60){return "FGH _ Maganja da Costa";}
elseif($model->parceiro_id==61){return "FGH _ Mocuba";}
elseif($model->parceiro_id==62){return "FGH _ Namacurra";}
elseif($model->parceiro_id==63){return "FGH _ Inhassunge";}
elseif($model->parceiro_id==64){return "FGH _ Mocubela";}
elseif($model->parceiro_id==65){return "FGH _ Pebane";}
elseif($model->parceiro_id==66){return "FGH _ Milange";}
elseif($model->parceiro_id==68){return "Fundacao Ariel Glaser_Cabo Delgado";}
elseif($model->parceiro_id==69){return "ABTECHO_ Caia";}
elseif($model->parceiro_id==71){return "EGPAF _ MAXIXE";}
elseif($model->parceiro_id==72){return "EGPAF _ INHAMBANE";}
elseif($model->parceiro_id==73){return "Fundacao Ariel Glaser_Maputo Provincia";}
elseif($model->parceiro_id==74){return "Fundacao Ariel Glaser_ Boane";}
elseif($model->parceiro_id==75){return "Fundacao Ariel Glaser_ Matola";}
elseif($model->parceiro_id==76){return "Fundacao Ariel Glaser_ Magude";}
elseif($model->parceiro_id==77){return "Fundacao Ariel Glaser_ Manhica";}
elseif($model->parceiro_id==78){return "Fundacao Ariel Glaser_ Marracuene";}
elseif($model->parceiro_id==79){return "Fundacao Ariel Glaser_ Moamba";}
elseif($model->parceiro_id==80){return "EGPAF _ Guija";}
elseif($model->parceiro_id==81){return "N'WETI _  Maganja da Costa";}
elseif($model->parceiro_id==82){return "N'WETI _  Milange";}
elseif($model->parceiro_id==83){return "N'WETI _  Mocuba";}
elseif($model->parceiro_id==84){return "N'WETI _  Namacurra";}
elseif($model->parceiro_id==85){return "N'WETI _  Inhassunge";}
elseif($model->parceiro_id==86){return "N'WETI _  Ile";}
elseif($model->parceiro_id==87){return "N'WETI _  Mocubela";}
elseif($model->parceiro_id==88){return "N'WETI _  Lugela";}
elseif($model->parceiro_id==89){return "N'WETI _  Pebane";}
elseif($model->parceiro_id==90){return "N'WETI _  Gile";}
elseif($model->parceiro_id==91){return "Associação SOPROC_Caia";}
elseif($model->parceiro_id==92){return "Nucleo Provincial de Combate ao HIV SIDA _Gaza";}
elseif($model->parceiro_id==93){return "ANDA_Chimoio";}
elseif($model->parceiro_id==94){return "Associação Coalizão FHI360Covida _Erati";}
elseif($model->parceiro_id==95){return "Associação Niiwanane FHI360Covida _ Nampula";}
elseif($model->parceiro_id==96){return "Associação Coalizão da Juventude MoçambicanaFHI360Covida_Matola";}
elseif($model->parceiro_id==97){return "Associação Coalizão da Juventude Moçambicana_FHI360Covida_Boane";}
elseif($model->parceiro_id==98){return "FHI360 Covida_Nampula";}
elseif($model->parceiro_id==99){return "FHI360 Covida_Erati";}
elseif($model->parceiro_id==100){return "DPS Nampula";}
elseif($model->parceiro_id==101){return "DPS Inhambane";}
elseif($model->parceiro_id==102){return "Associação_UNIDOS_NWETI_Maganja da Costa";}
elseif($model->parceiro_id==103){return "Associacao_KUKUMBI_ OCB _NWETI_Namacurra";}
elseif($model->parceiro_id==104){return "Kukumbi OCB NWETI - Nicoadala";}
elseif($model->parceiro_id==105){return "AMME - OCB NWETI_Quelimane";}
elseif($model->parceiro_id==106){return "Associação Ovarelelana_FHI360Covida_Nampula";}
elseif($model->parceiro_id==107){return "Kukumbi OCB NWETI - Mocuba";}
elseif($model->parceiro_id==108){return "Associação CARITAS - OCB _NWETI - Milange";}
elseif($model->parceiro_id==109){return "Associação Assodeli _OCB_NWETI_ Pebane";}
elseif($model->parceiro_id==110){return "Associação Kubatsirana_OCB_ANDA_Chimoio";}
elseif($model->parceiro_id==111){return "Associação Okuluvela_OCB_NWETI_Inhassunge";}
elseif($model->parceiro_id==112){return "Conselho Cristão de Moçambique_OCB_Beira";}
elseif($model->parceiro_id==113){return "EGPAF_Maxixe _Comunitario";}
elseif($model->parceiro_id==114){return "Conselho Provincial de Combate ao SIDA _INHAMBANE";}
elseif($model->parceiro_id==115){return "Associação Kugarissica_OCB_Beira";}
elseif($model->parceiro_id==117){return "ADPP_FHI360Covida_Marracuene";}
elseif($model->parceiro_id==118){return "ADPP_FHI360Covida_Manhica";}
elseif($model->parceiro_id==119){return "ADPP_FHI360Covida_Magude";}
elseif($model->parceiro_id==120){return "ADPP_FHI360Covida_Moamba";}

// Hardcode 2021

elseif($model->parceiro_id==121){return "Associação AMME_OCB Nweti_Ile";}
elseif($model->parceiro_id==122){return "Associação Olipa_FHI360Covida _ Pemba";}
elseif($model->parceiro_id==123){return "Conselho Cristão de Moçambique_OCB_Nweti _Mocubela";}
elseif($model->parceiro_id==124){return "COMUSANAS_SOFALA";}
elseif($model->parceiro_id==125){return "Fundacao Ariel Glaser_SDSMAS_Pemba";}
elseif($model->parceiro_id==126){return "Associação ADESCA_ OCB_ NWETI_Lugela";}
elseif($model->parceiro_id==128){return "FHI360 Covida_Pemba";}
elseif($model->parceiro_id==129){return "ADPP_MAPUTO PROVINCIA";}
elseif($model->parceiro_id==130){return "ASSEDUCO - OCB FHII360 COVIDA  MAPUTO PROVINCIA";}
	  
   },
    ],      
      
      //else {return "SDSMAS de Namaacha";}

        [
            'attribute'=>'criado_por',
            'label'=>'Criado Por',
            'format'=>'raw',
            'value'=> function ($model) { return Yii::$app->user->identity->role==20 ? '<small>'.$model->user['username'].'</small>':'<small>'.$model->user['username'].'</small>'; },
            'filter'=> ArrayHelper::map(Utilizadores::find()->where(['>','provin_code',0])->distinct()->orderBy('username ASC')->asArray()->all(), 'id', 'username'),

        ],
      
		 [
            'attribute'=>'actualizado_por',
			 'label'=>'Actualizado Por',
			'format'=>'raw',
            'value'=> function ($model) { return Yii::$app->user->identity->role==20 ? '<small>'.$model->update['username'].'</small>':'<small>'.$model->update['username'].'</small>'; },
            'filter'=> ArrayHelper::map(Utilizadores::find()->where(['>','provin_code',0])->distinct()->orderBy('username ASC')->asArray()->all(), 'id', 'username'),
				 
          ],


[
 'attribute'=>'criado_em',
 'label'=>'Criado Em',
 'format'=>'raw',
'content'=>function($data){
           return Yii::$app->formatter->asDate($data['criado_em'], "php:Y-m-d");
   }
],
/*
[
'attribute'=> 'criado_em',
'format' => 'html',
'label'=>'Criado em',
'value' => function ($model) {
return  $model->member_id>10 ?  '<font color="#cd2660"><b>'.
// Yii::$app->formatter->asDate($data['criado_em'], "php:Y-m-d")
ucfirst(strftime("%B", strtotime($model->criado_em)))

.'</b></font>': "-";
},
],*/

/*
[
            'attribute' => 'criado_em',
'label'=>'Data Criação',
'value' => function ($model) {
return  substr($model->criado_em,0,10); 
},
            'filter' => \yii\jui\DatePicker::widget(['language' => 'us', 'dateFormat' => 'Y-m-d']),
          
 // 'format' => 'raw',
        ],
*/
        ['class' => 'yii\grid\ActionColumn',
        'template'=>'{view} {update}',
                        'buttons'=>[
                          'create' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
                                    'title' => Yii::t('yii', 'Create'),
                            ]);

                          }
                      ]/**/],
    ],
'pjax'=>Yii::$app->user->identity->role==20 ? true:false, // pjax is set to always true for this demo
    // set your toolbar
    'toolbar'=> [
		['content'=>Html::a(Yii::t('app', '<i class="glyphicon glyphicon-plus"></i>'), ['create'], ['class' => 'btn btn-success']).' '.
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>'Reset List'])
            ],
        '{export}',
        '{toggleData}',
    ],
    // parameters from the demo form

  //  'showPageSummary'=>$pageSummary,
    'panel'=>[
        'type'=>GridView::TYPE_PRIMARY,
      //  'heading'=>$heading,
    ],
    'persistResize'=>true,
    //'exportConfig'=>$exportConfig,
      //  'responsive'=>true,
        'hover'=>true
    ]);


?>
<?php Pjax::end(); ?>



   <p>
     <?= Html::a('<i class="glyphicon glyphicon-home"></i>', ['site/index'], ['class' => 'btn btn-danger']) ?>
        <?= Html::a('<i class="fa fa-plus"></i> Novo Beneficiário', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


</div>
</div>

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
