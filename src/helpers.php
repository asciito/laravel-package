<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use function Spatie\PestPluginTestTime\testTime;

if (! function_exists('join_paths')) {
    /**
     * Join the given paths, without trailing slash
     */
    function join_paths(string ...$path): string
    {
        return rtrim(Arr::join($path, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
    }
}
