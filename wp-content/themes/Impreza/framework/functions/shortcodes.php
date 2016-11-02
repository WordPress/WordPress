<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

class US_Shortcodes {

	/**
	 * @var {String} Template directory
	 */
	protected $_template_directory;

	protected $config;

	/**
	 * @var array Current shortcode config
	 */
	protected $_settings;

	/**
	 * Retrieve one setting (used for compatibility with VC)
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function settings( $key ) {
		return isset( $this->_settings[ $key ] ) ? $this->_settings[ $key ] : NULL;
	}

	/**
	 * @var US_Shortcodes
	 */
	protected static $instance;

	/**
	 * Singleton pattern: US_Shortcodes::instance()->us_blog($atts, $content)
	 *
	 * @return US_Shortcodes
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	protected function __construct() {
		global $us_template_directory, $us_stylesheet_directory;
		$this->config = us_config( 'shortcodes' );

		// TODO Figure out all the mess about paragraphs
		add_filter( 'the_content', array( $this, 'paragraph_fix' ) );
		add_filter( 'the_content', array( $this, 'a_to_img_magnific_pupup' ) );

		// Make sure that priority makes the class init after Visual Composer
		add_action( 'init', array( $this, 'init' ), 20 );

		$this->_template_directory = $us_template_directory;
		$this->_stylesheet_directory = $us_stylesheet_directory;
	}

	/**
	 * @var bool Is the shortcode inited?
	 */
	protected $inited = FALSE;

	public function init() {
		// Adding new shortcodes
		foreach ( $this->config as $shortcode => $shortcode_params ) {
			if ( isset( $shortcode_params['supported'] ) AND ! $shortcode_params['supported'] ) {
				// If the shortcode is disabled, don't handle it at all
				if ( ! us_get_option( 'enable_unsupported_vc_shortcodes', FALSE ) AND shortcode_exists( $shortcode ) ) {
					remove_shortcode( $shortcode );
				}
				continue;
			}
			if ( isset( $shortcode_params['overload'] ) AND ! $shortcode_params['overload'] ) {
				continue;
			}
			// Overloading the previous declaration
			if ( shortcode_exists( $shortcode ) ) {
				remove_shortcode( $shortcode );
			}
			add_shortcode( $shortcode, array( $this, $shortcode ) );
		}

		$this->inited = TRUE;
	}

	/**
	 * Handling shortcodes
	 *
	 * @param string $shortcode Shortcode name
	 * @param array $args
	 *
	 * @return string Generated shortcode output
	 *
	 * TODO Handle $this-> calls from shortcodes properly
	 */
	public function __call( $shortcode, $args ) {
		$_output = '';
		if ( ! isset( $this->config[ $shortcode ] ) ) {
			return $_output;
		}

		// Even if the shortcode will be overloaded by an alias, it's still possible to obtain the original name
		$shortcode_base = $shortcode;
		$config = $this->config[ $shortcode ];
		if ( isset( $config['alias_of'] ) ) {
			$shortcode = $config['alias_of'];
			// Wrong alias, but still keeping the inner content
			if ( ! isset( $this->config[ $shortcode ] ) ) {
				return isset( $args[1] ) ? $args[1] : '';
			}
			if ( isset( $config['compatibility_func'] ) AND is_callable( $config['compatibility_func'] ) ) {
				$compatibility_func = $config['compatibility_func'];
				$args = $compatibility_func( $args );
			}
		}
		unset( $config );

		// Preparing params for shortcodes (can be used inside of the input)
		$atts = isset( $args[0] ) ? $args[0] : array();
		$content = isset( $args[1] ) ? $args[1] : '';

		// Preserving VC before hook
		if ( substr( $shortcode_base, 0, 3 ) == 'vc_' AND defined( 'VC_SHORTCODE_BEFORE_CUSTOMIZE_PREFIX' ) ) {
			$custom_output_before = VC_SHORTCODE_BEFORE_CUSTOMIZE_PREFIX . $shortcode_base;
			if ( function_exists( $custom_output_before ) ) {
				$_output .= $custom_output_before( $atts, $content );
			}
			unset( $custom_output_before );
		}

		// The shortcode itself
		$_filename = $this->_template_directory . '/framework/shortcodes/' . $shortcode . '.php';
		ob_start();
		require $_filename;
		$_output .= ob_get_clean();

		// Preserving VC after hooks
		if ( substr( $shortcode_base, 0, 3 ) == 'vc_' ) {
			if ( defined( 'VC_SHORTCODE_AFTER_CUSTOMIZE_PREFIX' ) ) {
				$custom_output_after = VC_SHORTCODE_AFTER_CUSTOMIZE_PREFIX . $shortcode_base;
				if ( function_exists( $custom_output_after ) ) {
					$_output .= $custom_output_after( $atts, $content );
				}
			}
			$this->_settings = array(
				'base' => $shortcode_base,
			);
			$_output = apply_filters( 'vc_shortcode_output', $_output, $this, isset( $args[0] ) ? $args[0] : array() );
		}

		return $_output;
	}

	public function paragraph_fix( $content ) {
		$array = array(
			'<p>[' => '[',
			']</p>' => ']',
			']<br />' => ']',
			']<br>' => ']',
		);

		$content = strtr( $content, $array );

		return $content;
	}

	public function a_to_img_magnific_pupup( $content ) {
		$pattern = "/<a(.*?)href=('|\")([^>]*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
		$replacement = '<a$1ref="magnificPopup" href=$2$3.$4$5$6>';
		$content = preg_replace( $pattern, $replacement, $content );

		return $content;
	}

	/**
	 * Remove some of the shortcodes handlers to use native VC shortcodes instead for front-end compatibility
	 */
	public function vc_front_end_compatibility() {
		if ( WP_DEBUG AND $this->inited ) {
			wp_die( 'Shortcodes VC front end compatibility should be provided before the shortcodes init' );
		}
		unset( $this->config['vc_tta_tabs'], $this->config['vc_tta_accordion'], $this->config['vc_tta_tour'], $this->config['vc_tta_section'] );
		unset( $this->config['vc_row'], $this->config['vc_row_inner'] );
		unset( $this->config['vc_column_text'] );
	}

}

global $us_shortcodes;
$us_shortcodes = US_Shortcodes::instance();

// Add custom options to WP Gallery window
add_action( 'print_media_templates', 'us_media_templates' );
function us_media_templates() {
	?>
	<script type="text/html" id="tmpl-my-custom-gallery-setting">
		<label class="setting">
			<span><?php _e( 'Add indents between items' , 'us' ) ?></span>
			<input type="checkbox" data-setting="indents">
		</label>
		<label class="setting">
			<span><?php _e( 'Enable Masonry layout mode' , 'us' ) ?></span>
			<input type="checkbox" data-setting="masonry">
		</label>
		<label class="setting">
			<span><?php _e( 'Show items titles and description' , 'us' ) ?></span>
			<input type="checkbox" data-setting="meta">
		</label>
	</script>
	<script>
		jQuery(document).ready(function(){

			// add your shortcode attribute and its default value to the
			// gallery settings list; $.extend should work as well...
			_.extend(wp.media.gallery.defaults, {
				type: 'default_val'
			});

			// merge default gallery settings template with yours
			wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
				template: function(view){
					return wp.media.template('gallery-settings')(view)
						+ wp.media.template('my-custom-gallery-setting')(view);
				}
			});

		});
	</script>
	<?php
}
