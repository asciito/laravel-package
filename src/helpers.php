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

if (! function_exists('absolute')) {
    /**
     * Return the absolute path of the given path
     *
     * @returns false if the resulting path is not absolute
     */
    function absolute(string $path, string $absolutePath = null): string
    {
        if (! realpath($path)) {
            return join_paths(rtrim($absolutePath ?? '', DIRECTORY_SEPARATOR), trim($path, DIRECTORY_SEPARATOR));
        }

        return $path;
    }
}
