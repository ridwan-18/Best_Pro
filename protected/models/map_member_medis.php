<?php

namespace app\models;

use DateTime;
use Yii;


class map_member_medis extends \yii\db\ActiveRecord
{
    const STATUS_INFORCE = 'Inforce';

    const EM_MANUAL = 1;
    const EM_FROM_PRODUCT = 2;

    const PAGE_SIZE = 20;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_map_member_medis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['policy_no', 'batch_no', 'personal_no', 'term', 'start_date', 'end_date', 'sum_insured'], 'required'],
            [['age', 'term', 'em_type', 'created_by', 'updated_by'], 'integer'],
            [['start_date', 'end_date', 'stnc_date', 'created_at', 'updated_at'], 'safe'],
            [['sum_insured', 'total_si', 'total_premium', 'rate_premi', 'rate_saving', 'gross_premium', 'basic_premium', 'saving_premium', 'percentage_discount', 'discount_premium', 'nett_premium', 'percentage_extra_premium', 'extra_premium', 'percentage_em', 'rate_em', 'em_premium'], 'number'],
            [['policy_no', 'batch_no', 'medical_code'], 'string', 'max' => 50],
            [['member_no'], 'string', 'max' => 100],
            [['personal_no', 'branch', 'branch_code', 'account_no', 'bank_branch', 'status_reason', 'stnc_status', 'stnc_reason', 'acc_status', 'em_notes', 'uw_notes'], 'string', 'max' => 255],
            [['status', 'member_status', 'reas_status'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_loan' => 'id_loan',
			'kode_dokumen' => 'kode_dokumen',
            'files' => 'files',
            
      
        ];
    }

	
	  public static function getAllParticipantjatim($params = [])
    {
        $query = self::find()
            ->select([
                self::tableName() . '.id',
                self::tableName() . '.policy_no',
                self::tableName() . '.batch_no',
                self::tableName() . '.member_no',
                self::tableName() . '.term',
                self::tableName() . '.age',
                self::tableName() . '.start_date',
                self::tableName() . '.end_date',
                self::tableName() . '.sum_insured',
                self::tableName() . '.total_si',
                self::tableName() . '.total_premium',
                self::tableName() . '.rate_premi',
                self::tableName() . '.rate_saving',
                self::tableName() . '.gross_premium',
                self::tableName() . '.basic_premium',
                self::tableName() . '.saving_premium',
                self::tableName() . '.percentage_discount',
                self::tableName() . '.discount_premium',
                self::tableName() . '.nett_premium',
                self::tableName() . '.medical_code',
                self::tableName() . '.status',
                self::tableName() . '.member_status',
                self::tableName() . '.reas_status',
                self::tableName() . '.status_reason',
                self::tableName() . '.stnc_date',
                self::tableName() . '.stnc_status',
                self::tableName() . '.stnc_reason',
                self::tableName() . '.acc_status',
                self::tableName() . '.percentage_extra_premium',
                self::tableName() . '.extra_premium',
                self::tableName() . '.em_type',
                self::tableName() . '.percentage_em',
                self::tableName() . '.rate_em',
                self::tableName() . '.em_premium',
                self::tableName() . '.em_notes',
                self::tableName() . '.uw_notes',
                '(SELECT ' . Personal::tableName() . '.name' .  ' FROM ' . Personal::tableName() . ' WHERE ' . Personal::tableName() . '.personal_no = ' . self::tableName() . '.personal_no GROUP BY ' . self::tableName() . '.personal_no) AS name',
                '(SELECT ' . Personal::tableName() . '.birth_date' .  ' FROM ' . Personal::tableName() . ' WHERE ' . Personal::tableName() . '.personal_no = ' . self::tableName() . '.personal_no GROUP BY ' . self::tableName() . '.personal_no) AS birth_date',
                '(SELECT ' . Personal::tableName() . '.gender' .  ' FROM ' . Personal::tableName() . ' WHERE ' . Personal::tableName() . '.personal_no = ' . self::tableName() . '.personal_no GROUP BY ' . self::tableName() . '.personal_no) AS gender',
            ])
            ->asArray();

        

        $query->groupBy([self::tableName() . '.id', self::tableName() . '.personal_no']);
        $query->orderBy([self::tableName() . '.id' => $params['sort']]);

        return $query->all();
    }
	
	
public static function getAll($params = [])
    {
        $query = self::find()
            ->asArray();
	

        if (isset($params['offset']) && $params['offset'] != null) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] != null) {
            $query->limit($params['limit']);
        }

        $query->orderBy(['id' => $params['sort']]);

        return $query->all();
    }
	
	
	
}
