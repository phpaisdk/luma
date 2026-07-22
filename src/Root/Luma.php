<?php

declare(strict_types=1);

namespace AiSdk;

use AiSdk\Contracts\Model;
use AiSdk\Luma\LumaOptions;
use AiSdk\Luma\LumaProvider;

final class Luma
{
    private static ?LumaProvider $default = null;

    /** @param array<string, mixed> $c */
    public static function create(array $c = []): LumaProvider
    {
        return self::$default = new LumaProvider(LumaOptions::fromArray($c));
    }

    public static function default(): LumaProvider
    {
        return self::$default ??= self::create();
    }

    public static function reset(): void
    {
        self::$default = null;
    }

    public static function model(string $id): Model
    {
        return self::default()->model($id);
    }
}
