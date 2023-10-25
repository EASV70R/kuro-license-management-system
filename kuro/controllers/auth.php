<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../models/user.model.php';
require_once __DIR__.'/../utils/session.php';
require_once __DIR__.'/../utils/validator.php';

class Auth
{
    public function GetAllUsers(): array
    {
        $user = new UserModel();
        return $user->GetUsers();
    }

    public function GetUserById($userId): stdclass
    {
        $user = new UserModel();
        return $user->GetUserById($userId);
    }

    public function GetAllOrgs(): array
    {
        $user = new UserModel();
        return $user->GetAllOrgs();
    }

    public function GetOrgName(int $orgId): null|string
    {
        $orgs = $this->GetAllOrgs();
        foreach ($orgs as $org) {
            if ($org->orgId == $orgId) {
               
                return $org->orgName;
            }
        }
        return 'Unknown';
    }

    public function Register($data): null|string
    {
        try {
            $user = new UserModel();

            $username = trim($data['username']);
            $password = (string)$data['generatedPassword'];
            //$password = (string) $data['password'];
            //$confirmPassword = (string) $data['confirmPassword'];
            $email = (string) $data['email'];
            $roleId = (int) $data['roleId'];
            $orgId = (int) $data['orgId'];
            
            $loggedInRoleId = Session::Get("roleId");
            $loggedInOrgId = Session::Get("orgId");
            
            if (Session::isSuperAdmin($loggedInRoleId)) {
                // Super Admin can assign any role to any org.
            } elseif (Session::isOrgAdmin($loggedInRoleId)) {
                if ($roleId < 2 || $roleId > 3 ||$orgId != $loggedInOrgId) {
                    // Org Admin can only assign Org Admins (2) or regular users (3) under their orgId.
                    return "Insufficient permissions to assign this role or org.";
                }
            } else {
                return "Insufficient permissions.";
            }

            $userExists = $user->GetUsername($username);
            if ($userExists) {
                return "Username already exists.";
            }
            
            $emailExists = $user->GetEmail($email);
            if ($emailExists) {
                return "Email already exists.";
            }
            
            $validationError = Validator::RegisterFormAdm($username, $password, $password, $email, $roleId, $orgId);
            if ($validationError) {
                return $validationError;
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $response = $user->RegisterSuperAdmin($username, $hashedPassword, $email, $roleId, $orgId);
            if ($response) {
                return 'Registration successful.';
            } else {
                return 'Registration failed.';
            }
        } catch (Throwable $error) {
            return 'Registration failed.';
        } finally {

        }
    }


    public function Login($data): null|string
    {
        $user = new UserModel();

        $username = trim($data['username']);
        $password = (string) $data['password'];

        $validationError = Validator::LoginForm($username, $password);
        if ($validationError) {
            return $validationError;
        }

        $response = $user->Login($username, $password);
        if ($response) {
            if($response->status == 0){
                if($response->roleId == 1 || $response->roleId == 2){
                    Session::CreateUserSession($response);
                    Util::Redirect('/admin/admin');
                    return ($response) ? 'Login successful.' : 'Login failed.';
                }else{
                    return 'User is not an admin.';
                }
            }else{
                return 'User is banned.';
            }
        } else {
            return 'Invalid username or password.';
        }
    }

    public function Logout()
    {
        session_unset();
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public function EditUser($data): null|string
    {
        $user = new UserModel();

        $username = trim($data['mUsername']);
        if( isset($data['mPassword']) ){
            $password = (string) $data['mPassword'];
        }
        $email = (string) $data['mEmail'];
        $roleId = (int) $data['mRoleId'];
        $orgId = (int) $data['mOrgId'];
        $userId = (int) $data['userId'];
        if (isset($data['mStatus']) && $data['mStatus'] == "on") {
            $status = (int)$data['mStatus'];
            $status = 1; // User is banned
        } else {
            $status = 0; // User is not banned
        }
        $loggedInRole = Session::Get("roleId");

        if($orgId == 0){
            $orgId = $user->GetUserById($userId)->orgId;
        }
        if($roleId == 0){
            $roleId = $user->GetUserById($userId)->roleId;
        }

        if (Session::isSuperAdmin($loggedInRole)) {
            // No additional checks needed
        } elseif (Session::isOrgAdmin($loggedInRole)) {
            if (($roleId < 2 || $roleId > 3) || $orgId != Session::Get("orgId")) {
                // Org Admin can only assign Org Admins (2) or regular users (3) under their orgId.
                return "Insufficient permissions to assign this role or org.";
            }
        } else {
            return 'Insufficient permissions.';
        }

        $validationError = Validator::EditUserForm($username, $email);
        if ($validationError) {
            return $validationError;
        }
        if( isset($data['mPassword'])){
            if($data['mPassword'] != '')
            {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            }else{
                $hashedPassword = null;
            }
        }

        $response = $user->EditUser($userId, $username, $hashedPassword, $email, $roleId, $orgId, $status);

        return ($response) ? 'User edited successfully.' : 'User edit failed.';
    }

    public function DeleteUser($data): null|string
    {
        $user = new UserModel();
        $uid = (int)$data['uid'];
        $response = $user->DeleteUser($uid);
        return ($response) ? 'User deleted.' : 'User delete failed.';
    }

    public function GetPaginationData()
    {
        $user = new UserModel();
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
        $start = ($page - 1) * $user->GetLimit();
        $users = $user->GetRecords($start);
        $totalRecords = $user->GetTotalRecords();

        return [
            'users' => $users,
            'totalRecords' => $totalRecords,
            'limit' => $user->GetLimit()
        ];
    }

    public function GetOrgsPaginationDate($orgId)
    {
        $user = new UserModel();
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
        $start = ($page - 1) * $user->GetLimit();
        $users = $user->GetUsersByOrgs($orgId, $start);
        $totalRecords = $user->GetOrgsTotalRecords($orgId);

        return [
            'users' => $users,
            'totalRecords' => $totalRecords,
            'limit' => $user->GetLimit()
        ];
    }
}
$auth = new Auth();
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(isset($_POST['login']))
    {
        $response = $auth->Login($_POST);
    }  
    if(isset($_POST['registerSuperAdmin']))
    {
        $response = $auth->Register($_POST);
    }
    if(isset($_POST['edit']))
    {
        $response = $auth->EditUser($_POST);
        var_dump($_POST);
    }
    if(isset($_POST['delete']))
    {
        $response = $auth->DeleteUser($_POST);
        Util::Refresh();
    }
}