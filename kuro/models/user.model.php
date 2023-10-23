<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../core/database.php';
require_once __DIR__.'/../models/sql/usersql.php';

class UserModel extends Database
{
    public function GetUsers()
    {
        $this->prepare(USER);
        $this->statement->execute();
        return $this->fetchAll();
    }

    public function GetUsername($username): bool|stdClass
    {
        $this->prepare(USERBYUSERNAME);
        $this->statement->execute([$username]);
        return $this->fetch();
    }

    public function Register($username, $hashedPassword, $email): bool
    {
        try{
            $this->connect()->beginTransaction();
            $this->prepare(REGISTER);
            $this->statement->execute([$username, $hashedPassword, $email]);
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            print_r("Error: " . $e->getMessage());
            return false;
        }
    }

    public function Login($username, $password): bool|object
    {
        $row = $this->GetUsername($username);
        return $row && password_verify($password, $row->password) ? $row : false;
    }
}