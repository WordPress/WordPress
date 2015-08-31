<?php

global $wpdb_cluster;

/**
 * Persistent (bool)
 * 
 * This determines whether to use mysql_connect or mysql_pconnect. The effects
 * of this setting may vary and should be carefully tested.
 * Default: false
 */
$wpdb_cluster->persistent = false;

/**
 * check_tcp_responsiveness
 * 
 * Enables checking TCP responsiveness by fsockopen prior to mysql_connect or
 * mysql_pconnect. This was added because PHP's mysql functions do not provide
 * a variable timeout setting. Disabling it may improve average performance by
 * a very tiny margin but lose protection against connections failing slowly.
 * Default: true
 */
$wpdb_cluster->check_tcp_responsiveness = true;

/**
 * Default is to always (reads & writes) use the master db when user is in administration backend.
 * Set use_master_in_backend to false to disable this behavior.
 *
 * WARNING: if your cluster has any replication delays then when this is enabled, you may not see
 * any admin changes until the replication catches up with the change written to your master
 * server and will see old content/configuration until that point in time - You should test this 
 * in your environment fully.
 */
//$wpdb_cluster->use_master_in_backend = false;

/**
 * This set the charset that the db connection should use.
 * If DB_CHARSET is set there is no need to set $wpdb_cluster->charset.
 */
//$wpdb_cluster->charset = 'utf-8';

/**
 * This set the charset that the db connection should use.
 * If DB_COLLATE is set there is no need to set $wpdb_cluster->collate.
 */
//$wpdb_cluster->collate = 'utf8_general_ci';
/** Configuration Functions **/

/**
 * $wpdb_cluster->add_database( $database );
 *
 * $database is an associative array with these parameters:
 * host             (required) Hostname with optional :port. Default port is 3306.
 * user             (required) MySQL user name.
 * password         (required) MySQL user password.
 * name             (required) MySQL database name.
 * read             (optional) Whether server is readable. Default is 1 (readable).
 *			    Also used to assign preference. 
 * write            (optional) Whether server is writable. Default is 1 (writable).
 *                             Also used to assign preference in multi-master mode.
 * dataset          (optional) Name of dataset. Default is 'global'.
 * timeout          (optional) Seconds to wait for TCP responsiveness. Default is 0.2
 * connect_function (optional) connection function to use
 * zone             (optional) name of zone where server is located.
 *                             Used for web applications hosted on cluster 
 */

/**
 * $wpdb_cluster->add_callback( $callback, $callback_group = 'dataset' );
 *
 * $callback is a callable function or method. $callback_group is the
 * group of callbacks, this $callback belongs to.
 *
 * Callbacks are executed in the order in which they are registered until one
 * of them returns something other than null.
 *
 * The default $callback_group is 'dataset'. Callback in this group
 * will be called with two arguments and expected to compute a dataset or return null.
 * $dataset = $callback($table, &$wpdb);
 *
 */

/** Masters and slaves
 *
 * A database definition can include 'read' and 'write' parameters. These
 * operate as boolean switches but they are typically specified as integers.
 * They allow or disallow use of the database for reading or writing.
 *
 * A master database might be configured to allow reading and writing:
 *   'write' => true,
 *   'read'  => true,
 * while a slave would be allowed only to read:
 *   'write' => false,
 *   'read'  => true,
 *
 * It might be advantageous to disallow reading from the master, such as when
 * there are many slaves available and the master is very busy with writes.
 *   'write' => true,
 *   'read'  => false,
 */

/**
 * Web applications hosted on cluster
 *
 * When your databases are located in separate physical locations there is
 * typically an advantage to connecting to a nearby server instead of a more
 * distant one. This can be configured by defining zones.
 * 
 * Add 'zone' parameter to add_server call:
 *     'zone' => 'A'
 * 
 * Plugin determines where application is running by checking 
 * $_SERVER['SERVER_NAME'] system variable against defined in zone definition
 * and then connects to servers following defined order:
 * Value '*' can be used as 'server_names' item to indicate any server.
 * 
 * $wpdb_cluster->add_zone(array(
 *   'name' => 'A',
 *   'server_names' => array('host1', 'host1.1'),
 *   'zone_priorities' => array('A', 'B')
 * ));
 * 
 * As a result it will try to connect to servers in zone A first, then servers
 * in zone B.
 */

/**
 * This is the most basic way to add a server using only the
 * required parameters: host, user, password, name.
 * This adds the DB defined in wp-config.php as a read/write server for
 * the 'global' dataset. (Every table is in 'global' by default.)
 */
$wpdb_cluster->add_database(array(
	'host'     => DB_HOST,     // If port is other than 3306, use host:port.
	'user'     => DB_USER,
	'password' => DB_PASSWORD,
	'name'     => DB_NAME,
));

/**
 * This adds the same server again, only this time it is configured as a slave.
 * The last three parameters are set to the defaults but are shown for clarity.
 */
/*
$wpdb_cluster->add_database(array(
	'host'     => DB_HOST,     // If port is other than 3306, use host:port.
	'user'     => DB_USER,
	'password' => DB_PASSWORD,
	'name'     => DB_NAME,
	'write'    => false,
	'read'     => true,
	'dataset'  => 'global',
	'timeout'  => 0.2,
));
*/

/** Sample Configuration 2: Partitioning **/

/**
 * This example shows a setup where the multisite blog tables have been
 * separated from the global dataset.
 */
/*
$wpdb_cluster->add_database(array(
	'host'     => 'global.db.example.com',
	'user'     => 'globaluser',
	'password' => 'globalpassword',
	'name'     => 'globaldb',
));
$wpdb_cluster->add_database(array(
	'host'     => 'blog.db.example.com',
	'user'     => 'bloguser',
	'password' => 'blogpassword',
	'name'     => 'blogdb',
	'dataset'  => 'blog2',
));
$wpdb_cluster->add_callback('my_db_callback', 'blog_dataset');
function my_db_callback($blog_id, $wpdb_cluster) {
    if ($blog_id > 5))
        return 'blog2';
}
*/
