<?php
/*
 * Copyright 2010-2011 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */


/*%******************************************************************************************%*/
// CORE DEPENDENCIES

// Look for include file in the same directory (e.g. `./config.inc.php`).
if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php'))
{
	include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php';
}
// Fallback to `~/.aws/sdk/config.inc.php`
elseif (getenv('HOME') && file_exists(getenv('HOME') . DIRECTORY_SEPARATOR . '.aws' . DIRECTORY_SEPARATOR . 'sdk' . DIRECTORY_SEPARATOR . 'config.inc.php'))
{
	include_once getenv('HOME') . DIRECTORY_SEPARATOR . '.aws' . DIRECTORY_SEPARATOR . 'sdk' . DIRECTORY_SEPARATOR . 'config.inc.php';
}


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Default CFRuntime Exception.
 */
class CFRuntime_Exception extends Exception {}


/*%******************************************************************************************%*/
// DETERMINE WHAT ENVIRONMENT DATA TO ADD TO THE USERAGENT FOR METRIC TRACKING

/*
	Define a temporary callback function for this calculation. Get the PHP version and any
	required/optional extensions that are leveraged.

	Tracking this data gives Amazon better metrics about what configurations are being used
	so that forward-looking plans for the code can be made with more certainty (e.g. What
	version of PHP are most people running? Do they tend to have the latest PCRE?).
*/
function __aws_sdk_ua_callback()
{
	$ua_append = '';
	$extensions = get_loaded_extensions();
	$sorted_extensions = array();

	if ($extensions)
	{
		foreach ($extensions as $extension)
		{
			if ($extension === 'curl' && function_exists('curl_version'))
			{
				$curl_version = curl_version();
				$sorted_extensions[strtolower($extension)] = $curl_version['version'];
			}
			elseif ($extension === 'pcre' && defined('PCRE_VERSION'))
			{
				$pcre_version = explode(' ', PCRE_VERSION);
				$sorted_extensions[strtolower($extension)] = $pcre_version[0];
			}
			elseif ($extension === 'openssl' && defined('OPENSSL_VERSION_TEXT'))
			{
				$openssl_version = explode(' ', OPENSSL_VERSION_TEXT);
				$sorted_extensions[strtolower($extension)] = $openssl_version[1];
			}
			else
			{
				$sorted_extensions[strtolower($extension)] = phpversion($extension);
			}
		}
	}

	foreach (array('simplexml', 'json', 'pcre', 'spl', 'curl', 'openssl', 'apc', 'xcache', 'memcache', 'memcached', 'pdo', 'pdo_sqlite', 'sqlite', 'sqlite3', 'zlib', 'xdebug') as $ua_ext)
	{
		if (isset($sorted_extensions[$ua_ext]) && $sorted_extensions[$ua_ext])
		{
			$ua_append .= ' ' . $ua_ext . '/' . $sorted_extensions[$ua_ext];
		}
		elseif (isset($sorted_extensions[$ua_ext]))
		{
			$ua_append .= ' ' . $ua_ext . '/0';
		}
	}

	foreach (array('memory_limit', 'date.timezone', 'open_basedir', 'safe_mode', 'zend.enable_gc') as $cfg)
	{
		$cfg_value = get_cfg_var($cfg);

		if (in_array($cfg, array('memory_limit', 'date.timezone'), true))
		{
			$ua_append .= ' ' . $cfg . '/' . str_replace('/', '.', $cfg_value);
		}
		elseif (in_array($cfg, array('open_basedir', 'safe_mode', 'zend.enable_gc'), true))
		{
			if ($cfg_value === false || $cfg_value === '' || $cfg_value === 0)
			{
				$cfg_value = 'off';
			}
			elseif ($cfg_value === true || $cfg_value === '1' || $cfg_value === 1)
			{
				$cfg_value = 'on';
			}

			$ua_append .= ' ' . $cfg . '/' . $cfg_value;
		}
	}

	return $ua_append;
}


/*%******************************************************************************************%*/
// INTERMEDIARY CONSTANTS

define('CFRUNTIME_NAME', 'aws-sdk-php');
define('CFRUNTIME_VERSION', '1.4.3');
// define('CFRUNTIME_BUILD', gmdate('YmdHis', filemtime(__FILE__))); // @todo: Hardcode for release.
define('CFRUNTIME_BUILD', '20110930191027');
define('CFRUNTIME_USERAGENT', CFRUNTIME_NAME . '/' . CFRUNTIME_VERSION . ' PHP/' . PHP_VERSION . ' ' . str_replace(' ', '_', php_uname('s')) . '/' . str_replace(' ', '_', php_uname('r')) . ' Arch/' . php_uname('m') . ' SAPI/' . php_sapi_name() . ' Integer/' . PHP_INT_MAX . ' Build/' . CFRUNTIME_BUILD . __aws_sdk_ua_callback());


/*%******************************************************************************************%*/
// CLASS

/**
 * Core functionality and default settings shared across all SDK classes. All methods and properties in this
 * class are inherited by the service-specific classes.
 *
 * @version 2011.07.28
 * @license See the included NOTICE.md file for more information.
 * @copyright See the included NOTICE.md file for more information.
 * @link http://aws.amazon.com/php/ PHP Developer Center
 */
class CFRuntime
{
	/*%******************************************************************************************%*/
	// CONSTANTS

	/**
	 * Name of the software.
	 */
	const NAME = CFRUNTIME_NAME;

	/**
	 * Version of the software.
	 */
	const VERSION = CFRUNTIME_VERSION;

	/**
	 * Build ID of the software.
	 */
	const BUILD = CFRUNTIME_BUILD;

	/**
	 * User agent string used to identify the software.
	 */
	const USERAGENT = CFRUNTIME_USERAGENT;


	/*%******************************************************************************************%*/
	// PROPERTIES

	/**
	 * The Amazon API Key.
	 */
	public $key;

	/**
	 * The Amazon API Secret Key.
	 */
	public $secret_key;

	/**
	 * The Amazon Authentication Token.
	 */
	public $auth_token;

	/**
	 * The Amazon Account ID, without hyphens.
	 */
	public $account_id;

	/**
	 * The Amazon Associates ID.
	 */
	public $assoc_id;

