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
 * wget https://www.libssh2.org/download/libssh2-0.14.tar.gz
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

	/**
	 * @since 2.7.0
	 * @var resource
	 */
	public $link = false;

	/**
	 * @since 2.7.0
	 * @var resource
	 */
	public $sftp_link;

	/**
	 * @since 2.7.0
	 * @var bool
	 */
	public $keys = false;

	/**
	 * Constructor.
	 *
	 * @since 2.7.0
	 *
	 * @param array $opt
	 */
	public function __construct( $opt = '' ) {
		$this->method = 'ssh2';
		$this->errors = new WP_Error();

		// Check if possible to use ssh2 functions.
		if ( ! extension_loaded( 'ssh2' ) ) {
			$this->errors->add( 'no_ssh2_ext', __( 'The ssh2 PHP extension is not available' ) );
			return;
		}
		if ( ! function_exists( 'stream_get_contents' ) ) {
			$this->errors->add(
				'ssh2_php_requirement',
				sprintf(
					/* translators: %s: stream_get_contents() */
					__( 'The ssh2 PHP extension is available, however, we require the PHP5 function %s' ),
					'<code>stream_get_contents()</code>'
				)
			);
			return;
		}

		// Set defaults:
		if ( empty( $opt['port'] ) ) {
			$this->options['port'] = 22;
		} else {
			$this->options['port'] = $opt['port'];
		}

		if ( empty( $opt['hostname'] ) ) {
			$this->errors->add( 'empty_hostname', __( 'SSH2 hostname is required' ) );
		} else {
			$this->options['hostname'] = $opt['hostname'];
		}

		// Check if the options provided are OK.
		if ( ! empty( $opt['public_key'] ) && ! empty( $opt['private_key'] ) ) {
			$this->options['public_key']  = $opt['public_key'];
			$this->options['private_key'] = $opt['private_key'];

			$this->options['hostkey'] = array( 'hostkey' => 'ssh-rsa' );

			$this->keys = true;
		} elseif ( empty( $opt['username'] ) ) {
			$this->errors->add( 'empty_username', __( 'SSH2 username is required' ) );
		}

		if ( ! empty( $opt['username'] ) ) {
			$this->options['username'] = $opt['username'];
		}

		if ( empty( $opt['password'] ) ) {
			// Password can be blank if we are using keys.
			if ( ! $this->keys ) {
				$this->errors->add( 'empty_password', __( 'SSH2 password is required' ) );
			}
		} else {
			$this->options['password'] = $opt['password'];
		}
	}

	/**
	 * Connects filesystem.
	 *
	 * @since 2.7.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function connect() {
		if ( ! $this->keys ) {
			$this->link = @ssh2_connect( $this->options['hostname'], $this->options['port'] );
		} else {
			$this->link = @ssh2_connect( $this->options['hostname'], $this->options['port'], $this->options['hostkey'] );
		}

		if ( ! $this->link ) {
			$this->errors->add(
				'connect',
				sprintf(
					/* translators: %s: hostname:port */
					__( 'Failed to connect to SSH2 Server %s' ),
					$this->options['hostname'] . ':' . $this->options['port']
				)
			);
			return false;
		}

		if ( ! $this->keys ) {
			if ( ! @ssh2_auth_password( $this->link, $this->options['username'], $this->options['password'] ) ) {
				$this->errors->add(
					'auth',
					sprintf(
						/* translators: %s: Username. */
						__( 'Username/Password incorrect for %s' ),
						$this->options['username']
					)
				);
				return false;
			}
		} else {
			if ( ! @ssh2_auth_pubkey_file( $this->link, $this->options['username'], $this->options['public_key'], $this->options['private_key'], $this->options['password'] ) ) {
				$this->errors->add(
					'auth',
					sprintf(
						/* translators: %s: Username. */
						__( 'Public and Private keys incorrect for %s' ),
						$this->options['username']
					)
				);
				return false;
			}
		}

		$this->sftp_link = ssh2_sftp( $this->link );
		if ( ! $this->sftp_link ) {
			$this->errors->add(
				'connect',
				sprintf(
					/* translators: %s: hostname:port */
					__( 'Failed to initialize a SFTP subsystem session with the SSH2 Server %s' ),
					$this->options['hostname'] . ':' . $this->options['port']
				)
			);
			return false;
		}

		return true;
	}

	/**
	 * Gets the ssh2.sftp PHP stream wrapper path to open for the given file.
	 *
	 * This method also works around a PHP bug where the root directory (/) cannot
	 * be opened by PHP functions, causing a false failure. In order to work around
	 * this, the path is converted to /./ which is semantically the same as /
	 * See https://bugs.php.net/bug.php?id=64169 for more details.
	 *
	 * @since 4.4.0
	 *
	 * @param string $path The File/Directory path on the remote server to return
	 * @return string The ssh2.sftp:// wrapped path to use.
	 */
	public function sftp_path( $path ) {
		if ( '/' === $path ) {
			$path = '/./';
		}
		return 'ssh2.sftp://' . $this->sftp_link . '/' . ltrim( $path, '/' );
	}

	/**
	 * @since 2.7.0
	 *
	 * @param string $command
	 * @param bool $returnbool
	 * @return bool|string True on success, false on failure. String if the command was executed, `$returnbool`
	 *                     is false (default), and data from the resulting stream was retrieved.
	 */
	public function run_command( $command, $returnbool = false ) {
		if ( ! $this->link ) {
			return false;
		}

		$stream = ssh2_exec( $this->link, $command );
		if ( ! $stream ) {
			$this->errors->add(
				'command',
				sprintf(
					/* translators: %s: Command. */
					__( 'Unable to perform command: %s' ),
					$command
				)
			);
		} else {
			stream_set_blocking( $stream, true );
			stream_set_timeout( $stream, FS_TIMEOUT );
			$data = stream_get_contents( $stream );
			fclose( $stream );

			if ( $returnbool ) {
				return ( false === $data ) ? false : '' != trim( $data );
			} else {
				return $data;
			}
		}
		return false;
	}

	/**
	 * Reads entire file into a string.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file Name of the file to read.
	 * @return string|false Read data on success, false if no temporary file could be opened,
	 *                      or if the file couldn't be retrieved.
	 */
	public function get_contents( $file ) {
		return file_get_contents( $this->sftp_path( $file ) );
	}

	/**
	 * Reads entire file into an array.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file Path to the file.
	 * @return array|false File contents in an array on success, false on failure.
	 */
	public function get_contents_array( $file ) {
		return file( $this->sftp_path( $file ) );
	}

	/**
	 * Writes a string to a file.
	 *
	 * @since 2.7.0
	 *
	 * @param string    $file     Remote path to the file where to write the data.
	 * @param string    $contents The data to write.
	 * @param int|false $mode     Optional. The file permissions as octal number, usually 0644.
	 *                            Default false.
	 * @return bool True on success, false on failure.
	 */
	public function put_contents( $file, $contents, $mode = false ) {
		$ret = file_put_contents( $this->sftp_path( $file ), $contents );

		if ( strlen( $contents ) !== $ret ) {
			return false;
		}

		$this->chmod( $file, $mode );

		return true;
	}

	/**
	 * Gets the current working directory.
	 *
	 * @since 2.7.0
	 *
	 * @return string|false The current working directory on success, false on failure.
	 */
	public function cwd() {
		$cwd = ssh2_sftp_realpath( $this->sftp_link, '.' );
		if ( $cwd ) {
			$cwd = trailingslashit( trim( $cwd ) );
		}
		return $cwd;
	}

	/**
	 * Changes current directory.
	 *
	 * @since 2.7.0
	 *
	 * @param string $dir The new current directory.
	 * @return bool True on success, false on failure.
	 */
	public function chdir( $dir ) {
		return $this->run_command( 'cd ' . $dir, true );
	}

	/**
	 * Changes the file group.
	 *
	 * @since 2.7.0
	 *
	 * @param string     $file      Path to the file.
	 * @param string|int $group     A group name or number.
	 * @param bool       $recursive Optional. If set to true, changes file group recursively.
	 *                              Default false.
	 * @return bool True on success, false on failure.
	 */
	public function chgrp( $file, $group, $recursive = false ) {
		if ( ! $this->exists( $file ) ) {
			return false;
		}
		if ( ! $recursive || ! $this->is_dir( $file ) ) {
			return $this->run_command( sprintf( 'chgrp %s %s', escapeshellarg( $group ), escapeshellarg( $file ) ), true );
		}
		return $this->run_command( sprintf( 'chgrp -R %s %s', escapeshellarg( $group ), escapeshellarg( $file ) ), true );
	}

	/**
	 * Changes filesystem permissions.
	 *
	 * @since 2.7.0
	 *
	 * @param string    $file      Path to the file.
	 * @param int|false $mode      Optional. The permissions as octal number, usually 0644 for files,
	 *                             0755 for directories. Default false.
	 * @param bool      $recursive Optional. If set to true, changes file permissions recursively.
	 *                             Default false.
	 * @return bool True on success, false on failure.
	 */
	public function chmod( $file, $mode = false, $recursive = false ) {
		if ( ! $this->exists( $file ) ) {
			return false;
		}

		if ( ! $mode ) {
			if ( $this->is_file( $file ) ) {
				$mode = FS_CHMOD_FILE;
			} elseif ( $this->is_dir( $file ) ) {
				$mode = FS_CHMOD_DIR;
			} else {
				return false;
			}
		}

		if ( ! $recursive || ! $this->is_dir( $file ) ) {
			return $this->run_command( sprintf( 'chmod %o %s', $mode, escapeshellarg( $file ) ), true );
		}
		return $this->run_command( sprintf( 'chmod -R %o %s', $mode, escapeshellarg( $file ) ), true );
	}

	/**
	 * Changes the owner of a file or directory.
	 *
	 * @since 2.7.0
	 *
	 * @param string     $file      Path to the file or directory.
	 * @param string|int $owner     A user name or number.
	 * @param bool       $recursive Optional. If set to true, changes file owner recursively.
	 *                              Default false.
	 * @return bool True on success, false on failure.
	 */
	public function chown( $file, $owner, $recursive = false ) {
		if ( ! $this->exists( $file ) ) {
			return false;
		}
		if ( ! $recursive || ! $this->is_dir( $file ) ) {
			return $this->run_command( sprintf( 'chown %s %s', escapeshellarg( $owner ), escapeshellarg( $file ) ), true );
		}
		return $this->run_command( sprintf( 'chown -R %s %s', escapeshellarg( $owner ), escapeshellarg( $file ) ), true );
	}

	/**
	 * Gets the file owner.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file Path to the file.
	 * @return string|false Username of the owner on success, false on failure.
	 */
	public function owner( $file ) {
		$owneruid = @fileowner( $this->sftp_path( $file ) );
		if ( ! $owneruid ) {
			return false;
		}
		if ( ! function_exists( 'posix_getpwuid' ) ) {
			return $owneruid;
		}
		$ownerarray = posix_getpwuid( $owneruid );
		return $ownerarray['name'];
	}

	/**
	 * Gets the permissions of the specified file or filepath in their octal format.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file Path to the file.
	 * @return string Mode of the file (the last 3 digits).
	 */
	public function getchmod( $file ) {
		return substr( decoct( @fileperms( $this->sftp_path( $file ) ) ), -3 );
	}

	/**
	 * Gets the file's group.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file Path to the file.
	 * @return string|false The group on success, false on failure.
	 */
	public function group( $file ) {
		$gid = @filegroup( $this->sftp_path( $file ) );
		if ( ! $gid ) {
			return false;
		}
		if ( ! function_exists( 'posix_getgrgid' ) ) {
			return $gid;
		}
		$grouparray = posix_getgrgid( $gid );
		return $grouparray['name'];
	}

	/**
	 * Copies a file.
	 *
	 * @since 2.7.0
	 *
	 * @param string    $source      Path to the source file.
	 * @param string    $destination Path to the destination file.
	 * @param bool      $overwrite   Optional. Whether to overwrite the destination file if it exists.
	 *                               Default false.
	 * @param int|false $mode        Optional. The permissions as octal number, usually 0644 for files,
	 *                               0755 for dirs. Default false.
	 * @return bool True on success, false on failure.
	 */
	public function copy( $source, $destination, $overwrite = false, $mode = false ) {
		if ( ! $overwrite && $this->exists( $destination ) ) {
			return false;
		}
		$content = $this->get_contents( $source );
		if ( false === $content ) {
			return false;
		}
		return $this->put_contents( $destination, $content, $mode );
	}

	/**
	 * Moves a file.
	 *
	 * @since 2.7.0
	 *
	 * @param string $source      Path to the source file.
	 * @param string $destination Path to the destination file.
	 * @param bool   $overwrite   Optional. Whether to overwrite the destination file if it exists.
	 *                            Default false.
	 * @return bool True on success, false on failure.
	 */
	public function move( $source, $destination, $overwrite = false ) {
		if ( $this->exists( $destination ) ) {
			if ( $overwrite ) {
				// We need to remove the destination file before we can rename the source.
				$this->delete( $destination, false, 'f' );
			} else {
				// If we're not overwriting, the rename will fail, so return early.
				return false;
			}
		}

		return ssh2_sftp_rename( $this->sftp_link, $source, $destination );
	}

	/**
	 * Deletes a file or directory.
	 *
	 * @since 2.7.0
	 *
	 * @param string       $file      Path to the file or directory.
	 * @param bool         $recursive Optional. If set to true, deletes files and folders recursively.
	 *                                Default false.
	 * @param string|false $type      Type of resource. 'f' for file, 'd' for directory.
	 *                                Default false.
	 * @return bool True on success, false on failure.
	 */
	public function delete( $file, $recursive = false, $type = false ) {
		if ( 'f' == $type || $this->is_file( $file ) ) {
			return ssh2_sftp_unlink( $this->sftp_link, $file );
		}
		if ( ! $recursive ) {
			return ssh2_sftp_rmdir( $this->sftp_link, $file );
		}
		$filelist = $this->dirlist( $file );
		if ( is_array( $filelist ) ) {
			foreach ( $filelist as $filename => $fileinfo ) {
				$this->delete( $file . '/' . $filename, $recursive, $fileinfo['type'] );
			}
		}
		return ssh2_sftp_rmdir( $this->sftp_link, $file );
	}

	/**
	 * Checks if a file or directory exists.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file Path to file or directory.
	 * @return bool Whether $file exists or not.
	 */
	public function exists( $file ) {
		return file_exists( $this->sftp_path( $file ) );
	}

	/**
	 * Checks if resource is a file.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file File path.
	 * @return bool Whether $file is a file.
	 */
	public function is_file( $file ) {
		return is_file( $this->sftp_path( $file ) );
	}

	/**
	 * Checks if resource is a directory.
	 *
	 * @since 2.7.0
	 *
	 * @param string $path Directory path.
	 * @return bool Whether $path is a directory.
	 */
	public function is_dir( $path ) {
		return is_dir( $this->sftp_path( $path ) );
	}

	/**
	 * Checks if a file is readable.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file Path to file.
	 * @return bool Whether $file is readable.
	 */
	public function is_readable( $file ) {
		return is_readable( $this->sftp_path( $file ) );
	}

	/**
	 * Checks if a file or directory is writable.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file Path to file or directory.
	 * @return bool Whether $file is writable.
	 */
	public function is_writable( $file ) {
		// PHP will base its writable checks on system_user === file_owner, not ssh_user === file_owner.
		return true;
	}

	/**
	 * Gets the file's last access time.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file Path to file.
	 * @return int|false Unix timestamp representing last access time, false on failure.
	 */
	public function atime( $file ) {
		return fileatime( $this->sftp_path( $file ) );
	}

	/**
	 * Gets the file modification time.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file Path to file.
	 * @return int|false Unix timestamp representing modification time, false on failure.
	 */
	public function mtime( $file ) {
		return filemtime( $this->sftp_path( $file ) );
	}

	/**
	 * Gets the file size (in bytes).
	 *
	 * @since 2.7.0
	 *
	 * @param string $file Path to file.
	 * @return int|false Size of the file in bytes on success, false on failure.
	 */
	public function size( $file ) {
		return filesize( $this->sftp_path( $file ) );
	}

	/**
	 * Sets the access and modification times of a file.
	 *
	 * Note: Not implemented.
	 *
	 * @since 2.7.0
	 *
	 * @param string $file  Path to file.
	 * @param int    $time  Optional. Modified time to set for file.
	 *                      Default 0.
	 * @param int    $atime Optional. Access time to set for file.
	 *                      Default 0.
	 */
	public function touch( $file, $time = 0, $atime = 0 ) {
		// Not implemented.
	}

	/**
	 * Creates a directory.
	 *
	 * @since 2.7.0
	 *
	 * @param string     $path  Path for new directory.
	 * @param int|false  $chmod Optional. The permissions as octal number (or false to skip chmod).
	 *                          Default false.
	 * @param string|int $chown Optional. A user name or number (or false to skip chown).
	 *                          Default false.
	 * @param string|int $chgrp Optional. A group name or number (or false to skip chgrp).
	 *                          Default false.
	 * @return bool True on success, false on failure.
	 */
	public function mkdir( $path, $chmod = false, $chown = false, $chgrp = false ) {
		$path = untrailingslashit( $path );
		if ( empty( $path ) ) {
			return false;
		}

		if ( ! $chmod ) {
			$chmod = FS_CHMOD_DIR;
		}
		if ( ! ssh2_sftp_mkdir( $this->sftp_link, $path, $chmod, true ) ) {
			return false;
		}
		if ( $chown ) {
			$this->chown( $path, $chown );
		}
		if ( $chgrp ) {
			$this->chgrp( $path, $chgrp );
		}
		return true;
	}

	/**
	 * Deletes a directory.
	 *
	 * @since 2.7.0
	 *
	 * @param string $path      Path to directory.
	 * @param bool   $recursive Optional. Whether to recursively remove files/directories.
	 *                          Default false.
	 * @return bool True on success, false on failure.
	 */
	public function rmdir( $path, $recursive = false ) {
		return $this->delete( $path, $recursive );
	}

	/**
	 * Gets details for files in a directory or a specific file.
	 *
	 * @since 2.7.0
	 *
	 * @param string $path           Path to directory or file.
	 * @param bool   $include_hidden Optional. Whether to include details of hidden ("." prefixed) files.
	 *                               Default true.
	 * @param bool   $recursive      Optional. Whether to recursively include file details in nested directories.
	 *                               Default false.
	 * @return array|false {
	 *     Array of files. False if unable to list directory contents.
	 *
	 *     @type string $name        Name of the file or directory.
	 *     @type string $perms       *nix representation of permissions.
	 *     @type int    $permsn      Octal representation of permissions.
	 *     @type string $owner       Owner name or ID.
	 *     @type int    $size        Size of file in bytes.
	 *     @type int    $lastmodunix Last modified unix timestamp.
	 *     @type mixed  $lastmod     Last modified month (3 letter) and day (without leading 0).
	 *     @type int    $time        Last modified time.
	 *     @type string $type        Type of resource. 'f' for file, 'd' for directory.
	 *     @type mixed  $files       If a directory and $recursive is true, contains another array of files.
	 * }
	 */
	public function dirlist( $path, $include_hidden = true, $recursive = false ) {
		if ( $this->is_file( $path ) ) {
			$limit_file = basename( $path );
			$path       = dirname( $path );
		} else {
			$limit_file = false;
		}

		if ( ! $this->is_dir( $path ) || ! $this->is_readable( $path ) ) {
			return false;
		}

		$ret = array();
		$dir = dir( $this->sftp_path( $path ) );

		if ( ! $dir ) {
			return false;
		}

		while ( false !== ( $entry = $dir->read() ) ) {
			$struc         = array();
			$struc['name'] = $entry;

			if ( '.' == $struc['name'] || '..' == $struc['name'] ) {
				continue; // Do not care about these folders.
			}

			if ( ! $include_hidden && '.' == $struc['name'][0] ) {
				continue;
			}

			if ( $limit_file && $struc['name'] != $limit_file ) {
				continue;
			}

			$struc['perms']       = $this->gethchmod( $path . '/' . $entry );
			$struc['permsn']      = $this->getnumchmodfromh( $struc['perms'] );
			$struc['number']      = false;
			$struc['owner']       = $this->owner( $path . '/' . $entry );
			$struc['group']       = $this->group( $path . '/' . $entry );
			$struc['size']        = $this->size( $path . '/' . $entry );
			$struc['lastmodunix'] = $this->mtime( $path . '/' . $entry );
			$struc['lastmod']     = gmdate( 'M j', $struc['lastmodunix'] );
			$struc['time']        = gmdate( 'h:i:s', $struc['lastmodunix'] );
			$struc['type']        = $this->is_dir( $path . '/' . $entry ) ? 'd' : 'f';

			if ( 'd' == $struc['type'] ) {
				if ( $recursive ) {
					$struc['files'] = $this->dirlist( $path . '/' . $struc['name'], $include_hidden, $recursive );
				} else {
					$struc['files'] = array();
				}
			}

			$ret[ $struc['name'] ] = $struc;
		}
		$dir->close();
		unset( $dir );
		return $ret;
	}
}
