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
class VariablesDao extends \Piwik\Plugins\TagManager\Dao\BaseDao implements \Piwik\Plugins\TagManager\Dao\TagManagerDao
{
    protected $table = 'tagmanager_variable';
    public function install()
    {
        DbHelper::createTable($this->table, "\n                  `idvariable` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n                  `idcontainerversion` BIGINT UNSIGNED NOT NULL,\n                  `idsite` int(11) UNSIGNED NOT NULL,\n                  `type` VARCHAR(50) NOT NULL,\n                  `name` VARCHAR(" . Name::MAX_LENGTH . ") NOT NULL,\n                  `description` VARCHAR(" . Description::MAX_LENGTH . ") NOT NULL,\n                  `status` VARCHAR(10) NOT NULL,\n                  `parameters` MEDIUMTEXT NOT NULL DEFAULT '',\n                  `lookup_table` MEDIUMTEXT NOT NULL DEFAULT '',\n                  `default_value` TEXT NULL,\n                  `created_date` DATETIME NOT NULL,\n                  `updated_date` DATETIME NOT NULL,\n                  `deleted_date` DATETIME NULL,\n                  PRIMARY KEY(`idvariable`), KEY (`idsite`, `idcontainerversion`)");
        // we cannot set a unique key on (`idsite`, `idcontainerversion`, `name`) because we soft delete tags and want to make sure names can be used again after deleting an entry
    }
    private function isNameInUse($idSite, $idContainerVersion, $name, $exceptIdVariable = null)
    {
        $sql = sprintf("SELECT idvariable FROM %s WHERE idsite = ? AND idcontainerversion = ? AND `name` = ? AND status = ?", $this->tablePrefixed);
        $bind = array($idSite, $idContainerVersion, $name, self::STATUS_ACTIVE);
        if (!empty($exceptIdVariable)) {
            $sql .= ' AND idvariable != ?';
            $bind[] = $exceptIdVariable;
        }
        $idSite = Db::fetchOne($sql, $bind);
        return !empty($idSite);
    }
    public function createVariable($idSite, $idContainerVersion, $type, $name, $parameters, $defaultValue, $lookupTable, $createdDate, $description = '')
    {
        if ($this->isNameInUse($idSite, $idContainerVersion, $name)) {
            throw new Exception(Piwik::translate('TagManager_ErrorNameDuplicate'));
        }
        $values = array('idsite' => $idSite, 'idcontainerversion' => $idContainerVersion, 'status' => self::STATUS_ACTIVE, 'type' => $type, 'name' => $name, 'description' => $description, 'parameters' => $parameters, 'lookup_table' => $lookupTable, 'default_value' => $defaultValue, 'created_date' => $createdDate, 'updated_date' => $createdDate);
        $values = $this->encodeFieldsWhereNeeded($values);
        return $this->insertRecord($values);
    }
    public function updateVariableColumns($idSite, $idContainerVersion, $idVariable, $columns)
    {
        $columns = $this->encodeFieldsWhereNeeded($columns);
        if (!empty($columns)) {
            if (isset($columns['name']) && $this->isNameInUse($idSite, $idContainerVersion, $columns['name'], $idVariable)) {
                throw new Exception(Piwik::translate('TagManager_ErrorNameDuplicate'));
            }
            $this->updateEntity($columns, ['idsite' => (int) $idSite, 'idcontainerversion' => (int) $idContainerVersion, 'idvariable' => (int) $idVariable]);
        }
    }
    private function encodeFieldsWhereNeeded($columns)
    {
        if (!empty($columns['parameters'])) {
            $columns['parameters'] = json_encode($columns['parameters']);
        } elseif (isset($columns['parameters'])) {
            $columns['parameters'] = '';
        }
        if (!empty($columns['lookup_table'])) {
            $columns['lookup_table'] = json_encode($columns['lookup_table']);
        } elseif (isset($columns['lookup_table'])) {
            $columns['lookup_table'] = '';
        }
        return $columns;
    }
    public function getAllVariables()
    {
        $variables = Db::fetchAll('SELECT * FROM ' . $this->tablePrefixed . ' ORDER BY idvariable ASC');
        return $this->enrichVariables($variables);
    }
    /**
     * @param int $idSite
     * @param int $idContainerVersion
     * @return array
     */
    public function getContainerVariables($idSite, $idContainerVersion)
    {
        $bind = array(self::STATUS_ACTIVE, $idSite, $idContainerVersion);
        $table = $this->tablePrefixed;
        $variables = Db::fetchAll("SELECT * FROM {$table} WHERE status = ? AND idsite = ? and idcontainerversion = ? ORDER BY created_date ASC", $bind);
        return $this->enrichVariables($variables);
    }
    /**
     * @param $idSite
     * @param $idContainerVersion
     * @param $idVariable
     * @return array|false
     * @throws \Exception
     */
    public function getContainerVariable($idSite, $idContainerVersion, $idVariable)
    {
        $table = $this->tablePrefixed;
        $bind = array(self::STATUS_ACTIVE, $idVariable, $idContainerVersion, $idSite);
        $variable = Db::fetchRow("SELECT * FROM {$table} WHERE status = ? and idvariable = ? and idcontainerversion = ? and idsite = ?", $bind);
        return $this->enrichVariable($variable);
    }
    /**
     * @param $idSite
     * @param $variableName
     * @return array|false
     * @throws \Exception
     */
    public function findVariableByName($idSite, $idContainerVersion, $variableName)
    {
        $table = $this->tablePrefixed;
        $bind = array(self::STATUS_ACTIVE, $idSite, $idContainerVersion, $variableName);
        $variable = Db::fetchRow("SELECT * FROM {$table} WHERE status = ? and idsite = ? and idcontainerversion = ? and `name` = ?", $bind);
        return $this->enrichVariable($variable);
    }
    /**
     * @param int $idSite
     * @param string $deletedDate
     */
    public function deleteVariablesForSite($idSite, $deletedDate)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ?, deleted_date = ? WHERE idsite = ? and status != ?";
        $bind = array(self::STATUS_DELETED, $deletedDate, $idSite, self::STATUS_DELETED);
        Db::query($query, $bind);
    }
    /**
     * @param int $idSite
     * @param int $idContainerVersion
     * @param int $idVariable
     * @param string $deletedDate
     */
    public function deleteContainerVariable($idSite, $idContainerVersion, $idVariable, $deletedDate)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ?, deleted_date = ? WHERE idsite = ? and idcontainerversion = ? and idvariable = ? and status != ?";
        $bind = array(self::STATUS_DELETED, $deletedDate, $idSite, $idContainerVersion, $idVariable, self::STATUS_DELETED);
        Db::query($query, $bind);
    }
    /**
     * @param int $idSite
     * @param int $idContainerVersion
     * @param string $variableType The type of variable to filter by, such as 'MatomoConfiguration'
     * @return array
     */
    public function getContainerVariableIdsByType($idSite, $idContainerVersion, $variableType)
    {
        $bind = [self::STATUS_ACTIVE, $idSite, $idContainerVersion, $variableType];
        $table = $this->tablePrefixed;
        $variables = Db::fetchAll("SELECT idvariable FROM {$table} WHERE status = ? AND idsite = ? and idcontainerversion = ? and type = ? ORDER BY created_date ASC", $bind);
        return is_array($variables) && count($variables) ? array_column($variables, 'idvariable') : [];
    }
    protected function isNameAlreadyUsed(int $idSite, string $name, ?int $idContainerVersion = null) : bool
    {
        return $this->isNameInUse($idSite, $idContainerVersion, $name);
    }
    private function enrichVariables($variables)
    {
        if (empty($variables)) {
            return array();
        }
        foreach ($variables as $index => $variable) {
            $variables[$index] = $this->enrichVariable($variable);
        }
        return $variables;
    }
    private function enrichVariable($variable)
    {
        if (empty($variable)) {
            return $variable;
        }
        $variable['idvariable'] = (int) $variable['idvariable'];
        $variable['idsite'] = (int) $variable['idsite'];
        $variable['idcontainerversion'] = (int) $variable['idcontainerversion'];
        if (!empty($variable['parameters'])) {
            $variable['parameters'] = json_decode($variable['parameters'], \true);
        }
        if (empty($variable['parameters'])) {
            $variable['parameters'] = [];
        }
        if (!empty($variable['lookup_table'])) {
            $variable['lookup_table'] = json_decode($variable['lookup_table'], \true);
        }
        if (empty($variable['lookup_table'])) {
            $variable['lookup_table'] = [];
        }
        return $variable;
    }
}
