<?php
namespace Controllers;

use Phalcon\Mvc\Controller;
use Exceptions\RestParameterNotFoundException;

class ControllerBase extends Controller
{

public function buildToken($user_id)
    {
        return $this->jwt->buildTokenWithUserID($user_id);
    }

    public function verifyToken($token){
        return $this->jwt->validateToken($token);
    }

    public function getUserIdFromToken(){
        return $this->jwt->getUserId($this->request->getHeader('Auth'));
    }

    /**
     * Get JSON parameter from POST and validate
     * @param $name
     * @param $type
     * @param $required
     * @param int $defaultvalue
     * @return null
     * @throws RestParameterNotFoundException
     */
    public function getJsonParamFromPOST($name, $type, $required, $validator = null, $defaultvalue = null, $jsonInnerObject = null)
    {
        if(!is_bool($required)){
            throw new RestParameterNotFoundException("500","Internal Server Error","Function not called properly, 'required' problem",$name);
        }
        if($validator != null and !($validator instanceof ValidatorInterface)){
            throw new RestParameterNotFoundException("500","Internal Server Error","Function not called properly, 'validator' problem",$name);
        }
        if($this->request->isPost()){
            $json = $this->request->getJsonRawBody();
            if($jsonInnerObject != null) {
                $split = explode('->', $jsonInnerObject);
                if (sizeof($split) == 1) {
                    if (isset($json->$jsonInnerObject)) {
                        $json = $json->$jsonInnerObject;
                    } else {
                        throw new RestParameterNotFoundException("400", "Bad Request", "Missing object $jsonInnerObject", $jsonInnerObject);
                    }
                } else {
                    $firstobj = $split[0];
                    $secondobj = $split[1];
                    if (isset($json->$firstobj->$secondobj)) {
                        $json = $json->$firstobj->$secondobj;
                    } else {
                        throw new RestParameterNotFoundException("400", "Bad Request", "Missing object $secondobj", $secondobj);
                    }
                }
            }
            switch ($type){
                case "string":
                    if(isset($json->$name)){
                        if($required){
                            if(!empty(trim($json->$name))){
                                return $json->$name;
                            } elseif ($validator != null and $validator->validate($json->$name)) {
                                return trim($json->$name);
                            } else {
                                throw new RestParameterNotFoundException("400","Bad Request","Missing parameter, $name is required ",$name);
                            }
                        } else {
                            return trim($json->$name);
                        }
                    } else {
                        throw new RestParameterNotFoundException("400","Bad Request","Missing parameter $name",$name);
                    }
                    break;
                case "int":
                    return $json->$name;
                    break;
                case "double":
                    break;
                case "date":
                    break;
                case "boolean":
                    break;
                case "file":
                    break;
                default:
                    throw new RestParameterNotFoundException("500","Internal Server Error","Function not called properly, 'type' problem",$name);
            }
        } else {
            throw new RestParameterNotFoundException("400","Bad Request","Missing parameter",$name);
        }
    }

}
