<?php
/**
 * Widget API: WP_Media_Widget class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.8.0
 */

/**
 * Core class that implements a media widget.
 *
 * @since 4.8.0
 *
 * @see WP_Widget
 */
abstract class WP_Widget_Media extends WP_Widget {

	/**
	 * Translation labels.
	 *
	 * @since 4.8.0
	 * @var array
	 */
	public $l10n = array(
		'add_to_widget'              => '',
		'replace_media'              => '',
		'edit_media'                 => '',
		'media_library_state_multi'  => '',
		'media_library_state_single' => '',
		'missing_attachment'         => '',
		'no_media_selected'          => '',
		'add_media'                  => '',
	);

	/**
	 * Whether or not the widget has been registered yet.
	 *
	 * @since 4.8.1
	 * @var bool
	 */
	protected $registered = false;

	/**
	 * The default widget description.
	 *
	 * @since 6.0.0
	 * @var string
	 */
	protected static $default_description = '';

	/**
	 * The default localized strings used by the widget.
	 *
	 * @since 6.0.0
	 * @var string[]
	 */
	protected static $l10n_defaults = array();

	/**
	 * Constructor.
	 *
	 * @since 4.8.0
	 *
	 * @param string $id_base         Base ID for the widget, lowercase and unique.
	 * @param string $name            Name for the widget displayed on the configuration page.
	 * @param array  $widget_options  Optional. Widget options. See wp_register_sidebar_widget() for
	 *                                information on accepted arguments. Default empty array.
	 * @param array  $control_options Optional. Widget control options. See wp_register_widget_control()
	 *                                for information on accepted arguments. Default empty array.
	 */
	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
		$widget_opts = wp_parse_args(
			$widget_options,
			array(
				'description'                 => self::get_default_description(),
				'customize_selective_refresh' => true,
				'show_instance_in_rest'       => true,
				'mime_type'                   => '',
			)
		);

		$control_opts = wp_parse_args( $control_options, array() );

		$this->l10n = array_merge( self::get_l10n_defaults(), array_filter( $this->l10n ) );

