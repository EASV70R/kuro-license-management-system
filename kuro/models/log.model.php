<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../core/database.php';
require_once __DIR__.'/../models/sql/logsql.php';

class LogModel extends Database
{
    protected function AddLicenseLog($userId, $action)
    {
        $this->prepare("INSERT INTO license_logs (licenseId, action, actionBy) VALUES (:licenseId, :action, :actionBy)");
        $this->statement->bindParam(':licenseId', $userId, PDO::PARAM_INT);
        $this->statement->bindParam(':action', $action, PDO::PARAM_STR);
        $this->statement->bindParam(':actionBy', $userId, PDO::PARAM_INT);
        $this->statement->execute();
        $this->close();
    }

    protected function InsertLog($userId, $orgId, $status, $ipAddress, $apiKey)
    {
        $this->prepare("INSERT INTO login_logs (userId, orgId, status, ipAddress, apiKeyUsed) VALUES (:userId, :orgId, :status, :ipAddress, :apiKey)");
        $this->statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
        $this->statement->bindParam(':status', $status, PDO::PARAM_INT);
        $this->statement->bindParam(':ipAddress', $ipAddress, PDO::PARAM_STR);
        $this->statement->bindParam(':apiKey', $apiKey, PDO::PARAM_STR);
        $this->statement->execute();
        $this->close();
    }

    protected function AllLogs(): array{
        //$this->prepare("SELECT login_logs.*, users.username FROM login_logs JOIN users ON login_logs.userId = users.userId ORDER BY login_logs.timestamp DESC");
        $this->prepare('SELECT login_logs.*, users.username 
        FROM login_logs 
        LEFT JOIN users ON login_logs.userId = users.userId 
        ORDER BY login_logs.createdAt DESC');
        $this->statement->execute();
        return $this->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function LogsByOrgId($orgId): array {
        $this->prepare("SELECT login_logs.*, users.username FROM login_logs JOIN users ON login_logs.userId = users.userId WHERE login_logs.orgId = :orgId ORDER BY login_logs.createdAt DESC");
        $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
        $this->statement->execute();
        return $this->fetchAll(PDO::FETCH_ASSOC);
    }


    protected $limit = 10;

    protected function TotalRecords($roleId, $orgId = null,)
    {
        $sql = "SELECT COUNT(*) as count FROM login_logs";
        $apiKey = $this->GetAPIKeyForOrg($orgId);
        if ($roleId == 2) { // Organization Admin
            $sql .= " WHERE (orgId = :orgId OR apiKeyUsed = :apiKeyUsed)";
        }
        $this->prepare($sql);
        if ($roleId == 2) { // Organization Admin
            $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
            $this->statement->bindParam(':apiKeyUsed', $apiKey, PDO::PARAM_STR);
        }
        $this->statement->execute();
        return $this->fetchColumn();
    }

    protected function UserLogs($start, $roleId, $orgId = null)
    {
        $sql = "SELECT login_logs.*, users.username, users.status AS ban, organizations.apiKey 
            FROM login_logs 
            LEFT JOIN users ON login_logs.userId = users.userId 
            LEFT JOIN organizations ON login_logs.orgId = organizations.orgId";
            $apiKey = $this->GetAPIKeyForOrg($orgId);
        if ($roleId == 2) { // Organization Admin
            $sql .= " WHERE (login_logs.orgId = :orgId OR login_logs.apiKeyUsed = :apiKeyUsed)";
        }
        $sql .= " ORDER BY createdAt DESC LIMIT :start, :limit";
        $this->prepare($sql);
        if ($roleId == 2) { // Organization Admin
            $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
            $this->statement->bindParam(':apiKeyUsed', $apiKey, PDO::PARAM_STR);
        }
        
        $this->statement->bindValue(':start', $start, PDO::PARAM_INT);
        $this->statement->bindValue(':limit', $this->limit, PDO::PARAM_INT);
        $this->statement->execute();
        return $this->fetchAll(PDO::FETCH_OBJ);
    }

    protected function GetAPIKeyForOrg($orgId) {
        $sql = "SELECT apiKey FROM organizations WHERE orgId = :orgId LIMIT 1";
        $this->prepare($sql);
        $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
        $this->statement->execute();
        $apiKey = $this->statement->fetch(PDO::FETCH_COLUMN);  // Store in a variable
        return $apiKey;  // Return the stored variable
    }

    protected function GetLimit()
    {
        return $this->limit;
    }
}
?>