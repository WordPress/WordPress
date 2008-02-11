<?php
class WP_Filesystem_ftpsockets{
	var $ftp = false;
	var $timeout = 5;
	var $errors = array();
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
		//Check if possible to use ftp functions.
		if( ! @include_once ABSPATH . 'wp-admin/includes/class-ftp.php' )
				return false;
		$this->ftp = new FTP();

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
			
		if ( ! $this->ftp->connect($this->options['hostname'], $this->options['port'], $this->timeout) ) {
			$this->errors->add('connect', sprintf(__('Failed to connect to FTP Server %1$s:%2$s'), $this->options['hostname'], $this->options['port']));
			return false;
		}

		if ( ! $this->ftp->login($this->options['username'], $this->options['password']) ) {
			$this->errors->add('auth', sprintf(__('Username/Password incorrect for %s'), $this->options['username']));
			return false;
		}

		return true;
	}

	function setDefaultPermissions($perm) {
		$this->permission = $perm;
	}

	function find_base_dir($base = '.',$echo = false) {
		if( empty( $base ) || '.' == $base ) $base = $this->cwd();
		if( empty( $base ) ) $base = '/';
		if( '/' != substr($base, -1) ) $base .= '/';
		
		if($echo) echo __('Changing to ') . $base  .'<br>';
		if( false === $this->ftp->chdir($base) )
			return false;

		if( $this->exists($base . 'wp-settings.php') ){
			if($echo) echo __('Found ') . $base . 'wp-settings.php<br>';
			$this->wp_base = $base;
			return $this->wp_base;
		}

		if( strpos(ABSPATH, $base) > 0)
			$arrPath = split('/',substr(ABSPATH,strpos(ABSPATH, $base)));
		else
			$arrPath = split('/',ABSPATH);

		for($i = 0; $i <= count($arrPath); $i++)
			if( $arrPath[ $i ] == '' ) unset( $arrPath[ $i ] );

		foreach($arrPath as $key=>$folder){
			if( $this->is_dir($base . $folder) ){
				if($echo) echo __('Found ') . $folder . ' ' . __('Changing to') . ' ' . $base . $folder . '/<br>';
				return $this->find_base_dir($base .  $folder . '/',$echo);
			}
		}

		if( $base == '/' )
			return false;
		//If we get this far, somethings gone wrong, change to / and restart the process.
		return $this->find_base_dir('/',$echo);
	}
	function get_base_dir($base = '.'){
		if( empty($this->wp_base) )
			$this->wp_base = $this->find_base_dir($base);
		return $this->wp_base;
	}
	function get_contents($file,$type='',$resumepos=0){
		if( empty($type) ){
			$extension = substr(strrchr($filename, "."), 1);
			$type = isset($this->filetypes[ $extension ]) ? $this->filetypes[ $extension ] : FTP_AUTOASCII;
		}
		$this->ftp->SetType($type);
		
		return $this->ftp->get($file);
	}
	function get_contents_array($file){
		return explode("\n",$this->get_contents($file));
	}
	function put_contents($file,$contents,$type=''){
		if( empty($type) ){
			$extension = substr(strrchr($filename, "."), 1);
			$type = isset($this->filetypes[ $extension ]) ? $this->filetypes[ $extension ] : FTP_ASCII;
		}
		$this->ftp->SetType($type);
		
		$temp = tmpfile();
		fwrite($temp,$contents);
		fseek($temp, 0); //Skip back to the start of the file being written to
		$ret = $this->ftp->put($temp, $file);
		fclose($temp);
		return $ret;
	}
	function cwd(){
		return $this->ftp->pwd();
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
		$this->put_contents($destination,$content);
	}
	function move($source,$destination,$overwrite=false){
		return $this->ftp->rename($source,$destination);
	}

	function delete($file,$recursive=false) {
		if ( $this->is_file($file) )
			return $this->ftp->delete($file);
		if ( !$recursive )
			return $this->ftp->rmdir($file);
		$filelist = $this->dirlist($file);
		foreach ($filelist as $filename) {
			$this->delete($file.'/'.$filename,$recursive);
		}
		return $this->ftp->rmdir($file);
	}

	function exists($file){
		return $this->ftp->is_exists($file);
	}
	function is_file($file){
		//return $this->ftp->file_exists($file);
		$list = $this->ftp->rawlist($file,'-a');
		if( ! $list )
			return false;
		return ($list[0] == '-'); 
	}
	function is_dir($path){
		$list = $this->ftp->rawlist($file,'-a');
		if( ! $list )
			return false;
		return true;
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
			$this->chmod($chmod);
		if( $chown )
			$this->chown($chown);
		if( $chgrp )
			$this->chgrp($chgrp);
		return true;
	}
	function rmdir($path,$recursive=false){
		if( ! $recursive )
			return $this->ftp->rmdir($file);
		return false;
		//TODO: Recursive Directory delete, Have to delete files from the folder first.
		//$dir = $this->dirlist($path);
		//foreach($dir as $file)
			
	}
	function dirlist($path='.',$incdot=false,$recursive=false){
		if( $this->is_file($path) ){
			$limitFile = basename($path);
			$path = dirname($path) . '/';
		} else {
			$limitFile = false;
		}
		//if( ! $this->is_dir($path) )
		//	return false;
		$list = $this->ftp->rawlist($path,'-a');
		//var_dump($list);
		if( ! $list )
			return false;
		if( empty($list) )
			return array();

		$ret = array();
		foreach($list as $line){
			$struc = array();
			$current = preg_split("/[\s]+/",$line,9);
			$struc['name']    	= str_replace('//','',$current[8]);

			if( '.' == $struc['name'][0] && !$incdot)
				continue;
			if( $limitFile && $struc['name'] != $limitFile)
				continue;

			$struc['perms']    	= $current[0];
			$struc['permsn']	= $this->getnumchmodfromh($current[0]);
			$struc['number']	= $current[1];
			$struc['owner']    	= $current[2];
			$struc['group']    	= $current[3];
			$struc['size']    	= $current[4];
			$struc['lastmod']   = $current[5].' '.$current[6];
			$struc['time']    	= $current[7];

			$struc['type']		= ('d' == $struc['perms'][0] || 'l' == $struc['perms'][0] ) ? 'folder' : 'file';
			if('folder' == $struc['type'] ){
				$struc['files'] = array();

				if( $incdot ){
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
