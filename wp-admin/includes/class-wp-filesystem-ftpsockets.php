<?php
class WP_Filesystem_ftpsockets{
	var $ftp = false;
	var $timeout = 5;
	var $errors;
	var $options = array();

	var $wp_base = '';
	var $permission = null;

	var $filetypes = array(
							'php'=>FTP_ASCII,
							'css'=>FTP_ASCII,
							'txt'=>FTP_ASCII,
							'js'=>FTP_ASCII,
							'html'=>FTP_ASCII,
							'htm'=>FTP_ASCII,
							'xml'=>FTP_ASCII,

							'jpg'=>FTP_BINARY,
							'png'=>FTP_BINARY,
							'gif'=>FTP_BINARY,
							'bmp'=>FTP_BINARY
							);

	function WP_Filesystem_ftpsockets($opt='') {
		$this->errors = new WP_Error();

		//Check if possible to use ftp functions.
		if( ! @include_once ABSPATH . 'wp-admin/includes/class-ftp.php' )
				return false;
		$this->ftp = new ftp();

		//Set defaults:
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
	}

	function connect() {
		if ( ! $this->ftp )
			return false;

		//$this->ftp->Verbose = true;

		if ( ! $this->ftp->SetServer($this->options['hostname'], $this->options['port']) ) {
			$this->errors->add('connect', sprintf(__('Failed to connect to FTP Server %1$s:%2$s'), $this->options['hostname'], $this->options['port']));
			return false;
		}
		if ( ! $this->ftp->connect() ) {
			$this->errors->add('connect', sprintf(__('Failed to connect to FTP Server %1$s:%2$s'), $this->options['hostname'], $this->options['port']));
			return false;
		}

		if ( ! $this->ftp->login($this->options['username'], $this->options['password']) ) {
			$this->errors->add('auth', sprintf(__('Username/Password incorrect for %s'), $this->options['username']));
			return false;
		}

		$this->ftp->SetType(FTP_AUTOASCII);
		$this->ftp->Passive(true);
		return true;
	}

	function setDefaultPermissions($perm) {
		$this->permission = $perm;
	}

	function find_base_dir($base = '.',$echo = false, $loop = false) {
		//Sanitize the Windows path formats, This allows easier conparison and aligns it to FTP output.
		$abspath = str_replace('\\','/',ABSPATH); //windows: Straighten up the paths..
		if( strpos($abspath, ':') ){ //Windows, Strip out the driveletter
			if( preg_match("|.{1}\:(.+)|i", $abspath, $mat) )
				$abspath = $mat[1];
		}
	
		//Set up the base directory (Which unless specified, is the current one)
		if( empty( $base ) || '.' == $base ) $base = $this->cwd();
		$base = trailingslashit($base);

		//Can we see the Current directory as part of the ABSPATH?
		$location = strpos($abspath, $base);
		if( false !== $location ) {
			$newbase = path_join($base, substr($abspath, $location + strlen($base)));

			if( false !== $this->chdir($newbase) ){ //chdir sometimes returns null under certain circumstances, even when its changed correctly, FALSE will be returned if it doesnt change correctly.
				if($echo) printf( __('Changing to %s') . '<br/>', $newbase );
				//Check to see if it exists in that folder.
				if( $this->exists($newbase . 'wp-settings.php') ){
					if($echo) printf( __('Found %s'),  $newbase . 'wp-settings.php<br/>' );
					return $newbase;
				}	
			}
		}
	
		//Ok, Couldnt do a magic location from that particular folder level
		
		//Get a list of the files in the current directory, See if we can locate where we are in the folder stucture.
		$files = $this->dirlist($base);
		
		$arrPath = explode('/', $abspath);
		foreach($arrPath as $key){
			//Working from /home/ to /user/ to /wordpress/ see if that file exists within the current folder, 
			// If its found, change into it and follow through looking for it. 
			// If it cant find WordPress down that route, it'll continue onto the next folder level, and see if that matches, and so on.
			// If it reaches the end, and still cant find it, it'll return false for the entire function.
			if( isset($files[ $key ]) ){
				//Lets try that folder:
				$folder = path_join($base, $key);
				if($echo) printf( __('Changing to %s') . '<br/>', $folder );
				$ret = $this->find_base_dir( $folder, $echo, $loop);
				if( $ret )
					return $ret;
			}
		}
		//Only check this as a last resort, to prevent locating the incorrect install. All above proceeedures will fail quickly if this is the right branch to take.
		if(isset( $files[ 'wp-settings.php' ]) ){
			if($echo) printf( __('Found %s'),  $base . 'wp-settings.php<br/>' );
			return $base;
		}
		if( $loop )
			return false;//Prevent tihs function looping again.
		//As an extra last resort, Change back to / if the folder wasnt found. This comes into effect when the CWD is /home/user/ but WP is at /var/www/.... mainly dedicated setups.
		return $this->find_base_dir('/', $echo, true); 
	}

