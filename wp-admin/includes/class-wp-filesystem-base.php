<?php
/**
 * Base WordPress Filesystem
 *
 * @package WordPress
 * @subpackage Filesystem
 */

/**
 * Base WordPress Filesystem class for which Filesystem implementations extend
 *
 * @since 2.5.0
 */
class WP_Filesystem_Base {
	/**
	 * Whether to display debug data for the connection.
	 *
	 * @since 2.5.0
	 * @var bool
	 */
	public $verbose = false;

	/**
	 * Cached list of local filepaths to mapped remote filepaths.
	 *
	 * @since 2.7.0
	 * @var array
	 */
	public $cache = array();

	/**
	 * The Access method of the current connection, Set automatically.
	 *
	 * @since 2.5.0
	 * @var string
	 */
	public $method = '';

	/**
	 * @var WP_Error
	 */
	public $errors = null;

	/**
	 */
	public $options = array();

	/**
	 * Return the path on the remote filesystem of ABSPATH.
	 *
	 * @since 2.7.0
	 *
	 * @return string The location of the remote path.
	 */
	public function abspath() {
		$folder = $this->find_folder(ABSPATH);
		// Perhaps the FTP folder is rooted at the WordPress install, Check for wp-includes folder in root, Could have some false positives, but rare.
		if ( ! $folder && $this->is_dir( '/' . WPINC ) )
			$folder = '/';
		return $folder;
	}

	/**
	 * Return the path on the remote filesystem of WP_CONTENT_DIR.
	 *
	 * @since 2.7.0
	 *
	 * @return string The location of the remote path.
	 */
	public function wp_content_dir() {
		return $this->find_folder(WP_CONTENT_DIR);
	}

	/**
	 * Return the path on the remote filesystem of WP_PLUGIN_DIR.
	 *
	 * @since 2.7.0
	 *
	 * @return string The location of the remote path.
	 */
	public function wp_plugins_dir() {
		return $this->find_folder(WP_PLUGIN_DIR);
	}

	/**
	 * Return the path on the remote filesystem of the Themes Directory.
	 *
	 * @since 2.7.0
	 *
	 * @param string $theme The Theme stylesheet or template for the directory.
	 * @return string The location of the remote path.
	 */
	public function wp_themes_dir( $theme = false ) {
		$theme_root = get_theme_root( $theme );

		// Account for relative theme roots
		if ( '/themes' == $theme_root || ! is_dir( $theme_root ) )
			$theme_root = WP_CONTENT_DIR . $theme_root;

		return $this->find_folder( $theme_root );
	}

	/**
	 * Return the path on the remote filesystem of WP_LANG_DIR.
	 *
	 * @since 3.2.0
	 *
	 * @return string The location of the remote path.
	 */
	public function wp_lang_dir() {
		return $this->find_folder(WP_LANG_DIR);
	}

	/**
	 * Locate a folder on the remote filesystem.
	 *
	 * @since 2.5.0
	 * @deprecated 2.7.0 use WP_Filesystem::abspath() or WP_Filesystem::wp_*_dir() instead.
	 * @see WP_Filesystem::abspath()
	 * @see WP_Filesystem::wp_content_dir()
	 * @see WP_Filesystem::wp_plugins_dir()
	 * @see WP_Filesystem::wp_themes_dir()
	 * @see WP_Filesystem::wp_lang_dir()
	 *
	 * @param string $base The folder to start searching from.
	 * @param bool   $echo True to display debug information.
	 *                     Default false.
	 * @return string The location of the remote path.
	 */
	public function find_base_dir( $base = '.', $echo = false ) {
		_deprecated_function(__FUNCTION__, '2.7.0', 'WP_Filesystem::abspath() or WP_Filesystem::wp_*_dir()' );
		$this->verbose = $echo;
		return $this->abspath();
	}

