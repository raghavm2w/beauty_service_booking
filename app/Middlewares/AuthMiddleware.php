<?php
use Firebase\JWT\Key;
use App\Helpers\JwtHelper as JWT;
use App\Models\User;



class AuthMiddleware
{
    public static function handle()
    {
        if (empty($_COOKIE['access_token'])) {
            error(401, "Unauthorized");
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
            error(401, "Unauthorized");
        }
    }

    private static function refreshAccessToken()
    {
        // $user = new User();
        // $refreshRow = $user->getValidRefreshToken();

        // if (!$refreshRow) {
        //     error(401, "Unauthorized");
        // }

        // if (strtotime($refreshRow['expires_at']) < time()) {
        //     error(401, "Unauthorized");
        // }

        // $userId =  $refreshRow['user_id'];

        // $newAccessToken = JWT::generateAccessToken(
        //     $userId,
        //     $refreshRow['role']
        // );

        // setcookie("access_token", $newAccessToken, [
        //     'expires'  => time() + $config['access_expiry'],
        //     'path'     => '/',
        //     'secure'   => isset($_SERVER['HTTPS']),
        //     'httponly' => true,
        //     'samesite' => 'Strict'
        // ]);

        // $_REQUEST['auth_user'] = [
        //     'id'   => $userId,
        //     'role' => $refreshRow['role']
        // ];
    }

 
}
