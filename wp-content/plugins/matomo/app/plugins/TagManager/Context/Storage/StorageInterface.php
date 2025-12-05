<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Context\Storage;

interface StorageInterface
{
    public function save($name, $data);
    public function delete($name);
    public function find($sDir, $sPattern);
}
