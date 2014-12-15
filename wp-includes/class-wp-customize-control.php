<?php
/**
 * Customize Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
class WP_Customize_Control {

	/**
	 * Incremented with each new class instantiation, then stored in $instance_number.
	 *
	 * Used when sorting two instances whose priorities are equal.
	 *
	 * @since 4.1.0
	 * @access protected
	 * @var int
	 */
	protected static $instance_count = 0;

	/**
	 * Order in which this instance was created in relation to other instances.
	 *
	 * @since 4.1.0
	 * @access public
	 * @var int
	 */
	public $instance_number;

	/**
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * All settings tied to the control.
	 *
	 * @access public
	 * @var array
	 */
	public $settings;

	/**
	 * The primary setting for the control (if there is one).
	 *
	 * @access public
	 * @var string
	 */
	public $setting = 'default';

	/**
	 * @access public
	 * @var int
	 */
	public $priority = 10;

	/**
	 * @access public
	 * @var string
	 */
	public $section = '';

	/**
	 * @access public
	 * @var string
	 */
	public $label = '';

	/**
	 * @access public
	 * @var string
	 */
	public $description = '';

	/**
	 * @todo: Remove choices
	 *
	 * @access public
	 * @var array
	 */
	public $choices = array();

	/**
	 * @access public
	 * @var array
	 */
	public $input_attrs = array();

	/**
	 * @deprecated It is better to just call the json() method
	 * @access public
	 * @var array
	 */
	public $json = array();

	/**
	 * @access public
	 * @var string
	 */
	public $type = 'text';

	/**
	 * Callback.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @see WP_Customize_Control::active()
	 *
	 * @var callable Callback is called with one argument, the instance of
	 *               WP_Customize_Control, and returns bool to indicate whether
	 *               the control is active (such as it relates to the URL
	 *               currently being previewed).
	 */
	public $active_callback = '';

	/**
	 * Constructor.
	 *
	 * Supplied $args override class property defaults.
	 *
	 * If $args['settings'] is not defined, use the $id as the setting ID.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string $id
	 * @param array $args
	 */
	public function __construct( $manager, $id, $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->manager = $manager;
		$this->id = $id;
		if ( empty( $this->active_callback ) ) {
			$this->active_callback = array( $this, 'active_callback' );
		}
		self::$instance_count += 1;
		$this->instance_number = self::$instance_count;

		// Process settings.
		if ( empty( $this->settings ) ) {
			$this->settings = $id;
		}

		$settings = array();
		if ( is_array( $this->settings ) ) {
			foreach ( $this->settings as $key => $setting ) {
				$settings[ $key ] = $this->manager->get_setting( $setting );
			}
		} else {
			$this->setting = $this->manager->get_setting( $this->settings );
			$settings['default'] = $this->setting;
		}
		$this->settings = $settings;
	}

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 3.4.0
	 */
	public function enqueue() {}

	/**
	 * Check whether control is active to current Customizer preview.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	public final function active() {
		$control = $this;
		$active = call_user_func( $this->active_callback, $this );

		/**
		 * Filter response of WP_Customize_Control::active().
		 *
		 * @since 4.0.0
		 *
		 * @param bool                 $active  Whether the Customizer control is active.
		 * @param WP_Customize_Control $control WP_Customize_Control instance.
		 */
		$active = apply_filters( 'customize_control_active', $active, $control );

		return $active;
	}

	/**
	 * Default callback used when invoking WP_Customize_Control::active().
	 *
	 * Subclasses can override this with their specific logic, or they may
	 * provide an 'active_callback' argument to the constructor.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @return bool Always true.
	 */
	public function active_callback() {
		return true;
	}

	/**
	 * Fetch a setting's value.
	 * Grabs the main setting by default.
	 *
	 * @since 3.4.0
	 *
	 * @param string $setting_key
	 * @return mixed The requested setting's value, if the setting exists.
	 */
	public final function value( $setting_key = 'default' ) {
		if ( isset( $this->settings[ $setting_key ] ) ) {
			return $this->settings[ $setting_key ]->value();
		}
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 */
	public function to_json() {
		$this->json['settings'] = array();
		foreach ( $this->settings as $key => $setting ) {
			$this->json['settings'][ $key ] = $setting->id;
		}

		$this->json['type'] = $this->type;
		$this->json['priority'] = $this->priority;
		$this->json['active'] = $this->active();
		$this->json['section'] = $this->section;
		$this->json['content'] = $this->get_content();
		$this->json['label'] = $this->label;
		$this->json['description'] = $this->description;
		$this->json['instanceNumber'] = $this->instance_number;
	}

	/**
	 * Get the data to export to the client via JSON.
	 *
	 * @since 4.1.0
	 *
	 * @return array Array of parameters passed to the JavaScript.
	 */
	public function json() {
		$this->to_json();
		return $this->json;
	}

	/**
	 * Check if the theme supports the control and check user capabilities.
	 *
	 * @since 3.4.0
	 *
	 * @return bool False if theme doesn't support the control or user doesn't have the required permissions, otherwise true.
	 */
	public final function check_capabilities() {
		foreach ( $this->settings as $setting ) {
			if ( ! $setting->check_capabilities() )
				return false;
		}

		$section = $this->manager->get_section( $this->section );
		if ( isset( $section ) && ! $section->check_capabilities() )
			return false;

		return true;
	}

	/**
	 * Get the control's content for insertion into the Customizer pane.
	 *
	 * @since 4.1.0
	 *
	 * @return string Contents of the control.
	 */
	public final function get_content() {
		ob_start();
		$this->maybe_render();
		$template = trim( ob_get_contents() );
		ob_end_clean();
		return $template;
	}

	/**
	 * Check capabilities and render the control.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Control::render()
	 */
	public final function maybe_render() {
		if ( ! $this->check_capabilities() )
			return;

		/**
		 * Fires just before the current Customizer control is rendered.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Control $this WP_Customize_Control instance.
		 */
		do_action( 'customize_render_control', $this );

		/**
		 * Fires just before a specific Customizer control is rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the control ID.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Control $this {@see WP_Customize_Control} instance.
		 */
		do_action( 'customize_render_control_' . $this->id, $this );

		$this->render();
	}

	/**
	 * Renders the control wrapper and calls $this->render_content() for the internals.
	 *
	 * @since 3.4.0
	 */
	protected function render() {
		$id    = 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) );
		$class = 'customize-control customize-control-' . $this->type;

		?><li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php $this->render_content(); ?>
		</li><?php
	}

	/**
	 * Get the data link attribute for a setting.
	 *
	 * @since 3.4.0
	 *
	 * @param string $setting_key
	 * @return string Data link parameter, if $setting_key is a valid setting, empty string otherwise.
	 */
	public function get_link( $setting_key = 'default' ) {
		if ( ! isset( $this->settings[ $setting_key ] ) )
			return '';

		return 'data-customize-setting-link="' . esc_attr( $this->settings[ $setting_key ]->id ) . '"';
	}

	/**
	 * Render the data link attribute for the control's input element.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Control::get_link()
	 *
	 * @param string $setting_key
	 */
	public function link( $setting_key = 'default' ) {
		echo $this->get_link( $setting_key );
	}

	/**
	 * Render the custom attributes for the control's input element.
	 *
	 * @since 4.0.0
	 * @access public
	 */
	public function input_attrs() {
		foreach( $this->input_attrs as $attr => $value ) {
			echo $attr . '="' . esc_attr( $value ) . '" ';
		}
	}

	/**
	 * Render the control's content.
	 *
	 * Allows the content to be overriden without having to rewrite the wrapper in $this->render().
	 *
	 * Supports basic input types `text`, `checkbox`, `textarea`, `radio`, `select` and `dropdown-pages`.
	 * Additional input types such as `email`, `url`, `number`, `hidden` and `date` are supported implicitly.
	 *
	 * Control content can alternately be rendered in JS. See {@see WP_Customize_Control::print_template()}.
	 *
	 * @since 3.4.0
	 */
	protected function render_content() {
		switch( $this->type ) {
			case 'checkbox':
				?>
				<label>
					<input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />
					<?php echo esc_html( $this->label ); ?>
					<?php if ( ! empty( $this->description ) ) : ?>
						<span class="description customize-control-description"><?php echo $this->description; ?></span>
					<?php endif; ?>
				</label>
				<?php
				break;
			case 'radio':
				if ( empty( $this->choices ) )
					return;

				$name = '_customize-radio-' . $this->id;

				if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description ; ?></span>
				<?php endif;

				foreach ( $this->choices as $value => $label ) :
					?>
					<label>
						<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
						<?php echo esc_html( $label ); ?><br/>
					</label>
					<?php
				endforeach;
				break;
			case 'select':
				if ( empty( $this->choices ) )
					return;

				?>
				<label>
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif;
					if ( ! empty( $this->description ) ) : ?>
						<span class="description customize-control-description"><?php echo $this->description; ?></span>
					<?php endif; ?>

					<select <?php $this->link(); ?>>
						<?php
						foreach ( $this->choices as $value => $label )
							echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
						?>
					</select>
				</label>
				<?php
				break;
			case 'textarea':
				?>
				<label>
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif;
					if ( ! empty( $this->description ) ) : ?>
						<span class="description customize-control-description"><?php echo $this->description; ?></span>
					<?php endif; ?>
					<textarea rows="5" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
				</label>
				<?php
				break;
			case 'dropdown-pages':
				$dropdown = wp_dropdown_pages(
					array(
						'name'              => '_customize-dropdown-pages-' . $this->id,
						'echo'              => 0,
						'show_option_none'  => __( '&mdash; Select &mdash;' ),
						'option_none_value' => '0',
						'selected'          => $this->value(),
					)
				);

				// Hackily add in the data link parameter.
				$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

				printf(
					'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
					$this->label,
					$dropdown
				);
				break;
			default:
				?>
				<label>
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif;
					if ( ! empty( $this->description ) ) : ?>
						<span class="description customize-control-description"><?php echo $this->description; ?></span>
					<?php endif; ?>
					<input type="<?php echo esc_attr( $this->type ); ?>" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
				</label>
				<?php
				break;
		}
	}

	/**
	 * Render the control's JS template.
	 *
	 * This function is only run for control types that have been registered with
	 * {@see WP_Customize_Manager::register_control_type()}.
	 *
	 * In the future, this will also print the template for the control's container
	 * element and be override-able.
	 *
	 * @since 4.1.0
	 */
	final public function print_template() {
	        ?>
	        <script type="text/html" id="tmpl-customize-control-<?php echo $this->type; ?>-content">
	                <?php $this->content_template(); ?>
	        </script>
	        <?php
	}

	/**
	 * An Underscore (JS) template for this control's content (but not its container).
	 *
	 * Class variables for this control class are available in the `data` JS object;
	 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
	 *
	 * @see WP_Customize_Control::print_template()
	 *
	 * @since 4.1.0
	 */
	protected function content_template() {}

}

