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
 * @since 2.5.0
 *
 * @see WP_Filesystem_Base
 */
class WP_Filesystem_Direct extends WP_Filesystem_Base {

	/**
	 * Constructor.
	 *
	 * @since 2.5.0
	 *
	 * @param mixed $arg Not used.
	 */
	public function __construct( $arg ) {
		$this->method = 'direct';
		$this->errors = new WP_Error();
	}

	/**
	 * Reads entire file into a string.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Name of the file to read.
	 * @return string|false Read data on success, false on failure.
	 */
	public function get_contents( $file ) {
		return @file_get_contents( $file );
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
		return @file( $file );
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
		$fp = @fopen( $file, 'wb' );

		if ( ! $fp ) {
			return false;
		}

		mbstring_binary_safe_encoding();

		$data_length = strlen( $contents );

		$bytes_written = fwrite( $fp, $contents );

		reset_mbstring_encoding();

		fclose( $fp );

		if ( $data_length !== $bytes_written ) {
			return false;
		}

		$this->chmod( $file, $mode );

		return true;
	}

	/**
	 * Gets the current working directory.
	 *
	 * @since 2.5.0
	 *
	 * @return string|false The current working directory on success, false on failure.
	 */
	public function cwd() {
		return getcwd();
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
		return @chdir( $dir );
	}

	/**
	 * Changes the file group.
	 *
	 * @since 2.5.0
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

		if ( ! $recursive ) {
			return chgrp( $file, $group );
		}

		if ( ! $this->is_dir( $file ) ) {
			return chgrp( $file, $group );
		}

		// Is a directory, and we want recursive.
		$file     = trailingslashit( $file );
		$filelist = $this->dirlist( $file );

		foreach ( $filelist as $filename ) {
			$this->chgrp( $file . $filename, $group, $recursive );
		}

		return true;
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

		if ( ! $recursive || ! $this->is_dir( $file ) ) {
			return chmod( $file, $mode );
		}

		// Is a directory, and we want recursive.
		$file     = trailingslashit( $file );
		$filelist = $this->dirlist( $file );

		foreach ( (array) $filelist as $filename => $filemeta ) {
			$this->chmod( $file . $filename, $mode, $recursive );
		}

		return true;
	}

	/**
	 * Changes the owner of a file or directory.
	 *
	 * @since 2.5.0
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

		if ( ! $recursive ) {
			return chown( $file, $owner );
		}

		if ( ! $this->is_dir( $file ) ) {
			return chown( $file, $owner );
		}

		// Is a directory, and we want recursive.
		$filelist = $this->dirlist( $file );

		foreach ( $filelist as $filename ) {
			$this->chown( $file . '/' . $filename, $owner, $recursive );
		}

		return true;
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
		$owneruid = @fileowner( $file );

		if ( ! $owneruid ) {
			return false;
		}

		if ( ! function_exists( 'posix_getpwuid' ) ) {
			return $owneruid;
		}

		$ownerarray = posix_getpwuid( $owneruid );

		if ( ! $ownerarray ) {
			return false;
		}

		return $ownerarray['name'];
	}

	/**
	 * Gets the permissions of the specified file or filepath in their octal format.
	 *
	 * FIXME does not handle errors in fileperms()
	 *
	 * @since 2.5.0
	 *
	 * @param string $file Path to the file.
	 * @return string Mode of the file (the last 3 digits).
	 */
	public function getchmod( $file ) {
		return substr( decoct( @fileperms( $file ) ), -3 );
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
		$gid = @filegroup( $file );

		if ( ! $gid ) {
			return false;
		}

		if ( ! function_exists( 'posix_getgrgid' ) ) {
			return $gid;
		}

		$grouparray = posix_getgrgid( $gid );

		if ( ! $grouparray ) {
			return false;
		}

		return $grouparray['name'];
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

		$rtval = copy( $source, $destination );

		if ( $mode ) {
			$this->chmod( $destination, $mode );
		}

		return $rtval;
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
	 * @param string $source      Path to the source file.
	 * @param string $destination Path to the destination file.
	 * @param bool   $overwrite   Optional. Whether to overwrite the destination file if it exists.
	 *                            Default false.
	 * @return bool True on success, false on failure.
	 */
	public function move( $source, $destination, $overwrite = false ) {
		if ( ! $overwrite && $this->exists( $destination ) ) {
			return false;
		}

		if ( $overwrite && $this->exists( $destination ) && ! $this->delete( $destination, true ) ) {
			// Can't overwrite if the destination couldn't be deleted.
			return false;
		}

		// Try using rename first. if that fails (for example, source is read only) try copy.
		if ( @rename( $source, $destination ) ) {
			return true;
		}

		// Backward compatibility: Only fall back to `::copy()` for single files.
		if ( $this->is_file( $source ) && $this->copy( $source, $destination, $overwrite ) && $this->exists( $destination ) ) {
			$this->delete( $source );

			return true;
		} else {
			return false;
		}
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
			// Some filesystems report this as /, which can cause non-expected recursive deletion of all files in the filesystem.
			return false;
		}

		$file = str_replace( '\\', '/', $file ); // For Win32, occasional problems deleting files otherwise.

		if ( 'f' === $type || $this->is_file( $file ) ) {
			return @unlink( $file );
		}

		if ( ! $recursive && $this->is_dir( $file ) ) {
			return @rmdir( $file );
		}

		// At this point it's a folder, and we're in recursive mode.
		$file     = trailingslashit( $file );
		$filelist = $this->dirlist( $file, true );

		$retval = true;

		if ( is_array( $filelist ) ) {
			foreach ( $filelist as $filename => $fileinfo ) {
				if ( ! $this->delete( $file . $filename, $recursive, $fileinfo['type'] ) ) {
					$retval = false;
				}
			}
		}

		if ( file_exists( $file ) && ! @rmdir( $file ) ) {
			$retval = false;
		}

		return $retval;
	}

