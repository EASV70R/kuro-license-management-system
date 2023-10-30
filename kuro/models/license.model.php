<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../core/database.php';
require_once __DIR__.'/../models/sql/licensesql.php';

class LicenseModel extends Database
{
    public function GetAllLicenses()
    {
        $this->prepare(ALL_LICENSES);
        $this->statement->execute();
        return $this->fetchAll();
    }

    public function GetLicenseById($licenseId): ?stdClass
    {
        $this->prepare(LICENSE_BY_ID);
        $this->statement->bindParam(':licenseId', $licenseId);
        $this->statement->execute();
        return $this->fetch();
    }

    public function CreateLicense($licenseKey, $startDate, $expiryDate, $orgId, $createdBy): bool
    {
        try {
            $this->connect()->beginTransaction();
            $this->prepare(ADD_LICENSE);

            $this->statement->bindParam(':licenseKey', $licenseKey);
            $this->statement->bindParam(':startDate', $startDate);
            $this->statement->bindParam(':expiryDate', $expiryDate);
            $this->statement->bindParam(':orgId', $orgId);
            $this->statement->bindParam(':createdBy', $createdBy);

            $this->statement->execute();
            $this->commit();

            return true;
        } catch (Exception $e) {
            $this->rollBack();
            print_r("Error: " . $e->getMessage());
            return false;
        }
    }

    public function EditLicense($licenseId, $licenseKey, $startDate, $expiryDate, $orgId): string
    {
        try {
            $this->connect()->beginTransaction();
            $this->prepare(EDIT_LICENSE);

            $this->statement->bindParam(':licenseId', $licenseId);
            $this->statement->bindParam(':licenseKey', $licenseKey);
            $this->statement->bindParam(':startDate', $startDate);
            $this->statement->bindParam(':expiryDate', $expiryDate);
            $this->statement->bindParam(':orgId', $orgId);

            $this->statement->execute();
            $this->commit();

            return 'License updated successfully!';
        } catch (Throwable $error) {
            $this->rollBack();
            print_r("Error: " . $error->getMessage());
            return 'Failed to update license.';
        }
    }

    public function DeleteLicense($licenseId): bool
    {
        try {
            $this->connect()->beginTransaction();
            $this->prepare(DELETE_LICENSE);

            $this->statement->bindParam(':licenseId', $licenseId);
            $this->statement->execute();

            $this->commit();
            return true;
        } catch (Throwable $error) {
            $this->rollBack();
            print_r("Error: " . $error->getMessage());
            return false;
        }
    }

    public function AssignLicenseToUser($licenseKey, $userId): bool
    {
        try {
            $this->connect()->beginTransaction();

            $this->prepare(ASSIGN_LICENSE_TO_USER);

            $this->statement->bindParam(':licenseKey', $licenseKey);
            $this->statement->bindParam(':userId', $userId);

            $this->statement->execute();
            $this->commit();

            return true;
        } catch (Throwable $error) {
            $this->rollBack();
            print_r("Error: " . $error->getMessage());
            return false;
        }
    }

    private function LogLicenseUsage($licenseId, $userId, $action): void
    {
        $this->prepare(LOG_LICENSE_USAGE);

        $this->statement->bindParam(':licenseId', $licenseId);
        $this->statement->bindParam(':userId', $userId);
        $this->statement->bindParam(':action', $action);
        $timestamp = date("Y-m-d H:i:s");  // Current timestamp
        $this->statement->bindParam(':timestamp', $timestamp);

        $this->statement->execute();
    }

    public $limit = 5;

    public function GetTotalRecords()
    {
        $this->prepare(GETTOTALLICENSERECORDS);
        $this->statement->execute();
        return $this->fetchColumn();
    }

    public function GetRecords($start)
    {
        $this->prepare(GETLICENSERECORDS);
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
