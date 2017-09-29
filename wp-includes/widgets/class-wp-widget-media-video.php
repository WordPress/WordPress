<?php
/**
 * Widget API: WP_Widget_Media_Video class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.8.0
 */

/**
 * Core class that implements a video widget.
 *
 * @since 4.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_Media_Video extends WP_Widget_Media {

	/**
	 * Constructor.
	 *
	 * @since  4.8.0
	 */
	public function __construct() {
		parent::__construct( 'media_video', __( 'Video' ), array(
			'description' => __( 'Displays a video from the media library or from YouTube, Vimeo, or another provider.' ),
			'mime_type'   => 'video',
		) );

		$this->l10n = array_merge( $this->l10n, array(
			'no_media_selected' => __( 'No video selected' ),
			'add_media' => _x( 'Add Video', 'label for button in the video widget' ),
			'replace_media' => _x( 'Replace Video', 'label for button in the video widget; should preferably not be longer than ~13 characters long' ),
			'edit_media' => _x( 'Edit Video', 'label for button in the video widget; should preferably not be longer than ~13 characters long' ),
			'missing_attachment' => sprintf(
				/* translators: %s: URL to media library */
				__( 'We can&#8217;t find that video. Check your <a href="%s">media library</a> and make sure it wasn&#8217;t deleted.' ),
				esc_url( admin_url( 'upload.php' ) )
			),
			/* translators: %d: widget count */
			'media_library_state_multi' => _n_noop( 'Video Widget (%d)', 'Video Widget (%d)' ),
			'media_library_state_single' => __( 'Video Widget' ),
			/* translators: %s: a list of valid video file extensions */
			'unsupported_file_type' => sprintf( __( 'Sorry, we can&#8217;t display the video file type selected. Please select a supported video file (%s) or stream (YouTube or Vimeo) instead.' ), '<code>.' . implode( '</code>, <code>.', wp_get_video_extensions() ) . '</code>' ),
		) );
	}

	/**
	 * Get schema for properties of a widget instance (item).
	 *
	 * @since  4.8.0
	 *
	 * @see WP_REST_Controller::get_item_schema()
	 * @see WP_REST_Controller::get_additional_fields()
	 * @link https://core.trac.wordpress.org/ticket/35574
	 * @return array Schema for properties.
	 */
	public function get_instance_schema() {
		$schema = array_merge(
			parent::get_instance_schema(),
			array(
				'preload' => array(
					'type' => 'string',
					'enum' => array( 'none', 'auto', 'metadata' ),
					'default' => 'metadata',
					'description' => __( 'Preload' ),
					'should_preview_update' => false,
				),
				'loop' => array(
					'type' => 'boolean',
					'default' => false,
					'description' => __( 'Loop' ),
					'should_preview_update' => false,
				),
				'content' => array(
					'type' => 'string',
					'default' => '',
					'sanitize_callback' => 'wp_kses_post',
					'description' => __( 'Tracks (subtitles, captions, descriptions, chapters, or metadata)' ),
					'should_preview_update' => false,
				),
			)
		);

		foreach ( wp_get_video_extensions() as $video_extension ) {
			$schema[ $video_extension ] = array(
				'type' => 'string',
				'default' => '',
				'format' => 'uri',
				/* translators: %s: video extension */
				'description' => sprintf( __( 'URL to the %s video source file' ), $video_extension ),
			);
		}

		return $schema;
	}

	/**
	 * Render the media on the frontend.
	 *
	 * @since  4.8.0
	 *
	 * @param array $instance Widget instance props.
	 *
	 * @return void
	 */
	public function render_media( $instance ) {
		$instance = array_merge( wp_list_pluck( $this->get_instance_schema(), 'default' ), $instance );
		$attachment = null;

		if ( $this->is_attachment_with_mime_type( $instance['attachment_id'], $this->widget_options['mime_type'] ) ) {
			$attachment = get_post( $instance['attachment_id'] );
		}

		$src = $instance['url'];
		if ( $attachment ) {
			$src = wp_get_attachment_url( $attachment->ID );
		}

		if ( empty( $src ) ) {
			return;
		}

		add_filter( 'wp_video_shortcode', array( $this, 'inject_video_max_width_style' ) );

		echo wp_video_shortcode(
			array_merge(
				$instance,
				compact( 'src' )
			),
			$instance['content']
		);

		remove_filter( 'wp_video_shortcode', array( $this, 'inject_video_max_width_style' ) );
	}

	/**
	 * Inject max-width and remove height for videos too constrained to fit inside sidebars on frontend.
	 *
	 * @since 4.8.0
	 *
	 * @param string $html Video shortcode HTML output.
	 * @return string HTML Output.
	 */
	public function inject_video_max_width_style( $html ) {
		$html = preg_replace( '/\sheight="\d+"/', '', $html );
		$html = preg_replace( '/\swidth="\d+"/', '', $html );
		$html = preg_replace( '/(?<=width:)\s*\d+px(?=;?)/', '100%', $html );
		return $html;
	}

	/**
	 * Enqueue preview scripts.
	 *
	 * These scripts normally are enqueued just-in-time when a video shortcode is used.
	 * In the customizer, however, widgets can be dynamically added and rendered via
	 * selective refresh, and so it is important to unconditionally enqueue them in
	 * case a widget does get added.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_preview_scripts() {
		/** This filter is documented in wp-includes/media.php */
		if ( 'mediaelement' === apply_filters( 'wp_video_shortcode_library', 'mediaelement' ) ) {
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'mediaelement-vimeo' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
	}

	/**
	 * Loads the required scripts and styles for the widget control.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_admin_scripts() {
		parent::enqueue_admin_scripts();

		$handle = 'media-video-widget';
		wp_enqueue_script( $handle );

		$exported_schema = array();
		foreach ( $this->get_instance_schema() as $field => $field_schema ) {
			$exported_schema[ $field ] = wp_array_slice_assoc( $field_schema, array( 'type', 'default', 'enum', 'minimum', 'format', 'media_prop', 'should_preview_update' ) );
		}
		wp_add_inline_script(
			$handle,
			sprintf(
				'wp.mediaWidgets.modelConstructors[ %s ].prototype.schema = %s;',
				wp_json_encode( $this->id_base ),
				wp_json_encode( $exported_schema )
			)
		);

		wp_add_inline_script(
			$handle,
			sprintf(
				'
					wp.mediaWidgets.controlConstructors[ %1$s ].prototype.mime_type = %2$s;
					wp.mediaWidgets.controlConstructors[ %1$s ].prototype.l10n = _.extend( {}, wp.mediaWidgets.controlConstructors[ %1$s ].prototype.l10n, %3$s );
				',
				wp_json_encode( $this->id_base ),
				wp_json_encode( $this->widget_options['mime_type'] ),
				wp_json_encode( $this->l10n )
			)
		);
	}

	/**
	 * Render form template scripts.
	 *
	 * @since 4.8.0
	 */
	public function render_control_template_scripts() {
		parent::render_control_template_scripts()
		?>
		<script type="text/html" id="tmpl-wp-media-widget-video-preview">
			<# if ( data.error && 'missing_attachment' === data.error ) { #>
				<div class="notice notice-error notice-alt notice-missing-attachment">
					<p><?php echo $this->l10n['missing_attachment']; ?></p>
				</div>
			<# } else if ( data.error && 'unsupported_file_type' === data.error ) { #>
				<div class="notice notice-error notice-alt notice-missing-attachment">
					<p><?php echo $this->l10n['unsupported_file_type']; ?></p>
				</div>
			<# } else if ( data.error ) { #>
				<div class="notice notice-error notice-alt">
					<p><?php _e( 'Unable to preview media due to an unknown error.' ); ?></p>
				</div>
			<# } else if ( data.is_hosted_embed && data.model.poster ) { #>
				<a href="{{ data.model.src }}" target="_blank" class="media-widget-video-link">
					<img src="{{ data.model.poster }}" />
				</a>
			<# } else if ( data.is_hosted_embed ) { #>
				<a href="{{ data.model.src }}" target="_blank" class="media-widget-video-link no-poster">
					<span class="dashicons dashicons-format-video"></span>
				</a>
			<# } else if ( data.model.src ) { #>
				<?php wp_underscore_video_template() ?>
			<# } #>
		</script>
		<?php
	}
}
