<?php
/**
 * Plugin Name: WP SQLite DB
 * Description: SQLite database driver drop-in. (based on SQLite Integration by Kojima Toshiyasu)
 * Author: Evan Mattson
 * Author URI: https://aaemnnost.tv
 * Plugin URI: https://github.com/aaemnnosttv/wp-sqlite-db
 * Version: 1.3.1
 * Requires PHP: 5.6
 *
 * This project is based on the original work of Kojima Toshiyasu and his SQLite Integration plugin.
 */

namespace WP_SQLite_DB {

    use DateTime;
    use DateInterval;
    use PDO;
    use PDOException;
    use SQLite3;

    if (! defined('ABSPATH')) {
        exit;
    }

    /**
     * USE_MYSQL is a directive for using MySQL for database.
     * If you want to change the database from SQLite to MySQL or from MySQL to SQLite,
     * the line below in the wp-config.php will enable you to use MySQL.
     *
     * <code>
     * define('USE_MYSQL', true);
     * </code>
     *
     * If you want to use SQLite, the line below will do. Or simply removing the line will
     * be enough.
     *
     * <code>
     * define('USE_MYSQL', false);
     * </code>
     */
    if (defined('USE_MYSQL') && USE_MYSQL) {
        return;
    }

    function pdo_log_error($message, $data = null)
    {
        if (strpos($_SERVER['SCRIPT_NAME'], 'wp-admin') !== false) {
            $admin_dir = '';
        } else {
            $admin_dir = 'wp-admin/';
        }
        die(<<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>WordPress &rsaquo; Error</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="{$admin_dir}css/install.css" type="text/css" />
</head>
<body>
  <h1 id="logo"><img alt="WordPress" src="{$admin_dir}images/wordpress-logo.png" /></h1>
  <p>$message</p>
  <p>$data</p>
</body>
<html>

HTML
        );
    }

    if (version_compare(PHP_VERSION, '5.4', '<')) {
        pdo_log_error('PHP version on this server is too old.', sprintf("Your server is running PHP version %d but this SQLite driver requires at least 5.4", phpversion()));
    }

    if (! extension_loaded('pdo')) {
        pdo_log_error('PHP PDO Extension is not loaded.',
            'Your PHP installation appears to be missing the PDO extension which is required for this version of WordPress.');
    }

    if (! extension_loaded('pdo_sqlite')) {
        pdo_log_error('PDO Driver for SQLite is missing.',
            'Your PHP installation appears not to have the right PDO drivers loaded. These are required for this version of WordPress and the type of database you have specified.');
    }

    /**
     * Notice:
     * Your scripts have the permission to create directories or files on your server.
     * If you write in your wp-config.php like below, we take these definitions.
     * define('DB_DIR', '/full_path_to_the_database_directory/');
     * define('DB_FILE', 'database_file_name');
     */

    /**
     * FQDBDIR is a directory where the sqlite database file is placed.
     * If DB_DIR is defined, it is used as FQDBDIR.
     */
    if (defined('DB_DIR')) {
        if (substr(DB_DIR, -1, 1) != '/') {
            define('FQDBDIR', DB_DIR . '/');
        } else {
            define('FQDBDIR', DB_DIR);
        }
    } else {
        if (defined('WP_CONTENT_DIR')) {
            define('FQDBDIR', WP_CONTENT_DIR . '/database/');
        } else {
            define('FQDBDIR', ABSPATH . 'wp-content/database/');
        }
    }

    /**
     * FQDB is a database file name. If DB_FILE is defined, it is used
     * as FQDB.
     */
    if (defined('DB_FILE')) {
        define('FQDB', FQDBDIR . DB_FILE);
    } else {
        define('FQDB', FQDBDIR . '.ht.sqlite');
    }

    /**
     * This class defines user defined functions(UDFs) for PDO library.
     *
     * These functions replace those used in the SQL statement with the PHP functions.
     *
     * Usage:
     *
     * <code>
     * new PDOSQLiteUDFS(ref_to_pdo_obj);
     * </code>
     *
     * This automatically enables ref_to_pdo_obj to replace the function in the SQL statement
     * to the ones defined here.
     */
    class PDOSQLiteUDFS
    {
        /**
         * The class constructor
         *
         * Initializes the use defined functions to PDO object with PDO::sqliteCreateFunction().
         *
         * @param PDO $pdo
         */
        public function __construct($pdo)
        {
            if (! $pdo) {
                wp_die('Database is not initialized.', 'Database Error');
            }
            foreach ($this->functions as $f => $t) {
                $pdo->sqliteCreateFunction($f, [$this, $t]);
            }
        }

        /**
         * array to define MySQL function => function defined with PHP.
         *
         * Replaced functions must be public.
         *
         * @var array
         */
        private $functions = [
            'month' => 'month',
            'year' => 'year',
            'day' => 'day',
            'unix_timestamp' => 'unix_timestamp',
            'now' => 'now',
            'char_length' => 'char_length',
            'md5' => 'md5',
            'curdate' => 'curdate',
            'rand' => 'rand',
            'substring' => 'substring',
            'dayofmonth' => 'day',
            'second' => 'second',
            'minute' => 'minute',
            'hour' => 'hour',
            'date_format' => 'dateformat',
            'from_unixtime' => 'from_unixtime',
            'date_add' => 'date_add',
            'date_sub' => 'date_sub',
            'adddate' => 'date_add',
            'subdate' => 'date_sub',
            'localtime' => 'now',
            'localtimestamp' => 'now',
            'isnull' => 'isnull',
            'if' => '_if',
            'regexpp' => 'regexp',
            'concat' => 'concat',
            'field' => 'field',
            'log' => 'log',
            'least' => 'least',
            'greatest' => 'greatest',
            'get_lock' => 'get_lock',
            'release_lock' => 'release_lock',
            'ucase' => 'ucase',
            'lcase' => 'lcase',
            'inet_ntoa' => 'inet_ntoa',
            'inet_aton' => 'inet_aton',
            'datediff' => 'datediff',
            'locate' => 'locate',
            'utc_date' => 'utc_date',
            'utc_time' => 'utc_time',
            'utc_timestamp' => 'utc_timestamp',
            'version' => 'version',
        ];

        /**
         * Method to extract the month value from the date.
         *
         * @param string representing the date formatted as 0000-00-00.
         *
         * @return string representing the number of the month between 1 and 12.
         */
        public function month($field)
        {
            $t = strtotime($field);

            return date('n', $t);
        }

        /**
         * Method to extract the year value from the date.
         *
         * @param string representing the date formatted as 0000-00-00.
         *
         * @return string representing the number of the year.
         */
        public function year($field)
        {
            $t = strtotime($field);

            return date('Y', $t);
        }

        /**
         * Method to extract the day value from the date.
         *
         * @param string representing the date formatted as 0000-00-00.
         *
         * @return string representing the number of the day of the month from 1 and 31.
         */
        public function day($field)
        {
            $t = strtotime($field);

            return date('j', $t);
        }

        /**
         * Method to return the unix timestamp.
         *
         * Used without an argument, it returns PHP time() function (total seconds passed
         * from '1970-01-01 00:00:00' GMT). Used with the argument, it changes the value
         * to the timestamp.
         *
         * @param string representing the date formatted as '0000-00-00 00:00:00'.
         *
         * @return number of unsigned integer
         */
        public function unix_timestamp($field = null)
        {
            return is_null($field) ? time() : strtotime($field);
        }

        /**
         * Method to emulate MySQL SECOND() function.
         *
         * @param string representing the time formatted as '00:00:00'.
         *
         * @return number of unsigned integer
         */
        public function second($field)
        {
            $t = strtotime($field);

            return intval(date("s", $t));
        }

        /**
         * Method to emulate MySQL MINUTE() function.
         *
         * @param string representing the time formatted as '00:00:00'.
         *
         * @return number of unsigned integer
         */
        public function minute($field)
        {
            $t = strtotime($field);

            return intval(date("i", $t));
        }

        /**
         * Method to emulate MySQL HOUR() function.
         *
         * @param string representing the time formatted as '00:00:00'.
         *
         * @return number
         */
        public function hour($time)
        {
            list($hours) = explode(":", $time);

            return intval($hours);
        }

        /**
         * Method to emulate MySQL FROM_UNIXTIME() function.
         *
         * @param integer of unix timestamp
         * @param string to indicate the way of formatting(optional)
         *
         * @return string formatted as '0000-00-00 00:00:00'.
         */
        public function from_unixtime($field, $format = null)
        {
            //convert to ISO time
            $date = date("Y-m-d H:i:s", $field);

            return is_null($format) ? $date : $this->dateformat($date, $format);
        }

        /**
         * Method to emulate MySQL NOW() function.
         *
         * @return string representing current time formatted as '0000-00-00 00:00:00'.
         */
        public function now()
        {
            return date("Y-m-d H:i:s");
        }

        /**
         * Method to emulate MySQL CURDATE() function.
         *
         * @return string representing current time formatted as '0000-00-00'.
         */
        public function curdate()
        {
            return date("Y-m-d");
        }

        /**
         * Method to emulate MySQL CHAR_LENGTH() function.
         *
         * @param string
         *
         * @return int unsigned integer for the length of the argument.
         */
        public function char_length($field)
        {
            return strlen($field);
        }

        /**
         * Method to emulate MySQL MD5() function.
         *
         * @param string
         *
         * @return string of the md5 hash value of the argument.
         */
        public function md5($field)
        {
            return md5($field);
        }

        /**
         * Method to emulate MySQL RAND() function.
         *
         * SQLite does have a random generator, but it is called RANDOM() and returns random
         * number between -9223372036854775808 and +9223372036854775807. So we substitute it
         * with PHP random generator.
         *
         * This function uses mt_rand() which is four times faster than rand() and returns
         * the random number between 0 and 1.
         *
         * @return int
         */
        public function rand()
        {
            return mt_rand(0, 1);
        }

        /**
         * Method to emulate MySQL SUBSTRING() function.
         *
         * This function rewrites the function name to SQLite compatible substr(),
         * which can manipulate UTF-8 characters.
         *
         * @param string $text
         * @param integer $pos representing the start point.
         * @param integer $len representing the length of the substring(optional).
         *
         * @return string
         */
        public function substring($text, $pos, $len = null)
        {
            return "substr($text, $pos, $len)";
        }

        /**
         * Method to emulate MySQL DATEFORMAT() function.
         *
         * @param string date formatted as '0000-00-00' or datetime as '0000-00-00 00:00:00'.
         * @param string $format
         *
         * @return string formatted according to $format
         */
        public function dateformat($date, $format)
        {
            $mysql_php_date_formats = [
                '%a' => 'D',
                '%b' => 'M',
                '%c' => 'n',
                '%D' => 'jS',
                '%d' => 'd',
                '%e' => 'j',
                '%H' => 'H',
                '%h' => 'h',
                '%I' => 'h',
                '%i' => 'i',
                '%j' => 'z',
                '%k' => 'G',
                '%l' => 'g',
                '%M' => 'F',
                '%m' => 'm',
                '%p' => 'A',
                '%r' => 'h:i:s A',
                '%S' => 's',
                '%s' => 's',
                '%T' => 'H:i:s',
                '%U' => 'W',
                '%u' => 'W',
                '%V' => 'W',
                '%v' => 'W',
                '%W' => 'l',
                '%w' => 'w',
                '%X' => 'Y',
                '%x' => 'o',
                '%Y' => 'Y',
                '%y' => 'y',
            ];
            $t = strtotime($date);
            $format = strtr($format, $mysql_php_date_formats);
            $output = date($format, $t);

            return $output;
        }

        /**
         * Method to emulate MySQL DATE_ADD() function.
         *
         * This function adds the time value of $interval expression to $date.
         * $interval is a single quoted strings rewritten by SQLiteQueryDriver::rewrite_query().
         * It is calculated in the private function deriveInterval().
         *
         * @param string $date representing the start date.
         * @param string $interval representing the expression of the time to add.
         *
         * @return string date formatted as '0000-00-00 00:00:00'.
         * @throws Exception
         */
        public function date_add($date, $interval)
        {
            $interval = $this->deriveInterval($interval);
            switch (strtolower($date)) {
                case "curdate()":
                    $objDate = new DateTime($this->curdate());
                    $objDate->add(new DateInterval($interval));
                    $formatted = $objDate->format("Y-m-d");
                    break;
                case "now()":
                    $objDate = new DateTime($this->now());
                    $objDate->add(new DateInterval($interval));
                    $formatted = $objDate->format("Y-m-d H:i:s");
                    break;
                default:
                    $objDate = new DateTime($date);
                    $objDate->add(new DateInterval($interval));
                    $formatted = $objDate->format("Y-m-d H:i:s");
            }

            return $formatted;
        }

        /**
         * Method to emulate MySQL DATE_SUB() function.
         *
         * This function subtracts the time value of $interval expression from $date.
         * $interval is a single quoted strings rewritten by SQLiteQueryDriver::rewrite_query().
         * It is calculated in the private function deriveInterval().
         *
         * @param string $date representing the start date.
         * @param string $interval representing the expression of the time to subtract.
         *
         * @return string date formatted as '0000-00-00 00:00:00'.
         * @throws Exception
         */
        public function date_sub($date, $interval)
        {
            $interval = $this->deriveInterval($interval);
            switch (strtolower($date)) {
                case "curdate()":
                    $objDate = new DateTime($this->curdate());
                    $objDate->sub(new DateInterval($interval));
                    $returnval = $objDate->format("Y-m-d");
                    break;
                case "now()":
                    $objDate = new DateTime($this->now());
                    $objDate->sub(new DateInterval($interval));
                    $returnval = $objDate->format("Y-m-d H:i:s");
                    break;
                default:
                    $objDate = new DateTime($date);
                    $objDate->sub(new DateInterval($interval));
                    $returnval = $objDate->format("Y-m-d H:i:s");
            }

            return $returnval;
        }

        /**
         * Method to calculate the interval time between two dates value.
         *
         * @access private
         *
         * @param string $interval white space separated expression.
         *
         * @return string representing the time to add or substract.
         */
        private function deriveInterval($interval)
        {
            $interval = trim(substr(trim($interval), 8));
            $parts = explode(' ', $interval);
            foreach ($parts as $part) {
                if (! empty($part)) {
                    $_parts[] = $part;
                }
            }
            $type = strtolower(end($_parts));
            switch ($type) {
                case "second":
                    $unit = 'S';

                    return 'PT' . $_parts[0] . $unit;
                    break;
                case "minute":
                    $unit = 'M';

                    return 'PT' . $_parts[0] . $unit;
                    break;
                case "hour":
                    $unit = 'H';

                    return 'PT' . $_parts[0] . $unit;
                    break;
                case "day":
                    $unit = 'D';

                    return 'P' . $_parts[0] . $unit;
                    break;
                case "week":
                    $unit = 'W';

                    return 'P' . $_parts[0] . $unit;
                    break;
                case "month":
                    $unit = 'M';

                    return 'P' . $_parts[0] . $unit;
                    break;
                case "year":
                    $unit = 'Y';

                    return 'P' . $_parts[0] . $unit;
                    break;
                case "minute_second":
                    list($minutes, $seconds) = explode(':', $_parts[0]);

                    return 'PT' . $minutes . 'M' . $seconds . 'S';
                case "hour_second":
                    list($hours, $minutes, $seconds) = explode(':', $_parts[0]);

                    return 'PT' . $hours . 'H' . $minutes . 'M' . $seconds . 'S';
                case "hour_minute":
                    list($hours, $minutes) = explode(':', $_parts[0]);

                    return 'PT' . $hours . 'H' . $minutes . 'M';
                case "day_second":
                    $days = intval($_parts[0]);
                    list($hours, $minutes, $seconds) = explode(':', $_parts[1]);

                    return 'P' . $days . 'D' . 'T' . $hours . 'H' . $minutes . 'M' . $seconds . 'S';
                case "day_minute":
                    $days = intval($_parts[0]);
                    list($hours, $minutes) = explode(':', $parts[1]);

                    return 'P' . $days . 'D' . 'T' . $hours . 'H' . $minutes . 'M';
                case "day_hour":
                    $days = intval($_parts[0]);
                    $hours = intval($_parts[1]);

                    return 'P' . $days . 'D' . 'T' . $hours . 'H';
                case "year_month":
                    list($years, $months) = explode('-', $_parts[0]);

                    return 'P' . $years . 'Y' . $months . 'M';
            }
        }

        /**
         * Method to emulate MySQL DATE() function.
         *
         * @param string $date formatted as unix time.
         *
         * @return string formatted as '0000-00-00'.
         */
        public function date($date)
        {
            return date("Y-m-d", strtotime($date));
        }

        /**
         * Method to emulate MySQL ISNULL() function.
         *
         * This function returns true if the argument is null, and true if not.
         *
         * @param various types $field
         *
         * @return boolean
         */
        public function isnull($field)
        {
            return is_null($field);
        }

        /**
         * Method to emulate MySQL IF() function.
         *
         * As 'IF' is a reserved word for PHP, function name must be changed.
         *
         * @param unknonw $expression the statement to be evaluated as true or false.
         * @param unknown $true statement or value returned if $expression is true.
         * @param unknown $false statement or value returned if $expression is false.
         *
         * @return unknown
         */
        public function _if($expression, $true, $false)
        {
            return ($expression == true) ? $true : $false;
        }

        /**
         * Method to emulate MySQL REGEXP() function.
         *
         * @param string $field haystack
         * @param string $pattern : regular expression to match.
         *
         * @return integer 1 if matched, 0 if not matched.
         */
        public function regexp($field, $pattern)
        {
            $pattern = str_replace('/', '\/', $pattern);
            $pattern = "/" . $pattern . "/i";

            return preg_match($pattern, $field);
        }

        /**
         * Method to emulate MySQL CONCAT() function.
         *
         * SQLite does have CONCAT() function, but it has a different syntax from MySQL.
         * So this function must be manipulated here.
         *
         * @param string
         *
         * @return NULL if the argument is null | string conatenated if the argument is given.
         */
        public function concat()
        {
            $returnValue = "";
            $argsNum = func_num_args();
            $argsList = func_get_args();
            for ($i = 0; $i < $argsNum; $i++) {
                if (is_null($argsList[$i])) {
                    return null;
                }
                $returnValue .= $argsList[$i];
            }

            return $returnValue;
        }

