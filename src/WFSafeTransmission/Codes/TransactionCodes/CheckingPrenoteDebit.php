<?php
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 10:02 PM
 */

namespace WFSafeTransmission\Codes\TransactionCodes;


class CheckingPrenoteDebit implements TransactionCode {
    public function getCode() {
        return '28';
    }

    public function getType() {
        return self::DEBIT;
    }
}