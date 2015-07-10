<?php
/**
 * WordPress Customize Control classes
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

/**
 * Customize Control class.
 *
 * @since 3.4.0
 */
class WP_Customize_Control {

	/**
	 * Incremented with each new class instantiation, then stored in $instance_number.
	 *
	 * Used when sorting two instances whose priorities are equal.
	 *
	 * @since 4.1.0
	 *
	 * @static
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
	final public function active() {
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
	 * @return true Always true.
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
	final public function value( $setting_key = 'default' ) {
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
	final public function check_capabilities() {
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
	final public function get_content() {
		ob_start();
		$this->maybe_render();
		return trim( ob_get_clean() );
	}

	/**
	 * Check capabilities and render the control.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Control::render()
	 */
	final public function maybe_render() {
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
 * Customize Color Control class.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Control
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
 * Customize Media Control class.
 *
 * @since 4.2.0
 *
 * @see WP_Customize_Control
 */
class WP_Customize_Media_Control extends WP_Customize_Control {
	/**
	 * Control type.
	 *
	 * @since 4.2.0
	 * @access public
	 * @var string
	 */
	public $type = 'media';

	/**
	 * Media control mime type.
	 *
	 * @since 4.2.0
	 * @access public
	 * @var string
	 */
	public $mime_type = '';

	/**
	 * Button labels.
	 *
	 * @since 4.2.0
	 * @access public
	 * @var array
	 */
	public $button_labels = array();

	/**
	 * Constructor.
	 *
	 * @since 4.1.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 *
	 * @param WP_Customize_Manager $manager {@see WP_Customize_Manager} instance.
	 * @param string $id
	 * @param array $args
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
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 */
	public function enqueue() {
		wp_enqueue_media();
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 *
	 * @see WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['label'] = html_entity_decode( $this->label, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$this->json['mime_type'] = $this->mime_type;
		$this->json['button_labels'] = $this->button_labels;
		$this->json['canUpload'] = current_user_can( 'upload_files' );

		$value = $this->value();

		if ( is_object( $this->setting ) ) {
			if ( $this->setting->default ) {
				// Fake an attachment model - needs all fields used by template.
				// Note that the default value must be a URL, NOT an attachment ID.
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
				$this->json['attachment'] = wp_prepare_attachment_for_js( $value );
			}
		}
	}

	/**
	 * Don't render any content for this control from PHP.
	 *
	 * @since 3.4.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 *
	 * @see WP_Customize_Media_Control::content_template()
	 */
	public function render_content() {}

	/**
	 * Render a JS template for the content of the media control.
	 *
	 * @since 4.1.0
	 * @since 4.2.0 Moved from WP_Customize_Upload_Control.
	 */
	public function content_template() {
		?>
		<label for="{{ data.settings['default'] }}-button">
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
								<# if ( data.attachment.image && data.attachment.image.src && data.attachment.image.src !== data.attachment.icon ) { #>
									<img src="{{ data.attachment.image.src }}" class="thumbnail" draggable="false" />
								<# } else { #>
									<img src="{{ data.attachment.icon }}" class="attachment-thumb type-icon" draggable="false" />
								<# } #>
								<p class="attachment-meta attachment-meta-title">&#8220;{{ data.attachment.title }}&#8221;</p>
								<# if ( data.attachment.album || data.attachment.meta.album ) { #>
								<p class="attachment-meta"><em>{{ data.attachment.album || data.attachment.meta.album }}</em></p>
								<# } #>
								<# if ( data.attachment.artist || data.attachment.meta.artist ) { #>
								<p class="attachment-meta">{{ data.attachment.artist || data.attachment.meta.artist }}</p>
								<# } #>
								<audio style="visibility: hidden" controls class="wp-audio-shortcode" width="100%" preload="none">
									<source type="{{ data.attachment.mime }}" src="{{ data.attachment.url }}"/>
								</audio>
							<# } else if ( 'video' === data.attachment.type ) { #>
								<div class="wp-media-wrapper wp-video">
									<video controls="controls" class="wp-video-shortcode" preload="metadata"
										<# if ( data.attachment.image && data.attachment.image.src !== data.attachment.icon ) { #>poster="{{ data.attachment.image.src }}"<# } #>>
										<source type="{{ data.attachment.mime }}" src="{{ data.attachment.url }}"/>
									</video>
								</div>
							<# } else { #>
								<img class="attachment-thumb type-icon icon" src="{{ data.attachment.icon }}" draggable="false" />
								<p class="attachment-title">{{ data.attachment.title }}</p>
							<# } #>
						</div>
					</div>
				</div>
			</div>
			<div class="actions">
				<# if ( data.canUpload ) { #>
				<button type="button" class="button remove-button"><?php echo $this->button_labels['remove']; ?></button>
				<button type="button" class="button upload-button" id="{{ data.settings['default'] }}-button"><?php echo $this->button_labels['change']; ?></button>
				<div style="clear:both"></div>
				<# } #>
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
				<# if ( data.canUpload ) { #>
				<button type="button" class="button upload-button" id="{{ data.settings['default'] }}-button"><?php echo $this->button_labels['select']; ?></button>
				<# } #>
				<div style="clear:both"></div>
			</div>
		<# } #>
		<?php
	}
}

/**
 * Customize Upload Control Class.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Media_Control
 */
class WP_Customize_Upload_Control extends WP_Customize_Media_Control {
	public $type          = 'upload';
	public $mime_type     = '';
	public $button_labels = array();
	public $removed = ''; // unused
	public $context; // unused
	public $extensions = array(); // unused

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 *
	 * @uses WP_Customize_Media_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();

		$value = $this->value();
		if ( $value ) {
			// Get the attachment model for the existing file.
			$attachment_id = attachment_url_to_postid( $value );
			if ( $attachment_id ) {
				$this->json['attachment'] = wp_prepare_attachment_for_js( $attachment_id );
			}
		}
	}
}

/**
 * Customize Image Control class.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Upload_Control
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
 * Customize Background Image Control class.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Image_Control
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

/**
 * Customize Cropped Image Control class.
 *
 * @since 4.3.0
 *
 * @see WP_Customize_Image_Control
 */
class WP_Customize_Cropped_Image_Control extends WP_Customize_Image_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'cropped_image';

	/**
	 * Suggested width for cropped image.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var int
	 */
	public $width = 150;

	/**
	 * Suggested height for cropped image.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var int
	 */
	public $height = 150;

	/**
	 * Whether the width is flexible.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var bool
	 */
	public $flex_width = false;

	/**
	 * Whether the height is flexible.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var bool
	 */
	public $flex_height = false;

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 */
	public function enqueue() {
		wp_enqueue_script( 'customize-views' );

		parent::enqueue();
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @uses WP_Customize_Image_Control::to_json()
	 * @see WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();

		$this->json['width']       = absint( $this->width );
		$this->json['height']      = absint( $this->height );
		$this->json['flex_width']  = absint( $this->flex_width );
		$this->json['flex_height'] = absint( $this->flex_height );
	}

}

/**
 * Customize Site Icon control class.
 *
 * Used only for custom functionality in JavaScript.
 *
 * @since 4.3.0
 *
 * @see WP_Customize_Cropped_Image_Control
 */
class WP_Customize_Site_Icon_Control extends WP_Customize_Cropped_Image_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'site_icon';

