<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\User;
use App\Helpers\JwtHelper as JWT;




class AuthController extends Controller
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function register()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (empty($input['name']) || empty($input['email']) || empty($input['password']) || empty($input['phone']) || empty($input['gender']) || empty($input['role']) || empty($input['confirm_password'])) {
                error(400, "All fields are required");
            }
            $name = trim($input['name']);
            $email = trim($input['email']);
            $password = $input['password'];
            $confirmPassword = $input['confirm_password'];
            $gender = strtolower($input['gender']);
            $role = strtolower(trim($input['role']));
            $phone = $input['phone'];
            if (strlen($name) < 3 || strlen($name) > 50) {
                error(400, "Name must be between 3 to 50 characters long.");
            }
            if (!preg_match("/^[A-Za-z ]+$/", $name)) {
                error(400, "Username can contain only letters.");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error(400, "Invalid email format.");
            }
            if (strlen($email) > 60) {
                error(400, "Email is too long.");
            }
            if (strlen($password) < 6) {
                error(400, "Password must be at least 6 characters.");
            }
            if ($password !== $confirmPassword) {
                error(400, "Password and confirm password do not match.");
            }
            if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
                error(400, "Invalid Indian mobile number (must be 10 digits starting with 6-9)");
            }
            // Sanitize name
            $name = htmlspecialchars(strip_tags($name), ENT_QUOTES, 'UTF-8');
            $allowedRoles = ['customer', 'provider', 'admin'];
            $allowedGender = ['male', 'female', 'other'];
            if (!in_array($role, $allowedRoles)) {
                error(400, "Invalid role");
            }
            if (!in_array($gender, $allowedGender)) {
                error(400, "Invalid gender");
            }
            $roleMap = [
                'customer' => 0,
                'provider' => 1,
                'admin' => 2
            ];
            $userData = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ];
            $userData['role'] = $roleMap[$role];
            $userData['role_label'] = $role;
            if ($this->user->findByEmail($email)) {
                error(400, "email already exists");
            }
            $result = $this->user->register($userData);
            $accessToken = $result['access_token'];
            $userId = $result['user_id'];
            if (!$userId) {
                error(500, "Registration failed");
            }


            $cookieRules = [
                'expires' => time() + (60 * 60),
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ];

            setcookie(
                "access_token",
                $accessToken,
                $cookieRules
            );

            $refreshCookieRules = $cookieRules;
            $refreshCookieRules['expires'] = time() + (60 * 60 * 24 * 7); // 7 days example, or match config

            setcookie(
                "refresh_token",
                $result['refresh_token'],
                $refreshCookieRules
            );
                log_event(
        'auth',
        'register',
        'success in register user',
        $userId
        );
            success(201, "Registration successful", ["user_id" => $userId, "role" => $role]);
        } catch (\Exception $e) {
             log_event(
            'error',
            'exception',
            'register failed: ' . $e->getMessage(),
             null
        );
            error_log("Registration error: " . $e->getMessage());
            error(500, "Internal Server error: " . $e->getMessage());
        }
    }
    public function login()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            if (empty($input['email'])) {
                error(400, "Email is required");
            }
            if (empty($input['password'])) {
                error(400, "Password is required");
            }
            $email = trim($input['email']);
            $password = $input['password'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error(400, "Invalid email format.");
            }
            if (strlen($email) > 50) {
                error(400, "Email is too long.");
            }
            if (strlen($password) < 6) {
                error(400, "Password must be at least 6 characters.");
            }
            $user = $this->user->findByEmail($email);
            if (!$user) {
                error(404, "User not found. Please register first.");
            }
            if (password_verify($password, $user['password'])) {
                $role_map = [
                    0 => 'customer',
                    1 => 'provider',
                    2 => 'admin'
                ];
                $role = $role_map[$user['role']];
                $accessToken = JWT::generateAccessToken($user['id'], $role);
                $refreshData = JWT::generateRefreshToken($user['id']);
                $this->user->setRefreshToken($refreshData);// store in db
                $cookieRules = [
                    'expires' => time() + (60 * 60),
                    'path' => '/',
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Strict'
                ];

                setcookie(
                    "access_token",
                    $accessToken,
                    $cookieRules
                );

                $refreshCookieRules = $cookieRules;
                $refreshCookieRules['expires'] = time() + (60 * 60 * 24 * 7); // 7 days

                setcookie(
                    "refresh_token",
                    $refreshData['token'],
                    $refreshCookieRules
                );
                    log_event('auth','log in','User logged in success',$user['id']);
                success(200, "Login successful", ["user_id" => $user['id'], "role" => $role]);
            } else {
                error(401, "Incorrect password");
            }


        } catch (\Exception $e) {
             log_event(
            'error',
            'exception',
            'login failed: ' . $e->getMessage(),
            null
        );
            error_log("login error: " . $e->getMessage());
            error(500, "Internal Server error: " . $e->getMessage());
        }

    }
    public function logout()
    {
        try {
        $userId = null;

        if (!empty($_COOKIE['refresh_token'])) {
            try {
                $decodedRefresh = JWT::verifyJwt($_COOKIE['refresh_token']);
                if (!empty($decodedRefresh->sub)) {
                    $userId = $decodedRefresh->sub;
                    $this->user->deleteRefreshToken($userId);
                }
            } catch (\Exception $e) {

            }
        }

            setcookie("access_token", "", [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            setcookie("refresh_token", "", [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
             log_event(
        'auth',
        'log out',
        'User logged out success',
        $userId);

            success(200, "Logged out successfully");

        } catch (\Exception $e) {
             log_event(
            'error',
            'exception',
            'logout failed: ' . $e->getMessage(),
             null);
            error_log("Logout error: " . $e->getMessage());
            success(200, "Logged out successfully");
        }
    }
    
    //----setting timezone by provider according to their timezone slots timings are calculated
    public function setTimezone()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            if (!isset($input['timezone'])) {
                error(400, "timezone value is required");
            }
            $timezone = $input['timezone'];
            $provider_id = $_REQUEST['auth_user']['id'];
            if (!isset($provider_id)) {
                error(400, "user id is required");
            }

            $isUpdated = $this->user->updateTimezone($timezone, $provider_id);
            if (!$isUpdated) {
                error(500, "failed to update timezone");
            }
                log_event('success','timezone','User timezone updated',$provider_id);
            success(200, "timezone updated");


        } catch (\Exception $e) {
            log_event(
            'error',
            'exception',
            'Setting timezone failed: ' . $e->getMessage(),
            $_REQUEST['auth_user']['id'] ?? null
        );
          log_event(
            'error',
            'exception',
            'setting timezone failed: ' . $e->getMessage(),
            null
        );
            error_log("setting timezone error: " . $e->getMessage());
            error(500, "setting timezone failed");
        }
    }

    public function getUser()
    {
        try {
            if (empty($_REQUEST['auth_user']['id'])) {
                error(401, "Unauthorized");
            }
            $userId = $_REQUEST['auth_user']['id'];
            $user = $this->user->findById($userId);

            if (!$user) {
                error(404, "User not found");
            }
                 log_event('success','get_user','get user controller success',$userId);

            success(200, "User details fetched", $user);
        } catch (\Exception $e) {
             log_event(
            'error',
            'exception',
            'getting user by id failed: ' . $e->getMessage(),
            $_REQUEST['auth_user']['id'] ?? null
        );
            error_log("Get user error: " . $e->getMessage());
            error(500, "Internal Server Error");
        }
    }

    public function updateProfile()
    {
        try {
            if (empty($_REQUEST['auth_user']['id'])) {
                error(401, "Unauthorized");
            }
            $userId = $_REQUEST['auth_user']['id'];

            $input = json_decode(file_get_contents("php://input"), true);

            $updateData = [];
            if (!empty($input['name'])) {
                $name = trim($input['name']);
                if (strlen($name) < 3 || strlen($name) > 100) {
                    error(400, "Name must be between 3 to 100 characters long.");
                }
                if (!preg_match("/^[A-Za-z ]+$/", $name)) {
                    error(400, "Name can contain only letters.");
                }
                $updateData['name'] = htmlspecialchars(strip_tags($name), ENT_QUOTES, 'UTF-8');
            }

            if (!empty($input['phone'])) {
                $phone = $input['phone'];
                if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
                    error(400, "Invalid Indian mobile number (must be 10 digits starting with 6-9)");
                }
                $updateData['phone'] = $phone;
            }

            if (!empty($input['gender'])) {
                $allowedGender = ['male', 'female', 'other'];
                if (!in_array(strtolower($input['gender']), $allowedGender)) {
                    error(400, "Invalid gender");
                }
                $updateData['gender'] = strtolower($input['gender']);
            }

            if (empty($updateData)) {
                error(400, "No valid fields to update");
            }

            $success = $this->user->updateUser($userId, $updateData);

            if (!$success) {
                 log_event(
            'error',
            'update profile',
            'update profile query failed',
            $_REQUEST['auth_user']['id'] ?? null
        );
                error(500, "Failed to update profile");
            }
            log_event('success','update profile','User profile updated',$userId);

            success(200, "Profile updated successfully");

        } catch (\Exception $e) {
             log_event(
            'error',
            'exception',
            'updating proile failed: ' . $e->getMessage(),
            $_REQUEST['auth_user']['id'] ?? null
        );
            error_log("Update profile error: " . $e->getMessage());
            error(500, "Internal Server Error");
        }
    }
}


