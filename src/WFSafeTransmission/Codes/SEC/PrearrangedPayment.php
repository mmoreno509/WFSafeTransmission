<?php
namespace WFSafeTransmission\Codes\SEC;

/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 9:57 PM
 */
class PrearrangedPayment implements SECCode {
    public function getCode() {
        return 'PPD';
    }
}