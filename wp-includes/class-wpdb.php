<?php
/**
 * WordPress database access abstraction class.
 *
 * Original code from {@link http://php.justinvincent.com Justin Vincent (justin@visunet.ie)}
 *
 * @package WordPress
 * @subpackage Database
 * @since 0.71
 */

/**
 * @since 0.71
 */
define( 'EZSQL_VERSION', 'WP1.25' );

/**
 * @since 0.71
 */
define( 'OBJECT', 'OBJECT' );
// phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ConstantNotUpperCase
define( 'object', 'OBJECT' ); // Back compat.

/**
 * @since 2.5.0
 */
define( 'OBJECT_K', 'OBJECT_K' );

/**
 * @since 0.71
 */
define( 'ARRAY_A', 'ARRAY_A' );

/**
 * @since 0.71
 */
define( 'ARRAY_N', 'ARRAY_N' );

/**
 * WordPress database access abstraction class.
 *
 * This class is used to interact with a database without needing to use raw SQL statements.
 * By default, WordPress uses this class to instantiate the global $wpdb object, providing
 * access to the WordPress database.
 *
 * It is possible to replace this class with your own by setting the $wpdb global variable
 * in wp-content/db.php file to your class. The wpdb class will still be included, so you can
 * extend it or simply use your own.
 *
 * @link https://developer.wordpress.org/reference/classes/wpdb/
 *
 * @since 0.71
 */
#[AllowDynamicProperties]
class wpdb {

	/**
	 * Whether to show SQL/DB errors.
	 *
	 * Default is to show errors if both WP_DEBUG and WP_DEBUG_DISPLAY evaluate to true.
	 *
	 * @since 0.71
	 *
	 * @var bool
	 */
	public $show_errors = false;

	/**
	 * Whether to suppress errors during the DB bootstrapping. Default false.
	 *
	 * @since 2.5.0
	 *
	 * @var bool
	 */
	public $suppress_errors = false;

	/**
	 * The error encountered during the last query.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	public $last_error = '';

	/**
	 * The number of queries made.
	 *
	 * @since 1.2.0
	 *
	 * @var int
	 */
	public $num_queries = 0;

	/**
	 * Count of rows returned by the last query.
	 *
	 * @since 0.71
	 *
	 * @var int
	 */
	public $num_rows = 0;

	/**
	 * Count of rows affected by the last query.
	 *
	 * @since 0.71
	 *
	 * @var int
	 */
	public $rows_affected = 0;

	/**
	 * The ID generated for an AUTO_INCREMENT column by the last query (usually INSERT).
	 *
	 * @since 0.71
	 *
	 * @var int
	 */
	public $insert_id = 0;

	/**
	 * The last query made.
	 *
	 * @since 0.71
	 *
	 * @var string
	 */
	public $last_query;

	/**
	 * Results of the last query.
	 *
	 * @since 0.71
	 *
	 * @var stdClass[]|null
	 */
	public $last_result;

	/**
	 * Database query result.
	 *
	 * Possible values:
	 *
	 * - `mysqli_result` instance for successful SELECT, SHOW, DESCRIBE, or EXPLAIN queries
	 * - `true` for other query types that were successful
	 * - `null` if a query is yet to be made or if the result has since been flushed
	 * - `false` if the query returned an error
	 *
	 * @since 0.71
	 *
	 * @var mysqli_result|bool|null
	 */
	protected $result;

	/**
	 * Cached column info, for confidence checking data before inserting.
	 *
	 * @since 4.2.0
	 *
	 * @var array
	 */
	protected $col_meta = array();

	/**
	 * Calculated character sets keyed by table name.
	 *
	 * @since 4.2.0
	 *
	 * @var string[]
	 */
	protected $table_charset = array();

	/**
	 * Whether text fields in the current query need to be confidence checked.
	 *
	 * @since 4.2.0
	 *
	 * @var bool
	 */
	protected $check_current_query = true;

	/**
	 * Flag to ensure we don't run into recursion problems when checking the collation.
	 *
	 * @since 4.2.0
	 *
	 * @see wpdb::check_safe_collation()
	 * @var bool
	 */
	private $checking_collation = false;

	/**
	 * Saved info on the table column.
	 *
	 * @since 0.71
	 *
	 * @var array
	 */
	protected $col_info;

	/**
	 * Log of queries that were executed, for debugging purposes.
	 *
	 * @since 1.5.0
	 * @since 2.5.0 The third element in each query log was added to record the calling functions.
	 * @since 5.1.0 The fourth element in each query log was added to record the start time.
	 * @since 5.3.0 The fifth element in each query log was added to record custom data.
	 *
	 * @var array[] {
	 *     Array of arrays containing information about queries that were executed.
	 *
	 *     @type array ...$0 {
	 *         Data for each query.
	 *
	 *         @type string $0 The query's SQL.
	 *         @type float  $1 Total time spent on the query, in seconds.
	 *         @type string $2 Comma-separated list of the calling functions.
	 *         @type float  $3 Unix timestamp of the time at the start of the query.
	 *         @type array  $4 Custom query data.
	 *     }
	 * }
	 */
	public $queries;

	/**
	 * The number of times to retry reconnecting before dying. Default 5.
	 *
	 * @since 3.9.0
	 *
	 * @see wpdb::check_connection()
	 * @var int
	 */
	protected $reconnect_retries = 5;

	/**
	 * WordPress table prefix.
	 *
	 * You can set this to have multiple WordPress installations in a single database.
	 * The second reason is for possible security precautions.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	public $prefix = '';

	/**
	 * WordPress base table prefix.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $base_prefix;

	/**
	 * Whether the database queries are ready to start executing.
	 *
	 * @since 2.3.2
	 *
	 * @var bool
	 */
	public $ready = false;

	/**
	 * Blog ID.
	 *
	 * @since 3.0.0
	 *
	 * @var int
	 */
	public $blogid = 0;

	/**
	 * Site ID.
	 *
	 * @since 3.0.0
	 *
	 * @var int
	 */
	public $siteid = 0;

	/**
	 * List of WordPress per-site tables.
	 *
	 * @since 2.5.0
	 *
	 * @see wpdb::tables()
	 * @var string[]
	 */
	public $tables = array(
		'posts',
		'comments',
		'links',
		'options',
		'postmeta',
		'terms',
		'term_taxonomy',
		'term_relationships',
		'termmeta',
		'commentmeta',
	);

	/**
	 * List of deprecated WordPress tables.
	 *
	 * 'categories', 'post2cat', and 'link2cat' were deprecated in 2.3.0, db version 5539.
	 *
	 * @since 2.9.0
	 *
	 * @see wpdb::tables()
	 * @var string[]
	 */
	public $old_tables = array( 'categories', 'post2cat', 'link2cat' );

	/**
	 * List of WordPress global tables.
	 *
	 * @since 3.0.0
	 *
	 * @see wpdb::tables()
	 * @var string[]
	 */
	public $global_tables = array( 'users', 'usermeta' );

	/**
	 * List of Multisite global tables.
	 *
	 * @since 3.0.0
	 *
	 * @see wpdb::tables()
	 * @var string[]
	 */
	public $ms_global_tables = array(
		'blogs',
		'blogmeta',
		'signups',
		'site',
		'sitemeta',
		'registration_log',
	);

	/**
	 * List of deprecated WordPress Multisite global tables.
	 *
	 * @since 6.1.0
	 *
	 * @see wpdb::tables()
	 * @var string[]
	 */
	public $old_ms_global_tables = array( 'sitecategories' );

	/**
	 * WordPress Comments table.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $comments;

	/**
	 * WordPress Comment Metadata table.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	public $commentmeta;

	/**
	 * WordPress Links table.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $links;

	/**
	 * WordPress Options table.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $options;

	/**
	 * WordPress Post Metadata table.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $postmeta;

	/**
	 * WordPress Posts table.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $posts;

	/**
	 * WordPress Terms table.
	 *
	 * @since 2.3.0
	 *
	 * @var string
	 */
	public $terms;

	/**
	 * WordPress Term Relationships table.
	 *
	 * @since 2.3.0
	 *
	 * @var string
	 */
	public $term_relationships;

	/**
	 * WordPress Term Taxonomy table.
	 *
	 * @since 2.3.0
	 *
	 * @var string
	 */
	public $term_taxonomy;

	/**
	 * WordPress Term Meta table.
	 *
	 * @since 4.4.0
	 *
	 * @var string
	 */
	public $termmeta;

	//
	// Global and Multisite tables
	//

	/**
	 * WordPress User Metadata table.
	 *
	 * @since 2.3.0
	 *
	 * @var string
	 */
	public $usermeta;

	/**
	 * WordPress Users table.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $users;

	/**
	 * Multisite Blogs table.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $blogs;

	/**
	 * Multisite Blog Metadata table.
	 *
	 * @since 5.1.0
	 *
	 * @var string
	 */
	public $blogmeta;

	/**
	 * Multisite Registration Log table.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $registration_log;

	/**
	 * Multisite Signups table.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $signups;

	/**
	 * Multisite Sites table.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $site;

	/**
	 * Multisite Sitewide Terms table.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $sitecategories;

	/**
	 * Multisite Site Metadata table.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $sitemeta;

	/**
	 * Format specifiers for DB columns.
	 *
	 * Columns not listed here default to %s. Initialized during WP load.
	 * Keys are column names, values are format types: 'ID' => '%d'.
	 *
	 * @since 2.8.0
	 *
	 * @see wpdb::prepare()
	 * @see wpdb::insert()
	 * @see wpdb::update()
	 * @see wpdb::delete()
	 * @see wp_set_wpdb_vars()
	 * @var array
	 */
	public $field_types = array();

	/**
	 * Database table columns charset.
	 *
	 * @since 2.2.0
	 *
	 * @var string
	 */
	public $charset;

	/**
	 * Database table columns collate.
	 *
	 * @since 2.2.0
	 *
	 * @var string
	 */
	public $collate;

	/**
	 * Database Username.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	protected $dbuser;

	/**
	 * Database Password.
	 *
	 * @since 3.1.0
	 *
	 * @var string
	 */
	protected $dbpassword;

	/**
	 * Database Name.
	 *
	 * @since 3.1.0
	 *
	 * @var string
	 */
	protected $dbname;

	/**
	 * Database Host.
	 *
	 * @since 3.1.0
	 *
	 * @var string
	 */
	protected $dbhost;

	/**
	 * Database handle.
	 *
	 * Possible values:
	 *
	 * - `mysqli` instance during normal operation
	 * - `null` if the connection is yet to be made or has been closed
	 * - `false` if the connection has failed
	 *
	 * @since 0.71
	 *
	 * @var mysqli|false|null
	 */
	protected $dbh;

	/**
	 * A textual description of the last query/get_row/get_var call.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $func_call;

	/**
	 * Whether MySQL is used as the database engine.
	 *
	 * Set in wpdb::db_connect() to true, by default. This is used when checking
	 * against the required MySQL version for WordPress. Normally, a replacement
	 * database drop-in (db.php) will skip these checks, but setting this to true
	 * will force the checks to occur.
	 *
	 * @since 3.3.0
	 *
	 * @var bool
	 */
	public $is_mysql = null;

	/**
	 * A list of incompatible SQL modes.
	 *
	 * @since 3.9.0
	 *
	 * @var string[]
	 */
	protected $incompatible_modes = array(
		'NO_ZERO_DATE',
		'ONLY_FULL_GROUP_BY',
		'STRICT_TRANS_TABLES',
		'STRICT_ALL_TABLES',
		'TRADITIONAL',
		'ANSI',
	);

	/**
	 * Backward compatibility, where wpdb::prepare() has not quoted formatted/argnum placeholders.
	 *
	 * This is often used for table/field names (before %i was supported), and sometimes string formatting, e.g.
	 *
	 *     $wpdb->prepare( 'WHERE `%1$s` = "%2$s something %3$s" OR %1$s = "%4$-10s"', 'field_1', 'a', 'b', 'c' );
	 *
	 * But it's risky, e.g. forgetting to add quotes, resulting in SQL Injection vulnerabilities:
	 *
	 *     $wpdb->prepare( 'WHERE (id = %1s) OR (id = %2$s)', $_GET['id'], $_GET['id'] ); // ?id=id
	 *
	 * This feature is preserved while plugin authors update their code to use safer approaches:
	 *
	 *     $_GET['key'] = 'a`b';
	 *
	 *     $wpdb->prepare( 'WHERE %1s = %s',        $_GET['key'], $_GET['value'] ); // WHERE a`b = 'value'
	 *     $wpdb->prepare( 'WHERE `%1$s` = "%2$s"', $_GET['key'], $_GET['value'] ); // WHERE `a`b` = "value"
	 *
	 *     $wpdb->prepare( 'WHERE %i = %s',         $_GET['key'], $_GET['value'] ); // WHERE `a``b` = 'value'
	 *
	 * While changing to false will be fine for queries not using formatted/argnum placeholders,
	 * any remaining cases are most likely going to result in SQL errors (good, in a way):
	 *
	 *     $wpdb->prepare( 'WHERE %1$s = "%2$-10s"', 'my_field', 'my_value' );
	 *     true  = WHERE my_field = "my_value  "
	 *     false = WHERE 'my_field' = "'my_value  '"
	 *
	 * But there may be some queries that result in an SQL Injection vulnerability:
	 *
	 *     $wpdb->prepare( 'WHERE id = %1$s', $_GET['id'] ); // ?id=id
	 *
	 * So there may need to be a `_doing_it_wrong()` phase, after we know everyone can use
	 * identifier placeholders (%i), but before this feature is disabled or removed.
	 *
	 * @since 6.2.0
	 * @var bool
	 */
	private $allow_unsafe_unquoted_parameters = true;

	/**
	 * Whether to use the mysqli extension over mysql. This is no longer used as the mysql
	 * extension is no longer supported.
	 *
	 * Default true.
	 *
	 * @since 3.9.0
	 * @since 6.4.0 This property was removed.
	 * @since 6.4.1 This property was reinstated and its default value was changed to true.
	 *              The property is no longer used in core but may be accessed externally.
	 *
	 * @var bool
	 */
	private $use_mysqli = true;

	/**
	 * Whether we've managed to successfully connect at some point.
	 *
	 * @since 3.9.0
	 *
	 * @var bool
	 */
	private $has_connected = false;

	/**
	 * Time when the last query was performed.
	 *
	 * Only set when `SAVEQUERIES` is defined and truthy.
	 *
	 * @since 1.5.0
	 *
	 * @var float
	 */
	public $time_start = null;

	/**
	 * The last SQL error that was encountered.
	 *
	 * @since 2.5.0
	 *
	 * @var WP_Error|string
	 */
	public $error = null;

	/**
	 * Connects to the database server and selects a database.
	 *
	 * Does the actual setting up
	 * of the class properties and connection to the database.
	 *
	 * @since 2.0.8
	 *
	 * @link https://core.trac.wordpress.org/ticket/3354
	 *
	 * @param string $dbuser     Database user.
	 * @param string $dbpassword Database password.
	 * @param string $dbname     Database name.
	 * @param string $dbhost     Database host.
	 */
	public function __construct( $dbuser, $dbpassword, $dbname, $dbhost ) {
		if ( WP_DEBUG && WP_DEBUG_DISPLAY ) {
			$this->show_errors();
		}

		$this->dbuser     = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname     = $dbname;
		$this->dbhost     = $dbhost;

		// wp-config.php creation will manually connect when ready.
		if ( defined( 'WP_SETUP_CONFIG' ) ) {
			return;
		}

		$this->db_connect();
	}

