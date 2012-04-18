<?php
/**
 * Customize Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

class WP_Customize_Control {
	public $manager;
	public $id;

	public $settings;
	public $setting;

	public $priority          = 10;
	public $section           = '';
	public $label             = '';
	// @todo: remove choices
	public $choices           = array();

	public $json = array();

	public $visibility;

	public $type = 'text';


	/**
	 * Constructor.
	 *
	 * If $args['settings'] is not defined, use the $id as the setting ID.
	 *
	 * @since 3.4.0
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
	public function enqueue() {
		switch( $this->type ) {
			case 'color':
				wp_enqueue_script( 'farbtastic' );
				wp_enqueue_style( 'farbtastic' );
				break;
			case 'upload':
				wp_enqueue_script( 'wp-plupload' );
				break;
		}
	}


	/**
	 * Fetch a setting's value.
	 * Grabs the main setting by default.
	 *
	 * @since 3.4.0
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

		if ( $this->visibility ) {
			if ( is_string( $this->visibility ) ) {
				$this->json['visibility'] = array(
					'id'    => $this->visibility,
					'value' => true,
				);
			} else {
				$this->json['visibility'] = array(
					'id'    => $this->visibility[0],
					'value' => $this->visibility[1],
				);
			}

		}
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

		$style = '';
		if ( $this->visibility ) {
			if ( is_string( $this->visibility ) ) {
				$visibility_id    = $this->visibility;
				$visibility_value = true;
			} else {
				$visibility_id    = $this->visibility[0];
				$visibility_value = $this->visibility[1];
			}
			$visibility_setting = $this->manager->get_setting( $visibility_id );

			if ( $visibility_setting && $visibility_value != $visibility_setting->value() )
				$style = 'style="display:none;"';
		}

		?><li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>" <?php echo $style; ?>>
			<?php $this->render_content(); ?>
		</li><?php
	}

	public function get_link( $setting_key = 'default' ) {
		if ( ! isset( $this->settings[ $setting_key ] ) )
			return '';

		return 'data-customize-setting-link="' . esc_attr( $this->settings[ $setting_key ]->id ) . '"';
	}

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
			case 'color':
				?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<a href="#" class="color-picker-toggle">
						<div class="color-picker-spot"></div>
						<div class="color-picker-dropdown"></div>
					</a>
					<div class="color-picker-control color-picker-hex">
						<span>#</span>
						<input type="text" <?php $this->link(); ?> />
					</div>
					<div class="color-picker-control farbtastic-placeholder"></div>
				</label>
				<?php
				break;
			case 'checkbox':
				?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> class="customize-control-content" />
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
					<select <?php $this->link(); ?> class="customize-control-content">
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

class WP_Customize_Upload_Control extends WP_Customize_Control {
	public $type    = 'upload';
	public $removed = '';
	public $context;

	public function enqueue() {
		wp_enqueue_script( 'wp-plupload' );
	}

	public function to_json() {
		parent::to_json();

		$this->json['removed'] = $this->removed;

		if ( $this->context )
			$this->json['context'] = $this->context;
	}

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

class WP_Customize_Image_Control extends WP_Customize_Upload_Control {
	public $type = 'image';
	public $tabs = array();
	public $get_url;

	public function render_content() {
		$src = $this->value();
		if ( isset( $this->get_url ) )
			$src = call_user_func( $this->get_url, $src );

		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class="customize-image-picker">
				<div class="thumbnail">
					<?php if ( empty( $src ) ): ?>
						<img style="display:none;" />
					<?php else: ?>
						<img src="<?php echo esc_url( $src ); ?>" />
					<?php endif; ?>
				</div>
				<div class="actions">
					<a href="#" class="upload"><?php _e( 'Upload New' ); ?></a>
					<a href="#" class="change"><?php _e( 'Change Image' ); ?></a>
					<a href="#" class="remove"><?php _e( 'Remove Image' ); ?></a>
				</div>
				<div class="library">
					<ul>
						<?php foreach ( $this->tabs as $tab ): ?>
							<li data-customize-tab='<?php echo esc_attr( $tab[0] ); ?>'>
								<?php echo esc_html( $tab[1] ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php foreach ( $this->tabs as $tab ): ?>
						<div class="library-content" data-customize-tab='<?php echo esc_attr( $tab[0] ); ?>'>
							<?php call_user_func( $tab[2] ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</label>
		<?php
	}
}
