<?php
class wfDB {
	public $errorMsg = false;
  
  /**
   * Returns the table prefix for the main site on multisites and the site itself on single site installations.
   *
   * @return string
   */
	public static function networkPrefix() {
		global $wpdb;
		return $wpdb->base_prefix;
	}
  
  /**
   * Returns the table with the site (single site installations) or network (multisite) prefix added.
   *
   * @param string $table
   * @return string
   */
	public static function networkTable($table) {
	  return self::networkPrefix() . $table;
	}
  
  /**
   * Returns the table prefix for the given blog ID. On single site installations, this will be equivalent to wfDB::networkPrefix().
   *
   * @param int $blogID
   * @return string
   */
	public static function blogPrefix($blogID) {
	  global $wpdb;
	  return $wpdb->get_blog_prefix($blogID);
	}
  
  /**
   * Returns the table with the site (single site installations) or blog-specific (multisite) prefix added.
   *
   * @param string $table
   * @return string
   */
	public static function blogTable($table, $blogID) {
	  return self::blogPrefix($blogID) . $table;
	}
	
	public function __construct(){
	}
	public function querySingle(){
		global $wpdb;
		if(func_num_args() > 1){
			$args = func_get_args();
			return $wpdb->get_var(call_user_func_array(array($wpdb, 'prepare'), $args));
		} else {
			return $wpdb->get_var(func_get_arg(0));
		}
	}
	public function querySingleRec(){ //queryInSprintfFormat, arg1, arg2, ... :: Returns a single assoc-array or null if nothing found.
		global $wpdb;
		if(func_num_args() > 1){
			$args = func_get_args();
			return $wpdb->get_row(call_user_func_array(array($wpdb, 'prepare'), $args), ARRAY_A);
		} else {
			return $wpdb->get_row(func_get_arg(0), ARRAY_A);
		}
	}
	public function queryWrite(){
		global $wpdb;
		if(func_num_args() > 1){
			$args = func_get_args();
			return $wpdb->query(call_user_func_array(array($wpdb, 'prepare'), $args));
		} else {
			return $wpdb->query(func_get_arg(0));
		}
	}
	public function flush(){ //Clear cache
		global $wpdb;
		$wpdb->flush();
	}
	public function querySelect(){ //sprintfString, arguments :: always returns array() and will be empty if no results.
		global $wpdb;
		if(func_num_args() > 1){
			$args = func_get_args();
			return $wpdb->get_results(call_user_func_array(array($wpdb, 'prepare'), $args), ARRAY_A);
		} else {
			return $wpdb->get_results(func_get_arg(0), ARRAY_A);
		}
	}
	public function queryWriteIgnoreError(){ //sprintfString, arguments
		global $wpdb;
		$oldSuppress = $wpdb->suppress_errors(true);
		$args = func_get_args();
		call_user_func_array(array($this, 'queryWrite'), $args);
		$wpdb->suppress_errors($oldSuppress);
	}
	public function columnExists($table, $col){
		$table = wfDB::networkTable($table);
		$q = $this->querySelect("desc $table");
		foreach($q as $row){
			if($row['Field'] == $col){
				return true;
			}
		}
		return false;
	}
	public function dropColumn($table, $col){
		$table = wfDB::networkTable($table);
		$this->queryWrite("alter table $table drop column $col");
	}
	public function createKeyIfNotExists($table, $col, $keyName){
		$table = wfDB::networkTable($table);
		
		$exists = $this->querySingle(<<<SQL
SELECT TABLE_NAME FROM information_schema.TABLES
WHERE TABLE_SCHEMA=DATABASE()
AND TABLE_NAME='%s'
SQL
			, $table);
		$keyFound = false;
		if($exists){
			$q = $this->querySelect("show keys from $table");
			foreach($q as $row){
				if($row['Key_name'] == $keyName){
					$keyFound = true;
				}
			}
		}
		if(! $keyFound){
			$this->queryWrite("alter table $table add KEY $keyName($col)");
		}
	}
	public function getMaxAllowedPacketBytes(){
		$rec = $this->querySingleRec("show variables like 'max_allowed_packet'");
		return intval($rec['Value']);
	}
	public function getMaxLongDataSizeBytes() {
		$rec = $this->querySingleRec("show variables like 'max_long_data_size'");
		return $rec['Value'];
	}
	public function truncate($table){ //Ensures everything is deleted if user is using MySQL >= 5.1.16 and does not have "drop" privileges
		$this->queryWrite("truncate table $table");
		$this->queryWrite("delete from $table");
	}
	public function getLastError(){
		global $wpdb;
		return $wpdb->last_error;
	}
	public function realEscape($str){
		global $wpdb;
		return $wpdb->_real_escape($str);
	}
}

