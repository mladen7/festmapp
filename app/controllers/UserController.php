<?php
namespace Controllers;

use Exceptions\RestParameterNotFoundException;
use Phalcon\Mvc\Controller;
use Models\User;
//use Models\Read\UserCommunitiesListRead;

class UserController extends ControllerBase {

    /**
     * @ApiDescription(section="User", description="Register new user")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/user/register")
     * @ApiParams(name="user", type="object", nullable=false, sample = "{'email':'pera@gmail.com', 'password':'pera123', 'full_name':'Petar Petrovic', 'phone':'+38164123456'}", description="User info")
     * @ApiReturnHeaders(sample="HTTP 201 Created")
     * @ApiReturn(type="array", sample=" [{
     * 'status': 'Uspešno',
     * 'messages': 'Registracija uspešna'
     * }]
     * ")
     */
    public function register()
    {
        $response = $this->response;
        try {
            $email = $this->getJsonParamFromPOST('email','string',true);
            $pass = $this->getJsonParamFromPOST('password','string',true);
            $name = $this->getJsonParamFromPOST('full_name','string',true);
            $fb_id = $this->getJsonParamFromPOST('fb_id','string',false);
             $google_id = $this->getJsonParamFromPOST('fb_id','string',false);

            $user = User::findFirst(
                [
                    "email = :email:",
                    "bind" => [
                        "email" => $email
                    ]
                ]
            );

            // Check if user already exists
            if (!$user) {
                $user = new User();

                // Populate User model
                $user->email = $email;
                $user->name = $name;
                $user->fb_id = $fb_id;
                $user->google_id = $google_id;
                $user->password = $this->security->hash($pass);
//                $user->image = $image;
                // Save the new user
                if ($user->save()) {
                    $response->setStatusCode(201, "Created");
                    $response->setJsonContent(array('status' => 'Uspešno', 'messages' => 'Registracija uspešna'));
                } else {
                    $response->setStatusCode(500, "Unexpected error");
                    $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Registracija neuspešna'));
                }
            } else {
                // User already exists. There is a conflict.
                $response->setStatusCode(409, "Conflict");
                $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Korisnik sa datom e-mail adresom već postoji!'));
            }
        } catch (RestParameterNotFoundException $e) {
            $response = $e->getPhalconResponse();
        } catch (\Exception $e) {
            $response->setStatusCode(500, "Unexpected error");
            $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Došlo je do neočekivane greške'));
        } finally {
            return $response;
        }
    }

    /**
     **
     * @ApiDescription(section="User", description="Find user by id")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/user/get-user")
     * @ApiParams(name="user_id", type="integer", nullable=false, description="User id")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample=" {
    'id': '4',
    'name': 'Nikola Nikolic',
    'email': 'nikola@gmail.com',
    'role': 4
     * }
     * ")
     */
    public function getUserById()
    {
        $response = $this->response;

        try {
            $id = $this->getJsonParamFromPOST('user_id','int',true);
            $user = User::findFirst(
                [
                    "id = :id:",
                    "bind" => [
                        "id" => $id
                    ]
                ]
            );

            if ($user) {
                $response->setStatusCode(200, "OK");
                $user->password = '';
                $response->setJsonContent($user);
            } else {
                $response->setStatusCode(404, "Not found");
                $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Ne postoji korisnik sa datim podacima.'));
            }
        } catch(RestParameterNotFoundException $r) {
            $response = $r->getPhalconResponse();
        } catch (\Exception $e) {
            $response->setStatusCode(500, "Unexpected error");
            $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Došlo je do neočekivane greške. Molimo pokušajte ponovo i/ili kontaktirajte nas ukoliko se problem ne otkloni.'));
        } finally {
            return $response;
        }
    }

