<?php

namespace KryuuMainOmt;

defined('__AUTHORIZE__') or define('__AUTHORIZE__','bjyauthorize');

$router     = include(__DIR__.'/router.config.php');
$service    = include(__DIR__.'/services.config.php');
$authorize  = include(__DIR__.'/authorize.config.php');

$config =  array(
    __NAMESPACE__ => array(
    ),
    
    __AUTHORIZE__       => $authorize,
    
    'router'            => $router,
    
    'service_manager'   => $service,

    'controllers' => array(
        'invokables' => array(
            'KryuuMainOmt\Controller\Index' => 'KryuuMainOmt\Controller\IndexController',
        ),
    ),

    'doctrine'=> array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            __NAMESPACE__.'\Config'                   => __NAMESPACE__.'\Service\ConfigServiceFactory',
            __NAMESPACE__.'\GlobalConfig'             => __NAMESPACE__.'\Service\GlobalConfigServiceFactory',
        ),
        'invokables'  => array(
            //'BjyAuthorize\View\RedirectionStrategy' => 'BjyAuthorize\View\RedirectionStrategy',
        ),
        'aliases'     => array(
            //'bjyauthorize_zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
        ),
        'initializers' => array(
            //'BjyAuthorize\Service\AuthorizeAwareServiceInitializer'
            //    => 'BjyAuthorize\Service\AuthorizeAwareServiceInitializer'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'omt/no-sidebar'          => __DIR__ . '/../view/layout/no-sidebar.phtml',
            'omt/layout'              => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
            'kryuumainomt' => __DIR__ . '/../view',
        ),
    ),
);


return $config;