        /**
         * Method to emulate MySQL FIELD() function.
         *
         * This function gets the list argument and compares the first item to all the others.
         * If the same value is found, it returns the position of that value. If not, it
         * returns 0.
         *
         * @param int...|float... variable number of string, integer or double
         *
         * @return int unsigned integer
         */
        public function field()
        {
            global $wpdb;
            $numArgs = func_num_args();
            if ($numArgs < 2 or is_null(func_get_arg(0))) {
                return 0;
            } else {
                $arg_list = func_get_args();
            }
            $searchString = array_shift($arg_list);
            $str_to_check = substr($searchString, 0, strpos($searchString, '.'));
            $str_to_check = str_replace($wpdb->prefix, '', $str_to_check);
            if ($str_to_check && in_array(trim($str_to_check), $wpdb->tables)) {
                return 0;
            }
            for ($i = 0; $i < $numArgs - 1; $i++) {
                if ($searchString === strtolower($arg_list[$i])) {
                    return $i + 1;
                }
            }

            return 0;
        }

        /**
         * Method to emulate MySQL LOG() function.
         *
         * Used with one argument, it returns the natural logarithm of X.
         * <code>
         * LOG(X)
         * </code>
         * Used with two arguments, it returns the natural logarithm of X base B.
         * <code>
         * LOG(B, X)
         * </code>
         * In this case, it returns the value of log(X) / log(B).
         *
         * Used without an argument, it returns false. This returned value will be
         * rewritten to 0, because SQLite doesn't understand true/false value.
         *
         * @param integer representing the base of the logarithm, which is optional.
         * @param double value to turn into logarithm.
         *
         * @return double | NULL
         */
        public function log()
        {
            $numArgs = func_num_args();
            if ($numArgs == 1) {
                $arg1 = func_get_arg(0);

                return log($arg1);
            } elseif ($numArgs == 2) {
                $arg1 = func_get_arg(0);
                $arg2 = func_get_arg(1);

                return log($arg1) / log($arg2);
            } else {
                return null;
            }
        }

        /**
         * Method to emulate MySQL LEAST() function.
         *
         * This function rewrites the function name to SQLite compatible function name.
         *
         * @return mixed
         */
        public function least()
        {
            $arg_list = func_get_args();

            return "min($arg_list)";
        }

        /**
         * Method to emulate MySQL GREATEST() function.
         *
         * This function rewrites the function name to SQLite compatible function name.
         *
         * @return mixed
         */
        public function greatest()
        {
            $arg_list = func_get_args();

            return "max($arg_list)";
        }

        /**
         * Method to dummy out MySQL GET_LOCK() function.
         *
         * This function is meaningless in SQLite, so we do nothing.
         *
         * @param string $name
         * @param integer $timeout
         *
         * @return string
         */
        public function get_lock($name, $timeout)
        {
            return '1=1';
        }

        /**
         * Method to dummy out MySQL RELEASE_LOCK() function.
         *
         * This function is meaningless in SQLite, so we do nothing.
         *
         * @param string $name
         *
         * @return string
         */
        public function release_lock($name)
        {
            return '1=1';
        }

        /**
         * Method to emulate MySQL UCASE() function.
         *
         * This is MySQL alias for upper() function. This function rewrites it
         * to SQLite compatible name upper().
         *
         * @param string
         *
         * @return string SQLite compatible function name.
         */
        public function ucase($string)
        {
            return "upper($string)";
        }

        /**
         * Method to emulate MySQL LCASE() function.
         *
         *
         * This is MySQL alias for lower() function. This function rewrites it
         * to SQLite compatible name lower().
         *
         * @param string
         *
         * @return string SQLite compatible function name.
         */
        public function lcase($string)
        {
            return "lower($string)";
        }

        /**
         * Method to emulate MySQL INET_NTOA() function.
         *
         * This function gets 4 or 8 bytes integer and turn it into the network address.
         *
         * @param unsigned long integer
         *
         * @return string
         */
        public function inet_ntoa($num)
        {
            return long2ip($num);
        }

        /**
         * Method to emulate MySQL INET_ATON() function.
         *
         * This function gets the network address and turns it into integer.
         *
         * @param string
         *
         * @return int long integer
         */
        public function inet_aton($addr)
        {
            return absint(ip2long($addr));
        }

        /**
         * Method to emulate MySQL DATEDIFF() function.
         *
         * This function compares two dates value and returns the difference.
         *
         * @param string start
         * @param string end
         *
         * @return string
         */
        public function datediff($start, $end)
        {
			$start_date = new DateTime($start);
			$end_date = new DateTime($end);
			$interval = $end_date->diff($start_date, false);

			return $interval->format('%r%a');
        }

        /**
         * Method to emulate MySQL LOCATE() function.
         *
         * This function returns the position if $substr is found in $str. If not,
         * it returns 0. If mbstring extension is loaded, mb_strpos() function is
         * used.
         *
         * @param string needle
         * @param string haystack
         * @param integer position
         *
         * @return integer
         */
        public function locate($substr, $str, $pos = 0)
        {
            if (! extension_loaded('mbstring')) {
                if (($val = strpos($str, $substr, $pos)) !== false) {
                    return $val + 1;
                } else {
                    return 0;
                }
            } else {
                if (($val = mb_strpos($str, $substr, $pos)) !== false) {
                    return $val + 1;
                } else {
                    return 0;
                }
            }
        }

        /**
         * Method to return GMT date in the string format.
         *
         * @param none
         *
         * @return string formatted GMT date 'dddd-mm-dd'
         */
        public function utc_date()
        {
            return gmdate('Y-m-d', time());
        }

        /**
         * Method to return GMT time in the string format.
         *
         * @param none
         *
         * @return string formatted GMT time '00:00:00'
         */
        public function utc_time()
        {
            return gmdate('H:i:s', time());
        }

        /**
         * Method to return GMT time stamp in the string format.
         *
         * @param none
         *
         * @return string formatted GMT timestamp 'yyyy-mm-dd 00:00:00'
         */
        public function utc_timestamp()
        {
            return gmdate('Y-m-d H:i:s', time());
        }

        /**
         * Method to return MySQL version.
         *
         * This function only returns the current newest version number of MySQL,
         * because it is meaningless for SQLite database.
         *
         * @param none
         *
         * @return string representing the version number: major_version.minor_version
         */
        public function version()
        {
            //global $required_mysql_version;
            //return $required_mysql_version;
            return '5.5';
        }
    }

    /**
     * This class extends PDO class and does the real work.
     *
     * It accepts a request from wpdb class, initialize PDO instance,
     * execute SQL statement, and returns the results to WordPress.
     */
    class PDOEngine extends PDO
    {
        /**
         * Class variable to check if there is an error.
         *
         * @var boolean
         */
        public $is_error = false;
        /**
         * Class variable which is used for CALC_FOUND_ROW query.
         *
         * @var unsigned integer
         */
        public $found_rows_result = null;
        /**
         * Class variable used for query with ORDER BY FIELD()
         *
         * @var array of the object
         */
        public $pre_ordered_results = null;
        /**
         * Class variable to store the rewritten queries.
         *
         * @var array
         * @access private
         */
        private $rewritten_query;
        /**
         * Class variable to have what kind of query to execute.
         *
         * @var string
         * @access private
         */
        private $query_type;
        /**
         * Class variable to store the result of the query.
         *
         * @var array reference to the PHP object
         * @access private
         */
        private $results = null;
        /**
         * Class variable to store the results of the query.
         *
         * This is for the backward compatibility.
         *
         * @var array reference to the PHP object
         * @access private
         */
        private $_results = null;
        /**
         * Class variable to reference to the PDO instance.
         *
         * @var PDO object
         * @access private
         */
        private $pdo;
        /**
         * Class variable to store the query string prepared to execute.
         *
         * @var string|array
         */
        private $prepared_query;
        /**
         * Class variable to store the values in the query string.
         *
         * @var array
         * @access private
         */
        private $extracted_variables = [];
        /**
         * Class variable to store the error messages.
         *
         * @var array
         * @access private
         */
        private $error_messages = [];
        /**
         * Class variable to store the file name and function to cause error.
         *
         * @var array
         * @access private
         */
        private $errors;
        /**
         * Class variable to store the query strings.
         *
         * @var array
         */
        public $queries = [];
        /**
         * Class variable to store the affected row id.
         *
         * @var unsigned integer
         * @access private
         */
        private $last_insert_id;
        /**
         * Class variable to store the number of rows affected.
         *
         * @var unsigned integer
         */
        private $affected_rows;
        /**
         * Class variable to store the queried column info.
         *
         * @var array
         */
        private $column_data;
        /**
         * Variable to emulate MySQL affected row.
         *
         * @var integer
         */
        private $num_rows;
        /**
         * Return value from query().
         *
         * Each query has its own return value.
         *
         * @var mixed
         */
        private $return_value;
        /**
         * Variable to determine which insert query to use.
         *
         * Whether VALUES clause in the INSERT query can take multiple values or not
         * depends on the version of SQLite library. We check the version and set
         * this varable to true or false.
         *
         * @var boolean
         */
        private $can_insert_multiple_rows = false;
        /**
         *
         * @var integer
         */
        private $param_num;
        /**
         * Varible to check if there is an active transaction.
         * @var boolean
         * @access protected
         */
        protected $has_active_transaction = false;

