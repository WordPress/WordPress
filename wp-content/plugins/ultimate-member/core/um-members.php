<?php

class UM_Members {

	function __construct() {

		add_filter('user_search_columns', array(&$this, 'user_search_columns'), 99 );

		add_action('template_redirect', array(&$this, 'access_members'), 555);

		$this->core_search_fields = array(
			'user_login',
			'username',
			'display_name',
			'user_email',
		);
		
		add_filter( 'um_search_select_fields', array(&$this, 'um_search_select_fields'), 10, 1 );

	}

	/***
	***	@user_search_columns
	***/
	function user_search_columns( $search_columns ){
		if ( is_admin() ) {
			$search_columns[] = 'display_name';
		}
		return $search_columns;
	}

	/***
	***	@Members page allowed?
	***/
	function access_members() {

		if ( um_get_option('members_page') == 0 && um_is_core_page('members') ) {
			um_redirect_home();
		}

	}

	/***
	***	@tag conversion for member directory
	***/
	function convert_tags( $string, $array ) {

		$search = array(
			'{total_users}',
		);

		$replace = array(
			$array['total_users'],
		);

		$string = str_replace($search, $replace, $string);
		return $string;
	}

	/***
	***	@show filter
	***/
	function show_filter( $filter ) {
		global $ultimatemember;

		$fields = $ultimatemember->builtin->all_user_fields;

		if ( isset( $fields[ $filter ] ) ) {
			$attrs = $fields[ $filter ];
		} else {
			$attrs = apply_filters("um_custom_search_field_{$filter}", array() );
		}

		// additional filter for search field attributes
		$attrs = apply_filters("um_search_field_{$filter}", $attrs);

		if ( $ultimatemember->builtin->is_dropdown_field( $filter, $attrs ) ) {
			$type = 'select';
		} else if ( 'user_tags' == $attrs['type'] ) {
				$attrs['options'] = apply_filters('um_multiselect_options_user_tags', array(), $attrs);
				$attrs['custom']  = 1;
				$type = 'select';
		} else {
			$type = 'text';
		}

		// filter all search fields
		$attrs = apply_filters( 'um_search_fields', $attrs );
		
		if( $type == 'select' ){
		    $attrs = apply_filters( 'um_search_select_fields', $attrs );
		}

		switch( $type ) {

			case 'select':

				?>

				<select name="<?php echo $filter; ?>" id="<?php echo $filter; ?>" class="um-s1" style="width: 100%" data-placeholder="<?php echo __( stripslashes( $attrs['label'] ), 'ultimate-member'); ?>">

					<option></option>

					<?php foreach( $attrs['options'] as $k => $v ) {

						$v = stripslashes($v);

						$opt = $v;

						if ( strstr($filter, 'role_') )
							$opt = $k;

						if ( isset( $attrs['custom'] ) )
							$opt = $k;

					?>

					<option value="<?php echo $opt; ?>" <?php um_select_if_in_query_params( $filter, $opt ); ?>><?php echo __( $v, 'ultimate-member'); ?></option>

					<?php } ?>

				</select>

				<?php

				break;

			case 'text':

				?>

				<input type="text" autocomplete="off" name="<?php echo $filter; ?>" id="<?php echo $filter; ?>" placeholder="<?php echo isset( $attrs['label'] ) ? __( $attrs['label'], 'ultimate-member') : ''; ?>" value='<?php echo esc_attr( um_queried_search_value(  $filter, false ) ); ?>' />

				<?php

				break;

		}

	}
	
	/**
	 * Display assigned roles in search filter 'role' field
	 * @param  	array $attrs 
	 * @return 	array
	 * @uses  	add_filter 'um_search_select_fields'
	 * @since 	1.3.83
	 */
	function um_search_select_fields( $attrs ) {
		global $ultimatemember;

		if( strstr( $attrs['metakey'], 'role_' ) ){

			$shortcode_roles = get_post_meta( $ultimatemember->shortcodes->form_id, '_um_roles', true );
			$um_roles = $ultimatemember->query->get_roles( false );
			
			if( ! empty( $shortcode_roles ) && is_array( $shortcode_roles ) ){ 

				$attrs['options'] = array();

				foreach ( $um_roles as $key => $value ) {
				    if ( in_array( $key, $shortcode_roles ) ) {
						$attrs['options'][ $key ] = $value;
				    }
				}

			}
			
		}

		return $attrs;
 	}


	

