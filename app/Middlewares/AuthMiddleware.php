<?php
namespace App\Middlewares;
use App\Helpers\JwtHelper as JWT;
use App\Models\User;



class AuthMiddleware
{
    public static function verify()
    {
        if (empty($_COOKIE['access_token'])) {
            if (!empty($_COOKIE['refresh_token'])) {
            self::refreshAccessToken(null); // Try to refresh using refresh token
                return;
            }
            error_log("cookie not found");
            redirect(401, "/login");
        }

        try {
            $decoded = JWT::verifyJwt($_COOKIE['access_token']);
            $_REQUEST['auth_user'] = [
                'id' => $decoded->sub,
                'role' => $decoded->role
            ];
            return;

        } catch (\Firebase\JWT\ExpiredException $e) {
            self::refreshAccessToken(null);

        } catch (\Exception $e) {
            error_log("JWT verification error: " . $e->getMessage());
            if (!empty($_COOKIE['refresh_token'])) {
                self::refreshAccessToken(null);
                return;
            }
            redirect(401, "/login");
        }
    }

    private static function refreshAccessToken($expiredToken = null)
    {
        try {

            if (empty($_COOKIE['refresh_token'])) {
                error_log("No refresh token found");
                redirect(401, "/login");
            }
            error_log("refresing");
            try {
                $decodedRef = JWT::verifyJwt($_COOKIE['refresh_token']);
                $userId = $decodedRef->sub;
            } catch (\Exception $e) {
                error_log("Invalid refresh token signature: " . $e->getMessage());
                redirect(401, "/login");
            }

            if (!$userId) {
                error_log("No user ID found in refresh token");
                redirect(401, "/login");
            }

            $user = new User();
            $refreshRow = $user->getValidRefreshToken($userId);

            if (!$refreshRow) {
                error_log("no valid refresh token found for user: " . $userId);
                redirect(401, "/login");
            }


            if (strtotime($refreshRow['expires_at']) < time()) {
                error_log("expired refresh token in DB");
                redirect(401, "/login");
            }

            $role_map = [
                0 => 'customer',
                1 => 'provider',
                2 => 'admin'
            ];
            $role = $role_map[$refreshRow['role']];

            // Generate NEW Tokens
            $newAccessToken = JWT::generateAccessToken($userId, $role);
            $newRefreshTokenData = JWT::generateRefreshToken($userId);

            // Update DB with new refresh token
            $user->setRefreshToken($newRefreshTokenData);

            // Set Cookies
            $cookieRules = [
                'expires' => time() + (60 * 60),
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ];

            setcookie("access_token", $newAccessToken, $cookieRules);
            $_COOKIE['access_token'] = $newAccessToken;

            $refreshCookieRules = $cookieRules;
            $refreshCookieRules['expires'] = time() + (60 * 60 * 24 * 7); // 7 days
            setcookie("refresh_token", $newRefreshTokenData['token'], $refreshCookieRules);

            $_REQUEST['auth_user'] = [
                'id' => $userId,
                'role' => $role
            ];

        } catch (\Exception $e) {
            error_log("Refresh token error: " . $e->getMessage());
            redirect(401, "/login");
        }
    }

    public static function providerOnly()
    {
        if (empty($_REQUEST['auth_user'])) {

            if (empty($_REQUEST['auth_user'])) {
                error_log("auth user not found in providerOnly");
                redirect(401, "/login");
            }
        }

        if (empty($_REQUEST['auth_user'])) {
            error_log("auth user not found");
            redirect(401, "/login");
        }

        if ($_REQUEST['auth_user']['role'] !== 'provider') {
            error_log($_REQUEST['auth_user']['role']);
            error_log("user is not provider");
            redirect(403, "/login");
        }
    }

    /**
     * Generic role check
     */
    public static function requireRole(string|array $roles)
    {
        if (empty($_REQUEST['auth_user'])) {
            redirect(401, "/login");
        }

        $userRole = $_REQUEST['auth_user']['role'];

        if (is_string($roles)) {
            $roles = [$roles];
        }

        if (!in_array($userRole, $roles, true)) {
            redirect(403, "/login");
        }
    }

    public static function checkAuth()
    {


        if (empty($_COOKIE['access_token'])) {
            if (!empty($_COOKIE['refresh_token'])) {
                try {
                    return [
                        'loggedIn' => false,

                        'user' => null
                    ];
                } catch (\Exception $e) {
                }
            }
            return [
                'loggedIn' => false,
                'user' => null
            ];
        }

        try {
            $decoded = JWT::verifyJwt($_COOKIE['access_token']);

            return [
                'loggedIn' => true,
                'user' => [
                    'id' => $decoded->sub,
                    'role' => $decoded->role
                ]
            ];

        } catch (\Exception $e) {
            return [
                'loggedIn' => false,
                'user' => null
            ];
        }
    }


}