/**
 * Customize Color Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
class WP_Customize_Color_Control extends WP_Customize_Control {
	/**
	 * @access public
	 * @var string
	 */
	public $type = 'color';

	/**
	 * @access public
	 * @var array
	 */
	public $statuses;

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string $id
	 * @param array $args
	 */
	public function __construct( $manager, $id, $args = array() ) {
		$this->statuses = array( '' => __('Default') );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Enqueue scripts/styles for the color picker.
	 *
	 * @since 3.4.0
	 */
	public function enqueue() {
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['statuses'] = $this->statuses;
		$this->json['defaultValue'] = $this->setting->default;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 *
	 * @since 3.4.0
	 */
	public function render_content() {}

	/**
	 * Render a JS template for the content of the color picker control.
	 *
	 * @since 4.1.0
	 */
	public function content_template() {
		?>
		<# var defaultValue = '';
		if ( data.defaultValue ) {
			if ( '#' !== data.defaultValue.substring( 0, 1 ) ) {
				defaultValue = '#' + data.defaultValue;
			} else {
				defaultValue = data.defaultValue;
			}
			defaultValue = ' data-default-color=' + defaultValue; // Quotes added automatically.
		} #>
		<label>
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{{ data.label }}}</span>
			<# } #>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
			<div class="customize-control-content">
				<input class="color-picker-hex" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value' ); ?>" {{ defaultValue }} />
			</div>
		</label>
		<?php
	}
}

