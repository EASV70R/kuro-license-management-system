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

    public function GetLicenseById($licenseId): stdClass
    {
        $licenseModel = new LicenseModel();
        return $licenseModel->GetLicenseById($licenseId);
    }

    public function CreateLicense($data): string
    {
        try {
            $licenseModel = new LicenseModel();

            $licenseKey = bin2hex(random_bytes(8));
            $startDate = NULL;
            $expiryDate = NULL;
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

            $licenseId = (int) $data['licenseId'];
            $licenseKey = trim($data['licenseKey']);
            $startDate = $data['startDate'];
            $expiryDate = $data['expiryDate'];
            $orgId = (int) $data['orgId'];

            $response = $licenseModel->EditLicense($licenseId, $licenseKey, $startDate, $expiryDate, $orgId);

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
        var_dump($response);
        var_dump($_POST);
    }
}