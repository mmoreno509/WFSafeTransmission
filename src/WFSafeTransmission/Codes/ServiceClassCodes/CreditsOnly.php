<?php
namespace WFSafeTransmission\Codes\ServiceClassCodes;

use WFSafeTransmission\Interfaces\ServiceClassCode;
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 9:59 PM
 */
class CreditsOnly  implements ServiceClassCode {
    public function getCode()
    {
        return '220';
    }
}