	/**
	 * Constructor.
	 *
	 * @since 4.3.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		add_action( 'customize_controls_print_styles', 'wp_site_icon', 99 );
	}
}

/**
 * Customize Header Image Control class.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Image_Control
 */
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

	/**
	 * @access public
	 */
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

	/**
	 *
	 * @global Custom_Image_Header $custom_image_header
	 */
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

	/**
	 * @access public
	 */
	public function print_header_image_template() {
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
				<button type="button" class="dashicons dashicons-no close"><span class="screen-reader-text"><?php _e( 'Remove image' ); ?></span></button>
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

	/**
	 * @return string|void
	 */
	public function get_current_image_src() {
		$src = $this->value();
		if ( isset( $this->get_url ) ) {
			$src = call_user_func( $this->get_url, $src );
			return $src;
		}
	}

	/**
	 * @access public
	 */
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
				<?php if ( current_user_can( 'upload_files' ) ): ?>
				<button type="button"<?php echo $visibility; ?> class="button remove" aria-label="<?php esc_attr_e( 'Hide header image' ); ?>"><?php _e( 'Hide image' ); ?></button>
				<button type="button" class="button new" aria-label="<?php esc_attr_e( 'Add new header image' ); ?>"><?php _e( 'Add new image' ); ?></button>
				<div style="clear:both"></div>
				<?php endif; ?>
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
 * Customize Theme Control class.
 *
 * @since 4.2.0
 *
 * @see WP_Customize_Control
 */
class WP_Customize_Theme_Control extends WP_Customize_Control {

	/**
	 * Customize control type.
	 *
	 * @since 4.2.0
	 * @access public
	 * @var string
	 */
	public $type = 'theme';

	/**
	 * Theme object.
	 *
	 * @since 4.2.0
	 * @access public
	 * @var WP_Theme
	 */
	public $theme;

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @see WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['theme'] = $this->theme;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 *
	 * @since 4.2.0
	 * @access public
	 */
	public function render_content() {}

	/**
	 * Render a JS template for theme display.
	 *
	 * @since 4.2.0
	 * @access public
	 */
	public function content_template() {
		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$active_url  = esc_url( remove_query_arg( 'theme', $current_url ) );
		$preview_url = esc_url( add_query_arg( 'theme', '__THEME__', $current_url ) ); // Token because esc_url() strips curly braces.
		$preview_url = str_replace( '__THEME__', '{{ data.theme.id }}', $preview_url );
		?>
		<# if ( data.theme.isActiveTheme ) { #>
			<div class="theme active" tabindex="0" data-preview-url="<?php echo esc_attr( $active_url ); ?>" aria-describedby="{{ data.theme.id }}-action {{ data.theme.id }}-name">
		<# } else { #>
			<div class="theme" tabindex="0" data-preview-url="<?php echo esc_attr( $preview_url ); ?>" aria-describedby="{{ data.theme.id }}-action {{ data.theme.id }}-name">
		<# } #>

			<# if ( data.theme.screenshot[0] ) { #>
				<div class="theme-screenshot">
					<img data-src="{{ data.theme.screenshot[0] }}" alt="" />
				</div>
			<# } else { #>
				<div class="theme-screenshot blank"></div>
			<# } #>

			<# if ( data.theme.isActiveTheme ) { #>
				<span class="more-details" id="{{ data.theme.id }}-action"><?php _e( 'Customize' ); ?></span>
			<# } else { #>
				<span class="more-details" id="{{ data.theme.id }}-action"><?php _e( 'Live Preview' ); ?></span>
			<# } #>

			<div class="theme-author"><?php printf( __( 'By %s' ), '{{ data.theme.author }}' ); ?></div>

			<# if ( data.theme.isActiveTheme ) { #>
				<h3 class="theme-name" id="{{ data.theme.id }}-name">
					<?php
					/* translators: %s: theme name */
					printf( __( '<span>Active:</span> %s' ), '{{ data.theme.name }}' );
					?>
				</h3>
			<# } else { #>
				<h3 class="theme-name" id="{{ data.theme.id }}-name">{{ data.theme.name }}</h3>
				<div class="theme-actions">
					<button type="button" class="button theme-details"><?php _e( 'Theme Details' ); ?></button>
				</div>
			<# } #>
		</div>
	<?php
	}
}

/**
 * Widget Area Customize Control class.
 *
 * @since 3.9.0
 *
 * @see WP_Customize_Control
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

	/**
	 * @access public
	 */
	public function render_content() {
		?>
		<span class="button-secondary add-new-widget" tabindex="0">
			<?php _e( 'Add a Widget' ); ?>
		</span>

		<span class="reorder-toggle" tabindex="0">
			<span class="reorder"><?php _ex( 'Reorder', 'Reorder widgets in Customizer' ); ?></span>
			<span class="reorder-done"><?php _ex( 'Done', 'Cancel reordering widgets in Customizer' ); ?></span>
		</span>
		<?php
	}

}

/**
 * Widget Form Customize Control class.
 *
 * @since 3.9.0
 *
 * @see WP_Customize_Control
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

	/**
	 *
	 * @global array $wp_registered_widgets
	 */
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
	public function active_callback() {
		return $this->manager->widgets->is_widget_rendered( $this->widget_id );
	}
}

