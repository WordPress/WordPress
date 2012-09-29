<?php
/**
 * Base WordPress Filesystem.
 *
 * @package WordPress
 * @subpackage Filesystem
 */

/**
 * Base WordPress Filesystem class for which Filesystem implementations extend
 *
 * @since 2.5
 */
class WP_Filesystem_Base {
	/**
	 * Whether to display debug data for the connection.
	 *
	 * @since 2.5
	 * @access public
	 * @var bool
	 */
	var $verbose = false;
	/**
	 * Cached list of local filepaths to mapped remote filepaths.
	 *
	 * @since 2.7
	 * @access private
	 * @var array
	 */
	var $cache = array();

	/**
	 * The Access method of the current connection, Set automatically.
	 *
	 * @since 2.5
	 * @access public
	 * @var string
	 */
	var $method = '';

	/**
	 * Returns the path on the remote filesystem of ABSPATH
	 *
	 * @since 2.7
	 * @access public
	 * @return string The location of the remote path.
	 */
	function abspath() {
		$folder = $this->find_folder(ABSPATH);
		//Perhaps the FTP folder is rooted at the WordPress install, Check for wp-includes folder in root, Could have some false positives, but rare.
		if ( ! $folder && $this->is_dir('/wp-includes') )
			$folder = '/';
		return $folder;
	}
	/**
	 * Returns the path on the remote filesystem of WP_CONTENT_DIR
	 *
	 * @since 2.7
	 * @access public
	 * @return string The location of the remote path.
	 */
	function wp_content_dir() {
		return $this->find_folder(WP_CONTENT_DIR);
	}
	/**
	 * Returns the path on the remote filesystem of WP_PLUGIN_DIR
	 *
	 * @since 2.7
	 * @access public
	 *
	 * @return string The location of the remote path.
	 */
	function wp_plugins_dir() {
		return $this->find_folder(WP_PLUGIN_DIR);
	}
	/**
	 * Returns the path on the remote filesystem of the Themes Directory
	 *
	 * @since 2.7
	 * @access public
	 *
	 * @return string The location of the remote path.
	 */
	function wp_themes_dir() {
		return $this->wp_content_dir() . 'themes/';
	}
	/**
	 * Returns the path on the remote filesystem of WP_LANG_DIR
	 *
	 * @since 3.2.0
	 * @access public
	 *
	 * @return string The location of the remote path.
	 */
	function wp_lang_dir() {
		return $this->find_folder(WP_LANG_DIR);
	}

	/**
	 * Locates a folder on the remote filesystem.
	 *
	 * Deprecated; use WP_Filesystem::abspath() or WP_Filesystem::wp_*_dir() methods instead.
	 *
	 * @since 2.5
	 * @deprecated 2.7
	 * @access public
	 *
	 * @param string $base The folder to start searching from
	 * @param bool $echo True to display debug information
	 * @return string The location of the remote path.
	 */
	function find_base_dir($base = '.', $echo = false) {
		_deprecated_function(__FUNCTION__, '2.7', 'WP_Filesystem::abspath() or WP_Filesystem::wp_*_dir()' );
		$this->verbose = $echo;
		return $this->abspath();
	}
	/**
	 * Locates a folder on the remote filesystem.
	 *
	 * Deprecated; use WP_Filesystem::abspath() or WP_Filesystem::wp_*_dir() methods instead.
	 *
	 * @since 2.5
	 * @deprecated 2.7
	 * @access public
	 *
	 * @param string $base The folder to start searching from
	 * @param bool $echo True to display debug information
	 * @return string The location of the remote path.
	 */
	function get_base_dir($base = '.', $echo = false) {
		_deprecated_function(__FUNCTION__, '2.7', 'WP_Filesystem::abspath() or WP_Filesystem::wp_*_dir()' );
		$this->verbose = $echo;
		return $this->abspath();
	}

