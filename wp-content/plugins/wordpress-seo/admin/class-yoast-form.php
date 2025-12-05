<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

use Yoast\WP\SEO\Presenters\Admin\Light_Switch_Presenter;
use Yoast\WP\SEO\Presenters\Admin\Sidebar_Presenter;

/**
 * Admin form class.
 *
 * @since 2.0
 */
class Yoast_Form {

	/**
	 * Instance of this class
	 *
	 * @since 2.0
	 *
	 * @var Yoast_Form
	 */
	public static $instance;

	/**
	 * The short name of the option to use for the current page.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $option_name;

	/**
	 * Option instance.
	 *
	 * @since 8.4
	 * @var WPSEO_Option|null
	 */
	protected $option_instance = null;

	/**
	 * Get the singleton instance of this class.
	 *
	 * @since 2.0
	 *
	 * @return Yoast_Form
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Generates the header for admin pages.
	 *
	 * @since 2.0
	 *
	 * @param bool   $form             Whether or not the form start tag should be included.
	 * @param string $option           The short name of the option to use for the current page.
	 * @param bool   $contains_files   Whether the form should allow for file uploads.
	 * @param bool   $option_long_name Group name of the option.
	 *
	 * @return void
	 */
	public function admin_header( $form = true, $option = 'wpseo', $contains_files = false, $option_long_name = false ) {
		if ( ! $option_long_name ) {
			$option_long_name = WPSEO_Options::get_group_name( $option );
		}
		?>
		<div class="wrap yoast wpseo-admin-page <?php echo esc_attr( 'page-' . $option ); ?>">
		<?php
		/**
		 * Display the updated/error messages.
		 * Only needed as our settings page is not under options, otherwise it will automatically be included.
		 *
		 * @see settings_errors()
		 */
		require_once ABSPATH . 'wp-admin/options-head.php';
		?>
		<h1 id="wpseo-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<div id="yst-settings-header-root"></div>
		<div class="wpseo_content_wrapper">
		<div class="wpseo_content_cell" id="wpseo_content_top">
		<?php
		if ( $form === true ) {
			$enctype = ( $contains_files ) ? ' enctype="multipart/form-data"' : '';

			$network_admin = new Yoast_Network_Admin();
			if ( $network_admin->meets_requirements() ) {
				$action_url       = network_admin_url( 'settings.php' );
				$hidden_fields_cb = [ $network_admin, 'settings_fields' ];
			}
			else {
				$action_url       = admin_url( 'options.php' );
				$hidden_fields_cb = 'settings_fields';
			}

			echo '<form action="'
				. esc_url( $action_url )
				. '" method="post" id="wpseo-conf"'
				. $enctype . ' accept-charset="' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nothing to escape.
				. esc_attr( get_bloginfo( 'charset' ) )
				. '" novalidate="novalidate">';
			call_user_func( $hidden_fields_cb, $option_long_name );
		}
		$this->set_option( $option );
	}

	/**
	 * Set the option used in output for form elements.
	 *
	 * @since 2.0
	 *
	 * @param string $option_name Option key.
	 *
	 * @return void
	 */
	public function set_option( $option_name ) {
		$this->option_name = $option_name;

		$this->option_instance = WPSEO_Options::get_option_instance( $option_name );
		if ( ! $this->option_instance ) {
			$this->option_instance = null;
		}
	}

	/**
	 * Generates the footer for admin pages.
	 *
	 * @since 2.0
	 *
	 * @param bool $submit       Whether or not a submit button and form end tag should be shown.
	 * @param bool $show_sidebar Whether or not to show the banner sidebar - used by premium plugins to disable it.
	 *
	 * @return void
	 */
	public function admin_footer( $submit = true, $show_sidebar = true ) {
		if ( $submit ) {
			$settings_changed_listener = new WPSEO_Admin_Settings_Changed_Listener();
			echo '<div id="wpseo-submit-container">';

			echo '<div id="wpseo-submit-container-float" class="wpseo-admin-submit">';
			submit_button( __( 'Save changes', 'wordpress-seo' ) );
			$settings_changed_listener->show_success_message();
			echo '</div>';

			echo '<div id="wpseo-submit-container-fixed" class="wpseo-admin-submit wpseo-admin-submit-fixed" style="display: none;">';
			submit_button( __( 'Save changes', 'wordpress-seo' ) );
			$settings_changed_listener->show_success_message();
			echo '</div>';

			echo '</div>';

			echo '
			</form>';
		}

		/**
		 * Apply general admin_footer hooks.
		 */
		do_action( 'wpseo_admin_footer', $this );

		/**
		 * Run possibly set actions to add for example an i18n box.
		 */
		do_action( 'wpseo_admin_promo_footer' );

		echo '
			</div><!-- end of div wpseo_content_top -->';

		if ( $show_sidebar ) {
			$this->admin_sidebar();
		}

		echo '</div><!-- end of div wpseo_content_wrapper -->';

		do_action( 'wpseo_admin_below_content', $this );

		echo '
			</div><!-- end of wrap -->';
	}