/**
 * Customize Nav Menu Control Class
 *
 * @since 4.3.0
 */
class WP_Customize_Nav_Menu_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'nav_menu';

	/**
	 * The nav menu setting.
	 *
	 * @since 4.3.0
	 *
	 * @var WP_Customize_Nav_Menu_Setting
	 */
	public $setting;

	/**
	 * Don't render the control's content - it uses a JS template instead.
	 *
	 * @since 4.3.0
	 */
	public function render_content() {}

	/**
	 * JS/Underscore template for the control UI.
	 *
	 * @since 4.3.0
	 */
	public function content_template() {
		?>
		<button type="button" class="button-secondary add-new-menu-item" aria-label="<?php esc_attr_e( 'Add or remove menu items' ); ?>" aria-expanded="false" aria-controls="available-menu-items">
			<?php _e( 'Add Items' ); ?>
		</button>
		<button type="button" role="presentation" class="not-a-button reorder-toggle" tabindex="-1">
			<span class="reorder" tabindex="0" role="button" aria-label="<?php esc_attr_e( 'Reorder menu items' ); ?>" aria-describedby="reorder-items-desc"><?php _ex( 'Reorder', 'Reorder menu items in Customizer' ); ?></span>
			<span class="reorder-done" tabindex="0" role="button" aria-label="<?php esc_attr_e( 'Close reorder mode' ); ?>"><?php _ex( 'Done', 'Cancel reordering menu items in Customizer' ); ?></span>
		</button>
		<p class="screen-reader-text" id="reorder-items-desc"><?php _e( 'When in reorder mode, additional controls to reorder menu items will be available in the items list above.' ); ?></p>
		<span class="add-menu-item-loading spinner"></span>
		<span class="menu-delete-item">
			<button type="button" class="not-a-button menu-delete">
				<?php _e( 'Delete menu' ); ?> <span class="screen-reader-text">{{ data.menu_name }}</span>
			</button>
		</span>
		<?php if ( current_theme_supports( 'menus' ) ) : ?>
		<ul class="menu-settings">
			<li class="customize-control">
				<span class="customize-control-title"><?php _e( 'Menu locations' ); ?></span>
			</li>

			<?php foreach ( get_registered_nav_menus() as $location => $description ) : ?>
			<li class="customize-control customize-control-checkbox assigned-menu-location">
				<label>
					<input type="checkbox" data-menu-id="{{ data.menu_id }}" data-location-id="<?php echo esc_attr( $location ); ?>" class="menu-location" /> <?php echo $description; ?>
					<span class="theme-location-set"><?php printf( _x( '(Current: %s)', 'Current menu location' ), '<span class="current-menu-location-name-' . esc_attr( $location ) . '"></span>' ); ?></span>
				</label>
			</li>
			<?php endforeach; ?>

		</ul>
		<?php endif; ?>
		<p>
			<label>
				<input type="checkbox" class="auto_add">
				<?php _e( 'Automatically add new top-level pages to this menu.' ) ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Return params for this control.
	 *
	 * @since 4.3.0
	 *
	 * @return array
	 */
	public function json() {
		$exported            = parent::json();
		$exported['menu_id'] = $this->setting->term_id;

		return $exported;
	}
}

