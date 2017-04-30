<?php
/**
 * User query based on the WordPress User Query class.
 *
 */
class PB_WP_User_Query {

	/**
	 * Query vars, after parsing
	 *
	 * @since 3.5.0
	 * @access public
	 * @var array
	 */
	var $query_vars = array();

	/**
	 * List of found user ids
	 *
	 * @since 3.1.0
	 * @access private
	 * @var array
	 */
	var $results;

	/**
	 * Total number of found users for the current query
	 *
	 * @since 3.1.0
	 * @access private
	 * @var int
	 */
	var $total_users = 0;

	/**
	 * PHP5 constructor
	 *
	 * @since 3.1.0
	 *
	 * @param string|array $args The query variables
	 * @return PB_WP_User_Query
	 */
	function __construct( $query = null ) {			
		if ( !empty( $query ) ) {
			$order = 'ASC';
			$criteria = 'login';
			$resultsPerPage = 10;
			
			$customUserListingSettings = get_option('customUserListingSettings','not_found');
			if ($customUserListingSettings != 'not_found'){
				$order = strtoupper(trim($customUserListingSettings['sortingOrder']));
				if (trim($customUserListingSettings['sortingCriteria']) !== '')
					$criteria = trim($customUserListingSettings['sortingCriteria']);
				$resultsPerPage = $customUserListingSettings['sortingNumber'];
			}
		
			$this->query_vars = wp_parse_args( $query, array(
				'blog_id'						=> $GLOBALS['blog_id'],
				'role'							=> '*',  				//can be either array or a string
				'meta_key' 						=> '',					//the meta-key of the extra field to search after
				'meta_value'					=> '',					//the meta-value of the extra field to search after
				'meta_compare' 					=> '',					//how to compare found results
				'use_wildcard' 					=> false,				//use the % wildcard in the sql search or not
				'search' 						=> '',					//search the fields for a given value
				'search_only_default_fields'	=> false,				//search the default fields only to improve search time
				'offset'						=> '', 					//where to start the LIMIT (sql) from
				'sorting_criteria'				=> $criteria,			//the final sorting criteria in the listing
				'sorting_order'					=> $order,				//the final sorting order in the listing
				'results_per_page'				=> $resultsPerPage		//number of rows per page
			) );

			$this->select_sorting_order_criteria();
			$this->select_roles();
			$this->select_approved();
			$this->select_visible();
			$this->select_meta_field();
			$this->search_results();
			$this->intersect_results();
			
		}
	}
	
