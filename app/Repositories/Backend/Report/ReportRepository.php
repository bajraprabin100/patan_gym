<?php

namespace App\Repositories\Backend\Report;

use App\Models\Admin\BookingInformation;
use App\Models\Admin\CreditStatementMaster;
use App\Models\Admin\ManifestDetail;
use App\Models\Admin\RouteDeliveryDetail;
use App\Models\Admin\RouteDeliveryMaster;
use App\Repositories\Backend\BaseAbstract;
use Illuminate\Container\Container as App;
use DB;

/**
 * Class BranchRepository.
 *
 * @author Kourtier
 */
class ReportRepository extends BaseAbstract implements ReportInterface
{
    public $manifest_detail;

    public function __construct(App $app, ManifestDetail $manifest_detail)
    {
        parent::__construct($app);
        $this->manifest_detail = $manifest_detail;
    }

    function model()
    {
        return 'App\Models\Admin\BranchPara';
    }

    public function manifestQuery($attribute, $login_user)
    {
        return $this->manifest_detail->join('manifest_master', 'manifest_detail.manifest_no', '=', 'manifest_master.manifest_no')
            ->where(function ($query) use ($attribute) {
                if (isset($attribute['bill_no']))
                    $query->where('manifest_detail.bill_no', '=', $attribute['bill_no']);
                if (isset($attribute['shipper_code']))
                    $query->where('manifest_detail.shipper_code', '=', $attribute['shipper_code']);
                if (isset($attribute['shipper_code']))
                    $query->where('manifest_detail.location_to', '=', $attribute['location_to']);
            })
            ->whereBetween('manifest_master.manifest_date', [$attribute['fromDate'], $attribute['toDate']])
            ->where('manifest_master.branch_code', $login_user->branch_code)
            ->get();
    }
    public function bookingList($attribute, $login_user,$group_code)
    {
        return BookingInformation::join('branch_paras as b', 'b.branch_code', '=', 'booking_information.branch_code')
            ->join('customer_para as c', 'c.shipper_code', '=', 'booking_information.shipper_code')
            ->where('b.group_code', '=', $group_code->group_code)
            ->where(function ($query) use ($attribute) {
                if (isset($attribute['shipper_code']))
                    $query->where('booking_information.shipper_code', '=', $attribute['shipper_code']);
                if (isset($attribute['dest_location_code']))
                    $query->where('booking_information.dest_location_code', '=', $attribute['location_code']);
                if (isset($attribute['payment_mode']))
                    $query->where('booking_information.payment_mode', '=', $attribute['payment_mode']);

            })
            ->whereBetween('booking_information.book_date', [$attribute['fromDate'], $attribute['toDate']])
            ->where('booking_information.crossing_no', '=', isset($attribute['crossing_no']) ? $attribute['crossing_no'] : '')
            ->get();
    }