/**
 * Customize control to represent the name field for a given menu.
 *
 * @since 4.3.0
 */
class WP_Customize_Nav_Menu_Item_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'nav_menu_item';

	/**
	 * The nav menu item setting.
	 *
	 * @since 4.3.0
	 *
	 * @var WP_Customize_Nav_Menu_Item_Setting
	 */
	public $setting;

	/**
	 * Constructor.
	 *
	 * @since 4.3.0
	 *
	 * @uses WP_Customize_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager An instance of the WP_Customize_Manager class.
	 * @param string               $id      The control ID.
	 * @param array                $args    Optional. Overrides class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Don't render the control's content - it's rendered with a JS template.
	 *
	 * @since 4.3.0
	 */
	public function render_content() {}

	/**
	 * JS/Underscore template for the control UI.
	 *
	 * @since 4.3.0
	 */
	public function content_template() {
		?>
		<div class="menu-item-bar">
			<div class="menu-item-handle">
				<span class="item-type">{{ data.item_type_label }}</span>
				<span class="item-title">
					<span class="spinner"></span>
					<span class="menu-item-title<# if ( ! data.title ) { #> no-title<# } #>">{{ data.title || wp.customize.Menus.data.l10n.untitled }}</span>
				</span>
				<span class="item-controls">
					<button type="button" class="not-a-button item-edit"><span class="screen-reader-text"><?php _e( 'Edit Menu Item' ); ?></span></button>
					<button type="button" class="not-a-button item-delete submitdelete deletion"><span class="screen-reader-text"><?php _e( 'Remove Menu Item' ); ?></span></button>
				</span>
			</div>
		</div>

		<div class="menu-item-settings" id="menu-item-settings-{{ data.menu_item_id }}">
			<# if ( 'custom' === data.item_type ) { #>
			<p class="field-url description description-thin">
				<label for="edit-menu-item-url-{{ data.menu_item_id }}">
					<?php _e( 'URL' ); ?><br />
					<input class="widefat code edit-menu-item-url" type="text" id="edit-menu-item-url-{{ data.menu_item_id }}" name="menu-item-url" />
				</label>
			</p>
		<# } #>
			<p class="description description-thin">
				<label for="edit-menu-item-title-{{ data.menu_item_id }}">
					<?php _e( 'Navigation Label' ); ?><br />
					<input type="text" id="edit-menu-item-title-{{ data.menu_item_id }}" class="widefat edit-menu-item-title" name="menu-item-title" />
				</label>
			</p>
			<p class="field-link-target description description-thin">
				<label for="edit-menu-item-target-{{ data.menu_item_id }}">
					<input type="checkbox" id="edit-menu-item-target-{{ data.menu_item_id }}" class="edit-menu-item-target" value="_blank" name="menu-item-target" />
					<?php _e( 'Open link in a new tab' ); ?>
				</label>
			</p>
			<p class="field-attr-title description description-thin">
				<label for="edit-menu-item-attr-title-{{ data.menu_item_id }}">
					<?php _e( 'Title Attribute' ); ?><br />
					<input type="text" id="edit-menu-item-attr-title-{{ data.menu_item_id }}" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title" />
				</label>
			</p>
			<p class="field-css-classes description description-thin">
				<label for="edit-menu-item-classes-{{ data.menu_item_id }}">
					<?php _e( 'CSS Classes' ); ?><br />
					<input type="text" id="edit-menu-item-classes-{{ data.menu_item_id }}" class="widefat code edit-menu-item-classes" name="menu-item-classes" />
				</label>
			</p>
			<p class="field-xfn description description-thin">
				<label for="edit-menu-item-xfn-{{ data.menu_item_id }}">
					<?php _e( 'Link Relationship (XFN)' ); ?><br />
					<input type="text" id="edit-menu-item-xfn-{{ data.menu_item_id }}" class="widefat code edit-menu-item-xfn" name="menu-item-xfn" />
				</label>
			</p>
			<p class="field-description description description-thin">
				<label for="edit-menu-item-description-{{ data.menu_item_id }}">
					<?php _e( 'Description' ); ?><br />
					<textarea id="edit-menu-item-description-{{ data.menu_item_id }}" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description">{{ data.description }}</textarea>
					<span class="description"><?php _e( 'The description will be displayed in the menu if the current theme supports it.' ); ?></span>
				</label>
			</p>

			<div class="menu-item-actions description-thin submitbox">
				<# if ( 'custom' != data.item_type && '' != data.original_title ) { #>
				<p class="link-to-original">
					<?php printf( __( 'Original: %s' ), '<a class="original-link" href="{{ data.url }}">{{ data.original_title }}</a>' ); ?>
				</p>
				<# } #>

				<button type="button" class="not-a-button item-delete submitdelete deletion"><?php _e( 'Remove' ); ?></button>
				<span class="spinner"></span>
			</div>
			<input type="hidden" name="menu-item-db-id[{{ data.menu_item_id }}]" class="menu-item-data-db-id" value="{{ data.menu_item_id }}" />
			<input type="hidden" name="menu-item-parent-id[{{ data.menu_item_id }}]" class="menu-item-data-parent-id" value="{{ data.parent }}" />
		</div><!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
		<?php
	}

	/**
	 * Return params for this control.
	 *
	 * @since 4.3.0
	 *
	 * @return array
	 */
	public function json() {
		$exported                 = parent::json();
		$exported['menu_item_id'] = $this->setting->post_id;

		return $exported;
	}
}

