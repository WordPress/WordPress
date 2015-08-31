<?php
/**
 * WordPress DB Class
 *
 * Original code from {@link http://php.justinvincent.com Justin Vincent (justin@visunet.ie)}
 *
 * @package WordPress
 * @subpackage Database
 * @since 0.71
 */

/**
 * WordPress Database Access Abstraction Object
 *
 * It is possible to replace this class with your own
 * by setting the $wpdb global variable in wp-content/db.php
 * file with your class. You can name it wpdb also, since
 * this file will not be included, if the other file is
 * available.
 *
 * @link http://codex.wordpress.org/Function_Reference/wpdb_Class
 *
 * @package WordPress
 * @subpackage Database
 * @since 0.71
 * @final
 */
class W3_Db_Driver extends SQL_Translations {

        /**
         * Whether to show SQL/DB errors
         *
         * @since 0.71
         * @access private
         * @var bool
         */
        var $show_errors = false;

        /**
         * Whether to suppress errors during the DB bootstrapping.
         *
         * @access private
         * @since 2.5
         * @var bool
         */
        var $suppress_errors = false;

        /**
         * The last error during query.
         *
         * @see get_last_error()
         * @since 2.5
         * @access private
         * @var string
         */
        var $last_error = '';

        /**
         * Amount of queries made
         *
         * @since 1.2.0
         * @access private
         * @var int
         */
        var $num_queries = 0;

        /**
         * Count of rows returned by previous query
         *
         * @since 1.2
         * @access private
         * @var int
         */
        var $num_rows = 0;

        /**
         * Count of affected rows by previous query
         *
         * @since 0.71
         * @access private
         * @var int
         */
        var $rows_affected = 0;

        /**
         * The ID generated for an AUTO_INCREMENT column by the previous query (usually INSERT).
         *
         * @since 0.71
         * @access public
         * @var int
         */
        var $insert_id = 0;

        /**
         * Saved result of the last query made
         *
         * @since 1.2.0
         * @access private
         * @var array
         */
        var $last_query;

        /**
         * Results of the last query made
         *
         * @since 1.0.0
         * @access private
         * @var array|null
         */
        var $last_result;

        /**
         * Saved info on the table column
         *
         * @since 1.2.0
         * @access private
         * @var array
         */
        var $col_info;

        /**
         * Saved queries that were executed
         *
         * @since 1.5.0
         * @access private
         * @var array
         */
        var $queries;

        /**
         * WordPress table prefix
         *
         * You can set this to have multiple WordPress installations
         * in a single database. The second reason is for possible
         * security precautions.
         *
         * @since 0.71
         * @access private
         * @var string
         */
        var $prefix = '';

        /**
         * Whether the database queries are ready to start executing.
         *
         * @since 2.5.0
         * @access private
         * @var bool
         */
        var $ready = false;

        /**
         * {@internal Missing Description}}
         *
         * @since 3.0.0
         * @access public
         * @var int
         */
        var $blogid = 0;

        /**
         * {@internal Missing Description}}
         *
         * @since 3.0.0
         * @access public
         * @var int
         */
        var $siteid = 0;

        /**
         * List of WordPress per-blog tables
         *
         * @since 2.5.0
         * @access private
         * @see wpdb::tables()
         * @var array
         */
        var $tables = array( 'posts', 'comments', 'links', 'options', 'postmeta',
                'terms', 'term_taxonomy', 'term_relationships', 'commentmeta' );

        /**
         * List of deprecated WordPress tables
         *
         * categories, post2cat, and link2cat were deprecated in 2.3.0, db version 5539
         *
         * @since 2.9.0
         * @access private
         * @see wpdb::tables()
         * @var array
         */
        var $old_tables = array( 'categories', 'post2cat', 'link2cat' );

        /**
         * List of WordPress global tables
         *
         * @since 3.0.0
         * @access private
         * @see wpdb::tables()
         * @var array
         */
        var $global_tables = array( 'users', 'usermeta' );

        /**
         * List of Multisite global tables
         *
         * @since 3.0.0
         * @access private
         * @see wpdb::tables()
         * @var array
         */
        var $ms_global_tables = array( 'blogs', 'signups', 'site', 'sitemeta',
                'sitecategories', 'registration_log', 'blog_versions' );

        /**
         * WordPress Comments table
         *
         * @since 1.5.0
         * @access public
         * @var string
         */
        var $comments;

        /**
         * WordPress Comment Metadata table
         *
         * @since 2.9.0
         * @access public
         * @var string
         */
        var $commentmeta;

        /**
         * WordPress Links table
         *
         * @since 1.5.0
         * @access public
         * @var string
         */
        var $links;

        /**
         * WordPress Options table
         *
         * @since 1.5.0
         * @access public
         * @var string
         */
        var $options;

        /**
         * WordPress Post Metadata table
         *
         * @since 1.5.0
         * @access public
         * @var string
         */
        var $postmeta;

        /**
         * WordPress Posts table
         *
         * @since 1.5.0
         * @access public
         * @var string
         */
        var $posts;

        /**
         * WordPress Terms table
         *
         * @since 2.3.0
         * @access public
         * @var string
         */
        var $terms;

        /**
         * WordPress Term Relationships table
         *
         * @since 2.3.0
         * @access public
         * @var string
         */
        var $term_relationships;

        /**
         * WordPress Term Taxonomy table
         *
         * @since 2.3.0
         * @access public
         * @var string
         */
        var $term_taxonomy;

        /*
         * Global and Multisite tables
         */

        /**
         * WordPress User Metadata table
         *
         * @since 2.3.0
         * @access public
         * @var string
         */
        var $usermeta;

        /**
         * WordPress Users table
         *
         * @since 1.5.0
         * @access public
         * @var string
         */
        var $users;

        /**
         * Multisite Blogs table
         *
         * @since 3.0.0
         * @access public
         * @var string
         */
        var $blogs;

        /**
         * Multisite Blog Versions table
         *
         * @since 3.0.0
         * @access public
         * @var string
         */
        var $blog_versions;

        /**
         * Multisite Registration Log table
         *
         * @since 3.0.0
         * @access public
         * @var string
         */
        var $registration_log;

        /**
         * Multisite Signups table
         *
         * @since 3.0.0
         * @access public
         * @var string
         */
        var $signups;

        /**
         * Multisite Sites table
         *
         * @since 3.0.0
         * @access public
         * @var string
         */
        var $site;

        /**
         * Multisite Sitewide Terms table
         *
         * @since 3.0.0
         * @access public
         * @var string
         */
        var $sitecategories;

        /**
         * Multisite Site Metadata table
         *
         * @since 3.0.0
         * @access public
         * @var string
         */
        var $sitemeta;

        /**
         * Format specifiers for DB columns. Columns not listed here default to %s. Initialized during WP load.
         *
         * Keys are column names, values are format types: 'ID' => '%d'
         *
         * @since 2.8.0
         * @see wpdb:prepare()
         * @see wpdb:insert()
         * @see wpdb:update()
         * @see wp_set_wpdb_vars()
         * @access public
         * @var array
         */
        var $field_types = array();

        /**
         * Database table columns charset
         *
         * @since 2.2.0
         * @access public
         * @var string
         */
        var $charset;

        /**
         * Database table columns collate
         *
         * @since 2.2.0
         * @access public
         * @var string
         */
        var $collate;

        /**
         * Whether to use mysql_real_escape_string
         *
         * @since 2.8.0
         * @access public
         * @var bool
         */
        var $real_escape = false;

        /**
         * Database Username
         *
         * @since 2.9.0
         * @access private
         * @var string
         */
        var $dbuser;

        /**
         * A textual description of the last query/get_row/get_var call
         *
         * @since unknown
         * @access public
         * @var string
         */
        var $func_call;

        /**
         * Saved result of the last translated query made
         *
         * @since 1.2.0
         * @access private
         * @var array
         */
        var $previous_query;

        /**
        * Database type
        *
        * @access public
        * @var string
        */
        var $db_type;

        /**
         * Connects to the database server and selects a database
         *
         * PHP4 compatibility layer for calling the PHP5 constructor.
         *
         * @uses wpdb::__construct() Passes parameters and returns result
         * @since 0.71
         *
         * @param string $dbuser MySQL database user
         * @param string $dbpassword MySQL database password
         * @param string $dbname MySQL database name
         * @param string $dbhost MySQL database host
         */
        function wpdb( $dbuser, $dbpassword, $dbname, $dbhost ) {
                if( defined( 'WP_USE_MULTIPLE_DB' ) && WP_USE_MULTIPLE_DB )
                        $this->db_connect();
                return $this->__construct( $dbuser, $dbpassword, $dbname, $dbhost );
        }

