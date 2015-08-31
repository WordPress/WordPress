<?php
/**
 * 
 * Utility class for static directory methods.
 * 
 * @category Solar
 * 
 * @package Solar
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Dir.php 2926 2007-11-09 16:25:44Z pmjones $
 * 
 */
class Solar_Dir {
    
    /**
     * 
     * The OS-specific temporary directory location.
     * 
     * @var string
     * 
     */
    protected static $_tmp;
    
    /**
     * 
     * Hack for [[php::is_dir() | ]] that checks the include_path.
     * 
     * Use this to see if a directory exists anywhere in the include_path.
     * 
     * {{code: php
     *     $dir = Solar_Dir::exists('path/to/dir')
     *     if ($dir) {
     *         $files = scandir($dir);
     *     } else {
     *         echo "Not found in the include-path.";
     *     }
     * }}
     * 
     * @param string $dir Check for this directory in the include_path.
     * 
     * @return mixed If the directory exists in the include_path, returns the
     * absolute path; if not, returns boolean false.
     * 
     */
    public static function exists($dir)
    {
        // no file requested?
        $dir = trim($dir);
        if (! $dir) {
            return false;
        }
        
        // using an absolute path for the file?
        // dual check for Unix '/' and Windows '\',
        // or Windows drive letter and a ':'.
        $abs = ($dir[0] == '/' || $dir[0] == '\\' || $dir[1] == ':');
        if ($abs && is_dir($dir)) {
            return $dir;
        }
        
        // using a relative path on the file
        $path = explode(PATH_SEPARATOR, ini_get('include_path'));
        foreach ($path as $base) {
            // strip Unix '/' and Windows '\'
            $target = rtrim($base, '\\/') . DIRECTORY_SEPARATOR . $dir;
            if (is_dir($target)) {
                return $target;
            }
        }
        
        // never found it
        return false;
    }
    
    /**
     * 
     * "Fixes" a directory string for the operating system.
     * 
     * Use slashes anywhere you need a directory separator. Then run the
     * string through fixdir() and the slashes will be converted to the
     * proper separator (for example '\' on Windows).
     * 
     * Always adds a final trailing separator.
     * 
     * @param string $dir The directory string to 'fix'.
     * 
     * @return string The "fixed" directory string.
     * 
     */
    public static function fix($dir)
    {
        $dir = str_replace('/', DIRECTORY_SEPARATOR, $dir);
        return rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
    
    /**
     * 
     * Convenience method for dirname() and higher-level directories.
     * 
     * @param string $file Get the dirname() of this file.
     * 
     * @param int $up Move up in the directory structure this many 
     * times, default 0.
     * 
     * @return string The dirname() of the file.
     * 
     */
    public static function name($file, $up = 0)
    {
        $dir = dirname($file);
        while ($up --) {
            $dir = dirname($dir);
        }
        return $dir;
    }
    
    /**
     * 
     * Returns the OS-specific directory for temporary files.
     * 
     * @param string $sub Add this subdirectory to the returned temporary
     * directory name.
     * 
     * @return string The temporary directory path.
     * 
     */
    public static function tmp($sub = '')
    {
        // find the tmp dir if needed
        if (! Solar_Dir::$_tmp) {
            
            // use the system if we can
            if (function_exists('sys_get_temp_dir')) {
                $tmp = sys_get_temp_dir();
            } else {
                $tmp = Solar_Dir::_tmp();
            }
            
            // remove trailing separator and save
            Solar_Dir::$_tmp = rtrim($tmp, DIRECTORY_SEPARATOR);
        }
        
        // do we have a subdirectory request?
        $sub = trim($sub);
        if ($sub) {
            // remove leading and trailing separators, and force exactly
            // one trailing separator
            $sub = trim($sub, DIRECTORY_SEPARATOR)
                 . DIRECTORY_SEPARATOR;
        }
        
        return Solar_Dir::$_tmp . DIRECTORY_SEPARATOR . $sub;
    }
    
    /**
     * 
     * Returns the OS-specific temporary directory location.
     * 
     * @return string The temp directory path.
     * 
     */
    protected static function _tmp()
    {
        // non-Windows system?
        if (strtolower(substr(PHP_OS, 0, 3)) != 'win') {
            $tmp = empty($_ENV['TMPDIR']) ? getenv('TMPDIR') : $_ENV['TMPDIR'];
            if ($tmp) {
                return $tmp;
            } else {
                return '/tmp';
            }
        }
        
        // Windows 'TEMP'
        $tmp = empty($_ENV['TEMP']) ? getenv('TEMP') : $_ENV['TEMP'];
        if ($tmp) {
            return $tmp;
        }
    
        // Windows 'TMP'
        $tmp = empty($_ENV['TMP']) ? getenv('TMP') : $_ENV['TMP'];
        if ($tmp) {
            return $tmp;
        }
    
        // Windows 'windir'
        $tmp = empty($_ENV['windir']) ? getenv('windir') : $_ENV['windir'];
        if ($tmp) {
            return $tmp;
        }
    
        // final fallback for Windows
        return getenv('SystemRoot') . '\\temp';
    }
}