/**
 * Customize Upload Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
class WP_Customize_Upload_Control extends WP_Customize_Control {
	public $type          = 'upload';
	public $mime_type     = '';
	public $button_labels = array();
	public $removed = ''; // unused
	public $context; // unused
	public $extensions = array(); // unused

	/**
	 * Constructor.
	 *
	 * @since 4.1.0
	 *
	 * @param WP_Customize_Manager $manager {@see WP_Customize_Manager} instance.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		$this->button_labels = array(
			'select'       => __( 'Select File' ),
			'change'       => __( 'Change File' ),
			'default'      => __( 'Default' ),
			'remove'       => __( 'Remove' ),
			'placeholder'  => __( 'No file selected' ),
			'frame_title'  => __( 'Select File' ),
			'frame_button' => __( 'Choose File' ),
		);
	}

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 3.4.0
	 */
	public function enqueue() {
		wp_enqueue_media();
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['mime_type'] = $this->mime_type;
		$this->json['button_labels'] = $this->button_labels;

		$value = $this->value();

		if ( is_object( $this->setting ) ) {
			if ( $this->setting->default ) {
				// Fake an attachment model - needs all fields used by template.
				$type = in_array( substr( $this->setting->default, -3 ), array( 'jpg', 'png', 'gif', 'bmp' ) ) ? 'image' : 'document';
				$default_attachment = array(
					'id' => 1,
					'url' => $this->setting->default,
					'type' => $type,
					'icon' => wp_mime_type_icon( $type ),
					'title' => basename( $this->setting->default ),
				);

				if ( 'image' === $type ) {
					$default_attachment['sizes'] = array(
						'full' => array( 'url' => $this->setting->default ),
					);
				}

				$this->json['defaultAttachment'] = $default_attachment;
			}

			if ( $value && $this->setting->default && $value === $this->setting->default ) {
				// Set the default as the attachment.
				$this->json['attachment'] = $this->json['defaultAttachment'];
			} elseif ( $value ) {
				// Get the attachment model for the existing file.
				$attachment_id = attachment_url_to_postid( $value );
				if ( $attachment_id ) {
					$this->json['attachment'] = wp_prepare_attachment_for_js( $attachment_id );
				}
			}
		}
	}

	/**
	 * Don't render any content for this control from PHP.
	 *
	 * @see WP_Customize_Upload_Control::content_template()
	 * @since 3.4.0
	 */
	public function render_content() {}

	/**
	 * Render a JS template for the content of the upload control.
	 *
	 * @since 4.1.0
	 */
	public function content_template() {
		?>
		<label for="{{ data.settings.default }}-button">
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
		</label>

		<# if ( data.attachment && data.attachment.id ) { #>
			<div class="current">
				<div class="container">
					<div class="attachment-media-view attachment-media-view-{{ data.attachment.type }} {{ data.attachment.orientation }}">
						<div class="thumbnail thumbnail-{{ data.attachment.type }}">
							<# if ( 'image' === data.attachment.type && data.attachment.sizes && data.attachment.sizes.medium ) { #>
								<img class="attachment-thumb" src="{{ data.attachment.sizes.medium.url }}" draggable="false" />
							<# } else if ( 'image' === data.attachment.type && data.attachment.sizes && data.attachment.sizes.full ) { #>
								<img class="attachment-thumb" src="{{ data.attachment.sizes.full.url }}" draggable="false" />
							<# } else if ( 'audio' === data.attachment.type ) { #>
								<img class="attachment-thumb type-icon" src="{{ data.attachment.icon }}" class="icon" draggable="false" />
								<p class="attachment-meta attachment-meta-title">&#8220;{{ data.attachment.title }}&#8221;</p>
								<# if ( data.attachment.album || data.attachment.meta.album ) { #>
								<p class="attachment-meta"><em>{{ data.attachment.album || data.attachment.meta.album }}</em></p>
								<# } #>
								<# if ( data.attachment.artist || data.attachment.meta.artist ) { #>
								<p class="attachment-meta">{{ data.attachment.artist || data.attachment.meta.artist }}</p>
								<# } #>
							<# } else { #>
								<img class="attachment-thumb type-icon" src="{{ data.attachment.icon }}" class="icon" draggable="false" />
								<p class="attachment-title">{{ data.attachment.title }}</p>
							<# } #>
						</div>
					</div>
				</div>
			</div>
			<div class="actions">
				<button type="button" class="button remove-button"><?php echo $this->button_labels['remove']; ?></button>
				<button type="button" class="button upload-button" id="{{ data.settings.default }}-button"><?php echo $this->button_labels['change']; ?></button>
				<div style="clear:both"></div>
			</div>
		<# } else { #>
			<div class="current">
				<div class="container">
					<div class="placeholder">
						<div class="inner">
							<span>
								<?php echo $this->button_labels['placeholder']; ?>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="actions">
				<# if ( data.defaultAttachment ) { #>
					<button type="button" class="button default-button"><?php echo $this->button_labels['default']; ?></button>
				<# } #>
				<button type="button" class="button upload-button" id="{{ data.settings.default }}-button"><?php echo $this->button_labels['select']; ?></button>
				<div style="clear:both"></div>
			</div>
		<# } #>
		<?php
	}
}

