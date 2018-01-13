<?php
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 9:45 PM
 */

namespace WFSafeTransmission\Interfaces;


interface TransactionCode {
    const DEBIT = 'Debit';
    const CREDIT = 'Credit';

    public function getCode();

    public function getType();
}