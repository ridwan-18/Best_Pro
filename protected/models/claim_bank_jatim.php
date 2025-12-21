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
class claim_bank_jatim extends \yii\db\ActiveRecord
{
    const STATUS_INFORCE = 'Inforce';
    const STATUS_LAPSED = 'Lapsed';
    const STATUS_CLAIM = 'Claim';
    const STATUS_SURRENDER = 'Surrender';
    const STATUS_MATURITY = 'Maturity';
    const STATUS_CHANGE = 'Change';
    const STATUS_CANCEL = 'Cancel';

    const MEMBER_STATUS_INFORCE = 'Inforce';
    const MEMBER_STATUS_PENDING = 'Pending';
    const MEMBER_STATUS_DECLINED = 'Declined';

    const REAS_STATUS_TREATY = 'Treaty';
    const REAS_STATUS_OUT = 'Out of Treaty';
    const REAS_STATUS_FACULTATIVE = 'Facultative';

    const EM_MANUAL = 1;
    const EM_FROM_PRODUCT = 2;

    const PAGE_SIZE = 20;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_claim_jatim';
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
            'id_loan' => 'Policy No',
            'name' => 'Batch No',
            'dokument' => 'Member No',
            'create_at' => 'Created At',
      
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

        // if (isset($params['member_id']) && $params['member_id'] != null) {
            // $query->andFilterWhere(['=', self::tableName() . '.id', $params['member_id']]);
        // }

        // if (isset($params['policy_no']) && $params['policy_no'] != null) {
            // $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        // }

        // if (isset($params['batch_no']) && $params['batch_no'] != null) {
            // $query->andFilterWhere(['=', self::tableName() . '.batch_no', $params['batch_no']]);
        // }

        // if (
            // isset($params['start_date'])
            // && $params['start_date'] != null
            // && isset($params['end_date'])
            // && $params['end_date'] != null
        // ) {
            // $query->andFilterWhere(['>=', self::tableName() . '.start_date', $params['start_date']]);
            // $query->andFilterWhere(['<=', self::tableName() . '.end_date', $params['end_date']]);
        // }

        // if (isset($params['status']) && $params['status'] != null) {
            // $query->andFilterWhere(['=', self::tableName() . '.status', $params['status']]);
        // }

        // if (isset($params['member_status']) && $params['member_status'] != null) {
            // $query->andFilterWhere(['=', self::tableName() . '.member_status', $params['member_status']]);
        // }

        // if (isset($params['reas_status']) && $params['reas_status'] != null) {
            // $query->andFilterWhere(['=', self::tableName() . '.reas_status', $params['reas_status']]);
        // }

        // if (isset($params['is_accumulated']) && $params['is_accumulated'] == 1) {
            // $query->andFilterWhere(['like', self::tableName() . '.acc_status', 'Accumulated']);
        // }

        // if (isset($params['offset']) && $params['offset'] != null) {
            // $query->offset($params['offset']);
        // }

        // if (isset($params['limit']) && $params['limit'] != null) {
            // $query->limit($params['limit']);
        // }

        $query->groupBy([self::tableName() . '.id', self::tableName() . '.personal_no']);
        $query->orderBy([self::tableName() . '.id' => $params['sort']]);

        return $query->all();
    }
}