        /**
         * Connects to the database server and selects a database
         *
         * PHP5 style constructor for compatibility with PHP5. Does
         * the actual setting up of the class properties and connection
         * to the database.
         *
         * @link http://core.trac.wordpress.org/ticket/3354
         * @since 2.0.8
         *
         * @param string $dbuser MySQL database user
         * @param string $dbpassword MySQL database password
         * @param string $dbname MySQL database name
         * @param string $dbhost MySQL database host
         */
        function __construct( $dbuser, $dbpassword, $dbname, $dbhost ) {
                register_shutdown_function( array( &$this, '__destruct' ) );

                if ( WP_DEBUG )
                        $this->show_errors();

                if ( is_multisite() ) {
                        $this->charset = 'utf8';
                        if ( defined( 'DB_COLLATE' ) && DB_COLLATE )
                                $this->collate = DB_COLLATE;
                        else
                                $this->collate = 'utf8_general_ci';
                } elseif ( defined( 'DB_COLLATE' ) ) {
                        $this->collate = DB_COLLATE;
                }

                if ( defined( 'DB_CHARSET' ) )
                        $this->charset = DB_CHARSET;

                parent::__construct();

                $this->db_type = DB_TYPE;

                $this->dbuser = $dbuser;

                // Make sure the version is the same for your ntwdblib.dll.
                // The TDS library and the ntwdblib.dll can't be speaking two different protocols.
                putenv("TDSVER=70");

                // Set text limit sizes to max BEFORE connection is made
                ini_set('mssql.textlimit', 2147483647);
                ini_set('mssql.textsize', 2147483647);

                if (get_magic_quotes_gpc()) {
                        $dbhost = trim(str_replace("\\\\", "\\", $dbhost));
                }

                $this->dbh = mssql_connect($dbhost, $dbuser, $dbpassword);
                mssql_min_error_severity(0);
                mssql_min_message_severity(17);

                if ( !$this->dbh ) {
                        $this->bail( sprintf( /*WP_I18N_DB_CONN_ERROR*/"
<h1>Error establishing a database connection</h1>
<p>This either means that the username and password information in your <code>wp-config.php</code> file is incorrect or we can't contact the database server at <code>%s</code>. This could mean your host's database server is down.</p>
<ul>
        <li>Are you sure you have the correct username and password?</li>
        <li>Are you sure that you have typed the correct hostname?</li>
        <li>Are you sure that the database server is running?</li>
</ul>
<p>If you're unsure what these terms mean you should probably contact your host. If you still need help you can always visit the <a href='http://wordpress.org/support/'>WordPress Support Forums</a>.</p>
"/*/WP_I18N_DB_CONN_ERROR*/, $dbhost ), 'db_connect_fail' );
                        if ( defined( 'WP_SETUP_CONFIG' ) )
                            return;

                        die();
                }

                $this->ready = true;

                @mssql_query('SET TEXTSIZE 2147483647');
                /*
                if ( $this->has_cap( 'collation' ) && !empty( $this->charset ) ) {
                        if ( function_exists( 'mysql_set_charset' ) ) {
                                mysql_set_charset( $this->charset, $this->dbh );
                                $this->real_escape = true;
                        } else {
                                $query = $this->prepare( 'SET NAMES %s', $this->charset );
                                if ( ! empty( $this->collate ) )
                                        $query .= $this->prepare( ' COLLATE %s', $this->collate );
                                $this->query( $query );
                        }
                }
                */

                $this->select( $dbname, $this->dbh );
        }

        /**
         * PHP5 style destructor and will run when database object is destroyed.
         *
         * @see wpdb::__construct()
         * @since 2.0.8
         * @return bool true
         */
        function __destruct() {
                return true;
        }

        /**
         * Sets the table prefix for the WordPress tables.
         *
         * @since 2.5.0
         *
         * @param string $prefix Alphanumeric name for the new prefix.
         * @return string|WP_Error Old prefix or WP_Error on error
         */
        function set_prefix( $prefix, $set_table_names = true ) {

                if ( preg_match( '|[^a-z0-9_]|i', $prefix ) )
                        return new WP_Error('invalid_db_prefix', /*WP_I18N_DB_BAD_PREFIX*/'Invalid database prefix'/*/WP_I18N_DB_BAD_PREFIX*/);

                $old_prefix = is_multisite() ? '' : $prefix;

                if ( isset( $this->base_prefix ) )
                        $old_prefix = $this->base_prefix;

                $this->base_prefix = $prefix;

                if ( $set_table_names ) {
                        foreach ( $this->tables( 'global' ) as $table => $prefixed_table )
                                $this->$table = $prefixed_table;

                        if ( is_multisite() && empty( $this->blogid ) )
                                return $old_prefix;

                        $this->prefix = $this->get_blog_prefix();

                        foreach ( $this->tables( 'blog' ) as $table => $prefixed_table )
                                $this->$table = $prefixed_table;

                        foreach ( $this->tables( 'old' ) as $table => $prefixed_table )
                                $this->$table = $prefixed_table;
                }
                return $old_prefix;
        }

        /**
         * Sets blog id.
         *
         * @since 3.0.0
         * @access public
         * @param int $blog_id
         * @param int $site_id Optional.
         * @return string previous blog id
         */
        function set_blog_id( $blog_id, $site_id = 0 ) {
                if ( ! empty( $site_id ) )
                        $this->siteid = $site_id;

                $old_blog_id  = $this->blogid;
                $this->blogid = $blog_id;

                $this->prefix = $this->get_blog_prefix();

                foreach ( $this->tables( 'blog' ) as $table => $prefixed_table )
                        $this->$table = $prefixed_table;

                foreach ( $this->tables( 'old' ) as $table => $prefixed_table )
                        $this->$table = $prefixed_table;

                return $old_blog_id;
        }

        /**
         * Gets blog prefix.
         *
         * @uses is_multisite()
         * @since 3.0.0
         * @param int $blog_id Optional.
         * @return string Blog prefix.
         */
        function get_blog_prefix( $blog_id = null ) {
                if ( is_multisite() ) {
                        if ( null === $blog_id )
                                $blog_id = $this->blogid;
                        if ( defined( 'MULTISITE' ) && ( 0 == $blog_id || 1 == $blog_id ) )
                                return $this->base_prefix;
                        else
                                return $this->base_prefix . $blog_id . '_';
                } else {
                        return $this->base_prefix;
                }
        }

        /**
         * Returns an array of WordPress tables.
         *
         * Also allows for the CUSTOM_USER_TABLE and CUSTOM_USER_META_TABLE to
         * override the WordPress users and usersmeta tables that would otherwise
         * be determined by the prefix.
         *
         * The scope argument can take one of the following:
         *
         * 'all' - returns 'all' and 'global' tables. No old tables are returned.
         * 'blog' - returns the blog-level tables for the queried blog.
         * 'global' - returns the global tables for the installation, returning multisite tables only if running multisite.
         * 'ms_global' - returns the multisite global tables, regardless if current installation is multisite.
         * 'old' - returns tables which are deprecated.
         *
         * @since 3.0.0
         * @uses wpdb::$tables
         * @uses wpdb::$old_tables
         * @uses wpdb::$global_tables
         * @uses wpdb::$ms_global_tables
         * @uses is_multisite()
         *
         * @param string $scope Optional. Can be all, global, ms_global, blog, or old tables. Defaults to all.
         * @param bool $prefix Optional. Whether to include table prefixes. Default true. If blog
         *      prefix is requested, then the custom users and usermeta tables will be mapped.
         * @param int $blog_id Optional. The blog_id to prefix. Defaults to wpdb::$blogid. Used only when prefix is requested.
         * @return array Table names. When a prefix is requested, the key is the unprefixed table name.
         */
        function tables( $scope = 'all', $prefix = true, $blog_id = 0 ) {
                switch ( $scope ) {
                        case 'all' :
                                $tables = array_merge( $this->global_tables, $this->tables );
                                if ( is_multisite() )
                                        $tables = array_merge( $tables, $this->ms_global_tables );
                                break;
                        case 'blog' :
                                $tables = $this->tables;
                                break;
                        case 'global' :
                                $tables = $this->global_tables;
                                if ( is_multisite() )
                                        $tables = array_merge( $tables, $this->ms_global_tables );
                                break;
                        case 'ms_global' :
                                $tables = $this->ms_global_tables;
                                break;
                        case 'old' :
                                $tables = $this->old_tables;
                                break;
                        default :
                                return array();
                                break;
                }

                if ( $prefix ) {
                        if ( ! $blog_id )
                                $blog_id = $this->blogid;
                        $blog_prefix = $this->get_blog_prefix( $blog_id );
                        $base_prefix = $this->base_prefix;
                        $global_tables = array_merge( $this->global_tables, $this->ms_global_tables );
                        foreach ( $tables as $k => $table ) {
                                if ( in_array( $table, $global_tables ) )
                                        $tables[ $table ] = $base_prefix . $table;
                                else
                                        $tables[ $table ] = $blog_prefix . $table;
                                unset( $tables[ $k ] );
                        }

                        if ( isset( $tables['users'] ) && defined( 'CUSTOM_USER_TABLE' ) )
                                $tables['users'] = CUSTOM_USER_TABLE;

                        if ( isset( $tables['usermeta'] ) && defined( 'CUSTOM_USER_META_TABLE' ) )
                                $tables['usermeta'] = CUSTOM_USER_META_TABLE;
                }

                return $tables;
        }

        /**
         * Selects a database using the current database connection.
         *
         * The database name will be changed based on the current database
         * connection. On failure, the execution will bail and display an DB error.
         *
         * @since 0.71
         *
         * @param string $db MySQL database name
         * @return null Always null.
         */
        function select( $db, &$dbh ) {
                if ( !@mssql_select_db($db, $dbh) ) {
                        $this->ready = false;
                        $this->bail( sprintf( /*WP_I18N_DB_SELECT_DB*/'
<h1>Can&#8217;t select database</h1>
<p>We were able to connect to the database server (which means your username and password is okay) but not able to select the <code>%1$s</code> database.</p>
<ul>
<li>Are you sure it exists?</li>
<li>Does the user <code>%2$s</code> have permission to use the <code>%1$s</code> database?</li>
<li>On some systems the name of your database is prefixed with your username, so it would be like <code>username_%1$s</code>. Could that be the problem?</li>
</ul>
<p>If you don\'t know how to set up a database you should <strong>contact your host</strong>. If all else fails you may find help at the <a href="http://wordpress.org/support/">WordPress Support Forums</a>.</p>'/*/WP_I18N_DB_SELECT_DB*/, $db, $this->dbuser ), 'db_select_fail' );
                        return;
                }
        }

        /**
         * Weak escape, using addslashes()
         *
         * @see addslashes()
         * @since 2.8.0
         * @access private
         *
         * @param string $string
         * @return string
         */
        function _weak_escape( $string ) {
                return str_replace("'", "''", $string);
        }

        /**
         * Real escape, using mysql_real_escape_string() or addslashes()
         *
         * @see mysql_real_escape_string()
         * @see addslashes()
         * @since 2.8
         * @access private
         *
         * @param  string $string to escape
         * @return string escaped
         */
        function _real_escape( $string ) {
                return str_replace("'", "''", $string);
        }

        /**
         * Escape data. Works on arrays.
         *
     * @uses wpdb::_escape()
     * @uses wpdb::_real_escape()
         * @since  2.8
         * @access private
         *
         * @param  string|array $data
         * @return string|array escaped
         */
        function _escape( $data ) {
                if ( is_array( $data ) ) {
                        foreach ( (array) $data as $k => $v ) {
                                if ( is_array($v) )
                                        $data[$k] = $this->_escape( $v );
                                else
                                        $data[$k] = $this->_real_escape( $v );
                        }
                } else {
                        $data = $this->_real_escape( $data );
                }

                return $data;
        }

        /**
         * Escapes content for insertion into the database using addslashes(), for security.
         *
         * Works on arrays.
         *
         * @since 0.71
         * @param string|array $data to escape
         * @return string|array escaped as query safe string
         */
        function escape( $data ) {
                if ( is_array( $data ) ) {
                        foreach ( (array) $data as $k => $v ) {
                                if ( is_array( $v ) )
                                        $data[$k] = $this->escape( $v );
                                else
                                        $data[$k] = $this->_weak_escape( $v );
                        }
                } else {
                        $data = $this->_weak_escape( $data );
                }

                return $data;
        }

        /**
         * Escapes content by reference for insertion into the database, for security
         *
         * @uses wpdb::_real_escape()
         * @since 2.3.0
         * @param string $string to escape
         * @return void
         */
        function escape_by_ref( &$string ) {
                $string = $this->_real_escape( $string );
        }

        /**
         * Prepares a SQL query for safe execution. Uses sprintf()-like syntax.
         *
         * The following directives can be used in the query format string:
         *   %d (decimal number)
         *   %s (string)
         *   %% (literal percentage sign - no argument needed)
         *
         * Both %d and %s are to be left unquoted in the query string and they need an argument passed for them.
         * Literals (%) as parts of the query must be properly written as %%.
         *
         * This function only supports a small subset of the sprintf syntax; it only supports %d (decimal number), %s (string).
         * Does not support sign, padding, alignment, width or precision specifiers.
         * Does not support argument numbering/swapping.
         *
         * May be called like {@link http://php.net/sprintf sprintf()} or like {@link http://php.net/vsprintf vsprintf()}.
         *
         * Both %d and %s should be left unquoted in the query string.
         *
         * <code>
         * wpdb::prepare( "SELECT * FROM `table` WHERE `column` = %s AND `field` = %d", 'foo', 1337 )
         * wpdb::prepare( "SELECT DATE_FORMAT(`field`, '%%c') FROM `table` WHERE `column` = %s", 'foo' );
         * </code>
         *
         * @link http://php.net/sprintf Description of syntax.
         * @since 2.3.0
         *
         * @param string $query Query statement with sprintf()-like placeholders
         * @param array|mixed $args The array of variables to substitute into the query's placeholders if being called like
         *      {@link http://php.net/vsprintf vsprintf()}, or the first variable to substitute into the query's placeholders if
         *      being called like {@link http://php.net/sprintf sprintf()}.
         * @param mixed $args,... further variables to substitute into the query's placeholders if being called like
         *      {@link http://php.net/sprintf sprintf()}.
         * @return null|false|string Sanitized query string, null if there is no query, false if there is an error and string
         *      if there was something to prepare
         */
        function prepare( $query = null ) { // ( $query, *$args )
                if ( is_null( $query ) ) {
                        return;
                }
                $this->prepare_args = func_get_args();
                array_shift($this->prepare_args);
                // If args were passed as an array (as in vsprintf), move them up
                if ( isset($this->prepare_args[0]) && is_array($this->prepare_args[0]) ) {
                        $this->prepare_args = $this->prepare_args[0];
                }
                $flag = '--PREPARE';
                foreach($this->prepare_args as $key => $arg){
                        if (is_serialized($arg)) {
                                $flag = '--SERIALIZED';
                        }
                }
                $query = str_replace("'%s'", '%s', $query); // in case someone mistakenly already singlequoted it
                $query = str_replace('"%s"', '%s', $query); // doublequote unquoting
                $query = preg_replace( '|(?<!%)%s|', "'%s'", $query ); // quote the strings, avoiding escaped strings like %%s
                array_walk($this->prepare_args, array(&$this, 'escape_by_ref'));
                return @vsprintf($query, $this->prepare_args).$flag;
        }

        /**
         * Print SQL/DB error.
         *
         * @since 0.71
         * @global array $EZSQL_ERROR Stores error information of query and error string
         *
         * @param string $str The error to display
         * @return bool False if the showing of errors is disabled.
         */
        function print_error( $str = '' ) {
                global $EZSQL_ERROR;

                if ( !$str )
                        $str = mssql_get_last_message();
                $EZSQL_ERROR[] = array( 'query' => $this->last_query, 'error_str' => $str );

                if ( $this->suppress_errors )
                        return false;

                if ( $caller = $this->get_caller() )
                        $error_str = sprintf( /*WP_I18N_DB_QUERY_ERROR_FULL*/'WordPress database error %1$s for query %2$s made by %3$s'/*/WP_I18N_DB_QUERY_ERROR_FULL*/, $str, $this->last_query, $caller );
                else
                        $error_str = sprintf( /*WP_I18N_DB_QUERY_ERROR*/'WordPress database error %1$s for query %2$s'/*/WP_I18N_DB_QUERY_ERROR*/, $str, $this->last_query );

                if ( function_exists( 'error_log' )
                        && ( $log_file = @ini_get( 'error_log' ) )
                        && ( 'syslog' == $log_file || @is_writable( $log_file ) )
                        )
                        @error_log( $error_str );

                // Are we showing errors?
                if ( ! $this->show_errors )
                        return false;

                // If there is an error then take note of it
                if ( is_multisite() ) {
                        $msg = "WordPress database error: [$str]\n{$this->last_query}\n";
                        if ( defined( 'ERRORLOGFILE' ) )
                                error_log( $msg, 3, ERRORLOGFILE );
                        if ( defined( 'DIEONDBERROR' ) )
                                wp_die( $msg );
                } else {
                        $str   = htmlspecialchars( $str, ENT_QUOTES );
                        $query = htmlspecialchars( $this->last_query, ENT_QUOTES );

                        print "<div id='error'>
                        <p class='wpdberror'><strong>WordPress database error:</strong> [$str]<br />
                        <code>$query</code></p>
                        </div>";
                }
        }

        /**
         * Enables showing of database errors.
         *
         * This function should be used only to enable showing of errors.
         * wpdb::hide_errors() should be used instead for hiding of errors. However,
         * this function can be used to enable and disable showing of database
         * errors.
         *
         * @since 0.71
         * @see wpdb::hide_errors()
         *
         * @param bool $show Whether to show or hide errors
         * @return bool Old value for showing errors.
         */
        function show_errors( $show = true ) {
                $errors = $this->show_errors;
                $this->show_errors = $show;
                return $errors;
        }

        /**
         * Disables showing of database errors.
         *
         * By default database errors are not shown.
         *
         * @since 0.71
         * @see wpdb::show_errors()
         *
         * @return bool Whether showing of errors was active
         */
        function hide_errors() {
                $show = $this->show_errors;
                $this->show_errors = false;
                return $show;
        }

        /**
         * Whether to suppress database errors.
         *
         * By default database errors are suppressed, with a simple
         * call to this function they can be enabled.
         *
         * @since 2.5
         * @see wpdb::hide_errors()
         * @param bool $suppress Optional. New value. Defaults to true.
         * @return bool Old value
         */
        function suppress_errors( $suppress = true ) {
                $errors = $this->suppress_errors;
                $this->suppress_errors = (bool) $suppress;
                return $errors;
        }

        /**
         * Kill cached query results.
         *
         * @since 0.71
         * @return void
         */
        function flush() {
                $this->last_result = array();
                $this->col_info    = null;
                $this->last_query  = null;
        }

        function db_connect( $query = "SELECT" ) {
                global $db_list, $global_db_list;
                if ( ! is_array( $db_list ) )
                        return true;

                if ( $this->blogs != '' && preg_match("/(" . $this->blogs . "|" . $this->users . "|" . $this->usermeta . "|" . $this->site . "|" . $this->sitemeta . "|" . $this->sitecategories . ")/i",$query) ) {
                        $action = 'global';
                        $details = $global_db_list[ mt_rand( 0, count( $global_db_list ) -1 ) ];
                        $this->db_global = $details;
                } elseif ( preg_match("/^\\s*(alter table|create|insert|delete|update|replace) /i",$query) ) {
                        $action = 'write';
                        $details = $db_list[ 'write' ][ mt_rand( 0, count( $db_list[ 'write' ] ) -1 ) ];
                        $this->db_write = $details;
                } else {
                        $action = '';
                        $details = $db_list[ 'read' ][ mt_rand( 0, count( $db_list[ 'read' ] ) -1 ) ];
                        $this->db_read = $details;
                }

                $dbhname = "dbh" . $action;
                $this->$dbhname = @mssql_connect( $details[ 'db_host' ], $details[ 'db_user' ], $details[ 'db_password' ] );
                $this->is_mysql = false;

                if (!$this->$dbhname ) {
                        $this->bail( sprintf( /*WP_I18N_DB_CONN_ERROR*/"
<h1>Error establishing a database connection</h1>
<p>This either means that the username and password information in your <code>wp-config.php</code> file is incorrect or we can't contact the database server at <code>%s</code>. This could mean your host's database server is down.</p>
<ul>
        <li>Are you sure you have the correct username and password?</li>
        <li>Are you sure that you have typed the correct hostname?</li>
        <li>Are you sure that the database server is running?</li>
</ul>
<p>If you're unsure what these terms mean you should probably contact your host. If you still need help you can always visit the <a href='http://wordpress.org/support/'>WordPress Support Forums</a>.</p>
"/*/WP_I18N_DB_CONN_ERROR*/, $details['db_host'] ), 'db_connect_fail' );
                }

                mssql_min_error_severity(0);
                mssql_min_message_severity(17);
                @mssql_query('SET TEXTSIZE 2147483647');

                $this->select( $details[ 'db_name' ], $this->$dbhname );
        }

        /**
         * Perform a MySQL database query, using current database connection.
         *
         * More information can be found on the codex page.
         *
         * @since 0.71
         *
         * @param string $query Database query
         * @return int|false Number of rows affected/selected or false on error
         */
        function query( $query, $translate = true ) {
                if ( ! $this->ready )
                        return false;

                // some queries are made before the plugins have been loaded, and thus cannot be filtered with this method
                if ( function_exists( 'apply_filters' ) )
                        $query = apply_filters( 'query', $query );

                $return_val = 0;
                $this->flush();

                // Log how the function was called
                $this->func_call = "\$db->query(\"$query\")";

                // Keep track of the last query for debug..
                $this->last_query = $query;

                // use $this->dbh for read ops, and $this->dbhwrite for write ops
                // use $this->dbhglobal for gloal table ops
                unset( $dbh );
                if( defined( 'WP_USE_MULTIPLE_DB' ) && WP_USE_MULTIPLE_DB ) {
                        if( $this->blogs != '' && preg_match("/(" . $this->blogs . "|" . $this->users . "|" . $this->usermeta . "|" . $this->site . "|" . $this->sitemeta . "|" . $this->sitecategories . ")/i",$query) ) {
                                if( false == isset( $this->dbhglobal ) ) {
                                        $this->db_connect( $query );
                                }
                                $dbh =& $this->dbhglobal;
                                $this->last_db_used = "global";
                        } elseif ( preg_match("/^\\s*(alter table|create|insert|delete|update|replace) /i",$query) ) {
                                if( false == isset( $this->dbhwrite ) ) {
                                        $this->db_connect( $query );
                                }
                                $dbh =& $this->dbhwrite;
                                $this->last_db_used = "write";
                        } else {
                                $dbh =& $this->dbh;
                                $this->last_db_used = "read";
                        }
                } else {
                        $dbh =& $this->dbh;
                        $this->last_db_used = "other/read";
                }

                // Make Necessary Translations
                if ($translate === true) {
                        $query = $this->translate($query);
                        $this->previous_query = $query;
                }

                if ($this->preceeding_query !== false) {
                        if (is_array($this->preceeding_query)) {
                                foreach ($this->preceeding_query as $p_query) {
                                        @mssql_query($sub_query, $dbh);
                                }
                        } else {
                                @mssql_query($this->preceeding_query, $dbh);
                        }
                        $this->preceeding_query = false;
                }

                // Check if array of queries (this happens for INSERTS with multiple VALUES blocks)
                if (is_array($query)) {
                        foreach ($query as $sub_query) {
                                $this->_pre_query();
                                $this->result = @mssql_query($sub_query, $dbh);
                                $return_val = $this->_post_query($sub_query, $dbh);
                        }
                } else {
                        $this->_pre_query();
                        $this->result = @mssql_query($query, $dbh);
                        $return_val = $this->_post_query($query, $dbh);
                }

                if ($this->following_query !== false) {
                        if (is_array($this->following_query)) {
                                foreach ($this->following_query as $f_query) {
                                        @mssql_query($f_query, $dbh);
                                }
                        } else {
                                @mssql_query($this->following_query, $dbh);
                        }
                        $this->following_query = false;
                }

                if ( function_exists( 'apply_filters' ) )
                    apply_filters( 'after_query', $query );

                return $return_val;
        }

        function _pre_query() {
                if ( defined('SAVEQUERIES') && SAVEQUERIES ) {
                        $this->timer_start();
                }
        }

        function _post_query($query, $dbh) {
                ++$this->num_queries;
                // If there is an error then take note of it..
                if ( $this->result == FALSE && $this->last_error = mssql_get_last_message() ) {
                        $this->log_query($this->last_error);
                        //var_dump($query);
                        //var_dump($this->translation_changes);
                        $this->print_error();
                        return false;
                }

                if ( defined('SAVEQUERIES') && SAVEQUERIES ) {
                        $this->queries[] = array( $query, $this->timer_stop(), $this->get_caller() );
                }

                if ( preg_match("/^\\s*(insert|delete|update|replace) /i",$query) ) {

                        $this->rows_affected = mssql_rows_affected($dbh);
                        // Take note of the insert_id
                        if ( preg_match("/^\\s*(insert|replace) /i",$query) ) {
                                $result = @mssql_fetch_object(@mssql_query("SELECT SCOPE_IDENTITY() AS ID"));
                                $this->insert_id = $result->ID;
                        }

                        $return_val = $this->rows_affected;
                } else {

                        $i = 0;
                        while ($i < @mssql_num_fields($this->result)) {
                                $field = @mssql_fetch_field($this->result, $i);
                                $new_field = new stdClass();
                                $new_field->name = $field->name;
                                $new_field->table = $field->column_source;
                                $new_field->def = null;
                                $new_field->max_length = $field->max_length;
                                $new_field->not_null = true;
                                $new_field->primary_key = null;
                                $new_field->unique_key = null;
                                $new_field->multiple_key = null;
                                $new_field->numeric = $field->numeric;
                                $new_field->blob = null;
                                $new_field->type = $field->type;
                                if(isset($field->unsigned)) {
                                        $new_field->unsigned = $field->unsigned;
                                } else {
                                        $new_field->unsigned = null;
                                }
                                $new_field->zerofill = null;
                                $this->col_info[$i] = $new_field;
                                $i++;
                        }
                        $num_rows = 0;
                        while ( $row = @mssql_fetch_object($this->result) ) {
                                $this->last_result[$num_rows] = $row;
                                $num_rows++;
                        }
                        $this->last_result = $this->fix_results($this->last_result);
                        // perform limit
                        if (!empty($this->limit)) {
                                $this->last_result = array_slice($this->last_result, $this->limit['from'], $this->limit['to']);
                                $num_rows = count($this->last_result);
                        }

                        @mssql_free_result($this->result);

                        // Log number of rows the query returned
                        $this->num_rows = $num_rows;

                        // Return number of rows selected
                        $return_val = $this->num_rows;
                }

                $this->log_query();
                return $return_val;
        }

        function log_query($error = null)
        {
            if (!defined('SAVEQUERIES') || !SAVEQUERIES) {
                return; //bail
            }

            if (!defined('QUERY_LOG')) {
                $log = ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR . 'queries.log';
            } else {
                $log = QUERY_LOG;
            }

            if (!empty($this->queries)) {
                $last_query = end($this->queries);
                if (preg_match( "/^\\s*(insert|delete|update|replace|alter) /i", $last_query[0])) {
                    $result = serialize($this->rows_affected);
                } else {
                    $result = serialize($this->last_result);
                }
                                if (is_array($error)) {
                                        $error = serialize($error);
                                }
                                $q = str_replace("\n", ' ', $last_query[0]);
                file_put_contents($log, $q . '|~|' . $result . '|~|' . $error . "\n", FILE_APPEND);
            }
        }

        /**
         * Insert a row into a table.
         *
         * <code>
         * wpdb::insert( 'table', array( 'column' => 'foo', 'field' => 'bar' ) )
         * wpdb::insert( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( '%s', '%d' ) )
         * </code>
         *
         * @since 2.5.0
         * @see wpdb::prepare()
         * @see wpdb::$field_types
         * @see wp_set_wpdb_vars()
         *
         * @param string $table table name
         * @param array $data Data to insert (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
         * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string, that format will be used for all of the values in $data.
         *      A format is one of '%d', '%s' (decimal number, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
         * @return int|false The number of rows inserted, or false on error.
         */
        function insert( $table, $data, $format = null ) {
                return $this->_insert_replace_helper( $table, $data, $format, 'INSERT' );
        }

        /**
         * Replace a row into a table.
         *
         * <code>
         * wpdb::replace( 'table', array( 'column' => 'foo', 'field' => 'bar' ) )
         * wpdb::replace( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( '%s', '%d' ) )
         * </code>
         *
         * @since 3.0.0
         * @see wpdb::prepare()
         * @see wpdb::$field_types
         * @see wp_set_wpdb_vars()
         *
         * @param string $table table name
         * @param array $data Data to insert (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
         * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string, that format will be used for all of the values in $data.
         *      A format is one of '%d', '%s' (decimal number, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
         * @return int|false The number of rows affected, or false on error.
         */
        function replace( $table, $data, $format = null ) {
                return $this->_insert_replace_helper( $table, $data, $format, 'REPLACE' );
        }

        /**
         * Helper function for insert and replace.
         *
         * Runs an insert or replace query based on $type argument.
         *
         * @access private
         * @since 3.0.0
         * @see wpdb::prepare()
         * @see wpdb::$field_types
         * @see wp_set_wpdb_vars()
         *
         * @param string $table table name
         * @param array $data Data to insert (in column => value pairs).  Both $data columns and $data values should be "raw" (neither should be SQL escaped).
         * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string, that format will be used for all of the values in $data.
         *      A format is one of '%d', '%s' (decimal number, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
         * @return int|false The number of rows affected, or false on error.
         */
        function _insert_replace_helper( $table, $data, $format = null, $type = 'INSERT' ) {
                if ( ! in_array( strtoupper( $type ), array( 'REPLACE', 'INSERT' ) ) )
                        return false;
                $formats = $format = (array) $format;
                $fields = array_keys( $data );
                $formatted_fields = array();
                foreach ( $fields as $field ) {
                        if ( !empty( $format ) )
                                $form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
                        elseif ( isset( $this->field_types[$field] ) )
                                $form = $this->field_types[$field];
                        else
                                $form = '%s';
                        $formatted_fields[] = $form;
                }
                $sql = "{$type} INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES ('" . implode( "','", $formatted_fields ) . "')";
                return $this->query( $this->prepare( $sql, $data ) );
        }

        /**
         * Update a row in the table
         *
         * <code>
         * wpdb::update( 'table', array( 'column' => 'foo', 'field' => 'bar' ), array( 'ID' => 1 ) )
         * wpdb::update( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( 'ID' => 1 ), array( '%s', '%d' ), array( '%d' ) )
         * </code>
         *
         * @since 2.5.0
         * @see wpdb::prepare()
         * @see wpdb::$field_types
         * @see wp_set_wpdb_vars()
         *
         * @param string $table table name
         * @param array $data Data to update (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
         * @param array $where A named array of WHERE clauses (in column => value pairs). Multiple clauses will be joined with ANDs. Both $where columns and $where values should be "raw".
         * @param array|string $format Optional. An array of formats to be mapped to each of the values in $data. If string, that format will be used for all of the values in $data.
         *      A format is one of '%d', '%s' (decimal number, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
         * @param array|string $format_where Optional. An array of formats to be mapped to each of the values in $where. If string, that format will be used for all of the items in $where.  A format is one of '%d', '%s' (decimal number, string).  If omitted, all values in $where will be treated as strings.
         * @return int|false The number of rows updated, or false on error.
         */
        function update( $table, $data, $where, $format = null, $where_format = null ) {
                if ( ! is_array( $data ) || ! is_array( $where ) )
                        return false;

                $formats = $format = (array) $format;
                $bits = $wheres = array();
                foreach ( (array) array_keys( $data ) as $field ) {
                        if ( !empty( $format ) )
                                $form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
                        elseif ( isset($this->field_types[$field]) )
                                $form = $this->field_types[$field];
                        else
                                $form = '%s';
                        $bits[] = "`$field` = {$form}";
                }

                $where_formats = $where_format = (array) $where_format;
                foreach ( (array) array_keys( $where ) as $field ) {
                        if ( !empty( $where_format ) )
                                $form = ( $form = array_shift( $where_formats ) ) ? $form : $where_format[0];
                        elseif ( isset( $this->field_types[$field] ) )
                                $form = $this->field_types[$field];
                        else
                                $form = '%s';
                        $wheres[] = "`$field` = {$form}";
                }

                $sql = "UPDATE `$table` SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres );
                return $this->query( $this->prepare( $sql, array_merge( array_values( $data ), array_values( $where ) ) ) );
        }

        /**
         * Retrieve one variable from the database.
         *
         * Executes a SQL query and returns the value from the SQL result.
         * If the SQL result contains more than one column and/or more than one row, this function returns the value in the column and row specified.
         * If $query is null, this function returns the value in the specified column and row from the previous SQL result.
         *
         * @since 0.71
         *
         * @param string|null $query Optional. SQL query. Defaults to null, use the result from the previous query.
         * @param int $x Optional. Column of value to return.  Indexed from 0.
         * @param int $y Optional. Row of value to return.  Indexed from 0.
         * @return string|null Database query result (as string), or null on failure
         */
        function get_var( $query = null, $x = 0, $y = 0 ) {
                $this->func_call = "\$db->get_var(\"$query\", $x, $y)";
                if ( $query )
                        $this->query( $query );

                // Extract var out of cached results based x,y vals
                if ( !empty( $this->last_result[$y] ) ) {
                        $values = array_values( get_object_vars( $this->last_result[$y] ) );
                }

                // If there is a value return it else return null
                return ( isset( $values[$x] ) && $values[$x] !== '' ) ? $values[$x] : null;
        }

        /**
         * Retrieve one row from the database.
         *
         * Executes a SQL query and returns the row from the SQL result.
         *
         * @since 0.71
         *
         * @param string|null $query SQL query.
         * @param string $output Optional. one of ARRAY_A | ARRAY_N | OBJECT constants. Return an associative array (column => value, ...),
         *      a numerically indexed array (0 => value, ...) or an object ( ->column = value ), respectively.
         * @param int $y Optional. Row to return. Indexed from 0.
         * @return mixed Database query result in format specifed by $output or null on failure
         */
        function get_row( $query = null, $output = OBJECT, $y = 0 ) {
                $this->func_call = "\$db->get_row(\"$query\",$output,$y)";
                if ( $query )
                        $this->query( $query );
                else
                        return null;

                if ( !isset( $this->last_result[$y] ) )
                        return null;

                if ( $output == OBJECT ) {
                        return $this->last_result[$y] ? $this->last_result[$y] : null;
                } elseif ( $output == ARRAY_A ) {
                        return $this->last_result[$y] ? get_object_vars( $this->last_result[$y] ) : null;
                } elseif ( $output == ARRAY_N ) {
                        return $this->last_result[$y] ? array_values( get_object_vars( $this->last_result[$y] ) ) : null;
                } else {
                        $this->print_error(/*WP_I18N_DB_GETROW_ERROR*/" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N"/*/WP_I18N_DB_GETROW_ERROR*/);
                }
        }

        /**
         * Retrieve one column from the database.
         *
         * Executes a SQL query and returns the column from the SQL result.
         * If the SQL result contains more than one column, this function returns the column specified.
         * If $query is null, this function returns the specified column from the previous SQL result.
         *
         * @since 0.71
         *
         * @param string|null $query Optional. SQL query. Defaults to previous query.
         * @param int $x Optional. Column to return. Indexed from 0.
         * @return array Database query result. Array indexed from 0 by SQL result row number.
         */
        function get_col( $query = null , $x = 0 ) {
                if ( $query )
                        $this->query( $query );

                $new_array = array();
                // Extract the column values
                for ( $i = 0, $j = count( $this->last_result ); $i < $j; $i++ ) {
                        $new_array[$i] = $this->get_var( null, $x, $i );
                }
                return $new_array;
        }

        /**
         * Retrieve an entire SQL result set from the database (i.e., many rows)
         *
         * Executes a SQL query and returns the entire SQL result.
         *
         * @since 0.71
         *
         * @param string $query SQL query.
         * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants. With one of the first three, return an array of rows indexed from 0 by SQL result row number.
         *      Each row is an associative array (column => value, ...), a numerically indexed array (0 => value, ...), or an object. ( ->column = value ), respectively.
         *      With OBJECT_K, return an associative array of row objects keyed by the value of each row's first column's value.  Duplicate keys are discarded.
         * @return mixed Database query results
         */
        function get_results( $query = null, $output = OBJECT ) {
                $this->func_call = "\$db->get_results(\"$query\", $output)";

                if ( $query )
                        $this->query( $query );
                else
                        return null;

                $new_array = array();
                if ( $output == OBJECT ) {
                        // Return an integer-keyed array of row objects
                        return $this->last_result;
                } elseif ( $output == OBJECT_K ) {
                        // Return an array of row objects with keys from column 1
                        // (Duplicates are discarded)
                        foreach ( $this->last_result as $row ) {
                                $key = array_shift( get_object_vars( $row ) );
                                if ( ! isset( $new_array[ $key ] ) )
                                        $new_array[ $key ] = $row;
                        }
                        return $new_array;
                } elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
                        // Return an integer-keyed array of...
                        if ( $this->last_result ) {
                                foreach( (array) $this->last_result as $row ) {
                                        if ( $output == ARRAY_N ) {
                                                // ...integer-keyed row arrays
                                                $new_array[] = array_values( get_object_vars( $row ) );
                                        } else {
                                                // ...column name-keyed row arrays
                                                $new_array[] = get_object_vars( $row );
                                        }
                                }
                        }
                        return $new_array;
                }
                return null;
        }

        /**
         * Retrieve column metadata from the last query.
         *
         * @since 0.71
         *
         * @param string $info_type Optional. Type one of name, table, def, max_length, not_null, primary_key, multiple_key, unique_key, numeric, blob, type, unsigned, zerofill
         * @param int $col_offset Optional. 0: col name. 1: which table the col's in. 2: col's max length. 3: if the col is numeric. 4: col's type
         * @return mixed Column Results
         */
        function get_col_info( $info_type = 'name', $col_offset = -1 ) {
                if ( $this->col_info ) {
                        if ( $col_offset == -1 ) {
                                $i = 0;
                                $new_array = array();
                                foreach( (array) $this->col_info as $col ) {
                                        $new_array[$i] = $col->{$info_type};
                                        $i++;
                                }
                                return $new_array;
                        } else {
                                return $this->col_info[$col_offset]->{$info_type};
                        }
                }
        }

        /**
         * Starts the timer, for debugging purposes.
         *
         * @since 1.5.0
         *
         * @return true
         */
        function timer_start() {
                $mtime            = explode( ' ', microtime() );
                $this->time_start = $mtime[1] + $mtime[0];
                return true;
        }

        /**
         * Stops the debugging timer.
         *
         * @since 1.5.0
         *
         * @return int Total time spent on the query, in milliseconds
         */
        function timer_stop() {
                $mtime      = explode( ' ', microtime() );
                $time_end   = $mtime[1] + $mtime[0];
                $time_total = $time_end - $this->time_start;
                return $time_total;
        }

        /**
         * Wraps errors in a nice header and footer and dies.
         *
         * Will not die if wpdb::$show_errors is true
         *
         * @since 1.5.0
         *
         * @param string $message The Error message
         * @param string $error_code Optional. A Computer readable string to identify the error.
         * @return false|void
         */
        function bail( $message, $error_code = '500' ) {
                if ( !$this->show_errors ) {
                        if ( class_exists( 'WP_Error' ) )
                                $this->error = new WP_Error($error_code, $message);
                        else
                                $this->error = $message;
                        return false;
                }
                wp_die($message);
        }

        /**
         * Whether MySQL database is at least the required minimum version.
         *
         * @since 2.5.0
         * @uses $wp_version
         * @uses $required_mysql_version
         *
         * @return WP_Error
         */
        function check_database_version() {
                global $wp_version, $required_mysql_version;
                // Make sure the server has the required MySQL version
                //if ( version_compare($this->db_version(), $required_mysql_version, '<') )
                        //return new WP_Error('database_version', sprintf( __( '<strong>ERROR</strong>: WordPress %1$s requires MySQL %2$s or higher' ), $wp_version, $required_mysql_version ));
        }

        /**
         * Whether the database supports collation.
         *
         * Called when WordPress is generating the table scheme.
         *
         * @since 2.5.0
         *
         * @return bool True if collation is supported, false if version does not
         */
        function supports_collation() {
                return $this->has_cap( 'collation' );
        }

        /**
         * Determine if a database supports a particular feature
         *
         * @since 2.7
         * @see   wpdb::db_version()
         *
         * @param string $db_cap the feature
         * @return bool
         */
        function has_cap( $db_cap ) {
                $version = $this->db_version();

                switch ( strtolower( $db_cap ) ) {
                        case 'collation' :    // @since 2.5.0
                        case 'group_concat' : // @since 2.7
                        case 'subqueries' :   // @since 2.7
                                return version_compare( $version, '4.1', '>=' );
                };

                return false;
        }

        /**
         * Retrieve the name of the function that called wpdb.
         *
         * Searches up the list of functions until it reaches
         * the one that would most logically had called this method.
         *
         * @since 2.5.0
         *
         * @return string The name of the calling function
         */
        function get_caller() {
                $trace  = array_reverse( debug_backtrace() );
                $caller = array();

                foreach ( $trace as $call ) {
                        if ( isset( $call['class'] ) && __CLASS__ == $call['class'] )
                                continue; // Filter out wpdb calls.
                        $caller[] = isset( $call['class'] ) ? "{$call['class']}->{$call['function']}" : $call['function'];
                }

                return join( ', ', $caller );
        }

        /**
         * The database version number.
         *
         * @return false|string false on failure, version number on success
         */
        function db_version() {
                return '5.0';
        }
}

/**
 * Fields mapping
 *
 * Some column types from MySQL
 * don't have an exact equivalent for SQL Server
 * That is why we need to know what column types they were
 * originally to make the translations.
 *
 * @category MSSQL
 * @package MySQL_Translations
 * @author A.Garcia & A.Gentile
 * */
class Fields_map
{
    var $fields_map = array();
    var $filepath = '';

    /**
     * Set filepath
     *
     * PHP5 style constructor for compatibility with PHP5.
     *
     * @since 2.7.1
     */
    function __construct() {
        $folder = basename(WPMU_PLUGIN_DIR);
        $this->filepath = trim(str_replace($folder . '/wp-db-abstraction/translations/sqlsrv', '', strtr(dirname(__FILE__), '\\', '/')), '/') . '/fields_map.parsed_types.php';
    }

    /**
     * Get array of fields by type from fields_map property
     *
     * @since 2.8
     * @param $type
     * @param $table
     *
     * @return array
     */
    function by_type($type, $table = null) {
        $ret = array();
        foreach ($this->fields_map as $tables => $fields) {
            if ( is_array($fields) ) {
                foreach ($fields as $field_name => $field_meta) {
                    if ( $field_meta['type'] == $type ) {
                        if (is_null($table) || $tables == $table) {
                            $ret[] = $field_name;
                        }
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * Get array of tables from fields_map property
     *
     * @since 2.8
     *
     * @return array
     */
    function get_tables() {
        $ret = array();
        foreach ($this->fields_map as $tables => $fields) {
            $ret[] = $tables;
        }
        return $ret;
    }

    /**
     * Given a query find the column types
     *
     * @since 2.8
     * @param $qry
     *
     * @return array
     */
    function extract_column_types($qry) {
        //table name
        $matches = array();
        if (preg_match('/[CREATE|ALTER] TABLE (.*) \(/i',$qry,$matches)){
            $table_name = $matches[1];
        } else {
            $table_name = '';
        }


        $fields_and_indexes = substr($qry,strpos($qry,'(')+1,strrpos($qry,')')-(strpos($qry,'(')+1));
        $just_fields = trim(substr($fields_and_indexes,0,$this->index_pos($fields_and_indexes)));

        $field_lines = explode(',',$just_fields);
        $field_types = array();
        foreach ($field_lines as $field_line) {
            if (!empty($field_line)){
                $field_line = trim($field_line);
            $words = explode(' ',$field_line,3);
            $first_word = $words[0];
            $field_type = $this->type_translations($words[1]);
            if ($field_type !== false) {
                $field_types[$first_word] = array('type'=>$field_type);
            }
            }
        }

        //get primary key
        $just_indexes = trim(substr($fields_and_indexes,$this->index_pos($fields_and_indexes)));
        $matches = array();
        $has_primary_key = preg_match('/PRIMARY KEY *\((.*?)[,|\)]/i',$just_indexes,$matches);
        if ($has_primary_key) {
            $primary_key = trim($matches[1]);
            $field_types[$primary_key] = array('type' => 'primary_id');
        }
        ksort($field_types);

        return array($table_name => $field_types);
    }

    /**
     * According to the column types in MySQL
     *
     * @since 2.8
     * @param $field_type
     *
     * @return array
     */
    function type_translations($field_type) {
        //false means not translate this field.
        $translations = array(
            array('pattern' => '/varchar(.*)/', 'trans' => 'nvarchar'),
            array('pattern' => '/.*text.*/',    'trans' => 'nvarchar'),
            array('pattern' => '/.*datetime.*/','trans' => 'date'),
            array('pattern' => '/int(.*)/',     'trans' => 'int'),
        );

        $res = '';
        while (($res === '') && ($trans = array_shift($translations))) {
            if (preg_match($trans['pattern'],$field_type)) {
                $res = $trans['trans'];
            }
        }

        if ($res === '') {
            $res = $field_type;
        }
        return $res;
    }


    /**
     * Get array of tables from fields_map property
     *
     * @since 2.8
     * @param $fields_and_indexes
     *
     * @return array
     */
    function index_pos($fields_and_indexes) {
        $reserved_words = array('PRIMARY KEY', 'UNIQUE');
        $res = false;
        while (($res === false) && ($reserved_word = array_shift($reserved_words))){
            $res = stripos($fields_and_indexes,$reserved_word);
        }

        return $res;
    }

    /**
     * Update fields may given a CREATE | ALTER query
     *
     * @since 2.8
     * @param $qry
     *
     * @return array
     */
    function update_for($qry) {
        $this->read();
        $this->fields_map = array_merge($this->fields_map, $this->extract_column_types($qry));
        file_put_contents($this->filepath, '<?php return ' . var_export($this->fields_map, true) . "\n ?>");
        return $this->fields_map;
    }

    /**
     * Get the fields_map from memory or from the file.
     *
     * @since 2.8
     *
     * @return array
     */
    function read() {
        if (empty($this->fields_map)) {
            if (file_exists($this->filepath)) {
                $this->fields_map = require($this->filepath);
            } else {
                $this->fields_map = array();
            }
        }
        return $this->fields_map;
    }

}

/**
 * SQL Dialect Translations
 *
 * @category MSSQL
 * @package MySQL_Translations
 * @author A.Garcia & A.Gentile
 * */
class SQL_Translations
{
    /**
     * Field Mapping
     *
     * @since 2.7.1
     * @access private
     * @var array
     */
    var $fields_map = null;

    /**
     * Was this query prepared?
     *
     * @since 2.7.1
     * @access private
     * @var bool
     */
    var $prepared = false;

    /**
     * Update query?
     *
     * @since 2.7.1
     * @access private
     * @var bool
     */
    var $update_query = false;

    /**
     * Select query?
     *
     * @since 2.7.1
     * @access private
     * @var bool
     */
    var $select_query = false;

    /**
     * Create query?
     *
     * @since 2.7.1
     * @access private
     * @var bool
     */
    var $create_query = false;

    /**
     * Alter query?
     *
     * @since 2.7.1
     * @access private
     * @var bool
     */
    var $alter_query = false;

    /**
     * Insert query?
     *
     * @since 2.7.1
     * @access private
     * @var bool
     */
    var $insert_query = false;

    /**
     * Delete query?
     *
     * @since 2.7.1
     * @access private
     * @var bool
     */
    var $delete_query = false;

    /**
     * Prepare arguments
     *
     * @since 2.7.1
     * @access private
     * @var array
     */
    var $prepare_args = array();

    /**
     * Update Data
     *
     * @since 2.7.1
     * @access private
     * @var array
     */
    var $update_data = array();

    /**
     * Limit Info
     *
     * @since 2.7.1
     * @access private
     * @var array
     */
    var $limit = array();

    /**
     * Update Data
     *
     * @since 2.7.1
     * @access private
     * @var array
     */
    var $translation_changes = array();

    /**
     * Azure
     * Are we dealing with a SQL Azure DB?
     *
     * @since 2.7.1
     * @access public
     * @var bool
     */
    var $azure = false;

    /**
     * Preceeding query
     * Sometimes we need to issue a query
     * before the original query
     *
     * @since 2.8.5
     * @access public
     * @var mixed
     */
    var $preceeding_query = false;

    /**
     * Following query
     * Sometimes we need to issue a query
     * right after the original query
     *
     * @since 2.8.5
     * @access public
     * @var mixed
     */
    var $following_query = false;

    /**
     * Should we verify update/insert queries?
     *
     * @since 2.8.5
     * @access public
     * @var mixed
     */
    var $verify = false;

        /**
     * WordPress table prefix
     *
     * You can set this to have multiple WordPress installations
     * in a single database. The second reason is for possible
     * security precautions.
     *
     * @since 0.71
     * @access private
     * @var string
     */
    var $prefix = '';

    /**
     * Assign fields_map as a new Fields_map object
     *
     * PHP5 style constructor for compatibility with PHP5.
     *
     * @since 2.7.1
     */
    function __construct()
    {
        $this->fields_map = new Fields_map();
    }

    /**
     * MySQL > MSSQL Query Translation
     * Processes smaller translation sub-functions
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate($query)
    {
        $this->limit = array();

        $this->set_query_type(trim($query));

        $this->preceeding_query = false;
        $this->following_query = false;

        // Was this query prepared?
        if ( strripos($query, '--PREPARE') !== FALSE ) {
            $query = str_replace('--PREPARE', '', $query);
            $this->prepared = TRUE;
        } else {
            $this->prepared = FALSE;
        }

        // Do we have serialized arguments?
        if ( strripos($query, '--SERIALIZED') !== FALSE ) {
            $query = str_replace('--SERIALIZED', '', $query);
            if ($this->insert_query) {
                $query = $this->on_duplicate_key($query);
            }
            $query = $this->translate_general($query);
            return $query;
        }

        $query = trim($query);

        $sub_funcs = array(
            'translate_general',
            'translate_date_add',
            'translate_if_stmt',
            'translate_sqlcalcrows',
            'translate_limit',
            'translate_now_datetime',
            'translate_distinct_orderby',
            'translate_replace_casting',
            'translate_sort_casting',
            'translate_column_type',
            'translate_remove_groupby',
            'translate_insert_nulltime',
            'translate_incompat_data_type',
            'translate_create_queries',
            'translate_specific',
        );

        // Perform translations and record query changes.
        $this->translation_changes = array();
        foreach ( $sub_funcs as $sub_func ) {
            $old_query = $query;
            $query = $this->$sub_func($query);
            if ( $old_query !== $query ) {
                $this->translation_changes[] = $sub_func;
                $this->translation_changes[] = $query;
                $this->translation_changes[] = $old_query;
            }
        }
        if ( $this->insert_query ) {
            $query = $this->on_duplicate_key($query);
            $query = $this->split_insert_values($query);
        }
        if ( $this->prepared && $this->insert_query && $this->verify ) {
            if ( is_array($query) ) {
                foreach ($query as $k => $v) {
                    $query[$k] = $this->verify_insert($v);
                }
            } else {
                $query = $this->verify_insert($query);
            }
        }

        if ( $this->update_query && $this->verify ) {
            $query = $this->verify_update($query);
        }

        return $query;
    }

    function set_query_type($query)
    {
        $this->insert_query = false;
        $this->delete_query = false;
        $this->update_query = false;
        $this->select_query = false;
        $this->alter_query  = false;
        $this->create_query = false;

        if ( stripos($query, 'INSERT') === 0 ) {
            $this->insert_query = true;
        } else if ( stripos($query, 'SELECT') === 0 ) {
            $this->select_query = true;
        } else if ( stripos($query, 'DELETE') === 0 ) {
            $this->delete_query = true;
        } else if ( stripos($query, 'UPDATE') === 0 ) {
            $this->update_query = true;
        } else if ( stripos($query, 'ALTER') === 0 ) {
            $this->alter_query = true;
        } else if ( stripos($query, 'CREATE') === 0 ) {
            $this->create_query = true;
        }
    }

    /**
     * More generalized information gathering queries
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_general($query)
    {
        // SERVER VERSION
        if ( stripos($query, 'SELECT VERSION()' ) === 0) {
            $query = substr_replace($query, 'SELECT @@VERSION', 0, 16);
        }
        // SQL_MODE NO EQUIV
        if ( stripos($query, "SHOW VARIABLES LIKE 'sql_mode'" ) === 0) {
            $query = '';
        }
        // LAST INSERT ID
        if ( stripos($query, 'LAST_INSERT_ID()') > 0 ) {
            $start_pos = stripos($query, 'LAST_INSERT_ID()');
            $query = substr_replace($query, '@@IDENTITY', $start_pos, 16);
        }
        // SHOW TABLES
        if ( strtolower($query) === 'show tables;' ) {
            $query = str_ireplace('show tables',"select name from SYSOBJECTS where TYPE = 'U' order by NAME",$query);
        }
        if ( stripos($query, 'show tables like ') === 0 ) {
            $end_pos = strlen($query);
            $param = substr($query, 17, $end_pos - 17);
            $query = 'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE ' . $param;
        }
        // SET NAMES doesn't exist in T-SQL
        if ( stristr($query, "set names 'utf8'") !== FALSE ) {
            $query = "";
        }
        // SHOW COLUMNS
        if ( stripos($query, 'SHOW COLUMNS FROM ') === 0 ) {
            $end_pos = strlen($query);
            $param = substr($query, 18, $end_pos - 18);
            $param = "'". trim($param, "'") . "'";
            $query = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ' . $param;
        }

        // SHOW INDEXES - issue with sql azure trying to fix....sys.sysindexes is coming back as invalid onject name
        if ( stripos($query, 'SHOW INDEXES FROM ') === 0 ) {
            return $query;
            $table = substr($query, 18);
            $query = "SELECT sys.sysindexes.name AS IndexName
                      FROM sysobjects
                       JOIN sys.key_constraints ON parent_object_id = sys.sysobjects.id
                       JOIN sys.sysindexes ON sys.sysindexes.id = sys.sysobjects.id and sys.key_constraints.unique_index_id = sys.sysindexes.indid
                       JOIN sys.index_columns ON sys.index_columns.object_id = sys.sysindexes.id  and sys.index_columns.index_id = sys.sysindexes.indid
                       JOIN sys.syscolumns ON sys.syscolumns.id = sys.sysindexes.id AND sys.index_columns.column_id = sys.syscolumns.colid
                      WHERE sys.sysobjects.type = 'u'
                       AND sys.sysobjects.name = '{$table}'";
        }

        // USE INDEX
        if ( stripos($query, 'USE INDEX (') !== FALSE) {
            $start_pos = stripos($query, 'USE INDEX (');
            $end_pos = $this->get_matching_paren($query, $start_pos + 11);
            $params = substr($query, $start_pos + 11, $end_pos - ($start_pos + 11));
            $params = explode(',', $params);
            foreach ($params as $k => $v) {
                $params[$k] = trim($v);
                foreach ($this->fields_map->read() as $table => $fields) {
                    if ( is_array($fields) ) {
                        foreach ($fields as $field_name => $field_meta) {
                            if ( $field_name == $params[$k] ) {
                                $params[$k] = $table . '_' . $params[$k];
                            }
                        }
                    }
                }
            }
            $params = implode(',', $params);
            $query = substr_replace($query, 'WITH (INDEX(' . $params . '))', $start_pos, ($end_pos + 1) - $start_pos);
        }

        // DESCRIBE - this is pretty darn close to mysql equiv, however it will need to have a flag to modify the result set
        // this and SHOW INDEX FROM are used in WP upgrading. The problem is that WP will see the different data types and try
        // to alter the table thinking an upgrade is necessary. So the result set from this query needs to be modified using
        // the field_mapping to revert column types back to their mysql equiv to fool WP.
        if ( stripos($query, 'DESCRIBE ') === 0 ) {
            return $query;
            $table = substr($query, 9);
            $query = $this->describe($table);
        }

        // DROP TABLES
        if ( stripos($query, 'DROP TABLE IF EXISTS ') === 0 ) {
            $table = substr($query, 21, strlen($query) - 21);
            $query = 'DROP TABLE ' . $table;
        } elseif ( stripos($query, 'DROP TABLE ') === 0 ) {
            $table = substr($query, 11, strlen($query) - 11);
            $query = 'DROP TABLE ' . $table;
        }

        // REGEXP - not supported in TSQL
        if ( stripos($query, 'REGEXP') > 0 ) {
                if ( $this->delete_query && stripos($query, '^rss_[0-9a-f]{32}(_ts)?$') > 0 ) {
                        $start_pos = stripos($query, 'REGEXP');
                        $query = substr_replace($query, "LIKE 'rss_'", $start_pos);
                }
        }

        // LEN not LENGTH
        $query = str_replace('LENGTH(', 'LEN(', $query);
        $query = str_replace('LENGTH (', 'LEN (', $query);

        // TICKS
        $query = str_replace('`', '', $query);

        // avoiding some nested as Computed issues
        if (stristr($query, 'SELECT COUNT(DISTINCT(' . $this->prefix . 'users.ID))') !== FALSE) {
            $query = str_ireplace(
                'SELECT COUNT(DISTINCT(' . $this->prefix . 'users.ID))',
                'SELECT COUNT(DISTINCT(' . $this->prefix . 'users.ID)) as Computed', $query);
        }

        if (!preg_match('/CHAR_LENGTH\((.*?)\) as/i', $query)) {
            $query = preg_replace('/CHAR_LENGTH\((.*?)\)/i', 'LEN(\1)', $query);
        }

        if (!preg_match('/LENGTH\((.*?)\) as/i', $query)) {
            $query = preg_replace('/LENGTH\((.*?)\)/i', 'LEN(\1)', $query);
        }

        // Computed
        // This is done as the SQLSRV driver doesn't seem to set a property value for computed
        // selected columns, thus WP doesn't have anything to work with.
        if (!preg_match('/COUNT\((.*?)\) as/i', $query)) {
            $query = preg_replace('/COUNT\((.*?)\)/i', 'COUNT(\1) as Computed', $query);
        }

        // Replace RAND() with NEWID() since RAND() generates the same result for the same query
        if (preg_match('/(ORDER BY RAND\(\))(.*)$/i', $query)) {
            $query = preg_replace('/(ORDER BY RAND\(\))(.*)$/i', 'ORDER BY NEWID()\2', $query);
        }

        // Remove uncessary ORDER BY clauses as ORDER BY fields needs to
        // be contained in either an aggregate function or the GROUP BY clause.
        // something that isn't enforced in mysql
        if (preg_match('/SELECT COUNT\((.*?)\) as Computed FROM/i', $query)) {
            $order_pos = stripos($query, 'ORDER BY');
            if ($order_pos !== false) {
                $query = substr($query, 0, $order_pos);
            }
        }

        // Turn on IDENTITY_INSERT for Importing inserts or category/tag adds that are
        // trying to explicitly set and IDENTITY column
        if ($this->insert_query) {
            $tables = array(
                $this->get_blog_prefix() . 'posts' => 'id',
                $this->get_blog_prefix() . 'terms' => 'term_id',
            );
            foreach ($tables as $table => $pid) {
                if (stristr($query, 'INTO ' . $table) !== FALSE) {
                    $strlen = strlen($table);
                    $start_pos = stripos($query, $table) + $strlen;
                    $start_pos = stripos($query, '(', $start_pos);
                    $end_pos = $this->get_matching_paren($query, $start_pos + 1);
                    $params = substr($query, $start_pos + 1, $end_pos - ($start_pos + 1));
                    $params = explode(',', $params);
                    $found = false;
                    foreach ($params as $k => $v) {
                        if (strtolower($v) === $pid) {
                            $found = true;
                        }
                    }

                    if ($found) {
                        $this->preceeding_query = "SET IDENTITY_INSERT $table ON";
                        $this->following_query = "SET IDENTITY_INSERT $table OFF";
                    }
                }
            }
        }

        // UPDATE queries trying to change an IDENTITY column this happens
        // for cat/tag adds (WPMU) e.g. UPDATE wp_1_terms SET term_id = 5 WHERE term_id = 3330
        if ($this->update_query) {
            $tables = array(
                $this->prefix . 'terms' => 'term_id',
            );
            foreach ($tables as $table => $pid) {
                if (stristr($query, $table . ' SET ' . $pid) !== FALSE) {
                    preg_match_all("^=\s\d+^", $query, $matches);
                    if (!empty($matches) && count($matches[0]) == 2) {
                        $to = trim($matches[0][0], '= ');
                        $from = trim($matches[0][1], '= ');
                        $this->preceeding_query = "SET IDENTITY_INSERT $table ON";
                        // find a better way to get columns (field mapping doesn't grab all)
                        $query = "INSERT INTO $table (term_id,name,slug,term_group) SELECT $to,name,slug,term_group FROM $table WHERE $pid = $from";
                        $this->following_query = array("DELETE $table WHERE $pid = $from","SET IDENTITY_INSERT $table OFF");
                        $this->verify = false;
                    }
                }
            }
        }

        return $query;
    }

    /**
     * Changes for DATE_ADD and INTERVAL
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_date_add($query)
    {
        $query = preg_replace('/date_add\((.*?),.*?([0-9]+?) (.*?)\)/i', 'DATEADD(\3,\2,\1)', $query);
        $query = preg_replace('/date_sub\((.*?),.*?([0-9]+?) (.*?)\)/i', 'DATEADD(\3,-\2,\1)', $query);

        return $query;
    }


    /**
     * Removing Unnecessary IF statement that T-SQL doesn't play nice with
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_if_stmt($query)
    {
        if ( stripos($query, 'IF (DATEADD(') > 0 ) {
            $start_pos = stripos($query, 'DATEADD(');
            $end_pos = $this->get_matching_paren($query, $start_pos + 8);
            $stmt = substr($query, $start_pos, ($end_pos - $start_pos)) . ') >= getdate() THEN 1 ELSE 0 END)';

            $start_pos = stripos($query, 'IF (');
            $end_pos = $this->get_matching_paren($query, ($start_pos+6))+1;
            $query = substr_replace($query, '(CASE WHEN ' . $stmt, $start_pos, ($end_pos - $start_pos));
        }
        return $query;
    }

    /**
     * SQL_CALC_FOUND_ROWS does not exist in T-SQL
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_sqlcalcrows($query)
    {
        if (stripos($query, 'SQL_CALC_FOUND_ROWS') > 0 ) {
            $sql_calc_pos = stripos($query, 'SQL_CALC_FOUND_ROWS');
            $from_pos = stripos($query, 'FROM');
            $query = substr_replace($query,'* ', $sql_calc_pos, ($from_pos - $sql_calc_pos));
        }
        // catch the next query.
        if ( stripos($query, 'FOUND_ROWS()') > 0 ) {
            $from_pos = stripos($this->previous_query, 'FROM');
            $where_pos = stripos($this->previous_query, 'WHERE');
            $from_str = trim(substr($this->previous_query, $from_pos, ($where_pos - $from_pos)));
            $order_by_pos = stripos($this->previous_query, 'ORDER BY');
            $where_str = trim(substr($this->previous_query, $where_pos, ($order_by_pos - $where_pos)));
            $query = str_ireplace('FOUND_ROWS()', 'COUNT(1) as Computed ' . $from_str . ' ' . $where_str, $query);
        }
        return $query;
    }

    /**
     * Translate specific queries
     *
     * @since 3.0
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_specific($query)
    {
        if ($query == "SELECT COUNT(NULLIF(meta_value LIKE '%administrator%', FALSE) as Computed), "
                        . "COUNT(NULLIF(meta_value LIKE '%editor%', FALSE) as Computed), "
                        . "COUNT(NULLIF(meta_value LIKE '%author%', FALSE) as Computed), "
                        . "COUNT(NULLIF(meta_value LIKE '%contributor%', FALSE) as Computed), "
                        . "COUNT(NULLIF(meta_value LIKE '%subscriber%', FALSE) as Computed), "
                        . "COUNT(*) as Computed FROM " . $this->prefix . "usermeta WHERE meta_key LIKE '" . $this->prefix . "capabilities'") {
            $query = "SELECT
    (SELECT COUNT(*) FROM " . $this->prefix . "usermeta WHERE meta_key LIKE '" . $this->prefix . "capabilities' AND meta_value LIKE '%administrator%') as ca,
    (SELECT COUNT(*) FROM " . $this->prefix . "usermeta WHERE meta_key LIKE '" . $this->prefix . "capabilities' AND meta_value LIKE '%editor%') as cb,
    (SELECT COUNT(*) FROM " . $this->prefix . "usermeta WHERE meta_key LIKE '" . $this->prefix . "capabilities' AND meta_value LIKE '%author%') as cc,
    (SELECT COUNT(*) FROM " . $this->prefix . "usermeta WHERE meta_key LIKE '" . $this->prefix . "capabilities' AND meta_value LIKE '%contributor%') as cd,
    (SELECT COUNT(*) FROM " . $this->prefix . "usermeta WHERE meta_key LIKE '" . $this->prefix . "capabilities' AND meta_value LIKE '%subscriber%') as ce,
    COUNT(*) as c FROM " . $this->prefix . "usermeta WHERE meta_key LIKE '" . $this->prefix . "capabilities'";
        }

        if (stristr($query, "SELECT DISTINCT TOP 50 (" . $this->prefix . "users.ID) FROM " . $this->prefix . "users") !== FALSE) {
            $query = str_ireplace(
                "SELECT DISTINCT TOP 50 (" . $this->prefix . "users.ID) FROM",
                "SELECT DISTINCT TOP 50 (" . $this->prefix . "users.ID), user_login FROM", $query);
        }

        if (stristr($query, 'INNER JOIN ' . $this->prefix . 'terms USING (term_id)') !== FALSE) {
            $query = str_ireplace(
                'USING (term_id)',
                'ON ' . $this->prefix . 'terms.term_id = ' . $this->prefix . 'term_taxonomy.term_id', $query);
        }

        return $query;
    }

    /**
     * Changing LIMIT to TOP...mimicking offset while possible with rownum, it has turned
     * out to be very problematic as depending on the original query, the derived table
     * will have a lot of problems with columns names, ordering and what not.
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_limit($query)
    {
        if ( (stripos($query,'SELECT') !== 0 && stripos($query,'SELECT') !== FALSE)
            && (stripos($query,'UPDATE') !== 0  && stripos($query,'UPDATE') !== FALSE) ) {
            return $query;
        }
        $pattern = '/LIMIT\s*(\d+)((\s*,?\s*)(\d+)*)$/is';
        $matched = preg_match($pattern, $query, $limit_matches);
        if ( $matched == 0 ) {
            return $query;
        }
        // Remove the LIMIT statement
        $true_offset = false;
        $query = preg_replace($pattern, '', $query);
        if ( $this->delete_query ) {
            return $query;
        }
        // Check for true offset
        if ( count($limit_matches) == 5 && $limit_matches[1] != '0' ) {
            $true_offset = true;
        } elseif ( count($limit_matches) == 5 && $limit_matches[1] == '0' ) {
            $limit_matches[1] = $limit_matches[4];
        }

        // Rewrite the query.
        if ( $true_offset === false ) {
            if ( stripos($query, 'DISTINCT') > 0 ) {
                $query = str_ireplace('DISTINCT', 'DISTINCT TOP ' . $limit_matches[1] . ' ', $query);
            } else {
                $query = str_ireplace('DELETE ', 'DELETE TOP ' . $limit_matches[1] . ' ', $query);
                $query = str_ireplace('SELECT ', 'SELECT TOP ' . $limit_matches[1] . ' ', $query);
            }
        } else {
            $limit_matches[1] = (int) $limit_matches[1];
            $limit_matches[4] = (int) $limit_matches[4];

            $this->limit = array(
                'from' => $limit_matches[1],
                'to' => $limit_matches[4]
            );
        }
        return $query;
    }


    /**
     * Replace From UnixTime and now()
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_now_datetime($query)
    {
        $replacement = 'getdate()';
        $query = preg_replace('/(from_unixtime|unix_timestamp)\s*\(([^\)]*)\)/i', $replacement, $query);
        $query = str_ireplace('NOW()', $replacement, $query);

        // REPLACE dayofmonth which doesn't exist in T-SQL
        $check = $query;
        $query = preg_replace('/dayofmonth\((.*?)\)/i', 'DATEPART(DD,\1)',$query);
        if ($check !== $query) {
            $as_array = $this->get_as_fields($query);
            if (empty($as_array)) {
                $query = str_ireplace('FROM','as dom FROM',$query);
                $query = str_ireplace('* as dom','*',$query);
            }
        }
        return $query;
    }

    /**
     * Order By within a Select Distinct needs to have an field for every alias
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_distinct_orderby($query)
    {
        if ( stripos($query, 'DISTINCT') > 0 ) {
            if ( stripos($query, 'ORDER') > 0 ) {
                $ord = '';
                $order_pos = stripos($query, 'ORDER');
                if ( stripos($query, 'BY', $order_pos) > $order_pos ) {
                    $fields = $this->get_as_fields($query);
                    $ob = stripos($query, 'BY', $order_pos);
                    if ( stripos($query, ' ASC', $ob) > 0 ) {
                        $ord = stripos($query, ' ASC', $ob);
                    }
                    if ( stripos($query, ' DESC', $ob) > 0 ) {
                        $ord = stripos($query, ' DESC', $ob);
                    }
                    $str = 'BY ';
                    $str .= implode(', ',$fields);

                    $query = substr_replace($query, $str, $ob, ($ord-$ob));
                    $query = str_replace('ORDER BY BY', 'ORDER BY', $query);
                }
            }
        }
        return $query;
    }

    /**
     * To use REPLACE() fields need to be cast as varchar
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
     function translate_replace_casting($query)
     {
        $query = preg_replace('/REPLACE\((.*?),.*?(.*?),.*?(.*?)\)/i', 'REPLACE(cast(\1 as nvarchar(max)),cast(\2 as nvarchar(max)),cast(\3 as nvarchar(max)))', $query);
        return $query;
     }

    /**
     * To sort text fields they need to be first cast as varchar
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_sort_casting($query)
    {
        if ( stripos($query, 'ORDER') > 0 ) {
            $ord = '';
            $order_pos = stripos($query, 'ORDER');
            if ( stripos($query, 'BY', $order_pos) == ($order_pos + 6) && stripos($query, 'OVER(', $order_pos - 5) != ($order_pos - 5)) {
                $ob = stripos($query, 'BY', $order_pos);
                if ( stripos($query,' ASC', $ob) > 0 ) {
                    $ord = stripos($query, ' ASC', $ob);
                }
                if ( stripos($query,' DESC', $ob) > 0 ) {
                    $ord = stripos($query, ' DESC', $ob);
                }

                $params = substr($query, ($ob + 3), ($ord - ($ob + 3)));
                $params = preg_split('/[\s,]+/', $params);
                $p = array();
                foreach ( $params as $value ) {
                    $value = str_replace(',', '', $value);
                    if ( !empty($value) ) {
                        $p[] = $value;
                    }
                }
                $str = '';

                foreach ($p as $v ) {
                    $match = false;
                    foreach( $this->fields_map->read() as $table => $table_fields ) {
                        if ( is_array($table_fields) ) {
                            foreach ( $table_fields as $field => $field_meta) {
                                if ($field_meta['type'] == 'ntext') {
                                    if ( $v == $table . '.' . $field || $v == $field) {
                                        $match = true;
                                    }
                                }
                            }
                        }
                    }
                    if ( $match ) {
                        $str .= 'cast(' . $v . ' as nvarchar(255)), ';
                    } else {
                        $str .= $v . ', ';
                    }
                }
                $str = rtrim($str, ', ');
                $query = substr_replace($query, $str, ($ob + 3), ($ord - ($ob + 3)));
            }
        }
        return $query;
    }

    /**
     * Meta key fix. \_%  to  [_]%
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_column_type($query)
    {
        if ( stripos($query, "LIKE '\_%'") > 0 ) {
            $start_pos = stripos($query, "LIKE '\_%'");
            $end_pos = $start_pos + 10;
            $str = "LIKE '[_]%'";
            $query = substr_replace($query, $str, $start_pos, ($end_pos - $start_pos));
        }
        return $query;
    }


    /**
     * Remove group by stmt in certain queries as T-SQL will
     * want all column names to execute query properly
     *
     * FIXES: Column 'wp_posts.post_author' is invalid in the select list because
     * it is not contained in either an aggregate function or the GROUP BY clause.
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_remove_groupby($query)
    {
        $query = str_ireplace("GROUP BY {$this->prefix}posts.ID ", ' ', $query);
        // Fixed query for archives widgets.
        $query = str_ireplace(
            'GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC',
            'GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY month DESC, year DESC',
            $query
        );
        return $query;
    }


    /**
     * When INSERTING 0000-00-00 00:00:00 or '' for datetime SQL Server says wtf
     * because it's null value begins at 1900-01-01...so lets change this to current time.
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_insert_nulltime($query)
    {

        if ( !$this->insert_query ) {
            return $query;
        }

        // Lets grab the fields to be inserted into and their position
        // based on the csv.
        $first_paren = stripos($query, '(', 11) + 1;
        $last_paren = $this->get_matching_paren($query, $first_paren);
        $fields = explode(',',substr($query, $first_paren, ($last_paren - $first_paren)));
        $date_fields = array();
        $date_fields_map = $this->fields_map->by_type('date');
        foreach ($fields as $key => $field ) {
                $field = trim($field);

                if ( in_array($field, $date_fields_map) ) {
                        $date_fields[] = array('pos' => $key, 'field' => $field);
                }
        }

        // we have date fields to check
        if ( count($date_fields) > 0 ) {
                // we need to get the values
                $values_pos = stripos($query, 'VALUES');
                $first_paren = stripos($query, '(', $values_pos);
                $last_paren = $this->get_matching_paren($query, ($first_paren + 1));
                $values = explode(',',substr($query, ($first_paren+1), ($last_paren-($first_paren+1))));
                foreach ( $date_fields as $df ) {
                        $v = trim($values[$df['pos']]);
                        $quote = ( stripos($v, "'0000-00-00 00:00:00'") === 0 || $v === "''" ) ? "'" : '';
                        if ( stripos($v, '0000-00-00 00:00:00') === 0
                                || stripos($v, "'0000-00-00 00:00:00'") === 0
                                || $v === "''" ) {
                                if ( stripos($df['field'], 'gmt') > 0 ) {
                                        $v = $quote.gmdate('Y-m-d H:i:s').$quote;
                                } else {
                                        $v = $quote.date('Y-m-d H:i:s').$quote;
                                }
                        }
                        $values[$df['pos']] = $v;
                }
                $str = implode(',', $values);
                $query = substr_replace($query, $str, ($first_paren+1), ($last_paren-($first_paren+1)));
        }

        return $query;
    }

    /**
     * The data types text and varchar are incompatible in the equal to operator.
     * TODO: Have a check for the appropriate table of the field to avoid collision
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_incompat_data_type($query)
    {
        if ( !$this->select_query && !$this->delete_query ) {
            return $query;
        }

        $operators = array(
            '='  => 'LIKE',
            '!=' => 'NOT LIKE',
            '<>' => 'NOT LIKE'
        );

        $field_types = array('ntext', 'nvarchar', 'text', 'varchar');

        foreach($this->fields_map->read() as $table => $table_fields) {
            if (!is_array($table_fields)) {
                continue;
            }
            foreach ($table_fields as $field => $field_meta) {
                if ( !in_array($field_meta['type'], $field_types) ) {
                    continue;
                }
                foreach($operators as $oper => $val) {
                    $query = preg_replace('/\s+'.$table . '.' . $field.'\s*'.$oper.'/i', ' '.$table . '.' . $field . ' ' . $val, $query);
                    $query = preg_replace('/\s+'.$field.'\s*'.$oper.'/i', ' ' . $field . ' ' . $val, $query);
                    // check for integers to cast.
                    $query = preg_replace('/\s+LIKE\s*(-?\d+)/i', " {$val} cast($1 as nvarchar(max))", $query);
                }
            }

        }

        return $query;
    }

    /**
     * General create/alter query translations
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Translated Query
     */
    function translate_create_queries($query)
    {
        if ( !$this->create_query ) {
            return $query;
        }

        // fix enum as it doesn't exist in T-SQL
        if (stripos($query, 'enum(') !== false) {
            $enums = array_reverse($this->stripos_all($query, 'enum('));
            foreach ($enums as $start_pos) {
                $end = $this->get_matching_paren($query, $start_pos + 5);
                // get values inside enum
                $values = substr($query, $start_pos + 5, ($end - ($start_pos + 5)));
                $values = explode(',', $values);
                $all_int = true;
                foreach ($values as $value) {
                    $val = trim(str_replace("'", '', $value));
                    if (!is_numeric($val) || (int) $val != $val) {
                        $all_int = false;
                    }
                }
                // if enum of ints create an appropriate int column otherwise create a varchar
                if ($all_int) {
                    $query = substr_replace($query, 'smallint', $start_pos, ($end + 1) - $start_pos);
                } else {
                    $query = substr_replace($query, 'nvarchar(255)', $start_pos, ($end + 1) - $start_pos);
                }
            }
        }

        // remove IF NOT EXISTS as that doesn't exist in T-SQL
        $query = str_ireplace(' IF NOT EXISTS', '', $query);

        // save array to file_maps
        $this->fields_map->update_for($query);

        // change auto increment to indentity
        $start_positions = array_reverse($this->stripos_all($query, 'auto_increment'));
        if( stripos($query, 'auto_increment') > 0 ) {
            foreach ($start_positions as $start_pos) {
                $query = substr_replace($query, 'IDENTITY(1,1)', $start_pos, 14);
            }
        }
        if(stripos($query, 'AFTER') > 0) {
            $start_pos = stripos($query, 'AFTER');
            $query = substr($query, 0, $start_pos);
        }
        // replacement of certain data types and functions
        $fields = array(
            'int (',
            'int(',
            'index (',
            'index(',
        );

        foreach ( $fields as $field ) {
            // reverse so that when we make changes it wont effect the next change.
            $start_positions = array_reverse($this->stripos_all($query, $field));
            foreach ($start_positions as $start_pos) {
                $first_paren = stripos($query, '(', $start_pos);
                $end_pos = $this->get_matching_paren($query, $first_paren + 1) + 1;
                if( $field == 'index(' || $field == 'index (' ) {
                    $query = substr_replace($query, '', $start_pos, $end_pos - $start_pos);
                } else {
                    $query = substr_replace($query, rtrim(rtrim($field,'('), ' '), $start_pos, ($end_pos - $start_pos));
                }
            }
        }

        $query = str_ireplace("'0000-00-00 00:00:00'", 'getdate()', $query);

        // strip unsigned
        $query = str_ireplace("unsigned ", '', $query);

        // strip collation, engine type, etc from end of query
        $pos = stripos($query, '(', stripos($query, 'TABLE '));
        $end = $this->get_matching_paren($query, $pos + 1);
        $query = substr_replace($query, ');', $end);

        $query = str_ireplace("DEFAULT CHARACTER SET utf8", '', $query);
        $query = str_ireplace("CHARACTER SET utf8", '', $query);

        if ( ! empty($this->charset) ) {
            $query = str_ireplace("DEFAULT CHARACTER SET {$this->charset}", '', $query);
        }
        if ( ! empty($this->collate) ) {
            $query = str_ireplace("COLLATE {$this->collate}", '', $query);
        }

        // add collation
        $ac_types = array('tinytext', 'longtext', 'mediumtext', 'text', 'varchar');
        foreach ($ac_types as $ac_type) {
            $start_positions = array_reverse($this->stripos_all($query, $ac_type));
            foreach ($start_positions as $start_pos) {
                if ($ac_type == 'varchar') {
                    if (substr($query, $start_pos - 1, 8) == 'NVARCHAR') {
                        continue;
                    }
                    $query = substr_replace($query, 'NVARCHAR', $start_pos, strlen($ac_type));
                    $end = $this->get_matching_paren($query, $start_pos + 9);
                    $sub = substr($query, $end + 2, 7);
                    $end_pos = $end + 1;
                } else {
                    if ($ac_type == 'text' && substr($query, $start_pos - 1, strlen($ac_type) + 1) == 'NTEXT') {
                        continue;
                    }
                    $query = substr_replace($query, 'NVARCHAR(MAX)', $start_pos, strlen($ac_type));
                    $sub = substr($query, $start_pos + 14, 7);
                    $end_pos = $start_pos + 13;
                }

                if ($sub !== 'COLLATE') {
                    $query = $this->add_collation($query, $end_pos);
                }
            }
        }

        $keys = array();
        $table_pos = stripos($query, ' TABLE ') + 6;
        $table = substr($query, $table_pos, stripos($query, '(', $table_pos) - $table_pos);
        $table = trim($table);

        $reserved_words = array('public');
        // get column names to check for reserved words to encapsulate with [ ]
        foreach($this->fields_map->read() as $table_name => $table_fields) {
            if ($table_name == $table && is_array($table_fields)) {
                foreach ($table_fields as $field => $field_meta) {
                    if (in_array($field, $reserved_words)) {
                        $query = str_ireplace($field, "[{$field}]", $query);
                    }
                }
            }
        }

        // get primary key constraints
        if ( stripos($query, 'PRIMARY KEY') > 0) {
            $start_positions = $this->stripos_all($query, 'PRIMARY KEY');
            foreach ($start_positions as $start_pos) {
                $start = stripos($query, '(', $start_pos);
                $end_paren = $this->get_matching_paren($query, $start + 1);
                $field = explode(',', substr($query, $start + 1, $end_paren - ($start + 1)));
                foreach ($field as $k => $v) {
                    if (stripos($v, '(') !== false) {
                        $field[$k] = preg_replace('/\(.*\)/', '', $v);
                    }
                }
                $keys[] = array('type' => 'PRIMARY KEY', 'pos' => $start_pos, 'field' => $field);
            }
        }
        // get unique key constraints
        if ( stripos($query, 'UNIQUE KEY') > 0) {
            $start_positions = $this->stripos_all($query, 'UNIQUE KEY');
            foreach ($start_positions as $start_pos) {
                $start = stripos($query, '(', $start_pos);
                $end_paren = $this->get_matching_paren($query, $start + 1);
                $field = explode(',', substr($query, $start + 1, $end_paren - ($start + 1)));
                foreach ($field as $k => $v) {
                    if (stripos($v, '(') !== false) {
                        $field[$k] = preg_replace('/\(.*\)/', '', $v);
                    }
                }
                $keys[] = array('type' => 'UNIQUE KEY', 'pos' => $start_pos, 'field' => $field);
            }
        }
        // get key constraints
        if ( stripos($query, 'KEY') > 0) {
            $start_positions = $this->stripos_all($query, 'KEY');
            foreach ($start_positions as $start_pos) {
                if (substr($query, $start_pos - 7, 6) !== 'UNIQUE'
                    && substr($query, $start_pos - 8, 7) !== 'PRIMARY'
                    && (substr($query, $start_pos - 1, 1) == ' ' || substr($query, $start_pos - 1, 1) == "\n")) {
                    $start = stripos($query, '(', $start_pos);
                    $end_paren = $this->get_matching_paren($query, $start + 1);
                    $field = explode(',', substr($query, $start + 1, $end_paren - ($start + 1)));
                    foreach ($field as $k => $v) {
                        if (stripos($v, '(') !== false) {
                            $field[$k] = preg_replace('/\(.*\)/', '', $v);
                        }
                    }
                    $keys[] = array('type' => 'KEY', 'pos' => $start_pos, 'field' => $field);
                }
            }
        }

        $count = count($keys);
        $add_primary = false;
        $key_str = '';
        $lowest_start_pos = false;
        $unwanted = array(
            'slug',
            'name',
            'term_id',
            'taxonomy',
            'term_taxonomy_id',
            'comment_approved',
            'comment_post_ID',
            'comment_approved',
            'link_visible',
            'post_id',
            'meta_key',
            'post_type',
            'post_status',
            'post_date',
            'ID',
            'post_name',
            'post_parent',
            'user_login',
            'user_nicename',
            'user_id',
        );
        for ($i = 0; $i < $count; $i++) {
            if ($keys[$i]['pos'] < $lowest_start_pos || $lowest_start_pos === false) {
                $lowest_start_pos = $keys[$i]['pos'];
            }
            if ($keys[$i]['type'] == 'PRIMARY KEY') {
                $add_primary = true;
            }
            switch ($keys[$i]['type']) {
                case 'PRIMARY KEY':
                    $str = "CONSTRAINT [" . $table . "_" . implode('_', $keys[$i]['field']) . "] PRIMARY KEY CLUSTERED (" . implode(',', $keys[$i]['field']) . ") WITH (IGNORE_DUP_KEY = OFF)";
                    if (!$this->azure ) {
                        $str .= " ON [PRIMARY]";
                    }
                break;
                case 'UNIQUE KEY':
                    $check = true;
                    foreach ($keys[$i]['field'] as $field) {
                        if (in_array($field, $unwanted)) {
                            $check = false;
                        }
                    }
                    if ($check) {
                        if ($this->azure) {
                            $str = 'CONSTRAINT [' . $table . '_' . implode('_', $keys[$i]['field']) . '] UNIQUE NONCLUSTERED (' . implode(',', $keys[$i]['field']) . ')';
                        } else {
                            $str = 'CONSTRAINT [' . $table . '_' . implode('_', $keys[$i]['field']) . '] UNIQUE NONCLUSTERED (' . implode(',', $keys[$i]['field']) . ')';
                        }
                    } else {
                        $str = '';
                    }
                break;
                case 'KEY':
                    // CREATE NONCLUSTERED INDEX index_name ON table(col1,col2)
                    $check = true;
                    $str = '';
                    foreach ($keys[$i]['field'] as $field) {
                        if (in_array($field, $unwanted)) {
                            $check = false;
                        }
                    }
                    if ($check) {
                        if (!is_array($this->following_query) && $this->following_query === false) {
                            $this->following_query = array();
                        } elseif (!is_array($this->following_query)) {
                            $this->following_query = array($this->following_query);
                        }
                        if ($this->azure) {
                            $this->following_query[] = 'CREATE CLUSTERED INDEX ' .
                            $table . '_' . implode('_', $keys[$i]['field']) .
                            ' ON '.$table.'('.implode(',', $keys[$i]['field']).')';
                        } else {
                            $this->following_query[] = 'CREATE NONCLUSTERED INDEX ' .
                            $table . '_' . implode('_', $keys[$i]['field']) .
                            ' ON '.$table.'('.implode(',', $keys[$i]['field']).')';
                        }
                    }
                break;
            }
            if ($i !== $count - 1 && $str !== '') {
                $str .= ',';
            }
            $key_str .= $str . "\n";
        }
        if ($key_str !== '') {
            if ($add_primary && !$this->azure) {
                $query = substr_replace($query, $key_str . ") ON [PRIMARY];", $lowest_start_pos);
            } else {
                $query = substr_replace($query, $key_str . ");", $lowest_start_pos);
            }
        }

        return $query;
    }

    /**
     * Given a first parenthesis ( ...will find its matching closing paren )
     *
     * @since 2.7.1
     *
     * @param string $str given string
     * @param int $start_pos position of where desired starting paren begins+1
     *
     * @return int position of matching ending parenthesis
     */
    function get_matching_paren($str, $start_pos)
    {
        $count = strlen($str);
        $bracket = 1;
        for ( $i = $start_pos; $i < $count; $i++ ) {
            if ( $str[$i] == '(' ) {
                $bracket++;
            } elseif ( $str[$i] == ')' ) {
                $bracket--;
            }
            if ( $bracket == 0 ) {
                return $i;
            }
        }
    }

    /**
     * Get the Aliases in a query
     * E.G. Field1 AS yyear, Field2 AS mmonth
     * will return array with yyear and mmonth
     *
     * @since 2.7.1
     *
     * @param string $str a query
     *
     * @return array array of aliases in a query
     */
    function get_as_fields($query)
    {
        $arr = array();
        $tok = preg_split('/[\s,]+/', $query);
        $count = count($tok);
        for ( $i = 0; $i < $count; $i++ ) {
            if ( strtolower($tok[$i]) === 'as' ) {
                $arr[] = $tok[($i + 1)];
            }
        }
        return $arr;
    }

    /**
    * Fix for SQL Server returning null values with one space.
    * Fix for SQL Server returning datetime fields with milliseconds.
    * Fix for SQL Server returning integer fields as integer (mysql returns as string)
    *
    * @since 2.7.1
    *
    * @param array $result_set result set array of an executed query
    *
    * @return array result set array with modified fields
    */
    function fix_results($result_set)
    {
        // If empty bail early.
        if ( is_null($result_set)) {
            return false;
        }
        if (is_array($result_set) && empty($result_set)) {
            return array();
        }
        $map_fields = $this->fields_map->by_type('date');
        $fields = array_keys(get_object_vars(current($result_set)));
        foreach ( $result_set as $key => $result ) {
            // Remove milliseconds
            foreach ( $map_fields as $date_field ) {
                if ( isset($result->$date_field) ) {
                    // date_format is a PHP5 function. sqlsrv is only PHP5 compat
                    // the result set for datetime columns is a PHP DateTime object, to extract
                    // the string we need to use date_format().
                    if (is_object($result->$date_field)) {
                        $result_set[$key]->$date_field = date_format($result->$date_field, 'Y-m-d H:i:s');
                    }
                }
            }
            // Check for null values being returned as space and change integers to strings (to mimic mysql results)
            foreach ( $fields as $field ) {
                if ($field == 'crdate' || $field == 'refdate') {
                    $result_set[$key]->$field = date_format($result->$field, 'Y-m-d H:i:s');
                }
                if ( $result->$field === ' ' ) {
                    $result->$field = '';
                }
                if ( is_int($result->$field) ) {
                    $result->$field = (string) $result->$field;
                }
            }
        }

        $map_fields = $this->fields_map->by_type('ntext');
        foreach ( $result_set as $key => $result ) {
            foreach ( $map_fields as $text_field ) {
                if ( isset($result->$text_field) ) {
                    $result_set[$key]->$text_field = str_replace("''", "'", $result->$text_field);
                }
            }
        }
        return $result_set;
    }

    /**
     * Check to see if INSERT has an ON DUPLICATE KEY statement
     * This is MySQL specific and will be removed and put into
     * a following_query UPDATE STATEMENT
     *
     * @param string $query Query coming in
     * @return string query without ON DUPLICATE KEY statement
     */
     function on_duplicate_key($query)
     {
        if ( stripos($query, 'ON DUPLICATE KEY UPDATE') > 0 ) {
            $table = substr($query, 12, (strpos($query, ' ', 12) - 12));
            // currently just deal with wp_options table
            if (stristr($table, 'options') !== FALSE) {
                $start_pos = stripos($query, 'ON DUPLICATE KEY UPDATE');
                $query = substr_replace($query, '', $start_pos);
                $values_pos = stripos($query, 'VALUES');
                $first_paren = stripos($query, '(', $values_pos);
                $last_paren = $this->get_matching_paren($query, $first_paren + 1);
                $values = explode(',', substr($query, ($first_paren + 1), ($last_paren-($first_paren + 1))));
                // change this to use mapped fields
                $update = 'UPDATE ' . $table . ' SET option_value = ' . $values[1] . ', autoload = ' . $values[2] .
                    ' WHERE option_name = ' . $values[0];
                $this->following_query = $update;
            }
        }
        return $query;
     }

    /**
     * Check to see if an INSERT query has multiple VALUES blocks. If so we need create
     * seperate queries for each.
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return array array of insert queries
     */
    function split_insert_values($query)
    {
        $arr = array();
        if (stripos($query, 'INSERT') === 0) {
            $first = substr($query, 0, (stripos($query, 'VALUES') + 7));
            $values = substr($query, (stripos($query, 'VALUES') + 7));
            $arr = preg_split('/\),\s+\(/', $values);
            foreach ($arr as $k => $v) {
                if (substr($v, -1) !== ')') {
                    $v = $v . ')';
                }

                if (substr($v, 0, 1) !== '(') {
                    $v = '(' . $v;
                }

                $arr[$k] = $first . $v;
            }
        }
        if (count($arr) < 2) {
            return $query;
        }
        return $arr;
    }

    /**
     * Check query to make sure translations weren't made to INSERT query values
     * If so replace translation with original data.
     * E.G. INSERT INTO wp_posts (wp_title) VALUES ('SELECT * FROM wp_posts LIMIT 1');
     * The translations may change the value data to SELECT TOP 1 FROM wp_posts...in this case
     * we don't want that to happen.
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Verified Query
     */
    function verify_insert($query)
    {
        $map_fields = $this->fields_map->by_type('ntext') + $this->fields_map->by_type('nvarchar');

        $first_paren = stripos($query, '(', 0);
        $last_paren = $this->get_matching_paren($query, $first_paren + 1);
        $cols = explode(',', substr($query, ($first_paren + 1), ($last_paren-($first_paren + 1))));
        $values_pos = stripos($query, 'VALUES');
        $first_paren = stripos($query, '(', $values_pos);
        $last_paren = $this->get_matching_paren($query, $first_paren + 1);
        $values = explode(',', substr($query, ($first_paren + 1), ($last_paren-($first_paren + 1))));

        $arr = array();
        foreach ( $values as $k => $value ) {
            if (isset($cols[$k])) {
                foreach ($map_fields as $text_field) {
                    if (trim($cols[$k]) == $text_field) {
                        $arr[] = $k;
                    }
                }
            }
        }
        $str = '';
        foreach ($values as $k => $value) {
            $val = trim($value);
            $end = strlen($val) - 1;
            if (in_array($k, $arr) && $val[0] == "'" && $val[$end] == "'") {
                $str .= 'N' . trim($value) . ',';
            } else {
                $str .= $value . ',';
            }
        }
        $str = rtrim($str, ',');
        $query = substr_replace($query, $str, ($first_paren + 1), ($last_paren - ($first_paren + 1)));

        if ( count($this->prepare_args) !== count($values) ) {
            return $query;
        }
        $i = 0;
        foreach ( $values as $k => $value ) {
            $N = '';
            if (isset($cols[$k])) {
                foreach ($map_fields as $text_field) {
                    if (trim($cols[$k]) == $text_field) {
                        $N = 'N';
                    }
                }
            }
            $value = trim($value);
            foreach ($this->prepare_args as $r => $arg) {
                if ( $k == $i && $arg !== $value ) {
                    if ( $arg !== '' && $arg !== '0000-00-00 00:00:00' ) {
                        $values[$k] = $N . "'" . $arg . "'";
                    }
                }
                $i++;
            }
        }
        $str = implode(',', $values);
        $first_paren = stripos($query, '(', 0);
        $last_paren = $this->get_matching_paren($query, $first_paren + 1);
        $cols = explode(',', substr($query, ($first_paren + 1), ($last_paren-($first_paren + 1))));
        $values_pos = stripos($query, 'VALUES');
        $first_paren = stripos($query, '(', $values_pos);
        $last_paren = $this->get_matching_paren($query, $first_paren + 1);
        $query = substr_replace($query, $str, ($first_paren + 1), ($last_paren - ($first_paren + 1)));
        return $query;
    }

    /**
     * Check query to make sure translations weren't made to UPDATE query values
     * If so replace translation with original data.
     * E.G. UPDATE wp_posts SET post_title = 'SELECT * FROM wp_posts LIMIT 1' WHERE post_id = 1;
     * The translations may change the value data to SELECT TOP 1 FROM wp_posts...in this case
     * we don't want that to happen
     *
     * @since 2.7.1
     *
     * @param string $query Query coming in
     *
     * @return string Verified Query
     */
    function verify_update($query)
    {
        $values = array();
        $keys = array();
        $map_fields = $this->fields_map->by_type('ntext') + $this->fields_map->by_type('nvarchar');
        $start = stripos($query, 'SET') + 3;
        $end = strripos($query, 'WHERE');
        $sub = substr($query, $start, $end - $start);
        $arr = explode(', ', $sub);
        foreach ( $arr as $k => $v ) {
            $v = trim($v);
            $st = stripos($v, ' =');
            $sv = substr($v, 0, $st);
            $sp = substr($v, $st + 4, -1);
            $keys[] = $sv;
            $values[] = str_replace("'", "''", $sp);
        }

        foreach ( $values as $y => $vt ) {
            $n = '';
            foreach ($map_fields as $text_field) {
                if (trim($keys[$y]) == $text_field) {
                    $n = 'N';
                }
            }
            $values[$y] = $keys[$y] . " = $n'" . $vt . "'";
        }

        $str = implode(', ', $values) . ' ';
        $query = substr_replace($query, $str, ($start+1), ($end-($start+1)));

        return $query;
    }

    /**
     * Add collation for a field definition within a CREATE/ALTER query
     *
     * @since 2.8
     * @param $type
     *
     * @return string
     */
    function add_collation($query, $pos)
    {
        switch (WPLANG) {
            case 'ru_RU':
                $collation = 'Cyrillic_General_BIN';
            break;
            case 'en_US':
            default:
                $collation = 'Latin1_General_BIN';
            break;
        }
        $query = substr_replace($query, " COLLATE $collation", $pos, 0);
        return $query;
    }

    /**
     * Describe wrapper
     *
     * @since 2.8.5
     * @param $table
     *
     * @return string
     */
    function describe($table)
    {
        $sql = "SELECT
            c.name AS Field
            ,t.name + t.length_string AS Type
            ,CASE c.is_nullable WHEN 1 THEN 'YES' ELSE 'NO' END AS [Null]
            ,CASE
                WHEN EXISTS (SELECT * FROM sys.key_constraints AS kc
                               INNER JOIN sys.index_columns AS ic ON kc.unique_index_id = ic.index_id AND kc.parent_object_id = ic.object_id
                               WHERE kc.type = 'PK' AND ic.column_id = c.column_id AND c.object_id = ic.object_id)
                               THEN 'PRI'
                WHEN EXISTS (SELECT * FROM sys.key_constraints AS kc
                               INNER JOIN sys.index_columns AS ic ON kc.unique_index_id = ic.index_id AND kc.parent_object_id = ic.object_id
                               WHERE kc.type <> 'PK' AND ic.column_id = c.column_id AND c.object_id = ic.object_id)
                               THEN 'UNI'
                ELSE ''
            END AS [Key]
            ,ISNULL((
                SELECT TOP(1)
                    dc.definition
                FROM sys.default_constraints AS dc
                WHERE dc.parent_column_id = c.column_id AND c.object_id = dc.parent_object_id)
            ,'') AS [Default]
            ,CASE
                WHEN EXISTS (
                    SELECT
                        *
                    FROM sys.identity_columns AS ic
                    WHERE ic.column_id = c.column_id AND c.object_id = ic.object_id)
                        THEN 'auto_increment'
                ELSE ''
            END AS Extra
        FROM sys.columns AS c
        CROSS APPLY (
            SELECT
                t.name AS n1
                ,CASE
                    -- Types with length
                    WHEN c.max_length > 0 AND t.name IN ('varchar', 'char', 'varbinary', 'binary') THEN '(' + CAST(c.max_length AS VARCHAR) + ')'
                    WHEN c.max_length > 0 AND t.name IN ('nvarchar', 'nchar') THEN '(' + CAST(c.max_length/2 AS VARCHAR) + ')'
                    WHEN c.max_length < 0 AND t.name IN ('nvarchar', 'varchar', 'varbinary') THEN '(max)'
                    -- Types with precision & scale
                    WHEN t.name IN ('decimal', 'numeric') THEN '(' + CAST(c.precision AS VARCHAR) + ',' + CAST(c.scale AS VARCHAR) + ')'
                    -- Types with only precision
                    WHEN t.name IN ('float') THEN '(' + CAST(c.precision AS VARCHAR) + ')'
                    -- Types with only scale
                    WHEN t.name IN ('datetime2', 'time', 'datetimeoffset') THEN '(' + CAST(c.scale AS VARCHAR) + ')'
                    -- The rest take no arguments
                    ELSE ''
                END AS length_string
                ,*
            FROM sys.types AS t
            WHERE t.system_type_id = c.system_type_id AND t.system_type_id = t.user_type_id
        ) AS t
        WHERE object_id = OBJECT_ID('{$table}');";
        return $sql;
    }

    /**
     * Get all occurrences(positions) of a string within a string
     *
     * @since 2.8
     * @param $type
     *
     * @return array
     */
    function stripos_all($haystack, $needle, $offset = 0)
    {
        $arr = array();
        while ($offset !== false) {
            $pos = stripos($haystack, $needle, $offset);
            if ($pos !== false) {
                $arr[] = $pos;
                $pos = $pos + strlen($needle);
            }
            $offset = $pos;
        }
        return $arr;
    }
}

if ( !function_exists('str_ireplace') ) {
    /**
     * PHP 4 Compatible str_ireplace function
     * found in php.net comments
     *
     * @since 2.7.1
     *
     * @param string $search what needs to be replaced
     * @param string $replace replacing value
     * @param string $subject string to perform replace on
     *
     * @return string the string with replacements
     */
    function str_ireplace($search, $replace, $subject)
    {
        $token = chr(1);
        $haystack = strtolower($subject);
        $needle = strtolower($search);
        while ( $pos = strpos($haystack, $needle) !== FALSE ) {
            $subject = substr_replace($subject, $token, $pos, strlen($search));
            $haystack = substr_replace($haystack, $token, $pos, strlen($search));
        }
        return str_replace($token, $replace, $subject);
    }
}

if ( !function_exists('stripos') ) {
    /**
     * PHP 4 Compatible stripos function
     * found in php.net comments
     *
     * @since 2.7.1
     *
     * @param string $str the string to search in
     * @param string $needle what we are looking for
     * @param int $offset starting position
     *
     * @return int position of needle if found. FALSE if not found.
     */
    function stripos($str, $needle, $offset = 0)
    {
        return strpos(strtolower($str), strtolower($needle), $offset);
    }
}

if ( !function_exists('strripos') ) {
    /**
     * PHP 4 Compatible strripos function
     * found in php.net comments
     *
     * @since 2.7.1
     *
     * @param string $haystack the string to search in
     * @param string $needle what we are looking for
     *
     * @return int position of needle if found. FALSE if not found.
     */
    function strripos($haystack, $needle, $offset=0)
    {
        if ( !is_string($needle) ) {
            $needle = chr(intval($needle));
        }
        if ( $offset < 0 ) {
            $temp_cut = strrev(substr($haystack, 0, abs($offset)));
        } else{
            $temp_cut = strrev(substr($haystack, 0, max((strlen($haystack) - $offset ), 0)));
        }
        if ( stripos($temp_cut, strrev($needle)) === false ) {
            return false;
        } else {
            $found = stripos($temp_cut, strrev($needle));
        }
        $pos = (strlen($haystack) - ($found + $offset + strlen($needle)));
        return $pos;
    }
}
