<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host' => 'localhost',
                    'port' => '3306',
                    'user' => 'root',
                    'password' => '12345',
                    'dbname' => 'blog',
                )
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory'
        ),
        'aliases' => array(
            'db' => 'Zend\Db\Adapter\Adapter'
        )
    ),
    'db' => array(
        'driver' => 'pdo',
        'dsn' => 'mysql:dbname=blog;host=localhost',
        'username' => 'root',
        'password' => '12345',
        'dbname' => 'blog',
        'host' =>' localhost',
        'charset' => 'utf8',
        'options' => array(
            'buffer_results' => true,
        )
    ),
    'phpSettings' => array(
        'display_errors' => 1,
        'display_startup_errors' => 1,
        'error_reporting' => E_ALL,
    ),
);
