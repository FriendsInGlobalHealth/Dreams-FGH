<?php

namespace backend\controllers;

use Yii;
use app\models\Beneficiarios;
use app\models\BeneficiariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;


use yii\filters\AccessControl;
use common\models\User;
use common\components\AccessRule;
use app\models\Distritos;
use app\models\ComiteLocalidades;
use app\models\SubServicosDreams;
use app\models\ServicosDream;
use app\models\Bairros;
use app\models\Provincias;
use app\models\Organizacoes;
use app\models\AgywPrev;
use PHPExcel;
use PHPExcel_IOFactory;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/**
 * BeneficiariosController implements the CRUD actions for Beneficiarios model.
 */
class BeneficiariosController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'lists','listasdistritos','listaspostos', 'listas', 'servicos', 'localidades', 'bairros', 'todos', 'filtros', 'relatorio', 'relatoriofy19', 'relatoriofy20q1', 'relatoriofy20q2'],

                        'allow' => true,
                        'roles' => [
                            User::ROLE_USER,
                            User::ROLE_MA,
                            User::ROLE_ADMIN,
                            User::ROLE_GESTOR,
                            User::ROLE_DIGITADOR,
                            User::ROLE_EDUCADOR_DE_PAR,
                            User::ROLE_MENTOR,
                            User::ROLE_ENFERMEIRA,
                            User::ROLE_CORDENADOR
                        ],
                    ],

                    [
                        'actions' => ['update', 'referidos'],
                        'allow' => true,
                        //  'roles' => ['@'],
                        'roles' => [
                            User::ROLE_USER,
                            User::ROLE_ADMIN,
                            User::ROLE_GESTOR,
                            User::ROLE_DIGITADOR,
                            User::ROLE_EDUCADOR_DE_PAR,
                            User::ROLE_MENTOR,
                            User::ROLE_ENFERMEIRA,
                            User::ROLE_CORDENADOR
                        ],
                    ],

                    [
                        'actions' => ['relatorioagyw',  'relatorioagywprev', 'exportlist', 'exportreport', 'listdistricts'],
                        'allow' => true,
                        'roles' => [
                            User::ROLE_ADMIN,
                            User::ROLE_DOADOR
                        ],
                    ],

                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return User::isUserAdmin(Yii::$app->user->identity->username);
                        }
                    ],
                ]
            ]
        ];
    }

    
    public function actionExportreport(){
      
        $model = new AgywPrev();
        if ($model->load(Yii::$app->request->post()) /*&& $model->validate()*/) {
            $province = Provincias::find()
                    ->where(['id' => $model->province_code])->one();

            $district = Distritos::find()
                    ->where(['district_code' => $model->district_code])->one();
           
            $model->execute();

            $firstdesagregation = $model->getFirstDesagregationResults();
            $seconddesagregation = $model->getSecondDesagregationResults();
            $thirddesagregation = $model->getThirdDesagregationResults();
            $fourthdesagregation = $model->getFourthDesagregationResults();
            $fifthdesagregation = $model->getFifthDesagregationResults();
            $sixthdesagregation = $model->getSixthDesagregationResults();
            $seventhdesagregation = $model->getSeventhDesagregationResults();

            // load report template
            $tmpfname = 'template_agywprev.xls';
            $excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
            $excelObj = $excelReader->load($tmpfname);
            $excelObj->setActiveSheetIndex(0);
            

            // fill the report 
            // report identification
            $row = 9; //starting row in the template
            foreach($model->districts as $districtId){
                $total = 0;
                //echo json_encode($districtId);
                $district = Distritos::find()
                    ->where(['district_code' => $districtId])->one();

                $province = Provincias::find()
                    ->where(['id' => $district->province_code])->one();

                $excelObj->getActiveSheet()
                        ->setCellValue('A'.$row, $model->start_date.' - '.$model->end_date)
                        ->setCellValue('B'.$row, $province->province_name)
                        ->setCellValue('C'.$row, $district->district_name);
                
                // report first desagregation
                $firstdesagregationResults = $firstdesagregation[$districtId]['results'];
                $subtotal1 = $firstdesagregationResults['9-14']['0_6'] + $firstdesagregationResults['15-19']['0_6'] + $firstdesagregationResults['20-24']['0_6']+$firstdesagregationResults['25-29']['0_6'];
                $subtotal2 = $firstdesagregationResults['9-14']['7_12'] + $firstdesagregationResults['15-19']['7_12'] + $firstdesagregationResults['20-24']['7_12']+$firstdesagregationResults['25-29']['7_12'];
                $subtotal3 = $firstdesagregationResults['9-14']['13_24'] + $firstdesagregationResults['15-19']['13_24'] + $firstdesagregationResults['20-24']['13_24']+$firstdesagregationResults['25-29']['13_24'];
                $subtotal4 = $firstdesagregationResults['9-14']['25+'] + $firstdesagregationResults['15-19']['25+'] + $firstdesagregationResults['20-24']['25+']+$firstdesagregationResults['25-29']['25+'];

                $excelObj->getActiveSheet()
                ->setCellValue('E'.$row, $firstdesagregationResults['9-14']['0_6'])
                ->setCellValue('F'.$row, $firstdesagregationResults['15-19']['0_6'])
                ->setCellValue('G'.$row, $firstdesagregationResults['20-24']['0_6'])
                ->setCellValue('H'.$row, $firstdesagregationResults['25-29']['0_6'])
                ->setCellValue('I'.$row, $subtotal1)
                ->setCellValue('J'.$row, $firstdesagregationResults['9-14']['7_12'])
                ->setCellValue('K'.$row, $firstdesagregationResults['15-19']['7_12'])
                ->setCellValue('L'.$row, $firstdesagregationResults['20-24']['7_12'])
                ->setCellValue('M'.$row, $firstdesagregationResults['25-29']['7_12'])
                ->setCellValue('N'.$row, $subtotal2)
                ->setCellValue('O'.$row, $firstdesagregationResults['9-14']['13_24'])
                ->setCellValue('P'.$row, $firstdesagregationResults['15-19']['13_24'])
                ->setCellValue('Q'.$row, $firstdesagregationResults['20-24']['13_24'])
                ->setCellValue('R'.$row, $firstdesagregationResults['25-29']['13_24'])
                ->setCellValue('S'.$row, $subtotal3)
                ->setCellValue('T'.$row, $firstdesagregationResults['9-14']['25+'])
                ->setCellValue('U'.$row, $firstdesagregationResults['15-19']['25+'])
                ->setCellValue('V'.$row, $firstdesagregationResults['20-24']['25+'])
                ->setCellValue('W'.$row, $firstdesagregationResults['25-29']['25+'])
                ->setCellValue('X'.$row, $subtotal4);
                
                // report second desagregation
                $seconddesagregationResults = $seconddesagregation[$districtId]['results'];
                $subtotal5 = $seconddesagregationResults['9-14']['0_6'] + $seconddesagregationResults['15-19']['0_6'] + $seconddesagregationResults['20-24']['0_6']+$seconddesagregationResults['25-29']['0_6'];
                $subtotal6 = $seconddesagregationResults['9-14']['7_12'] + $seconddesagregationResults['15-19']['7_12'] + $seconddesagregationResults['20-24']['7_12']+$seconddesagregationResults['25-29']['7_12'];
                $subtotal7 = $seconddesagregationResults['9-14']['13_24'] + $seconddesagregationResults['15-19']['13_24'] + $seconddesagregationResults['20-24']['13_24']+$seconddesagregationResults['25-29']['13_24'];
                $subtotal8 = $seconddesagregationResults['9-14']['25+'] + $seconddesagregationResults['15-19']['25+'] + $seconddesagregationResults['20-24']['25+']+$seconddesagregationResults['25-29']['25+'];
                
                $excelObj->getActiveSheet()
                ->setCellValue('Y'.$row, json_encode($seconddesagregationResults['9-14']['0_6']))
                ->setCellValue('Z'.$row, $seconddesagregationResults['15-19']['0_6'])
                ->setCellValue('AA'.$row, $seconddesagregationResults['20-24']['0_6'])
                ->setCellValue('AB'.$row, $seconddesagregationResults['25-29']['0_6'])
                ->setCellValue('AC'.$row, $subtotal5)
                ->setCellValue('AD'.$row, $seconddesagregationResults['9-14']['7_12'])
                ->setCellValue('AE'.$row, $seconddesagregationResults['15-19']['7_12'])
                ->setCellValue('AF'.$row, $seconddesagregationResults['20-24']['7_12'])
                ->setCellValue('AG'.$row, $seconddesagregationResults['25-29']['7_12'])
                ->setCellValue('AH'.$row, $subtotal6)
                ->setCellValue('AI'.$row, $seconddesagregationResults['9-14']['13_24'])
                ->setCellValue('AJ'.$row, $seconddesagregationResults['15-19']['13_24'])
                ->setCellValue('AK'.$row, $seconddesagregationResults['20-24']['13_24'])
                ->setCellValue('AL'.$row, $seconddesagregationResults['25-29']['13_24'])
                ->setCellValue('AM'.$row, $subtotal7)
                ->setCellValue('AN'.$row, $seconddesagregationResults['9-14']['25+'])
                ->setCellValue('AO'.$row, $seconddesagregationResults['15-19']['25+'])
                ->setCellValue('AP'.$row, $seconddesagregationResults['20-24']['25+'])
                ->setCellValue('AQ'.$row, $seconddesagregationResults['25-29']['25+'])
                ->setCellValue('AR'.$row, $subtotal8);

                // report third desagregation
                $thirddesagregationResults = $thirddesagregation[$districtId]['results'];
                $subtota9 = $thirddesagregationResults['9-14']['0_6'] + $thirddesagregationResults['15-19']['0_6'] + $thirddesagregationResults['20-24']['0_6']+$thirddesagregationResults['25-29']['0_6'];
                $subtota10 = $thirddesagregationResults['9-14']['7_12'] + $thirddesagregationResults['15-19']['7_12'] + $thirddesagregationResults['20-24']['7_12']+$thirddesagregationResults['25-29']['7_12'];
                $subtota11 = $thirddesagregationResults['9-14']['13_24'] + $thirddesagregationResults['15-19']['13_24'] + $thirddesagregationResults['20-24']['13_24']+$thirddesagregationResults['25-29']['13_24'];
                $subtota12 = $thirddesagregationResults['9-14']['25+'] + $thirddesagregationResults['15-19']['25+'] + $thirddesagregationResults['20-24']['25+']+$thirddesagregationResults['25-29']['25+'];
                $excelObj->getActiveSheet()
                ->setCellValue('AS'.$row, $thirddesagregationResults['9-14']['0_6'])
                ->setCellValue('AT'.$row, $thirddesagregationResults['15-19']['0_6'])
                ->setCellValue('AU'.$row, $thirddesagregationResults['20-24']['0_6'])
                ->setCellValue('AV'.$row, $thirddesagregationResults['25-29']['0_6'])
                ->setCellValue('AW'.$row, $subtota9)
                ->setCellValue('AX'.$row, $thirddesagregationResults['9-14']['7_12'])
                ->setCellValue('AY'.$row, $thirddesagregationResults['15-19']['7_12'])
                ->setCellValue('AZ'.$row, $thirddesagregationResults['20-24']['7_12'])
                ->setCellValue('BA'.$row, $thirddesagregationResults['25-29']['7_12'])
                ->setCellValue('BB'.$row, $subtota10)
                ->setCellValue('BC'.$row, $thirddesagregationResults['9-14']['13_24'])
                ->setCellValue('BD'.$row, $thirddesagregationResults['15-19']['13_24'])
                ->setCellValue('BE'.$row, $thirddesagregationResults['20-24']['13_24'])
                ->setCellValue('BF'.$row, $thirddesagregationResults['25-29']['13_24'])
                ->setCellValue('BG'.$row, $subtota11)
                ->setCellValue('BH'.$row, $thirddesagregationResults['9-14']['25+'])
                ->setCellValue('BI'.$row, $thirddesagregationResults['15-19']['25+'])
                ->setCellValue('BJ'.$row, $thirddesagregationResults['20-24']['25+'])
                ->setCellValue('BK'.$row, $thirddesagregationResults['25-29']['25+'])
                ->setCellValue('BL'.$row, $subtota12);

                // report fourth desagregation
                $fourthdesagregationResults = $fourthdesagregation[$districtId]['results'];
                $subtotal13 = $fourthdesagregationResults['9-14']['0_6'] + $fourthdesagregationResults['15-19']['0_6'] + $fourthdesagregationResults['20-24']['0_6']+$fourthdesagregationResults['25-29']['0_6'];
                $subtotal14 = $fourthdesagregationResults['9-14']['7_12'] + $fourthdesagregationResults['15-19']['7_12'] + $fourthdesagregationResults['20-24']['7_12']+$fourthdesagregationResults['25-29']['7_12'];
                $subtotal15 = $fourthdesagregationResults['9-14']['13_24'] + $fourthdesagregationResults['15-19']['13_24'] + $fourthdesagregationResults['20-24']['13_24']+$fourthdesagregationResults['25-29']['13_24'];
                $subtotal16 = $fourthdesagregationResults['9-14']['25+'] + $fourthdesagregationResults['15-19']['25+'] + $fourthdesagregationResults['20-24']['25+']+$fourthdesagregationResults['25-29']['25+'];


                $excelObj->getActiveSheet()
                ->setCellValue('BM'.$row, $fourthdesagregationResults['9-14']['0_6'])
                ->setCellValue('BN'.$row, $fourthdesagregationResults['15-19']['0_6'])
                ->setCellValue('BO'.$row, $fourthdesagregationResults['20-24']['0_6'])
                ->setCellValue('BP'.$row, $fourthdesagregationResults['25-29']['0_6'])
                ->setCellValue('BQ'.$row, $subtotal13)
                ->setCellValue('BR'.$row, $fourthdesagregationResults['9-14']['7_12'])
                ->setCellValue('BS'.$row, $fourthdesagregationResults['15-19']['7_12'])
                ->setCellValue('BT'.$row, $fourthdesagregationResults['20-24']['7_12'])
                ->setCellValue('BU'.$row, $fourthdesagregationResults['25-29']['7_12'])
                ->setCellValue('BV'.$row, $subtotal14)
                ->setCellValue('BW'.$row, $fourthdesagregationResults['9-14']['13_24'])
                ->setCellValue('BX'.$row, $fourthdesagregationResults['15-19']['13_24'])
                ->setCellValue('BY'.$row, $fourthdesagregationResults['20-24']['13_24'])
                ->setCellValue('BZ'.$row, $fourthdesagregationResults['25-29']['13_24'])
                ->setCellValue('CA'.$row, $subtotal15)
                ->setCellValue('CB'.$row, $fourthdesagregationResults['9-14']['25+'])
                ->setCellValue('CC'.$row, $fourthdesagregationResults['15-19']['25+'])
                ->setCellValue('CD'.$row, $fourthdesagregationResults['20-24']['25+'])
                ->setCellValue('CE'.$row, $fourthdesagregationResults['25-29']['25+'])
                ->setCellValue('CF'.$row, $subtotal16);

                // report fifth desagregation
                $violencePreventionCount = 0;
                $fifthdesagregationResults = $fifthdesagregation[$districtId]['results'];
                foreach(['0_6','7_12', '13_24', '25+'] as $index2){
                    foreach(['9-14','15-19', '20-24', '25-29'] as $index1){  
                        $violencePreventionCount += $fifthdesagregationResults[$index1][$index2];
                    }
                };
                $excelObj->getActiveSheet()
                            ->setCellValue('CG'.$row, $violencePreventionCount);

                // report sixth desagregation
                $educationSupportCount = 0;
                $sixthdesagregationResults = $sixthdesagregation[$districtId]['results'];
                foreach(['0_6','7_12', '13_24', '25+'] as $index2){
                    foreach(['9-14','15-19', '20-24', '25-29'] as $index1){  
                        $educationSupportCount += $sixthdesagregationResults[$index1][$index2];
                    }
                };
                $excelObj->getActiveSheet()
                            ->setCellValue('CH'.$row, $educationSupportCount);

                // report seventh desagregation
                $economicStrengtheningCount = 0;
                $seventhdesagregationResults = $seventhdesagregation[$districtId]['results'];
                foreach(['0_6','7_12', '13_24', '25+'] as $index2){
                    foreach(['9-14','15-19', '20-24', '25-29'] as $index1){  
                        $economicStrengtheningCount += $seventhdesagregationResults[$index1][$index2];
                    }
                };
                $excelObj->getActiveSheet()
                            ->setCellValue('CI'.$row, $economicStrengtheningCount);

                //set row total
                $excelObj->getActiveSheet()
                            ->setCellValue('D'.$row, $subtotal1+$subtotal2+$subtotal3+$subtotal4+$subtotal5+$subtotal6+$subtotal7+$subtotal8+$subtota9+
                                                        $subtota10+$subtota11+$subtota12+$subtotal13+$subtotal14+$subtotal15+$subtotal16);

                $row++;
            }

            // generate report 
            $objWriter = PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
            $filename = 'PEPFAR_MER_2.6_AGYW_PREV_Semi-Annual_Indicator_' .  date('Ymd_his') . '.xls';
            $objWriter->save($filename);   
           
            ob_end_clean();  
            header('Content-type: application/xlsx');
            header('Content-Disposition: attachment; ');
            header("Pragma: no-cache");
            header("Expires: 0");

            return Yii::$app->response->sendFile($filename)->on(\yii\web\Response::EVENT_AFTER_SEND, function($event) {
                unlink($event->data);
            }, $filename);

        }
        
        return $this->render('relatorioagyw', [
            'model' => $model,
        ]);
    }

    public function actionExportlist(){
        $beneficiaries = isset($_POST['beneficiaries'])? $_POST['beneficiaries'] : null;
        
        $searchModel = new BeneficiariosSearch();
        $dataProvider = $searchModel->fetchAGYW($beneficiaries);

        $tmpfname = 'template_beneficiaries.xls';
        $excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
        $excelObj = $excelReader->load($tmpfname);
        $excelObj->setActiveSheetIndex(0);
        $worksheet = $excelObj->getActiveSheet();

        $row = 1;

        foreach($dataProvider as $data){
            $row++;
                
            $worksheet->setCellValue('A'.$row, $data['provincia']);
            $worksheet->setCellValue('B'.$row, $data['distrito']);
            $worksheet->setCellValue('C'.$row, $data['bairro']);
            $worksheet->setCellValue('D'.$row, $data['ponto_entrada']);
            $worksheet->setCellValue('E'.$row, $data['organizacao']);
            $worksheet->setCellValue('F'.$row, $data['data_registo']);
            $worksheet->setCellValue('G'.$row, $data['nui']);
            $worksheet->setCellValue('H'.$row, $data['idade_registo']);
            $worksheet->setCellValue('I'.$row, $data['idade_actual']);
            $worksheet->setCellValue('J'.$row, $data['faixa_registo']);
            $worksheet->setCellValue('K'.$row, $data['faixa_actual']);
            $worksheet->setCellValue('L'.$row, $data['data_nascimento']);
            $worksheet->setCellValue('M'.$row, $data['vulnerabilidades']);
            $worksheet->setCellValue('N'.$row, $data['tipo_servico']);
            $worksheet->setCellValue('O'.$row, $data['servico']);
            $worksheet->setCellValue('P'.$row, $data['subservico']);
            $worksheet->setCellValue('Q'.$row, $data['pacote']);
            $worksheet->setCellValue('R'.$row, $data['ponto_entrada_servico']);
            $worksheet->setCellValue('S'.$row, $data['localizacao']);
            $worksheet->setCellValue('T'.$row, $data['data_servico']);
            $worksheet->setCellValue('U'.$row, $data['provedor']);
            $worksheet->setCellValue('V'.$row, $data['observacoes']);

        }

        // generate report 
        $objWriter = PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
        $filename = 'PEPFAR_MER_2.6_AGYW_PREV_Beneficiaries_' .  date('Ymd_his') . '.xls';
        $objWriter->save($filename);   

        ob_end_clean();  
        header('Content-type: application/xlsx');
        header('Content-Disposition: attachment; ');
        header("Pragma: no-cache");
        header("Expires: 0");

        return Yii::$app->response->sendFile($filename)->on(\yii\web\Response::EVENT_AFTER_SEND, function($event) {
            unlink($event->data);
        }, $filename);

    }

    /**
     * Lists all Beneficiarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BeneficiariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        /** updated by: jordao.cololo@gmail.com on 13th July 2018
        *   O digitadores so visualizam 5 Beneficiarios por lista
        */
        Yii::$app->user->identity->role < 18 ? $dataProvider->pagination->pageSize = 5 : $dataProvider->pagination->pageSize = 10;

        if (isset(Yii::$app->user->identity->provin_code)&&Yii::$app->user->identity->provin_code>0)
        {

            $dists=Distritos::find()->where(['province_code'=>(int)Yii::$app->user->identity->provin_code])->asArray()->all();
            $dist=ArrayHelper::getColumn($dists, 'district_code');

            $orgs=Organizacoes::find()->where(['IN','distrito_id',$dist])->orderBy('parceria_id ASC')->asArray()->all();

        } else {

            $orgs=Organizacoes::find()->where(['=', 'status', 1])->orderBy('parceria_id ASC')->asArray()->all();

            $dists=Distritos::find()->asArray()->all();
            $dist=ArrayHelper::getColumn($dists, 'district_code');

        }

        $org=ArrayHelper::getColumn($orgs, 'id');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dist' => $dist,
            'org' => $org,
        ]);
    }

    public function actionReferidos($id)
    {
        return $this->render('referidos', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionRelatorioagywprev(){

        $params = Yii::$app->request->queryParams;
        $enrollmentTime = isset($params['eTime'])? $params['eTime'] : null;
        $ageBand = isset($params['aBand'])? $params['aBand'] : null;
        $province_code = isset($params['pcode'])? $params['pcode'] : null;
        $district_code = isset($params['dcode'])? $params['dcode'] : null;
        $start_date = isset($params['sdate'])? $params['sdate'] : null;
        $end_date = isset($params['edate'])? $params['edate'] : null;
        $indicatorID = isset($params['iID'])? $params['iID'] : null;

        $model = new AgywPrev();
        $model->provinces = array($province_code);
        $model->districts = array($district_code);
        
        $model->start_date = $start_date;
        $model->end_date = $end_date;

        $model->execute();

        $desagregationResults = null;
        $beneficiaries = null;

        switch($indicatorID){
            case 0: 
                    $desagregationResults = $model->getAllDisaggregationsResults();
                    $beneficiaries = $desagregationResults[$district_code]['beneficiaries'];
                    break;
            case 1: 
                    $desagregationResults = $model->getFirstDesagregationResults();
                    $firstdesagregationResults = $desagregationResults[$district_code]['beneficiaries'];
                    $beneficiaries = $this->getBeneficiaries($firstdesagregationResults, $ageBand, $enrollmentTime);
                    break;
            case 2: 
                    $desagregationResults = $model->getSecondDesagregationResults();
                    $seconddesagregationResults = $desagregationResults[$district_code]['beneficiaries'];
                    $beneficiaries = $this->getBeneficiaries($seconddesagregationResults, $ageBand, $enrollmentTime);
                    // $beneficiaries = $seconddesagregationResults[$ageBand][$enrollmentTime];
                    break;
            case 3: 
                    $desagregationResults = $model->getThirdDesagregationResults();
                    $thirddesagregationResults = $desagregationResults[$district_code]['beneficiaries'];
                    $beneficiaries = $this->getBeneficiaries($thirddesagregationResults, $ageBand, $enrollmentTime);
                    // $beneficiaries = $thirddesagregationResults[$ageBand][$enrollmentTime];
                    break;
            case 4: 
                    $desagregationResults = $model->getFourthDesagregationResults();
                    $fouthdesagregationResults = $desagregationResults[$district_code]['beneficiaries'];
                    $beneficiaries = $this->getBeneficiaries($fouthdesagregationResults, $ageBand, $enrollmentTime);
                    // $beneficiaries = $fouthdesagregationResults[$ageBand][$enrollmentTime];
                    break;
            case 5: 
                    $desagregationResults = $model->getFifthDesagregationResults();
                    $fifthdesagregationResults = $desagregationResults[$district_code]['beneficiaries'];
                    $beneficiaries = $this->getBeneficiaries($fifthdesagregationResults, $ageBand, $enrollmentTime);
                    // $beneficiaries = $fifthdesagregationResults[$ageBand][$enrollmentTime];
                    break;
            case 6: 
                    $desagregationResults = $model->getSixthDesagregationResults();
                    $sixthdesagregationResults = $desagregationResults[$district_code]['beneficiaries'];
                    $beneficiaries = $this->getBeneficiaries($sixthdesagregationResults, $ageBand, $enrollmentTime);
                    // $beneficiaries = $sixthdesagregationResults[$ageBand][$enrollmentTime];
                    break;
            case 7: 
                    $desagregationResults = $model->getSeventhDesagregationResults();
                    $seventhdesagregationResults = $desagregationResults[$district_code]['beneficiaries'];
                    $beneficiaries = $this->getBeneficiaries($seventhdesagregationResults, $ageBand, $enrollmentTime);
                    // $beneficiaries = $seventhdesagregationResults[$ageBand][$enrollmentTime];
                    break;       
                     
        };
        
        // use session storage
        $session = Yii::$app->session;
        if (!$session->isActive) 
            $session->open();

        if($beneficiaries != null){
            $session->set('beneficiaries', gzcompress(implode(',',$beneficiaries),9));
        } else {
            
            $beneficiaries = $session->has('beneficiaries') ? explode(',', gzuncompress($session->get('beneficiaries'))) : [];
        };  
        
        $searchModel = new BeneficiariosSearch();
        $dataProvider = $searchModel->searchList(Yii::$app->request->queryParams, $beneficiaries);
        $dataProvider->pagination->pageSize = 10;

        return $this->render('agywlist', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'beneficiaries' => implode(',',$beneficiaries)
        ]);
        
        
        
    }

    private function getBeneficiaries($disaggregation, $ageBand, $enrollmentTime)
    {
        $ageBands = ['9-14','15-19', '20-24', '25-29'];
        $enrollmentTimes = ['0_6','7_12', '13_24', '25+'];
        $beneficiaries = [];
        if ($ageBand == null) {
            foreach ($ageBands as $aBand) {
                if ($enrollmentTime == null) {
                    foreach($enrollmentTimes as $eTime) {
                        $beneficiaries = array_merge($beneficiaries, $disaggregation[$aBand][$eTime]);
                    }
                }
                else {
                    $beneficiaries = array_merge($beneficiaries, $disaggregation[$aBand][$enrollmentTime]);
                }
            }
        }
        else {
            $beneficiaries = $disaggregation[$ageBand][$enrollmentTime];
        }
        return $beneficiaries;
    }

    public function actionRelatorioagyw()
    {
        $model = new AgywPrev();

        if ($model->load(Yii::$app->request->post()) /* && $model->validate() */) {
            
            $provinces = Provincias::find()
                    ->where(['in','id', $model->provinces])->all();

            $districts = Distritos::find()
                    ->where(['in','district_code', $model->districts])->all();
            
            $model->execute();
            
            $firstdesagregationResults = $model->getFirstDesagregationResults();
            $seconddesagregationResults = $model->getSecondDesagregationResults();
            $thirddesagregationResults = $model->getThirdDesagregationResults();
            $fourthdesagregationResults = $model->getFourthDesagregationResults();
            $fifthdesagregationResults = $model->getFifthDesagregationResults();
            $sixthdesagregationResults = $model->getSixthDesagregationResults();
            $seventhdesagregationResults = $model->getSeventhDesagregationResults();
            $alldisaggragationsResults = $model->getAllDisaggregationsResults();

            $totaisresults = $model->getSummary($districts);
            $totalAgyw = $model->getTotaisAgyW();

            // reset session storage
            $session = Yii::$app->session;
            if (!$session->isActive){
                $session->open();
            } 
            $session->remove('beneficiaries');

            return $this->render('relatorioagywprev', [
                'model' => $model,
                'provinces' => $provinces,
                'districts' => $districts,
                'firstdesagregation' => $firstdesagregationResults,
                'seconddesagregation' => $seconddesagregationResults,
                'thirddesagregation' => $thirddesagregationResults,
                'fourthdesagregation' => $fourthdesagregationResults,
                'fifthdesagregation' => $fifthdesagregationResults,
                'sixthdesagregation' => $sixthdesagregationResults,
                'seventhdesagregation' => $seventhdesagregationResults,
                'allbeneficiaries' => $alldisaggragationsResults,
                'totals' => $totaisresults,
                'totalsAgyw' => $totalAgyw
            ]);
            
        }
        
        return $this->render('relatorioagyw', [
            'model' => $model,
        ]);
    }





    /**
     * Displays a single Beneficiarios model.
     * @param integer $id
     * @return mixed
     */
    public function actionRelatoriofy19()
    {
        $model = new Beneficiarios();

        return $this->render('relatorioFY19', [
            'model' => $model,

        ]);
    }



    /**
     * Displays a single Beneficiarios model.
     * @param integer $id
     * @return mixed
     */
    public function actionRelatoriofy20q1()
    {
        $model = new Beneficiarios();

        return $this->render('relatorioFY20Q1', [
            'model' => $model,

        ]);
    }

    /**
     * Displays a single Beneficiarios model.
     * @param integer $id
     * @return mixed
     */
    public function actionRelatoriofy20q2()
    {
        $model = new Beneficiarios();

        return $this->render('relatorioFY20Q2', [
            'model' => $model,

        ]);
    }


    /**
     * Displays a single Beneficiarios model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Beneficiarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Beneficiarios();


        /*		$ben=Beneficiarios::find()
    ->where(['id' => Beneficiarios::find()->max('id')])
    ->one();
     $emp_number = $ben->id+1;
     $model->emp_number=$emp_number;
	*/

        $model->emp_gender = 2;
        $model->estudante = 1;
        $model->gravida = 0;
        $model->filhos = 0;
        $model->emp_status = 1;
        $model->deficiencia = 0;
        $model->ponto_entrada = 1;


        /*$model->parceiro_id  = 0;*/

        /*$model->provin_code = 5;
		$model->district_code = 1;
		$model->bairro_id = 1;
        $model->us_id = 1;
		$model->membro_localidade_id = 2;*/

        if ($model->load(Yii::$app->request->post())) {

            if (!empty($_POST['Beneficiarios']['encarregado_educacao'])) {
                $model->encarregado_educacao = implode(", ", $_POST['Beneficiarios']['encarregado_educacao']);
            } else {
            }
            //Table write lock
            yii::$app->db->createcommand("lock tables hs_hr_employee write")->execute();
            if ($model->save()) {
                Yii::$app->db->close();
                Yii::$app->db->open();
                //Table unlocked
                yii::$app->db->createcommand("unlock tables")->execute();
                return $this->redirect(['update', 'id' => $model->id]);
            }
            yii::$app->db->createcommand("unlock tables")->execute();
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Beneficiarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->encarregado_educacao = explode(',', $model->encarregado_educacao);

        if ($model->load(Yii::$app->request->post())) {
            if (!empty($_POST['Beneficiarios']['encarregado_educacao'])) {
                $model->encarregado_educacao = implode(", ", $_POST['Beneficiarios']['encarregado_educacao']);
            } else {
            }

            if ($model->save()) {
                Yii::$app->db->close();
                Yii::$app->db->open();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Beneficiarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Beneficiarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Beneficiarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Beneficiarios::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionListdistricts()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if (isset($_POST['depdrop_parents'])) {
            $ids = end($_POST['depdrop_parents']);
            $provinces = implode(',',$ids);
            
            $query = "select district_code as id, district_name, hs_hr_province.province_name, hs_hr_province.id as province_id
                    from hs_hr_district left join hs_hr_province on hs_hr_district.province_code = hs_hr_province.id
                    where hs_hr_district.province_code in (".$provinces.");";
            $preparedQuery = Yii::$app->db->createCommand($query);
            $result = $preparedQuery->queryAll();

            $selected  = null;
            if ($ids != null && count($result) > 0) {
                $selected = '';
                
                $map=array();
                foreach ($ids as $provinceId) {
                    foreach($result as $district){
                        if(!isset($map[$district['province_name']])){
                            $map[$district['province_name']] = array();
                        }
                        if($district['province_id'] == $provinceId){
                            array_push($map[$district['province_name']], ['id' => $district['id'], 'name' => $district['district_name']]);
                        }
                        
                    }
                   
                }
                
                return ['output' => $map, 'selected' => $selected];
            }
            
        }
        return ['output' => '', 'selected' => ''];

    }


    public function actionLists($id)
    {
        $countDistritos = Distritos::find()
            ->where(['province_code' => $id])
            ->count();
        $Distritos  = Distritos::find()
            ->where(['province_code' => $id])
            ->all();
        echo "<option>-</option>";
        if ($countDistritos > 0) {
            foreach ($Distritos as $distrito) {
                echo "<option value='" . $distrito->district_code . "'>" . $distrito->district_name . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }

    public function actionListasdistritos()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $provinciaId = $parents[0];

                $distritos  = Distritos::find() 
                ->where(['province_code'=>$provinciaId])
                ->all();

                $map = array();
                foreach ($distritos as $distrito){
                    array_push($map,['id'=>$distrito['district_code'],'name'=>$distrito['district_name']]);
                }

                return ['output'=>$map, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];

    }

    public function actionListaspostos()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];

        if (isset($_POST['depdrop_parents'])) {

            $ids = $_POST['depdrop_parents'];
            $provinciaId = empty($ids[0]) ? null : $ids[0];
            $distritoId = empty($ids[1]) ? null : $ids[1];

            if ($distritoId != null) {

                /*$bairros  = Bairros::find()
                ->where(['distrito_id' => $distritoId])
                ->all();*/

                $bairros = ComiteLocalidades::find()->where(['c_distrito_id' => $distritoId])->all();

                $map = array();

                foreach ($bairros as $bairro){

                    array_push($map,['id'=>$bairro['id'],'name'=>$bairro['name']]);
                }

                return ['output'=>$map, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];

    }

    public function actionTodos($id)
    {

        $countBenes = Beneficiarios::find()
            ->where(['district_code' => $id])
            ->count();
        $Benes  = Beneficiarios::find()
            ->where(['district_code' => $id])
            ->all();

        if ($countBenes > 0) {
            echo "<option>-</option>";
            foreach ($Benes as $bene) {
                echo "<option value='" . $bene->emp_firstname . "'>" . $bene->emp_firstname . " " . $bene->emp_lastname . " | " . $bene->us['name'] . "</option>";
            }
        } else {
            echo "<option> -- </option>";
        }
    }


    public function actionServicos($id)
    {
        $countServicos = ServicosDream::find()
            ->where(['servico_id' => $id])
            ->count();
        $Servicos  = ServicosDream::find()
            ->where(['servico_id' => $id])
            ->all();

        if ($countServicos > 0) {
            echo "<option value='NULL'>--SELECIONE O SERVI&Ccedil;O--</option>";
            foreach ($Servicos as $servico) {
                echo "<option value='" . $servico->id . "'>" . $servico->name . "</option>";
            }
        } else {
        }
    }

    public function actionListas($id)
    {
        $countSubservicos = SubServicosDreams::find()
            ->where(['servico_id' => $id])
            ->count();
        $Subservicos  = SubServicosDreams::find()
            ->where(['servico_id' => $id])
            ->all();

        if ($countSubservicos > 0) {
            echo "<option value='NULL'>--SELECIONE--</option>";
            foreach ($Subservicos as $subservico) {
                echo "<option value='" . $subservico->id . "'>" . $subservico->name . "</option>";
            }
        } else {
            echo "<option value='NULL'>--SEM SUB-SERVICOS--</option>";
        }
    }

    public function actionLocalidades($id)
    {
        $countLocalidades = ComiteLocalidades::find()
            ->where(['c_distrito_id' => $id])
            ->count();
        $Localidades  = ComiteLocalidades::find()
            ->where(['c_distrito_id' => $id])
            ->all();
        echo "<option>-</option>";
        if ($countLocalidades > 0) {
            foreach ($Localidades as $localidade) {
                echo "<option value='" . $localidade->id . "'>" . $localidade->name . "</option>";
            }
        } else {
        }
    }


    public function actionBairros($id)
    {
        $countBairros = Bairros::find()
            ->where(['post_admin_id' => $id])
            ->count();
        $Bairros  = Bairros::find()
            ->where(['post_admin_id' => $id])
            ->all();
        echo "<option>-</option>";
        if ($countBairros > 0) {
            foreach ($Bairros as $bairros) {
                echo "<option value='" . $bairros->id . "'>" . $bairros->name . "</option>";
            }
        } else { /*echo "<option>-</option>";*/
        }
    }
    //added on 01.12.2017 by cololo
    public function actionFiltros()
    {
        $searchModel = new BeneficiariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('filtros', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    //added on 26.02.2019 relatorioQ1
    public function actionRelatorio($id)
    {
        return $this->render('relatorio', [
            'model' => $this->findModel($id),
        ]);
    }



    //added on 11.02.2020 by Gerzelio Saide relatorioQ1
    public function actionRelatorioQ1($id)
    {
        return $this->render('relatorioQ1', [
            'model' => $this->findModel($id),
        ]);
    }
}
