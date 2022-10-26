<?php namespace App\Sport;

use App\Sport\Provider\Phoenix\PhoenixLineProvider;
use App\Sport\Provider\SportLineProvider;
use Illuminate\Support\Facades\Cache;

class Sport {

    public static function getLine(): SportLineProvider {
        return new PhoenixLineProvider();
    }

    public static function cachedRequest(string $url, int $seconds) {
        if(Cache::has($url)) return Cache::get($url);
        $result = file_get_contents($url);
        Cache::put($url, $result, now()->addSeconds($seconds));
        return $result;
    }

}
