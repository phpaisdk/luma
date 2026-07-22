<?php

declare(strict_types=1);

namespace AiSdk\Luma\Support;

final class LumaAuth
{
    public static function token(string $accessKey, string $secretKey): string
    {
        $now = time();
        $h = self::enc(json_encode(['alg' => 'HS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR));
        $p = self::enc(json_encode(['iss' => $accessKey, 'exp' => $now + 1800, 'nbf' => $now - 5], JSON_THROW_ON_ERROR));
        $in = $h.'.'.$p;

        return $in.'.'.self::enc(hash_hmac('sha256', $in, $secretKey, true));
    }

    private static function enc(string $v): string
    {
        return rtrim(strtr(base64_encode($v), '+/', '-_'), '=');
    }
}
