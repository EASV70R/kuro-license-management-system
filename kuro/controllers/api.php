<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../models/api.model.php';
require_once __DIR__.'/../utils/session.php';
require_once __DIR__.'/../utils/validator.php';

class Api extends ApiModel
{
    public function UserLogin($apiKey, $username, $password): array
    {
        $userData = $this->AuthenticateUser($apiKey, $username, $password);

        if ($userData) {
            return [
                'status' => 'success',
                'message' => 'User authenticated successfully.',
                'data' => $userData
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Invalid username or password.'
            ];
        }
    }
}
?>