<?php
/**
 * WordPress FTP Sockets Filesystem.
 *
 * @package WordPress
 * @subpackage Filesystem
 */

/**
 * WordPress Filesystem Class for implementing FTP Sockets.
 *
 * @since 2.5
 * @package WordPress
 * @subpackage Filesystem
 * @uses WP_Filesystem_Base Extends class
 */
class WP_Filesystem_ftpsockets extends WP_Filesystem_Base {
	var $ftp = false;
	var $errors = null;
	var $options = array();

	function WP_Filesystem_ftpsockets($opt = '') {
		$this->method = 'ftpsockets';
		$this->errors = new WP_Error();

		//Check if possible to use ftp functions.
		if ( ! @include_once ABSPATH . 'wp-admin/includes/class-ftp.php' )
				return false;
		$this->ftp = new ftp();

		//Set defaults:
		if ( empty($opt['port']) )
			$this->options['port'] = 21;
		else
			$this->options['port'] = $opt['port'];

		if ( empty($opt['hostname']) )
			$this->errors->add('empty_hostname', __('FTP hostname is required'));
		else
			$this->options['hostname'] = $opt['hostname'];

		if ( isset($opt['base']) && ! empty($opt['base']) )
			$this->wp_base = $opt['base'];

		// Check if the options provided are OK.
		if ( empty ($opt['username']) )
			$this->errors->add('empty_username', __('FTP username is required'));
		else
			$this->options['username'] = $opt['username'];

		if ( empty ($opt['password']) )
			$this->errors->add('empty_password', __('FTP password is required'));
		else
			$this->options['password'] = $opt['password'];
	}

	function connect() {
		if ( ! $this->ftp )
			return false;

		$this->ftp->setTimeout(FS_CONNECT_TIMEOUT);

		if ( ! $this->ftp->SetServer($this->options['hostname'], $this->options['port']) ) {
			$this->errors->add('connect', sprintf(__('Failed to connect to FTP Server %1$s:%2$s'), $this->options['hostname'], $this->options['port']));
			return false;
		}

		if ( ! $this->ftp->connect() ) {
			$this->errors->add('connect', sprintf(__('Failed to connect to FTP Server %1$s:%2$s'), $this->options['hostname'], $this->options['port']));
			return false;
		}

		if ( ! $this->ftp->login($this->options['username'], $this->options['password']) ) {
			$this->errors->add('auth', sprintf(__('Username/Password incorrect for %s'), $this->options['username']));
			return false;
		}

		$this->ftp->SetType(FTP_AUTOASCII);
		$this->ftp->Passive(true);
		$this->ftp->setTimeout(FS_TIMEOUT);
		return true;
	}

	function get_contents($file, $type = '', $resumepos = 0) {
		if ( ! $this->exists($file) )
			return false;

		if ( empty($type) )
			$type = FTP_AUTOASCII;
		$this->ftp->SetType($type);

		$temp = wp_tempnam( $file );

		if ( ! $temphandle = fopen($temp, 'w+') )
			return false;

		if ( ! $this->ftp->fget($temphandle, $file) ) {
			fclose($temphandle);
			unlink($temp);
			return ''; //Blank document, File does exist, Its just blank.
		}

		fseek($temphandle, 0); //Skip back to the start of the file being written to
		$contents = '';

		while ( ! feof($temphandle) )
			$contents .= fread($temphandle, 8192);

		fclose($temphandle);
		unlink($temp);
		return $contents;
	}

	function get_contents_array($file) {
		return explode("\n", $this->get_contents($file) );
	}

	function put_contents($file, $contents, $mode = false ) {
		$temp = wp_tempnam( $file );
		if ( ! $temphandle = @fopen($temp, 'w+') ) {
			unlink($temp);
			return false;
		}

		fwrite($temphandle, $contents);
		fseek($temphandle, 0); //Skip back to the start of the file being written to

		$type = $this->is_binary($contents) ? FTP_BINARY : FTP_ASCII;
		$this->ftp->SetType($type);

		$ret = $this->ftp->fput($file, $temphandle);

		fclose($temphandle);
		unlink($temp);

		$this->chmod($file, $mode);

		return $ret;
	}

	function cwd() {
		$cwd = $this->ftp->pwd();
		if ( $cwd )
			$cwd = trailingslashit($cwd);
		return $cwd;
	}

