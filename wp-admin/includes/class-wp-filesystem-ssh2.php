<?php
/**
 * WordPress SSH2 Filesystem.
 *
 * @package WordPress
 * @subpackage Filesystem
 */

/**
 * WordPress Filesystem Class for implementing SSH2.
 *
 * To use this class you must follow these steps for PHP 5.2.6+
 *
 * @contrib http://kevin.vanzonneveld.net/techblog/article/make_ssh_connections_with_php/ - Installation Notes
 *
 * Complie libssh2 (Note: Only 0.14 is officaly working with PHP 5.2.6+ right now.)
 *
 * cd /usr/src
 * wget http://surfnet.dl.sourceforge.net/sourceforge/libssh2/libssh2-0.14.tar.gz
 * tar -zxvf libssh2-0.14.tar.gz
 * cd libssh2-0.14/
 * ./configure
 * make all install
 *
 * Note: No not leave the directory yet!
 *
 * Enter: pecl install -f ssh2
 *
 * Copy the ssh.so file it creates to your PHP Module Directory.
 * Open up your PHP.INI file and look for where extensions are placed.
 * Add in your PHP.ini file: extension=ssh2.so
 *
 * Restart Apache!
 * Check phpinfo() streams to confirm that: ssh2.shell, ssh2.exec, ssh2.tunnel, ssh2.scp, ssh2.sftp  exist.
 *
 *
 * @since 2.7
 * @package WordPress
 * @subpackage Filesystem
 * @uses WP_Filesystem_Base Extends class
 */
class WP_Filesystem_SSH2 extends WP_Filesystem_Base {

	var $debugtest = false;	//	set this to true only if your a debuging your connection

	var $link = null;
	var $sftp_link = null;
	var $keys = false;
	/*
	 * This is the timeout value for ssh results to comeback.
	 * Slower servers might need this incressed, but this number otherwise should not change.
	 *
	 * @parm $timeout int
	 *
	 */
	var $timeout = 15;
	var $errors = array();
	var $options = array();

	var $permission = 0644;

	function WP_Filesystem_SSH2($opt='') {
		$this->method = 'ssh2';
		$this->errors = new WP_Error();

		//Check if possible to use ssh2 functions.
		if ( ! extension_loaded('ssh2') ) {
			$this->errors->add('no_ssh2_ext', __('The ssh2 PHP extension is not available'));
			return false;
		}

		// Set defaults:
		if ( empty($opt['port']) )
			$this->options['port'] = 22;
		else
			$this->options['port'] = $opt['port'];

		if ( empty($opt['hostname']) )
			$this->errors->add('empty_hostname', __('SSH2 hostname is required'));
		else
			$this->options['hostname'] = $opt['hostname'];

		if ( isset($opt['base']) && ! empty($opt['base']) )
			$this->wp_base = $opt['base'];

		// Check if the options provided are OK.
		if ( empty ($opt['username']) )
			$this->errors->add('empty_username', __('SSH2 username is required'));
		else
			$this->options['username'] = $opt['username'];

		if ( ( !empty ($opt['public_key']) ) && ( !empty ($opt['private_key']) ) ) {
			$this->options['public_key'] = $opt['public_key'];
			$this->options['private_key'] = $opt['private_key'];

			$this->options['hostkey'] = array("hostkey" => "ssh-rsa");

			$this->keys = true;
		}


		if ( empty ($opt['password']) ) {
			if ( !$this->keys )	//	 password can be blank if we are using keys
				$this->errors->add('empty_password', __('SSH2 password is required'));
		} else {
			$this->options['password'] = $opt['password'];
		}

	}

	function connect() {
		$this->debug("connect();");

		if ( ! $this->keys ) {
			$this->link = @ssh2_connect($this->options['hostname'], $this->options['port']);
		} else {
			$this->link = @ssh2_connect($this->options['hostname'], $this->options['port'], $this->options['hostkey']);
		}

		if ( ! $this->link ) {
			$this->errors->add('connect', sprintf(__('Failed to connect to SSH2 Server %1$s:%2$s'), $this->options['hostname'], $this->options['port']));
			return false;
		}

		if ( !$this->keys ) {
			if ( ! @ssh2_auth_password($this->link, $this->options['username'], $this->options['password']) ) {
				$this->errors->add('auth', sprintf(__('Username/Password incorrect for %s'), $this->options['username']));
				return false;
			}
		} else {
			if ( ! @ssh2_auth_pubkey_file($this->link, $this->options['username'], $this->options['public_key'], $this->options['private_key'], $this->options['password'] ) ) {
				$this->errors->add('auth', sprintf(__('Public and Private keys incorrent for %s'), $this->options['username']));
				return false;
			}
		}

		$this->sftp_link = ssh2_sftp($this->link);

		return true;
	}

