<?php

use yii\helpers\Html;
use frontend\assets\AdminAsset;
use frontend\assets\MaterialAsset;
use yii\helpers\Url;

use app\models\Provincias;
use app\models\Distritos;
use app\models\ComiteProvincial;
use app\models\ComiteDistrital;
use app\models\ComiteZonal;
use common\models\User;
use app\models\ComiteCelulas;
use app\models\ComiteCirculos;
use app\models\ComiteCidades;
use app\models\ComiteLocalidades;
use app\models\ComiteCargos;
use app\models\TiposDeQuotas;
use app\models\Membros;
use app\models\Beneficiarios;
use app\models\Quotas;
use app\models\ComiteFormaPagamento;
use app\models\Organizacoes;
use app\models\TipoCargos;
use app\models\ParceirosTipo;
use app\models\TipoServicos;
use app\models\ServicosDream;
use app\models\TipoUs;
use app\models\ServicosBeneficiados;
use app\models\Bairros;
use app\models\Curriculum;
use app\models\CurriculumServicos;
use app\models\Us;
use app\models\EscolasDreams;
use app\models\SubServicosDreams;


/*adde on 08-02-2019 by jordao cololo*/
use app\models\NivelIntervensao;
use app\models\FaixaEtaria;
use app\models\FaixaEtariaServico;

/*IMPORTANTE*/
/*Yii::$app->cache->flush();*/


/* @var $this \yii\web\View */
/* @var $content string */

//AppAsset::register($this);
Yii::$app->log->targets['debug'] = null;

   if (!Yii::$app->user->isGuest) {
    AdminAsset::register($this); } else {
MaterialAsset::register($this);}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>

        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?= Html::csrfMetaTags() ?>
  
        <title><?= Html::encode($this->title) ?></title>
        <link href="<?=Url::base();?>/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
        <?php $this->head() ?>
      <script src="<?=Url::base();?>/css/bootstrap/js/jquery.canvasjs.min.js"></script>
       <link href="<?=Url::base();?>/css/circle.css" rel="stylesheet">
       <link href="<?=Url::base();?>/sass/circle.scss" rel="stylesheet">
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-100004921-2', 'auto');
  ga('send', 'pageview');