/**
 * Customize Image Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
class WP_Customize_Image_Control extends WP_Customize_Upload_Control {
	public $type = 'image';
	public $mime_type = 'image';

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Upload_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string $id
	 * @param array  $args
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		$this->button_labels = array(
			'select'       => __( 'Select Image' ),
			'change'       => __( 'Change Image' ),
			'remove'       => __( 'Remove' ),
			'default'      => __( 'Default' ),
			'placeholder'  => __( 'No image selected' ),
			'frame_title'  => __( 'Select Image' ),
			'frame_button' => __( 'Choose Image' ),
		);
	}

	/**
	 * @since 3.4.2
	 * @deprecated 4.1.0
	 */
	public function prepare_control() {}

	/**
	 * @since 3.4.0
	 * @deprecated 4.1.0
	 *
	 * @param string $id
	 * @param string $label
	 * @param mixed $callback
	 */
	public function add_tab( $id, $label, $callback ) {}

	/**
	 * @since 3.4.0
	 * @deprecated 4.1.0
	 *
	 * @param string $id
	 */
	public function remove_tab( $id ) {}

	/**
	 * @since 3.4.0
	 * @deprecated 4.1.0
	 *
	 * @param string $url
	 * @param string $thumbnail_url
	 */
	public function print_tab_image( $url, $thumbnail_url = null ) {}
}

