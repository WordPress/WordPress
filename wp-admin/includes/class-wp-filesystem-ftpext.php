<?php
/**
 * WordPress FTP Filesystem.
 *
 * @package WordPress
 * @subpackage Filesystem
 */

/**
 * WordPress Filesystem Class for implementing FTP.
 *
 * @since 2.5.0
 * @package WordPress
 * @subpackage Filesystem
 * @uses WP_Filesystem_Base Extends class
 */
class WP_Filesystem_FTPext extends WP_Filesystem_Base {
	public $link;

	public function __construct($opt='') {
		$this->method = 'ftpext';
		$this->errors = new WP_Error();

		// Check if possible to use ftp functions.
		if ( ! extension_loaded('ftp') ) {
			$this->errors->add('no_ftp_ext', __('The ftp PHP extension is not available'));
			return;
		}

		// This Class uses the timeout on a per-connection basis, Others use it on a per-action basis.

		if ( ! defined('FS_TIMEOUT') )
			define('FS_TIMEOUT', 240);

		if ( empty($opt['port']) )
			$this->options['port'] = 21;
		else
			$this->options['port'] = $opt['port'];

		if ( empty($opt['hostname']) )
			$this->errors->add('empty_hostname', __('FTP hostname is required'));
		else
			$this->options['hostname'] = $opt['hostname'];

		// Check if the options provided are OK.
		if ( empty($opt['username']) )
			$this->errors->add('empty_username', __('FTP username is required'));
		else
			$this->options['username'] = $opt['username'];

		if ( empty($opt['password']) )
			$this->errors->add('empty_password', __('FTP password is required'));
		else
			$this->options['password'] = $opt['password'];

		$this->options['ssl'] = false;
		if ( isset($opt['connection_type']) && 'ftps' == $opt['connection_type'] )
			$this->options['ssl'] = true;
	}

	public function connect() {
		if ( isset($this->options['ssl']) && $this->options['ssl'] && function_exists('ftp_ssl_connect') )
			$this->link = @ftp_ssl_connect($this->options['hostname'], $this->options['port'], FS_CONNECT_TIMEOUT);
		else
			$this->link = @ftp_connect($this->options['hostname'], $this->options['port'], FS_CONNECT_TIMEOUT);

		if ( ! $this->link ) {
			$this->errors->add('connect', sprintf(__('Failed to connect to FTP Server %1$s:%2$s'), $this->options['hostname'], $this->options['port']));
			return false;
		}

		if ( ! @ftp_login($this->link,$this->options['username'], $this->options['password']) ) {
			$this->errors->add('auth', sprintf(__('Username/Password incorrect for %s'), $this->options['username']));
			return false;
		}

		// Set the Connection to use Passive FTP
		@ftp_pasv( $this->link, true );
		if ( @ftp_get_option($this->link, FTP_TIMEOUT_SEC) < FS_TIMEOUT )
			@ftp_set_option($this->link, FTP_TIMEOUT_SEC, FS_TIMEOUT);

		return true;
	}

	/**
	 * @param string $file
	 * @return false|string
	 */
	public function get_contents( $file ) {
		$tempfile = wp_tempnam($file);
		$temp = fopen($tempfile, 'w+');

		if ( ! $temp )
			return false;

		if ( ! @ftp_fget($this->link, $temp, $file, FTP_BINARY ) )
			return false;

		fseek( $temp, 0 ); // Skip back to the start of the file being written to
		$contents = '';

		while ( ! feof($temp) )
			$contents .= fread($temp, 8192);

		fclose($temp);
		unlink($tempfile);
		return $contents;
	}

	/**
	 * @param string $file
	 * @return array
	 */
	public function get_contents_array($file) {
		return explode("\n", $this->get_contents($file));
	}

	/**
	 * @param string $file
	 * @param string $contents
	 * @param bool|int $mode
	 * @return bool
	 */
	public function put_contents($file, $contents, $mode = false ) {
		$tempfile = wp_tempnam($file);
		$temp = fopen( $tempfile, 'wb+' );
		if ( ! $temp )
			return false;

		mbstring_binary_safe_encoding();

		$data_length = strlen( $contents );
		$bytes_written = fwrite( $temp, $contents );

		reset_mbstring_encoding();

		if ( $data_length !== $bytes_written ) {
			fclose( $temp );
			unlink( $tempfile );
			return false;
		}

		fseek( $temp, 0 ); // Skip back to the start of the file being written to

		$ret = @ftp_fput( $this->link, $file, $temp, FTP_BINARY );

		fclose($temp);
		unlink($tempfile);

		$this->chmod($file, $mode);

		return $ret;
	}

	/**
	 * @return string
	 */
	public function cwd() {
		$cwd = @ftp_pwd($this->link);
		if ( $cwd )
			$cwd = trailingslashit($cwd);
		return $cwd;
	}

