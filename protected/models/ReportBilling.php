<?php

namespace app\models;

use Yii;

class ReportBilling extends \yii\db\ActiveRecord
{
    public static function getAll($wheres = [], $limit)
    {
        $subQueryPartner = "(
            SELECT 
            " . Partner::tableName() . ".name 
            FROM 
            " . Policy::tableName() . " 
            INNER JOIN " . Partner::tableName() . " ON " . Policy::tableName() . ".partner_id = " . Partner::tableName() . ".id 
            WHERE " . Policy::tableName() . ".policy_no = " . Member::tableName() . ".policy_no 
            GROUP BY " . Policy::tableName() . ".policy_no
        ) AS partner";

        $subQueryProduct = "(
            SELECT
            (
                SELECT
                (
                    SELECT
                    " . Product::tableName() . ".name
                    FROM
                    " . QuotationProduct::tableName() . "
                    INNER JOIN " . Product::tableName() . " ON " . Product::tableName() . ".id = " . QuotationProduct::tableName() . ".product_id
                    WHERE
                    " . QuotationProduct::tableName() . ".quotation_id = " . Quotation::tableName() . ".id
                )
                FROM
                " . Quotation::tableName() . "
                WHERE
                " . Quotation::tableName() . ".id = " . Policy::tableName() . ".quotation_id
            )
            FROM 
            " . Policy::tableName() . " 
            WHERE 
            " . Policy::tableName() . ".policy_no = " . Member::tableName() . ".policy_no
        ) AS product";

        $query = Member::find()
            ->asArray()
            ->select([
                Member::tableName() . '.policy_no',
                Member::tableName() . '.batch_no',
                Member::tableName() . '.member_no',
                Member::tableName() . '.age',
                Member::tableName() . '.term',
                Member::tableName() . '.start_date',
                Member::tableName() . '.end_date',
                Member::tableName() . '.sum_insured',
                Member::tableName() . '.gross_premium',
                Member::tableName() . '.extra_premium',
                Member::tableName() . '.em_premium',
                Member::tableName() . '.nett_premium',
                Member::tableName() . '.medical_code',
                Member::tableName() . '.status',
                Member::tableName() . '.member_status',
                Member::tableName() . '.stnc_date',
                Billing::tableName() . '.invoice_no',
                Billing::tableName() . '.reg_no',
                Billing::tableName() . '.accept_date',
                Personal::tableName() . '.name',
                Personal::tableName() . '.birth_date',
                $subQueryPartner,
                $subQueryProduct,
            ])
            ->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' . Member::tableName() . '.personal_no')
            ->innerJoin(Billing::tableName(), Billing::tableName() . '.policy_no = ' . Member::tableName() . '.policy_no AND ' . Billing::tableName() . '.batch_no = ' . Member::tableName() . '.batch_no');
        $query->where($wheres);
        $query->groupBy([Member::tableName() . '.id']);
        $query->limit($limit);

        return $query->all();
    }

    public static function countAll($wheres = [])
    {
        return Member::find()
            ->innerJoin(Billing::tableName(), Billing::tableName() . '.policy_no = ' . Member::tableName() . '.policy_no AND ' . Billing::tableName() . '.batch_no = ' . Member::tableName() . '.batch_no')
            ->where($wheres)
            ->count();
    }
}
