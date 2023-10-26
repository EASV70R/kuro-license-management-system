<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../core/database.php';
require_once __DIR__.'/../models/sql/orgsql.php';

class OrgModel extends Database
{
    protected function GetOrgs()
    {
        $this->prepare(GETORGS);
        $this->statement->execute();
        return $this->fetchAll();
    }

    protected function GetOrgById($orgId)
    {
        $this->prepare(GETORGBYID);
        $this->statement->execute([$orgId]);
        return $this->fetchAll();
    }

    protected function GetOrgByName($orgName): bool|stdClass
    {
        $this->prepare(GETORGBYNAME);
        $this->statement->execute([$orgName]);
        return $this->fetch();
    }

    protected function GetOrgByAPI($orgApi): bool|stdClass
    {
        $this->prepare(GETORGBYAPI);
        $this->statement->bindParam(':apiKey', $orgApi, PDO::PARAM_INT);
        $this->statement->execute();
        return $this->fetch();
    }

    protected function AddOrg($orgName): bool
    {
        try{
            $this->connect()->beginTransaction();
            $this->prepare(ADDORG);
            $apiKey = bin2hex(random_bytes(15));
            $this->statement->bindParam(':orgName', $orgName, PDO::PARAM_STR);
            $this->statement->bindParam(':apiKey', $apiKey, PDO::PARAM_STR);
            $this->statement->execute();
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            print_r("Error: " . $e->getMessage());
            return false;
        }
    }

    protected function UpdateOrg($orgId, $orgName, $apiKey): bool
    {
        try{
            $this->connect()->beginTransaction();
            $this->prepare(GETORGBYID);
            $this->statement->execute([$orgId]);
            $userById = $this->statement->fetch();
            $this->prepare(UPDATEORG);
            if($apiKey == "on")
                $regenApiKey = bin2hex(random_bytes(15));
            else
                $regenApiKey = $userById->apiKey;
            $this->statement->bindParam(':orgName', $orgName, PDO::PARAM_STR);
            $this->statement->bindParam(':apiKey', $regenApiKey, PDO::PARAM_STR);
            $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
            $this->statement->execute();
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            print_r("Error: " . $e->getMessage());
            return false;
        }
    }

    protected function DeleteOrg($orgId): bool
    {
        try{
            $this->connect()->beginTransaction();
            $this->prepare(DELETEORG);
            $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
            $this->statement->execute();
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            print_r("Error: " . $e->getMessage());
            return false;
        }
    }
}