    public function creditStatementBillQuery($attribute, $login_user)
    {
//        $query = "SELECT T1.BILL_NO, T1.BOOK_DATE, GETBSDATE(T1.BOOK_DATE) BOOKDATEBS, T1.CATEGORY, T1.SHIPPER_CODE,
//				T1.SHIPPERS_ADDRESS, T1.CUSTOMER_CODE, GET_CUSTOMER_NAME(T1.CUSTOMER_CODE) CUSTOMER_NAME,
//				T1.AGENT_ID, T1.CONSIGNEE_CODE, T1.CONSIGNEE_NAME, T1.CONSIGNEE_ADDRESS, T1.DEST_LOCATION_CODE,
//				GET_LOCATION_NAME(T1.DEST_LOCATION_CODE) DEST_LOCATION_NAME,
//				T1.ORG_LOCATION_CODE, T1.TELEPHONE_NO, T1.MOBILE_NO, T1.MERCHANDISE_TYPE, T1.MERCHANDISE_CODE,
//				GET_MERCHANDISE_NAME(T1.MERCHANDISE_CODE) MERCHANDISE_NAME, T1.MAILING_MODE, T1.QUANTITY,
//				T1.WEIGHT, T1.DESCRIPTION, T1.PAYMENT_MODE, T1.SERVICE_CHARGE, T1.WEIGHT_CHARGE, T1.OTHER_CHARGE,
//				T1.DISCOUNT_PERCENT, T1.DISCOUNT_AMOUNT, T1.TAXABLE_AMOUNT, T1.VAT, T1.DECLARED_VALUE,
//				T1.DECLARED_VALUE_CURRENCY, T1.VOUCHER_NO, T1.VOUCHER_CODE, T1.BANK_CODE, T1.CHEQUE_NO,
//				T1.PREPARED_BY, T1.PREPARED_ON, T1.BRANCH_CODE, T1.MANIFEST_NO, T1.VAT_APPLICABLE, T1.CROSSING_NO,
//				(NVL(T1.SERVICE_CHARGE,0) + NVL(T1.WEIGHT_CHARGE,0) + NVL(T1.OTHER_CHARGE,0)) AMT, T1.AMOUNT,
//				T1.TOTAL_AMOUNT, T2.STATEMENT_NO
//			FROM BKIN T1, CRST T2, CRSTD T3
//			WHERE T3.STATEMENT_NO = T2.STATEMENT_NO
//				AND T3.BILL_NO = T1.BILL_NO
//				AND NVL(T2.STATEMENT_NO, 'AA') = NVL(NVL('$statementId', T2.STATEMENT_NO), 'AA')
//				AND NVL(T1.CUSTOMER_CODE, 'BB') = NVL(NVL('$pCustomer', T1.CUSTOMER_CODE), 'BB')
//				AND NVL(T1.SHIPPER_CODE, 'CC') = NVL(NVL('$pShipper', T1.SHIPPER_CODE), 'CC')
//				AND T2.STATEMENT_DATE BETWEEN NVL('$pDateFrom', T2.STATEMENT_DATE) AND NVL('$pDateTo', T2.STATEMENT_DATE)
//				AND T1.BRANCH_CODE =  NVL('".$_SESSION['branchCode']."', T1.BRANCH_CODE)
//				AND T1.FISCAL_YEAR =  '".$_SESSION['fiscalYear']."'
//			ORDER BY T1.BOOK_DATE, (TO_NUMBER(SUBSTR(BILL_NO,5,20)))";
        return BookingInformation::select('*', DB::raw('GET_LOCATION_NAME(booking_information.dest_location_code) dest_location_name'))
            ->join('credit_statement_detail as c1', 'c1.bill_no', '=', 'booking_information.bill_no')
            ->join('credit_statement_master as c2', 'c2.statement_no', 'c1.statement_no')
            ->leftjoin('customer_para as cus', 'cus.shipper_code', '=', 'booking_information.shipper_code')
            ->where(function ($query) use ($attribute) {
                if (isset($attribute['statement_no']))
                    $query->where('c2.statement_no', '=', $attribute['statement_no']);
                if (isset($attribute['shipper_code']))
                    $query->where('booking_information.shipper_code', '=', $attribute['shipper_code']);
                if (isset($attribute['fromDate']) && isset($attribute['toDate']))
                    $query->whereBetween('c2.statement_date', [$attribute['fromDate'], $attribute['toDate']]);
            })
            ->where('booking_information.branch_code', $login_user->branch_code)
            ->get();
    }

    public function creditStatementListQuery($attribute, $login_user)
    {
//        $query = "SELECT T1.STATEMENT_NO, CALENDAR.ADTOBS(T1.DATE_FROM) DATE_FROM,
//				CALENDAR.ADTOBS(T1.DATE_TO) DATE_TO, T1.CUSTOMER_CODE, T2.FISCAL_YEAR,
//				SUM(T3.TOTAL_AMOUNT) TOTAL_AMOUNT, GET_CUSTOMER_NAME(T1.CUSTOMER_CODE) CUSTOMER_NAME
//			FROM CRST T1, CRSTD T2, BKIN T3
//			WHERE T2.STATEMENT_NO = T1.STATEMENT_NO
//				AND T2.BILL_NO = T3.BILL_NO
//				AND T1.STATEMENT_NO = T3.STATEMENT_NO
//				AND NVL(T1.CUSTOMER_CODE, 'AA') = NVL(NVL('$pCustomer', T1.CUSTOMER_CODE),'AA')
//				AND NVL(T1.STATEMENT_NO, 'BB') = NVL(NVL('$pStatementNo', T1.STATEMENT_NO),'BB')
//				AND T1.DATE_FROM >= '$pDateFrom'
//				AND T1.DATE_TO <= '$pDateTo'
//				AND T1.BRANCH_CODE = '" . $_SESSION['branchCode'] . "'
//				AND T1.FISCAL_YEAR = '" . $_SESSION['fiscalYear'] . "'
//			GROUP BY T1.STATEMENT_NO, T1.DATE_FROM, T1.DATE_TO, T1.CUSTOMER_CODE, T2.FISCAL_YEAR";

        return BookingInformation::join('credit_statement_master as t1', 't1.statement_no', '=', 'booking_information.statement_no')
            ->join('credit_statement_detail as t2', 't2.statement_no', '=', 'booking_information.statement_no')
            ->join('customer_para as cus', 'cus.shipper_code', '=', 't1.shipper_code')
            ->where(function ($query) use ($attribute) {
                if (isset($attribute['statement_no']))
                    $query->where('t1.statement_no', '=', $attribute['statement_no']);
                if (isset($attribute['shipper_code']))
                    $query->where('booking_information.shipper_code', '=', $attribute['shipper_code']);
                if (isset($attribute['fromDate']) && isset($attribute['toDate']))
                    $query->whereBetween('t1.statement_date', [$attribute['fromDate'], $attribute['toDate']]);
            })
            ->where('booking_information.branch_code', $login_user->branch_code)
            ->groupBy('t1.statement_no', 't1.date_from', 't1.date_to', 'cus.customer_name')
            ->selectRaw('sum(booking_information.total_amount) as total_amount,t1.statement_no,t1.date_from,t1.date_to,cus.customer_name')
            ->get();

    }

