<?php
defined('BASE_PATH') or exit('No direct script access allowed');

require_once __DIR__.'/../models/user.model.php';
require_once __DIR__.'/../utils/session.php';
require_once __DIR__.'/../utils/validator.php';

class Auth
{
    public function GetAllUsers(): array
    {
        $User = new UserModel();
        return $User->GetUsers();
    }

    public function GetUserById($userId): stdclass
    {
        $User = new UserModel();
        return $User->GetUserById($userId);
    }

    public function GetAllOrgs(): array
    {
        $User = new UserModel();
        return $User->GetAllOrgs();
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
            $User = new UserModel();

            $username = trim($data['username']);
            $password = (string) $data['password'];
            $confirmPassword = (string) $data['confirmPassword'];
            $email = (string) $data['email'];
            $roleId = (int) $data['roleId'];
            $orgId = (int) $data['orgId'];
            
            $loggedInRoleId = Session::Get("roleId");
            $loggedInOrgId = Session::Get("orgId");
            
            if (Session::isSuperAdmin($loggedInRoleId)) {
                // Super Admin can assign any role to any org.
            } elseif (Session::isOrgAdmin($loggedInRoleId)) {
                if ($roleId < 2 || $orgId != $loggedInOrgId) {
                    // Org Admin can only assign Org Admins (2) or regular users (3) under their orgId.
                    return "Insufficient permissions to assign this role or org.";
                }
            } else {
                return "Insufficient permissions.";
            }

            $userExists = $User->GetUsername($username);
            if ($userExists) {
                return "Username already exists.";
            }
            
            $emailExists = $User->GetEmail($email);
            if ($emailExists) {
                return "Email already exists.";
            }
            
            $validationError = Validator::RegisterFormAdm($username, $password, $confirmPassword, $email, $roleId, $orgId);
            if ($validationError) {
                return $validationError;
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $response = $User->RegisterSuperAdmin($username, $hashedPassword, $email, $roleId, $orgId);
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
        $User = new UserModel();

        $username = trim($data['username']);
        $password = (string) $data['password'];

        $validationError = Validator::LoginForm($username, $password);
        if ($validationError) {
            return $validationError;
        }

        $response = $User->Login($username, $password);
        if ($response) {
            Session::CreateUserSession($response);
            Util::Redirect('/admin/admin');
            return ($response) ? 'Login successful.' : 'Login failed.';
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
        $User = new UserModel();

        $username = trim($data['mUsername']);
        if( isset($data['mPassword']) ){
            $password = (string) $data['mPassword'];
        }
        $email = (string) $data['mEmail'];
        $roleId = (int) $data['mRoleId'];
        $orgId = (int) $data['mOrgId'];
        $userId = (int) $data['userId'];

        $loggedInRole = Session::Get("roleId");

        if (!Session::isSuperAdmin($loggedInRole)) {
            if($orgId == 0){
                $orgId = $User->GetUserById($userId)->orgId;
            }
        }

        if (Session::isSuperAdmin($loggedInRole)) {
            // No additional checks needed
        } elseif (Session::isOrgAdmin($loggedInRole)) {
            if ($roleId == 1) {
                return 'Insufficient permissions.';
            }

            if ($orgId != Session::Get("orgId")) {
                return 'Insufficient permissions.';
            }
        } else {
            return 'Insufficient permissions.';
        }

        $validationError = Validator::EditUserForm($username, $email);
        if ($validationError) {
            return $validationError;
        }

        $response = $User->EditUser($userId, $username, $password, $email, $roleId, $orgId);

        return ($response) ? 'User edited successfully.' : 'User edit failed.';
    }

    public function GetPaginationData()
    {
        $User = new UserModel();
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
        $start = ($page - 1) * $User->GetLimit();
        $users = $User->GetRecords($start);
        $totalRecords = $User->GetTotalRecords();

        return [
            'users' => $users,
            'totalRecords' => $totalRecords,
            'limit' => $User->GetLimit()
        ];
    }

    public function GetOrgsPaginationDate($orgId)
    {
        $User = new UserModel();
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
        $start = ($page - 1) * $User->GetLimit();
        $users = $User->GetUsersByOrgs($orgId, $start);
        $totalRecords = $User->GetOrgsTotalRecords($orgId);

        return [
            'users' => $users,
            'totalRecords' => $totalRecords,
            'limit' => $User->GetLimit()
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
        var_dump($_POST);
        $response = $auth->EditUser($_POST);
    }
}