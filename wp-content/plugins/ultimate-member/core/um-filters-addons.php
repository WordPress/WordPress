<?php

	/***
	***	@add-ons panel
	***/
	add_filter("redux/options/um_options/sections", 'um_add_addons_settings_tab', 8 );
	function um_add_addons_settings_tab($sections){
		global $ultimatemember;

		foreach( $ultimatemember->addons as $addon_id => $addon ) {
			$array[] = 	array(
				'id'       		=> 'addon_' . $addon_id,
				'type'     		=> 'switch',
				'title'   		=> $addon[0],
				'desc' 	   		=> $addon[1],
				'on'			=> __('Activated','ultimate-member'),
				'off'			=> __('Deactivated','ultimate-member'),
			);
		}
		
		$array = apply_filters('um_builtin_addons_options', $array );
		
		$sections[] = array(

			'icon'       => 'um-faicon-plug',
			'title'      => __( 'Extensions','ultimate-member'),

		);
		
		$sections[] = array(

			'subsection' => true,
			'title'      => __( 'Tools','ultimate-member'),
			'fields'	 => $array,

		);

		return $sections;
		
	}
	
	/***
	***	@licenses
	***/
	add_filter("redux/options/um_options/sections", 'um_add_licenses_tab', 9999 );
	function um_add_licenses_tab($sections){
		global $ultimatemember;
		
		$fields = array();
		$fields = apply_filters('um_licensed_products_settings', $fields );
		
		if ( $fields ) {
		
		$sections[] = array(

			'icon'       => 'um-faicon-key',
			'title'      => __( 'Licenses','ultimate-member'),
			'fields'	 => $fields,
			'subsection' => false,

		);
		
		}
		
		return $sections;
		
	}