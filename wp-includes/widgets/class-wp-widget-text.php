<?php
/**
 * Widget API: WP_Widget_Text class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement a Text widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_Text extends WP_Widget {

	/**
	 * Whether or not the widget has been registered yet.
	 *
	 * @since 4.8.1
	 * @var bool
	 */
	protected $registered = false;

	/**
	 * Sets up a new Text widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_text',
			'description' => __( 'Arbitrary text.' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array(
			'width' => 400,
			'height' => 350,
		);
		parent::__construct( 'text', __( 'Text' ), $widget_ops, $control_ops );
	}

	/**
	 * Add hooks for enqueueing assets when registering all widget instances of this widget class.
	 *
	 * @param integer $number Optional. The unique order number of this widget instance
	 *                        compared to other instances of the same class. Default -1.
	 */
	public function _register_one( $number = -1 ) {
		parent::_register_one( $number );
		if ( $this->registered ) {
			return;
		}
		$this->registered = true;

		wp_add_inline_script( 'text-widgets', sprintf( 'wp.textWidgets.idBases.push( %s );', wp_json_encode( $this->id_base ) ) );

		// Note that the widgets component in the customizer will also do the 'admin_print_scripts-widgets.php' action in WP_Customize_Widgets::print_scripts().
		add_action( 'admin_print_scripts-widgets.php', array( $this, 'enqueue_admin_scripts' ) );

		// Note that the widgets component in the customizer will also do the 'admin_footer-widgets.php' action in WP_Customize_Widgets::print_footer_scripts().
		add_action( 'admin_footer-widgets.php', array( 'WP_Widget_Text', 'render_control_template_scripts' ) );
	}

	/**
	 * Determines whether a given instance is legacy and should bypass using TinyMCE.
	 *
	 * @since 4.8.1
	 *
	 * @param array $instance {
	 *     Instance data.
	 *
	 *     @type string      $text   Content.
	 *     @type bool|string $filter Whether autop or content filters should apply.
	 *     @type bool        $legacy Whether widget is in legacy mode.
	 * }
	 * @return bool Whether Text widget instance contains legacy data.
	 */
	public function is_legacy_instance( $instance ) {

		// Legacy mode when not in visual mode.
		if ( isset( $instance['visual'] ) ) {
			return ! $instance['visual'];
		}

		// Or, the widget has been added/updated in 4.8.0 then filter prop is 'content' and it is no longer legacy.
		if ( isset( $instance['filter'] ) && 'content' === $instance['filter'] ) {
			return false;
		}

		// If the text is empty, then nothing is preventing migration to TinyMCE.
		if ( empty( $instance['text'] ) ) {
			return false;
		}

		$wpautop = ! empty( $instance['filter'] );
		$has_line_breaks = ( false !== strpos( trim( $instance['text'] ), "\n" ) );

		// If auto-paragraphs are not enabled and there are line breaks, then ensure legacy mode.
		if ( ! $wpautop && $has_line_breaks ) {
			return true;
		}

		// If an HTML comment is present, assume legacy mode.
		if ( false !== strpos( $instance['text'], '<!--' ) ) {
			return true;
		}

		// In the rare case that DOMDocument is not available we cannot reliably sniff content and so we assume legacy.
		if ( ! class_exists( 'DOMDocument' ) ) {
			// @codeCoverageIgnoreStart
			return true;
			// @codeCoverageIgnoreEnd
		}

		$doc = new DOMDocument();
		@$doc->loadHTML( sprintf(
			'<!DOCTYPE html><html><head><meta charset="%s"></head><body>%s</body></html>',
			esc_attr( get_bloginfo( 'charset' ) ),
			$instance['text']
		) );
		$body = $doc->getElementsByTagName( 'body' )->item( 0 );

		// See $allowedposttags.
		$safe_elements_attributes = array(
			'strong' => array(),
			'em' => array(),
			'b' => array(),
			'i' => array(),
			'u' => array(),
			's' => array(),
			'ul' => array(),
			'ol' => array(),
			'li' => array(),
			'hr' => array(),
			'abbr' => array(),
			'acronym' => array(),
			'code' => array(),
			'dfn' => array(),
			'a' => array(
				'href' => true,
			),
			'img' => array(
				'src' => true,
				'alt' => true,
			),
		);
		$safe_empty_elements = array( 'img', 'hr', 'iframe' );

		foreach ( $body->getElementsByTagName( '*' ) as $element ) {
			/** @var DOMElement $element */
			$tag_name = strtolower( $element->nodeName );

			// If the element is not safe, then the instance is legacy.
			if ( ! isset( $safe_elements_attributes[ $tag_name ] ) ) {
				return true;
			}

			// If the element is not safely empty and it has empty contents, then legacy mode.
			if ( ! in_array( $tag_name, $safe_empty_elements, true ) && '' === trim( $element->textContent ) ) {
				return true;
			}

			// If an attribute is not recognized as safe, then the instance is legacy.
			foreach ( $element->attributes as $attribute ) {
				/** @var DOMAttr $attribute */
				$attribute_name = strtolower( $attribute->nodeName );

				if ( ! isset( $safe_elements_attributes[ $tag_name ][ $attribute_name ] ) ) {
					return true;
				}
			}
		}

		// Otherwise, the text contains no elements/attributes that TinyMCE could drop, and therefore the widget does not need legacy mode.
		return false;
	}

	/**
	 * Outputs the content for the current Text widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @global WP_Post $post
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Text widget instance.
	 */
	public function widget( $args, $instance ) {
		global $post;

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$text = ! empty( $instance['text'] ) ? $instance['text'] : '';
		$is_visual_text_widget = ( ! empty( $instance['visual'] ) && ! empty( $instance['filter'] ) );

		// In 4.8.0 only, visual Text widgets get filter=content, without visual prop; upgrade instance props just-in-time.
		if ( ! $is_visual_text_widget ) {
			$is_visual_text_widget = ( isset( $instance['filter'] ) && 'content' === $instance['filter'] );
		}
		if ( $is_visual_text_widget ) {
			$instance['filter'] = true;
			$instance['visual'] = true;
		}

		/*
		 * Suspend legacy plugin-supplied do_shortcode() for 'widget_text' filter for the visual Text widget to prevent
		 * shortcodes being processed twice. Now do_shortcode() is added to the 'widget_text_content' filter in core itself
		 * and it applies after wpautop() to prevent corrupting HTML output added by the shortcode. When do_shortcode() is
		 * added to 'widget_text_content' then do_shortcode() will be manually called when in legacy mode as well.
		 */
		$widget_text_do_shortcode_priority = has_filter( 'widget_text', 'do_shortcode' );
		$should_suspend_legacy_shortcode_support = ( $is_visual_text_widget && false !== $widget_text_do_shortcode_priority );
		if ( $should_suspend_legacy_shortcode_support ) {
			remove_filter( 'widget_text', 'do_shortcode', $widget_text_do_shortcode_priority );
		}

		// Nullify the $post global during widget rendering to prevent shortcodes from running with the unexpected context.
		$suspended_post = null;
		if ( isset( $post ) ) {
			$suspended_post = $post;
			$post = null;
		}

		/**
		 * Filters the content of the Text widget.
		 *
		 * @since 2.3.0
		 * @since 4.4.0 Added the `$this` parameter.
		 * @since 4.8.1 The `$this` param may now be a `WP_Widget_Custom_HTML` object in addition to a `WP_Widget_Text` object.
		 *
		 * @param string                               $text     The widget content.
		 * @param array                                $instance Array of settings for the current widget.
		 * @param WP_Widget_Text|WP_Widget_Custom_HTML $this     Current Text widget instance.
		 */
		$text = apply_filters( 'widget_text', $text, $instance, $this );

		if ( $is_visual_text_widget ) {

			/**
			 * Filters the content of the Text widget to apply changes expected from the visual (TinyMCE) editor.
			 *
			 * By default a subset of the_content filters are applied, including wpautop and wptexturize.
			 *
			 * @since 4.8.0
			 *
			 * @param string         $text     The widget content.
			 * @param array          $instance Array of settings for the current widget.
			 * @param WP_Widget_Text $this     Current Text widget instance.
			 */
			$text = apply_filters( 'widget_text_content', $text, $instance, $this );
		} else {
			// Now in legacy mode, add paragraphs and line breaks when checkbox is checked.
			if ( ! empty( $instance['filter'] ) ) {
				$text = wpautop( $text );
			}

			/*
			 * Manually do shortcodes on the content when the core-added filter is present. It is added by default
			 * in core by adding do_shortcode() to the 'widget_text_content' filter to apply after wpautop().
			 * Since the legacy Text widget runs wpautop() after 'widget_text' filters are applied, the widget in
			 * legacy mode here manually applies do_shortcode() on the content unless the default
			 * core filter for 'widget_text_content' has been removed, or if do_shortcode() has already
			 * been applied via a plugin adding do_shortcode() to 'widget_text' filters.
			 */
			if ( has_filter( 'widget_text_content', 'do_shortcode' ) && ! $widget_text_do_shortcode_priority ) {
				if ( ! empty( $instance['filter'] ) ) {
					$text = shortcode_unautop( $text );
				}
				$text = do_shortcode( $text );
			}
		}

		// Restore post global.
		if ( isset( $suspended_post ) ) {
			$post = $suspended_post;
		}

		// Undo suspension of legacy plugin-supplied shortcode handling.
		if ( $should_suspend_legacy_shortcode_support ) {
			add_filter( 'widget_text', 'do_shortcode', $widget_text_do_shortcode_priority );
		}

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$text = preg_replace_callback( '#<[^>]*>#', array( $this, 'inject_video_max_width_style' ), $text );

		?>
			<div class="textwidget"><?php echo $text; ?></div>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Inject max-width and remove height for videos too constrained to fit inside sidebars on frontend.
	 *
	 * @since 4.9.0
	 *
	 * @see WP_Widget_Media_Video::inject_video_max_width_style()
	 * @param array $matches Pattern matches from preg_replace_callback.
	 * @return string HTML Output.
	 */
	public function inject_video_max_width_style( $matches ) {
		$html = $matches[0];
		$html = preg_replace( '/\sheight="\d+"/', '', $html );
		$html = preg_replace( '/\swidth="\d+"/', '', $html );
		$html = preg_replace( '/(?<=width:)\s*\d+px(?=;?)/', '100%', $html );
		return $html;
	}

	/**
	 * Handles updating settings for the current Text widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance = wp_parse_args( $new_instance, array(
			'title' => '',
			'text' => '',
			'filter' => false, // For back-compat.
			'visual' => null, // Must be explicitly defined.
		) );

		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = wp_kses_post( $new_instance['text'] );
		}

		$instance['filter'] = ! empty( $new_instance['filter'] );

		// Upgrade 4.8.0 format.
		if ( isset( $old_instance['filter'] ) && 'content' === $old_instance['filter'] ) {
			$instance['visual'] = true;
		}
		if ( 'content' === $new_instance['filter'] ) {
			$instance['visual'] = true;
		}

		if ( isset( $new_instance['visual'] ) ) {
			$instance['visual'] = ! empty( $new_instance['visual'] );
		}

		// Filter is always true in visual mode.
		if ( ! empty( $instance['visual'] ) ) {
			$instance['filter'] = true;
		}

		return $instance;
	}

	/**
	 * Loads the required scripts and styles for the widget control.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_editor();
		wp_enqueue_script( 'text-widgets' );
		wp_add_inline_script( 'text-widgets', 'wp.textWidgets.init();', 'after' );
	}

	/**
	 * Outputs the Text widget settings form.
	 *
	 * @since 2.8.0
	 * @since 4.8.0 Form only contains hidden inputs which are synced with JS template.
	 * @since 4.8.1 Restored original form to be displayed when in legacy mode.
	 * @see WP_Widget_Visual_Text::render_control_template_scripts()
	 * @see _WP_Editors::editor()
	 *
	 * @param array $instance Current settings.
	 * @return void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title' => '',
				'text' => '',
			)
		);
		?>
		<?php if ( ! $this->is_legacy_instance( $instance ) ) : ?>
			<?php

			if ( user_can_richedit() ) {
				add_filter( 'the_editor_content', 'format_for_editor', 10, 2 );
				$default_editor = 'tinymce';
			} else {
				$default_editor = 'html';
			}

			/** This filter is documented in wp-includes/class-wp-editor.php */
			$text = apply_filters( 'the_editor_content', $instance['text'], $default_editor );

			// Reset filter addition.
			if ( user_can_richedit() ) {
				remove_filter( 'the_editor_content', 'format_for_editor' );
			}

			// Prevent premature closing of textarea in case format_for_editor() didn't apply or the_editor_content filter did a wrong thing.
			$escaped_text = preg_replace( '#</textarea#i', '&lt;/textarea', $text );

			?>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="title sync-input" type="hidden" value="<?php echo esc_attr( $instance['title'] ); ?>">
			<textarea id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" class="text sync-input" hidden><?php echo $escaped_text; ?></textarea>
			<input id="<?php echo $this->get_field_id( 'filter' ); ?>" name="<?php echo $this->get_field_name( 'filter' ); ?>" class="filter sync-input" type="hidden" value="on">
			<input id="<?php echo $this->get_field_id( 'visual' ); ?>" name="<?php echo $this->get_field_name( 'visual' ); ?>" class="visual sync-input" type="hidden" value="on">
		<?php else : ?>
			<input id="<?php echo $this->get_field_id( 'visual' ); ?>" name="<?php echo $this->get_field_name( 'visual' ); ?>" class="visual" type="hidden" value="">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>"/>
			</p>
			<div class="notice inline notice-info notice-alt">
				<?php if ( ! isset( $instance['visual'] ) ) : ?>
					<p><?php _e( 'This widget may contain code that may work better in the &#8220;Custom HTML&#8221; widget. How about trying that widget instead?' ); ?></p>
				<?php else : ?>
					<p><?php _e( 'This widget may have contained code that may work better in the &#8220;Custom HTML&#8221; widget. If you haven&#8217;t yet, how about trying that widget instead?' ); ?></p>
				<?php endif; ?>
			</div>
			<p>
				<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Content:' ); ?></label>
				<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo esc_textarea( $instance['text'] ); ?></textarea>
			</p>
			<p>
				<input id="<?php echo $this->get_field_id( 'filter' ); ?>" name="<?php echo $this->get_field_name( 'filter' ); ?>" type="checkbox"<?php checked( ! empty( $instance['filter'] ) ); ?> />&nbsp;<label for="<?php echo $this->get_field_id( 'filter' ); ?>"><?php _e( 'Automatically add paragraphs' ); ?></label>
			</p>
		<?php
		endif;
	}

	/**
	 * Render form template scripts.
	 *
	 * @since 4.8.0
	 * @since 4.9.0 The method is now static.
	 */
	public static function render_control_template_scripts() {
		$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		?>
		<script type="text/html" id="tmpl-widget-text-control-fields">
			<# var elementIdPrefix = 'el' + String( Math.random() ).replace( /\D/g, '' ) + '_' #>
			<p>
				<label for="{{ elementIdPrefix }}title"><?php esc_html_e( 'Title:' ); ?></label>
				<input id="{{ elementIdPrefix }}title" type="text" class="widefat title">
			</p>

			<?php if ( ! in_array( 'text_widget_custom_html', $dismissed_pointers, true ) ) : ?>
				<div hidden class="wp-pointer custom-html-widget-pointer wp-pointer-top">
					<div class="wp-pointer-content">
						<h3><?php _e( 'New Custom HTML Widget' ); ?></h3>
						<?php if ( is_customize_preview() ) : ?>
							<p><?php _e( 'Did you know there is a &#8220;Custom HTML&#8221; widget now? You can find it by pressing the &#8220;<a class="add-widget" href="#">Add a Widget</a>&#8221; button and searching for &#8220;HTML&#8221;. Check it out to add some custom code to your site!' ); ?></p>
						<?php else : ?>
							<p><?php _e( 'Did you know there is a &#8220;Custom HTML&#8221; widget now? You can find it by scanning the list of available widgets on this screen. Check it out to add some custom code to your site!' ); ?></p>
						<?php endif; ?>
						<div class="wp-pointer-buttons">
							<a class="close" href="#"><?php _e( 'Dismiss' ); ?></a>
						</div>
					</div>
					<div class="wp-pointer-arrow">
						<div class="wp-pointer-arrow-inner"></div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! in_array( 'text_widget_paste_html', $dismissed_pointers, true ) ) : ?>
				<div hidden class="wp-pointer paste-html-pointer wp-pointer-top">
					<div class="wp-pointer-content">
						<h3><?php _e( 'Did you just paste HTML?' ); ?></h3>
						<p><?php _e( 'Hey there, looks like you just pasted HTML into the &#8220;Visual&#8221; tab of the Text widget. You may want to paste your code into the &#8220;Text&#8221; tab instead. Alternately, try out the new &#8220;Custom HTML&#8221; widget!' ); ?></p>
						<div class="wp-pointer-buttons">
							<a class="close" href="#"><?php _e( 'Dismiss' ); ?></a>
						</div>
					</div>
					<div class="wp-pointer-arrow">
						<div class="wp-pointer-arrow-inner"></div>
					</div>
				</div>
			<?php endif; ?>

			<p>
				<label for="{{ elementIdPrefix }}text" class="screen-reader-text"><?php esc_html_e( 'Content:' ); ?></label>
				<textarea id="{{ elementIdPrefix }}text" class="widefat text wp-editor-area" style="height: 200px" rows="16" cols="20"></textarea>
			</p>
		</script>
		<?php
	}
}
