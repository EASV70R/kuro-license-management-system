<?php
require_once 'kuro/require.php';
include 'router.php';

$request = $_SERVER['REQUEST_URI'];
$router = new Router();

$router->any('/', 'views/index');
$router->any('/home', 'views/index');
$router->any('/404', 'views/404');