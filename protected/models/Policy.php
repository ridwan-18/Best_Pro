<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "policy".
 *
 * @property int $id
 * @property int $quotation_id
 * @property string $spa_no
 * @property string|null $policy_no
 * @property int $partner_id
 * @property string $spa_date
 * @property string $distribution_channel
 * @property string|null $pic_name
 * @property string|null $pic_title
 * @property string|null $pic_id_card_no
 * @property string|null $pic_phone
 * @property string|null $pic_email
 * @property int $bank_id
 * @property string $bank_branch
 * @property string $bank_account_no
 * @property string $bank_account_name
 * @property string $payment_method
 * @property string $effective_date
 * @property string $end_date
 * @property int $insurance_period
 * @property int $payment_period
 * @property string $member_type
 * @property int $member_qty
 * @property int $member_insured
 * @property string|null $notes
 * @property string|null $work_location
 * @property string|null $sign_location
 * @property string|null $sign_date
 * @property string|null $sign_by
 * @property string|null $sign_title
 * @property string $spa_status
 * @property string $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $issued_at
 * @property int|null $issued_by
 */
class Policy extends \yii\db\ActiveRecord
{
    const SPA_STATUS_REGISTER = 'Register';
    const SPA_STATUS_ISSUED = 'Issued';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'policy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id', 'spa_no', 'partner_id', 'spa_date', 'distribution_channel', 'bank_id', 'bank_branch', 'bank_account_no', 'bank_account_name', 'payment_method', 'effective_date', 'end_date', 'insurance_period', 'payment_period', 'member_type', 'member_qty', 'member_insured'], 'required'],
            [['quotation_id', 'partner_id', 'bank_id', 'insurance_period', 'payment_period', 'member_qty', 'member_insured', 'created_by', 'updated_by', 'issued_by'], 'integer'],
            [['spa_date', 'effective_date', 'end_date', 'sign_date', 'created_at', 'updated_at', 'issued_at'], 'safe'],
            [['spa_no', 'policy_no', 'distribution_channel', 'pic_id_card_no', 'pic_phone', 'pic_email', 'payment_method', 'member_type', 'spa_status', 'status'], 'string', 'max' => 50],
            [['pic_name', 'pic_title', 'bank_branch', 'bank_account_no', 'bank_account_name', 'notes', 'work_location', 'sign_location', 'sign_by', 'sign_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quotation_id' => 'Quotation ID',
            'spa_no' => 'Spa No',
            'policy_no' => 'Policy No',
            'partner_id' => 'Partner ID',
            'spa_date' => 'Spa Date',
            'distribution_channel' => 'Distribution Channel',
            'pic_name' => 'Pic Name',
            'pic_title' => 'Pic Title',
            'pic_id_card_no' => 'Pic Id Card No',
            'pic_phone' => 'Pic Phone',
            'pic_email' => 'Pic Email',
            'bank_id' => 'Bank ID',
            'bank_branch' => 'Bank Branch',
            'bank_account_no' => 'Bank Account No',
            'bank_account_name' => 'Bank Account Name',
            'payment_method' => 'Payment Method',
            'effective_date' => 'Effective Date',
            'end_date' => 'End Date',
            'insurance_period' => 'Insurance Period',
            'payment_period' => 'Payment Period',
            'member_type' => 'Member Type',
            'member_qty' => 'Member Qty',
            'member_insured' => 'Member Insured',
            'notes' => 'Notes',
            'work_location' => 'Work Location',
            'sign_location' => 'Sign Location',
            'sign_date' => 'Sign Date',
            'sign_by' => 'Sign By',
            'sign_title' => 'Sign Title',
            'spa_status' => 'Spa Status',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'issued_at' => 'Issued At',
            'issued_by' => 'Issued By',
        ];
    }

    public static function getAll($params = [])
    {
        $query = self::find()
            ->select([
                self::tableName() . '.id',
                self::tableName() . '.spa_no',
                self::tableName() . '.policy_no',
                self::tableName() . '.spa_date',
                self::tableName() . '.effective_date',
                self::tableName() . '.end_date',
                self::tableName() . '.spa_status',
                self::tableName() . '.created_by',
                Partner::tableName() . '.name AS partner',
            ])
            ->asArray()
            ->innerJoin(Partner::tableName(), Partner::tableName() . '.id = ' . self::tableName() . '.partner_id');

        if (isset($params['spa_no']) && $params['spa_no'] != null) {
            $query->andFilterWhere(['like', self::tableName() . '.spa_no', $params['spa_no']]);
        }

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['like', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['partner_name']) && $params['partner_name'] != null) {
            $query->andFilterWhere(['like', Partner::tableName() . '.name', $params['partner_name']]);
        }

        if (isset($params['spa_status']) && $params['spa_status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.spa_status', $params['spa_status']]);
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
        $query = self::find()
            ->innerJoin(Partner::tableName(), Partner::tableName() . '.id = ' . self::tableName() . '.partner_id');

        if (isset($params['spa_no']) && $params['spa_no'] != null) {
            $query->andFilterWhere(['like', self::tableName() . '.spa_no', $params['spa_no']]);
        }

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['like', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['partner_name']) && $params['partner_name'] != null) {
            $query->andFilterWhere(['like', Partner::tableName() . '.name', $params['partner_name']]);
        }

        if (isset($params['spa_status']) && $params['spa_status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.spa_status', $params['spa_status']]);
        }

        return $query->count();
    }

    public static function generateSpaNo($params)
    {
        return date("ym") . str_pad($params['id'], 6, '0', STR_PAD_LEFT);
    }

    public static function generatePolicyNo($params)
    {
        return '1' . $params['code'] . date("ym") . str_pad($params['id'], 6, '0', STR_PAD_LEFT);
    }

    public static function spaStatuses($value = null)
    {
        $data = [
            self::SPA_STATUS_REGISTER => self::SPA_STATUS_REGISTER,
            self::SPA_STATUS_ISSUED => self::SPA_STATUS_ISSUED,
        ];

        if ($value != null) {
            return $data[$value];
        }

        return $data;
    }

    public static function getEndDate($term, $effectiveDate)
    {
        if ($term == Term::_1_MONTH) {
            return date("Y-m-d", strtotime("+1 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_2_MONTH) {
            return date("Y-m-d", strtotime("+2 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_3_MONTH) {
            return date("Y-m-d", strtotime("+3 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_4_MONTH) {
            return date("Y-m-d", strtotime("+4 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_5_MONTH) {
            return date("Y-m-d", strtotime("+5 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_5_MONTH) {
            return date("Y-m-d", strtotime("+5 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_6_MONTH) {
            return date("Y-m-d", strtotime("+6 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_7_MONTH) {
            return date("Y-m-d", strtotime("+7 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_8_MONTH) {
            return date("Y-m-d", strtotime("+8 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_9_MONTH) {
            return date("Y-m-d", strtotime("+9 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_10_MONTH) {
            return date("Y-m-d", strtotime("+10 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_11_MONTH) {
            return date("Y-m-d", strtotime("+11 month", strtotime($effectiveDate)));
        }
        if ($term == Term::_12_MONTH) {
            return date("Y-m-d", strtotime("+12 month", strtotime($effectiveDate)));
        }
        if ($term == Term::OVER_12_MONTH || $term == Term::OPEN_POLIS) {
            return date("Y-m-d", strtotime("+100 year", strtotime($effectiveDate)));
        }
        if ($term == Term::UNDER_1_MONTH) {
            return date("Y-m-d", strtotime("+29 day", strtotime($effectiveDate)));
        }
        if ($term == Term::UNDER_1_WEEK) {
            return date("Y-m-d", strtotime("+6 day", strtotime($effectiveDate)));
        }
        if ($term == Term::UNDER_2_WEEK) {
            return date("Y-m-d", strtotime("+13 day", strtotime($effectiveDate)));
        }
    }
}