	/**
	 * Get the users based on sorting criteria and order
	 * 
	 * Function identificator: fn0
	 *
	 */
	function select_sorting_order_criteria(){
		global $wpdb;
	
		$qv = &$this->query_vars;
		$this->fn0_query_results_array = array();
		
		$this->fn0_query_fields = "wppb_t1.ID";
		$this->fn0_query_from = "FROM $wpdb->users AS wppb_t1";
		
		if ($qv['sorting_criteria'] == 'ID')
			$criteria = 'wppb_t1.ID';
		elseif ($qv['sorting_criteria'] == 'login')
			$criteria = 'wppb_t1.user_login';
		elseif ($qv['sorting_criteria'] == 'email')
			$criteria = 'wppb_t1.user_email';
		elseif ($qv['sorting_criteria'] == 'url')
			$criteria = 'wppb_t1.user_url';
		elseif ($qv['sorting_criteria'] == 'registered')
			$criteria = 'wppb_t1.user_registered';
		elseif ($qv['sorting_criteria'] == 'nicename')
			$criteria = 'wppb_t1.display_name';
		elseif ($qv['sorting_criteria'] == 'post_count'){
			$where = get_posts_by_author_sql('post');
			$this->fn0_query_from .= " LEFT OUTER JOIN (SELECT post_author, COUNT(*) AS post_count FROM $wpdb->posts $where GROUP BY post_author) p ON wppb_t1.ID = p.post_author";
			$criteria = 'wppb_t1.ID';
		}elseif ($qv['sorting_criteria'] == 'bio'){
			$this->fn0_query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = 'description'";
			$criteria = 'wppb_t2.meta_value';
		}elseif ($qv['sorting_criteria'] == 'aim'){
			$this->fn0_query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = 'aim'";
			$criteria = 'wppb_t2.meta_value';
		}elseif ($qv['sorting_criteria'] == 'yim'){
			$this->fn0_query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = 'yim'";
			$criteria = 'wppb_t2.meta_value';
		}elseif ($qv['sorting_criteria'] == 'jabber'){
			$this->fn0_query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = 'jabber'";
			$criteria = 'wppb_t2.meta_value';
		}elseif ($qv['sorting_criteria'] == 'firstname'){
			$this->fn0_query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = 'first_name'";
			$criteria = 'wppb_t2.meta_value';
		}elseif ($qv['sorting_criteria'] == 'lastname'){
			$this->fn0_query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = 'last_name'";
			$criteria = 'wppb_t2.meta_value';
		}else{
			$wppbFetchArray = get_option('wppb_custom_fields', 'not_found');

			if ($wppbFetchArray != 'not_found')
				foreach($wppbFetchArray as $thisKey => $thisValue){
					if ($thisValue['item_type'] != 'heading'){
						if ($qv['sorting_criteria'] == $thisValue['item_metaName']){
							$this->fn0_query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = '".$thisValue['item_metaName']."'";
							$criteria = 'wppb_t2.meta_value';
						}
					}
				}
		}
		
		
		$this->fn0_query_where = "WHERE 1";
		$this->fn0_query_orderby = "ORDER BY $criteria ". strtoupper(trim($qv['sorting_order']));
		$this->fn0_query_limit = "";

		do_action_ref_array( 'wppb_pre_select_by_sorting_order_and_criteria', array( &$this ) );
	
		$this->fn0_query_results = apply_filters('wppb_select_by_sorting_order_and_criteria_result', $wpdb->get_results(trim("SELECT $this->fn0_query_fields $this->fn0_query_from $this->fn0_query_where $this->fn0_query_orderby $this->fn0_query_limit")));
		
		//create an array with IDs from result
		foreach ($this->fn0_query_results as $qr_key => $qr_value){
			array_push($this->fn0_query_results_array, $qr_value->ID);
		}
		$this->fn0_query_results_array = apply_filters('wppb_select_by_sorting_order_and_criteria_array', $this->fn0_query_results_array);

		do_action_ref_array( 'wppb_post_select_by_sorting_order_and_criteria', array( &$this ) );
	}	
	

