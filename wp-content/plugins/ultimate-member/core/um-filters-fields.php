<?php

	/***
	***	@field is required?
	***/
	add_filter('um_edit_label_all_fields', 'um_edit_label_all_fields', 10, 2);
	function um_edit_label_all_fields( $label, $data ) {

		$asterisk = um_get_option('form_asterisk');
		if ( $asterisk && isset( $data['required'] ) && $data['required'] == 1 )
			$label = $label . '<span class="um-req" title="'.__('Required','ultimate-member').'">*</span>';

		return $label;
	}

	/***
	***	@outputs a soundcloud track
	***/
	add_filter('um_profile_field_filter_hook__soundcloud_track', 'um_profile_field_filter_hook__soundcloud_track', 99, 2);
	function um_profile_field_filter_hook__soundcloud_track( $value, $data ) {

		if ( !is_numeric( $value ) ) {
			return __('Invalid soundcloud track ID','ultimate-member');
		}

		$value = '<div class="um-soundcloud">
					<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/' . $value . '&amp;color=ff6600&amp;auto_play=false&amp;show_artwork=true"></iframe>
					</div>';

		return $value;
	}

	/***
	***	@outputs a youtube video
	***/
	add_filter('um_profile_field_filter_hook__youtube_video', 'um_profile_field_filter_hook__youtube_video', 99, 2);
	function um_profile_field_filter_hook__youtube_video( $value, $data ) {
		if( empty( $value ) ){
			return '';
		}
		$value = ( strstr( $value, 'http') || strstr( $value, '://' ) ) ? um_youtube_id_from_url( $value ) : $value;
		$value = '<div class="um-youtube">
					<iframe width="600" height="450" src="https://www.youtube.com/embed/' . $value . '" frameborder="0" allowfullscreen></iframe>
					</div>';

		return $value;
	}

	/***
	***	@outputs a vimeo video
	***/
	add_filter('um_profile_field_filter_hook__vimeo_video', 'um_profile_field_filter_hook__vimeo_video', 99, 2);
	function um_profile_field_filter_hook__vimeo_video( $value, $data ) {
		if( empty( $value ) ){
			return '';
		}

		$value = ( !is_numeric( $value ) ) ? (int) substr(parse_url($value, PHP_URL_PATH), 1) : $value;
		$value = '<div class="um-vimeo">
					<iframe src="https://player.vimeo.com/video/'. $value . '" width="600" height="450" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
					</div>';
		return $value;
	}

	/***
	***	@outputs a google map
	***/
	add_filter('um_profile_field_filter_hook__googlemap', 'um_profile_field_filter_hook__googlemap', 99, 2);
	function um_profile_field_filter_hook__googlemap( $value, $data ) {
		$value = '<div class="um-googlemap">
					<iframe width="600" height="450" frameborder="0" style="border:0" src="https://maps.google.it/maps?q=' . urlencode( $value ) . '&output=embed"></iframe>
				</div>';
		return $value;
	}

	/***
	***	@user's registration date
	***/
	add_filter('um_profile_field_filter_hook__user_registered', 'um_profile_field_filter_hook__user_registered', 99, 2);
	function um_profile_field_filter_hook__user_registered( $value, $data ) {
		$value = strtotime($value);
		$value = sprintf(__('Joined %s','ultimate-member'), date_i18n('F d, Y', $value) );
		return $value;
	}

	/***
	***	@last login date
	***/
	add_filter('um_profile_field_filter_hook__last_login', 'um_profile_field_filter_hook__last_login', 99, 2);
	add_filter('um_profile_field_filter_hook___um_last_login', 'um_profile_field_filter_hook__last_login', 99, 2);
	function um_profile_field_filter_hook__last_login( $value, $data ) {

		$value = sprintf( __('Last login: %s','ultimate-member'), um_user_last_login( um_user('ID') ) );
		
		return $value;
	}

	/***
	***	@urls in description
	***/
	add_filter('um_profile_field_filter_hook__textarea', 'um_profile_field_filter_hook__textarea', 99, 2);
	function um_profile_field_filter_hook__textarea( $value, $data ) {
		global $ultimatemember;

		if ( isset( $data ) && isset( $data['html'] ) && $data['html'] == 1 )
			return $value;

		$value = esc_textarea( $value );
		$value = preg_replace('$(https?://[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', ' <a href="$1" target="_blank">$1</a> ', $value." ");
		$value = preg_replace('$(www\.[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', '<a target="_blank" href="http://$1">$1</a> ', $value." ");
		$value = wpautop($value);

		return $value;
	}

	add_filter('um_profile_field_filter_hook__description', 'um_profile_field_filter_hook__description', 99, 2);
	function um_profile_field_filter_hook__description( $value, $data ) {
		global $ultimatemember;

		if ( isset( $data ) && isset( $data['html'] ) && $data['html'] == 1 )
			return $value;

		$value = esc_textarea( $value );
		$value = preg_replace('$(https?://[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', ' <a href="$1" target="_blank">$1</a> ', $value." ");
		$value = preg_replace('$(www\.[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', '<a target="_blank" href="http://$1">$1</a> ', $value." ");
		
		return $value;
	}

	/***
	***	@time
	***/
	add_filter('um_profile_field_filter_hook__time', 'um_profile_field_filter_hook__time', 99, 2);
	function um_profile_field_filter_hook__time( $value, $data ) {
		global $ultimatemember;
		$value = $ultimatemember->datetime->format( $value, $data['format'] );

		$value = str_replace('am', 'a.m.', $value );
		$value = str_replace('pm', 'p.m.', $value );
		return $value;
	}

	/***
	***	@date
	***/
	add_filter('um_profile_field_filter_hook__date', 'um_profile_field_filter_hook__date', 99, 2);
	function um_profile_field_filter_hook__date( $value, $data ) {
		global $ultimatemember;

		if ( $data['pretty_format'] == 1 ) {
			$value = $ultimatemember->datetime->get_age( $value );
		} else {
			$value = $ultimatemember->datetime->format( $value, $data['format'] );
		}

		return $value;
	}

	/***
	***	@file
	***/
	add_filter('um_profile_field_filter_hook__file', 'um_profile_field_filter_hook__file', 99, 2);
	function um_profile_field_filter_hook__file( $value, $data ) {
		global $ultimatemember;

		$uri = um_user_uploads_uri() . $value;
		$extension = pathinfo( $uri, PATHINFO_EXTENSION);

		if ( !file_exists( um_user_uploads_dir() . $value ) ) {
			$value = __('This file has been removed.');
		} else {
			$value = '<div class="um-single-file-preview show">
										<div class="um-single-fileinfo">
											<a href="' . $uri  . '" target="_blank">
												<span class="icon" style="background:'. $ultimatemember->files->get_fonticon_bg_by_ext( $extension ) . '"><i class="'. $ultimatemember->files->get_fonticon_by_ext( $extension ) .'"></i></span>
												<span class="filename">' . $value . '</span>
											</a>
										</div>
							</div>';
		}

		return $value;
	}

	/***
	***	@image
	***/
	add_filter('um_profile_field_filter_hook__image', 'um_profile_field_filter_hook__image', 99, 2);
	function um_profile_field_filter_hook__image( $value, $data ) {
		$uri = um_user_uploads_uri() . $value;
		$title = ( isset( $data['title'] ) ) ? $data['title'] : __('Untitled photo');

		// if value is an image tag
		if( preg_match( '/\<img.*src=\"([^"]+).*/', $value, $matches ) ) {
			$uri   = $matches[1];
			$value = '<div class="um-photo"><a href="#" class="um-photo-modal" data-src="'.$uri.'"><img src="'. $uri .'" alt="'.$title.'" title="'.$title.'" class="" /></a></div>';
		} else if ( file_exists( um_user_uploads_dir() . $value ) ) {
			$value = '<div class="um-photo"><a href="#" class="um-photo-modal" data-src="'.$uri.'"><img src="'. $uri .'" alt="'.$title.'" title="'.$title.'" class="" /></a></div>';
		} else {
			$value = '';
		}

		return $value;
	}

	/***
	***	@global
	***/
	add_filter('um_profile_field_filter_hook__', 'um_profile_field_filter_hook__', 99, 2);
	function um_profile_field_filter_hook__( $value, $data ) {
		global $ultimatemember;
		if ( !$value ) return '';

		if ( ( isset( $data['validate'] ) && $data['validate'] != '' && strstr( $data['validate'], 'url' ) ) || ( isset( $data['type'] ) && $data['type'] == 'url' ) ) {
			$alt = ( isset( $data['url_text'] ) && !empty( $data['url_text'] ) ) ? $data['url_text'] : $value;
			$url_rel = ( isset( $data['url_rel'] ) && $data['url_rel'] == 'nofollow' ) ? 'rel="nofollow"' : '';
			if( !strstr( $value, 'http' )
				&& !strstr( $value, '://' )
				&& !strstr( $value, 'www.' )
				&& !strstr( $value, '.com' )
				&& !strstr( $value, '.net' )
				&& !strstr( $value, '.org' )
			) {
				if ( $data['validate'] == 'soundcloud_url' ) 	$value = 'https://soundcloud.com/' . $value;
				if ( $data['validate'] == 'youtube_url' ) 		$value = 'https://youtube.com/user/' . $value;
				if ( $data['validate'] == 'facebook_url' ) 		$value = 'https://facebook.com/' . $value;
				if ( $data['validate'] == 'twitter_url' ) 		$value = 'https://twitter.com/' . $value;
				if ( $data['validate'] == 'linkedin_url' ) 		$value = 'https://linkedin.com/' . $value;
				if ( $data['validate'] == 'skype' ) 			$value = $value;
				if ( $data['validate'] == 'googleplus_url' ) 	$value = 'https://plus.google.com/' . $value;
				if ( $data['validate'] == 'instagram_url' ) 	$value = 'https://instagram.com/' . $value;
				if ( $data['validate'] == 'vk_url' ) 			$value = 'https://vk.com/' . $value;
			}

			if ( isset( $data['validate'] ) && $data['validate'] == 'skype' ) {

				$value = $value;

			} else {

				if ( strpos($value, 'http://') !== 0 ) {
					$value = 'http://' . $value;
				}
				$data['url_target'] = ( isset( $data['url_target'] ) ) ? $data['url_target'] : '_blank';
				$value = '<a href="'. $value .'" title="'.$alt.'" target="'.$data['url_target'].'" ' . $url_rel . '>'.$alt.'</a>';

			}

		}

		if ( !is_array( $value ) ) {
			if ( is_email( $value ) )
				$value = '<a href="mailto:'. $value.'" title="'.$value.'">'.$value.'</a>';
		} else {
			$value = implode(', ', $value);
		}

		$value = str_replace('https://https://','https://',$value);
		$value = str_replace('http://https://','https://',$value);
		$value = $ultimatemember->shortcodes->emotize( $value );
		return $value;

	}

	/***
	***	@get form fields
	***/
	add_filter('um_get_form_fields', 'um_get_form_fields', 99);
	function um_get_form_fields( $array ) {

		global $ultimatemember;

		$form_id = (isset ( $ultimatemember->fields->set_id ) ) ? $ultimatemember->fields->set_id : null;
		$mode = (isset( $ultimatemember->fields->set_mode ) ) ? $ultimatemember->fields->set_mode : null;

		if ( $form_id && $mode ) {
			$array = $ultimatemember->query->get_attr('custom_fields', $form_id );
		} else {
			$array = '';
		}

		return $array;

	}

	/***
	*** @validate conditional logic
	***/
	add_filter('um_get_custom_field_array', 'um_get_custom_field_array',99,2);

	function um_get_custom_field_array( $array, $fields ){

		if( isset( $array['conditions'] ) ){
				$found = 0;
				for( $a = 0; $a < count( $array['conditions'] ); $a++ ){
					    if(  isset( $array['conditional_value'] ) || isset( $array['conditional_value'.$a] ) ){

							if( isset( $array['conditions'] ) && ! empty( $array['conditions'] ) ){

								$arr_conditions = array();

								foreach ($array['conditions'] as $key => $value) {
									$metakey = $fields[ $value[1] ]['metakey'] ;
									$arr_conditions[ $metakey ] = isset( $_POST[ $metakey ] )? $_POST[ $metakey ]: '';
								}

								foreach ($array['conditions'] as $key => $value) {
									$metakey = $fields[ $value[1] ]['metakey'] ;
									$arr_conditions[ $metakey ] = isset( $_POST[ $metakey ] )? $_POST[ $metakey ]: '';
									 if( isset( $_POST[ $metakey ] ) &&   isset( $array['conditional_value'] )  && $_POST[ $metakey ] !== $array['conditional_value'] ){
									 		$array['required'] = 0;
									 }
									 if( isset( $_POST[ $metakey ] ) && isset( $array['conditional_value'.$a] ) &&  $_POST[ $metakey ] !== $array['conditional_value'.$a] ){
									 		$array['required'] = 0;
									 }
								}

							}
						}
				}
		}

		 return $array;
	}


	/**
	 * Force fields to use UTF-8 encoding
	 * @param  mixed $value
	 * @return mixed
	 * @uses  hook filter: um_profile_field_filter_hook__
	 */
	add_filter('um_profile_field_filter_hook__','um_force_utf8_fields',1,10);
	function um_force_utf8_fields( $value ){

		if( ! um_get_option('um_force_utf8_strings') )
			return $value;

		$value = um_force_utf8_string( $value );

		return $value;

	}

	/**
	 * Filter profile data value
	 * @param  mixed $value
	 * @return mixed
	 * @uses   hook filter: um_is_selected_filter_value
	 */
	add_filter('um_is_selected_filter_value','um_is_selected_filter_value',1,9);
	add_filter('um_select_dropdown_dynamic_option_value','um_is_selected_filter_value',1,10);
	function um_is_selected_filter_value( $value ){
		global $ultimatemember;

		if( ! um_get_option('um_force_utf8_strings') )
			return $value;

		$value = um_force_utf8_string( $value );

		return $value;
	}

	/**
	 * Filter select dropdown to use UTF-8 encoding
	 * @param  array $options
	 * @param  array $data
	 * @return $options
	 * @uses   hook filter: um_select_dropdown_dynamic_options
	 */
	add_filter('um_select_dropdown_dynamic_options','um_select_dropdown_dynamic_options_to_utf8',2,10);
	function um_select_dropdown_dynamic_options_to_utf8( $options, $data ){
		global $ultimatemember;

		if( ! um_get_option('um_force_utf8_strings') )
			return $options;

		foreach ( $options as $key => $value ) {
			$options[ $key ] = um_force_utf8_string( $value );

		}

		return $options;
	}
    

    /**
     * Filter non-UTF8 strings
     * @param  string $value 
     * @return string
     * @uses hook filter: um_field_non_utf8_value
     */
    add_filter('um_field_non_utf8_value','um_field_non_utf8_value');
    function um_field_non_utf8_value( $value ){
    	
    	$encoding = mb_detect_encoding( $value, 'utf-8, iso-8859-1, ascii', true);
	    if (strcasecmp($encoding, 'UTF-8') !== 0) {
	      	$value = iconv($encoding, 'utf-8', $value);
	    }

		return $value;
    }
    

    /**
     * Returns dropdown/multi-select options from a callback function
     * @param  $options array
     * @param  $data array
     * @return $options array
     * @uses   hook filter: um_select_dropdown_dynamic_options, um_multiselect_options
     */
    add_filter('um_select_dropdown_dynamic_options','um_select_dropdown_dynamic_callback_options', 10, 2);
    add_filter('um_multiselect_options','um_select_dropdown_dynamic_callback_options', 10, 2);
    function um_select_dropdown_dynamic_callback_options( $options, $data ){
        
        if( isset( $data['custom_dropdown_options_source'] ) && ! empty( $data['custom_dropdown_options_source'] ) ){

        	if( function_exists( $data['custom_dropdown_options_source'] ) ){
        		$options = call_user_func( $data['custom_dropdown_options_source'] );
        	}
        }

    	return $options;
    }

    /**
     *
     * Pair dropdown/multi-select options from a callback function
     * @param  $value string
     * @param  $type  string
     * @param  $data  array
     * @return $value string
     * @uses   hook filter: um_profile_field_filter_hook__
     */
    add_filter('um_profile_field_filter_hook__select','um_option_match_callback_view_field', 10, 2);
    add_filter('um_profile_field_filter_hook__multiselect','um_option_match_callback_view_field', 10, 2);
    add_filter('um_field_select_default_value','um_option_match_callback_view_field', 10, 2);
    add_filter('um_field_multiselect_default_value','um_option_match_callback_view_field', 10, 2);
    function um_option_match_callback_view_field( $value, $data ){
    	global $ultimatemember;
		
		if( ! empty( $data['custom_dropdown_options_source'] ) ){
			return $ultimatemember->fields->get_option_value_from_callback( $value, $data, $data['type'] );
		}

    	return $value;
    }

    /**
     * Apply textdomain in select/multi-select options
     * @param  $value string
     * @param  $type  string
     * @param  $data  array
     * @return $value string
     * @uses   hook filters: um_profile_field_filter_hook__select, um_profile_field_filter_hook__multiselect
     */
    add_filter('um_profile_field_filter_hook__select','um_profile_field__select_translate', 10, 2);
    add_filter('um_profile_field_filter_hook__multiselect','um_profile_field__select_translate', 10, 2);
    function um_profile_field__select_translate( $value, $data ){

    	if( empty( $value  ) ) return $value;

    	$options = explode(", ", $value );
    	$arr_options = array();
    	if( is_array( $options ) ){
    		foreach ( $options as $item ) {
    			$arr_options[] = __( $item, 'ultimate-member');
    		}
    	}

    	$value = implode(", ", $arr_options);

    	return $value;
    }



