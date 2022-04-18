<?php

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $config) {
    $config->test(true);
    $config->secret('test');

    $config->router()
        ->utf8(true)
        ->resource('%kernel.project_dir%/config/routing.php')
    ;

    $config->session()
        ->storageFactoryId('session.storage.factory.mock_file')
    ;
};