	/**
	 * Get the users with a specific role
	 * 
	 * Function identificator: fn1
	 *
	 */
	function select_roles(){
		global $wpdb;
	
		$qv = &$this->query_vars;
		$this->fn1_query_results_array = array();
	
		$this->fn1_query_fields = "wppb_t1.ID";
		
		if (is_string($qv['role']) && (trim($qv['role']) != '*')){
			$qv['role'] = explode(',', trim($qv['role']));
		}
		
		if ((count($qv['role']) > 0) && ($qv['role'] != '*')){
			$this->fn1_query_from = "FROM  $wpdb->users AS wppb_t1 LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = '".$wpdb->prefix."capabilities'";
			
			$this->fn1_query_where = "WHERE (";
			foreach ($qv['role'] as $thisKey => $thisValue){
				$this->fn1_query_where .= "wppb_t2.meta_value LIKE '%".mysql_real_escape_string(trim($thisValue))."%'";
				if ($thisKey < count($qv['role'])-1)
					$this->fn1_query_where .= " OR ";
			}
			$this->fn1_query_where .= ")";
			
		}else{
			$this->fn1_query_from = "FROM  $wpdb->users AS wppb_t1";
			$this->fn1_query_where = "WHERE 1";
		}
			
		$this->fn1_query_orderby = "ORDER BY wppb_t1.ID ASC";
		$this->fn1_query_limit = "";
		
		do_action_ref_array( 'wppb_pre_user_role_select_query', array( &$this ) );
	
		$this->fn1_query_results = apply_filters('wppb_user_role_select_query_result', $wpdb->get_results(trim("SELECT $this->fn1_query_fields $this->fn1_query_from $this->fn1_query_where $this->fn1_query_orderby $this->fn1_query_limit")));
		
		//create an array with IDs from result 
		foreach ($this->fn1_query_results as $qr_key => $qr_value){
			array_push($this->fn1_query_results_array, $qr_value->ID);
		}
		$this->fn1_query_results_array = apply_filters('wppb_user_role_select_query_result_array', $this->fn1_query_results_array);

		do_action_ref_array( 'wppb_post_user_role_select_query', array( &$this ) );
	}	
	
	
	/**
	 * Get only the approved users
	 * 
	 * Function identificator: fn2
	 *
	 */
	function select_approved(){
		global $wpdb;
	
		$qv = &$this->query_vars;
		$this->fn2_query_results_array = array();
		$this->fn2_found_unapproved = false;
		
		$wppb_generalSettings = get_option('wppb_general_settings');
		
		if($wppb_generalSettings['adminApproval'] == 'yes'){
			$arrayID = array();
		
			// Get term by name ''unapproved'' in user_status taxonomy.
			$user_statusTaxID = get_term_by('name', 'unapproved', 'user_status');
			$term_taxonomy_id = $user_statusTaxID->term_taxonomy_id;		
			
			$result = mysql_query("SELECT wppb_t1.ID FROM $wpdb->users AS wppb_t1 LEFT OUTER JOIN $wpdb->term_relationships AS wppb_t0 ON wppb_t1.ID = wppb_t0.object_id WHERE wppb_t0.term_taxonomy_id = $term_taxonomy_id");
			if ($result !== false){
				while ($row = mysql_fetch_assoc($result))
					array_push($arrayID, $row['ID']);
					
				$arrayID= implode( ',', $arrayID );
				
				//now exclude certain users
				if ($arrayID !== ''){
					$this->fn2_found_unapproved = true;
					
					$this->fn2_query_fields = "wppb_t1.ID";
					$this->fn2_query_from = "FROM $wpdb->users AS wppb_t1";
					$this->fn2_query_where = "WHERE wppb_t1.ID NOT IN ($arrayID)";
					$this->fn2_query_orderby = "ORDER BY wppb_t1.ID ASC";
					$this->fn2_query_limit = "";
		
					do_action_ref_array( 'wppb_pre_approved_users_select_query1', array( &$this ) );
				
					$this->fn2_query_results = apply_filters('wppb_approved_users_select_query_result_array', $wpdb->get_results(trim("SELECT $this->fn2_query_fields $this->fn2_query_from $this->fn2_query_where $this->fn2_query_orderby $this->fn2_query_limit")));
					
					//create an array with IDs from result 
					foreach ($this->fn2_query_results as $qr_key => $qr_value){
						array_push($this->fn2_query_results_array, $qr_value->ID);
					}
					$this->fn2_query_results_array = apply_filters('wppb_approved_users_select_query_result_array', $this->fn2_query_results_array);
					
					do_action_ref_array( 'wppb_post_approved_users_select_query1', array( &$this ) );
				}
			}			
		}
	}
	
	
	/**
	 * Get only the users who want their profiles shown; this function eliminates those who have selected "no" to list their profile
	 * 
	 * Function identificator: fn3
	 *
	 */
	function select_visible(){
		global $wpdb;
	
		$this->fn3_query_results_array = array();
		$this->fn3_found_matching_hiden_users = false;
	
		$extraField_meta_key = apply_filters('wppb_display_profile_meta_field_name', '');	//meta-name of the extra-field which checks if the user wants his profile hidden
		$extraField_meta_value = apply_filters('wppb_display_profile_meta_field_value', '');	//the value of the above parameter; the users with these 2 combinations will be excluded
		
		if ((trim($extraField_meta_key) != '') && (trim($extraField_meta_value) != '')){
			$result = mysql_query("SELECT wppb_t1.ID FROM $wpdb->users AS wppb_t1 LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = '".$extraField_meta_key."' WHERE wppb_t2.meta_value LIKE '%".mysql_real_escape_string(trim($extraField_meta_value))."%'");
			
			if ($result !== false){
				$arrayID = array();
				
				while ($row = mysql_fetch_assoc($result))
					array_push($arrayID, $row['ID']);
					
				$arrayID= implode( ',', $arrayID );
				
				//now exclude certain users
				if ($arrayID !== ''){
					$this->fn3_found_matching_hiden_users = true;
				
					$this->fn3_query_fields = "wppb_t1.ID";
					$this->fn3_query_from = "FROM $wpdb->users AS wppb_t1";
					$this->fn3_query_where = "WHERE wppb_t1.ID NOT IN ($arrayID)";
					$this->fn3_query_orderby = "ORDER BY wppb_t1.ID ASC";
					$this->fn3_query_limit = "";
		
					do_action_ref_array( 'wppb_pre_display_profile_select_query1', array( &$this ) );
				
					$this->fn3_query_results = apply_filters('wppb_display_profile_select_query_result1', $wpdb->get_results(trim("SELECT $this->fn3_query_fields $this->fn3_query_from $this->fn3_query_where $this->fn3_query_orderby $this->fn3_query_limit")));
					
					//create an array with IDs from result 
					foreach ($this->fn3_query_results as $qr_key => $qr_value){
						array_push($this->fn3_query_results_array, $qr_value->ID);
					}
					$this->fn3_query_results_array = apply_filters('wppb_display_profile_select_query_result_array1', $this->fn3_query_results_array);

					do_action_ref_array( 'wppb_post_display_profile_select_query1', array( &$this ) );
				}
			}
			
		}else{
			$this->fn3_query_fields = "wppb_t1.ID";
			$this->fn3_query_from = "FROM $wpdb->users AS wppb_t1";
			$this->fn3_query_where = "WHERE 1";
			$this->fn3_query_orderby = "ORDER BY wppb_t1.ID ASC";
			$this->fn3_query_limit = "";

			do_action_ref_array( 'wppb_pre_display_profile_select_query2', array( &$this ) );
		
			$this->fn3_query_results = apply_filters('wppb_display_profile_select_query_result2', $wpdb->get_results(trim("SELECT $this->fn3_query_fields $this->fn3_query_from $this->fn3_query_where $this->fn3_query_orderby $this->fn3_query_limit")));
			
			//create an array with IDs from result 
			foreach ($this->fn3_query_results as $qr_key => $qr_value){
				array_push($this->fn3_query_results_array, $qr_value->ID);
			}
			$this->fn3_query_results_array = apply_filters('wppb_display_profile_select_query_result_array2', $this->fn3_query_results_array);

			do_action_ref_array( 'wppb_post_display_profile_select_query2', array( &$this ) );
		}
	}	
	
	
	/**
	 * Get only the users who have a certain meta_field and meta_value combination
	 * 
	 * Function identificator: fn4
	 *
	 */
	function select_meta_field(){
		global $wpdb;
	
		$qv = &$this->query_vars;
		$this->fn4_query_results_array = array();
	
		$meta_key = apply_filters('wppb_select_meta_field_key', $qv['meta_key']);
		$meta_value = apply_filters('wppb_select_meta_field_value', $qv['meta_value']);
		$meta_compare = apply_filters('wppb_select_meta_field_compare', $qv['meta_compare']);
		$use_wildcard = apply_filters('wppb_select_meta_field_wildcard', $qv['use_wildcard']);
		
		if ((trim($meta_key) != '') && (trim($meta_value) != '') && (trim($meta_compare) != '')){
			if ($use_wildcard)
				$card = '%';
			else
				$card = '';
		
			$this->fn4_query_fields = "wppb_t1.ID";
			$this->fn4_query_from = "FROM $wpdb->users AS wppb_t1 LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = '".$meta_key."'";
			$this->fn4_query_where = "WHERE wppb_t2.meta_value ".$meta_compare." '".$card.mysql_real_escape_string(trim($meta_value)).$card."'";
			$this->fn4_query_orderby = "ORDER BY wppb_t1.ID ASC";
			$this->fn4_query_limit = "";

			do_action_ref_array( 'wppb_pre_custom_meta_select_query1', array( &$this ) );
		
			$this->fn4_query_results = apply_filters('wppb_custom_meta_select_query_result1', $wpdb->get_results(trim("SELECT $this->fn4_query_fields $this->fn4_query_from $this->fn4_query_where $this->fn4_query_orderby $this->fn4_query_limit")));
			
			//create an array with IDs from result
			foreach ($this->fn4_query_results as $qr_key => $qr_value){
				array_push($this->fn4_query_results_array, $qr_value->ID);
			}
			$this->fn4_query_results_array = apply_filters('wppb_custom_meta_select_query_result_array1', $this->fn4_query_results_array);

			do_action_ref_array( 'wppb_post_custom_meta_select_query1', array( &$this ) );
			
		}else{
			$this->fn4_query_fields = "wppb_t1.ID";
			$this->fn4_query_from = "FROM $wpdb->users AS wppb_t1";
			$this->fn4_query_where = "WHERE 1";
			$this->fn4_query_orderby = "ORDER BY wppb_t1.ID ASC";
			$this->fn4_query_limit = "";

			do_action_ref_array( 'wppb_pre_custom_meta_select_query2', array( &$this ) );
		
			$this->fn4_query_results = apply_filters('wppb_custom_meta_select_query_result2', $wpdb->get_results(trim("SELECT $this->fn4_query_fields $this->fn4_query_from $this->fn4_query_where $this->fn4_query_orderby $this->fn4_query_limit")));
			
			//create an array with IDs from result
			foreach ($this->fn4_query_results as $qr_key => $qr_value){
				array_push($this->fn4_query_results_array, $qr_value->ID);
			}
			$this->fn4_query_results_array = apply_filters('wppb_custom_meta_select_query_result_array2', $this->fn4_query_results_array);

			do_action_ref_array( 'wppb_post_custom_meta_select_query2', array( &$this ) );
		}
	}
	
