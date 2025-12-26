<?php
namespace App\Middlewares;
use App\Helpers\JwtHelper as JWT;
use App\Models\User;



class AuthMiddleware
{
    public static function verify()
    {
        if (empty($_COOKIE['access_token'])) {
            error_log("cookie not found");
            redirect(401,"/login");
            // error(401, "Unauthorized");
        }
        try {
            $decoded = JWT::verifyJwt($_COOKIE['access_token']);
            $_REQUEST['auth_user'] = [
                'id'   => $decoded->sub,
                'role' => $decoded->role
            ];
            return; 

        } catch (\Firebase\JWT\ExpiredException $e) {
            self::refreshAccessToken();

        } catch (\Exception $e) {
            error_log("JWT verification error: " . $e->getMessage());
            redirect(401,"/login");

            // error(401, "Unauthorized");
        }
    }

    private static function refreshAccessToken()
    {
        $user = new User();
        $refreshRow = $user->getValidRefreshToken();
        if (!$refreshRow) {
            error_log("no valid refresh token found");
            redirect(401,"/login");
            // error(401, "Unauthorized");
        }
        error_log("refreshed token");
        if (strtotime($refreshRow['expires_at']) < time()) {
                        error_log("expired refresh token");

              redirect(401,"/login");

            //error(401, "Unauthorized");
        }

        $userId =  $refreshRow['user_id'];

        $newAccessToken = JWT::generateAccessToken(
            $userId,
            $refreshRow['role']
        );

        setcookie("access_token", $newAccessToken, [
            'expires'  => time() + (60*60),
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        $_REQUEST['auth_user'] = [
            'id'   => $userId,
            'role' => $refreshRow['role'] === 1 ? 'provider':'customer'
        ];
    }
     public static function providerOnly()
    {
        if (empty($_REQUEST['auth_user'])) {
            error_log("auth user not found");

            redirect(401,"/login");
            //error(401, "Unauthorized");

        }

        if ($_REQUEST['auth_user']['role'] !== 'provider') {
                        error_log("user is not provider");
            redirect(403,"/login");
            //error(403, "Forbidden");

        }
    }

    /**
     * Generic role check
     */
    public static function requireRole(string|array $roles)
    {
        if (empty($_REQUEST['auth_user'])) {
            redirect(401,"/login");
        }

        $userRole = $_REQUEST['auth_user']['role'];

        if (is_string($roles)) {
            $roles = [$roles];
        }

        if (!in_array($userRole, $roles, true)) {
            redirect(403,"/login");
        }
    }

 
}