</script>

    </head>
    <?php $this->beginBody() ?>
    <body class="skin-blue" >

        <header class="header">
      <?php    if (!Yii::$app->user->isGuest) { ?>       <a href="<?=Url::base();?>" class="logo">              
                
            <?= Html::img('@web/img/logo3.png', ['alt' => 'DREAMS ONLINE']) ?>

            </a>

            <?php } ?>
            <!-- Header Navbar: style can be found in header.less -->
              <nav class="navbar navbar-default navbar-static-top" role="navigation">
         
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">

  <?php if (isset(Yii::$app->user->identity->role)&&Yii::$app->user->identity->role==20) { ?>      
    <!-- Fixed navbar -->
 <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-list"></i>
                                <span class="label label-success">Menu<?php Beneficiarios::find()->where(['=','emp_status',1])->count();?></span>
                            </a>

    <ul class="dropdown-menu">
          <li class="active">
                            <a href="<?=Url::base();?>">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
	<!--	<li>
                            <a href="<?php echo Url::toRoute('beneficiarios/create'); ?>">
                                <i class="fa fa-plus"></i> <span>Novo Benefici&aacute;rio <small class="badge pull-right bg-green">+</small></span>
                            </a>
                        </li>
-->
                         <li>
                            <a href="<?php echo Url::toRoute('beneficiarios/index'); ?>">
                                <i class="fa fa-group"></i> <span>Lista de Benefici&aacute;rios <small class="badge pull-right bg-green"><?= Beneficiarios::find()->where(['=','emp_status',1])->count();?></small></span>
                            </a>
                        </li>
		                                             					 <li>
                                <?= Html::a(Yii::t('app', '<i class="fa fa-exchange"></i> <span>Benefici&aacute;rios Referidos<small class="badge pull-right bg-yellow">
   0 </small></span>'), ['referencias-dreams/index','id'=>5]) ?>
                        </li>
                        <li>
                             <a href="<?=Url::base();?>">
                                <i class="fa fa-envelope"></i> <span>Caixa de Mensagens</span>
                                <small class="badge pull-right bg-yellow"> 
<?php if (Yii::$app->user->identity->role==20) { echo 0; } else {echo 0;} ?>

                                </small>
                            </a>
                        </li>

                        <?php if ((Yii::$app->user->identity->username=='admin')&&(Yii::$app->user->identity->role==20)) { ?>
                       <li class="treeview">
                            <a href="#">
                                <i class="fa fa-edit"></i>
                                <span>Parametrização</span>
                                <i class="fa fa-angle-left pull-right"> </i>
                            </a>

                            <ul  class="treeview-menu">
                                <li>
                                <a href="#"><i class="fa fa-angle-double-right"></i><span> Províncias </span><span class="badge pull-right"> <?php // Provincias::find()->count();?>
</span></a></li>
                                <li>
                                <a href="<?php echo Url::toRoute('distritos/index'); ?>"><i class="fa fa-plus-circle"  style="color:green;"></i> Distritos <span class="badge pull-right"> <?php // Distritos::find()->count();?></span></a>
                                </li>
                               <li>
                                <a href="<?php echo Url::toRoute('comite-localidades/index'); ?>">
                                <i class="fa fa-plus-circle"  style="color:green;"> 
                                </i> Postos Administrativos 
                                <span class="badge pull-right"> <?php // ComiteLocalidades::find()->count();?> 
                                </span>
                                </a>
                                </li>
 <li>
                                <a href="<?php echo Url::toRoute('bairros/index'); ?>"><i class="fa fa-plus-circle"  style="color:green;"></i> <span>Bairros DREAMS<span class="badge pull-right">
 <?php // Bairros::find()->count();?>
</span></span>
</a>
                                </li>
                                <li>
                                <a href="<?php echo Url::toRoute('us/index'); ?>"><i class="fa fa-plus-circle"  style="color:green;"></i> <span>Unidade Sanitária <span class="badge pull-right"> 
<?php // Us::find()->count();?>
</span></span></a>
                                </li>


                                   <li>
                                <a href="<?php echo Url::toRoute('servicos-dream/index'); ?>"><i class="fa fa-plus-circle"  style="color:green;"></i> <span>Serviços DREAMS <span class="badge pull-right"> 
<?php // ServicosDream::find()->count();?>
</span></span></a>
                                </li>

                                        <li>
                                <a href="<?php echo Url::toRoute('sub-servicos-dreams/index'); ?>"><i class="fa fa-plus-circle"  style="color:green;"></i> <span>Sub-Serviços DREAMS <span class="badge pull-right"> <?php // SubServicosDreams::find()->count();?>
</span></span></a>
                                </li>
 

                                <li>
                                <a href="<?php echo Url::toRoute('organizacoes/index'); ?>"><i class="fa fa-plus-circle"  style="color:green;"></i> <span>Parceiros DREAMS <span class="badge pull-right"> <?php // Organizacoes::find()->count();?>
</span></span></a>
                                </li>

                                <li>
                                <a href="<?php echo Url::toRoute('tipo-servicos/index'); ?>"><i class="fa fa-plus-circle"  style="color:green;"></i> <span>Tipos de Serviços<span class="badge pull-right"> <?php // TipoServicos::find()->count();?>
</span></span></a>
                                </li>
                                   <li>
                                <a href="<?php echo Url::toRoute('parceiros-tipo/index'); ?>"><i class="fa fa-plus-circle"  style="color:green;"></i> <span>Tipos de Parceirias<span class="badge pull-right"> <?php // ParceirosTipo::find()->count();?>
</span></span></a>
                                </li>
                                <li>
                                <a href="<?php echo Url::toRoute('curriculum/index'); ?>"><i class="fa fa-plus-circle"  style="color:green;"></i> <span>Curriculum<span class="badge pull-right"></span></span></a>
                                </li>
                                <li>
                                <a href="<?php echo Url::toRoute('curriculum-servicos/index'); ?>"><i class="fa fa-plus-circle"  style="color:green;"></i> <span>Curriculum-Serviço<span class="badge pull-right"></span></span></a>
                                </li>

                            </ul>
                        </li>

                        <?php } ?>
                                                <li>
                            <a href="<?php echo Url::toRoute('user/admin/index'); ?>">
                                <i class="fa fa-user"></i> <span>Utilizadores <span class="badge pull-right bg-red"><?php // User::find()->count()-3;?></span></span>
                            </a>
                        </li>
    </ul>
</li>
<?php } ?>





                        <!-- Messages: style can be found in dropdown.less-->
                        <?php    if (!Yii::$app->user->isGuest) { ?>

                        <?php } ?>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">


<?php    if (Yii::$app->user->isGuest) { ?>
<!-- <a href="index.php?r=user/security/login" >Login  </a> -->
 <?php   }else { ?>

     <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-red">
                                 <?php
				 if (Yii::$app->user->identity->username=='admin') {
			echo	Html::img('@web/img/users/cololo_avatar.png', ['alt' => 'user', 'class'=>'img-circle' ]);} else {
			echo	 Html::img('@web/img/users/profile.png', ['alt' => 'user', 'class'=>'img-circle' ]);
				 }
									?>
                                    <p>
                                      <?=  Yii::$app->user->identity->username; ?>
                                        <small>Utilizador DREAMS </small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class="user-body">
                                    <div class="col-xs-4 text-center">
                                      <!--  <a href="#">Utilizadores</a> -->
                                    </div>
                                    <div class="col-xs-4 text-center">
                                      <!--  <a href="<?php echo Url::toRoute('servicos-dream/index'); ?>">Serviços</a> -->
                                    </div>
                                    <div class="col-xs-4 text-center">
                                     <!--   <a href="#">US</a> -->
                                    </div>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="<?= Url::to(['/user/settings/account']); ?>" class="btn btn-default btn-flat"> Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <?php if (Yii::$app->user->isGuest): ?>
                                            <a href="<?= Url::to(['user/security/login']); ?>" data-method="post" class="btn btn-default btn-flat"> Sign In</a>
                                        <?php else: ?>
                                            <a href="<?= Url::to(['user/security/logout']); ?>" data-method="post" class="btn btn-default btn-flat">Sign out</a>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            </ul>

<?php } ?>

                        </li>
                        <li> &nbsp;&nbsp;</li>
                    </ul>

                </div>
            </nav>
        </header>


        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
         <?php    if (!Yii::$app->user->isGuest) { ?>
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                        <?php 	 if (Yii::$app->user->identity->username=='admin') {
			echo	Html::img('@web/img/users/cololo_avatar.png', ['alt' => 'user', 'class'=>'img-circle' ]);} else {
			echo	 Html::img('@web/img/users/profile.png', ['alt' => 'user', 'class'=>'img-circle' ]);
				 } ?>
                        </div>
                        <div class="pull-left info">
                            <p>Hello, <?=  Yii::$app->user->identity->username; ?></p>

                            <a href="<?=Url::base();?>"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    <!-- search form -->
                    <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search..."/>
                            <span class="input-group-btn">
                                <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="active">
                            <a href="<?=Url::base();?>">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
					<!--	<li>
                            <a href="<?php echo Url::toRoute('beneficiarios/create'); ?>">
                                <i class="fa fa-plus"></i> <span>Novo Benefici&aacute;rio <small class="badge pull-right bg-green">+</small></span>
                            </a>
                        </li> -->
                         <li>
                            <a href="<?php echo Url::toRoute('beneficiarios/index'); ?>">
                                <i class="fa fa-group"></i> <span>Benefici&aacute;rios <small class="badge pull-right bg-green">
								  <?php
												  $ben=0;
if (isset(Yii::$app->user->identity->role)&&(Yii::$app->user->identity->role>0)) { 
if(isset(Yii::$app->user->identity->provin_code)&&(Yii::$app->user->identity->provin_code>0)) {
$prov=(int)Yii::$app->user->identity->provin_code;
$ben=Beneficiarios::find()->where(['provin_code'=>$prov])->andWhere(['emp_status'=>1])->count();

} elseif(Yii::$app->user->identity->role==20) {

$ben=Beneficiarios::find()->where(['emp_status'=>1])->count();

}
} else {
$ben=Beneficiarios::find()->where(['provin_code'=>5])->andWhere(['emp_status'=>1])->count();
}
												  echo (int)$ben;
?>

								</small></span>
                            </a>
                        </li>

     <li>
     <?php if (Yii::$app->user->identity->role==20) { ?>
        <li class="treeview">
            <a href="#" data-toggle="collapse" data-target="#referencias">

                <i class="fa fa-exchange"></i>
                <span>Refer&ecirc;ncias</span>
                <i class="fa fa-angle-left pull-right"> </i>

            </a>
            <ul id="referencias" class="collapse">

               <?php
                    if(isset(Yii::$app->user->identity->provin_code)&&Yii::$app->user->identity->provin_code>0) 
                    {
                        echo Html::a(Yii::t('app', '<li><i class="fa fa-angle-double-right"></i> <span>Gest&atilde;o de Refer&ecirc;ncias<small class="badge pull-right bg-yellow"> 0 </small></span></li>'), ['referencias-dreams/index', 'id' => Yii::$app->user->identity->provin_code]); 
                    } else {
                        echo	Html::a(Yii::t('app', '<li><i class="fa fa-angle-double-right"></i> <span>Gest&atilde;o de Refer&ecirc;ncias<small class="badge pull-right bg-yellow"> 0 </small></span></li>'), ['referencias-dreams/index', 'id' => 5]);										  
                    }
                ?>
                <li>
                    <a href="<?php echo Url::toRoute('referencias-dreams/pendentes'); ?>"><i class="fa fa-angle-double-right"></i> Cancelamento de Refer&ecirc;ncias Pendentes </a>
                </li>
            </ul>
        </li>
        <?php } else {
            if(isset(Yii::$app->user->identity->provin_code)&&Yii::$app->user->identity->provin_code>0) 
            {
                echo Html::a(Yii::t('app', '<i class="fa fa-exchange"></i> <span>Gest&atilde;o de Refer&ecirc;ncias<small class="badge pull-right bg-yellow"> 0 </small></span>'), ['referencias-dreams/index', 'id' => Yii::$app->user->identity->provin_code]); 
            } else {
                echo	Html::a(Yii::t('app', '<i class="fa fa-exchange"></i> <span>Gest&atilde;o de Refer&ecirc;ncias<small class="badge pull-right bg-yellow"> 0 </small></span>'), ['referencias-dreams/index', 'id' => 5]);										  
            }
        } ?>                             
    </li>            




<?php if (Yii::$app->user->identity->role==20) { ?>



                        <li class="treeview">
                            <a href="#" data-toggle="collapse" data-target="#config">
                                <i class="fa fa-cog"></i>
                                <span>Configurações</span>
                                <i class="fa fa-angle-left pull-right"> </i>
                            </a>

                                 <!-- EDITADO PARA COLLAPSE  -->
                            <!-- <ul class="treeview-menu">  -->
                            <ul id="config" class="collapse">
                               <li>
                                <a href="#"><i class="fa fa-angle-double-right"></i> Províncias <span class="badge pull-right"> <?= Provincias::find()->count();?></span></a>
                                </li>
                               
                                <li>
                                <a href="<?php echo Url::toRoute('distritos/index'); ?>"><i class="fa fa-globe"  style="color:green;"></i>
                                  <span>Distritos <span class="badge pull-right"> <?php // Distritos::find()->count();?></span></span></a>
                                </li>
                             
                               <li>
                                <a href="<?php echo Url::toRoute('comite-localidades/index'); ?>">
                                <i class="fa fa-globe"  style="color:green;"></i> Postos Administrativos 
                                <span class="badge pull-right"> <?php // ComiteLocalidades::find()->count();?></span></a> 

                                </li>
                                <li>
                                <a href="<?php echo Url::toRoute('us/index'); ?>"><i class="fa fa-hospital-o"  style="color:green;"></i>
                                  <span>Unidades Sanitárias <span class="badge pull-right"> <?php // Us::find()->count();?></span></span></a>
                                </li>
                                <li>
                                <a href="<?php echo Url::toRoute('bairros/index'); ?>"><i class="fa fa-globe"  style="color:green;"></i> 
                                  <span>Bairros DREAMS<span class="badge pull-right"> <?php // Bairros::find()->count();?></span></span></a>
                                </li>
								<li>
                                <a href="<?php echo Url::toRoute('escolas-dreams/index'); ?>"><i class="fa fa-globe"  style="color:green;">
                                  </i> <span>Escolas DREAMS <span class="badge pull-right"> <?php // EscolasDreams::find()->count();?></span></span></a>
                                </li>
								
							     <li>
                                <a href="<?php echo Url::toRoute('servicos-dream/index'); ?>">
                                  <i class="fa fa-medkit"  style="color:green;"></i> <span>Serviços DREAMS <span class="badge pull-right">
                                  <?php // ServicosDream::find()->where(['=','status',1])->count();?></span></span></a>
                                </li>	
								
                                   <li>
                                <a href="<?php echo Url::toRoute('sub-servicos-dreams/index'); ?>">
                                  <i class="fa fa-plus-circle"  style="color:green;"></i> <span>Sub-Serviços DREAMS 
                                  <span class="badge pull-right"> <?php echo SubServicosDreams::find()->where(['=','status',1])->count();?></span></span></a>
                                </li>

                              

                                <li>
                                <a href="<?php echo Url::toRoute('organizacoes/index'); ?>">
                                  <i class="fa fa-briefcase"  style="color:green;"></i> <span>Parceiros DREAMS 
                                  <span class="badge pull-right"> <?php echo  Organizacoes::find()->count();?></span></span></a>
                                </li>


                                <li>
				 <a href="<?php echo Url::toRoute('nivel-intervensao/index'); ?>"><i class="fa fa-level-up"  style="color:green;"></i> 
                   <span>Nível de Intervenção<span class="badge pull-right"> <?php echo NivelIntervensao::find()->count();?></span></span></a>
                                </li>

                                <li>
                                <a href="<?php echo Url::toRoute('faixa-etaria/index'); ?>">
                                  <i class="fa fa-sort-numeric-asc"  style="color:green;"></i> <span>Faixa Etária
                                  <span class="badge pull-right"> <?php echo FaixaEtaria::find()->count();?></span></span></a>
                                </li>

                                <li>
                                <a href="<?php echo Url::toRoute('faixa-etaria-servico/index'); ?>">
                                  <i class="fa fa-list-ol"  style="color:green;"></i> <span>Faixa Etária-Serviço
                                  <span class="badge pull-right"> <?= FaixaEtariaServico::find()->count();?></span></span></a>
                                </li>



                                <li>
                                <a href="<?php echo Url::toRoute('tipo-servicos/index'); ?>"><i class="fa fa-gears"  style="color:green;"></i> <span>Tipos de Serviços<span class="badge pull-right"> <?= TipoServicos::find()->count();?></span></span></a>
                                </li>
                                   <li>
                                <a href="<?php echo Url::toRoute('parceiros-tipo/index'); ?>"><i class="fa fa-gears"  style="color:green;"></i> <span>Tipos de Parceirias<span class="badge pull-right"> <?= ParceirosTipo::find()->count();?></span></span></a>
                                </li>
                                   <li>
                                <a href="<?php echo Url::toRoute('tipo-us/index'); ?>"><i class="fa fa-gears"  style="color:green;"></i> <span>Tipos de US<span class="badge pull-right"> <?= TipoUs::find()->count();?></span></span></a>
                                </li>
                                <li>
                                <a href="<?php echo Url::toRoute('curriculum/index'); ?>"><i class="fa fa-gears"  style="color:green;"></i> <span>Curriculum<span class="badge pull-right"> <?= Curriculum::find()->count();?></span></span></a>
                                </li>
                                <li>
                                <a href="<?php echo Url::toRoute('curriculum-servicos/index'); ?>">
                                  <i class="fa fa-list-ol"  style="color:green;"></i> <span>Curriculum-Serviço
                                  <span class="badge pull-right"> <?= CurriculumServicos::find()->count();?></span></span></a>
                                </li>
                         

                      
                              
                              
                                
                         



                            </ul>
                        </li>

                          <li>
                             <a href="<?=Url::base();?>">
                                <i class="fa fa-envelope"></i> <span>Caixa de Mensagens</span>
                                <small class="badge pull-right bg-yellow"> 
<?php if (Yii::$app->user->identity->role==20) { echo 0; } else {echo 0;} ?>
                                

                                </small>
                            </a>
                        </li>

                        <li class="treeview">
                            <a href="#" data-toggle="collapse" data-target="#doc">
                                <i class="fa fa-folder"></i> <span>Documentos Diversos</span>
                                <i class="fa fa-angle-left pull-right"> </i>
                            </a>
                                 <!-- EDITADO PARA COLLAPSE  -->
                            <!-- <ul class="treeview-menu">  -->
                            <ul id="doc" class="collapse">
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Manuais de Formação</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Manual de Utilizador</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Galeria de Fotos</a></li>
             
                            </ul>
                        </li>

 
    <li class="treeview">
        <a href="#" data-toggle="collapse" data-target="#user">
            <i class="fa fa-user"></i> <span>Utilizadores</span>
            <i class="fa fa-angle-left pull-right"> </i>
        </a>
                                 <!-- EDITADO PARA COLLAPSE  -->
                            <!-- <ul class="treeview-menu">  -->
                            <ul id="user" class="collapse">

            <li>
                <a href="<?php echo Url::toRoute('user/admin/create'); ?>">
                    <i class="fa fa-user"></i> <span>Criar Utilizadores <span class="badge pull-right bg-red"><?php //echo User::find()->count()-3;?></span></span>
                </a>
            </li>

            <li>
                <a href="<?php echo Url::toRoute('user/admin/index'); ?>">
                    <i class="fa fa-user"></i> <span>Actualizar Utilizadores <span class="badge pull-right bg-red"><?php //echo User::find()->count()-3;?></span></span>
                </a>
            </li>          
          
            <li>
                <a href="<?php echo Url::toRoute('utilizadores/index'); ?>">
                    <i class="fa fa-user"></i> <span>Listar Utilizadores <span class="badge pull-right bg-red"><?php // echo User::find()->count()-3;?></span></span>
                </a>
            </li>

        </ul>
    </li>                      
                      
	   		

  <?php } ?>


 <li class="treeview">
                            <a href="#"  data-toggle="collapse" data-target="#report">
								<i class="ion ion-pie-graph info"></i> RELAT&Oacute;RIOS DREAMS
                                <small class="badge pull-right bg-green"><?= 2; ?></small></span>

                            </a>
                            <!-- <ul class="treeview"> -->
                            <ul id="report" class="collapse">
                                <li><a href="<?php echo Url::toRoute('site/reports'); ?>">
                                  <i class="fa fa-angle-double-right"></i> GERAL</a>
                                </li>
                                <li><a href="<?php echo Url::toRoute('beneficiarios/filtros'); ?>">
                                  <i class="fa fa-angle-double-right"></i> FILTROS DREAMS</a>
                                </li>
                            <?php if (Yii::$app->user->identity->role==20 || Yii::$app->user->identity->role==14) { ?>
                                <li><a href="<?php echo Url::toRoute('/benefits/index'); ?>">
                                  <i class="fa fa-angle-double-right"></i> FILTROS MENSAL</a>
                                </li>
                              <li><a href="<?php echo Url::toRoute('/utilizadores/index'); ?>">
                                  <i class="fa fa-angle-double-right"></i> FILTROS UTILIZADORES</a>
                                </li>
                                <li>
                                    <?= Html::a(Yii::t('app', '<i class="fa fa-angle-double-right"></i> PEPFAR MER 2.6 AGYW_PREV'), ['/beneficiarios/relatorioagyw']) ?> 
                                </li>

                            <?php } ?>  
                                               	 
                        <li>
                               <?= Html::a(Yii::t('app', '<i class="fa fa-angle-double-right"></i> FY19'), ['/beneficiarios/relatoriofy19','id'=>5]) ?> 
                        </li>
                        

		<li class="treeview">
                            <a href="#"  data-toggle="collapse" data-target="#apr">
                                  <i class="fa fa-angle-double-right"></i> FY20</a>
                              <!-- <ul class="treeview"> -->
                              <ul id="apr" class="collapse">
                              <li>
                                <?= Html::a(Yii::t('app', '<i class="fa fa-angle-double-right"></i> Q1'), ['/beneficiarios/relatoriofy20q1']) ?>
                       	     </li>

                              <li>
                                <?= Html::a(Yii::t('app', '<i class="fa fa-angle-double-right"></i> Q2'), ['/beneficiarios/relatoriofy20q2']) ?>
                             </li>
                             
                          </ul>
                            </ul>
    </li>

                    </ul>
                </section>
                <!-- /.sidebar -->

<!--
<div class="dropdown">
  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">SISTEMAS
  <span class="caret"></span></button>
  <ul class="dropdown-menu">
    <li><a href="#">DREAMS</a></li>
    <li><a href="#">IMD</a></li>
    <li><a href="#">VBG</a></li>
  </ul>
</div>
-->



            </aside>
<?php } ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">                
                <!-- Content Header (Page header) -->
             <?php  if (!Yii::$app->user->isGuest) {   ?>    
                <section class="content-header">
                    <h1>
                      SISTEMA INTEGRADO DE CADASTRO DE ADOLESCENTES E JOVENS 
                      <br>
                      <small>WORKING TOGETHER FOR AN AIDS-FREE FOR GIRLS & WOMEN</small>
                    </h1>

             </section>
             <?php } ?>  
                <!-- Main content -->
                <section class="content">
                                    <div class="row"></div>
<div class="row">
   <?php  if (!Yii::$app->user->isGuest) {   ?>  
                     <?php
                    \yii\widgets\Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ])
                    ?> 
    <?php } ?>  