	function chdir($file) {
		return $this->ftp->chdir($file);
	}

	function chgrp($file, $group, $recursive = false ) {
		return false;
	}

	function chmod($file, $mode = false, $recursive = false ) {
		if ( ! $mode ) {
			if ( $this->is_file($file) )
				$mode = FS_CHMOD_FILE;
			elseif ( $this->is_dir($file) )
				$mode = FS_CHMOD_DIR;
			else
				return false;
		}

		// chmod any sub-objects if recursive.
		if ( $recursive && $this->is_dir($file) ) {
			$filelist = $this->dirlist($file);
			foreach ( (array)$filelist as $filename => $filemeta )
				$this->chmod($file . '/' . $filename, $mode, $recursive);
		}

		// chmod the file or directory
		return $this->ftp->chmod($file, $mode);
	}

	function chown($file, $owner, $recursive = false ) {
		return false;
	}

	function owner($file) {
		$dir = $this->dirlist($file);
		return $dir[$file]['owner'];
	}

	function getchmod($file) {
		$dir = $this->dirlist($file);
		return $dir[$file]['permsn'];
	}

	function group($file) {
		$dir = $this->dirlist($file);
		return $dir[$file]['group'];
	}

	function copy($source, $destination, $overwrite = false ) {
		if ( ! $overwrite && $this->exists($destination) )
			return false;

		$content = $this->get_contents($source);
		if ( false === $content )
			return false;

		return $this->put_contents($destination, $content);
	}

	function move($source, $destination, $overwrite = false ) {
		return $this->ftp->rename($source, $destination);
	}

	function delete($file, $recursive = false ) {
		if ( empty($file) )
			return false;
		if ( $this->is_file($file) )
			return $this->ftp->delete($file);
		if ( !$recursive )
			return $this->ftp->rmdir($file);

		return $this->ftp->mdel($file);
	}

	function exists($file) {
		return $this->ftp->is_exists($file);
	}

	function is_file($file) {
		return $this->is_dir($file) ? false : true;
	}

	function is_dir($path) {
		$cwd = $this->cwd();
		if ( $this->chdir($path) ) {
			$this->chdir($cwd);
			return true;
		}
		return false;
	}

	function is_readable($file) {
		//Get dir list, Check if the file is writable by the current user??
		return true;
	}

	function is_writable($file) {
		//Get dir list, Check if the file is writable by the current user??
		return true;
	}

	function atime($file) {
		return false;
	}

	function mtime($file) {
		return $this->ftp->mdtm($file);
	}

	function size($file) {
		return $this->ftp->filesize($file);
	}

	function touch($file, $time = 0, $atime = 0 ) {
		return false;
	}

	function mkdir($path, $chmod = false, $chown = false, $chgrp = false ) {
		if ( ! $this->ftp->mkdir($path) )
			return false;
		if ( ! $chmod )
			$chmod = FS_CHMOD_DIR;
		$this->chmod($path, $chmod);
		if ( $chown )
			$this->chown($path, $chown);
		if ( $chgrp )
			$this->chgrp($path, $chgrp);
		return true;
	}

	function rmdir($path, $recursive = false ) {
		$this->delete($path, $recursive);
	}

	function dirlist($path = '.', $include_hidden = true, $recursive = false ) {
		if ( $this->is_file($path) ) {
			$limit_file = basename($path);
			$path = dirname($path) . '/';
		} else {
			$limit_file = false;
		}

		$list = $this->ftp->dirlist($path);
		if ( ! $list )
			return false;

		$ret = array();
		foreach ( $list as $struc ) {

			if ( '.' == $struc['name'] || '..' == $struc['name'] )
				continue;

			if ( ! $include_hidden && '.' == $struc['name'][0] )
				continue;

			if ( $limit_file && $struc['name'] != $limit_file )
				continue;

			if ( 'd' == $struc['type'] ) {
				if ( $recursive )
					$struc['files'] = $this->dirlist($path . '/' . $struc['name'], $include_hidden, $recursive);
				else
					$struc['files'] = array();
			}

			$ret[ $struc['name'] ] = $struc;
		}
		return $ret;
	}

	function __destruct() {
		$this->ftp->quit();
	}
}

?>