    public function statementwiseQuery($attribute, $login_user)
    {
//        $query = "SELECT STATEMENT_NO, STATEMENT_DATE, DATE_FROM, DATE_TO, CUSTOMER_CODE,
//				GET_CUSTOMER_NAME(CUSTOMER_CODE) CUSTOMER_NAME, BRANCH_CODE, 11, FISCAL_YEAR
//			FROM CRST
//			WHERE DATE_FROM BETWEEN NVL('$pDateFrom', DATE_FROM) AND NVL('$pDateTo', DATE_FROM)
//				AND FISCAL_YEAR = '" . $_SESSION['fiscalYear'] . "'
//				AND BRANCH_CODE = NVL('" . $_SESSION['branchCode'] . "', BRANCH_CODE)
//			ORDER BY TO_NUMBER(SUBSTR(STATEMENT_NO,9,14))";
//        $resQuery = $conn->selectdatafromtable($c, $query);
        return CreditStatementMaster::whereBetween('credit_statement_master.date_from', [$attribute['fromDate'], $attribute['toDate']])
            ->join('credit_statement_detail as c1', 'c1.statement_no', '=', 'credit_statement_master.statement_no')
            ->join('booking_information as b', 'b.bill_no', '=', 'c1.bill_no')
            ->join('customer_para as cus', 'cus.shipper_code', '=', 'credit_statement_master.shipper_code')
            ->where('credit_statement_master.branch_code', '=', $login_user->branch_code)
            ->groupBy('credit_statement_master.statement_no', 'credit_statement_master.date_from', 'credit_statement_master.date_to', 'cus.customer_name')
            ->selectRaw('sum(b.total_amount) as total_amount,sum(b.taxable_amount) as taxable_amount,credit_statement_master.statement_no,credit_statement_master.date_from,cus.customer_name')
            ->get();

    }

    public function statementDeliveryQuery($attribute, $login_user)
    {
//        $query = "SELECT CRST.STATEMENT_NO, GET_CUSTOMER_NAME(CUSTOMER_CODE) CUSTOMER_NAME, BILL_DATE,
//				CALENDAR.ADTOBS(BILL_DATE) BILL_DATEBS, BILL_NO, REMARKS, CRST.FISCAL_YEAR
//			FROM CRST, CRSTD
//			WHERE CRST.STATEMENT_NO = CRSTD.STATEMENT_NO
//				AND CRST.STATEMENT_NO =  '$statementId'
//				AND CRST.FISCAL_YEAR = '" . $_SESSION['fiscalYear'] . "'
//				AND BRANCH_CODE = NVL('" . $_SESSION['branchCode'] . "', BRANCH_CODE)
//			ORDER BY BILL_DATE, BILL_NO";
        return CreditStatementMaster::join('credit_statement_detail as c_d', 'c_d.statement_no', '=', 'credit_statement_master.statement_no')
            ->join('booking_information as b', 'b.bill_no', '=', 'c_d.bill_no')
            ->join('route_delivery_detail as r', 'r.bill_no', '=', 'b.bill_no')
            ->join('route_delivery_master as rm', 'rm.id', '=', 'r.master_id')
            ->where('credit_statement_master.statement_no', '=', $attribute['statement_no'])
            ->where('credit_statement_master.branch_code', $login_user->branch_code)
            ->get();

    }

