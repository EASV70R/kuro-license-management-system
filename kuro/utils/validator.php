<?php

class Validator
{
    private static function ValidateUsername(string $username): string|bool
    {
        $usernameSchema = "/^[a-zA-Z0-9]*$/";
        if (empty($username)) {
            $error = "Please enter a username.";
        } elseif (strlen($username) < 3) {
            $error = "Username is too short.";
        } elseif (strlen($username) > 14) {
            $error = "Username is too long.";
        } elseif (!preg_match($usernameSchema, $username)) {
            $error = "Username must contain only letters and numbers.";
        }
        return $error ?? false;
    }

    private static function ValidatePassword(string $password): string|bool
    {
        // https://stackoverflow.com/a/8141210
        // NOTE: Use only for live server.
        $passwordSchema = "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$/";
        if (empty($password)) {
            $error = "Please enter a password.";
        } elseif (strlen($password) < 4) {
            $error = "Password is too short.";
        } elseif (strlen($password) > 50) {
            $error = "Password is too long.";
        } elseif (!preg_match($passwordSchema, $password)) {
            $error = "Password must contain at least 8 letter long, one uppercase letter, one lowercase letter, one number, and one special character.";
        }
        return $error ?? false;
    }

    private static function ValidateEmail(string $email): string|bool
    {
        $emailSchema = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
        if (empty($email)) {
            $error = "Please enter your Email.";
        } elseif (strlen($email) < 3) {
            $error = "null.";
        } elseif (strlen($email) > 30) {
            $error = "Email is too long.";
        } elseif (!preg_match($emailSchema, $email)) {
            $error = "Please enter a valid Email.";
        }
        return $error ?? false;
    }
    
    private static function ValidateRoleId(int $roleId): int|bool
    {
        if (empty($roleId)) {
            $error = "Please select a role.";
        } elseif (!is_numeric($roleId)) {
            $error = "Please select a valid role.";
        }
        return $error ?? false;
    }

    private static function ValidateOrgId(int $orgId): int|bool
    {
        if (empty($orgId)) {
            $error = "Please select an organization.";
        } elseif (!is_numeric($orgId)) {
            $error = "Please select a valid organization.";
        }
        return $error ?? false;
    }

    public static function RegisterForm(
        string $username,
        string $password,
        string $confirmPassword,
        string $email,
    ): string|bool{
    
        $validateUsername = self::ValidateUsername($username);
        if ($validateUsername) {
            return (string) $validateUsername;
        }

        $validatePassword = self::ValidatePassword($password);
        if ($validatePassword) {
            return (string) $validatePassword;
        }

        $validateEmail = self::ValidateEmail($email);
        if ($validateEmail) {
            return (string) $validateEmail;
        }

        if (empty($confirmPassword) && $password != $confirmPassword) {
            return "Passwords do not match, please try again.";
        }

        return false;
    }

    public static function RegisterFormAdm(
        string $username,
        string $password,
        string $confirmPassword,
        string $email,
        string $roleId,
        string $orgId,
    ): string|bool{
    
        $validateUsername = self::ValidateUsername($username);
        if ($validateUsername) {
            return (string) $validateUsername;
        }

        $validatePassword = self::ValidatePassword($password);
        if ($validatePassword) {
            return (string) $validatePassword;
        }

        $validateEmail = self::ValidateEmail($email);
        if ($validateEmail) {
            return (string) $validateEmail;
        }

        $validateRoleId = self::ValidateOrgId($roleId);
        if ($validateRoleId) {
            return (int) $validateRoleId;
        }

        $validateOrgId = self::ValidateOrgId($orgId);
        if ($validateOrgId) {
            return (int) $validateOrgId;
        }

        if (empty($confirmPassword) && $password != $confirmPassword) {
            return "Passwords do not match, please try again.";
        }

        return false;
    }

    public static function LoginForm(string $username, string $password): string|bool
    {
        $validateUsername = self::ValidateUsername($username);
        if ($validateUsername) {
            return (string) $validateUsername;
        }

      /*  $validatePassword = self::ValidatePassword($password);
        if ($validatePassword) {
            return (string) $validatePassword;
        }*/

        return false;
    }

    public static function EditUserForm(
        string $username,
        string $email,
    ): string|bool{
    
        $validateUsername = self::ValidateUsername($username);
        if ($validateUsername) {
            return (string) $validateUsername;
        }

        $validateEmail = self::ValidateEmail($email);
        if ($validateEmail) {
            return (string) $validateEmail;
        }

        return false;
    }
}