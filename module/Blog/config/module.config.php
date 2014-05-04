<?php

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Blog\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'blog' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '[/][:action][/:id]',
                    'constraints' => array(
                      'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                      'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                      'controller' => 'Blog\Controller\Index',
                      'action'     => 'index',
                  ),
                ),
            ),
            'auth' => array(
                'type'    => 'segment',
                    'options' => array(
                    'route' => '/auth/[:action]',
                    'constraints' => array(
                      'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Blog\Controller\Auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'oauth' => array(
                'type'    => 'segment',
                    'options' => array(
                    'route' => '/oauth/[:action]',
                    'constraints' => array(
                      'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Blog\Controller\Oauth',
                        'action'     => 'index',
                    ),
                ),
            ),
            'comment' => array(
                'type'    => 'segment',
                    'options' => array(
                    'route' => '/comment/[:action]',
                    'constraints' => array(
                      'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Blog\Controller\Comment',
                        'action'     => 'addcomment',
                    ),
                ),
            ),
        ),
    ),
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=blog;host=localhost',
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Blog\Controller\Index'    => 'Blog\Controller\IndexController',
            'Blog\Controller\Auth'     => 'Blog\Controller\AuthController',
            'Blog\Controller\Oauth'    => 'Blog\Controller\OauthController',
            'Blog\Controller\Comment'  => 'Blog\Controller\CommentController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'service_manager' => array(
            'aliases' => array( 
                'Zend\Authentication\AuthenticationService' => 'my_auth_service',
            ),
            'invokables' => array(
                'my_auth_service' => 'Zend\Authentication\AuthenticationService',
            ),
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    'view_helpers' => array(
      'invokables' => array(
          'showMessages' => 'Blog\View\Helper\ShowMessages',
      ),
    ),


    'doctrine' => array(
        'driver' => array(
            'blog_entity' => array(
              'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
              'paths' => array(__DIR__ . '/../src/Blog/Entity')
            ),
            'orm_default' => array(
               'drivers' => array(
                  'Blog\Entity' => 'blog_entity',
                )
            )
        ),
        // 'authentication'    => array(
        //     'orm_default' => array(
        //     'object_manager' => 'Doctrine\ORM\EntityManager',
        //     'identity_class' => 'Blog\Entity\User',
        //     'identity_property' => 'email',
        //     'credential_property' => 'password',
        //     'credential_callable' => function(Blog\Entity\User $user, $passwordGiven) { 
        //             if ($user->getPassword() == md5($passwordGiven."salt")) 
        //             {
        //                 return true;
        //             }
        //             else 
        //             {
        //                 return false;
        //             }
        //     },
        //     ),
        // ),
        'authentication'    => array(
            'orm_default' => array(
            'object_manager' => 'Doctrine\ORM\EntityManager',
            'identity_class' => 'Blog\Entity\OAuthUser',
            'identity_property' => 'email',
            'credential_property' => 'name',
            ),
        ),
    ),
);