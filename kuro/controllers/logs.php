<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../models/log.model.php';

class Logs extends LogModel
{
    public function AddLog($userId, $orgId, $status, $apiKey)
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $this->InsertLog($userId, $orgId, $status, $ipAddress, $apiKey);
    }

    public function ShowLogs($roleId, $orgId): array
    {
        if ($roleId == 1) { // Super Admin
            $logs = $this->AllLogs();
            return $logs;
        } elseif ($roleId == 2) { // Organization Admin
            $logs = $this->LogsByOrgId($orgId);
            return $logs;
        } else {
            return "You do not have permission to view logs.";
        }
    }
    public function GetPaginationData($roleId, $orgId)
    {
        $logModel = new LogModel();
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
        $start = ($page - 1) * $logModel->GetLimit();
        $logs = $logModel->UserLogs($start, $roleId, $orgId);
        $totalRecords = $logModel->TotalRecords($roleId, $orgId);
        
        return [
            'logs' => $logs,
            'totalRecords' => $totalRecords,
            'limit' => $logModel->GetLimit()
        ];
    }
}
?>