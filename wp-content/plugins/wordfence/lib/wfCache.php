<?php
class wfCache {
	private static $cacheStats = array();
	private static $cacheClearedThisRequest = false;
	private static $lastRecursiveDeleteError = false;
	
	public static function removeCaching() {
		$cacheType = wfConfig::get('cacheType', false);
		if ($cacheType === 'disabled') {
			return;
		}
		
		if ($cacheType == 'falcon') {
			self::addHtaccessCode('remove');
			self::updateBlockedIPs('remove');
		}
		
		wfConfig::set('cacheType', 'disabled');
		
		$cacheDir = WP_CONTENT_DIR . '/wfcache/';
		if (file_exists($cacheDir . '.htaccess')) {
			unlink($cacheDir . '.htaccess');
		}
		
		self::clearPageCacheSafe();
	}
	public static function clearPageCacheSafe(){
		if(self::$cacheClearedThisRequest){ return; }
		self::$cacheClearedThisRequest = true;
		self::clearPageCache();
	}
	public static function clearPageCache(){ //If a clear is in progress this does nothing. 
		self::$cacheStats = array(
			'dirsDeleted' => 0,
			'filesDeleted' => 0,
			'totalData' => 0,
			'totalErrors' => 0,
			'error' => '',
			);
		
		$cacheDir = WP_CONTENT_DIR . '/wfcache/';
		if (!file_exists($cacheDir)) {
			return self::$cacheStats;
		}
		
		$cacheClearLock = WP_CONTENT_DIR . '/wfcache/clear.lock';
		if(! is_file($cacheClearLock)){
			if(! touch($cacheClearLock)){
				self::$cacheStats['error'] = "Could not create a lock file $cacheClearLock to clear the cache.";
				self::$cacheStats['totalErrors']++;
				return self::$cacheStats;
			}
		}
		$fp = fopen($cacheClearLock, 'w');
		if(! $fp){ 
			self::$cacheStats['error'] = "Could not open the lock file $cacheClearLock to clear the cache. Please make sure the directory is writable by your web server.";
			self::$cacheStats['totalErrors']++;
			return self::$cacheStats;
		}
		if(flock($fp, LOCK_EX | LOCK_NB)){ //non blocking exclusive flock attempt. If we get a lock then it continues and returns true. If we don't lock, then return false, don't block and don't clear the cache. 
					// This logic means that if a cache clear is currently in progress we don't try to clear the cache.
					// This prevents web server children from being queued up waiting to be able to also clear the cache. 
			self::$lastRecursiveDeleteError = false;
			self::recursiveDelete(WP_CONTENT_DIR . '/wfcache/');
			if(self::$lastRecursiveDeleteError){
				self::$cacheStats['error'] = self::$lastRecursiveDeleteError;
				self::$cacheStats['totalErrors']++;
			}
			flock($fp, LOCK_UN);
			@unlink($cacheClearLock);
			@rmdir($cacheDir);
		}
		fclose($fp);

		return self::$cacheStats;
	}
	private static function recursiveDelete($dir) {
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) { 
			if(is_dir($dir . '/' . $file)){
				if(! self::recursiveDelete($dir . '/' . $file)){
					return false;
				}
			} else {
				if($file == 'clear.lock'){ continue; } //Don't delete our lock file
				$size = filesize($dir . '/' . $file);
				if($size){
					self::$cacheStats['totalData'] += round($size / 1024);
				}
				if(strpos($dir, 'wfcache/') === false){
					self::$lastRecursiveDeleteError = "Not deleting file in directory $dir because it appears to be in the wrong path.";
					self::$cacheStats['totalErrors']++;
					return false; //Safety check that we're in a subdir of the cache
				}
				if(@unlink($dir . '/' . $file)){
					self::$cacheStats['filesDeleted']++;
				} else {
					self::$lastRecursiveDeleteError = "Could not delete file " . $dir . "/" . $file . " : " . wfUtils::getLastError();
					self::$cacheStats['totalErrors']++;
					return false;
				}
			}
		} 
		if($dir != WP_CONTENT_DIR . '/wfcache/'){
			if(strpos($dir, 'wfcache/') === false){
				self::$lastRecursiveDeleteError = "Not deleting directory $dir because it appears to be in the wrong path.";
				self::$cacheStats['totalErrors']++;
				return false; //Safety check that we're in a subdir of the cache
			}
			if(@rmdir($dir)){
				self::$cacheStats['dirsDeleted']++;
			} else {
				self::$lastRecursiveDeleteError = "Could not delete directory $dir : " . wfUtils::getLastError();
				self::$cacheStats['totalErrors']++;
				return false;
			}
			return true;
		} else {
			return true;
		}
	}
	public static function addHtaccessCode($action){
		if($action != 'remove'){
			die("Error: addHtaccessCode must be called with 'remove' as param");
		}
		$htaccessPath = self::getHtaccessPath();
		if(! $htaccessPath){
			return "Wordfence could not find your .htaccess file.";
		}
		$fh = @fopen($htaccessPath, 'r+');
		if(! $fh){
			$err = error_get_last();
			return $err['message'];
		}
		flock($fh, LOCK_EX);
		fseek($fh, 0, SEEK_SET); //start of file
		clearstatcache();
		$contents = fread($fh, filesize($htaccessPath));
		if(! $contents){
			fclose($fh);
			return "Could not read from $htaccessPath";
		}
		$contents = preg_replace('/#WFCACHECODE.*WFCACHECODE[\r\s\n\t]*/s', '', $contents);
		ftruncate($fh, 0);
		fflush($fh);
		fseek($fh, 0, SEEK_SET);
		fwrite($fh, $contents);
		flock($fh, LOCK_UN);
		fclose($fh);
		return false;
	}

	/**
	 * @param $action
	 * @return bool|string|void
	 */
	public static function updateBlockedIPs($action){ //'add' or 'remove'
		$htaccessPath = self::getHtaccessPath();
		if(! $htaccessPath){
			return "Wordfence could not find your .htaccess file.";
		}
		if($action == 'remove'){
			$fh = @fopen($htaccessPath, 'r+');
			if(! $fh){
				$err = error_get_last();
				return $err['message'];
			}
			flock($fh, LOCK_EX);
			fseek($fh, 0, SEEK_SET); //start of file
			clearstatcache();
			$contents = @fread($fh, filesize($htaccessPath));
			if(! $contents){
				fclose($fh);
				return "Could not read from $htaccessPath";
			}

			$contents = preg_replace('/#WFIPBLOCKS.*WFIPBLOCKS[\r\s\n\t]*/s', '', $contents);

			ftruncate($fh, 0);
			fflush($fh);
			fseek($fh, 0, SEEK_SET);
			@fwrite($fh, $contents);
			flock($fh, LOCK_UN);
			fclose($fh);
			return false;
		}
		return false;
	}
	public static function getHtaccessPath(){
		if (!function_exists('get_home_path')) {
			include_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$homePath = get_home_path();
		$htaccessFile = $homePath.'.htaccess';
		return $htaccessFile;
	}
	public static function doNotCache(){
		if(! defined('WFDONOTCACHE')){
			define('WFDONOTCACHE', true);
		}
	}
}