	/**
	 * Locate a folder on the remote filesystem.
	 *
	 * @since 2.5.0
	 * @deprecated 2.7.0 use WP_Filesystem::abspath() or WP_Filesystem::wp_*_dir() methods instead.
	 * @see WP_Filesystem::abspath()
	 * @see WP_Filesystem::wp_content_dir()
	 * @see WP_Filesystem::wp_plugins_dir()
	 * @see WP_Filesystem::wp_themes_dir()
	 * @see WP_Filesystem::wp_lang_dir()
	 *
	 * @param string $base The folder to start searching from.
	 * @param bool   $echo True to display debug information.
	 * @return string The location of the remote path.
	 */
	public function get_base_dir( $base = '.', $echo = false ) {
		_deprecated_function(__FUNCTION__, '2.7.0', 'WP_Filesystem::abspath() or WP_Filesystem::wp_*_dir()' );
		$this->verbose = $echo;
		return $this->abspath();
	}

	/**
	 * Locate a folder on the remote filesystem.
	 *
	 * Assumes that on Windows systems, Stripping off the Drive
	 * letter is OK Sanitizes \\ to / in windows filepaths.
	 *
	 * @since 2.7.0
	 *
	 * @param string $folder the folder to locate.
	 * @return string|false The location of the remote path, false on failure.
	 */
	public function find_folder( $folder ) {
		if ( isset( $this->cache[ $folder ] ) )
			return $this->cache[ $folder ];

		if ( stripos($this->method, 'ftp') !== false ) {
			$constant_overrides = array(
				'FTP_BASE' => ABSPATH,
				'FTP_CONTENT_DIR' => WP_CONTENT_DIR,
				'FTP_PLUGIN_DIR' => WP_PLUGIN_DIR,
				'FTP_LANG_DIR' => WP_LANG_DIR
			);

			// Direct matches ( folder = CONSTANT/ )
			foreach ( $constant_overrides as $constant => $dir ) {
				if ( ! defined( $constant ) )
					continue;
				if ( $folder === $dir )
					return trailingslashit( constant( $constant ) );
			}

			// Prefix Matches ( folder = CONSTANT/subdir )
			foreach ( $constant_overrides as $constant => $dir ) {
				if ( ! defined( $constant ) )
					continue;
				if ( 0 === stripos( $folder, $dir ) ) { // $folder starts with $dir
					$potential_folder = preg_replace( '#^' . preg_quote( $dir, '#' ) . '/#i', trailingslashit( constant( $constant ) ), $folder );
					$potential_folder = trailingslashit( $potential_folder );

					if ( $this->is_dir( $potential_folder ) ) {
						$this->cache[ $folder ] = $potential_folder;
						return $potential_folder;
					}
				}
			}
		} elseif ( 'direct' == $this->method ) {
			$folder = str_replace('\\', '/', $folder); // Windows path sanitisation
			return trailingslashit($folder);
		}

		$folder = preg_replace('|^([a-z]{1}):|i', '', $folder); // Strip out windows drive letter if it's there.
		$folder = str_replace('\\', '/', $folder); // Windows path sanitisation

		if ( isset($this->cache[ $folder ] ) )
			return $this->cache[ $folder ];

		if ( $this->exists($folder) ) { // Folder exists at that absolute path.
			$folder = trailingslashit($folder);
			$this->cache[ $folder ] = $folder;
			return $folder;
		}
		if ( $return = $this->search_for_folder($folder) )
			$this->cache[ $folder ] = $return;
		return $return;
	}

