<?php
defined('BASE_PATH') or exit('No direct script access allowed');

define('GETUSERBYAPI', 'SELECT 
u.userId, 
u.username, 
u.email, 
u.password,
u.status,
r.roleName,
o.orgName
FROM users u 
JOIN organizations o ON u.orgId = o.orgId 
JOIN roles r ON u.roleId = r.roleId
WHERE o.apiKey = :apiKey AND u.username = :username');