<?php
defined('BASE_PATH') or exit('No direct script access allowed');

define('USER', 'SELECT * FROM `users`');
define('USERBYUSERNAME', 'SELECT * FROM `users` WHERE `username` = ? LIMIT 1');
define('REGISTER', 'INSERT INTO `users` (`username`, `password`, `email`) VALUES (?, ?, ?)');