<?php
/**
 * WordPress Direct Filesystem.
 *
 * @package WordPress
 * @subpackage Filesystem
 */

/**
 * WordPress Filesystem Class for direct PHP file and folder manipulation.
 *
 * @since 2.5
 * @package WordPress
 * @subpackage Filesystem
 * @uses WP_Filesystem_Base Extends class
 */
class WP_Filesystem_Direct extends WP_Filesystem_Base {
	var $permission = null;
	var $errors = null;
	function WP_Filesystem_Direct($arg) {
		$this->method = 'direct';
		$this->errors = new WP_Error();
		$this->permission = umask();
	}
	function connect() {
		return true;
	}
	function setDefaultPermissions($perm) {
		$this->permission = $perm;
	}
	function get_contents($file) {
		return @file_get_contents($file);
	}
	function get_contents_array($file) {
		return @file($file);
	}
	function put_contents($file, $contents, $mode = false, $type = '') {
		if ( ! ($fp = @fopen($file, 'w' . $type)) )
			return false;
		@fwrite($fp, $contents);
		@fclose($fp);
		$this->chmod($file,$mode);
		return true;
	}
	function cwd() {
		return @getcwd();
	}
	function chdir($dir) {
		return @chdir($dir);
	}
	function chgrp($file, $group, $recursive = false) {
		if ( ! $this->exists($file) )
			return false;
		if ( ! $recursive )
			return @chgrp($file, $group);
		if ( ! $this->is_dir($file) )
			return @chgrp($file, $group);
		//Is a directory, and we want recursive
		$file = trailingslashit($file);
		$filelist = $this->dirlist($file);
		foreach ($filelist as $filename)
			$this->chgrp($file . $filename, $group, $recursive);

		return true;
	}
	function chmod($file, $mode = false, $recursive = false) {
		if ( ! $mode )
			$mode = $this->permission;
		if ( ! $this->exists($file) )
			return false;
		if ( ! $recursive )
			return @chmod($file,$mode);
		if ( ! $this->is_dir($file) )
			return @chmod($file, $mode);
		//Is a directory, and we want recursive
		$file = trailingslashit($file);
		$filelist = $this->dirlist($file);
		foreach ($filelist as $filename)
			$this->chmod($file . $filename, $mode, $recursive);

		return true;
	}
	function chown($file, $owner, $recursive = false) {
		if ( ! $this->exists($file) )
			return false;
		if ( ! $recursive )
			return @chown($file, $owner);
		if ( ! $this->is_dir($file) )
			return @chown($file, $owner);
		//Is a directory, and we want recursive
		$filelist = $this->dirlist($file);
		foreach ($filelist as $filename){
			$this->chown($file . '/' . $filename, $owner, $recursive);
		}
		return true;
	}
	function owner($file) {
		$owneruid = @fileowner($file);
		if ( ! $owneruid )
			return false;
		if ( ! function_exists('posix_getpwuid') )
			return $owneruid;
		$ownerarray = posix_getpwuid($owneruid);
		return $ownerarray['name'];
	}
	function getchmod($file) {
		return substr(decoct(@fileperms($file)),3);
	}
	function group($file) {
		$gid = @filegroup($file);
		if ( ! $gid )
			return false;
		if ( ! function_exists('posix_getgrgid') )
			return $gid;
		$grouparray = posix_getgrgid($gid);
		return $grouparray['name'];
	}

	function copy($source, $destination, $overwrite = false) {
		if ( ! $overwrite && $this->exists($destination) )
			return false;
		return copy($source, $destination);
	}

	function move($source, $destination, $overwrite = false) {
		//Possible to use rename()?
		if ( $this->copy($source, $destination, $overwrite) && $this->exists($destination) ){
			$this->delete($source);
			return true;
		} else {
			return false;
		}
	}