	/**
	 * Locate a folder on the remote filesystem.
	 *
	 * Expects Windows sanitized path.
	 *
	 * @since 2.7.0
	 *
	 * @param string $folder The folder to locate.
	 * @param string $base   The folder to start searching from.
	 * @param bool   $loop   If the function has recursed, Internal use only.
	 * @return string|false The location of the remote path, false to cease looping.
	 */
	public function search_for_folder( $folder, $base = '.', $loop = false ) {
		if ( empty( $base ) || '.' == $base )
			$base = trailingslashit($this->cwd());

		$folder = untrailingslashit($folder);

		if ( $this->verbose ) {
			/* translators: 1: folder to locate, 2: folder to start searching from */
			printf( "\n" . __( 'Looking for %1$s in %2$s' ) . "<br/>\n", $folder, $base );
		}

		$folder_parts = explode('/', $folder);
		$folder_part_keys = array_keys( $folder_parts );
		$last_index = array_pop( $folder_part_keys );
		$last_path = $folder_parts[ $last_index ];

		$files = $this->dirlist( $base );

		foreach ( $folder_parts as $index => $key ) {
			if ( $index == $last_index )
				continue; // We want this to be caught by the next code block.

			/*
			 * Working from /home/ to /user/ to /wordpress/ see if that file exists within
			 * the current folder, If it's found, change into it and follow through looking
			 * for it. If it cant find WordPress down that route, it'll continue onto the next
			 * folder level, and see if that matches, and so on. If it reaches the end, and still
			 * cant find it, it'll return false for the entire function.
			 */
			if ( isset($files[ $key ]) ){

				// Let's try that folder:
				$newdir = trailingslashit(path_join($base, $key));
				if ( $this->verbose ) {
					/* translators: %s: directory name */
					printf( "\n" . __( 'Changing to %s' ) . "<br/>\n", $newdir );
				}

				// Only search for the remaining path tokens in the directory, not the full path again.
				$newfolder = implode( '/', array_slice( $folder_parts, $index + 1 ) );
				if ( $ret = $this->search_for_folder( $newfolder, $newdir, $loop) )
					return $ret;
			}
		}

		// Only check this as a last resort, to prevent locating the incorrect install.
		// All above procedures will fail quickly if this is the right branch to take.
		if (isset( $files[ $last_path ] ) ) {
			if ( $this->verbose ) {
				/* translators: %s: directory name */
				printf( "\n" . __( 'Found %s' ) . "<br/>\n",  $base . $last_path );
			}
			return trailingslashit($base . $last_path);
		}

		// Prevent this function from looping again.
		// No need to proceed if we've just searched in /
		if ( $loop || '/' == $base )
			return false;

		// As an extra last resort, Change back to / if the folder wasn't found.
		// This comes into effect when the CWD is /home/user/ but WP is at /var/www/....
		return $this->search_for_folder( $folder, '/', true );

	}

