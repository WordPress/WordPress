<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Dao;

use Piwik\Db;
use Piwik\DbHelper;
use Piwik\Plugins\TagManager\Input\Description;
use Piwik\Plugins\TagManager\Input\Name;
use Piwik\Piwik;
use Exception;
class ContainersDao extends \Piwik\Plugins\TagManager\Dao\BaseDao implements \Piwik\Plugins\TagManager\Dao\TagManagerDao
{
    public const ERROR_NAME_IN_USE = 2919;
    protected $table = 'tagmanager_container';
    public function install()
    {
        DbHelper::createTable($this->table, "\n                  `idcontainer` VARCHAR(8) NOT NULL,\n                  `idsite` int(11) UNSIGNED NOT NULL,\n                  `context` VARCHAR(10) NOT NULL,\n                  `name` VARCHAR(" . Name::MAX_LENGTH . ") NOT NULL,\n                  `description` VARCHAR(" . Description::MAX_LENGTH . ") NOT NULL DEFAULT '',\n                  `ignoreGtmDataLayer` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,\n                  `activelySyncGtmDataLayer` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,\n                  `isTagFireLimitAllowedInPreviewMode` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,\n                  `status` VARCHAR(10) NOT NULL,\n                  `created_date` DATETIME NOT NULL,\n                  `updated_date` DATETIME NOT NULL,\n                  `deleted_date` DATETIME NULL,\n                  PRIMARY KEY(`idcontainer`), KEY (`idsite`)");
        // we cannot set a unique key on (`idsite`, `name`) because we soft delete tags and want to make sure names can be used again after deleting an entry
    }
    private function isNameInUse($idSite, $name, $exceptIdContainer = null)
    {
        $sql = sprintf("SELECT idcontainer FROM %s WHERE idsite = ? AND `name` = ? AND status = ?", $this->tablePrefixed);
        $bind = array($idSite, $name, self::STATUS_ACTIVE);
        if (!empty($exceptIdContainer)) {
            $sql .= ' AND idcontainer != ?';
            $bind[] = $exceptIdContainer;
        }
        $idSite = Db::fetchOne($sql, $bind);
        return !empty($idSite);
    }
    private function isContainerInUse($idContainer)
    {
        $sql = sprintf("SELECT idcontainer FROM %s WHERE idcontainer = ?", $this->tablePrefixed);
        $bind = array($idContainer);
        $idSite = Db::fetchOne($sql, $bind);
        return !empty($idSite);
    }
    public function hasContainer($idContainer)
    {
        $table = $this->tablePrefixed;
        $bind = array($idContainer);
        $container = Db::fetchOne("SELECT idcontainer FROM {$table} WHERE idcontainer = ?", $bind);
        return !empty($container);
    }
    public function createContainer($idSite, $idContainer, $context, $name, $description, $createdDate, $ignoreGtmDataLayer, $isTagFireLimitAllowedInPreviewMode, $activelySyncGtmDataLayer)
    {
        if ($this->isContainerInUse($idContainer)) {
            throw new Exception(Piwik::translate('TagManager_ErrorContainerIdDuplicate'));
        }
        if ($this->isNameInUse($idSite, $name)) {
            throw new Exception(Piwik::translate('TagManager_ErrorNameDuplicate'), self::ERROR_NAME_IN_USE);
        }
        $status = \Piwik\Plugins\TagManager\Dao\ContainersDao::STATUS_ACTIVE;
        $values = array('idsite' => $idSite, 'idcontainer' => $idContainer, 'context' => $context, 'name' => $name, 'description' => !empty($description) ? $description : '', 'ignoreGtmDataLayer' => !empty($ignoreGtmDataLayer) ? $ignoreGtmDataLayer : 0, 'activelySyncGtmDataLayer' => !empty($activelySyncGtmDataLayer) ? $activelySyncGtmDataLayer : 0, 'isTagFireLimitAllowedInPreviewMode' => !empty($isTagFireLimitAllowedInPreviewMode) ? $isTagFireLimitAllowedInPreviewMode : 0, 'status' => $status, 'created_date' => $createdDate, 'updated_date' => $createdDate);
        $this->insertRecord($values);
        return $values['idcontainer'];
    }
    public function updateContainerColumns($idSite, $idContainer, $columns)
    {
        if (!empty($columns)) {
            if (isset($columns['description']) && empty($columns['description'])) {
                $columns['description'] = '';
            }
            if (isset($columns['name']) && $this->isNameInUse($idSite, $columns['name'], $idContainer)) {
                throw new Exception(Piwik::translate('TagManager_ErrorNameDuplicate'), self::ERROR_NAME_IN_USE);
            }
            $this->updateEntity($columns, ['idsite' => (int) $idSite, 'idcontainer' => $idContainer]);
        }
    }
    /**
     * @return int
     */
    public function getNumContainersTotal()
    {
        $sql = sprintf("SELECT COUNT(*) as containers FROM %s WHERE `status` = ?", $this->tablePrefixed);
        return (int) Db::fetchOne($sql, array(self::STATUS_ACTIVE));
    }
    /**
     * @return int
     */
    public function getNumContainersInSite($idSite)
    {
        $sql = sprintf("SELECT COUNT(*) as containers FROM %s WHERE `status` = ? and `idsite` = ?", $this->tablePrefixed);
        return (int) Db::fetchOne($sql, array(self::STATUS_ACTIVE, $idSite));
    }
    /**
     * @return array
     */
    public function getActiveContainersInfo()
    {
        $sql = sprintf("SELECT idcontainer, idsite FROM %s WHERE `status` = ?", $this->tablePrefixed);
        return Db::fetchAll($sql, array(self::STATUS_ACTIVE));
    }
    public function getAllContainers()
    {
        $containers = Db::fetchAll('SELECT * FROM ' . $this->tablePrefixed . ' ORDER BY idcontainer ASC');
        return $this->enrichContainers($containers);
    }
    /**
     * @param int $idSite
     * @return array
     */
    public function getContainersForSite($idSite)
    {
        $table = $this->tablePrefixed;
        $containers = Db::fetchAll("SELECT * FROM {$table} WHERE idsite = ? and status = ? ORDER BY created_date ASC, name ASC", array($idSite, self::STATUS_ACTIVE));
        return $this->enrichContainers($containers);
    }
    /**
     * @param $idSite
     * @param $idContainer
     * @return array|false
     */
    public function getContainer($idSite, $idContainer)
    {
        $table = $this->tablePrefixed;
        $bind = array(self::STATUS_ACTIVE, $idSite, $idContainer);
        $container = Db::fetchRow("SELECT * FROM {$table} WHERE status = ? and idsite = ? and idcontainer = ?", $bind);
        return $this->enrichContainer($container);
    }
    /**
     * @param int $idSite
     * @param string $deletedDate
     */
    public function deleteContainersForSite($idSite, $deletedDate)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ?, deleted_date = ? WHERE idsite = ? and status != ?";
        $bind = array(self::STATUS_DELETED, $deletedDate, $idSite, self::STATUS_DELETED);
        Db::query($query, $bind);
    }
    /**
     * @param int $idSite
     * @param int $idContainer
     * @param string $deletedDate
     */
    public function deleteContainer($idSite, $idContainer, $deletedDate)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ?, deleted_date = ? WHERE idsite = ? and idcontainer = ? and status != ?";
        $bind = array(self::STATUS_DELETED, $deletedDate, $idSite, $idContainer, self::STATUS_DELETED);
        Db::query($query, $bind);
    }
    protected function isNameAlreadyUsed(int $idSite, string $name, ?int $idContainerVersion = null) : bool
    {
        return $this->isNameInUse($idSite, $name);
    }
    private function enrichContainers($containers)
    {
        if (empty($containers)) {
            return array();
        }
        foreach ($containers as $index => $container) {
            $containers[$index] = $this->enrichContainer($container);
        }
        return $containers;
    }
    private function enrichContainer($container)
    {
        if (empty($container)) {
            return $container;
        }
        $container['idsite'] = (int) $container['idsite'];
        return $container;
    }
}
