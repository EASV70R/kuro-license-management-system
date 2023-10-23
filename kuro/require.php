<?php
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // 1 for HTTPS (production)
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_lifetime', 0);

require_once 'core/config.php';
require_once 'utils/util.php';
require_once 'utils/session.php';

Session::Init();

var_dump($_SESSION);