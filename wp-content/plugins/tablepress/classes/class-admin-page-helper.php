<?php
/**
 * Admin Page Helper Class for TablePress with functions needed in the admin area
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Admin Page class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Admin_Page {

	/**
	 * Enqueue a CSS file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name         Name of the CSS file, without extension.
	 * @param array  $dependencies Optional. List of names of CSS stylesheets that this stylesheet depends on, and which need to be included before this one.
	 */
	public function enqueue_style( $name, array $dependencies = array() ) {
		$suffix = SCRIPT_DEBUG ? '' : '.min';
		$css_file = "admin/css/{$name}{$suffix}.css";
		$css_url = plugins_url( $css_file, TABLEPRESS__FILE__ );
		wp_enqueue_style( "tablepress-{$name}", $css_url, $dependencies, TablePress::version );
	}

	/**
	 * Enqueue a JavaScript file, possibly with dependencies and extra information.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name            Name of the JS file, without extension.
	 * @param array  $dependencies    Optional. List of names of JS scripts that this script depends on, and which need to be included before this one.
	 * @param array  $localize_script Optional. An array with strings that gets transformed into a JS object and is added to the page before the script is included.
	 * @param bool   $force_minified  Optional. Always load the minified version, regardless of SCRIPT_DEBUG constant value.
	 */
	public function enqueue_script( $name, array $dependencies = array(), array $localize_script = array(), $force_minified = false ) {
		$suffix = ( ! $force_minified && SCRIPT_DEBUG ) ? '' : '.min';
		$js_file = "admin/js/{$name}{$suffix}.js";
		$js_url = plugins_url( $js_file, TABLEPRESS__FILE__ );
		wp_enqueue_script( "tablepress-{$name}", $js_url, $dependencies, TablePress::version, true );
		if ( ! empty( $localize_script ) ) {
			foreach ( $localize_script as $var_name => $var_data ) {
				wp_localize_script( "tablepress-{$name}", "tablepress_{$var_name}", $var_data );
			}
		}
	}

	/**
	 * Register a filter hook on the admin footer.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_footer_text() {
		// Show admin footer message (only on TablePress admin screens).
		add_filter( 'admin_footer_text', array( $this, '_admin_footer_text' ) );
	}

	/**
	 * Add a TablePress "Thank You" message to the admin footer content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Current admin footer content.
	 * @return string New admin footer content.
	 */
	public function _admin_footer_text( $content ) {
		$content .= ' &bull; ' . sprintf( __( 'Thank you for using <a href="%s">TablePress</a>.', 'tablepress' ), 'https://tablepress.org/' );
		$content .= ' ' . sprintf( __( 'Support the plugin with your <a href="%s">donation</a>!', 'tablepress' ), 'https://tablepress.org/donate/' );
		return $content;
	}

	/**
	 * Print the JavaScript code for a WP feature pointer.
	 *
	 * @since 1.0.0
	 *
	 * @param string $pointer_id The pointer ID.
	 * @param string $selector   The HTML elements, on which the pointer should be attached.
	 * @param array  $args       Arguments to be passed to the pointer JS (see wp-pointer.js).
	 */
	public function print_wp_pointer_js( $pointer_id, $selector, array $args ) {
		if ( empty( $pointer_id ) || empty( $selector ) || empty( $args ) || empty( $args['content'] ) ) {
			return;
		}
		?>
		<script type="text/javascript">
		( function( $ ) {
			var options = <?php echo wp_json_encode( $args ); ?>, setup;

			if ( ! options ) {
				return;
			}

			options = $.extend( options, {
				close: function() {
					$.post( ajaxurl, {
						pointer: '<?php echo $pointer_id; ?>',
						action: 'dismiss-wp-pointer'
					} );
				}
			} );

			setup = function() {
				$( '<?php echo $selector; ?>' ).pointer( options ).pointer( 'open' );
			};

			if ( options.position && options.position.defer_loading ) {
				$( window ).bind( 'load.wp-pointers', setup );
			} else {
				$( document ).ready( setup );
			}
		} )( jQuery );
		</script>
		<?php
	}

} // class TablePress_Admin_Page
