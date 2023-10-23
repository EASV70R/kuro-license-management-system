<?php
Util::IsLoggedIn();

require_once './kuro/controllers/auth.php';

(new Auth())->Logout();
Util::Redirect('/login');