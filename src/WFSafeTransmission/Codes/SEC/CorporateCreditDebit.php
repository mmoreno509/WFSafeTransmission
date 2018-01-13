<?php
namespace WFSafeTransmission\Codes\SEC;
/**
 * Created by PhpStorm.
 * User: mikelmoreno
 * Date: 1/11/18
 * Time: 9:56 PM
 */
class CorporateCreditDebit implements SECCode {
    public function getCode() {
        return 'CCD';
    }
}