<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Matomo\Ini;

/**
 * Writes INI configuration.
 */
class IniWriter
{
    /**
     * Writes an array configuration to a INI file.
     *
     * The array provided must be multidimensional, indexed by section names:
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
     * @param string $filename
     * @param array $config
     * @param string $header Optional header to insert at the top of the file.
     * @throws IniWritingException
     */
    public function writeToFile($filename, array $config, $header = '')
    {
        $ini = $this->writeToString($config, $header);
        if (!file_put_contents($filename, $ini)) {
            throw new \Matomo\Ini\IniWritingException(sprintf('Impossible to write to file %s', $filename));
        }
    }
    /**
     * Writes an array configuration to a INI string and returns it.
     *
     * The array provided must be multidimensional, indexed by section names:
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
     * @param array $config
     * @param string $header Optional header to insert at the top of the file.
     * @return string
     * @throws IniWritingException
     */
    public function writeToString(array $config, $header = '')
    {
        $ini = $header;
        $sectionNames = array_keys($config);
        foreach ($sectionNames as $sectionName) {
            $section = $config[$sectionName];
            // no point in writing empty sections
            if (empty($section)) {
                continue;
            }
            if (!is_array($section)) {
                throw new \Matomo\Ini\IniWritingException(sprintf("Section \"%s\" doesn't contain an array of values", $sectionName));
            }
            $sectionName = $this->encodeSectionName($sectionName);
            $ini .= "[{$sectionName}]\n";
            foreach ($section as $option => $value) {
                if (is_numeric($option)) {
                    $option = $sectionName;
                    $value = array($value);
                }
                if (is_array($value)) {
                    foreach ($value as $key => $currentValue) {
                        if (is_int($key)) {
                            $ini .= $this->encodeKey($option) . '[] = ' . $this->encodeValue($currentValue) . "\n";
                        } else {
                            $ini .= $this->encodeKey($option) . '[' . $this->encodeKey($key) . '] = ' . $this->encodeValue($currentValue) . "\n";
                        }
                    }
                } else {
                    $ini .= $option . ' = ' . $this->encodeValue($value) . "\n";
                }
            }
            $ini .= "\n";
        }
        return $ini;
    }
    /**
     * @param $value
     * @return int|string
     */
    private function encodeValue($value)
    {
        if (is_bool($value)) {
            return (int) $value;
        }
        if (is_string($value)) {
            // remove any quotes w/ newlines after it since INI parsing will consider it the end of the string
            $value = preg_replace('/\\"[\\n\\r]/', "\n", $value);
            $value = addcslashes($value, '"');
            return '"' . $value . '"';
        }
        return $value;
    }
    /**
     * @param $key
     * @return string
     */
    private function encodeKey($key)
    {
        $key = preg_replace('/[^A-Za-z0-9\\-_]/', '', $key);
        return $key;
    }
    /**
     * @param $key
     * @return string
     */
    private function encodeSectionName($key)
    {
        $key = preg_replace('/[^A-Za-z0-9_ \\-]/', '', $key);
        return $key;
    }
}