	/**
	 * Get the results if a search has been requested
	 * 
	 * Function identificator: fn5
	 *
	 */
	function search_results(){
		global $wpdb;
	
		$qv = &$this->query_vars;
		$this->fn5_query_results_array = array();
		$this->fn5_search_requested = false;
		
		$searchText = __('Search Users by All Fields', 'profilebuilder');
		$searchText = apply_filters('wppb_userlisting_search_field_text', $searchText);
		
		//only search the fields if the entered search-string differs from the default one
		if ((trim($qv['search']) !== $searchText) && (trim($qv['search']) !== '')){
			$this->fn5_search_requested = true;
			
			$this->fn5_query_fields = "wppb_t1.ID";
			$this->fn5_query_from = "FROM  $wpdb->users AS wppb_t1 
									LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = 'first_name' 
									LEFT OUTER JOIN $wpdb->usermeta AS wppb_t3 ON wppb_t1.ID = wppb_t3.user_id AND wppb_t3.meta_key = 'last_name' 
									LEFT OUTER JOIN $wpdb->usermeta AS wppb_t4 ON wppb_t1.ID = wppb_t4.user_id AND wppb_t4.meta_key = 'nickname'";
									
			$this->fn5_query_where = 	"WHERE wppb_t1.user_login LIKE '%".mysql_real_escape_string(trim($qv['search']))."%' 
										OR 
											wppb_t1.user_nicename LIKE '%".mysql_real_escape_string(trim($qv['search']))."%' 
										OR 
											wppb_t1.user_email LIKE '%".mysql_real_escape_string(trim($qv['search']))."%' 
										OR 
											wppb_t1.user_url LIKE '%".mysql_real_escape_string(trim($qv['search']))."%' 
										OR 
											wppb_t1.user_registered LIKE '%".mysql_real_escape_string(trim($qv['search']))."%' 
										OR 
											wppb_t1.display_name LIKE '%".mysql_real_escape_string(trim($qv['search']))."%'
										OR
											wppb_t2.meta_value LIKE '%".mysql_real_escape_string(trim($qv['search']))."%' 
										OR
											wppb_t3.meta_value LIKE '%".mysql_real_escape_string(trim($qv['search']))."%' 
										OR
											wppb_t4.meta_value LIKE '%".mysql_real_escape_string(trim($qv['search']))."%'";
									
			if ($qv['search_only_default_fields'] !== true){
				$this->fn5_query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS wppb_t5 ON wppb_t1.ID = wppb_t5.user_id AND wppb_t5.meta_key = 'description' 
										LEFT OUTER JOIN $wpdb->usermeta AS wppb_t6 ON wppb_t1.ID = wppb_t6.user_id AND wppb_t6.meta_key = 'aim' 
										LEFT OUTER JOIN $wpdb->usermeta AS wppb_t7 ON wppb_t1.ID = wppb_t7.user_id AND wppb_t7.meta_key = 'yim' 
										LEFT OUTER JOIN $wpdb->usermeta AS wppb_t8 ON wppb_t1.ID = wppb_t8.user_id AND wppb_t8.meta_key = 'jabber' 
										LEFT OUTER JOIN $wpdb->usermeta AS wppb_t9 ON wppb_t1.ID = wppb_t9.user_id AND wppb_t9.meta_key = '".$wpdb->prefix."capabilities'";	
									
				$this->fn5_query_where .= 	" OR
												wppb_t5.meta_value LIKE '%".mysql_real_escape_string(trim($qv['search']))."%' 
											OR
												wppb_t6.meta_value LIKE '%".mysql_real_escape_string(trim($qv['search']))."%' 
											OR
												wppb_t7.meta_value LIKE '%".mysql_real_escape_string(trim($qv['search']))."%' 
											OR
												wppb_t8.meta_value LIKE '%".mysql_real_escape_string(trim($qv['search']))."%' 
											OR 
												wppb_t9.meta_value LIKE '%".mysql_real_escape_string(trim($qv['search']))."%'";

				$i = 9;
				$wppbFetchArray = get_option('wppb_custom_fields', 'not_found');

				if ($wppbFetchArray != 'not_found')
					foreach($wppbFetchArray as $thisKey => $thisValue)
						if ($thisValue['item_type'] != 'heading'){
							$i++;
							
							//set the FROM condition for the custom fields
							$this->fn5_query_from .= " ";
							$this->fn5_query_from .= "LEFT OUTER JOIN $wpdb->usermeta AS wppb_t".$i." ON wppb_t1.ID = wppb_t".$i.".user_id AND wppb_t".$i.".meta_key = '".$thisValue['item_metaName']."'";
							
							//add WHERE conditions for the custom fields
							$this->fn5_query_where .= " ";
							$this->fn5_query_where .= "OR wppb_t".$i.".meta_value LIKE '%".mysql_real_escape_string(trim($qv['search']))."%'";
						}
			}
			
			$this->fn5_query_orderby = "ORDER BY wppb_t1.ID ASC";
			$this->fn5_query_limit = "";

			do_action_ref_array( 'wppb_pre_search_select_query', array( &$this ) );
		
			$this->fn5_query_results = apply_filters('wppb_search_select_query', $wpdb->get_results(trim("SELECT $this->fn5_query_fields $this->fn5_query_from $this->fn5_query_where $this->fn5_query_orderby $this->fn5_query_limit")));
			
			//create an array with IDs from result
			foreach ($this->fn5_query_results as $qr_key => $qr_value){
				array_push($this->fn5_query_results_array, $qr_value->ID);
			}
			$this->fn5_query_results_array = apply_filters('wppb_search_select_query_result_array', $this->fn5_query_results_array);

			do_action_ref_array( 'wppb_post_search_select_query', array( &$this ) );
		}
	}
	 
		
	/**
	 * Get the results from the above 6 functions into one result (intersect results)
	 * 
	 * Function identificator: fn6
	 *
	 */
	function intersect_results(){
		
		$qv = &$this->query_vars;
	
		$this->fn6_query_results_intersected = apply_filters('wppb_array_intersect_results', array_intersect($this->fn0_query_results_array, $this->fn1_query_results_array, $this->fn4_query_results_array));
		
		$wppb_generalSettings = get_option('wppb_general_settings');
		if ( ($wppb_generalSettings['adminApproval'] == 'yes') && (isset($this->fn2_found_unapproved) && $this->fn2_found_unapproved) )
			$this->fn6_query_results_intersected = apply_filters('wppb_array_intersect_results_with_admin_approval', array_intersect($this->fn0_query_results_array, $this->fn1_query_results_array, $this->fn2_query_results_array, $this->fn3_query_results_array, $this->fn4_query_results_array));
			
		if (isset($this->fn3_found_matching_hiden_users) && $this->fn3_found_matching_hiden_users)
			$this->fn6_query_results_intersected = apply_filters('wppb_array_intersect_results_with_hidden_users', array_intersect($this->fn6_query_results_intersected, $this->fn3_query_results_array));
		
		if (isset($this->fn5_search_requested) && $this->fn5_search_requested)
			$this->fn6_query_results_intersected = apply_filters('wppb_array_intersect_results_wtih_search', array_intersect($this->fn6_query_results_intersected, $this->fn5_query_results_array));
	}
	 
	
	/**
	 * Return the total number of users for the current query
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return array
	 */
	function get_total() {
		
		$qv['count_total'] = count ($this->fn6_query_results_intersected);
	
		return	$this->total_users = apply_filters( 'wppb_found_total_users', $qv['count_total'] );
	}
	
	
	/**
	 * Return the list of users
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return array
	 */
	function get_results() {
		
		$r = array();
		$qv = &$this->query_vars;
		$nrOfUsers = $iterator = -1;
		
		if (!empty($this->fn6_query_results_intersected))
			foreach ($this->fn6_query_results_intersected as $userRes => $userID){
				$iterator++;
				
				if ($iterator >= $qv['offset']){	
					$nrOfUsers++;
				
					if ($nrOfUsers < $qv['results_per_page'])
						$r[ $userRes ] = new WP_User( $userID, '', $qv['blog_id'] );
				}
			}

		$this->results = $r;
	
		return $this->results;
	}
}