	/**
	 * Return the *nix-style file permissions for a file.
	 *
	 * From the PHP documentation page for fileperms().
	 *
	 * @link https://secure.php.net/manual/en/function.fileperms.php
	 *
	 * @since 2.5.0
	 *
	 * @param string $file String filename.
	 * @return string The *nix-style representation of permissions.
	 */
	public function gethchmod( $file ){
		$perms = intval( $this->getchmod( $file ), 8 );
		if (($perms & 0xC000) == 0xC000) // Socket
			$info = 's';
		elseif (($perms & 0xA000) == 0xA000) // Symbolic Link
			$info = 'l';
		elseif (($perms & 0x8000) == 0x8000) // Regular
			$info = '-';
		elseif (($perms & 0x6000) == 0x6000) // Block special
			$info = 'b';
		elseif (($perms & 0x4000) == 0x4000) // Directory
			$info = 'd';
		elseif (($perms & 0x2000) == 0x2000) // Character special
			$info = 'c';
		elseif (($perms & 0x1000) == 0x1000) // FIFO pipe
			$info = 'p';
		else // Unknown
			$info = 'u';

		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
					(($perms & 0x0800) ? 's' : 'x' ) :
					(($perms & 0x0800) ? 'S' : '-'));

		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
					(($perms & 0x0400) ? 's' : 'x' ) :
					(($perms & 0x0400) ? 'S' : '-'));

		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
					(($perms & 0x0200) ? 't' : 'x' ) :
					(($perms & 0x0200) ? 'T' : '-'));
		return $info;
	}

	/**
	 * Gets the permissions of the specified file or filepath in their octal format
	 *
	 * @since 2.5.0
	 * @param string $file
	 * @return string the last 3 characters of the octal number
	 */
	public function getchmod( $file ) {
		return '777';
	}

	/**
	 * Convert *nix-style file permissions to a octal number.
	 *
	 * Converts '-rw-r--r--' to 0644
	 * From "info at rvgate dot nl"'s comment on the PHP documentation for chmod()
 	 *
	 * @link https://secure.php.net/manual/en/function.chmod.php#49614
	 *
	 * @since 2.5.0
	 *
	 * @param string $mode string The *nix-style file permission.
	 * @return int octal representation
	 */
	public function getnumchmodfromh( $mode ) {
		$realmode = '';
		$legal =  array('', 'w', 'r', 'x', '-');
		$attarray = preg_split('//', $mode);

		for ( $i = 0, $c = count( $attarray ); $i < $c; $i++ ) {
		   if ($key = array_search($attarray[$i], $legal)) {
			   $realmode .= $legal[$key];
		   }
		}

		$mode = str_pad($realmode, 10, '-', STR_PAD_LEFT);
		$trans = array('-'=>'0', 'r'=>'4', 'w'=>'2', 'x'=>'1');
		$mode = strtr($mode,$trans);

		$newmode = $mode[0];
		$newmode .= $mode[1] + $mode[2] + $mode[3];
		$newmode .= $mode[4] + $mode[5] + $mode[6];
		$newmode .= $mode[7] + $mode[8] + $mode[9];
		return $newmode;
	}

	/**
	 * Determine if the string provided contains binary characters.
	 *
	 * @since 2.7.0
	 *
	 * @param string $text String to test against.
	 * @return bool true if string is binary, false otherwise.
	 */
	public function is_binary( $text ) {
		return (bool) preg_match( '|[^\x20-\x7E]|', $text ); // chr(32)..chr(127)
	}

	/**
	 * Change the ownership of a file / folder.
	 *
	 * Default behavior is to do nothing, override this in your subclass, if desired.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file      Path to the file.
	 * @param mixed  $owner     A user name or number.
	 * @param bool   $recursive Optional. If set True changes file owner recursivly. Defaults to False.
	 * @return bool Returns true on success or false on failure.
	 */
	public function chown( $file, $owner, $recursive = false ) {
		return false;
	}

	/**
	 * Connect filesystem.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @return bool True on success or false on failure (always true for WP_Filesystem_Direct).
	 */
	public function connect() {
		return true;
	}

	/**
	 * Read entire file into a string.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Name of the file to read.
	 * @return mixed|bool Returns the read data or false on failure.
	 */
	public function get_contents( $file ) {
		return false;
	}

	/**
	 * Read entire file into an array.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to the file.
	 * @return array|bool the file contents in an array or false on failure.
	 */
	public function get_contents_array( $file ) {
		return false;
	}

	/**
	 * Write a string to a file.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file     Remote path to the file where to write the data.
	 * @param string $contents The data to write.
	 * @param int    $mode     Optional. The file permissions as octal number, usually 0644.
	 * @return bool False on failure.
	 */
	public function put_contents( $file, $contents, $mode = false ) {
		return false;
	}

	/**
	 * Get the current working directory.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @return string|bool The current working directory on success, or false on failure.
	 */
	public function cwd() {
		return false;
	}

	/**
	 * Change current directory.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $dir The new current directory.
	 * @return bool|string
	 */
	public function chdir( $dir ) {
		return false;
	}

	/**
	 * Change the file group.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file      Path to the file.
	 * @param mixed  $group     A group name or number.
	 * @param bool   $recursive Optional. If set True changes file group recursively. Defaults to False.
	 * @return bool|string
	 */
	public function chgrp( $file, $group, $recursive = false ) {
		return false;
	}

	/**
	 * Change filesystem permissions.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file      Path to the file.
	 * @param int    $mode      Optional. The permissions as octal number, usually 0644 for files, 0755 for dirs.
	 * @param bool   $recursive Optional. If set True changes file group recursively. Defaults to False.
	 * @return bool|string
	 */
	public function chmod( $file, $mode = false, $recursive = false ) {
		return false;
	}

	/**
	 * Get the file owner.
	 *
	 * @since 2.5.0
	 * @abstract
	 * 
	 * @param string $file Path to the file.
	 * @return string|bool Username of the user or false on error.
	 */
	public function owner( $file ) {
		return false;
	}

	/**
	 * Get the file's group.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to the file.
	 * @return string|bool The group or false on error.
	 */
	public function group( $file ) {
		return false;
	}

	/**
	 * Copy a file.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $source      Path to the source file.
	 * @param string $destination Path to the destination file.
	 * @param bool   $overwrite   Optional. Whether to overwrite the destination file if it exists.
	 *                            Default false.
	 * @param int    $mode        Optional. The permissions as octal number, usually 0644 for files, 0755 for dirs.
	 *                            Default false.
	 * @return bool True if file copied successfully, False otherwise.
	 */
	public function copy( $source, $destination, $overwrite = false, $mode = false ) {
		return false;
	}

	/**
	 * Move a file.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $source      Path to the source file.
	 * @param string $destination Path to the destination file.
	 * @param bool   $overwrite   Optional. Whether to overwrite the destination file if it exists.
	 *                            Default false.
	 * @return bool True if file copied successfully, False otherwise.
	 */
	public function move( $source, $destination, $overwrite = false ) {
		return false;
	}

	/**
	 * Delete a file or directory.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file      Path to the file.
	 * @param bool   $recursive Optional. If set True changes file group recursively. Defaults to False.
	 *                          Default false.
	 * @param bool   $type      Type of resource. 'f' for file, 'd' for directory.
	 *                          Default false.
	 * @return bool True if the file or directory was deleted, false on failure.
	 */
	public function delete( $file, $recursive = false, $type = false ) {
		return false;
	}

	/**
	 * Check if a file or directory exists.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file/directory.
	 * @return bool Whether $file exists or not.
	 */
	public function exists( $file ) {
		return false;
	}

	/**
	 * Check if resource is a file.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file File path.
	 * @return bool Whether $file is a file.
	 */
	public function is_file( $file ) {
		return false;
	}

	/**
	 * Check if resource is a directory.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $path Directory path.
	 * @return bool Whether $path is a directory.
	 */
	public function is_dir( $path ) {
		return false;
	}

	/**
	 * Check if a file is readable.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file.
	 * @return bool Whether $file is readable.
	 */
	public function is_readable( $file ) {
		return false;
	}

	/**
	 * Check if a file or directory is writable.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file.
	 * @return bool Whether $file is writable.
	 */
	public function is_writable( $file ) {
		return false;
	}

	/**
	 * Gets the file's last access time.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file.
	 * @return int|bool Unix timestamp representing last access time.
	 */
	public function atime( $file ) {
		return false;
	}

	/**
	 * Gets the file modification time.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file.
	 * @return int|bool Unix timestamp representing modification time.
	 */
	public function mtime( $file ) {
		return false;
	}

	/**
	 * Gets the file size (in bytes).
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file Path to file.
	 * @return int|bool Size of the file in bytes.
	 */
	public function size( $file ) {
		return false;
	}

	/**
	 * Set the access and modification times of a file.
	 *
	 * Note: If $file doesn't exist, it will be created.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $file  Path to file.
	 * @param int    $time  Optional. Modified time to set for file.
	 *                      Default 0.
	 * @param int    $atime Optional. Access time to set for file.
	 *                      Default 0.
	 * @return bool Whether operation was successful or not.
	 */
	public function touch( $file, $time = 0, $atime = 0 ) {
		return false;
	}

	/**
	 * Create a directory.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $path  Path for new directory.
	 * @param mixed  $chmod Optional. The permissions as octal number, (or False to skip chmod)
	 *                      Default false.
	 * @param mixed  $chown Optional. A user name or number (or False to skip chown)
	 *                      Default false.
	 * @param mixed  $chgrp Optional. A group name or number (or False to skip chgrp).
	 *                      Default false.
	 * @return bool False if directory cannot be created, true otherwise.
	 */
	public function mkdir( $path, $chmod = false, $chown = false, $chgrp = false ) {
		return false;
	}

	/**
	 * Delete a directory.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $path      Path to directory.
	 * @param bool   $recursive Optional. Whether to recursively remove files/directories.
	 *                          Default false.
	 * @return bool Whether directory is deleted successfully or not.
	 */
	public function rmdir( $path, $recursive = false ) {
		return false;
	}

	/**
	 * Get details for files in a directory or a specific file.
	 *
	 * @since 2.5.0
	 * @abstract
	 *
	 * @param string $path           Path to directory or file.
	 * @param bool   $include_hidden Optional. Whether to include details of hidden ("." prefixed) files.
	 *                               Default true.
	 * @param bool   $recursive      Optional. Whether to recursively include file details in nested directories.
	 *                               Default false.
	 * @return array|bool {
	 *     Array of files. False if unable to list directory contents.
	 *
	 *     @type string $name        Name of the file/directory.
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
		return false;
	}

} // WP_Filesystem_Base
