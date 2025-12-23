<?php
namespace App\Helpers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    private static function config(): array
    {
        return require __DIR__ .'/../Config/jwtConfig.php';
    }

    public static function generateAccessToken(int $userId, string $role)
    {
        $config = self::config();

        $payload = [
            'iss'  => $config['issuer'],
            'iat'  => time(),
            'exp'  => time() + $config['access_expiry'],
            'sub'  => $userId,
            'role' => $role
        ];

        return JWT::encode($payload, $config['secret'], $config['algo']);
    }

    public static function generateRefreshToken(int $userId)
    {
        $config = self::config();

        $payload = [
            'iss' => $config['issuer'],
            'iat' => time(),
            'exp' => time() + $config['refresh_expiry'],
            'sub' => $userId
        ];

        return [
            "token"=>JWT::encode($payload, $config['secret'], $config['algo']),
                "expires_at"=> date('Y-m-d H:i:s',time() + $config['refresh_expiry']),
            "user_id"=>$userId
        ];
    }

    public static function verifyJwt(string $token)
    {
        $config = self::config();

        return JWT::decode(
            $token,
            new Key($config['secret'], $config['algo'])
        );
    }

    public static function getBearerToken(): ?string
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            return null;
        }

        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }

        return null;
    }
}
