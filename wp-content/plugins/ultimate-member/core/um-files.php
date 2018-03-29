<?php

class UM_Files {

	function __construct() {

		add_action('init',  array(&$this, 'setup_paths'), 1);

		$this->fonticon = array(
			'pdf' 	=> array('icon' 	=> 'um-faicon-file-pdf-o', 'color' => '#D24D4D' ),
			'txt' 	=> array('icon' 	=> 'um-faicon-file-text-o' ),
			'csv' 	=> array('icon' 	=> 'um-faicon-file-text-o' ),
			'doc' 	=> array('icon' 	=> 'um-faicon-file-text-o', 'color' => '#2C95D5' ),
			'docx' 	=> array('icon' 	=> 'um-faicon-file-text-o', 'color' => '#2C95D5' ),
			'odt' 	=> array('icon' 	=> 'um-faicon-file-text-o', 'color' => '#2C95D5' ),
			'ods' 	=> array('icon' 	=> 'um-faicon-file-excel-o', 'color' => '#51BA6A' ),
			'xls' 	=> array('icon' 	=> 'um-faicon-file-excel-o', 'color' => '#51BA6A' ),
			'xlsx' 	=> array('icon' 	=> 'um-faicon-file-excel-o', 'color' => '#51BA6A' ),
			'zip' 	=> array('icon' 	=> 'um-faicon-file-zip-o' ),
			'rar' 	=> array('icon'		=> 'um-faicon-file-zip-o' ),
			'mp3'	=> array('icon'		=> 'um-faicon-file-audio-o' ),
			'jpg' 	=> array('icon' 	=> 'um-faicon-picture-o' ),
			'jpeg' 	=> array('icon' 	=> 'um-faicon-picture-o' ),
			'png' 	=> array('icon' 	=> 'um-icon-image' ),
			'gif' 	=> array('icon' 	=> 'um-icon-images' ),
			'eps' 	=> array('icon' 	=> 'um-icon-images' ),
			'psd' 	=> array('icon' 	=> 'um-icon-images' ),
			'tif' 	=> array('icon' 	=> 'um-icon-image' ),
			'tiff' 	=> array('icon' 	=> 'um-icon-image' ),
		);

		$this->default_file_fonticon = 'um-faicon-file-o';

	}

	/***
	***	@allowed image types
	***/
	function allowed_image_types() {

		$array['png'] = 'PNG';
		$array['jpeg'] = 'JPEG';
		$array['jpg'] = 'JPG';
		$array['gif'] = 'GIF';

		$array = apply_filters('um_allowed_image_types', $array);
		return $array;
	}

	/***
	***	@allowed file types
	***/
	function allowed_file_types() {

		$array['pdf']  = 'PDF';
		$array['txt']  = 'Text';
		$array['csv']  = 'CSV';
		$array['doc']  = 'DOC';
		$array['docx'] = 'DOCX';
		$array['odt']  = 'ODT';
		$array['ods']  = 'ODS';
		$array['xls']  = 'XLS';
		$array['xlsx'] = 'XLSX';
		$array['zip']  = 'ZIP';
		$array['rar']  = 'RAR';
		$array['mp3']  = 'MP3';
		$array['jpg']  = 'JPG';
		$array['jpeg'] = 'JPEG';
		$array['png']  = 'PNG';
		$array['gif']  = 'GIF';
		$array['eps']  = 'EPS';
		$array['psd']  = 'PSD';
		$array['tif']  = 'TIF';
		$array['tiff'] = 'TIFF';

		$array = apply_filters('um_allowed_file_types', $array);
		return $array;
	}

	/***
	***	@Get extension icon
	***/
	function get_fonticon_by_ext( $extension ) {
		if (isset($this->fonticon[$extension]['icon'])){
			return $this->fonticon[$extension]['icon'];
		} else {
			return $this->default_file_fonticon;
		}
	}

	/***
	***	@Get extension icon background
	***/
	function get_fonticon_bg_by_ext( $extension ) {
		if (isset($this->fonticon[$extension]['color'])){
			return $this->fonticon[$extension]['color'];
		} else {
			return '#666';
		}
	}

