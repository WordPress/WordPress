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
 * @since 2.5.0
 * @package WordPress
 * @subpackage Filesystem
 * @uses WP_Filesystem_Base Extends class
 */
class WP_Filesystem_ftpsockets extends WP_Filesystem_Base {
	public $ftp = false;
	public $errors = null;
	public $options = array();

	public function __construct($opt = '') {
		$this->method = 'ftpsockets';
		$this->errors = new WP_Error();

		// Check if possible to use ftp functions.
		if ( ! @include_once( ABSPATH . 'wp-admin/includes/class-ftp.php' ) ) {
			return false;
		}
		$this->ftp = new ftp();

		if ( empty($opt['port']) )
			$this->options['port'] = 21;
		else
			$this->options['port'] = $opt['port'];

		if ( empty($opt['hostname']) )
			$this->errors->add('empty_hostname', __('FTP hostname is required'));
		else
			$this->options['hostname'] = $opt['hostname'];

		if ( ! empty($opt['base']) )
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

	public function connect() {
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

		$this->ftp->SetType( FTP_BINARY );
		$this->ftp->Passive( true );
		$this->ftp->setTimeout( FS_TIMEOUT );
		return true;
	}

	public function get_contents( $file ) {
		if ( ! $this->exists($file) )
			return false;

		$temp = wp_tempnam( $file );

		if ( ! $temphandle = fopen($temp, 'w+') )
			return false;

		mbstring_binary_safe_encoding();

		if ( ! $this->ftp->fget($temphandle, $file) ) {
			fclose($temphandle);
			unlink($temp);

			reset_mbstring_encoding();

			return ''; // Blank document, File does exist, It's just blank.
		}

		reset_mbstring_encoding();

		fseek( $temphandle, 0 ); // Skip back to the start of the file being written to
		$contents = '';

		while ( ! feof($temphandle) )
			$contents .= fread($temphandle, 8192);

		fclose($temphandle);
		unlink($temp);
		return $contents;
	}

	public function get_contents_array($file) {
		return explode("\n", $this->get_contents($file) );
	}

	public function put_contents($file, $contents, $mode = false ) {
		$temp = wp_tempnam( $file );
		if ( ! $temphandle = @fopen($temp, 'w+') ) {
			unlink($temp);
			return false;
		}

		// The FTP class uses string functions internally during file download/upload
		mbstring_binary_safe_encoding();

		$bytes_written = fwrite( $temphandle, $contents );
		if ( false === $bytes_written || $bytes_written != strlen( $contents ) ) {
			fclose( $temphandle );
			unlink( $temp );

			reset_mbstring_encoding();

			return false;
		}

		fseek( $temphandle, 0 ); // Skip back to the start of the file being written to

		$ret = $this->ftp->fput($file, $temphandle);

		reset_mbstring_encoding();

		fclose($temphandle);
		unlink($temp);

		$this->chmod($file, $mode);

		return $ret;
	}

	public function cwd() {
		$cwd = $this->ftp->pwd();
		if ( $cwd )
			$cwd = trailingslashit($cwd);
		return $cwd;
	}

	public function chdir($file) {
		return $this->ftp->chdir($file);
	}

	public function chgrp($file, $group, $recursive = false ) {
		return false;
	}

	public function chmod($file, $mode = false, $recursive = false ) {
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

	public function owner($file) {
		$dir = $this->dirlist($file);
		return $dir[$file]['owner'];
	}

	public function getchmod($file) {
		$dir = $this->dirlist($file);
		return $dir[$file]['permsn'];
	}

	public function group($file) {
		$dir = $this->dirlist($file);
		return $dir[$file]['group'];
	}

	public function copy($source, $destination, $overwrite = false, $mode = false) {
		if ( ! $overwrite && $this->exists($destination) )
			return false;

		$content = $this->get_contents($source);
		if ( false === $content )
			return false;

		return $this->put_contents($destination, $content, $mode);
	}

	public function move($source, $destination, $overwrite = false ) {
		return $this->ftp->rename($source, $destination);
	}

	public function delete($file, $recursive = false, $type = false) {
		if ( empty($file) )
			return false;
		if ( 'f' == $type || $this->is_file($file) )
			return $this->ftp->delete($file);
		if ( !$recursive )
			return $this->ftp->rmdir($file);

		return $this->ftp->mdel($file);
	}

	public function exists( $file ) {
		$list = $this->ftp->nlist( $file );
		return !empty( $list ); //empty list = no file, so invert.
		// Return $this->ftp->is_exists($file); has issues with ABOR+426 responses on the ncFTPd server.
	}

	public function is_file($file) {
		if ( $this->is_dir($file) )
			return false;
		if ( $this->exists($file) )
			return true;
		return false;
	}

	public function is_dir($path) {
		$cwd = $this->cwd();
		if ( $this->chdir($path) ) {
			$this->chdir($cwd);
			return true;
		}
		return false;
	}

	public function is_readable($file) {
		return true;
	}

	public function is_writable($file) {
		return true;
	}

	public function atime($file) {
		return false;
	}

	public function mtime($file) {
		return $this->ftp->mdtm($file);
	}

	public function size($file) {
		return $this->ftp->filesize($file);
	}

	public function touch($file, $time = 0, $atime = 0 ) {
		return false;
	}

	public function mkdir($path, $chmod = false, $chown = false, $chgrp = false ) {
		$path = untrailingslashit($path);
		if ( empty($path) )
			return false;

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

	public function rmdir($path, $recursive = false ) {
		$this->delete($path, $recursive);
	}

	public function dirlist($path = '.', $include_hidden = true, $recursive = false ) {
		if ( $this->is_file($path) ) {
			$limit_file = basename($path);
			$path = dirname($path) . '/';
		} else {
			$limit_file = false;
		}

		mbstring_binary_safe_encoding();

		$list = $this->ftp->dirlist($path);
		if ( empty( $list ) && ! $this->exists( $path ) ) {

			reset_mbstring_encoding();

			return false;
		}

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

			// Replace symlinks formatted as "source -> target" with just the source name
			if ( $struc['islink'] )
				$struc['name'] = preg_replace( '/(\s*->\s*.*)$/', '', $struc['name'] );

			$ret[ $struc['name'] ] = $struc;
		}

		reset_mbstring_encoding();

		return $ret;
	}

	public function __destruct() {
		$this->ftp->quit();
	}
}
