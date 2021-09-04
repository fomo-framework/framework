<?php

namespace Tower;

class Loader
{
    protected static array $files = [];

    public static function save(array $files)
    {
        foreach ($files as $index => $file){
            self::$files[$index] = include $file;
        }
    }

    public static function get(string $fileKey)
    {
        return self::$files[$fileKey];
    }
}