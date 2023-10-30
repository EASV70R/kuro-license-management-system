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

    protected function UserIdByUsername($username): int {
        $this->prepare('SELECT userId FROM users WHERE username = :username');
        $this->statement->bindParam(':username', $username, PDO::PARAM_STR);
        $this->statement->execute();
        $result = $this->fetch();

        if ($result !== false && property_exists($result, 'userId')) {
            return (int)$result->userId;
        } else {
            return -1; // Return a default value if no result or 'userId' property is found
        }
    }
    
    public function CheckUserLicense($userId)
    {
        $this->prepare("SELECT * FROM licenses WHERE userId = :userId AND status = 'active' AND CURDATE() <= expirationDate");
        $this->statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $this->statement->execute();
    
        $license = $this->fetch(PDO::FETCH_OBJ);

        return $license;
    }
}
?>