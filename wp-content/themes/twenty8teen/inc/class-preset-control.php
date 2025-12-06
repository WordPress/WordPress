<?php
/**
 * Twenty8teen preset Customizer control class
 * @package Twenty8teen
 */

class Twenty8teen_Customize_Preset_Control extends WP_Customize_Control {
	/**
	 * This type of customize control is used to manage option presets.
	 */
	public $type = 'preset';

	/**
	 * The function to call to retrieve preset values.
	 */
	public $preset_values_callback;

	/**
	 * The list of setting IDs to select from to create a preset.
	 */
	public $settings_choices = array();

	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		if ( ! function_exists( $this->preset_values_callback ) ) {
			$this->preset_values_callback = 'get_theme_mod';
		}
		$affected = array();
		$this->settings_choices = self::flatten_array( $this->settings_choices );
		foreach ( $this->settings_choices as $setting_id => $data ) {
			$control = $manager->get_control( $setting_id );
			if ( $control ) {
				$affected[$setting_id] = $control->label ? $control->label : $setting_id;
			}
		}
		$this->settings_choices = $affected;
		add_filter( 'customize_refresh_nonces', array( $this, 'refresh_nonces' ) );
		add_action( 'wp_ajax_twenty8teen_retrieve_preset', array( $this,'ajax_preset_values' ) );
		add_action( 'wp_ajax_twenty8teen_save_preset', array( $this, 'ajax_save_preset' ) );
		add_action( 'wp_ajax_twenty8teen_delete_preset', array( $this, 'ajax_delete_preset' ) );
		add_action( 'customize_save_' . $this->id, array( $this, 'external_save_action' ) );
	}

	/**
	 * Enqueue scripts/styles.
	 */
	public function enqueue() {
		wp_enqueue_style( 'twenty8teen-customize-preset', get_template_directory_uri() .
      '/css/customize-preset.css', array(), '20250301.3' );
		wp_enqueue_script( 'twenty8teen-customize-preset', get_template_directory_uri() .
      '/js/customize-preset.js', array( 'jquery', 'customize-controls' ), '20190103' );
	}

	/**
	 * Indicate whether the preset is saved in the database.
	 */
	public function saved( $preset ) {
		$all = get_theme_mod( $this->id, array() );
		return isset ( $all[$preset] );
	}

	/**
	 * Flatten an array of values.
	 */
	public static function flatten_array( $values ) {
		$flat = array();
		foreach ($values as $setting_id => $avalue) {
			if ( is_array( $avalue ) ) {
				foreach ($avalue as $subkey => $subvalue) {
					$flat[$setting_id . '[' . $subkey . ']'] = $subvalue;
				}
			}
			else {
				$flat[$setting_id] = $avalue;
			}
		}
		return $flat;
	}

	/**
	 * Retrieve the values for a particular preset.
	 */
	public function get_preset_values( $preset ) {
		$values = call_user_func_array( $this->preset_values_callback, array(
			$this->id,
			$preset,
		) );
		return self::flatten_array( $values );
	}

	/**
	 * Add nonces for Customizer for presets.
	 */
	function refresh_nonces( $nonces ) {
		$nonces['twenty8teen-customize-presets' . $this->id] =
			wp_create_nonce( 'twenty8teen-customize-presets' . $this->id );
		$nonces['twenty8teen-customize-presets-delete' . $this->id] =
			wp_create_nonce( 'twenty8teen-customize-presets-delete' . $this->id );
		return $nonces;
	}

