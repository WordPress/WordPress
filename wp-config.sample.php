<?php
/** WordPress's config file **/

// ** MySQL settings **
define('DB_NAME', 'wordpress');     // The name of the database
define('DB_USER', 'username');     // Your MySQL username
define('DB_PASSWORD', 'password'); // ...and password
define('DB_HOST', 'localhost');     // 99% chance you won't need to change this value

// Database tables' names
//
// Change them if you want to have multiple blogs in a single database.
// If you already have custom names leave table_prefix empty and just
// edit the names.

$table_prefix             = 'wp_';   // eg 'wp_' or 'b2' or 'mylogin_'

$tableposts               = $table_prefix . 'posts';
$tableusers               = $table_prefix . 'users';
$tablesettings            = $table_prefix . 'settings';
$tablecategories          = $table_prefix . 'categories';
$tablecomments            = $table_prefix . 'comments';
$tablelinks               = $table_prefix . 'links';
$tablelinkcategories      = $table_prefix . 'linkcategories';
$tableoptions             = $table_prefix . 'options';
$tableoptiontypes         = $table_prefix . 'optiontypes';
$tableoptionvalues        = $table_prefix . 'optionvalues';
$tableoptiongroups        = $table_prefix . 'optiongroups';
$tableoptiongroup_options = $table_prefix . 'optiongroup_options';

// This is the name of the include directory. No "/" allowed.
$b2inc = 'b2-include';

/* Stop editing */

// setup your own smilies (if not there is a set in b2vars)
if (file_exists('mysmilies.php')) {
    include('mysmilies.php');
}

// pull in the day and month translations
require_once('day-month-trans.php');

$HTTP_HOST = getenv('HTTP_HOST');  /* domain name */
$REMOTE_ADDR = getenv('REMOTE_ADDR'); /* visitor's IP */
$HTTP_USER_AGENT = getenv('HTTP_USER_AGENT'); /* visitor's browser */

$server = DB_HOST;
$loginsql = DB_USER;
$passsql = DB_PASSWORD;
$base = DB_NAME;

$abspath = dirname(__FILE__).'/';
require_once($abspath.$b2inc.'/wp-db.php');
require_once($abspath.$b2inc.'/b2functions.php');
require_once($abspath.'wp-settings.php');
?>
