<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\AssetManager;

abstract class UIAsset
{
    public abstract function validateFile();
    /**
     * @return string
     */
    public abstract function getAbsoluteLocation();
    /**
     * @return string
     */
    public abstract function getRelativeLocation();
    /**
     * @return string
     */
    public abstract function getBaseDirectory();
    /**
     * Removes the previous file if it exists.
     * Also tries to remove compressed version of the file.
     *
     * @see ProxyStaticFile::serveStaticFile(serveFile
     * @throws Exception if the file couldn't be deleted
     */
    public abstract function delete();
    /**
     * @param string $content
     * @throws \Exception
     */
    public abstract function writeContent($content);
    /**
     * @return string
     */
    public abstract function getContent();
    /**
     * @return boolean
     */
    public abstract function exists();
    /**
     * @return int
     */
    public abstract function getModificationDate();
}
