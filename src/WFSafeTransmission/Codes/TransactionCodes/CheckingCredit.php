<?php
namespace WFSafeTransmission\Codes\TransactionCodes;

use WFSafeTransmission\Interfaces\TransactionCode;
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 10:00 PM
 */
class CheckingCredit implements TransactionCode {
    public function getCode() {
        return '22';
    }

    public function getType() {
        return self::CREDIT;
    }
}