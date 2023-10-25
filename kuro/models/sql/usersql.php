<?php
defined('BASE_PATH') or exit('No direct script access allowed');

define('USER', 'SELECT * FROM `users`');
define('USERBYID', 'SELECT * FROM `users` WHERE `userId` = ? LIMIT 1');
define('USERBYUSERNAME', 'SELECT * FROM `users` WHERE `username` = ? LIMIT 1');
define('USERBYEMAIL', 'SELECT * FROM `users` WHERE `email` = ? LIMIT 1');
define('SUPERADMINREG', 'INSERT INTO `users` (`username`, `password`, `email`, `roleId`) VALUES (?, ?, ?, ?)');
define('ORGANIZATIONREG', 'INSERT INTO `users` (`username`, `password`, `email`, `roleId`, `orgId`) VALUES (?, ?, ?, ?, ?)');
define('REGUSER', 'INSERT INTO `users` (`username`, `password`, `email`, `roleId`, `orgId`) VALUES (?, ?, ?, ?, ?)');
define('EDITUSER', 'UPDATE `users` SET `username` = :username, `password` = :password, `email` = :email , `roleId` = :roleId, `orgId` = :orgId, `status` = :mstatus WHERE `userId` = :userId');
define('EDITUSER2', 'UPDATE `users` SET `username` = :username, `email` = :email, `roleId` = :roleId, `orgId` = :orgId, `status` = :mstatus WHERE `userId` = :userId');
define('DELETEUSER', 'DELETE FROM `users` WHERE `userId` = :uid');
define('GETALLORGS', 'SELECT * FROM `organizations`');
define('GETTOTALUSERRECORDS', 'SELECT COUNT(*) FROM `users`');
define('GETUSERRECORDS', 'SELECT * FROM users LIMIT :start, :limit');