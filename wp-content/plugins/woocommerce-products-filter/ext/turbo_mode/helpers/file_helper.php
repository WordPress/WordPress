<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WOOF_FILE_GENERATOR_HELPER{
    public static $dir_name="uploads_woof_turbo_mode";
    public static $possible_types=array('xml','csv','txt','json');
    public $type="json";
    public function __construct() {

    }
    public static function get_full_path_dir(){
        $path="";
        $upload = wp_upload_dir();
	$path = $upload['basedir'];
	$path = $path . '/'. self::$dir_name. '/';
        if(!file_exists($path)){
           wp_mkdir_p($path); 
        } 
        return $path;
    }
    public static function get_full_link_dir(){
        return WP_CONTENT_URL.'/uploads/'.self::$dir_name.'/';
    }
    public static function delete_file_is_exist($name,$type,$check_all=false){
        $check_file_type=array();
        $check_file_type[]=$type;
        if($check_all){
           $check_file_type=self::$possible_types; 
        }
        foreach ($check_file_type as $type){
            $path= self::get_full_path_dir().$name.'.'.$type;
            if (file_exists($path)) {
                unlink($path);
            }
        }
        
    }
    public static function delete_file_all_files(){
        $path = self::get_full_path_dir();
        $files = glob("{$path}/*"); // get all file names
        foreach($files as $file){ // iterate files
          if(is_file($file))
                unlink($file); // delete file
        }	

        
    }
    
}
