<?php

return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'up-db-route' => array(
                    'options' => array(
                        'route' => 'db (up|down|create|setup):action [<revNumber>] [<username>] [<password>]',
                        'defaults' => array(
                            'controller' => 'Migration\Controller\Index',
                            'action'     => 'up'
                        )
                    )
                )
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Migration\Controller\Index' => 'Migration\Controller\IndexController'
        ),
    ),
);