<?php
defined('BASE_PATH') or exit('No direct script access allowed');

define('GETUSERBYAPI', 'SELECT u.userId, u.username, u.email, u.password 
FROM users u 
JOIN organizations o ON u.orgId = o.orgId 
WHERE o.apiKey = :apiKey AND u.username = :username');