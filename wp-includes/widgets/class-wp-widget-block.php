<?php
/**
 * Widget API: WP_Widget_Block class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 5.8.0
 */

/**
 * Core class used to implement a Block widget.
 *
 * @since 5.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_Block extends WP_Widget {

	/**
	 * Default instance.
	 *
	 * @since 5.8.0
	 * @var array
	 */
	protected $default_instance = array(
		'content' => '',
	);

	/**
	 * Sets up a new Block widget instance.
	 *
	 * @since 5.8.0
	 */
	public function __construct() {
		$widget_ops  = array(
			'classname'                   => 'widget_block',
			'description'                 => __( 'A widget containing a block.' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'block', __( 'Block' ), $widget_ops, $control_ops );

		add_filter( 'is_wide_widget_in_customizer', array( $this, 'set_is_wide_widget_in_customizer' ), 10, 2 );
	}

	/**
	 * Outputs the content for the current Block widget instance.
	 *
	 * @since 5.8.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Block widget instance.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->default_instance );

		echo str_replace(
			'widget_block',
			$this->get_dynamic_classname( $instance['content'] ),
			$args['before_widget']
		);

		/**
		 * Filters the content of the Block widget before output.
		 *
		 * @since 5.8.0
		 *
		 * @param string          $content  The widget content.
		 * @param array           $instance Array of settings for the current widget.
		 * @param WP_Widget_Block $widget   Current Block widget instance.
		 */
		echo apply_filters(
			'widget_block_content',
			$instance['content'],
			$instance,
			$this
		);

		echo $args['after_widget'];
	}

	/**
	 * Calculates the classname to use in the block widget's container HTML.
	 *
	 * Usually this is set to `$this->widget_options['classname']` by
	 * dynamic_sidebar(). In this case, however, we want to set the classname
	 * dynamically depending on the block contained by this block widget.
	 *
	 * If a block widget contains a block that has an equivalent legacy widget,
	 * we display that legacy widget's class name. This helps with theme
	 * backwards compatibility.
	 *
	 * @since 5.8.0
	 *
	 * @param string $content The HTML content of the current block widget.
	 * @return string The classname to use in the block widget's container HTML.
	 */
	private function get_dynamic_classname( $content ) {
		$blocks = parse_blocks( $content );

		$block_name = isset( $blocks[0] ) ? $blocks[0]['blockName'] : null;

		switch ( $block_name ) {
			case 'core/paragraph':
				$classname = 'widget_block widget_text';
				break;
			case 'core/calendar':
				$classname = 'widget_block widget_calendar';
				break;
			case 'core/search':
				$classname = 'widget_block widget_search';
				break;
			case 'core/html':
				$classname = 'widget_block widget_custom_html';
				break;
			case 'core/archives':
				$classname = 'widget_block widget_archive';
				break;
			case 'core/latest-posts':
				$classname = 'widget_block widget_recent_entries';
				break;
			case 'core/latest-comments':
				$classname = 'widget_block widget_recent_comments';
				break;
			case 'core/tag-cloud':
				$classname = 'widget_block widget_tag_cloud';
				break;
			case 'core/categories':
				$classname = 'widget_block widget_categories';
				break;
			case 'core/audio':
				$classname = 'widget_block widget_media_audio';
				break;
			case 'core/video':
				$classname = 'widget_block widget_media_video';
				break;
			case 'core/image':
				$classname = 'widget_block widget_media_image';
				break;
			case 'core/gallery':
				$classname = 'widget_block widget_media_gallery';
				break;
			case 'core/rss':
				$classname = 'widget_block widget_rss';
				break;
			default:
				$classname = 'widget_block';
		}

		/**
		 * The classname used in the block widget's container HTML.
		 *
		 * This can be set according to the name of the block contained by the block widget.
		 *
		 * @since 5.8.0
		 *
		 * @param string $classname  The classname to be used in the block widget's container HTML,
		 *                           e.g. 'widget_block widget_text'.
		 * @param string $block_name The name of the block contained by the block widget,
		 *                           e.g. 'core/paragraph'.
		 */
		return apply_filters( 'widget_block_dynamic_classname', $classname, $block_name );
	}

	/**
	 * Handles updating settings for the current Block widget instance.
	 *
	 * @since 5.8.0

	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array_merge( $this->default_instance, $old_instance );

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['content'] = $new_instance['content'];
		} else {
			$instance['content'] = wp_kses_post( $new_instance['content'] );
		}

		return $instance;
	}

	/**
	 * Outputs the Block widget settings form.
	 *
	 * @since 5.8.0
	 *
	 * @see WP_Widget_Custom_HTML::render_control_template_scripts()
	 *
	 * @param array $instance Current instance.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->default_instance );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'content' ); ?>">
				<?php
				/* translators: HTML code of the block, not an option that blocks HTML. */
				_e( 'Block HTML:' );
				?>
			</label>
			<textarea id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" rows="6" cols="50" class="widefat code"><?php echo esc_textarea( $instance['content'] ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Makes sure no block widget is considered to be wide.
	 *
	 * @since 5.8.0
	 *
	 * @param bool   $is_wide   Whether the widget is considered wide.
	 * @param string $widget_id Widget ID.
	 * @return bool Updated `is_wide` value.
	 */
	public function set_is_wide_widget_in_customizer( $is_wide, $widget_id ) {
		if ( str_starts_with( $widget_id, 'block-' ) ) {
			return false;
		}

		return $is_wide;
	}
}
