<?php
namespace Models\Exceptions;

use Phalcon\Exception;
/**
 * Created by PhpStorm.
 * User: ProudSourceIT
 * Date: 10.5.2016
 * Time: 11:30
 */
class InvalidRESTParameterException extends Exception
{
    public $responsecode;
    public $responsestatus;
    public $responsemessage;
    public $responseJSON;
    public $paramName;

    //public function InvalidRESTParameterException($code, $wrongparam)
    public function __construct($code, $status, $wrongparam)
    {
        $this->responsecode = $code;
        $this->responsestatus = $status;
        $this->responsemessage = "Field '$wrongparam' is invalid!";
        $this->paramName = $wrongparam;

        parent::__construct($this->responsemessage);
    }

    public function jsonSerialize()
    {
        $data = array();

        $data['status'] = 'Invalid REST Parameters';
        $data['messages'] = $this->responsemessage;

        return $data;
    }
}