<?php
defined('BASE_PATH') or exit('No direct script access allowed');

define('ALL_LICENSES', 'SELECT 
l.licenseId,
l.licenseKey,
l.startDate,
l.expiryDate,
l.orgId,
o.orgName,
l.createdBy,
u.username AS creatorName
FROM licenses l
JOIN organizations o ON l.orgId = o.orgId
LEFT JOIN users u ON l.createdBy = u.userId');

define('LICENSE_BY_ID', 'SELECT 
l.licenseId,
l.licenseKey,
l.startDate,
l.expiryDate,
l.orgId,
o.orgName,
l.createdBy,
u.username AS creatorName,
l.userId AS activatedByUserId,
u2.username AS activatedByUsername
FROM licenses l
JOIN organizations o ON l.orgId = o.orgId
LEFT JOIN users u ON l.createdBy = u.userId
LEFT JOIN users u2 ON l.userId = u2.userId
WHERE l.licenseId = :licenseId');

define('ADD_LICENSE', 'INSERT INTO licenses 
(licenseKey, startDate, expiryDate, orgId, createdBy) 
VALUES 
(:licenseKey, :startDate, :expiryDate, :orgId, :createdBy)');

define('EDIT_LICENSE', 'UPDATE licenses 
SET 
licenseKey = :licenseKey,
startDate = :startDate,
expiryDate = :expiryDate,
orgId = :orgId
WHERE 
licenseId = :licenseId');

define('DELETE_LICENSE', 'DELETE FROM licenses 
WHERE 
licenseId = :licenseId');

define('ASSIGN_LICENSE_TO_USER', '
    UPDATE licenses
    SET userId = :userId
    WHERE licenseKey = :licenseKey AND status = 0
');

define('LOG_LICENSE_USAGE', 'INSERT INTO license_logs 
(licenseId, userId, action, timestamp) 
VALUES 
(:licenseId, :userId, :action, :timestamp)');

define('GETTOTALLICENSERECORDS', 'SELECT COUNT(*) FROM licenses');
define('GETLICENSERECORDS', 'SELECT * FROM licenses LIMIT :start, :limit');

?>