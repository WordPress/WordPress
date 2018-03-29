<?php

class wfImportExportController {
	/**
	 * Returns the singleton wfImportExportController.
	 *
	 * @return wfImportExportController
	 */
	public static function shared() {
		static $_shared = null;
		if ($_shared === null) {
			$_shared = new wfImportExportController();
		}
		return $_shared;
	}
	
	public function export() {
		$export = array();
		
		//Basic Options
		$keys = wfConfig::getExportableOptionsKeys();
		foreach ($keys as $key) {
			$export[$key] = wfConfig::get($key, '');
		}
		
		//Serialized Options
		$export['scanSched'] = wfConfig::get_ser('scanSched', array());
		
		//Table-based Options
		$export['blocks'] = wfBlock::exportBlocks();
		
		//Make the API call
		try {
			$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
			$res = $api->call('export_options', array(), array('export' => json_encode($export)));
			if ($res['ok'] && $res['token']) {
				return array(
					'ok' => 1,
					'token' => $res['token'],
				);
			}
			else if ($res['err']) {
				return array('err' => __("An error occurred: ", 'wordfence') . $res['err']);
			}
			else {
				throw new Exception(__("Invalid response: ", 'wordfence') . var_export($res, true));
			}
		}
		catch (Exception $e) {
			return array('err' => __("An error occurred: ", 'wordfence') . $e->getMessage());
		}
	}
	
	public function import($token) {
		try {
			$api = new wfAPI(wfConfig::get('apiKey'), wfUtils::getWPVersion());
			$res = $api->call('import_options', array(), array('token' => $token));
			if ($res['ok'] && $res['export']) {
				$totalSet = 0;
				$import = @json_decode($res['export'], true);
				if (!is_array($import)) {
					return array('err' => __("An error occurred: Invalid options format received.", 'wordfence'));
				}
				
				//Basic Options
				$keys = wfConfig::getExportableOptionsKeys();
				$toSet = array();
				foreach ($keys as $key) {
					if (isset($import[$key])) {
						$toSet[$key] = $import[$key];
					}
				}
				
				if (count($toSet)) {
					$validation = wfConfig::validate($toSet);
					$skipped = array();
					if ($validation !== true) {
						foreach ($validation as $error) {
							$skipped[$error['option']] = $error['error'];
							unset($toSet[$error['option']]);
						}
					}
					
					$totalSet += count($toSet);
					wfConfig::save($toSet);
				}
				
				//Serialized Options
				if (isset($import['scanSched']) && is_array($import['scanSched'])) {
					wfConfig::set_ser('scanSched', $import['scanSched']);
					wfScanner::shared()->scheduleScans();
					$totalSet++;
				}
				
				//Table-based Options
				if (isset($import['blocks']) && is_array($import['blocks'])) {
					wfBlock::importBlocks($import['blocks']);
					$totalSet += count($import['blocks']);
				}
				
				return array(
					'ok' => 1,
					'totalSet' => $totalSet,
				);
			}
			else if ($res['err']) {
				return array('err' => "An error occurred: " . $res['err']);
			}
			else {
				throw new Exception("Invalid response: " . var_export($res, true));
			}
		}
		catch (Exception $e) {
			return array('err' => "An error occurred: " . $e->getMessage());
		}
	}
}
