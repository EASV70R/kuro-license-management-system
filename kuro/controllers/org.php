<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../models/org.model.php';

class Organzation extends OrgModel
{
    public function GetAllOrgs(): array
    {
        return $this->GetOrgs();
    }

    public function GetOrganizationById($orgId): array
    {
        return $this->GetOrgById($orgId);
    }

    public function GetOrganizationByName($orgName): bool|stdClass
    {
        return $this->GetOrgByName($orgName);
    }

    public function GetOrganizationByAPI($orgApi): bool|stdClass
    {
        return $this->GetOrgByAPI($orgApi);
    }

    public function AddOrganization($data): null|string
    {
        try{
            $orgName = trim($data['orgName']);

            $orgExist = $this->GetOrganizationByName($orgName);
            if ($orgExist) {
               return "Organization already exists.";
            }

            $response = $this->AddOrg($orgName);
            if ($response) {
                return 'Organization added.';
             } else {
                return 'Organization failed.';
            }
        } catch (Throwable $error) {
             return 'Organization failed.';
        }
    }

    public function UpdateOrganization($data): null|string
    {
        try{
            $orgName = trim($data['mName']);
            if(isset($data['mRegenApi']))
            {
                $apiKey = (string)$data['mRegenApi'];
            } else {
                $apiKey = (string)'off';
            }
            $orgId = (int)$data['orgId'];
            $response = $this->UpdateOrg($orgId, $orgName, $apiKey);
            if ($response) {
                return 'Organization updated.';
            } else {
                return 'Organization failed.';
            }
        } catch (Throwable $error) {
            return 'Organization failed.';
        }
    }

    public function DeleteOrganization($data): null|string
    {
        $orgId = (int)$data['orgId'];
        $response = $this->DeleteOrg($orgId);
        return ($response) ? 'Organization deleted.' : 'Organization delete failed.';
    }
}

$org = new Organzation();
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(isset($_POST['add']))
    {
        $response = $org->AddOrganization($_POST);
    }
    if(isset($_POST['edit']))
    {
        $response = $org->UpdateOrganization($_POST);
    }
    if(isset($_POST['delete']))
    {
        $response = $org->DeleteOrganization($_POST);
    }
}