<?php

/*
*  Upgrade
*
*  @description: All the functionality for upgrading ACF
*  @since 3.2.6
*  @created: 23/06/12
*/

class acf_upgrade
{

	/*
	*  __construct
	*
	*  @description:
	*  @since 3.1.8
	*  @created: 23/06/12
	*/

	function __construct()
	{
		// actions
		add_action('admin_menu', array($this,'admin_menu'), 11);


		// ajax
		add_action('wp_ajax_acf_upgrade', array($this, 'upgrade_ajax'));
	}


	/*
	*  admin_menu
	*
	*  @description:
	*  @since 3.1.8
	*  @created: 23/06/12
	*/

	function admin_menu()
	{
		// dont run on plugin activate!
		if( isset($_GET['action']) && $_GET['action'] == 'activate-plugin' )
		{
			return;
		}

		
		// vars
		$plugin_version = apply_filters('acf/get_info', 'version');
		$acf_version = get_option('acf_version');
		
		
		// bail early if a new install
		if( empty($acf_version) ) {
		
			update_option('acf_version', $plugin_version );
			return;
			
		}
		
		
		// bail early if $acf_version is >= $plugin_version
		if( version_compare( $acf_version, $plugin_version, '>=') ) {
		
			return;
			
		}
		
		
		// update version
		update_option('acf_version', $plugin_version );
		
		
		// update admin page
		add_submenu_page('edit.php?post_type=acf', __('Upgrade','acf'), __('Upgrade','acf'), 'manage_options','acf-upgrade', array($this,'html') );
	}

	

	/*
	*  html
	*
	*  @description:
	*  @since 3.1.8
	*  @created: 23/06/12
	*/

	function html()
	{
		$version = get_option('acf_version','1.0.5');
		$next = false;

		// list of starting points
		if( $version < '3.0.0' )
		{
			$next = '3.0.0';
		}
		elseif( $version < '3.1.8' )
		{
			$next = '3.1.8';
		}
		elseif( $version < '3.2.5' )
		{
			$next = '3.2.5';
		}
		elseif( $version < '3.3.3' )
		{
			$next = '3.3.3';
		}
		elseif( $version < '3.4.1' )
		{
			$next = '3.4.1';
		}

		?>
		<script type="text/javascript">
		(function($){

			function add_message(messaage)
			{
				$('#wpbody-content').append('<p>' + messaage + '</p>');
			}

			function run_upgrade(version)
			{
				$.ajax({
					url: ajaxurl,
					data: {
						action : 'acf_upgrade',
						version : version
					},
					type: 'post',
					dataType: 'json',
					success: function(json){

						if(json)
						{
							if(json.status)
							{
								add_message(json.message);

								// next update?
								if(json.next)
								{
									run_upgrade(json.next);
								}
								else
								{
									// all done
									add_message('Upgrade Complete! <a href="<?php echo admin_url(); ?>edit.php?post_type=acf">Continue to ACF &raquo;</a>');
								}
							}
							else
							{
								// error!
								add_message('Error: ' + json.message);
							}
						}
						else
						{
							// major error!
							add_message('Sorry. Something went wrong during the upgrade process. Please report this on the support forum');
						}
					}
				});
			}

			<?php if($next){ echo 'run_upgrade("' . $next . '");'; } ?>

		})(jQuery);
		</script>
		<style type="text/css">
			#message {
				display: none;
			}
		</style>
		<?php