        /**
         * Constructor
         *
         * Create PDO object, set user defined functions and initialize other settings.
         * Don't use parent::__construct() because this class does not only returns
         * PDO instance but many others jobs.
         *
         * Constructor definition is changed since version 1.7.1.
         *
         * @param none
         */
        function __construct()
        {
            register_shutdown_function([$this, '__destruct']);
            if (! is_file(FQDB)) {
                $this->prepare_directory();
            }
            $dsn = 'sqlite:' . FQDB;
            if (isset($GLOBALS['@pdo'])) {
                $this->pdo = $GLOBALS['@pdo'];
            } else {
                $locked = false;
                $status = 0;
                do {
                    try {
                        $this->pdo = new PDO($dsn, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                        new PDOSQLiteUDFS($this->pdo);
                        $GLOBALS['@pdo'] = $this->pdo;
                    } catch (PDOException $ex) {
                        $status = $ex->getCode();
                        if ($status == 5 || $status == 6) {
                            $locked = true;
                        } else {
                            $err_message = $ex->getMessage();
                        }
                    }
                } while ($locked);
                if ($status > 0) {
                    $message = 'Database initialization error!<br />' .
                        'Code: ' . $status .
                        (isset($err_message) ? '<br />Error Message: ' . $err_message : '');
                    $this->set_error(__LINE__, __FILE__, $message);

                    return false;
                }
            }
            $this->init();
        }

        /**
         * Destructor
         *
         * If SQLITE_MEM_DEBUG constant is defined, append information about
         * memory usage into database/mem_debug.txt.
         *
         * This definition is changed since version 1.7.
         *
         * @return boolean
         */
        function __destruct()
        {
            if (defined('SQLITE_MEM_DEBUG') && SQLITE_MEM_DEBUG) {
                $max = ini_get('memory_limit');
                if (is_null($max)) {
                    $message = sprintf("[%s] Memory_limit is not set in php.ini file.",
                        date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
                    file_put_contents(FQDBDIR . 'mem_debug.txt', $message, FILE_APPEND);

                    return true;
                }
                if (stripos($max, 'M') !== false) {
                    $max = (int) $max * 1024 * 1024;
                }
                $peak = memory_get_peak_usage(true);
                $used = round((int) $peak / (int) $max * 100, 2);
                if ($used > 90) {
                    $message = sprintf("[%s] Memory peak usage warning: %s %% used. (max: %sM, now: %sM)\n",
                        date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $used, $max, $peak);
                    file_put_contents(FQDBDIR . 'mem_debug.txt', $message, FILE_APPEND);
                }
            }

            //$this->pdo = null;
            return true;
        }

        /**
         * Method to initialize database, executed in the constructor.
         *
         * It checks if WordPress is in the installing process and does the required
         * jobs. SQLite library version specific settings are also in this function.
         *
         * Some developers use WP_INSTALLING constant for other purposes, if so, this
         * function will do no harms.
         */
        private function init()
        {
            if (version_compare($this->get_sqlite_version(), '3.7.11', '>=')) {
                $this->can_insert_multiple_rows = true;
            }
            $statement = $this->pdo->query('PRAGMA foreign_keys');
            if ($statement->fetchColumn(0) == '0') {
                $this->pdo->query('PRAGMA foreign_keys = ON');
            }
        }

        /**
         * This method makes database direcotry and .htaccess file.
         *
         * It is executed only once when the installation begins.
         */
        private function prepare_directory()
        {
            global $wpdb;
            $u = umask(0000);
            if (! is_dir(FQDBDIR)) {
                if (! @mkdir(FQDBDIR, 0704, true)) {
                    umask($u);
                    $message = 'Unable to create the required directory! Please check your server settings.';
                    wp_die($message, 'Error!');
                }
            }
            if (! is_writable(FQDBDIR)) {
                umask($u);
                $message = 'Unable to create a file in the directory! Please check your server settings.';
                wp_die($message, 'Error!');
            }
            if (! is_file(FQDBDIR . '.htaccess')) {
                $fh = fopen(FQDBDIR . '.htaccess', "w");
                if (! $fh) {
                    umask($u);
                    $message = 'Unable to create a file in the directory! Please check your server settings.';
                    echo $message;

                    return false;
                }
                fwrite($fh, 'DENY FROM ALL');
                fclose($fh);
            }
            if (! is_file(FQDBDIR . 'index.php')) {
                $fh = fopen(FQDBDIR . 'index.php', "w");
                if (! $fh) {
                    umask($u);
                    $message = 'Unable to create a file in the directory! Please check your server settings.';
                    echo $message;

                    return false;
                }
                fwrite($fh, '<?php // Silence is gold. ?>');
                fclose($fh);
            }
            umask($u);

            return true;
        }

        /**
         * Method to execute query().
         *
         * Divide the query types into seven different ones. That is to say:
         *
         * 1. SELECT SQL_CALC_FOUND_ROWS
         * 2. INSERT
         * 3. CREATE TABLE(INDEX)
         * 4. ALTER TABLE
         * 5. SHOW VARIABLES
         * 6. DROP INDEX
         * 7. THE OTHERS
         *
         * #1 is just a tricky play. See the private function handle_sql_count() in query.class.php.
         * From #2 through #5 call different functions respectively.
         * #6 call the ALTER TABLE query.
         * #7 is a normal process: sequentially call prepare_query() and execute_query().
         *
         * #1 process has been changed since version 1.5.1.
         *
         * @param string $statement full SQL statement string
         *
         * @param int $mode
         * @param array $fetch_mode_args
         *
         * @return mixed according to the query type
         * @see PDO::query()
         */
        #[\ReturnTypeWillChange]
        public function query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, ...$fetch_mode_args)
        {
            $this->flush();

            $this->queries[] = "Raw query:\n$statement";
            $res = $this->determine_query_type($statement);
            if (! $res && defined('PDO_DEBUG') && PDO_DEBUG) {
                $bailoutString = sprintf(__("<h1>Unknown query type</h1><p>Sorry, we cannot determine the type of query that is requested.</p><p>The query is %s</p>",
                    'sqlite-integration'), $statement);
                $this->set_error(__LINE__, __FUNCTION__, $bailoutString);
            }
            switch (strtolower($this->query_type)) {
                case 'set':
                    $this->return_value = false;
                    break;
                case 'foundrows':
                    $_column = ['FOUND_ROWS()' => ''];
                    $column = [];
                    if (! is_null($this->found_rows_result)) {
                        $this->num_rows = $this->found_rows_result;
                        $_column['FOUND_ROWS()'] = $this->num_rows;
                        //foreach ($this->found_rows_result[0] as $key => $value) {
                        //$_column['FOUND_ROWS()'] = $value;
                        //}
                        $column[] = new ObjectArray($_column);
                        $this->results = $column;
                        $this->found_rows_result = null;
                    }
                    break;
                case 'insert':
                    if ($this->can_insert_multiple_rows) {
                        $this->execute_insert_query_new($statement);
                    } else {
                        $this->execute_insert_query($statement);
                    }
                    break;
                case 'create':
                    $result = $this->execute_create_query($statement);
                    $this->return_value = $result;
                    break;
                case 'alter':
                    $result = $this->execute_alter_query($statement);
                    $this->return_value = $result;
                    break;
                case 'show_variables':
                    $this->return_value = $this->show_variables_workaround($statement);
                    break;
                case 'showstatus':
                    $this->return_value = $this->show_status_workaround($statement);
                    break;
                case 'drop_index':
                    $pattern = '/^\\s*(DROP\\s*INDEX\\s*.*?)\\s*ON\\s*(.*)/im';
                    if (preg_match($pattern, $statement, $match)) {
                        $drop_query = 'ALTER TABLE ' . trim($match[2]) . ' ' . trim($match[1]);
                        $this->query_type = 'alter';
                        $result = $this->execute_alter_query($drop_query);
                        $this->return_value = $result;
                    } else {
                        $this->return_value = false;
                    }
                    break;
                default:
                    $engine = $this->prepare_engine($this->query_type);
                    $this->rewritten_query = $engine->rewrite_query($statement, $this->query_type);
                    if (! is_null($this->pre_ordered_results)) {
                        $this->results = $this->pre_ordered_results;
                        $this->num_rows = $this->return_value = count($this->results);
                        $this->pre_ordered_results = null;
                        break;
                    }
                    $this->queries[] = "Rewritten:\n$this->rewritten_query";
                    $this->extract_variables();
                    $prepared_query = $this->prepare_query();
                    $this->execute_query($prepared_query);
                    if (! $this->is_error) {
                        $this->process_results($engine);
                    } else {
                        // Error
                    }
                    break;
            }
            if (defined('PDO_DEBUG') && PDO_DEBUG === true) {
                file_put_contents(FQDBDIR . 'debug.txt', $this->get_debug_info(), FILE_APPEND);
            }

            return $this->return_value;
        }

        /**
         * Method to return inserted row id.
         */
        public function get_insert_id()
        {
            return $this->last_insert_id;
        }

        /**
         * Method to return the number of rows affected.
         */
        public function get_affected_rows()
        {
            return $this->affected_rows;
        }

        /**
         * Method to return the queried column names.
         *
         * These data are meaningless for SQLite. So they are dummy emulating
         * MySQL columns data.
         *
         * @return array of the object
         */
        public function get_columns()
        {
            if (! empty($this->results)) {
                $primary_key = [
                    'meta_id',
                    'comment_ID',
                    'link_ID',
                    'option_id',
                    'blog_id',
                    'option_name',
                    'ID',
                    'term_id',
                    'object_id',
                    'term_taxonomy_id',
                    'umeta_id',
                    'id',
                ];
                $unique_key = ['term_id', 'taxonomy', 'slug'];
                $data = [
                    'name' => '', // column name
                    'table' => '', // table name
                    'max_length' => 0,  // max length of the column
                    'not_null' => 1,  // 1 if not null
                    'primary_key' => 0,  // 1 if column has primary key
                    'unique_key' => 0,  // 1 if column has unique key
                    'multiple_key' => 0,  // 1 if column doesn't have unique key
                    'numeric' => 0,  // 1 if column has numeric value
                    'blob' => 0,  // 1 if column is blob
                    'type' => '', // type of the column
                    'unsigned' => 0,  // 1 if column is unsigned integer
                    'zerofill' => 0   // 1 if column is zero-filled
                ];
                if (preg_match("/\s*FROM\s*(.*)?\s*/i", $this->rewritten_query, $match)) {
                    $table_name = trim($match[1]);
                } else {
                    $table_name = '';
                }
                foreach ($this->results[0] as $key => $value) {
                    $data['name'] = $key;
                    $data['table'] = $table_name;
                    if (in_array($key, $primary_key)) {
                        $data['primary_key'] = 1;
                    } elseif (in_array($key, $unique_key)) {
                        $data['unique_key'] = 1;
                    } else {
                        $data['multiple_key'] = 1;
                    }
                    $this->column_data[] = new ObjectArray($data);
                    $data['name'] = '';
                    $data['table'] = '';
                    $data['primary_key'] = 0;
                    $data['unique_key'] = 0;
                    $data['multiple_key'] = 0;
                }

                return $this->column_data;
            } else {
                return null;
            }
        }

        /**
         * Method to return the queried result data.
         *
         * @return mixed
         */
        public function get_query_results()
        {
            return $this->results;
        }

        /**
         * Method to return the number of rows from the queried result.
         */
        public function get_num_rows()
        {
            return $this->num_rows;
        }

        /**
         * Method to return the queried results according to the query types.
         *
         * @return mixed
         */
        public function get_return_value()
        {
            return $this->return_value;
        }

        /**
         * Method to return error messages.
         *
         * @return string
         */
        public function get_error_message()
        {
            if (count($this->error_messages) === 0) {
                $this->is_error = false;
                $this->error_messages = [];

                return '';
            }
            $output = '<div style="clear:both">&nbsp;</div>';
            if ($this->is_error === false) {
                //return $output;
                return '';
            }
            $output .= "<div class=\"queries\" style=\"clear:both; margin_bottom:2px; border: red dotted thin;\">Queries made or created this session were<br/>\r\n\t<ol>\r\n";
            foreach ($this->queries as $q) {
                $output .= "\t\t<li>" . $q . "</li>\r\n";
            }
            $output .= "\t</ol>\r\n</div>";
            foreach ($this->error_messages as $num => $m) {
                $output .= "<div style=\"clear:both; margin_bottom:2px; border: red dotted thin;\" class=\"error_message\" style=\"border-bottom:dotted blue thin;\">Error occurred at line {$this->errors[$num]['line']} in Function {$this->errors[$num]['function']}. <br/> Error message was: $m </div>";
            }

            ob_start();
            debug_print_backtrace();
            $output .= '<pre>' . ob_get_contents() . '</pre>';
            ob_end_clean();

            return $output;

        }

        /**
         * Method to return information about query string for debugging.
         *
         * @return string
         */
        private function get_debug_info()
        {
            $output = '';
            foreach ($this->queries as $q) {
                $output .= $q . "\n";
            }

            return $output;
        }

        /**
         * Method to clear previous data.
         */
        private function flush()
        {
            $this->rewritten_query = '';
            $this->query_type = '';
            $this->results = null;
            $this->_results = null;
            $this->last_insert_id = null;
            $this->affected_rows = null;
            $this->column_data = [];
            $this->num_rows = null;
            $this->return_value = null;
            $this->extracted_variables = [];
            $this->error_messages = [];
            $this->is_error = false;
            $this->queries = [];
            $this->param_num = 0;
        }

        /**
         * Method to include the apropreate class files.
         *
         * It is not a good habit to change the include files programatically.
         * Needs to be fixed some other way.
         *
         * @param string $query_type
         *
         * @return object reference to apropreate driver
         */
        private function prepare_engine($query_type = null)
        {
            if (stripos($query_type, 'create') !== false) {
                $engine = new CreateQuery();
            } elseif (stripos($query_type, 'alter') !== false) {
                $engine = new AlterQuery();
            } else {
                $engine = new PDOSQLiteDriver();
            }

            return $engine;
        }

        /**
         * Method to create a PDO statement object from the query string.
         *
         * @return PDOStatement
         */
        private function prepare_query()
        {
            $this->queries[] = "Prepare:\n" . $this->prepared_query;
            $reason = 0;
            $message = '';
            $statement = null;
            do {
                try {
                    $statement = $this->pdo->prepare($this->prepared_query);
                } catch (PDOException $err) {
                    $reason = $err->getCode();
                    $message = $err->getMessage();
                }
            } while (5 == $reason || 6 == $reason);

            if ($reason > 0) {
                $err_message = sprintf("Problem preparing the PDO SQL Statement.  Error was: %s", $message);
                $this->set_error(__LINE__, __FUNCTION__, $err_message);
            }

            return $statement;
        }

        /**
         * Method to execute PDO statement object.
         *
         * This function executes query and sets the variables to give back to WordPress.
         * The variables are class fields. So if success, no return value. If failure, it
         * returns void and stops.
         *
         * @param object $statement of PDO statement
         *
         * @return boolean
         */
        private function execute_query($statement)
        {
            $reason = 0;
            $message = '';
            if (! is_object($statement)) {
                return false;
            }
            if (count($this->extracted_variables) > 0) {
                $this->queries[] = "Executing:\n" . var_export($this->extracted_variables, true);
                do {
                    if ($this->query_type == 'update' || $this->query_type == 'replace') {
                        try {
                            $this->beginTransaction();
                            $statement->execute($this->extracted_variables);
                            $this->commit();
                        } catch (PDOException $err) {
                            $reason = $err->getCode();
                            $message = $err->getMessage();
                            $this->rollBack();
                        }
                    } else {
                        try {
                            $statement->execute($this->extracted_variables);
                        } catch (PDOException $err) {
                            $reason = $err->getCode();
                            $message = $err->getMessage();
                        }
                    }
                } while (5 == $reason || 6 == $reason);
            } else {
                $this->queries[] = 'Executing: (no parameters)';
                do {
                    if ($this->query_type == 'update' || $this->query_type == 'replace') {
                        try {
                            $this->beginTransaction();
                            $statement->execute();
                            $this->commit();
                        } catch (PDOException $err) {
                            $reason = $err->getCode();
                            $message = $err->getMessage();
                            $this->rollBack();
                        }
                    } else {
                        try {
                            $statement->execute();
                        } catch (PDOException $err) {
                            $reason = $err->getCode();
                            $message = $err->getMessage();
                        }
                    }
                } while (5 == $reason || 6 == $reason);
            }
            if ($reason > 0) {
                $err_message = sprintf("Error while executing query! Error message was: %s", $message);
                $this->set_error(__LINE__, __FUNCTION__, $err_message);

                return false;
            } else {
                $this->_results = $statement->fetchAll(PDO::FETCH_OBJ);
            }
            //generate the results that $wpdb will want to see
            switch ($this->query_type) {
                case 'insert':
                case 'update':
                case 'replace':
                    $this->last_insert_id = $this->pdo->lastInsertId();
                    $this->affected_rows = $statement->rowCount();
                    $this->return_value = $this->affected_rows;
                    break;
                case 'select':
                case 'show':
                case 'showcolumns':
                case 'showindex':
                case 'describe':
                case 'desc':
                case 'check':
                case 'analyze':
                    //case "foundrows":
                    $this->num_rows = count($this->_results);
                    $this->return_value = $this->num_rows;
                    break;
                case 'delete':
                    $this->affected_rows = $statement->rowCount();
                    $this->return_value = $this->affected_rows;
                    break;
                case 'alter':
                case 'drop':
                case 'create':
                case 'optimize':
                case 'truncate':
                    if ($this->is_error) {
                        $this->return_value = false;
                    } else {
                        $this->return_value = true;
                    }
                    break;
            }
        }

        /**
         * Method to extract field data to an array and prepare the query statement.
         *
         * If original SQL statement is CREATE query, this function does nothing.
         */
        private function extract_variables()
        {
            if ($this->query_type == 'create') {
                $this->prepared_query = $this->rewritten_query;

                return;
            }

            //long queries can really kill this
            $pattern = '/(?<!\\\\)([\'"])(.*?)(?<!\\\\)\\1/imsx';
            $_limit = $limit = ini_get('pcre.backtrack_limit');
            // if user's setting is more than default * 10, make PHP do the job.
            if ($limit > 10000000) {
                $query = preg_replace_callback($pattern, [$this, 'replace_variables_with_placeholders'],
                    $this->rewritten_query);
            } else {
                do {
                    if ($limit > 10000000) {
                        $this->set_error(__LINE__, __FUNCTION__, 'The query is too big to parse properly');
                        break; //no point in continuing execution, would get into a loop
                    } else {
                        ini_set('pcre.backtrack_limit', $limit);
                        $query = preg_replace_callback($pattern, [$this, 'replace_variables_with_placeholders'],
                            $this->rewritten_query);
                    }
                    $limit = $limit * 10;
                } while (is_null($query));

                //reset the pcre.backtrack_limit
                ini_set('pcre.backtrack_limit', $_limit);
            }

            if (isset($query)) {
                $this->queries[] = "With Placeholders:\n" . $query;
                $this->prepared_query = $query;
            }
        }

        /**
         * Call back function to replace field data with PDO parameter.
         *
         * @param string $matches
         *
         * @return string
         */
        private function replace_variables_with_placeholders($matches)
        {
            //remove the wordpress escaping mechanism
            $param = stripslashes($matches[0]);

            //remove trailing spaces
            $param = trim($param);

            //remove the quotes at the end and the beginning
            if (in_array($param[strlen($param) - 1], ["'", '"'])) {
                $param = substr($param, 0, -1);//end
            }
            if (in_array($param[0], ["'", '"'])) {
                $param = substr($param, 1); //start
            }
            //$this->extracted_variables[] = $param;
            $key = ':param_' . $this->param_num++;
            $this->extracted_variables[] = $param;
            //return the placeholder
            //return ' ? ';
            return ' ' . $key . ' ';
        }

        /**
         * Method to determine which query type the argument is.
         *
         * It takes the query string ,determines the type and returns the type string.
         * If the query is the type that SQLite Integration can't executes, returns false.
         *
         * @param string $query
         *
         * @return boolean|string
         */
        private function determine_query_type($query)
        {
            $result = preg_match('/^\\s*(SET|EXPLAIN|PRAGMA|SELECT\\s*FOUND_ROWS|SELECT|INSERT|UPDATE|REPLACE|DELETE|ALTER|CREATE|DROP\\s*INDEX|DROP|SHOW\\s*\\w+\\s*\\w+\\s*|DESCRIBE|DESC|TRUNCATE|OPTIMIZE|CHECK|ANALYZE)/i',
                $query, $match);

            if (! $result) {
                return false;
            }
            $this->query_type = strtolower($match[1]);
            if (stripos($this->query_type, 'found') !== false) {
                $this->query_type = 'foundrows';
            }
            if (stripos($this->query_type, 'show') !== false) {
                if (stripos($this->query_type, 'show table status') !== false) {
                    $this->query_type = 'showstatus';
                } elseif (stripos($this->query_type, 'show tables') !== false || stripos($this->query_type,
                        'show full tables') !== false) {
                    $this->query_type = 'show';
                } elseif (stripos($this->query_type, 'show columns') !== false || stripos($this->query_type,
                        'show fields') !== false || stripos($this->query_type, 'show full columns') !== false) {
                    $this->query_type = 'showcolumns';
                } elseif (stripos($this->query_type, 'show index') !== false || stripos($this->query_type,
                        'show indexes') !== false || stripos($this->query_type, 'show keys') !== false) {
                    $this->query_type = 'showindex';
                } elseif (stripos($this->query_type, 'show variables') !== false || stripos($this->query_type,
                        'show global variables') !== false || stripos($this->query_type,
                        'show session variables') !== false) {
                    $this->query_type = 'show_variables';
                } else {
                    return false;
                }
            }
            if (stripos($this->query_type, 'drop index') !== false) {
                $this->query_type = 'drop_index';
            }

            return true;
        }

        /**
         * Method to execute INSERT query for SQLite version 3.7.11 or later.
         *
         * SQLite version 3.7.11 began to support multiple rows insert with values
         * clause. This is for that version or later.
         *
         * @param string $query
         */
        private function execute_insert_query_new($query)
        {
            $engine = $this->prepare_engine($this->query_type);
            $this->rewritten_query = $engine->rewrite_query($query, $this->query_type);
            $this->queries[] = "Rewritten:\n" . $this->rewritten_query;
            $this->extract_variables();
            $statement = $this->prepare_query();
            $this->execute_query($statement);
        }

        /**
         * Method to execute INSERT query for SQLite version 3.7.10 or lesser.
         *
         * It executes the INSERT query for SQLite version 3.7.10 or lesser. It is
         * necessary to rewrite multiple row values.
         *
         * @param string $query
         */
        private function execute_insert_query($query)
        {
            global $wpdb;
            $multi_insert = false;
            $statement = null;
            $engine = $this->prepare_engine($this->query_type);
            if (preg_match('/(INSERT.*?VALUES\\s*)(\(.*\))/imsx', $query, $matched)) {
                $query_prefix = $matched[1];
                $values_data = $matched[2];
                if (stripos($values_data, 'ON DUPLICATE KEY') !== false) {
                    $exploded_parts = $values_data;
                } elseif (stripos($query_prefix, "INSERT INTO $wpdb->comments") !== false) {
                    $exploded_parts = $values_data;
                } else {
                    $exploded_parts = $this->parse_multiple_inserts($values_data);
                }
                $count = count($exploded_parts);
                if ($count > 1) {
                    $multi_insert = true;
                }
            }
            if ($multi_insert) {
                $first = true;
                foreach ($exploded_parts as $value) {
                    if (substr($value, -1, 1) === ')') {
                        $suffix = '';
                    } else {
                        $suffix = ')';
                    }
                    $query_string = $query_prefix . ' ' . $value . $suffix;
                    $this->rewritten_query = $engine->rewrite_query($query_string, $this->query_type);
                    $this->queries[] = "Rewritten:\n" . $this->rewritten_query;
                    $this->extracted_variables = [];
                    $this->extract_variables();
                    if ($first) {
                        $statement = $this->prepare_query();
                        $this->execute_query($statement);
                        $first = false;
                    } else {
                        $this->execute_query($statement);
                    }
                }
            } else {
                $this->rewritten_query = $engine->rewrite_query($query, $this->query_type);
                $this->queries[] = "Rewritten:\n" . $this->rewritten_query;
                $this->extract_variables();
                $statement = $this->prepare_query();
                $this->execute_query($statement);
            }
        }

        /**
         * Method to help rewriting multiple row values insert query.
         *
         * It splits the values clause into an array to execute separately.
         *
         * @param string $values
         *
         * @return array
         */
        private function parse_multiple_inserts($values)
        {
            $tokens = preg_split("/(''|(?<!\\\\)'|(?<!\()\),(?=\s*\())/s", $values, -1, PREG_SPLIT_DELIM_CAPTURE);
            $exploded_parts = [];
            $part = '';
            $literal = false;
            foreach ($tokens as $token) {
                switch ($token) {
                    case "),":
                        if (! $literal) {
                            $exploded_parts[] = $part;
                            $part = '';
                        } else {
                            $part .= $token;
                        }
                        break;
                    case "'":
                        if ($literal) {
                            $literal = false;
                        } else {
                            $literal = true;
                        }
                        $part .= $token;
                        break;
                    default:
                        $part .= $token;
                        break;
                }
            }
            if (! empty($part)) {
                $exploded_parts[] = $part;
            }

            return $exploded_parts;
        }

        /**
         * Method to execute CREATE query.
         *
         * @param string
         *
         * @return boolean
         */
        private function execute_create_query($query)
        {
            $engine = $this->prepare_engine($this->query_type);
            $rewritten_query = $engine->rewrite_query($query);
            $reason = 0;
            $message = '';
            //$queries = explode(";", $this->rewritten_query);
            try {
                $this->beginTransaction();
                foreach ($rewritten_query as $single_query) {
                    $this->queries[] = "Executing:\n" . $single_query;
                    $single_query = trim($single_query);
                    if (empty($single_query)) {
                        continue;
                    }
                    $this->pdo->exec($single_query);
                }
                $this->commit();
            } catch (PDOException $err) {
                $reason = $err->getCode();
                $message = $err->getMessage();
                if (5 == $reason || 6 == $reason) {
                    $this->commit();
                } else {
                    $this->rollBack();
                }
            }
            if ($reason > 0) {
                $err_message = sprintf("Problem in creating table or index. Error was: %s", $message);
                $this->set_error(__LINE__, __FUNCTION__, $err_message);

                return false;
            }

            return true;
        }

        /**
         * Method to execute ALTER TABLE query.
         *
         * @param string
         *
         * @return boolean
         */
        private function execute_alter_query($query)
        {
            $engine = $this->prepare_engine($this->query_type);
            $reason = 0;
            $message = '';
            $re_query = '';
            $rewritten_query = $engine->rewrite_query($query, $this->query_type);
            if (is_array($rewritten_query) && array_key_exists('recursion', $rewritten_query)) {
                $re_query = $rewritten_query['recursion'];
                unset($rewritten_query['recursion']);
            }
            try {
                $this->beginTransaction();
                if (is_array($rewritten_query)) {
                    foreach ($rewritten_query as $single_query) {
                        $this->queries[] = "Executing:\n" . $single_query;
                        $single_query = trim($single_query);
                        if (empty($single_query)) {
                            continue;
                        }
                        $this->pdo->exec($single_query);
                    }
                } else {
                    $this->queries[] = "Executing:\n" . $rewritten_query;
                    $rewritten_query = trim($rewritten_query);
                    $this->pdo->exec($rewritten_query);
                }
                $this->commit();
            } catch (PDOException $err) {
                $reason = $err->getCode();
                $message = $err->getMessage();
                if (5 == $reason || 6 == $reason) {
                    $this->commit();
                    usleep(10000);
                } else {
                    $this->rollBack();
                }
            }
            if ($re_query != '') {
                $this->query($re_query);
            }
            if ($reason > 0) {
                $err_message = sprintf("Problem in executing alter query. Error was: %s", $message);
                $this->set_error(__LINE__, __FUNCTION__, $err_message);

                return false;
            }

            return true;
        }

        /**
         * Method to execute SHOW VARIABLES query
         *
         * This query is meaningless for SQLite. This function returns null data with some
         * exceptions and only avoids the error message.
         *
         * @param string
         *
         * @return bool
         */
        private function show_variables_workaround($query)
        {
            $dummy_data = ['Variable_name' => '', 'Value' => null];
            $pattern = '/SHOW\\s*VARIABLES\\s*LIKE\\s*(.*)?$/im';
            if (preg_match($pattern, $query, $match)) {
                $value = str_replace("'", '', $match[1]);
                $dummy_data['Variable_name'] = trim($value);
                // this is set for Wordfence Security Plugin
                if ($value == 'max_allowed_packet') {
                    $dummy_data['Value'] = 1047552;
                } else {
                    $dummy_data['Value'] = '';
                }
            }
            $_results[] = new ObjectArray($dummy_data);
            $this->results = $_results;
            $this->num_rows = count($this->results);
            $this->return_value = $this->num_rows;

            return true;
        }

        /**
         * Method to execute SHOW TABLE STATUS query.
         *
         * This query is meaningless for SQLite. This function return dummy data.
         *
         * @param string
         *
         * @return bool
         */
        private function show_status_workaround($query)
        {
            $pattern = '/^SHOW\\s*TABLE\\s*STATUS\\s*LIKE\\s*(.*?)$/im';
            if (preg_match($pattern, $query, $match)) {
                $table_name = str_replace("'", '', $match[1]);
            } else {
                $table_name = '';
            }
            $dummy_data = [
                'Name' => $table_name,
                'Engine' => '',
                'Version' => '',
                'Row_format' => '',
                'Rows' => 0,
                'Avg_row_length' => 0,
                'Data_length' => 0,
                'Max_data_length' => 0,
                'Index_length' => 0,
                'Data_free' => 0,
                'Auto_increment' => 0,
                'Create_time' => '',
                'Update_time' => '',
                'Check_time' => '',
                'Collation' => '',
                'Checksum' => '',
                'Create_options' => '',
                'Comment' => '',
            ];
            $_results[] = new ObjectArray($dummy_data);
            $this->results = $_results;
            $this->num_rows = count($this->results);
            $this->return_value = $this->num_rows;

            return true;
        }

        /**
         * Method to format the queried data to that of MySQL.
         *
         * @param string $engine
         */
        private function process_results($engine)
        {
            if (in_array($this->query_type, ['describe', 'desc', 'showcolumns'])) {
                $this->convert_to_columns_object();
            } elseif ('showindex' === $this->query_type) {
                $this->convert_to_index_object();
            } elseif (in_array($this->query_type, ['check', 'analyze'])) {
                $this->convert_result_check_or_analyze();
            } else {
                $this->results = $this->_results;
            }
        }

        /**
         * Method to format the error messages and put out to the file.
         *
         * When $wpdb::suppress_errors is set to true or $wpdb::show_errors is set to false,
         * the error messages are ignored.
         *
         * @param string $line where the error occurred.
         * @param string $function to indicate the function name where the error occurred.
         * @param string $message
         *
         * @return boolean
         */
        private function set_error($line, $function, $message)
        {
            global $wpdb;
            $this->errors[] = ["line" => $line, "function" => $function];
            $this->error_messages[] = $message;
            $this->is_error = true;
            if ($wpdb->suppress_errors) {
                return false;
            }
            if (! $wpdb->show_errors) {
                return false;
            }
            file_put_contents(FQDBDIR . 'debug.txt', "Line $line, Function: $function, Message: $message \n", FILE_APPEND);
        }

        /**
         * Method to change the queried data to PHP object format.
         *
         * It takes the associative array of query results and creates a numeric
         * array of anonymous objects
         *
         * @access private
         */
        private function convert_to_object()
        {
            $_results = [];
            if (count($this->results) === 0) {
                echo $this->get_error_message();
            } else {
                foreach ($this->results as $row) {
                    $_results[] = new ObjectArray($row);
                }
            }
            $this->results = $_results;
        }

        /**
         * Method to convert the SHOW COLUMNS query data to an object.
         *
         * It rewrites pragma results to mysql compatible array
         * when query_type is describe, we use sqlite pragma function.
         *
         * @access private
         */
        private function convert_to_columns_object()
        {
            $_results = [];
            $_columns = [ //Field names MySQL SHOW COLUMNS returns
                'Field' => "",
                'Type' => "",
                'Null' => "",
                'Key' => "",
                'Default' => "",
                'Extra' => "",
            ];
            if (empty($this->_results)) {
                echo $this->get_error_message();
            } else {
                foreach ($this->_results as $row) {
                    $_columns['Field'] = $row->name;
                    $_columns['Type'] = $row->type;
                    $_columns['Null'] = $row->notnull ? "NO" : "YES";
                    $_columns['Key'] = $row->pk ? "PRI" : "";
                    $_columns['Default'] = $row->dflt_value;
                    $_results[] = new ObjectArray($_columns);
                }
            }
            $this->results = $_results;
        }

        /**
         * Method to convert SHOW INDEX query data to PHP object.
         *
         * It rewrites the result of SHOW INDEX to the Object compatible with MySQL
         * added the WHERE clause manipulation (ver 1.3.1)
         *
         * @access private
         */
        private function convert_to_index_object()
        {
            $_results = [];
            $_columns = [
                'Table' => "",
                'Non_unique' => "",// unique -> 0, not unique -> 1
                'Key_name' => "",// the name of the index
                'Seq_in_index' => "",// column sequence number in the index. begins at 1
                'Column_name' => "",
                'Collation' => "",//A(scend) or NULL
                'Cardinality' => "",
                'Sub_part' => "",// set to NULL
                'Packed' => "",// How to pack key or else NULL
                'Null' => "",// If column contains null, YES. If not, NO.
                'Index_type' => "",// BTREE, FULLTEXT, HASH, RTREE
                'Comment' => "",
            ];
            if (count($this->_results) == 0) {
                echo $this->get_error_message();
            } else {
                foreach ($this->_results as $row) {
                    if ($row->type == 'table' && ! stripos($row->sql, 'primary')) {
                        continue;
                    }
                    if ($row->type == 'index' && stripos($row->name, 'sqlite_autoindex') !== false) {
                        continue;
                    }
                    switch ($row->type) {
                        case 'table':
                            $pattern1 = '/^\\s*PRIMARY.*\((.*)\)/im';
                            $pattern2 = '/^\\s*(\\w+)?\\s*.*PRIMARY.*(?!\()/im';
                            if (preg_match($pattern1, $row->sql, $match)) {
                                $col_name = trim($match[1]);
                                $_columns['Key_name'] = 'PRIMARY';
                                $_columns['Non_unique'] = 0;
                                $_columns['Column_name'] = $col_name;
                            } elseif (preg_match($pattern2, $row->sql, $match)) {
                                $col_name = trim($match[1]);
                                $_columns['Key_name'] = 'PRIMARY';
                                $_columns['Non_unique'] = 0;
                                $_columns['Column_name'] = $col_name;
                            }
                            break;
                        case 'index':
                            if (stripos($row->sql, 'unique') !== false) {
                                $_columns['Non_unique'] = 0;
                            } else {
                                $_columns['Non_unique'] = 1;
                            }
                            if (preg_match('/^.*\((.*)\)/i', $row->sql, $match)) {
                                $col_name = str_replace("'", '', $match[1]);
                                $_columns['Column_name'] = trim($col_name);
                            }
                            $_columns['Key_name'] = $row->name;
                            break;
                        default:
                            break;
                    }
                    $_columns['Table'] = $row->tbl_name;
                    $_columns['Collation'] = null;
                    $_columns['Cardinality'] = 0;
                    $_columns['Sub_part'] = null;
                    $_columns['Packed'] = null;
                    $_columns['Null'] = 'NO';
                    $_columns['Index_type'] = 'BTREE';
                    $_columns['Comment'] = '';
                    $_results[] = new ObjectArray($_columns);
                }
                if (stripos($this->queries[0], 'WHERE') !== false) {
                    preg_match('/WHERE\\s*(.*)$/im', $this->queries[0], $match);
                    list($key, $value) = explode('=', $match[1]);
                    $key = trim($key);
                    $value = preg_replace("/[\';]/", '', $value);
                    $value = trim($value);
                    foreach ($_results as $result) {
                        if (! empty($result->$key) && is_scalar($result->$key) && stripos($value, $result->$key) !== false) {
                            unset($_results);
                            $_results[] = $result;
                            break;
                        }
                    }
                }
            }
            $this->results = $_results;
        }

        /**
         * Method to the CHECK query data to an object.
         *
         * @access private
         */
        private function convert_result_check_or_analyze()
        {
            $results = [];
            if ($this->query_type == 'check') {
                $_columns = [
                    'Table' => '',
                    'Op' => 'check',
                    'Msg_type' => 'status',
                    'Msg_text' => 'OK',
                ];
            } else {
                $_columns = [
                    'Table' => '',
                    'Op' => 'analyze',
                    'Msg_type' => 'status',
                    'Msg_text' => 'Table is already up to date',
                ];
            }
            $_results[] = new ObjectArray($_columns);
            $this->results = $_results;
        }

        /**
         * Method to check SQLite library version.
         *
         * This is used for checking if SQLite can execute multiple rows insert.
         *
         * @return version number string or 0
         * @access private
         */
        private function get_sqlite_version()
        {
            try {
                $statement = $this->pdo->prepare('SELECT sqlite_version()');
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_NUM);

                return $result[0];
            } catch (PDOException $err) {
                return '0';
            }
        }

