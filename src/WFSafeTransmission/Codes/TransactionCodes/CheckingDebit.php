<?php
namespace WFSafeTransmission\Codes\TransactionCodes;

use WFSafeTransmission\Interfaces\TransactionCode;
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 10:00 PM
 */
class CheckingDebit implements TransactionCode {
    public function getCode() {
        return '27';
    }

    public function getType() {
        return self::DEBIT;
    }
}