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
use Piwik\Piwik;
use Exception;
use Piwik\Plugins\TagManager\Input\Description;
use Piwik\Plugins\TagManager\Input\Name;
class ContainerVersionsDao extends \Piwik\Plugins\TagManager\Dao\BaseDao implements \Piwik\Plugins\TagManager\Dao\TagManagerDao
{
    public const REVISION_DRAFT = 0;
    protected $table = 'tagmanager_container_version';
    public function install()
    {
        DbHelper::createTable($this->table, "\n                  `idcontainerversion` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n                  `idcontainer` VARCHAR(8) NOT NULL,\n                  `idsite` int(11) UNSIGNED NOT NULL,\n                  `status` VARCHAR(10) NOT NULL,\n                  `revision` MEDIUMINT UNSIGNED NOT NULL DEFAULT 1,\n                  `name` VARCHAR(" . Name::MAX_LENGTH . ") NOT NULL DEFAULT '',\n                  `description` VARCHAR(" . Description::MAX_LENGTH . ") NOT NULL DEFAULT '',\n                  `created_date` DATETIME NOT NULL,\n                  `updated_date` DATETIME NOT NULL,\n                  `deleted_date` DATETIME NULL,\n                  PRIMARY KEY(`idcontainerversion`), KEY(`idcontainer`), KEY (`idsite`, `idcontainer`)");
        // we cannot set a unique key on (`idsite`, `idcontainerversion`, `name`) because we soft delete tags and want to make sure names can be used again after deleting an entry
    }
    private function isNameInUse($idSite, $idContainer, $name, $exceptIdVersion = null)
    {
        $sql = sprintf("SELECT idcontainerversion FROM %s WHERE idsite = ? AND idcontainer = ? AND `name` = ? AND status = ?", $this->tablePrefixed);
        $bind = array($idSite, $idContainer, $name, self::STATUS_ACTIVE);
        if (!empty($exceptIdVersion)) {
            $sql .= ' AND idcontainerversion != ?';
            $bind[] = $exceptIdVersion;
        }
        $idSite = Db::fetchOne($sql, $bind);
        return !empty($idSite);
    }
    public function getNextRevisionOfContainer($idSite, $idContainer)
    {
        $sql = "SELECT max(revision) FROM " . $this->tablePrefixed . " WHERE idsite = ? and idcontainer = ?";
        $revision = Db::fetchOne($sql, array($idSite, $idContainer));
        if (empty($revision)) {
            return 1;
        }
        return $revision + 1;
    }
    private function hasDraftVersionAlready($idSite, $idContainer)
    {
        $sql = sprintf("SELECT idsite FROM %s WHERE idsite = ? AND idcontainer = ? AND `revision` = 0 AND status = ?", $this->tablePrefixed);
        $bind = array($idSite, $idContainer, self::STATUS_ACTIVE);
        $idSite = Db::fetchOne($sql, $bind);
        return !empty($idSite);
    }
    public function createDraftVersion($idSite, $idContainer, $createdDate)
    {
        if ($this->hasDraftVersionAlready($idSite, $idContainer)) {
            throw new Exception('A draft version for this container already exists');
        }
        $values = array('idcontainer' => $idContainer, 'idsite' => $idSite, 'status' => self::STATUS_ACTIVE, 'created_date' => $createdDate, 'updated_date' => $createdDate, 'revision' => self::REVISION_DRAFT);
        return $this->insertRecord($values);
    }
    public function createVersion($idSite, $idContainer, $versionName, $versionDescription, $createdDate)
    {
        if ($this->isNameInUse($idSite, $idContainer, $versionName)) {
            throw new Exception(Piwik::translate('TagManager_ErrorNameDuplicate'));
        }
        $revision = $this->getNextRevisionOfContainer($idSite, $idContainer);
        $values = array('idcontainer' => $idContainer, 'idsite' => $idSite, 'status' => self::STATUS_ACTIVE, 'name' => $versionName, 'description' => !empty($versionDescription) ? $versionDescription : '', 'created_date' => $createdDate, 'updated_date' => $createdDate, 'revision' => $revision);
        return $this->insertRecord($values);
    }
    /**
     * @param int $idSite
     * @param array $statuses
     * @return array
     */
    public function getVersionsOfContainer($idSite, $idContainer)
    {
        $bind = array(self::STATUS_ACTIVE, $idSite, $idContainer);
        $table = $this->tablePrefixed;
        $versions = Db::fetchAll("SELECT * FROM {$table} WHERE status = ? AND idsite = ? and idcontainer = ? and revision > 0 order by revision desc", $bind);
        return $this->enrichVersions($versions);
    }
    /**
     * @param int $idSite
     * @param array $statuses
     * @return array
     */
    public function getDraftVersion($idSite, $idContainer)
    {
        $bind = array(self::STATUS_ACTIVE, $idSite, $idContainer);
        $table = $this->tablePrefixed;
        $version = Db::fetchRow("SELECT * FROM {$table} WHERE status = ? AND idsite = ? and idcontainer = ? and revision = 0 LIMIT 1", $bind);
        return $this->enrichVersion($version);
    }
    /**
     * @param int $idSite
     * @param array $statuses
     * @return array
     */
    public function getVersion($idSite, $idContainer, $idContainerVersion)
    {
        $bind = array(self::STATUS_ACTIVE, $idSite, $idContainer, $idContainerVersion);
        $table = $this->tablePrefixed;
        $version = Db::fetchRow("SELECT * FROM {$table} WHERE status = ? AND idsite = ? and idcontainer = ? and idcontainerversion = ? LIMIT 1", $bind);
        return $this->enrichVersion($version);
    }
    public function updateContainerColumns($idSite, $idContainer, $idContainerVersion, $columns)
    {
        if (!empty($columns)) {
            if (isset($columns['description']) && empty($columns['description'])) {
                $columns['description'] = '';
            }
            if (isset($columns['name']) && $this->isNameInUse($idSite, $idContainer, $columns['name'], $idContainerVersion)) {
                throw new Exception(Piwik::translate('TagManager_ErrorNameDuplicate'));
            }
            $this->updateEntity($columns, ['idsite' => (int) $idSite, 'idcontainer' => $idContainer, 'idcontainerversion' => (int) $idContainerVersion]);
        }
    }
    public function getAllVersions()
    {
        $containers = Db::fetchAll('SELECT * FROM ' . $this->tablePrefixed . ' ORDER BY idcontainerversion ASC');
        return $this->enrichVersions($containers);
    }
    /**
     * @param int $idSite
     * @param string $deletedDate
     */
    public function deleteAllVersionsForSite($idSite, $deletedDate)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ?, deleted_date = ? WHERE idsite = ? and status != ?";
        $bind = array(self::STATUS_DELETED, $deletedDate, $idSite, self::STATUS_DELETED);
        Db::query($query, $bind);
    }
    /**
     * @param int $idSite
     * @param int $idContainerVersion
     * @param string $deletedDate
     */
    public function deleteVersion($idSite, $idContainerVersion, $deletedDate)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ?, deleted_date = ? WHERE idsite = ? and idcontainerversion = ? and status != ?";
        $bind = array(self::STATUS_DELETED, $deletedDate, $idSite, $idContainerVersion, self::STATUS_DELETED);
        Db::query($query, $bind);
    }
    protected function isNameAlreadyUsed(int $idSite, string $name, ?int $idContainerVersion = null) : bool
    {
        // Look up the container ID using the version ID
        $bind = array(self::STATUS_ACTIVE, $idSite, $idContainerVersion);
        $table = $this->tablePrefixed;
        $version = Db::fetchRow("SELECT idcontainer FROM {$table} WHERE status = ? AND idsite = ? AND idcontainerversion = ? LIMIT 1", $bind);
        if (empty($version['idcontainer'])) {
            return \false;
        }
        return $this->isNameInUse($idSite, $version['idcontainer'], $name);
    }
    private function enrichVersions($containers)
    {
        if (empty($containers)) {
            return array();
        }
        foreach ($containers as $index => $container) {
            $containers[$index] = $this->enrichVersion($container);
        }
        return $containers;
    }
    private function enrichVersion($container)
    {
        if (empty($container)) {
            return $container;
        }
        $container['idcontainerversion'] = (int) $container['idcontainerversion'];
        $container['revision'] = (int) $container['revision'];
        $container['idsite'] = (int) $container['idsite'];
        return $container;
    }
}
