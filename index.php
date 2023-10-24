<?php
require_once 'kuro/require.php';
include 'router.php';

$request = $_SERVER['REQUEST_URI'];
$router = new Router();

$router->any('/', 'views/index');
$router->any('/home', 'views/index');
$router->any('/login', 'views/login');
$router->any('/logout', 'views/logout');
$router->any('/admin/admin', 'views/admin/admin');
$router->any('/admin/users', 'views/admin/users');
$router->any('/admin/organizations', 'views/admin/organizations');
$router->any('/admin/logs', 'views/admin/logs');
$router->any('/404', 'views/404');