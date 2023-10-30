<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../models/license.model.php';
require_once __DIR__.'/../utils/session.php';
require_once __DIR__.'/../utils/validator.php';

class LicenseController
{
    public function GetAllLicenses(): array
    {
        $licenseModel = new LicenseModel();
        return $licenseModel->GetAllLicenses();
    }

    public function GetLicenseById($licenseId): bool|stdclass
    {
        $licenseModel = new LicenseModel();
        return $licenseModel->GetLicenseById($licenseId);
    }

    public function GetLicenseByKey($licenseKey): bool|stdclass
    {
        $licenseModel = new LicenseModel();
        return $licenseModel->GetLicenseByKey($licenseKey);
    }

    public function GetLicenseByUserId($userId): bool|stdclass
    {
        $licenseModel = new LicenseModel();
        return $licenseModel->GetLicenseByUserId($userId);
    }

    public function CreateLicense($data): string
    {
        try {
            $licenseModel = new LicenseModel();

            $licenseKey = bin2hex(random_bytes(8));
            $startDate = date("Y-m-d");
            $expiryDate = date("Y-m-d");
            $orgId = (int)Session::Get('orgId');
            $createdBy = (int)Session::Get('username');


            $response = $licenseModel->CreateLicense($licenseKey, $startDate, $expiryDate, $orgId, $createdBy);

            return ($response) ? 'License created successfully.' : 'Failed to create license.';
        } catch (Throwable $error) {
            return 'Failed to create license.';
        }
    }

    public function EditLicense($data): string
    {
        try {
            $licenseModel = new LicenseModel();

            $userId = (int) $data['editLicenseUserId'];
            $startDate = $data['licenseStartDate'];
            $expiryDate = $data['licenseEndDate'];
            $startDate1 = strtotime($startDate);
            $expiryDate1 = strtotime($expiryDate);
            $startDate2 = date('Y-m-d h:i:s', $startDate1);
            $expiryDate2 = date('Y-m-d h:i:s', $expiryDate1);
            if (isset($data['licenseStatus']) && $data['licenseStatus'] == "on") {
                $status = (int)$data['licenseStatus'];
                $status = 1; // Active
            } else {
                $status = 0; // Inactive
            }
            $loggedInRole = Session::Get("roleId");
            $orgId = $licenseModel->GetLicenseByUserId($userId)->orgId;
            if (Session::isSuperAdmin($loggedInRole)) {
                // No additional checks needed
            } elseif (Session::isOrgAdmin($loggedInRole)) {
                if (($loggedInRole < 2 || $loggedInRole > 3) || $orgId != Session::Get("orgId")) {
                    return "Insufficient permissions.";
                }
            } else {
                return 'Insufficient permissions.';
            }

            $response = $licenseModel->EditLicense($userId, $startDate2, $expiryDate2, $status);

            return ($response) ? 'License updated successfully.' : 'Failed to update license.';
        } catch (Throwable $error) {
            return 'Failed to update license.';
        }
    }

    public function DeleteLicense($data): string
    {
        try {
            $licenseModel = new LicenseModel();
            $licenseId = (int) $data['licenseId'];
            $loggedInRole = Session::Get("roleId");
            $orgId = $licenseModel->GetLicenseById($licenseId)->orgId;
            if (Session::isSuperAdmin($loggedInRole)) {
                // No additional checks needed
            } elseif (Session::isOrgAdmin($loggedInRole)) {
                if (($loggedInRole < 2 || $loggedInRole > 3) || $orgId != Session::Get("orgId")) {
                    return "Insufficient permissions.";
                }
            } else {
                return 'Insufficient permissions.';
            }

            $response = $licenseModel->DeleteLicense($licenseId);

            return ($response) ? 'License deleted successfully.' : 'Failed to delete license.';
        } catch (Throwable $error) {
            return 'Failed to delete license.';
        }
    }

    public function AssignLicense($data): string
    {
    try {
        $licenseModel = new LicenseModel();
        
        $licenseKey = trim($data['licenseKey']);
        $userId = (int)$data['assignLicenseUserId'];
        $loggedInRole = Session::Get("roleId");
        
        $keyInfo = $licenseModel->GetLicenseByKey($licenseKey);
        var_dump($keyInfo);
        if($keyInfo == false){
            return 'License key does not exist.';
        }elseif($keyInfo->activatedByUserId != $userId && $keyInfo->activatedByUserId != null){
            return 'License key is used.';
        }

        $orgId = $licenseModel->GetLicenseByKey($licenseKey)->orgId;
        if (Session::isSuperAdmin($loggedInRole)) {
            // No additional checks needed
        } elseif (Session::isOrgAdmin($loggedInRole)) {
            if (($loggedInRole < 2 || $loggedInRole > 3) || $orgId != Session::Get("orgId")) {
                return "Insufficient permissions.";
            }
        } else {
            return 'Insufficient permissions.';
        }
        $response = $licenseModel->AssignLicenseToUser($licenseKey, $userId);

        return ($response) ? 'License assigned to user successfully.' : 'Failed to assign license.';
    } catch (Throwable $error) {
        return 'Failed to assign license.';
        }
    }

    public function GetPaginationData()
    {
        $licenseModel = new LicenseModel();
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
        $start = ($page - 1) * $licenseModel->GetLimit();
        $licenses = $licenseModel->GetRecords($start);
        $totalRecords = $licenseModel->GetTotalRecords();

        return [
            'licenses' => $licenses,
            'totalRecords' => $totalRecords,
            'limit' => $licenseModel->GetLimit()
        ];
    }

    public function GetOrgPaginationData($orgId)
    {
        $licenseModel = new LicenseModel();
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
        $start = ($page - 1) * $licenseModel->GetLimit();
        $licenses = $licenseModel->GetOrgsRecords($start);
        $totalRecords = $licenseModel->GetOrgsTotalRecords();

        return [
            'licenses' => $licenses,
            'totalRecords' => $totalRecords,
            'limit' => $licenseModel->GetLimit()
        ];
    }
}

$licenseController = new LicenseController();

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (isset($_POST['createLicense'])) {
        $response = $licenseController->CreateLicense($_POST);
    }
    if (isset($_POST['editLicense'])) {
        $response = $licenseController->EditLicense($_POST);
    }
    if (isset($_POST['deleteLicense'])) {
        $response = $licenseController->DeleteLicense($_POST);
    }
    if (isset($_POST['assignLicense'])) {
        $response = $licenseController->AssignLicense($_POST);
    }
}