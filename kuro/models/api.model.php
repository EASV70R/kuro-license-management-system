<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../core/Database.php';
require_once __DIR__.'/../models/sql/apisql.php';

class ApiModel extends Database
{
    protected function AuthenticateUser($apiKey, $username, $password): bool|object
    {
        $this->prepare(GETUSERBYAPI);
        $this->statement->bindParam(':apiKey', $apiKey, PDO::PARAM_STR);
        $this->statement->bindParam(':username', $username, PDO::PARAM_STR);
        $this->statement->execute();
        
        $row = $this->fetch();
        return $row && password_verify($password, $row->password) ? $row : false;
    }
}
?>