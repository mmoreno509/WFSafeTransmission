<?php
namespace WFSafeTransmission\Codes\TransactionCodes;

use WFSafeTransmission\Interfaces\TransactionCode;
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 10:03 PM
 */
class SavingsPrenoteCredit implements TransactionCode {
    public function getCode() {
        return '33';
    }

    public function getType() {
        return self::CREDIT;
    }
}