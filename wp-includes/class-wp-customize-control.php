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
	public $priority          = 10;

	/**
	 * @access public
	 * @var string
	 */
	public $section           = '';

	/**
	 * @access public
	 * @var string
	 */
	public $label             = '';

	/**
	 * @todo: Remove choices
	 *
	 * @access public
	 * @var array
	 */
	public $choices           = array();

	/**
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
	 * Constructor.
	 *
	 * If $args['settings'] is not defined, use the $id as the setting ID.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string $id
	 * @param array $args
	 */
	function __construct( $manager, $id, $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->manager = $manager;
		$this->id = $id;


		// Process settings.
		if ( empty( $this->settings ) )
			$this->settings = $id;

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
	 * Fetch a setting's value.
	 * Grabs the main setting by default.
	 *
	 * @since 3.4.0
	 *
	 * @param string $setting_key
	 * @return mixed The requested setting's value, if the setting exists.
	 */
	public final function value( $setting_key = 'default' ) {
		if ( isset( $this->settings[ $setting_key ] ) )
			return $this->settings[ $setting_key ]->value();
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
	 * Check capabilities and render the control.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Control::render()
	 */
	public final function maybe_render() {
		if ( ! $this->check_capabilities() )
			return;

		do_action( 'customize_render_control', $this );
		do_action( 'customize_render_control_' . $this->id, $this );

		$this->render();
	}

	/**
	 * Render the control. Renders the control wrapper, then calls $this->render_content().
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
	 * Get the data link parameter for a setting.
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
	 * Render the data link parameter for a setting
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
	 * Render the control's content.
	 *
	 * Allows the content to be overriden without having to rewrite the wrapper.
	 *
	 * @since 3.4.0
	 */
	protected function render_content() {
		switch( $this->type ) {
			case 'text':
				?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<input type="text" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
				</label>
				<?php
				break;
			case 'checkbox':
				?>
				<label>
					<input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />
					<?php echo esc_html( $this->label ); ?>
				</label>
				<?php
				break;
			case 'radio':
				if ( empty( $this->choices ) )
					return;

				$name = '_customize-radio-' . $this->id;

				?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php
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
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<select <?php $this->link(); ?>>
						<?php
						foreach ( $this->choices as $value => $label )
							echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
						?>
					</select>
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
		}
	}
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
	 * If $args['settings'] is not defined, use the $id as the setting ID.
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
	 * Enqueue control related scripts/styles.
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
	}

	/**
	 * Render the control's content.
	 *
	 * @since 3.4.0
	 */
	public function render_content() {
		$this_default = $this->setting->default;
		$default_attr = '';
		if ( $this_default ) {
			if ( false === strpos( $this_default, '#' ) )
				$this_default = '#' . $this_default;
			$default_attr = ' data-default-color="' . esc_attr( $this_default ) . '"';
		}
		// The input's value gets set by JS. Don't fill it.
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class="customize-control-content">
				<input class="color-picker-hex" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value' ); ?>"<?php echo $default_attr ?> />
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
	public $type    = 'upload';
	public $removed = '';
	public $context;
	public $extensions = array();

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 3.4.0
	 */
	public function enqueue() {
		wp_enqueue_script( 'wp-plupload' );
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();

		$this->json['removed'] = $this->removed;

		if ( $this->context )
			$this->json['context'] = $this->context;

		if ( $this->extensions )
			$this->json['extensions'] = implode( ',', $this->extensions );
	}

	/**
	 * Render the control's content.
	 *
	 * @since 3.4.0
	 */
	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div>
				<a href="#" class="button-secondary upload"><?php _e( 'Upload' ); ?></a>
				<a href="#" class="remove"><?php _e( 'Remove' ); ?></a>
			</div>
		</label>
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
	public $get_url;
	public $statuses;
	public $extensions = array( 'jpg', 'jpeg', 'gif', 'png' );

	protected $tabs = array();

	/**
	 * Constructor.
	 *
	 * If $args['settings'] is not defined, use the $id as the setting ID.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Upload_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string $id
	 * @param array $args
	 */
	public function __construct( $manager, $id, $args ) {
		$this->statuses = array( '' => __('No Image') );

		parent::__construct( $manager, $id, $args );

		$this->add_tab( 'upload-new', __('Upload New'), array( $this, 'tab_upload_new' ) );
		$this->add_tab( 'uploaded',   __('Uploaded'),   array( $this, 'tab_uploaded' ) );

		// Early priority to occur before $this->manager->prepare_controls();
		add_action( 'customize_controls_init', array( $this, 'prepare_control' ), 5 );
	}

	/**
	 * Prepares the control.
	 *
	 * If no tabs exist, removes the control from the manager.
	 *
	 * @since 3.4.2
	 */
	public function prepare_control() {
		if ( ! $this->tabs )
			$this->manager->remove_control( $this->id );
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Upload_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['statuses'] = $this->statuses;
	}

	/**
	 * Render the control's content.
	 *
	 * @since 3.4.0
	 */
	public function render_content() {
		$src = $this->value();
		if ( isset( $this->get_url ) )
			$src = call_user_func( $this->get_url, $src );

		?>
		<div class="customize-image-picker">
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

			<div class="customize-control-content">
				<div class="dropdown preview-thumbnail" tabindex="0">
					<div class="dropdown-content">
						<?php if ( empty( $src ) ): ?>
							<img style="display:none;" />
						<?php else: ?>
							<img src="<?php echo esc_url( set_url_scheme( $src ) ); ?>" />
						<?php endif; ?>
						<div class="dropdown-status"></div>
					</div>
					<div class="dropdown-arrow"></div>
				</div>
			</div>

			<div class="library">
				<ul>
					<?php foreach ( $this->tabs as $id => $tab ): ?>
						<li data-customize-tab='<?php echo esc_attr( $id ); ?>' tabindex='0'>
							<?php echo esc_html( $tab['label'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php foreach ( $this->tabs as $id => $tab ): ?>
					<div class="library-content" data-customize-tab='<?php echo esc_attr( $id ); ?>'>
						<?php call_user_func( $tab['callback'] ); ?>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="actions">
				<a href="#" class="remove"><?php _e( 'Remove Image' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Add a tab to the control.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id
	 * @param string $label
	 * @param mixed $callback
	 */
	public function add_tab( $id, $label, $callback ) {
		$this->tabs[ $id ] = array(
			'label'    => $label,
			'callback' => $callback,
		);
	}

	/**
	 * Remove a tab from the control.
	 *
	 * @since 3.4.0
	 *
	 * @param string $id
	 */
	public function remove_tab( $id ) {
		unset( $this->tabs[ $id ] );
	}

	/**
	 * @since 3.4.0
	 */
	public function tab_upload_new() {
		if ( ! _device_can_upload() ) {
			?>
			<p><?php _e('The web browser on your device cannot be used to upload files. You may be able to use the <a href="http://wordpress.org/extend/mobile/">native app for your device</a> instead.'); ?></p>
			<?php
		} else {
			?>
			<div class="upload-dropzone">
				<?php _e('Drop a file here or <a href="#" class="upload">select a file</a>.'); ?>
			</div>
			<div class="upload-fallback">
				<span class="button-secondary"><?php _e('Select File'); ?></span>
			</div>
			<?php
		}
	}

	/**
	 * @since 3.4.0
	 */
	public function tab_uploaded() {
		?>
		<div class="uploaded-target"></div>
		<?php
	}

	/**
	 * @since 3.4.0
	 *
	 * @param string $url
	 * @param string $thumbnail_url
	 */
	public function print_tab_image( $url, $thumbnail_url = null ) {
		$url = set_url_scheme( $url );
		$thumbnail_url = ( $thumbnail_url ) ? set_url_scheme( $thumbnail_url ) : $url;
		?>
		<a href="#" class="thumbnail" data-customize-image-value="<?php echo esc_url( $url ); ?>">
			<img src="<?php echo esc_url( $thumbnail_url ); ?>" />
		</a>
		<?php
	}
}

/**
 * Customize Background Image Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
class WP_Customize_Background_Image_Control extends WP_Customize_Image_Control {

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
			'context'  => 'custom-background',
			'get_url'  => 'get_background_image',
		) );

		if ( $this->setting->default )
			$this->add_tab( 'default',  __('Default'),  array( $this, 'tab_default_background' ) );
	}

	/**
	 * @since 3.4.0
	 */
	public function tab_uploaded() {
		$backgrounds = get_posts( array(
			'post_type'  => 'attachment',
			'meta_key'   => '_wp_attachment_is_custom_background',
			'meta_value' => $this->manager->get_stylesheet(),
			'orderby'    => 'none',
			'nopaging'   => true,
		) );

		?><div class="uploaded-target"></div><?php

		if ( empty( $backgrounds ) )
			return;

		foreach ( (array) $backgrounds as $background )
			$this->print_tab_image( esc_url_raw( $background->guid ) );
	}

	/**
	 * @since 3.4.0
	 * @uses WP_Customize_Image_Control::print_tab_image()
	 */
	public function tab_default_background() {
		$this->print_tab_image( $this->setting->default );
	}
}

/**
 * Customize Header Image Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
class WP_Customize_Header_Image_Control extends WP_Customize_Image_Control {
	/**
	 * The processed default headers.
	 * @since 3.4.2
	 * @var array
	 */
	protected $default_headers;

	/**
	 * The uploaded headers.
	 * @since 3.4.2
	 * @var array
	 */
	protected $uploaded_headers;

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Image_Control::__construct()
	 * @uses WP_Customize_Image_Control::add_tab()
	 *
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
			'context'  => 'custom-header',
			'removed'  => 'remove-header',
			'get_url'  => 'get_header_image',
			'statuses' => array(
				''                      => __('Default'),
				'remove-header'         => __('No Image'),
				'random-default-image'  => __('Random Default Image'),
				'random-uploaded-image' => __('Random Uploaded Image'),
			)
		) );

		// Remove the upload tab.
		$this->remove_tab( 'upload-new' );
	}

	/**
	 * Prepares the control.
	 *
	 * If no tabs exist, removes the control from the manager.
	 *
	 * @since 3.4.2
	 */
	public function prepare_control() {
		global $custom_image_header;
		if ( empty( $custom_image_header ) )
			return parent::prepare_control();

		// Process default headers and uploaded headers.
		$custom_image_header->process_default_headers();
		$this->default_headers = $custom_image_header->default_headers;
		$this->uploaded_headers = get_uploaded_header_images();

		if ( $this->default_headers )
			$this->add_tab( 'default',  __('Default'),  array( $this, 'tab_default_headers' ) );

		if ( ! $this->uploaded_headers )
			$this->remove_tab( 'uploaded' );

		return parent::prepare_control();
	}

	/**
	 * @since 3.4.0
	 *
	 * @param mixed $choice Which header image to select. (@see Custom_Image_Header::get_header_image() )
	 * @param array $header
	 */
	public function print_header_image( $choice, $header ) {
		$header['url']           = set_url_scheme( $header['url'] );
		$header['thumbnail_url'] = set_url_scheme( $header['thumbnail_url'] );

		$header_image_data = array( 'choice' => $choice );
		foreach ( array( 'attachment_id', 'width', 'height', 'url', 'thumbnail_url' ) as $key ) {
			if ( isset( $header[ $key ] ) )
				$header_image_data[ $key ] = $header[ $key ];
		}


		?>
		<a href="#" class="thumbnail"
			data-customize-image-value="<?php echo esc_url( $header['url'] ); ?>"
			data-customize-header-image-data="<?php echo esc_attr( json_encode( $header_image_data ) ); ?>">
			<img src="<?php echo esc_url( $header['thumbnail_url'] ); ?>" />
		</a>
		<?php
	}

	/**
	 * @since 3.4.0
	 */
	public function tab_uploaded() {
		?><div class="uploaded-target"></div><?php

		foreach ( $this->uploaded_headers as $choice => $header )
			$this->print_header_image( $choice, $header );
	}

	/**
	 * @since 3.4.0
	 */
	public function tab_default_headers() {
		foreach ( $this->default_headers as $choice => $header )
			$this->print_header_image( $choice, $header );
	}
}