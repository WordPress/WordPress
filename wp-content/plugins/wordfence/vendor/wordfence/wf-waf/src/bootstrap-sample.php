<?php

/*
	php_value auto_prepend_file ~/wp-content/plugins/wordfence/waf/bootstrap.php
*/

require_once dirname(__FILE__) . '/init.php';
if (!defined('WFWAF_LOG_PATH')) {
	define('WFWAF_LOG_PATH', WFWAF_PATH . 'logs/');
}

wfWAF::setInstance(new wfWAF(
	wfWAFRequest::createFromGlobals(),
	new wfWAFStorageFile(
		WFWAF_LOG_PATH . 'attack-data.php',
		WFWAF_LOG_PATH . 'ips.php',
		WFWAF_LOG_PATH . 'config.php',
		WFWAF_LOG_PATH . 'wafRules.rules'
	)
));
wfWAF::getInstance()->getEventBus()->attach(new wfWAFBaseObserver);

$rulesFiles = array(
	WFWAF_PATH . 'rules.php',
	WFWAF_LOG_PATH . 'rules.php',
);
foreach ($rulesFiles as $rulesFile) {
	if (!file_exists($rulesFile)) {
		@touch($rulesFile);
	}
	if (is_writable($rulesFile)) {
		wfWAF::getInstance()->setCompiledRulesFile($rulesFile);
		break;
	}
}

try {
	if (!file_exists(wfWAF::getInstance()->getCompiledRulesFile()) || !filesize(wfWAF::getInstance()->getCompiledRulesFile())) {
		try {
			wfWAF::getInstance()->updateRuleSet(file_get_contents(WFWAF_PATH . 'baseRules.rules'));
		} catch (wfWAFBuildRulesException $e) {
			error_log($e->getMessage());
		} catch (Exception $e) {
			error_log($e->getMessage());
		}
	}

	try {
		wfWAF::getInstance()->run();
	} catch (wfWAFBuildRulesException $e) {
		error_log($e->getMessage());
	} catch (Exception $e) {
		error_log($e->getMessage());
	}
} catch (wfWAFStorageFileException $e) {
	// Choose another storage engine here.
}
