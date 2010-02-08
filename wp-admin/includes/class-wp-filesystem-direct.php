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
	var $errors = null;
	/**
	 * constructor
	 *
	 * @param $arg mixed ingored argument
	 */
	function WP_Filesystem_Direct($arg) {
		$this->method = 'direct';
		$this->errors = new WP_Error();
	}
	/**
	 * connect filesystem.
	 *
	 * @return bool Returns true on success or false on failure (always true for WP_Filesystem_Direct).
	 */
	function connect() {
		return true;
	}
	/**
	 * Reads entire file into a string
	 *
	 * @param $file string Name of the file to read.
	 * @return string|bool The function returns the read data or false on failure.
	 */
	function get_contents($file) {
		return @file_get_contents($file);
	}
	/**
	 * Reads entire file into an array
	 *
	 * @param $file string Path to the file.
	 * @return array|bool the file contents in an array or false on failure.
	 */
	function get_contents_array($file) {
		return @file($file);
	}
	/**
	 * Write a string to a file
	 *
	 * @param $file string Remote path to the file where to write the data.
	 * @param $contents string The data to write.
	 * @param $mode int (optional) The file permissions as octal number, usually 0644.
	 * @return bool False upon failure.
	 */
	function put_contents($file, $contents, $mode = false ) {
		if ( ! ($fp = @fopen($file, 'w')) )
			return false;
		@fwrite($fp, $contents);
		@fclose($fp);
		$this->chmod($file, $mode);
		return true;
	}
	/**
	 * Gets the current working directory
	 *
	 * @return string|bool the current working directory on success, or false on failure.
	 */
	function cwd() {
		return @getcwd();
	}
	/**
	 * Change directory
	 *
	 * @param $dir string The new current directory.
	 * @return bool Returns true on success or false on failure.
	 */
	function chdir($dir) {
		return @chdir($dir);
	}
	/**
	 * Changes file group
	 *
	 * @param $file string Path to the file.
	 * @param $group mixed A group name or number.
	 * @param $recursive bool (optional) If set True changes file group recursivly. Defaults to False.
	 * @return bool Returns true on success or false on failure.
	 */
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
	/**
	 * Changes filesystem permissions
	 *
	 * @param $file string Path to the file.
	 * @param $mode int (optional) The permissions as octal number, usually 0644 for files, 0755 for dirs.
	 * @param $recursive bool (optional) If set True changes file group recursivly. Defaults to False.
	 * @return bool Returns true on success or false on failure.
	 */
	function chmod($file, $mode = false, $recursive = false) {
		if ( ! $mode ) {
			if ( $this->is_file($file) )
				$mode = FS_CHMOD_FILE;
			elseif ( $this->is_dir($file) )
				$mode = FS_CHMOD_DIR;
			else
				return false;
		}

		if ( ! $recursive || ! $this->is_dir($file) )
			return @chmod($file, $mode);
		//Is a directory, and we want recursive
		$file = trailingslashit($file);
		$filelist = $this->dirlist($file);
		foreach ( (array)$filelist as $filename => $filemeta)
			$this->chmod($file . $filename, $mode, $recursive);

		return true;
	}
	/**
	 * Changes file owner
	 *
	 * @param $file string Path to the file.
	 * @param $owner mixed A user name or number.
	 * @param $recursive bool (optional) If set True changes file owner recursivly. Defaults to False.
	 * @return bool Returns true on success or false on failure.
	 */
	function chown($file, $owner, $recursive = false) {
		if ( ! $this->exists($file) )
			return false;
		if ( ! $recursive )
			return @chown($file, $owner);
		if ( ! $this->is_dir($file) )
			return @chown($file, $owner);
		//Is a directory, and we want recursive
		$filelist = $this->dirlist($file);
		foreach ($filelist as $filename) {
			$this->chown($file . '/' . $filename, $owner, $recursive);
		}
		return true;
	}
	/**
	 * Gets file owner
	 *
	 * @param $file string Path to the file.
	 * @return string Username of the user.
	 */
	function owner($file) {
		$owneruid = @fileowner($file);
		if ( ! $owneruid )
			return false;
		if ( ! function_exists('posix_getpwuid') )
			return $owneruid;
		$ownerarray = posix_getpwuid($owneruid);
		return $ownerarray['name'];
	}
	/**
	 * Gets file permissions
	 *
	 * FIXME does not handle errors in fileperms()
	 *
	 * @param $file string Path to the file.
	 * @return string Mode of the file (last 4 digits).
	 */
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
		if ( ! $overwrite && $this->exists($destination) )
			return false;

		// try using rename first.  if that fails (for example, source is read only) try copy
		if ( @rename($source, $destination) )
			return true;

		if ( $this->copy($source, $destination, $overwrite) && $this->exists($destination) ) {
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

	function touch($file, $time = 0, $atime = 0) {
		if ($time == 0)
			$time = time();
		if ($atime == 0)
			$atime = time();
		return @touch($file, $time, $atime);
	}

	function mkdir($path, $chmod = false, $chown = false, $chgrp = false) {
		// safe mode fails with a trailing slash under certain PHP versions.
		$path = untrailingslashit($path);
		if ( empty($path) )
			$path = '/';

		if ( ! $chmod )
			$chmod = FS_CHMOD_DIR;

		if ( ! @mkdir($path) )
			return false;
		$this->chmod($path, $chmod);
		if ( $chown )
			$this->chown($path, $chown);
		if ( $chgrp )
			$this->chgrp($path, $chgrp);
		return true;
	}

	function rmdir($path, $recursive = false) {
		return $this->delete($path, $recursive);
	}

	function dirlist($path, $include_hidden = true, $recursive = false) {
		if ( $this->is_file($path) ) {
			$limit_file = basename($path);
			$path = dirname($path);
		} else {
			$limit_file = false;
		}

		if ( ! $this->is_dir($path) )
			return false;

		$dir = @dir($path);
		if ( ! $dir )
			return false;

		$ret = array();

		while (false !== ($entry = $dir->read()) ) {
			$struc = array();
			$struc['name'] = $entry;

			if ( '.' == $struc['name'] || '..' == $struc['name'] )
				continue;

			if ( ! $include_hidden && '.' == $struc['name'][0] )
				continue;

			if ( $limit_file && $struc['name'] != $limit_file)
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
					$struc['files'] = $this->dirlist($path . '/' . $struc['name'], $include_hidden, $recursive);
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
