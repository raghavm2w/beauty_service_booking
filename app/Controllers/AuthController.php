<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\User;
use App\Helpers\JwtHelper as JWT;




class AuthController extends Controller{
   private User $user;

    public function __construct()
    {
        $this->user = new User();
    }
     
    public function register()
    {
        try{
             $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['name']) || empty($input['email']) || empty($input['password']) || empty($input['phone']) ||empty($input['gender']) || empty($input['role']) || empty($input['confirm_password']))
        {
            error(400,"All fields are required");
        }
        $name = trim($input['name']);
        $email = trim($input['email']);
        $password = $input['password'];
        $confirmPassword = $input['confirm_password'];
        $gender = strtolower($input['gender']);
        $role = strtolower(trim($input['role']));
        $phone = $input['phone'];
        if (strlen($name) < 3 || strlen($name) > 30) {
            error( 400, "Name must be between  3  to 30 characters long.");
        }
        if (!preg_match("/^[A-Za-z ]+$/", $name)) {
            error( 400, "Username can contain only letters.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error( 400, "Invalid email format.");
        }
        if (strlen($email) > 50) {
            error( 400, "Email is too long.");
        }
        if (strlen($password) < 6) {
         error( 400, "Password must be at least 6 characters.");
        }
        if ($password !== $confirmPassword) {
            error( 400, "Password and confirm password do not match.");
        }
        if (!preg_match('/^[1-9]\d{9}$/', $phone)) {
             error(400, "Invalid phone number");
        }
        $allowedRoles  = ['customer', 'provider', 'admin'];
        $allowedGender = ['male', 'female', 'other'];
        if (!in_array($role, $allowedRoles)) {
            error(400, "Invalid role");
        }
        if (!in_array($gender, $allowedGender)) {
             error(400, "Invalid gender");
        }
        $roleMap = [
        'customer'     => 0,
        'provider' => 1,
         'admin'        => 2
        ];
        $userData = [
            'name'     => $name,
            'email'    => $email,
            'phone'    => $phone,
            'gender'   => $gender,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        $userData['role'] = $roleMap[$role]; 
         if ($this->user->findByEmail($email)) {
            error(400,"email already exists");
        }
        $result = $this->user->register($userData);
         $accessToken = $result['access_token'];
        $userId = $result['user_id'];
        if(!$userId){
            error(500,"Registration failed");
        }
       

    $cookieRules =  [
        'expires'  => time() + (60 * 60),
        'path'     => '/',
        'secure'=>isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ];
    setcookie(
    "access_token",
    $accessToken,
    $cookieRules
    );

    success(201,"Registration successful",["user_id"=>$userId,"role"=>$role]);
    }
    catch(\Exception $e){
        error_log("Registration error: ".$e->getMessage());
        error(500,"Internal Server error: ".$e->getMessage());
    }
}
public function login(){
    try{
     $input = json_decode(file_get_contents("php://input"), true);
     if(empty($input['email'])){
        error(400,"Email is required");
     }
     if(empty($input['password'])){
        error(400,"Password is required");
     }
        $email = trim($input['email']);
        $password = $input['password'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error( 400, "Invalid email format.");
        }
        if (strlen($email) > 50) {
            error( 400, "Email is too long.");
        }
        if (strlen($password) < 6) {
         error( 400, "Password must be at least 6 characters.");
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
            $cookieRules =  [
              'expires'  => time() + (60 * 60),
                'path'     => '/',
                'secure'=>isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ];
    setcookie(
        "access_token",
        $accessToken,
        $cookieRules
    );
    success(200,"Login successful",["user_id"=>$user['id'],"role"=>$role]);
        } else {
            error(401,"Incorrect password");
        }


    }  catch(\Exception $e){
        error_log("login error: ".$e->getMessage());
        error(500,"Internal Server error: ".$e->getMessage());
    }
    
}
public function logout()
{
    try {
        if (empty($_COOKIE['access_token'])) {
            error(401, "Not logged in or cookie missing");
        }

        $accessToken = $_COOKIE['access_token'];

        $decoded = JWT::verifyJwt($accessToken);

        if (!$decoded || empty($decoded->sub)) {
            error(401, "Invalid token");
        }

        $userId = $decoded->sub;
        $this->user->deleteRefreshToken($userId);

        setcookie("access_token", "", [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        success(200, "Logged out successfully");

    } catch (\Exception $e) {
        error_log("Logout error: " . $e->getMessage());
        error(500, "Logout failed");
    }
}

}


