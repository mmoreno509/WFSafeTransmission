<?php
namespace WFSafeTransmission\Codes\TransactionCodes;

use WFSafeTransmission\Interfaces\TransactionCode;
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 10:03 PM
 */
class SavingsCredit implements TransactionCode {
    public function getCode() {
        return '32';
    }

    public function getType() {
        return self::CREDIT;
    }
}