/**
 * Customize Background Image Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
class WP_Customize_Background_Image_Control extends WP_Customize_Image_Control {
	public $type = 'background';

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Image_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager
	 */
	public function __construct( $manager ) {
		parent::__construct( $manager, 'background_image', array(
			'label'    => __( 'Background Image' ),
			'section'  => 'background_image',
		) );
	}

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 4.1.0
	 */
	public function enqueue() {
		parent::enqueue();

		wp_localize_script( 'customize-controls', '_wpCustomizeBackground', array(
			'nonces' => array(
				'add' => wp_create_nonce( 'background-add' ),
			),
		) );
	}
}

class WP_Customize_Header_Image_Control extends WP_Customize_Image_Control {
	public $type = 'header';
	public $uploaded_headers;
	public $default_headers;

	/**
	 * @param WP_Customize_Manager $manager
	 */
	public function __construct( $manager ) {
		parent::__construct( $manager, 'header_image', array(
			'label'    => __( 'Header Image' ),
			'settings' => array(
				'default' => 'header_image',
				'data'    => 'header_image_data',
			),
			'section'  => 'header_image',
			'removed'  => 'remove-header',
			'get_url'  => 'get_header_image',
		) );

	}

	public function to_json() {
		parent::to_json();
	}

	public function enqueue() {
		wp_enqueue_media();
		wp_enqueue_script( 'customize-views' );

		$this->prepare_control();

		wp_localize_script( 'customize-views', '_wpCustomizeHeader', array(
			'data' => array(
				'width' => absint( get_theme_support( 'custom-header', 'width' ) ),
				'height' => absint( get_theme_support( 'custom-header', 'height' ) ),
				'flex-width' => absint( get_theme_support( 'custom-header', 'flex-width' ) ),
				'flex-height' => absint( get_theme_support( 'custom-header', 'flex-height' ) ),
				'currentImgSrc' => $this->get_current_image_src(),
			),
			'nonces' => array(
				'add' => wp_create_nonce( 'header-add' ),
				'remove' => wp_create_nonce( 'header-remove' ),
			),
			'uploads' => $this->uploaded_headers,
			'defaults' => $this->default_headers
		) );

		parent::enqueue();
	}

