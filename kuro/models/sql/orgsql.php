<?php
defined('BASE_PATH') or exit('No direct script access allowed');

define('GETORGS', 'SELECT * FROM `organizations`');
define('GETORGBYID', 'SELECT * FROM `organizations` WHERE `orgId` = ? LIMIT 1');
define('GETORGBYNAME', 'SELECT * FROM `organizations` WHERE `orgName` = ? LIMIT 1');
define('GETORGBYAPI', 'SELECT * FROM organizations WHERE apiKey = :apiKey LIMIT 1');
define('ADDORG', 'INSERT INTO organizations (orgName, apiKey) VALUES (:orgName, :apiKey)');
define('UPDATEORG', 'UPDATE organizations SET `orgName` = :orgName, `apiKey` = :apiKey WHERE `orgId` = :orgId');
define('DELETEORG', 'DELETE FROM organizations WHERE orgId = :orgId');