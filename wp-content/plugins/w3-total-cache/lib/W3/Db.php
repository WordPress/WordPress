<?php

/**
 * W3 Database object
 */
if (!defined('ABSPATH')) {
    die();
}

if (!class_exists('W3_Db_Driver')) {
    require_once ABSPATH . 'wp-includes/wp-db.php';

    class W3_Db_Driver extends wpdb {
    }
}

//TODO: Added for backwards compatibility
if(!class_exists('W3_Db')){
/**
 * Class W3_Db
 * Database access mediator
 */
class W3_Db extends W3_Db_Driver {
    /**
     * Returns onject instance. Called by WP engine
     *
     * @return W3_Db
     */
    static function instance() {
        static $instances = array();

        if (!isset($instances[0])) {
            $processors = array();
            $call_default_constructor = true;

            // no caching during activation
            $is_installing = (defined('WP_INSTALLING') && WP_INSTALLING);

            $config = w3_instance('W3_Config');
            if (!$is_installing && $config->get_boolean('dbcache.enabled')) {
                $processors[] = w3_instance('W3_DbCache');
            }
            if (w3_is_dbcluster()) {
                $processors[] = w3_instance('W3_Enterprise_DbCluster');
            }
            
            $processors[] = new W3_DbProcessor();
            
            $class = __CLASS__;
            $o = new $class($processors);
            
            $underlying_manager = new W3_DbCallUnderlying($o);
            
            foreach ($processors as $processor) {
                $processor->manager = $o;
                $processor->underlying_manager = $underlying_manager;
            }

            // initialize after processors configured
            $o->initialize();
            
            @$instances[0] = $o;
        }

        return $instances[0];
    }
    
    /*
     * @param boolean $call_default_constructor
     */
    function __construct($processors) {
        $this->processors = $processors;
        $this->processor = $processors[0];
        $this->processor_number = 0;
    }

    /**
     * Initializes object after processors configured. Called from instance() only
     */
    function initialize() {
        return $this->processor->initialize();
    }

    /**
     * Overriten logic of wp_db by processor.
     */
    function insert($table, $data, $format = null) {
        return $this->processor->insert($table, $data, $format);
    }

    /**
     * Overriten logic of wp_db by processor.
     */
    function query($query) {
        return $this->processor->query($query);
    }

    /**
     * Overriten logic of wp_db by processor.
     */
    function replace($table, $data, $format = null) {
        return $this->processor->replace($table, $data, $format);
    }

    /**
     * Overriten logic of wp_db by processor.
     */
    function update($table, $data, $where, $format = null, $where_format = null) {
        return $this->processor->update($table, $data, $where, $format, $where_format);
    }
    
    /**
     * Overriten logic of wp_db by processor.
     */
    function init_charset() {
        return $this->processor->init_charset();
    }

    /**
     * Overriten logic of wp_db by processor.
     */
    function set_charset($dbh, $charset = null, $collate = null) {
        return $this->processor->set_charset($dbh, $charset, $collate);
    }

    /**
     * Overriten logic of wp_db by processor.
     */
    function flush() {
        return $this->processor->flush();
    }

    /**
     * Overriten logic of wp_db by processor.
     */
    function check_database_version($dbh_or_table = false) {
        return $this->processor->check_database_version($dbh_or_table);
    }

    /**
     * Overriten logic of wp_db by processor.
     */
    function supports_collation( $dbh_or_table = false ) {
        return $this->processor->supports_collation($dbh_or_table);
    }

    /**
     * Overriten logic of wp_db by processor.
     */
    function has_cap( $db_cap, $dbh_or_table = false ) {
        return $this->processor->has_cap($db_cap, $dbh_or_table);
    }

    /**
     * Overriten logic of wp_db by processor.
     */
    function db_version( $dbh_or_table = false ) {
        return $this->processor->db_version($dbh_or_table);
    }
    
    /**
     * Default initialization method, calls wp_db apropriate method
     */
    function default_initialize() {
        parent::__construct(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
    }

    /**
     * Default implementation, calls wp_db apropriate method
     */
    function default_insert($table, $data, $format = null) {
        return parent::insert($table, $data, $format);
    }

    /**
     * Default implementation, calls wp_db apropriate method
     */
    function default_query($query) {
        return parent::query($query);
    }

    /**
     * Default implementation, calls wp_db apropriate method
     */
    function default_replace($table, $data, $format = null) {
        return parent::replace($table, $data, $format);
    }

    /**
     * Default implementation, calls wp_db apropriate method
     */
    function default_update($table, $data, $where, $format = null, $where_format = null) {
        return parent::update($table, $data, $where, $format, $where_format);
    }
    
    /**
     * Default implementation, calls wp_db apropriate method
     */
    function default_init_charset() {
        return parent::init_charset();
    }

    /**
     * Default implementation, calls wp_db apropriate method
     */
    function default_set_charset($dbh, $charset = null, $collate = null) {
        return parent::set_charset($dbh, $charset, $collate);
    }

    /**
     * Default implementation, calls wp_db apropriate method
     */
    function default_flush() {
        return parent::flush();
    }

    /**
     * Default implementation, calls wp_db apropriate method
     */
    function default_check_database_version($dbh_or_table = false) {
        return parent::check_database_version($dbh_or_table);
    }

    /**
     * Default implementation, calls wp_db apropriate method
     */
    function default_supports_collation( $dbh_or_table = false ) {
        return parent::supports_collation($dbh_or_table);
    }

    /**
     * Default implementation, calls wp_db apropriate method
     */
    function default_has_cap( $db_cap, $dbh_or_table = false ) {
        return parent::has_cap($db_cap, $dbh_or_table);
    }

    /**
     * Default implementation, calls wp_db apropriate method
     */
    function default_db_version( $dbh_or_table = false ) {
        return parent::db_version($dbh_or_table);
    }
    
    /**
     * Default implementation, calls wp_db apropriate method
     */
    function switch_active_processor($offset) {
        $new_processor_number = $this->processor_number + $offset;
        if ($new_processor_number <= 0) {
            $new_processor_number = 0;
        } else if ($new_processor_number >= count($this->processors)) {
            $new_processor_number = count($this->processors) - 1;
        }
        
        $offset_made = $new_processor_number - $this->processor_number;
        $this->processor_number = $new_processor_number;
        $this->processor = $this->processors[$new_processor_number];
        
        return $offset_made;
    }
}



/**
 * Class W3_DbProcessor
 * Does separate operation without inheritance
 */
class W3_DbProcessor {
    /**
     * Top database-connection object.
     * Initialized by W3_Db::instance
     *
     * @var object
     */
    var $manager = null;

    /**
     * Database-connection using overrides of next processor in queue
     * Initialized by W3_Db::instance
     * 
     * @var object
     */
    var $underlying_manager = null;
    
    /**
     * Placeholder for database initialization
     */
    function initialize() {
        return $this->manager->default_initialize();
    }
    
    /**
     * Placeholder for apropriate wp_db method replacement.
     * By default calls wp_db implementation
     */
    function insert($table, $data, $format = null) {
        return $this->manager->default_insert($table, $data, $format);
    }

    /**
     * Placeholder for apropriate wp_db method replacement.
     * By default calls wp_db implementation
     */
    function query($query) {
        return $this->manager->default_query($query);
    }

    /**
     * Placeholder for apropriate wp_db method replacement.
     * By default calls wp_db implementation
     */
    function replace($table, $data, $format = null) {
        return $this->manager->default_replace($table, $data, $format);
    }

    /**
     * Placeholder for apropriate wp_db method replacement.
     * By default calls wp_db implementation
     */
    function update($table, $data, $where, $format = null, $where_format = null) {
        return $this->manager->default_update($table, $data, $where, $format, $where_format);
    }
    
    /**
     * Placeholder for apropriate wp_db method replacement.
     * By default calls wp_db implementation
     */
    function init_charset() {
        return $this->manager->default_init_charset();
    }

    /**
     * Placeholder for apropriate wp_db method replacement.
     * By default calls wp_db implementation
     */
    function set_charset($dbh, $charset = null, $collate = null) {
        return $this->manager->default_set_charset($dbh, $charset, $collate);
    }

    /**
     * Placeholder for apropriate wp_db method replacement.
     * By default calls wp_db implementation
     */
    function flush() {
        return $this->manager->default_flush();
    }

    /**
     * Placeholder for apropriate wp_db method replacement.
     * By default calls wp_db implementation
     */
    function check_database_version($dbh_or_table = false) {
        return $this->manager->default_check_database_version($dbh_or_table);
    }

    /**
     * Placeholder for apropriate wp_db method replacement.
     * By default calls wp_db implementation
     */
    function supports_collation( $dbh_or_table = false ) {
        return $this->manager->default_supports_collation($dbh_or_table);
    }

    /**
     * Placeholder for apropriate wp_db method replacement.
     * By default calls wp_db implementation
     */
    function has_cap( $db_cap, $dbh_or_table = false ) {
        return $this->manager->default_has_cap($db_cap, $dbh_or_table);
    }

    /**
     * Placeholder for apropriate wp_db method replacement.
     * By default calls wp_db implementation
     */
    function db_version( $dbh_or_table = false ) {
        return $this->manager->default_db_version($dbh_or_table);
    }
}



/**
 * Class W3_DbCallUnderlying
 */
class W3_DbCallUnderlying {
    function __construct($manager) {
        $this->manager = $manager;
    }

    /**
     * Calls underlying processor's aproptiate method of wp_db
     */
    function initialize() {
        $switched = $this->manager->switch_active_processor(1);
        
        try {
            $r = $this->manager->initialize();
            
            $this->manager->switch_active_processor(-$switched);
            return $r;
        } catch (Exception $e) {
            $this->manager->switch_active_processor(-$switched);
            throw $e;
        }
    }

    /**
     * Calls underlying processor's aproptiate method of wp_db
     */
    function flush() {
        $switched = $this->manager->switch_active_processor(1);
        
        try {
            $r = $this->manager->flush();
            
            $this->manager->switch_active_processor(-$switched);
            return $r;
        } catch (Exception $e) {
            $this->manager->switch_active_processor(-$switched);
            throw $e;
        }
    }
    
    /**
     * Calls underlying processor's aproptiate method of wp_db
     */
    function query($query) {
        $switched = $this->manager->switch_active_processor(1);
        
        try {
            $r = $this->manager->query($query);
            
            $this->manager->switch_active_processor(-$switched);
            return $r;
        } catch (Exception $e) {
            $this->manager->switch_active_processor(-$switched);
            throw $e;
        }
    }
    
    /**
     * Calls underlying processor's aproptiate method of wp_db
     */
    function insert($table, $data, $format = null) {
        $switched = $this->manager->switch_active_processor(1);
        
        try {
            $r = $this->manager->insert($table, $data, $format);
            
            $this->manager->switch_active_processor(-$switched);
            return $r;
        } catch (Exception $e) {
            $this->manager->switch_active_processor(-$switched);
            throw $e;
        }
    }

    /**
     * Calls underlying processor's aproptiate method of wp_db
     */
    function replace($table, $data, $format = null) {
        $switched = $this->manager->switch_active_processor(1);
        
        try {
            $r = $this->manager->replace($table, $data, $format);
            
            $this->manager->switch_active_processor(-$switched);
            return $r;
        } catch (Exception $e) {
            $this->manager->switch_active_processor(-$switched);
            throw $e;
        }
    }

    /**
     * Calls underlying processor's aproptiate method of wp_db
     */
    function update($table, $data, $where, $format = null, $where_format = null) {
        $switched = $this->manager->switch_active_processor(1);
        
        try {
            $r = $this->manager->update($table, $data, $where, $format, $where_format);
            
            $this->manager->switch_active_processor(-$switched);
            return $r;
        } catch (Exception $e) {
            $this->manager->switch_active_processor(-$switched);
            throw $e;
        }
    }
}
}
?>