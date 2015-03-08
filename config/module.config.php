<?php

return [
    'service_manager' => [
        'factories' => [
            'NoDbModule\Configuration'             => 'NoDbModule\Factory\ConfigurationFactory',
            'NoDbModule\Resolver\DoctrineResolver' => 'NoDbModule\Factory\DoctrineResolverFactory'
        ]
    ],

    'view_manager' => [
        'template_map' => [
            'error/dberror' => __DIR__ . '/../view/error/dberror.phtml',
        ],
    ],
];