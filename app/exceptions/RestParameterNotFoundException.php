<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11/21/2017
 * Time: 5:27 PM
 */

namespace Exceptions;

use Phalcon\Http\Response;

class RestParameterNotFoundException extends \Exception
{
    public $response_code; // ex: 404
    public $response_status; // ex: Not Found
    public $response_message; // Server is in maintenance
    public $parameter_name; // parameter that is missing

    /**
     * RestParameterNotFoundException constructor.
     * @param $response_code
     * @param $response_status
     * @param $response_message
     * @param $parameter_name
     */
    public function __construct($response_code, $response_status, $response_message, $parameter_name)
    {
        $this->response_code = $response_code;
        $this->response_status = $response_status;
        $this->response_message = $response_message;
        $this->parameter_name = $parameter_name;
        parent::__construct("[Greška] " . $response_message);
    }

    /**
     * Returns json data as body payload, only status and response_message
     * @return array
     */
    public function json_serialize()
    {
        $data = array();
        $data['status'] = "Neispravan REST parametar $this->parameter_name";
        $data['message'] = $this->response_message;
        return $data;
    }

    /**
     * Returns Phalcon\Response generated from this Exception
     * @return Response
     */
    public function getPhalconResponse()
    {
        $response = new Response();
        $response->setStatusCode($this->response_code, $this->response_status);
        $response->setJsonContent($this->json_serialize());
        return $response;
    }

}