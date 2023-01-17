<?php
// TODO -> delete file
require_once realpath('vendor/autoload.php');
use App\Handlers\RequestHandler;

$handler = new RequestHandler();
$handler->handleRequest($argv[1], 'GET', '');