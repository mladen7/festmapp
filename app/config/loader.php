<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
//$loader->registerDirs(
//    [
//        $config->application->controllersDir,
//        $config->application->modelsDir
//    ]
//)->register();
$loader->registerNamespaces(
    [
        "Controllers" => $config->application->controllersDir,
        "Models" => $config->application->modelsDir,
        'Security\JWT' => $config->application->jwtDir,
        'Lcobucci\JWT' => $config->application->vendor,
        'Crada\Apidoc' => BASE_PATH. '/vendor/crada/php-apidoc/Crada',
        'Exceptions' => $config->application->exceptions,
    ]
)->register();