	function delete($file, $recursive = false) {
		if ( empty($file) ) //Some filesystems report this as /, which can cause non-expected recursive deletion of all files in the filesystem.
			return false;
		$file = str_replace('\\', '/', $file); //for win32, occasional problems deleteing files otherwise

		if ( $this->is_file($file) )
			return @unlink($file);
		if ( ! $recursive && $this->is_dir($file) )
			return @rmdir($file);

		//At this point its a folder, and we're in recursive mode
		$file = trailingslashit($file);
		$filelist = $this->dirlist($file, true);

		$retval = true;
		if ( is_array($filelist) ) //false if no files, So check first.
			foreach ($filelist as $filename => $fileinfo)
				if ( ! $this->delete($file . $filename, $recursive) )
					$retval = false;

		if ( file_exists($file) && ! @rmdir($file) )
			$retval = false;
		return $retval;
	}

	function exists($file) {
		return @file_exists($file);
	}

	function is_file($file) {
		return @is_file($file);
	}

	function is_dir($path) {
		return @is_dir($path);
	}

	function is_readable($file) {
		return @is_readable($file);
	}

	function is_writable($file) {
		return @is_writable($file);
	}

	function atime($file) {
		return @fileatime($file);
	}

	function mtime($file) {
		return @filemtime($file);
	}
	function size($file) {
		return @filesize($file);
	}

	function touch($file, $time = 0, $atime = 0){
		if ($time == 0)
			$time = time();
		if ($atime == 0)
			$atime = time();
		return @touch($file, $time, $atime);
	}

	function mkdir($path, $chmod = false, $chown = false, $chgrp = false){
		if ( ! $chmod)
			$chmod = $this->permission;

		if ( ! @mkdir($path, $chmod) )
			return false;
		if ( $chown )
			$this->chown($path, $chown);
		if ( $chgrp )
			$this->chgrp($path, $chgrp);
		return true;
	}

	function rmdir($path, $recursive = false) {
		//Currently unused and untested, Use delete() instead.
		if ( ! $recursive )
			return @rmdir($path);
		//recursive:
		$filelist = $this->dirlist($path);
		foreach ($filelist as $filename => $det) {
			if ( '/' == substr($filename, -1, 1) )
				$this->rmdir($path . '/' . $filename, $recursive);
			@rmdir($filename);
		}
		return @rmdir($path);
	}

	function dirlist($path, $incdot = false, $recursive = false) {
		if ( $this->is_file($path) ) {
			$limitFile = basename($path);
			$path = dirname($path);
		} else {
			$limitFile = false;
		}
		if ( ! $this->is_dir($path) )
			return false;

		$ret = array();
		$dir = @dir($path);
		if ( ! $dir )
			return false;
		while (false !== ($entry = $dir->read()) ) {
			$struc = array();
			$struc['name'] = $entry;

			if ( '.' == $struc['name'] || '..' == $struc['name'] )
				continue; //Do not care about these folders.
			if ( '.' == $struc['name'][0] && !$incdot)
				continue;
			if ( $limitFile && $struc['name'] != $limitFile)
				continue;

			$struc['perms'] 	= $this->gethchmod($path.'/'.$entry);
			$struc['permsn']	= $this->getnumchmodfromh($struc['perms']);
			$struc['number'] 	= false;
			$struc['owner']    	= $this->owner($path.'/'.$entry);
			$struc['group']    	= $this->group($path.'/'.$entry);
			$struc['size']    	= $this->size($path.'/'.$entry);
			$struc['lastmodunix']= $this->mtime($path.'/'.$entry);
			$struc['lastmod']   = date('M j',$struc['lastmodunix']);
			$struc['time']    	= date('h:i:s',$struc['lastmodunix']);
			$struc['type']		= $this->is_dir($path.'/'.$entry) ? 'd' : 'f';

			if ( 'd' == $struc['type'] ) {
				if ( $recursive )
					$struc['files'] = $this->dirlist($path . '/' . $struc['name'], $incdot, $recursive);
				else
					$struc['files'] = array();
			}

			$ret[ $struc['name'] ] = $struc;
		}
		$dir->close();
		unset($dir);
		return $ret;
	}
}
?>
