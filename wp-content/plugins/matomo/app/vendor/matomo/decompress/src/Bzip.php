<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL v3 or later
 */
namespace Matomo\Decompress;

/**
 * Unzip implementation for .bz2 files.
 */
class Bzip implements \Matomo\Decompress\DecompressInterface
{
    /**
     * Name of .bz2 file.
     *
     * @var string
     */
    private $filename = null;
    /**
     * Error string.
     *
     * @var string
     */
    private $error = null;
    /**
     * Constructor.
     *
     * @param string $filename Name of .bz2 file.
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }
    /**
     * Extracts the contents of the .bz2 file to $pathExtracted.
     *
     * @param string $pathExtracted Must be file, not directory.
     * @return bool true if successful, false if otherwise.
     */
    public function extract($pathExtracted)
    {
        $file = @bzopen($this->filename, 'r');
        if ($file === \false) {
            $this->error = "bzopen failed";
            return \false;
        }
        $output = fopen($pathExtracted, 'w');
        while (!feof($file)) {
            fwrite($output, fread($file, 1024 * 1024));
        }
        fclose($output);
        $success = bzclose($file);
        if (\false === $success) {
            $this->error = "bzclose failed";
            return \false;
        }
        return \true;
    }
    /**
     * Get error status string for the latest error.
     *
     * @return string
     */
    public function errorInfo()
    {
        return $this->error;
    }
}
