<?php
class WP_Filesystem_Base{
	var $verbose = false;
	var $cache = array();
	
	var $method = '';
	
	function abspath() {
		if ( defined('FTP_BASE') && strpos($this->method, 'ftp') !== false ) 
			return FTP_BASE;
		return $this->find_folder(ABSPATH);
	}
	function wp_content_dir() {
		if ( defined('FTP_CONTENT_DIR') && strpos($this->method, 'ftp') !== false ) 
			return FTP_CONTENT_DIR;
		return $this->find_folder(WP_CONTENT_DIR);
	}
	function wp_plugins_dir() {
		if ( defined('FTP_PLUGIN_DIR') && strpos($this->method, 'ftp') !== false ) 
			return FTP_PLUGIN_DIR;
		return $this->find_folder(WP_PLUGIN_DIR);
	}
	function wp_themes_dir() {
		return $this->wp_content_dir() . '/themes';
	}
	//Back compat: use abspath() or wp_*_dir
	function find_base_dir($base = '.', $echo = false) {
		$this->verbose = $echo;
		return $this->abspath();
	}
	//Back compat: use ::abspath() or ::wp_*_dir
	function get_base_dir($base = '.', $echo = false) {
		$this->verbose = $echo;
		return $this->abspath();
	}
	
	function find_folder($folder) {
		$folder = str_replace('\\', '/', $folder); //Windows Sanitiation
		if ( isset($this->cache[ $folder ] ) )
			return $this->cache[ $folder ];

		if ( $this->exists($folder) ) { //Folder exists at that absolute path.
			$this->cache[ $folder ] = $folder;
			return $folder;
		}
		if( $return = $this->search_for_folder($folder) )
			$this->cache[ $folder ] = $return;
		return $return;
	}
	
	// Assumes $folder is windows sanitized;
	// Assumes that the drive letter is safe to be stripped off, Should not be a problem for windows servers.
	function search_for_folder($folder, $base = '.', $loop = false ) {
		if ( empty( $base ) || '.' == $base )
			$base = trailingslashit($this->cwd());
		
		$folder = preg_replace('|^([a-z]{1}):|i', '', $folder); //Strip out windows driveletter if its there.
		
		$folder_parts = explode('/', $folder);
		$last_path = $folder_parts[ count($folder_parts) - 1 ];
		
		$files = $this->dirlist( $base );
		
		foreach ( $folder_parts as $key ) {
			if ( $key == $last_path )
				continue; //We want this to be caught by the next code block.

			//Working from /home/ to /user/ to /wordpress/ see if that file exists within the current folder, 
			// If its found, change into it and follow through looking for it. 
			// If it cant find WordPress down that route, it'll continue onto the next folder level, and see if that matches, and so on.
			// If it reaches the end, and still cant find it, it'll return false for the entire function.
			if( isset($files[ $key ]) ){
				//Lets try that folder:
				$newdir = trailingslashit(path_join($base, $key));
				if( $this->verbose )
					printf( __('Changing to %s') . '<br/>', $newdir );
				if( $ret = $this->search_for_folder( $folder, $newdir, $loop) )
					return $ret;
			}
		}
		
		//Only check this as a last resort, to prevent locating the incorrect install. All above proceeedures will fail quickly if this is the right branch to take.
		if(isset( $files[ $last_path ] ) ) {
			if( $this->verbose )
				printf( __('Found %s') . '<br/>',  $base . $last_path );
			return $base . $last_path;
		}
		if( $loop )
			return false;//Prevent tihs function looping again.
		//As an extra last resort, Change back to / if the folder wasnt found. This comes into effect when the CWD is /home/user/ but WP is at /var/www/.... mainly dedicated setups.
		return $this->search_for_folder($folder, '/', true); 
		
	}
	
	//Common Helper functions.
	function gethchmod($file){
		//From the PHP.net page for ...?
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
		elseif (($perms & 0x1000) == 0x1000)// FIFO pipe
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
	function getnumchmodfromh($mode) {
		$realmode = "";
		$legal =  array("", "w", "r", "x", "-");
		$attarray = preg_split("//", $mode);

		for($i=0; $i < count($attarray); $i++)
		   if($key = array_search($attarray[$i], $legal))
			   $realmode .= $legal[$key];
			   
		$mode = str_pad($realmode, 9, '-');
		$trans = array('-'=>'0', 'r'=>'4', 'w'=>'2', 'x'=>'1');
		$mode = strtr($mode,$trans);
		
		$newmode = '';
		$newmode .= $mode[0] + $mode[1] + $mode[2];
		$newmode .= $mode[3] + $mode[4] + $mode[5];
		$newmode .= $mode[6] + $mode[7] + $mode[8];
		return $newmode;
	}
}
?>
