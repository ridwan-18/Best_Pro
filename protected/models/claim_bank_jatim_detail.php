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
class claim_bank_jatim_detail extends \yii\db\ActiveRecord
{
    const STATUS_INFORCE = 'Inforce';

    const EM_MANUAL = 1;
    const EM_FROM_PRODUCT = 2;

    const PAGE_SIZE = 20;
	
	const PICTURE_PATH = '/images/post_images/';
    const PICTURE_MAX_WIDTH = 300;
    const PICTURE_MAX_HEIGHT = 300;
	
	public $file_upload;
	public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_claim_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_dokumen', 'files',], 'string', 'max' => 500],
			       [['id_loan'], 'integer'],
			 [['tgl_upload'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_loan' => 'ID',
            'kode_dokumen' => 'kode_dokumen',
            'files' => 'files',
            'tgl_upload' => 'tgl_upload',
      
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
	
	
	public function upload()
    {
        $filename = $this->id_loan;
        $extension = $this->file_upload->extension;

        $path = \Yii::getAlias('@webroot') . self::PICTURE_PATH . $filename . '.' . $extension;
        $this->file_upload->saveAs($path);


        $this->file_upload = null;
        $this->file_medis = $filename . '.' . $extension;
        return true;
		
    }
	
	public function uploadclaim()
    {
        $filename = $this->id;
		$filenamepersonal = $this->personal_no;
        $extension = $this->file_upload->extension;

        $path = \Yii::getAlias('@webroot') . self::PICTURE_PATH . $filename . '-' . $filenamepersonal . '.' . $extension;
        $this->file_upload->saveAs($path);


        $this->file_upload = null;
        $this->file_medis = $filename . '-' . $filenamepersonal . '.' . $extension;
        return true;
		
    }
	
}
