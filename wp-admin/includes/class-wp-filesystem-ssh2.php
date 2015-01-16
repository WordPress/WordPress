<?php
/**
 * WordPress Filesystem Class for implementing SSH2
 *
 * To use this class you must follow these steps for PHP 5.2.6+
 *
 * @contrib http://kevin.vanzonneveld.net/techblog/article/make_ssh_connections_with_php/ - Installation Notes
 *
 * Complie libssh2 (Note: Only 0.14 is officaly working with PHP 5.2.6+ right now, But many users have found the latest versions work)
 *
 * cd /usr/src
 * wget http://surfnet.dl.sourceforge.net/sourceforge/libssh2/libssh2-0.14.tar.gz
 * tar -zxvf libssh2-0.14.tar.gz
 * cd libssh2-0.14/
 * ./configure
 * make all install
 *
 * Note: Do not leave the directory yet!
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
 * Note: as of WordPress 2.8, This utilises the PHP5+ function 'stream_get_contents'
 *
 * @since 2.7.0
 *
 * @package WordPress
 * @subpackage Filesystem
 */
class WP_Filesystem_SSH2 extends WP_Filesystem_Base {

	public $link = false;
	/**
	 * @var resource
	 */
	public $sftp_link;
	public $keys = false;

	public function __construct($opt='') {
		$this->method = 'ssh2';
		$this->errors = new WP_Error();

		//Check if possible to use ssh2 functions.
		if ( ! extension_loaded('ssh2') ) {
			$this->errors->add('no_ssh2_ext', __('The ssh2 PHP extension is not available'));
			return;
		}
		if ( !function_exists('stream_get_contents') ) {
			$this->errors->add('ssh2_php_requirement', __('The ssh2 PHP extension is available, however, we require the PHP5 function <code>stream_get_contents()</code>'));
			return;
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

		// Check if the options provided are OK.
		if ( !empty ($opt['public_key']) && !empty ($opt['private_key']) ) {
			$this->options['public_key'] = $opt['public_key'];
			$this->options['private_key'] = $opt['private_key'];

			$this->options['hostkey'] = array('hostkey' => 'ssh-rsa');

			$this->keys = true;
		} elseif ( empty ($opt['username']) ) {
			$this->errors->add('empty_username', __('SSH2 username is required'));
		}

		if ( !empty($opt['username']) )
			$this->options['username'] = $opt['username'];

		if ( empty ($opt['password']) ) {
			// Password can be blank if we are using keys.
			if ( !$this->keys )
				$this->errors->add('empty_password', __('SSH2 password is required'));
		} else {
			$this->options['password'] = $opt['password'];
		}

	}

	public function connect() {
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
				$this->errors->add('auth', sprintf(__('Public and Private keys incorrect for %s'), $this->options['username']));
				return false;
			}
		}

		$this->sftp_link = ssh2_sftp($this->link);

