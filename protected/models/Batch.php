<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\Partner;

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
class Batch extends \yii\db\ActiveRecord
{
    const STATUS_OPEN = 'OPEN';
    const STATUS_PENDING = 'PENDING';
    const STATUS_CLOSED = 'CLOSED';
	
    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'batch';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['batch_no', 'policy_no', 'total_member', 'total_member_accepted', 'total_member_pending', 'total_up', 'total_gross_premium', 'total_discount_premium', 'total_extra_premium', 'total_saving_premium', 'total_nett_premium', 'status'], 'required'],
            [['total_member', 'total_member_accepted', 'total_member_pending', 'created_by', 'updated_by'], 'integer'],
            [['total_up', 'total_gross_premium', 'total_discount_premium', 'total_extra_premium', 'total_saving_premium', 'total_nett_premium'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['batch_no', 'policy_no'], 'string', 'max' => 50],
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
            'total_member' => 'Total Member',
            'total_member_accepted' => 'Total Member Accepted',
            'total_member_pending' => 'Total Member Pending',
            'total_up' => 'Total Up',
            'total_gross_premium' => 'Total Gross Premium',
            'total_discount_premium' => 'Total Discount Premium',
            'total_extra_premium' => 'Total Extra Premium',
            'total_saving_premium' => 'Total Saving Premium',
            'total_nett_premium' => 'Total Nett Premium',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

   
	
	
	
	public static function getAll($params = [])
	{
		$tableBatch   = self::tableName();
$tableUser    = User::tableName();
$tablePolicy  = Policy::tableName();
$tablePartner = Partner::tableName();

$identity = Yii::$app->user->identity;

$query = self::find()
    ->select([
        $tableBatch . '.id',
        $tableBatch . '.policy_no',
        $tableBatch . '.batch_no',
        $tableBatch . '.total_member',
        $tableBatch . '.status',
        $tableBatch . '.created_at',
        $tableBatch . '.created_by',
        $tableBatch . '.files',

        'u.id AS user_id',
        'u.name AS name',

        '(' .
            'SELECT p.name
             FROM ' . $tablePolicy . ' pol
             INNER JOIN ' . $tablePartner . ' p
                 ON pol.partner_id = p.id
             WHERE pol.policy_no = ' . $tableBatch . '.policy_no
             LIMIT 1
            ) AS partner',
    ])
    ->innerJoin(
        $tableUser . ' u',
        'u.id = ' . $tableBatch . '.created_by'
    )
    ->groupBy([
        $tableBatch . '.policy_no',
        $tableBatch . '.batch_no'
    ])
    ->orderBy([
        $tableBatch . '.id' => SORT_DESC
    ])
    ->asArray();


/*
 * ==========================================================
 * ROLE ACCESS
 * ==========================================================
 */

/*
 * SUPER ADMIN
 * ROLE = 1
 *
 * Bisa melihat seluruh batch.
 */
if ($identity->role == User::ROLE_SUPERADMIN) 
{

} 
elseif ($identity->role == User::ROLE_PUSAT) {

    $query->andWhere([
        'u.partner_id' => $identity->partner_id
    ]);
}


/*
 * CABANG / UW
 * ROLE = 2
 *
 * Hanya melihat batch yang dibuat oleh user
 * dengan partner_id yang sama.
 */
 elseif ($identity->role == User::ROLE_UW)
 {

    $query->andWhere([
        'u.partner_id' => $identity->partner_id
    ]);
}

		/*
		 * ==========================================================
		 * CABANG / UW
		 * ROLE = 2
		 * ==========================================================
		 *
		 * Cabang hanya melihat batch yang dibuat oleh dirinya sendiri.
		 *
		 * PENTING:
		 * Jangan menggunakan partner_id di sini.
		 */
		elseif ($identity->role == User::ROLE_UW) {

			$query->andWhere([
				$tableBatch . '.created_by' => $identity->id
			]);
		}


		/*
		 * ==========================================================
		 * ROLE TIDAK DIKENAL
		 * ==========================================================
		 *
		 * Untuk keamanan, jangan tampilkan data.
		 */
		else {

			$query->andWhere('1 = 0');
		}


		/*
		 * ==========================================================
		 * FILTER POLICY
		 * ==========================================================
		 */

		if (
			isset($params['policy_no']) &&
			$params['policy_no'] !== null &&
			$params['policy_no'] !== ''
		) {
			$query->andWhere([
				$tableBatch . '.policy_no' => $params['policy_no']
			]);
		}


		/*
		 * ==========================================================
		 * FILTER BATCH
		 * ==========================================================
		 */

		if (
			isset($params['batch_no']) &&
			$params['batch_no'] !== null &&
			$params['batch_no'] !== ''
		) {
			$query->andWhere([
				$tableBatch . '.batch_no' => $params['batch_no']
			]);
		}


		/*
		 * ==========================================================
		 * FILTER STATUS
		 * ==========================================================
		 */

		if (
			isset($params['status']) &&
			$params['status'] !== null &&
			$params['status'] !== ''
		) {
			$query->andWhere([
				$tableBatch . '.status' => $params['status']
			]);
		}


		/*
		 * ==========================================================
		 * PAGINATION
		 * ==========================================================
		 */

		if (
			isset($params['offset']) &&
			$params['offset'] !== null &&
			$params['offset'] !== ''
		) {
			$query->offset((int) $params['offset']);
		}

		if (
			isset($params['limit']) &&
			$params['limit'] !== null &&
			$params['limit'] !== ''
		) {
			$query->limit((int) $params['limit']);
		}


		/*
		 * ==========================================================
		 * GROUP
		 * ==========================================================
		 */

		$query->groupBy([
			$tableBatch . '.policy_no',
			$tableBatch . '.batch_no'
		]);


		/*
		 * ==========================================================
		 * SORT
		 * ==========================================================
		 */

		$sort = !empty($params['sort'])
			? $params['sort']
			: SORT_DESC;

		$query->orderBy([
			$tableBatch . '.id' => $sort
		]);


		/*
		 * ==========================================================
		 * RETURN
		 * ==========================================================
		 */

		return $query->all();
	}




    public static function countAll($params = [])
    {
        $query = self::find();
		
		if (!Yii::$app->user->isGuest) {
			if (Yii::$app->user->identity->role == User::ROLE_UW) {
				$query->andWhere(['=', self::tableName() . '.created_by', Yii::$app->user->identity->id]);
			}
		}

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['batch_no']) && $params['batch_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.batch_no', $params['batch_no']]);
        }

        $query->groupBy(['policy_no', 'batch_no']);

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

    public static function generateBatchNo($policyNo)
    {
        $batch = Batch::find()
            ->where(['policy_no' => $policyNo])
            ->orderBy(['id' => SORT_DESC])
            ->one();
        return str_pad(($batch != null) ? (int)$batch->batch_no + 1 : 1, 6, '0', STR_PAD_LEFT);
    }
	
	
	public static function getAllProd($params = [])
	{
		$tableBatch   = self::tableName();
		$tableUser    = User::tableName();
		$tablePolicy  = Policy::tableName();
		$tablePartner = Partner::tableName();

		$identity = Yii::$app->user->identity;

		$query = self::find()
			->select([
				$tableBatch . '.id',
				$tableBatch . '.policy_no',
				$tableBatch . '.batch_no',
				$tableBatch . '.total_member',
				$tableBatch . '.status',
				$tableBatch . '.created_at',
				$tableBatch . '.created_by',
				$tableBatch . '.files',
				$tableBatch . '.total_gross_premium',
				$tableBatch . '.total_discount_premium',
				$tableBatch . '.total_extra_premium',
				$tableBatch . '.total_nett_premium',

				/*
				 * Ambil nama partner berdasarkan policy_no
				 */
				'(' .
					'SELECT ' . $tablePartner . '.name
					 FROM ' . $tablePolicy . '
					 INNER JOIN ' . $tablePartner . '
						ON ' . $tablePolicy . '.partner_id = ' .
						   $tablePartner . '.id
					 WHERE ' . $tablePolicy . '.policy_no = ' .
						   $tableBatch . '.policy_no
					 LIMIT 1
				) AS partner',
			])
			->asArray();


		/*
		 * ==========================================================
		 * ROLE ACCESS
		 * ==========================================================
		 */

		/*
		 * ==========================================================
		 * SUPER ADMIN
		 * ROLE = 1
		 * ==========================================================
		 *
		 * Bisa melihat seluruh batch.
		 */
		if ($identity->role == User::ROLE_SUPERADMIN) {

			// Tidak ada filter


		/*
		 * ==========================================================
		 * PUSAT
		 * ROLE = 6
		 * ==========================================================
		 *
		 * Pusat melihat seluruh batch yang dibuat oleh user
		 * dengan partner_id yang sama.
		 */
		} elseif ($identity->role == User::ROLE_PUSAT) {

			$query->innerJoin(
				$tableUser,
				$tableUser . '.id = ' .
				$tableBatch . '.created_by'
			);

			$query->andWhere([
				$tableUser . '.partner_id' => $identity->partner_id
			]);


		/*
		 * ==========================================================
		 * CABANG / UW
		 * ROLE = 2
		 * ==========================================================
		 *
		 * Cabang hanya melihat batch yang dibuat oleh dirinya sendiri.
		 *
		 * PENTING:
		 * Jangan menggunakan partner_id di sini.
		 */
		} elseif ($identity->role == User::ROLE_UW) {

			$query->andWhere([
				$tableBatch . '.created_by' => $identity->id
			]);


		/*
		 * ==========================================================
		 * ROLE TIDAK DIKENAL
		 * ==========================================================
		 *
		 * Untuk keamanan, jangan tampilkan data.
		 */
		} else {

			$query->andWhere('1 = 0');
		}


		/*
		 * ==========================================================
		 * FILTER POLICY
		 * ==========================================================
		 */

		if (
			isset($params['policy_no']) &&
			$params['policy_no'] !== null &&
			$params['policy_no'] !== ''
		) {
			$query->andWhere([
				$tableBatch . '.policy_no' => $params['policy_no']
			]);
		}


		/*
		 * ==========================================================
		 * FILTER BATCH
		 * ==========================================================
		 */

		if (
			isset($params['batch_no']) &&
			$params['batch_no'] !== null &&
			$params['batch_no'] !== ''
		) {
			$query->andWhere([
				$tableBatch . '.batch_no' => $params['batch_no']
			]);
		}


		/*
		 * ==========================================================
		 * FILTER STATUS
		 * ==========================================================
		 */

		if (
			isset($params['status']) &&
			$params['status'] !== null &&
			$params['status'] !== ''
		) {
			$query->andWhere([
				$tableBatch . '.status' => $params['status']
			]);
		}


		/*
		 * ==========================================================
		 * PAGINATION
		 * ==========================================================
		 */

		if (
			isset($params['offset']) &&
			$params['offset'] !== null &&
			$params['offset'] !== ''
		) {
			$query->offset((int) $params['offset']);
		}

		if (
			isset($params['limit']) &&
			$params['limit'] !== null &&
			$params['limit'] !== ''
		) {
			$query->limit((int) $params['limit']);
		}


		/*
		 * ==========================================================
		 * GROUP
		 * ==========================================================
		 */

		$query->groupBy([
			$tableBatch . '.policy_no',
			$tableBatch . '.batch_no'
		]);


		/*
		 * ==========================================================
		 * SORT
		 * ==========================================================
		 */

		$sort = !empty($params['sort'])
			? $params['sort']
			: SORT_DESC;

		$query->orderBy([
			$tableBatch . '.id' => $sort
		]);


		/*
		 * ==========================================================
		 * RETURN
		 * ==========================================================
		 */

		return $query->all();
	}
	
	public static function countAllProd($params = [])
    {
        $query = self::find();
		
		if (!Yii::$app->user->isGuest) {
			if (Yii::$app->user->identity->role == User::ROLE_UW) {
				$query->andWhere(['=', self::tableName() . '.created_by', Yii::$app->user->identity->id]);
			}
		}

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['batch_no']) && $params['batch_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.batch_no', $params['batch_no']]);
        }

        $query->groupBy(['policy_no', 'batch_no']);

        return $query->count();
    }
}
