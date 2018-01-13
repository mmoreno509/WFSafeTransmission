<?php
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 10:00 PM
 */

namespace WFSafeTransmission\Codes\ServiceClassCodes;

use WFSafeTransmission\Interfaces\ServiceClassCode;

class DebitsOnly implements ServiceClassCode {
    public function getCode()
    {
        return '225';
    }
}