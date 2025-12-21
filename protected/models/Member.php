<?php

namespace app\models;

use DateTime;
use Yii;
use yii\web\UploadedFile;
use yii\image\drivers\Image;

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
class Member extends \yii\db\ActiveRecord
{
    const STATUS_INFORCE = 'Inforce';
    const STATUS_LAPSED = 'Lapsed';
    const STATUS_CLAIM = 'Claim';
    const STATUS_SURRENDER = 'Surrender';
    const STATUS_MATURITY = 'Maturity';
    const STATUS_CHANGE = 'Change';
    const STATUS_CANCEL = 'Cancel';
	const STATUS_PROGRES = 'Progres';

    const MEMBER_STATUS_INFORCE = 'Inforce';
    const MEMBER_STATUS_PENDING = 'Pending';
    const MEMBER_STATUS_DECLINED = 'Declined';
	
	const MEMBER_STATUS_DIPROSES = 'DIPROSES';
    const MEMBER_STATUS_DITOLAK = 'DITOLAK';
    const MEMBER_STATUS_DISETUJUI = 'DISETUJUI';
	

    const REAS_STATUS_TREATY = 'Treaty';
    const REAS_STATUS_OUT = 'Out of Treaty';
    const REAS_STATUS_FACULTATIVE = 'Facultative';

    const EM_MANUAL = 1;
    const EM_FROM_PRODUCT = 2;

    const PAGE_SIZE = 20;
	
	const PICTURE_PATH = '/images/penghantar_medis/';
    const PICTURE_MAX_WIDTH = 300;
    const PICTURE_MAX_HEIGHT = 300;
	
	public $file_upload;
	public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'member';
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
            [['sum_insured', 'total_si', 'total_premium', 'rate_premi', 'rate_saving', 'gross_premium', 'basic_premium', 'saving_premium', 
			'percentage_discount', 'discount_premium', 'nett_premium', 'percentage_extra_premium', 'extra_premium', 'percentage_em', 'rate_em', 'em_premium'], 'number'],
            [['policy_no', 'batch_no', 'medical_code'], 'string', 'max' => 50],
            [['member_no'], 'string', 'max' => 100],
            [['personal_no', 'branch', 'branch_code', 'account_no', 'bank_branch', 'status_reason', 'stnc_status', 'stnc_reason', 'acc_status', 'em_notes', 'uw_notes'],
			'string', 'max' => 255],
            [['status', 'member_status', 'reas_status'], 'string', 'max' => 20],
			