	function get_base_dir($base = '.', $echo = false){
		if( defined('FTP_BASE') )
			$this->wp_base = FTP_BASE;
		if( empty($this->wp_base) )
			$this->wp_base = $this->find_base_dir($base, $echo);
		return $this->wp_base;
	}

	function get_contents($file,$type='',$resumepos=0){
		if( ! $this->exists($file) )
			return false;

		if( empty($type) ){
			$extension = substr(strrchr($file, "."), 1);
			$type = isset($this->filetypes[ $extension ]) ? $this->filetypes[ $extension ] : FTP_AUTOASCII;
		}
		$this->ftp->SetType($type);
		$temp = tmpfile();
		if ( ! $temp )
			return false;
		if ( ! $this->ftp->fget($temp, $file) ) {
			fclose($temp);
			return ''; //Blank document, File does exist, Its just blank.
		}
		fseek($temp, 0); //Skip back to the start of the file being written to
		$contents = '';
		while ( !feof($temp) )
			$contents .= fread($temp, 8192);
		fclose($temp);
		return $contents;
	}

	function get_contents_array($file){
		return explode("\n",$this->get_contents($file));
	}

	function put_contents($file,$contents,$type=''){
		if( empty($type) ){
			$extension = substr(strrchr($file, "."), 1);
			$type = isset($this->filetypes[ $extension ]) ? $this->filetypes[ $extension ] : FTP_ASCII;
		}
		$this->ftp->SetType($type);

		$temp = tmpfile();
		if ( ! $temp )
			return false;
		fwrite($temp,$contents);
		fseek($temp, 0); //Skip back to the start of the file being written to
		$ret = $this->ftp->fput($file, $temp);
		fclose($temp);
		return $ret;
	}

	function cwd(){
		$cwd = $this->ftp->pwd();
		if( $cwd )
			$cwd = trailingslashit($cwd);
		return $cwd;
	}

	function chdir($file){
		return $this->ftp->chdir($file);
	}
	
	function chgrp($file,$group,$recursive=false){
		return false;
	}

	function chmod($file,$mode=false,$recursive=false){
		if( ! $mode )
			$mode = $this->permission;
		if( ! $mode )
			return false;
		//if( ! $this->exists($file) )
		//	return false;
		if( ! $recursive || ! $this->is_dir($file) ){
			return $this->ftp->chmod($file,$mode);
		}
		//Is a directory, and we want recursive
		$filelist = $this->dirlist($file);
		foreach($filelist as $filename){
			$this->chmod($file.'/'.$filename,$mode,$recursive);
		}
		return true;
	}

	function chown($file,$owner,$recursive=false){
		return false;
	}

	function owner($file){
		$dir = $this->dirlist($file);
		return $dir[$file]['owner'];
	}

	function getchmod($file){
		$dir = $this->dirlist($file);
		return $dir[$file]['permsn'];
	}