	public function prepare_control() {
		global $custom_image_header;
		if ( empty( $custom_image_header ) ) {
			return;
		}

		// Process default headers and uploaded headers.
		$custom_image_header->process_default_headers();
		$this->default_headers = $custom_image_header->get_default_header_images();
		$this->uploaded_headers = $custom_image_header->get_uploaded_header_images();
	}

	function print_header_image_template() {
		?>
		<script type="text/template" id="tmpl-header-choice">
			<# if (data.random) { #>
					<button type="button" class="button display-options random">
						<span class="dashicons dashicons-randomize dice"></span>
						<# if ( data.type === 'uploaded' ) { #>
							<?php _e( 'Randomize uploaded headers' ); ?>
						<# } else if ( data.type === 'default' ) { #>
							<?php _e( 'Randomize suggested headers' ); ?>
						<# } #>
					</button>

			<# } else { #>

			<# if (data.type === 'uploaded') { #>
				<div class="dashicons dashicons-no close"></div>
			<# } #>

			<button type="button" class="choice thumbnail"
				data-customize-image-value="{{{data.header.url}}}"
				data-customize-header-image-data="{{JSON.stringify(data.header)}}">
				<span class="screen-reader-text"><?php _e( 'Set image' ); ?></span>
				<img src="{{{data.header.thumbnail_url}}}" alt="{{{data.header.alt_text || data.header.description}}}">
			</button>

			<# } #>
		</script>

		<script type="text/template" id="tmpl-header-current">
			<# if (data.choice) { #>
				<# if (data.random) { #>

			<div class="placeholder">
				<div class="inner">
					<span><span class="dashicons dashicons-randomize dice"></span>
					<# if ( data.type === 'uploaded' ) { #>
						<?php _e( 'Randomizing uploaded headers' ); ?>
					<# } else if ( data.type === 'default' ) { #>
						<?php _e( 'Randomizing suggested headers' ); ?>
					<# } #>
					</span>
				</div>
			</div>

				<# } else { #>

			<img src="{{{data.header.thumbnail_url}}}" alt="{{{data.header.alt_text || data.header.description}}}" tabindex="0"/>

				<# } #>
			<# } else { #>

			<div class="placeholder">
				<div class="inner">
					<span>
						<?php _e( 'No image set' ); ?>
					</span>
				</div>
			</div>

			<# } #>
		</script>
		<?php
	}

	public function get_current_image_src() {
		$src = $this->value();
		if ( isset( $this->get_url ) ) {
			$src = call_user_func( $this->get_url, $src );
			return $src;
		}
		return null;
	}