	function run_command($link, $command, $returnbool = false) {
		$this->debug("run_command();");
		if(!($stream = @ssh2_exec( $link, $command . "; echo \"__COMMAND_FINISHED__\";"))) {
			$this->errors->add('command', sprintf(__('Unable to perform command: %s'), $command));
		} else {
			stream_set_blocking( $stream, true );
			$time_start = time();
			$data = null;
			while( true ) {
				if (strpos($data,"__COMMAND_FINISHED__") !== false){
					break;	//	the command has finshed!
				}
				if( (time()-$time_start) > $this->timeout ){
					$this->errors->add('command', sprintf(__('Connection to the server has timeout after %s seconds.'), $this->timeout));
					unset($this->link);
					unset($this->sftp_link); //	close connections
					return false;
				}
				while( $buf = fread( $stream, strlen($stream) ) )
					$data .= $buf;
			}
			fclose($stream);
			$data = trim(str_replace("__COMMAND_FINISHED__", "", $data));
			if (($returnbool) && ( (int) $data )) {
				return true;
			} elseif (($returnbool) && (! (int) $data )) {
				return false;
			} else {
				return $data;
			}
		}
		return false;
	}

	function debug($text)
	{
		if ($this->debugtest)
		{
			echo "<br/>" . $text . "<br/>";
		}
	}

	function setDefaultPermissions($perm) {
		$this->debug("setDefaultPermissions();");
		if ( $perm )
			$this->permission = $perm;
	}

	function get_contents($file, $type = '', $resumepos = 0 ) {
		$this->debug("get_contents();");
		$tempfile = wp_tempnam( $file );
		if ( ! $tempfile )
			return false;
		if( ! ssh2_scp_recv($this->link, $file, $tempfile) )
			return false;
		$contents = file_get_contents($tempfile);
		unlink($tempfile);
		return $contents;
	}

	function get_contents_array($file) {
		$this->debug("get_contents_array();");
		return explode("\n", $this->get_contents($file));
	}

	function put_contents($file, $contents, $type = '' ) {
		$this->debug("put_contents($file);");
		$tempfile = wp_tempnam( $file );
		$temp = fopen($tempfile, 'w');
		if ( ! $temp )
			return false;
		fwrite($temp, $contents);
		fclose($temp);
		$ret = ssh2_scp_send($this->link, $tempfile, $file, $this->permission);
		unlink($tempfile);
		return $ret;
	}

	function cwd() {
		$this->debug("cwd();");
		$cwd = $this->run_command($this->link, 'pwd');
		if( $cwd )
			$cwd = trailingslashit($cwd);
		return $cwd;
	}

	function chdir($dir) {
		$this->debug("chdir();");
		return $this->run_command($this->link, 'cd ' . $dir, true);
	}

	function chgrp($file, $group, $recursive = false ) {
		$this->debug("chgrp();");
		if ( ! $this->exists($file) )
			return false;
		if ( ! $recursive || ! $this->is_dir($file) )
			return $this->run_command($this->link, sprintf('chgrp %o %s', $mode, $file), true);
		return $this->run_command($this->link, sprintf('chgrp -R %o %s', $mode, $file), true);
	}

	function chmod($file, $mode = false, $recursive = false) {
		$this->debug("chmod();");
		if( ! $mode )
			$mode = $this->permission;
		if( ! $mode )
			return false;
		if ( ! $this->exists($file) )
			return false;
		if ( ! $recursive || ! $this->is_dir($file) )
			return $this->run_command($this->link, sprintf('chmod %o %s', $mode, $file), true);
		return $this->run_command($this->link, sprintf('chmod -R %o %s', $mode, $file), true);
	}

	function chown($file, $owner, $recursive = false ) {
		$this->debug("chown();");
		if ( ! $this->exists($file) )
			return false;
		if ( ! $recursive || ! $this->is_dir($file) )
			return $this->run_command($this->link, sprintf('chown %o %s', $mode, $file), true);
		return $this->run_command($this->link, sprintf('chown -R %o %s', $mode, $file), true);
	}

