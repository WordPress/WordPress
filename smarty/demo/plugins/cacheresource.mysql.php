<?php

/**
 * MySQL CacheResource
 *
 * CacheResource Implementation based on the Custom API to use
 * MySQL as the storage resource for Smarty's output caching.
 *
 * Table definition:
 * <pre>CREATE TABLE IF NOT EXISTS `output_cache` (
 *   `id` CHAR(40) NOT NULL COMMENT 'sha1 hash',
 *   `name` VARCHAR(250) NOT NULL,
 *   `cache_id` VARCHAR(250) NULL DEFAULT NULL,
 *   `compile_id` VARCHAR(250) NULL DEFAULT NULL,
 *   `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *   `content` LONGTEXT NOT NULL,
 *   PRIMARY KEY (`id`),
 *   INDEX(`name`),
 *   INDEX(`cache_id`),
 *   INDEX(`compile_id`),
 *   INDEX(`modified`)
 * ) ENGINE = InnoDB;</pre>
 *
 * @package CacheResource-examples
 * @author Rodney Rehm
 */
class Smarty_CacheResource_Mysql extends Smarty_CacheResource_Custom
{
    // PDO instance
    protected $db;
    protected $fetch;
    protected $fetchTimestamp;
    protected $save;

    public function __construct()
    {
        try {
            $this->db = new PDO("mysql:dbname=test;host=127.0.0.1", "smarty");
        } catch (PDOException $e) {
            throw new SmartyException('Mysql Resource failed: ' . $e->getMessage());
        }
        $this->fetch = $this->db->prepare('SELECT modified, content FROM output_cache WHERE id = :id');
        $this->fetchTimestamp = $this->db->prepare('SELECT modified FROM output_cache WHERE id = :id');
        $this->save = $this->db->prepare('REPLACE INTO output_cache (id, name, cache_id, compile_id, content)
            VALUES  (:id, :name, :cache_id, :compile_id, :content)');
    }

    /**
     * fetch cached content and its modification time from data source
     *
     * @param  string  $id         unique cache content identifier
     * @param  string  $name       template name
     * @param  string  $cache_id   cache id
     * @param  string  $compile_id compile id
     * @param  string  $content    cached content
     * @param  integer $mtime      cache modification timestamp (epoch)
     * @return void
     */
    protected function fetch($id, $name, $cache_id, $compile_id, &$content, &$mtime)
    {
        $this->fetch->execute(array('id' => $id));
        $row = $this->fetch->fetch();
        $this->fetch->closeCursor();
        if ($row) {
            $content = $row['content'];
            $mtime = strtotime($row['modified']);
        } else {
            $content = null;
            $mtime = null;
        }
    }

    /**
     * Fetch cached content's modification timestamp from data source
     *
     * @note implementing this method is optional. Only implement it if modification times can be accessed faster than loading the complete cached content.
     * @param  string          $id         unique cache content identifier
     * @param  string          $name       template name
     * @param  string          $cache_id   cache id
     * @param  string          $compile_id compile id
     * @return integer|boolean timestamp (epoch) the template was modified, or false if not found
     */
    protected function fetchTimestamp($id, $name, $cache_id, $compile_id)
    {
        $this->fetchTimestamp->execute(array('id' => $id));
        $mtime = strtotime($this->fetchTimestamp->fetchColumn());
        $this->fetchTimestamp->closeCursor();

        return $mtime;
    }

    /**
     * Save content to cache
     *
     * @param  string       $id         unique cache content identifier
     * @param  string       $name       template name
     * @param  string       $cache_id   cache id
     * @param  string       $compile_id compile id
     * @param  integer|null $exp_time   seconds till expiration time in seconds or null
     * @param  string       $content    content to cache
     * @return boolean      success
     */
    protected function save($id, $name, $cache_id, $compile_id, $exp_time, $content)
    {
        $this->save->execute(array(
            'id' => $id,
            'name' => $name,
            'cache_id' => $cache_id,
            'compile_id' => $compile_id,
            'content' => $content,
        ));

        return !!$this->save->rowCount();
    }

    /**
     * Delete content from cache
     *
     * @param  string       $name       template name
     * @param  string       $cache_id   cache id
     * @param  string       $compile_id compile id
     * @param  integer|null $exp_time   seconds till expiration or null
     * @return integer      number of deleted caches
     */
    protected function delete($name, $cache_id, $compile_id, $exp_time)
    {
        // delete the whole cache
        if ($name === null && $cache_id === null && $compile_id === null && $exp_time === null) {
            // returning the number of deleted caches would require a second query to count them
            $query = $this->db->query('TRUNCATE TABLE output_cache');

            return -1;
        }
        // build the filter
        $where = array();
        // equal test name
        if ($name !== null) {
            $where[] = 'name = ' . $this->db->quote($name);
        }
        // equal test compile_id
        if ($compile_id !== null) {
            $where[] = 'compile_id = ' . $this->db->quote($compile_id);
        }
        // range test expiration time
        if ($exp_time !== null) {
            $where[] = 'modified < DATE_SUB(NOW(), INTERVAL ' . intval($exp_time) . ' SECOND)';
        }
        // equal test cache_id and match sub-groups
        if ($cache_id !== null) {
            $where[] = '(cache_id = '. $this->db->quote($cache_id)
                . ' OR cache_id LIKE '. $this->db->quote($cache_id .'|%') .')';
        }
        // run delete query
        $query = $this->db->query('DELETE FROM output_cache WHERE ' . join(' AND ', $where));

        return $query->rowCount();
    }
}