		parent::__construct(
			$id_base,
			$name,
			$widget_opts,
			$control_opts
		);
	}

	/**
	 * Add hooks while registering all widget instances of this widget class.
	 *
	 * @since 4.8.0
	 *
	 * @param int $number Optional. The unique order number of this widget instance
	 *                    compared to other instances of the same class. Default -1.
	 */
	public function _register_one( $number = -1 ) {
		parent::_register_one( $number );
		if ( $this->registered ) {
			return;
		}
		$this->registered = true;

		// Note that the widgets component in the customizer will also do
		// the 'admin_print_scripts-widgets.php' action in WP_Customize_Widgets::print_scripts().
		add_action( 'admin_print_scripts-widgets.php', array( $this, 'enqueue_admin_scripts' ) );

		if ( $this->is_preview() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_preview_scripts' ) );
		}

		// Note that the widgets component in the customizer will also do
		// the 'admin_footer-widgets.php' action in WP_Customize_Widgets::print_footer_scripts().
		add_action( 'admin_footer-widgets.php', array( $this, 'render_control_template_scripts' ) );

		add_filter( 'display_media_states', array( $this, 'display_media_state' ), 10, 2 );
	}

	/**
	 * Get schema for properties of a widget instance (item).
	 *
	 * @since 4.8.0
	 *
	 * @see WP_REST_Controller::get_item_schema()
	 * @see WP_REST_Controller::get_additional_fields()
	 * @link https://core.trac.wordpress.org/ticket/35574
	 *
	 * @return array Schema for properties.
	 */
	public function get_instance_schema() {
		$schema = array(
			'attachment_id' => array(
				'type'        => 'integer',
				'default'     => 0,
				'minimum'     => 0,
				'description' => __( 'Attachment post ID' ),
				'media_prop'  => 'id',
			),
			'url'           => array(
				'type'        => 'string',
				'default'     => '',
				'format'      => 'uri',
				'description' => __( 'URL to the media file' ),
			),
			'title'         => array(
				'type'                  => 'string',
				'default'               => '',
				'sanitize_callback'     => 'sanitize_text_field',
				'description'           => __( 'Title for the widget' ),
				'should_preview_update' => false,
			),
		);

		/**
		 * Filters the media widget instance schema to add additional properties.
		 *
		 * @since 4.9.0
		 *
		 * @param array           $schema Instance schema.
		 * @param WP_Widget_Media $widget Widget object.
		 */
		$schema = apply_filters( "widget_{$this->id_base}_instance_schema", $schema, $this );

		return $schema;
	}

	/**
	 * Determine if the supplied attachment is for a valid attachment post with the specified MIME type.
	 *
	 * @since 4.8.0
	 *
	 * @param int|WP_Post $attachment Attachment post ID or object.
	 * @param string      $mime_type  MIME type.
	 * @return bool Is matching MIME type.
	 */
	public function is_attachment_with_mime_type( $attachment, $mime_type ) {
		if ( empty( $attachment ) ) {
			return false;
		}
		$attachment = get_post( $attachment );
		if ( ! $attachment ) {
			return false;
		}
		if ( 'attachment' !== $attachment->post_type ) {
			return false;
		}
		return wp_attachment_is( $mime_type, $attachment );
	}

	/**
	 * Sanitize a token list string, such as used in HTML rel and class attributes.
	 *
	 * @since 4.8.0
	 *
	 * @link http://w3c.github.io/html/infrastructure.html#space-separated-tokens
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/DOMTokenList
	 * @param string|array $tokens List of tokens separated by spaces, or an array of tokens.
	 * @return string Sanitized token string list.
	 */
	public function sanitize_token_list( $tokens ) {
		if ( is_string( $tokens ) ) {
			$tokens = preg_split( '/\s+/', trim( $tokens ) );
		}
		$tokens = array_map( 'sanitize_html_class', $tokens );
		$tokens = array_filter( $tokens );
		return implode( ' ', $tokens );
	}

	/**
	 * Displays the widget on the front-end.
	 *
	 * @since 4.8.0
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance Saved setting from the database.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, wp_list_pluck( $this->get_instance_schema(), 'default' ) );

		// Short-circuit if no media is selected.
		if ( ! $this->has_content( $instance ) ) {
			return;
		}

		echo $args['before_widget'];

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		/**
		 * Filters the media widget instance prior to rendering the media.
		 *
		 * @since 4.8.0
		 *
		 * @param array           $instance Instance data.
		 * @param array           $args     Widget args.
		 * @param WP_Widget_Media $widget   Widget object.
		 */
		$instance = apply_filters( "widget_{$this->id_base}_instance", $instance, $args, $this );

		$this->render_media( $instance );

		echo $args['after_widget'];
	}

	/**
	 * Sanitizes the widget form values as they are saved.
	 *
	 * @since 4.8.0
	 * @since 5.9.0 Renamed `$instance` to `$old_instance` to match parent class
	 *              for PHP 8 named parameter support.
	 *
	 * @see WP_Widget::update()
	 * @see WP_REST_Request::has_valid_params()
	 * @see WP_REST_Request::sanitize_params()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$schema = $this->get_instance_schema();
		foreach ( $schema as $field => $field_schema ) {
			if ( ! array_key_exists( $field, $new_instance ) ) {
				continue;
			}
			$value = $new_instance[ $field ];

			/*
			 * Workaround for rest_validate_value_from_schema() due to the fact that
			 * rest_is_boolean( '' ) === false, while rest_is_boolean( '1' ) is true.
			 */
			if ( 'boolean' === $field_schema['type'] && '' === $value ) {
				$value = false;
			}

			if ( true !== rest_validate_value_from_schema( $value, $field_schema, $field ) ) {
				continue;
			}

			$value = rest_sanitize_value_from_schema( $value, $field_schema );

			// @codeCoverageIgnoreStart
			if ( is_wp_error( $value ) ) {
				continue; // Handle case when rest_sanitize_value_from_schema() ever returns WP_Error as its phpdoc @return tag indicates.
			}

			// @codeCoverageIgnoreEnd
			if ( isset( $field_schema['sanitize_callback'] ) ) {
				$value = call_user_func( $field_schema['sanitize_callback'], $value );
			}
			if ( is_wp_error( $value ) ) {
				continue;
			}
			$old_instance[ $field ] = $value;
		}

		return $old_instance;
	}

	/**
	 * Render the media on the frontend.
	 *
	 * @since 4.8.0
	 *
	 * @param array $instance Widget instance props.
	 */
	abstract public function render_media( $instance );

	/**
	 * Outputs the settings update form.
	 *
	 * Note that the widget UI itself is rendered with JavaScript via `MediaWidgetControl#render()`.
	 *
	 * @since 4.8.0
	 *
	 * @see \WP_Widget_Media::render_control_template_scripts() Where the JS template is located.
	 *
	 * @param array $instance Current settings.
	 */
	final public function form( $instance ) {
		$instance_schema = $this->get_instance_schema();
		$instance        = wp_array_slice_assoc(
			wp_parse_args( (array) $instance, wp_list_pluck( $instance_schema, 'default' ) ),
			array_keys( $instance_schema )
		);

		foreach ( $instance as $name => $value ) : ?>
			<input
				type="hidden"
				data-property="<?php echo esc_attr( $name ); ?>"
				class="media-widget-instance-property"
				name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( $name ) ); // Needed specifically by wpWidgets.appendTitle(). ?>"
				value="<?php echo esc_attr( is_array( $value ) ? implode( ',', $value ) : (string) $value ); ?>"
			/>
			<?php
		endforeach;
	}

	/**
	 * Filters the default media display states for items in the Media list table.
	 *
	 * @since 4.8.0
	 *
	 * @param array   $states An array of media states.
	 * @param WP_Post $post   The current attachment object.
	 * @return array
	 */
	public function display_media_state( $states, $post = null ) {
		if ( ! $post ) {
			$post = get_post();
		}

		// Count how many times this attachment is used in widgets.
		$use_count = 0;
		foreach ( $this->get_settings() as $instance ) {
			if ( isset( $instance['attachment_id'] ) && $instance['attachment_id'] === $post->ID ) {
				$use_count++;
			}
		}

		if ( 1 === $use_count ) {
			$states[] = $this->l10n['media_library_state_single'];
		} elseif ( $use_count > 0 ) {
			$states[] = sprintf( translate_nooped_plural( $this->l10n['media_library_state_multi'], $use_count ), number_format_i18n( $use_count ) );
		}

		return $states;
	}

	/**
	 * Enqueue preview scripts.
	 *
	 * These scripts normally are enqueued just-in-time when a widget is rendered.
	 * In the customizer, however, widgets can be dynamically added and rendered via
	 * selective refresh, and so it is important to unconditionally enqueue them in
	 * case a widget does get added.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_preview_scripts() {}

	/**
	 * Loads the required scripts and styles for the widget control.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_media();
		wp_enqueue_script( 'media-widgets' );
	}

	/**
	 * Render form template scripts.
	 *
	 * @since 4.8.0
	 */
	public function render_control_template_scripts() {
		?>
		<script type="text/html" id="tmpl-widget-media-<?php echo esc_attr( $this->id_base ); ?>-control">
			<# var elementIdPrefix = 'el' + String( Math.random() ) + '_' #>
			<p>
				<label for="{{ elementIdPrefix }}title"><?php esc_html_e( 'Title:' ); ?></label>
				<input id="{{ elementIdPrefix }}title" type="text" class="widefat title">
			</p>
			<div class="media-widget-preview <?php echo esc_attr( $this->id_base ); ?>">
				<div class="attachment-media-view">
					<button type="button" class="select-media button-add-media not-selected">
						<?php echo esc_html( $this->l10n['add_media'] ); ?>
					</button>
				</div>
			</div>
			<p class="media-widget-buttons">
				<button type="button" class="button edit-media selected">
					<?php echo esc_html( $this->l10n['edit_media'] ); ?>
				</button>
			<?php if ( ! empty( $this->l10n['replace_media'] ) ) : ?>
				<button type="button" class="button change-media select-media selected">
					<?php echo esc_html( $this->l10n['replace_media'] ); ?>
				</button>
			<?php endif; ?>
			</p>
			<div class="media-widget-fields">
			</div>
		</script>
		<?php
	}

	/**
	 * Resets the cache for the default labels.
	 *
	 * @since 6.0.0
	 */
	public static function reset_default_labels() {
		self::$default_description = '';
		self::$l10n_defaults       = array();
	}

	/**
	 * Whether the widget has content to show.
	 *
	 * @since 4.8.0
	 *
	 * @param array $instance Widget instance props.
	 * @return bool Whether widget has content.
	 */
	protected function has_content( $instance ) {
		return ( $instance['attachment_id'] && 'attachment' === get_post_type( $instance['attachment_id'] ) ) || $instance['url'];
	}

	/**
	 * Returns the default description of the widget.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	protected static function get_default_description() {
		if ( self::$default_description ) {
			return self::$default_description;
		}

		self::$default_description = __( 'A media item.' );
		return self::$default_description;
	}

	/**
	 * Returns the default localized strings used by the widget.
	 *
	 * @since 6.0.0
	 *
	 * @return (string|array)[]
	 */
	protected static function get_l10n_defaults() {
		if ( ! empty( self::$l10n_defaults ) ) {
			return self::$l10n_defaults;
		}

		self::$l10n_defaults = array(
			'no_media_selected'          => __( 'No media selected' ),
			'add_media'                  => _x( 'Add Media', 'label for button in the media widget' ),
			'replace_media'              => _x( 'Replace Media', 'label for button in the media widget; should preferably not be longer than ~13 characters long' ),
			'edit_media'                 => _x( 'Edit Media', 'label for button in the media widget; should preferably not be longer than ~13 characters long' ),
			'add_to_widget'              => __( 'Add to Widget' ),
			'missing_attachment'         => sprintf(
				/* translators: %s: URL to media library. */
				__( 'That file cannot be found. Check your <a href="%s">media library</a> and make sure it was not deleted.' ),
				esc_url( admin_url( 'upload.php' ) )
			),
			/* translators: %d: Widget count. */
			'media_library_state_multi'  => _n_noop( 'Media Widget (%d)', 'Media Widget (%d)' ),
			'media_library_state_single' => __( 'Media Widget' ),
			'unsupported_file_type'      => __( 'Looks like this is not the correct kind of file. Please link to an appropriate file instead.' ),
		);

		return self::$l10n_defaults;
	}
}
