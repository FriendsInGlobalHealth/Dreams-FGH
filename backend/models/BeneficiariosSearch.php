<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Beneficiarios;
use yii\helpers\VarDumper;



use yii\helpers\ArrayHelper;

/**
 * BeneficiariosSearch represents the model behind the search form about `app\models\Beneficiarios`.
 */
class BeneficiariosSearch extends Beneficiarios
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'emp_number', 'parceiro_id', 'membro_zona', 'membro_circulo', 'us_id', 'membro_celula', 'membro_localidade_id', 'emp_smoker', 'nation_code', 'emp_gender', 'emp_status', 'job_title_code', 'eeo_cat_code', 'work_station', 'termination_id', 'criado_por', 'actualizado_por', 'ponto_entrada'], 'integer'],
            [[
                'member_id',
                'emp_lastname', 'emp_firstname', 'criado_em', 'emp_middle_name', 'emp_nick_name', 'membro_data_admissao', 'membro_caratao_eleitor', 'membro_cargo_partido_id', 'ethnic_race_code', 'emp_birthday', 'emp_marital_status', 'emp_ssn_num', 'emp_sin_num', 'emp_other_id', 'emp_dri_lice_num', 'emp_dri_lice_exp_date', 'emp_military_service', 'emp_street1', 'emp_street2', 'city_code', 'coun_code', 'provin_code', 'district_code', 'emp_zipcode', 'emp_hm_telephone', 'emp_mobile', 'emp_work_telephone', 'emp_work_email', 'sal_grd_code', 'joined_date', 'emp_oth_email', 'bi', 'nuit', 'passaporte', 'dire', 'bi_data_i', 'bi_data_f', 'custom3', 'other_prof_info', 'nuit_data_i', 'nuit_data_f', 'custom7', 'custom8', 'custom9', 'custom10', 'criado_em', 'actualizado_em', 'user_location', 'user_location2', 'deficiencia_tipo', 'idade_anos', 'estudante', 'estudante_classe', 'estudante_escola', 'gravida', 'filhos', 'bairro_id', 'encarregado_educacao',
                'deficiencia', 'house_sustainer', 'married_before', 'pregant_or_breastfeed', 'employed', 'tested_hiv', 'vbg_exploracao_sexual', 'vbg_migrante_trafico', 'vbg_sexual_activa', 'vbg_relacao_multipla', 'vbg_vitima', 'vbg_vitima_trafico'
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function fetchAGYW($listBeneficiaries){
        $query = "
            select hs_hr_province.province_name as provincia, hs_hr_district.district_name as distrito, app_dream_bairros.name as bairro, 
                                if(app_dream_vw_agyw_prev.ponto_entrada=1,'US',if(app_dream_vw_agyw_prev.ponto_entrada=2,'CM','ES')) as ponto_entrada,
                                app_dream_parceiros.name as organizacao,
                                data_registo, nui, faixa_registo, faixa_actual, idade_registo, idade_actual, data_nascimento,
                                if(idade_actual < 20 and sustenta_casa=1,1,0) +
                                if(idade_actual < 18 and e_orfa=1,1,0) +
                                if(idade_actual < 18 and vai_escola=0,1,0) +
                                if(tem_deficiencia=1,1,0) +
                                if(idade_actual < 20 and foi_casada=1,1,0) +
                                if(idade_actual < 20 and esteve_gravida=1,1,0) +
                                if(idade_actual < 20 and tem_filhos=1,1,0) +
                                if(idade_actual < 18 and gravida_amamentar=1,1,0) +
                                if(teste_hiv < 2 and data_registo < '2022-01-01',1,0) +
                                if(vitima_exploracao_sexual=1,1,0) +
                                if(idade_actual < 20 and migrante=1,1,0) +
                                if(idade_actual < 20 and vitima_trafico=1,1,0) +
                                if(idade_actual < 18 and sexualmente_activa=1,1,0) +
                                if(relacoes_multiplas_cocorrentes=1,1,0) +
                                if(vitima_vbg=1,1,0) +
                                if(idade_actual < 18 and trabalha<>0,1,0) +
                                if(idade_actual > 17 and trabalhadora_sexo=1,1,0) +
                                if(abuso_alcool_drogas=1,1,0) +
                                if(historico_its=1,1,0) as vulnerabilidades,
                    app_dream_tipo_servicos.name as tipo_servico,
                    app_dream_servicos.name as servico,
                    app_dream_servicos_sub.name as subservico,
                    app_dream_nivel_intervensao.name as pacote,
                    if(app_dream_vw_agyw_prev.ponto_entrada_id=1,'US',if(app_dream_vw_agyw_prev.ponto_entrada_id=2,'CM','ES')) as ponto_entrada_servico,
                    app_dream_us.name as localizacao, DATE_FORMAT(data_servico,'%d/%m/%Y') data_servico, provedor, observacoes
            from app_dream_vw_agyw_prev 
                left join hs_hr_province on hs_hr_province.id = app_dream_vw_agyw_prev.provincia_id
                left join hs_hr_district on hs_hr_district.district_code = app_dream_vw_agyw_prev.distrito_id
                left join app_dream_bairros on app_dream_bairros.id = app_dream_vw_agyw_prev.bairro_id
                left join app_dream_parceiros on app_dream_parceiros.id = app_dream_vw_agyw_prev.organizacao_id
                left join app_dream_tipo_servicos on app_dream_tipo_servicos.id = app_dream_vw_agyw_prev.area_servico_id
                left join app_dream_servicos on app_dream_servicos.id = app_dream_vw_agyw_prev.servico_id
                left join app_dream_servicos_sub on app_dream_servicos_sub.id = app_dream_vw_agyw_prev.sub_servico_id
                left join app_dream_nivel_intervensao on app_dream_nivel_intervensao.id = app_dream_vw_agyw_prev.pacote_servico_id
                left join app_dream_us on app_dream_us.id = app_dream_vw_agyw_prev.localizacao_id
            where beneficiario_id in (".$listBeneficiaries.");";

        $preparedQuery = Yii::$app->db->createCommand($query);
        $result = $preparedQuery->queryAll();

        return $result;

    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchList($params, $listBeneficiaries){
        
        $query = Beneficiarios::find()->where(['in', 'id', $listBeneficiaries]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        $query->andFilterWhere([
            'id' => $this->id,
            'emp_number' => $this->emp_number,
            'us_id' => $this->us_id,
            'ponto_entrada' => $this->ponto_entrada,
            'membro_zona' => $this->membro_zona,
            'membro_circulo' => $this->membro_circulo,
            'membro_celula' => $this->membro_celula,
            'membro_localidade_id' => $this->membro_localidade_id,
            'emp_smoker' => $this->emp_smoker,
            'nation_code' => $this->nation_code,
            'emp_gender' => $this->emp_gender,
            'emp_dri_lice_exp_date' => $this->emp_dri_lice_exp_date,
            'emp_status' => $this->emp_status,
            'job_title_code' => $this->job_title_code,
            'eeo_cat_code' => $this->eeo_cat_code,
            'work_station' => $this->work_station,
            'joined_date' => $this->joined_date,
            'termination_id' => $this->termination_id,
            'criado_por' => $this->criado_por,
            'actualizado_por' => $this->actualizado_por,
            'actualizado_em' => $this->actualizado_em,
            'estudante' => $this->estudante,
            'deficiencia' => $this->deficiencia,
            'bairro_id' => $this->bairro_id,
            'gravida' => $this->gravida,
            'filhos' => $this->filhos,
            'parceiro_id' => $this->parceiro_id,
        ]);

        $query->andFilterWhere(['like', 'member_id', $this->member_id])
            ->andFilterWhere(['like', 'emp_lastname', $this->emp_lastname])
            ->andFilterWhere(['like', 'emp_firstname', $this->emp_firstname])
            ->andFilterWhere(['like', 'emp_middle_name', $this->emp_middle_name])
            ->andFilterWhere(['like', 'us_id', $this->us_id])
            ->andFilterWhere(['like', 'ponto_entrada', $this->ponto_entrada])
            ->andFilterWhere(['like', 'estudante', $this->estudante])
            ->andFilterWhere(['like', 'deficiencia', $this->deficiencia])
            ->andFilterWhere(['=', 'bairro_id', $this->bairro_id])
            ->andFilterWhere(['like', 'gravida', $this->gravida])
            ->andFilterWhere(['like', 'filhos', $this->filhos])
            ->andFilterWhere(['like', 'emp_nick_name', $this->emp_nick_name])
            ->andFilterWhere(['like', 'membro_data_admissao', $this->membro_data_admissao])
            ->andFilterWhere(['like', 'membro_caratao_eleitor', $this->membro_caratao_eleitor])
            ->andFilterWhere(['like', 'membro_cargo_partido_id', $this->membro_cargo_partido_id])
            ->andFilterWhere(['like', 'ethnic_race_code', $this->ethnic_race_code])
            ->andFilterWhere(['like', 'emp_birthday', $this->emp_birthday])
            ->andFilterWhere(['like', 'emp_marital_status', $this->emp_marital_status])
            ->andFilterWhere(['like', 'emp_ssn_num', $this->emp_ssn_num])
            ->andFilterWhere(['like', 'emp_sin_num', $this->emp_sin_num])
            ->andFilterWhere(['like', 'emp_other_id', $this->emp_other_id])
            ->andFilterWhere(['like', 'emp_dri_lice_num', $this->emp_dri_lice_num])
            ->andFilterWhere(['like', 'emp_military_service', $this->emp_military_service])
            ->andFilterWhere(['like', 'emp_street1', $this->emp_street1])
            ->andFilterWhere(['like', 'emp_street2', $this->emp_street2])
            ->andFilterWhere(['like', 'city_code', $this->city_code])
            ->andFilterWhere(['like', 'coun_code', $this->coun_code])
            ->andFilterWhere(['=', 'provin_code', $this->provin_code])
            ->andFilterWhere(['=', 'district_code', $this->district_code])
            ->andFilterWhere(['=', 'emp_zipcode', $this->emp_zipcode])
            ->andFilterWhere(['like', 'emp_hm_telephone', $this->emp_hm_telephone])
            ->andFilterWhere(['like', 'emp_mobile', $this->emp_mobile])
            ->andFilterWhere(['like', 'emp_work_telephone', $this->emp_work_telephone])
            ->andFilterWhere(['like', 'emp_work_email', $this->emp_work_email])
            ->andFilterWhere(['like', 'sal_grd_code', $this->sal_grd_code])
            ->andFilterWhere(['like', 'emp_oth_email', $this->emp_oth_email])
            ->andFilterWhere(['like', 'bi', $this->bi])
            ->andFilterWhere(['like', 'nuit', $this->nuit])
            ->andFilterWhere(['like', 'passaporte', $this->passaporte])
            ->andFilterWhere(['like', 'dire', $this->dire])
            ->andFilterWhere(['like', 'bi_data_i', $this->bi_data_i])
            ->andFilterWhere(['like', 'bi_data_f', $this->bi_data_f])
            ->andFilterWhere(['like', 'custom3', $this->custom3])
            ->andFilterWhere(['like', 'other_prof_info', $this->other_prof_info])
            ->andFilterWhere(['like', 'nuit_data_i', $this->nuit_data_i])
            ->andFilterWhere(['like', 'nuit_data_f', $this->nuit_data_f])
            ->andFilterWhere(['like', 'custom7', $this->custom7])
            ->andFilterWhere(['like', 'custom8', $this->custom8])
            ->andFilterWhere(['like', 'custom9', $this->custom9])
            ->andFilterWhere(['like', 'custom10', $this->custom10])
            ->andFilterWhere(['like', 'criado_em', $this->criado_em])
            ->andFilterWhere(['like', 'user_location', $this->user_location])
            ->andFilterWhere(['like', 'user_location2', $this->user_location2]);

        return $dataProvider;

    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Beneficiarios::find()->where(['emp_status'=>1]);
		if (isset(Yii::$app->user->identity->role)&&(Yii::$app->user->identity->role>0)) { 
            if(isset(Yii::$app->user->identity->provin_code)&&(Yii::$app->user->identity->provin_code>0)) {
            $prov=(int)Yii::$app->user->identity->provin_code;
            $query = Beneficiarios::find()->where(['provin_code'=>$prov])->andWhere(['emp_status'=>1]);

            } elseif(Yii::$app->user->identity->role==20) {

                /*$todos=ServicosBeneficiados::find()
  ->where(['=','criado_por',Yii::$app->user->identity->id])
  ->andWhere(['=','status',1])
   ->asArray()
   ->all();

  $ids=ArrayHelper::getColumn($todos,'beneficiario_id');

$query = Beneficiarios::find()->andFilterWhere(['NOT IN','id',$ids])->andWhere(['emp_status'=>1]);
*/

$query = Beneficiarios::find()->where(['provin_code'=>5])->where(['emp_status'=>1]);

}

} else {
$query = Beneficiarios::find()->where(['emp_status'=>1]);
}
		

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);
		

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'emp_number' => $this->emp_number,
			'us_id' => $this->us_id,
			'ponto_entrada'=>$this->ponto_entrada,
            'membro_zona' => $this->membro_zona,
            'membro_circulo' => $this->membro_circulo,
            'membro_celula' => $this->membro_celula,
            'membro_localidade_id' => $this->membro_localidade_id,
            'emp_smoker' => $this->emp_smoker,
            'nation_code' => $this->nation_code,
            'emp_gender' => $this->emp_gender,
            'emp_dri_lice_exp_date' => $this->emp_dri_lice_exp_date,
            'emp_status' => $this->emp_status,
            'job_title_code' => $this->job_title_code,
            'eeo_cat_code' => $this->eeo_cat_code,
            'work_station' => $this->work_station,
            'joined_date' => $this->joined_date,
            'termination_id' => $this->termination_id,
            'criado_por' => $this->criado_por,
            'actualizado_por' => $this->actualizado_por,
         //   'criado_em' => $this->criado_em,
            'actualizado_em' => $this->actualizado_em,
           
            'estudante'=> $this->estudante,
            'deficiencia'=> $this->deficiencia,
            'bairro_id'=>$this->bairro_id,
            'gravida'=>$this->gravida,
            'filhos'=>$this->filhos,
	    'parceiro_id'=>$this->parceiro_id,
        ]);

        $query->andFilterWhere(['like', 'member_id', $this->member_id])
            ->andFilterWhere(['like', 'emp_lastname', $this->emp_lastname])
            ->andFilterWhere(['like', 'emp_firstname', $this->emp_firstname])
            ->andFilterWhere(['like', 'emp_middle_name', $this->emp_middle_name])
	    //->andFilterWhere(['=', 'us_id', $this->us_id])
          ->andFilterWhere(['like', 'us_id', $this->us_id])
	    ->andFilterWhere(['like', 'ponto_entrada', $this->ponto_entrada])

            ->andFilterWhere(['like', 'estudante', $this->estudante])
            ->andFilterWhere(['like', 'deficiencia', $this->deficiencia])
            ->andFilterWhere(['=', 'bairro_id', $this->bairro_id])
            ->andFilterWhere(['like', 'gravida', $this->gravida])
            ->andFilterWhere(['like', 'filhos', $this->filhos])
	    //->andFilterWhere(['=', 'parceiro_id', $this->parceiro_id])
          //->andFilterWhere(['like', 'parceiro_id', $this->parceiro_id])





            ->andFilterWhere(['like', 'emp_nick_name', $this->emp_nick_name])
            ->andFilterWhere(['like', 'membro_data_admissao', $this->membro_data_admissao])
            ->andFilterWhere(['like', 'membro_caratao_eleitor', $this->membro_caratao_eleitor])
            ->andFilterWhere(['like', 'membro_cargo_partido_id', $this->membro_cargo_partido_id])
            ->andFilterWhere(['like', 'ethnic_race_code', $this->ethnic_race_code])
            ->andFilterWhere(['like', 'emp_birthday', $this->emp_birthday])
            ->andFilterWhere(['like', 'emp_marital_status', $this->emp_marital_status])
            ->andFilterWhere(['like', 'emp_ssn_num', $this->emp_ssn_num])
            ->andFilterWhere(['like', 'emp_sin_num', $this->emp_sin_num])
            ->andFilterWhere(['like', 'emp_other_id', $this->emp_other_id])
            ->andFilterWhere(['like', 'emp_dri_lice_num', $this->emp_dri_lice_num])
            ->andFilterWhere(['like', 'emp_military_service', $this->emp_military_service])
            ->andFilterWhere(['like', 'emp_street1', $this->emp_street1])
            ->andFilterWhere(['like', 'emp_street2', $this->emp_street2])
            ->andFilterWhere(['like', 'city_code', $this->city_code])
            ->andFilterWhere(['like', 'coun_code', $this->coun_code])
            ->andFilterWhere(['=', 'provin_code', $this->provin_code])
            ->andFilterWhere(['=', 'district_code', $this->district_code])
            ->andFilterWhere(['=', 'emp_zipcode', $this->emp_zipcode])
           
 	    ->andFilterWhere(['like', 'emp_hm_telephone', $this->emp_hm_telephone])
            ->andFilterWhere(['like', 'emp_mobile', $this->emp_mobile])
            ->andFilterWhere(['like', 'emp_work_telephone', $this->emp_work_telephone])
            ->andFilterWhere(['like', 'emp_work_email', $this->emp_work_email])
            ->andFilterWhere(['like', 'sal_grd_code', $this->sal_grd_code])
            ->andFilterWhere(['like', 'emp_oth_email', $this->emp_oth_email])
            ->andFilterWhere(['like', 'bi', $this->bi])
            ->andFilterWhere(['like', 'nuit', $this->nuit])
            ->andFilterWhere(['like', 'passaporte', $this->passaporte])
            ->andFilterWhere(['like', 'dire', $this->dire])
            ->andFilterWhere(['like', 'bi_data_i', $this->bi_data_i])
            ->andFilterWhere(['like', 'bi_data_f', $this->bi_data_f])
            ->andFilterWhere(['like', 'custom3', $this->custom3])
            ->andFilterWhere(['like', 'other_prof_info', $this->other_prof_info])
            ->andFilterWhere(['like', 'nuit_data_i', $this->nuit_data_i])
            ->andFilterWhere(['like', 'nuit_data_f', $this->nuit_data_f])
            ->andFilterWhere(['like', 'custom7', $this->custom7])
            ->andFilterWhere(['like', 'custom8', $this->custom8])
            ->andFilterWhere(['like', 'custom9', $this->custom9])
            ->andFilterWhere(['like', 'custom10', $this->custom10])
	   
	    ->andFilterWhere(['like', 'criado_em', $this->criado_em])
            ->andFilterWhere(['like', 'user_location', $this->user_location])
            ->andFilterWhere(['like', 'user_location2', $this->user_location2]);

        return $dataProvider;
    }
}
