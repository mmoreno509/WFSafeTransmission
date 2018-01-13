<?php
namespace WFSafeTransmission\Codes\TransactionCodes;

use WFSafeTransmission\Interfaces\TransactionCode;
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 10:00 PM
 */
class CheckingPrenoteCredit implements TransactionCode {
    public function getCode() {
        return '23';
    }

    public function getType() {
        return self::CREDIT;
    }
}
