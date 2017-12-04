<?php
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

use Phalcon\Mvc\Micro\Collection as MicroCol;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

/**
 * Add your routes here
 */
$app->get('/', function () {
    echo $this['view']->render('index');
});




/**
 * Not found handler
 */
$app->notFound(function () use($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->render('404');
});

// Micro controllers instantiation
$artists = new MicroCol();
$friends = new MicroCol();
$mapItems = new MicroCol();
$sponsors = new MicroCol();
$stages = new MicroCol();
$users = new MicroCol();


$artists->setHandler(new \Controllers\ArtistController());

$friends->setHandler(new Controllers\FriendsController());

$mapItems->setHandler(new Controllers\MapItemsController());

$sponsors->setHandler(new Controllers\SponsorsController());

$stages->setHandler(new Controllers\StageController());

$users->setHandler(new Controllers\UserController());


// Setting Route prefixes
$artists->setPrefix('/artist');
$friends->setPrefix('/friend');
$mapItems->setPrefix('/mapItem');
$sponsors->setPrefix('/sponsors');
$stages->setPrefix('/stage');
$users->setPrefix('/user');

// Artists routes

$artists->get('/get-all', 'getAllCommunities');
$artists->post('/get-by-id', 'getCommunityById');
$artists->post('/delete', 'deleteCommunity');
$artists->post('/edit', 'editCommunity');
$artists->post('/filter', 'getCommunitiesWithManagerByFilter');
$artists->post('/create', 'createAndJoinCommunity');

// Friends routes
$friends->get('/get-all-municipalities', 'getAllMunicipalities','get all municipalities');
$friends->post('/get-municipalities-by-name', 'getMunicipalityByName');
$friends->post('/get-municipalities-by-id', 'getMunicipalityById');

// Map items routes
$mapItems->get('/get-all', 'getAllAddresses');
$mapItems->post('/get-by-municipality', 'getAddressesByMunicipality');

// Sponsors routes
$sponsors->post('/add', 'addMember');
$sponsors->post('/get-members-by-community', 'getMembersByCommunity');
$sponsors->get('/filter', 'getMembersViewByCommunity'); //community_id, active
$sponsors->post('/approve', 'confirmMemberToCommunity');

// Stages routes
$stages->post('/', 'addNewPost');
$stages->post('/vote', 'addNewVote');
$stages->post('/filter', 'getPostsView');
$stages->post('/delete', 'deletePost');

// User routes
$users->post('/register', 'register', 'no-token');
$users->post('/getUserById', 'getUserById');
$users->post('/login', 'login', 'no-token');
$users->post('/login/social', 'socialLogin', 'no-token');
$users->post('/edit', 'editUserByID'); //
$users->post('/getUserByName', 'getUsersByName');
$users->post('/getUserByEmail', 'getUsersByEmail');

// Mounting controllers
$app->mount($users);
$app->mount($artists);
$app->mount($friends);
$app->mount($sponsors);
$app->mount($mapItems);
$app->mount($stages);

/*
 * Event handler
 */
$eventsManager = new EventsManager();
$eventsManager->attach(
    'micro:beforeExecuteRoute',
    function (Event $event, $app) {
        $route_name = $app->router->getMatchedRoute()->getName();
        if($route_name != null and strpos($route_name, 'no-token') === false){
            $token = $app->request->getHeader('Auth');
            if(!empty($token)){
                if(!$app->jwt->verifyToken($token)){//not valid/verified
                    $app->response->setStatusCode(401, "Unauthorized ");
                    $app->response->setJsonContent([
                        'message' => 'Token invalid'
                    ]);
                    $app->response->send();
                    return false;
                }
                if($app->jwt->isExpired($token)){
                    $app->response->setStatusCode(401, "Unauthorized ");
                    $app->response->setJsonContent([
                        'message' => 'Token expired'
                    ]);
                    $app->response->send();
                    return false;
                }
                return true;
            } else {
                //nije token u headeru
                $app->response->setStatusCode(401, "Unauthorized ");
                $app->response->setJsonContent([
                    'message' => 'Token missing'
                ]);
                $app->response->send();
                return false;
            }
        } else {
            //
            return true;
        }
    }
);
$app->setEventsManager($eventsManager);