</div>
                    <?= $content ?>                    
                </section><!-- /.content -->
            </aside><!-- /.right-side -->

        </div><!-- ./wrapper -->
<?php    if (Yii::$app->user->isGuest) { ?>
     <div class="row">
<div class="navbar navbar-fixed-bottom" style="background-color:#fff">
<div class="col-md-12 col-md-offset-2"  style="background-color:#fff">
  <div class="row">
    <div class="" style="padding: 2px;">
<font size="-1">
  <?= Html::img('@web/img/parceiros/Dreams_moz_icap_logo.png', ['width' => '10%', 'alt' => 'ICAP']); ?>
 <?= Html::img('@web/img/parceiros/Dreams_moz_Jhpiego_logo.png', ['width' => '10%', 'alt' => 'Jhpiego']); ?>
 <?= Html::img('@web/img/parceiros/DREAMS_MOZ_FHI360_LOGO.png', ['width' => '10%', 'alt' => 'Fhi360']); ?>
 <?= Html::img('@web/img/parceiros/Dreams_mz_wei-combined_logo.png', ['width' => '10%', 'alt' => 'World Education']); ?>
 <?= Html::img('@web/img/parceiros/dreams_moz_FGH_Logo.png', ['width' => '10%', 'alt' => 'FGH']); ?>
 <?= Html::img('@web/img/parceiros/Dreams_moz_NWETI_logo.png', ['width' => '10%', 'alt' => 'NWETI']); ?>
 <?= Html::img('@web/img/parceiros/Dreams_mz_World_Vision_logo.png', ['width' => '10%', 'alt' => 'World Vision']); ?>
 <?= Html::img('@web/img/parceiros/DREAMS_moz_elizabethglaser_logo.png', ['width' => '10%', 'alt' => 'Elizabeth Glaser']); ?>
</font>
</div>
</div>
</div>
</div>
    </div>
<?php } ?>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