	/**
	 * Handle for the utility functions.
	 */
	public $util;

	/**
	 * An identifier for the current AWS service.
	 */
	public $service = null;

	/**
	 * The supported API version.
	 */
	public $api_version = null;

	/**
	 * The state of whether auth should be handled as AWS Query.
	 */
	public $use_aws_query = true;

	/**
	 * The default class to use for utilities (defaults to <CFUtilities>).
	 */
	public $utilities_class = 'CFUtilities';

	/**
	 * The default class to use for HTTP requests (defaults to <CFRequest>).
	 */
	public $request_class = 'CFRequest';

	/**
	 * The default class to use for HTTP responses (defaults to <CFResponse>).
	 */
	public $response_class = 'CFResponse';

	/**
	 * The default class to use for parsing XML (defaults to <CFSimpleXML>).
	 */
	public $parser_class = 'CFSimpleXML';

	/**
	 * The default class to use for handling batch requests (defaults to <CFBatchRequest>).
	 */
	public $batch_class = 'CFBatchRequest';

	/**
	 * The number of seconds to adjust the request timestamp by (defaults to 0).
	 */
	public $adjust_offset = 0;

	/**
	 * The state of SSL/HTTPS use.
	 */
	public $use_ssl = true;

	/**
	 * The state of SSL certificate verification.
	 */
	public $ssl_verification = true;

	/**
	 * The proxy to use for connecting.
	 */
	public $proxy = null;

	/**
	 * The alternate hostname to use, if any.
	 */
	public $hostname = null;

	/**
	 * The state of the capability to override the hostname with <set_hostname()>.
	 */
	public $override_hostname = true;

	/**
	 * The alternate port number to use, if any.
	 */
	public $port_number = null;

	/**
	 * The alternate resource prefix to use, if any.
	 */
	public $resource_prefix = null;

	/**
	 * The state of cache flow usage.
	 */
	public $use_cache_flow = false;

	/**
	 * The caching class to use.
	 */
	public $cache_class = null;

	/**
	 * The caching location to use.
	 */
	public $cache_location = null;

	/**
	 * When the cache should be considered stale.
	 */
	public $cache_expires = null;

	/**
	 * The state of cache compression.
	 */
	public $cache_compress = null;

	/**
	 * The current instantiated cache object.
	 */
	public $cache_object = null;

	/**
	 * The current instantiated batch request object.
	 */
	public $batch_object = null;

	/**
	 * The internally instantiated batch request object.
	 */
	public $internal_batch_object = null;

	/**
	 * The state of batch flow usage.
	 */
	public $use_batch_flow = false;

	/**
	 * The state of the cache deletion setting.
	 */
	public $delete_cache = false;

	/**
	 * The state of the debug mode setting.
	 */
	public $debug_mode = false;

	/**
	 * The number of times to retry failed requests.
	 */
	public $max_retries = 3;

	/**
	 * The user-defined callback function to call when a stream is read from.
	 */
	public $registered_streaming_read_callback = null;

	/**
	 * The user-defined callback function to call when a stream is written to.
	 */
	public $registered_streaming_write_callback = null;


	/*%******************************************************************************************%*/
	// CONSTRUCTOR

	/**
	 * The constructor. You would not normally instantiate this class directly. Rather, you would instantiate
	 * a service-specific class.
	 *
	 * @param string $key (Optional) Your AWS key, or a session key. If blank, it will look for the <code>AWS_KEY</code> constant.
	 * @param string $secret_key (Optional) Your AWS secret key, or a session secret key. If blank, it will look for the <code>AWS_SECRET_KEY</code> constant.
	 * @param string $token (optional) An AWS session token. If blank, a request will be made to the AWS Secure Token Service to fetch a set of session credentials.
	 * @return boolean A value of `false` if no valid values are set, otherwise `true`.
	 */
	public function __construct($key = null, $secret_key = null, $token = null)
	{
		// Instantiate the utilities class.
		$this->util = new $this->utilities_class();

		// Determine the current service.
		$this->service = get_class($this);

		// Set default values
		$this->key = null;
		$this->secret_key = null;
		$this->auth_token = $token;

		// If both a key and secret key are passed in, use those.
		if ($key && $secret_key)
		{
			$this->key = $key;
			$this->secret_key = $secret_key;
			return true;
		}
		// If neither are passed in, look for the constants instead.
		elseif (defined('AWS_KEY') && defined('AWS_SECRET_KEY'))
		{
			$this->key = AWS_KEY;
			$this->secret_key = AWS_SECRET_KEY;
			return true;
		}

		// Otherwise set the values to blank and return false.
		else
		{
			throw new CFRuntime_Exception('No valid credentials were used to authenticate with AWS.');
		}
	}

	/**
	 * Handle session-based authentication for services that support it.
	 *
	 * @param string $key (Optional) Your AWS key, or a session key. If blank, it will look for the <code>AWS_KEY</code> constant.
	 * @param string $secret_key (Optional) Your AWS secret key, or a session secret key. If blank, it will look for the <code>AWS_SECRET_KEY</code> constant.
	 * @param string $token (optional) An AWS session token. If blank, a request will be made to the AWS Secure Token Service to fetch a set of session credentials.
	 * @return boolean A value of `false` if no valid values are set, otherwise `true`.
	 */
	public function session_based_auth($key = null, $secret_key = null, $token = null)
	{
		// Instantiate the utilities class.
		$this->util = new $this->utilities_class();

		// Use 'em if we've got 'em
		if ($key && $secret_key && $token)
		{
			$this->key = $key;
			$this->secret_key = $secret_key;
			$this->auth_token = $token;
			return true;
		}
		else
		{
			if (!$key && !defined('AWS_KEY'))
			{
				// @codeCoverageIgnoreStart
				throw new CFRuntime_Exception('No account key was passed into the constructor, nor was it set in the AWS_KEY constant.');
				// @codeCoverageIgnoreEnd
			}

			if (!$secret_key && !defined('AWS_SECRET_KEY'))
			{
				// @codeCoverageIgnoreStart
				throw new CFRuntime_Exception('No account secret was passed into the constructor, nor was it set in the AWS_SECRET_KEY constant.');
				// @codeCoverageIgnoreEnd
			}

			// If both a key and secret key are passed in, use those.
			if ($key && $secret_key)
			{
				$this->key = $key;
				$this->secret_key = $secret_key;
			}
			// If neither are passed in, look for the constants instead.
			elseif (defined('AWS_KEY') && defined('AWS_SECRET_KEY'))
			{
				$this->key = AWS_KEY;
				$this->secret_key = AWS_SECRET_KEY;
			}

			// Determine storage type.
			$this->set_cache_config(AWS_DEFAULT_CACHE_CONFIG);
			$cache_class = $this->cache_class;
			$cache_object = new $cache_class('aws_active_session_credentials_' . get_class($this) . '_' . $this->key, AWS_DEFAULT_CACHE_CONFIG, 3600); // AWS_DEFAULT_CACHE_CONFIG only matters if it's a file system path.

			// Fetch session credentials
			$session_credentials = $cache_object->response_manager(array($this, 'cache_token'), array($this->key, $this->secret_key));
			$this->auth_token = $session_credentials['SessionToken'];

			// If both a key and secret key are passed in, use those.
			if (isset($session_credentials['AccessKeyId']) && isset($session_credentials['SecretAccessKey']))
			{
				$this->key = $session_credentials['AccessKeyId'];
				$this->secret_key = $session_credentials['SecretAccessKey'];
				return true;
			}
			// Otherwise set the values to blank and return false.
			else
			{
				throw new CFRuntime_Exception('No valid credentials were used to authenticate with AWS.');
			}
		}
	}