		if(!$next)
		{
			echo '<p>No Upgrade Required</p>';
		}
	}


	/*
	*  upgrade_ajax
	*
	*  @description:
	*  @since 3.1.8
	*  @created: 23/06/12
	*/

	function upgrade_ajax()
	{
		// global
		global $wpdb;


		// tables
		$acf_fields = $wpdb->prefix.'acf_fields';
		$acf_values = $wpdb->prefix.'acf_values';
		$acf_rules = $wpdb->prefix.'acf_rules';
		$wp_postmeta = $wpdb->prefix.'postmeta';
		$wp_options = $wpdb->prefix.'options';


		// vars
		$return = array(
			'status'	=>	false,
			'message'	=>	"",
			'next'		=>	false,
		);


		// versions
		switch($_POST['version'])
		{

			/*---------------------
			*
			*	3.0.0
			*
			*--------------------*/

			case '3.0.0':

				// upgrade options first as "field_group_layout" will cause get_fields to fail!

				// get acf's
				$acfs = get_posts(array(
					'numberposts' 	=> -1,
					'post_type' 	=> 'acf',
					'orderby' 		=> 'menu_order title',
					'order' 		=> 'asc',
					'suppress_filters' => false,
				));

				if($acfs)
				{
					foreach($acfs as $acf)
					{
						// position
						update_post_meta($acf->ID, 'position', 'normal');

						//layout
						$layout = get_post_meta($acf->ID, 'field_group_layout', true) ? get_post_meta($acf->ID, 'field_group_layout', true) : 'in_box';
						if($layout == 'in_box')
						{
							$layout = 'default';
						}
						else
						{
							$layout = 'no_box';
						}
						update_post_meta($acf->ID, 'layout', $layout);
						delete_post_meta($acf->ID, 'field_group_layout');

						// show_on_page
						$show_on_page = get_post_meta($acf->ID, 'show_on_page', true) ? get_post_meta($acf->ID, 'show_on_page', true) : array();
						if($show_on_page)
				 		{
				 			$show_on_page = unserialize($show_on_page);
				 		}
				 		update_post_meta($acf->ID, 'show_on_page', $show_on_page);

					}
				}

			    $return = array(
			    	'status'	=>	true,
					'message'	=>	"Migrating Options...",
					'next'		=>	'3.0.0 (step 2)',
			    );

			break;

			/*---------------------
			*
			*	3.0.0
			*
			*--------------------*/

			case '3.0.0 (step 2)':

				// get acf's
				$acfs = get_posts(array(
					'numberposts' 	=> -1,
					'post_type' 	=> 'acf',
					'orderby' 		=> 'menu_order title',
					'order' 		=> 'asc',
					'suppress_filters' => false,
				));

				if($acfs)
				{
					foreach($acfs as $acf)
					{
						// allorany doesn't need to change!

			 			$rules = $wpdb->get_results("SELECT * FROM $acf_rules WHERE acf_id = '$acf->ID' ORDER BY order_no ASC", ARRAY_A);

						if($rules)
						{
							foreach($rules as $rule)
							{
								// options rule has changed
								if($rule['param'] == 'options_page')
								{
									$rule['value'] = 'Options';
								}

								add_post_meta($acf->ID, 'rule', $rule);
							}
						}

					}
				}

			    $return = array(
			    	'status'	=>	true,
					'message'	=>	"Migrating Location Rules...",
					'next'		=>	'3.0.0 (step 3)',
			    );

			break;

			/*---------------------
			*
			*	3.0.0
			*
			*--------------------*/

			case '3.0.0 (step 3)':

				$message = "Migrating Fields?";

			    $parent_id = 0;
			    $fields = $wpdb->get_results("SELECT * FROM $acf_fields WHERE parent_id = $parent_id ORDER BY order_no, name", ARRAY_A);

			 	if($fields)
			 	{
					// loop through fields
				 	foreach($fields as $field)
				 	{

						// unserialize options
						if(@unserialize($field['options']))
						{
							$field['options'] = unserialize($field['options']);
						}
						else
						{
							$field['options'] = array();
						}


				 		// sub fields
				 		if($field['type'] == 'repeater')
				 		{
				 			$field['options']['sub_fields'] = array();

				 			$parent_id = $field['id'];
				 			$sub_fields = $wpdb->get_results("SELECT * FROM $acf_fields WHERE parent_id = $parent_id ORDER BY order_no, name", ARRAY_A);


				 			// if fields are empty, this must be a new or broken acf.
						 	if(empty($sub_fields))
						 	{
						 		$field['options']['sub_fields'] = array();
						 	}
						 	else
						 	{
						 		// loop through fields
							 	foreach($sub_fields as $sub_field)
							 	{
							 		// unserialize options
							 		if(@unserialize($sub_field['options']))
									{
										$sub_field['options'] = @unserialize($sub_field['options']);
									}
									else
									{
										$sub_field['options'] = array();
									}

									// merge options with field
							 		$sub_field = array_merge($sub_field, $sub_field['options']);

							 		unset($sub_field['options']);

									// each field has a unique id!
									if(!isset($sub_field['key'])) $sub_field['key'] = 'field_' . $sub_field['id'];

									$field['options']['sub_fields'][] = $sub_field;
								}
						 	}

				 		}
				 		// end if sub field


				 		// merge options with field
				 		$field = array_merge($field, $field['options']);

				 		unset($field['options']);

				 		// each field has a unique id!
						if(!isset($field['key'])) $field['key'] = 'field_' . $field['id'];

						// update field
						$this->parent->update_field( $field['post_id'], $field);

				 		// create field name (field_rand)
				 		//$message .= print_r($field, true) . '<br /><br />';
				 	}
				 	// end foreach $fields
			 	}


				$return = array(
			    	'status'	=>	true,
					'message'	=>	$message,
					'next'		=>	'3.0.0 (step 4)',
			    );

			break;

			/*---------------------
			*
			*	3.0.0
			*
			*--------------------*/

			case '3.0.0 (step 4)':

				$message = "Migrating Values...";

				// update normal values
				$values = $wpdb->get_results("SELECT v.field_id, m.post_id, m.meta_key, m.meta_value FROM $acf_values v LEFT JOIN $wp_postmeta m ON v.value = m.meta_id WHERE v.sub_field_id = 0", ARRAY_A);
				if($values)
				{
					foreach($values as $value)
					{
						// options page
						if($value['post_id'] == 0) $value['post_id'] = 999999999;

						// unserialize value (relationship, multi select, etc)
						if(@unserialize($value['meta_value']))
						{
							$value['meta_value'] = unserialize($value['meta_value']);
						}

						update_post_meta($value['post_id'], $value['meta_key'], $value['meta_value']);
						update_post_meta($value['post_id'], '_' . $value['meta_key'], 'field_' . $value['field_id']);
					}
				}

				// update repeater values
				$values = $wpdb->get_results("SELECT v.field_id, v.sub_field_id, v.order_no, m.post_id, m.meta_key, m.meta_value FROM $acf_values v LEFT JOIN $wp_postmeta m ON v.value = m.meta_id WHERE v.sub_field_id != 0", ARRAY_A);
				if($values)
				{
					$rows = array();

					foreach($values as $value)
					{
						// update row count
						$row = (int) $value['order_no'] + 1;

						// options page
						if($value['post_id'] == 0) $value['post_id'] = 999999999;

						// unserialize value (relationship, multi select, etc)
						if(@unserialize($value['meta_value']))
						{
							$value['meta_value'] = unserialize($value['meta_value']);
						}

						// current row
						$current_row = isset($rows[$value['post_id']][$value['field_id']]) ? $rows[$value['post_id']][$value['field_id']] : 0;
						if($row > $current_row) $rows[$value['post_id']][$value['field_id']] = (int) $row;

						// get field name
						$field_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM $acf_fields WHERE id = %d", $value['field_id']));

						// get sub field name
						$sub_field_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM $acf_fields WHERE id = %d", $value['sub_field_id']));

						// save new value
						$new_meta_key = $field_name . '_' . $value['order_no'] . '_' . $sub_field_name;
						update_post_meta($value['post_id'], $new_meta_key , $value['meta_value']);

						// save value hidden field id
						update_post_meta($value['post_id'], '_' . $new_meta_key, 'field_' . $value['sub_field_id']);
					}

					foreach($rows as $post_id => $field_ids)
					{
						foreach($field_ids as $field_id => $row_count)
						{
							// get sub field name
							$field_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM $acf_fields WHERE id = %d", $field_id));

							delete_post_meta($post_id, $field_name);
							update_post_meta($post_id, $field_name, $row_count);
							update_post_meta($post_id, '_' . $field_name, 'field_' . $field_id);

						}
					}

				}

				// update version (only upgrade 1 time)
				update_option('acf_version','3.0.0');

			    $return = array(
			    	'status'	=>	true,
					'message'	=>	$message,
					'next'		=>	'3.1.8',
			    );

			break;


			/*---------------------
			*
			*	3.1.8
			*
			*--------------------*/

			case '3.1.8':

				// vars
				$message = __("Migrating options values from the $wp_postmeta table to the $wp_options table",'acf') . '...';

				// update normal values
				$rows = $wpdb->get_results( $wpdb->prepare("SELECT meta_key FROM $wp_postmeta WHERE post_id = %d", 999999999) , ARRAY_A);

				if($rows)
				{
					foreach($rows as $row)
					{
						// original name
						$field_name = $row['meta_key'];


						//  name
						$new_name = "";
						if( substr($field_name, 0, 1) == "_" )
						{
							 $new_name = '_options' . $field_name;
						}
						else
						{
							$new_name = 'options_' . $field_name;
						}


						// value
						$value = get_post_meta( 999999999, $field_name, true );


						// update option
						update_option( $new_name, $value );


						// deleet old postmeta
						delete_post_meta( 999999999, $field_name );

					}
					// foreach($values as $value)
				}
				// if($values)


				// update version
				update_option('acf_version','3.1.8');

			    $return = array(
			    	'status'	=>	true,
					'message'	=>	$message,
					'next'		=>	'3.2.5',
			    );

			break;


			/*---------------------
			*
			*	3.1.8
			*
			*--------------------*/

			case '3.2.5':

				// vars
				$message = __("Modifying field group options 'show on page'",'acf') . '...';


				// get acf's
				$acfs = get_posts(array(
					'numberposts' 	=> -1,
					'post_type' 	=> 'acf',
					'orderby' 		=> 'menu_order title',
					'order' 		=> 'asc',
					'suppress_filters' => false,
				));


				$show_all = array('the_content', 'discussion', 'custom_fields', 'comments', 'slug', 'author');


				// populate acfs
				if($acfs)
				{
					foreach($acfs as $acf)
					{
						$show_on_page = get_post_meta($acf->ID, 'show_on_page', true) ? get_post_meta($acf->ID, 'show_on_page', true) : array();

						$hide_on_screen = array_diff($show_all, $show_on_page);

						update_post_meta($acf->ID, 'hide_on_screen', $hide_on_screen);
						delete_post_meta($acf->ID, 'show_on_page');

					}
				}


				// update version
				update_option('acf_version','3.2.5');

			    $return = array(
			    	'status'	=>	true,
					'message'	=>	$message,
					'next'		=>	'3.3.3',
			    );

			break;


			/*
			*  3.3.3
			*
			*  @description: changed field option: taxonomies filter on relationship / post object and page link fields.
			*  @created: 20/07/12
			*/

			case '3.3.3':

				// vars
				$message = __("Modifying field option 'taxonomy'",'acf') . '...';
				$wp_term_taxonomy = $wpdb->prefix.'term_taxonomy';
				$term_taxonomies = array();

				$rows = $wpdb->get_results("SELECT * FROM $wp_term_taxonomy", ARRAY_A);

				if($rows)
				{
					foreach($rows as $row)
					{
						$term_taxonomies[ $row['term_id'] ] = $row['taxonomy'] . ":" . $row['term_id'];
					}
				}


				// get acf's
				$acfs = get_posts(array(
					'numberposts' 	=> -1,
					'post_type' 	=> 'acf',
					'orderby' 		=> 'menu_order title',
					'order' 		=> 'asc',
					'suppress_filters' => false,
				));

				// populate acfs
				if($acfs)
				{
				foreach($acfs as $acf)
				{
					$fields = $this->parent->get_acf_fields($acf->ID);

					if( $fields )
					{
					foreach( $fields as $field )
					{

						// only edit the option: taxonomy
						if( !isset($field['taxonomy']) )
						{
							continue;
						}


						if( is_array($field['taxonomy']) )
						{
						foreach( $field['taxonomy'] as $k => $v )
						{

							// could be "all"
							if( !is_numeric($v) )
							{
								continue;
							}

							$field['taxonomy'][ $k ] = $term_taxonomies[ $v ];


						}
						// foreach( $field['taxonomy'] as $k => $v )
						}
						// if( $field['taxonomy'] )


						$this->parent->update_field( $acf->ID, $field);

					}
					// foreach( $fields as $field )
					}
					// if( $fields )
				}
				// foreach($acfs as $acf)
				}
				// if($acfs)


				// update version
				update_option('acf_version','3.3.3');

				$return = array(
			    	'status'	=>	true,
					'message'	=>	$message,
					'next'		=>	'3.4.1',
			    );

			break;


			/*
			*  3.4.1
			*
			*  @description: Move user custom fields from wp_options to wp_usermeta
			*  @created: 20/07/12
			*/

			case '3.4.1':

				// vars
				$message = __("Moving user custom fields from wp_options to wp_usermeta'",'acf') . '...';

				$option_row_ids = array();
				$option_rows = $wpdb->get_results("SELECT option_id, option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'user%' OR option_name LIKE '\_user%'", ARRAY_A);


				if( $option_rows )
				{
					foreach( $option_rows as $k => $row)
					{
						preg_match('/user_([0-9]+)_(.*)/', $row['option_name'], $matches);


						// if no matches, this is not an acf value, ignore it
						if( !$matches )
						{
							continue;
						}


						// add to $delete_option_rows
						$option_row_ids[] = $row['option_id'];


						// meta_key prefix
						$meta_key_prefix = "";
						if( substr($row['option_name'], 0, 1) == "_" )
						{
							$meta_key_prefix = '_';
						}


						// update user meta
						update_user_meta( $matches[1], $meta_key_prefix . $matches[2], $row['option_value'] );

					}
				}


				// clear up some memory ( aprox 14 kb )
				unset( $option_rows );


				// remove $option_row_ids
				if( $option_row_ids )
				{
					$option_row_ids = implode(', ', $option_row_ids);

					$wpdb->query("DELETE FROM $wpdb->options WHERE option_id IN ($option_row_ids)");
				}


				// update version
				update_option('acf_version','3.4.1');

				$return = array(
			    	'status'	=>	true,
					'message'	=>	$message,
					'next'		=>	false,
			    );

			break;


		}

		// return json
		echo json_encode($return);
		die;

	}




}

new acf_upgrade();

?>