<?php

namespace app\models;

use DateTime;
use Yii;

/**
 * This is the model class for table "member".
 *
 * @property int $id
 * @property string $policy_no
 * @property string $batch_no
 * @property string|null $member_no
 * @property string $personal_no
 * @property int|null $age
 * @property string|null $branch
 * @property string|null $branch_code
 * @property string|null $account_no
 * @property string|null $bank_branch
 * @property int $term
 * @property string $start_date
 * @property string $end_date
 * @property float $sum_insured
 * @property float|null $total_si
 * @property float|null $total_premium
 * @property float|null $rate_premi
 * @property float|null $rate_saving
 * @property float|null $gross_premium
 * @property float|null $basic_premium
 * @property float|null $saving_premium
 * @property float|null $percentage_discount
 * @property float|null $discount_premium
 * @property float|null $nett_premium
 * @property string|null $medical_code
 * @property string|null $status
 * @property string|null $member_status
 * @property string|null $reas_status
 * @property string|null $status_reason
 * @property string|null $stnc_date
 * @property string|null $stnc_status
 * @property string|null $stnc_reason
 * @property string|null $acc_status
 * @property float|null $percentage_extra_premium
 * @property float|null $extra_premium
 * @property int|null $em_type
 * @property float|null $percentage_em
 * @property float|null $rate_em
 * @property float|null $em_premium
 * @property string|null $em_notes
 * @property string|null $uw_notes
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class dokument_claim_jatim extends \yii\db\ActiveRecord
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
        return 'dokument_claim_jatim';
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
            'jenis_kematian' => 'jenis_kematian',
            'kode' => 'kode',
            'nama_dokument' => 'nama_dokument',
      
        ];
    }

	
	  public static function getAll($params = [])
    {
         $query = self::find()
		   -> select ([
		   self::tableName() . '.kode',
		    self::tableName() . '.nama_dokument',
		   ])
            ->asArray();

        if (isset($params['jenis_claim']) && $params['jenis_claim'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.jenis_claim', $params['jenis_claim']]);
        }

        // if (isset($params['offset']) && $params['offset'] != null) {
            // $query->offset($params['offset']);
        // }

        // if (isset($params['limit']) && $params['limit'] != null) {
            // $query->limit($params['limit']);
        // }

        $query->orderBy(['id' => $params['sort']]);

        return $query->all();
    }
}
