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
 *
 * @see WP_Filesystem_Base
 */
class WP_Filesystem_ftpsockets extends WP_Filesystem_Base {

	/**
	 * @since 2.5.0
	 * @var ftp
	 */
	public $ftp;

	/**
	 * Constructor.
	 *
	 * @since 2.5.0
	 *
	 * @param array $opt
	 */
	public function __construct( $opt = '' ) {
		$this->method = 'ftpsockets';
		$this->errors = new WP_Error();

		// Check if possible to use ftp functions.
		if ( ! require_once ABSPATH . 'wp-admin/includes/class-ftp.php' ) {
			return;
		}

		$this->ftp = new ftp();

		if ( empty( $opt['port'] ) ) {
			$this->options['port'] = 21;
		} else {
			$this->options['port'] = (int) $opt['port'];
		}

		if ( empty( $opt['hostname'] ) ) {
			$this->errors->add( 'empty_hostname', __( 'FTP hostname is required' ) );
		} else {
			$this->options['hostname'] = $opt['hostname'];
		}

		// Check if the options provided are OK.
		if ( empty( $opt['username'] ) ) {
			$this->errors->add( 'empty_username', __( 'FTP username is required' ) );
		} else {
			$this->options['username'] = $opt['username'];
		}

		if ( empty( $opt['password'] ) ) {
			$this->errors->add( 'empty_password', __( 'FTP password is required' ) );
		} else {
			$this->options['password'] = $opt['password'];
		}
	}