	public function render_content() {
		$this->print_header_image_template();
		$visibility = $this->get_current_image_src() ? '' : ' style="display:none" ';
		$width = absint( get_theme_support( 'custom-header', 'width' ) );
		$height = absint( get_theme_support( 'custom-header', 'height' ) );
		?>


		<div class="customize-control-content">
			<p class="customizer-section-intro">
				<?php
				if ( $width && $height ) {
					printf( __( 'While you can crop images to your liking after clicking <strong>Add new image</strong>, your theme recommends a header size of <strong>%s &times; %s</strong> pixels.' ), $width, $height );
				} elseif ( $width ) {
					printf( __( 'While you can crop images to your liking after clicking <strong>Add new image</strong>, your theme recommends a header width of <strong>%s</strong> pixels.' ), $width );
				} else {
					printf( __( 'While you can crop images to your liking after clicking <strong>Add new image</strong>, your theme recommends a header height of <strong>%s</strong> pixels.' ), $height );
				}
				?>
			</p>
			<div class="current">
				<span class="customize-control-title">
					<?php _e( 'Current header' ); ?>
				</span>
				<div class="container">
				</div>
			</div>
			<div class="actions">
				<?php /* translators: Hide as in hide header image via the Customizer */ ?>
				<button type="button"<?php echo $visibility ?> class="button remove"><?php _ex( 'Hide image', 'custom header' ); ?></button>
				<?php /* translators: New as in add new header image via the Customizer */ ?>
				<button type="button" class="button new"><?php _ex( 'Add new image', 'header image' ); ?></button>
				<div style="clear:both"></div>
			</div>
			<div class="choices">
				<span class="customize-control-title header-previously-uploaded">
					<?php _ex( 'Previously uploaded', 'custom headers' ); ?>
				</span>
				<div class="uploaded">
					<div class="list">
					</div>
				</div>
				<span class="customize-control-title header-default">
					<?php _ex( 'Suggested', 'custom headers' ); ?>
				</span>
				<div class="default">
					<div class="list">
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

/**
 * Widget Area Customize Control Class
 *
 * @since 3.9.0
 */
class WP_Widget_Area_Customize_Control extends WP_Customize_Control {
	public $type = 'sidebar_widgets';
	public $sidebar_id;

	public function to_json() {
		parent::to_json();
		$exported_properties = array( 'sidebar_id' );
		foreach ( $exported_properties as $key ) {
			$this->json[ $key ] = $this->$key;
		}
	}

	public function render_content() {
		?>
		<span class="button-secondary add-new-widget" tabindex="0">
			<?php _e( 'Add a Widget' ); ?>
		</span>

		<span class="reorder-toggle" tabindex="0">
			<span class="reorder"><?php _ex( 'Reorder', 'Reorder widgets in Customizer' ); ?></span>
			<span class="reorder-done"><?php _ex( 'Done', 'Cancel reordering widgets in Customizer'  ); ?></span>
		</span>
		<?php
	}

}

/**
 * Widget Form Customize Control Class
 *
 * @since 3.9.0
 */
class WP_Widget_Form_Customize_Control extends WP_Customize_Control {
	public $type = 'widget_form';
	public $widget_id;
	public $widget_id_base;
	public $sidebar_id;
	public $is_new = false;
	public $width;
	public $height;
	public $is_wide = false;

	public function to_json() {
		parent::to_json();
		$exported_properties = array( 'widget_id', 'widget_id_base', 'sidebar_id', 'width', 'height', 'is_wide' );
		foreach ( $exported_properties as $key ) {
			$this->json[ $key ] = $this->$key;
		}
	}

	public function render_content() {
		global $wp_registered_widgets;
		require_once ABSPATH . '/wp-admin/includes/widgets.php';

		$widget = $wp_registered_widgets[ $this->widget_id ];
		if ( ! isset( $widget['params'][0] ) ) {
			$widget['params'][0] = array();
		}

		$args = array(
			'widget_id' => $widget['id'],
			'widget_name' => $widget['name'],
		);

		$args = wp_list_widget_controls_dynamic_sidebar( array( 0 => $args, 1 => $widget['params'][0] ) );
		echo $this->manager->widgets->get_widget_control( $args );
	}

	/**
	 * Whether the current widget is rendered on the page.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @return bool Whether the widget is rendered.
	 */
	function active_callback() {
		return $this->manager->widgets->is_widget_rendered( $this->widget_id );
	}
}

