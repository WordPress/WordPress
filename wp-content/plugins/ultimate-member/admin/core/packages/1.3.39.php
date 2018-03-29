<?php 
	
	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	/**
  	 * This populates all existing UM users with meta_key `last_login` as `user_registered` if the meta key doesn't exist.
  	 * Target Version: 1.3.39
  	 */
  	
	global $wpdb, $ultimatemember;
  	$wpdb->query('INSERT INTO '.$wpdb->usermeta.'(user_id, meta_key, meta_value) 
	   			SELECT uu.ID, "_um_last_login", uu.user_registered
	   				FROM '.$wpdb->users.' AS uu
	   			WHERE 
	   				uu.ID NOT IN(
	   						SELECT user_id FROM '.$wpdb->usermeta.' 
	      					WHERE meta_key = "_um_last_login" 
	      					GROUP BY user_id
	      			)'
  	);



 ?>