        /**
         * Method to call PDO::beginTransaction().
         *
         * @see PDO::beginTransaction()
         * @return boolean
         */
        #[\ReturnTypeWillChange]
        public function beginTransaction()
        {
            if ($this->has_active_transaction) {
                return false;
            }

            $this->has_active_transaction = $this->pdo->beginTransaction();

            return $this->has_active_transaction;
        }

        /**
         * Method to call PDO::commit().
         *
         * @see PDO::commit()
         */
        #[\ReturnTypeWillChange]
        public function commit()
        {
            $isSuccess = $this->pdo->commit();
            $this->has_active_transaction = false;

            return $isSuccess;
        }

        /**
         * Method to call PDO::rollBack().
         *
         * @see PDO::rollBack()
         */
        #[\ReturnTypeWillChange]
        public function rollBack()
        {
            $isSuccess = $this->pdo->rollBack();
            $this->has_active_transaction = false;

            return $isSuccess;
        }
    }

    /**
     * Class to change queried data to PHP object.
     *
     * @author kjm
     */
    #[\AllowDynamicProperties]
    class ObjectArray
    {
        function __construct($data = null, &$node = null)
        {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    if (! $node) {
                        $node =& $this;
                    }
                    $node->$key = new \stdClass();
                    self::__construct($value, $node->$key);
                } else {
                    if (! $node) {
                        $node =& $this;
                    }
                    $node->$key = $value;
                }
            }
        }
    }

    /**
     * This class extends wpdb and replaces it.
     *
     * It also rewrites some methods that use mysql specific functions.
     */
    class wpsqlitedb extends \wpdb
    {
        /**
         * Database Handle
         * @var PDOEngine
         */
        protected $dbh;

        /**
         * Constructor
         *
         * Unlike wpdb, no credentials are needed.
         */
        public function __construct()
        {
            parent::__construct('', '', '', '');
        }

        /**
         * Method to set character set for the database.
         *
         * This overrides wpdb::set_charset(), only to dummy out the MySQL function.
         *
         * @see wpdb::set_charset()
         *
         * @param resource $dbh The resource given by mysql_connect
         * @param string $charset Optional. The character set. Default null.
         * @param string $collate Optional. The collation. Default null.
         */
        public function set_charset($dbh, $charset = null, $collate = null)
        {
        }

        /**
         * Method to dummy out wpdb::set_sql_mode()
         *
         * @see wpdb::set_sql_mode()
         *
         * @param array $modes Optional. A list of SQL modes to set.
         */
        public function set_sql_mode($modes = [])
        {
        }

        /**
         * Method to select the database connection.
         *
         * This overrides wpdb::select(), only to dummy out the MySQL function.
         *
         * @see wpdb::select()
         *
         * @param string $db MySQL database name
         * @param resource|null $dbh Optional link identifier.
         */
        public function select($db, $dbh = null)
        {
            $this->ready = true;
        }

        /**
         * Method to escape characters.
         *
         * This overrides wpdb::_real_escape() to avoid using mysql_real_escape_string().
         *
         * @see wpdb::_real_escape()
         *
         * @param  string $string to escape
         *
         * @return string escaped
         */
        function _real_escape($string)
        {
            return addslashes($string);
        }

        /**
         * Method to dummy out wpdb::esc_like() function.
         *
         * WordPress 4.0.0 introduced esc_like() function that adds backslashes to %,
         * underscore and backslash, which is not interpreted as escape character
         * by SQLite. So we override it and dummy out this function.
         *
         * @param string $text The raw text to be escaped. The input typed by the user should have no
         *                     extra or deleted slashes.
         *
         * @return string Text in the form of a LIKE phrase. The output is not SQL safe. Call $wpdb::prepare()
         *                or real_escape next.
         */
        public function esc_like($text)
        {
            return $text;
        }

        /**
         * Method to put out the error message.
         *
         * This overrides wpdb::print_error(), for we can't use the parent class method.
         *
         * @see wpdb::print_error()
         *
         * @global array $EZSQL_ERROR Stores error information of query and error string
         *
         * @param string $str The error to display
         *
         * @return bool False if the showing of errors is disabled.
         */
        public function print_error($str = '')
        {
            global $EZSQL_ERROR;

            if (! $str) {
                $err = $this->dbh->get_error_message() ? $this->dbh->get_error_message() : '';
                if (! empty($err)) {
                    $str = $err[2];
                } else {
                    $str = '';
                }
            }
            $EZSQL_ERROR[] = ['query' => $this->last_query, 'error_str' => $str];

            if ($this->suppress_errors) {
                return false;
            }

            wp_load_translations_early();

            if ($caller = $this->get_caller()) {
                $error_str = sprintf(__('WordPress database error %1$s for query %2$s made by %3$s'), $str,
                    $this->last_query, $caller);
            } else {
                $error_str = sprintf(__('WordPress database error %1$s for query %2$s'), $str, $this->last_query);
            }

            error_log($error_str);

            if (! $this->show_errors) {
                return false;
            }

            if (is_multisite()) {
                $msg = "WordPress database error: [$str]\n{$this->last_query}\n";
                if (defined('ERRORLOGFILE')) {
                    error_log($msg, 3, ERRORLOGFILE);
                }
                if (defined('DIEONDBERROR')) {
                    wp_die($msg);
                }
            } else {
                $str = htmlspecialchars($str, ENT_QUOTES);
                $query = htmlspecialchars($this->last_query, ENT_QUOTES);

                print "<div id='error'>
			<p class='wpdberror'><strong>WordPress database error:</strong> [$str]<br />
			<code>$query</code></p>
			</div>";
            }
        }

        /**
         * Method to flush cached data.
         *
         * This overrides wpdb::flush(). This is not necessarily overridden, because
         * $result will never be resource.
         *
         * @see wpdb::flush
         */
        public function flush()
        {
            $this->last_result = [];
            $this->col_info = null;
            $this->last_query = null;
            $this->rows_affected = $this->num_rows = 0;
            $this->last_error = '';
            $this->result = null;
        }

        /**
         * Method to do the database connection.
         *
         * This overrides wpdb::db_connect() to avoid using MySQL function.
         *
         * @see wpdb::db_connect()
         *
         * @param bool $allow_bail
         */
        public function db_connect($allow_bail = true)
        {
            $this->init_charset();
            $this->dbh = new PDOEngine();
            $this->ready = true;
        }

        /**
         * Method to dummy out wpdb::check_connection()
         *
         * @param bool $allow_bail
         *
         * @return bool
         */
        public function check_connection($allow_bail = true)
        {
            return true;
        }

        /**
         * Method to execute the query.
         *
         * This overrides wpdb::query(). In fact, this method does all the database
         * access jobs.
         *
         * @see wpdb::query()
         *
         * @param string $query Database query
         *
         * @return int|false Number of rows affected/selected or false on error
         */
        public function query($query)
        {
            if (! $this->ready) {
                return false;
            }

            $query = apply_filters('query', $query);

            $return_val = 0;
            $this->flush();

            $this->func_call = "\$db->query(\"$query\")";

            $this->last_query = $query;

            if (defined('SAVEQUERIES') && SAVEQUERIES) {
                $this->timer_start();
            }

            $this->result = $this->dbh->query($query);
            $this->num_queries++;

            if (defined('SAVEQUERIES') && SAVEQUERIES) {
                $this->queries[] = [$query, $this->timer_stop(), $this->get_caller()];
            }

            if ($this->last_error = $this->dbh->get_error_message()) {
                if (defined('WP_INSTALLING') && WP_INSTALLING) {
                    //$this->suppress_errors();
                } else {
                    $this->print_error($this->last_error);

                    return false;
                }
            }

            if (preg_match('/^\\s*(create|alter|truncate|drop|optimize)\\s*/i', $query)) {
                //$return_val = $this->result;
                $return_val = $this->dbh->get_return_value();
            } elseif (preg_match('/^\\s*(insert|delete|update|replace)\s/i', $query)) {
                $this->rows_affected = $this->dbh->get_affected_rows();
                if (preg_match('/^\s*(insert|replace)\s/i', $query)) {
                    $this->insert_id = $this->dbh->get_insert_id();
                }
                $return_val = $this->rows_affected;
            } else {
                $this->last_result = $this->dbh->get_query_results();
                $this->num_rows = $this->dbh->get_num_rows();
                $return_val = $this->num_rows;
            }

            return $return_val;
        }

        /**
         * Method to set the class variable $col_info.
         *
         * This overrides wpdb::load_col_info(), which uses a mysql function.
         *
         * @see    wpdb::load_col_info()
         * @access protected
         */
        protected function load_col_info()
        {
            if ($this->col_info) {
                return;
            }
            $this->col_info = $this->dbh->get_columns();
        }

        /**
         * Method to return what the database can do.
         *
         * This overrides wpdb::has_cap() to avoid using MySQL functions.
         * SQLite supports subqueries, but not support collation, group_concat and set_charset.
         *
         * @see wpdb::has_cap()
         *
         * @param string $db_cap The feature to check for. Accepts 'collation',
         *                       'group_concat', 'subqueries', 'set_charset',
         *                       'utf8mb4', or 'utf8mb4_520'.
         *
         * @return int|false Whether the database feature is supported, false otherwise.
         */
        public function has_cap($db_cap)
        {
            switch (strtolower($db_cap)) {
                case 'collation':
                case 'group_concat':
                case 'set_charset':
                    return false;
                case 'subqueries':
                    return true;
                default:
                    return false;
            }
        }

        /**
         * Method to return database version number.
         *
         * This overrides wpdb::db_version() to avoid using MySQL function.
         * It returns mysql version number, but it means nothing for SQLite.
         * So it return the newest mysql version.
         *
         * @see wpdb::db_version()
         */
        public function db_version()
        {
            // WordPress currently requires this to be 5.0 or greater.
            return '5.5';
        }

        /**
         * Retrieves full database server information.
         *
         * @return string|false Server info on success, false on failure.
         */
        public function db_server_info()
        {
            return SQLite3::version()['versionString'] . '-SQLite3';
        }
    }


    /**
     * This class is for rewriting various query string except CREATE and ALTER.
     *
     */
    class PDOSQLiteDriver
    {

        /**
         * Variable to indicate the query types.
         *
         * @var string $query_type
         */
        public $query_type = '';
        /**
         * Variable to store query string.
         *
         * @var string
         */
        public $_query = '';
        /**
         * Variable to check if rewriting CALC_FOUND_ROWS is needed.
         *
         * @var boolean
         */
        private $rewrite_calc_found = false;
        /**
         * Variable to check if rewriting ON DUPLICATE KEY UPDATE is needed.
         *
         * @var boolean
         */
        private $rewrite_duplicate_key = false;
        /**
         * Variable to check if rewriting index hints is needed.
         *
         * @var boolean
         */
        private $rewrite_index_hint = false;
        /**
         * Variable to check if rewriting BETWEEN is needed.
         *
         * @var boolean
         */
        private $rewrite_between = false;
        /**
         * Variable to check how many times rewriting BETWEEN is needed.
         *
         * @var integer
         */
        private $num_of_rewrite_between = 0;
        /**
         * Variable to check order by field() with column data.
         *
         * @var boolean
         */
        private $orderby_field = false;

        /**
         * Method to rewrite a query string for SQLite to execute.
         *
         * @param strin $query
         * @param string $query_type
         *
         * @return string
         */
        public function rewrite_query($query, $query_type)
        {
            $this->query_type = $query_type;
            $this->_query = $query;
            $this->parse_query();
            switch ($this->query_type) {
                case 'truncate':
                    $this->handle_truncate_query();
                    break;
                case 'alter':
                    $this->handle_alter_query();
                    break;
                case 'create':
                    $this->handle_create_query();
                    break;
                case 'describe':
                case 'desc':
                    $this->handle_describe_query();
                    break;
                case 'show':
                    $this->handle_show_query();
                    break;
                case 'showcolumns':
                    $this->handle_show_columns_query();
                    break;
                case 'showindex':
                    $this->handle_show_index();
                    break;
                case 'select':
                    //$this->strip_backticks();
                    $this->handle_sql_count();
                    $this->rewrite_date_sub();
                    $this->delete_index_hints();
                    $this->rewrite_regexp();
                    //$this->rewrite_boolean();
                    $this->fix_date_quoting();
                    $this->rewrite_between();
                    $this->handle_orderby_field();
                    break;
                case 'insert':
                    //$this->safe_strip_backticks();
                    $this->execute_duplicate_key_update();
                    $this->rewrite_insert_ignore();
                    $this->rewrite_regexp();
                    $this->fix_date_quoting();
                    break;
                case 'update':
                    //$this->safe_strip_backticks();
                    $this->rewrite_update_ignore();
                    //$this->_rewrite_date_sub();
                    $this->rewrite_limit_usage();
                    $this->rewrite_order_by_usage();
                    $this->rewrite_regexp();
                    $this->rewrite_between();
                    break;
                case 'delete':
                    //$this->strip_backticks();
                    $this->rewrite_limit_usage();
                    $this->rewrite_order_by_usage();
                    $this->rewrite_date_sub();
                    $this->rewrite_regexp();
                    $this->delete_workaround();
                    break;
                case 'replace':
                    //$this->safe_strip_backticks();
                    $this->rewrite_date_sub();
                    $this->rewrite_regexp();
                    break;
                case 'optimize':
                    $this->rewrite_optimize();
                    break;
                case 'pragma':
                    break;
                default:
                    if (defined(WP_DEBUG) && WP_DEBUG) {
                        break;
                    } else {
                        $this->return_true();
                        break;
                    }
            }

            return $this->_query;
        }

        /**
         * Method to parse query string and determine which operation is needed.
         *
         * Remove backticks and change true/false values into 1/0. And determines
         * if rewriting CALC_FOUND_ROWS or ON DUPLICATE KEY UPDATE etc is needed.
         *
         * @access private
         */
        private function parse_query()
        {
            $tokens = preg_split("/(\\\'|''|')/s", $this->_query, -1, PREG_SPLIT_DELIM_CAPTURE);
            $literal = false;
            $query_string = '';
            foreach ($tokens as $token) {
                if ($token == "'") {
                    if ($literal) {
                        $literal = false;
                    } else {
                        $literal = true;
                    }
                } else {
                    if ($literal === false) {
                        if (strpos($token, '`') !== false) {
                            $token = str_replace('`', '', $token);
                        }
                        if (preg_match('/\\bTRUE\\b/i', $token)) {
                            $token = str_ireplace('TRUE', '1', $token);
                        }
                        if (preg_match('/\\bFALSE\\b/i', $token)) {
                            $token = str_ireplace('FALSE', '0', $token);
                        }
                        if (stripos($token, 'SQL_CALC_FOUND_ROWS') !== false) {
                            $this->rewrite_calc_found = true;
                        }
                        if (stripos($token, 'ON DUPLICATE KEY UPDATE') !== false) {
                            $this->rewrite_duplicate_key = true;
                        }
                        if (stripos($token, 'USE INDEX') !== false) {
                            $this->rewrite_index_hint = true;
                        }
                        if (stripos($token, 'IGNORE INDEX') !== false) {
                            $this->rewrite_index_hint = true;
                        }
                        if (stripos($token, 'FORCE INDEX') !== false) {
                            $this->rewrite_index_hint = true;
                        }
                        if (stripos($token, 'BETWEEN') !== false) {
                            $this->rewrite_between = true;
                            $this->num_of_rewrite_between++;
                        }
                        if (stripos($token, 'ORDER BY FIELD') !== false) {
                            $this->orderby_field = true;
                        }
                    }
                }
                $query_string .= $token;
            }
            $this->_query = $query_string;
        }

        /**
         * method to handle SHOW TABLES query.
         *
         * @access private
         */
        private function handle_show_query()
        {
            $this->_query = str_ireplace(' FULL', '', $this->_query);
            $table_name = '';
            $pattern = '/^\\s*SHOW\\s*TABLES\\s*.*?(LIKE\\s*(.*))$/im';
            if (preg_match($pattern, $this->_query, $matches)) {
                $table_name = str_replace(["'", ';'], '', $matches[2]);
            }
            if (! empty($table_name)) {
                $suffix = ' AND name LIKE ' . "'" . $table_name . "'";
            } else {
                $suffix = '';
            }
            $this->_query = "SELECT name FROM sqlite_master WHERE type='table'" . $suffix . ' ORDER BY name DESC';
        }

        /**
         * Method to emulate the SQL_CALC_FOUND_ROWS placeholder for MySQL.
         *
         * This is a kind of tricky play.
         * 1. remove SQL_CALC_FOUND_ROWS option, and give it to the pdo engine
         * 2. make another $wpdb instance, and execute the rewritten query
         * 3. give the returned value (integer: number of the rows) to the original instance variable without LIMIT
         *
         * We no longer use SELECT COUNT query, because it returns the inexact values when used with WP_Meta_Query().
         *
         * This kind of statement is required for WordPress to calculate the paging information.
         * see also WP_Query class in wp-includes/query.php
         */
        private function handle_sql_count()
        {
            if (! $this->rewrite_calc_found) {
                return;
            }
            global $wpdb;
            // first strip the code. this is the end of rewriting process
            $this->_query = str_ireplace('SQL_CALC_FOUND_ROWS', '', $this->_query);
            // we make the data for next SELECE FOUND_ROWS() statement
            $unlimited_query = preg_replace('/\\bLIMIT\\s*.*/imsx', '', $this->_query);
            //$unlimited_query = preg_replace('/\\bGROUP\\s*BY\\s*.*/imsx', '', $unlimited_query);
            // we no longer use SELECT COUNT query
            //$unlimited_query = $this->_transform_to_count($unlimited_query);
            $_wpdb = new wpsqlitedb();
            $result = $_wpdb->query($unlimited_query);
            $wpdb->dbh->found_rows_result = $result;
            $_wpdb = null;
        }

        /**
         * Method to rewrite INSERT IGNORE to INSERT OR IGNORE.
         *
         * @access private
         */
        private function rewrite_insert_ignore()
        {
            $this->_query = str_ireplace('INSERT IGNORE', 'INSERT OR IGNORE ', $this->_query);
        }

        /**
         * Method to rewrite UPDATE IGNORE to UPDATE OR IGNORE.
         *
         * @access private
         */
        private function rewrite_update_ignore()
        {
            $this->_query = str_ireplace('UPDATE IGNORE', 'UPDATE OR IGNORE ', $this->_query);
        }

        /**
         * Method to rewrite DATE_ADD() function.
         *
         * DATE_ADD has a parameter PHP function can't parse, so we quote the list and
         * pass it to the user defined function.
         *
         * @access private
         */
        private function rewrite_date_add()
        {
            //(date,interval expression unit)
            $pattern = '/\\s*date_add\\s*\(([^,]*),([^\)]*)\)/imsx';
            if (preg_match($pattern, $this->_query, $matches)) {
                $expression = "'" . trim($matches[2]) . "'";
                $this->_query = preg_replace($pattern, " date_add($matches[1], $expression) ", $this->_query);
            }
        }

        /**
         * Method to rewrite DATE_SUB() function.
         *
         * DATE_SUB has a parameter PHP function can't parse, so we quote the list and
         * pass it to the user defined function.
         *
         * @access private
         */
        private function rewrite_date_sub()
        {
            //(date,interval expression unit)
            $pattern = '/\\s*date_sub\\s*\(([^,]*),([^\)]*)\)/imsx';
            if (preg_match($pattern, $this->_query, $matches)) {
                $expression = "'" . trim($matches[2]) . "'";
                $this->_query = preg_replace($pattern, " date_sub($matches[1], $expression) ", $this->_query);
            }
        }

        /**
         * Method to handle CREATE query.
         *
         * If the query is CREATE query, it will be passed to the query_create.class.php.
         * So this method can't be used. It's here for safety.
         *
         * @access private
         */
        private function handle_create_query()
        {
            $engine = new CreateQuery();
            $this->_query = $engine->rewrite_query($this->_query);
            $engine = null;
        }

        /**
         * Method to handle ALTER query.
         *
         * If the query is ALTER query, it will be passed ot the query_alter.class.php.
         * So this method can't be used. It is here for safety.
         *
         * @access private
         */
        private function handle_alter_query()
        {
            $engine = new AlterQuery();
            $this->_query = $engine->rewrite_query($this->_query, 'alter');
            $engine = null;
        }

        /**
         * Method to handle DESCRIBE or DESC query.
         *
         * DESCRIBE is required for WordPress installation process. DESC is
         * an alias for DESCRIBE, but it is not used in core WordPress.
         *
         * @access private
         */
        private function handle_describe_query()
        {
            $pattern = '/^\\s*(DESCRIBE|DESC)\\s*(.*)/i';
            if (preg_match($pattern, $this->_query, $match)) {
                $tablename = preg_replace('/[\';]/', '', $match[2]);
                $this->_query = "PRAGMA table_info($tablename)";
            }
        }

        /**
         * Method to remove LIMIT clause from DELETE or UPDATE query.
         *
         * The author of the original 'PDO for WordPress' says update method of wpdb
         * insists on adding LIMIT. But the newest version of WordPress doesn't do that.
         * Nevertheless some plugins use DELETE with LIMIT, UPDATE with LIMIT.
         * We need to exclude sub query's LIMIT. And if SQLite is compiled with
         * ENABLE_UPDATE_DELETE_LIMIT option, we don't remove it.
         *
         * @access private
         */
        private function rewrite_limit_usage()
        {
            $_wpdb = new wpsqlitedb();
            $options = $_wpdb->get_results('PRAGMA compile_options');
            foreach ($options as $opt) {
                if (isset($opt->compile_option) && stripos($opt->compile_option, 'ENABLE_UPDATE_DELETE_LIMIT') !== false) {
                    return;
                }
            }
            if (stripos($this->_query, '(select') === false) {
                $this->_query = preg_replace('/\\s*LIMIT\\s*[0-9]$/i', '', $this->_query);
            }
        }

        /**
         * Method to remove ORDER BY clause from DELETE or UPDATE query.
         *
         * SQLite compiled without SQLITE_ENABLE_UPDATE_DELETE_LIMIT option can't
         * execute UPDATE with ORDER BY, DELETE with GROUP BY.
         * We need to exclude sub query's GROUP BY.
         *
         * @access private
         */
        private function rewrite_order_by_usage()
        {
            $_wpdb = new wpsqlitedb();
            $options = $_wpdb->get_results('PRAGMA compile_options');
            foreach ($options as $opt) {
                if (isset($opt->compile_option) && stripos($opt->compile_option, 'ENABLE_UPDATE_DELETE_LIMIT') !== false) {
                    return;
                }
            }
            if (stripos($this->_query, '(select') === false) {
                $this->_query = preg_replace('/\\s+ORDER\\s+BY\\s*.*$/i', '', $this->_query);
            }
        }

        /**
         * Method to handle TRUNCATE query.
         *
         * @access private
         */
        private function handle_truncate_query()
        {
            $pattern = '/TRUNCATE TABLE (.*)/im';
            $this->_query = preg_replace($pattern, 'DELETE FROM $1', $this->_query);
        }

        /**
         * Method to handle OPTIMIZE query.
         *
         * Original query has the table names, but they are simply ignored.
         * Table names are meaningless in SQLite.
         *
         * @access private
         */
        private function rewrite_optimize()
        {
            $this->_query = "VACUUM";
        }

        /**
         * Method to rewrite day.
         *
         * Jusitn Adie says: some wp UI interfaces (notably the post interface)
         * badly composes the day part of the date leading to problems in sqlite
         * sort ordering etc.
         *
         * I don't understand that...
         *
         * @return void
         * @access private
         */
        private function rewrite_badly_formed_dates()
        {
            $pattern = '/([12]\d{3,}-\d{2}-)(\d )/ims';
            $this->_query = preg_replace($pattern, '${1}0$2', $this->_query);
        }

        /**
         * Method to remove INDEX HINT.
         *
         * @return void
         * @access private
         */
        private function delete_index_hints()
        {
            $pattern = '/\\s*(use|ignore|force)\\s+index\\s*\(.*?\)/i';
            $this->_query = preg_replace($pattern, '', $this->_query);
        }

        /**
         * Method to fix the date string and quoting.
         *
         * This is required for the calendar widget.
         *
         * WHERE month(fieldname)=08 is converted to month(fieldname)='8'
         * WHERE month(fieldname)='08' is converted to month(fieldname)='8'
         *
         * I use preg_replace_callback instead of 'e' option because of security reason.
         * cf. PHP manual (regular expression)
         *
         * @return void
         * @access private
         */
        private function fix_date_quoting()
        {
            $pattern = '/(month|year|second|day|minute|hour|dayofmonth)\\s*\((.*?)\)\\s*=\\s*["\']?(\d{1,4})[\'"]?\\s*/im';
            $this->_query = preg_replace_callback($pattern, [$this, '_fix_date_quoting'], $this->_query);
        }

        /**
         * Call back method to rewrite date string.
         *
         * @param string $match
         *
         * @return string
         * @access private
         */
        private function _fix_date_quoting($match)
        {
            $fixed_val = "{$match[1]}({$match[2]})='" . intval($match[3]) . "' ";

            return $fixed_val;
        }

        /**
         * Method to rewrite REGEXP() function.
         *
         * This method changes function name to regexpp() and pass it to the user defined
         * function.
         *
         * @access private
         */
        private function rewrite_regexp()
        {
            $pattern = '/\s([^\s]*)\s*regexp\s*(\'.*?\')/im';
            $this->_query = preg_replace($pattern, ' regexpp(\1, \2)', $this->_query);
        }

        /**
         * Method to handl SHOW COLUMN query.
         *
         * @access private
         */
        private function handle_show_columns_query()
        {
            $this->_query = str_ireplace(' FULL', '', $this->_query);
            $pattern_like = '/^\\s*SHOW\\s*(COLUMNS|FIELDS)\\s*FROM\\s*(.*)?\\s*LIKE\\s*(.*)?/i';
            $pattern = '/^\\s*SHOW\\s*(COLUMNS|FIELDS)\\s*FROM\\s*(.*)?/i';
            if (preg_match($pattern_like, $this->_query, $matches)) {
                $table_name = str_replace("'", "", trim($matches[2]));
                $column_name = str_replace("'", "", trim($matches[3]));
                $query_string = "SELECT sql FROM sqlite_master WHERE tbl_name='$table_name' AND sql LIKE '%$column_name%'";
                $this->_query = $query_string;
            } elseif (preg_match($pattern, $this->_query, $matches)) {
                $table_name = $matches[2];
                $query_string = preg_replace($pattern, "PRAGMA table_info($table_name)", $this->_query);
                $this->_query = $query_string;
            }
        }

        /**
         * Method to handle SHOW INDEX query.
         *
         * Moved the WHERE clause manipulation to pdoengin.class.php (ver 1.3.1)
         *
         * @access private
         */
        private function handle_show_index()
        {
            $pattern = '/^\\s*SHOW\\s*(?:INDEX|INDEXES|KEYS)\\s*FROM\\s*(\\w+)?/im';
            if (preg_match($pattern, $this->_query, $match)) {
                $table_name = preg_replace("/[\';]/", '', $match[1]);
                $table_name = trim($table_name);
                $this->_query = "SELECT * FROM sqlite_master WHERE tbl_name='$table_name'";
            }
        }

        /**
         * Method to handle ON DUPLICATE KEY UPDATE statement.
         *
         * First we use SELECT query and check if INSERT is allowed or not.
         * Rewriting procedure looks like a detour, but I've got no other ways.
         *
         * Added the literal check since the version 1.5.1.
         *
         * @return void
         * @access private
         */
        private function execute_duplicate_key_update()
        {
            if (! $this->rewrite_duplicate_key) {
                return;
            }
            $unique_keys_for_cond = [];
            $unique_keys_for_check = [];
            $pattern = '/^\\s*INSERT\\s*INTO\\s*(\\w+)?\\s*(.*)\\s*ON\\s*DUPLICATE\\s*KEY\\s*UPDATE\\s*(.*)$/ims';
            if (preg_match($pattern, $this->_query, $match_0)) {
                $table_name = trim($match_0[1]);
                $insert_data = trim($match_0[2]);
                $update_data = trim($match_0[3]);
                // prepare two unique key data for the table
                // 1. array('col1', 'col2, col3', etc) 2. array('col1', 'col2', 'col3', etc)
                $_wpdb = new wpsqlitedb();
                $indexes = $_wpdb->get_results("SHOW INDEX FROM {$table_name}");
                if (! empty($indexes)) {
                    foreach ($indexes as $index) {
                        if ($index->Non_unique == 0) {
                            $unique_keys_for_cond[] = $index->Column_name;
                            if (strpos($index->Column_name, ',') !== false) {
                                $unique_keys_for_check = array_merge($unique_keys_for_check,
                                    explode(',', $index->Column_name));
                            } else {
                                $unique_keys_for_check[] = $index->Column_name;
                            }
                        }
                    }
                    $unique_keys_for_check = array_map('trim', $unique_keys_for_check);
                } else {
                    // Without unique key or primary key, UPDATE statement will affect all the rows!
                    $query = "INSERT INTO $table_name $insert_data";
                    $this->_query = $query;
                    $_wpdb = null;

                    return;
                }
                // data check
                if (preg_match('/^\((.*)\)\\s*VALUES\\s*\((.*)\)$/ims', $insert_data, $match_1)) {
                    $col_array = explode(',', $match_1[1]);
                    $ins_data_array = explode(',', $match_1[2]);
                    foreach ($col_array as $col) {
                        $val = trim(array_shift($ins_data_array));
                        $ins_data_assoc[trim($col)] = $val;
                    }
                    $condition = '';
                    foreach ($unique_keys_for_cond as $unique_key) {
                        if (strpos($unique_key, ',') !== false) {
                            $unique_key_array = explode(',', $unique_key);
                            $counter = count($unique_key_array);
                            for ($i = 0; $i < $counter; ++$i) {
                                $col = trim($unique_key_array[$i]);
                                if (isset($ins_data_assoc[$col]) && $i == $counter - 1) {
                                    $condition .= $col . '=' . $ins_data_assoc[$col] . ' OR ';
                                } elseif (isset($ins_data_assoc[$col])) {
                                    $condition .= $col . '=' . $ins_data_assoc[$col] . ' AND ';
                                } else {
                                    continue;
                                }
                            }
                        } else {
                            $col = trim($unique_key);
                            if (isset($ins_data_assoc[$col])) {
                                $condition .= $col . '=' . $ins_data_assoc[$col] . ' OR ';
                            } else {
                                continue;
                            }
                        }
                    }
                    $condition = rtrim($condition, ' OR ');
                    $test_query = "SELECT * FROM {$table_name} WHERE {$condition}";
                    $results = $_wpdb->query($test_query);
                    $_wpdb = null;
                    if ($results == 0) {
                        $this->_query = "INSERT INTO $table_name $insert_data";

                        return;
                    } else {
                        $ins_array_assoc = [];

                        if (preg_match('/^\((.*)\)\\s*VALUES\\s*\((.*)\)$/im', $insert_data, $match_2)) {
                            $col_array = explode(',', $match_2[1]);
                            $ins_array = explode(',', $match_2[2]);
                            $count = count($col_array);
                            for ($i = 0; $i < $count; $i++) {
                                $col = trim($col_array[$i]);
                                $val = trim($ins_array[$i]);
                                $ins_array_assoc[$col] = $val;
                            }
                        }
                        $update_data = rtrim($update_data, ';');
                        $tmp_array = explode(',', $update_data);
                        foreach ($tmp_array as $pair) {
                            list($col, $value) = explode('=', $pair);
                            $col = trim($col);
                            $value = trim($value);
                            $update_array_assoc[$col] = $value;
                        }
                        foreach ($update_array_assoc as $key => &$value) {
                            if (preg_match('/^VALUES\\s*\((.*)\)$/im', $value, $match_3)) {
                                $col = trim($match_3[1]);
                                $value = $ins_array_assoc[$col];
                            }
                        }
                        foreach ($ins_array_assoc as $key => $val) {
                            if (in_array($key, $unique_keys_for_check)) {
                                $where_array[] = $key . '=' . $val;
                            }
                        }
                        $update_strings = '';
                        foreach ($update_array_assoc as $key => $val) {
                            if (in_array($key, $unique_keys_for_check)) {
                                $where_array[] = $key . '=' . $val;
                            } else {
                                $update_strings .= $key . '=' . $val . ',';
                            }
                        }
                        $update_strings = rtrim($update_strings, ',');
                        $unique_where = array_unique($where_array, SORT_REGULAR);
                        $where_string = ' WHERE ' . implode(' AND ', $unique_where);
                        $update_query = 'UPDATE ' . $table_name . ' SET ' . $update_strings . $where_string;
                        $this->_query = $update_query;
                    }
                }
            }
        }

        /**
         * Method to rewrite BETWEEN A AND B clause.
         *
         * This clause is the same form as natural language, so we have to check if it is
         * in the data or SQL statement.
         *
         * @access private
         */
        private function rewrite_between()
        {
            if (! $this->rewrite_between) {
                return;
            }
            $pattern = '/\\s*(CAST\([^\)]+?\)|[^\\s\(]*)?\\s*BETWEEN\\s*([^\\s]*)?\\s*AND\\s*([^\\s\)]*)?\\s*/ims';
            do {
                if (preg_match($pattern, $this->_query, $match)) {
                    $column_name = trim($match[1]);
                    $min_value = trim($match[2]);
                    $max_value = trim($match[3]);
                    $max_value = rtrim($max_value);
                    $replacement = " ($column_name >= $min_value AND $column_name <= $max_value)";
                    $this->_query = str_ireplace($match[0], $replacement, $this->_query);
                }
                $this->num_of_rewrite_between--;
            } while ($this->num_of_rewrite_between > 0);
        }

        /**
         * Method to handle ORDER BY FIELD() clause.
         *
         * When FIELD() function has column name to compare, we can't rewrite it with
         * use defined functions. When this function detect column name in the argument,
         * it creates another instance, does the query withuot ORDER BY clause and gives
         * the result array sorted to the main instance.
         *
         * If FIELD() function doesn't have column name, it will use the user defined
         * function. usort() function closure function to compare the items.
         *
         * @access private
         */
        private function handle_orderby_field()
        {
            if (! $this->orderby_field) {
                return;
            }
            global $wpdb;
            $pattern = '/\\s+ORDER\\s+BY\\s+FIELD\\s*\(\\s*([^\)]+?)\\s*\)/i';
            if (preg_match($pattern, $this->_query, $match)) {
                $params = explode(',', $match[1]);
                $params = array_map('trim', $params);
                $tbl_col = array_shift($params);
                $flipped = array_flip($params);
                $tbl_name = substr($tbl_col, 0, strpos($tbl_col, '.'));
                $tbl_name = str_replace($wpdb->prefix, '', $tbl_name);

                if ($tbl_name && in_array($tbl_name, $wpdb->tables)) {
                    $query = str_replace($match[0], '', $this->_query);
                    $_wpdb = new wpsqlitedb();
                    $results = $_wpdb->get_results($query);
                    $_wpdb = null;
                    usort($results, function ($a, $b) use ($flipped) {
                        return $flipped[$a->ID] - $flipped[$b->ID];
                    });
                }
                $wpdb->dbh->pre_ordered_results = $results;
            }
        }

        /**
         * Method to avoid DELETE with JOIN statement.
         *
         * wp-admin/includes/upgrade.php contains 'DELETE ... JOIN' statement.
         * This query can't be replaced with regular expression or udf, so we
         * replace all the statement with another. But this query was used in
         * the very old version of WordPress when it was upgraded. So we won't
         * have no chance that this method should be used.
         *
         * @access private
         */
        private function delete_workaround()
        {
            global $wpdb;
            $pattern = "DELETE o1 FROM $wpdb->options AS o1 JOIN $wpdb->options AS o2";
            $pattern2 = "DELETE a, b FROM $wpdb->sitemeta AS a, $wpdb->sitemeta AS b";
            $rewritten = "DELETE FROM $wpdb->options WHERE option_id IN (SELECT MIN(option_id) FROM $wpdb->options GROUP BY option_name HAVING COUNT(*) > 1)";
            if (stripos($this->_query, $pattern) !== false) {
                $this->_query = $rewritten;
            } elseif (stripos($this->_query, $pattern2) !== false) {
                $time = time();
                $prep_query = "SELECT a.meta_id AS aid, b.meta_id AS bid FROM $wpdb->sitemeta AS a INNER JOIN $wpdb->sitemeta AS b ON a.meta_key='_site_transient_timeout_'||substr(b.meta_key, 17) WHERE b.meta_key='_site_transient_'||substr(a.meta_key, 25) AND a.meta_value < $time";
                $_wpdb = new wpsqlitedb();
                $ids = $_wpdb->get_results($prep_query);
                foreach ($ids as $id) {
                    $ids_to_delete[] = $id->aid;
                    $ids_to_delete[] = $id->bid;
                }
                $rewritten = "DELETE FROM $wpdb->sitemeta WHERE meta_id IN (" . implode(',', $ids_to_delete) . ")";
                $this->_query = $rewritten;
            }
        }

        /**
         * Method to suppress errors.
         *
         * When the query string is the one that this class can't manipulate,
         * the query string is replaced with the one that always returns true
         * and does nothing.
         *
         * @access private
         */
        private function return_true()
        {
            $this->_query = 'SELECT 1=1';
        }
    }

    /**
     * This class provides a function to rewrite CREATE query.
     *
     */
    class CreateQuery
    {

        /**
         * The query string to be rewritten in this class.
         *
         * @var string
         * @access private
         */
        private $_query = '';
        /**
         * The array to contain CREATE INDEX queries.
         *
         * @var array of strings
         * @access private
         */
        private $index_queries = [];
        /**
         * The array to contain error messages.
         *
         * @var array of string
         * @access private
         */
        private $_errors = [];
        /**
         * Variable to have the table name to be executed.
         *
         * @var string
         * @access private
         */
        private $table_name = '';
        /**
         * Variable to check if the query has the primary key.
         *
         * @var boolean
         * @access private
         */
        private $has_primary_key = false;

        /**
         * Function to rewrite query.
         *
         * @param string $query the query being processed
         *
         * @return string|array    the processed (rewritten) query
         */
        public function rewrite_query($query)
        {
            $this->_query = $query;
            $this->_errors [] = '';
            if (preg_match('/^CREATE\\s*(UNIQUE|FULLTEXT|)\\s*INDEX/ims', $this->_query, $match)) {
                // we manipulate CREATE INDEX query in PDOEngine.class.php
                // FULLTEXT index creation is simply ignored.
                if (isset($match[1]) && stripos($match[1], 'fulltext') !== false) {
                    return 'SELECT 1=1';
                } else {
                    return $this->_query;
                }
            } elseif (preg_match('/^CREATE\\s*(TEMP|TEMPORARY|)\\s*TRIGGER\\s*/im', $this->_query)) {
                // if WordPress comes to use foreign key constraint, trigger will be needed.
                // we don't use it for now.
                return $this->_query;
            }
            $this->strip_backticks();
            $this->quote_illegal_field();
            $this->get_table_name();
            $this->rewrite_comments();
            $this->rewrite_field_types();
            $this->rewrite_character_set();
            $this->rewrite_engine_info();
            $this->rewrite_unsigned();
            $this->rewrite_autoincrement();
            $this->rewrite_primary_key();
            $this->rewrite_foreign_key();
            $this->rewrite_unique_key();
            $this->rewrite_enum();
            $this->rewrite_set();
            $this->rewrite_key();
            $this->add_if_not_exists();

            return $this->post_process();
        }

        /**
         * Method to get table name from the query string.
         *
         * 'IF NOT EXISTS' clause is removed for the easy regular expression usage.
         * It will be added at the end of the process.
         *
         * @access private
         */
        private function get_table_name()
        {
            // $pattern = '/^\\s*CREATE\\s*(TEMP|TEMPORARY)?\\s*TABLE\\s*(IF NOT EXISTS)?\\s*([^\(]*)/imsx';
            $pattern = '/^\\s*CREATE\\s*(?:TEMP|TEMPORARY)?\\s*TABLE\\s*(?:IF\\s*NOT\\s*EXISTS)?\\s*([^\(]*)/imsx';
            if (preg_match($pattern, $this->_query, $matches)) {
                $this->table_name = trim($matches[1]);
            }
        }

        /**
         * Method to change the MySQL field types to SQLite compatible types.
         *
         * If column name is the same as the key value, e.g. "date" or "timestamp",
         * and the column is on the top of the line, we add a single quote and avoid
         * to be replaced. But this doesn't work if that column name is in the middle
         * of the line.
         * Order of the key value is important. Don't change it.
         *
         * @access private
         */
        private function rewrite_field_types()
        {
            $array_types = [
                'bit' => 'integer',
                'bool' => 'integer',
                'boolean' => 'integer',
                'tinyint' => 'integer',
                'smallint' => 'integer',
                'mediumint' => 'integer',
                'int' => 'integer',
                'integer' => 'integer',
                'bigint' => 'integer',
                'float' => 'real',
                'double' => 'real',
                'decimal' => 'real',
                'dec' => 'real',
                'numeric' => 'real',
                'fixed' => 'real',
                'date' => 'text',
                'datetime' => 'text',
                'timestamp' => 'text',
                'time' => 'text',
                'year' => 'text',
                'char' => 'text',
                'varchar' => 'text',
                'binary' => 'integer',
                'varbinary' => 'blob',
                'tinyblob' => 'blob',
                'tinytext' => 'text',
                'blob' => 'blob',
                'text' => 'text',
                'mediumblob' => 'blob',
                'mediumtext' => 'text',
                'longblob' => 'blob',
                'longtext' => 'text',
            ];
            foreach ($array_types as $o => $r) {
                if (preg_match("/^\\s*(?<!')$o\\s+(.+$)/im", $this->_query, $match)) {
                    $ptrn = "/$match[1]/im";
                    $replaced = str_ireplace($ptrn, '#placeholder#', $this->_query);
                    $replaced = str_ireplace($o, "'{$o}'", $replaced);
                    $this->_query = str_replace('#placeholder#', $ptrn, $replaced);
                }
                $pattern = "/\\b(?<!')$o\\b\\s*(\([^\)]*\)*)?\\s*/ims";
                if (preg_match("/^\\s*.*?\\s*\(.*?$o.*?\)/im", $this->_query)) {
                    ;
                } else {
                    $this->_query = preg_replace($pattern, " $r ", $this->_query);
                }
            }
        }

        /**
         * Method for stripping the comments from the SQL statement.
         *
         * @access private
         */
        private function rewrite_comments()
        {
            $this->_query = preg_replace("/# --------------------------------------------------------/",
                "-- ******************************************************", $this->_query);
            $this->_query = preg_replace("/#/", "--", $this->_query);
        }

        /**
         * Method for stripping the engine and other stuffs.
         *
         * TYPE, ENGINE and AUTO_INCREMENT are removed here.
         * @access private
         */
        private function rewrite_engine_info()
        {
            $this->_query = preg_replace("/\\s*(TYPE|ENGINE)\\s*=\\s*.*(?<!;)/ims", '', $this->_query);
            $this->_query = preg_replace("/ AUTO_INCREMENT\\s*=\\s*[0-9]*/ims", '', $this->_query);
        }

        /**
         * Method for stripping unsigned.
         *
         * SQLite doesn't have unsigned int data type. So UNSIGNED INT(EGER) is converted
         * to INTEGER here.
         *
         * @access private
         */
        private function rewrite_unsigned()
        {
            $this->_query = preg_replace('/\\bunsigned\\b/ims', ' ', $this->_query);
        }

        /**
         * Method for rewriting primary key auto_increment.
         *
         * If the field type is 'INTEGER PRIMARY KEY', it is automatically autoincremented
         * by SQLite. There's a little difference between PRIMARY KEY and AUTOINCREMENT, so
         * we may well convert to PRIMARY KEY only.
         *
         * @access private
         */
        private function rewrite_autoincrement()
        {
            $this->_query = preg_replace('/\\bauto_increment\\s*primary\\s*key\\s*(,)?/ims',
                ' PRIMARY KEY AUTOINCREMENT \\1', $this->_query, -1, $count);
            $this->_query = preg_replace('/\\bauto_increment\\b\\s*(,)?/ims', ' PRIMARY KEY AUTOINCREMENT $1',
                $this->_query, -1, $count);
            if ($count > 0) {
                $this->has_primary_key = true;
            }
        }

        /**
         * Method for rewriting primary key.
         *
         * @access private
         */
        private function rewrite_primary_key()
        {
            if ($this->has_primary_key) {
                $this->_query = preg_replace('/\\s*primary key\\s*.*?\([^\)]*\)\\s*(,|)/i', ' ', $this->_query);
            } else {
                // If primary key has an index name, we remove that name.
                $this->_query = preg_replace('/\\bprimary\\s*key\\s*.*?\\s*(\(.*?\))/im', 'PRIMARY KEY \\1', $this->_query);
            }
        }

        /**
         * Method for rewriting foreign key.
         *
         * @access private
         */
        private function rewrite_foreign_key()
        {
            $pattern = '/\\s*foreign\\s*key\\s*(|.*?)\([^\)]+?\)\\s*references\\s*.*/i';
            if (preg_match_all($pattern, $this->_query, $match)) {
                if (isset($match[1])) {
                    $this->_query = str_ireplace($match[1], '', $this->_query);
                }
            }
        }

        /**
         * Method for rewriting unique key.
         *
         * @access private
         */
        private function rewrite_unique_key()
        {
            $this->_query = preg_replace_callback('/\\bunique key\\b([^\(]*)(\(.*\))/im', [$this, '_rewrite_unique_key'],
                $this->_query);
        }

        /**
         * Callback method for rewrite_unique_key.
         *
         * @param array $matches an array of matches from the Regex
         *
         * @access private
         * @return string
         */
        private function _rewrite_unique_key($matches)
        {
            $index_name = trim($matches[1]);
            $col_name = trim($matches[2]);
            $tbl_name = $this->table_name;
            if (preg_match('/\(\\d+?\)/', $col_name)) {
                $col_name = preg_replace('/\(\\d+?\)/', '', $col_name);
            }
            $_wpdb = new wpsqlitedb();
            $results = $_wpdb->get_results("SELECT name FROM sqlite_master WHERE type='index'");
            $_wpdb = null;
            if ($results) {
                foreach ($results as $result) {
                    if ($result->name == $index_name) {
                        $r = rand(0, 50);
                        $index_name = $index_name . "_$r";
                        break;
                    }
                }
            }
            $index_name = str_replace(' ', '', $index_name);
            $this->index_queries[] = "CREATE UNIQUE INDEX $index_name ON " . $tbl_name . $col_name;

            return '';
        }

        /**
         * Method for handling ENUM fields.
         *
         * SQLite doesn't support enum, so we change it to check constraint.
         *
         * @access private
         */
        private function rewrite_enum()
        {
            $pattern = '/(,|\))([^,]*)enum\((.*?)\)([^,\)]*)/ims';
            $this->_query = preg_replace_callback($pattern, [$this, '_rewrite_enum'], $this->_query);
        }

        /**
         * Call back method for rewrite_enum() and rewrite_set().
         *
         * @access private
         *
         * @param $matches
         *
         * @return string
         */
        private function _rewrite_enum($matches)
        {
            $output = $matches[1] . ' ' . $matches[2] . ' TEXT ' . $matches[4] . ' CHECK (' . $matches[2] . ' IN (' . $matches[3] . ')) ';

            return $output;
        }

        /**
         * Method for rewriting usage of set.
         *
         * It is similar but not identical to enum. SQLite does not support either.
         *
         * @access private
         */
        private function rewrite_set()
        {
            $pattern = '/\b(\w)*\bset\\s*\((.*?)\)\\s*(.*?)(,)*/ims';
            $this->_query = preg_replace_callback($pattern, [$this, '_rewrite_enum'], $this->_query);
        }

        /**
         * Method for rewriting usage of key to create an index.
         *
         * SQLite cannot create non-unique indices as part of the create query,
         * so we need to create an index by hand and append it to the create query.
         *
         * @access private
         */
        private function rewrite_key()
        {
            $this->_query = preg_replace_callback('/,\\s*(KEY|INDEX)\\s*(\\w+)?\\s*(\(.+\))/im', [$this, '_rewrite_key'],
                $this->_query);
        }

        /**
         * Callback method for rewrite_key.
         *
         * @param array $matches an array of matches from the Regex
         *
         * @access private
         * @return string
         */
        private function _rewrite_key($matches)
        {
            $index_name = trim($matches[2]);
            $col_name = trim($matches[3]);
            if (preg_match('/\([0-9]+?\)/', $col_name, $match)) {
                $col_name = preg_replace_callback('/\([0-9]+?\)/', [$this, '_remove_length'], $col_name);
            }
            $tbl_name = $this->table_name;
            $_wpdb = new wpsqlitedb();
            $results = $_wpdb->get_results("SELECT name FROM sqlite_master WHERE type='index'");
            $_wpdb = null;
            if ($results) {
                foreach ($results as $result) {
                    if ($result->name == $index_name) {
                        $r = rand(0, 50);
                        $index_name = $index_name . "_$r";
                        break;
                    }
                }
            }
            $this->index_queries[] = 'CREATE INDEX ' . $index_name . ' ON ' . $tbl_name . $col_name;

            return '';
        }

        /**
         * Call back method to remove unnecessary string.
         *
         * This method is deprecated.
         *
         * @param string $match
         *
         * @return string whose length is zero
         * @access private
         */
        private function _remove_length($match)
        {
            return '';
        }

        /**
         * Method to assemble the main query and index queries into an array.
         *
         * It return the array of the queries to be executed separately.
         *
         * @return array
         * @access private
         */
        private function post_process()
        {
            $mainquery = $this->_query;
            do {
                $count = 0;
                $mainquery = preg_replace('/,\\s*\)/imsx', ')', $mainquery, -1, $count);
            } while ($count > 0);
            do {
                $count = 0;
                $mainquery = preg_replace('/\(\\s*?,/imsx', '(', $mainquery, -1, $count);
            } while ($count > 0);
            $return_val[] = $mainquery;
            $return_val = array_merge($return_val, $this->index_queries);

            return $return_val;
        }

        /**
         * Method to add IF NOT EXISTS to query string.
         *
         * This adds IF NOT EXISTS to every query string, which prevent the exception
         * from being thrown.
         *
         * @access private
         */
        private function add_if_not_exists()
        {
            $pattern_table = '/^\\s*CREATE\\s*(TEMP|TEMPORARY)?\\s*TABLE\\s*(IF NOT EXISTS)?\\s*/ims';
            $this->_query = preg_replace($pattern_table, 'CREATE $1 TABLE IF NOT EXISTS ', $this->_query);
            $pattern_index = '/^\\s*CREATE\\s*(UNIQUE)?\\s*INDEX\\s*(IF NOT EXISTS)?\\s*/ims';
            for ($i = 0; $i < count($this->index_queries); $i++) {
                $this->index_queries[$i] = preg_replace($pattern_index, 'CREATE $1 INDEX IF NOT EXISTS ',
                    $this->index_queries[$i]);
            }
        }

        /**
         * Method to strip back quotes.
         *
         * @access private
         */
        private function strip_backticks()
        {
            $this->_query = str_replace('`', '', $this->_query);
            foreach ($this->index_queries as &$query) {
                $query = str_replace('`', '', $query);
            }
        }

        /**
         * Method to remove the character set information from within mysql queries.
         *
         * This removes DEFAULT CHAR(ACTER) SET and COLLATE, which is meaningless for
         * SQLite.
         *
         * @access private
         */
        private function rewrite_character_set()
        {
            $pattern_charset = '/\\b(default\\s*character\\s*set|default\\s*charset|character\\s*set)\\s*(?<!\()[^ ]*/im';
            $pattern_collate1 = '/\\s*collate\\s*[^ ]*(?=,)/im';
            $pattern_collate2 = '/\\s*collate\\s*[^ ]*(?<!;)/im';
            $patterns = [$pattern_charset, $pattern_collate1, $pattern_collate2];
            $this->_query = preg_replace($patterns, '', $this->_query);
        }

        /**
         * Method to quote illegal field name for SQLite
         *
         * @access private
         */
        private function quote_illegal_field()
        {
            $this->_query = preg_replace("/^\\s*(?<!')(default|values)/im", "'\\1'", $this->_query);
        }
    }

    class AlterQuery
    {
        /**
         * Variable to store the rewritten query string.
         * @var string
         */
        public $_query = null;

        /**
         * Function to split the query string to the tokens and call appropriate functions.
         *
         * @param $query
         * @param string $query_type
         *
         * @return boolean | string
         */
        public function rewrite_query($query, $query_type)
        {
            if (stripos($query, $query_type) === false) {
                return false;
            }
            $query = str_replace('`', '', $query);
            if (preg_match('/^\\s*(ALTER\\s*TABLE)\\s*(\\w+)?\\s*/ims', $query, $match)) {
                $tmp_query = [];
                $re_command = '';
                $command = str_ireplace($match[0], '', $query);
                $tmp_tokens['query_type'] = trim($match[1]);
                $tmp_tokens['table_name'] = trim($match[2]);
                $command_array = explode(',', $command);

                $single_command = array_shift($command_array);
                if (! empty($command_array)) {
                    $re_command = "ALTER TABLE {$tmp_tokens['table_name']} ";
                    $re_command .= implode(',', $command_array);
                }
                $command_tokens = $this->command_tokenizer($single_command);
                if (! empty($command_tokens)) {
                    $tokens = array_merge($tmp_tokens, $command_tokens);
                } else {
                    $this->_query = 'SELECT 1=1';

                    return $this->_query;
                }
                $command_name = strtolower($tokens['command']);
                switch ($command_name) {
                    case 'add column':
                    case 'rename to':
                    case 'add index':
                    case 'drop index':
                        $tmp_query = $this->handle_single_command($tokens);
                        break;
                    case 'add primary key':
                        $tmp_query = $this->handle_add_primary_key($tokens);
                        break;
                    case 'drop primary key':
                        $tmp_query = $this->handle_drop_primary_key($tokens);
                        break;
                    case 'modify column':
                        $tmp_query = $this->handle_modify_command($tokens);
                        break;
                    case 'change column':
                        $tmp_query = $this->handle_change_command($tokens);
                        break;
                    case 'alter column':
                        $tmp_query = $this->handle_alter_command($tokens);
                        break;
                    default:
                        break;
                }
                if (! is_array($tmp_query)) {
                    $this->_query[] = $tmp_query;
                } else {
                    $this->_query = $tmp_query;
                }
                if ($re_command != '') {
                    $this->_query = array_merge($this->_query, ['recursion' => $re_command]);
                }
            } else {
                $this->_query = 'SELECT 1=1';
            }

            return $this->_query;
        }

        /**
         * Function to analyze ALTER TABLE command and sets the data to an array.
         *
         * @param string $command
         *
         * @return boolean|array
         * @access private
         */
        private function command_tokenizer($command)
        {
            $tokens = [];
            if (preg_match('/^(ADD|DROP|RENAME|MODIFY|CHANGE|ALTER)\\s*(\\w+)?\\s*(\\w+(\(.+\)|))?\\s*/ims', $command,
                $match)) {
                $the_rest = str_ireplace($match[0], '', $command);
                $match_1 = trim($match[1]);
                $match_2 = trim($match[2]);
                $match_3 = isset($match[3]) ? trim($match[3]) : '';
                switch (strtolower($match_1)) {
                    case 'add':
                        if (in_array(strtolower($match_2), ['fulltext', 'constraint', 'foreign'])) {
                            break;
                        } elseif (stripos('column', $match_2) !== false) {
                            $tokens['command'] = $match_1 . ' ' . $match_2;
                            $tokens['column_name'] = $match_3;
                            $tokens['column_def'] = trim($the_rest);
                        } elseif (stripos('primary', $match_2) !== false) {
                            $tokens['command'] = $match_1 . ' ' . $match_2 . ' ' . $match_3;
                            $tokens['column_name'] = $the_rest;
                        } elseif (stripos('unique', $match_2) !== false) {
                            list($index_name, $col_name) = preg_split('/[\(\)]/s', trim($the_rest), -1,
                                PREG_SPLIT_DELIM_CAPTURE);
                            $tokens['unique'] = true;
                            $tokens['command'] = $match_1 . ' ' . $match_3;
                            $tokens['index_name'] = trim($index_name);
                            $tokens['column_name'] = '(' . trim($col_name) . ')';
                        } elseif (in_array(strtolower($match_2), ['index', 'key'])) {
                            $tokens['command'] = $match_1 . ' ' . $match_2;
                            if ($match_3 == '') {
                                $tokens['index_name'] = str_replace(['(', ')'], '', $the_rest);
                            } else {
                                $tokens['index_name'] = $match_3;
                            }
                            $tokens['column_name'] = trim($the_rest);
                        } else {
                            $tokens['command'] = $match_1 . ' COLUMN';
                            $tokens['column_name'] = $match_2;
                            $tokens['column_def'] = $match_3 . ' ' . $the_rest;
                        }
                        break;
                    case 'drop':
                        if (stripos('column', $match_2) !== false) {
                            $tokens['command'] = $match_1 . ' ' . $match_2;
                            $tokens['column_name'] = trim($match_3);
                        } elseif (stripos('primary', $match_2) !== false) {
                            $tokens['command'] = $match_1 . ' ' . $match_2 . ' ' . $match_3;
                        } elseif (in_array(strtolower($match_2), ['index', 'key'])) {
                            $tokens['command'] = $match_1 . ' ' . $match_2;
                            $tokens['index_name'] = $match_3;
                        } elseif (stripos('primary', $match_2) !== false) {
                            $tokens['command'] = $match_1 . ' ' . $match_2 . ' ' . $match_3;
                        } else {
                            $tokens['command'] = $match_1 . ' COLUMN';
                            $tokens['column_name'] = $match_2;
                        }
                        break;
                    case 'rename':
                        if (stripos('to', $match_2) !== false) {
                            $tokens['command'] = $match_1 . ' ' . $match_2;
                            $tokens['column_name'] = $match_3;
                        } else {
                            $tokens['command'] = $match_1 . ' TO';
                            $tokens['column_name'] = $match_2;
                        }
                        break;
                    case 'modify':
                        if (stripos('column', $match_2) !== false) {
                            $tokens['command'] = $match_1 . ' ' . $match_2;
                            $tokens['column_name'] = $match_3;
                            $tokens['column_def'] = trim($the_rest);
                        } else {
                            $tokens['command'] = $match_1 . ' COLUMN';
                            $tokens['column_name'] = $match_2;
                            $tokens['column_def'] = $match_3 . ' ' . trim($the_rest);
                        }
                        break;
                    case 'change':
                        $the_rest = trim($the_rest);
                        if (stripos('column', $match_2) !== false) {
                            $tokens['command'] = $match_1 . ' ' . $match_2;
                            $tokens['old_column'] = $match_3;
                            list($new_col) = explode(' ', $the_rest);
                            $tmp_col = preg_replace('/\(.+?\)/im', '', $new_col);
                            if (array_key_exists(strtolower($tmp_col), $this->array_types)) {
                                $tokens['column_def'] = $the_rest;
                            } else {
                                $tokens['new_column'] = $new_col;
                                $col_def = str_replace($new_col, '', $the_rest);
                                $tokens['column_def'] = trim($col_def);
                            }
                        } else {
                            $tokens['command'] = $match_1 . ' column';
                            $tokens['old_column'] = $match_2;
                            $tmp_col = preg_replace('/\(.+?\)/im', '', $match_3);
                            if (array_key_exists(strtolower($tmp_col), $this->array_types)) {
                                $tokens['column_def'] = $match_3 . ' ' . $the_rest;
                            } else {
                                $tokens['new_column'] = $match_3;
                                $tokens['column_def'] = $the_rest;
                            }
                        }
                        break;
                    case 'alter':
                        if (stripos('column', $match_2) !== false) {
                            $tokens['command'] = $match_1 . ' ' . $match_2;
                            $tokens['column_name'] = $match_3;
                            list($set_or_drop) = explode(' ', $the_rest);
                            if (stripos('set', $set_or_drop) !== false) {
                                $tokens['default_command'] = 'SET DEFAULT';
                                $default_value = str_ireplace('set default', '', $the_rest);
                                $tokens['default_value'] = trim($default_value);
                            } else {
                                $tokens['default_command'] = 'DROP DEFAULT';
                            }
                        } else {
                            $tokens['command'] = $match_1 . ' COLUMN';
                            $tokens['column_name'] = $match_2;
                            if (stripos('set', $match_3) !== false) {
                                $tokens['default_command'] = 'SET DEFAULT';
                                $default_value = str_ireplace('default', '', $the_rest);
                                $tokens['default_value'] = trim($default_value);
                            } else {
                                $tokens['default_command'] = 'DROP DEFAULT';
                            }
                        }
                        break;
                    default:
                        break;
                }

                return $tokens;
            }
        }

        /**
         * Function to handle single command.
         *
         * @access private
         *
         * @param array of string $queries
         *
         * @return string
         */
        private function handle_single_command($queries)
        {
            $tokenized_query = $queries;
            if (stripos($tokenized_query['command'], 'add column') !== false) {
                $column_def = $this->convert_field_types($tokenized_query['column_name'], $tokenized_query['column_def']);
                $query = "ALTER TABLE {$tokenized_query['table_name']} ADD COLUMN {$tokenized_query['column_name']} $column_def";
            } elseif (stripos($tokenized_query['command'], 'rename') !== false) {
                $query = "ALTER TABLE {$tokenized_query['table_name']} RENAME TO {$tokenized_query['column_name']}";
            } elseif (stripos($tokenized_query['command'], 'add index') !== false) {
                $unique = isset($tokenized_query['unique']) ? 'UNIQUE' : '';
                $query = "CREATE $unique INDEX IF NOT EXISTS {$tokenized_query['index_name']} ON {$tokenized_query['table_name']} {$tokenized_query['column_name']}";
            } elseif (stripos($tokenized_query['command'], 'drop index') !== false) {
                $query = "DROP INDEX IF EXISTS {$tokenized_query['index_name']}";
            } else {
                $query = 'SELECT 1=1';
            }

            return $query;
        }

        /**
         * Function to handle ADD PRIMARY KEY.
         *
         * @access private
         *
         * @param array of string $queries
         *
         * @return array of string
         */
        private function handle_add_primary_key($queries)
        {
            $tokenized_query = $queries;
            $tbl_name = $tokenized_query['table_name'];
            $temp_table = 'temp_' . $tokenized_query['table_name'];
            $_wpdb = new wpsqlitedb();
            $query_obj = $_wpdb->get_results("SELECT sql FROM sqlite_master WHERE tbl_name='$tbl_name'");
            $_wpdb = null;
            for ($i = 0; $i < count($query_obj); $i++) {
                $index_queries[$i] = $query_obj[$i]->sql;
            }
            $table_query = array_shift($index_queries);
            $table_query = str_replace($tokenized_query['table_name'], $temp_table, $table_query);
            $table_query = rtrim($table_query, ')');
            $table_query = ", PRIMARY KEY {$tokenized_query['column_name']}";
            $query[] = $table_query;
            $query[] = "INSERT INTO $temp_table SELECT * FROM {$tokenized_query['table_name']}";
            $query[] = "DROP TABLE IF EXISTS {$tokenized_query['table_name']}";
            $query[] = "ALTER TABLE $temp_table RENAME TO {$tokenized_query['table_name']}";
            foreach ($index_queries as $index) {
                $query[] = $index;
            }

            return $query;
        }

        /**
         * Function to handle DROP PRIMARY KEY.
         *
         * @access private
         *
         * @param array of string $queries
         *
         * @return array of string
         */
        private function handle_drop_primary_key($queries)
        {
            $tokenized_query = $queries;
            $temp_table = 'temp_' . $tokenized_query['table_name'];
            $_wpdb = new wpsqlitedb();
            $query_obj = $_wpdb->get_results("SELECT sql FROM sqlite_master WHERE tbl_name='{$tokenized_query['table_name']}'");
            $_wpdb = null;
            for ($i = 0; $i < count($query_obj); $i++) {
                $index_queries[$i] = $query_obj[$i]->sql;
            }
            $table_query = array_shift($index_queries);
            $pattern1 = '/^\\s*PRIMARY\\s*KEY\\s*\(.*\)/im';
            $pattern2 = '/^\\s*.*(PRIMARY\\s*KEY\\s*(:?AUTOINCREMENT|))\\s*(?!\()/im';
            if (preg_match($pattern1, $table_query, $match)) {
                $table_query = str_replace($match[0], '', $table_query);
            } elseif (preg_match($pattern2, $table_query, $match)) {
                $table_query = str_replace($match[1], '', $table_query);
            }
            $table_query = str_replace($tokenized_query['table_name'], $temp_table, $table_query);
            $query[] = $table_query;
            $query[] = "INSERT INTO $temp_table SELECT * FROM {$tokenized_query['table_name']}";
            $query[] = "DROP TABLE IF EXISTS {$tokenized_query['table_name']}";
            $query[] = "ALTER TABLE $temp_table RENAME TO {$tokenized_query['table_name']}";
            foreach ($index_queries as $index) {
                $query[] = $index;
            }

            return $query;
        }

        /**
         * Function to handle MODIFY COLUMN.
         *
         * @access private
         *
         * @param array of string $queries
         *
         * @return string|array of string
         */
        private function handle_modify_command($queries)
        {
            $tokenized_query = $queries;
            $temp_table = 'temp_' . $tokenized_query['table_name'];
            $column_def = $this->convert_field_types($tokenized_query['column_name'], $tokenized_query['column_def']);
            $_wpdb = new wpsqlitedb();
            $query_obj = $_wpdb->get_results("SELECT sql FROM sqlite_master WHERE tbl_name='{$tokenized_query['table_name']}'");
            $_wpdb = null;
            for ($i = 0; $i < count($query_obj); $i++) {
                $index_queries[$i] = $query_obj[$i]->sql;
            }
            $create_query = array_shift($index_queries);
            if (stripos($create_query, $tokenized_query['column_name']) === false) {
                return 'SELECT 1=1';
            } elseif (preg_match("/{$tokenized_query['column_name']}\\s*{$column_def}\\s*[,)]/i", $create_query)) {
                return 'SELECT 1=1';
            }
            $create_query = preg_replace("/{$tokenized_query['table_name']}/i", $temp_table, $create_query);
            if (preg_match("/\\b{$tokenized_query['column_name']}\\s*.*(?=,)/ims", $create_query)) {
                $create_query = preg_replace("/\\b{$tokenized_query['column_name']}\\s*.*(?=,)/ims",
                    "{$tokenized_query['column_name']} {$column_def}", $create_query);
            } elseif (preg_match("/\\b{$tokenized_query['column_name']}\\s*.*(?=\))/ims", $create_query)) {
                $create_query = preg_replace("/\\b{$tokenized_query['column_name']}\\s*.*(?=\))/ims",
                    "{$tokenized_query['column_name']} {$column_def}", $create_query);
            }
            $query[] = $create_query;
            $query[] = "INSERT INTO $temp_table SELECT * FROM {$tokenized_query['table_name']}";
            $query[] = "DROP TABLE IF EXISTS {$tokenized_query['table_name']}";
            $query[] = "ALTER TABLE $temp_table RENAME TO {$tokenized_query['table_name']}";
            foreach ($index_queries as $index) {
                $query[] = $index;
            }

            return $query;
        }

        /**
         * Function to handle CHANGE COLUMN.
         *
         * @access private
         *
         * @param array of string $queries
         *
         * @return string|array of string
         */
        private function handle_change_command($queries)
        {
            $col_check = false;
            $old_fields = '';
            $tokenized_query = $queries;
            $temp_table = 'temp_' . $tokenized_query['table_name'];
            if (isset($tokenized_query['new_column'])) {
                $column_name = $tokenized_query['new_column'];
            } else {
                $column_name = $tokenized_query['old_column'];
            }
            $column_def = $this->convert_field_types($column_name, $tokenized_query['column_def']);
            $_wpdb = new wpsqlitedb();
            $col_obj = $_wpdb->get_results("SHOW COLUMNS FROM {$tokenized_query['table_name']}");
            foreach ($col_obj as $col) {
                if (stripos($col->Field, $tokenized_query['old_column']) !== false) {
                    $col_check = true;
                }
                $old_fields .= $col->Field . ',';
            }
            if ($col_check == false) {
                $_wpdb = null;

                return 'SELECT 1=1';
            }
            $old_fields = rtrim($old_fields, ',');
            $new_fields = str_ireplace($tokenized_query['old_column'], $column_name, $old_fields);
            $query_obj = $_wpdb->get_results("SELECT sql FROM sqlite_master WHERE tbl_name='{$tokenized_query['table_name']}'");
            $_wpdb = null;
            for ($i = 0; $i < count($query_obj); $i++) {
                $index_queries[$i] = $query_obj[$i]->sql;
            }
            $create_query = array_shift($index_queries);
            $create_query = preg_replace("/{$tokenized_query['table_name']}/i", $temp_table, $create_query);
            if (preg_match("/\\b{$tokenized_query['old_column']}\\s*(.+?)(?=,)/ims", $create_query, $match)) {
                if (stripos(trim($match[1]), $column_def) !== false) {
                    return 'SELECT 1=1';
                } else {
                    $create_query = preg_replace("/\\b{$tokenized_query['old_column']}\\s*.+?(?=,)/ims",
                        "{$column_name} {$column_def}", $create_query, 1);
                }
            } elseif (preg_match("/\\b{$tokenized_query['old_column']}\\s*(.+?)(?=\))/ims", $create_query, $match)) {
                if (stripos(trim($match[1]), $column_def) !== false) {
                    return 'SELECT 1=1';
                } else {
                    $create_query = preg_replace("/\\b{$tokenized_query['old_column']}\\s*.*(?=\))/ims",
                        "{$column_name} {$column_def}", $create_query, 1);
                }
            }
            $query[] = $create_query;
            $query[] = "INSERT INTO $temp_table ($new_fields) SELECT $old_fields FROM {$tokenized_query['table_name']}";
            $query[] = "DROP TABLE IF EXISTS {$tokenized_query['table_name']}";
            $query[] = "ALTER TABLE $temp_table RENAME TO {$tokenized_query['table_name']}";
            foreach ($index_queries as $index) {
                $query[] = $index;
            }

            return $query;
        }

        /**
         * Function to handle ALTER COLUMN.
         *
         * @access private
         *
         * @param array of string $queries
         *
         * @return string|array of string
         */
        private function handle_alter_command($queries)
        {
            $tokenized_query = $queries;
            $temp_table = 'temp_' . $tokenized_query['table_name'];
            if (isset($tokenized_query['default_value'])) {
                $def_value = $this->convert_field_types($tokenized_query['column_name'], $tokenized_query['default_value']);
                $def_value = 'DEFAULT ' . $def_value;
            } else {
                $def_value = null;
            }
            $_wpdb = new wpsqlitedb();
            $query_obj = $_wpdb->get_results("SELECT sql FROM sqlite_master WHERE tbl_name='{$tokenized_query['table_name']}'");
            $_wpdb = null;
            for ($i = 0; $i < count($query_obj); $i++) {
                $index_queries[$i] = $query_obj[$i]->sql;
            }
            $create_query = array_shift($index_queries);
            if (stripos($create_query, $tokenized_query['column_name']) === false) {
                return 'SELECT 1=1';
            }
            if (preg_match("/\\s*({$tokenized_query['column_name']})\\s*(.*)?(DEFAULT\\s*.*)[,)]/im", $create_query,
                $match)) {
                $col_name = trim($match[1]);
                $col_def = trim($match[2]);
                $col_def_esc = str_replace(['(', ')'], ['\(', '\)'], $col_def);
                $checked_col_def = $this->convert_field_types($col_name, $col_def);
                $old_default = trim($match[3]);
                $pattern = "/$col_name\\s*$col_def_esc\\s*$old_default/im";
                if (is_null($def_value)) {
                    $replacement = $col_name . ' ' . $checked_col_def;
                } else {
                    $replacement = $col_name . ' ' . $checked_col_def . ' ' . $def_value;
                }
                $create_query = preg_replace($pattern, $replacement, $create_query);
                $create_query = str_ireplace($tokenized_query['table_name'], $temp_table, $create_query);
            } elseif (preg_match("/\\s*({$tokenized_query['column_name']})\\s*(.*)?[,)]/im", $create_query, $match)) {
                $col_name = trim($match[1]);
                $col_def = trim($match[2]);
                $col_def_esc = str_replace(['(', ')'], ['\(', '\)'], $col_def);
                $checked_col_def = $this->convert_field_types($col_name, $col_def);
                $pattern = "/$col_name\\s*$col_def_esc/im";
                if (is_null($def_value)) {
                    $replacement = $col_name . ' ' . $checked_col_def;
                } else {
                    $replacement = $col_name . ' ' . $checked_col_def . ' ' . $def_value;
                }
                $create_query = preg_replace($pattern, $replacement, $create_query);
                $create_query = str_ireplace($tokenized_query['table_name'], $temp_table, $create_query);
            } else {
                return 'SELECT 1=1';
            }
            $query[] = $create_query;
            $query[] = "INSERT INTO $temp_table SELECT * FROM {$tokenized_query['table_name']}";
            $query[] = "DROP TABLE IF EXISTS {$tokenized_query['table_name']}";
            $query[] = "ALTER TABLE $temp_table RENAME TO {$tokenized_query['table_name']}";
            foreach ($index_queries as $index) {
                $query[] = $index;
            }

            return $query;
        }

        /**
         * Function to change the field definition to SQLite compatible data type.
         *
         * @access private
         *
         * @param string $col_name
         * @param string $col_def
         *
         * @return string
         */
        private function convert_field_types($col_name, $col_def)
        {
            $array_curtime = ['current_timestamp', 'current_time', 'current_date'];
            $array_reptime = ["'0000-00-00 00:00:00'", "'0000-00-00 00:00:00'", "'0000-00-00'"];
            $def_string = str_replace('`', '', $col_def);
            foreach ($this->array_types as $o => $r) {
                $pattern = "/\\b$o\\s*(\([^\)]*\)*)?\\s*/ims";
                if (preg_match($pattern, $def_string)) {
                    $def_string = preg_replace($pattern, "$r ", $def_string);
                    break;
                }
            }
            $def_string = preg_replace('/unsigned/im', '', $def_string);
            $def_string = preg_replace('/auto_increment/im', 'PRIMARY KEY AUTOINCREMENT', $def_string);
            // when you use ALTER TABLE ADD, you can't use current_*. so we replace
            $def_string = str_ireplace($array_curtime, $array_reptime, $def_string);
            // colDef is enum
            $pattern_enum = '/enum\((.*?)\)([^,\)]*)/ims';
            if (preg_match($pattern_enum, $col_def, $matches)) {
                $def_string = 'TEXT' . $matches[2] . ' CHECK (' . $col_name . ' IN (' . $matches[1] . '))';
            }

            return $def_string;
        }

        /**
         * Variable to store the data definition table.
         *
         * @access private
         * @var associative array
         */
        private $array_types = [
            'bit' => 'INTEGER',
            'bool' => 'INTEGER',
            'boolean' => 'INTEGER',
            'tinyint' => 'INTEGER',
            'smallint' => 'INTEGER',
            'mediumint' => 'INTEGER',
            'bigint' => 'INTEGER',
            'integer' => 'INTEGER',
            'int' => 'INTEGER',
            'float' => 'REAL',
            'double' => 'REAL',
            'decimal' => 'REAL',
            'dec' => 'REAL',
            'numeric' => 'REAL',
            'fixed' => 'REAL',
            'datetime' => 'TEXT',
            'date' => 'TEXT',
            'timestamp' => 'TEXT',
            'time' => 'TEXT',
            'year' => 'TEXT',
            'varchar' => 'TEXT',
            'char' => 'TEXT',
            'varbinary' => 'BLOB',
            'binary' => 'BLOB',
            'tinyblob' => 'BLOB',
            'mediumblob' => 'BLOB',
            'longblob' => 'BLOB',
            'blob' => 'BLOB',
            'tinytext' => 'TEXT',
            'mediumtext' => 'TEXT',
            'longtext' => 'TEXT',
            'text' => 'TEXT',
        ];
    }

    /**
     * Function to create tables according to the schemas of WordPress.
     *
     * This is executed only once while installation.
     *
     * @return boolean
     */
    function make_db_sqlite()
    {
        include_once ABSPATH . 'wp-admin/includes/schema.php';
        $index_array = [];

        $table_schemas = wp_get_db_schema();
        $queries = explode(";", $table_schemas);
        $query_parser = new CreateQuery();
        try {
            $pdo = new PDO('sqlite:' . FQDB, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $err) {
            $err_data = $err->errorInfo;
            $message = 'Database connection error!<br />';
            $message .= sprintf("Error message is: %s", $err_data[2]);
            wp_die($message, 'Database Error!');
        }

        try {
            $pdo->beginTransaction();
            foreach ($queries as $query) {
                $query = trim($query);
                if (empty($query)) {
                    continue;
                }
                $rewritten_query = $query_parser->rewrite_query($query);
                if (is_array($rewritten_query)) {
                    $table_query = array_shift($rewritten_query);
                    $index_queries = $rewritten_query;
                    $table_query = trim($table_query);
                    $pdo->exec($table_query);
                    //foreach($rewritten_query as $single_query) {
                    //  $single_query = trim($single_query);
                    //  $pdo->exec($single_query);
                    //}
                } else {
                    $rewritten_query = trim($rewritten_query);
                    $pdo->exec($rewritten_query);
                }
            }
            $pdo->commit();
            if ($index_queries) {
                // $query_parser rewrites KEY to INDEX, so we don't need KEY pattern
                $pattern = '/CREATE\\s*(UNIQUE\\s*INDEX|INDEX)\\s*IF\\s*NOT\\s*EXISTS\\s*(\\w+)?\\s*.*/im';
                $pdo->beginTransaction();
                foreach ($index_queries as $index_query) {
                    preg_match($pattern, $index_query, $match);
                    $index_name = trim($match[2]);
                    if (in_array($index_name, $index_array)) {
                        $r = rand(0, 50);
                        $replacement = $index_name . "_$r";
                        $index_query = str_ireplace('EXISTS ' . $index_name, 'EXISTS ' . $replacement,
                            $index_query);
                    } else {
                        $index_array[] = $index_name;
                    }
                    $pdo->exec($index_query);
                }
                $pdo->commit();
            }
        } catch (PDOException $err) {
            $err_data = $err->errorInfo;
            $err_code = $err_data[1];
            if (5 == $err_code || 6 == $err_code) {
                // if the database is locked, commit again
                $pdo->commit();
            } else {
                $pdo->rollBack();
                $message = sprintf("Error occured while creating tables or indexes...<br />Query was: %s<br />",
                    var_export($rewritten_query, true));
                $message .= sprintf("Error message is: %s", $err_data[2]);
                wp_die($message, 'Database Error!');
            }
        }

        $query_parser = null;
        $pdo = null;

        return true;
    }
} // WP_SQLite_DB namespace

