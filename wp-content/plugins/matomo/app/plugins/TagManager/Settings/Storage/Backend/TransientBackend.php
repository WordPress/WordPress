<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Settings\Storage\Backend;

use Piwik\Settings\Storage;
class TransientBackend implements Storage\Backend\BackendInterface
{
    /**
     * @var array
     */
    private $values;
    private $storageId;
    public function __construct($storageId, $values = [])
    {
        $this->values = $values;
        $this->storageId = 'transient_' . $storageId;
    }
    public function load()
    {
        return $this->values;
    }
    public function getStorageId()
    {
        return $this->storageId;
    }
    public function delete()
    {
        $this->values = [];
    }
    public function save($values)
    {
        $this->values = $values;
    }
}
