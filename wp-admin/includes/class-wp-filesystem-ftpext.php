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
 * @since 2.5
 * @package WordPress
 * @subpackage Filesystem
 * @uses WP_Filesystem_Base Extends class
 */
class WP_Filesystem_FTPext extends WP_Filesystem_Base {
	var $link;
	var $timeout = 5;
	var $errors = array();
	var $options = array();

	var $permission = null;

	function WP_Filesystem_FTPext($opt='') {
		$this->method = 'ftpext';
		$this->errors = new WP_Error();

		//Check if possible to use ftp functions.
		if ( ! extension_loaded('ftp') ) {
			$this->errors->add('no_ftp_ext', __('The ftp PHP extension is not available'));
			return false;
		}

		// Set defaults:
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

		$this->options['ssl'] = false;
		if ( isset($opt['ssl']) )
			$this->options['ssl'] = ( !empty($opt['ssl']) );
		elseif ( isset( $opt['connection_type']) )
			$this->options['ssl'] = ( 'ftps' == $opt['connection_type'] );
	}

	function connect() {
		if ( $this->options['ssl'] && function_exists('ftp_ssl_connect') )
			$this->link = @ftp_ssl_connect($this->options['hostname'], $this->options['port'],$this->timeout);
		else
			$this->link = @ftp_connect($this->options['hostname'], $this->options['port'],$this->timeout);

		if ( ! $this->link ) {
			$this->errors->add('connect', sprintf(__('Failed to connect to FTP Server %1$s:%2$s'), $this->options['hostname'], $this->options['port']));
			return false;
		}

		if ( ! @ftp_login($this->link,$this->options['username'], $this->options['password']) ) {
			$this->errors->add('auth', sprintf(__('Username/Password incorrect for %s'), $this->options['username']));
			return false;
		}

		//Set the Connection to use Passive FTP
		@ftp_pasv( $this->link, true );

		return true;
	}

	function setDefaultPermissions($perm) {
		$this->permission = $perm;
	}

	function get_contents($file, $type = '', $resumepos = 0 ){
		if( empty($type) )
			$type = FTP_BINARY;

		$temp = tmpfile();
		if ( ! $temp )
			return false;

		if( ! @ftp_fget($this->link, $temp, $file, $type, $resumepos) )
			return false;

		fseek($temp, 0); //Skip back to the start of the file being written to
		$contents = '';

		while ( ! feof($temp) )
			$contents .= fread($temp, 8192);

		fclose($temp);
		return $contents;
	}
	function get_contents_array($file) {
		return explode("\n", $this->get_contents($file));
	}
	function put_contents($file, $contents, $type = '' ) {
		if( empty($type) )
			$type = $this->is_binary($contents) ? FTP_BINARY : FTP_ASCII;

		$temp = tmpfile();
		if ( ! $temp )
			return false;

		fwrite($temp, $contents);
		fseek($temp, 0); //Skip back to the start of the file being written to

		$ret = @ftp_fput($this->link, $file, $temp, $type);

		fclose($temp);
		return $ret;
	}
	function cwd() {
		$cwd = @ftp_pwd($this->link);
		if( $cwd )
			$cwd = trailingslashit($cwd);
		return $cwd;
	}
	function chdir($dir) {
		return @ftp_chdir($dir);
	}
	function chgrp($file, $group, $recursive = false ) {
		return false;
	}
	function chmod($file, $mode = false, $recursive = false) {
		if( ! $mode )
			$mode = $this->permission;
		if( ! $mode )
			return false;
		if ( ! $this->exists($file) )
			return false;
		if ( ! $recursive || ! $this->is_dir($file) ) {
			if ( ! function_exists('ftp_chmod') )
				return @ftp_site($this->link, sprintf('CHMOD %o %s', $mode, $file));
			return @ftp_chmod($this->link, $mode, $file);
		}
		//Is a directory, and we want recursive
		$filelist = $this->dirlist($file);
		foreach($filelist as $filename){
			$this->chmod($file . '/' . $filename, $mode, $recursive);
		}
		return true;
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
		if( ! $overwrite && $this->exists($destination) )
			return false;
		$content = $this->get_contents($source);
		if( false === $content)
			return false;
		return $this->put_contents($destination, $content);
	}
	function move($source, $destination, $overwrite = false) {
		return ftp_rename($this->link, $source, $destination);
	}

	function delete($file,$recursive=false) {
		if ( $this->is_file($file) )
			return @ftp_delete($this->link, $file);
		if ( !$recursive )
			return @ftp_rmdir($this->link, $file);
		$filelist = $this->dirlist($file);
		foreach ((array) $filelist as $filename => $fileinfo) {
			$this->delete($file . '/' . $filename, $recursive);
		}
		return @ftp_rmdir($this->link, $file);
	}

