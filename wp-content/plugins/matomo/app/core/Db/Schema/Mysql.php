<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Db\Schema;

use Exception;
use Piwik\Common;
use Piwik\Concurrency\Lock;
use Piwik\Config;
use Piwik\Date;
use Piwik\Db\SchemaInterface;
use Piwik\Db;
use Piwik\DbHelper;
use Piwik\Option;
use Piwik\Piwik;
use Piwik\Plugin\Manager;
use Piwik\Plugins\UsersManager\Model;
use Piwik\Version;
/**
 * MySQL schema
 */
class Mysql implements SchemaInterface
{
    public const OPTION_NAME_MATOMO_INSTALL_VERSION = 'install_version';
    public const MAX_TABLE_NAME_LENGTH = 64;
    private $tablesInstalled = null;
    /**
     * Get the SQL to create Piwik tables
     *
     * @return array  array of strings containing SQL
     */
    public function getTablesCreateSql()
    {
        $prefixTables = $this->getTablePrefix();
        $tableOptions = $this->getTableCreateOptions();
        $tables = array('user' => "CREATE TABLE {$prefixTables}user (\n                          login VARCHAR(100) NOT NULL,\n                          password VARCHAR(255) NOT NULL,\n                          email VARCHAR(100) NOT NULL,\n                          twofactor_secret VARCHAR(40) NOT NULL DEFAULT '',\n                          superuser_access TINYINT(2) unsigned NOT NULL DEFAULT '0',\n                          date_registered TIMESTAMP NULL,\n                          ts_password_modified TIMESTAMP NULL,\n                          idchange_last_viewed INTEGER UNSIGNED NULL,\n                          invited_by VARCHAR(100) NULL,\n                          invite_token VARCHAR(191) NULL,\n                          invite_link_token VARCHAR(191) NULL,\n                          invite_expired_at TIMESTAMP NULL,\n                          invite_accept_at TIMESTAMP NULL,\n                          ts_changes_shown TIMESTAMP NULL,\n                            PRIMARY KEY(login),\n                            UNIQUE INDEX `uniq_email` (`email`)\n                          ) {$tableOptions}\n            ", 'user_token_auth' => "CREATE TABLE {$prefixTables}user_token_auth (\n                          idusertokenauth BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n                          login VARCHAR(100) NOT NULL,\n                          description VARCHAR(" . Model::MAX_LENGTH_TOKEN_DESCRIPTION . ") NOT NULL,\n                          password VARCHAR(191) NOT NULL,\n                          hash_algo VARCHAR(30) NOT NULL,\n                          system_token TINYINT(1) NOT NULL DEFAULT 0,\n                          last_used DATETIME NULL,\n                          date_created DATETIME NOT NULL,\n                          date_expired DATETIME NULL,\n                          secure_only TINYINT(2) unsigned NOT NULL DEFAULT '0',\n                            PRIMARY KEY(idusertokenauth),\n                            UNIQUE KEY uniq_password(password)\n                          ) {$tableOptions}\n            ", 'twofactor_recovery_code' => "CREATE TABLE {$prefixTables}twofactor_recovery_code (\n                          idrecoverycode BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n                          login VARCHAR(100) NOT NULL,\n                          recovery_code VARCHAR(40) NOT NULL,\n                            PRIMARY KEY(idrecoverycode)\n                          ) {$tableOptions}\n            ", 'access' => "CREATE TABLE {$prefixTables}access (\n                          idaccess INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n                          login VARCHAR(100) NOT NULL,\n                          idsite INTEGER UNSIGNED NOT NULL,\n                          access VARCHAR(50) NULL,\n                            PRIMARY KEY(idaccess),\n                            INDEX index_loginidsite (login, idsite)\n                          ) {$tableOptions}\n            ", 'site' => "CREATE TABLE {$prefixTables}site (\n                          idsite INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n                          name VARCHAR(90) NOT NULL,\n                          main_url VARCHAR(255) NOT NULL,\n                            ts_created TIMESTAMP NULL,\n                            ecommerce TINYINT DEFAULT 0,\n                            sitesearch TINYINT DEFAULT 1,\n                            sitesearch_keyword_parameters TEXT NOT NULL,\n                            sitesearch_category_parameters TEXT NOT NULL,\n                            timezone VARCHAR( 50 ) NOT NULL,\n                            currency CHAR( 3 ) NOT NULL,\n                            exclude_unknown_urls TINYINT(1) DEFAULT 0,\n                            excluded_ips TEXT NOT NULL,\n                            excluded_parameters TEXT NOT NULL,\n                            excluded_user_agents TEXT NOT NULL,\n                            excluded_referrers TEXT NOT NULL,\n                            `group` VARCHAR(250) NOT NULL,\n                            `type` VARCHAR(255) NOT NULL,\n                            keep_url_fragment TINYINT NOT NULL DEFAULT 0,\n                            creator_login VARCHAR(100) NULL,\n                              PRIMARY KEY(idsite)\n                            ) {$tableOptions}\n            ", 'plugin_setting' => "CREATE TABLE {$prefixTables}plugin_setting (\n                              `plugin_name` VARCHAR(60) NOT NULL,\n                              `setting_name` VARCHAR(255) NOT NULL,\n                              `setting_value` LONGTEXT NOT NULL,\n                              `json_encoded` TINYINT UNSIGNED NOT NULL DEFAULT 0,\n                              `user_login` VARCHAR(100) NOT NULL DEFAULT '',\n                              `idplugin_setting` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n                              PRIMARY KEY (idplugin_setting),\n                              INDEX(plugin_name, user_login)\n                            ) {$tableOptions}\n            ", 'site_setting' => "CREATE TABLE {$prefixTables}site_setting (\n                              idsite INTEGER(10) UNSIGNED NOT NULL,\n                              `plugin_name` VARCHAR(60) NOT NULL,\n                              `setting_name` VARCHAR(255) NOT NULL,\n                              `setting_value` LONGTEXT NOT NULL,\n                              `json_encoded` TINYINT UNSIGNED NOT NULL DEFAULT 0,\n                              `idsite_setting` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n                              PRIMARY KEY (idsite_setting),\n                              INDEX(idsite, plugin_name)\n                            ) {$tableOptions}\n            ", 'site_url' => "CREATE TABLE {$prefixTables}site_url (\n                              idsite INTEGER(10) UNSIGNED NOT NULL,\n                              url VARCHAR(190) NOT NULL,\n                                PRIMARY KEY(idsite, url)\n                              ) {$tableOptions}\n            ", 'goal' => "CREATE TABLE `{$prefixTables}goal` (\n                              `idsite` int(11) NOT NULL,\n                              `idgoal` int(11) NOT NULL,\n                              `name` varchar(50) NOT NULL,\n                              `description` varchar(255) NOT NULL DEFAULT '',\n                              `match_attribute` varchar(20) NOT NULL,\n                              `pattern` varchar(255) NOT NULL,\n                              `pattern_type` varchar(25) NOT NULL,\n                              `case_sensitive` tinyint(4) NOT NULL,\n                              `allow_multiple` tinyint(4) NOT NULL,\n                              `revenue` DOUBLE NOT NULL,\n                              `deleted` tinyint(4) NOT NULL default '0',\n                              `event_value_as_revenue` tinyint(4) NOT NULL default '0',\n                                PRIMARY KEY  (`idsite`,`idgoal`)\n                              ) {$tableOptions}\n            ", 'logger_message' => "CREATE TABLE {$prefixTables}logger_message (\n                                      idlogger_message INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,\n                                      tag VARCHAR(50) NULL,\n                                      timestamp TIMESTAMP NULL,\n                                      level VARCHAR(16) NULL,\n                                      message TEXT NULL,\n                                        PRIMARY KEY(idlogger_message)\n                                      ) {$tableOptions}\n            ", 'log_action' => "CREATE TABLE {$prefixTables}log_action (\n                                      idaction INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n                                      name VARCHAR(4096),\n                                      hash INTEGER(10) UNSIGNED NOT NULL,\n                                      type TINYINT UNSIGNED NULL,\n                                      url_prefix TINYINT(2) NULL,\n                                        PRIMARY KEY(idaction),\n                                        INDEX index_type_hash (type, hash)\n                                      ) {$tableOptions}\n            ", 'log_visit' => "CREATE TABLE {$prefixTables}log_visit (\n                              idvisit BIGINT(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n                              idsite INTEGER(10) UNSIGNED NOT NULL,\n                              idvisitor BINARY(8) NOT NULL,\n                              visit_last_action_time DATETIME NOT NULL,\n                              config_id BINARY(8) NOT NULL,\n                              location_ip VARBINARY(16) NOT NULL,\n                                PRIMARY KEY(idvisit),\n                                INDEX index_idsite_config_datetime (idsite, config_id, visit_last_action_time),\n                                INDEX index_idsite_datetime (idsite, visit_last_action_time),\n                                INDEX index_idsite_idvisitor_time (idsite, idvisitor, visit_last_action_time DESC)\n                              ) {$tableOptions}\n            ", 'log_conversion_item' => "CREATE TABLE `{$prefixTables}log_conversion_item` (\n                                        idsite int(10) UNSIGNED NOT NULL,\n                                        idvisitor BINARY(8) NOT NULL,\n                                        server_time DATETIME NOT NULL,\n                                        idvisit BIGINT(10) UNSIGNED NOT NULL,\n                                        idorder varchar(100) NOT NULL,\n                                        idaction_sku INTEGER(10) UNSIGNED NOT NULL,\n                                        idaction_name INTEGER(10) UNSIGNED NOT NULL,\n                                        idaction_category INTEGER(10) UNSIGNED NOT NULL,\n                                        idaction_category2 INTEGER(10) UNSIGNED NOT NULL,\n                                        idaction_category3 INTEGER(10) UNSIGNED NOT NULL,\n                                        idaction_category4 INTEGER(10) UNSIGNED NOT NULL,\n                                        idaction_category5 INTEGER(10) UNSIGNED NOT NULL,\n                                        price DOUBLE NOT NULL,\n                                        quantity INTEGER(10) UNSIGNED NOT NULL,\n                                        deleted TINYINT(1) UNSIGNED NOT NULL,\n                                          PRIMARY KEY(idvisit, idorder, idaction_sku),\n                                          INDEX index_idsite_servertime ( idsite, server_time )\n                                        ) {$tableOptions}\n            ", 'log_conversion' => "CREATE TABLE `{$prefixTables}log_conversion` (\n                                      idvisit BIGINT(10) unsigned NOT NULL,\n                                      idsite int(10) unsigned NOT NULL,\n                                      idvisitor BINARY(8) NOT NULL,\n                                      server_time datetime NOT NULL,\n                                      idaction_url INTEGER(10) UNSIGNED default NULL,\n                                      idlink_va BIGINT(10) UNSIGNED default NULL,\n                                      idgoal int(10) NOT NULL,\n                                      buster int unsigned NOT NULL,\n                                      idorder varchar(100) default NULL,\n                                      items SMALLINT UNSIGNED DEFAULT NULL,\n                                      url VARCHAR(4096) NOT NULL,\n                                      revenue DOUBLE default NULL,\n                                      revenue_shipping DOUBLE default NULL,\n                                      revenue_subtotal DOUBLE default NULL,\n                                      revenue_tax DOUBLE default NULL,\n                                      revenue_discount DOUBLE default NULL,\n                                      pageviews_before SMALLINT UNSIGNED DEFAULT NULL,\n                                        PRIMARY KEY (idvisit, idgoal, buster),\n                                        UNIQUE KEY unique_idsite_idorder (idsite, idorder),\n                                        INDEX index_idsite_datetime ( idsite, server_time )\n                                      ) {$tableOptions}\n            ", 'log_link_visit_action' => "CREATE TABLE {$prefixTables}log_link_visit_action (\n                                        idlink_va BIGINT(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n                                        idsite int(10) UNSIGNED NOT NULL,\n                                        idvisitor BINARY(8) NOT NULL,\n                                        idvisit BIGINT(10) UNSIGNED NOT NULL,\n                                        idaction_url_ref INTEGER(10) UNSIGNED NULL DEFAULT 0,\n                                        idaction_name_ref INTEGER(10) UNSIGNED NULL,\n                                        custom_float DOUBLE NULL DEFAULT NULL,\n                                        pageview_position MEDIUMINT UNSIGNED DEFAULT NULL,\n                                          PRIMARY KEY(idlink_va),\n                                          INDEX index_idvisit(idvisit)\n                                        ) {$tableOptions}\n            ", 'log_profiling' => "CREATE TABLE {$prefixTables}log_profiling (\n                                  query TEXT NOT NULL,\n                                  count INTEGER UNSIGNED NULL,\n                                  sum_time_ms FLOAT NULL,\n                                  idprofiling BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n                                    PRIMARY KEY (idprofiling),\n                                    UNIQUE KEY query(query(100))\n                                  ) {$tableOptions}\n            ", 'option' => "CREATE TABLE `{$prefixTables}option` (\n                                option_name VARCHAR( 191 ) NOT NULL,\n                                option_value LONGTEXT NOT NULL,\n                                autoload TINYINT NOT NULL DEFAULT '1',\n                                  PRIMARY KEY ( option_name ),\n                                  INDEX autoload( autoload )\n                                ) {$tableOptions}\n            ", 'session' => "CREATE TABLE {$prefixTables}session (\n                                id VARCHAR( 191 ) NOT NULL,\n                                modified INTEGER,\n                                lifetime INTEGER,\n                                data MEDIUMTEXT,\n                                  PRIMARY KEY ( id )\n                                ) {$tableOptions}\n            ", 'archive_numeric' => "CREATE TABLE {$prefixTables}archive_numeric (\n                                      idarchive INTEGER UNSIGNED NOT NULL,\n                                      name VARCHAR(190) NOT NULL,\n                                      idsite INTEGER UNSIGNED NULL,\n                                      date1 DATE NULL,\n                                      date2 DATE NULL,\n                                      period TINYINT UNSIGNED NULL,\n                                      ts_archived DATETIME NULL,\n                                      value DOUBLE NULL,\n                                        PRIMARY KEY(idarchive, name),\n                                        INDEX index_idsite_dates_period(idsite, date1, date2, period, name(6)),\n                                        INDEX index_period_archived(period, ts_archived)\n                                      ) {$tableOptions}\n            ", 'archive_blob' => "CREATE TABLE {$prefixTables}archive_blob (\n                                      idarchive INTEGER UNSIGNED NOT NULL,\n                                      name VARCHAR(190) NOT NULL,\n                                      idsite INTEGER UNSIGNED NULL,\n                                      date1 DATE NULL,\n                                      date2 DATE NULL,\n                                      period TINYINT UNSIGNED NULL,\n                                      ts_archived DATETIME NULL,\n                                      value MEDIUMBLOB NULL,\n                                        PRIMARY KEY(idarchive, name),\n                                        INDEX index_period_archived(period, ts_archived)\n                                      ) {$tableOptions}\n            ", 'archive_invalidations' => "CREATE TABLE `{$prefixTables}archive_invalidations` (\n                                            idinvalidation BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n                                            idarchive INTEGER UNSIGNED NULL,\n                                            name VARCHAR(255) NOT NULL,\n                                            idsite INTEGER UNSIGNED NOT NULL,\n                                            date1 DATE NOT NULL,\n                                            date2 DATE NOT NULL,\n                                            period TINYINT UNSIGNED NOT NULL,\n                                            ts_invalidated DATETIME NULL,\n                                            ts_started DATETIME NULL,\n                                            status TINYINT(1) UNSIGNED DEFAULT 0,\n                                            `report` VARCHAR(255) NULL,\n                                            processing_host VARCHAR(100) NULL DEFAULT NULL,\n                                            process_id VARCHAR(15) NULL DEFAULT NULL,\n                                            PRIMARY KEY(idinvalidation),\n                                            INDEX index_idsite_dates_period_name(idsite, date1, period)\n                                        ) {$tableOptions}\n            ", 'sequence' => "CREATE TABLE {$prefixTables}sequence (\n                                      `name` VARCHAR(120) NOT NULL,\n                                      `value` BIGINT(20) UNSIGNED NOT NULL ,\n                                      PRIMARY KEY(`name`)\n                                  ) {$tableOptions}\n            ", 'brute_force_log' => "CREATE TABLE {$prefixTables}brute_force_log (\n                                      `id_brute_force_log` bigint(11) NOT NULL AUTO_INCREMENT,\n                                      `ip_address` VARCHAR(60) DEFAULT NULL,\n                                      `attempted_at` datetime NOT NULL,\n                                      `login` VARCHAR(100) NULL,\n                                        INDEX index_ip_address(ip_address),\n                                      PRIMARY KEY(`id_brute_force_log`)\n                                      ) {$tableOptions}\n            ", 'tracking_failure' => "CREATE TABLE {$prefixTables}tracking_failure (\n                                      `idsite` BIGINT(20) UNSIGNED NOT NULL ,\n                                      `idfailure` SMALLINT UNSIGNED NOT NULL ,\n                                      `date_first_occurred` DATETIME NOT NULL ,\n                                      `request_url` MEDIUMTEXT NOT NULL ,\n                                      PRIMARY KEY(`idsite`, `idfailure`)\n                                  ) {$tableOptions}\n            ", 'locks' => "CREATE TABLE `{$prefixTables}locks` (\n                                      `key` VARCHAR(" . Lock::MAX_KEY_LEN . ") NOT NULL,\n                                      `value` VARCHAR(255) NULL DEFAULT NULL,\n                                      `expiry_time` BIGINT UNSIGNED DEFAULT 9999999999,\n                                      PRIMARY KEY (`key`)\n                                  ) {$tableOptions}\n            ", 'changes' => "CREATE TABLE `{$prefixTables}changes` (\n                                      `idchange` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,\n                                      `created_time` DATETIME NOT NULL,\n                                      `plugin_name` VARCHAR(60) NOT NULL,\n                                      `version` VARCHAR(20) NOT NULL, \n                                      `title` VARCHAR(255) NOT NULL,                                      \n                                      `description` TEXT NULL,\n                                      `link_name` VARCHAR(255) NULL,\n                                      `link` VARCHAR(255) NULL,       \n                                      PRIMARY KEY(`idchange`),\n                                      UNIQUE KEY unique_plugin_version_title (`plugin_name`, `version`, `title`(100))                            \n                                  ) {$tableOptions}\n            ");
        return $tables;
    }
    /**
     * Get the SQL to create a specific Piwik table
     *
     * @param string $tableName
     * @throws Exception
     * @return string  SQL
     */
    public function getTableCreateSql($tableName)
    {
        $tables = DbHelper::getTablesCreateSql();
        if (!isset($tables[$tableName])) {
            throw new Exception("The table '{$tableName}' SQL creation code couldn't be found.");
        }
        return $tables[$tableName];
    }
    /**
     * Names of all the prefixed tables in piwik
     * Doesn't use the DB
     *
     * @return array  Table names
     */
    public function getTablesNames()
    {
        $aTables = array_keys($this->getTablesCreateSql());
        $prefixTables = $this->getTablePrefix();
        $return = array();
        foreach ($aTables as $table) {
            $return[] = $prefixTables . $table;
        }
        return $return;
    }
    /**
     * Get list of installed columns in a table
     *
     * @param  string $tableName The name of a table.
     *
     * @return array  Installed columns indexed by the column name.
     */
    public function getTableColumns($tableName)
    {
        $db = $this->getDb();
        $allColumns = $db->fetchAll("SHOW COLUMNS FROM " . $tableName);
        $fields = array();
        foreach ($allColumns as $column) {
            $fields[trim($column['Field'])] = $column;
        }
        return $fields;
    }
    /**
     * Get list of tables installed (including tables defined by deactivated plugins)
     *
     * @param bool $forceReload Invalidate cache
     * @return array  installed Tables
     */
    public function getTablesInstalled($forceReload = \true)
    {
        if (is_null($this->tablesInstalled) || $forceReload === \true) {
            $db = $this->getDb();
            $prefixTables = $this->getTablePrefixEscaped();
            $allTables = $this->getAllExistingTables($prefixTables);
            // all the tables to be installed
            $allMyTables = $this->getTablesNames();
            /**
             * Triggered when detecting which tables have already been created by Matomo.
             * This should be used by plugins to define it's database tables. Table names need to be added prefixed.
             *
             * **Example**
             *
             *     Piwik::addAction('Db.getTablesInstalled', function(&$allTablesInstalled) {
             *         $allTablesInstalled = 'log_custom';
             *     });
             * @param array $result
             */
            if (count($allTables) && empty($GLOBALS['DISABLE_GET_TABLES_INSTALLED_EVENTS_FOR_TEST'])) {
                Manager::getInstance()->loadPlugins(Manager::getAllPluginsNames());
                Piwik::postEvent('Db.getTablesInstalled', [&$allMyTables]);
                Manager::getInstance()->unloadPlugins();
                Manager::getInstance()->loadActivatedPlugins();
            }
            // we get the intersection between all the tables in the DB and the tables to be installed
            $tablesInstalled = array_intersect($allMyTables, $allTables);
            // at this point we have the static list of core tables, but let's add the monthly archive tables
            $allArchiveNumeric = $db->fetchCol("SHOW TABLES LIKE '" . $prefixTables . "archive_numeric%'");
            $allArchiveBlob = $db->fetchCol("SHOW TABLES LIKE '" . $prefixTables . "archive_blob%'");
            $allTablesReallyInstalled = array_merge($tablesInstalled, $allArchiveNumeric, $allArchiveBlob);
            $allTablesReallyInstalled = array_unique($allTablesReallyInstalled);
            $this->tablesInstalled = $allTablesReallyInstalled;
        }
        return $this->tablesInstalled;
    }
    /**
     * Checks whether any table exists
     *
     * @return bool  True if tables exist; false otherwise
     */
    public function hasTables()
    {
        return count($this->getTablesInstalled()) != 0;
    }
    /**
     * Create database
     *
     * @param string $dbName Name of the database to create
     */
    public function createDatabase($dbName = null)
    {
        if (is_null($dbName)) {
            $dbName = $this->getDbName();
        }
        $createOptions = $this->getDatabaseCreateOptions();
        $dbName = str_replace('`', '', $dbName);
        Db::exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` {$createOptions}");
    }
    /**
     * Creates a new table in the database.
     *
     * @param string $nameWithoutPrefix The name of the table without any piwik prefix.
     * @param string $createDefinition  The table create definition, see the "MySQL CREATE TABLE" specification for
     *                                  more information.
     * @throws \Exception
     */
    public function createTable($nameWithoutPrefix, $createDefinition)
    {
        $statement = sprintf("CREATE TABLE IF NOT EXISTS `%s` ( %s ) %s;", Common::prefixTable($nameWithoutPrefix), $createDefinition, $this->getTableCreateOptions());
        try {
            Db::exec($statement);
        } catch (Exception $e) {
            // mysql code error 1050:table already exists
            // see bug #153 https://github.com/piwik/piwik/issues/153
            if (!$this->getDb()->isErrNo($e, '1050')) {
                throw $e;
            }
        }
    }
    /**
     * Drop database
     */
    public function dropDatabase($dbName = null)
    {
        $dbName = $dbName ?: $this->getDbName();
        $dbName = str_replace('`', '', $dbName);
        Db::exec("DROP DATABASE IF EXISTS `" . $dbName . "`");
    }
    /**
     * Create all tables
     */
    public function createTables()
    {
        $db = $this->getDb();
        $prefixTables = $this->getTablePrefix();
        $tablesAlreadyInstalled = $this->getAllExistingTables($prefixTables);
        $tablesToCreate = $this->getTablesCreateSql();
        unset($tablesToCreate['archive_blob']);
        unset($tablesToCreate['archive_numeric']);
        foreach ($tablesToCreate as $tableName => $tableSql) {
            $tableName = $prefixTables . $tableName;
            if (!in_array($tableName, $tablesAlreadyInstalled)) {
                $db->query($tableSql);
            }
        }
    }
    /**
     * Creates an entry in the User table for the "anonymous" user.
     */
    public function createAnonymousUser()
    {
        $now = Date::factory('now')->getDatetime();
        // The anonymous user is the user that is assigned by default
        // note that the token_auth value is anonymous, which is assigned by default as well in the Login plugin
        $db = $this->getDb();
        $db->query("INSERT IGNORE INTO " . Common::prefixTable("user") . "\n                    (`login`, `password`, `email`, `twofactor_secret`, `superuser_access`, `date_registered`, `ts_password_modified`,\n                    `idchange_last_viewed`)\n                    VALUES ( 'anonymous', '', 'anonymous@example.org', '', 0, '{$now}', '{$now}' , NULL);");
        $model = new Model();
        $model->addTokenAuth('anonymous', 'anonymous', 'anonymous default token', $now);
    }
    /**
     * Records the Matomo version a user used when installing this Matomo for the first time
     */
    public function recordInstallVersion()
    {
        if (!self::getInstallVersion()) {
            Option::set(self::OPTION_NAME_MATOMO_INSTALL_VERSION, Version::VERSION);
        }
    }
    /**
     * Returns which Matomo version was used to install this Matomo for the first time.
     */
    public function getInstallVersion()
    {
        Option::clearCachedOption(self::OPTION_NAME_MATOMO_INSTALL_VERSION);
        $version = Option::get(self::OPTION_NAME_MATOMO_INSTALL_VERSION);
        if (!empty($version)) {
            return $version;
        }
    }
    /**
     * Truncate all tables
     */
    public function truncateAllTables()
    {
        $tables = $this->getAllExistingTables();
        foreach ($tables as $table) {
            Db::query("TRUNCATE `{$table}`");
        }
    }
    /**
     * Adds a MAX_EXECUTION_TIME hint into a SELECT query if $limit is bigger than 0
     *
     * @param string $sql  query to add hint to
     * @param float $limit  time limit in seconds
     * @return string
     */
    public function addMaxExecutionTimeHintToQuery(string $sql, float $limit) : string
    {
        if ($limit <= 0) {
            return $sql;
        }
        $sql = trim($sql);
        $pos = stripos($sql, 'SELECT');
        $isMaxExecutionTimeoutAlreadyPresent = stripos($sql, 'MAX_EXECUTION_TIME(') !== \false;
        if ($pos !== \false && !$isMaxExecutionTimeoutAlreadyPresent) {
            $timeInMs = $limit * 1000;
            $timeInMs = (int) $timeInMs;
            $maxExecutionTimeHint = ' /*+ MAX_EXECUTION_TIME(' . $timeInMs . ') */ ';
            $sql = substr_replace($sql, 'SELECT ' . $maxExecutionTimeHint, $pos, strlen('SELECT'));
        }
        return $sql;
    }
    public function supportsComplexColumnUpdates() : bool
    {
        return \true;
    }
    /**
     * Returns the default collation for a charset.
     *
     * Will return an empty string for an unknown charset
     * (can happen for alias charsets like "utf8").
     *
     * @param string $charset
     *
     * @return string
     * @throws Exception
     */
    public function getDefaultCollationForCharset(string $charset) : string
    {
        $result = $this->getDb()->fetchRow('SHOW CHARACTER SET WHERE `Charset` = ?', [$charset]);
        return $result['Default collation'] ?? '';
    }
    public function getDefaultPort() : int
    {
        return 3306;
    }
    public function getTableCreateOptions() : string
    {
        $engine = $this->getTableEngine();
        $charset = $this->getUsedCharset();
        $collation = $this->getUsedCollation();
        $rowFormat = $this->getTableRowFormat();
        $options = "ENGINE={$engine} DEFAULT CHARSET={$charset}";
        if ('' !== $collation) {
            $options .= " COLLATE={$collation}";
        }
        if ('' !== $rowFormat) {
            $options .= " {$rowFormat}";
        }
        return $options;
    }
    public function optimizeTables(array $tables, bool $force = \false) : bool
    {
        $optimize = Config::getInstance()->General['enable_sql_optimize_queries'];
        if (empty($optimize) && !$force) {
            return \false;
        }
        if (empty($tables)) {
            return \false;
        }
        if (!$this->isOptimizeInnoDBSupported() && !$force) {
            // filter out all InnoDB tables
            $myisamDbTables = array();
            foreach ($this->getTableStatus() as $row) {
                if (strtolower($row['Engine']) == 'myisam' && in_array($row['Name'], $tables)) {
                    $myisamDbTables[] = $row['Name'];
                }
            }
            $tables = $myisamDbTables;
        }
        if (empty($tables)) {
            return \false;
        }
        // optimize the tables
        $success = \true;
        foreach ($tables as &$t) {
            $ok = Db::query('OPTIMIZE TABLE ' . $t);
            if (!$ok) {
                $success = \false;
            }
        }
        return $success;
    }
    public function isOptimizeInnoDBSupported() : bool
    {
        $version = strtolower($this->getVersion());
        // Note: This check for MariaDb is here on purpose, so it's working correctly for people
        // having MySQL still configured, when using MariaDb
        if (strpos($version, "mariadb") === \false) {
            return \false;
        }
        $semanticVersion = strstr($version, '-', $beforeNeedle = \true);
        return version_compare($semanticVersion, '10.1.1', '>=');
    }
    public function supportsSortingInSubquery() : bool
    {
        return \true;
    }
    public function getSupportedReadIsolationTransactionLevel() : string
    {
        return 'READ UNCOMMITTED';
    }
    protected function getDatabaseCreateOptions() : string
    {
        $charset = DbHelper::getDefaultCharset();
        $collation = $this->getDefaultCollationForCharset($charset);
        $options = "DEFAULT CHARACTER SET {$charset}";
        if ('' !== $collation) {
            $options .= " COLLATE {$collation}";
        }
        return $options;
    }
    protected function getTableEngine()
    {
        return $this->getDbSettings()->getEngine();
    }
    protected function getTableRowFormat() : string
    {
        return $this->getDbSettings()->getRowFormat();
    }
    protected function getUsedCharset() : string
    {
        return $this->getDbSettings()->getUsedCharset();
    }
    protected function getUsedCollation() : string
    {
        return $this->getDbSettings()->getUsedCollation();
    }
    private function getTablePrefix()
    {
        return $this->getDbSettings()->getTablePrefix();
    }
    protected function getVersion() : string
    {
        return Db::fetchOne("SELECT VERSION()");
    }
    protected function getTableStatus()
    {
        return Db::fetchAll("SHOW TABLE STATUS");
    }
    private function getDb()
    {
        return Db::get();
    }
    private function getDbSettings()
    {
        return new Db\Settings();
    }
    private function getDbName()
    {
        return $this->getDbSettings()->getDbName();
    }
    private function getAllExistingTables($prefixTables = \false)
    {
        if (empty($prefixTables)) {
            $prefixTables = $this->getTablePrefixEscaped();
        }
        return Db::get()->fetchCol("SHOW TABLES LIKE '" . $prefixTables . "%'");
    }
    private function getTablePrefixEscaped()
    {
        $prefixTables = $this->getTablePrefix();
        // '_' matches any character; force it to be literal
        $prefixTables = str_replace('_', '\\_', $prefixTables);
        return $prefixTables;
    }
}
