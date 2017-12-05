<?php
require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/app/controllers/ControllerBase.php';
require_once __DIR__ . '/app/controllers/FriendsController.php';
require_once __DIR__ . '/app/controllers/ArtistController.php';
require_once __DIR__ . '/app/controllers/SponsorsController.php';
require_once __DIR__ . '/app/controllers/MapItemsController.php';
require_once __DIR__ . '/app/controllers/UserController.php';
require_once  __DIR__ . '/app/controllers/StageController.php';

use Crada\Apidoc\Builder;
use Crada\Apidoc\Exception;

$classes = array(
    'Controllers\ArtistController',
    'Controllers\FriendsController',
    'Controllers\SponsorsController',
    'Controllers\MapItemsController',
    'Controllers\UserController',
    'Controllers\StageController'
);

$output_dir = __DIR__ . '/apidocs';
$output_file = 'api.html'; // defaults to index.html

try {
    $builder = new Builder($classes, $output_dir, 'Festivali v1', $output_file);
    $builder->generate();
} catch (Exception $e) {
    echo 'There was an error generating the documentation: ', $e->getMessage();
}