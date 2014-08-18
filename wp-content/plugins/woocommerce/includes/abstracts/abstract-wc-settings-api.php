<?php
/**
 * Admin Settings API used by Shipping Methods and Payment Gateways
 *
 * @class 		WC_Settings_API
 * @version		2.1.0
 * @package		WooCommerce/Abstracts
 * @category	Abstract Class
 * @author 		WooThemes
 */
abstract class WC_Settings_API {

	/** @var string The plugin ID. Used for option names. */
	public $plugin_id = 'woocommerce_';

	/** @var array Array of setting values. */
	public $settings = array();

	/** @var array Array of form option fields. */
	public $form_fields = array();

	/** @var array Array of validation errors. */
	public $errors = array();

	/** @var array Sanitized fields after validation. */
	public $sanitized_fields = array();

	/**
	 * Admin Options
	 *
	 * Setup the gateway settings screen.
	 * Override this in your gateway.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_options() { ?>
		<h3><?php echo ( ! empty( $this->method_title ) ) ? $this->method_title : __( 'Settings', 'woocommerce' ) ; ?></h3>

		<?php echo ( ! empty( $this->method_description ) ) ? wpautop( $this->method_description ) : ''; ?>

		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table><?php
	}

	/**
	 * Initialise Settings Form Fields
	 *
	 * Add an array of fields to be displayed
	 * on the gateway's settings screen.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function init_form_fields() {}

	/**
	 * Get the form fields after they are initialized
	 * 
	 * @return array of options
	 */
	public function get_form_fields() {
		return apply_filters( 'woocommerce_settings_api_form_fields_' . $this->id, $this->form_fields );
	}

