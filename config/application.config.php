<?php

if (!defined('APPLICATION_ENV')) {
    if (isset($_SERVER['APPLICATION_ENV'])) {
        $env = $_SERVER['APPLICATION_ENV'];
    } elseif (!$env = getenv('APPLICATION_ENV')) {
        $env = 'production';
    }

    define('APPLICATION_ENV', $env);
}

return [
    'modules' => [
        'CpmsClient',
        'DoctrineModule',
        'DoctrineORMModule',
        'DvsaLogger',
        'Laminas\ApiTools\Versioning',
        'Laminas\Cache',
        'Laminas\InputFilter',
        'Laminas\Filter',
        'Laminas\Form',
        'Laminas\Hydrator',
        'Laminas\Paginator',
        'Laminas\Router',
        'Laminas\Validator',
        'SharedReport',
    ],
    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,' . APPLICATION_ENV . ',local}.php',
        ],
    ],
];
