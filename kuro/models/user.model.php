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

    public function GetUserById($userId): bool|stdclass
    {
        $this->prepare(USERBYID);
        $this->statement->execute([$userId]);
        return $this->fetch();
    }

    public function GetUsername($username): bool|stdClass
    {
        $this->prepare(USERBYUSERNAME);
        $this->statement->execute([$username]);
        return $this->fetch();
    }

    public function GetEmail($email): bool|stdClass
    {
        $this->prepare(USERBYEMAIL);
        $this->statement->execute([$email]);
        return $this->fetch();
    }

    public function GetAllOrgs()
    {
        $this->prepare(GETALLORGS);
        $this->statement->execute();
        return $this->fetchAll();
    }

    public function RegisterSuperAdmin($username, $hashedPassword, $email, $roleId, $orgId): bool
    {
        try{
            $this->connect()->beginTransaction();
            $this->prepare(REGUSER);
            $this->statement->execute([$username, $hashedPassword, $email, $roleId, $orgId]);
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

    public function EditUser($userId, $username, $password, $email, $roleId, $orgId, $status) : string
    {
        try
        {
            $this->connect()->beginTransaction();
           // $row = $this->GetUserById($userId);
            $this->prepare(USERBYID);
            $this->statement->execute([$userId]);
            $row = $this->statement->fetch();

            if ($row) {
                if($password != null){
                    $this->prepare(EDITUSER);
                    $this->statement->bindParam(':password', $password);
                }else{
                    $this->prepare(EDITUSER2);
                }
                $this->statement->bindParam(':username', $username, PDO::PARAM_STR);
                $this->statement->bindParam(':email', $email, PDO::PARAM_STR);
                $this->statement->bindParam(':userId', $userId, PDO::PARAM_INT);
                $this->statement->bindParam(':roleId', $roleId, PDO::PARAM_INT);
                $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
                $this->statement->bindParam(':mstatus', $status, PDO::PARAM_INT);
                $this->statement->execute();

                $this->commit();

                return 'User updated successfully!';
            }else{
                $this->rollBack();
                return 'User not found';
            }
        } catch (Throwable $error) {
            $this->rollBack();
            print_r("Error: " . $error->getMessage());
        }
    }

    public function DeleteUser($uid) : bool
    {
        try{
            $this->connect()->beginTransaction();
            $this->prepare(DELETEUSER);
            $this->statement->bindParam(':uid', $uid, PDO::PARAM_INT);
            $this->statement->execute();
            $this->commit();
        } catch (Throwable $error) {
            $this->rollBack();
            print_r("Error: " . $error->getMessage());
            return false;
        } finally {
            return true;
        }
    }

    public function BanUser($uid, $status) : bool
    {
        try{
            $this->connect()->beginTransaction();
            $this->prepare(BANUSER);
            $this->statement->bindParam(':banStatus', $status, PDO::PARAM_INT);
            $this->statement->bindParam(':uid', $uid, PDO::PARAM_INT);
            $this->statement->execute();
            $this->commit();
        } catch (Throwable $error) {
            $this->rollBack();
            print_r("Error: " . $error->getMessage());
            return false;
        } finally {
            return true;
        }
    }

    public $limit = 5;

    public function GetTotalRecords()
    {
        $this->prepare(GETTOTALUSERRECORDS);
        $this->statement->execute();
        return $this->fetchColumn();
    }

    public function GetRecords($start)
    {
        $this->prepare(GETUSERRECORDS);
        $this->statement->bindValue(':start', $start, PDO::PARAM_INT);
        $this->statement->bindValue(':limit', $this->limit, PDO::PARAM_INT);
        $this->statement->execute();
        return $this->fetchAll(PDO::FETCH_OBJ);
    }

    public function GetOrgsTotalRecords($orgId)
    {
        $superAdminId = 1;
        $this->prepare('SELECT COUNT(*) as count FROM `users` WHERE `orgId` = :orgId AND `userId` <> :superAdminId');
        $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
        $this->statement->bindParam(':superAdminId', $superAdminId, PDO::PARAM_INT);
        $this->statement->execute();
        return $this->fetchColumn();
    }


    public function GetUsersByOrgs($orgId, $start)
    {
        $superAdminId = 1; // Replace with the actual super admin ID
        $this->prepare('SELECT 
            users.*,
            licenses.licenseKey, 
            licenses.startDate, 
            licenses.expiryDate, 
            licenses.status AS licenseStatus 
        FROM users 
        LEFT JOIN licenses ON users.userId = licenses.userId 
        WHERE users.orgId = :orgId AND users.roleId <> :superAdminId
        ORDER BY users.userId LIMIT :start, :limit');
        $this->statement->bindParam(':orgId', $orgId, PDO::PARAM_INT);
        $this->statement->bindParam(':superAdminId', $superAdminId, PDO::PARAM_INT);
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