	/**
	 * Admin Panel Options Processing
	 * - Saves the options to the DB
	 *
	 * @since 1.0.0
	 * @access public
	 * @return bool
	 */
    public function process_admin_options() {
    	$this->validate_settings_fields();

    	if ( count( $this->errors ) > 0 ) {
    		$this->display_errors();
    		return false;
    	} else {
    		update_option( $this->plugin_id . $this->id . '_settings', apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->id, $this->sanitized_fields ) );
    		$this->init_settings();
    		return true;
    	}
    }

    /**
     * Display admin error messages.
     *
     * @since 1.0.0
	 * @access public
	 * @return void
	 */
    public function display_errors() {}

	/**
     * Initialise Gateway Settings
     *
     * Store all settings in a single database entry
     * and make sure the $settings array is either the default
     * or the settings stored in the database.
     *
     * @since 1.0.0
     * @uses get_option(), add_option()
	 * @access public
	 * @return void
	 */
    public function init_settings() {
    	// Load form_field settings
    	$this->settings = get_option( $this->plugin_id . $this->id . '_settings', null );

    	if ( ! $this->settings || ! is_array( $this->settings ) ) {

	    	$this->settings = array();

    		// If there are no settings defined, load defaults
    		if ( $form_fields = $this->get_form_fields() )
	    		foreach ( $form_fields as $k => $v )
	    			$this->settings[ $k ] = isset( $v['default'] ) ? $v['default'] : '';
    	}

        if ( $this->settings && is_array( $this->settings ) ) {
			$this->settings = array_map( array( $this, 'format_settings' ), $this->settings );
			$this->enabled  = isset( $this->settings['enabled'] ) && $this->settings['enabled'] == 'yes' ? 'yes' : 'no';
        }
    }

    /**
     * get_option function.
     *
     * Gets and option from the settings API, using defaults if necessary to prevent undefined notices.
     *
     * @access public
     * @param string $key
     * @param mixed $empty_value
     * @return string The value specified for the option or a default value for the option
     */
    public function get_option( $key, $empty_value = null ) {
	    if ( empty( $this->settings ) )
	    	$this->init_settings();

    	// Get option default if unset
	    if ( ! isset( $this->settings[ $key ] ) ) {
			$form_fields            = $this->get_form_fields();
			$this->settings[ $key ] = isset( $form_fields[ $key ]['default'] ) ? $form_fields[ $key ]['default'] : '';
	    }

	    if ( ! is_null( $empty_value ) && empty( $this->settings[ $key ] ) )
	    	$this->settings[ $key ] = $empty_value;

	    return $this->settings[ $key ];
    }

    /**
     * Decode values for settings.
     *
     * @access public
     * @param mixed $value
     * @return array
     */
    public function format_settings( $value ) {
    	return is_array( $value ) ? $value : $value;
    }

    /**
     * Generate Settings HTML.
     *
     * Generate the HTML for the fields on the "settings" screen.
     *
     * @access public
     * @param bool $form_fields (default: false)
     * @since 1.0.0
     * @uses method_exists()
	 * @access public
	 * @return string the html for the settings
     */
    public function generate_settings_html( $form_fields = false ) {
    	if ( ! $form_fields )
    		$form_fields = $this->get_form_fields();

    	$html = '';
    	foreach ( $form_fields as $k => $v ) {
    		if ( ! isset( $v['type'] ) || ( $v['type'] == '' ) )
    			$v['type'] = 'text'; // Default to "text" field type.

    		if ( method_exists( $this, 'generate_' . $v['type'] . '_html' ) ) {
    			$html .= $this->{'generate_' . $v['type'] . '_html'}( $k, $v );
    		} else {
	    		$html .= $this->{'generate_text_html'}( $k, $v );
    		}
    	}

    	echo $html;
    }

    /**
     * Get HTML for tooltips
     * @param  array $data
     * @return string
     */
    public function get_tooltip_html( $data ) {
    	if ( $data['desc_tip'] === true ) {
			$tip = $data['description'];
		} elseif ( ! empty( $data['desc_tip'] ) ) {
			$tip = $data['desc_tip'];
		} else {
			$tip = '';
		}

		return $tip ? '<img class="help_tip" data-tip="' . esc_attr( $tip ) . '" src="' . WC()->plugin_url() . '/assets/images/help.png" height="16" width="16" />' : '';
    }

    /**
     * Get HTML for descriptions
     * @param  array $data
     * @return string
     */
    public function get_description_html( $data ) {
    	if ( $data['desc_tip'] === true ) {
			$description = '';
		} elseif ( ! empty( $data['desc_tip'] ) ) {
			$description = $data['description'];
		} elseif ( ! empty( $data['description'] ) ) {
			$description = $data['description'];
		} else {
			$description = '';
		}

    	return $description ? '<p class="description">' . wp_kses_post( $description ) . '</p>' . "\n" : '';
    }

    /**
     * Get custom attributes
     * @param  array $data
     * @return string
     */
    public function get_custom_attribute_html( $data ) {
    	$custom_attributes = array();

    	if ( ! empty( $data['custom_attributes'] ) && is_array( $data['custom_attributes'] ) )
			foreach ( $data['custom_attributes'] as $attribute => $attribute_value )
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';

		return implode( ' ', $custom_attributes );
    }

    /**
     * Generate Text Input HTML.
     *
     * @access public
     * @param mixed $key
     * @param mixed $data
     * @since 1.0.0
     * @return string
     */
    public function generate_text_html( $key, $data ) {
    	$field    = $this->plugin_id . $this->id . '_' . $key;
    	$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $this->get_option( $key ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
    }

    /**
     * Generate Password Input HTML.
     *
     * @access public
     * @param mixed $key
     * @param mixed $data
     * @since 1.0.0
     * @return string
     */
    public function generate_price_html( $key, $data ) {
     	$field    = $this->plugin_id . $this->id . '_' . $key;
    	$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<input class="wc_input_price input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="text" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $this->get_option( $key ) ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
    }

    /**
     * Generate Password Input HTML.
     *
     * @access public
     * @param mixed $key
     * @param mixed $data
     * @since 1.0.0
     * @return string
     */
    public function generate_decimal_html( $key, $data ) {
     	$field    = $this->plugin_id . $this->id . '_' . $key;
    	$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<input class="wc_input_decimal input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="text" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( wc_format_localized_decimal( $this->get_option( $key ) ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
    }

    /**
     * Generate Password Input HTML.
     *
     * @access public
     * @param mixed $key
     * @param mixed $data
     * @since 1.0.0
     * @return string
     */
    public function generate_password_html( $key, $data ) {
    	$data['type'] = 'password';
    	return $this->generate_text_html( $key, $data );
    }

    /**
     * Generate Textarea HTML.
     *
     * @access public
     * @param mixed $key
     * @param mixed $data
     * @since 1.0.0
     * @return string
     */
    public function generate_textarea_html( $key, $data ) {
    	$field    = $this->plugin_id . $this->id . '_' . $key;
    	$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<textarea rows="3" cols="20" class="input-text wide-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>><?php echo esc_textarea( $this->get_option( $key ) ); ?></textarea>
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
    }

    /**
     * Generate Checkbox HTML.
     *
     * @access public
     * @param mixed $key
     * @param mixed $data
     * @since 1.0.0
     * @return string
     */
    public function generate_checkbox_html( $key, $data ) {
    	$field    = $this->plugin_id . $this->id . '_' . $key;
    	$defaults = array(
			'title'             => '',
			'label'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);

		$data = wp_parse_args( $data, $defaults );

		if ( ! $data['label'] )
			$data['label'] = $data['title'];

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<label for="<?php echo esc_attr( $field ); ?>">
					<input <?php disabled( $data['disabled'], true ); ?> class="<?php echo esc_attr( $data['class'] ); ?>" type="checkbox" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="1" <?php checked( $this->get_option( $key ), 'yes' ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> /> <?php echo wp_kses_post( $data['label'] ); ?></label><br/>
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
    }

    /**
     * Generate Select HTML.
     *
     * @access public
     * @param mixed $key
     * @param mixed $data
     * @since 1.0.0
     * @return string
     */
    public function generate_select_html( $key, $data ) {
    	$field    = $this->plugin_id . $this->id . '_' . $key;
    	$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'options'           => array()
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<select class="select <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>>
						<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
							<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, esc_attr( $this->get_option( $key ) ) ); ?>><?php echo esc_attr( $option_value ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
    }

    /**
     * Generate Multiselect HTML.
     *
     * @access public
     * @param mixed $key
     * @param mixed $data
     * @since 1.0.0
     * @return string
     */
    public function generate_multiselect_html( $key, $data ) {
    	$field    = $this->plugin_id . $this->id . '_' . $key;
    	$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'options'           => array()
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<select multiple="multiple" class="multiselect <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field ); ?>[]" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>>
						<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
							<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( in_array( $option_key, $this->get_option( $key, array() ) ), true ); ?>><?php echo esc_attr( $option_value ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
    }

	/**
     * Generate Title HTML.
     *
     * @access public
     * @param mixed $key
     * @param mixed $data
     * @since 1.6.2
     * @return string
     */
	public function generate_title_html( $key, $data ) {
    	$defaults = array(
			'title'             => '',
			'class'             => '',
			'css'               => ''
		);

		$data = wp_parse_args( $data, $defaults );

    	ob_start();
		?>
			</table>
			<h4 class="<?php echo esc_attr( $data['class'] ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></h4>
			<?php if ( ! empty( $data['description'] ) ) : ?>
				<p><?php echo wp_kses_post( $data['description'] ); ?></p>
			<?php endif; ?>
			<table class="form-table">
		<?php
		return ob_get_clean();
    }

    /**
     * Validate Settings Field Data.
     *
     * Validate the data on the "Settings" form.
     *
     * @since 1.0.0
     * @uses method_exists()
     * @param bool $form_fields (default: false)
     */
    public function validate_settings_fields( $form_fields = false ) {
    	if ( ! $form_fields )
    		$form_fields = $this->get_form_fields();

    	$this->sanitized_fields = array();

    	foreach ( $form_fields as $k => $v ) {
    		if ( empty( $v['type'] ) )
    			$v['type'] = 'text'; // Default to "text" field type.

    		// Look for a validate_FIELDID_field method for special handling
    		if ( method_exists( $this, 'validate_' . $k . '_field' ) ) {
    			$field = $this->{'validate_' . $k . '_field'}( $k );
    			$this->sanitized_fields[ $k ] = $field;

    		// Look for a validate_FIELDTYPE_field method
    		} elseif ( method_exists( $this, 'validate_' . $v['type'] . '_field' ) ) {
    			$field = $this->{'validate_' . $v['type'] . '_field'}( $k );
    			$this->sanitized_fields[ $k ] = $field;
    		
    		// Default to text
    		} else {
    			$field = $this->{'validate_text_field'}( $k );
    			$this->sanitized_fields[ $k ] = $field;
    		}
    	}
    }

    /**
     * Validate Checkbox Field.
     *
     * If not set, return "no", otherwise return "yes".
     *
     * @access public
     * @param mixed $key
     * @since 1.0.0
     * @return string
     */
    public function validate_checkbox_field( $key ) {
    	$status = 'no';
    	if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) && ( 1 == $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ) {
    		$status = 'yes';
    	}

    	return $status;
    }

    /**
     * Validate Text Field.
     *
     * @param mixed $key
     * @return string
     */
    public function validate_text_field( $key ) {
    	$text = $this->get_option( $key );

    	if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) )
    		$text = wp_kses_post( trim( stripslashes( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ) );

    	return $text;
    }

    /**
     * Validate Price Field.
     *
     * @param mixed $key
     * @return string
     */
    public function validate_price_field( $key ) {
    	$text = $this->get_option( $key );

    	if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ) {
    		if ( $_POST[ $this->plugin_id . $this->id . '_' . $key ] !== '' )
    			$text = wc_format_decimal( trim( stripslashes( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ) );
    		else
    			$text = '';
    	}

    	return $text;
    }   

    /**
     * Validate Price Field.
     *
     * @param mixed $key
     * @return string
     */
    public function validate_decimal_field( $key ) {
    	$text = $this->get_option( $key );

    	if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ) {
    		if ( $_POST[ $this->plugin_id . $this->id . '_' . $key ] !== '' )
    			$text = wc_format_decimal( trim( stripslashes( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ) );
    		else
    			$text = '';
    	}

    	return $text;
    }  

    /**
     * Validate Password Field.
     *
     * Make sure the data is escaped correctly, etc.
     *
     * @access public
     * @param mixed $key
     * @since 1.0.0
     * @return string
     */
    public function validate_password_field( $key ) {
    	$text = $this->get_option( $key );

    	if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ) {
    		$text = wc_clean( stripslashes( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) );
    	}

    	return $text;
    }

    /**
     * Validate Textarea Field.
     *
     * Make sure the data is escaped correctly, etc.
     *
     * @access public
     * @param mixed $key
     * @since 1.0.0
     * @return string
     */
    public function validate_textarea_field( $key ) {
    	$text = $this->get_option( $key );

    	if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ) {
    		$text = wp_kses( trim( stripslashes( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ),
    			array_merge(
    				array(
    					'iframe' => array( 'src' => true, 'style' => true, 'id' => true, 'class' => true )
    				),
    				wp_kses_allowed_html( 'post' )
    			)
    		);
    	}

    	return $text;
    }

    /**
     * Validate Select Field.
     *
     * Make sure the data is escaped correctly, etc.
     *
     * @access public
     * @param mixed $key
     * @since 1.0.0
     * @return string
     */
    public function validate_select_field( $key ) {
    	$value = $this->get_option( $key );

    	if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ) {
    		$value = wc_clean( stripslashes( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) );
    	}

    	return $value;
    }

    /**
     * Validate Multiselect Field.
     *
     * Make sure the data is escaped correctly, etc.
     *
     * @access public
     * @param mixed $key
     * @since 1.0.0
     * @return string
     */
    public function validate_multiselect_field( $key ) {
    	$value = $this->get_option( $key );

    	if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ) {
    		$value = array_map( 'wc_clean', array_map( 'stripslashes', (array) $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) );
    	} else {
	    	$value = '';
    	}

    	return $value;
    }
}