namespace {

    /**
     * Installs the site.
     *
     * Runs the required functions to set up and populate the database,
     * including primary admin user and initial options.
     *
     * @since 2.1.0
     *
     * @param string $blog_title Site title.
     * @param string $user_name User's username.
     * @param string $user_email User's email.
     * @param bool $public Whether site is public.
     * @param string $deprecated Optional. Not used.
     * @param string $user_password Optional. User's chosen password. Default empty (random password).
     * @param string $language Optional. Language chosen. Default empty.
     *
     * @return array Array keys 'url', 'user_id', 'password', and 'password_message'.
     */
    function wp_install($blog_title, $user_name, $user_email, $public, $deprecated = '', $user_password = '', $language = '')
    {
        if (! empty($deprecated)) {
            _deprecated_argument(__FUNCTION__, '2.6.0');
        }

        wp_check_mysql_version();
        wp_cache_flush();
        /* begin wp-sqlite-db changes */
        // make_db_current_silent();
        WP_SQLite_DB\make_db_sqlite();
        /* end wp-sqlite-db changes */
        populate_options();
        populate_roles();

        update_option('blogname', $blog_title);
        update_option('admin_email', $user_email);
        update_option('blog_public', $public);

        // Freshness of site - in the future, this could get more specific about actions taken, perhaps.
        update_option('fresh_site', 1);

        if ($language) {
            update_option('WPLANG', $language);
        }

        $guessurl = wp_guess_url();

        update_option('siteurl', $guessurl);

        // If not a public blog, don't ping.
        if (! $public) {
            update_option('default_pingback_flag', 0);
        }

        /*
         * Create default user. If the user already exists, the user tables are
         * being shared among sites. Just set the role in that case.
         */
        $user_id = username_exists($user_name);
        $user_password = trim($user_password);
        $email_password = false;
        if (! $user_id && empty($user_password)) {
            $user_password = wp_generate_password(12, false);
            $message = __('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you.');
            $user_id = wp_create_user($user_name, $user_password, $user_email);
            update_user_option($user_id, 'default_password_nag', true, true);
            $email_password = true;
        } elseif (! $user_id) {
            // Password has been provided
            $message = '<em>' . __('Your chosen password.') . '</em>';
            $user_id = wp_create_user($user_name, $user_password, $user_email);
        } else {
            $message = __('User already exists. Password inherited.');
        }

        $user = new WP_User($user_id);
        $user->set_role('administrator');

        wp_install_defaults($user_id);

        wp_install_maybe_enable_pretty_permalinks();

        flush_rewrite_rules();

        wp_new_blog_notification($blog_title, $guessurl, $user_id, ($email_password ? $user_password : __('The password you chose during installation.')));

        wp_cache_flush();

        /**
         * Fires after a site is fully installed.
         *
         * @since 3.9.0
         *
         * @param WP_User $user The site owner.
         */
        do_action('wp_install', $user);

        return ['url' => $guessurl, 'user_id' => $user_id, 'password' => $user_password, 'password_message' => $message];
    }

    $GLOBALS['wpdb'] = new WP_SQLite_DB\wpsqlitedb();
}
