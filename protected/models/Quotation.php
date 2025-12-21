<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotation".
 *
 * @property int $id
 * @property string $proposal_no
 * @property int|null $policy_id
 * @property int $partner_id
 * @property string $member_type
 * @property int $member_qty
 * @property string $last_insurance
 * @property string $business_type
 * @property string $proposed_date
 * @property string $expired_date
 * @property string $term
 * @property int $member_card
 * @property int $certificate_card
 * @property string $distribution_channel
 * @property int $agent_id
 * @property string $payment_method
 * @property int $min_age
 * @property int $max_age
 * @property string $age_calculate
 * @property string $effective_policy
 * @property string $rate_type
 * @property string|null $notes
 * @property string $status
 * @property int $is_req_new_rate
 * @property int $is_req_tc
 * @property int $is_req_reas
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $verified_at
 * @property int|null $verified_by
 * @property string|null $closed_at
 * @property int|null $closed_by
 */
class Quotation extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 'New';
    const STATUS_PROPOSED = 'Proposed';
    const STATUS_REVISED = 'Revised';
    const STATUS_UPDATE_PROPOSED = 'Update Proposed';
    const STATUS_APPROVED = 'Approved';
    const STATUS_CLOSED = 'Closed';

    const EFFECTIVE_POLICY_DTD = 'day to day';
    const EFFECTIVE_POLICY_DTDM = 'day to day -1';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['proposal_no', 'partner_id', 'member_type', 'member_qty', 'business_type', 'proposed_date', 'expired_date', 'term', 'distribution_channel', 'agent_id', 'payment_method', 'min_age', 'max_age', 'age_calculate', 'effective_policy', 'rate_type'], 'required'],
            [['policy_id', 'partner_id', 'member_qty', 'member_card', 'certificate_card', 'agent_id', 'min_age', 'max_age', 'is_req_new_rate', 'is_req_tc', 'is_req_reas', 'created_by', 'updated_by', 'verified_by', 'closed_by'], 'integer'],
            [['proposed_date', 'expired_date', 'created_at', 'updated_at', 'verified_at', 'closed_at'], 'safe'],
            [['proposal_no', 'member_type', 'business_type', 'term', 'distribution_channel', 'payment_method', 'age_calculate', 'effective_policy', 'rate_type', 'status'], 'string', 'max' => 50],
            [['last_insurance', 'notes'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'proposal_no' => 'Proposal No',
            'policy_id' => 'Policy ID',
            'partner_id' => 'Partner ID',
            'member_type' => 'Member Type',
            'member_qty' => 'Member Qty',
            'last_insurance' => 'Last Insurance',
            'business_type' => 'Business Type',
            'proposed_date' => 'Proposed Date',
            'expired_date' => 'Expired Date',
            'term' => 'Term',
            'member_card' => 'Member Card',
            'certificate_card' => 'Certificate Card',
            'distribution_channel' => 'Distribution Channel',
            'agent_id' => 'Agent ID',
            'payment_method' => 'Payment Method',
            'min_age' => 'Min Age',
            'max_age' => 'Max Age',
            'age_calculate' => 'Age Calculate',
            'effective_policy' => 'Effective Policy',
            'rate_type' => 'Rate Type',
            'notes' => 'Notes',
            'status' => 'Status',
            'is_req_new_rate' => 'Is Req New Rate',
            'is_req_tc' => 'Is Req Tc',
            'is_req_reas' => 'Is Req Reas',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'verified_at' => 'Verified At',
            'verified_by' => 'Verified By',
            'closed_at' => 'Closed At',
            'closed_by' => 'Closed By',
        ];
    }

    public static function getAll($params = [])
    {
        $query = self::find()
            ->select([
                self::tableName() . '.id',
                self::tableName() . '.proposal_no',
                self::tableName() . '.proposed_date',
                self::tableName() . '.status',
                self::tableName() . '.is_req_new_rate',
                self::tableName() . '.is_req_tc',
                self::tableName() . '.is_req_reas',
                self::tableName() . '.created_at',
                self::tableName() . '.created_by',
                Partner::tableName() . '.name AS partner',
                User::tableName() . '.username AS created_by',
            ])
            ->asArray()
            ->innerJoin(Partner::tableName(), Partner::tableName() . '.id = ' . self::tableName() . '.partner_id')
            ->innerJoin(User::tableName(), User::tableName() . '.id = ' . self::tableName() . '.created_by');

        if (isset($params['proposal_no']) && $params['proposal_no'] != null) {
            $query->andFilterWhere(['like', self::tableName() . '.proposal_no', $params['proposal_no']]);
        }

        if (isset($params['partner_name']) && $params['partner_name'] != null) {
            $query->andFilterWhere(['like', Partner::tableName() . '.name', $params['partner_name']]);
        }

        if (isset($params['offset']) && $params['offset'] != null) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] != null) {
            $query->limit($params['limit']);
        }

        $query->orderBy(['id' => $params['sort']]);

        return $query->all();
    }

    public static function countAll($params = [])
    {
        $query = self::find();

        if (isset($params['proposal_no']) && $params['proposal_no'] != null) {
            $query->andFilterWhere(['like', self::tableName() . '.proposal_no', $params['proposal_no']]);
        }

        return $query->count();
    }

    public static function statuses($selected = null)
    {
        $data = [
            self::STATUS_NEW => self::STATUS_NEW,
            self::STATUS_PROPOSED => self::STATUS_PROPOSED,
            self::STATUS_REVISED => self::STATUS_REVISED,
            self::STATUS_UPDATE_PROPOSED => self::STATUS_UPDATE_PROPOSED,
            self::STATUS_APPROVED => self::STATUS_APPROVED,
            self::STATUS_CLOSED => self::STATUS_CLOSED,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function generateProposalNo($params)
    {
        $words = explode(" ", $params['partner_name']);
        $partnerName = "";
        foreach ($words as $w) {
            $partnerName .= $w[0];
        }

        return str_pad($params['id'], 6, '0', STR_PAD_LEFT) . '/AJRI-M/' . $partnerName . $params['id'] . '/' . date("m") . '/' . date("Y");
    }

    public static function generateProposedDate()
    {
        return date("Y-m-d");
    }

    public static function generateExpiredDate()
    {
        return date('Y-m-d', strtotime(self::generateProposedDate() . ' + 30 days'));
    }
}
