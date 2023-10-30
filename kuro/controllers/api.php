<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../models/api.model.php';
require_once __DIR__.'/../controllers/license.php';
require_once __DIR__.'/../utils/session.php';
require_once __DIR__.'/../utils/validator.php';

class Api extends ApiModel
{
    public function UserLogin($apiKey, $username, $password): array
    {
        $licenseController = new LicenseController();
        $userData = $this->AuthenticateUser($apiKey, $username, $password);

        if ($userData) {
            unset($userData->password);
            if($userData->status == 0)
            {
                $userId = $userData->userId;
                $license = $licenseController->GetLicenseByUserId($userId);
                if(!$license)
                {
                    return [
                        'status' => 'error',
                        'message' => 'User does not have a license.'
                    ];
                }
                if($license->status == 1)
                {
                    if($license->expiryDate < date("Y-m-d"))
                    {
                        return [
                            'status' => 'error',
                            'message' => 'User license has expired.'
                        ];
                    }else{
                        return [
                            'status' => 'success',
                            'message' => 'User authenticated successfully.',
                            'data' => $userData
                        ];
                    }
                }
                else
                {
                    return [
                        'status' => 'error',
                        'message' => 'User license is not active.'
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => 'User is banned.'
                ];
            }
            
        } else {
            return [
                'status' => 'error',
                'message' => 'Invalid username or password.'
            ];
        }
    }

    public function GetUserIdByUsername($username): int {
        return $this->UserIdByUsername($username);
    }
}
?>