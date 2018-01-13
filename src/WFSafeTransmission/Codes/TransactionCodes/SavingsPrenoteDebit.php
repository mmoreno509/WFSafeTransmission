<?php
namespace WFSafeTransmission\Codes\TransactionCodes;

use WFSafeTransmission\Interfaces\TransactionCode;
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 10:05 PM
 */
class SavingsPrenoteDebit implements TransactionCode {
    public function getCode() {
        return '38';
    }

    public function getType() {
        return self::DEBIT;
    }
}