	function gethchmod($file){
		//From the PHP.net page for ...?
		$perms = $this->getchmod($file);
		if (($perms & 0xC000) == 0xC000) {
			// Socket
			$info = 's';
		} elseif (($perms & 0xA000) == 0xA000) {
			// Symbolic Link
			$info = 'l';
		} elseif (($perms & 0x8000) == 0x8000) {
			// Regular
			$info = '-';
		} elseif (($perms & 0x6000) == 0x6000) {
			// Block special
			$info = 'b';
		} elseif (($perms & 0x4000) == 0x4000) {
			// Directory
			$info = 'd';
		} elseif (($perms & 0x2000) == 0x2000) {
			// Character special
			$info = 'c';
		} elseif (($perms & 0x1000) == 0x1000) {
			// FIFO pipe
			$info = 'p';
		} else {
			// Unknown
			$info = 'u';
		}

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
		$legal =  array("","w","r","x","-");
		$attarray = preg_split("//",$mode);
		for($i=0;$i<count($attarray);$i++){
		   if($key = array_search($attarray[$i],$legal)){
			   $realmode .= $legal[$key];
		   }
		}
		$mode = str_pad($realmode,9,'-');
		$trans = array('-'=>'0','r'=>'4','w'=>'2','x'=>'1');
		$mode = strtr($mode,$trans);
		$newmode = '';
		$newmode .= $mode[0]+$mode[1]+$mode[2];
		$newmode .= $mode[3]+$mode[4]+$mode[5];
		$newmode .= $mode[6]+$mode[7]+$mode[8];
		return $newmode;
	}

	function group($file){
		$dir = $this->dirlist($file);
		return $dir[$file]['group'];
	}

	function copy($source,$destination,$overwrite=false){
		if( ! $overwrite && $this->exists($destination) )
			return false;

		$content = $this->get_contents($source);
		if ( false === $content )
			return false;

		return $this->put_contents($destination,$content);
	}

	function move($source,$destination,$overwrite=false){
		return $this->ftp->rename($source,$destination);
	}

	function delete($file,$recursive=false) {
		if ( $this->is_file($file) )
			return $this->ftp->delete($file);
		if ( !$recursive )
			return $this->ftp->rmdir($file);

		return $this->ftp->mdel($file);
	}

	function exists($file){
		return $this->ftp->is_exists($file);
	}

	function is_file($file){
		return $this->is_dir($file) ? false : true;
	}

	function is_dir($path){
		$cwd = $this->cwd();
		if ( $this->chdir($path) ) {
			$this->chdir($cwd);
			return true;
		}
		return false;
	}

	function is_readable($file){
		//Get dir list, Check if the file is writable by the current user??
		return true;
	}

	function is_writable($file){
		//Get dir list, Check if the file is writable by the current user??
		return true;
	}

	function atime($file){
		return false;
	}

	function mtime($file){
		return $this->ftp->mdtm($file);
	}

	function size($file){
		return $this->ftp->filesize($file);
	}

	function touch($file,$time=0,$atime=0){
		return false;
	}

	function mkdir($path,$chmod=false,$chown=false,$chgrp=false){
		if( ! $this->ftp->mkdir($path) )
			return false;
		if( $chmod )
			$this->chmod($path, $chmod);
		if( $chown )
			$this->chown($path, $chown);
		if( $chgrp )
			$this->chgrp($path, $chgrp);
		return true;
	}

	function rmdir($path,$recursive=false){
		if( ! $recursive )
			return $this->ftp->rmdir($path);

		return $this->ftp->mdel($path);
	}

	function dirlist($path='.',$incdot=false,$recursive=false){
		if( $this->is_file($path) ){
			$limitFile = basename($path);
			$path = dirname($path) . '/';
		} else {
			$limitFile = false;
		}

		$list = $this->ftp->dirlist($path);
		if( ! $list )
			return false;
		if( empty($list) )
			return array();

		$ret = array();
		foreach ( $list as $struc ) {

			if ( 'd' == $struc['type'] ) {
				$struc['files'] = array();

				if ( $incdot ){
					//We're including the doted starts
					if( '.' != $struc['name'] && '..' != $struc['name'] ){ //Ok, It isnt a special folder
						if ($recursive)
							$struc['files'] = $this->dirlist($path.'/'.$struc['name'],$incdot,$recursive);
					}
				} else { //No dots
					if ($recursive)
						$struc['files'] = $this->dirlist($path.'/'.$struc['name'],$incdot,$recursive);
				}
			}
			//File
			$ret[$struc['name']] = $struc;
		}
		return $ret;
	}

	function __destruct(){
		$this->ftp->quit();
	}
}
?>
