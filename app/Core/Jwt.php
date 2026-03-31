<?php

namespace App\Core;

use Firebase\JWT\JWT as FirebaseJwt;
use Firebase\JWT\Key;
use Throwable;

class Jwt
{
    public static function secret(): string
    {
        return (string) Config::get('JWT_SECRET', '3db4fdc7f698ac7dab463b72d9d43448fc7109d055682f0fe5fa95fb63c687e4');
    }

    public static function algo(): string
    {
        return (string) Config::get('JWT_ALGO', 'HS256');
    }

    public static function ttl(): int
    {
        return (int) Config::get('JWT_TTL', 3600);
    }

    public static function encode(array $payload): string
    {
        $now = time();

        $claims = array_merge([
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + self::ttl(),
        ], $payload);

        return FirebaseJwt::encode($claims, self::secret(), self::algo());
    }

    public static function decode(string $token): ?array
    {
        try {
            $decoded = FirebaseJwt::decode(
                $token,
                new Key(self::secret(), self::algo())
            );

            return (array) $decoded;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function fromAuthorizationHeader(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? '';

        if (!$header && function_exists('getallheaders')) {
            $headers = getallheaders();
            $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (!$header || !preg_match('/Bearer\s+(.+)/i', $header, $matches)) {
            return null;
        }

        return trim($matches[1]);
    }
}