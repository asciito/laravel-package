<?php

use Illuminate\Support\Arr;

if (! function_exists('join_paths')) {
    /**
     * Join the given paths, without trailing slash
     */
    function join_paths(string ...$path): string
    {
        return rtrim(Arr::join($path, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
    }
}
