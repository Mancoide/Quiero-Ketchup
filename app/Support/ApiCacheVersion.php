<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

final class ApiCacheVersion
{
    public static function key(string $namespace): string
    {
        return "api:version:{$namespace}";
    }

    public static function get(string $namespace): int
    {
        $version = Cache::get(self::key($namespace));

        if (! is_numeric($version)) {
            return 1;
        }

        return max(1, (int) $version);
    }

    public static function bump(string $namespace): int
    {
        $key = self::key($namespace);
        $version = self::get($namespace) + 1;

        Cache::forever($key, $version);

        return $version;
    }
}