	/**
	 * The callback function that is executed  while caching the session credentials.
	 *
	 * @param string $key (Optional) Your AWS key, or a session key. If blank, it will look for the <code>AWS_KEY</code> constant.
	 * @param string $secret_key (Optional) Your AWS secret key, or a session secret key. If blank, it will look for the <code>AWS_SECRET_KEY</code> constant.
	 * @return mixed The data to be cached or null.
	 */
	public function cache_token($key, $secret_key)
	{
		$token = new AmazonSTS($key, $secret_key);
		$response = $token->get_session_token();

		if ($response->isOK())
		{
			/*
			Array
			(
			    [AccessKeyId] => ******
			    [Expiration] => ******
			    [SecretAccessKey] => ******
				[SessionToken] => ******
			)
			*/
			return $response->body->GetSessionTokenResult->Credentials->to_array()->getArrayCopy();
		}

		return null;
	}

	/**
	 * Alternate approach to constructing a new instance. Supports chaining.
	 *
	 * @param string $key (Optional) Your AWS key, or a session key. If blank, it will look for the <code>AWS_KEY</code> constant.
	 * @param string $secret_key (Optional) Your AWS secret key, or a session secret key. If blank, it will look for the <code>AWS_SECRET_KEY</code> constant.
	 * @param string $token (optional) An AWS session token. If blank, a request will be made to the AWS Secure Token Service to fetch a set of session credentials.
	 * @return boolean A value of `false` if no valid values are set, otherwise `true`.
	 */
	public static function init($key = null, $secret_key = null, $token = null)
	{
		if (version_compare(PHP_VERSION, '5.3.0', '<'))
		{
			throw new Exception('PHP 5.3 or newer is required to instantiate a new class with CLASS::init().');
		}

		$self = get_called_class();
		return new $self($key, $secret_key, $token);
	}


	/*%******************************************************************************************%*/
	// MAGIC METHODS

