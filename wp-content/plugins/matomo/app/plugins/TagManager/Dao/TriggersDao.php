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
class TriggersDao extends \Piwik\Plugins\TagManager\Dao\BaseDao implements \Piwik\Plugins\TagManager\Dao\TagManagerDao
{
    protected $table = 'tagmanager_trigger';
    public function install()
    {
        DbHelper::createTable($this->table, "\n                  `idtrigger` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n                  `idcontainerversion` BIGINT UNSIGNED NOT NULL,\n                  `idsite` INT(11) UNSIGNED NOT NULL,\n                  `type` VARCHAR(50) NOT NULL,\n                  `name` VARCHAR(" . Name::MAX_LENGTH . ") NOT NULL,\n                  `description` VARCHAR(" . Description::MAX_LENGTH . ") NOT NULL,\n                  `status` VARCHAR(10) NOT NULL,\n                  `parameters` MEDIUMTEXT NOT NULL DEFAULT '',\n                  `conditions` MEDIUMTEXT NOT NULL DEFAULT '',\n                  `created_date` DATETIME NOT NULL,\n                  `updated_date` DATETIME NOT NULL,\n                  `deleted_date` DATETIME NULL,\n                  PRIMARY KEY(`idtrigger`), KEY (`idsite`, `idcontainerversion`)");
        // we cannot set a unique key on (`idsite`, `idcontainerversion`, `name`) because we soft delete tags and want to make sure names can be used again after deleting an entry
    }
    private function isNameInUse($idSite, $idContainerVersion, $name, $exceptIdTrigger = null)
    {
        $sql = sprintf("SELECT idtrigger FROM %s WHERE idsite = ? AND idcontainerversion = ? AND `name` = ? AND status = ?", $this->tablePrefixed);
        $bind = array($idSite, $idContainerVersion, $name, self::STATUS_ACTIVE);
        if (!empty($exceptIdTrigger)) {
            $sql .= ' AND idtrigger != ?';
            $bind[] = $exceptIdTrigger;
        }
        $idSite = Db::fetchOne($sql, $bind);
        return !empty($idSite);
    }
    public function createTrigger($idSite, $idContainerVersion, $type, $name, $parameters, $conditions, $createdDate, $description = '')
    {
        if ($this->isNameInUse($idSite, $idContainerVersion, $name)) {
            throw new Exception(Piwik::translate('TagManager_ErrorNameDuplicate'));
        }
        $values = array('idsite' => $idSite, 'idcontainerversion' => $idContainerVersion, 'status' => self::STATUS_ACTIVE, 'type' => $type, 'name' => $name, 'description' => $description, 'parameters' => $parameters, 'conditions' => $conditions, 'created_date' => $createdDate, 'updated_date' => $createdDate);
        $values = $this->encodeFieldsWhereNeeded($values);
        return $this->insertRecord($values);
    }
    public function updateTriggerColumns($idSite, $idContainerVersion, $idTrigger, $columns)
    {
        $columns = $this->encodeFieldsWhereNeeded($columns);
        if (!empty($columns)) {
            if (isset($columns['name']) && $this->isNameInUse($idSite, $idContainerVersion, $columns['name'], $idTrigger)) {
                throw new Exception(Piwik::translate('TagManager_ErrorNameDuplicate'));
            }
            $this->updateEntity($columns, ['idsite' => (int) $idSite, 'idcontainerversion' => (int) $idContainerVersion, 'idtrigger' => (int) $idTrigger]);
        }
    }
    private function encodeFieldsWhereNeeded($columns)
    {
        if (!empty($columns['parameters'])) {
            $columns['parameters'] = json_encode($columns['parameters']);
        } elseif (isset($columns['parameters'])) {
            $columns['parameters'] = '';
        }
        if (!empty($columns['conditions'])) {
            $columns['conditions'] = json_encode($columns['conditions']);
        } elseif (isset($columns['conditions'])) {
            $columns['conditions'] = '';
        }
        return $columns;
    }
    public function getAllTriggers()
    {
        $triggers = Db::fetchAll('SELECT * FROM ' . $this->tablePrefixed . ' ORDER BY idtrigger ASC');
        return $this->enrichTriggers($triggers);
    }
    /**
     * @param int $idSite
     * @param int $idContainerVersion
     * @return array
     */
    public function getContainerTriggers($idSite, $idContainerVersion)
    {
        $bind = array(self::STATUS_ACTIVE, $idSite, $idContainerVersion);
        $table = $this->tablePrefixed;
        $triggers = Db::fetchAll("SELECT * FROM {$table} WHERE status = ? AND idsite = ? and idcontainerversion = ? ORDER BY created_date ASC", $bind);
        return $this->enrichTriggers($triggers);
    }
    /**
     * @param $idSite
     * @param $idContainerVersion
     * @param $idTrigger
     * @return array|false
     * @throws \Exception
     */
    public function getContainerTrigger($idSite, $idContainerVersion, $idTrigger)
    {
        $table = $this->tablePrefixed;
        $bind = array(self::STATUS_ACTIVE, $idTrigger, $idContainerVersion, $idSite);
        $trigger = Db::fetchRow("SELECT * FROM {$table} WHERE status = ? and idtrigger = ? and idcontainerversion = ? and idsite = ?", $bind);
        return $this->enrichTrigger($trigger);
    }
    /**
     * Look up the trigger using its name
     *
     * @param int $idSite
     * @param int  $idContainerVersion
     * @param string $triggerName
     * @return array|false
     * @throws \Exception
     */
    public function findTriggerByName(int $idSite, int $idContainerVersion, string $triggerName)
    {
        $table = $this->tablePrefixed;
        $bind = array($idSite, $idContainerVersion, self::STATUS_ACTIVE, $triggerName);
        $trigger = Db::fetchRow("SELECT * FROM {$table} WHERE idsite = ? AND idcontainerversion = ? AND status = ? AND `name` = ?", $bind);
        return $this->enrichTrigger($trigger);
    }
    /**
     * @param int $idSite
     * @param string $deletedDate
     */
    public function deleteTriggersForSite($idSite, $deletedDate)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ?, deleted_date = ? WHERE idsite = ? and status != ?";
        $bind = array(self::STATUS_DELETED, $deletedDate, $idSite, self::STATUS_DELETED);
        Db::query($query, $bind);
    }
    /**
     * @param int $idSite
     * @param int $idContainerVersion
     * @param int $idTrigger
     * @param string $deletedDate
     */
    public function deleteContainerTrigger($idSite, $idContainerVersion, $idTrigger, $deletedDate)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ?, deleted_date = ? WHERE idsite = ? and idcontainerversion = ? and idtrigger = ? and status != ?";
        $bind = array(self::STATUS_DELETED, $deletedDate, $idSite, $idContainerVersion, $idTrigger, self::STATUS_DELETED);
        Db::query($query, $bind);
    }
    protected function isNameAlreadyUsed(int $idSite, string $name, ?int $idContainerVersion = null) : bool
    {
        return $this->isNameInUse($idSite, $idContainerVersion, $name);
    }
    private function enrichTriggers($triggers)
    {
        if (empty($triggers)) {
            return array();
        }
        foreach ($triggers as $index => $trigger) {
            $triggers[$index] = $this->enrichTrigger($trigger);
        }
        return $triggers;
    }
    private function enrichTrigger($trigger)
    {
        if (empty($trigger)) {
            return $trigger;
        }
        $trigger['idtrigger'] = (int) $trigger['idtrigger'];
        $trigger['idsite'] = (int) $trigger['idsite'];
        $trigger['idcontainerversion'] = (int) $trigger['idcontainerversion'];
        if (!empty($trigger['parameters'])) {
            $trigger['parameters'] = json_decode($trigger['parameters'], \true);
        }
        if (empty($trigger['parameters'])) {
            $trigger['parameters'] = [];
        }
        if (!empty($trigger['conditions'])) {
            $trigger['conditions'] = json_decode($trigger['conditions'], \true);
        }
        if (empty($trigger['conditions'])) {
            $trigger['conditions'] = [];
        }
        return $trigger;
    }
}