	/**
	 * Makes private properties readable for backward compatibility.
	 *
	 * @since 3.5.0
	 *
	 * @param string $name The private member to get, and optionally process.
	 * @return mixed The private member.
	 */
	public function __get( $name ) {
		if ( 'col_info' === $name ) {
			$this->load_col_info();
		}

		return $this->$name;
	}

	/**
	 * Makes private properties settable for backward compatibility.
	 *
	 * @since 3.5.0
	 *
	 * @param string $name  The private member to set.
	 * @param mixed  $value The value to set.
	 */
	public function __set( $name, $value ) {
		$protected_members = array(
			'col_meta',
			'table_charset',
			'check_current_query',
			'allow_unsafe_unquoted_parameters',
		);
		if ( in_array( $name, $protected_members, true ) ) {
			return;
		}
		$this->$name = $value;
	}

	/**
	 * Makes private properties check-able for backward compatibility.
	 *
	 * @since 3.5.0
	 *
	 * @param string $name The private member to check.
	 * @return bool If the member is set or not.
	 */
	public function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * Makes private properties un-settable for backward compatibility.
	 *
	 * @since 3.5.0
	 *
	 * @param string $name  The private member to unset
	 */
	public function __unset( $name ) {
		unset( $this->$name );
	}

	/**
	 * Sets $this->charset and $this->collate.
	 *
	 * @since 3.1.0
	 */
	public function init_charset() {
		$charset = '';
		$collate = '';

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$charset = 'utf8';
			if ( defined( 'DB_COLLATE' ) && DB_COLLATE ) {
				$collate = DB_COLLATE;
			} else {
				$collate = 'utf8_general_ci';
			}
		} elseif ( defined( 'DB_COLLATE' ) ) {
			$collate = DB_COLLATE;
		}

		if ( defined( 'DB_CHARSET' ) ) {
			$charset = DB_CHARSET;
		}

		$charset_collate = $this->determine_charset( $charset, $collate );