	/***
	***	@Setup upload directory
	***/
	function setup_paths(){

		$this->upload_dir = wp_upload_dir();

		$this->upload_basedir = $this->upload_dir['basedir'] . '/ultimatemember/';
		$this->upload_baseurl = $this->upload_dir['baseurl'] . '/ultimatemember/';

		$this->upload_basedir = apply_filters('um_upload_basedir_filter', $this->upload_basedir );
		$this->upload_baseurl = apply_filters('um_upload_baseurl_filter', $this->upload_baseurl );

		// @note : is_ssl() doesn't work properly for some sites running with load balancers
		// Check the links for more info about this bug
		// https://codex.wordpress.org/Function_Reference/is_ssl
		// http://snippets.webaware.com.au/snippets/wordpress-is_ssl-doesnt-work-behind-some-load-balancers/
		if( is_ssl() || stripos( get_option( 'siteurl' ), 'https://' ) !== false
		  || ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] )
		  	&& $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) ) {
			 $this->upload_baseurl = str_replace("http://", "https://",  $this->upload_baseurl);
		}

		$this->upload_temp = $this->upload_basedir . 'temp/';
		$this->upload_temp_url = $this->upload_baseurl . 'temp/';

		if (!file_exists( $this->upload_basedir )) {
			$old = umask(0);
			@mkdir( $this->upload_basedir, 0755, true);
			umask($old);
		}

		if (!file_exists( $this->upload_temp )) {
			$old = umask(0);
			@mkdir( $this->upload_temp , 0755, true);
			umask($old);
		}

	}

	/***
	***	@Generate unique temp directory
	***/
	function unique_dir(){
		global $ultimatemember;
		$unique_number = $ultimatemember->validation->generate();
		$array['dir'] = $this->upload_temp . $unique_number . '/';
		$array['url'] = $this->upload_temp_url . $unique_number . '/';
		return $array;
	}

	/***
	***	@get path only without file name
	***/
	function path_only( $file ) {
		return trailingslashit( dirname( $file ) );
	}

	/***
	***	@fix image orientation
	***/
	function fix_image_orientation($rotate, $source){
		if ( extension_loaded('exif') ){
			$exif = @exif_read_data($source);

			if (isset($exif['Orientation'])) {
				switch ($exif['Orientation']) {
					case 3:
						$rotate = imagerotate($rotate, 180, 0);
						break;

					case 6:
						$rotate = imagerotate($rotate, -90, 0);
						break;

					case 8:
						$rotate = imagerotate($rotate, 90, 0);
						break;
				}
			}
		}
		return $rotate;
	}

	/***
	***	@Process an image
	***/
	function create_and_copy_image($source, $destination, $quality = 100) {

		$info = @getimagesize($source);

		if ($info['mime'] == 'image/jpeg'){

			$image = imagecreatefromjpeg( $source );

		} else if ($info['mime'] == 'image/gif'){

			$image = imagecreatefromgif( $source );

		} else if ($info['mime'] == 'image/png'){

			$image = imagecreatefrompng( $source );
			imagealphablending( $image, false );
			imagesavealpha( $image, true );

		}

		list($w, $h) = @getimagesize( $source );
		if ( $w > um_get_option('image_max_width') ) {

			$ratio = round( $w / $h, 2 );
			$new_w = um_get_option('image_max_width');
			$new_h = round( $new_w / $ratio, 2 );

			if ( $info['mime'] == 'image/jpeg' ||  $info['mime'] == 'image/gif' ){

				$image_p = imagecreatetruecolor( $new_w, $new_h );
				imagecopyresampled( $image_p, $image, 0, 0, 0, 0, $new_w, $new_h, $w, $h );
				$image_p = $this->fix_image_orientation( $image_p, $source );
			
			}else if( $info['mime'] == 'image/png' ){
				
				$srcImage = $image; 
				$targetImage = imagecreatetruecolor( $new_w, $new_h );   
				imagealphablending( $targetImage, false );
				imagesavealpha( $targetImage, true );
				imagecopyresampled( $targetImage, $srcImage,   0, 0, 0, 0, $new_w, $new_h, $w, $h );

			}

			if ( $info['mime'] == 'image/jpeg' ){
				$has_copied = imagejpeg( $image_p, $destination, $quality );
			}else if ( $info['mime'] == 'image/gif' ){
				$has_copied = imagegif( $image_p, $destination );
			}else if ( $info['mime'] == 'image/png' ){
				$has_copied = imagepng( $targetImage, $destination, 0 ,PNG_ALL_FILTERS);
			}

			$info['um_has_max_width'] = 'custom';
			$info['um_has_copied'] = $has_copied ? 'yes':'no';

		} else {

			$image = $this->fix_image_orientation( $image, $source );
			
			if ( $info['mime'] == 'image/jpeg' ){
				$has_copied = imagejpeg( $image, $destination, $quality );
			}else if ( $info['mime'] == 'image/gif' ){
				$has_copied = imagegif( $image, $destination );
			}else if ( $info['mime'] == 'image/png' ){
				$has_copied = imagepng( $image , $destination , 0 ,PNG_ALL_FILTERS);
			}
			
			$info['um_has_max_width'] = 'default';
			$info['um_has_copied'] = $has_copied ? 'yes':'no';
		}

		

		return $info;
	}

	/***
	***	@Process a file
	***/
	function upload_temp_file( $source, $destination ) {

		move_uploaded_file( $source, $destination );

	}

	/***
	***	@Process a temp upload
	***/
	function new_image_upload_temp( $source, $destination, $quality = 100 ){

		$unique_dir = $this->unique_dir();

		$this->make_dir( $unique_dir['dir'] );

		$info = $this->create_and_copy_image( $source, $unique_dir['dir'] . $destination, $quality );

		$url = $unique_dir['url'] . $destination ;

		return $url;

	}

	/***
	***	@Process a temp upload for files
	***/
	function new_file_upload_temp( $source, $destination ){

		$unique_dir = $this->unique_dir();

		$this->make_dir( $unique_dir['dir'] );

		$this->upload_temp_file( $source, $unique_dir['dir'] . $destination );

		$url = $unique_dir['url'] . $destination;

		return $url;

	}

	/***
	***	@Make a Folder
	***/
	function make_dir( $dir ){

		$old = umask(0);
		@mkdir( $dir, 0755, true);
		umask( $old );

	}

	/***
	***	@Get extension by mime type
	***/
	function get_extension_by_mime_type( $mime ){
		$split = explode('/', $mime );
		return $split[1];
	}

	/***
	***	@Get file data
	***/
	function get_file_data( $file ){

		$array['size'] = filesize( $file );

		return $array;
	}

	/***
	***	@Get image data
	***/
	function get_image_data( $file ){

		$array['size'] = filesize( $file );

		$array['image'] = @getimagesize( $file );

		if ( $array['image'] > 0 ) {

			$array['invalid_image'] = false;

			list($width, $height, $type, $attr) = @getimagesize( $file );

			$array['width'] = $width;
			$array['height'] = $height;
			$array['ratio'] = $width / $height;

			$array['extension'] = $this->get_extension_by_mime_type( $array['image']['mime'] );

		} else {

			$array['invalid_image'] = true;

		}

		return $array;
	}

	/***
	***	@Check image upload and handle errors
	***/
	function check_image_upload( $file, $field ) {
		global $ultimatemember;
		$error = null;

		$fileinfo = $this->get_image_data( $file );
		$data = $ultimatemember->fields->get_field( $field );

		if ( $data == null ) {
			$data = apply_filters("um_custom_image_handle_{$field}", '' );
			if ( !$data  ) {
				$error = __('This media type is not recognized.','ultimate-member');
			}
		}

		$data = apply_filters("um_image_handle_global__option", $data );
		$data = apply_filters("um_image_handle_{$field}__option", $data );

		if ( $fileinfo['invalid_image'] == true ) {
			$error = sprintf(__('Your image is invalid or too large!','ultimate-member') );
		} elseif ( isset( $data['allowed_types'] ) && !$this->in_array( $fileinfo['extension'], $data['allowed_types'] ) ) {
			$error = ( isset( $data['extension_error'] ) && !empty( $data['extension_error'] ) ) ? $data['extension_error'] : 'not allowed';
		} elseif ( isset($data['min_size']) && ( $fileinfo['size'] < $data['min_size'] ) ) {
			$error = $data['min_size_error'];
		} elseif ( isset($data['min_width']) && ( $fileinfo['width'] < $data['min_width'] ) ) {
			$error = sprintf(__('Your photo is too small. It must be at least %spx wide.','ultimate-member'), $data['min_width']);
		} elseif ( isset($data['min_height']) && ( $fileinfo['height'] < $data['min_height'] ) ) {
			$error = sprintf(__('Your photo is too small. It must be at least %spx wide.','ultimate-member'), $data['min_height']);
		}

		return $error;
	}

	/***
	***	@Check file upload and handle errors
	***/
	function check_file_upload( $file, $extension, $field ) {
		global $ultimatemember;
		$error = null;

		$fileinfo = $this->get_file_data( $file );
		$data = $ultimatemember->fields->get_field( $field );

		if ( !$this->in_array( $extension, $data['allowed_types'] ) ) {
			$error = ( isset( $data['extension_error'] ) && !empty( $data['extension_error'] ) ) ? $data['extension_error'] : 'not allowed';
		} elseif ( isset($data['min_size']) && ( $fileinfo['size'] < $data['min_size'] ) ) {
			$error = $data['min_size_error'];
		}

		return $error;
	}

	/***
	***	@If a value exists in comma seperated list
	***/
	function in_array( $value, $array ){
		
		if ( in_array( $value, explode(',', $array ) ) ){
			return true;
		}

		return false;
	}

	/***
	***	@This function will delete file upload from server
	***/
	function delete_file( $src ) {

		if ( strstr( $src, '?' ) ){
			$splitted = explode('?', $src );
			$src = $splitted[0];
		}

		$is_temp = um_is_temp_upload( $src );
		if ( $is_temp ) {
			unlink( $is_temp );
			rmdir( dirname( $is_temp ) );
		} else {
			wp_die( __('Ultimate Member: Not a valid temp file','ultimate-member') );
		} 
	}

	/***
	***	@delete a main user photo
	***/
	function delete_core_user_photo( $user_id, $type ) {

		delete_user_meta( $user_id, $type );

		do_action("um_after_remove_{$type}", $user_id);

		$dir = $this->upload_basedir . $user_id . '/';
		$prefix = $type;
		chdir($dir);
		$matches = glob($prefix.'*',GLOB_MARK);

		if( is_array($matches) && !empty($matches)) {
			foreach($matches as $match) {
				if( is_file($dir.$match) ) unlink($dir.$match);
			}
		}

		if ( count(glob("$dir/*")) === 0) {
			rmdir( $dir );
		}

	}

	/***
	***	@resize a local image
	***/
	function resize_image( $file, $crop ) {

		$targ_x1 = $crop[0];
		$targ_y1 = $crop[1];
		$targ_x2 = $crop[2];
		$targ_y2 = $crop[3];

		
		$info = @getimagesize( $file );
		
		if ( $info['mime'] == 'image/gif' ){
			
			$img_r = imagecreatefromgif( $file );
			$dst_r = imagecreatetruecolor( $targ_x2, $targ_y2 );
			imagecopy( $dst_r, $img_r, 0, 0, $targ_x1, $targ_y1, $targ_x2, $targ_y2 );
			imagegif( $dst_r, $this->path_only( $file ) . basename( $file ) );

		} else if ( $info['mime'] == 'image/png' ){
			
			$img_r = imagecreatefrompng( $file );
			$dst_r = imagecreatetruecolor( $targ_x2, $targ_y2 );
			imagealphablending( $dst_r, false);
		    imagesavealpha( $dst_r, true);
		    imagecopy( $dst_r, $img_r, 0, 0, $targ_x1, $targ_y1, $targ_x2, $targ_y2 );
			imagepng( $dst_r, $this->path_only( $file ) . basename( $file ) );

		}else{

			$img_r = imagecreatefromjpeg( $file );
			$dst_r = imagecreatetruecolor( $targ_x2, $targ_y2 );
			imagecopy( $dst_r, $img_r, 0, 0, $targ_x1, $targ_y1, $targ_x2, $targ_y2 );
			imagejpeg( $dst_r, $this->path_only( $file ) . basename( $file ), 100 );

		}

		$split = explode('/ultimatemember/temp/', $file );
		return $this->upload_temp_url . $split[1];

	}

	/***
	***	@make a user folder for uploads
	***/
	function new_user( $user_id ) {
		if ( !file_exists( $this->upload_basedir . $user_id . '/' ) ) {
			$old = umask(0);
			@mkdir( $this->upload_basedir . $user_id . '/' , 0755, true);
			umask($old);
		}
	}

	/***
	***	@new user upload
	***/
	function new_user_upload( $user_id, $source, $key ) {

		if( ! is_numeric( $user_id ) ){
			wp_die( __("Invalid user ID: ".json_encode( $user_id )." ",'ultimate-member') );
		}

		$user_id = trim( $user_id );
		
		// if he does not have uploads dir yet
		$this->new_user( $user_id );

		if ( is_user_logged_in() && ( get_current_user_id() != $user_id ) && !um_user_can('can_edit_everyone') ) {
			wp_die( __('Unauthorized to do this attempt.','ultimate-member') );
		}

		$allow_frontend_image_uploads = apply_filters('um_allow_frontend_image_uploads', false, $user_id, $key );

		if ( $allow_frontend_image_uploads == false && !is_user_logged_in() && ( $key == 'profile_photo' || $key == 'cover_photo' ) ) {
			wp_die( __('Unauthorized to do this attempt.','ultimate-member') );
		}

		$ext = '.' . pathinfo($source, PATHINFO_EXTENSION);
		
		// copy & overwrite file
			
		if( in_array( $key , array('profile_photo','cover_photo') ) ){
			$filename = $key . $ext;
			$name = $key;
		}else{
			$filename = basename( $source );
		}
		


		if ( file_exists( $this->upload_basedir . $user_id . '/' . $filename ) ) {
			unlink( $this->upload_basedir . $user_id . '/' . $filename );
		}
		copy( $source, $this->upload_basedir . $user_id . '/' . $filename );
		
		$info = @getimagesize( $source );

		// thumbs
		if ( $key == 'profile_photo' ) {

			list($w, $h) = @getimagesize( $source );
				

			$sizes = um_get_option('photo_thumb_sizes');
			foreach( $sizes as $size ) {
				
				$ratio = round( $w / $h, 2 );
				$height = round( $size / $ratio, 2 );

				if ( file_exists(  $this->upload_basedir . $user_id . '/' . $name . '-' . $size . $ext ) ) {
					unlink( $this->upload_basedir . $user_id . '/' . $name . '-' . $size . $ext );
				}

				if ( $size < $w ) {
					
					if ( $info['mime'] == 'image/jpeg' ){
						$thumb_s = imagecreatefromjpeg( $source );
						$thumb = imagecreatetruecolor( $size, $size );
						imagecopyresampled( $thumb, $thumb_s, 0, 0, 0, 0, $size, $size, $w, $h );
						imagejpeg( $thumb, $this->upload_basedir . $user_id . '/' . $name . '-' . $size . $ext, 100);
						imagejpeg( $thumb, $this->upload_basedir . $user_id . '/' . $name . $ext, 100);
					}else if ( $info['mime'] == 'image/png' ){
						$thumb_s  = imagecreatefrompng( $source );
						$thumb = imagecreatetruecolor( $size, $size ); 
						imagealphablending( $thumb, false);
		    			imagesavealpha( $thumb, true);
		    			imagecopyresampled( $thumb, $thumb_s, 0, 0, 0, 0, $size, $size, $w, $h );
						imagepng( $thumb, $this->upload_basedir . $user_id . '/' . $name . '-' . $size . $ext );
					}else if ( $info['mime'] == 'image/gif' ){
						$thumb_s = imagecreatefromgif( $source );
						$thumb = imagecreatetruecolor( $size, $size );
						imagecopyresampled( $thumb, $thumb_s, 0, 0, 0, 0, $size, $size, $w, $h );
						imagegif( $thumb, $this->upload_basedir . $user_id . '/' . $name . '-' . $size . $ext);
						imagegif( $thumb, $this->upload_basedir . $user_id . '/' . $name . $ext);
					}
				}

			}

			// removes a synced profile photo
			delete_user_meta( $user_id, 'synced_profile_photo' );

		}else if ( $key == 'cover_photo' ) {

			list($w, $h) = @getimagesize( $source );

			$sizes = um_get_option('cover_thumb_sizes');
			foreach( $sizes as $size ) {

				$ratio = round( $w / $h, 2 );
				$height = round( $size / $ratio, 2 );

				if ( file_exists(  $this->upload_basedir . $user_id . '/' . $name . '-' . $size . $ext ) ) {
					unlink( $this->upload_basedir . $user_id . '/' . $name . '-' . $size . $ext );
				}

				if ( $size < $w ) {

					if ( $info['mime'] == 'image/jpeg' ){
						$thumb = imagecreatetruecolor( $size, $height );
						$thumb_s = imagecreatefromjpeg( $source );
						imagecopyresampled( $thumb, $thumb_s, 0, 0, 0, 0, $size, $height, $w, $h );
						imagejpeg( $thumb, $this->upload_basedir . $user_id . '/' . $name . '-' . $size . $ext, 100);
					}else if ( $info['mime'] == 'image/png' ){
						$thumb_s  = imagecreatefrompng( $source );
						$thumb = imagecreatetruecolor( $size, $height ); 
						imagealphablending( $thumb, false);
		    			imagesavealpha( $thumb, true);
		    			imagecopyresampled( $thumb, $thumb_s, 0, 0, 0, 0, $size, $height, $w, $h );
						imagepng( $thumb, $this->upload_basedir . $user_id . '/' . $name . '-' . $size . $ext );
					}else if ( $info['mime'] == 'image/gif' ){
						$thumb = imagecreatetruecolor( $size, $height );
						$thumb_s = imagecreatefromgif( $source );
						imagecopyresampled( $thumb, $thumb_s, 0, 0, 0, 0, $size, $height, $w, $h );
						imagegif( $thumb, $this->upload_basedir . $user_id . '/' . $name . '-' . $size . $ext);
					}
				}

			}

		}

		// clean up temp
		$dir = dirname( $source );
		unlink( $source );
		rmdir( $dir );

		// update user's meta
		do_action('um_before_upload_db_meta', $user_id, $key );
		do_action("um_before_upload_db_meta_{$key}", $user_id );

		update_user_meta( $user_id, $key, $filename );

		do_action('um_after_upload_db_meta', $user_id, $key );
		do_action("um_after_upload_db_meta_{$key}", $user_id );

		// the url of upload
		return $this->upload_baseurl . $user_id . '/' . $filename;

	}

	/***
	***	@Remove a directory
	***/
	function remove_dir($dir) {
		if ( file_exists( $dir ) ) {
			foreach(glob($dir . '/*') as $file) {
				if(is_dir($file)) $this->remove_dir($file); else unlink($file);
			} rmdir($dir);
		}
	}

	/***
	*** @Format Bytes
	****/
	function format_bytes( $size , $precision = 1 ) { 
	    
 		$base = log($size, 1024);
	    $suffixes = array('', 'kb', 'MB', 'GB', 'TB');   
	    $computed_size = round(pow(1024, $base - floor($base)), $precision);
	    $unit = $suffixes[ floor($base) ];

	    return   $computed_size.' '.$unit;

	} 

}
