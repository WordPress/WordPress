<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Dao;

use Piwik\Common;
use Piwik\Date;
use Piwik\Db;
use Piwik\Plugins\TagManager\Input\Name;
abstract class BaseDao
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DELETED = 'deleted';
    public const STATUS_PAUSED = 'paused';
    protected $table = '';
    protected $tablePrefixed = '';
    public function __construct()
    {
        $this->tablePrefixed = Common::prefixTable($this->table);
    }
    public function uninstall()
    {
        Db::query(sprintf('DROP TABLE IF EXISTS `%s`', $this->tablePrefixed));
    }
    protected function insertRecord($values)
    {
        $columns = implode('`,`', array_keys($values));
        $fields = Common::getSqlStringFieldsArray($values);
        $sql = sprintf('INSERT INTO %s (`%s`) VALUES(%s)', $this->tablePrefixed, $columns, $fields);
        $bind = array_values($values);
        Db::query($sql, $bind);
        $id = Db::get()->lastInsertId();
        return (int) $id;
    }
    public function updateEntity($columns, $whereColumns)
    {
        if (!empty($columns)) {
            $fields = array();
            $bind = array();
            foreach ($columns as $key => $value) {
                $fields[] = ' ' . $key . ' = ?';
                $bind[] = $value;
            }
            $fields = implode(',', $fields);
            $where = [];
            foreach ($whereColumns as $col => $val) {
                $where[] = '`' . $col . '` = ?';
                $bind[] = $val;
            }
            $where = implode(' AND ', $where);
            $query = sprintf('UPDATE %s SET %s WHERE %s', $this->tablePrefixed, $fields, $where);
            // we do not use $db->update() here as this method is as well used in Tracker mode and the tracker DB does not
            // support "->update()". Therefore we use the query method where we know it works with tracker and regular DB
            Db::query($query, $bind);
        }
    }
    /**
     * Make sure that the name is unique. This means appending a number at the end, or if there's already been a number
     * appended, increment the previous number. This way, when copying a tag/trigger/variable to the same site, we don't
     * get an error that the name is already in use.
     *
     * @param int $idSite
     * @param string $name
     * @param null|int $idContainerVersion Optional ID of the container version. It's only optional since containers
     * don't need it.
     * @return string
     * @throws \Exception Throws an exception if it's a Tag, Trigger, or Variable and doesn't have a idContainerVersion
     */
    public function makeCopyNameUnique(int $idSite, string $name, ?int $idContainerVersion = null) : string
    {
        $requireVersion = ['Piwik\\Plugins\\TagManager\\Dao\\TagsDao', 'Piwik\\Plugins\\TagManager\\Dao\\TriggersDao', 'Piwik\\Plugins\\TagManager\\Dao\\VariablesDao', 'Piwik\\Plugins\\TagManager\\Dao\\ContainerVersionsDao'];
        if (in_array(get_class($this), $requireVersion) && $idContainerVersion === null) {
            throw new \Exception('The idContainerVersion is required for Tags, Triggers, and Variables');
        }
        // If the name isn't already in use, simply return it
        if (!$this->isNameAlreadyUsed($idSite, $name, $idContainerVersion)) {
            return $name;
        }
        $newName = $this->incrementNameWithNumber($name);
        // Make sure that the new name doesn't already exist
        // Call this method recursively until we have a unique name
        if ($this->isNameAlreadyUsed($idSite, $newName, $idContainerVersion)) {
            $newName = $this->makeCopyNameUnique($idSite, $newName, $idContainerVersion);
        }
        return $newName;
    }
    /**
     * Check if the name is already in use. If it's a container, the idContainerVersion isn't needed. It's required for
     * tags, triggers, and variables.
     *
     * @param int $idSite
     * @param string $name
     * @param null|int $idContainerVersion Optional ID of the container version. It's only optional since containers
     * don't need it.
     * @return bool Indicating whether the name is already in use
     */
    protected abstract function isNameAlreadyUsed(int $idSite, string $name, ?int $idContainerVersion = null) : bool;
    protected function getCurrentDateTime()
    {
        return Date::now()->getDatetime();
    }
    /**
     * Update the provided name with a number suffix. It will either add a suffix or increment the number in the suffix.
     *
     * @param string $name The name that needs to be updated with a number suffix. If no suffix exists, one will be
     * added. If one already exists, the number in the suffix will be incremented.
     * @return string Name with the updated number suffix
     */
    protected function incrementNameWithNumber(string $name) : string
    {
        $newName = $name;
        // First check if the name already has a number suffix
        $matches = [];
        $number = 1;
        if (preg_match('/ \\(\\d+\\)$/', $name, $matches)) {
            // Increment the number in the name suffix
            $number = intval(str_replace(['(', ')'], '', $matches[0]));
            ++$number;
            $newName = str_replace($matches[0], '', $name);
        }
        // Make sure that we don't exceed the max length the name fields
        if (strlen($newName . " ({$number})") > Name::MAX_LENGTH) {
            $newName = substr($newName, 0, Name::MAX_LENGTH - 3 - strlen((string) $number));
        }
        $newName .= " ({$number})";
        return $newName;
    }
}
