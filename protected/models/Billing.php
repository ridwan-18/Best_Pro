<?php

namespace app\models;

use SebastianBergmann\CodeCoverage\Util;
use Yii;


/**
 * This is the model class for table "billing".
 *
 * @property int $id
 * @property string $batch_no
 * @property string $policy_no
 * @property string $reg_no
 * @property string $invoice_no
 * @property string $invoice_date
 * @property string $due_date
 * @property string $accept_date
 * @property int $total_member
 * @property float $gross_premium
 * @property float $extra_premium
 * @property float $discount
 * @property float $handling_fee
 * @property float $pph
 * @property float $ppn
 * @property float $nett_premium
 * @property float $admin_cost
 * @property float $policy_cost
 * @property float $member_card_cost
 * @property float $certificate_cost
 * @property float $stamp_cost
 * @property float $total_billing
 * @property string|null $memo_no
 * @property string|null $memo_date
 * @property string|null $print_date
 * @property string|null $payment_date
 * @property string|null $remarks
 * @property int $is_checked_by_uw
 * @property int $is_checked_by_finance
 * @property string $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Billing extends \yii\db\ActiveRecord
{
    const STATUS_UNVERIFIED = 'Unverified';
    const STATUS_VERIFIED = 'Verified';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'billing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['batch_no', 'policy_no', 'reg_no', 'invoice_no', 'invoice_date', 'due_date', 'accept_date', 'status'], 'required'],
            [['invoice_date', 'due_date', 'accept_date', 'memo_date', 'print_date', 'payment_date', 'created_at', 'updated_at'], 'safe'],
            [['total_member', 'is_checked_by_uw', 'is_checked_by_finance', 'created_by', 'updated_by'], 'integer'],
            [['gross_premium', 'extra_premium', 'discount', 'handling_fee', 'pph', 'ppn', 'nett_premium', 'admin_cost', 'policy_cost', 'member_card_cost', 'certificate_cost', 'stamp_cost', 'total_billing'], 'number'],
            [['batch_no', 'policy_no', 'reg_no', 'invoice_no', 'memo_no'], 'string', 'max' => 50],
            [['remarks'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'batch_no' => 'Batch No',
            'policy_no' => 'Policy No',
            'reg_no' => 'Reg No',
            'invoice_no' => 'Invoice No',
            'invoice_date' => 'Invoice Date',
            'due_date' => 'Due Date',
            'accept_date' => 'Accept Date',
            'total_member' => 'Total Member',
            'gross_premium' => 'Gross Premium',
            'extra_premium' => 'Extra Premium',
            'discount' => 'Discount',
            'handling_fee' => 'Handling Fee',
            'pph' => 'Pph',
            'ppn' => 'Ppn',
            'nett_premium' => 'Nett Premium',
            'admin_cost' => 'Admin Cost',
            'policy_cost' => 'Policy Cost',
            'member_card_cost' => 'Member Card Cost',
            'certificate_cost' => 'Certificate Cost',
            'stamp_cost' => 'Stamp Cost',
            'total_billing' => 'Total Billing',
            'memo_no' => 'Memo No',
            'memo_date' => 'Memo Date',
            'print_date' => 'Print Date',
            'payment_date' => 'Payment Date',
            'remarks' => 'Remarks',
            'is_checked_by_uw' => 'Is Checked By Uw',
            'is_checked_by_finance' => 'Is Checked By Finance',
            'status' => 'Status',
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
                self::tableName() . '.policy_no',
                self::tableName() . '.batch_no',
                self::tableName() . '.reg_no',
                self::tableName() . '.invoice_no',
                self::tableName() . '.invoice_date',
                self::tableName() . '.due_date',
                self::tableName() . '.accept_date',
                self::tableName() . '.nett_premium',
                self::tableName() . '.memo_date',
                self::tableName() . '.remarks',
                self::tableName() . '.status',
                Partner::tableName() . '.name AS partner',
            ])
            ->asArray()
            ->innerJoin(Policy::tableName(), Policy::tableName() . '.policy_no = ' . self::tableName() . '.policy_no')
            ->innerJoin(Partner::tableName(), Partner::tableName() . '.id = ' . Policy::tableName() . '.partner_id');
		
		if (!Yii::$app->user->isGuest) {
			if (Yii::$app->user->identity->role == User::ROLE_UW) {
				$query->andWhere(['=', self::tableName() . '.created_by', Yii::$app->user->identity->id]);
			}
		}
		
        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
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

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        return $query->count();
    }

    public static function getDueDate($gracePeriod)
    {
        $currentDate = date("Y-m-d");
        return date("Y-m-d", strtotime("+" . $gracePeriod . " day", strtotime($currentDate)));
    }

    public static function generateRegNo($params)
    {
        return str_pad($params['id'], 6, '0', STR_PAD_LEFT) . '/UW-M/AJRI/' . Utils::getRomanNumeral($params['month']) . '/' . date("Y");
    }

    public static function generateInvoiceNo($params)
    {
        $prefix = substr($params['policy_no'], 0, 7) . substr($params['policy_no'], -3, 3);
        return $prefix . '/' . str_pad($params['id'], 3, '0', STR_PAD_LEFT) . '/AJRI-DN/' . Utils::getRomanNumeral($params['month']) . '/' . date("Y");
    }
}