	function exists($file) {
		$list = ftp_rawlist($this->link, $file, false);
		if( ! $list )
			return false;
		return count($list) == 1 ? true : false;
	}
	function is_file($file) {
		return $this->is_dir($file) ? false : true;
	}
	function is_dir($path) {
		$cwd = $this->cwd();
		$result = @ftp_chdir($this->link, $path);
		if( $result && $path == $this->cwd() || $this->cwd() != $cwd ) {
			@ftp_chdir($this->link, $cwd);
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
		return ftp_mdtm($this->link, $file);
	}
	function size($file) {
		return ftp_size($this->link, $file);
	}
	function touch($file, $time = 0, $atime = 0) {
		return false;
	}
	function mkdir($path, $chmod = false, $chown = false, $chgrp = false) {
		if( !@ftp_mkdir($this->link, $path) )
			return false;
		if( $chmod )
			$this->chmod($path, $chmod);
		if( $chown )
			$this->chown($path, $chown);
		if( $chgrp )
			$this->chgrp($path, $chgrp);
		return true;
	}
	function rmdir($path, $recursive = false) {
		if( ! $recursive )
			return @ftp_rmdir($this->link, $path);

		//TODO: Recursive Directory delete, Have to delete files from the folder first.
		//$dir = $this->dirlist($path);
		//foreach($dir as $file)

	}

	function parselisting($line) {
		$is_windows = ($this->OS_remote == FTP_OS_Windows);
		if ($is_windows && preg_match("/([0-9]{2})-([0-9]{2})-([0-9]{2}) +([0-9]{2}):([0-9]{2})(AM|PM) +([0-9]+|<DIR>) +(.+)/", $line, $lucifer)) {
			$b = array();
			if ($lucifer[3]<70) { $lucifer[3] +=2000; } else { $lucifer[3]+=1900; } // 4digit year fix
			$b['isdir'] = ($lucifer[7]=="<DIR>");
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
			$b['time'] = @mktime($lucifer[4]+(strcasecmp($lucifer[6],"PM")==0?12:0),$lucifer[5],0,$lucifer[1],$lucifer[2],$lucifer[3]);
			$b['am/pm'] = $lucifer[6];
			$b['name'] = $lucifer[8];
		} else if (!$is_windows && $lucifer=preg_split("/[ ]/",$line,9,PREG_SPLIT_NO_EMPTY)) {
			//echo $line."\n";
			$lcount=count($lucifer);
			if ($lcount<8) return '';
			$b = array();
			$b['isdir'] = $lucifer[0]{0} === "d";
			$b['islink'] = $lucifer[0]{0} === "l";
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
			if ($lcount==8) {
				sscanf($lucifer[5],"%d-%d-%d",$b['year'],$b['month'],$b['day']);
				sscanf($lucifer[6],"%d:%d",$b['hour'],$b['minute']);
				$b['time'] = @mktime($b['hour'],$b['minute'],0,$b['month'],$b['day'],$b['year']);
				$b['name'] = $lucifer[7];
			} else {
				$b['month'] = $lucifer[5];
				$b['day'] = $lucifer[6];
				if (preg_match("/([0-9]{2}):([0-9]{2})/",$lucifer[7],$l2)) {
					$b['year'] = date("Y");
					$b['hour'] = $l2[1];
					$b['minute'] = $l2[2];
				} else {
					$b['year'] = $lucifer[7];
					$b['hour'] = 0;
					$b['minute'] = 0;
				}
				$b['time'] = strtotime(sprintf("%d %s %d %02d:%02d",$b['day'],$b['month'],$b['year'],$b['hour'],$b['minute']));
				$b['name'] = $lucifer[8];
			}
		}

		return $b;
	}

	function dirlist($path = '.', $incdot = false, $recursive = false) {
		if( $this->is_file($path) ) {
			$limitFile = basename($path);
			$path = dirname($path) . '/';
		} else {
			$limitFile = false;
		}

		$list = @ftp_rawlist($this->link, '-a ' . $path, false);

		if ( $list === false )
			return false;

		$dirlist = array();
		foreach ( $list as $k => $v ) {
			$entry = $this->parselisting($v);
			if ( empty($entry) )
				continue;

			if ( '.' == $entry["name"] || '..' == $entry["name"] )
				continue;

			$dirlist[ $entry['name'] ] = $entry;
		}

		if ( ! $dirlist )
			return false;
		if ( empty($dirlist) )
			return array();

		$ret = array();
		foreach ( $dirlist as $struc ) {

			if ( 'd' == $struc['type'] ) {
				$struc['files'] = array();

				if ( $incdot ){
					//We're including the doted starts
					if( '.' != $struc['name'] && '..' != $struc['name'] ){ //Ok, It isnt a special folder
						if ($recursive)
							$struc['files'] = $this->dirlist($path . '/' . $struc['name'], $incdot, $recursive);
					}
				} else { //No dots
					if ($recursive)
						$struc['files'] = $this->dirlist($path . '/' . $struc['name'], $incdot, $recursive);
				}
			}
			//File
			$ret[$struc['name']] = $struc;
		}
		return $ret;
	}

	function __destruct(){
		if( $this->link )
			ftp_close($this->link);
	}
}

?>
