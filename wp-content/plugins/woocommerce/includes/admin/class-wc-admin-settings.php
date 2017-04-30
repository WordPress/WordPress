<?php
/**
 * WooCommerce Admin Settings Class.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_Settings' ) ) :

/**
 * WC_Admin_Settings
 */
class WC_Admin_Settings {

	private static $settings = array();
	private static $errors   = array();
	private static $messages = array();

	/**
	 * Include the settings page classes
	 */
	public static function get_settings_pages() {
		if ( empty( self::$settings ) ) {
			$settings = array();

			include_once( 'settings/class-wc-settings-page.php' );

			$settings[] = include( 'settings/class-wc-settings-general.php' );
			$settings[] = include( 'settings/class-wc-settings-products.php' );
			$settings[] = include( 'settings/class-wc-settings-tax.php' );
			$settings[] = include( 'settings/class-wc-settings-checkout.php' );
			$settings[] = include( 'settings/class-wc-settings-shipping.php' );
			$settings[] = include( 'settings/class-wc-settings-accounts.php' );
			$settings[] = include( 'settings/class-wc-settings-emails.php' );
			$settings[] = include( 'settings/class-wc-settings-integrations.php' );

			self::$settings = apply_filters( 'woocommerce_get_settings_pages', $settings );
		}
		return self::$settings;
	}