	/**
	 * Connects filesystem.
	 *
	 * @since 2.5.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function connect() {
		if ( ! $this->ftp ) {
			return false;
		}

		$this->ftp->setTimeout( FS_CONNECT_TIMEOUT );

		if ( ! $this->ftp->SetServer( $this->options['hostname'], $this->options['port'] ) ) {
			$this->errors->add(
				'connect',
				sprintf(
					/* translators: %s: hostname:port */
					__( 'Failed to connect to FTP Server %s' ),
					$this->options['hostname'] . ':' . $this->options['port']
				)
			);

			return false;
		}

		if ( ! $this->ftp->connect() ) {
			$this->errors->add(
				'connect',
				sprintf(
					/* translators: %s: hostname:port */
					__( 'Failed to connect to FTP Server %s' ),
					$this->options['hostname'] . ':' . $this->options['port']
				)
			);

			return false;
		}

		if ( ! $this->ftp->login( $this->options['username'], $this->options['password'] ) ) {
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

		$this->ftp->SetType( FTP_BINARY );
		$this->ftp->Passive( true );
		$this->ftp->setTimeout( FS_TIMEOUT );

		return true;
	}

	/**
	 * Reads entire file into a string.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Name of the file to read.
	 * @return string|false Read data on success, false if no temporary file could be opened,
	 *                      or if the file couldn't be retrieved.
	 */
	public function get_contents( $file ) {
		if ( ! $this->exists( $file ) ) {
			return false;
		}

		$tempfile   = wp_tempnam( $file );
		$temphandle = fopen( $tempfile, 'w+' );

		if ( ! $temphandle ) {
			unlink( $tempfile );
			return false;
		}

		mbstring_binary_safe_encoding();

		if ( ! $this->ftp->fget( $temphandle, $file ) ) {
			fclose( $temphandle );
			unlink( $tempfile );

			reset_mbstring_encoding();

			return ''; // Blank document. File does exist, it's just blank.
		}

		reset_mbstring_encoding();

		fseek( $temphandle, 0 ); // Skip back to the start of the file being written to.
		$contents = '';

		while ( ! feof( $temphandle ) ) {
			$contents .= fread( $temphandle, 8 * KB_IN_BYTES );
		}

		fclose( $temphandle );
		unlink( $tempfile );

		return $contents;
	}

	/**
	 * Reads entire file into an array.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Path to the file.
	 * @return array|false File contents in an array on success, false on failure.
	 */
	public function get_contents_array( $file ) {
		return explode( "\n", $this->get_contents( $file ) );
	}

	/**
	 * Writes a string to a file.
	 *
	 * @since 2.5.0
	 *
	 * @param string    $file     Remote path to the file where to write the data.
	 * @param string    $contents The data to write.
	 * @param int|false $mode     Optional. The file permissions as octal number, usually 0644.
	 *                            Default false.
	 * @return bool True on success, false on failure.
	 */
	public function put_contents( $file, $contents, $mode = false ) {
		$tempfile   = wp_tempnam( $file );
		$temphandle = @fopen( $tempfile, 'w+' );

		if ( ! $temphandle ) {
			unlink( $tempfile );
			return false;
		}

		// The FTP class uses string functions internally during file download/upload.
		mbstring_binary_safe_encoding();

		$bytes_written = fwrite( $temphandle, $contents );

		if ( false === $bytes_written || strlen( $contents ) !== $bytes_written ) {
			fclose( $temphandle );
			unlink( $tempfile );

			reset_mbstring_encoding();

			return false;
		}

		fseek( $temphandle, 0 ); // Skip back to the start of the file being written to.

		$ret = $this->ftp->fput( $file, $temphandle );

		reset_mbstring_encoding();

		fclose( $temphandle );
		unlink( $tempfile );

		$this->chmod( $file, $mode );

		return $ret;
	}

	/**
	 * Gets the current working directory.
	 *
	 * @since 2.5.0
	 *
	 * @return string|false The current working directory on success, false on failure.
	 */
	public function cwd() {
		$cwd = $this->ftp->pwd();

		if ( $cwd ) {
			$cwd = trailingslashit( $cwd );
		}

		return $cwd;
	}

	/**
	 * Changes current directory.
	 *
	 * @since 2.5.0
	 *
	 * @param string $dir The new current directory.
	 * @return bool True on success, false on failure.
	 */
	public function chdir( $dir ) {
		return $this->ftp->chdir( $dir );
	}

	/**
	 * Changes filesystem permissions.
	 *
	 * @since 2.5.0
	 *
	 * @param string    $file      Path to the file.
	 * @param int|false $mode      Optional. The permissions as octal number, usually 0644 for files,
	 *                             0755 for directories. Default false.
	 * @param bool      $recursive Optional. If set to true, changes file permissions recursively.
	 *                             Default false.
	 * @return bool True on success, false on failure.
	 */
	public function chmod( $file, $mode = false, $recursive = false ) {
		if ( ! $mode ) {
			if ( $this->is_file( $file ) ) {
				$mode = FS_CHMOD_FILE;
			} elseif ( $this->is_dir( $file ) ) {
				$mode = FS_CHMOD_DIR;
			} else {
				return false;
			}
		}

		// chmod any sub-objects if recursive.
		if ( $recursive && $this->is_dir( $file ) ) {
			$filelist = $this->dirlist( $file );

			foreach ( (array) $filelist as $filename => $filemeta ) {
				$this->chmod( $file . '/' . $filename, $mode, $recursive );
			}
		}

		// chmod the file or directory.
		return $this->ftp->chmod( $file, $mode );
	}

	/**
	 * Gets the file owner.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Path to the file.
	 * @return string|false Username of the owner on success, false on failure.
	 */
	public function owner( $file ) {
		$dir = $this->dirlist( $file );

		return $dir[ $file ]['owner'];
	}

	/**
	 * Gets the permissions of the specified file or filepath in their octal format.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Path to the file.
	 * @return string Mode of the file (the last 3 digits).
	 */
	public function getchmod( $file ) {
		$dir = $this->dirlist( $file );

		return $dir[ $file ]['permsn'];
	}

	/**
	 * Gets the file's group.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Path to the file.
	 * @return string|false The group on success, false on failure.
	 */
	public function group( $file ) {
		$dir = $this->dirlist( $file );

		return $dir[ $file ]['group'];
	}

	/**
	 * Copies a file.
	 *
	 * @since 2.5.0
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
	 * Moves a file or directory.
	 *
	 * After moving files or directories, OPcache will need to be invalidated.
	 *
	 * If moving a directory fails, `copy_dir()` can be used for a recursive copy.
	 *
	 * Use `move_dir()` for moving directories with OPcache invalidation and a
	 * fallback to `copy_dir()`.
	 *
	 * @since 2.5.0
	 *
	 * @param string $source      Path to the source file or directory.
	 * @param string $destination Path to the destination file or directory.
	 * @param bool   $overwrite   Optional. Whether to overwrite the destination if it exists.
	 *                            Default false.
	 * @return bool True on success, false on failure.
	 */
	public function move( $source, $destination, $overwrite = false ) {
		return $this->ftp->rename( $source, $destination );
	}

	/**
	 * Deletes a file or directory.
	 *
	 * @since 2.5.0
	 *
	 * @param string       $file      Path to the file or directory.
	 * @param bool         $recursive Optional. If set to true, deletes files and folders recursively.
	 *                                Default false.
	 * @param string|false $type      Type of resource. 'f' for file, 'd' for directory.
	 *                                Default false.
	 * @return bool True on success, false on failure.
	 */
	public function delete( $file, $recursive = false, $type = false ) {
		if ( empty( $file ) ) {
			return false;
		}

		if ( 'f' === $type || $this->is_file( $file ) ) {
			return $this->ftp->delete( $file );
		}

		if ( ! $recursive ) {
			return $this->ftp->rmdir( $file );
		}

		return $this->ftp->mdel( $file );
	}

	/**
	 * Checks if a file or directory exists.
	 *
	 * @since 2.5.0
	 * @since 6.3.0 Returns false for an empty path.
	 *
	 * @param string $path Path to file or directory.
	 * @return bool Whether $path exists or not.
	 */
	public function exists( $path ) {
		/*
		 * Check for empty path. If ftp::nlist() receives an empty path,
		 * it checks the current working directory and may return true.
		 *
		 * See https://core.trac.wordpress.org/ticket/33058.
		 */
		if ( '' === $path ) {
			return false;
		}

		$list = $this->ftp->nlist( $path );

		if ( empty( $list ) && $this->is_dir( $path ) ) {
			return true; // File is an empty directory.
		}

		return ! empty( $list ); // Empty list = no file, so invert.
		// Return $this->ftp->is_exists($file); has issues with ABOR+426 responses on the ncFTPd server.
	}

	/**
	 * Checks if resource is a file.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file File path.
	 * @return bool Whether $file is a file.
	 */
	public function is_file( $file ) {
		if ( $this->is_dir( $file ) ) {
			return false;
		}

		if ( $this->exists( $file ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if resource is a directory.
	 *
	 * @since 2.5.0
	 *
	 * @param string $path Directory path.
	 * @return bool Whether $path is a directory.
	 */
	public function is_dir( $path ) {
		$cwd = $this->cwd();

		if ( $this->chdir( $path ) ) {
			$this->chdir( $cwd );
			return true;
		}

		return false;
	}

	/**
	 * Checks if a file is readable.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Path to file.
	 * @return bool Whether $file is readable.
	 */
	public function is_readable( $file ) {
		return true;
	}

	/**
	 * Checks if a file or directory is writable.
	 *
	 * @since 2.5.0
	 *
	 * @param string $path Path to file or directory.
	 * @return bool Whether $path is writable.
	 */
	public function is_writable( $path ) {
		return true;
	}

	/**
	 * Gets the file's last access time.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Path to file.
	 * @return int|false Unix timestamp representing last access time, false on failure.
	 */
	public function atime( $file ) {
		return false;
	}

	/**
	 * Gets the file modification time.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Path to file.
	 * @return int|false Unix timestamp representing modification time, false on failure.
	 */
	public function mtime( $file ) {
		return $this->ftp->mdtm( $file );
	}

	/**
	 * Gets the file size (in bytes).
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Path to file.
	 * @return int|false Size of the file in bytes on success, false on failure.
	 */
	public function size( $file ) {
		return $this->ftp->filesize( $file );
	}

	/**
	 * Sets the access and modification times of a file.
	 *
	 * Note: If $file doesn't exist, it will be created.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file  Path to file.
	 * @param int    $time  Optional. Modified time to set for file.
	 *                      Default 0.
	 * @param int    $atime Optional. Access time to set for file.
	 *                      Default 0.
	 * @return bool True on success, false on failure.
	 */
	public function touch( $file, $time = 0, $atime = 0 ) {
		return false;
	}

	/**
	 * Creates a directory.
	 *
	 * @since 2.5.0
	 *
	 * @param string           $path  Path for new directory.
	 * @param int|false        $chmod Optional. The permissions as octal number (or false to skip chmod).
	 *                                Default false.
	 * @param string|int|false $chown Optional. A user name or number (or false to skip chown).
	 *                                Default false.
	 * @param string|int|false $chgrp Optional. A group name or number (or false to skip chgrp).
	 *                                Default false.
	 * @return bool True on success, false on failure.
	 */
	public function mkdir( $path, $chmod = false, $chown = false, $chgrp = false ) {
		$path = untrailingslashit( $path );

		if ( empty( $path ) ) {
			return false;
		}

		if ( ! $this->ftp->mkdir( $path ) ) {
			return false;
		}

		if ( ! $chmod ) {
			$chmod = FS_CHMOD_DIR;
		}

		$this->chmod( $path, $chmod );

		return true;
	}

	/**
	 * Deletes a directory.
	 *
	 * @since 2.5.0
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
	 * @since 2.5.0
	 *
	 * @param string $path           Path to directory or file.
	 * @param bool   $include_hidden Optional. Whether to include details of hidden ("." prefixed) files.
	 *                               Default true.
	 * @param bool   $recursive      Optional. Whether to recursively include file details in nested directories.
	 *                               Default false.
	 * @return array|false {
	 *     Array of arrays containing file information. False if unable to list directory contents.
	 *
	 *     @type array $0... {
	 *         Array of file information. Note that some elements may not be available on all filesystems.
	 *
	 *         @type string           $name        Name of the file or directory.
	 *         @type string           $perms       *nix representation of permissions.
	 *         @type string           $permsn      Octal representation of permissions.
	 *         @type int|string|false $number      File number. May be a numeric string. False if not available.
	 *         @type string|false     $owner       Owner name or ID, or false if not available.
	 *         @type string|false     $group       File permissions group, or false if not available.
	 *         @type int|string|false $size        Size of file in bytes. May be a numeric string.
	 *                                             False if not available.
	 *         @type int|string|false $lastmodunix Last modified unix timestamp. May be a numeric string.
	 *                                             False if not available.
	 *         @type string|false     $lastmod     Last modified month (3 letters) and day (without leading 0), or
	 *                                             false if not available.
	 *         @type string|false     $time        Last modified time, or false if not available.
	 *         @type string           $type        Type of resource. 'f' for file, 'd' for directory, 'l' for link.
	 *         @type array|false      $files       If a directory and `$recursive` is true, contains another array of
	 *                                             files. False if unable to list directory contents.
	 *     }
	 * }
	 */
	public function dirlist( $path = '.', $include_hidden = true, $recursive = false ) {
		if ( $this->is_file( $path ) ) {
			$limit_file = basename( $path );
			$path       = dirname( $path ) . '/';
		} else {
			$limit_file = false;
		}

		mbstring_binary_safe_encoding();

		$list = $this->ftp->dirlist( $path );

		if ( empty( $list ) && ! $this->exists( $path ) ) {

			reset_mbstring_encoding();

			return false;
		}

		$path = trailingslashit( $path );
		$ret  = array();

		foreach ( $list as $struc ) {

			if ( '.' === $struc['name'] || '..' === $struc['name'] ) {
				continue;
			}

			if ( ! $include_hidden && '.' === $struc['name'][0] ) {
				continue;
			}

			if ( $limit_file && $struc['name'] !== $limit_file ) {
				continue;
			}

			if ( 'd' === $struc['type'] ) {
				if ( $recursive ) {
					$struc['files'] = $this->dirlist( $path . $struc['name'], $include_hidden, $recursive );
				} else {
					$struc['files'] = array();
				}
			}

			// Replace symlinks formatted as "source -> target" with just the source name.
			if ( $struc['islink'] ) {
				$struc['name'] = preg_replace( '/(\s*->\s*.*)$/', '', $struc['name'] );
			}

			// Add the octal representation of the file permissions.
			$struc['permsn'] = $this->getnumchmodfromh( $struc['perms'] );

			$ret[ $struc['name'] ] = $struc;
		}

		reset_mbstring_encoding();

		return $ret;
	}

	/**
	 * Destructor.
	 *
	 * @since 2.5.0
	 */
	public function __destruct() {
		$this->ftp->quit();
	}
}
