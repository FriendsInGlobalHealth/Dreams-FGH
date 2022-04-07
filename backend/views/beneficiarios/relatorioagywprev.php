<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Provincias;
use app\models\Distritos;

$this->title = "INDICADORES DREAMS";
$this->params['breadcrumbs'][] = ['label' => 'AGYW', 'url' => ['relatorioAGYWprevView']];
$this->params['breadcrumbs'][] = $this->title;
$i=0;
?>


<div class="membros-view">

	<div class="col-lg-12">

	    <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-dashboard  text-primary"></i> <strong> PEPFAR MER 2.6 Indicador Semi-Annual AGYW_PREV  </strong>
            </div>
            <div class="panel-body">
                <div class="row"> </div>

                <table width="100%" class="table table-dashed">
                    <col width="174">
                    <col width="117">
                    <col width="119">
                    <col width="154">
                    <col width="142">

                    <tr>
                        <td colspan="5" bgcolor="#FFFFFF"><b>Data Inicial: <?php echo $model->start_date ?></b></td>
                    </tr>
                    <tr>
                            
                        <td colspan="5" bgcolor="#FFFFFF"><b>Data final: <?php echo $model->end_date ?></b></td>
                    </tr> 
                    
                </table>

                <?php foreach($provinces as $province){
                        $i++;
                        $divId = 'tag'.$i;
                        $tag = '#tag'.$i;
                    ?>

                    <button class="btn btn-primary btn-block  mb1 black bg-darken-1" data-toggle="collapse" data-target=<?= $tag ?>  ><b> <?php echo $province->province_name ?> </b></button>

                    <div id=<?= $divId  ?> class="collapse">
                        <table width="100%" class="table table-dashed">

                            <tr>
                                <td colspan="5" bgcolor="#FFFFFF"><b> <?php echo $province->province_name ?> </b></td>
                            </tr>	
                        </table>
                        <?php foreach($districts as $district){
                                $i++;
                                $subdivId = 'subtag'.$i;
                                $subtag = '#subtag'.$i;
                                if( $district->province_code == $province->id){
                                    $firstdesagregationResults = $firstdesagregation[$district->district_code]['results'];
                                    $seconddesagregationResults = $seconddesagregation[$district->district_code]['results'];
                                    $thirddesagregationResults = $thirddesagregation[$district->district_code]['results'];
                                    $fourthdesagregationResults = $fourthdesagregation[$district->district_code]['results'];
                                    $fifthdesagregationResults = $fifthdesagregation[$district->district_code]['results'];
                                    $sixthdesagregationResults = $sixthdesagregation[$district->district_code]['results'];
                                    $seventhdesagregationResults = $seventhdesagregation[$district->district_code]['results'];

                            ?>
                                <button class="btn btn-primary btn-block  mb1 black bg-orange" data-toggle="collapse" data-target=<?= $subtag ?> ><b> <?php echo $district->district_name ?> </b></button>
                                <div id=<?= $subdivId  ?> class="collapse">
                                    <table width="100%" class="table table-dashed">
                                        <tr>
                                            <td colspan="6" bgcolor="#FFFFFF"><b> Distrito: <?php echo $district->district_name ?> </b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" bgcolor="#FFFFFF"><b>RESUMO DISTRITAL</b></td>
                                        </tr>	
                                        <tr>
                                            <td colspan="6" bgcolor="#FFFFFF"> <b>Total de Adolescentes e Jovens Registados: <b>  <?php echo $totals[$district->district_code]['total_registos'] ?> </b></td>
                                        </tr> 
                                        <tr>
                                            <td colspan="6" bgcolor="#FFFFFF"><b>Total de Adolescentes e Jovens do Sexo Feminino: <?php echo $totals[$district->district_code]['total_femininos'] ?></b></td>
                                        </tr> 
                                        
                                        <tr>
                                            <td colspan="6" bgcolor="#FFFFFF"><b>Total de Adolescentes e Jovens do Sexo Masculino: <?php echo $totals[$district->district_code]['total_masculinos'] ?></b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" bgcolor="#FFFFFF"><b> Total de Beneficiárias no Indicador AGYW_PREV: 
                                            <?php if($totalsAgyw[$district->district_code] > 0){ ?>
                                                    <?= Html::a('<span style="color:#0549a3;"> '.($totalsAgyw[$district->district_code]).'</span>', ['relatorioagywprev', 
                                                                                                                                                        'aBand' => 'Todos',
                                                                                                                                                        'eTime' => 'Todos',
                                                                                                                                                        'pcode' => $district->province_code,
                                                                                                                                                        'dcode' => $district->district_code,
                                                                                                                                                        'sdate' => $model->start_date,
                                                                                                                                                        'edate' => $model->end_date,
                                                                                                                                                        'iID' => 0], []) ?>
                                                    <?php  } else { ?>
                                                        <span style="color:#0549a3;"> <?= $totalsAgyw[$district->district_code]?> </span>
                                                    <?php  } ?>
                                            </b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" height="40" bgcolor="#FFFFFF"><b> </b></td>
                                        </tr>

                                        <tr>
                                            <td width="706" colspan="7" bgcolor="#F4A460"><b>Beneficiaries that have fully completed the DREAMS primary package of services/interventions but no additional services/interventions: 
                                            <?php if(($firstdesagregationResults['9-14']['0_6'] + $firstdesagregationResults['15-19']['0_6'] + $firstdesagregationResults['20-24']['0_6'] + $firstdesagregationResults['25-29']['0_6'] +
                                                        $firstdesagregationResults['9-14']['7_12'] + $firstdesagregationResults['15-19']['7_12'] + $firstdesagregationResults['20-24']['7_12'] + $firstdesagregationResults['25-29']['7_12'] +
                                                        $firstdesagregationResults['9-14']['13_24'] + $firstdesagregationResults['15-19']['13_24'] + $firstdesagregationResults['20-24']['13_24'] + $firstdesagregationResults['25-29']['13_24'] +
                                                        $firstdesagregationResults['9-14']['25+'] + $firstdesagregationResults['15-19']['25+'] + $firstdesagregationResults['20-24']['25+'] + $firstdesagregationResults['25-29']['25+']) > 0) { ?>
                                                <?= Html::a('<span style="color:#0549a3;"> '.($firstdesagregationResults['9-14']['0_6'] + $firstdesagregationResults['15-19']['0_6'] + $firstdesagregationResults['20-24']['0_6'] + $firstdesagregationResults['25-29']['0_6'] +
                                                                                            $firstdesagregationResults['9-14']['7_12'] + $firstdesagregationResults['15-19']['7_12'] + $firstdesagregationResults['20-24']['7_12'] + $firstdesagregationResults['25-29']['7_12'] +
                                                                                            $firstdesagregationResults['9-14']['13_24'] + $firstdesagregationResults['15-19']['13_24'] + $firstdesagregationResults['20-24']['13_24'] + $firstdesagregationResults['25-29']['13_24'] +
                                                                                            $firstdesagregationResults['9-14']['25+'] + $firstdesagregationResults['15-19']['25+'] + $firstdesagregationResults['20-24']['25+'] + $firstdesagregationResults['25-29']['25+']).'</span>', ['relatorioagywprev', 
                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                    'iID' => 1], []) ?>
                                            <?php  } else { ?>
                                                <span style="color:#0549a3;"> 0 </span>
                                            <?php  } ?>
                                            </b> </td>
                                        </tr>
                                        <tr>
                                            <td width="174" bgcolor="#FFDEAD">Tempo de registo como beneficiário DREAMS </td>
                                            <td bgcolor="#FFDEAD">10-14</td>
                                            <td bgcolor="#FFDEAD">15-19</td>
                                            <td bgcolor="#FFDEAD">20-24</td>
                                            <td bgcolor="#FFDEAD">25-29</td>
                                            <td bgcolor="#FFDEAD"><b>SUB-TOTAL</b></td>
                                        </tr>
                                        <?php foreach(['0_6','7_12', '13_24', '25+'] as $index2){ ?>
                                                <tr>
                                                    <td bgcolor="#FAEBD7"><?php echo $index2 ?> meses </td>
                                                    <?php foreach(['9-14','15-19', '20-24', '25-29'] as $index1){ ?>
                                                        <td bgcolor="#FAEBD7"> 
                                                            
                                                            <?php if( $firstdesagregationResults[$index1][$index2] > 0 ){ ?>
                                                                <?= Html::a('<span style="color:#0549a3;"> '.$firstdesagregationResults[$index1][$index2].'</span>', ['relatorioagywprev', 
                                                                                                                                                                    'aBand' => $index1,
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 1], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $firstdesagregationResults[$index1][$index2]?> </span>
                                                            <?php  } ?>
                                                        
                                                        </td>
                                                    <?php  } ?>
                                                    <td bgcolor="#FAEBD7"><b>
                                                        <?php if($firstdesagregationResults['9-14'][$index2] + $firstdesagregationResults['15-19'][$index2] + $firstdesagregationResults['20-24'][$index2] + $firstdesagregationResults['25-29'][$index2] > 0 ){ ?>
                                                            <?= Html::a('<span style="color:#0549a3;"> '.($firstdesagregationResults['9-14'][$index2] + $firstdesagregationResults['15-19'][$index2] + $firstdesagregationResults['20-24'][$index2] + $firstdesagregationResults['25-29'][$index2]).'</span>', ['relatorioagywprev', 
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 1], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $firstdesagregationResults['9-14'][$index2] + $firstdesagregationResults['15-19'][$index2] + $firstdesagregationResults['20-24'][$index2] + $firstdesagregationResults['25-29'][$index2] ?> </span>
                                                            <?php  } ?>
                                                    </b></td>
                                                </tr>
                                        <?php  } ?>
                                        <tr>
                                            <td colspan="6" height="40" bgcolor="#FFFFFF"><b> </b></td>
                                        </tr>
                                        <tr>
                                            <td width="706" colspan="6" bgcolor="#F4A460"><b>Beneficiaries that have fully completed the DREAMS primary package of services/interventions AND at least one secondary service/intervention: 
                                            <?php if(($seconddesagregationResults['9-14']['0_6'] + $seconddesagregationResults['15-19']['0_6'] + $seconddesagregationResults['20-24']['0_6'] + $seconddesagregationResults['25-29']['0_6'] +
                                                        $seconddesagregationResults['9-14']['7_12'] + $seconddesagregationResults['15-19']['7_12'] + $seconddesagregationResults['20-24']['7_12'] + $seconddesagregationResults['25-29']['7_12'] +
                                                        $seconddesagregationResults['9-14']['13_24'] + $seconddesagregationResults['15-19']['13_24'] + $seconddesagregationResults['20-24']['13_24'] + $seconddesagregationResults['25-29']['13_24'] +
                                                        $seconddesagregationResults['9-14']['25+'] + $seconddesagregationResults['15-19']['25+'] + $seconddesagregationResults['20-24']['25+'] + $seconddesagregationResults['25-29']['25+']) > 0) { ?>
                                                <?= Html::a('<span style="color:#0549a3;"> '.($seconddesagregationResults['9-14']['0_6'] + $seconddesagregationResults['15-19']['0_6'] + $seconddesagregationResults['20-24']['0_6'] + $seconddesagregationResults['25-29']['0_6'] +
                                                                                            $seconddesagregationResults['9-14']['7_12'] + $seconddesagregationResults['15-19']['7_12'] + $seconddesagregationResults['20-24']['7_12'] + $seconddesagregationResults['25-29']['7_12'] +
                                                                                            $seconddesagregationResults['9-14']['13_24'] + $seconddesagregationResults['15-19']['13_24'] + $seconddesagregationResults['20-24']['13_24'] + $seconddesagregationResults['25-29']['13_24'] +
                                                                                            $seconddesagregationResults['9-14']['25+'] + $seconddesagregationResults['15-19']['25+'] + $seconddesagregationResults['20-24']['25+'] + $seconddesagregationResults['25-29']['25+']).'</span>', ['relatorioagywprev', 
                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                    'iID' => 2], []) ?>
                                            <?php  } else { ?>
                                                <span style="color:#0549a3;"> 0 </span>
                                            <?php  } ?>
                                            </b> </td>
                                        </tr>
                                        <tr>
                                            <td width="174" bgcolor="#FFDEAD">Tempo de registo como beneficiário DREAMS </td>
                                            <td bgcolor="#FFDEAD">10-14</td>
                                            <td bgcolor="#FFDEAD">15-19</td>
                                            <td bgcolor="#FFDEAD">20-24</td>
                                            <td bgcolor="#FFDEAD">25-29</td>
                                            <td bgcolor="#FFDEAD"><b>SUB-TOTAL</b></td>
                                        </tr>
                                        <?php foreach(['0_6','7_12', '13_24', '25+'] as $index2){ ?>
                                                <tr>
                                                    <td bgcolor="#FAEBD7"><?php echo $index2 ?> meses </td>
                                                    <?php foreach(['9-14','15-19', '20-24', '25-29'] as $index1){ ?>
                                                        <td bgcolor="#FAEBD7"> 
                                                            
                                                            <?php if( $seconddesagregationResults[$index1][$index2] > 0 ){ ?>
                                                                <?= Html::a('<span style="color:#0549a3;">'.$seconddesagregationResults[$index1][$index2].'</span>', ['relatorioagywprev', 
                                                                                                                                                                    'aBand' => $index1,
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 2], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $seconddesagregationResults[$index1][$index2]?> </span>
                                                            <?php  } ?>
                                                        
                                                        </td>
                                                    <?php  } ?>
                                                    <td bgcolor="#FAEBD7"><b>
                                                        <?php if($seconddesagregationResults['9-14'][$index2] + $seconddesagregationResults['15-19'][$index2] + $seconddesagregationResults['20-24'][$index2] + $seconddesagregationResults['25-29'][$index2] > 0 ){ ?>
                                                            <?= Html::a('<span style="color:#0549a3;"> '.($seconddesagregationResults['9-14'][$index2] + $seconddesagregationResults['15-19'][$index2] + $seconddesagregationResults['20-24'][$index2] + $seconddesagregationResults['25-29'][$index2]).'</span>', ['relatorioagywprev', 
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 2], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $seconddesagregationResults['9-14'][$index2] + $seconddesagregationResults['15-19'][$index2] + $seconddesagregationResults['20-24'][$index2] + $seconddesagregationResults['25-29'][$index2] ?> </span>
                                                            <?php  } ?>
                                                    </b></td>
                                                </tr>
                                        <?php  } ?>
                                        <tr>
                                            <td colspan="5" height="40" bgcolor="#FFFFFF"><b> </b></td>
                                        </tr>
                                        <tr>
                                            <td width="706" colspan="6" bgcolor="#F4A460"><b>Beneficiaries that have completed at least one DREAMS service/intervention but not the full primary package: 
                                            <?php if(($thirddesagregationResults['9-14']['0_6'] + $thirddesagregationResults['15-19']['0_6'] + $thirddesagregationResults['20-24']['0_6'] + $thirddesagregationResults['25-29']['0_6'] +
                                                        $thirddesagregationResults['9-14']['7_12'] + $thirddesagregationResults['15-19']['7_12'] + $thirddesagregationResults['20-24']['7_12'] + $thirddesagregationResults['25-29']['7_12'] +
                                                        $thirddesagregationResults['9-14']['13_24'] + $thirddesagregationResults['15-19']['13_24'] + $thirddesagregationResults['20-24']['13_24'] + $thirddesagregationResults['25-29']['13_24'] +
                                                        $thirddesagregationResults['9-14']['25+'] + $thirddesagregationResults['15-19']['25+'] + $thirddesagregationResults['20-24']['25+'] + $thirddesagregationResults['25-29']['25+']) > 0) { ?>
                                                <?= Html::a('<span style="color:#0549a3;"> '.($thirddesagregationResults['9-14']['0_6'] + $thirddesagregationResults['15-19']['0_6'] + $thirddesagregationResults['20-24']['0_6'] + $thirddesagregationResults['25-29']['0_6'] +
                                                                                            $thirddesagregationResults['9-14']['7_12'] + $thirddesagregationResults['15-19']['7_12'] + $thirddesagregationResults['20-24']['7_12'] + $thirddesagregationResults['25-29']['7_12'] +
                                                                                            $thirddesagregationResults['9-14']['13_24'] + $thirddesagregationResults['15-19']['13_24'] + $thirddesagregationResults['20-24']['13_24'] + $thirddesagregationResults['25-29']['13_24'] +
                                                                                            $thirddesagregationResults['9-14']['25+'] + $thirddesagregationResults['15-19']['25+'] + $thirddesagregationResults['20-24']['25+'] + $thirddesagregationResults['25-29']['25+']).'</span>', ['relatorioagywprev', 
                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                    'iID' => 3], []) ?>
                                            <?php  } else { ?>
                                                <span style="color:#0549a3;"> 0 </span>
                                            <?php  } ?>
                                            </b> </td>
                                        </tr>
                                        <tr>
                                            <td width="174" bgcolor="#FFDEAD">Tempo de registo como beneficiário DREAMS </td>
                                            <td bgcolor="#FFDEAD">10-14</td>
                                            <td bgcolor="#FFDEAD">15-19</td>
                                            <td bgcolor="#FFDEAD">20-24</td>
                                            <td bgcolor="#FFDEAD">25-29</td>
                                            <td bgcolor="#FFDEAD"><b>SUB-TOTAL</b></td>
                                        </tr>
                                        <?php foreach(['0_6','7_12', '13_24', '25+'] as $index2){ ?>
                                                <tr>
                                                    <td bgcolor="#FAEBD7"><?php echo $index2 ?> meses </td>
                                                    <?php foreach(['9-14','15-19', '20-24', '25-29'] as $index1){ ?>
                                                        <td bgcolor="#FAEBD7"> 
                                                            
                                                            <?php if( $thirddesagregationResults[$index1][$index2] > 0 ){ ?>
                                                                <?= Html::a('<span style="color:#0549a3;">'.$thirddesagregationResults[$index1][$index2].'</span>', ['relatorioagywprev','aBand' => $index1,
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 3], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $thirddesagregationResults[$index1][$index2]?> </span>
                                                            <?php  } ?>
                                                                
                                                        </td>
                                                    <?php  } ?>
                                                    <td bgcolor="#FAEBD7"><b>
                                                        <?php if($thirddesagregationResults['9-14'][$index2] + $thirddesagregationResults['15-19'][$index2] + $thirddesagregationResults['20-24'][$index2] + $thirddesagregationResults['25-29'][$index2] > 0 ){ ?>
                                                            <?= Html::a('<span style="color:#0549a3;"> '.($thirddesagregationResults['9-14'][$index2] + $thirddesagregationResults['15-19'][$index2] + $thirddesagregationResults['20-24'][$index2] + $thirddesagregationResults['25-29'][$index2]).'</span>', ['relatorioagywprev', 
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 3], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $thirddesagregationResults['9-14'][$index2] + $thirddesagregationResults['15-19'][$index2] + $thirddesagregationResults['20-24'][$index2] + $thirddesagregationResults['25-29'][$index2] ?> </span>
                                                            <?php  } ?>
                                                    </b></td>
                                                </tr>
                                        <?php  } ?>
                                        <tr>
                                            <td colspan="5" height="40" bgcolor="#FFFFFF"><b> </b></td>
                                        </tr>
                                        <tr>
                                            <td width="706" colspan="6" bgcolor="#F4A460"><b>Beneficiaries that have started a DREAMS service/intervention but have not yet completed it: 
                                            <?php if(($fourthdesagregationResults['9-14']['0_6'] + $fourthdesagregationResults['15-19']['0_6'] + $fourthdesagregationResults['20-24']['0_6'] + $fourthdesagregationResults['25-29']['0_6'] +
                                                        $fourthdesagregationResults['9-14']['7_12'] + $fourthdesagregationResults['15-19']['7_12'] + $fourthdesagregationResults['20-24']['7_12'] + $fourthdesagregationResults['25-29']['7_12'] +
                                                        $fourthdesagregationResults['9-14']['13_24'] + $fourthdesagregationResults['15-19']['13_24'] + $fourthdesagregationResults['20-24']['13_24'] + $fourthdesagregationResults['25-29']['13_24'] +
                                                        $fourthdesagregationResults['9-14']['25+'] + $fourthdesagregationResults['15-19']['25+'] + $fourthdesagregationResults['20-24']['25+'] + $fourthdesagregationResults['25-29']['25+']) > 0) { ?>
                                                <?= Html::a('<span style="color:#0549a3;"> '.($fourthdesagregationResults['9-14']['0_6'] + $fourthdesagregationResults['15-19']['0_6'] + $fourthdesagregationResults['20-24']['0_6'] + $fourthdesagregationResults['25-29']['0_6'] +
                                                                                            $fourthdesagregationResults['9-14']['7_12'] + $fourthdesagregationResults['15-19']['7_12'] + $fourthdesagregationResults['20-24']['7_12'] + $fourthdesagregationResults['25-29']['7_12'] +
                                                                                            $fourthdesagregationResults['9-14']['13_24'] + $fourthdesagregationResults['15-19']['13_24'] + $fourthdesagregationResults['20-24']['13_24'] + $fourthdesagregationResults['25-29']['13_24'] +
                                                                                            $fourthdesagregationResults['9-14']['25+'] + $fourthdesagregationResults['15-19']['25+'] + $fourthdesagregationResults['20-24']['25+'] + $fourthdesagregationResults['25-29']['25+']).'</span>', ['relatorioagywprev', 
                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                    'iID' => 4], []) ?>
                                            <?php  } else { ?>
                                                <span style="color:#0549a3;"> 0 </span>
                                            <?php  } ?>
                                            </b> </td>
                                        </tr>
                                        <tr>
                                            <td width="174" bgcolor="#FFDEAD">Tempo de registo como beneficiário DREAMS </td>
                                            <td bgcolor="#FFDEAD">10-14</td>
                                            <td bgcolor="#FFDEAD">15-19</td>
                                            <td bgcolor="#FFDEAD">20-24</td>
                                            <td bgcolor="#FFDEAD">25-29</td>
                                            <td bgcolor="#FFDEAD"><b>SUB-TOTAL</b></td>
                                        </tr>
                                        <?php foreach(['0_6','7_12', '13_24', '25+'] as $index2){ ?>
                                                <tr>
                                                    <td bgcolor="#FAEBD7"><?php echo $index2 ?> meses </td>
                                                    <?php foreach(['9-14','15-19', '20-24', '25-29'] as $index1){ ?>
                                                        <td bgcolor="#FAEBD7"> 
                                                            
                                                            <?php if( $fourthdesagregationResults[$index1][$index2] > 0 ){ ?>
                                                                <?= Html::a('<span style="color:#0549a3;">'.$fourthdesagregationResults[$index1][$index2].'</span>', ['relatorioagywprev','aBand' => $index1,
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 4], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $fourthdesagregationResults[$index1][$index2]?> </span>
                                                            <?php  } ?>
                                                        
                                                        </td>
                                                    <?php  } ?>
                                                    <td bgcolor="#FAEBD7"><b>
                                                        <?php if($fourthdesagregationResults['9-14'][$index2] + $fourthdesagregationResults['15-19'][$index2] + $fourthdesagregationResults['20-24'][$index2] + $fourthdesagregationResults['25-29'][$index2] > 0 ){ ?>
                                                            <?= Html::a('<span style="color:#0549a3;"> '.($fourthdesagregationResults['9-14'][$index2] + $fourthdesagregationResults['15-19'][$index2] + $fourthdesagregationResults['20-24'][$index2] + $fourthdesagregationResults['25-29'][$index2]).'</span>', ['relatorioagywprev', 
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 4], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $fourthdesagregationResults['9-14'][$index2] + $fourthdesagregationResults['15-19'][$index2] + $fourthdesagregationResults['20-24'][$index2] + $fourthdesagregationResults['25-29'][$index2] ?> </span>
                                                            <?php  } ?>
                                                    </b></td>
                                                </tr>
                                        <?php  } ?>
                                        <tr>
                                            <td colspan="5" height="40" bgcolor="#FFFFFF"><b> </b></td>
                                        </tr>
                                        <tr>
                                            <td width="706" colspan="6" bgcolor="#F4A460"><b>Number of AGYW enrolled in DREAMS that completed an evidence-based intervention focused on preventing violence within the reporting period: 
                                            <?php if(($fifthdesagregationResults['9-14']['0_6'] + $fifthdesagregationResults['15-19']['0_6'] + $fifthdesagregationResults['20-24']['0_6'] + $fifthdesagregationResults['25-29']['0_6'] +
                                                        $fifthdesagregationResults['9-14']['7_12'] + $fifthdesagregationResults['15-19']['7_12'] + $fifthdesagregationResults['20-24']['7_12'] + $fifthdesagregationResults['25-29']['7_12'] +
                                                        $fifthdesagregationResults['9-14']['13_24'] + $fifthdesagregationResults['15-19']['13_24'] + $fifthdesagregationResults['20-24']['13_24'] + $fifthdesagregationResults['25-29']['13_24'] +
                                                        $fifthdesagregationResults['9-14']['25+'] + $fifthdesagregationResults['15-19']['25+'] + $fifthdesagregationResults['20-24']['25+'] + $fifthdesagregationResults['25-29']['25+']) > 0) { ?>
                                                <?= Html::a('<span style="color:#0549a3;"> '.($fifthdesagregationResults['9-14']['0_6'] + $fifthdesagregationResults['15-19']['0_6'] + $fifthdesagregationResults['20-24']['0_6'] + $fifthdesagregationResults['25-29']['0_6'] +
                                                                                            $fifthdesagregationResults['9-14']['7_12'] + $fifthdesagregationResults['15-19']['7_12'] + $fifthdesagregationResults['20-24']['7_12'] + $fifthdesagregationResults['25-29']['7_12'] +
                                                                                            $fifthdesagregationResults['9-14']['13_24'] + $fifthdesagregationResults['15-19']['13_24'] + $fifthdesagregationResults['20-24']['13_24'] + $fifthdesagregationResults['25-29']['13_24'] +
                                                                                            $fifthdesagregationResults['9-14']['25+'] + $fifthdesagregationResults['15-19']['25+'] + $fifthdesagregationResults['20-24']['25+'] + $fifthdesagregationResults['25-29']['25+']).'</span>', ['relatorioagywprev', 
                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                    'iID' => 5], []) ?>
                                            <?php  } else { ?>
                                                <span style="color:#0549a3;"> 0 </span>
                                            <?php  } ?>
                                            </b> </td>
                                        </tr>
                                        <tr>
                                            <td width="174" bgcolor="#FFDEAD">Tempo de registo como beneficiário DREAMS </td>
                                            <td bgcolor="#FFDEAD">10-14</td>
                                            <td bgcolor="#FFDEAD">15-19</td>
                                            <td bgcolor="#FFDEAD">20-24</td>
                                            <td bgcolor="#FFDEAD">25-29</td>
                                            <td bgcolor="#FFDEAD"><b>SUB-TOTAL</b></td>
                                        </tr>
                                        <?php foreach(['0_6','7_12', '13_24', '25+'] as $index2){ ?>
                                                <tr>
                                                    <td bgcolor="#FAEBD7"><?php echo $index2 ?> meses </td>
                                                    <?php foreach(['9-14','15-19', '20-24', '25-29'] as $index1){ ?>
                                                        <td bgcolor="#FAEBD7"> 
                                                            
                                                            <?php if( $fifthdesagregationResults[$index1][$index2] > 0 ){ ?>
                                                                <?= Html::a('<span style="color:#0549a3;">'.$fifthdesagregationResults[$index1][$index2].'</span>', ['relatorioagywprev','aBand' => $index1,
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 5], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $fifthdesagregationResults[$index1][$index2]?> </span>
                                                            <?php  } ?>
                                                        
                                                        </td>
                                                    <?php  } ?>
                                                    <td bgcolor="#FAEBD7"><b>
                                                        <?php if($fifthdesagregationResults['9-14'][$index2] + $fifthdesagregationResults['15-19'][$index2] + $fifthdesagregationResults['20-24'][$index2] + $fifthdesagregationResults['25-29'][$index2] > 0 ){ ?>
                                                            <?= Html::a('<span style="color:#0549a3;"> '.($fifthdesagregationResults['9-14'][$index2] + $fifthdesagregationResults['15-19'][$index2] + $fifthdesagregationResults['20-24'][$index2] + $fifthdesagregationResults['25-29'][$index2]).'</span>', ['relatorioagywprev', 
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 5], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $fifthdesagregationResults['9-14'][$index2] + $fifthdesagregationResults['15-19'][$index2] + $fifthdesagregationResults['20-24'][$index2] + $fifthdesagregationResults['25-29'][$index2] ?> </span>
                                                            <?php  } ?>
                                                    </b></td>
                                                </tr>
                                        <?php  } ?>
                                        <tr>
                                            <td colspan="5" height="40" bgcolor="#FFFFFF"><b> </b></td>
                                        </tr>
                                        <tr>
                                            <td width="706" colspan="6" bgcolor="#F4A460"><b>Number of AGYW enrolled in DREAMS that received educational support to remain in, advance, and/or rematriculate in school within the reporting period: 
                                            <?php if(($sixthdesagregationResults['9-14']['0_6'] + $sixthdesagregationResults['15-19']['0_6'] + $sixthdesagregationResults['20-24']['0_6'] + $sixthdesagregationResults['25-29']['0_6'] +
                                                        $sixthdesagregationResults['9-14']['7_12'] + $sixthdesagregationResults['15-19']['7_12'] + $sixthdesagregationResults['20-24']['7_12'] + $sixthdesagregationResults['25-29']['7_12'] +
                                                        $sixthdesagregationResults['9-14']['13_24'] + $sixthdesagregationResults['15-19']['13_24'] + $sixthdesagregationResults['20-24']['13_24'] + $sixthdesagregationResults['25-29']['13_24'] +
                                                        $sixthdesagregationResults['9-14']['25+'] + $sixthdesagregationResults['15-19']['25+'] + $sixthdesagregationResults['20-24']['25+'] + $sixthdesagregationResults['25-29']['25+']) > 0) { ?>
                                                <?= Html::a('<span style="color:#0549a3;"> '.($sixthdesagregationResults['9-14']['0_6'] + $sixthdesagregationResults['15-19']['0_6'] + $sixthdesagregationResults['20-24']['0_6'] + $sixthdesagregationResults['25-29']['0_6'] +
                                                                                            $sixthdesagregationResults['9-14']['7_12'] + $sixthdesagregationResults['15-19']['7_12'] + $sixthdesagregationResults['20-24']['7_12'] + $sixthdesagregationResults['25-29']['7_12'] +
                                                                                            $sixthdesagregationResults['9-14']['13_24'] + $sixthdesagregationResults['15-19']['13_24'] + $sixthdesagregationResults['20-24']['13_24'] + $sixthdesagregationResults['25-29']['13_24'] +
                                                                                            $sixthdesagregationResults['9-14']['25+'] + $sixthdesagregationResults['15-19']['25+'] + $sixthdesagregationResults['20-24']['25+'] + $sixthdesagregationResults['25-29']['25+']).'</span>', ['relatorioagywprev', 
                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                    'iID' => 6], []) ?>
                                            <?php  } else { ?>
                                                <span style="color:#0549a3;"> 0 </span>
                                            <?php  } ?>
                                            </b> </td>
                                        </tr>
                                        <tr>
                                            <td width="174" bgcolor="#FFDEAD">Tempo de registo como beneficiário DREAMS </td>
                                            <td bgcolor="#FFDEAD">10-14</td>
                                            <td bgcolor="#FFDEAD">15-19</td>
                                            <td bgcolor="#FFDEAD">20-24</td>
                                            <td bgcolor="#FFDEAD">25-29</td>
                                            <td bgcolor="#FFDEAD"><b>SUB-TOTAL</b></td>
                                        </tr>
                                        <?php foreach(['0_6','7_12', '13_24', '25+'] as $index2){ ?>
                                                <tr>
                                                    <td bgcolor="#FAEBD7"><?php echo $index2 ?> meses </td>
                                                    <?php foreach(['9-14','15-19', '20-24', '25-29'] as $index1){ ?>
                                                        <td bgcolor="#FAEBD7"> 
                                                            <?php if( $sixthdesagregationResults[$index1][$index2] > 0 ){ ?>
                                                            
                                                                <?= Html::a('<span style="color:#0549a3;">'.$sixthdesagregationResults[$index1][$index2].'</span>', ['relatorioagywprev','aBand' => $index1,
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 6], []) ?>

                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $sixthdesagregationResults[$index1][$index2]?> </span>
                                                            <?php  } ?>
                                                        </td>
                                                    <?php  } ?>
                                                    <td bgcolor="#FAEBD7"><b>
                                                        <?php if($sixthdesagregationResults['9-14'][$index2] + $sixthdesagregationResults['15-19'][$index2] + $sixthdesagregationResults['20-24'][$index2] + $sixthdesagregationResults['25-29'][$index2] > 0 ){ ?>
                                                            <?= Html::a('<span style="color:#0549a3;"> '.($sixthdesagregationResults['9-14'][$index2] + $sixthdesagregationResults['15-19'][$index2] + $sixthdesagregationResults['20-24'][$index2] + $sixthdesagregationResults['25-29'][$index2]).'</span>', ['relatorioagywprev', 
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 6], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $sixthdesagregationResults['9-14'][$index2] + $sixthdesagregationResults['15-19'][$index2] + $sixthdesagregationResults['20-24'][$index2] + $sixthdesagregationResults['25-29'][$index2] ?> </span>
                                                            <?php  } ?>
                                                    </b></td>
                                                </tr>
                                        <?php  } ?>
                                        <tr>
                                            <td colspan="5" height="40" bgcolor="#FFFFFF"><b> </b></td>
                                        </tr>
                                        <tr>
                                            <td width="706" colspan="6" bgcolor="#F4A460"><b>Number of AGYW ages 15-24 years enrolled in DREAMS that completed a comprehensive economic strengthening intervention within the reporting period: 
                                            <?php if(($seventhdesagregationResults['9-14']['0_6'] + $seventhdesagregationResults['15-19']['0_6'] + $seventhdesagregationResults['20-24']['0_6'] + $seventhdesagregationResults['25-29']['0_6'] +
                                                        $seventhdesagregationResults['9-14']['7_12'] + $seventhdesagregationResults['15-19']['7_12'] + $seventhdesagregationResults['20-24']['7_12'] + $seventhdesagregationResults['25-29']['7_12'] +
                                                        $seventhdesagregationResults['9-14']['13_24'] + $seventhdesagregationResults['15-19']['13_24'] + $seventhdesagregationResults['20-24']['13_24'] + $seventhdesagregationResults['25-29']['13_24'] +
                                                        $seventhdesagregationResults['9-14']['25+'] + $seventhdesagregationResults['15-19']['25+'] + $seventhdesagregationResults['20-24']['25+'] + $seventhdesagregationResults['25-29']['25+']) > 0) { ?>
                                                <?= Html::a('<span style="color:#0549a3;"> '.($seventhdesagregationResults['9-14']['0_6'] + $seventhdesagregationResults['15-19']['0_6'] + $seventhdesagregationResults['20-24']['0_6'] + $seventhdesagregationResults['25-29']['0_6'] +
                                                                                            $seventhdesagregationResults['9-14']['7_12'] + $seventhdesagregationResults['15-19']['7_12'] + $seventhdesagregationResults['20-24']['7_12'] + $seventhdesagregationResults['25-29']['7_12'] +
                                                                                            $seventhdesagregationResults['9-14']['13_24'] + $seventhdesagregationResults['15-19']['13_24'] + $seventhdesagregationResults['20-24']['13_24'] + $seventhdesagregationResults['25-29']['13_24'] +
                                                                                            $seventhdesagregationResults['9-14']['25+'] + $seventhdesagregationResults['15-19']['25+'] + $seventhdesagregationResults['20-24']['25+'] + $seventhdesagregationResults['25-29']['25+']).'</span>', ['relatorioagywprev', 
                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                    'iID' => 7], []) ?>
                                            <?php  } else { ?>
                                                <span style="color:#0549a3;"> 0 </span>
                                            <?php  } ?>
                                            </b> </td>
                                        </tr>
                                        <tr>
                                            <td width="174" bgcolor="#FFDEAD">Tempo de registo como beneficiário DREAMS </td>
                                            <td bgcolor="#FFDEAD">10-14</td>
                                            <td bgcolor="#FFDEAD">15-19</td>
                                            <td bgcolor="#FFDEAD">20-24</td>
                                            <td bgcolor="#FFDEAD">25-29</td>
                                            <td bgcolor="#FFDEAD"><b>SUB-TOTAL</b></td>
                                        </tr>
                                        <?php foreach(['0_6','7_12', '13_24', '25+'] as $index2){ ?>
                                                <tr>
                                                    <td bgcolor="#FAEBD7"><?php echo $index2 ?> meses </td>
                                                    <?php foreach(['9-14','15-19', '20-24', '25-29'] as $index1){ ?>
                                                        <td bgcolor="#FAEBD7"> 
                                                            <?php if( $seventhdesagregationResults[$index1][$index2] > 0 ){ ?>

                                                                <?= Html::a('<span style="color:#0549a3;">'.$seventhdesagregationResults[$index1][$index2].'</span>', ['relatorioagywprev','aBand' => $index1,
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 7], []) ?>

                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $seventhdesagregationResults[$index1][$index2]?> </span>
                                                            <?php  } ?>
                                                        </td>
                                                    <?php  } ?>
                                                    <td bgcolor="#FAEBD7"><b>
                                                        <?php if($seventhdesagregationResults['9-14'][$index2] + $seventhdesagregationResults['15-19'][$index2] + $seventhdesagregationResults['20-24'][$index2] + $seventhdesagregationResults['25-29'][$index2] > 0 ){ ?>
                                                            <?= Html::a('<span style="color:#0549a3;"> '.($seventhdesagregationResults['9-14'][$index2] + $seventhdesagregationResults['15-19'][$index2] + $seventhdesagregationResults['20-24'][$index2] + $seventhdesagregationResults['25-29'][$index2]).'</span>', ['relatorioagywprev', 
                                                                                                                                                                    'eTime' => $index2,
                                                                                                                                                                    'pcode' => $district->province_code,
                                                                                                                                                                    'dcode' => $district->district_code,
                                                                                                                                                                    'sdate' => $model->start_date,
                                                                                                                                                                    'edate' => $model->end_date,
                                                                                                                                                                    'iID' => 7], []) ?>
                                                            <?php  } else { ?>
                                                                <span style="color:#0549a3;"> <?= $seventhdesagregationResults['9-14'][$index2] + $seventhdesagregationResults['15-19'][$index2] + $seventhdesagregationResults['20-24'][$index2] + $seventhdesagregationResults['25-29'][$index2] ?> </span>
                                                            <?php  } ?>
                                                    </b></td>
                                                </tr>
                                        <?php  } ?>
                                        <tr>
                                            <td colspan="5" height="40" bgcolor="#FFFFFF"><b> </b></td>
                                        </tr>
                                        
                                    </table>
                                </div>
                            <?php  } ?> 
                        <?php  } ?> 
                    </div>
                <?php  } ?>                                        
            </div>
        </div>
    </div>
</div>