	/**
	 * A magic method that allows `camelCase` method names to be translated into `snake_case` names.
	 *
	 * @param string $name (Required) The name of the method.
	 * @param array $arguments (Required) The arguments passed to the method.
	 * @return mixed The results of the intended method.
	 */
	public function  __call($name, $arguments)
	{
		// Convert camelCase method calls to snake_case.
		$method_name = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));

		if (method_exists($this, $method_name))
		{
			return call_user_func_array(array($this, $method_name), $arguments);
		}

		throw new CFRuntime_Exception('The method ' . $name . '() is undefined. Attempted to map to ' . $method_name . '() which is also undefined. Error occurred');
	}


	/*%******************************************************************************************%*/
	// SET CUSTOM SETTINGS

	/**
	 * Adjusts the current time. Use this method for occasions when a server is out of sync with Amazon
	 * servers.
	 *
	 * @param integer $seconds (Required) The number of seconds to adjust the sent timestamp by.
	 * @return $this A reference to the current instance.
	 */
	public function adjust_offset($seconds)
	{
		$this->adjust_offset = $seconds;
		return $this;
	}

	/**
	 * Set the proxy settings to use.
	 *
	 * @param string $proxy (Required) Accepts proxy credentials in the following format: `proxy://user:pass@hostname:port`
	 * @return $this A reference to the current instance.
	 */
	public function set_proxy($proxy)
	{
		$this->proxy = $proxy;
		return $this;
	}

	/**
	 * Set the hostname to connect to. This is useful for alternate services that are API-compatible with
	 * AWS, but run from a different hostname.
	 *
	 * @param string $hostname (Required) The alternate hostname to use in place of the default one. Useful for mock or test applications living on different hostnames.
	 * @param integer $port_number (Optional) The alternate port number to use in place of the default one. Useful for mock or test applications living on different port numbers.
	 * @return $this A reference to the current instance.
	 */
	public function set_hostname($hostname, $port_number = null)
	{
		if ($this->override_hostname)
		{
			$this->hostname = $hostname;

			if ($port_number)
			{
				$this->port_number = $port_number;
				$this->hostname .= ':' . (string) $this->port_number;
			}
		}

		return $this;
	}

	/**
	 * Set the resource prefix to use. This method is useful for alternate services that are API-compatible
	 * with AWS.
	 *
	 * @param string $prefix (Required) An alternate prefix to prepend to the resource path. Useful for mock or test applications.
	 * @return $this A reference to the current instance.
	 */
	public function set_resource_prefix($prefix)
	{
		$this->resource_prefix = $prefix;
		return $this;
	}

	/**
	 * Disables any subsequent use of the <set_hostname()> method.
	 *
	 * @param boolean $override (Optional) Whether or not subsequent calls to <set_hostname()> should be obeyed. A `false` value disables the further effectiveness of <set_hostname()>. Defaults to `true`.
	 * @return $this A reference to the current instance.
	 */
	public function allow_hostname_override($override = true)
	{
		$this->override_hostname = $override;
		return $this;
	}

	/**
	 * Disables SSL/HTTPS connections for hosts that don't support them. Some services, however, still
	 * require SSL support.
	 *
	 * This method will throw a user warning when invoked, which can be hidden by changing your
	 * <php:error_reporting()> settings.
	 *
	 * @return $this A reference to the current instance.
	 */
	public function disable_ssl()
	{
		trigger_error('Disabling SSL connections is potentially unsafe and highly discouraged.', E_USER_WARNING);
		$this->use_ssl = false;
		return $this;
	}

	/**
	 * Disables the verification of the SSL Certificate Authority. Doing so can enable an attacker to carry
	 * out a man-in-the-middle attack.
	 *
	 * https://secure.wikimedia.org/wikipedia/en/wiki/Man-in-the-middle_attack
	 *
	 * This method will throw a user warning when invoked, which can be hidden by changing your
	 * <php:error_reporting()> settings.
	 *
	 * @return $this A reference to the current instance.
	 */
	public function disable_ssl_verification($ssl_verification = false)
	{
		trigger_error('Disabling the verification of SSL certificates can lead to man-in-the-middle attacks. It is potentially unsafe and highly discouraged.', E_USER_WARNING);
		$this->ssl_verification = $ssl_verification;
		return $this;
	}

	/**
	 * Enables HTTP request/response header logging to `STDERR`.
	 *
	 * @param boolean $enabled (Optional) Whether or not to enable debug mode. Defaults to `true`.
	 * @return $this A reference to the current instance.
	 */
	public function enable_debug_mode($enabled = true)
	{
		$this->debug_mode = $enabled;
		return $this;
	}

	/**
	 * Sets the maximum number of times to retry failed requests.
	 *
	 * @param integer $retries (Optional) The maximum number of times to retry failed requests. Defaults to `3`.
	 * @return $this A reference to the current instance.
	 */
	public function set_max_retries($retries = 3)
	{
		$this->max_retries = $retries;
		return $this;
	}

	/**
	 * Set the caching configuration to use for response caching.
	 *
	 * @param string $location (Required) <p>The location to store the cache object in. This may vary by cache method.</p><ul><li>File - The local file system paths such as <code>./cache</code> (relative) or <code>/tmp/cache/</code> (absolute). The location must be server-writable.</li><li>APC - Pass in <code>apc</code> to use this lightweight cache. You must have the <a href="http://php.net/apc">APC extension</a> installed.</li><li>XCache - Pass in <code>xcache</code> to use this lightweight cache. You must have the <a href="http://xcache.lighttpd.net">XCache</a> extension installed.</li><li>Memcached - Pass in an indexed array of associative arrays. Each associative array should have a <code>host</code> and a <code>port</code> value representing a <a href="http://php.net/memcached">Memcached</a> server to connect to.</li><li>PDO - A URL-style string (e.g. <code>pdo.mysql://user:pass@localhost/cache</code>) or a standard DSN-style string (e.g. <code>pdo.sqlite:/sqlite/cache.db</code>). MUST be prefixed with <code>pdo.</code>. See <code>CachePDO</code> and <a href="http://php.net/pdo">PDO</a> for more details.</li></ul>
	 * @param boolean $gzip (Optional) Whether or not data should be gzipped before being stored. A value of `true` will compress the contents before caching them. A value of `false` will leave the contents uncompressed. Defaults to `true`.
	 * @return $this A reference to the current instance.
	 */
	public function set_cache_config($location, $gzip = true)
	{
		// If we have an array, we're probably passing in Memcached servers and ports.
		if (is_array($location))
		{
			$this->cache_class = 'CacheMC';
		}
		else
		{
			// I would expect locations like `/tmp/cache`, `pdo.mysql://user:pass@hostname:port`, `pdo.sqlite:memory:`, and `apc`.
			$type = strtolower(substr($location, 0, 3));
			switch ($type)
			{
				case 'apc':
					$this->cache_class = 'CacheAPC';
					break;

				case 'xca': // First three letters of `xcache`
					$this->cache_class = 'CacheXCache';
					break;

				case 'pdo':
					$this->cache_class = 'CachePDO';
					$location = substr($location, 4);
					break;

				default:
					$this->cache_class = 'CacheFile';
					break;
			}
		}

		// Set the remaining cache information.
		$this->cache_location = $location;
		$this->cache_compress = $gzip;

		return $this;
	}

	/**
	 * Register a callback function to execute whenever a data stream is read from using
	 * <CFRequest::streaming_read_callback()>.
	 *
	 * The user-defined callback function should accept three arguments:
	 *
	 * <ul>
	 * 	<li><code>$curl_handle</code> - <code>resource</code> - Required - The cURL handle resource that represents the in-progress transfer.</li>
	 * 	<li><code>$file_handle</code> - <code>resource</code> - Required - The file handle resource that represents the file on the local file system.</li>
	 * 	<li><code>$length</code> - <code>integer</code> - Required - The length in kilobytes of the data chunk that was transferred.</li>
	 * </ul>
	 *
	 * @param string|array|function $callback (Required) The callback function is called by <php:call_user_func()>, so you can pass the following values: <ul>
	 * 	<li>The name of a global function to execute, passed as a string.</li>
	 * 	<li>A method to execute, passed as <code>array('ClassName', 'MethodName')</code>.</li>
	 * 	<li>An anonymous function (PHP 5.3+).</li></ul>
	 * @return $this A reference to the current instance.
	 */
	public function register_streaming_read_callback($callback)
	{
		$this->registered_streaming_read_callback = $callback;
		return $this;
	}

	/**
	 * Register a callback function to execute whenever a data stream is written to using
	 * <CFRequest::streaming_write_callback()>.
	 *
	 * The user-defined callback function should accept two arguments:
	 *
	 * <ul>
	 * 	<li><code>$curl_handle</code> - <code>resource</code> - Required - The cURL handle resource that represents the in-progress transfer.</li>
	 * 	<li><code>$length</code> - <code>integer</code> - Required - The length in kilobytes of the data chunk that was transferred.</li>
	 * </ul>
	 *
	 * @param string|array|function $callback (Required) The callback function is called by <php:call_user_func()>, so you can pass the following values: <ul>
	 * 	<li>The name of a global function to execute, passed as a string.</li>
	 * 	<li>A method to execute, passed as <code>array('ClassName', 'MethodName')</code>.</li>
	 * 	<li>An anonymous function (PHP 5.3+).</li></ul>
	 * @return $this A reference to the current instance.
	 */
	public function register_streaming_write_callback($callback)
	{
		$this->registered_streaming_write_callback = $callback;
		return $this;
	}


	/*%******************************************************************************************%*/
	// SET CUSTOM CLASSES

	/**
	 * Set a custom class for this functionality. Use this method when extending/overriding existing classes
	 * with new functionality.
	 *
	 * The replacement class must extend from <CFUtilities>.
	 *
	 * @param string $class (Optional) The name of the new class to use for this functionality.
	 * @return $this A reference to the current instance.
	 */
	public function set_utilities_class($class = 'CFUtilities')
	{
		$this->utilities_class = $class;
		$this->util = new $this->utilities_class();
		return $this;
	}

	/**
	 * Set a custom class for this functionality. Use this method when extending/overriding existing classes
	 * with new functionality.
	 *
	 * The replacement class must extend from <CFRequest>.
	 *
	 * @param string $class (Optional) The name of the new class to use for this functionality.
	 * @param $this A reference to the current instance.
	 */
	public function set_request_class($class = 'CFRequest')
	{
		$this->request_class = $class;
		return $this;
	}

	/**
	 * Set a custom class for this functionality. Use this method when extending/overriding existing classes
	 * with new functionality.
	 *
	 * The replacement class must extend from <CFResponse>.
	 *
	 * @param string $class (Optional) The name of the new class to use for this functionality.
	 * @return $this A reference to the current instance.
	 */
	public function set_response_class($class = 'CFResponse')
	{
		$this->response_class = $class;
		return $this;
	}

	/**
	 * Set a custom class for this functionality. Use this method when extending/overriding existing classes
	 * with new functionality.
	 *
	 * The replacement class must extend from <CFSimpleXML>.
	 *
	 * @param string $class (Optional) The name of the new class to use for this functionality.
	 * @return $this A reference to the current instance.
	 */
	public function set_parser_class($class = 'CFSimpleXML')
	{
		$this->parser_class = $class;
		return $this;
	}

	/**
	 * Set a custom class for this functionality. Use this method when extending/overriding existing classes
	 * with new functionality.
	 *
	 * The replacement class must extend from <CFBatchRequest>.
	 *
	 * @param string $class (Optional) The name of the new class to use for this functionality.
	 * @return $this A reference to the current instance.
	 */
	public function set_batch_class($class = 'CFBatchRequest')
	{
		$this->batch_class = $class;
		return $this;
	}


	/*%******************************************************************************************%*/
	// AUTHENTICATION

	/**
	 * Default, shared method for authenticating a connection to AWS. Overridden on a class-by-class basis
	 * as necessary.
	 *
	 * @param string $action (Required) Indicates the action to perform.
	 * @param array $opt (Optional) An associative array of parameters for authenticating. See the individual methods for allowed keys.
	 * @param string $domain (Optional) The URL of the queue to perform the action on.
	 * @param integer $signature_version (Optional) The signature version to use. Defaults to 2.
	 * @param integer $redirects (Do Not Use) Used internally by this function on occasions when Amazon S3 returns a redirect code and it needs to call itself recursively.
	 * @return CFResponse Object containing a parsed HTTP response.
	 */
	public function authenticate($action, $opt = null, $domain = null, $signature_version = 2, $redirects = 0)
	{
		// Handle nulls
		if (is_null($signature_version))
		{
			$signature_version = 2;
		}

		$method_arguments = func_get_args();
		$headers = array();
		$signed_headers = array();

		// Use the caching flow to determine if we need to do a round-trip to the server.
		if ($this->use_cache_flow)
		{
			// Generate an identifier specific to this particular set of arguments.
			$cache_id = $this->key . '_' . get_class($this) . '_' . $action . '_' . sha1(serialize($method_arguments));

			// Instantiate the appropriate caching object.
			$this->cache_object = new $this->cache_class($cache_id, $this->cache_location, $this->cache_expires, $this->cache_compress);

			if ($this->delete_cache)
			{
				$this->use_cache_flow = false;
				$this->delete_cache = false;
				return $this->cache_object->delete();
			}

			// Invoke the cache callback function to determine whether to pull data from the cache or make a fresh request.
			$data = $this->cache_object->response_manager(array($this, 'cache_callback'), $method_arguments);

			// Parse the XML body
			$data = $this->parse_callback($data);

			// End!
			return $data;
		}

		$return_curl_handle = false;
		$x_amz_target = null;

		// Do we have a custom resource prefix?
		if ($this->resource_prefix)
		{
			$domain .= $this->resource_prefix;
		}

		// Determine signing values
		$current_time = time() + $this->adjust_offset;
		$date = gmdate(CFUtilities::DATE_FORMAT_RFC2616, $current_time);
		$timestamp = gmdate(CFUtilities::DATE_FORMAT_ISO8601, $current_time);
		$nonce = $this->util->generate_guid();

		// Do we have an authentication token?
		if ($this->auth_token)
		{
			$headers['X-Amz-Security-Token'] = $this->auth_token;
			$query['SecurityToken'] = $this->auth_token;
		}

		// Manage the key-value pairs that are used in the query.
		if (stripos($action, 'x-amz-target') !== false)
		{
			$x_amz_target = trim(str_ireplace('x-amz-target:', '', $action));
		}
		else
		{
			$query['Action'] = $action;
		}

		// Only add it if it exists.
		if ($this->api_version)
		{
			$query['Version'] = $this->api_version;
		}

		// Only Signature v2
		if ($signature_version === 2)
		{
			$query['AWSAccessKeyId'] = $this->key;
			$query['SignatureMethod'] = 'HmacSHA256';
			$query['SignatureVersion'] = 2;
			$query['Timestamp'] = $timestamp;
		}

		$curlopts = array();

		// Set custom CURLOPT settings
		if (is_array($opt) && isset($opt['curlopts']))
		{
			$curlopts = $opt['curlopts'];
			unset($opt['curlopts']);
		}

		// Merge in any options that were passed in
		if (is_array($opt))
		{
			$query = array_merge($query, $opt);
		}

		$return_curl_handle = isset($query['returnCurlHandle']) ? $query['returnCurlHandle'] : false;
		unset($query['returnCurlHandle']);

		// Do a case-sensitive, natural order sort on the array keys.
		uksort($query, 'strcmp');

		// Normalize JSON input
		if (isset($query['body']) && $query['body'] === '[]')
		{
			$query['body'] = '{}';
		}

		if ($this->use_aws_query)
		{
			// Create the string that needs to be hashed.
			$canonical_query_string = $this->util->to_signable_string($query);
		}
		else
		{
			// Create the string that needs to be hashed.
			$canonical_query_string = $this->util->encode_signature2($query['body']);
		}

		// Remove the default scheme from the domain.
		$domain = str_replace(array('http://', 'https://'), '', $domain);

		// Parse our request.
		$parsed_url = parse_url('http://' . $domain);

		// Set the proper host header.
		if (isset($parsed_url['port']) && (integer) $parsed_url['port'] !== 80 && (integer) $parsed_url['port'] !== 443)
		{
			$host_header = strtolower($parsed_url['host']) . ':' . $parsed_url['port'];
		}
		else
		{
			$host_header = strtolower($parsed_url['host']);
		}

		// Set the proper request URI.
		$request_uri = isset($parsed_url['path']) ? $parsed_url['path'] : '/';

		if ($signature_version === 2)
		{
			// Prepare the string to sign
			$string_to_sign = "POST\n$host_header\n$request_uri\n$canonical_query_string";

			// Hash the AWS secret key and generate a signature for the request.
			$query['Signature'] = base64_encode(hash_hmac('sha256', $string_to_sign, $this->secret_key, true));
		}

		// Generate the querystring from $query
		$querystring = $this->util->to_query_string($query);

		// Gather information to pass along to other classes.
		$helpers = array(
			'utilities' => $this->utilities_class,
			'request' => $this->request_class,
			'response' => $this->response_class,
		);

		// Compose the request.
		$request_url = ($this->use_ssl ? 'https://' : 'http://') . $domain;
		$request_url .= !isset($parsed_url['path']) ? '/' : '';

		// Instantiate the request class
		$request = new $this->request_class($request_url, $this->proxy, $helpers);
		$request->set_method('POST');
		$request->set_body($querystring);
		$headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';

		// Signing using X-Amz-Target is handled differently.
		if ($signature_version === 3 && $x_amz_target)
		{
			$headers['X-Amz-Target'] = $x_amz_target;
			$headers['Content-Type'] = 'application/json; amzn-1.0';
			$headers['Content-Encoding'] = 'amz-1.0';

			$request->set_body($query['body']);
			$querystring = $query['body'];
		}

		// Pass along registered stream callbacks
		if ($this->registered_streaming_read_callback)
		{
			$request->register_streaming_read_callback($this->registered_streaming_read_callback);
		}

		if ($this->registered_streaming_write_callback)
		{
			$request->register_streaming_write_callback($this->registered_streaming_write_callback);
		}

		// Add authentication headers
		if ($signature_version === 3)
		{
			$headers['X-Amz-Nonce'] = $nonce;
			$headers['Date'] = $date;
			$headers['Content-Length'] = strlen($querystring);
			$headers['Content-MD5'] = $this->util->hex_to_base64(md5($querystring));
			$headers['Host'] = $host_header;
		}

		// Sort headers
		uksort($headers, 'strnatcasecmp');

		if ($signature_version === 3 && $this->use_ssl)
		{
			// Prepare the string to sign (HTTPS)
			$string_to_sign = $date . $nonce;
		}
		elseif ($signature_version === 3 && !$this->use_ssl)
		{
			// Prepare the string to sign (HTTP)
			$string_to_sign = "POST\n$request_uri\n\n";
		}

		// Add headers to request and compute the string to sign
		foreach ($headers as $header_key => $header_value)
		{
			// Strip linebreaks from header values as they're illegal and can allow for security issues
			$header_value = str_replace(array("\r", "\n"), '', $header_value);

			// Add the header if it has a value
			if ($header_value !== '')
			{
				$request->add_header($header_key, $header_value);
			}

			// Signature v3 over HTTP
			if ($signature_version === 3 && !$this->use_ssl)
			{
				// Generate the string to sign
				if (
					substr(strtolower($header_key), 0, 8) === 'content-' ||
					strtolower($header_key) === 'date' ||
					strtolower($header_key) === 'expires' ||
					strtolower($header_key) === 'host' ||
					substr(strtolower($header_key), 0, 6) === 'x-amz-'
				)
				{
					$string_to_sign .= strtolower($header_key) . ':' . $header_value . "\n";
					$signed_headers[] = $header_key;
				}
			}
		}

		if ($signature_version === 3)
		{
			if (!$this->use_ssl)
			{
				$string_to_sign .= "\n";

				if (isset($query['body']) && $query['body'] !== '')
				{
					$string_to_sign .= $query['body'];
				}

				// Convert from string-to-sign to bytes-to-sign
				$bytes_to_sign = hash('sha256', $string_to_sign, true);

				// Hash the AWS secret key and generate a signature for the request.
				$signature = base64_encode(hash_hmac('sha256', $bytes_to_sign, $this->secret_key, true));
			}
			else
			{
				// Hash the AWS secret key and generate a signature for the request.
				$signature = base64_encode(hash_hmac('sha256', $string_to_sign, $this->secret_key, true));
			}

			$headers['X-Amzn-Authorization'] = 'AWS3' . ($this->use_ssl ? '-HTTPS' : '')
				. ' AWSAccessKeyId=' . $this->key
				. ',Algorithm=HmacSHA256'
				. ',SignedHeaders=' . implode(';', $signed_headers)
				. ',Signature=' . $signature;

			$request->add_header('X-Amzn-Authorization', $headers['X-Amzn-Authorization']);
		}

		// Update RequestCore settings
		$request->request_class = $this->request_class;
		$request->response_class = $this->response_class;
		$request->ssl_verification = $this->ssl_verification;

		// Debug mode
		if ($this->debug_mode)
		{
			$request->debug_mode = $this->debug_mode;
		}

		if (count($curlopts))
		{
			$request->set_curlopts($curlopts);
		}

		// Manage the (newer) batch request API or the (older) returnCurlHandle setting.
		if ($this->use_batch_flow)
		{
			$handle = $request->prep_request();
			$this->batch_object->add($handle);
			$this->use_batch_flow = false;

			return $handle;
		}
		elseif ($return_curl_handle)
		{
			return $request->prep_request();
		}

		// Send!
		$request->send_request();

		$request_headers = $headers;

		// Prepare the response.
		$headers = $request->get_response_header();
		$headers['x-aws-stringtosign'] = $string_to_sign;
		$headers['x-aws-request-headers'] = $request_headers;
		$headers['x-aws-body'] = $querystring;

		$data = new $this->response_class($headers, $this->parse_callback($request->get_response_body(), $headers), $request->get_response_code());

		// Was it Amazon's fault the request failed? Retry the request until we reach $max_retries.
		if ((integer) $request->get_response_code() === 500 || (integer) $request->get_response_code() === 503)
		{
			if ($redirects <= $this->max_retries)
			{
				// Exponential backoff
				$delay = (integer) (pow(4, $redirects) * 100000);
				usleep($delay);
				$data = $this->authenticate($action, $opt, $domain, $signature_version, ++$redirects);
			}
		}

		return $data;
	}


	/*%******************************************************************************************%*/
	// BATCH REQUEST LAYER

	/**
	 * Specifies that the intended request should be queued for a later batch request.
	 *
	 * @param CFBatchRequest $queue (Optional) The <CFBatchRequest> instance to use for managing batch requests. If not available, it generates a new instance of <CFBatchRequest>.
	 * @return $this A reference to the current instance.
	 */
	public function batch(CFBatchRequest &$queue = null)
	{
		if ($queue)
		{
			$this->batch_object = $queue;
		}
		elseif ($this->internal_batch_object)
		{
			$this->batch_object = &$this->internal_batch_object;
		}
		else
		{
			$this->internal_batch_object = new $this->batch_class();
			$this->batch_object = &$this->internal_batch_object;
		}

		$this->use_batch_flow = true;

		return $this;
	}

	/**
	 * Executes the batch request queue by sending all queued requests.
	 *
	 * @param boolean $clear_after_send (Optional) Whether or not to clear the batch queue after sending a request. Defaults to `true`. Set this to `false` if you are caching batch responses and want to retrieve results later.
	 * @return array An array of <CFResponse> objects.
	 */
	public function send($clear_after_send = true)
	{
		if ($this->use_batch_flow)
		{
			// When we send the request, disable batch flow.
			$this->use_batch_flow = false;

			// If we're not caching, simply send the request.
			if (!$this->use_cache_flow)
			{
				$response = $this->batch_object->send();
				$parsed_data = array_map(array($this, 'parse_callback'), $response);
				$parsed_data = new CFArray($parsed_data);

				// Clear the queue
				if ($clear_after_send)
				{
					$this->batch_object->queue = array();
				}

				return $parsed_data;
			}

			// Generate an identifier specific to this particular set of arguments.
			$cache_id = $this->key . '_' . get_class($this) . '_' . sha1(serialize($this->batch_object));

			// Instantiate the appropriate caching object.
			$this->cache_object = new $this->cache_class($cache_id, $this->cache_location, $this->cache_expires, $this->cache_compress);

			if ($this->delete_cache)
			{
				$this->use_cache_flow = false;
				$this->delete_cache = false;
				return $this->cache_object->delete();
			}

			// Invoke the cache callback function to determine whether to pull data from the cache or make a fresh request.
			$data_set = $this->cache_object->response_manager(array($this, 'cache_callback_batch'), array($this->batch_object));
			$parsed_data = array_map(array($this, 'parse_callback'), $data_set);
			$parsed_data = new CFArray($parsed_data);

			// Clear the queue
			if ($clear_after_send)
			{
				$this->batch_object->queue = array();
			}

			// End!
			return $parsed_data;
		}

		// Load the class
		$null = new CFBatchRequest();
		unset($null);

		throw new CFBatchRequest_Exception('You must use $object->batch()->send()');
	}

	/**
	 * Parses a response body into a PHP object if appropriate.
	 *
	 * @param CFResponse|string $response (Required) The <CFResponse> object to parse, or an XML string that would otherwise be a response body.
	 * @param string $content_type (Optional) The content-type to use when determining how to parse the content.
	 * @return CFResponse|string A parsed <CFResponse> object, or parsed XML.
	 */
	public function parse_callback($response, $headers = null)
	{
		// Shorten this so we have a (mostly) single code path
		if (isset($response->body))
		{
			if (is_string($response->body))
			{
				$body = $response->body;
			}
			else
			{
				return $response;
			}
		}
		elseif (is_string($response))
		{
			$body = $response;
		}
		else
		{
			return $response;
		}

		// Decompress gzipped content
		if (isset($headers['content-encoding']))
		{
			switch (strtolower(trim($headers['content-encoding'], "\x09\x0A\x0D\x20")))
			{
				case 'gzip':
				case 'x-gzip':
					if (strpos($headers['_info']['url'], 'monitoring.') !== false)
					{
						// CloudWatch incorrectly uses the deflate algorithm when they say gzip.
						if (($uncompressed = gzuncompress($body)) !== false)
						{
							$body = $uncompressed;
						}
						elseif (($uncompressed = gzinflate($body)) !== false)
						{
							$body = $uncompressed;
						}
						break;
					}
					else
					{
						// Everyone else uses gzip correctly.
						$decoder = new CFGzipDecode($body);
						if ($decoder->parse())
						{
							$body = $decoder->data;
						}
						break;
					}

				case 'deflate':
					if (strpos($headers['_info']['url'], 'monitoring.') !== false)
					{
						// CloudWatchWatch incorrectly does nothing when they say deflate.
						continue;
					}
					else
					{
						// Everyone else uses deflate correctly.
						if (($uncompressed = gzuncompress($body)) !== false)
						{
							$body = $uncompressed;
						}
						elseif (($uncompressed = gzinflate($body)) !== false)
						{
							$body = $uncompressed;
						}
					}
					break;
			}
		}

		// Look for XML cues
		if (
			(isset($headers['content-type']) && ($headers['content-type'] === 'text/xml' || $headers['content-type'] === 'application/xml')) || // We know it's XML
			(!isset($headers['content-type']) && (stripos($body, '<?xml') === 0 || strpos($body, '<Error>') === 0) || preg_match('/^<(\w*) xmlns="http(s?):\/\/(\w*).amazon(aws)?.com/im', $body)) // Sniff for XML
		)
		{
			// Strip the default XML namespace to simplify XPath expressions
			$body = str_replace("xmlns=", "ns=", $body);

			// Parse the XML body
			$body = new $this->parser_class($body);
		}
		// Look for JSON cues
		elseif (
			(isset($headers['content-type']) && $headers['content-type'] === 'application/json') || // We know it's JSON
			(!isset($headers['content-type']) && $this->util->is_json($body)) // Sniff for JSON
		)
		{
			// Normalize JSON to a CFSimpleXML object
			$body = CFJSON::to_xml($body);
		}

		// Put the parsed data back where it goes
		if (isset($response->body))
		{
			$response->body = $body;
		}
		else
		{
			$response = $body;
		}

		return $response;
	}


	/*%******************************************************************************************%*/
	// CACHING LAYER

	/**
	 * Specifies that the resulting <CFResponse> object should be cached according to the settings from
	 * <set_cache_config()>.
	 *
	 * @param string|integer $expires (Required) The time the cache is to expire. Accepts a number of seconds as an integer, or an amount of time, as a string, that is understood by <php:strtotime()> (e.g. "1 hour").
	 * @param $this A reference to the current instance.
	 * @return $this
	 */
	public function cache($expires)
	{
		// Die if they haven't used set_cache_config().
		if (!$this->cache_class)
		{
			throw new CFRuntime_Exception('Must call set_cache_config() before using cache()');
		}

		if (is_string($expires))
		{
			$expires = strtotime($expires);
			$this->cache_expires = $expires - time();
		}
		elseif (is_int($expires))
		{
			$this->cache_expires = $expires;
		}

		$this->use_cache_flow = true;

		return $this;
	}

	/**
	 * The callback function that is executed when the cache doesn't exist or has expired. The response of
	 * this method is cached. Accepts identical parameters as the <authenticate()> method. Never call this
	 * method directly -- it is used internally by the caching system.
	 *
	 * @param string $action (Required) Indicates the action to perform.
	 * @param array $opt (Optional) An associative array of parameters for authenticating. See the individual methods for allowed keys.
	 * @param string $domain (Optional) The URL of the queue to perform the action on.
	 * @param integer $signature_version (Optional) The signature version to use. Defaults to 2.
	 * @return CFResponse A parsed HTTP response.
	 */
	public function cache_callback($action, $opt = null, $domain = null, $signature_version = 2)
	{
		// Disable the cache flow since it's already been handled.
		$this->use_cache_flow = false;

		// Make the request
		$response = $this->authenticate($action, $opt, $domain, $signature_version);

		// If this is an XML document, convert it back to a string.
		if (isset($response->body) && ($response->body instanceof SimpleXMLElement))
		{
			$response->body = $response->body->asXML();
		}

		return $response;
	}

	/**
	 * Used for caching the results of a batch request. Never call this method directly; it is used
	 * internally by the caching system.
	 *
	 * @param CFBatchRequest $batch (Required) The batch request object to send.
	 * @return CFResponse A parsed HTTP response.
	 */
	public function cache_callback_batch(CFBatchRequest $batch)
	{
		return $batch->send();
	}

	/**
	 * Deletes a cached <CFResponse> object using the specified cache storage type.
	 *
	 * @return boolean A value of `true` if cached object exists and is successfully deleted, otherwise `false`.
	 */
	public function delete_cache()
	{
		$this->use_cache_flow = true;
		$this->delete_cache = true;

		return $this;
	}
}


