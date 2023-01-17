<?php

// Add more routes when they are available
const ROUTES = [
    [
        'pattern' => '/^\/shout\/(\w+(-\w+)*){1}(\?limit=\d{1,2})?$/',
        'method' => 'GET',
        'action' => [
            'controller' => 'QuotesController',
            'methodName' => 'shoutAction'
        ]
    ],
];