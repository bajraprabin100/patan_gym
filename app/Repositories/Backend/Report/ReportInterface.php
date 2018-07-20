<?php

namespace App\Repositories\Backend\Report;

use App\Repositories\Backend\BaseInterface;


/**
 * Interface PageInterface.
 *
 * @author BanquadeSquad
 */
interface ReportInterface extends BaseInterface
{

    public function manifestQuery($attribute,$login_user);
    public function creditStatementBillQuery($attribute,$login_user);
    public function creditStatementListQuery($attribute,$login_user);
    public function statementwiseQuery($attribute,$login_user);
    public function statementDeliveryQuery($attribute,$login_user);
    public function internationalBookingQuery($attribute, $login_user);
    public function documentDeliveryQuery($attribute, $login_user);

    public function podReport($attribute, $login_user);
    public function cnReport($attribute, $login_user);
    public function bookingList($attribute,$login_user,$group_code);
}