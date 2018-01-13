<?php
namespace WFSafeTransmission\Codes\TransactionCodes;

use WFSafeTransmission\Interfaces\TransactionCode;
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 10:04 PM
 */
class SavingsDebit implements TransactionCode {
    public function getCode() {
        return '37';
    }

    public function getType() {
        return self::DEBIT;
    }
}