<?php

define('WFWAF_VERSION', '1.0.3');
define('WFWAF_PATH', dirname(__FILE__) . '/');
define('WFWAF_LIB_PATH', WFWAF_PATH . 'lib/');
define('WFWAF_VIEW_PATH', WFWAF_PATH . 'views/');
define('WFWAF_API_URL_SEC', 'https://noc4.wordfence.com/v1.8/');
if (!defined('WFWAF_DEBUG')) {
	define('WFWAF_DEBUG', false);
}
if (!defined('WFWAF_ENABLED')) {
	define('WFWAF_ENABLED', true);
}

define('WFWAF_IS_WINDOWS', strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

require_once WFWAF_LIB_PATH . 'waf.php';
require_once WFWAF_LIB_PATH . 'utils.php';

require_once WFWAF_LIB_PATH . 'storage.php';
require_once WFWAF_LIB_PATH . 'storage/file.php';

require_once WFWAF_LIB_PATH . 'config.php';

require_once WFWAF_LIB_PATH . 'rules.php';
require_once WFWAF_LIB_PATH . 'parser/lexer.php';
require_once WFWAF_LIB_PATH . 'parser/parser.php';
require_once WFWAF_LIB_PATH . 'parser/sqli.php';

require_once WFWAF_LIB_PATH . 'request.php';
require_once WFWAF_LIB_PATH . 'http.php';
require_once WFWAF_LIB_PATH . 'view.php';
