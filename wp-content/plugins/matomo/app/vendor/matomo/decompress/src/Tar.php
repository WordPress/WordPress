<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL v3 or later
 */
namespace Matomo\Decompress;

use \Matomo\Dependencies\Archive_Tar;
/**
 * Unzip implementation for \Matomo\Dependencies\Archive_Tar \Matomo\Dependencies\PEAR lib.
 */
class Tar implements \Matomo\Decompress\DecompressInterface
{
    /**
     * \Matomo\Dependencies\Archive_Tar instance.
     *
     * @var \Matomo\Dependencies\Archive_Tar
     */
    private $tarArchive = null;
    /**
     * Constructor.
     *
     * @param string $filename Path to tar file.
     * @param string|null $compression Either 'gz', 'bz2' or null for no compression.
     */
    public function __construct($filename, $compression = null)
    {
        $this->tarArchive = new \Matomo\Dependencies\Archive_Tar($filename, $compression);
    }
    /**
     * Extracts the contents of the tar file to $pathExtracted.
     *
     * @param string $pathExtracted Directory to extract into.
     * @return bool true if successful, false if otherwise.
     */
    public function extract($pathExtracted)
    {
        return $this->tarArchive->extract($pathExtracted);
    }
    /**
     * Extracts one file held in a tar archive and returns the deflated file
     * as a string.
     *
     * @param string $inArchivePath Path to file in the tar archive.
     * @return bool true if successful, false if otherwise.
     */
    public function extractInString($inArchivePath)
    {
        return $this->tarArchive->extractInString($inArchivePath);
    }
    /**
     * Lists the files held in the tar archive.
     *
     * @return array List of paths describing everything held in the tar archive.
     */
    public function listContent()
    {
        return $this->tarArchive->listContent();
    }
    /**
     * Get error status string for the latest error.
     *
     * @return string
     */
    public function errorInfo()
    {
        return $this->tarArchive->error_object->getMessage();
    }
}
