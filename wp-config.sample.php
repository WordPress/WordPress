<?php
/** WordPress's config file **/

// ** MySQL settings **
define('DB_NAME', 'wordpress');     // The name of the database
define('DB_USER', 'username');     // Your MySQL username
define('DB_PASSWORD', 'password'); // ...and password
define('DB_HOST', 'localhost');     // 99% chance you won't need to change this value

// Database tables' names
//
// Change the prefix if you want to have multiple blogs in a single database.

$table_prefix             = 'wp_';   // eg 'wp_' or 'b2' or 'mylogin_'

// This is the name of the include directory. No "/" allowed.
$b2inc = 'b2-include';

/* Stop editing */

$HTTP_HOST = getenv('HTTP_HOST');  /* domain name */
$REMOTE_ADDR = getenv('REMOTE_ADDR'); /* visitor's IP */
$HTTP_USER_AGENT = getenv('HTTP_USER_AGENT'); /* visitor's browser */

$server = DB_HOST;
$loginsql = DB_USER;
$passsql = DB_PASSWORD;
$base = DB_NAME;

$abspath = dirname(__FILE__).'/';

// pull in the day and month translations and the smilies
require_once($abspath.'wp-config-extra.php');
require_once($abspath.$b2inc.'/wp-db.php');
require_once($abspath.$b2inc.'/b2functions.php');
require_once($abspath.'wp-settings.php');
?>