	/**
	 * @param string $dir
	 * @return bool
	 */
	public function chdir($dir) {
		return @ftp_chdir($this->link, $dir);
	}

	/**
	 * @param string $file
	 * @param int $mode
	 * @param bool $recursive
	 * @return bool
	 */
	public function chmod($file, $mode = false, $recursive = false) {
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
		if ( ! function_exists('ftp_chmod') )
			return (bool)@ftp_site($this->link, sprintf('CHMOD %o %s', $mode, $file));
		return (bool)@ftp_chmod($this->link, $mode, $file);
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public function owner($file) {
		$dir = $this->dirlist($file);
		return $dir[$file]['owner'];
	}
	/**
	 * @param string $file
	 * @return string
	 */
	public function getchmod($file) {
		$dir = $this->dirlist($file);
		return $dir[$file]['permsn'];
	}
	/**
	 * @param string $file
	 * @return string
	 */
	public function group($file) {
		$dir = $this->dirlist($file);
		return $dir[$file]['group'];
	}

	/**
	 *
	 * @param string $source
	 * @param string $destination
	 * @param bool   $overwrite
	 * @param string|bool $mode
	 * @return bool
	 */
	public function copy($source, $destination, $overwrite = false, $mode = false) {
		if ( ! $overwrite && $this->exists($destination) )
			return false;
		$content = $this->get_contents($source);
		if ( false === $content )
			return false;
		return $this->put_contents($destination, $content, $mode);
	}
	/**
	 * @param string $source
	 * @param string $destination
	 * @param bool $overwrite
	 * @return bool
	 */
	public function move($source, $destination, $overwrite = false) {
		return ftp_rename($this->link, $source, $destination);
	}
	/**
	 * @param string $file
	 * @param bool $recursive
	 * @param string $type
	 * @return bool
	 */
	public function delete($file, $recursive = false, $type = false) {
		if ( empty($file) )
			return false;
		if ( 'f' == $type || $this->is_file($file) )
			return @ftp_delete($this->link, $file);
		if ( !$recursive )
			return @ftp_rmdir($this->link, $file);

		$filelist = $this->dirlist( trailingslashit($file) );
		if ( !empty($filelist) )
			foreach ( $filelist as $delete_file )
				$this->delete( trailingslashit($file) . $delete_file['name'], $recursive, $delete_file['type'] );
		return @ftp_rmdir($this->link, $file);
	}
	/**
	 * @param string $file
	 * @return bool
	 */
	public function exists($file) {
		$list = @ftp_nlist($this->link, $file);

		if ( empty( $list ) && $this->is_dir( $file ) ) {
			return true; // File is an empty directory.
		}

		return !empty($list); //empty list = no file, so invert.
	}
	/**
	 * @param string $file
	 * @return bool
	 */
	public function is_file($file) {
		return $this->exists($file) && !$this->is_dir($file);
	}
	/**
	 * @param string $path
	 * @return bool
	 */
	public function is_dir($path) {
		$cwd = $this->cwd();
		$result = @ftp_chdir($this->link, trailingslashit($path) );
		if ( $result && $path == $this->cwd() || $this->cwd() != $cwd ) {
			@ftp_chdir($this->link, $cwd);
			return true;
		}
		return false;
	}

	/**
	 * @param string $file
	 * @return bool
	 */
	public function is_readable($file) {
		return true;
	}
	/**
	 * @param string $file
	 * @return bool
	 */
	public function is_writable($file) {
		return true;
	}
	/**
	 * @param string $file
	 * @return bool
	 */
	public function atime($file) {
		return false;
	}
	/**
	 * @param string $file
	 * @return int
	 */
	public function mtime($file) {
		return ftp_mdtm($this->link, $file);
	}
	/**
	 * @param string $file
	 * @return int
	 */
	public function size($file) {
		return ftp_size($this->link, $file);
	}
	/**
	 * @param string $file
	 * @return bool
	 */
	public function touch($file, $time = 0, $atime = 0) {
		return false;
	}

	/**
	 * @param string $path
	 * @param mixed $chmod
	 * @param mixed $chown
	 * @param mixed $chgrp
	 * @return bool
	 */
	public function mkdir($path, $chmod = false, $chown = false, $chgrp = false) {
		$path = untrailingslashit($path);
		if ( empty($path) )
			return false;

		if ( !@ftp_mkdir($this->link, $path) )
			return false;
		$this->chmod($path, $chmod);
		return true;
	}

	/**
	 * @param string $path
	 * @param bool $recursive
	 * @return bool
	 */
	public function rmdir($path, $recursive = false) {
		return $this->delete($path, $recursive);
	}

	/**
	 * @staticvar bool $is_windows
	 * @param string $line
	 * @return string
	 */
	public function parselisting($line) {
		static $is_windows;
		if ( is_null($is_windows) )
			$is_windows = stripos( ftp_systype($this->link), 'win') !== false;

		if ( $is_windows && preg_match('/([0-9]{2})-([0-9]{2})-([0-9]{2}) +([0-9]{2}):([0-9]{2})(AM|PM) +([0-9]+|<DIR>) +(.+)/', $line, $lucifer) ) {
			$b = array();
			if ( $lucifer[3] < 70 )
				$lucifer[3] +=2000;
			else
				$lucifer[3] += 1900; // 4digit year fix
			$b['isdir'] = ( $lucifer[7] == '<DIR>');
			if ( $b['isdir'] )
				$b['type'] = 'd';
			else
				$b['type'] = 'f';
			$b['size'] = $lucifer[7];
			$b['month'] = $lucifer[1];
			$b['day'] = $lucifer[2];
			$b['year'] = $lucifer[3];
			$b['hour'] = $lucifer[4];
			$b['minute'] = $lucifer[5];
			$b['time'] = @mktime($lucifer[4] + (strcasecmp($lucifer[6], "PM") == 0 ? 12 : 0), $lucifer[5], 0, $lucifer[1], $lucifer[2], $lucifer[3]);
			$b['am/pm'] = $lucifer[6];
			$b['name'] = $lucifer[8];
		} elseif ( !$is_windows && $lucifer = preg_split('/[ ]/', $line, 9, PREG_SPLIT_NO_EMPTY)) {
			//echo $line."\n";
			$lcount = count($lucifer);
			if ( $lcount < 8 )
				return '';
			$b = array();
			$b['isdir'] = $lucifer[0]{0} === 'd';
			$b['islink'] = $lucifer[0]{0} === 'l';
			if ( $b['isdir'] )
				$b['type'] = 'd';
			elseif ( $b['islink'] )
				$b['type'] = 'l';
			else
				$b['type'] = 'f';
			$b['perms'] = $lucifer[0];
			$b['number'] = $lucifer[1];
			$b['owner'] = $lucifer[2];
			$b['group'] = $lucifer[3];
			$b['size'] = $lucifer[4];
			if ( $lcount == 8 ) {
				sscanf($lucifer[5], '%d-%d-%d', $b['year'], $b['month'], $b['day']);
				sscanf($lucifer[6], '%d:%d', $b['hour'], $b['minute']);
				$b['time'] = @mktime($b['hour'], $b['minute'], 0, $b['month'], $b['day'], $b['year']);
				$b['name'] = $lucifer[7];
			} else {
				$b['month'] = $lucifer[5];
				$b['day'] = $lucifer[6];
				if ( preg_match('/([0-9]{2}):([0-9]{2})/', $lucifer[7], $l2) ) {
					$b['year'] = date("Y");
					$b['hour'] = $l2[1];
					$b['minute'] = $l2[2];
				} else {
					$b['year'] = $lucifer[7];
					$b['hour'] = 0;
					$b['minute'] = 0;
				}
				$b['time'] = strtotime( sprintf('%d %s %d %02d:%02d', $b['day'], $b['month'], $b['year'], $b['hour'], $b['minute']) );
				$b['name'] = $lucifer[8];
			}
		}

		// Replace symlinks formatted as "source -> target" with just the source name
		if ( $b['islink'] )
			$b['name'] = preg_replace( '/(\s*->\s*.*)$/', '', $b['name'] );

		return $b;
	}

	/**
	 * @param string $path
	 * @param bool $include_hidden
	 * @param bool $recursive
	 * @return bool|array
	 */
	public function dirlist($path = '.', $include_hidden = true, $recursive = false) {
		if ( $this->is_file($path) ) {
			$limit_file = basename($path);
			$path = dirname($path) . '/';
		} else {
			$limit_file = false;
		}

		$pwd = @ftp_pwd($this->link);
		if ( ! @ftp_chdir($this->link, $path) ) // Cant change to folder = folder doesn't exist
			return false;
		$list = @ftp_rawlist($this->link, '-a', false);
		@ftp_chdir($this->link, $pwd);

		if ( empty($list) ) // Empty array = non-existent folder (real folder will show . at least)
			return false;

		$dirlist = array();
		foreach ( $list as $k => $v ) {
			$entry = $this->parselisting($v);
			if ( empty($entry) )
				continue;

			if ( '.' == $entry['name'] || '..' == $entry['name'] )
				continue;

			if ( ! $include_hidden && '.' == $entry['name'][0] )
				continue;

			if ( $limit_file && $entry['name'] != $limit_file)
				continue;

			$dirlist[ $entry['name'] ] = $entry;
		}

		$ret = array();
		foreach ( (array)$dirlist as $struc ) {
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

	public function __destruct() {
		if ( $this->link )
			ftp_close($this->link);
	}
}