	/**
	 * Save the settings
	 */
	public static function save() {
		global $current_section, $current_tab;

		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'woocommerce-settings' ) )
	    		die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );

	    // Trigger actions
	   	do_action( 'woocommerce_settings_save_' . $current_tab );
	    do_action( 'woocommerce_update_options_' . $current_tab );
	    do_action( 'woocommerce_update_options' );

    	// Clear any unwanted data
		wc_delete_product_transients();
		delete_transient( 'woocommerce_cache_excluded_uris' );

		self::add_message( __( 'Your settings have been saved.', 'woocommerce' ) );
		self::check_download_folder_protection();

		// Re-add endpoints and flush rules
		WC()->query->init_query_vars();
		WC()->query->add_endpoints();
		flush_rewrite_rules();

		do_action( 'woocommerce_settings_saved' );
	}

	/**
	 * Add a message
	 * @param string $text
	 */
	public static function add_message( $text ) {
		self::$messages[] = $text;
	}

	/**
	 * Add an error
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$errors[] = $text;
	}

	/**
	 * Output messages + errors
	 */
	public static function show_messages() {
		if ( sizeof( self::$errors ) > 0 ) {
			foreach ( self::$errors as $error )
				echo '<div id="message" class="error fade"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
		} elseif ( sizeof( self::$messages ) > 0 ) {
			foreach ( self::$messages as $message )
				echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
		}
	}

	/**
	 * Settings page.
	 *
	 * Handles the display of the main woocommerce settings page in admin.
	 *
	 * @access public
	 * @return void
	 */
	public static function output() {
	    global $current_section, $current_tab;

	    do_action( 'woocommerce_settings_start' );

	    wp_enqueue_script( 'woocommerce_settings', WC()->plugin_url() . '/assets/js/admin/settings.min.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'iris', 'chosen' ), WC()->version, true );

		wp_localize_script( 'woocommerce_settings', 'woocommerce_settings_params', array(
			'i18n_nav_warning' => __( 'The changes you made will be lost if you navigate away from this page.', 'woocommerce' )
		) );

		// Include settings pages
		self::get_settings_pages();

		// Get current tab/section
		$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );
		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( $_REQUEST['section'] );

	    // Save settings if data has been posted
	    if ( ! empty( $_POST ) )
	    	self::save();

	    // Add any posted messages
	    if ( ! empty( $_GET['wc_error'] ) )
	    	self::add_error( stripslashes( $_GET['wc_error'] ) );

	     if ( ! empty( $_GET['wc_message'] ) )
	    	self::add_message( stripslashes( $_GET['wc_message'] ) );

	    self::show_messages();

	    // Get tabs for the settings page
	    $tabs = apply_filters( 'woocommerce_settings_tabs_array', array() );

	    include 'views/html-admin-settings.php';
	}

	/**
	 * Get a setting from the settings API.
	 *
	 * @param mixed $option
	 * @return string
	 */
	public static function get_option( $option_name, $default = '' ) {
		// Array value
		if ( strstr( $option_name, '[' ) ) {

			parse_str( $option_name, $option_array );

			// Option name is first key
			$option_name = current( array_keys( $option_array ) );

			// Get value
			$option_values = get_option( $option_name, '' );

			$key = key( $option_array[ $option_name ] );

			if ( isset( $option_values[ $key ] ) )
				$option_value = $option_values[ $key ];
			else
				$option_value = null;

		// Single value
		} else {
			$option_value = get_option( $option_name, null );
		}

		if ( is_array( $option_value ) )
			$option_value = array_map( 'stripslashes', $option_value );
		elseif ( ! is_null( $option_value ) )
			$option_value = stripslashes( $option_value );

		return $option_value === null ? $default : $option_value;
	}

	/**
	 * Output admin fields.
	 *
	 * Loops though the woocommerce options array and outputs each field.
	 *
	 * @access public
	 * @param array $options Opens array to output
	 */
	public static function output_fields( $options ) {
	    foreach ( $options as $value ) {
	    	if ( ! isset( $value['type'] ) ) continue;
	    	if ( ! isset( $value['id'] ) ) $value['id'] = '';
	    	if ( ! isset( $value['title'] ) ) $value['title'] = isset( $value['name'] ) ? $value['name'] : '';
	    	if ( ! isset( $value['class'] ) ) $value['class'] = '';
	    	if ( ! isset( $value['css'] ) ) $value['css'] = '';
	    	if ( ! isset( $value['default'] ) ) $value['default'] = '';
	    	if ( ! isset( $value['desc'] ) ) $value['desc'] = '';
	    	if ( ! isset( $value['desc_tip'] ) ) $value['desc_tip'] = false;

	    	// Custom attribute handling
			$custom_attributes = array();

			if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) )
				foreach ( $value['custom_attributes'] as $attribute => $attribute_value )
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';

			// Description handling
			if ( $value['desc_tip'] === true ) {
				$description = '';
				$tip = $value['desc'];
			} elseif ( ! empty( $value['desc_tip'] ) ) {
				$description = $value['desc'];
				$tip = $value['desc_tip'];
			} elseif ( ! empty( $value['desc'] ) ) {
				$description = $value['desc'];
				$tip = '';
			} else {
				$description = $tip = '';
			}

			if ( $description && in_array( $value['type'], array( 'textarea', 'radio' ) ) ) {
				$description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
			} elseif ( $description && in_array( $value['type'], array( 'checkbox' ) ) ) {
				$description =  wp_kses_post( $description );
			} elseif ( $description ) {
				$description = '<span class="description">' . wp_kses_post( $description ) . '</span>';
			}

			if ( $tip && in_array( $value['type'], array( 'checkbox' ) ) ) {

				$tip = '<p class="description">' . $tip . '</p>';

			} elseif ( $tip ) {

				$tip = '<img class="help_tip" data-tip="' . esc_attr( $tip ) . '" src="' . WC()->plugin_url() . '/assets/images/help.png" height="16" width="16" />';

			}

			// Switch based on type
	        switch( $value['type'] ) {

	        	// Section Titles
	            case 'title':
	            	if ( ! empty( $value['title'] ) ) {
	            		echo '<h3>' . esc_html( $value['title'] ) . '</h3>';
	            	}
	            	if ( ! empty( $value['desc'] ) ) {
	            		echo wpautop( wptexturize( wp_kses_post( $value['desc'] ) ) );
	            	}
	            	echo '<table class="form-table">'. "\n\n";
	            	if ( ! empty( $value['id'] ) ) {
	            		do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) );
	            	}
	            break;

	            // Section Ends
	            case 'sectionend':
	            	if ( ! empty( $value['id'] ) ) {
	            		do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) . '_end' );
	            	}
	            	echo '</table>';
	            	if ( ! empty( $value['id'] ) ) {
	            		do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) . '_after' );
	            	}
	            break;

	            // Standard text inputs and subtypes like 'number'
	            case 'text':
	            case 'email':
	            case 'number':
	            case 'color' :
	            case 'password' :

	            	$type 			= $value['type'];
	            	$class 			= '';
	            	$option_value 	= self::get_option( $value['id'], $value['default'] );

	            	if ( $value['type'] == 'color' ) {
	            		$type = 'text';
	            		$value['class'] .= 'colorpick';
		            	$description .= '<div id="colorPickerDiv_' . esc_attr( $value['id'] ) . '" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>';
	            	}

	            	?><tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
	                    	<input
	                    		name="<?php echo esc_attr( $value['id'] ); ?>"
	                    		id="<?php echo esc_attr( $value['id'] ); ?>"
	                    		type="<?php echo esc_attr( $type ); ?>"
	                    		style="<?php echo esc_attr( $value['css'] ); ?>"
	                    		value="<?php echo esc_attr( $option_value ); ?>"
	                    		class="<?php echo esc_attr( $value['class'] ); ?>"
	                    		<?php echo implode( ' ', $custom_attributes ); ?>
	                    		/> <?php echo $description; ?>
	                    </td>
	                </tr><?php
	            break;

	            // Textarea
	            case 'textarea':

	            	$option_value 	= self::get_option( $value['id'], $value['default'] );

	            	?><tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
	                    	<?php echo $description; ?>

	                        <textarea
	                        	name="<?php echo esc_attr( $value['id'] ); ?>"
	                        	id="<?php echo esc_attr( $value['id'] ); ?>"
	                        	style="<?php echo esc_attr( $value['css'] ); ?>"
	                        	class="<?php echo esc_attr( $value['class'] ); ?>"
	                        	<?php echo implode( ' ', $custom_attributes ); ?>
	                        	><?php echo esc_textarea( $option_value );  ?></textarea>
	                    </td>
	                </tr><?php
	            break;

	            // Select boxes
	            case 'select' :
	            case 'multiselect' :

	            	$option_value 	= self::get_option( $value['id'], $value['default'] );

	            	?><tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
	                    	<select
	                    		name="<?php echo esc_attr( $value['id'] ); ?><?php if ( $value['type'] == 'multiselect' ) echo '[]'; ?>"
	                    		id="<?php echo esc_attr( $value['id'] ); ?>"
	                    		style="<?php echo esc_attr( $value['css'] ); ?>"
	                    		class="<?php echo esc_attr( $value['class'] ); ?>"
	                    		<?php echo implode( ' ', $custom_attributes ); ?>
	                    		<?php if ( $value['type'] == 'multiselect' ) echo 'multiple="multiple"'; ?>
	                    		>
		                    	<?php
			                        foreach ( $value['options'] as $key => $val ) {
			                        	?>
			                        	<option value="<?php echo esc_attr( $key ); ?>" <?php

				                        	if ( is_array( $option_value ) )
				                        		selected( in_array( $key, $option_value ), true );
				                        	else
				                        		selected( $option_value, $key );

			                        	?>><?php echo $val ?></option>
			                        	<?php
			                        }
			                    ?>
	                       </select> <?php echo $description; ?>
	                    </td>
	                </tr><?php
	            break;

	            // Radio inputs
	            case 'radio' :

	            	$option_value 	= self::get_option( $value['id'], $value['default'] );

	            	?><tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
	                    	<fieldset>
	                    		<?php echo $description; ?>
	                    		<ul>
	                    		<?php
	                    			foreach ( $value['options'] as $key => $val ) {
			                        	?>
			                        	<li>
			                        		<label><input
				                        		name="<?php echo esc_attr( $value['id'] ); ?>"
				                        		value="<?php echo $key; ?>"
				                        		type="radio"
					                    		style="<?php echo esc_attr( $value['css'] ); ?>"
					                    		class="<?php echo esc_attr( $value['class'] ); ?>"
					                    		<?php echo implode( ' ', $custom_attributes ); ?>
					                    		<?php checked( $key, $option_value ); ?>
				                        		/> <?php echo $val ?></label>
			                        	</li>
			                        	<?php
			                        }
	                    		?>
	                    		</ul>
	                    	</fieldset>
	                    </td>
	                </tr><?php
	            break;

	            // Checkbox input
	            case 'checkbox' :

					$option_value    = self::get_option( $value['id'], $value['default'] );
					$visbility_class = array();

	            	if ( ! isset( $value['hide_if_checked'] ) ) {
	            		$value['hide_if_checked'] = false;
	            	}
	            	if ( ! isset( $value['show_if_checked'] ) ) {
	            		$value['show_if_checked'] = false;
	            	}
	            	if ( $value['hide_if_checked'] == 'yes' || $value['show_if_checked'] == 'yes' ) {
	            		$visbility_class[] = 'hidden_option';
	            	}
	            	if ( $value['hide_if_checked'] == 'option' ) {
	            		$visbility_class[] = 'hide_options_if_checked';
	            	}
	            	if ( $value['show_if_checked'] == 'option' ) {
	            		$visbility_class[] = 'show_options_if_checked';
	            	}

	            	if ( ! isset( $value['checkboxgroup'] ) || 'start' == $value['checkboxgroup'] ) {
	            		?>
		            		<tr valign="top" class="<?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>">
								<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?></th>
								<td class="forminp forminp-checkbox">
									<fieldset>
						<?php
	            	} else {
	            		?>
		            		<fieldset class="<?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>">
	            		<?php
	            	}

	            	if ( ! empty( $value['title'] ) ) {
	            		?>
	            			<legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ) ?></span></legend>
	            		<?php
	            	}

	            	?>
						<label for="<?php echo $value['id'] ?>">
							<input
								name="<?php echo esc_attr( $value['id'] ); ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								type="checkbox"
								value="1"
								<?php checked( $option_value, 'yes'); ?>
								<?php echo implode( ' ', $custom_attributes ); ?>
							/> <?php echo $description ?>
						</label> <?php echo $tip; ?>
					<?php

					if ( ! isset( $value['checkboxgroup'] ) || 'end' == $value['checkboxgroup'] ) {
									?>
									</fieldset>
								</td>
							</tr>
						<?php
					} else {
						?>
							</fieldset>
						<?php
					}
	            break;

	            // Image width settings
	            case 'image_width' :

	            	$width 	= self::get_option( $value['id'] . '[width]', $value['default']['width'] );
	            	$height = self::get_option( $value['id'] . '[height]', $value['default']['height'] );
	            	$crop 	= checked( 1, self::get_option( $value['id'] . '[crop]', $value['default']['crop'] ), false );

	            	?><tr valign="top">
						<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?> <?php echo $tip; ?></th>
	                    <td class="forminp image_width_settings">

	                    	<input name="<?php echo esc_attr( $value['id'] ); ?>[width]" id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo $width; ?>" /> &times; <input name="<?php echo esc_attr( $value['id'] ); ?>[height]" id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo $height; ?>" />px

	                    	<label><input name="<?php echo esc_attr( $value['id'] ); ?>[crop]" id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox" <?php echo $crop; ?> /> <?php _e( 'Hard Crop?', 'woocommerce' ); ?></label>

	                    	</td>
	                </tr><?php
	            break;

	            // Single page selects
	            case 'single_select_page' :

	            	$args = array( 'name'				=> $value['id'],
	            				   'id'					=> $value['id'],
	            				   'sort_column' 		=> 'menu_order',
	            				   'sort_order'			=> 'ASC',
	            				   'show_option_none' 	=> ' ',
	            				   'class'				=> $value['class'],
	            				   'echo' 				=> false,
	            				   'selected'			=> absint( self::get_option( $value['id'] ) )
	            				   );

	            	if( isset( $value['args'] ) )
	            		$args = wp_parse_args( $value['args'], $args );

	            	?><tr valign="top" class="single_select_page">
	                    <th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?> <?php echo $tip; ?></th>
	                    <td class="forminp">
				        	<?php echo str_replace(' id=', " data-placeholder='" . __( 'Select a page&hellip;', 'woocommerce' ) .  "' style='" . $value['css'] . "' class='" . $value['class'] . "' id=", wp_dropdown_pages( $args ) ); ?> <?php echo $description; ?>
				        </td>
	               	</tr><?php
	            break;

	            // Single country selects
	            case 'single_select_country' :
					$country_setting = (string) self::get_option( $value['id'] );
					$countries       = WC()->countries->countries;

	            	if ( strstr( $country_setting, ':' ) ) {
						$country_setting = explode( ':', $country_setting );
						$country         = current( $country_setting );
						$state           = end( $country_setting );
	            	} else {
						$country = $country_setting;
						$state   = '*';
	            	}
	            	?><tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
	                    <td class="forminp"><select name="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" data-placeholder="<?php _e( 'Choose a country&hellip;', 'woocommerce' ); ?>" title="Country" class="chosen_select">
				        	<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
				        </select> <?php echo $description; ?>
	               		</td>
	               	</tr><?php
	            break;

	            // Country multiselects
	            case 'multi_select_countries' :

	            	$selections = (array) self::get_option( $value['id'] );

	            	if ( ! empty( $value['options'] ) )
	            		$countries = $value['options'];
	            	else
	            		$countries = WC()->countries->countries;

	            	asort( $countries );
	            	?><tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
	                    <td class="forminp">
		                    <select multiple="multiple" name="<?php echo esc_attr( $value['id'] ); ?>[]" style="width:350px" data-placeholder="<?php _e( 'Choose countries&hellip;', 'woocommerce' ); ?>" title="Country" class="chosen_select">
					        	<?php
					        		if ( $countries )
					        			foreach ( $countries as $key => $val )
		                    				echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $selections ), true, false ).'>' . $val . '</option>';
		                    	?>
					        </select> <?php if ( $description ) echo $description; ?> </br><a class="select_all button" href="#"><?php _e( 'Select all', 'woocommerce' ); ?></a> <a class="select_none button" href="#"><?php _e( 'Select none', 'woocommerce' ); ?></a>
	               		</td>
	               	</tr><?php
	            break;

	            // Default: run an action
	            default:
	            	do_action( 'woocommerce_admin_field_' . $value['type'], $value );
	            break;
	    	}
		}
	}

	/**
	 * Save admin fields.
	 *
	 * Loops though the woocommerce options array and outputs each field.
	 *
	 * @access public
	 * @param array $options Opens array to output
	 * @return bool
	 */
	public static function save_fields( $options ) {
	    if ( empty( $_POST ) )
	    	return false;

	    // Options to update will be stored here
	    $update_options = array();

	    // Loop options and get values to save
	    foreach ( $options as $value ) {

	    	if ( ! isset( $value['id'] ) )
	    		continue;

	    	$type = isset( $value['type'] ) ? sanitize_title( $value['type'] ) : '';

	    	// Get the option name
	    	$option_value = null;

	    	switch ( $type ) {

		    	// Standard types
		    	case "checkbox" :

		    		if ( isset( $_POST[ $value['id'] ] ) ) {
		    			$option_value = 'yes';
		            } else {
		            	$option_value = 'no';
		            }

		    	break;

		    	case "textarea" :

			    	if ( isset( $_POST[$value['id']] ) ) {
			    		$option_value = wp_kses_post( trim( stripslashes( $_POST[ $value['id'] ] ) ) );
		            } else {
		                $option_value = '';
		            }

		    	break;

		    	case "text" :
		    	case 'email':
	            case 'number':
		    	case "select" :
		    	case "color" :
	            case 'password' :
		    	case "single_select_page" :
		    	case "single_select_country" :
		    	case 'radio' :

		    		if ( $value['id'] == 'woocommerce_price_thousand_sep' || $value['id'] == 'woocommerce_price_decimal_sep' ) {

						// price separators get a special treatment as they should allow a spaces (don't trim)
						if ( isset( $_POST[ $value['id'] ] )  ) {
							$option_value = wp_kses_post( stripslashes( $_POST[ $value['id'] ] ) );
						} else {
			            	$option_value = '';
			            }

		    		} elseif ( $value['id'] == 'woocommerce_price_num_decimals' ) {

						// price separators get a special treatment as they should allow a spaces (don't trim)
						if ( isset( $_POST[ $value['id'] ] )  ) {
							$option_value = absint( $_POST[ $value['id'] ] );
						} else {
			               $option_value = 2;
			            }

		    		} elseif ( $value['id'] == 'woocommerce_hold_stock_minutes' ) {

		    			// Allow > 0 or set to ''
			            if ( ! empty( $_POST[ $value['id'] ] )  ) {
							$option_value = absint( $_POST[ $value['id'] ] );
						} else {
			            	$option_value = '';
			            }

			            wp_clear_scheduled_hook( 'woocommerce_cancel_unpaid_orders' );

			            if ( $option_value != '' )
			            	wp_schedule_single_event( time() + ( absint( $option_value ) * 60 ), 'woocommerce_cancel_unpaid_orders' );

			        } else {

				       if ( isset( $_POST[$value['id']] ) ) {
			            	$option_value = wc_clean( stripslashes( $_POST[ $value['id'] ] ) );
			            } else {
			                $option_value = '';
			            }

			        }

		    	break;

		    	// Special types
		    	case "multiselect" :
		    	case "multi_select_countries" :

		    		// Get countries array
					if ( isset( $_POST[ $value['id'] ] ) )
						$selected_countries = array_map( 'wc_clean', array_map( 'stripslashes', (array) $_POST[ $value['id'] ] ) );
					else
						$selected_countries = array();

					$option_value = $selected_countries;

		    	break;

		    	case "image_width" :

			    	if ( isset( $_POST[$value['id'] ]['width'] ) ) {

		              	$update_options[ $value['id'] ]['width']  = wc_clean( stripslashes( $_POST[ $value['id'] ]['width'] ) );
		              	$update_options[ $value['id'] ]['height'] = wc_clean( stripslashes( $_POST[ $value['id'] ]['height'] ) );

						if ( isset( $_POST[ $value['id'] ]['crop'] ) )
							$update_options[ $value['id'] ]['crop'] = 1;
						else
							$update_options[ $value['id'] ]['crop'] = 0;

		            } else {
		            	$update_options[ $value['id'] ]['width'] 	= $value['default']['width'];
		            	$update_options[ $value['id'] ]['height'] 	= $value['default']['height'];
		            	$update_options[ $value['id'] ]['crop'] 	= $value['default']['crop'];
		            }

		    	break;

		    	// Custom handling
		    	default :

		    		do_action( 'woocommerce_update_option_' . $type, $value );

		    	break;

	    	}

	    	if ( ! is_null( $option_value ) ) {
		    	// Check if option is an array
				if ( strstr( $value['id'], '[' ) ) {

					parse_str( $value['id'], $option_array );

		    		// Option name is first key
		    		$option_name = current( array_keys( $option_array ) );

		    		// Get old option value
		    		if ( ! isset( $update_options[ $option_name ] ) )
		    			 $update_options[ $option_name ] = get_option( $option_name, array() );

		    		if ( ! is_array( $update_options[ $option_name ] ) )
		    			$update_options[ $option_name ] = array();

		    		// Set keys and value
		    		$key = key( $option_array[ $option_name ] );

		    		$update_options[ $option_name ][ $key ] = $option_value;

				// Single value
				} else {
					$update_options[ $value['id'] ] = $option_value;
				}
			}

	    	// Custom handling
	    	do_action( 'woocommerce_update_option', $value );
	    }

	    // Now save the options
	    foreach( $update_options as $name => $value )
	    	update_option( $name, $value );

	    return true;
	}

	/**
	 * Checks which method we're using to serve downloads
	 *
	 * If using force or x-sendfile, this ensures the .htaccess is in place
	 *
	 * @access public
	 * @return void
	 */
	public static function check_download_folder_protection() {
		$upload_dir 		= wp_upload_dir();
		$downloads_url 		= $upload_dir['basedir'] . '/woocommerce_uploads';
		$download_method	= get_option('woocommerce_file_download_method');

		if ( $download_method == 'redirect' ) {

			// Redirect method - don't protect
			if ( file_exists( $downloads_url . '/.htaccess' ) )
				unlink( $downloads_url . '/.htaccess' );

		} else {

			// Force method - protect, add rules to the htaccess file
			if ( ! file_exists( $downloads_url . '/.htaccess' ) ) {
				if ( $file_handle = @fopen( $downloads_url . '/.htaccess', 'w' ) ) {
					fwrite( $file_handle, 'deny from all' );
					fclose( $file_handle );
				}
			}
		}
	}
}

endif;
