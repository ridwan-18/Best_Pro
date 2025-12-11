<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "batch".
 *
 * @property int $id
 * @property string $batch_no
 * @property string $policy_no
 * @property int $total_member
 * @property int $total_member_accepted
 * @property int $total_member_pending
 * @property float $total_up
 * @property float $total_gross_premium
 * @property float $total_discount_premium
 * @property float $total_extra_premium
 * @property float $total_saving_premium
 * @property float $total_nett_premium
 * @property string $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class BatchByPesertaPegadaian extends \yii\db\ActiveRecord
{
    const STATUS_APPROVED = 'CLAIM';
    const STATUS_PENDING = 'PENDING';
    const STATUS_CLOSED = 'CLOSED';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_member_pegadaian';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['berkas', 'no_polis_nd', 'total_member', 'total_member_accepted', 'total_member_pending', 'total_up', 'total_gross_premium', 'total_discount_premium', 'total_extra_premium', 'total_saving_premium', 'total_nett_premium', 'status'], 'required'],
            [['total_member', 'total_member_accepted', 'total_member_pending', 'created_by', 'updated_by'], 'integer'],
            [['total_up', 'total_gross_premium', 'total_discount_premium', 'total_extra_premium', 'total_saving_premium', 'total_nett_premium'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['berkas', 'no_polis_nd'], 'string', 'max' => 50],
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
            'no_polis_nd' => 'Policy No',
            'batch_no' => 'Batch No',
            'member_no' => 'Member No',
            'personal_no' => 'Personal No',
            'age' => 'Age',
            'branch' => 'Branch',
            'branch_code' => 'Branch Code',
            'account_no' => 'Account No',
            'bank_branch' => 'Bank Branch',
            'term' => 'Term',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'sum_insured' => 'Sum Insured',
            'total_si' => 'Total Si',
            'total_premium' => 'Total Premium',
            'rate_premi' => 'Rate Premi',
            'rate_saving' => 'Rate Saving',
            'gross_premium' => 'Gross Premium',
            'basic_premium' => 'Basic Premium',
            'saving_premium' => 'Saving Premium',
            'percentage_discount' => 'Percentage Discount',
            'discount_premium' => 'Discount Premium',
            'nett_premium' => 'Nett Premium',
            'medical_code' => 'Medical Code',
            'status' => 'Status',
            'member_status' => 'Member Status',
            'reas_status' => 'Reas Status',
            'status_reason' => 'Status Reason',
            'stnc_date' => 'Stnc Date',
            'stnc_status' => 'Stnc Status',
            'stnc_reason' => 'Stnc Reason',
            'acc_status' => 'Acc Status',
            'percentage_extra_premium' => 'Percentage Extra Premium',
            'extra_premium' => 'Extra Premium',
            'em_type' => 'Em Type',
            'percentage_em' => 'Percentage Em',
            'rate_em' => 'Rate Em',
            'em_premium' => 'Em Premium',
            'em_notes' => 'Em Notes',
            'uw_notes' => 'Uw Notes',
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
                self::tableName() . '.no_polis_nd',
				self::tableName() . '.nama_peserta',
                self::tableName() . '.nomor_peserta_nd',
                self::tableName() . '.cif',
				self::tableName() . '.sbg',
				self::tableName() . '.total_up',
				self::tableName() . '.premi_all',
				self::tableName() . '.premi_share',
				self::tableName() . '.berkas',
				self::tableName() . '.status_polis',
				self::tableName() . '.dob',
				self::tableName() . '.star_date',
				self::tableName() . '.end_date',
				self::tableName() . '.accep_date',
				self::tableName() . '.jenis_asuransi',
				self::tableName() . '.kode_unit',
            ])
            ->asArray();

        if (isset($params['no_polis_nd']) && $params['no_polis_nd'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.no_polis_nd', $params['no_polis_nd']]);
        }

        if (isset($params['berkas']) && $params['berkas'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.berkas', $params['berkas']]);
        }

        if (isset($params['status']) && $params['status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.status', $params['status']]);
        }

        if (isset($params['offset']) && $params['offset'] != null) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] != null) {
            $query->limit($params['limit']);
        }

        // $query->groupBy(['policy_no', 'batch_no']);
        // $query->orderBy(['id' => $params['sort']]);

        return $query->all();
    }

    public static function countAll($params = [])
    {
        $query = self::find();

        if (isset($params['no_polis_nd']) && $params['no_polis_nd'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.no_polis_nd', $params['no_polis_nd']]);
        }
        return $query->count();

    }

    public static function statuses($selected = null)
    {
        $data = [
            self::STATUS_OPEN => self::STATUS_OPEN,
            self::STATUS_PENDING => self::STATUS_PENDING,
            self::STATUS_CLOSED => self::STATUS_CLOSED,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }
	
	public static function getAllParticipantByFilter($params = [])
    {
        $query = self::find()
            ->select([
                self::tableName() . '.id',
                self::tableName() . '.no_polis_nd',
				self::tableName() . '.nama_peserta',
                self::tableName() . '.nomor_peserta_nd',
                self::tableName() . '.cif',
				self::tableName() . '.sbg',
				self::tableName() . '.total_up',
				self::tableName() . '.premi_all',
				self::tableName() . '.premi_share',
				self::tableName() . '.berkas',
				self::tableName() . '.status_polis',
				self::tableName() . '.dob',
				self::tableName() . '.star_date',
				self::tableName() . '.end_date',
				self::tableName() . '.accep_date',
				self::tableName() . '.jenis_asuransi',
				self::tableName() . '.kode_unit',
				
            ])
          
			   ->asArray();
		
       if (isset($params['no_polis_nd']) && $params['no_polis_nd'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.no_polis_nd', $params['no_polis_nd']]);
        }
		
        if (isset($params['berkas']) && $params['berkas'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.berkas', $params['berkas']]);
        }

        if (isset($params['nomor_peserta_nd']) && $params['nomor_peserta_nd'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.nomor_peserta_nd', $params['nomor_peserta_nd']]);
        }
		if (isset($params['nama_peserta']) && $params['nama_peserta'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.nama_peserta', $params['nama_peserta']]);
        }
	
		if (isset($params['cif']) && $params['cif'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.cif', $params['cif']]);
        }
		if (isset($params['sbg']) && $params['sbg'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.sbg', $params['sbg']]);
        }
		if (isset($params['status_polis']) && $params['status_polis'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.status_polis', $params['status_polis']]);
        }

        if (isset($params['offset']) && $params['offset'] != null) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] != null) {
            $query->limit($params['limit']);
        }

        return $query->all();
		
    }
	
	public static function getAllProductionParticipant($paramsGetAllProduksi = [])
    {
        $query = self::find()
            ->select([
                self::tableName() . '.id',
                self::tableName() . '.policy_no',
                self::tableName() . '.member_no',
                self::tableName() . '.batch_no',
                Personal::tableName() . '.name',
				Personal::tableName() . '.birth_date',
				Personal::tableName() . '.gender',
				self::tableName() . '.status',
            ])
          
			 ->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' . self::tableName() . '.personal_no')
			   ->asArray();
		
       if (isset($paramsGetAllProduksi['policy_no']) && $paramsGetAllProduksi['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $paramsGetAllProduksi['policy_no']]);
        }
		
        if (isset($paramsGetAllProduksi['batch_no']) && $paramsGetAllProduksi['batch_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.batch_no', $paramsGetAllProduksi['batch_no']]);
        }
		
		if (
            isset($paramsGetAllProduksi['start_date'])
            && $paramsGetAllProduksi['start_date'] != null
            && isset($paramsGetAllProduksi['end_date'])
            && $paramsGetAllProduksi['end_date'] != null
        ) {
            $query->andFilterWhere(['>=', self::tableName() . '.updated_at', $paramsGetAllProduksi['start_date']]);
            $query->andFilterWhere(['<=', self::tableName() . '.updated_at', $paramsGetAllProduksi['end_date']]);
        }

        if (isset($paramsGetAllProduksi['offset']) && $paramsGetAllProduksi['offset'] != null) {
            $query->offset($paramsGetAllProduksi['offset']);
        }

        if (isset($paramsGetAllProduksi['limit']) && $paramsGetAllProduksi['limit'] != null) {
            $query->limit($paramsGetAllProduksi['limit']);
        }
		
         // $query->groupBy(['policy_no', 'batch_no']);
        // $query->orderBy(['id' => $params['sort']]);
		// echo $query;
        return $query->all();
		
		
		
    }
	
		
	public static function countAllDataproduksi($paramsGetAllProduksi = [])
	{
		$query = self::find();

		if (isset($paramsGetAllProduksi['no_polis_nd']) && $paramsGetAllProduksi['no_polis_nd'] != null) {
			$query->andFilterWhere(['=', self::tableName() . '.no_polis_nd', $paramsGetAllProduksi['no_polis_nd']]);
		}

		if (isset($paramsGetAllProduksi['berkas']) && $paramsGetAllProduksi['berkas'] != null) {
			$query->andFilterWhere(['=', self::tableName() . '.berkas', $paramsGetAllProduksi['berkas']]);
		}

		$query->groupBy(['no_polis_nd', 'berkas']);

		return $query->count();
	}		

}