	/**
	 * Checks if a file or directory exists.
	 *
	 * @since 2.5.0
	 *
	 * @param string $path Path to file or directory.
	 * @return bool Whether $path exists or not.
	 */
	public function exists( $path ) {
		return @file_exists( $path );
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
		return @is_file( $file );
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
		return @is_dir( $path );
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
		return @is_readable( $file );
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
		return @is_writable( $path );
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
		return @fileatime( $file );
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
		return @filemtime( $file );
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
		return @filesize( $file );
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
		if ( 0 === $time ) {
			$time = time();
		}

		if ( 0 === $atime ) {
			$atime = time();
		}

		return touch( $file, $time, $atime );
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
		// Safe mode fails with a trailing slash under certain PHP versions.
		$path = untrailingslashit( $path );

		if ( empty( $path ) ) {
			return false;
		}

		if ( ! $chmod ) {
			$chmod = FS_CHMOD_DIR;
		}

		if ( ! @mkdir( $path ) ) {
			return false;
		}

		$this->chmod( $path, $chmod );

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
	 *     Array of files. False if unable to list directory contents.
	 *
	 *     @type string $name        Name of the file or directory.
	 *     @type string $perms       *nix representation of permissions.
	 *     @type string $permsn      Octal representation of permissions.
	 *     @type string $owner       Owner name or ID.
	 *     @type int    $size        Size of file in bytes.
	 *     @type int    $lastmodunix Last modified unix timestamp.
	 *     @type mixed  $lastmod     Last modified month (3 letter) and day (without leading 0).
	 *     @type int    $time        Last modified time.
	 *     @type string $type        Type of resource. 'f' for file, 'd' for directory.
	 *     @type mixed  $files       If a directory and `$recursive` is true, contains another array of files.
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

		$dir = dir( $path );

		if ( ! $dir ) {
			return false;
		}

		$path = trailingslashit( $path );
		$ret  = array();

		while ( false !== ( $entry = $dir->read() ) ) {
			$struc         = array();
			$struc['name'] = $entry;

			if ( '.' === $struc['name'] || '..' === $struc['name'] ) {
				continue;
			}

			if ( ! $include_hidden && '.' === $struc['name'][0] ) {
				continue;
			}

			if ( $limit_file && $struc['name'] !== $limit_file ) {
				continue;
			}

			$struc['perms']       = $this->gethchmod( $path . $entry );
			$struc['permsn']      = $this->getnumchmodfromh( $struc['perms'] );
			$struc['number']      = false;
			$struc['owner']       = $this->owner( $path . $entry );
			$struc['group']       = $this->group( $path . $entry );
			$struc['size']        = $this->size( $path . $entry );
			$struc['lastmodunix'] = $this->mtime( $path . $entry );
			$struc['lastmod']     = gmdate( 'M j', $struc['lastmodunix'] );
			$struc['time']        = gmdate( 'h:i:s', $struc['lastmodunix'] );
			$struc['type']        = $this->is_dir( $path . $entry ) ? 'd' : 'f';

			if ( 'd' === $struc['type'] ) {
				if ( $recursive ) {
					$struc['files'] = $this->dirlist( $path . $struc['name'], $include_hidden, $recursive );
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