	/**
	 * Locates a folder on the remote filesystem.
	 *
	 * Assumes that on Windows systems, Stripping off the Drive letter is OK
	 * Sanitizes \\ to / in windows filepaths.
	 *
	 * @since 2.7
	 * @access public
	 *
	 * @param string $folder the folder to locate
	 * @return string The location of the remote path.
	 */
	function find_folder($folder) {

		if ( strpos($this->method, 'ftp') !== false ) {
			$constant_overrides = array( 'FTP_BASE' => ABSPATH, 'FTP_CONTENT_DIR' => WP_CONTENT_DIR, 'FTP_PLUGIN_DIR' => WP_PLUGIN_DIR, 'FTP_LANG_DIR' => WP_LANG_DIR );
			foreach ( $constant_overrides as $constant => $dir )
				if ( defined($constant) && $folder === $dir )
					return trailingslashit(constant($constant));
		} elseif ( 'direct' == $this->method ) {
			$folder = str_replace('\\', '/', $folder); //Windows path sanitisation
			return trailingslashit($folder);
		}

		$folder = preg_replace('|^([a-z]{1}):|i', '', $folder); //Strip out windows drive letter if it's there.
		$folder = str_replace('\\', '/', $folder); //Windows path sanitisation

		if ( isset($this->cache[ $folder ] ) )
			return $this->cache[ $folder ];

		if ( $this->exists($folder) ) { //Folder exists at that absolute path.
			$folder = trailingslashit($folder);
			$this->cache[ $folder ] = $folder;
			return $folder;
		}
		if ( $return = $this->search_for_folder($folder) )
			$this->cache[ $folder ] = $return;
		return $return;
	}

	/**
	 * Locates a folder on the remote filesystem.
	 *
	 * Expects Windows sanitized path
	 *
	 * @since 2.7
	 * @access private
	 *
	 * @param string $folder the folder to locate
	 * @param string $base the folder to start searching from
	 * @param bool $loop if the function has recursed, Internal use only
	 * @return string The location of the remote path.
	 */
	function search_for_folder($folder, $base = '.', $loop = false ) {
		if ( empty( $base ) || '.' == $base )
			$base = trailingslashit($this->cwd());

		$folder = untrailingslashit($folder);

		$folder_parts = explode('/', $folder);
		$last_index = array_pop( array_keys( $folder_parts ) );
		$last_path = $folder_parts[ $last_index ];

		$files = $this->dirlist( $base );

		foreach ( $folder_parts as $index => $key ) {
			if ( $index == $last_index )
				continue; //We want this to be caught by the next code block.

			//Working from /home/ to /user/ to /wordpress/ see if that file exists within the current folder,
			// If its found, change into it and follow through looking for it.
			// If it cant find WordPress down that route, it'll continue onto the next folder level, and see if that matches, and so on.
			// If it reaches the end, and still cant find it, it'll return false for the entire function.
			if ( isset($files[ $key ]) ){
				//Lets try that folder:
				$newdir = trailingslashit(path_join($base, $key));
				if ( $this->verbose )
					printf( __('Changing to %s') . '<br/>', $newdir );
				// only search for the remaining path tokens in the directory, not the full path again
				$newfolder = implode( '/', array_slice( $folder_parts, $index + 1 ) );
				if ( $ret = $this->search_for_folder( $newfolder, $newdir, $loop) )
					return $ret;
			}
		}

		//Only check this as a last resort, to prevent locating the incorrect install. All above procedures will fail quickly if this is the right branch to take.
		if (isset( $files[ $last_path ] ) ) {
			if ( $this->verbose )
				printf( __('Found %s') . '<br/>',  $base . $last_path );
			return trailingslashit($base . $last_path);
		}
		if ( $loop )
			return false; //Prevent this function from looping again.
		//As an extra last resort, Change back to / if the folder wasn't found. This comes into effect when the CWD is /home/user/ but WP is at /var/www/.... mainly dedicated setups.
		return $this->search_for_folder($folder, '/', true);

	}

	/**
	 * Returns the *nix style file permissions for a file
	 *
	 * From the PHP documentation page for fileperms()
	 *
	 * @link http://docs.php.net/fileperms
	 * @since 2.5
	 * @access public
	 *
	 * @param string $file string filename
	 * @return int octal representation of permissions
	 */
	function gethchmod($file){
		$perms = $this->getchmod($file);
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
	 * Converts *nix style file permissions to a octal number.
	 *
	 * Converts '-rw-r--r--' to 0644
	 * From "info at rvgate dot nl"'s comment on the PHP documentation for chmod()
 	 *
	 * @link http://docs.php.net/manual/en/function.chmod.php#49614
	 * @since 2.5
	 * @access public
	 *
	 * @param string $mode string *nix style file permission
	 * @return int octal representation
	 */
	function getnumchmodfromh($mode) {
		$realmode = '';
		$legal =  array('', 'w', 'r', 'x', '-');
		$attarray = preg_split('//', $mode);

		for ($i=0; $i < count($attarray); $i++)
		   if ($key = array_search($attarray[$i], $legal))
			   $realmode .= $legal[$key];

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
	 * Determines if the string provided contains binary characters.
	 *
	 * @since 2.7
	 * @access private
	 *
	 * @param string $text String to test against
	 * @return bool true if string is binary, false otherwise
	 */
	function is_binary( $text ) {
		return (bool) preg_match('|[^\x20-\x7E]|', $text); //chr(32)..chr(127)
	}
}