/**
 * Customize Menu Location Control Class
 *
 * This custom control is only needed for JS.
 *
 * @since 4.3.0
 */
class WP_Customize_Nav_Menu_Location_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'nav_menu_location';

	/**
	 * Location ID.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var string
	 */
	public $location_id = '';

	/**
	 * Refresh the parameters passed to JavaScript via JSON.
	 *
	 * @since 4.3.0
	 *
	 * @uses WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['locationId'] = $this->location_id;
	}

	/**
	 * Render content just like a normal select control.
	 *
	 * @since 4.3.0
	 */
	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<select <?php $this->link(); ?>>
				<?php
				foreach ( $this->choices as $value => $label ) :
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
				endforeach;
				?>
			</select>
		</label>
		<?php
	}
}

/**
 * Customize control to represent the name field for a given menu.
 *
 * @since 4.3.0
 */
class WP_Customize_Nav_Menu_Name_Control extends WP_Customize_Control {

	/**
	 * Type of control, used by JS.
	 *
	 * @since 4.3.0
	 *
	 * @var string
	 */
	public $type = 'nav_menu_name';

	/**
	 * No-op since we're using JS template.
	 *
	 * @since 4.3.0
	 */
	protected function render_content() {}

	/**
	 * Render the Underscore template for this control.
	 *
	 * @since 4.3.0
	 */
	protected function content_template() {
		?>
		<label>
			<input type="text" class="menu-name-field live-update-section-title" />
		</label>
		<?php
	}
}

/**
 * Customize control class for new menus.
 *
 * @since 4.3.0
 */
class WP_New_Menu_Customize_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'new_menu';

	/**
	 * Render the control's content.
	 *
	 * @since 4.3.0
	 */
	public function render_content() {
		?>
		<button type="button" class="button button-primary" id="create-new-menu-submit"><?php _e( 'Create Menu' ); ?></button>
		<span class="spinner"></span>
		<?php
	}
}
