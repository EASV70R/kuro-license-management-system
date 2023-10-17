<?php
defined('BASE_PATH') or exit('No direct script access allowed');

class Util
{
    public static function Header(): void
    {
        include(SITE_ROOT.'/views/includes/header.inc.php');
    }

    public static function Navbar(): void
    {
        include(SITE_ROOT.'/views/includes/navbar.inc.php');
    }

    public static function Footer(): void
    {
        include(SITE_ROOT.'/views/includes/footer.inc.php');
    }
}