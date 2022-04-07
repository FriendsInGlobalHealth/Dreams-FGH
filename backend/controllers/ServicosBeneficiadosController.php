<?php

namespace backend\controllers;

use Yii;
use app\models\ServicosBeneficiados;
use app\models\ServicosBeneficiadosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;


use yii\filters\AccessControl;
use common\models\User;
use common\components\AccessRule;
use app\models\SubServicosDreams;
use app\models\ServicosDream;
use app\models\ReferenciasServicosReferidos;
/**
 * ServicosBeneficiadosController implements the CRUD actions for ServicosBeneficiados model.
 */
class ServicosBeneficiadosController extends Controller
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
                        'actions' => ['index','create','listas','servicos','listaservicos','listasubservicos'],

                        'allow' => true,
                        'roles' => [
                            User::ROLE_USER,
                            User::ROLE_ADMIN,
                            User::ROLE_GESTOR,
                            User::ROLE_CORDENADOR
                        ],
                    ],

                    [
                        'actions' => ['view','update'],
                        'allow' => true,                    
                        'roles' => [
                            User::ROLE_ADMIN,
                            User::ROLE_GESTOR,
                            User::ROLE_CORDENADOR
                        ],
                    ],

                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                        return User::isUserAdmin(Yii::$app->user->identity->username);}
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all ServicosBeneficiados models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ServicosBeneficiadosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
    public function actionServicos($id)
    {
        $countServicos = ServicosDream::find() 
            ->where(['servico_id'=>$id])
            ->count();
        $Servicos  = ServicosDream::find() 
            ->where(['servico_id'=>$id])->andWhere(['=','status',1])
            ->all();

        if($countServicos>0) {
            echo "<option value='NULL'>--SELECIONE O SERVI&Ccedil;O--</option>";
            foreach($Servicos as $servico) { 
                echo "<option value='".$servico->id."'>".$servico->name."</option>";
            }
        }
    }
	
	public function actionListas($id)
    {
       $countSubservicos = SubServicosDreams::find() 
            ->where(['servico_id'=>$id])
			->andWhere(['=','status',1])
            ->count();
        $Subservicos  = SubServicosDreams::find() 
            ->where(['servico_id'=>$id])
            ->andWhere(['=','status',1])
            ->all();

        if($countSubservicos>0) {
            echo "<option value='NULL'>--SELECIONE--</option>";
            foreach($Subservicos as $subservico) { 
                echo "<option value='".$subservico->id."'>".$subservico->name."</option>";
            }
        }
    }
	
    public $enableCsrfValidation = false;

    /**
     * Displays a single ServicosBeneficiados model.
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
     * Creates a new ServicosBeneficiados model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ServicosBeneficiados();

        

        if ($model->load(Yii::$app->request->post())) {

            $model->save();
            Yii::$app->db->close();
            Yii::$app->db->open();

            return $this->redirect(['beneficiarios/view', 'id' => $model->beneficiario_id]);

        } else {
           // return $this->renderAjax('create', [
			      return $this->render('create', [
                'model' => $model,
            ]);
        }

        
    }

    /**
     * Updates an existing ServicosBeneficiados model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            return $this->redirect(['servicos-beneficiados/view', 'id' => $model->id]);
        } else {
            //return $this->renderAjax('update', [
			return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ServicosBeneficiados model.
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
     * Finds the ServicosBeneficiados model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ServicosBeneficiados the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ServicosBeneficiados::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionListaservicos()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $tipoServicoId = $parents[0];

                $servicos  = ServicosDream::find()
                ->orderBy('name ASC')
                ->where(['servico_id'=>$tipoServicoId])
                ->andWhere(['=','status',1])
                ->all();

                $map = array();
                foreach ($servicos as $servico){
                    array_push($map,['id'=>$servico['id'],'name'=>$servico['name']]);
                }

                return ['output'=>$map, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];

    }

    public function actionListasubservicos()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];

        if (isset($_POST['depdrop_parents'])) {

            $ids = $_POST['depdrop_parents'];
            $tipoServicoId = empty($ids[0]) ? null : $ids[0];
            $servicoId = empty($ids[1]) ? null : $ids[1];

            if ($servicoId != null) {

                $subServicos  = SubServicosDreams::find()
                ->where(['servico_id'=>$servicoId])
                ->andWhere(['=','status',1])
                ->all();

                $map = array();

                foreach ($subServicos as $subServico){
                    if($subServico['mandatory']==1){
                        array_push($map,['id'=>$subServico['id'],'name'=>($subServico['name']." *")]);
                    }else{
                        array_push($map,['id'=>$subServico['id'],'name'=>$subServico['name']]);
                    }
                }

                return ['output'=>$map, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];

    }
    
}
