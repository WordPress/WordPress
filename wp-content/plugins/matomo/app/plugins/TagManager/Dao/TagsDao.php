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
use Piwik\Plugins\TagManager\Model\Tag;
class TagsDao extends \Piwik\Plugins\TagManager\Dao\BaseDao implements \Piwik\Plugins\TagManager\Dao\TagManagerDao
{
    protected $table = 'tagmanager_tag';
    public function install()
    {
        DbHelper::createTable($this->table, "\n                  `idtag` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n                  `idcontainerversion` BIGINT UNSIGNED NOT NULL,\n                  `idsite` int(11) UNSIGNED NOT NULL,\n                  `type` VARCHAR(50) NOT NULL,\n                  `name` VARCHAR(" . Name::MAX_LENGTH . ") NOT NULL,\n                  `description` VARCHAR(" . Description::MAX_LENGTH . ") NOT NULL,\n                  `status` VARCHAR(10) NOT NULL,\n                  `parameters` MEDIUMTEXT NOT NULL DEFAULT '',\n                  `fire_trigger_ids` TEXT NOT NULL DEFAULT '',\n                  `block_trigger_ids` TEXT NOT NULL DEFAULT '',\n                  `fire_limit` VARCHAR(20) NOT NULL DEFAULT '" . Tag::FIRE_LIMIT_UNLIMITED . "',\n                  `priority` SMALLINT(5) UNSIGNED NOT NULL,\n                  `fire_delay` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,\n                  `start_date` DATETIME DEFAULT NULL,\n                  `end_date` DATETIME DEFAULT NULL,\n                  `created_date` DATETIME NOT NULL,\n                  `updated_date` DATETIME NOT NULL,\n                  `deleted_date` DATETIME NULL,\n                  PRIMARY KEY(`idtag`), KEY (`idsite`, `idcontainerversion`)");
        // we cannot set a unique key on (`idsite`, `idcontainerversion`, `name`) because we soft delete tags and want to make sure names can be used again after deleting an entry
    }
    private function isNameInUse($idSite, $idContainerVersion, $name, $exceptIdTag = null)
    {
        $sql = sprintf("SELECT idtag FROM %s WHERE idsite = ? AND idcontainerversion = ? AND `name` = ? AND status != ?", $this->tablePrefixed);
        $bind = array($idSite, $idContainerVersion, $name, self::STATUS_DELETED);
        if (!empty($exceptIdTag)) {
            $sql .= ' AND idtag != ?';
            $bind[] = $exceptIdTag;
        }
        $idSite = Db::fetchOne($sql, $bind);
        return !empty($idSite);
    }
    protected function isNameAlreadyUsed(int $idSite, string $name, ?int $idContainerVersion = null) : bool
    {
        return $this->isNameInUse($idSite, $idContainerVersion, $name);
    }
    public function createTag($idSite, $idContainerVersion, $type, $name, $parameters, $fireTriggerIds, $blockTriggerIds, $fireLimit, $fireDelay, $priority, $startDate, $endDate, $createdDate, $description = '', $status = '')
    {
        if ($this->isNameInUse($idSite, $idContainerVersion, $name)) {
            throw new Exception(Piwik::translate('TagManager_ErrorNameDuplicate'));
        }
        if (!in_array($status, [self::STATUS_ACTIVE, self::STATUS_PAUSED, self::STATUS_DELETED])) {
            $status = self::STATUS_ACTIVE;
        }
        $values = array('idsite' => $idSite, 'idcontainerversion' => $idContainerVersion, 'status' => $status, 'type' => $type, 'name' => $name, 'description' => $description, 'parameters' => $parameters, 'fire_trigger_ids' => $fireTriggerIds, 'block_trigger_ids' => $blockTriggerIds, 'fire_limit' => $fireLimit, 'fire_delay' => $fireDelay, 'priority' => $priority, 'start_date' => empty($startDate) ? null : $startDate, 'end_date' => empty($endDate) ? null : $endDate, 'created_date' => $createdDate, 'updated_date' => $createdDate);
        $values = $this->encodeFieldsWhereNeeded($values);
        return $this->insertRecord($values);
    }
    public function updateTagColumns($idSite, $idContainerVersion, $idTag, $columns)
    {
        $columns = $this->encodeFieldsWhereNeeded($columns);
        if (!empty($columns)) {
            if (isset($columns['name']) && $this->isNameInUse($idSite, $idContainerVersion, $columns['name'], $idTag)) {
                throw new Exception(Piwik::translate('TagManager_ErrorNameDuplicate'));
            }
            $this->updateEntity($columns, ['idsite' => (int) $idSite, 'idcontainerversion' => (int) $idContainerVersion, 'idtag' => (int) $idTag]);
        }
    }
    private function encodeFieldsWhereNeeded($columns)
    {
        if (!empty($columns['fire_trigger_ids'])) {
            $columns['fire_trigger_ids'] = json_encode($columns['fire_trigger_ids']);
        } elseif (isset($columns['fire_trigger_ids'])) {
            $columns['fire_trigger_ids'] = '';
        }
        if (!empty($columns['block_trigger_ids'])) {
            $columns['block_trigger_ids'] = json_encode($columns['block_trigger_ids']);
        } elseif (isset($columns['block_trigger_ids'])) {
            $columns['block_trigger_ids'] = '';
        }
        if (!empty($columns['parameters'])) {
            $columns['parameters'] = json_encode($columns['parameters']);
        } elseif (isset($columns['parameters'])) {
            $columns['parameters'] = '';
        }
        return $columns;
    }
    public function getAllTags()
    {
        $tags = Db::fetchAll('SELECT * FROM ' . $this->tablePrefixed . ' ORDER BY idtag ASC');
        return $this->enrichTags($tags);
    }
    /**
     * @param int $idSite
     * @param int $idContainerVersion
     * @return array
     */
    public function getContainerTags($idSite, $idContainerVersion)
    {
        $bind = [self::STATUS_DELETED, $idSite, $idContainerVersion];
        $table = $this->tablePrefixed;
        $tags = Db::fetchAll("SELECT * FROM {$table} WHERE status != ? AND idsite = ? and idcontainerversion = ? ORDER BY priority, created_date ASC", $bind);
        return $this->enrichTags($tags);
    }
    /**
     * @param int $idSite
     * @param int $idContainerVersion
     * @param string $tagType The type of tag to filter by, such as 'Matomo', 'CustomHtml', ...
     * @return array
     */
    public function getContainerTagIdsByType($idSite, $idContainerVersion, $tagType)
    {
        $bind = [self::STATUS_DELETED, $idSite, $idContainerVersion, $tagType];
        $table = $this->tablePrefixed;
        $tags = Db::fetchAll("SELECT idtag FROM {$table} WHERE status != ? AND idsite = ? and idcontainerversion = ? and type = ? ORDER BY priority, created_date ASC", $bind);
        return is_array($tags) && count($tags) ? array_column($tags, 'idtag') : [];
    }
    /**
     * @param $idSite
     * @param $idContainerVersion
     * @param $idTag
     * @return array|false
     * @throws \Exception
     */
    public function getContainerTag($idSite, $idContainerVersion, $idTag)
    {
        $table = $this->tablePrefixed;
        $bind = array(self::STATUS_DELETED, $idTag, $idContainerVersion, $idSite);
        $tag = Db::fetchRow("SELECT * FROM {$table} WHERE status != ? and idtag = ? and idcontainerversion = ? and idsite = ?", $bind);
        return $this->enrichTag($tag);
    }
    /**
     * @param $idSite
     * @param $idContainerVersion
     * @param $idTag
     * @return array|false
     * @throws \Exception
     */
    public function getContainerTagAnyStatus($idSite, $idContainerVersion, $idTag)
    {
        $table = $this->tablePrefixed;
        $bind = array($idTag, $idContainerVersion, $idSite);
        $tag = Db::fetchRow("SELECT * FROM {$table} WHERE idtag = ? and idcontainerversion = ? and idsite = ?", $bind);
        return $this->enrichTag($tag);
    }
    /**
     * @param int $idSite
     * @param string $deletedDate
     */
    public function deleteTagsForSite($idSite, $deletedDate)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ?, deleted_date = ? WHERE idsite = ? and status != ?";
        $bind = array(self::STATUS_DELETED, $deletedDate, $idSite, self::STATUS_DELETED);
        Db::query($query, $bind);
    }
    /**
     * @param int $idSite
     * @param int $idContainerVersion
     * @param int $idTag
     * @param string $deletedDate
     */
    public function deleteContainerTag($idSite, $idContainerVersion, $idTag, $deletedDate)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ?, deleted_date = ? WHERE idsite = ? and idcontainerversion = ? and idtag = ? and status != ?";
        $bind = array(self::STATUS_DELETED, $deletedDate, $idSite, $idContainerVersion, $idTag, self::STATUS_DELETED);
        Db::query($query, $bind);
    }
    /**
     * @param int $idSite
     * @param int $idContainerVersion
     * @param int $idTag
     */
    public function pauseContainerTag($idSite, $idContainerVersion, $idTag)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ? WHERE idsite = ? and idcontainerversion = ? and idtag = ? and status != ?";
        $bind = array(self::STATUS_PAUSED, $idSite, $idContainerVersion, $idTag, self::STATUS_PAUSED);
        Db::query($query, $bind);
    }
    /**
     * @param int $idSite
     * @param int $idContainerVersion
     * @param int $idTag
     */
    public function resumeContainerTag($idSite, $idContainerVersion, $idTag)
    {
        $table = $this->tablePrefixed;
        $query = "UPDATE {$table} SET status = ? WHERE idsite = ? and idcontainerversion = ? and idtag = ? and status = ?";
        $bind = array(self::STATUS_ACTIVE, $idSite, $idContainerVersion, $idTag, self::STATUS_PAUSED);
        Db::query($query, $bind);
    }
    private function enrichTags($tags)
    {
        if (empty($tags)) {
            return array();
        }
        foreach ($tags as $index => $tag) {
            $tags[$index] = $this->enrichTag($tag);
        }
        usort($tags, function ($tagA, $tagB) use($tags) {
            return strcasecmp($tagA['priority'], $tagB['priority']);
        });
        return $tags;
    }
    private function enrichTag($tag)
    {
        if (empty($tag)) {
            return $tag;
        }
        $tag['idtag'] = (int) $tag['idtag'];
        $tag['idsite'] = (int) $tag['idsite'];
        $tag['idcontainerversion'] = (int) $tag['idcontainerversion'];
        $tag['fire_delay'] = (int) $tag['fire_delay'];
        $tag['priority'] = (int) $tag['priority'];
        if ($tag['start_date'] === '0000-00-00 00:00:00') {
            $tag['start_date'] = null;
        }
        if ($tag['end_date'] === '0000-00-00 00:00:00') {
            $tag['end_date'] = null;
        }
        if (!empty($tag['parameters'])) {
            $tag['parameters'] = json_decode($tag['parameters'], \true);
        }
        if (empty($tag['parameters'])) {
            $tag['parameters'] = [];
        }
        if (!empty($tag['fire_trigger_ids'])) {
            $tag['fire_trigger_ids'] = json_decode($tag['fire_trigger_ids'], \true);
        }
        if (empty($tag['fire_trigger_ids'])) {
            $tag['fire_trigger_ids'] = [];
        } else {
            $tag['fire_trigger_ids'] = array_map('intval', $tag['fire_trigger_ids']);
        }
        if (!empty($tag['block_trigger_ids'])) {
            $tag['block_trigger_ids'] = json_decode($tag['block_trigger_ids'], \true);
        }
        if (empty($tag['block_trigger_ids'])) {
            $tag['block_trigger_ids'] = [];
        } else {
            $tag['block_trigger_ids'] = array_map('intval', $tag['block_trigger_ids']);
        }
        return $tag;
    }
}
