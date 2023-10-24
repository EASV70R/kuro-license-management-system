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

/*if ($_SERVER['REMOTE_ADDR'] != $_SESSION['ipaddress'])
{
    session_unset();
    session_destroy();
}
if ($_SERVER['HTTP_USER_AGENT'] != $_SESSION['useragent'])
{
    session_unset();
    session_destroy();
}

if (isset($_SESSION['lastaccess']) && (time() - $_SESSION['lastaccess'] > 3600))
{
    session_unset();
    session_destroy();
}
else
{
    $_SESSION['lastaccess'] = time();
}*/