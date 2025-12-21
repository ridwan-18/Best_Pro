<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "claim".
 *
 * @property int $id
 * @property string $claim_no
 * @property string $policy_no
 * @property string $member_no
 * @property int|null $claim_age
 * @property float|null $sum_insured_reas
 * @property float|null $estimated_amount
 * @property string $incident_date
 * @property string $claim_reason
 * @property string $disease
 * @property string $place_of_death
 * @property string|null $borderaux_claim
 * @property string|null $doc_pre_received_date
 * @property string|null $doc_received_date
 * @property string $doc_status
 * @property string|null $doc_complete_date
 * @property string|null $doc_notes
 * @property string|null $payment_due_date
 * @property float|null $payment_amount
 * @property float|null $claim_amount
 * @property float|null $cash_value
 * @property string|null $transfer_type
 * @property string|null $bank_name
 * @property string|null $account_no
 * @property string|null $account_name
 * @property string|null $analyst1_diagnosed_by
 * @property string|null $analyst1_diagnose_notes
 * @property string|null $analyst1_historical_disease
 * @property string|null $analyst1_information
 * @property string|null $analyst1_investigation_by_phone
 * @property string|null $analyst1_medical_analysis
 * @property string|null $analyst1_result1
 * @property string|null $analyst1_recommendation1
 * @property string|null $analyst1_result2
 * @property string|null $analyst1_recommendation2
 * @property string|null $dept_approved_by
 * @property string|null $dept_approve_notes
 * @property string|null $dept_approve_status
 * @property string|null $div_approved_by
 * @property string|null $div_approve_notes
 * @property string|null $div_approve_status
 * @property string|null $gm_approved_by
 * @property string|null $gm_approve_notes
 * @property string|null $gm_approve_status
 * @property string|null $dir1_approved_by
 * @property string|null $dir1_approve_notes
 * @property string|null $dir1_approve_status
 * @property string|null $dir2_approved_by
 * @property string|null $dir2_approve_notes
 * @property string|null $dir2_approve_status
 * @property string|null $analyst2_diagnosed_by
 * @property string|null $analyst2_diagnose_notes
 * @property string|null $analyst2_result
 * @property string|null $analyst2_recommendation
 * @property string|null $dept_process_approved_by
 * @property string|null $dept_process_approve_notes
 * @property string|null $dept_process_approve_status
 * @property string|null $div_process_approved_by
 * @property string|null $div_process_approve_notes
 * @property string|null $div_process_approve_status
 * @property string|null $gm_process_approved_by
 * @property string|null $gm_process_approve_notes
 * @property string|null $gm_process_approve_status
 * @property string|null $dir1_process_approved_by
 * @property string|null $dir1_process_approve_notes
 * @property string|null $dir1_process_approve_status
 * @property string|null $dir2_process_approved_by
 * @property string|null $dir2_process_approve_notes
 * @property string|null $dir2_process_approve_status
 * @property float|null $approved_amount
 * @property string|null $status
 * @property string|null $decision
 * @property string|null $process_date
 * @property string|null $remarks
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Claim extends \yii\db\ActiveRecord
{
    const STATUS_REGISTRATION = 'Registration';
    const STATUS_VERIFICATION = 'Verification';
    const STATUS_ANALYSIS = 'Analysis';
    const STATUS_APPROVAL = 'Approval';
    const STATUS_APPEAL = 'Appeal/Ex-Gratia';

    const DECISION_APPROVED = 'Approved';
    const DECISION_REJECTED = 'Rejected';

    const DOC_STATUS_PENDING = 'Pending';
    const DOC_STATUS_COMPLETE = 'Complete';

    const TRANSFER_TYPE_POLICY = 'Policy';
    const TRANSFER_TYPE_MEMBER = 'Member';

    const RESULT_INVESTIGATE = 'Investigate';
    const RESULT_ACCEPTED = 'Accepted';
    const RESULT_REJECTED = 'Rejected';
    const RESULT_PENDING = 'Pending';

    const APPROVAL_INVESTIGATE = 'Investigate';
    const APPROVAL_APPROVE = 'Approve';
    const APPROVAL_REJECT = 'Reject';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'claim';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['claim_no', 'policy_no', 'member_no', 'incident_date', 'claim_reason', 'disease', 'place_of_death', 'doc_status'], 'required'],
            [['claim_age', 'created_by', 'updated_by'], 'integer'],
            [['sum_insured_reas', 'estimated_amount', 'payment_amount', 'claim_amount', 'cash_value', 'approved_amount'], 'number'],
            [['incident_date', 'doc_pre_received_date', 'doc_received_date', 'doc_complete_date', 'payment_due_date', 'process_date', 'created_at', 'updated_at'], 'safe'],
            [['claim_no', 'policy_no'], 'string', 'max' => 50],
            [['member_no'], 'string', 'max' => 100],
            [['claim_reason', 'disease', 'place_of_death', 'borderaux_claim', 'doc_notes', 'bank_name', 'account_no', 'account_name', 'analyst1_diagnosed_by', 'analyst1_diagnose_notes', 'analyst1_historical_disease', 'analyst1_information', 'analyst1_investigation_by_phone', 'analyst1_medical_analysis', 'analyst1_result1', 'analyst1_recommendation1', 'analyst1_result2', 'analyst1_recommendation2', 'dept_approved_by', 'dept_approve_notes', 'div_approved_by', 'div_approve_notes', 'gm_approved_by', 'gm_approve_notes', 'dir1_approved_by', 'dir1_approve_notes', 'dir2_approved_by', 'dir2_approve_notes', 'analyst2_diagnosed_by', 'analyst2_diagnose_notes', 'analyst2_result', 'analyst2_recommendation', 'dept_process_approved_by', 'dept_process_approve_notes', 'div_process_approved_by', 'div_process_approve_notes', 'gm_process_approved_by', 'gm_process_approve_notes', 'dir1_process_approved_by', 'dir1_process_approve_notes', 'dir2_process_approved_by', 'dir2_process_approve_notes', 'remarks'], 'string', 'max' => 255],
            [['doc_status', 'transfer_type'], 'string', 'max' => 10],
            [['dept_approve_status', 'div_approve_status', 'gm_approve_status', 'dir1_approve_status', 'dir2_approve_status', 'dept_process_approve_status', 'div_process_approve_status', 'gm_process_approve_status', 'dir1_process_approve_status', 'dir2_process_approve_status'], 'string', 'max' => 15],
            [['status', 'decision'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'claim_no' => 'Claim No',
            'policy_no' => 'Policy No',
            'member_no' => 'Member No',
            'claim_age' => 'Claim Age',
            'sum_insured_reas' => 'Sum Insured Reas',
            'estimated_amount' => 'Estimated Amount',
            'incident_date' => 'Incident Date',
            'claim_reason' => 'Claim Reason',
            'disease' => 'Disease',
            'place_of_death' => 'Place Of Death',
            'borderaux_claim' => 'Borderaux Claim',
            'doc_pre_received_date' => 'Doc Pre Received Date',
            'doc_received_date' => 'Doc Received Date',
            'doc_status' => 'Doc Status',
            'doc_complete_date' => 'Doc Complete Date',
            'doc_notes' => 'Doc Notes',
            'payment_due_date' => 'Payment Due Date',
            'payment_amount' => 'Payment Amount',
            'claim_amount' => 'Claim Amount',
            'cash_value' => 'Cash Value',
            'transfer_type' => 'Transfer Type',
            'bank_name' => 'Bank Name',
            'account_no' => 'Account No',
            'account_name' => 'Account Name',
            'analyst1_diagnosed_by' => 'Analyst 1 Diagnosed By',
            'analyst1_diagnose_notes' => 'Analyst 1 Diagnose Notes',
            'analyst1_historical_disease' => 'Analyst 1 Historical Disease',
            'analyst1_information' => 'Analyst 1 Information',
            'analyst1_investigation_by_phone' => 'Analyst 1 Investigation By Phone',
            'analyst1_medical_analysis' => 'Analyst 1 Medical Analysis',
            'analyst1_result1' => 'Analyst 1 Result 1',
            'analyst1_recommendation1' => 'Analyst 1 Recommendation 1',
            'analyst1_result2' => 'Analyst 1 Result 2',
            'analyst1_recommendation2' => 'Analyst 1 Recommendation 2',
            'dept_approved_by' => 'Dept Approved By',
            'dept_approve_notes' => 'Dept Approve Notes',
            'dept_approve_status' => 'Dept Approve Status',
            'div_approved_by' => 'Div Approved By',
            'div_approve_notes' => 'Div Approve Notes',
            'div_approve_status' => 'Div Approve Status',
            'gm_approved_by' => 'Gm Approved By',
            'gm_approve_notes' => 'Gm Approve Notes',
            'gm_approve_status' => 'Gm Approve Status',
            'dir1_approved_by' => 'Dir 1 Approved By',
            'dir1_approve_notes' => 'Dir 1 Approve Notes',
            'dir1_approve_status' => 'Dir 1 Approve Status',
            'dir2_approved_by' => 'Dir 2 Approved By',
            'dir2_approve_notes' => 'Dir 2 Approve Notes',
            'dir2_approve_status' => 'Dir 2 Approve Status',
            'analyst2_diagnosed_by' => 'Analyst 2 Diagnosed By',
            'analyst2_diagnose_notes' => 'Analyst 2 Diagnose Notes',
            'analyst2_result' => 'Analyst 2 Result',
            'analyst2_recommendation' => 'Analyst 2 Recommendation',
            'dept_process_approved_by' => 'Dept Process Approved By',
            'dept_process_approve_notes' => 'Dept Process Approve Notes',
            'dept_process_approve_status' => 'Dept Process Approve Status',
            'div_process_approved_by' => 'Div Process Approved By',
            'div_process_approve_notes' => 'Div Process Approve Notes',
            'div_process_approve_status' => 'Div Process Approve Status',
            'gm_process_approved_by' => 'Gm Process Approved By',
            'gm_process_approve_notes' => 'Gm Process Approve Notes',
            'gm_process_approve_status' => 'Gm Process Approve Status',
            'dir1_process_approved_by' => 'Dir 1 Process Approved By',
            'dir1_process_approve_notes' => 'Dir 1 Process Approve Notes',
            'dir1_process_approve_status' => 'Dir 1 Process Approve Status',
            'dir2_process_approved_by' => 'Dir 2 Process Approved By',
            'dir2_process_approve_notes' => 'Dir 2 Process Approve Notes',
            'dir2_process_approve_status' => 'Dir 2 Process Approve Status',
            'approved_amount' => 'Approved Amount',
            'status' => 'Status',
            'decision' => 'Decision',
            'process_date' => 'Process Date',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getAll($params = [])
    {
        $query = self::find()
            ->select([
                self::tableName() . '.id',
                self::tableName() . '.claim_no',
                self::tableName() . '.policy_no',
                self::tableName() . '.member_no',
                self::tableName() . '.claim_reason',
                self::tableName() . '.estimated_amount',
                self::tableName() . '.approved_amount',
                self::tableName() . '.status',
                self::tableName() . '.decision',
                Partner::tableName() . '.name AS partner',
                Personal::tableName() . '.name AS member',
            ])
            ->asArray()
            ->innerJoin(Policy::tableName(), Policy::tableName() . '.policy_no = ' . self::tableName() . '.policy_no')
            ->innerJoin(Partner::tableName(), Partner::tableName() . '.id = ' . Policy::tableName() . '.partner_id')
            ->innerJoin(Member::tableName(), Member::tableName() . '.member_no = ' . self::tableName() . '.member_no')
            ->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' . Member::tableName() . '.personal_no');
		
		if (!Yii::$app->user->isGuest) {
			if (Yii::$app->user->identity->role == User::ROLE_UW) {
				$query->andWhere(['=', self::tableName() . '.created_by', Yii::$app->user->identity->id]);
			}
		}
		
        if (isset($params['claim_no']) && $params['claim_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.claim_no', $params['claim_no']]);
        }

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['member_no']) && $params['member_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.member_no', $params['member_no']]);
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

        if (isset($params['claim_no']) && $params['claim_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.claim_no', $params['claim_no']]);
        }

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['member_no']) && $params['member_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.member_no', $params['member_no']]);
        }

        return $query->count();
    }

    public static function generateClaimNo($params)
    {
        return 'CLA-' . date("Ym") . $params['id'];
    }

    public static function getClaimAge($startDate, $endDate)
    {
        $d1 = new \DateTime($startDate);
        $d2 = new \DateTime($endDate);
        $Months = $d2->diff($d1);
        return $Months->y;
    }

    public static function statuses($selected = null)
    {
        $data = [
            self::STATUS_REGISTRATION => self::STATUS_REGISTRATION,
            self::STATUS_VERIFICATION => self::STATUS_VERIFICATION,
            self::STATUS_ANALYSIS => self::STATUS_ANALYSIS,
            self::STATUS_APPROVAL => self::STATUS_APPROVAL,
            self::STATUS_APPEAL => self::STATUS_APPEAL,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function decisions($selected = null)
    {
        $data = [
            self::DECISION_APPROVED => self::DECISION_APPROVED,
            self::DECISION_REJECTED => self::DECISION_REJECTED,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function results($selected = null)
    {
        $data = [
            self::RESULT_INVESTIGATE => self::RESULT_INVESTIGATE,
            self::RESULT_ACCEPTED => self::RESULT_ACCEPTED,
            self::RESULT_REJECTED => self::RESULT_REJECTED,
            self::RESULT_PENDING => self::RESULT_PENDING,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function approvals($selected = null)
    {
        $data = [
            self::APPROVAL_REJECT => self::APPROVAL_REJECT,
            self::APPROVAL_APPROVE => self::APPROVAL_APPROVE,
            self::APPROVAL_INVESTIGATE => self::APPROVAL_INVESTIGATE,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }
}