		return true;
	}

	/**
	 * @param string $command
	 * @param bool $returnbool
	 * @return bool|string
	 */
	public function run_command( $command, $returnbool = false) {

		if ( ! $this->link )
			return false;

		if ( ! ($stream = ssh2_exec($this->link, $command)) ) {
			$this->errors->add('command', sprintf(__('Unable to perform command: %s'), $command));
		} else {
			stream_set_blocking( $stream, true );
			stream_set_timeout( $stream, FS_TIMEOUT );
			$data = stream_get_contents( $stream );
			fclose( $stream );

			if ( $returnbool )
				return ( $data === false ) ? false : '' != trim($data);
			else
				return $data;
		}
		return false;
	}

	/**
	 * @param string $file
	 * @return string|false
	 */
	public function get_contents( $file ) {
		$file = ltrim($file, '/');
		return file_get_contents('ssh2.sftp://' . $this->sftp_link . '/' . $file);
	}

	/**
	 * @param string $file
	 * @return array
	 */
	public function get_contents_array($file) {
		$file = ltrim($file, '/');
		return file('ssh2.sftp://' . $this->sftp_link . '/' . $file);
	}

	/**
	 * @param string $file
	 * @param string $contents
	 * @param bool|int $mode
	 * @return bool
	 */
	public function put_contents($file, $contents, $mode = false ) {
		$ret = file_put_contents( 'ssh2.sftp://' . $this->sftp_link . '/' . ltrim( $file, '/' ), $contents );

		if ( $ret !== strlen( $contents ) )
			return false;

		$this->chmod($file, $mode);

		return true;
	}

	public function cwd() {
		$cwd = $this->run_command('pwd');
		if ( $cwd )
			$cwd = trailingslashit($cwd);
		return $cwd;
	}

	/**
	 * @param string $dir
	 * @return bool|string
	 */
	public function chdir($dir) {
		return $this->run_command('cd ' . $dir, true);
	}

	/**
	 * @param string $file
	 * @param string $group
	 * @param bool $recursive
	 */
	public function chgrp($file, $group, $recursive = false ) {
		if ( ! $this->exists($file) )
			return false;
		if ( ! $recursive || ! $this->is_dir($file) )
			return $this->run_command(sprintf('chgrp %s %s', escapeshellarg($group), escapeshellarg($file)), true);
		return $this->run_command(sprintf('chgrp -R %s %s', escapeshellarg($group), escapeshellarg($file)), true);
	}

	/**
	 * @param string $file
	 * @param int $mode
	 * @param bool $recursive
	 * @return bool|string
	 */
	public function chmod($file, $mode = false, $recursive = false) {
		if ( ! $this->exists($file) )
			return false;

		if ( ! $mode ) {
			if ( $this->is_file($file) )
				$mode = FS_CHMOD_FILE;
			elseif ( $this->is_dir($file) )
				$mode = FS_CHMOD_DIR;
			else
				return false;
		}

		if ( ! $recursive || ! $this->is_dir($file) )
			return $this->run_command(sprintf('chmod %o %s', $mode, escapeshellarg($file)), true);
		return $this->run_command(sprintf('chmod -R %o %s', $mode, escapeshellarg($file)), true);
	}

	/**
	 * Change the ownership of a file / folder.
	 *
	 * @since Unknown
	 *
	 * @param string     $file    Path to the file.
	 * @param string|int $owner   A user name or number.
	 * @param bool       $recursive Optional. If set True changes file owner recursivly. Defaults to False.
	 * @return bool|string Returns true on success or false on failure.
	 */
	public function chown( $file, $owner, $recursive = false ) {
		if ( ! $this->exists($file) )
			return false;
		if ( ! $recursive || ! $this->is_dir($file) )
			return $this->run_command(sprintf('chown %s %s', escapeshellarg($owner), escapeshellarg($file)), true);
		return $this->run_command(sprintf('chown -R %s %s', escapeshellarg($owner), escapeshellarg($file)), true);
	}

	/**
	 * @param string $file
	 * @return string|false
	 */
	public function owner($file) {
		$owneruid = @fileowner('ssh2.sftp://' . $this->sftp_link . '/' . ltrim($file, '/'));
		if ( ! $owneruid )
			return false;
		if ( ! function_exists('posix_getpwuid') )
			return $owneruid;
		$ownerarray = posix_getpwuid($owneruid);
		return $ownerarray['name'];
	}
	/**
	 * @param string $file
	 * @return string
	 */
	public function getchmod($file) {
		return substr( decoct( @fileperms( 'ssh2.sftp://' . $this->sftp_link . '/' . ltrim( $file, '/' ) ) ), -3 );
	}

	/**
	 * @param string $file
	 * @return string|false
	 */
	public function group($file) {
		$gid = @filegroup('ssh2.sftp://' . $this->sftp_link . '/' . ltrim($file, '/'));
		if ( ! $gid )
			return false;
		if ( ! function_exists('posix_getgrgid') )
			return $gid;
		$grouparray = posix_getgrgid($gid);
		return $grouparray['name'];
	}

	/**
	 * @param string $source
	 * @param string $destination
	 * @param bool $overwrite
	 * @param int|bool $mode
	 * @return bool
	 */
	public function copy($source, $destination, $overwrite = false, $mode = false) {
		if ( ! $overwrite && $this->exists($destination) )
			return false;
		$content = $this->get_contents($source);
		if ( false === $content)
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
		return @ssh2_sftp_rename( $this->sftp_link, $source, $destination );
	}

	/**
	 * @param string $file
	 * @param bool $recursive
	 * @param string|bool $type
	 * @return bool
	 */
	public function delete($file, $recursive = false, $type = false) {
		if ( 'f' == $type || $this->is_file($file) )
			return ssh2_sftp_unlink($this->sftp_link, $file);
		if ( ! $recursive )
			 return ssh2_sftp_rmdir($this->sftp_link, $file);
		$filelist = $this->dirlist($file);
		if ( is_array($filelist) ) {
			foreach ( $filelist as $filename => $fileinfo) {
				$this->delete($file . '/' . $filename, $recursive, $fileinfo['type']);
			}
		}
		return ssh2_sftp_rmdir($this->sftp_link, $file);
	}

	/**
	 * @param string $file
	 * @return bool
	 */
	public function exists($file) {
		$file = ltrim($file, '/');
		return file_exists('ssh2.sftp://' . $this->sftp_link . '/' . $file);
	}
	/**
	 * @param string $file
	 * @return bool
	 */
	public function is_file($file) {
		$file = ltrim($file, '/');
		return is_file('ssh2.sftp://' . $this->sftp_link . '/' . $file);
	}
	/**
	 * @param string $path
	 * @return bool
	 */
	public function is_dir($path) {
		$path = ltrim($path, '/');
		return is_dir('ssh2.sftp://' . $this->sftp_link . '/' . $path);
	}
	/**
	 * @param string $file
	 * @return bool
	 */
	public function is_readable($file) {
		$file = ltrim($file, '/');
		return is_readable('ssh2.sftp://' . $this->sftp_link . '/' . $file);
	}
	/**
	 * @param string $file
	 * @return bool
	 */
	public function is_writable($file) {
		$file = ltrim($file, '/');
		return is_writable('ssh2.sftp://' . $this->sftp_link . '/' . $file);
	}
	/**
	 * @param string $file
	 * @return int
	 */
	public function atime($file) {
		$file = ltrim($file, '/');
		return fileatime('ssh2.sftp://' . $this->sftp_link . '/' . $file);
	}

	/**
	 * @param string $file
	 * @return int
	 */
	public function mtime($file) {
		$file = ltrim($file, '/');
		return filemtime('ssh2.sftp://' . $this->sftp_link . '/' . $file);
	}

	/**
	 * @param string $file
	 * @return int
	 */
	public function size($file) {
		$file = ltrim($file, '/');
		return filesize('ssh2.sftp://' . $this->sftp_link . '/' . $file);
	}

	/**
	 * @param string $file
	 * @param int $time
	 * @param int $atime
	 */
	public function touch($file, $time = 0, $atime = 0) {
		//Not implemented.
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

		if ( ! $chmod )
			$chmod = FS_CHMOD_DIR;
		if ( ! ssh2_sftp_mkdir($this->sftp_link, $path, $chmod, true) )
			return false;
		if ( $chown )
			$this->chown($path, $chown);
		if ( $chgrp )
			$this->chgrp($path, $chgrp);
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
	 * @param string $path
	 * @param bool $include_hidden
	 * @param bool $recursive
	 * @return bool|array
	 */
	public function dirlist($path, $include_hidden = true, $recursive = false) {
		if ( $this->is_file($path) ) {
			$limit_file = basename($path);
			$path = dirname($path);
		} else {
			$limit_file = false;
		}

		if ( ! $this->is_dir($path) )
			return false;

		$ret = array();
		$dir = @dir('ssh2.sftp://' . $this->sftp_link .'/' . ltrim($path, '/') );

		if ( ! $dir )
			return false;

		while (false !== ($entry = $dir->read()) ) {
			$struc = array();
			$struc['name'] = $entry;

			if ( '.' == $struc['name'] || '..' == $struc['name'] )
				continue; //Do not care about these folders.

			if ( ! $include_hidden && '.' == $struc['name'][0] )
				continue;

			if ( $limit_file && $struc['name'] != $limit_file )
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