/**
 * Contains the functionality for auto-loading service classes.
 */
class CFLoader
{

	/*%******************************************************************************************%*/
	// AUTO-LOADER

	/**
	 * Automatically load classes that aren't included.
	 *
	 * @param string $class (Required) The classname to load.
	 * @return void
	 */
	public static function autoloader($class)
	{
		$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;

		// Amazon SDK classes
		if (strstr($class, 'Amazon'))
		{
			$path .= 'services' . DIRECTORY_SEPARATOR . str_ireplace('Amazon', '', strtolower($class)) . '.class.php';
		}

		// Utility classes
		elseif (strstr($class, 'CF'))
		{
			$path .= 'utilities' . DIRECTORY_SEPARATOR . str_ireplace('CF', '', strtolower($class)) . '.class.php';
		}

		// Load CacheCore
		elseif (strstr($class, 'Cache'))
		{
			if (file_exists($ipath = 'lib' . DIRECTORY_SEPARATOR . 'cachecore' . DIRECTORY_SEPARATOR . 'icachecore.interface.php'))
			{
				require_once($ipath);
			}

			$path .= 'lib' . DIRECTORY_SEPARATOR . 'cachecore' . DIRECTORY_SEPARATOR . strtolower($class) . '.class.php';
		}

		// Load RequestCore
		elseif (strstr($class, 'RequestCore') || strstr($class, 'ResponseCore'))
		{
			$path .= 'lib' . DIRECTORY_SEPARATOR . 'requestcore' . DIRECTORY_SEPARATOR . 'requestcore.class.php';
		}

		// Load Symfony YAML classes
		elseif (strstr($class, 'sfYaml'))
		{
			$path .= 'lib' . DIRECTORY_SEPARATOR . 'yaml' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'sfYaml.php';
		}

		// Fall back to the 'extensions' directory.
		elseif (defined('AWS_ENABLE_EXTENSIONS') && AWS_ENABLE_EXTENSIONS)
		{
			$path .= 'extensions' . DIRECTORY_SEPARATOR . strtolower($class) . '.class.php';
		}

		if (file_exists($path) && !is_dir($path))
		{
			require_once($path);
		}
	}
}

// Register the autoloader.
spl_autoload_register(array('CFLoader', 'autoloader'));
