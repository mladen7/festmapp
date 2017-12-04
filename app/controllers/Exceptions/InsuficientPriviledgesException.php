<?php
namespace Controllers\Exceptions;


/**
 * Created by PhpStorm.
 * User: ProudSourceIT
 * Date: 10.5.2016
 * Time: 11:30
 */
class InsuficientPriviledgesException extends \Exception
{
    public $responsecode;
    public $responsemessage;
    public $responseJSON;

    //public function InvalidRESTParameterException($code, $desc)
    public function __construct($code, $desc)
    {
        $this->responsecode = $code;
        $this->responsemessage = " Member does not have $desc privileges!";

        parent::__construct($this->responsemessage);
    }

    public function jsonSerialize()
    {
        $data = array();

        $data['status'] = 'Unauthorized';
        $data['messages'] = $this->responsemessage;

        return $data;
    }
}