<?php

use App\Handlers\RequestHandler;

require_once realpath('../vendor/autoload.php');

$handler = new RequestHandler();
$requestBody = file_get_contents('php://input');
$handler->handleRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $requestBody);