abstract class wfModel {

	private $data;
	private $db;
	private $dirty = false;

	/**
	 * Column name of the primary key field.
	 *
	 * @return string
	 */
	abstract public function getIDColumn();

	/**
	 * Table name.
	 *
	 * @return mixed
	 */
	abstract public function getTable();

	/**
	 * Checks if this is a valid column in the table before setting data on the model.
	 *
	 * @param string $column
	 * @return boolean
	 */
	abstract public function hasColumn($column);

	/**
	 * wfModel constructor.
	 * @param array|int|string $data
	 */
	public function __construct($data = array()) {
		if (is_array($data) || is_object($data)) {
			$this->setData($data);
		} else if (is_numeric($data)) {
			$this->fetchByID($data);
		}
	}

	public function fetchByID($id) {
		$id = absint($id);
		$data = $this->getDB()->get_row($this->getDB()->prepare('SELECT * FROM ' . $this->getTable() .
				' WHERE ' . $this->getIDColumn() . ' = %d', $id));
		if ($data) {
			$this->setData($data);
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function save() {
		if (!$this->dirty) {
			return false;
		}
		$this->dirty = ($this->getPrimaryKey() ? $this->update() : $this->insert()) === false;
		return !$this->dirty;
	}

	/**
	 * @return false|int
	 */
	public function insert() {
		$data = $this->getData();
		unset($data[$this->getPrimaryKey()]);
		$rowsAffected = $this->getDB()->insert($this->getTable(), $data);
		$this->setPrimaryKey($this->getDB()->insert_id);
		return $rowsAffected;
	}

	/**
	 * @return false|int
	 */
	public function update() {
		return $this->getDB()->update($this->getTable(), $this->getData(), array(
			$this->getIDColumn() => $this->getPrimaryKey(),
		));
	}

	/**
	 * @param $name string
	 * @return mixed
	 */
	public function __get($name) {
		if (!$this->hasColumn($name)) {
			return null;
		}
		return array_key_exists($name, $this->data) ? $this->data[$name] : null;
	}

	/**
	 * @param $name string
	 * @param $value mixed
	 */
	public function __set($name, $value) {
		if (!$this->hasColumn($name)) {
			return;
		}
		$this->data[$name] = $value;
		$this->dirty = true;
	}

	/**
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param array $data
	 * @param bool $flagDirty
	 */
	public function setData($data, $flagDirty = true) {
		$this->data = array();
		foreach ($data as $column => $value) {
			if ($this->hasColumn($column)) {
				$this->data[$column] = $value;
				$this->dirty = (bool) $flagDirty;
			}
		}
	}

	/**
	 * @return wpdb
	 */
	public function getDB() {
		if ($this->db === null) {
			global $wpdb;
			$this->db = $wpdb;
		}
		return $this->db;
	}

	/**
	 * @param wpdb $db
	 */
	public function setDB($db) {
		$this->db = $db;
	}

	/**
	 * @return int
	 */
	public function getPrimaryKey() {
		return $this->{$this->getIDColumn()};
	}

	/**
	 * @param int $value
	 */
	public function setPrimaryKey($value) {
		$this->{$this->getIDColumn()} = $value;
	}
}
