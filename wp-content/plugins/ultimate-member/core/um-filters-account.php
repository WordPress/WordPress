<?php

	/**
	 * Account default tabs
	 * @param  array $tabs 
	 * @return array $tabs
	 * @uses  um_account_page_default_tabs_hook
	 */
	add_filter('um_account_page_default_tabs_hook', 'um_account_page_default_tabs_hook' );
	function um_account_page_default_tabs_hook( $tabs ) {
		global $ultimatemember;
		
		foreach ($tabs as $k => $arr ) {
			foreach( $arr as $id => $info ) {
				
				$output = $ultimatemember->account->get_tab_output( $id );
				if ( !$output ) {
					unset( $tabs[$k][$id] );
				}
				
				if ( $id == 'delete' ) {
					if ( !um_user('can_delete_profile') && !um_user('can_delete_everyone') ) {

						unset( $tabs[$k][$id] );
					}
				}
				
			}
		}
		
		return $tabs;
	
	}

	/**
	 * Account secure fields
	 * @param  array $fields 
	 * @param  string $tab_key 
	 * @return array       
	 * @uses  um_account_secure_fields
	 */
	add_filter('um_account_secure_fields','um_account_secure_fields', 10, 2);
	function um_account_secure_fields( $fields, $tab_key ){
		global $ultimatemember;
		$secure = apply_filters('um_account_secure_fields__enabled', true );

		if( ! $secure ) return $fields;

		
		if( isset( $ultimatemember->account->register_fields ) && ! isset( $ultimatemember->account->register_fields[ $tab_key ] ) ){
			$ultimatemember->account->register_fields[ $tab_key ] = $fields;
		}

		

		return $fields;
	}

	/**
	 * Disables first and last name fields in account page
	 * @param  array $fields 
	 * @return array     
	 * @uses  um_get_field__first_name, um_get_field__last_name  
	 */
	add_filter("um_get_field__first_name","um_account_disable_name_fields", 10 ,1 );
	add_filter("um_get_field__last_name","um_account_disable_name_fields", 10 ,1 );
	function um_account_disable_name_fields( $fields ){
		global $ultimatemember;
		
		if( ! um_get_option("account_name_disable") ) return $fields;

		if( um_is_core_page("account") ){
			$fields['disabled'] = 'disabled="disabled"';
		}

		return $fields;
	}