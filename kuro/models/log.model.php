<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../core/Database.php';
require_once __DIR__.'/../models/sql/logsql.php';

class LogModel extends Database
{
    protected function InsertLog($userId, $orgId, $status, $ipAddress)
    {
        $this->prepare("INSERT INTO login_logs (userId, orgId, status, ipAddress) VALUES (:userId, :orgId, :status, :ipAddress)");
        $this->statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
        $this->statement->bindParam(':status', $status, PDO::PARAM_INT);
        $this->statement->bindParam(':ipAddress', $ipAddress, PDO::PARAM_STR);
        $this->statement->execute();
        $this->close();
    }

    protected function AllLogs(): array{
        //$this->prepare("SELECT login_logs.*, users.username FROM login_logs JOIN users ON login_logs.userId = users.userId ORDER BY login_logs.timestamp DESC");
        $this->prepare('SELECT login_logs.*, users.username 
        FROM login_logs 
        LEFT JOIN users ON login_logs.userId = users.userId 
        ORDER BY login_logs.timestamp DESC');
        $this->statement->execute();
        return $this->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function LogsByOrgId($orgId): array {
        $this->prepare("SELECT login_logs.*, users.username FROM login_logs JOIN users ON login_logs.userId = users.userId WHERE login_logs.orgId = :orgId ORDER BY login_logs.timestamp DESC");
        $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
        $this->statement->execute();
        return $this->fetchAll(PDO::FETCH_ASSOC);
    }


    protected $limit = 10;

    public function TotalRecords($roleId, $orgId = null,)
    {
        $sql = "SELECT COUNT(*) as count FROM login_logs";
        if ($roleId == 2) { // Organization Admin
            $sql .= " WHERE orgId = :orgId";
        }
        $this->prepare($sql);
        if ($roleId == 2) { // Organization Admin
            $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
        }
        $this->statement->execute();
        return $this->fetchColumn();
    }

    public function UserLogs($roleId, $orgId = null, $start)
    {
        $sql = "SELECT login_logs.*, users.username FROM login_logs LEFT JOIN users ON login_logs.userId = users.userId";
        if ($roleId == 2) { // Organization Admin
            $sql .= " WHERE login_logs.orgId = :orgId";
        }
        $sql .= " ORDER BY timestamp DESC LIMIT :start, :limit";
        $this->prepare($sql);
        if ($roleId == 2) { // Organization Admin
            $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
        }
        
        $this->statement->bindValue(':start', $start, PDO::PARAM_INT);
        $this->statement->bindValue(':limit', $this->limit, PDO::PARAM_INT);
        $this->statement->execute();
        return $this->fetchAll(PDO::FETCH_OBJ);
    }

    public function GetLimit()
    {
        return $this->limit;
    }
}
?>