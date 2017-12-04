<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11/27/2017
 * Time: 6:02 PM
 */

namespace Exceptions;
use Phalcon\Http\Response;

class TokenClaimMissingException extends \Exception
{
    public $claim_name; // parameter that is missing

    /**
     * RestParameterNotFoundException constructor.
     * @param $claim_name
     */
    public function __construct( $claim_name)
    {
        $this->claim_name = $claim_name;
        parent::__construct("[ERROR] missing claim : " . $claim_name);
    }

    /**
     * Returns json data as body payload, only status and response_message
     * @return array
     */
    public function json_serialize()
    {
        $data = array();
        $data['status'] = "Bad Request";
        $data['message'] = "[ERROR] missing claim : " . $this->claim_name;
        return $data;
    }

    /**
     * Returns Phalcon\Response generated from this Exception
     * @return Response
     */
    public function getPhalconResponse()
    {
        $response = new Response();
        $response->setStatusCode("400", "Bad request");
        $response->setJsonContent($this->json_serialize());
        return $response;
    }
}