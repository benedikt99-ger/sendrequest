<?php

/**
 * Copyright © benedikt nünemann. All rights reserved.
 */
 
use OxidEsales\Eshop\Application\Controller\BasketController as BasketControllerParent; 

$sMetadataVersion = '2.1';
/**
 * Module information
 */
$aModule = [
    'id'          => \nuenemann\sendrequest\Module::MODULE_ID,
    'title'       => 'sendrequest Formular für OXID 7',
    'description' => 'sendrequest Formular für OXID 7',
    'thumbnail'   => 'bn_logo.png',
    'version'     => '0.2.0',
    'author'      => 'Nünemann',
    'url'         => 'https://github.com/benedikt99-ger/sendrequest',
    'email'       => 'benedikt@nuenemann.de',
	'extend' => [
		BasketControllerParent::class => \nuenemann\sendrequest\Application\Extend\Controller\BasketController::class
	],
    'controllers' => [
        
    ],	
    'templates' => [

    ],	
   'settings' => [
        [
            'group' => 'sendrequestMain','name' => 'sendrequestEmail',
            'type' => 'str','value' => '','position' => 3
        ],
        [
            'group' => 'sendrequestMain','name' => 'sendrequestCC',
            'type' => 'arr','value' => [],'position' => 4
        ]
    ]
];