		$this->charset = $charset_collate['charset'];
		$this->collate = $charset_collate['collate'];
	}

	/**
	 * Determines the best charset and collation to use given a charset and collation.
	 *
	 * For example, when able, utf8mb4 should be used instead of utf8.
	 *
	 * @since 4.6.0
	 *
	 * @param string $charset The character set to check.
	 * @param string $collate The collation to check.
	 * @return array {
	 *     The most appropriate character set and collation to use.
	 *
	 *     @type string $charset Character set.
	 *     @type string $collate Collation.
	 * }
	 */
	public function determine_charset( $charset, $collate ) {
		if ( ( ! ( $this->dbh instanceof mysqli ) ) || empty( $this->dbh ) ) {
			return compact( 'charset', 'collate' );
		}

		if ( 'utf8' === $charset ) {
			$charset = 'utf8mb4';
		}

		if ( 'utf8mb4' === $charset ) {
			// _general_ is outdated, so we can upgrade it to _unicode_, instead.
			if ( ! $collate || 'utf8_general_ci' === $collate ) {
				$collate = 'utf8mb4_unicode_ci';
			} else {
				$collate = str_replace( 'utf8_', 'utf8mb4_', $collate );
			}
		}

		// _unicode_520_ is a better collation, we should use that when it's available.
		if ( $this->has_cap( 'utf8mb4_520' ) && 'utf8mb4_unicode_ci' === $collate ) {
			$collate = 'utf8mb4_unicode_520_ci';
		}

		return compact( 'charset', 'collate' );
	}

	/**
	 * Sets the connection's character set.
	 *
	 * @since 3.1.0
	 *
	 * @param mysqli $dbh     The connection returned by `mysqli_connect()`.
	 * @param string $charset Optional. The character set. Default null.
	 * @param string $collate Optional. The collation. Default null.
	 */
	public function set_charset( $dbh, $charset = null, $collate = null ) {
		if ( ! isset( $charset ) ) {
			$charset = $this->charset;
		}
		if ( ! isset( $collate ) ) {
			$collate = $this->collate;
		}
		if ( $this->has_cap( 'collation' ) && ! empty( $charset ) ) {
			$set_charset_succeeded = true;

			if ( function_exists( 'mysqli_set_charset' ) && $this->has_cap( 'set_charset' ) ) {
				$set_charset_succeeded = mysqli_set_charset( $dbh, $charset );
			}

			if ( $set_charset_succeeded ) {
				$query = $this->prepare( 'SET NAMES %s', $charset );
				if ( ! empty( $collate ) ) {
					$query .= $this->prepare( ' COLLATE %s', $collate );
				}
				mysqli_query( $dbh, $query );
			}
		}
	}

	/**
	 * Changes the current SQL mode, and ensures its WordPress compatibility.
	 *
	 * If no modes are passed, it will ensure the current MySQL server modes are compatible.
	 *
	 * @since 3.9.0
	 *
	 * @param array $modes Optional. A list of SQL modes to set. Default empty array.
	 */
	public function set_sql_mode( $modes = array() ) {
		if ( empty( $modes ) ) {
			$res = mysqli_query( $this->dbh, 'SELECT @@SESSION.sql_mode' );

			if ( empty( $res ) ) {
				return;
			}

			$modes_array = mysqli_fetch_array( $res );

			if ( empty( $modes_array[0] ) ) {
				return;
			}

			$modes_str = $modes_array[0];

			if ( empty( $modes_str ) ) {
				return;
			}

			$modes = explode( ',', $modes_str );
		}

		$modes = array_change_key_case( $modes, CASE_UPPER );

		/**
		 * Filters the list of incompatible SQL modes to exclude.
		 *
		 * @since 3.9.0
		 *
		 * @param array $incompatible_modes An array of incompatible modes.
		 */
		$incompatible_modes = (array) apply_filters( 'incompatible_sql_modes', $this->incompatible_modes );

		foreach ( $modes as $i => $mode ) {
			if ( in_array( $mode, $incompatible_modes, true ) ) {
				unset( $modes[ $i ] );
			}
		}

		$modes_str = implode( ',', $modes );

		mysqli_query( $this->dbh, "SET SESSION sql_mode='$modes_str'" );
	}

	/**
	 * Sets the table prefix for the WordPress tables.
	 *
	 * @since 2.5.0
	 *
	 * @param string $prefix          Alphanumeric name for the new prefix.
	 * @param bool   $set_table_names Optional. Whether the table names, e.g. wpdb::$posts,
	 *                                should be updated or not. Default true.
	 * @return string|WP_Error Old prefix or WP_Error on error.
	 */
	public function set_prefix( $prefix, $set_table_names = true ) {

		if ( preg_match( '|[^a-z0-9_]|i', $prefix ) ) {
			return new WP_Error( 'invalid_db_prefix', 'Invalid database prefix' );
		}

		$old_prefix = is_multisite() ? '' : $prefix;

		if ( isset( $this->base_prefix ) ) {
			$old_prefix = $this->base_prefix;
		}

		$this->base_prefix = $prefix;

		if ( $set_table_names ) {
			foreach ( $this->tables( 'global' ) as $table => $prefixed_table ) {
				$this->$table = $prefixed_table;
			}

			if ( is_multisite() && empty( $this->blogid ) ) {
				return $old_prefix;
			}

			$this->prefix = $this->get_blog_prefix();

			foreach ( $this->tables( 'blog' ) as $table => $prefixed_table ) {
				$this->$table = $prefixed_table;
			}

			foreach ( $this->tables( 'old' ) as $table => $prefixed_table ) {
				$this->$table = $prefixed_table;
			}
		}
		return $old_prefix;
	}

	/**
	 * Sets blog ID.
	 *
	 * @since 3.0.0
	 *
	 * @param int $blog_id
	 * @param int $network_id Optional. Network ID. Default 0.
	 * @return int Previous blog ID.
	 */
	public function set_blog_id( $blog_id, $network_id = 0 ) {
		if ( ! empty( $network_id ) ) {
			$this->siteid = $network_id;
		}

		$old_blog_id  = $this->blogid;
		$this->blogid = $blog_id;

		$this->prefix = $this->get_blog_prefix();

		foreach ( $this->tables( 'blog' ) as $table => $prefixed_table ) {
			$this->$table = $prefixed_table;
		}

		foreach ( $this->tables( 'old' ) as $table => $prefixed_table ) {
			$this->$table = $prefixed_table;
		}

		return $old_blog_id;
	}

	/**
	 * Gets blog prefix.
	 *
	 * @since 3.0.0
	 *
	 * @param int $blog_id Optional. Blog ID to retrieve the table prefix for.
	 *                     Defaults to the current blog ID.
	 * @return string Blog prefix.
	 */
	public function get_blog_prefix( $blog_id = null ) {
		if ( is_multisite() ) {
			if ( null === $blog_id ) {
				$blog_id = $this->blogid;
			}

			$blog_id = (int) $blog_id;

			if ( defined( 'MULTISITE' ) && ( 0 === $blog_id || 1 === $blog_id ) ) {
				return $this->base_prefix;
			} else {
				return $this->base_prefix . $blog_id . '_';
			}
		} else {
			return $this->base_prefix;
		}
	}

	/**
	 * Returns an array of WordPress tables.
	 *
	 * Also allows for the `CUSTOM_USER_TABLE` and `CUSTOM_USER_META_TABLE` to override the WordPress users
	 * and usermeta tables that would otherwise be determined by the prefix.
	 *
	 * The `$scope` argument can take one of the following:
	 *
	 * - 'all' - returns 'all' and 'global' tables. No old tables are returned.
	 * - 'blog' - returns the blog-level tables for the queried blog.
	 * - 'global' - returns the global tables for the installation, returning multisite tables only on multisite.
	 * - 'ms_global' - returns the multisite global tables, regardless if current installation is multisite.
	 * - 'old' - returns tables which are deprecated.
	 *
	 * @since 3.0.0
	 * @since 6.1.0 `old` now includes deprecated multisite global tables only on multisite.
	 *
	 * @uses wpdb::$tables
	 * @uses wpdb::$old_tables
	 * @uses wpdb::$global_tables
	 * @uses wpdb::$ms_global_tables
	 * @uses wpdb::$old_ms_global_tables
	 *
	 * @param string $scope   Optional. Possible values include 'all', 'global', 'ms_global', 'blog',
	 *                        or 'old' tables. Default 'all'.
	 * @param bool   $prefix  Optional. Whether to include table prefixes. If blog prefix is requested,
	 *                        then the custom users and usermeta tables will be mapped. Default true.
	 * @param int    $blog_id Optional. The blog_id to prefix. Used only when prefix is requested.
	 *                        Defaults to `wpdb::$blogid`.
	 * @return string[] Table names. When a prefix is requested, the key is the unprefixed table name.
	 */
	public function tables( $scope = 'all', $prefix = true, $blog_id = 0 ) {
		switch ( $scope ) {
			case 'all':
				$tables = array_merge( $this->global_tables, $this->tables );
				if ( is_multisite() ) {
					$tables = array_merge( $tables, $this->ms_global_tables );
				}
				break;
			case 'blog':
				$tables = $this->tables;
				break;
			case 'global':
				$tables = $this->global_tables;
				if ( is_multisite() ) {
					$tables = array_merge( $tables, $this->ms_global_tables );
				}
				break;
			case 'ms_global':
				$tables = $this->ms_global_tables;
				break;
			case 'old':
				$tables = $this->old_tables;
				if ( is_multisite() ) {
					$tables = array_merge( $tables, $this->old_ms_global_tables );
				}
				break;
			default:
				return array();
		}

		if ( $prefix ) {
			if ( ! $blog_id ) {
				$blog_id = $this->blogid;
			}
			$blog_prefix   = $this->get_blog_prefix( $blog_id );
			$base_prefix   = $this->base_prefix;
			$global_tables = array_merge( $this->global_tables, $this->ms_global_tables );
			foreach ( $tables as $k => $table ) {
				if ( in_array( $table, $global_tables, true ) ) {
					$tables[ $table ] = $base_prefix . $table;
				} else {
					$tables[ $table ] = $blog_prefix . $table;
				}
				unset( $tables[ $k ] );
			}

			if ( isset( $tables['users'] ) && defined( 'CUSTOM_USER_TABLE' ) ) {
				$tables['users'] = CUSTOM_USER_TABLE;
			}

			if ( isset( $tables['usermeta'] ) && defined( 'CUSTOM_USER_META_TABLE' ) ) {
				$tables['usermeta'] = CUSTOM_USER_META_TABLE;
			}
		}

		return $tables;
	}

	/**
	 * Selects a database using the current or provided database connection.
	 *
	 * The database name will be changed based on the current database connection.
	 * On failure, the execution will bail and display a DB error.
	 *
	 * @since 0.71
	 *
	 * @param string $db  Database name.
	 * @param mysqli $dbh Optional. Database connection.
	 *                    Defaults to the current database handle.
	 */
	public function select( $db, $dbh = null ) {
		if ( is_null( $dbh ) ) {
			$dbh = $this->dbh;
		}

		$success = mysqli_select_db( $dbh, $db );

		if ( ! $success ) {
			$this->ready = false;
			if ( ! did_action( 'template_redirect' ) ) {
				wp_load_translations_early();

				$message = '<h1>' . __( 'Cannot select database' ) . "</h1>\n";

				$message .= '<p>' . sprintf(
					/* translators: %s: Database name. */
					__( 'The database server could be connected to (which means your username and password is okay) but the %s database could not be selected.' ),
					'<code>' . htmlspecialchars( $db, ENT_QUOTES ) . '</code>'
				) . "</p>\n";

				$message .= "<ul>\n";
				$message .= '<li>' . __( 'Are you sure it exists?' ) . "</li>\n";

				$message .= '<li>' . sprintf(
					/* translators: 1: Database user, 2: Database name. */
					__( 'Does the user %1$s have permission to use the %2$s database?' ),
					'<code>' . htmlspecialchars( $this->dbuser, ENT_QUOTES ) . '</code>',
					'<code>' . htmlspecialchars( $db, ENT_QUOTES ) . '</code>'
				) . "</li>\n";

				$message .= '<li>' . sprintf(
					/* translators: %s: Database name. */
					__( 'On some systems the name of your database is prefixed with your username, so it would be like <code>username_%1$s</code>. Could that be the problem?' ),
					htmlspecialchars( $db, ENT_QUOTES )
				) . "</li>\n";

				$message .= "</ul>\n";

				$message .= '<p>' . sprintf(
					/* translators: %s: Support forums URL. */
					__( 'If you do not know how to set up a database you should <strong>contact your host</strong>. If all else fails you may find help at the <a href="%s">WordPress support forums</a>.' ),
					__( 'https://wordpress.org/support/forums/' )
				) . "</p>\n";

				$this->bail( $message, 'db_select_fail' );
			}
		}
	}

	/**
	 * Do not use, deprecated.
	 *
	 * Use esc_sql() or wpdb::prepare() instead.
	 *
	 * @since 2.8.0
	 * @deprecated 3.6.0 Use wpdb::prepare()
	 * @see wpdb::prepare()
	 * @see esc_sql()
	 *
	 * @param string $data
	 * @return string
	 */
	public function _weak_escape( $data ) {
		if ( func_num_args() === 1 && function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __METHOD__, '3.6.0', 'wpdb::prepare() or esc_sql()' );
		}
		return addslashes( $data );
	}

	/**
	 * Real escape using mysqli_real_escape_string().
	 *
	 * @since 2.8.0
	 *
	 * @see mysqli_real_escape_string()
	 *
	 * @param string $data String to escape.
	 * @return string Escaped string.
	 */
	public function _real_escape( $data ) {
		if ( ! is_scalar( $data ) ) {
			return '';
		}

		if ( $this->dbh ) {
			$escaped = mysqli_real_escape_string( $this->dbh, $data );
		} else {
			$class = get_class( $this );

			wp_load_translations_early();
			/* translators: %s: Database access abstraction class, usually wpdb or a class extending wpdb. */
			_doing_it_wrong( $class, sprintf( __( '%s must set a database connection for use with escaping.' ), $class ), '3.6.0' );

			$escaped = addslashes( $data );
		}

		return $this->add_placeholder_escape( $escaped );
	}

	/**
	 * Escapes data. Works on arrays.
	 *
	 * @since 2.8.0
	 *
	 * @uses wpdb::_real_escape()
	 *
	 * @param string|array $data Data to escape.
	 * @return string|array Escaped data, in the same type as supplied.
	 */
	public function _escape( $data ) {
		if ( is_array( $data ) ) {
			foreach ( $data as $k => $v ) {
				if ( is_array( $v ) ) {
					$data[ $k ] = $this->_escape( $v );
				} else {
					$data[ $k ] = $this->_real_escape( $v );
				}
			}
		} else {
			$data = $this->_real_escape( $data );
		}

		return $data;
	}

	/**
	 * Do not use, deprecated.
	 *
	 * Use esc_sql() or wpdb::prepare() instead.
	 *
	 * @since 0.71
	 * @deprecated 3.6.0 Use wpdb::prepare()
	 * @see wpdb::prepare()
	 * @see esc_sql()
	 *
	 * @param string|array $data Data to escape.
	 * @return string|array Escaped data, in the same type as supplied.
	 */
	public function escape( $data ) {
		if ( func_num_args() === 1 && function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __METHOD__, '3.6.0', 'wpdb::prepare() or esc_sql()' );
		}
		if ( is_array( $data ) ) {
			foreach ( $data as $k => $v ) {
				if ( is_array( $v ) ) {
					$data[ $k ] = $this->escape( $v, 'recursive' );
				} else {
					$data[ $k ] = $this->_weak_escape( $v, 'internal' );
				}
			}
		} else {
			$data = $this->_weak_escape( $data, 'internal' );
		}

		return $data;
	}

	/**
	 * Escapes content by reference for insertion into the database, for security.
	 *
	 * @uses wpdb::_real_escape()
	 *
	 * @since 2.3.0
	 *
	 * @param string $data String to escape.
	 */
	public function escape_by_ref( &$data ) {
		if ( ! is_float( $data ) ) {
			$data = $this->_real_escape( $data );
		}
	}

	/**
	 * Quotes an identifier for a MySQL database, e.g. table/field names.
	 *
	 * @since 6.2.0
	 *
	 * @param string $identifier Identifier to escape.
	 * @return string Escaped identifier.
	 */
	public function quote_identifier( $identifier ) {
		return '`' . $this->_escape_identifier_value( $identifier ) . '`';
	}

	/**
	 * Escapes an identifier value without adding the surrounding quotes.
	 *
	 * - Permitted characters in quoted identifiers include the full Unicode
	 *   Basic Multilingual Plane (BMP), except U+0000.
	 * - To quote the identifier itself, you need to double the character, e.g. `a``b`.
	 *
	 * @since 6.2.0
	 *
	 * @link https://dev.mysql.com/doc/refman/8.0/en/identifiers.html
	 *
	 * @param string $identifier Identifier to escape.
	 * @return string Escaped identifier.
	 */
	private function _escape_identifier_value( $identifier ) {
		return str_replace( '`', '``', $identifier );
	}

	/**
	 * Prepares a SQL query for safe execution.
	 *
	 * Uses `sprintf()`-like syntax. The following placeholders can be used in the query string:
	 *
	 * - `%d` (integer)
	 * - `%f` (float)
	 * - `%s` (string)
	 * - `%i` (identifier, e.g. table/field names)
	 *
	 * All placeholders MUST be left unquoted in the query string. A corresponding argument
	 * MUST be passed for each placeholder.
	 *
	 * Note: There is one exception to the above: for compatibility with old behavior,
	 * numbered or formatted string placeholders (eg, `%1$s`, `%5s`) will not have quotes
	 * added by this function, so should be passed with appropriate quotes around them.
	 *
	 * Literal percentage signs (`%`) in the query string must be written as `%%`. Percentage wildcards
	 * (for example, to use in LIKE syntax) must be passed via a substitution argument containing
	 * the complete LIKE string, these cannot be inserted directly in the query string.
	 * Also see wpdb::esc_like().
	 *
	 * Arguments may be passed as individual arguments to the method, or as a single array
	 * containing all arguments. A combination of the two is not supported.
	 *
	 * Examples:
	 *
	 *     $wpdb->prepare(
	 *         "SELECT * FROM `table` WHERE `column` = %s AND `field` = %d OR `other_field` LIKE %s",
	 *         array( 'foo', 1337, '%bar' )
	 *     );
	 *
	 *     $wpdb->prepare(
	 *         "SELECT DATE_FORMAT(`field`, '%%c') FROM `table` WHERE `column` = %s",
	 *         'foo'
	 *     );
	 *
	 * @since 2.3.0
	 * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
	 *              by updating the function signature. The second parameter was changed
	 *              from `$args` to `...$args`.
	 * @since 6.2.0 Added `%i` for identifiers, e.g. table or field names.
	 *              Check support via `wpdb::has_cap( 'identifier_placeholders' )`.
	 *              This preserves compatibility with `sprintf()`, as the C version uses
	 *              `%d` and `$i` as a signed integer, whereas PHP only supports `%d`.
	 *
	 * @link https://www.php.net/sprintf Description of syntax.
	 *
	 * @param string      $query   Query statement with `sprintf()`-like placeholders.
	 * @param array|mixed $args    The array of variables to substitute into the query's placeholders
	 *                             if being called with an array of arguments, or the first variable
	 *                             to substitute into the query's placeholders if being called with
	 *                             individual arguments.
	 * @param mixed       ...$args Further variables to substitute into the query's placeholders
	 *                             if being called with individual arguments.
	 * @return string|void Sanitized query string, if there is a query to prepare.
	 */
	public function prepare( $query, ...$args ) {
		if ( is_null( $query ) ) {
			return;
		}

		/*
		 * This is not meant to be foolproof -- but it will catch obviously incorrect usage.
		 *
		 * Note: str_contains() is not used here, as this file can be included
		 * directly outside of WordPress core, e.g. by HyperDB, in which case
		 * the polyfills from wp-includes/compat.php are not loaded.
		 */
		if ( false === strpos( $query, '%' ) ) {
			wp_load_translations_early();
			_doing_it_wrong(
				'wpdb::prepare',
				sprintf(
					/* translators: %s: wpdb::prepare() */
					__( 'The query argument of %s must have a placeholder.' ),
					'wpdb::prepare()'
				),
				'3.9.0'
			);
		}

		/*
		 * Specify the formatting allowed in a placeholder. The following are allowed:
		 *
		 * - Sign specifier, e.g. $+d
		 * - Numbered placeholders, e.g. %1$s
		 * - Padding specifier, including custom padding characters, e.g. %05s, %'#5s
		 * - Alignment specifier, e.g. %05-s
		 * - Precision specifier, e.g. %.2f
		 */
		$allowed_format = '(?:[1-9][0-9]*[$])?[-+0-9]*(?: |0|\'.)?[-+0-9]*(?:\.[0-9]+)?';

		/*
		 * If a %s placeholder already has quotes around it, removing the existing quotes
		 * and re-inserting them ensures the quotes are consistent.
		 *
		 * For backward compatibility, this is only applied to %s, and not to placeholders like %1$s,
		 * which are frequently used in the middle of longer strings, or as table name placeholders.
		 */
		$query = str_replace( "'%s'", '%s', $query ); // Strip any existing single quotes.
		$query = str_replace( '"%s"', '%s', $query ); // Strip any existing double quotes.

		// Escape any unescaped percents (i.e. anything unrecognised).
		$query = preg_replace( "/%(?:%|$|(?!($allowed_format)?[sdfFi]))/", '%%\\1', $query );

		// Extract placeholders from the query.
		$split_query = preg_split( "/(^|[^%]|(?:%%)+)(%(?:$allowed_format)?[sdfFi])/", $query, -1, PREG_SPLIT_DELIM_CAPTURE );

		$split_query_count = count( $split_query );

		/*
		 * Split always returns with 1 value before the first placeholder (even with $query = "%s"),
		 * then 3 additional values per placeholder.
		 */
		$placeholder_count = ( ( $split_query_count - 1 ) / 3 );

		// If args were passed as an array, as in vsprintf(), move them up.
		$passed_as_array = ( isset( $args[0] ) && is_array( $args[0] ) && 1 === count( $args ) );
		if ( $passed_as_array ) {
			$args = $args[0];
		}

		$new_query       = '';
		$key             = 2; // Keys 0 and 1 in $split_query contain values before the first placeholder.
		$arg_id          = 0;
		$arg_identifiers = array();
		$arg_strings     = array();

		while ( $key < $split_query_count ) {
			$placeholder = $split_query[ $key ];

			$format = substr( $placeholder, 1, -1 );
			$type   = substr( $placeholder, -1 );

			if ( 'f' === $type && true === $this->allow_unsafe_unquoted_parameters
				/*
				 * Note: str_ends_with() is not used here, as this file can be included
				 * directly outside of WordPress core, e.g. by HyperDB, in which case
				 * the polyfills from wp-includes/compat.php are not loaded.
				 */
				&& '%' === substr( $split_query[ $key - 1 ], -1, 1 )
			) {

				/*
				 * Before WP 6.2 the "force floats to be locale-unaware" RegEx didn't
				 * convert "%%%f" to "%%%F" (note the uppercase F).
				 * This was because it didn't check to see if the leading "%" was escaped.
				 * And because the "Escape any unescaped percents" RegEx used "[sdF]" in its
				 * negative lookahead assertion, when there was an odd number of "%", it added
				 * an extra "%", to give the fully escaped "%%%%f" (not a placeholder).
				 */

				$s = $split_query[ $key - 2 ] . $split_query[ $key - 1 ];
				$k = 1;
				$l = strlen( $s );
				while ( $k <= $l && '%' === $s[ $l - $k ] ) {
					++$k;
				}

				$placeholder = '%' . ( $k % 2 ? '%' : '' ) . $format . $type;

				--$placeholder_count;

			} else {

				// Force floats to be locale-unaware.
				if ( 'f' === $type ) {
					$type        = 'F';
					$placeholder = '%' . $format . $type;
				}

				if ( 'i' === $type ) {
					$placeholder = '`%' . $format . 's`';
					// Using a simple strpos() due to previous checking (e.g. $allowed_format).
					$argnum_pos = strpos( $format, '$' );

					if ( false !== $argnum_pos ) {
						// sprintf() argnum starts at 1, $arg_id from 0.
						$arg_identifiers[] = ( ( (int) substr( $format, 0, $argnum_pos ) ) - 1 );
					} else {
						$arg_identifiers[] = $arg_id;
					}
				} elseif ( 'd' !== $type && 'F' !== $type ) {
					/*
					 * i.e. ( 's' === $type ), where 'd' and 'F' keeps $placeholder unchanged,
					 * and we ensure string escaping is used as a safe default (e.g. even if 'x').
					 */
					$argnum_pos = strpos( $format, '$' );

					if ( false !== $argnum_pos ) {
						$arg_strings[] = ( ( (int) substr( $format, 0, $argnum_pos ) ) - 1 );
					} else {
						$arg_strings[] = $arg_id;
					}

					/*
					 * Unquoted strings for backward compatibility (dangerous).
					 * First, "numbered or formatted string placeholders (eg, %1$s, %5s)".
					 * Second, if "%s" has a "%" before it, even if it's unrelated (e.g. "LIKE '%%%s%%'").
					 */
					if ( true !== $this->allow_unsafe_unquoted_parameters
						/*
						 * Note: str_ends_with() is not used here, as this file can be included
						 * directly outside of WordPress core, e.g. by HyperDB, in which case
						 * the polyfills from wp-includes/compat.php are not loaded.
						 */
						|| ( '' === $format && '%' !== substr( $split_query[ $key - 1 ], -1, 1 ) )
					) {
						$placeholder = "'%" . $format . "s'";
					}
				}
			}

			// Glue (-2), any leading characters (-1), then the new $placeholder.
			$new_query .= $split_query[ $key - 2 ] . $split_query[ $key - 1 ] . $placeholder;

			$key += 3;
			++$arg_id;
		}

		// Replace $query; and add remaining $query characters, or index 0 if there were no placeholders.
		$query = $new_query . $split_query[ $key - 2 ];

		$dual_use = array_intersect( $arg_identifiers, $arg_strings );

		if ( count( $dual_use ) > 0 ) {
			wp_load_translations_early();

			$used_placeholders = array();

			$key    = 2;
			$arg_id = 0;
			// Parse again (only used when there is an error).
			while ( $key < $split_query_count ) {
				$placeholder = $split_query[ $key ];

				$format = substr( $placeholder, 1, -1 );

				$argnum_pos = strpos( $format, '$' );

				if ( false !== $argnum_pos ) {
					$arg_pos = ( ( (int) substr( $format, 0, $argnum_pos ) ) - 1 );
				} else {
					$arg_pos = $arg_id;
				}

				$used_placeholders[ $arg_pos ][] = $placeholder;

				$key += 3;
				++$arg_id;
			}

			$conflicts = array();
			foreach ( $dual_use as $arg_pos ) {
				$conflicts[] = implode( ' and ', $used_placeholders[ $arg_pos ] );
			}

			_doing_it_wrong(
				'wpdb::prepare',
				sprintf(
					/* translators: %s: A list of placeholders found to be a problem. */
					__( 'Arguments cannot be prepared as both an Identifier and Value. Found the following conflicts: %s' ),
					implode( ', ', $conflicts )
				),
				'6.2.0'
			);

			return;
		}

		$args_count = count( $args );

		if ( $args_count !== $placeholder_count ) {
			if ( 1 === $placeholder_count && $passed_as_array ) {
				/*
				 * If the passed query only expected one argument,
				 * but the wrong number of arguments was sent as an array, bail.
				 */
				wp_load_translations_early();
				_doing_it_wrong(
					'wpdb::prepare',
					__( 'The query only expected one placeholder, but an array of multiple placeholders was sent.' ),
					'4.9.0'
				);

				return;
			} else {
				/*
				 * If we don't have the right number of placeholders,
				 * but they were passed as individual arguments,
				 * or we were expecting multiple arguments in an array, throw a warning.
				 */
				wp_load_translations_early();
				_doing_it_wrong(
					'wpdb::prepare',
					sprintf(
						/* translators: 1: Number of placeholders, 2: Number of arguments passed. */
						__( 'The query does not contain the correct number of placeholders (%1$d) for the number of arguments passed (%2$d).' ),
						$placeholder_count,
						$args_count
					),
					'4.8.3'
				);

				/*
				 * If we don't have enough arguments to match the placeholders,
				 * return an empty string to avoid a fatal error on PHP 8.
				 */
				if ( $args_count < $placeholder_count ) {
					$max_numbered_placeholder = 0;

					for ( $i = 2, $l = $split_query_count; $i < $l; $i += 3 ) {
						// Assume a leading number is for a numbered placeholder, e.g. '%3$s'.
						$argnum = (int) substr( $split_query[ $i ], 1 );

						if ( $max_numbered_placeholder < $argnum ) {
							$max_numbered_placeholder = $argnum;
						}
					}

					if ( ! $max_numbered_placeholder || $args_count < $max_numbered_placeholder ) {
						return '';
					}
				}
			}
		}

		$args_escaped = array();

		foreach ( $args as $i => $value ) {
			if ( in_array( $i, $arg_identifiers, true ) ) {
				$args_escaped[] = $this->_escape_identifier_value( $value );
			} elseif ( is_int( $value ) || is_float( $value ) ) {
				$args_escaped[] = $value;
			} else {
				if ( ! is_scalar( $value ) && ! is_null( $value ) ) {
					wp_load_translations_early();
					_doing_it_wrong(
						'wpdb::prepare',
						sprintf(
							/* translators: %s: Value type. */
							__( 'Unsupported value type (%s).' ),
							gettype( $value )
						),
						'4.8.2'
					);

					// Preserving old behavior, where values are escaped as strings.
					$value = '';
				}

				$args_escaped[] = $this->_real_escape( $value );
			}
		}

		$query = vsprintf( $query, $args_escaped );

		return $this->add_placeholder_escape( $query );
	}

	/**
	 * First half of escaping for `LIKE` special characters `%` and `_` before preparing for SQL.
	 *
	 * Use this only before wpdb::prepare() or esc_sql(). Reversing the order is very bad for security.
	 *
	 * Example Prepared Statement:
	 *
	 *     $wild = '%';
	 *     $find = 'only 43% of planets';
	 *     $like = $wild . $wpdb->esc_like( $find ) . $wild;
	 *     $sql  = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE %s", $like );
	 *
	 * Example Escape Chain:
	 *
	 *     $sql  = esc_sql( $wpdb->esc_like( $input ) );
	 *
	 * @since 4.0.0
	 *
	 * @param string $text The raw text to be escaped. The input typed by the user
	 *                     should have no extra or deleted slashes.
	 * @return string Text in the form of a LIKE phrase. The output is not SQL safe.
	 *                Call wpdb::prepare() or wpdb::_real_escape() next.
	 */
	public function esc_like( $text ) {
		return addcslashes( $text, '_%\\' );
	}

	/**
	 * Prints SQL/DB error.
	 *
	 * @since 0.71
	 *
	 * @global array $EZSQL_ERROR Stores error information of query and error string.
	 *
	 * @param string $str The error to display.
	 * @return void|false Void if the showing of errors is enabled, false if disabled.
	 */
	public function print_error( $str = '' ) {
		global $EZSQL_ERROR;

		if ( ! $str ) {
			$str = mysqli_error( $this->dbh );
		}

		$EZSQL_ERROR[] = array(
			'query'     => $this->last_query,
			'error_str' => $str,
		);

		if ( $this->suppress_errors ) {
			return false;
		}

		$caller = $this->get_caller();
		if ( $caller ) {
			// Not translated, as this will only appear in the error log.
			$error_str = sprintf( 'WordPress database error %1$s for query %2$s made by %3$s', $str, $this->last_query, $caller );
		} else {
			$error_str = sprintf( 'WordPress database error %1$s for query %2$s', $str, $this->last_query );
		}

		error_log( $error_str );

		// Are we showing errors?
		if ( ! $this->show_errors ) {
			return false;
		}

		wp_load_translations_early();

		// If there is an error then take note of it.
		if ( is_multisite() ) {
			$msg = sprintf(
				"%s [%s]\n%s\n",
				__( 'WordPress database error:' ),
				$str,
				$this->last_query
			);

			if ( defined( 'ERRORLOGFILE' ) ) {
				error_log( $msg, 3, ERRORLOGFILE );
			}
			if ( defined( 'DIEONDBERROR' ) ) {
				wp_die( $msg );
			}
		} else {
			$str   = htmlspecialchars( $str, ENT_QUOTES );
			$query = htmlspecialchars( $this->last_query, ENT_QUOTES );

			printf(
				'<div id="error"><p class="wpdberror"><strong>%s</strong> [%s]<br /><code>%s</code></p></div>',
				__( 'WordPress database error:' ),
				$str,
				$query
			);
		}
	}

	/**
	 * Enables showing of database errors.
	 *
	 * This function should be used only to enable showing of errors.
	 * wpdb::hide_errors() should be used instead for hiding errors.
	 *
	 * @since 0.71
	 *
	 * @see wpdb::hide_errors()
	 *
	 * @param bool $show Optional. Whether to show errors. Default true.
	 * @return bool Whether showing of errors was previously active.
	 */
	public function show_errors( $show = true ) {
		$errors            = $this->show_errors;
		$this->show_errors = $show;
		return $errors;
	}

	/**
	 * Disables showing of database errors.
	 *
	 * By default database errors are not shown.
	 *
	 * @since 0.71
	 *
	 * @see wpdb::show_errors()
	 *
	 * @return bool Whether showing of errors was previously active.
	 */
	public function hide_errors() {
		$show              = $this->show_errors;
		$this->show_errors = false;
		return $show;
	}

	/**
	 * Enables or disables suppressing of database errors.
	 *
	 * By default database errors are suppressed.
	 *
	 * @since 2.5.0
	 *
	 * @see wpdb::hide_errors()
	 *
	 * @param bool $suppress Optional. Whether to suppress errors. Default true.
	 * @return bool Whether suppressing of errors was previously active.
	 */
	public function suppress_errors( $suppress = true ) {
		$errors                = $this->suppress_errors;
		$this->suppress_errors = (bool) $suppress;
		return $errors;
	}

	/**
	 * Kills cached query results.
	 *
	 * @since 0.71
	 */
	public function flush() {
		$this->last_result   = array();
		$this->col_info      = null;
		$this->last_query    = null;
		$this->rows_affected = 0;
		$this->num_rows      = 0;
		$this->last_error    = '';

		if ( $this->result instanceof mysqli_result ) {
			mysqli_free_result( $this->result );
			$this->result = null;

			// Confidence check before using the handle.
			if ( empty( $this->dbh ) || ! ( $this->dbh instanceof mysqli ) ) {
				return;
			}

			// Clear out any results from a multi-query.
			while ( mysqli_more_results( $this->dbh ) ) {
				mysqli_next_result( $this->dbh );
			}
		}
	}

	/**
	 * Connects to and selects database.
	 *
	 * If `$allow_bail` is false, the lack of database connection will need to be handled manually.
	 *
	 * @since 3.0.0
	 * @since 3.9.0 $allow_bail parameter added.
	 *
	 * @param bool $allow_bail Optional. Allows the function to bail. Default true.
	 * @return bool True with a successful connection, false on failure.
	 */
	public function db_connect( $allow_bail = true ) {
		$this->is_mysql = true;

		$client_flags = defined( 'MYSQL_CLIENT_FLAGS' ) ? MYSQL_CLIENT_FLAGS : 0;

		/*
		 * Set the MySQLi error reporting off because WordPress handles its own.
		 * This is due to the default value change from `MYSQLI_REPORT_OFF`
		 * to `MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT` in PHP 8.1.
		 */
		mysqli_report( MYSQLI_REPORT_OFF );

		$this->dbh = mysqli_init();

		$host    = $this->dbhost;
		$port    = null;
		$socket  = null;
		$is_ipv6 = false;

		$host_data = $this->parse_db_host( $this->dbhost );
		if ( $host_data ) {
			list( $host, $port, $socket, $is_ipv6 ) = $host_data;
		}

		/*
		 * If using the `mysqlnd` library, the IPv6 address needs to be enclosed
		 * in square brackets, whereas it doesn't while using the `libmysqlclient` library.
		 * @see https://bugs.php.net/bug.php?id=67563
		 */
		if ( $is_ipv6 && extension_loaded( 'mysqlnd' ) ) {
			$host = "[$host]";
		}

		if ( WP_DEBUG ) {
			mysqli_real_connect( $this->dbh, $host, $this->dbuser, $this->dbpassword, null, $port, $socket, $client_flags );
		} else {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@mysqli_real_connect( $this->dbh, $host, $this->dbuser, $this->dbpassword, null, $port, $socket, $client_flags );
		}

		if ( $this->dbh->connect_errno ) {
			$this->dbh = null;
		}

		if ( ! $this->dbh && $allow_bail ) {
			wp_load_translations_early();

			// Load custom DB error template, if present.
			if ( file_exists( WP_CONTENT_DIR . '/db-error.php' ) ) {
				require_once WP_CONTENT_DIR . '/db-error.php';
				die();
			}

			$message = '<h1>' . __( 'Error establishing a database connection' ) . "</h1>\n";

			$message .= '<p>' . sprintf(
				/* translators: 1: wp-config.php, 2: Database host. */
				__( 'This either means that the username and password information in your %1$s file is incorrect or that contact with the database server at %2$s could not be established. This could mean your host&#8217;s database server is down.' ),
				'<code>wp-config.php</code>',
				'<code>' . htmlspecialchars( $this->dbhost, ENT_QUOTES ) . '</code>'
			) . "</p>\n";

			$message .= "<ul>\n";
			$message .= '<li>' . __( 'Are you sure you have the correct username and password?' ) . "</li>\n";
			$message .= '<li>' . __( 'Are you sure you have typed the correct hostname?' ) . "</li>\n";
			$message .= '<li>' . __( 'Are you sure the database server is running?' ) . "</li>\n";
			$message .= "</ul>\n";

			$message .= '<p>' . sprintf(
				/* translators: %s: Support forums URL. */
				__( 'If you are unsure what these terms mean you should probably contact your host. If you still need help you can always visit the <a href="%s">WordPress support forums</a>.' ),
				__( 'https://wordpress.org/support/forums/' )
			) . "</p>\n";

			$this->bail( $message, 'db_connect_fail' );

			return false;
		} elseif ( $this->dbh ) {
			if ( ! $this->has_connected ) {
				$this->init_charset();
			}

			$this->has_connected = true;

			$this->set_charset( $this->dbh );

			$this->ready = true;
			$this->set_sql_mode();
			$this->select( $this->dbname, $this->dbh );

			return true;
		}

		return false;
	}

	/**
	 * Parses the DB_HOST setting to interpret it for mysqli_real_connect().
	 *
	 * mysqli_real_connect() doesn't support the host param including a port or socket
	 * like mysql_connect() does. This duplicates how mysql_connect() detects a port
	 * and/or socket file.
	 *
	 * @since 4.9.0
	 *
	 * @param string $host The DB_HOST setting to parse.
	 * @return array|false {
	 *     Array containing the host, the port, the socket and
	 *     whether it is an IPv6 address, in that order.
	 *     False if the host couldn't be parsed.
	 *
	 *     @type string      $0 Host name.
	 *     @type string|null $1 Port.
	 *     @type string|null $2 Socket.
	 *     @type bool        $3 Whether it is an IPv6 address.
	 * }
	 */
	public function parse_db_host( $host ) {
		$socket  = null;
		$is_ipv6 = false;

		// First peel off the socket parameter from the right, if it exists.
		$socket_pos = strpos( $host, ':/' );
		if ( false !== $socket_pos ) {
			$socket = substr( $host, $socket_pos + 1 );
			$host   = substr( $host, 0, $socket_pos );
		}

		/*
		 * We need to check for an IPv6 address first.
		 * An IPv6 address will always contain at least two colons.
		 */
		if ( substr_count( $host, ':' ) > 1 ) {
			$pattern = '#^(?:\[)?(?P<host>[0-9a-fA-F:]+)(?:\]:(?P<port>[\d]+))?#';
			$is_ipv6 = true;
		} else {
			// We seem to be dealing with an IPv4 address.
			$pattern = '#^(?P<host>[^:/]*)(?::(?P<port>[\d]+))?#';
		}

		$matches = array();
		$result  = preg_match( $pattern, $host, $matches );

		if ( 1 !== $result ) {
			// Couldn't parse the address, bail.
			return false;
		}

		$host = ! empty( $matches['host'] ) ? $matches['host'] : '';
		// MySQLi port cannot be a string; must be null or an integer.
		$port = ! empty( $matches['port'] ) ? absint( $matches['port'] ) : null;

		return array( $host, $port, $socket, $is_ipv6 );
	}

	/**
	 * Checks that the connection to the database is still up. If not, try to reconnect.
	 *
	 * If this function is unable to reconnect, it will forcibly die, or if called
	 * after the {@see 'template_redirect'} hook has been fired, return false instead.
	 *
	 * If `$allow_bail` is false, the lack of database connection will need to be handled manually.
	 *
	 * @since 3.9.0
	 *
	 * @param bool $allow_bail Optional. Allows the function to bail. Default true.
	 * @return bool|void True if the connection is up.
	 */
	public function check_connection( $allow_bail = true ) {
		// Check if the connection is alive.
		if ( ! empty( $this->dbh ) && mysqli_query( $this->dbh, 'DO 1' ) !== false ) {
			return true;
		}

		$error_reporting = false;

		// Disable warnings, as we don't want to see a multitude of "unable to connect" messages.
		if ( WP_DEBUG ) {
			$error_reporting = error_reporting();
			error_reporting( $error_reporting & ~E_WARNING );
		}

		for ( $tries = 1; $tries <= $this->reconnect_retries; $tries++ ) {
			/*
			 * On the last try, re-enable warnings. We want to see a single instance
			 * of the "unable to connect" message on the bail() screen, if it appears.
			 */
			if ( $this->reconnect_retries === $tries && WP_DEBUG ) {
				error_reporting( $error_reporting );
			}

			if ( $this->db_connect( false ) ) {
				if ( $error_reporting ) {
					error_reporting( $error_reporting );
				}

				return true;
			}

			sleep( 1 );
		}

		/*
		 * If template_redirect has already happened, it's too late for wp_die()/dead_db().
		 * Let's just return and hope for the best.
		 */
		if ( did_action( 'template_redirect' ) ) {
			return false;
		}

		if ( ! $allow_bail ) {
			return false;
		}

		wp_load_translations_early();

		$message = '<h1>' . __( 'Error reconnecting to the database' ) . "</h1>\n";

		$message .= '<p>' . sprintf(
			/* translators: %s: Database host. */
			__( 'This means that the contact with the database server at %s was lost. This could mean your host&#8217;s database server is down.' ),
			'<code>' . htmlspecialchars( $this->dbhost, ENT_QUOTES ) . '</code>'
		) . "</p>\n";

		$message .= "<ul>\n";
		$message .= '<li>' . __( 'Are you sure the database server is running?' ) . "</li>\n";
		$message .= '<li>' . __( 'Are you sure the database server is not under particularly heavy load?' ) . "</li>\n";
		$message .= "</ul>\n";

		$message .= '<p>' . sprintf(
			/* translators: %s: Support forums URL. */
			__( 'If you are unsure what these terms mean you should probably contact your host. If you still need help you can always visit the <a href="%s">WordPress support forums</a>.' ),
			__( 'https://wordpress.org/support/forums/' )
		) . "</p>\n";

		// We weren't able to reconnect, so we better bail.
		$this->bail( $message, 'db_connect_fail' );

		/*
		 * Call dead_db() if bail didn't die, because this database is no more.
		 * It has ceased to be (at least temporarily).
		 */
		dead_db();
	}

	/**
	 * Performs a database query, using current database connection.
	 *
	 * More information can be found on the documentation page.
	 *
	 * @since 0.71
	 *
	 * @link https://developer.wordpress.org/reference/classes/wpdb/
	 *
	 * @param string $query Database query.
	 * @return int|bool Boolean true for CREATE, ALTER, TRUNCATE and DROP queries. Number of rows
	 *                  affected/selected for all other queries. Boolean false on error.
	 */
	public function query( $query ) {
		if ( ! $this->ready ) {
			$this->check_current_query = true;
			return false;
		}

		/**
		 * Filters the database query.
		 *
		 * Some queries are made before the plugins have been loaded,
		 * and thus cannot be filtered with this method.
		 *
		 * @since 2.1.0
		 *
		 * @param string $query Database query.
		 */
		$query = apply_filters( 'query', $query );

		if ( ! $query ) {
			$this->insert_id = 0;
			return false;
		}

		$this->flush();

		// Log how the function was called.
		$this->func_call = "\$db->query(\"$query\")";

		// If we're writing to the database, make sure the query will write safely.
		if ( $this->check_current_query && ! $this->check_ascii( $query ) ) {
			$stripped_query = $this->strip_invalid_text_from_query( $query );
			/*
			 * strip_invalid_text_from_query() can perform queries, so we need
			 * to flush again, just to make sure everything is clear.
			 */
			$this->flush();
			if ( $stripped_query !== $query ) {
				$this->insert_id  = 0;
				$this->last_query = $query;

				wp_load_translations_early();

				$this->last_error = __( 'WordPress database error: Could not perform query because it contains invalid data.' );

				return false;
			}
		}

		$this->check_current_query = true;

		// Keep track of the last query for debug.
		$this->last_query = $query;

		$this->_do_query( $query );

		// Database server has gone away, try to reconnect.
		$mysql_errno = 0;

		if ( $this->dbh instanceof mysqli ) {
			$mysql_errno = mysqli_errno( $this->dbh );
		} else {
			/*
			 * $dbh is defined, but isn't a real connection.
			 * Something has gone horribly wrong, let's try a reconnect.
			 */
			$mysql_errno = 2006;
		}

		if ( empty( $this->dbh ) || 2006 === $mysql_errno ) {
			if ( $this->check_connection() ) {
				$this->_do_query( $query );
			} else {
				$this->insert_id = 0;
				return false;
			}
		}

		// If there is an error then take note of it.
		if ( $this->dbh instanceof mysqli ) {
			$this->last_error = mysqli_error( $this->dbh );
		} else {
			$this->last_error = __( 'Unable to retrieve the error message from MySQL' );
		}

		if ( $this->last_error ) {
			// Clear insert_id on a subsequent failed insert.
			if ( $this->insert_id && preg_match( '/^\s*(insert|replace)\s/i', $query ) ) {
				$this->insert_id = 0;
			}

			$this->print_error();
			return false;
		}

		if ( preg_match( '/^\s*(create|alter|truncate|drop)\s/i', $query ) ) {
			$return_val = $this->result;
		} elseif ( preg_match( '/^\s*(insert|delete|update|replace)\s/i', $query ) ) {
			$this->rows_affected = mysqli_affected_rows( $this->dbh );

			// Take note of the insert_id.
			if ( preg_match( '/^\s*(insert|replace)\s/i', $query ) ) {
				$this->insert_id = mysqli_insert_id( $this->dbh );
			}

			// Return number of rows affected.
			$return_val = $this->rows_affected;
		} else {
			$num_rows = 0;

			if ( $this->result instanceof mysqli_result ) {
				while ( $row = mysqli_fetch_object( $this->result ) ) {
					$this->last_result[ $num_rows ] = $row;
					++$num_rows;
				}
			}

			// Log and return the number of rows selected.
			$this->num_rows = $num_rows;
			$return_val     = $num_rows;
		}

		return $return_val;
	}

	/**
	 * Internal function to perform the mysqli_query() call.
	 *
	 * @since 3.9.0
	 *
	 * @see wpdb::query()
	 *
	 * @param string $query The query to run.
	 */
	private function _do_query( $query ) {
		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
			$this->timer_start();
		}

		if ( ! empty( $this->dbh ) ) {
			$this->result = mysqli_query( $this->dbh, $query );
		}

		++$this->num_queries;

		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
			$this->log_query(
				$query,
				$this->timer_stop(),
				$this->get_caller(),
				$this->time_start,
				array()
			);
		}
	}

	/**
	 * Logs query data.
	 *
	 * @since 5.3.0
	 *
	 * @param string $query           The query's SQL.
	 * @param float  $query_time      Total time spent on the query, in seconds.
	 * @param string $query_callstack Comma-separated list of the calling functions.
	 * @param float  $query_start     Unix timestamp of the time at the start of the query.
	 * @param array  $query_data      Custom query data.
	 */
	public function log_query( $query, $query_time, $query_callstack, $query_start, $query_data ) {
		/**
		 * Filters the custom data to log alongside a query.
		 *
		 * Caution should be used when modifying any of this data, it is recommended that any additional
		 * information you need to store about a query be added as a new associative array element.
		 *
		 * @since 5.3.0
		 *
		 * @param array  $query_data      Custom query data.
		 * @param string $query           The query's SQL.
		 * @param float  $query_time      Total time spent on the query, in seconds.
		 * @param string $query_callstack Comma-separated list of the calling functions.
		 * @param float  $query_start     Unix timestamp of the time at the start of the query.
		 */
		$query_data = apply_filters( 'log_query_custom_data', $query_data, $query, $query_time, $query_callstack, $query_start );

		$this->queries[] = array(
			$query,
			$query_time,
			$query_callstack,
			$query_start,
			$query_data,
		);
	}

	/**
	 * Generates and returns a placeholder escape string for use in queries returned by ::prepare().
	 *
	 * @since 4.8.3
	 *
	 * @return string String to escape placeholders.
	 */
	public function placeholder_escape() {
		static $placeholder;

		if ( ! $placeholder ) {
			// If ext/hash is not present, compat.php's hash_hmac() does not support sha256.
			$algo = function_exists( 'hash' ) ? 'sha256' : 'sha1';
			// Old WP installs may not have AUTH_SALT defined.
			$salt = defined( 'AUTH_SALT' ) && AUTH_SALT ? AUTH_SALT : (string) rand();

			$placeholder = '{' . hash_hmac( $algo, uniqid( $salt, true ), $salt ) . '}';
		}

		/*
		 * Add the filter to remove the placeholder escaper. Uses priority 0, so that anything
		 * else attached to this filter will receive the query with the placeholder string removed.
		 */
		if ( false === has_filter( 'query', array( $this, 'remove_placeholder_escape' ) ) ) {
			add_filter( 'query', array( $this, 'remove_placeholder_escape' ), 0 );
		}

		return $placeholder;
	}

	/**
	 * Adds a placeholder escape string, to escape anything that resembles a printf() placeholder.
	 *
	 * @since 4.8.3
	 *
	 * @param string $query The query to escape.
	 * @return string The query with the placeholder escape string inserted where necessary.
	 */
	public function add_placeholder_escape( $query ) {
		/*
		 * To prevent returning anything that even vaguely resembles a placeholder,
		 * we clobber every % we can find.
		 */
		return str_replace( '%', $this->placeholder_escape(), $query );
	}

	/**
	 * Removes the placeholder escape strings from a query.
	 *
	 * @since 4.8.3
	 *
	 * @param string $query The query from which the placeholder will be removed.
	 * @return string The query with the placeholder removed.
	 */
	public function remove_placeholder_escape( $query ) {
		return str_replace( $this->placeholder_escape(), '%', $query );
	}

	/**
	 * Inserts a row into the table.
	 *
	 * Examples:
	 *
	 *     $wpdb->insert(
	 *         'table',
	 *         array(
	 *             'column1' => 'foo',
	 *             'column2' => 'bar',
	 *         )
	 *     );
	 *     $wpdb->insert(
	 *         'table',
	 *         array(
	 *             'column1' => 'foo',
	 *             'column2' => 1337,
	 *         ),
	 *         array(
	 *             '%s',
	 *             '%d',
	 *         )
	 *     );
	 *
	 * @since 2.5.0
	 *
	 * @see wpdb::prepare()
	 * @see wpdb::$field_types
	 * @see wp_set_wpdb_vars()
	 *
	 * @param string          $table  Table name.
	 * @param array           $data   Data to insert (in column => value pairs).
	 *                                Both `$data` columns and `$data` values should be "raw" (neither should be SQL escaped).
	 *                                Sending a null value will cause the column to be set to NULL - the corresponding
	 *                                format is ignored in this case.
	 * @param string[]|string $format Optional. An array of formats to be mapped to each of the value in `$data`.
	 *                                If string, that format will be used for all of the values in `$data`.
	 *                                A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                If omitted, all values in `$data` will be treated as strings unless otherwise
	 *                                specified in wpdb::$field_types. Default null.
	 * @return int|false The number of rows inserted, or false on error.
	 */
	public function insert( $table, $data, $format = null ) {
		return $this->_insert_replace_helper( $table, $data, $format, 'INSERT' );
	}

	/**
	 * Replaces a row in the table or inserts it if it does not exist, based on a PRIMARY KEY or a UNIQUE index.
	 *
	 * A REPLACE works exactly like an INSERT, except that if an old row in the table has the same value as a new row
	 * for a PRIMARY KEY or a UNIQUE index, the old row is deleted before the new row is inserted.
	 *
	 * Examples:
	 *
	 *     $wpdb->replace(
	 *         'table',
	 *         array(
	 *             'ID'      => 123,
	 *             'column1' => 'foo',
	 *             'column2' => 'bar',
	 *         )
	 *     );
	 *     $wpdb->replace(
	 *         'table',
	 *         array(
	 *             'ID'      => 456,
	 *             'column1' => 'foo',
	 *             'column2' => 1337,
	 *         ),
	 *         array(
	 *             '%d',
	 *             '%s',
	 *             '%d',
	 *         )
	 *     );
	 *
	 * @since 3.0.0
	 *
	 * @see wpdb::prepare()
	 * @see wpdb::$field_types
	 * @see wp_set_wpdb_vars()
	 *
	 * @param string          $table  Table name.
	 * @param array           $data   Data to insert (in column => value pairs).
	 *                                Both `$data` columns and `$data` values should be "raw" (neither should be SQL escaped).
	 *                                A primary key or unique index is required to perform a replace operation.
	 *                                Sending a null value will cause the column to be set to NULL - the corresponding
	 *                                format is ignored in this case.
	 * @param string[]|string $format Optional. An array of formats to be mapped to each of the value in `$data`.
	 *                                If string, that format will be used for all of the values in `$data`.
	 *                                A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                If omitted, all values in `$data` will be treated as strings unless otherwise
	 *                                specified in wpdb::$field_types. Default null.
	 * @return int|false The number of rows affected, or false on error.
	 */
	public function replace( $table, $data, $format = null ) {
		return $this->_insert_replace_helper( $table, $data, $format, 'REPLACE' );
	}

	/**
	 * Helper function for insert and replace.
	 *
	 * Runs an insert or replace query based on `$type` argument.
	 *
	 * @since 3.0.0
	 *
	 * @see wpdb::prepare()
	 * @see wpdb::$field_types
	 * @see wp_set_wpdb_vars()
	 *
	 * @param string          $table  Table name.
	 * @param array           $data   Data to insert (in column => value pairs).
	 *                                Both `$data` columns and `$data` values should be "raw" (neither should be SQL escaped).
	 *                                Sending a null value will cause the column to be set to NULL - the corresponding
	 *                                format is ignored in this case.
	 * @param string[]|string $format Optional. An array of formats to be mapped to each of the value in `$data`.
	 *                                If string, that format will be used for all of the values in `$data`.
	 *                                A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                If omitted, all values in `$data` will be treated as strings unless otherwise
	 *                                specified in wpdb::$field_types. Default null.
	 * @param string          $type   Optional. Type of operation. Either 'INSERT' or 'REPLACE'.
	 *                                Default 'INSERT'.
	 * @return int|false The number of rows affected, or false on error.
	 */
	public function _insert_replace_helper( $table, $data, $format = null, $type = 'INSERT' ) {
		$this->insert_id = 0;

		if ( ! in_array( strtoupper( $type ), array( 'REPLACE', 'INSERT' ), true ) ) {
			return false;
		}

		$data = $this->process_fields( $table, $data, $format );
		if ( false === $data ) {
			return false;
		}

		$formats = array();
		$values  = array();
		foreach ( $data as $value ) {
			if ( is_null( $value['value'] ) ) {
				$formats[] = 'NULL';
				continue;
			}

			$formats[] = $value['format'];
			$values[]  = $value['value'];
		}

		$fields  = '`' . implode( '`, `', array_keys( $data ) ) . '`';
		$formats = implode( ', ', $formats );

		$sql = "$type INTO `$table` ($fields) VALUES ($formats)";

		$this->check_current_query = false;
		return $this->query( $this->prepare( $sql, $values ) );
	}

	/**
	 * Updates a row in the table.
	 *
	 * Examples:
	 *
	 *     $wpdb->update(
	 *         'table',
	 *         array(
	 *             'column1' => 'foo',
	 *             'column2' => 'bar',
	 *         ),
	 *         array(
	 *             'ID' => 1,
	 *         )
	 *     );
	 *     $wpdb->update(
	 *         'table',
	 *         array(
	 *             'column1' => 'foo',
	 *             'column2' => 1337,
	 *         ),
	 *         array(
	 *             'ID' => 1,
	 *         ),
	 *         array(
	 *             '%s',
	 *             '%d',
	 *         ),
	 *         array(
	 *             '%d',
	 *         )
	 *     );
	 *
	 * @since 2.5.0
	 *
	 * @see wpdb::prepare()
	 * @see wpdb::$field_types
	 * @see wp_set_wpdb_vars()
	 *
	 * @param string       $table           Table name.
	 * @param array        $data            Data to update (in column => value pairs).
	 *                                      Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 *                                      Sending a null value will cause the column to be set to NULL - the corresponding
	 *                                      format is ignored in this case.
	 * @param array        $where           A named array of WHERE clauses (in column => value pairs).
	 *                                      Multiple clauses will be joined with ANDs.
	 *                                      Both $where columns and $where values should be "raw".
	 *                                      Sending a null value will create an IS NULL comparison - the corresponding
	 *                                      format will be ignored in this case.
	 * @param string[]|string $format       Optional. An array of formats to be mapped to each of the values in $data.
	 *                                      If string, that format will be used for all of the values in $data.
	 *                                      A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                      If omitted, all values in $data will be treated as strings unless otherwise
	 *                                      specified in wpdb::$field_types. Default null.
	 * @param string[]|string $where_format Optional. An array of formats to be mapped to each of the values in $where.
	 *                                      If string, that format will be used for all of the items in $where.
	 *                                      A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                      If omitted, all values in $where will be treated as strings unless otherwise
	 *                                      specified in wpdb::$field_types. Default null.
	 * @return int|false The number of rows updated, or false on error.
	 */
	public function update( $table, $data, $where, $format = null, $where_format = null ) {
		if ( ! is_array( $data ) || ! is_array( $where ) ) {
			return false;
		}

		$data = $this->process_fields( $table, $data, $format );
		if ( false === $data ) {
			return false;
		}
		$where = $this->process_fields( $table, $where, $where_format );
		if ( false === $where ) {
			return false;
		}

		$fields     = array();
		$conditions = array();
		$values     = array();
		foreach ( $data as $field => $value ) {
			if ( is_null( $value['value'] ) ) {
				$fields[] = "`$field` = NULL";
				continue;
			}

			$fields[] = "`$field` = " . $value['format'];
			$values[] = $value['value'];
		}
		foreach ( $where as $field => $value ) {
			if ( is_null( $value['value'] ) ) {
				$conditions[] = "`$field` IS NULL";
				continue;
			}

			$conditions[] = "`$field` = " . $value['format'];
			$values[]     = $value['value'];
		}

		$fields     = implode( ', ', $fields );
		$conditions = implode( ' AND ', $conditions );

		$sql = "UPDATE `$table` SET $fields WHERE $conditions";

		$this->check_current_query = false;
		return $this->query( $this->prepare( $sql, $values ) );
	}

	/**
	 * Deletes a row in the table.
	 *
	 * Examples:
	 *
	 *     $wpdb->delete(
	 *         'table',
	 *         array(
	 *             'ID' => 1,
	 *         )
	 *     );
	 *     $wpdb->delete(
	 *         'table',
	 *         array(
	 *             'ID' => 1,
	 *         ),
	 *         array(
	 *             '%d',
	 *         )
	 *     );
	 *
	 * @since 3.4.0
	 *
	 * @see wpdb::prepare()
	 * @see wpdb::$field_types
	 * @see wp_set_wpdb_vars()
	 *
	 * @param string          $table        Table name.
	 * @param array           $where        A named array of WHERE clauses (in column => value pairs).
	 *                                      Multiple clauses will be joined with ANDs.
	 *                                      Both $where columns and $where values should be "raw".
	 *                                      Sending a null value will create an IS NULL comparison - the corresponding
	 *                                      format will be ignored in this case.
	 * @param string[]|string $where_format Optional. An array of formats to be mapped to each of the values in $where.
	 *                                      If string, that format will be used for all of the items in $where.
	 *                                      A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                      If omitted, all values in $data will be treated as strings unless otherwise
	 *                                      specified in wpdb::$field_types. Default null.
	 * @return int|false The number of rows deleted, or false on error.
	 */
	public function delete( $table, $where, $where_format = null ) {
		if ( ! is_array( $where ) ) {
			return false;
		}

		$where = $this->process_fields( $table, $where, $where_format );
		if ( false === $where ) {
			return false;
		}

		$conditions = array();
		$values     = array();
		foreach ( $where as $field => $value ) {
			if ( is_null( $value['value'] ) ) {
				$conditions[] = "`$field` IS NULL";
				continue;
			}

			$conditions[] = "`$field` = " . $value['format'];
			$values[]     = $value['value'];
		}

		$conditions = implode( ' AND ', $conditions );

		$sql = "DELETE FROM `$table` WHERE $conditions";

		$this->check_current_query = false;
		return $this->query( $this->prepare( $sql, $values ) );
	}

	/**
	 * Processes arrays of field/value pairs and field formats.
	 *
	 * This is a helper method for wpdb's CRUD methods, which take field/value pairs
	 * for inserts, updates, and where clauses. This method first pairs each value
	 * with a format. Then it determines the charset of that field, using that
	 * to determine if any invalid text would be stripped. If text is stripped,
	 * then field processing is rejected and the query fails.
	 *
	 * @since 4.2.0
	 *
	 * @param string          $table  Table name.
	 * @param array           $data   Array of values keyed by their field names.
	 * @param string[]|string $format Formats or format to be mapped to the values in the data.
	 * @return array|false An array of fields that contain paired value and formats.
	 *                     False for invalid values.
	 */
	protected function process_fields( $table, $data, $format ) {
		$data = $this->process_field_formats( $data, $format );
		if ( false === $data ) {
			return false;
		}

		$data = $this->process_field_charsets( $data, $table );
		if ( false === $data ) {
			return false;
		}

		$data = $this->process_field_lengths( $data, $table );
		if ( false === $data ) {
			return false;
		}

		$converted_data = $this->strip_invalid_text( $data );

		if ( $data !== $converted_data ) {

			$problem_fields = array();
			foreach ( $data as $field => $value ) {
				if ( $value !== $converted_data[ $field ] ) {
					$problem_fields[] = $field;
				}
			}

			wp_load_translations_early();

			if ( 1 === count( $problem_fields ) ) {
				$this->last_error = sprintf(
					/* translators: %s: Database field where the error occurred. */
					__( 'WordPress database error: Processing the value for the following field failed: %s. The supplied value may be too long or contains invalid data.' ),
					reset( $problem_fields )
				);
			} else {
				$this->last_error = sprintf(
					/* translators: %s: Database fields where the error occurred. */
					__( 'WordPress database error: Processing the values for the following fields failed: %s. The supplied values may be too long or contain invalid data.' ),
					implode( ', ', $problem_fields )
				);
			}

			return false;
		}

		return $data;
	}

	/**
	 * Prepares arrays of value/format pairs as passed to wpdb CRUD methods.
	 *
	 * @since 4.2.0
	 *
	 * @param array           $data   Array of values keyed by their field names.
	 * @param string[]|string $format Formats or format to be mapped to the values in the data.
	 * @return array {
	 *     Array of values and formats keyed by their field names.
	 *
	 *     @type mixed  $value  The value to be formatted.
	 *     @type string $format The format to be mapped to the value.
	 * }
	 */
	protected function process_field_formats( $data, $format ) {
		$formats          = (array) $format;
		$original_formats = $formats;

		foreach ( $data as $field => $value ) {
			$value = array(
				'value'  => $value,
				'format' => '%s',
			);

			if ( ! empty( $format ) ) {
				$value['format'] = array_shift( $formats );
				if ( ! $value['format'] ) {
					$value['format'] = reset( $original_formats );
				}
			} elseif ( isset( $this->field_types[ $field ] ) ) {
				$value['format'] = $this->field_types[ $field ];
			}

			$data[ $field ] = $value;
		}

		return $data;
	}

	/**
	 * Adds field charsets to field/value/format arrays generated by wpdb::process_field_formats().
	 *
	 * @since 4.2.0
	 *
	 * @param array $data {
	 *     Array of values and formats keyed by their field names,
	 *     as it comes from the wpdb::process_field_formats() method.
	 *
	 *     @type array ...$0 {
	 *         Value and format for this field.
	 *
	 *         @type mixed  $value  The value to be formatted.
	 *         @type string $format The format to be mapped to the value.
	 *     }
	 * }
	 * @param string $table Table name.
	 * @return array|false {
	 *     The same array of data with additional 'charset' keys, or false if
	 *     the charset for the table cannot be found.
	 *
	 *     @type array ...$0 {
	 *         Value, format, and charset for this field.
	 *
	 *         @type mixed        $value   The value to be formatted.
	 *         @type string       $format  The format to be mapped to the value.
	 *         @type string|false $charset The charset to be used for the value.
	 *     }
	 * }
	 */
	protected function process_field_charsets( $data, $table ) {
		foreach ( $data as $field => $value ) {
			if ( '%d' === $value['format'] || '%f' === $value['format'] ) {
				/*
				 * We can skip this field if we know it isn't a string.
				 * This checks %d/%f versus ! %s because its sprintf() could take more.
				 */
				$value['charset'] = false;
			} else {
				$value['charset'] = $this->get_col_charset( $table, $field );
				if ( is_wp_error( $value['charset'] ) ) {
					return false;
				}
			}

			$data[ $field ] = $value;
		}

		return $data;
	}

	/**
	 * For string fields, records the maximum string length that field can safely save.
	 *
	 * @since 4.2.1
	 *
	 * @param array $data {
	 *     Array of values, formats, and charsets keyed by their field names,
	 *     as it comes from the wpdb::process_field_charsets() method.
	 *
	 *     @type array ...$0 {
	 *         Value, format, and charset for this field.
	 *
	 *         @type mixed        $value   The value to be formatted.
	 *         @type string       $format  The format to be mapped to the value.
	 *         @type string|false $charset The charset to be used for the value.
	 *     }
	 * }
	 * @param string $table Table name.
	 * @return array|false {
	 *     The same array of data with additional 'length' keys, or false if
	 *     information for the table cannot be found.
	 *
	 *     @type array ...$0 {
	 *         Value, format, charset, and length for this field.
	 *
	 *         @type mixed        $value   The value to be formatted.
	 *         @type string       $format  The format to be mapped to the value.
	 *         @type string|false $charset The charset to be used for the value.
	 *         @type array|false  $length  {
	 *             Information about the maximum length of the value.
	 *             False if the column has no length.
	 *
	 *             @type string $type   One of 'byte' or 'char'.
	 *             @type int    $length The column length.
	 *         }
	 *     }
	 * }
	 */
	protected function process_field_lengths( $data, $table ) {
		foreach ( $data as $field => $value ) {
			if ( '%d' === $value['format'] || '%f' === $value['format'] ) {
				/*
				 * We can skip this field if we know it isn't a string.
				 * This checks %d/%f versus ! %s because its sprintf() could take more.
				 */
				$value['length'] = false;
			} else {
				$value['length'] = $this->get_col_length( $table, $field );
				if ( is_wp_error( $value['length'] ) ) {
					return false;
				}
			}

			$data[ $field ] = $value;
		}

		return $data;
	}

	/**
	 * Retrieves one value from the database.
	 *
	 * Executes a SQL query and returns the value from the SQL result.
	 * If the SQL result contains more than one column and/or more than one row,
	 * the value in the column and row specified is returned. If $query is null,
	 * the value in the specified column and row from the previous SQL result is returned.
	 *
	 * @since 0.71
	 *
	 * @param string|null $query Optional. SQL query. Defaults to null, use the result from the previous query.
	 * @param int         $x     Optional. Column of value to return. Indexed from 0. Default 0.
	 * @param int         $y     Optional. Row of value to return. Indexed from 0. Default 0.
	 * @return string|null Database query result (as string), or null on failure.
	 */
	public function get_var( $query = null, $x = 0, $y = 0 ) {
		$this->func_call = "\$db->get_var(\"$query\", $x, $y)";

		if ( $query ) {
			if ( $this->check_current_query && $this->check_safe_collation( $query ) ) {
				$this->check_current_query = false;
			}

			$this->query( $query );
		}

		// Extract var out of cached results based on x,y vals.
		if ( ! empty( $this->last_result[ $y ] ) ) {
			$values = array_values( get_object_vars( $this->last_result[ $y ] ) );
		}

		// If there is a value return it, else return null.
		return ( isset( $values[ $x ] ) && '' !== $values[ $x ] ) ? $values[ $x ] : null;
	}

	/**
	 * Retrieves one row from the database.
	 *
	 * Executes a SQL query and returns the row from the SQL result.
	 *
	 * @since 0.71
	 *
	 * @param string|null $query  SQL query.
	 * @param string      $output Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
	 *                            correspond to an stdClass object, an associative array, or a numeric array,
	 *                            respectively. Default OBJECT.
	 * @param int         $y      Optional. Row to return. Indexed from 0. Default 0.
	 * @return array|object|null|void Database query result in format specified by $output or null on failure.
	 */
	public function get_row( $query = null, $output = OBJECT, $y = 0 ) {
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";

		if ( $query ) {
			if ( $this->check_current_query && $this->check_safe_collation( $query ) ) {
				$this->check_current_query = false;
			}

			$this->query( $query );
		} else {
			return null;
		}

		if ( ! isset( $this->last_result[ $y ] ) ) {
			return null;
		}

		if ( OBJECT === $output ) {
			return $this->last_result[ $y ] ? $this->last_result[ $y ] : null;
		} elseif ( ARRAY_A === $output ) {
			return $this->last_result[ $y ] ? get_object_vars( $this->last_result[ $y ] ) : null;
		} elseif ( ARRAY_N === $output ) {
			return $this->last_result[ $y ] ? array_values( get_object_vars( $this->last_result[ $y ] ) ) : null;
		} elseif ( OBJECT === strtoupper( $output ) ) {
			// Back compat for OBJECT being previously case-insensitive.
			return $this->last_result[ $y ] ? $this->last_result[ $y ] : null;
		} else {
			$this->print_error( ' $db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N' );
		}
	}

	/**
	 * Retrieves one column from the database.
	 *
	 * Executes a SQL query and returns the column from the SQL result.
	 * If the SQL result contains more than one column, the column specified is returned.
	 * If $query is null, the specified column from the previous SQL result is returned.
	 *
	 * @since 0.71
	 *
	 * @param string|null $query Optional. SQL query. Defaults to previous query.
	 * @param int         $x     Optional. Column to return. Indexed from 0. Default 0.
	 * @return array Database query result. Array indexed from 0 by SQL result row number.
	 */
	public function get_col( $query = null, $x = 0 ) {
		if ( $query ) {
			if ( $this->check_current_query && $this->check_safe_collation( $query ) ) {
				$this->check_current_query = false;
			}

			$this->query( $query );
		}

		$new_array = array();
		// Extract the column values.
		if ( $this->last_result ) {
			for ( $i = 0, $j = count( $this->last_result ); $i < $j; $i++ ) {
				$new_array[ $i ] = $this->get_var( null, $x, $i );
			}
		}
		return $new_array;
	}

	/**
	 * Retrieves an entire SQL result set from the database (i.e., many rows).
	 *
	 * Executes a SQL query and returns the entire SQL result.
	 *
	 * @since 0.71
	 *
	 * @param string $query  SQL query.
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 *                       With one of the first three, return an array of rows indexed
	 *                       from 0 by SQL result row number. Each row is an associative array
	 *                       (column => value, ...), a numerically indexed array (0 => value, ...),
	 *                       or an object ( ->column = value ), respectively. With OBJECT_K,
	 *                       return an associative array of row objects keyed by the value
	 *                       of each row's first column's value. Duplicate keys are discarded.
	 *                       Default OBJECT.
	 * @return array|object|null Database query results.
	 */
	public function get_results( $query = null, $output = OBJECT ) {
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		if ( $query ) {
			if ( $this->check_current_query && $this->check_safe_collation( $query ) ) {
				$this->check_current_query = false;
			}

			$this->query( $query );
		} else {
			return null;
		}

		$new_array = array();
		if ( OBJECT === $output ) {
			// Return an integer-keyed array of row objects.
			return $this->last_result;
		} elseif ( OBJECT_K === $output ) {
			/*
			 * Return an array of row objects with keys from column 1.
			 * (Duplicates are discarded.)
			 */
			if ( $this->last_result ) {
				foreach ( $this->last_result as $row ) {
					$var_by_ref = get_object_vars( $row );
					$key        = array_shift( $var_by_ref );
					if ( ! isset( $new_array[ $key ] ) ) {
						$new_array[ $key ] = $row;
					}
				}
			}
			return $new_array;
		} elseif ( ARRAY_A === $output || ARRAY_N === $output ) {
			// Return an integer-keyed array of...
			if ( $this->last_result ) {
				if ( ARRAY_N === $output ) {
					foreach ( (array) $this->last_result as $row ) {
						// ...integer-keyed row arrays.
						$new_array[] = array_values( get_object_vars( $row ) );
					}
				} else {
					foreach ( (array) $this->last_result as $row ) {
						// ...column name-keyed row arrays.
						$new_array[] = get_object_vars( $row );
					}
				}
			}
			return $new_array;
		} elseif ( strtoupper( $output ) === OBJECT ) {
			// Back compat for OBJECT being previously case-insensitive.
			return $this->last_result;
		}
		return null;
	}

	/**
	 * Retrieves the character set for the given table.
	 *
	 * @since 4.2.0
	 *
	 * @param string $table Table name.
	 * @return string|WP_Error Table character set, WP_Error object if it couldn't be found.
	 */
	protected function get_table_charset( $table ) {
		$tablekey = strtolower( $table );

		/**
		 * Filters the table charset value before the DB is checked.
		 *
		 * Returning a non-null value from the filter will effectively short-circuit
		 * checking the DB for the charset, returning that value instead.
		 *
		 * @since 4.2.0
		 *
		 * @param string|WP_Error|null $charset The character set to use, WP_Error object
		 *                                      if it couldn't be found. Default null.
		 * @param string               $table   The name of the table being checked.
		 */
		$charset = apply_filters( 'pre_get_table_charset', null, $table );
		if ( null !== $charset ) {
			return $charset;
		}

		if ( isset( $this->table_charset[ $tablekey ] ) ) {
			return $this->table_charset[ $tablekey ];
		}

		$charsets = array();
		$columns  = array();

		$table_parts = explode( '.', $table );
		$table       = '`' . implode( '`.`', $table_parts ) . '`';
		$results     = $this->get_results( "SHOW FULL COLUMNS FROM $table" );
		if ( ! $results ) {
			return new WP_Error( 'wpdb_get_table_charset_failure', __( 'Could not retrieve table charset.' ) );
		}

		foreach ( $results as $column ) {
			$columns[ strtolower( $column->Field ) ] = $column;
		}

		$this->col_meta[ $tablekey ] = $columns;

		foreach ( $columns as $column ) {
			if ( ! empty( $column->Collation ) ) {
				list( $charset ) = explode( '_', $column->Collation );

				$charsets[ strtolower( $charset ) ] = true;
			}

			list( $type ) = explode( '(', $column->Type );

			// A binary/blob means the whole query gets treated like this.
			if ( in_array( strtoupper( $type ), array( 'BINARY', 'VARBINARY', 'TINYBLOB', 'MEDIUMBLOB', 'BLOB', 'LONGBLOB' ), true ) ) {
				$this->table_charset[ $tablekey ] = 'binary';
				return 'binary';
			}
		}

		// utf8mb3 is an alias for utf8.
		if ( isset( $charsets['utf8mb3'] ) ) {
			$charsets['utf8'] = true;
			unset( $charsets['utf8mb3'] );
		}

		// Check if we have more than one charset in play.
		$count = count( $charsets );
		if ( 1 === $count ) {
			$charset = key( $charsets );
		} elseif ( 0 === $count ) {
			// No charsets, assume this table can store whatever.
			$charset = false;
		} else {
			// More than one charset. Remove latin1 if present and recalculate.
			unset( $charsets['latin1'] );
			$count = count( $charsets );
			if ( 1 === $count ) {
				// Only one charset (besides latin1).
				$charset = key( $charsets );
			} elseif ( 2 === $count && isset( $charsets['utf8'], $charsets['utf8mb4'] ) ) {
				// Two charsets, but they're utf8 and utf8mb4, use utf8.
				$charset = 'utf8';
			} else {
				// Two mixed character sets. ascii.
				$charset = 'ascii';
			}
		}

		$this->table_charset[ $tablekey ] = $charset;
		return $charset;
	}

	/**
	 * Retrieves the character set for the given column.
	 *
	 * @since 4.2.0
	 *
	 * @param string $table  Table name.
	 * @param string $column Column name.
	 * @return string|false|WP_Error Column character set as a string. False if the column has
	 *                               no character set. WP_Error object if there was an error.
	 */
	public function get_col_charset( $table, $column ) {
		$tablekey  = strtolower( $table );
		$columnkey = strtolower( $column );

		/**
		 * Filters the column charset value before the DB is checked.
		 *
		 * Passing a non-null value to the filter will short-circuit
		 * checking the DB for the charset, returning that value instead.
		 *
		 * @since 4.2.0
		 *
		 * @param string|null|false|WP_Error $charset The character set to use. Default null.
		 * @param string                     $table   The name of the table being checked.
		 * @param string                     $column  The name of the column being checked.
		 */
		$charset = apply_filters( 'pre_get_col_charset', null, $table, $column );
		if ( null !== $charset ) {
			return $charset;
		}

		// Skip this entirely if this isn't a MySQL database.
		if ( empty( $this->is_mysql ) ) {
			return false;
		}

		if ( empty( $this->table_charset[ $tablekey ] ) ) {
			// This primes column information for us.
			$table_charset = $this->get_table_charset( $table );
			if ( is_wp_error( $table_charset ) ) {
				return $table_charset;
			}
		}

		// If still no column information, return the table charset.
		if ( empty( $this->col_meta[ $tablekey ] ) ) {
			return $this->table_charset[ $tablekey ];
		}

		// If this column doesn't exist, return the table charset.
		if ( empty( $this->col_meta[ $tablekey ][ $columnkey ] ) ) {
			return $this->table_charset[ $tablekey ];
		}

		// Return false when it's not a string column.
		if ( empty( $this->col_meta[ $tablekey ][ $columnkey ]->Collation ) ) {
			return false;
		}

		list( $charset ) = explode( '_', $this->col_meta[ $tablekey ][ $columnkey ]->Collation );
		return $charset;
	}

	/**
	 * Retrieves the maximum string length allowed in a given column.
	 *
	 * The length may either be specified as a byte length or a character length.
	 *
	 * @since 4.2.1
	 *
	 * @param string $table  Table name.
	 * @param string $column Column name.
	 * @return array|false|WP_Error {
	 *     Array of column length information, false if the column has no length (for
	 *     example, numeric column), WP_Error object if there was an error.
	 *
	 *     @type string $type   One of 'byte' or 'char'.
	 *     @type int    $length The column length.
	 * }
	 */
	public function get_col_length( $table, $column ) {
		$tablekey  = strtolower( $table );
		$columnkey = strtolower( $column );

		// Skip this entirely if this isn't a MySQL database.
		if ( empty( $this->is_mysql ) ) {
			return false;
		}

		if ( empty( $this->col_meta[ $tablekey ] ) ) {
			// This primes column information for us.
			$table_charset = $this->get_table_charset( $table );
			if ( is_wp_error( $table_charset ) ) {
				return $table_charset;
			}
		}

		if ( empty( $this->col_meta[ $tablekey ][ $columnkey ] ) ) {
			return false;
		}

		$typeinfo = explode( '(', $this->col_meta[ $tablekey ][ $columnkey ]->Type );

		$type = strtolower( $typeinfo[0] );
		if ( ! empty( $typeinfo[1] ) ) {
			$length = trim( $typeinfo[1], ')' );
		} else {
			$length = false;
		}

		switch ( $type ) {
			case 'char':
			case 'varchar':
				return array(
					'type'   => 'char',
					'length' => (int) $length,
				);

			case 'binary':
			case 'varbinary':
				return array(
					'type'   => 'byte',
					'length' => (int) $length,
				);

			case 'tinyblob':
			case 'tinytext':
				return array(
					'type'   => 'byte',
					'length' => 255,        // 2^8 - 1
				);

			case 'blob':
			case 'text':
				return array(
					'type'   => 'byte',
					'length' => 65535,      // 2^16 - 1
				);

			case 'mediumblob':
			case 'mediumtext':
				return array(
					'type'   => 'byte',
					'length' => 16777215,   // 2^24 - 1
				);

			case 'longblob':
			case 'longtext':
				return array(
					'type'   => 'byte',
					'length' => 4294967295, // 2^32 - 1
				);

			default:
				return false;
		}
	}

	/**
	 * Checks if a string is ASCII.
	 *
	 * The negative regex is faster for non-ASCII strings, as it allows
	 * the search to finish as soon as it encounters a non-ASCII character.
	 *
	 * @since 4.2.0
	 *
	 * @param string $input_string String to check.
	 * @return bool True if ASCII, false if not.
	 */
	protected function check_ascii( $input_string ) {
		if ( function_exists( 'mb_check_encoding' ) ) {
			if ( mb_check_encoding( $input_string, 'ASCII' ) ) {
				return true;
			}
		} elseif ( ! preg_match( '/[^\x00-\x7F]/', $input_string ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if the query is accessing a collation considered safe on the current version of MySQL.
	 *
	 * @since 4.2.0
	 *
	 * @param string $query The query to check.
	 * @return bool True if the collation is safe, false if it isn't.
	 */
	protected function check_safe_collation( $query ) {
		if ( $this->checking_collation ) {
			return true;
		}

		// We don't need to check the collation for queries that don't read data.
		$query = ltrim( $query, "\r\n\t (" );
		if ( preg_match( '/^(?:SHOW|DESCRIBE|DESC|EXPLAIN|CREATE)\s/i', $query ) ) {
			return true;
		}

		// All-ASCII queries don't need extra checking.
		if ( $this->check_ascii( $query ) ) {
			return true;
		}

		$table = $this->get_table_from_query( $query );
		if ( ! $table ) {
			return false;
		}

		$this->checking_collation = true;
		$collation                = $this->get_table_charset( $table );
		$this->checking_collation = false;

		// Tables with no collation, or latin1 only, don't need extra checking.
		if ( false === $collation || 'latin1' === $collation ) {
			return true;
		}

		$table = strtolower( $table );
		if ( empty( $this->col_meta[ $table ] ) ) {
			return false;
		}

		// If any of the columns don't have one of these collations, it needs more confidence checking.
		$safe_collations = array(
			'utf8_bin',
			'utf8_general_ci',
			'utf8mb3_bin',
			'utf8mb3_general_ci',
			'utf8mb4_bin',
			'utf8mb4_general_ci',
		);

		foreach ( $this->col_meta[ $table ] as $col ) {
			if ( empty( $col->Collation ) ) {
				continue;
			}

			if ( ! in_array( $col->Collation, $safe_collations, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Strips any invalid characters based on value/charset pairs.
	 *
	 * @since 4.2.0
	 *
	 * @param array $data Array of value arrays. Each value array has the keys 'value', 'charset', and 'length'.
	 *                    An optional 'ascii' key can be set to false to avoid redundant ASCII checks.
	 * @return array|WP_Error The $data parameter, with invalid characters removed from each value.
	 *                        This works as a passthrough: any additional keys such as 'field' are
	 *                        retained in each value array. If we cannot remove invalid characters,
	 *                        a WP_Error object is returned.
	 */
	protected function strip_invalid_text( $data ) {
		$db_check_string = false;

		foreach ( $data as &$value ) {
			$charset = $value['charset'];

			if ( is_array( $value['length'] ) ) {
				$length                  = $value['length']['length'];
				$truncate_by_byte_length = 'byte' === $value['length']['type'];
			} else {
				$length = false;
				/*
				 * Since we have no length, we'll never truncate. Initialize the variable to false.
				 * True would take us through an unnecessary (for this case) codepath below.
				 */
				$truncate_by_byte_length = false;
			}

			// There's no charset to work with.
			if ( false === $charset ) {
				continue;
			}

			// Column isn't a string.
			if ( ! is_string( $value['value'] ) ) {
				continue;
			}

			$needs_validation = true;
			if (
				// latin1 can store any byte sequence.
				'latin1' === $charset
			||
				// ASCII is always OK.
				( ! isset( $value['ascii'] ) && $this->check_ascii( $value['value'] ) )
			) {
				$truncate_by_byte_length = true;
				$needs_validation        = false;
			}

			if ( $truncate_by_byte_length ) {
				mbstring_binary_safe_encoding();
				if ( false !== $length && strlen( $value['value'] ) > $length ) {
					$value['value'] = substr( $value['value'], 0, $length );
				}
				reset_mbstring_encoding();

				if ( ! $needs_validation ) {
					continue;
				}
			}

			// utf8 can be handled by regex, which is a bunch faster than a DB lookup.
			if ( ( 'utf8' === $charset || 'utf8mb3' === $charset || 'utf8mb4' === $charset ) && function_exists( 'mb_strlen' ) ) {
				$regex = '/
					(
						(?: [\x00-\x7F]                  # single-byte sequences   0xxxxxxx
						|   [\xC2-\xDF][\x80-\xBF]       # double-byte sequences   110xxxxx 10xxxxxx
						|   \xE0[\xA0-\xBF][\x80-\xBF]   # triple-byte sequences   1110xxxx 10xxxxxx * 2
						|   [\xE1-\xEC][\x80-\xBF]{2}
						|   \xED[\x80-\x9F][\x80-\xBF]
						|   [\xEE-\xEF][\x80-\xBF]{2}';

				if ( 'utf8mb4' === $charset ) {
					$regex .= '
						|    \xF0[\x90-\xBF][\x80-\xBF]{2} # four-byte sequences   11110xxx 10xxxxxx * 3
						|    [\xF1-\xF3][\x80-\xBF]{3}
						|    \xF4[\x80-\x8F][\x80-\xBF]{2}
					';
				}

				$regex         .= '){1,40}                          # ...one or more times
					)
					| .                                  # anything else
					/x';
				$value['value'] = preg_replace( $regex, '$1', $value['value'] );

				if ( false !== $length && mb_strlen( $value['value'], 'UTF-8' ) > $length ) {
					$value['value'] = mb_substr( $value['value'], 0, $length, 'UTF-8' );
				}
				continue;
			}

			// We couldn't use any local conversions, send it to the DB.
			$value['db']     = true;
			$db_check_string = true;
		}
		unset( $value ); // Remove by reference.

		if ( $db_check_string ) {
			$queries = array();
			foreach ( $data as $col => $value ) {
				if ( ! empty( $value['db'] ) ) {
					// We're going to need to truncate by characters or bytes, depending on the length value we have.
					if ( isset( $value['length']['type'] ) && 'byte' === $value['length']['type'] ) {
						// Using binary causes LEFT() to truncate by bytes.
						$charset = 'binary';
					} else {
						$charset = $value['charset'];
					}

					if ( $this->charset ) {
						$connection_charset = $this->charset;
					} else {
						$connection_charset = mysqli_character_set_name( $this->dbh );
					}

					if ( is_array( $value['length'] ) ) {
						$length          = sprintf( '%.0f', $value['length']['length'] );
						$queries[ $col ] = $this->prepare( "CONVERT( LEFT( CONVERT( %s USING $charset ), $length ) USING $connection_charset )", $value['value'] );
					} elseif ( 'binary' !== $charset ) {
						// If we don't have a length, there's no need to convert binary - it will always return the same result.
						$queries[ $col ] = $this->prepare( "CONVERT( CONVERT( %s USING $charset ) USING $connection_charset )", $value['value'] );
					}

					unset( $data[ $col ]['db'] );
				}
			}

			$sql = array();
			foreach ( $queries as $column => $query ) {
				if ( ! $query ) {
					continue;
				}

				$sql[] = $query . " AS x_$column";
			}

			$this->check_current_query = false;
			$row                       = $this->get_row( 'SELECT ' . implode( ', ', $sql ), ARRAY_A );
			if ( ! $row ) {
				return new WP_Error( 'wpdb_strip_invalid_text_failure', __( 'Could not strip invalid text.' ) );
			}

			foreach ( array_keys( $data ) as $column ) {
				if ( isset( $row[ "x_$column" ] ) ) {
					$data[ $column ]['value'] = $row[ "x_$column" ];
				}
			}
		}

		return $data;
	}

	/**
	 * Strips any invalid characters from the query.
	 *
	 * @since 4.2.0
	 *
	 * @param string $query Query to convert.
	 * @return string|WP_Error The converted query, or a WP_Error object if the conversion fails.
	 */
	protected function strip_invalid_text_from_query( $query ) {
		// We don't need to check the collation for queries that don't read data.
		$trimmed_query = ltrim( $query, "\r\n\t (" );
		if ( preg_match( '/^(?:SHOW|DESCRIBE|DESC|EXPLAIN|CREATE)\s/i', $trimmed_query ) ) {
			return $query;
		}

		$table = $this->get_table_from_query( $query );
		if ( $table ) {
			$charset = $this->get_table_charset( $table );
			if ( is_wp_error( $charset ) ) {
				return $charset;
			}

			// We can't reliably strip text from tables containing binary/blob columns.
			if ( 'binary' === $charset ) {
				return $query;
			}
		} else {
			$charset = $this->charset;
		}

		$data = array(
			'value'   => $query,
			'charset' => $charset,
			'ascii'   => false,
			'length'  => false,
		);

		$data = $this->strip_invalid_text( array( $data ) );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		return $data[0]['value'];
	}

	/**
	 * Strips any invalid characters from the string for a given table and column.
	 *
	 * @since 4.2.0
	 *
	 * @param string $table  Table name.
	 * @param string $column Column name.
	 * @param string $value  The text to check.
	 * @return string|WP_Error The converted string, or a WP_Error object if the conversion fails.
	 */
	public function strip_invalid_text_for_column( $table, $column, $value ) {
		if ( ! is_string( $value ) ) {
			return $value;
		}

		$charset = $this->get_col_charset( $table, $column );
		if ( ! $charset ) {
			// Not a string column.
			return $value;
		} elseif ( is_wp_error( $charset ) ) {
			// Bail on real errors.
			return $charset;
		}

		$data = array(
			$column => array(
				'value'   => $value,
				'charset' => $charset,
				'length'  => $this->get_col_length( $table, $column ),
			),
		);

		$data = $this->strip_invalid_text( $data );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		return $data[ $column ]['value'];
	}

	/**
	 * Finds the first table name referenced in a query.
	 *
	 * @since 4.2.0
	 *
	 * @param string $query The query to search.
	 * @return string|false The table name found, or false if a table couldn't be found.
	 */
	protected function get_table_from_query( $query ) {
		// Remove characters that can legally trail the table name.
		$query = rtrim( $query, ';/-#' );

		// Allow (select...) union [...] style queries. Use the first query's table name.
		$query = ltrim( $query, "\r\n\t (" );

		// Strip everything between parentheses except nested selects.
		$query = preg_replace( '/\((?!\s*select)[^(]*?\)/is', '()', $query );

		// Quickly match most common queries.
		if ( preg_match(
			'/^\s*(?:'
				. 'SELECT.*?\s+FROM'
				. '|INSERT(?:\s+LOW_PRIORITY|\s+DELAYED|\s+HIGH_PRIORITY)?(?:\s+IGNORE)?(?:\s+INTO)?'
				. '|REPLACE(?:\s+LOW_PRIORITY|\s+DELAYED)?(?:\s+INTO)?'
				. '|UPDATE(?:\s+LOW_PRIORITY)?(?:\s+IGNORE)?'
				. '|DELETE(?:\s+LOW_PRIORITY|\s+QUICK|\s+IGNORE)*(?:.+?FROM)?'
			. ')\s+((?:[0-9a-zA-Z$_.`-]|[\xC2-\xDF][\x80-\xBF])+)/is',
			$query,
			$maybe
		) ) {
			return str_replace( '`', '', $maybe[1] );
		}

		// SHOW TABLE STATUS and SHOW TABLES WHERE Name = 'wp_posts'
		if ( preg_match( '/^\s*SHOW\s+(?:TABLE\s+STATUS|(?:FULL\s+)?TABLES).+WHERE\s+Name\s*=\s*("|\')((?:[0-9a-zA-Z$_.-]|[\xC2-\xDF][\x80-\xBF])+)\\1/is', $query, $maybe ) ) {
			return $maybe[2];
		}

		/*
		 * SHOW TABLE STATUS LIKE and SHOW TABLES LIKE 'wp\_123\_%'
		 * This quoted LIKE operand seldom holds a full table name.
		 * It is usually a pattern for matching a prefix so we just
		 * strip the trailing % and unescape the _ to get 'wp_123_'
		 * which drop-ins can use for routing these SQL statements.
		 */
		if ( preg_match( '/^\s*SHOW\s+(?:TABLE\s+STATUS|(?:FULL\s+)?TABLES)\s+(?:WHERE\s+Name\s+)?LIKE\s*("|\')((?:[\\\\0-9a-zA-Z$_.-]|[\xC2-\xDF][\x80-\xBF])+)%?\\1/is', $query, $maybe ) ) {
			return str_replace( '\\_', '_', $maybe[2] );
		}

		// Big pattern for the rest of the table-related queries.
		if ( preg_match(
			'/^\s*(?:'
				. '(?:EXPLAIN\s+(?:EXTENDED\s+)?)?SELECT.*?\s+FROM'
				. '|DESCRIBE|DESC|EXPLAIN|HANDLER'
				. '|(?:LOCK|UNLOCK)\s+TABLE(?:S)?'
				. '|(?:RENAME|OPTIMIZE|BACKUP|RESTORE|CHECK|CHECKSUM|ANALYZE|REPAIR).*\s+TABLE'
				. '|TRUNCATE(?:\s+TABLE)?'
				. '|CREATE(?:\s+TEMPORARY)?\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?'
				. '|ALTER(?:\s+IGNORE)?\s+TABLE'
				. '|DROP\s+TABLE(?:\s+IF\s+EXISTS)?'
				. '|CREATE(?:\s+\w+)?\s+INDEX.*\s+ON'
				. '|DROP\s+INDEX.*\s+ON'
				. '|LOAD\s+DATA.*INFILE.*INTO\s+TABLE'
				. '|(?:GRANT|REVOKE).*ON\s+TABLE'
				. '|SHOW\s+(?:.*FROM|.*TABLE)'
			. ')\s+\(*\s*((?:[0-9a-zA-Z$_.`-]|[\xC2-\xDF][\x80-\xBF])+)\s*\)*/is',
			$query,
			$maybe
		) ) {
			return str_replace( '`', '', $maybe[1] );
		}

		return false;
	}

	/**
	 * Loads the column metadata from the last query.
	 *
	 * @since 3.5.0
	 */
	protected function load_col_info() {
		if ( $this->col_info ) {
			return;
		}

		$num_fields = mysqli_num_fields( $this->result );

		for ( $i = 0; $i < $num_fields; $i++ ) {
			$this->col_info[ $i ] = mysqli_fetch_field( $this->result );
		}
	}

	/**
	 * Retrieves column metadata from the last query.
	 *
	 * @since 0.71
	 *
	 * @param string $info_type  Optional. Possible values include 'name', 'table', 'def', 'max_length',
	 *                           'not_null', 'primary_key', 'multiple_key', 'unique_key', 'numeric',
	 *                           'blob', 'type', 'unsigned', 'zerofill'. Default 'name'.
	 * @param int    $col_offset Optional. 0: col name. 1: which table the col's in. 2: col's max length.
	 *                           3: if the col is numeric. 4: col's type. Default -1.
	 * @return mixed Column results.
	 */
	public function get_col_info( $info_type = 'name', $col_offset = -1 ) {
		$this->load_col_info();

		if ( $this->col_info ) {
			if ( -1 === $col_offset ) {
				$i         = 0;
				$new_array = array();
				foreach ( (array) $this->col_info as $col ) {
					$new_array[ $i ] = $col->{$info_type};
					++$i;
				}
				return $new_array;
			} else {
				return $this->col_info[ $col_offset ]->{$info_type};
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
	public function timer_start() {
		$this->time_start = microtime( true );
		return true;
	}

	/**
	 * Stops the debugging timer.
	 *
	 * @since 1.5.0
	 *
	 * @return float Total time spent on the query, in seconds.
	 */
	public function timer_stop() {
		return ( microtime( true ) - $this->time_start );
	}

	/**
	 * Wraps errors in a nice header and footer and dies.
	 *
	 * Will not die if wpdb::$show_errors is false.
	 *
	 * @since 1.5.0
	 *
	 * @param string $message    The error message.
	 * @param string $error_code Optional. A computer-readable string to identify the error.
	 *                           Default '500'.
	 * @return void|false Void if the showing of errors is enabled, false if disabled.
	 */
	public function bail( $message, $error_code = '500' ) {
		if ( $this->show_errors ) {
			$error = '';

			if ( $this->dbh instanceof mysqli ) {
				$error = mysqli_error( $this->dbh );
			} elseif ( mysqli_connect_errno() ) {
				$error = mysqli_connect_error();
			}

			if ( $error ) {
				$message = '<p><code>' . $error . "</code></p>\n" . $message;
			}

			wp_die( $message );
		} else {
			if ( class_exists( 'WP_Error', false ) ) {
				$this->error = new WP_Error( $error_code, $message );
			} else {
				$this->error = $message;
			}

			return false;
		}
	}

	/**
	 * Closes the current database connection.
	 *
	 * @since 4.5.0
	 *
	 * @return bool True if the connection was successfully closed,
	 *              false if it wasn't, or if the connection doesn't exist.
	 */
	public function close() {
		if ( ! $this->dbh ) {
			return false;
		}

		$closed = mysqli_close( $this->dbh );

		if ( $closed ) {
			$this->dbh           = null;
			$this->ready         = false;
			$this->has_connected = false;
		}

		return $closed;
	}

	/**
	 * Determines whether MySQL database is at least the required minimum version.
	 *
	 * @since 2.5.0
	 *
	 * @global string $required_mysql_version The required MySQL version string.
	 * @return void|WP_Error
	 */
	public function check_database_version() {
		global $required_mysql_version;
		$wp_version = wp_get_wp_version();

		// Make sure the server has the required MySQL version.
		if ( version_compare( $this->db_version(), $required_mysql_version, '<' ) ) {
			/* translators: 1: WordPress version number, 2: Minimum required MySQL version number. */
			return new WP_Error( 'database_version', sprintf( __( '<strong>Error:</strong> WordPress %1$s requires MySQL %2$s or higher' ), $wp_version, $required_mysql_version ) );
		}
	}

	/**
	 * Determines whether the database supports collation.
	 *
	 * Called when WordPress is generating the table scheme.
	 *
	 * Use `wpdb::has_cap( 'collation' )`.
	 *
	 * @since 2.5.0
	 * @deprecated 3.5.0 Use wpdb::has_cap()
	 *
	 * @return bool True if collation is supported, false if not.
	 */
	public function supports_collation() {
		_deprecated_function( __FUNCTION__, '3.5.0', 'wpdb::has_cap( \'collation\' )' );
		return $this->has_cap( 'collation' );
	}

	/**
	 * Retrieves the database character collate.
	 *
	 * @since 3.5.0
	 *
	 * @return string The database character collate.
	 */
	public function get_charset_collate() {
		$charset_collate = '';

		if ( ! empty( $this->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $this->charset";
		}
		if ( ! empty( $this->collate ) ) {
			$charset_collate .= " COLLATE $this->collate";
		}

		return $charset_collate;
	}

	/**
	 * Determines whether the database or WPDB supports a particular feature.
	 *
	 * Capability sniffs for the database server and current version of WPDB.
	 *
	 * Database sniffs are based on the version of MySQL the site is using.
	 *
	 * WPDB sniffs are added as new features are introduced to allow theme and plugin
	 * developers to determine feature support. This is to account for drop-ins which may
	 * introduce feature support at a different time to WordPress.
	 *
	 * @since 2.7.0
	 * @since 4.1.0 Added support for the 'utf8mb4' feature.
	 * @since 4.6.0 Added support for the 'utf8mb4_520' feature.
	 * @since 6.2.0 Added support for the 'identifier_placeholders' feature.
	 * @since 6.6.0 The `utf8mb4` feature now always returns true.
	 *
	 * @see wpdb::db_version()
	 *
	 * @param string $db_cap The feature to check for. Accepts 'collation', 'group_concat',
	 *                       'subqueries', 'set_charset', 'utf8mb4', 'utf8mb4_520',
	 *                       or 'identifier_placeholders'.
	 * @return bool True when the database feature is supported, false otherwise.
	 */
	public function has_cap( $db_cap ) {
		$db_version     = $this->db_version();
		$db_server_info = $this->db_server_info();

		/*
		 * Account for MariaDB version being prefixed with '5.5.5-' on older PHP versions.
		 *
		 * Note: str_contains() is not used here, as this file can be included
		 * directly outside of WordPress core, e.g. by HyperDB, in which case
		 * the polyfills from wp-includes/compat.php are not loaded.
		 */
		if ( '5.5.5' === $db_version && false !== strpos( $db_server_info, 'MariaDB' )
			&& PHP_VERSION_ID < 80016 // PHP 8.0.15 or older.
		) {
			// Strip the '5.5.5-' prefix and set the version to the correct value.
			$db_server_info = preg_replace( '/^5\.5\.5-(.*)/', '$1', $db_server_info );
			$db_version     = preg_replace( '/[^0-9.].*/', '', $db_server_info );
		}

		switch ( strtolower( $db_cap ) ) {
			case 'collation':    // @since 2.5.0
			case 'group_concat': // @since 2.7.0
			case 'subqueries':   // @since 2.7.0
				return version_compare( $db_version, '4.1', '>=' );
			case 'set_charset':
				return version_compare( $db_version, '5.0.7', '>=' );
			case 'utf8mb4':      // @since 4.1.0
				return true;
			case 'utf8mb4_520': // @since 4.6.0
				return version_compare( $db_version, '5.6', '>=' );
			case 'identifier_placeholders': // @since 6.2.0
				/*
				 * As of WordPress 6.2, wpdb::prepare() supports identifiers via '%i',
				 * e.g. table/field names.
				 */
				return true;
		}

		return false;
	}

	/**
	 * Retrieves a comma-separated list of the names of the functions that called wpdb.
	 *
	 * @since 2.5.0
	 *
	 * @return string Comma-separated list of the calling functions.
	 */
	public function get_caller() {
		return wp_debug_backtrace_summary( __CLASS__ );
	}

	/**
	 * Retrieves the database server version.
	 *
	 * @since 2.7.0
	 *
	 * @return string|null Version number on success, null on failure.
	 */
	public function db_version() {
		return preg_replace( '/[^0-9.].*/', '', $this->db_server_info() );
	}

	/**
	 * Returns the version of the MySQL server.
	 *
	 * @since 5.5.0
	 *
	 * @return string Server version as a string.
	 */
	public function db_server_info() {
		return mysqli_get_server_info( $this->dbh );
	}
}