    public function internationalBookingQuery($attribute, $login_user)
    {
//        $query = "SELECT BILL_NO, MERCHANDISE_TYPE, CONSIGNEE_NAME, CROSSING_NO, BOOK_DATE, GETBSDATE(BOOK_DATE) BOOKDATEBS,              	MERCHANDISE_CODE, SHIPPER_CODE, GET_SHIPPER_NAME(SHIPPER_CODE) SHIPPER_NAME, CONSIGNEE_ADDRESS,
//				TAXABLE_AMOUNT, VAT, TOTAL_AMOUNT, DEST_LOCATION_CODE, WEIGHT
//			FROM BKIN
//			WHERE BOOK_DATE BETWEEN '$pDateFrom' AND '$pDateTo'
//				AND DEST_LOCATION_CODE IN (SELECT LOCATION_CODE FROM LH
//						WHERE MASTER_LOCATION_CODE = '$pLocCode')
//				AND FISCAL_YEAR = '".$_SESSION['fiscalYear']."'
//				AND BRANCH_CODE = NVL('".$_SESSION['branchCode']."', BRANCH_CODE)
//			UNION
//			SELECT BILL_NO, MERCHANDISE_TYPE, CONSIGNEE_NAME, CROSSING_NO, BOOK_DATE, GETBSDATE(BOOK_DATE) BOOKDATEBS,
//				MERCHANDISE_CODE, SHIPPER_CODE, GET_SHIPPER_NAME(SHIPPER_CODE) SHIPPER_NAME, CONSIGNEE_ADDRESS,
//				TAXABLE_AMOUNT, VAT, TOTAL_AMOUNT, DEST_LOCATION_CODE, WEIGHT
//			FROM BKIN
//			WHERE BOOK_DATE BETWEEN '$pDateFrom' AND '$pDateTo'
//				AND DEST_LOCATION_CODE = '$pLocCode'
//				AND FISCAL_YEAR = '".$_SESSION['fiscalYear']."'
//				AND BRANCH_CODE = NVL('".$_SESSION['branchCode']."', BRANCH_CODE)";
        $query1 = BookingInformation::whereBetween('booking_information.book_date', [$attribute['fromDate'], $attribute['toDate']])
            ->join('location_hierarchy as l', 'l.location_code', '=', 'booking_information.dest_location_code')
            ->where('l.master_location_code', '=', $attribute['location_code'])
            ->where('booking_information.branch_code', '=', $login_user->branch_code)
            ->get();
        $query2 = BookingInformation::select('booking_information.*')->whereBetween('booking_information.book_date', [$attribute['fromDate'], $attribute['toDate']])
            ->where('booking_information.dest_location_code', '=', $attribute['location_code'])
            ->where('branch_code', '=', $login_user->branch_code)
            ->get();
        return $query2->merge($query1);
    }

    public function documentDeliveryQuery($attribute, $login_user)
    {
        $route_delivery = RouteDeliveryMaster::whereBetween('delivery_date', [$attribute['fromDate'], $attribute['toDate']])
            ->where('branch_code', '=', $login_user->branch_code)
            ->where('route', '!=', '')
            ->get();
        if ($route_delivery->isNotEmpty()) {
            foreach ($route_delivery as $r) {
                $r->fordelivery = RouteDeliveryDetail::where('master_id', '=', $r->id)->count();
                $r->delivered = RouteDeliveryDetail::where('master_id', '=', $r->id)->where('received_by', '!=', '')->count();
                $r->rtobill = RouteDeliveryDetail::where('master_id', '=', $r->id)->where('rto', '=', 'Y')->count();
                $r->undelivered = number_format(($r->fordelivery - $r->delivered - $r->rtobill), 2);
                $r->delivered_per = number_format(($r->delivered / $r->fordelivery * 100), 2);
                $r->rto_per = number_format(($r->rtobill / $r->fordelivery * 100), 2);
                $r->undelivered_per = number_format(($r->undelivered / $r->fordelivery * 100), 2);
            }
        }
        return $route_delivery;
    }

    public function podReport($attribute, $login_user)
    {
        return BookingInformation::whereRaw('booking_information.bill_no BETWEEN ' . $attribute['billFrom'] . ' AND ' . $attribute['billTo'] . '')
            ->join('customer_para', 'customer_para.shipper_code', '=', 'booking_information.shipper_code')
            ->join('branch_paras', 'branch_paras.branch_code', '=', 'customer_para.branch_code')
            ->orderBy(DB::raw('LENGTH(booking_information.bill_no), booking_information.bill_no'))
            ->get();
    }

    public function cnReport($attribute, $login_user)
    {
        return BookingInformation::select('booking_information.*', 'customer_para.*')
            ->whereRaw('booking_information.bill_no BETWEEN ' . $attribute->billFrom . ' AND ' . $attribute->billTo . '')
            ->join('customer_para', 'customer_para.shipper_code', '=', 'booking_information.shipper_code')
            ->where('booking_information.branch_code', $this->admin_data['login_user']->branch_code)
            ->orderBy('booking_information.bill_no', 'asc')
            ->get();
    }


}