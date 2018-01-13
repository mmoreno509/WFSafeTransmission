<?php
namespace WFSafeTransmission\Codes\ServiceClassCodes;

use WFSafeTransmission\Interfaces\ServiceClassCode;
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 9:58 PM
 */
class MixedTransactions implements ServiceClassCode {
    public function getCode()
    {
        return '200';
    }
}