    /**
     * @ApiDescription(section="User", description="Login validation")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/user/login")
     * @ApiHeaders(name="No token required", nullable=true)
     * @ApiParams(name="email", type="string", nullable=false, description= "Email of the user")
     * @ApiParams(name="password", type="string", nullable=false, description= "Password of the user")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample="{
     *  'status' : 'string',
     *  'messages' : 'string',
     * }")
     * @ApiBody(type="object", sample="{
     *  'email':'string',
     *  'password':'string'
     * }")
     * @return Response
     */
    public function login()
    {
        $response = $this->response;
        try {
            $email = $this->getJsonParamFromPOST('email', 'string', true);
            $password = $this->getJsonParamFromPOST('password', 'string', true);

            $user = User::findFirst(
                [
                    "email = :email:",
                    "bind" => [
                        "email" => $email
                    ]
                ]
            );


            //Proverava da li korisnik postoji
            if ($user) {
                if ($this->security->checkHash($password, $user->password)) {
                    $response->setStatusCode(200, "OK");

                    //build JWT token and setting it as header
                    $response->setHeader("Auth", "Bearer " . $this->buildToken($user->id));

                    //prepare list of communities for user
//                    $uclRead = new UserCommunitiesListRead();
//                    $uclRead->executeRead($user);
//                    $response->setJsonContent($uclRead);
                    $user->password = '';
                    $response->setJsonContent($user);
                } else {
                    $response->setStatusCode(401, "Unauthorized");
                    $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Pogrešna šifra'));
                }
            } else {
                $response->setStatusCode(404, "Not Found");
                $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Nije pronađen korisnik sa datim e-mailom'));
            }
        } catch (RestParameterNotFoundException $e) {
            $response = $e->getPhalconResponse();
        } catch (\Exception $e) {
            $response->setStatusCode(500, "Internal Server Error");
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => $e->getMessage()));
        } finally {
            return $response;
        }
    }

    /**
     * @ApiDescription(section="User", description="Social login validation or registration of a new user")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/user/social-login")
     * @ApiHeaders(name="No token required", nullable=true)
     * @ApiParams(name="email", type="string", nullable=false, description= "Email of the user")
     * @ApiParams(name="fb_uuid", type="string", nullable=true, description= "Facebook id of the user, one of these two has to have a value")
     * @ApiParams(name="ggl_uuid", type="string", nullable=true, description= "Google id of the user, one of these two has to have a value")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturnHeaders(name="HTTP 333 OK")
     * @ApiReturn(type="object", sample="{
     *  'status' : 'string',
     *  'messages' : 'string',
     * }")
     * @ApiBody(type="object", sample="{
     *  'email':'string',
     *  'fb_uuid':'string',
     *  'ggl_uuid':'string'
     * }")
     * @return Response
     */
    public function socialLogin()
    {
        $response = $this->response;
        try {
            $email = $this->getJsonParamFromPOST('email', 'string', true);
            $fb_uuid = $this->getJsonParamFromPOST('fb_uuid', 'string', false);
            $ggl_uuid = $this->getJsonParamFromPOST('ggl_uuid', 'string', false);

            if (empty($fb_uuid) and empty($ggl_uuid)) {
                throw new RestParameterNotFoundException("400", "Bad Request", "Missing parameters for identification", null);
            }

//            TODO za name
//            if (isset($data['full_name'])) {
//                $full_name = $data['full_name'];
//            }

            $user = User::findFirst(
                [
                    "email = :email:",
                    "bind" => [
                        "email" => $email
                    ]
                ]
            );

            //Postoji user?
            if ($user) {
                //Ima fb?
                if (!empty($user->fb_uuid)) {
                    //Jednak fb?
                    if ($user->fb_uuid == $fb_uuid) {
                        $uclRead = new UserCommunitiesListRead();

                        $response->setHeader("Auth", "Bearer " . $this->buildToken($user->id));
                        $uclRead->executeRead($user);
                        $response->setStatusCode(200, 'OK');
                        $response->setJsonContent($uclRead);
                    } else { //Nije jednak fb?
                        $response->setStatusCode(400, 'Unexpected error');
                        $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Not valid FB uuid!'));
                    }
                } else { // Nema fb?
                    $user->fb_uuid = $fb_uuid;
                    if (!$user->update()) {
                        $response->setStatusCode(400, 'Unexpected error');
                        $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'User fb can not be updated!'));
                    } else {
                        $uclRead = new UserCommunitiesListRead();

                        $response->setHeader("Auth", "Bearer " . $this->buildToken($user->id));
                        $uclRead->executeRead($user);
                        $response->setStatusCode(200, 'OK');
                        $response->setJsonContent($uclRead);
                    }
                }
            } else {
                //Register user
                $user = new User();

                $user->email = $email;
                $user->fb_uuid = $fb_uuid;
                $user->ggl_uuid = $ggl_uuid;
//                $user->full_name = $full_name;

                // Save the new user
                if ($user->save()) {
                    $uclRead = new UserCommunitiesListRead();
                    $response->setHeader("Auth", "Bearer " . $this->buildToken($user->id));
                    $uclRead->executeRead($user);
                    $response->setStatusCode(200, "OK");
                    $response->setJsonContent($uclRead);
                } else {
                    $response->setStatusCode(400, "Unexpected error");
                    $response->setJsonContent(array('status' => 'Greška', 'messages' => $user->getMessages()[0]));
                }
            }
        } catch (RestParameterNotFoundException $e) {
            $response = $e->getPhalconResponse();
        } catch (\Exception $e) {
            $response->setStatusCode(500, "Internal Server Error");
            $response->setJsonContent(array('status' => 'Greška', 'messages' => $e->getMessage()));

        } finally {
            return $response;
        }
    }

    /**
     * @ApiDescription(section="User", description="Edit user by id")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/user/edit")
     * @ApiParams(name="email", type="string", nullable=true, description= "Email of the user")
     * @ApiParams(name="full_name", type="string", nullable=true, description= "Full name of the user")
     * @ApiParams(name="phone", type="string", nullable=true, description= "Phone number the user")
     * @ApiParams(name="profile_pic_URL", type="string", nullable=true, description= "Link to user's profile picture")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="array", sample=" [{
     * 'status': 'Uspešno',
     * 'messages': 'Promene su uspešno sačuvane'
     * }]
     * ")
     */
    public function editUserByID()
    {
        $response = $this->response;
        try {
            $id = 129; //zakucano za testiranje
//          $id = $this->getUserIdFromToken();
            $email = $this->getJsonParamFromPOST('email','string',false);
            $name = $this->getJsonParamFromPOST('full_name','string',false);
            $phone = $this->getJsonParamFromPOST('phone','string',false);
            $picture = $this->getJsonParamFromPOST('profile_pic_URL','string',false);
            //$password = $this->getJsonParamFromPOST('password','string',false);

            $user = User::findFirst(
                [
                    "id = :id:",
                    "bind" => [
                        "id" => $id
                    ]
                ]
            );

            if(!$user) {
                $response->setStatusCode(404, "Not found");
                $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Korisnik ne postoji'));
                return;
            }

            if (isset($name)) {
                $user->full_name = $name;
            }
            if (isset($email)) {
                $user->email = $email;
            }
//            if (isset($change->password)) {
//                $user->password = $this->security->hash($change->password);
//            }
            if (isset($phone)) {
                $user->phone = $phone;
            }
            if (isset($picture)) {
                $user->profile_pic_URL = $picture;
            }
            if (!$user->update()) {
                $response->setStatusCode(500, 'Unexpected error');
                $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Čuvanje promena neuspešno'));
            } else {
                $response->setStatusCode(200, 'OK');
                $response->setJsonContent(array('status' => 'Uspešno', 'messages' => 'Promene su uspešno sačuvane'));
            }
        } catch (RestParameterNotFoundException $e) {
            $response = $e->getPhalconResponse();
        } catch (\Exception $e) {
            $response->setStatusCode(500, "Unexpected error");
            $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Došlo je do neočekivane greške'));
        } finally {
            return $response;
        }
    }

    /**
     **
     * @ApiDescription(section="User", description="Find all users whose email addresses begin with given value")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/user/search-by-email")
     * @ApiParams(name="email", type="string", nullable=false, description="Email filter value")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="array", sample=" [{
     * 'id': '123',
     * 'email': 'pera@mail.com',
     * 'fb_uuid': null,
     * 'ggl_uuid':null,
     * 'full_name': 'Pera Peric',
     * 'phone': '+381234567',
     * 'password': '',
     * 'profile_pic_URL': ''
     * },
     * {
     * 'id': '456',
     * 'email': 'perica123@mail.com',
     * 'fb_uuid': null,
     * 'ggl_uuid':null,
     * 'full_name': 'Perica Petrovic',
     * 'phone': '+3819876543',
     * 'password': '',
     * 'profile_pic_URL': ''
     * }]
     * ")
     */
    public function getUsersByEmail()
    {
        $response = $this->response;
        try {
            $email = $this->getJsonParamFromPOST('email','string',true);

            $users = User::find(
                [
                    "email LIKE :email:",
                    "bind" => [
                        "email" => $email . '%'
                    ]
                ]
            );

            if(sizeof($users) == 0) {
                $response->setStatusCode(204, "No content");
            } else {
                $response->setStatusCode(200, "OK");
                $filtered = $this->removePasswordFromUsers($users);
                $response->setJsonContent($filtered);
            }

        } catch(RestParameterNotFoundException $r) {
            $response = $r->getPhalconResponse();
        } catch (\Exception $e) {
            $response->setStatusCode(500, "Unexpected error");
            $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Došlo je do neočekivane greške'));
        } finally {
            return $response;
        }
    }

    /**
     **
     * @ApiDescription(section="User", description="Find all users whose full names begin with given value")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/user/search-by-name")
     * @ApiParams(name="name", type="string", nullable=false, description="Name filter value")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="array", sample=" [{
     * 'id': '123',
     * 'email': 'pera@mail.com',
     * 'fb_uuid': null,
     * 'ggl_uuid':null,
     * 'full_name': 'Pera Peric',
     * 'phone': '+381234567',
     * 'password': '',
     * 'profile_pic_URL': ''
     * },
     * {
     * 'id': '456',
     * 'email': 'perica123@mail.com',
     * 'fb_uuid': null,
     * 'ggl_uuid':null,
     * 'full_name': 'Perica Petrovic',
     * 'phone': '+3819876543',
     * 'password': '',
     * 'profile_pic_URL': ''
     * }]
     * ")
     */
    public function getUsersByName()
    {
        $response = $this->response;
        try {
            $name = $this->getJsonParamFromPOST('name','string',true);
            $users = User::find(
                [
                    "full_name LIKE :name:",
                    "bind" => [
                        "name" => $name . '%'
                    ]
                ]
            );

            if (sizeof($users) > 0) {
                $response->setStatusCode(200, "OK");
                $filtered = $this->removePasswordFromUsers($users);
                $response->setJsonContent($filtered);
            } else {
                $response->setStatusCode(204, "No content");
            }
        } catch(RestParameterNotFoundException $r) {
            $response = $r->getPhalconResponse();
        } catch(\Exception $e) {
            $response->setStatusCode(500, "Unexpected error");
            $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Došlo je do neočekivane greške'));
        } finally {
            return $response;
        }
    }

    private function removePasswordFromUsers($users) {
        $result = [];
        foreach($users as $user) {
            $u = $user;
            $u->password = "";
            $result[] = $u;
        }
        return $result;
    }

//    private function createListUsersViewModel($users)
//    {
//
//        $users1 = array();
//        foreach ($users as $user) {
//            $user->password = "";
//            array_push($users1, $user);
//        }
//        return $users1;
//    }


}

