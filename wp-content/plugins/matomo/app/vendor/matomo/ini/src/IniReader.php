<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Matomo\Ini;

/**
 * Reads INI configuration.
 */
class IniReader
{
    /**
     * @var bool
     */
    private $useNativeFunction;
    public function __construct()
    {
        $this->useNativeFunction = function_exists('parse_ini_string');
    }
    /**
     * Reads a INI configuration file and returns it as an array.
     *
     * The array returned is multidimensional, indexed by section names:
     *
     * ```
     * array(
     *     'Section 1' => array(
     *         'value1' => 'hello',
     *         'value2' => 'world',
     *     ),
     *     'Section 2' => array(
     *         'value3' => 'foo',
     *     )
     * );
     * ```
     *
     * @param string $filename The file to read.
     * @throws IniReadingException
     * @return array
     */
    public function readFile($filename)
    {
        $ini = $this->getContentOfIniFile($filename);
        return $this->readString($ini);
    }
    /**
     * Reads a INI configuration string and returns it as an array.
     *
     * The array returned is multidimensional, indexed by section names:
     *
     * ```
     * array(
     *     'Section 1' => array(
     *         'value1' => 'hello',
     *         'value2' => 'world',
     *     ),
     *     'Section 2' => array(
     *         'value3' => 'foo',
     *     )
     * );
     * ```
     *
     * @param string $ini String containing INI configuration.
     * @throws IniReadingException
     * @return array
     */
    public function readString($ini)
    {
        // On PHP 5.3.3 an empty line return is needed at the end
        // See http://3v4l.org/jD1Lh
        $ini .= "\n";
        if ($this->useNativeFunction) {
            $array = $this->readWithNativeFunction($ini);
        } else {
            $array = $this->readWithAlternativeImplementation($ini);
        }
        return $array;
    }
    /**
     * @param string $ini
     * @throws IniReadingException
     * @return array
     */
    private function readWithNativeFunction($ini)
    {
        $array = @parse_ini_string($ini, \true);
        if ($array === \false) {
            $e = error_get_last();
            throw new \Matomo\Ini\IniReadingException('Syntax error in INI configuration: ' . $e['message']);
        }
        // We cannot use INI_SCANNER_RAW by default because it is buggy under PHP 5.3.14 and 5.4.4
        // http://3v4l.org/m24cT
        $rawValues = @parse_ini_string($ini, \true, \INI_SCANNER_RAW);
        if ($rawValues === \false) {
            return $this->decode($array, $array);
        }
        $array = $this->decode($array, $rawValues);
        return $array;
    }
    private function getContentOfIniFile($filename)
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new \Matomo\Ini\IniReadingException(sprintf("The file %s doesn't exist or is not readable", $filename));
        }
        $content = $this->getFileContent($filename);
        if ($content === \false) {
            throw new \Matomo\Ini\IniReadingException(sprintf('Impossible to read the file %s', $filename));
        }
        return $content;
    }
    /**
     * Reads ini comments for each key.
     *
     * The array returned is multidimensional, indexed by section names:
     *
     * ```
     * array(
     *     'Section 1' => array(
     *         'key1' => 'comment 1',
     *         'key2' => 'comment 2',
     *     ),
     *     'Section 2' => array(
     *         'key3' => 'comment 3',
     *     )
     * );
     * ```
     *
     * @param string $filename The path to a file.
     * @throws IniReadingException
     * @return array
     */
    public function readComments($filename)
    {
        $ini = $this->getContentOfIniFile($filename);
        $ini = $this->splitIniContentIntoLines($ini);
        $descriptions = array();
        $section = '';
        $lastComment = '';
        foreach ($ini as $line) {
            $line = trim($line);
            if (strpos($line, '[') === 0) {
                $tmp = explode(']', $line);
                $section = trim(substr($tmp[0], 1));
                $descriptions[$section] = array();
                $lastComment = '';
                continue;
            }
            if ($line === '') {
                $lastComment = "\n";
                continue;
            }
            if (!preg_match('/^[a-zA-Z0-9[]/', $line)) {
                if (strpos($line, ';') === 0) {
                    $line = trim(substr($line, 1));
                }
                // comment
                $lastComment .= $line . "\n";
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            if (strpos($key, '[]') === strlen($key) - 2) {
                $key = substr($key, 0, -2);
            }
            if (empty($descriptions[$section][$key])) {
                $descriptions[$section][$key] = $lastComment;
            }
            $lastComment = '';
        }
        return $descriptions;
    }
    private function splitIniContentIntoLines($ini)
    {
        if (is_string($ini)) {
            $ini = explode("\n", str_replace("\r", "\n", $ini));
        }
        return $ini;
    }
    /**
     * Reimplementation in case `parse_ini_file()` is disabled.
     *
     * @author Andrew Sohn <asohn (at) aircanopy (dot) net>
     * @author anthon (dot) pang (at) gmail (dot) com
     *
     * @param string $ini
     * @return array
     */
    private function readWithAlternativeImplementation($ini)
    {
        $ini = $this->splitIniContentIntoLines($ini);
        if (count($ini) == 0) {
            return array();
        }
        $sections = array();
        $values = array();
        $result = array();
        $globals = array();
        $i = 0;
        foreach ($ini as $line) {
            $line = trim($line);
            $line = str_replace("\t", " ", $line);
            // Comments
            if (!preg_match('/^[a-zA-Z0-9[]/', $line)) {
                continue;
            }
            // Sections
            if ($line[0] == '[') {
                $tmp = explode(']', $line);
                $sections[] = trim(substr($tmp[0], 1));
                $i++;
                continue;
            }
            // Key-value pair
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (strstr($value, ";")) {
                $tmp = explode(';', $value);
                if (count($tmp) == 2) {
                    if ($value[0] != '"' && $value[0] != "'" || preg_match('/^".*"\\s*;/', $value) || preg_match('/^".*;[^"]*$/', $value) || preg_match("/^'.*'\\s*;/", $value) || preg_match("/^'.*;[^']*\$/", $value)) {
                        $value = $tmp[0];
                    }
                } else {
                    if ($value[0] == '"') {
                        $value = preg_replace('/^"(.*)".*/', '$1', $value);
                    } elseif ($value[0] == "'") {
                        $value = preg_replace("/^'(.*)'.*/", '$1', $value);
                    } else {
                        $value = $tmp[0];
                    }
                }
            }
            $value = trim($value);
            // Special keywords
            if ($value === 'true' || $value === 'yes' || $value === 'on') {
                $value = \true;
            } elseif ($value === 'false' || $value === 'no' || $value === 'off') {
                $value = \false;
            } elseif ($value === '' || $value === 'null') {
                $value = null;
            }
            if (is_string($value)) {
                if (preg_match('/^"(.*)"$/', $value)) {
                    $value = preg_replace('/^"(.*)"$/', '$1', $value);
                    $value = str_replace('\\"', '"', $value);
                } elseif (preg_match("/^'(.*)'\$/", $value)) {
                    $value = preg_replace("/^'(.*)'\$/", '$1', $value);
                    $value = str_replace("\\'", "'", $value);
                } else {
                    $value = trim($value, "'\"");
                }
            }
            if ($i == 0) {
                if (substr($key, -2) == '[]') {
                    $globals[substr($key, 0, -2)][] = $value;
                } else {
                    $globals[$key] = $value;
                }
            } else {
                if (substr($key, -2) == '[]') {
                    $values[$i - 1][substr($key, 0, -2)][] = $value;
                } else {
                    $values[$i - 1][$key] = $value;
                }
            }
        }
        for ($j = 0; $j < $i; $j++) {
            if (isset($values[$j])) {
                $result[$sections[$j]] = $values[$j];
            } else {
                $result[$sections[$j]] = array();
            }
        }
        $finalResult = $result + $globals;
        return $this->decode($finalResult, $finalResult);
    }
    /**
     * @param string $filename
     * @return bool|string Returns false if failure.
     */
    private function getFileContent($filename)
    {
        if (function_exists('file_get_contents')) {
            return file_get_contents($filename);
        } elseif (function_exists('file')) {
            $ini = file($filename);
            if ($ini !== \false) {
                return implode("\n", $ini);
            }
        } elseif (function_exists('fopen') && function_exists('fread')) {
            $handle = fopen($filename, 'r');
            if (!$handle) {
                return \false;
            }
            $ini = fread($handle, filesize($filename));
            fclose($handle);
            return $ini;
        }
        return \false;
    }
    /**
     * We have to decode values manually because parse_ini_file() has a poor implementation.
     *
     * @param mixed $value    The array decoded by `parse_ini_file`
     * @param mixed $rawValue The same array but with raw strings, so that we can re-decode manually
     *                        and override the poor job of `parse_ini_file`
     * @return mixed
     */
    private function decode($value, $rawValue)
    {
        if (is_array($value)) {
            foreach ($value as $i => &$subValue) {
                $subValue = $this->decode($subValue, $rawValue[$i]);
            }
            return $value;
        }
        if (!is_string($value)) {
            return $value;
        }
        $value = $this->decodeBoolean($value, $rawValue);
        $value = $this->decodeNull($value, $rawValue);
        if (is_numeric($value) && $this->noLossWhenCastToInt($value)) {
            return $value + 0;
        }
        return $value;
    }
    private function decodeBoolean($value, $rawValue)
    {
        if ($value === '1' && ($rawValue === 'true' || $rawValue === 'yes' || $rawValue === 'on')) {
            return \true;
        }
        if ($value === '' && ($rawValue === 'false' || $rawValue === 'no' || $rawValue === 'off')) {
            return \false;
        }
        return $value;
    }
    private function decodeNull($value, $rawValue)
    {
        if ($value === '' && $rawValue === 'null') {
            return null;
        }
        return $value;
    }
    private function noLossWhenCastToInt($value)
    {
        return (string) ($value + 0) === $value;
    }
    /**
     * @return bool
     */
    public function isUseNativeFunction()
    {
        return $this->useNativeFunction;
    }
    /**
     * @param bool $useNativeFunction
     *
     * @return IniReader
     */
    public function setUseNativeFunction($useNativeFunction)
    {
        $this->useNativeFunction = $useNativeFunction;
        return $this;
    }
}