	function owner($file) {
		$this->debug("owner();");
		$dir = $this->dirlist($file);
		return $dir[$file]['owner'];
	}

	function getchmod($file) {
		$this->debug("getchmod();");
		$dir = $this->dirlist($file);
		return $dir[$file]['permsn'];
	}

	function group($file) {
		$this->debug("group();");
		$dir = $this->dirlist($file);
		return $dir[$file]['group'];
	}

	function copy($source, $destination, $overwrite = false ) {
		$this->debug("copy();");
		if( ! $overwrite && $this->exists($destination) )
			return false;
		$content = $this->get_contents($source);
		if( false === $content)
			return false;
		return $this->put_contents($destination, $content);
	}

	function move($source, $destination, $overwrite = false) {
		$this->debug("move();");
		return @ssh2_sftp_rename($this->link, $source, $destination);
	}

	function delete($file, $recursive = false) {
		$this->debug("delete();");
		if ( $this->is_file($file) )
			return ssh2_sftp_unlink($this->sftp_link, $file);
		if ( ! $recursive )
			 return ssh2_sftp_rmdir($this->sftp_link, $file);
		$filelist = $this->dirlist($file);
		if ( is_array($filelist) ) {
			foreach ( $filelist as $filename => $fileinfo) {
				$this->delete($file . '/' . $filename, $recursive);
			}
		}
		return ssh2_sftp_rmdir($this->sftp_link, $file);
	}

	function exists($file) {
		$this->debug("exists();");
		return $this->run_command($this->link, sprintf('ls -lad %s', $file), true);
	}

	function is_file($file) {
		$this->debug("is_file();");
		//DO NOT RELY ON dirlist()!
		$list = $this->run_command($this->link, sprintf('ls -lad %s', $file));
		$list = $this->parselisting($list);
		if ( ! $list )
			return false;
		else
			return ( !$list['isdir'] && !$list['islink'] ); //ie. not a file or link, yet exists, must be file.
	}

	function is_dir($path) {
		$this->debug("is_dir();");
		//DO NOT RELY ON dirlist()!
		$list = $this->parselisting($this->run_command($this->link, sprintf('ls -lad %s', untrailingslashit($path))));
		if ( ! $list )
			return false;
		else
			return $list['isdir'];
	}

	function is_readable($file) {
		//Not implmented.
	}

	function is_writable($file) {
		//Not implmented.
	}

	function atime($file) {
		//Not implmented.
	}

	function mtime($file) {
		//Not implmented.
	}

	function size($file) {
		//Not implmented.
	}

	function touch($file, $time = 0, $atime = 0) {
		//Not implmented.
	}

	function mkdir($path, $chmod = null, $chown = false, $chgrp = false) {
		$this->debug("mkdir();");
		$path = untrailingslashit($path);
		if( ! ssh2_sftp_mkdir($this->sftp_link, $path, $chmod, true) )
			return false;
		if( $chown )
			$this->chown($path, $chown);
		if( $chgrp )
			$this->chgrp($path, $chgrp);
		return true;
	}

	function rmdir($path, $recursive = false) {
		$this->debug("rmdir();");
		return $this->delete($path, $recursive);
	}

	function parselisting($line) {
	$this->debug("parselisting();");
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
		$this->debug("dirlist();");
		if( $this->is_file($path) ) {
			$limitFile = basename($path);
			$path = trailingslashit(dirname($path));
		} else {
			$limitFile = false;
		}

		$list = $this->run_command($this->link, sprintf('ls -la %s', $path));

		if ( $list === false )
			return false;

		$list = explode("\n", $list);

		$dirlist = array();
		foreach ( (array)$list as $k => $v ) {
			$entry = $this->parselisting($v);
			if ( empty($entry) )
				continue;

			if ( '.' == $entry['name'] || '..' == $entry['name'] )
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
					if ( $recursive )
						$struc['files'] = $this->dirlist($path . '/' . $struc['name'], $incdot, $recursive);
				}
			}
			//File
			$ret[$struc['name']] = $struc;
		}
		return $ret;
	}
	function __destruct() {
		$this->debug("__destruct();");
		if ( $this->link )
			unset($this->link);
		if ( $this->sftp_link )
			unset($this->sftp_link);
	}
}

?>