	/***
	***	@Generate a loop of results
	***/
	function get_members( $args ){

		global $ultimatemember, $wpdb, $post;

		extract($args);

		$query_args = array();
		$query_args = apply_filters( 'um_prepare_user_query_args', $query_args, $args );
		
		// Prepare for BIG SELECT query
		$wpdb->query('SET SQL_BIG_SELECTS=1');
		
		// number of profiles for mobile
		if ( $ultimatemember->mobile->isMobile() && isset( $profiles_per_page_mobile ) ){
			$profiles_per_page = $profiles_per_page_mobile;
		}

		$query_args['number'] = $profiles_per_page;

		if( isset( $args['number'] ) ){
			$query_args['number'] = $args['number'];
		}

		if(  isset( $args['page'] ) ){
			$members_page = $args['page'];
		}else{
			$members_page = isset( $_REQUEST['members_page'] ) ? $_REQUEST['members_page'] : 1;
		}

		$query_args['paged'] = $members_page;

		if( ! um_user('can_view_all') && is_user_logged_in() ){
			unset( $query_args );
		}
		
		do_action('um_user_before_query', $query_args );
		
		$users = new WP_User_Query( $query_args );

		do_action('um_user_after_query', $query_args, $users );
		
		
		$array['users'] = isset( $users->results ) && ! empty( $users->results ) ? array_unique( $users->results ) : array();

		$array['total_users'] = (isset( $max_users ) && $max_users && $max_users <= $users->total_users ) ? $max_users : $users->total_users;

		$array['page'] = $members_page;

		$array['total_pages'] = ceil( $array['total_users'] / $profiles_per_page );

		$array['header'] = $this->convert_tags( $header, $array );
		$array['header_single'] = $this->convert_tags( $header_single, $array );

		$array['users_per_page'] = $array['users'];

		for( $i = $array['page']; $i <= $array['page'] + 2; $i++ ) {
			if ( $i <= $array['total_pages'] ) {
				$pages_to_show[] = $i;
			}
		}

		if ( isset( $pages_to_show ) && count( $pages_to_show ) < 5 ) {
			$pages_needed = 5 - count( $pages_to_show );

			for ( $c = $array['page']; $c >= $array['page'] - 2; $c-- ) {
				if ( !in_array( $c, $pages_to_show ) && $c > 0 ) {
					$pages_to_add[] = $c;
				}
			}
		}

		if ( isset( $pages_to_add ) ) {

			asort( $pages_to_add );
			$pages_to_show = array_merge( (array)$pages_to_add, $pages_to_show );

			if ( count( $pages_to_show ) < 5 ) {
				if ( max($pages_to_show) - $array['page'] >= 2 ) {
					$pages_to_show[] = max($pages_to_show) + 1;
					if ( count( $pages_to_show ) < 5 ) {
						$pages_to_show[] = max($pages_to_show) + 1;
					}
				} else if ( $array['page'] - min($pages_to_show) >= 2 ) {
					$pages_to_show[] = min($pages_to_show) - 1;
					if ( count( $pages_to_show ) < 5 ) {
						$pages_to_show[] = min($pages_to_show) - 1;
					}
				}
			}

			asort( $pages_to_show );

			$array['pages_to_show'] = $pages_to_show;

		} else {

			if ( isset( $pages_to_show ) && count( $pages_to_show ) < 5 ) {
				if ( max($pages_to_show) - $array['page'] >= 2 ) {
					$pages_to_show[] = max($pages_to_show) + 1;
					if ( count( $pages_to_show ) < 5 ) {
						$pages_to_show[] = max($pages_to_show) + 1;
					}
				} else if ( $array['page'] - min($pages_to_show) >= 2 ) {
					$pages_to_show[] = min($pages_to_show) - 1;
					if ( count( $pages_to_show ) < 5 ) {
						$pages_to_show[] = min($pages_to_show) - 1;
					}
				}
			}

			if ( isset( $pages_to_show ) && is_array( $pages_to_show ) ) {

				asort( $pages_to_show );

				$array['pages_to_show'] = $pages_to_show;

			}

		}

		if ( isset( $array['pages_to_show'] ) ) {

			if ( $array['total_pages'] < count( $array['pages_to_show'] ) ) {
				foreach( $array['pages_to_show'] as $k => $v ) {
					if ( $v > $array['total_pages'] ) unset( $array['pages_to_show'][$k] );
				}
			}

			foreach( $array['pages_to_show'] as $k => $v ) {
				if ( (int)$v <= 0 ) {
					unset( $array['pages_to_show'][$k] );
				}
			}

		}

		return apply_filters('um_prepare_user_results_array', $array );

	}


	/**
	 * Optimizes Member directory with multiple LEFT JOINs
	 * @param  object $vars 
	 * @return object $var
	 */
	public function um_optimize_member_query( $vars ) {
		 	
			global $wpdb;

			$arr_where = explode("\n", $vars->query_where );
			$arr_left_join = explode("LEFT JOIN", $vars->query_from );
			$arr_user_photo_key = array('synced_profile_photo','profile_photo','synced_gravatar_hashed_id');
						
			foreach ( $arr_where as $where ) {
					
					foreach( $arr_user_photo_key as $key ){
						
						if( strpos( $where  , "'".$key."'" ) > -1 ){

								// find usermeta key
								preg_match("#mt[0-9]+.#",  $where, $meta_key );

								// remove period from found meta_key
								$meta_key = str_replace(".","", current( $meta_key ) );

								// remove matched LEFT JOIN clause
								$vars->query_from = str_replace('LEFT JOIN wp_usermeta AS '.$meta_key.' ON ( wp_users.ID = '.$meta_key.'.user_id )', '',  $vars->query_from );

								// prepare EXISTS replacement for LEFT JOIN clauses
								$where_exists = 'um_exist EXISTS( SELECT '.$wpdb->usermeta.'.umeta_id FROM '.$wpdb->usermeta.' WHERE '.$wpdb->usermeta.'.user_id = '.$wpdb->users.'.ID AND '.$wpdb->usermeta.'.meta_key IN("'.implode('","',  $arr_user_photo_key ).'") AND '.$wpdb->usermeta.'.meta_value != "" )';

								// Replace LEFT JOIN clauses with EXISTS and remove duplicates
								if( strpos( $vars->query_where, 'um_exist' ) === FALSE ){
									$vars->query_where = str_replace( $where , $where_exists,  $vars->query_where );
								}else{
									$vars->query_where = str_replace( $where , '1=0',  $vars->query_where );
								}
						}

					}
			
			}	

			$vars->query_where = str_replace("\n", "", $vars->query_where );
			$vars->query_where = str_replace("um_exist", "", $vars->query_where );

		return $vars;
	
	}


}