/**
	 * Ajax handler for supplying option preset values.
	 */
	public function ajax_preset_values() {
		check_ajax_referer( 'twenty8teen-customize-presets' . $this->id, 'presets_nonce' );
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( -1 );
		}

		if ( ! isset( $_POST['preset'] ) || empty( $_POST['preset'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing preset name.', 'twenty8teen' ) ) );
		}
		$preset = sanitize_text_field( wp_unslash( $_POST['preset'] ) );

		$values = $this->get_preset_values( $preset );
		if ( empty( $values ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing preset values.', 'twenty8teen' ) ) );
		}
		else {
			wp_send_json_success( array(
				'values' => apply_filters( 'twenty8teen_ajax_preset_values', $values, $preset, $this->id )
			) );
		}
	}

	/**
	 * Ajax handler for saving an option preset.
	 */
	public function ajax_save_preset() {
		check_ajax_referer( 'twenty8teen-customize-presets' . $this->id, 'presets_nonce' );
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( -1 );
		}

		if ( isset( $_POST['preset_values'] ) ) {
			$values = json_decode( sanitize_textarea_field( wp_unslash( $_POST['preset_values'] ) ), true );
			if ( count( $values ) === 0 ) {
				wp_send_json_error( array( 'message' => __( 'Missing preset values.', 'twenty8teen' ) ) );
			}
			foreach ( $values as $setting_id => $unsanitized_value ) {
				$setting = $this->manager->get_setting( $setting_id );
				if ( ! $setting ) {
					unset( $values[$setting_id] );
				}
				else {
					$values[$setting_id] = $setting->sanitize( $unsanitized_value );
				}
			}
		} else {
			wp_send_json_error( array( 'message' => __( 'Missing preset values.', 'twenty8teen' ) ) );
		}
		$preset = sanitize_text_field( wp_unslash( $_POST['preset'] ) );
		$preset = empty( $preset ) ?
			sanitize_text_field( date_i18n( get_option( 'date_format' ) .
				' '. get_option( 'time_format' ), current_time( 'timestamp' ) ) )
			: ( is_numeric( $preset ) ? 'n' . $preset : $preset );
		$all = get_theme_mod( $this->id, array() );
		$all[$preset] = $values;
		set_theme_mod( $this->id, $all );
		ob_start();
		$this->render_preset_content( $preset );
		$content = ob_get_clean();
		wp_send_json_success( array(
			'preset' => $preset,
			'content' => $content,
		) );
	}

	/**
	 * Ajax handler for deleting an option preset.
	 */
	public function ajax_delete_preset() {
		check_ajax_referer( 'twenty8teen-customize-presets-delete' . $this->id, 'presets_nonce' );
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( -1 );
		}

		if ( ! isset( $_POST['preset'] ) || empty( $_POST['preset'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing preset name.', 'twenty8teen' ) ) );
		}
		$preset = sanitize_text_field( wp_unslash( $_POST['preset'] ) );
		$all = get_theme_mod( $this->id, array() );
		unset( $all[$preset] );
		set_theme_mod( $this->id, $all );
		ob_start();
		$this->render_preset_content( $preset );
		$content = ob_get_clean();
		wp_send_json_success( array(
			'message' => __( 'Preset deleted.', 'twenty8teen' ),
			'preset' => $preset,
			'content' => $content,
		) );
	}

	/**
	 * Handle external action to save the presets.
	 */
	public function external_save_action( $wp_customize ) {
		if ( ! defined( 'DOING_AUTOSAVE' ) ) {
			add_filter( 'pre_set_theme_mod_' . $this->id, array( $this, 'pre_set_theme_mod_filter' ), 15, 2 );
		}
	}

	/**
	 * Handle external save by merging.
	 */
	public function pre_set_theme_mod_filter( $value, $old_value ) {
		$value = array_merge( (array) $old_value, (array) $value );
		remove_filter( 'pre_set_theme_mod_' . $this->id, array( $this, 'pre_set_theme_mod_filter' ), 15 );
		return $value;
	}

	/**
	 * Displays the content for one preset.
	 */
	public function render_preset_content( $preset ) {
		$values = $this->get_preset_values( $preset );
		if ( is_array( $values ) && count( $values ) ) {
			$esc_preset = esc_attr( $preset );
			$keys = join( ',', array_keys( $values ) ); ?>
			<details data-preset="<?php echo $esc_preset; ?>" class="preset-item">
				<summary class="preset-list-section-title">
					<?php echo esc_html( empty( $this->choices[$preset] ) ? $preset : $this->choices[$preset] ); ?>
				</summary>
				<div  class="preset-list-section-content submitbox">
					<?php if ( $keys ) : ?>
	      	<button type="button" class="button apply-preset-item"
						value="<?php echo $esc_preset; ?>"
						data-keys="<?php echo esc_attr( $keys ); ?>">
						<?php esc_html_e( 'Apply', 'twenty8teen' ); ?></button>
	      	<button type="button" class="button revert-preset-item hidden"
						value="<?php echo $esc_preset; ?>">
						<?php esc_html_e( 'Revert', 'twenty8teen' ); ?></button>
					<?php endif; ?>
					<ul>
					<?php foreach ( $values as $id => $data ) : ?>
						<li class="preset-list-item"><?php echo esc_html( $this->settings_choices[$id] ); ?></li>
					<?php endforeach; ?>
					</ul>
					<?php if ( $this->saved( $preset ) ) : ?>
					<button type="button" class="button-link item-delete submitdelete"
						value="<?php echo $esc_preset; ?>">
						<?php esc_html_e( 'Delete', 'twenty8teen' ); ?></button>
					<?php endif; ?>
				</div>
			</details>
		<?php
		}
	}

	/**
	 * Displays the control content.
	 */
	public function render_content() {
		if ( empty( $this->preset_values_callback ) || empty( $this->settings_choices ) ) {
			return;
		}

		if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif;

		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<div id="customize-control-preset" class="customize-preset preset-list-container">
		<?php foreach ( $this->choices as $preset => $label ) {
			$this->render_preset_content( $preset );
		} ?>

		<button type="button" class="button add-content">
		<?php esc_html_e( 'Create Preset', 'twenty8teen' ); ?></button>
		<div class="new-content-item hidden">
			<span class="description customize-control-description">
				<?php esc_html_e( 'Presets using the same name will overwrite the other.', 'twenty8teen' ); ?></span>
			<label for="create-input-<?php echo esc_attr( $this->id ); ?>">
				<span class="screen-reader-text"><?php esc_html_e( 'New preset name', 'twenty8teen' ); ?>
				</span>
			</label>
			<input type="text" id="create-input-<?php echo esc_attr( $this->id ); ?>"
		 		class="preset-name-field live-update-section-title create-item-input"
		 		placeholder="<?php esc_attr_e( 'Enter preset name', 'twenty8teen' ); ?>" />
			<p class="description customize-control-description">
				<?php esc_html_e( 'The current value of the selected options will be saved.', 'twenty8teen' ); ?></p>
			<?php foreach ( $this->settings_choices as $id => $label ) : ?>
      	<label><input type="checkbox" value="<?php echo esc_attr( $id ); ?>" />
				<?php echo esc_html( $label ); ?></label><br />
			<?php endforeach; ?>

			<p><button type="button" class="button button-secondary save-new-preset">
				<?php esc_html_e( 'Save Preset', 'twenty8teen' ); ?>
				<button type="button" class="button button-secondary cancel-new-preset">
					<?php esc_html_e( 'Cancel', 'twenty8teen' ); ?></button></p>
		</div>
		<input type="hidden" <?php $this->link(); ?> value="none" />
		</div>
	<?php
	}

}
