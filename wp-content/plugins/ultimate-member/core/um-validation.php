<?php

class UM_Validation {

	function __construct() {

		$this->regex_safe = '/\A[\w\-\.]+\z/';
		$this->regex_phone_number = '/\A[\d\-\.\+\(\)\ ]+\z/';
		
	}
	
	/***
	***	@removes html from any string
	***/
	function remove_html($string) {
		return wp_strip_all_tags( $string );
	}
	
	/***
	***	@normalize a string
	***/
	function normalize($string) {
		$string = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'));
		return $string;
	}
	
	/***
	***	@safe name usage ( for url purposes )
	***/
	function safe_name_in_url( $name ) {
		$name = strtolower( $name );
		$name = preg_replace("/'/","", $name );
		$name = stripslashes( $name );
		$name = $this->normalize($name);
		$name = rawurldecode( $name );
		return $name;
	}
	
	/***
	***	@password test
	***/
	function strong_pass($candidate) {
	   $r1='/[A-Z]/';
	   $r2='/[a-z]/';
	   $r3='/[0-9]/';
	   if(preg_match_all($r1,$candidate, $o)<1) return false;
	   if(preg_match_all($r2,$candidate, $o)<1) return false;
	   if(preg_match_all($r3,$candidate, $o)<1) return false;
	   return true;
	}
	
	/***
	***	@space, dash, underscore
	***/
	function safe_username( $string ) {

		$regex_safe_username = apply_filters('um_validation_safe_username_regex',$this->regex_safe );
		
		if ( is_email( $string ) )
			return true;
		if ( !is_email( $string) && !preg_match( $regex_safe_username, $string ) )
			return false;
		return true;
	}
	
	/***
	***	@dash and underscore (metakey)
	***/
	function safe_string($string){
		
		$regex_safe_string = apply_filters('um_validation_safe_string_regex',$this->regex_safe );
		
		if ( !preg_match( $regex_safe_string, $string) ){
			return false;
		}
		return true;
	}
	
	/***
	***	@is phone number
	***/
	function is_phone_number( $string ){
		if ( !$string )
			return true;
		if ( !preg_match( $this->regex_phone_number, $string) )
			return false;
		return true;
	}
	
	/***
	***	@is url
	***/
	function is_url( $url, $social = false ){
		if ( !$url ) return true;

		if ( $social ) {

			if ( !filter_var($url, FILTER_VALIDATE_URL) && strstr( $url, $social )  ) { // starts with social requested
				return true;
			} else {

				if ( filter_var($url, FILTER_VALIDATE_URL) && strstr( $url, $social ) ) {
					return true;
				} elseif ( preg_match( $this->regex_safe, $url) ) {
				
					if ( strstr( $url, '.com' ) ){
						return false;
					} else {
						return true;
					}
					
				}
				
			}
			
		} else {
			
			if ( strstr( $url, 'http://') || strstr( $url, 'https://') )
				return true;
		
		}
		
		return false;
	}

	/***
	***	@get a random string
	***/
	function randomize( $length = 10 ) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$result = '';
		for ($i = 0; $i < $length; $i++) {
			$result .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $result;
	}

	/***
	***	@generate a password, hash, or similar
	***/
	function generate( $length = 40 ) {
		return wp_generate_password( $length, false );
	}
	
	/***
	***	@random numbers only
	***/
	function random_number($len = false) {
	   $ints = array();
	   $len = $len ? $len : rand(2,9);
	   if($len > 9)
	   {
		  trigger_error('Maximum length should not exceed 9');
		  return 0;
	   }
	   while(true)
	   {
		  $current = rand(0,9);
		  if(!in_array($current,$ints))
		  {
			 $ints[] = $current;
		  }
		  if(count($ints) == $len)
		  {
			  return implode($ints);
		  }
	   }
	}
	
	/***
	***	@To validate given date input
	***/
	function validate_date( $date, $format='YYYY/MM/D' ) {
		if ( strlen( $date ) < strlen($format) ) return false;
		if ( $date[4] != '/' ) return false;
		if ( $date[7] != '/' ) return false;
		if ( false === strtotime($date) ) return false;
		return true;
	}

	/***
	***	@checks if data is serialized
	***/
	function is_serialized( $data ) {
		// if it isn't a string, it isn't serialized
		if ( !is_string( $data ) )
			return false;
		$data = trim( $data );
		if ( 'N;' == $data )
			return true;
		if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
			return false;
		switch ( $badions[1] ) {
			case 'a' :
			case 'O' :
			case 's' :
				if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
					return true;
				break;
			case 'b' :
			case 'i' :
			case 'd' :
				if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
					return true;
				break;
		}
		return false;
	}
	
}