			 [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg','pdf'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'policy_no' => 'Policy No',
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
			'id_loan' => 'id_loan',
			 'files_medis' => 'files_medis',
            'file_upload' => 'file_upload',
			'link_e_polis' => 'link_e_polis',
			'status_em' => 'status_em,'
        ];
    }

    public static function getAll($params = [])
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
				self::tableName() . '.status_em',
				self::tableName() . '.id_loan',
                '(SELECT ' . Personal::tableName() . '.name' .  ' FROM ' . Personal::tableName() . ' WHERE ' . Personal::tableName() . '.personal_no = ' . self::tableName() . '.personal_no GROUP BY ' . self::tableName() . '.personal_no) AS name',
                '(SELECT ' . Personal::tableName() . '.birth_date' .  ' FROM ' . Personal::tableName() . ' WHERE ' . Personal::tableName() . '.personal_no = ' . self::tableName() . '.personal_no GROUP BY ' . self::tableName() . '.personal_no) AS birth_date',
                '(SELECT ' . Personal::tableName() . '.gender' .  ' FROM ' . Personal::tableName() . ' WHERE ' . Personal::tableName() . '.personal_no = ' . self::tableName() . '.personal_no GROUP BY ' . self::tableName() . '.personal_no) AS gender',
            ])
            ->asArray();

        if (isset($params['member_id']) && $params['member_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.id', $params['member_id']]);
        }

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['batch_no']) && $params['batch_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.batch_no', $params['batch_no']]);
        }

        if (
            isset($params['start_date'])
            && $params['start_date'] != null
            && isset($params['end_date'])
            && $params['end_date'] != null
        ) {
            $query->andFilterWhere(['>=', self::tableName() . '.start_date', $params['start_date']]);
            $query->andFilterWhere(['<=', self::tableName() . '.end_date', $params['end_date']]);
        }

        if (isset($params['status']) && $params['status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.status', $params['status']]);
        }

        if (isset($params['member_status']) && $params['member_status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.member_status', $params['member_status']]);
        }

        if (isset($params['reas_status']) && $params['reas_status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.reas_status', $params['reas_status']]);
        }

        if (isset($params['is_accumulated']) && $params['is_accumulated'] == 1) {
            $query->andFilterWhere(['like', self::tableName() . '.acc_status', 'Accumulated']);
        }

        if (isset($params['offset']) && $params['offset'] != null) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] != null) {
            $query->limit($params['limit']);
        }

        $query->groupBy([self::tableName() . '.id', self::tableName() . '.personal_no']);
        $query->orderBy([self::tableName() . '.id' => $params['sort']]);

        return $query->all();
    }

    public static function countAll($params = [])
    {
        $query = self::find();

        if (isset($params['member_id']) && $params['member_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.id', $params['member_id']]);
        }

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['batch_no']) && $params['batch_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.batch_no', $params['batch_no']]);
        }

        if (
            isset($params['start_date'])
            && $params['start_date'] != null
            && isset($params['end_date'])
            && $params['end_date'] != null
        ) {
            $query->andFilterWhere(['>=', self::tableName() . '.start_date', $params['start_date']]);
            $query->andFilterWhere(['<=', self::tableName() . '.end_date', $params['end_date']]);
        }

        if (isset($params['status']) && $params['status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.status', $params['status']]);
        }

        if (isset($params['member_status']) && $params['member_status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.member_status', $params['member_status']]);
        }

        if (isset($params['reas_status']) && $params['reas_status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.reas_status', $params['reas_status']]);
        }

        $query->groupBy([self::tableName() . '.id', self::tableName() . '.personal_no']);

        return $query->count();
    }

    public static function getAccumulation($params = [])
    {
        $query = self::find()
            ->select([
                self::tableName() . '.id',
                self::tableName() . '.policy_no',
                self::tableName() . '.batch_no',
                self::tableName() . '.member_no',
                Personal::tableName() . '.name',
                Personal::tableName() . '.birth_date',
                self::tableName() . '.age',
                self::tableName() . '.start_date',
                self::tableName() . '.end_date',
                self::tableName() . '.term',
                self::tableName() . '.sum_insured',
                self::tableName() . '.gross_premium',
                self::tableName() . '.nett_premium',
                self::tableName() . '.em_premium',
            ])
            ->asArray()
            ->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' . self::tableName() . '.personal_no');

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['name']) && $params['name'] != null) {
            $query->andFilterWhere(['=', Personal::tableName() . '.name', $params['name']]);
        }

        if (isset($params['birth_date']) && $params['birth_date'] != null) {
            $query->andFilterWhere(['=', Personal::tableName() . '.birth_date', $params['birth_date']]);
        }

        if (isset($params['offset']) && $params['offset'] != null) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] != null) {
            $query->limit($params['limit']);
        }

        $query->groupBy([self::tableName() . '.id', self::tableName() . '.personal_no']);
        $query->orderBy([self::tableName() . '.id' => $params['sort']]);

        return $query->all();
    }

    public static function statuses($selected = null)
    {
        $data = [
            self::STATUS_INFORCE => self::STATUS_INFORCE,
            self::STATUS_LAPSED => self::STATUS_LAPSED,
            self::STATUS_CLAIM => self::STATUS_CLAIM,
            self::STATUS_SURRENDER => self::STATUS_SURRENDER,
            self::STATUS_MATURITY => self::STATUS_MATURITY,
            self::STATUS_CHANGE => self::STATUS_CHANGE,
            self::STATUS_CANCEL => self::STATUS_CANCEL,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function memberStatuses($selected = null)
    {
        $data = [
            self::MEMBER_STATUS_INFORCE => self::MEMBER_STATUS_INFORCE,
            self::MEMBER_STATUS_PENDING => self::MEMBER_STATUS_PENDING,
            self::MEMBER_STATUS_DECLINED => self::MEMBER_STATUS_DECLINED,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function reasStatuses($selected = null)
    {
        $data = [
            self::REAS_STATUS_TREATY => self::REAS_STATUS_TREATY,
            self::REAS_STATUS_OUT => self::REAS_STATUS_OUT,
            self::REAS_STATUS_FACULTATIVE => self::REAS_STATUS_FACULTATIVE,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function totalShows($selected = null)
    {
        $data = [
            20 => 20,
            50 => 50,
            100 => 100,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function accumulateOptions($selected = null)
    {
        $data = [
            0 => 'No',
            1 => 'Yes',
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function getTerm($rateType, $startDate, $endDate)
    {
        $date1 = date_create($startDate);
        $date2 = date_create($endDate);
        $diff = date_diff($date1, $date2);

        $term = ($diff->y * 12) + $diff->m;
        if ($rateType == RateType::RATE_ROUND_UP && $diff->d > 0) {
            $term = $term + 1;
        }

        return $term;
    }
	
	public static function getTermJatim($rateType, $startDate, $endDate)
    {
        $date1 = date_create($startDate);
        $date2 = date_create($endDate);
        $diff = date_diff($date1, $date2);

        $term =($diff->y * 12) + $diff->m;
      

        return $term;
    }

    public static function getAge($ageCalculate, $birthDate, $startDate)
    {
        $date1 = date_create($startDate);
        $date2 = date_create($birthDate);
        $diff = date_diff($date1, $date2);

        $age = $diff->y;
        if ($ageCalculate == AgeCalculate::NEAREST_BIRTHDAY && (($diff->m == 6 && $diff->d > 0) || $diff->m > 6)) {
            $age = $diff->y + 1;
        }

        return $age;
    }

    public static function getStnc($startDate, $retroactive)
    {
        $currentDate = date("Y-m-d");
        $validDate = date("Y-m-d", strtotime("+" . $retroactive . " day", strtotime($startDate)));
        if ($currentDate > $validDate) {
            return $currentDate;
        }
        return null;
    }

    public static function generateMemberNo($id, $policyNo)
    {
        $prefix = substr($policyNo, 0, 3);
        $suffix = substr($policyNo, -3, 3);
        return $prefix . '-' . date("ym") . str_pad($id, 7, '0', STR_PAD_LEFT) . '-' . $suffix;
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
	
	public function callAPIPostMemberCbc()
    {
        // $url = 'http://45.64.1.151/api/akseptasi/bankjatim/post-status-pertanggungan-cbc';
		$url = 'https://ws2u.winserver.aapialang.co.id/prod/akseptasi/bankjatim/post-status-pertanggungan-cbc';
		// https://ws2u.winserver.aapialang.co.id/prod/akseptasi/bankjatim/post-status-pertanggungan-cbc
		// $url = 'https://ws2u.winserver.aapialang.co.id/dev/pembatalan/bankjatim/post-pembayaran';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode('bjtm:bjtm!@##@!')
        ];
		
		$fileName = $this->id_loan . '.pdf';
		
		$data = json_encode([
            'ID_Loan' => $this->id_loan,
            'Extra_Premi' => $this->em_premium,
            'Status' => $this->uw_notes,
			'Link_E_Polis' => 'https://h2h-ajri.reli.id/images/e_policy/' .$fileName,
			'Nomor_Polis' => $this->policy_no,
			'Keterangan' => $this->em_notes,
			'Asuransi' => '1',
			
        ]);
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));

        curl_close($ch);

        return json_decode($body, true);
    }
	
	public function upload()
    {
        $filename = $this->id_loan;
        $extension = $this->file_upload->extension;

        $path = \Yii::getAlias('@webroot') . self::PICTURE_PATH . $filename . "." . $extension;
        $this->file_upload->saveAs($path);


        $this->file_upload = null;
        $this->file_medis = $filename . "." . $extension;
        return true;
		
    }
	
	 public static function memberCBC($selected = null)
    {
        $data = [
            self::MEMBER_STATUS_DIPROSES => self::MEMBER_STATUS_DIPROSES,
            self::MEMBER_STATUS_DITOLAK => self::MEMBER_STATUS_DITOLAK,
            self::MEMBER_STATUS_DISETUJUI => self::MEMBER_STATUS_DISETUJUI,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }
	
	
	public static function round_up ( $value, $precision ) { 
    $pow = pow ( 10, $precision ); 
    return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow; 
	} 
	
	 public static function getTermRefund($startDate, $endDate)
    {
        $date1 = date_create($startDate);
        $date2 = date_create($endDate);
        $diff = date_diff($date1, $date2);

        $term = ($diff->y * 12) + $diff->m;
        if ($diff->d > 0) {
            $term = $term + 1;
        }

        return $term;
    }
	
	 public function callAPIPostStatusrefund($description)
    {
        // $url = 'http://45.64.1.151/api/klaim/bankjatim/post-status';
		// $url = 'http://winserver.aapialang.co.id/api/klaim/bankjatim/post-status';
		
		$url= 'https://ws2u.winserver.aapialang.co.id/prod/pembatalan/bankjatim/post-status-pembatalan';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode('bjtm:bjtm!@##@!')
        ];
        $data = json_encode([
            'ID_Loan' => $this->id_loan,
			'No_Polis' => $this->policy_no,
            'Status' => $this->status_refund,
            // 'Keterangan' => $description,
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));

        curl_close($ch);

        return json_decode($body, true);
    }
	
	public function callAPIPostRefund()
    {
        // $url = 'http://45.64.1.151/api/klaim/bankjatim/post-pembayaran';
		// $url = 'http://winserver.aapialang.co.id/api/klaim/bankjatim/post-pembayaran';
		$url = 'https://ws2u.winserver.aapialang.co.id/prod/pembatalan/bankjatim/post-pembayaran';
        $headers = [
            'Content-Type: multipart/form-data',
            'Authorization: Basic ' . base64_encode('bjtm:bjtm!@##@!')
        ];
        $data = [
            'ID_Loan' => $this->id_loan,
			'No_Polis' => $this->policy_no,
            'Nilai_Dibayarkan' => $this->nilai_refund_dibayar,
            'Keterangan' => $this->keterangan_refund,
			'Files' => new \CurlFile($_FILES['bukti_bayar_refund']['tmp_name'], 'image/png', $_FILES['bukti_bayar_refund']['name'])
		];
		
		// return $data;
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));

        curl_close($ch);

        return json_decode($body, true);
    }

    public function uploadRefund()
    {
		$filename = $this->id_loan;
        $extension = $this->file_upload->extension;

        $path = \Yii::getAlias('@webroot') . self::PICTURE_PATH . $filename . "." . $extension;
        $this->file_upload->saveAs($path);


        $this->file_upload = null;
        $this->bukti_bayar_refund = $filename . "." . $extension;
        return true;
		
		
    }

}
