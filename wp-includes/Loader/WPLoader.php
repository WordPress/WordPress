<?php

namespace WordpressCore\Loader;

use Exception;

class WPLoader
{
    public static $filesMustBeLoaded = [];

    /**
     * Get Files Name from Bases Path
     * 
     * @param string $fileName
     */
    private static function getFilesMustBeLoaded($basePath, $fileName)
    {
        $path = $basePath . "Loader/Bases/{$fileName}.php";

        if (!file_exists($path)) {
            throw new Exception("The {$fileName} Handler is Invalid!");
        }

        self::$filesMustBeLoaded  = require($path);
    }

    /**
     * Loading The Files to Access into PHP Environment
     * 
     * @param string $basePath
     * 
     * @param string $loaderType
     */
    public static function load(string $basePath, $loaderType = 'MostWordpress', $cacheFiles = false)
    {
        self::getFilesMustBeLoaded($basePath, $loaderType);

        foreach (self::$filesMustBeLoaded as $file) {

            $filePath = $basePath . $file . ".php";

            require($filePath);
        }

        // remove the files when don't need cache them
        if (!$cacheFiles) {
            static::refresh();
        }
    }

    private static function refresh()
    {
        self::$filesMustBeLoaded = [];
    }
}
