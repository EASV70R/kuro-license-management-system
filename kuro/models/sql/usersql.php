<?php
defined('BASE_PATH') or exit('No direct script access allowed');

define('USER', 'SELECT * FROM `users`');
define('USERBYUSERNAME', 'SELECT * FROM `users` WHERE `username` = ? LIMIT 1');
define('USERBYEMAIL', 'SELECT * FROM `users` WHERE `email` = ? LIMIT 1');
define('SUPERADMINREG', 'INSERT INTO `users` (`username`, `password`, `email`, `roleId`) VALUES (?, ?, ?, ?)');
define('ORGANIZATIONREG', 'INSERT INTO `users` (`username`, `password`, `email`, `roleId`, `orgId`) VALUES (?, ?, ?, ?, ?)');
define('REGUSER', 'INSERT INTO `users` (`username`, `password`, `email`, `roleId`, `orgId`) VALUES (?, ?, ?, ?, ?)');
define('GETALLORGS', 'SELECT * FROM `organizations`');