	/**
	 * Generates the sidebar for admin pages.
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function admin_sidebar() {
		// No banners in Premium.
		$addon_manager = new WPSEO_Addon_Manager();
		if ( YoastSEO()->helpers->product->is_premium() && $addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG ) ) {
			return;
		}

		$sidebar_presenter = new Sidebar_Presenter();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output escaped in presenter.
		echo $sidebar_presenter->present();
	}

	/**
	 * Output a label element.
	 *
	 * @since 2.0
	 *
	 * @param string $text Label text string, which can contain escaped html.
	 * @param array  $attr HTML attributes set.
	 *
	 * @return void
	 */
	public function label( $text, $attr ) {
		$defaults = [
			'class'      => 'checkbox',
			'close'      => true,
			'for'        => '',
			'aria_label' => '',
		];

		$attr       = wp_parse_args( $attr, $defaults );
		$aria_label = '';
		if ( $attr['aria_label'] !== '' ) {
			$aria_label = ' aria-label="' . esc_attr( $attr['aria_label'] ) . '"';
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before. Specifically, the $text variable can contain escaped html.
		echo "<label class='" . esc_attr( $attr['class'] ) . "' for='" . esc_attr( $attr['for'] ) . "'$aria_label>$text";
		if ( $attr['close'] ) {
			echo '</label>';
		}
	}

	/**
	 * Output a legend element.
	 *
	 * @since 3.4
	 *
	 * @param string $text Legend text string.
	 * @param array  $attr HTML attributes set.
	 *
	 * @return void
	 */
	public function legend( $text, $attr ) {
		$defaults = [
			'id'    => '',
			'class' => '',
		];
		$attr     = wp_parse_args( $attr, $defaults );

		$id = ( $attr['id'] === '' ) ? '' : ' id="' . esc_attr( $attr['id'] ) . '"';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before.
		echo '<legend class="' . esc_attr( 'yoast-form-legend ' . $attr['class'] ) . '"' . $id . '>' . $text . '</legend>';
	}

	/**
	 * Create a Checkbox input field.
	 *
	 * @since 2.0
	 *
	 * @param string $variable   The variable within the option to create the checkbox for.
	 * @param string $label      The label to show for the variable.
	 * @param bool   $label_left Whether the label should be left (true) or right (false).
	 * @param array  $attr       Extra attributes to add to the checkbox.
	 *
	 * @return void
	 */
	public function checkbox( $variable, $label, $label_left = false, $attr = [] ) {
		$val = $this->get_field_value( $variable, false );

		$defaults = [
			'disabled' => false,
		];
		$attr     = wp_parse_args( $attr, $defaults );

		if ( $val === true ) {
			$val = 'on';
		}

		$class = '';
		if ( $label_left !== false ) {
			$this->label( $label_left, [ 'for' => $variable ] );
		}
		else {
			$class = 'double';
		}

		$disabled_attribute = $this->get_disabled_attribute( $variable, $attr );

		// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: $disabled_attribute output is hardcoded and all other output is properly escaped.
		echo '<input class="', esc_attr( 'checkbox ' . $class ), '" type="checkbox" id="', esc_attr( $variable ), '" name="', esc_attr( $this->option_name . '[' . $variable . ']' ), '" value="on"', checked( $val, 'on', false ), $disabled_attribute, '/>';

		if ( ! empty( $label ) ) {
			$this->label( $label, [ 'for' => $variable ] );
		}

		echo '<br class="clear" />';
	}

	/**
	 * Creates a Checkbox input field list.
	 *
	 * @since 12.8
	 *
	 * @param string $variable The variables within the option to create the checkbox list for.
	 * @param string $labels   The labels to show for the variable.
	 * @param array  $attr     Extra attributes to add to the checkbox list.
	 *
	 * @return void
	 */
	public function checkbox_list( $variable, $labels, $attr = [] ) {
		$defaults = [
			'disabled' => false,
		];
		$attr     = wp_parse_args( $attr, $defaults );

		$values = $this->get_field_value( $variable, [] );

		foreach ( $labels as $name => $label ) {
			printf(
				'<input class="checkbox double" id="%1$s" type="checkbox" name="%2$s" %3$s %5$s value="%4$s"/>',
				esc_attr( $variable . '-' . $name ),
				esc_attr( $this->option_name . '[' . $variable . '][' . $name . ']' ),
				checked( ! empty( $values[ $name ] ), true, false ),
				esc_attr( $name ),
				disabled( ( isset( $attr['disabled'] ) && $attr['disabled'] ), true, false )
			);

			printf(
				'<label class="checkbox" for="%1$s">%2$s</label>',
				esc_attr( $variable . '-' . $name ), // #1
				esc_html( $label )
			);
			echo '<br class="clear">';
		}
	}

	/**
	 * Create a light switch input field using a single checkbox.
	 *
	 * @since 3.1
	 *
	 * @param string $variable The variable within the option to create the checkbox for.
	 * @param string $label    The visual label text for the toggle.
	 * @param array  $buttons  Array of two visual labels for the buttons (defaults Disabled/Enabled).
	 * @param bool   $reverse  Reverse order of buttons (default true).
	 * @param string $help     Inline Help that will be printed out before the toggle.
	 * @param bool   $strong   Whether the visual label is displayed in strong text. Default is false.
	 *                         Starting from Yoast SEO 16.5, the visual label is forced to bold via CSS.
	 * @param array  $attr     Extra attributes to add to the light switch.
	 *
	 * @return void
	 */
	public function light_switch( $variable, $label, $buttons = [], $reverse = true, $help = '', $strong = false, $attr = [] ) {
		$val = $this->get_field_value( $variable, false );

		$defaults = [
			'disabled' => false,
		];
		$attr     = wp_parse_args( $attr, $defaults );

		if ( $val === true ) {
			$val = 'on';
		}

		$disabled_attribute = $this->get_disabled_attribute( $variable, $attr );

		$output = new Light_Switch_Presenter(
			$variable,
			$label,
			$buttons,
			$this->option_name . '[' . $variable . ']',
			$val,
			$reverse,
			$help,
			$strong,
			$disabled_attribute
		);

		// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: All output is properly escaped or hardcoded in the presenter.
		echo $output;
	}

	/**
	 * Create a Text input field.
	 *
	 * @since 2.0
	 * @since 2.1 Introduced the `$attr` parameter.
	 *
	 * @param string       $variable The variable within the option to create the text input field for.
	 * @param string       $label    The label to show for the variable.
	 * @param array|string $attr     Extra attributes to add to the input field. Can be class, disabled, autocomplete.
	 *
	 * @return void
	 */
	public function textinput( $variable, $label, $attr = [] ) {
		$type = 'text';
		if ( ! is_array( $attr ) ) {
			$attr = [
				'class'    => $attr,
				'disabled' => false,
			];
		}

		$defaults = [
			'placeholder' => '',
			'class'       => '',
		];
		$attr     = wp_parse_args( $attr, $defaults );
		$val      = $this->get_field_value( $variable, '' );
		if ( isset( $attr['type'] ) && $attr['type'] === 'url' ) {
			$val  = urldecode( $val );
			$type = 'url';
		}
		$attributes = isset( $attr['autocomplete'] ) ? ' autocomplete="' . esc_attr( $attr['autocomplete'] ) . '"' : '';

		$this->label(
			$label,
			[
				'for'   => $variable,
				'class' => 'textinput',
			]
		);

		$aria_attributes = Yoast_Input_Validation::get_the_aria_invalid_attribute( $variable );

		$aria_attributes .= Yoast_Input_Validation::get_the_aria_describedby_attribute( $variable );

		$disabled_attribute = $this->get_disabled_attribute( $variable, $attr );

		// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: $disabled_attribute output is hardcoded and all other output is properly escaped.
		echo '<input', $attributes, $aria_attributes, ' class="', esc_attr( 'textinput ' . $attr['class'] ), '" placeholder="', esc_attr( $attr['placeholder'] ), '" type="', $type, '" id="', esc_attr( $variable ), '" name="', esc_attr( $this->option_name . '[' . $variable . ']' ), '" value="', esc_attr( $val ), '"', $disabled_attribute, '/>', '<br class="clear" />';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output escaped in getter.
		echo Yoast_Input_Validation::get_the_error_description( $variable );
	}

	/**
	 * Create a Number input field.
	 *
	 * @param string       $variable The variable within the option to create the text input field for.
	 * @param string       $label    The label to show for the variable.
	 * @param array|string $attr     Extra attributes to add to the input field. Can be class, disabled, autocomplete.
	 *
	 * @return void
	 */
	public function number( $variable, $label, $attr = [] ) {
		$type     = 'number';
		$defaults = [
			'placeholder' => '',
			'class'       => 'number',
			'disabled'    => false,
			'min'         => 0,
			'max'         => 100,
		];
		$attr     = wp_parse_args( $attr, $defaults );
		$val      = $this->get_field_value( $variable, 0 );

		$this->label(
			$label,
			[
				'for'   => $variable,
				'class' => 'textinput ' . $attr['class'],
			]
		);

		$aria_attributes  = Yoast_Input_Validation::get_the_aria_invalid_attribute( $variable );
		$aria_attributes .= Yoast_Input_Validation::get_the_aria_describedby_attribute( $variable );

		$disabled_attribute = $this->get_disabled_attribute( $variable, $attr );

		// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: $disabled_attribute output is hardcoded and all other output is properly escaped.
		echo '<input' . $aria_attributes . ' class="' . esc_attr( $attr['class'] ) . '" type="' . $type . '" id="', esc_attr( $variable ), '" min="', esc_attr( $attr['min'] ), '" max="', esc_attr( $attr['max'] ), '" name="', esc_attr( $this->option_name . '[' . $variable . ']' ), '" value="', esc_attr( $val ), '"', $disabled_attribute, '/>', '<br class="clear" />';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output escaped in getter.
		echo Yoast_Input_Validation::get_the_error_description( $variable );
	}

	/**
	 * Creates a text input field with with the ability to add content after the label.
	 *
	 * @param string $variable The variable within the option to create the text input field for.
	 * @param string $label    The label to show for the variable.
	 * @param array  $attr     Extra attributes to add to the input field.
	 *
	 * @return void
	 */
	public function textinput_extra_content( $variable, $label, $attr = [] ) {
		$type = 'text';

		$defaults = [
			'class'       => 'yoast-field-group__inputfield',
			'disabled'    => false,
		];

		$attr = wp_parse_args( $attr, $defaults );
		$val  = $this->get_field_value( $variable, '' );

		if ( isset( $attr['type'] ) && $attr['type'] === 'url' ) {
			$val  = urldecode( $val );
			$type = 'url';
		}

		echo '<div class="yoast-field-group__title">';
		$this->label(
			$label,
			[
				'for'   => $variable,
				'class' => $attr['class'] . '--label',
			]
		);

		if ( isset( $attr['extra_content'] ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: may contain HTML that should not be escaped.
			echo $attr['extra_content'];
		}
		echo '</div>';

		$aria_attributes  = Yoast_Input_Validation::get_the_aria_invalid_attribute( $variable );
		$aria_attributes .= Yoast_Input_Validation::get_the_aria_describedby_attribute( $variable );

		// phpcs:disable WordPress.Security.EscapeOutput -- Reason: output is properly escaped or hardcoded.
		printf(
			'<input type="%1$s" name="%2$s" id="%3$s" class="%4$s"%5$s%6$s%7$s value="%8$s"%9$s>',
			$type,
			esc_attr( $this->option_name . '[' . $variable . ']' ),
			esc_attr( $variable ),
			esc_attr( $attr['class'] ),
			isset( $attr['placeholder'] ) ? ' placeholder="' . esc_attr( $attr['placeholder'] ) . '"' : '',
			isset( $attr['autocomplete'] ) ? ' autocomplete="' . esc_attr( $attr['autocomplete'] ) . '"' : '',
			$aria_attributes,
			esc_attr( $val ),
			$this->get_disabled_attribute( $variable, $attr )
		);
		// phpcs:enable
		// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: output is properly escaped.
		echo Yoast_Input_Validation::get_the_error_description( $variable );
	}

	/**
	 * Create a textarea.
	 *
	 * @since 2.0
	 *
	 * @param string       $variable The variable within the option to create the textarea for.
	 * @param string       $label    The label to show for the variable.
	 * @param string|array $attr     The CSS class or an array of attributes to assign to the textarea.
	 *
	 * @return void
	 */
	public function textarea( $variable, $label, $attr = [] ) {
		if ( ! is_array( $attr ) ) {
			$attr = [
				'class' => $attr,
			];
		}

		$defaults = [
			'cols'     => '',
			'rows'     => '',
			'class'    => '',
			'disabled' => false,
		];
		$attr     = wp_parse_args( $attr, $defaults );
		$val      = $this->get_field_value( $variable, '' );

		$this->label(
			$label,
			[
				'for'   => $variable,
				'class' => 'textinput',
			]
		);

		$disabled_attribute = $this->get_disabled_attribute( $variable, $attr );

		// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: $disabled_attribute output is hardcoded and all other output is properly escaped.
		echo '<textarea cols="' . esc_attr( $attr['cols'] ) . '" rows="' . esc_attr( $attr['rows'] ) . '" class="' . esc_attr( 'textinput ' . $attr['class'] ) . '" id="' . esc_attr( $variable ) . '" name="' . esc_attr( $this->option_name . '[' . $variable . ']' ), '"', $disabled_attribute, '>' . esc_textarea( $val ) . '</textarea><br class="clear" />';
	}

	/**
	 * Create a hidden input field.
	 *
	 * @since 2.0
	 *
	 * @param string $variable The variable within the option to create the hidden input for.
	 * @param string $id       The ID of the element.
	 * @param mixed  $val      Optional. The value to set in the input field. Otherwise the value from the options will be used.
	 *
	 * @return void
	 */
	public function hidden( $variable, $id = '', $val = null ) {
		if ( $val === null ) {
			$val = $this->get_field_value( $variable, '' );
		}

		if ( is_bool( $val ) ) {
			$val = ( $val === true ) ? 'true' : 'false';
		}

		if ( $id === '' ) {
			$id = 'hidden_' . $variable;
		}

		echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $this->option_name . '[' . $variable . ']' ), '" value="' . esc_attr( $val ) . '"/>';
	}

	/**
	 * Create a Select Box.
	 *
	 * @since 2.0
	 *
	 * @param string $variable       The variable within the option to create the select for.
	 * @param string $label          The label to show for the variable.
	 * @param array  $select_options The select options to choose from.
	 * @param string $styled         The select style. Use 'styled' to get a styled select. Default 'unstyled'.
	 * @param bool   $show_label     Whether or not to show the label, if not, it will be applied as an aria-label.
	 * @param array  $attr           Extra attributes to add to the select.
	 * @param string $help           Optional. Inline Help HTML that will be printed after the label. Default is empty.
	 *
	 * @return void
	 */
	public function select( $variable, $label, array $select_options, $styled = 'unstyled', $show_label = true, $attr = [], $help = '' ) {
		if ( empty( $select_options ) ) {
			return;
		}

		$defaults = [
			'disabled' => false,
		];
		$attr     = wp_parse_args( $attr, $defaults );

		if ( $show_label ) {
			$this->label(
				$label,
				[
					'for'   => $variable,
					'class' => 'select',
				]
			);
			echo $help; // phpcs:ignore WordPress.Security.EscapeOutput -- Reason: The help contains HTML.
		}

		$select_name       = esc_attr( $this->option_name ) . '[' . esc_attr( $variable ) . ']';
		$active_option     = $this->get_field_value( $variable, '' );
		$wrapper_start_tag = '';
		$wrapper_end_tag   = '';

		$select = new Yoast_Input_Select( $variable, $select_name, $select_options, $active_option );
		$select->add_attribute( 'class', 'select' );

		if ( $this->is_control_disabled( $variable )
			|| ( isset( $attr['disabled'] ) && $attr['disabled'] ) ) {
			$select->add_attribute( 'disabled', 'disabled' );
		}

		if ( ! $show_label ) {
			$select->add_attribute( 'aria-label', $label );
		}

		if ( $styled === 'styled' ) {
			$wrapper_start_tag = '<span class="yoast-styled-select">';
			$wrapper_end_tag   = '</span>';
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before.
		echo $wrapper_start_tag;
		$select->output_html();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before.
		echo $wrapper_end_tag;
		echo '<br class="clear"/>';
	}

	/**
	 * Create a File upload field.
	 *
	 * @since 2.0
	 *
	 * @param string $variable The variable within the option to create the file upload field for.
	 * @param string $label    The label to show for the variable.
	 * @param array  $attr     Extra attributes to add to the file upload input.
	 *
	 * @return void
	 */
	public function file_upload( $variable, $label, $attr = [] ) {
		$val = $this->get_field_value( $variable, '' );
		if ( is_array( $val ) ) {
			$val = $val['url'];
		}

		$defaults = [
			'disabled' => false,
		];
		$attr     = wp_parse_args( $attr, $defaults );

		$var_esc = esc_attr( $variable );
		$this->label(
			$label,
			[
				'for'   => $variable,
				'class' => 'select',
			]
		);

		$disabled_attribute = $this->get_disabled_attribute( $variable, $attr );

		// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: $disabled_attribute output is hardcoded and all other output is properly escaped.
		echo '<input type="file" value="' . esc_attr( $val ) . '" class="textinput" name="' . esc_attr( $this->option_name ) . '[' . $var_esc . ']" id="' . $var_esc . '"', $disabled_attribute, '/>';

		// Need to save separate array items in hidden inputs, because empty file inputs type will be deleted by settings API.
		if ( ! empty( $val ) ) {
			$this->hidden( 'file', $this->option_name . '_file' );
			$this->hidden( 'url', $this->option_name . '_url' );
			$this->hidden( 'type', $this->option_name . '_type' );
		}
		echo '<br class="clear"/>';
	}

	/**
	 * Media input.
	 *
	 * @since 2.0
	 * @deprecated 23.5
	 * @codeCoverageIgnore
	 *
	 * @param string $variable Option name.
	 * @param string $label    Label message.
	 * @param array  $attr     Extra attributes to add to the media input and buttons.
	 *
	 * @return void
	 */
	public function media_input( $variable, $label, $attr = [] ) {

		_deprecated_function( __METHOD__, 'Yoast SEO 23.5' );

		$val      = $this->get_field_value( $variable, '' );
		$id_value = $this->get_field_value( $variable . '_id', '' );

		$var_esc = esc_attr( $variable );

		$defaults = [
			'disabled' => false,
		];
		$attr     = wp_parse_args( $attr, $defaults );

		$this->label(
			$label,
			[
				'for'   => 'wpseo_' . $variable,
				'class' => 'select',
			]
		);

		$id_field_id = 'wpseo_' . $var_esc . '_id';

		echo '<span>';
			echo '<input',
				' class="textinput"',
				' id="wpseo_', $var_esc, '"', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before.
				' type="text" size="36"',
				' name="', esc_attr( $this->option_name ), '[', $var_esc, ']"', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before.
				' value="', esc_attr( $val ), '"',
				' readonly="readonly"',
				' /> ';
			echo '<input',
				' type="hidden"',
				' id="', esc_attr( $id_field_id ), '"',
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before.
				' name="', esc_attr( $this->option_name ), '[', $var_esc, '_id]"',
				' value="', esc_attr( $id_value ), '"',
				' />';
		echo '</span>';
		echo '<br class="clear"/>';
	}

	/**
	 * Create a Radio input field.
	 *
	 * @since 2.0
	 *
	 * @param string $variable    The variable within the option to create the radio button for.
	 * @param array  $values      The radio options to choose from.
	 * @param string $legend      Optional. The legend to show for the field set, if any.
	 * @param array  $legend_attr Optional. The attributes for the legend, if any.
	 * @param array  $attr        Extra attributes to add to the radio button.
	 *
	 * @return void
	 */
	public function radio( $variable, $values, $legend = '', $legend_attr = [], $attr = [] ) {
		if ( ! is_array( $values ) || $values === [] ) {
			return;
		}
		$val = $this->get_field_value( $variable, false );

		$var_esc = esc_attr( $variable );

		$defaults = [
			'disabled' => false,
		];
		$attr     = wp_parse_args( $attr, $defaults );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before.
		echo '<fieldset class="yoast-form-fieldset wpseo_radio_block" id="' . $var_esc . '">';

		if ( is_string( $legend ) && $legend !== '' ) {

			$legend_defaults = [
				'id'    => '',
				'class' => 'radiogroup',
			];

			$legend_attr = wp_parse_args( $legend_attr, $legend_defaults );

			$this->legend( $legend, $legend_attr );
		}

		foreach ( $values as $key => $value ) {
			$label      = $value;
			$aria_label = '';

			if ( is_array( $value ) ) {
				$label      = ( $value['label'] ?? '' );
				$aria_label = ( $value['aria_label'] ?? '' );
			}

			$key_esc = esc_attr( $key );

			$disabled_attribute = $this->get_disabled_attribute( $variable, $attr );

			// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: $disabled_attribute output is hardcoded and all other output is properly escaped.
			echo '<input type="radio" class="radio" id="' . $var_esc . '-' . $key_esc . '" name="' . esc_attr( $this->option_name ) . '[' . $var_esc . ']" value="' . $key_esc . '" ' . checked( $val, $key_esc, false ) . $disabled_attribute . ' />';
			$this->label(
				$label,
				[
					'for'        => $var_esc . '-' . $key_esc,
					'class'      => 'radio',
					'aria_label' => $aria_label,
				]
			);
		}
		echo '</fieldset>';
	}

	/**
	 * Create a toggle switch input field using two radio buttons.
	 *
	 * @since 3.1
	 *
	 * @param string $variable The variable within the option to create the radio buttons for.
	 * @param array  $values   Associative array of on/off keys and their values to be used as
	 *                         the label elements text for the radio buttons. Optionally, each
	 *                         value can be an array of visible label text and screen reader text.
	 * @param string $label    The visual label for the radio buttons group, used as the fieldset legend.
	 * @param string $help     Inline Help that will be printed out before the visible toggles text.
	 * @param array  $attr     Extra attributes to add to the toggle switch.
	 *
	 * @return void
	 */
	public function toggle_switch( $variable, $values, $label, $help = '', $attr = [] ) {
		if ( ! is_array( $values ) || $values === [] ) {
			return;
		}

		$defaults = [
			'disabled' => false,
		];
		$attr     = wp_parse_args( $attr, $defaults );

		if ( isset( $attr['preserve_disabled_value'] ) && $attr['preserve_disabled_value'] ) {
			$this->hidden( $variable );
			$variable .= '_disabled';
		}

		$val = $this->get_field_value( $variable, false );
		if ( $val === true ) {
			$val = 'on';
		}
		if ( $val === false ) {
			$val = 'off';
		}

		$help_class = ! empty( $help ) ? ' switch-container__has-help' : '';

		$has_premium_upsell = ( isset( $attr['show_premium_upsell'] ) && $attr['show_premium_upsell'] && isset( $attr['premium_upsell_url'] ) && ! empty( $attr['premium_upsell_url'] ) );
		$upsell_class       = ( $has_premium_upsell ) ? ' premium-upsell' : '';

		$var_esc = esc_attr( $variable );

		printf( '<div class="%s">', esc_attr( 'switch-container' . $help_class . $upsell_class ) );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before.
		echo '<fieldset id="', $var_esc, '" class="fieldset-switch-toggle"><legend>', $label, '</legend>', $help;

		// Show disabled note if attribute does not exists or does exist and is set to true.
		if ( ! isset( $attr['show_disabled_note'] ) || ( $attr['show_disabled_note'] === true ) ) {
			if ( isset( $attr['note_when_disabled'] ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before.
				echo $this->get_disabled_note( $variable, $attr['note_when_disabled'] );
			}
			else {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before.
				echo $this->get_disabled_note( $variable );
			}
		}

		echo '<div class="switch-toggle switch-candy switch-yoast-seo">';

		foreach ( $values as $key => $value ) {
			$screen_reader_text_html = '';

			if ( is_array( $value ) ) {
				$screen_reader_text      = $value['screen_reader_text'];
				$screen_reader_text_html = '<span class="screen-reader-text"> ' . esc_html( $screen_reader_text ) . '</span>';
				$value                   = $value['text'];
			}

			$key_esc            = esc_attr( $key );
			$for                = $var_esc . '-' . $key_esc;
			$disabled_attribute = $this->get_disabled_attribute( $variable, $attr );

			// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: $disabled_attribute output is hardcoded and all other output is properly escaped.
			echo '<input type="radio" id="' . $for . '" name="' . esc_attr( $this->option_name ) . '[' . $var_esc . ']" value="' . $key_esc . '" ' . checked( $val, $key_esc, false ) . $disabled_attribute . ' />',
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output escaped before.
			'<label for="', $for, '">', esc_html( $value ), $screen_reader_text_html, '</label>';
		}

		$upsell_button = '';
		if ( $has_premium_upsell ) {
			$upsell_button = '<a class="yoast-button yoast-button--buy yoast-button--small" data-action="load-nfd-ctb" data-ctb-id="f6a84663-465f-4cb5-8ba5-f7a6d72224b2" href='
							. esc_url( $attr['premium_upsell_url'] ) . ' target="_blank">'
							. esc_html__( 'Unlock with Premium!', 'wordpress-seo' )
							/* translators: Hidden accessibility text. */
							. '<span class="screen-reader-text">' . esc_html__( '(Opens in a new browser tab)', 'wordpress-seo' ) . '</span>'
							. '<span aria-hidden="true" class="yoast-button--buy__caret"></span></a>';
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All variable output is escaped above.
		echo '<a></a></div></fieldset><div class="clear"></div>' . $upsell_button . '</div>' . PHP_EOL . PHP_EOL;
	}

	/**
	 * Creates a toggle switch to define whether an indexable should be indexed or not.
	 *
	 * @param string $variable The variable within the option to create the radio buttons for.
	 * @param string $label    The visual label for the radio buttons group, used as the fieldset legend.
	 * @param string $help     Inline Help that will be printed out before the visible toggles text.
	 * @param array  $attr     Extra attributes to add to the index switch.
	 *
	 * @return void
	 */
	public function index_switch( $variable, $label, $help = '', $attr = [] ) {
		$defaults = [
			'disabled' => false,
		];
		$attr     = wp_parse_args( $attr, $defaults );

		$index_switch_values = [
			'off' => __( 'On', 'wordpress-seo' ),
			'on'  => __( 'Off', 'wordpress-seo' ),
		];

		$is_disabled = ( isset( $attr['disabled'] ) && $attr['disabled'] );

		$this->toggle_switch(
			$variable,
			$index_switch_values,
			sprintf(
				/* translators: %s expands to an indexable object's name, like a post type or taxonomy */
				esc_html__( 'Show %s in search results?', 'wordpress-seo' ),
				$label
			),
			$help,
			[ 'disabled' => $is_disabled ]
		);
	}

	/**
	 * Creates a toggle switch to show hide certain options.
	 *
	 * @param string $variable     The variable within the option to create the radio buttons for.
	 * @param string $label        The visual label for the radio buttons group, used as the fieldset legend.
	 * @param bool   $inverse_keys Whether or not the option keys need to be inverted to support older functions.
	 * @param string $help         Inline Help that will be printed out before the visible toggles text.
	 * @param array  $attr         Extra attributes to add to the show-hide switch.
	 *
	 * @return void
	 */
	public function show_hide_switch( $variable, $label, $inverse_keys = false, $help = '', $attr = [] ) {
		$defaults = [
			'disabled' => false,
		];
		$attr     = wp_parse_args( $attr, $defaults );

		$on_key  = ( $inverse_keys ) ? 'off' : 'on';
		$off_key = ( $inverse_keys ) ? 'on' : 'off';

		$show_hide_switch = [
			$on_key  => __( 'On', 'wordpress-seo' ),
			$off_key => __( 'Off', 'wordpress-seo' ),
		];

		$is_disabled = ( isset( $attr['disabled'] ) && $attr['disabled'] );

		$this->toggle_switch(
			$variable,
			$show_hide_switch,
			$label,
			$help,
			[ 'disabled' => $is_disabled ]
		);
	}

	/**
	 * Retrieves the value for the form field.
	 *
	 * @param string      $field_name    The field name to retrieve the value for.
	 * @param string|null $default_value The default value, when field has no value.
	 *
	 * @return mixed|null The retrieved value.
	 */
	protected function get_field_value( $field_name, $default_value = null ) {
		// On multisite subsites, the Usage tracking feature should always be set to Off.
		if ( $this->is_tracking_on_subsite( $field_name ) ) {
			return false;
		}

		return WPSEO_Options::get( $field_name, $default_value );
	}

	/**
	 * Checks whether a given control should be disabled.
	 *
	 * @param string $variable The variable within the option to check whether its control should be disabled.
	 *
	 * @return bool True if control should be disabled, false otherwise.
	 */
	protected function is_control_disabled( $variable ) {
		if ( $this->option_instance === null ) {
			return false;
		}

		// Disable the Usage tracking feature for multisite subsites.
		if ( $this->is_tracking_on_subsite( $variable ) ) {
			return true;
		}

		return $this->option_instance->is_disabled( $variable );
	}

	/**
	 * Gets the explanation note to print if a given control is disabled.
	 *
	 * @param string $variable    The variable within the option to print a disabled note for.
	 * @param string $custom_note An optional custom note to print instead.
	 *
	 * @return string Explanation note HTML string, or empty string if no note necessary.
	 */
	protected function get_disabled_note( $variable, $custom_note = '' ) {
		if ( $custom_note === '' && ! $this->is_control_disabled( $variable ) ) {
			return '';
		}
		$disabled_message = esc_html__( 'This feature has been disabled by the network admin.', 'wordpress-seo' );

		// The explanation to show when disabling the Usage tracking feature for multisite subsites.
		if ( $this->is_tracking_on_subsite( $variable ) ) {
			$disabled_message = esc_html__( 'This feature has been disabled since subsites never send tracking data.', 'wordpress-seo' );
		}

		if ( $custom_note ) {
			$disabled_message = esc_html( $custom_note );
		}

		return '<p class="disabled-note">' . $disabled_message . '</p>';
	}

	/**
	 * Determines whether we are dealing with the Usage tracking feature on a multisite subsite.
	 * This feature requires specific behavior for the toggle switch.
	 *
	 * @param string $feature_setting The feature setting.
	 *
	 * @return bool True if we are dealing with the Usage tracking feature on a multisite subsite.
	 */
	protected function is_tracking_on_subsite( $feature_setting ) {
		return ( $feature_setting === 'tracking' && ! is_network_admin() && ! is_main_site() );
	}

	/**
	 * Returns the disabled attribute HTML.
	 *
	 * @param string $variable The variable within the option of the related form element.
	 * @param array  $attr     Extra attributes added to the form element.
	 *
	 * @return string The disabled attribute HTML.
	 */
	protected function get_disabled_attribute( $variable, $attr ) {
		if ( $this->is_control_disabled( $variable ) || ( isset( $attr['disabled'] ) && $attr['disabled'] ) ) {
			return ' disabled';
		}

		return '';
	}
}
