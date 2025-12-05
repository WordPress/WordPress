<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
declare (strict_types=1);
namespace DeviceDetector\Yaml;

use Exception;
/**
 * Class Pecl
 *
 * Parses a YAML file with LibYAML library
 * @see http://php.net/manual/en/function.yaml-parse-file.php
 */
class Pecl implements \DeviceDetector\Yaml\ParserInterface
{
    /**
     * Parses the file with the given filename using PECL and returns the converted content
     * @param string $file The path to the YAML file to be parsed
     *
     * @return mixed The YAML converted to a PHP value or FALSE on failure
     *
     * @throws Exception If the YAML extension is not installed
     */
    public function parseFile(string $file)
    {
        if (\false === \function_exists('yaml_parse_file')) {
            throw new Exception('Pecl YAML extension is not installed');
        }
        return \yaml_parse_file($file);
    }
}
