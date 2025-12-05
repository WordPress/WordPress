<?php
namespace Elementor;

use Elementor\Core\Files\Fonts\Google_Font;
use Elementor\Core\Utils\Collection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor utils.
 *
 * Elementor utils handler class is responsible for different utility methods
 * used by Elementor.
 *
 * @since 1.0.0
 */
class Utils {

	const DEPRECATION_RANGE = 0.4;

	const EDITOR_BREAK_LINES_OPTION_KEY = 'elementor_editor_break_lines';

	/**
	 * A list of safe tags for `validate_html_tag` method.
	 */
	const ALLOWED_HTML_WRAPPER_TAGS = [
		'a',
		'article',
		'aside',
		'button',
		'div',
		'footer',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'header',
		'main',
		'nav',
		'p',
		'section',
		'span',
	];

	const EXTENDED_ALLOWED_HTML_TAGS = [
		'iframe' => [
			'iframe' => [
				'allow' => true,
				'allowfullscreen' => true,
				'frameborder' => true,
				'height' => true,
				'loading' => true,
				'name' => true,
				'referrerpolicy' => true,
				'sandbox' => true,
				'src' => true,
				'width' => true,
			],
		],
		'svg' => [
			'svg' => [
				'aria-hidden' => true,
				'aria-labelledby' => true,
				'class' => true,
				'height' => true,
				'role' => true,
				'viewbox' => true,
				'width' => true,
				'xmlns' => true,
			],
			'g' => [
				'fill' => true,
			],
			'title' => [
				'title' => true,
			],
			'path' => [
				'd' => true,
				'fill' => true,
			],
		],
		'image' => [
			'img' => [
				'srcset' => true,
				'sizes' => true,
			],
		],
	];

	/**
	 * Variables for free to pro upsale modal promotions
	 */

	const ANIMATED_HEADLINE = 'animated_headline';

	const CTA = 'cta';

	const VIDEO_PLAYLIST = 'video_playlist';

	const TESTIMONIAL_WIDGET = 'testimonial_widget';

	const IMAGE_CAROUSEL = 'image_carousel';

	/**
	 * Whether WordPress CLI mode is enabled or not.
	 *
	 * @access public
	 * @static
	 *
	 * @return bool
	 */
	public static function is_wp_cli() {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Whether script debug is enabled or not.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return bool
	 */
	public static function is_script_debug() {
		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	}

	/**
	 * Whether Elementor debug is enabled or not.
	 *
	 * @access public
	 * @static
	 *
	 * @return bool
	 */
	public static function is_elementor_debug() {
		return defined( 'ELEMENTOR_DEBUG' ) && ELEMENTOR_DEBUG;
	}

	/**
	 * Whether Elementor test mode is enabled or not.
	 *
	 * @access public
	 * @static
	 *
	 * @return bool
	 */
	public static function is_elementor_tests() {
		return defined( 'ELEMENTOR_TESTS' ) && ELEMENTOR_TESTS;
	}

	/**
	 * Get pro link.
	 *
	 * Retrieve the link to Elementor Pro.
	 *
	 * @since 1.7.0
	 * @access public
	 * @static
	 *
	 * @param string $link URL to Elementor pro.
	 *
	 * @return string Elementor pro link.
	 */
	public static function get_pro_link( $link ) {
		static $theme_name = false;

		if ( ! $theme_name ) {
			$theme_obj = wp_get_theme();
			if ( $theme_obj->parent() ) {
				$theme_name = $theme_obj->parent()->get( 'Name' );
			} else {
				$theme_name = $theme_obj->get( 'Name' );
			}

			$theme_name = sanitize_key( $theme_name );
		}

		$link = add_query_arg( 'utm_term', $theme_name, $link );

		return $link;
	}

	/**
	 * Replace URLs.
	 *
	 * Replace old URLs to new URLs. This method also updates all the Elementor data.
	 *
	 * @since 2.1.0
	 * @static
	 * @access public
	 *
	 * @param string $from
	 * @param string $to
	 *
	 * @return string
	 * @throws \Exception If URLs are missing or invalid URLs provided.
	 */
	public static function replace_urls( $from, $to ) {
		$from = trim( $from );
		$to = trim( $to );

		if ( empty( $from ) ) {
			throw new \Exception( 'Couldn’t replace your address because the old URL was not provided. Try again by entering the old URL.' );
		}

		if ( empty( $to ) ) {
			throw new \Exception( 'Couldn’t replace your address because the new URL was not provided. Try again by entering the new URL.' );
		}

		if ( $from === $to ) {
			throw new \Exception( 'Couldn’t replace your address because both of the URLs provided are identical. Try again by entering different URLs.' );
		}

		$is_valid_urls = ( filter_var( $from, FILTER_VALIDATE_URL ) && filter_var( $to, FILTER_VALIDATE_URL ) );

		if ( ! $is_valid_urls ) {
			throw new \Exception( 'Couldn’t replace your address because at least one of the URLs provided are invalid. Try again by entering valid URLs.' );
		}

		global $wpdb;
		$escaped_from = str_replace( '/', '\\/', $from );
		$escaped_to = str_replace( '/', '\\/', $to );
		$meta_value_like = '[%'; // meta_value LIKE '[%' are json formatted

		$rows_affected = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->postmeta} " .
				'SET `meta_value` = REPLACE(`meta_value`, %s, %s) ' .
				"WHERE `meta_key` = '_elementor_data' AND `meta_value` LIKE %s;",
				$escaped_from,
				$escaped_to,
				$meta_value_like
			)
		);

		if ( false === $rows_affected ) {
			throw new \Exception( 'An error occurred while replacing URL\'s.' );
		}

		// Allow externals to replace-urls, when they have to.
		$rows_affected += (int) apply_filters( 'elementor/tools/replace-urls', 0, $from, $to );

		Plugin::$instance->files_manager->clear_cache();
		Google_Font::clear_cache();

		return sprintf(
			/* translators: %d: Number of rows. */
			_n( '%d database row affected.', '%d database rows affected.', $rows_affected, 'elementor' ),
			$rows_affected
		);
	}

	/**
	 * Is post supports Elementor.
	 *
	 * Whether the post supports editing with Elementor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param int $post_id Optional. Post ID. Default is `0`.
	 *
	 * @return string True if post supports editing with Elementor, false otherwise.
	 */
	public static function is_post_support( $post_id = 0 ) {
		$post_type = get_post_type( $post_id );

		$is_supported = self::is_post_type_support( $post_type );

		/**
		 * Is post type support.
		 *
		 * Filters whether the post type supports editing with Elementor.
		 *
		 * @since 1.0.0
		 * @deprecated 2.2.0 Use `elementor/utils/is_post_support` hook Instead.
		 *
		 * @param bool $is_supported Whether the post type supports editing with Elementor.
		 * @param int $post_id Post ID.
		 * @param string $post_type Post type.
		 */
		$is_supported = apply_filters( 'elementor/utils/is_post_type_support', $is_supported, $post_id, $post_type );

		/**
		 * Is post support.
		 *
		 * Filters whether the post supports editing with Elementor.
		 *
		 * @since 2.2.0
		 *
		 * @param bool $is_supported Whether the post type supports editing with Elementor.
		 * @param int $post_id Post ID.
		 * @param string $post_type Post type.
		 */
		$is_supported = apply_filters( 'elementor/utils/is_post_support', $is_supported, $post_id, $post_type );

		return $is_supported;
	}


	/**
	 * Is post type supports Elementor.
	 *
	 * Whether the post type supports editing with Elementor.
	 *
	 * @since 2.2.0
	 * @access public
	 * @static
	 *
	 * @param string $post_type Post Type.
	 *
	 * @return string True if post type supports editing with Elementor, false otherwise.
	 */
	public static function is_post_type_support( $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		if ( ! post_type_supports( $post_type, 'elementor' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get placeholder image source.
	 *
	 * Retrieve the source of the placeholder image.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return string The source of the default placeholder image used by Elementor.
	 */
	public static function get_placeholder_image_src() {
		$placeholder_image = ELEMENTOR_ASSETS_URL . 'images/placeholder.png';

		/**
		 * Get placeholder image source.
		 *
		 * Filters the source of the default placeholder image used by Elementor.
		 *
		 * @since 1.0.0
		 *
		 * @param string $placeholder_image The source of the default placeholder image.
		 */
		$placeholder_image = apply_filters( 'elementor/utils/get_placeholder_image_src', $placeholder_image );

		return $placeholder_image;
	}

	/**
	 * Generate random string.
	 *
	 * Returns a string containing a hexadecimal representation of random number.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return string Random string.
	 */
	public static function generate_random_string() {
		return dechex( rand() );
	}

	/**
	 * Do not cache.
	 *
	 * Tell WordPress cache plugins not to cache this request.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function do_not_cache() {
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( 'DONOTCACHEDB', true );
		}

		if ( ! defined( 'DONOTMINIFY' ) ) {
			define( 'DONOTMINIFY', true );
		}

		if ( ! defined( 'DONOTCDN' ) ) {
			define( 'DONOTCDN', true );
		}

		if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
			define( 'DONOTCACHEOBJECT', true );
		}

		// Set the headers to prevent caching for the different browsers.
		nocache_headers();
	}

	/**
	 * Get timezone string.
	 *
	 * Retrieve timezone string from the WordPress database.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return string Timezone string.
	 */
	public static function get_timezone_string() {
		$current_offset = (float) get_option( 'gmt_offset' );
		$timezone_string = get_option( 'timezone_string' );

		// Create a UTC+- zone if no timezone string exists.
		if ( empty( $timezone_string ) ) {
			if ( $current_offset < 0 ) {
				$timezone_string = 'UTC' . $current_offset;
			} else {
				$timezone_string = 'UTC+' . $current_offset;
			}
		}

		return $timezone_string;
	}

	/**
	 * Get create new post URL.
	 *
	 * Retrieve a custom URL for creating a new post/page using Elementor.
	 *
	 * @since 1.9.0
	 * @access public
	 * @deprecated 3.3.0 Use `Plugin::$instance->documents->get_create_new_post_url()` instead.
	 * @static
	 *
	 * @param string      $post_type Optional. Post type slug. Default is 'page'.
	 * @param string|null $template_type Optional. Query arg 'template_type'. Default is null.
	 *
	 * @return string A URL for creating new post using Elementor.
	 */
	public static function get_create_new_post_url( $post_type = 'page', $template_type = null ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __FUNCTION__, '3.3.0', 'Plugin::$instance->documents->get_create_new_post_url()' );

		return Plugin::$instance->documents->get_create_new_post_url( $post_type, $template_type );
	}

	/**
	 * Get post autosave.
	 *
	 * Retrieve an autosave for any given post.
	 *
	 * @since 1.9.2
	 * @access public
	 * @static
	 *
	 * @param int $post_id Post ID.
	 * @param int $user_id Optional. User ID. Default is `0`.
	 *
	 * @return \WP_Post|false Post autosave or false.
	 */
	public static function get_post_autosave( $post_id, $user_id = 0 ) {
		global $wpdb;

		$post = get_post( $post_id );

		$where = $wpdb->prepare( 'post_parent = %d AND post_name LIKE %s AND post_modified_gmt > %s', [ $post_id, "{$post_id}-autosave%", $post->post_modified_gmt ] );

		if ( $user_id ) {
			$where .= $wpdb->prepare( ' AND post_author = %d', $user_id );
		}

		$revision = $wpdb->get_row( "SELECT * FROM $wpdb->posts WHERE $where AND post_type = 'revision'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( $revision ) {
			$revision = new \WP_Post( $revision );
		} else {
			$revision = false;
		}

		return $revision;
	}

	/**
	 * Is CPT supports custom templates.
	 *
	 * Whether the Custom Post Type supports templates.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 * @return bool True is templates are supported, False otherwise.
	 */
	public static function is_cpt_custom_templates_supported() {
		require_once ABSPATH . '/wp-admin/includes/theme.php';

		return method_exists( wp_get_theme(), 'get_post_templates' );
	}

	/**
	 * @since 2.1.2
	 * @access public
	 * @static
	 */
	public static function array_inject( $base_array, $key, $insert ) {
		$length = array_search( $key, array_keys( $base_array ), true ) + 1;

		return array_slice( $base_array, 0, $length, true ) +
			$insert +
			array_slice( $base_array, $length, null, true );
	}

	/**
	 * Render html attributes
	 *
	 * @access public
	 * @static
	 * @param array $attributes
	 *
	 * @return string
	 */
	public static function render_html_attributes( array $attributes ) {
		$rendered_attributes = [];

		foreach ( $attributes as $attribute_key => $attribute_values ) {
			if ( is_array( $attribute_values ) ) {
				$attribute_values = implode( ' ', $attribute_values );
			}

			$rendered_attributes[] = sprintf( '%1$s="%2$s"', $attribute_key, esc_attr( $attribute_values ) );
		}

		return implode( ' ', $rendered_attributes );
	}

	/**
	 * Safe print html attributes
	 *
	 * @access public
	 * @static
	 * @param array $attributes
	 */
	public static function print_html_attributes( array $attributes ) {
		// PHPCS - the method render_html_attributes is safe.
		echo self::render_html_attributes( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public static function get_meta_viewport( $context = '' ) {
		$meta_tag = '<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />';

		/**
		 * Viewport meta tag.
		 *
		 * Filters the meta tag containing the viewport information.
		 *
		 * This hook can be used to change the initial viewport meta tag set by Elementor
		 * and replace it with a different viewport tag.
		 *
		 * @since 2.5.0
		 *
		 * @param string $meta_tag Viewport meta tag.
		 * @param string $context  Page context.
		 */
		$meta_tag = apply_filters( 'elementor/template/viewport_tag', $meta_tag, $context );

		return $meta_tag;
	}

	/**
	 * Add Elementor Config js vars to the relevant script handle,
	 * WP will wrap it with <script> tag.
	 * To make sure this script runs thru the `script_loader_tag` hook, use a known handle value.
	 *
	 * @param string $handle
	 * @param string $js_var
	 * @param mixed  $config
	 */
	public static function print_js_config( $handle, $js_var, $config ) {
		$config = wp_json_encode( $config );

		if ( get_option( self::EDITOR_BREAK_LINES_OPTION_KEY ) ) {
			// Add new lines to avoid memory limits in some hosting servers that handles the buffer output according to new line characters
			$config = str_replace( '}},"', '}},' . PHP_EOL . '"', $config );
		}

		$script_data = 'var ' . $js_var . ' = ' . $config . ';';

		wp_add_inline_script( $handle, $script_data, 'before' );
	}

	public static function handle_deprecation( $item, $version, $replacement = null ) {
		preg_match( '/^[0-9]+\.[0-9]+/', ELEMENTOR_VERSION, $current_version );

		$current_version_as_float = (float) $current_version[0];

		preg_match( '/^[0-9]+\.[0-9]+/', $version, $alias_version );

		$alias_version_as_float = (float) $alias_version[0];

		if ( round( $current_version_as_float - $alias_version_as_float, 1 ) >= self::DEPRECATION_RANGE ) {
			_deprecated_file( $item, $version, $replacement ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Checks a control value for being empty, including a string of '0' not covered by PHP's empty().
	 *
	 * @param mixed       $source
	 * @param bool|string $key
	 *
	 * @return bool
	 */
	public static function is_empty( $source, $key = false ) {
		if ( is_array( $source ) ) {
			if ( ! isset( $source[ $key ] ) ) {
				return true;
			}

			$source = $source[ $key ];
		}

		return '0' !== $source && empty( $source );
	}

	public static function has_pro() {
		return defined( 'ELEMENTOR_PRO_VERSION' );
	}

	/**
	 * Convert HTMLEntities to UTF-8 characters
	 *
	 * @param string $html_string
	 * @return string
	 */
	public static function urlencode_html_entities( $html_string ) {
		$entities_dictionary = [
			'&#145;' => "'", // Opening single quote
			'&#146;' => "'", // Closing single quote
			'&#147;' => '"', // Closing double quote
			'&#148;' => '"', // Opening double quote
			'&#8216;' => "'", // Closing single quote
			'&#8217;' => "'", // Opening single quote
			'&#8218;' => "'", // Single low quote
			'&#8220;' => '"', // Closing double quote
			'&#8221;' => '"', // Opening double quote
			'&#8222;' => '"', // Double low quote
		];

		// Decode decimal entities
		$html_string = str_replace( array_keys( $entities_dictionary ), array_values( $entities_dictionary ), $html_string );

		return rawurlencode( html_entity_decode( $html_string, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	}

	/**
	 * Parse attributes that come as a string of comma-delimited key|value pairs.
	 * Removes Javascript events and unescaped `href` attributes.
	 *
	 * @param string $attributes_string
	 *
	 * @param string $delimiter Default comma `,`.
	 *
	 * @return array
	 */
	public static function parse_custom_attributes( $attributes_string, $delimiter = ',' ) {
		$attributes = explode( $delimiter, $attributes_string );
		$result = [];

		foreach ( $attributes as $attribute ) {
			$attr_key_value = explode( '|', $attribute );

			$attr_key = mb_strtolower( $attr_key_value[0] );

			// Remove any not allowed characters.
			preg_match( '/[-_a-z0-9]+/', $attr_key, $attr_key_matches );

			if ( empty( $attr_key_matches[0] ) ) {
				continue;
			}

			$attr_key = $attr_key_matches[0];

			// Avoid Javascript events and unescaped href.
			if ( 'href' === $attr_key || 'on' === substr( $attr_key, 0, 2 ) ) {
				continue;
			}

			if ( isset( $attr_key_value[1] ) ) {
				$attr_value = trim( $attr_key_value[1] );
			} else {
				$attr_value = '';
			}

			$result[ $attr_key ] = $attr_value;
		}

		return $result;
	}

	public static function find_element_recursive( $elements, $id ) {
		foreach ( $elements as $element ) {
			if ( $id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = self::find_element_recursive( $element['elements'], $id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	/**
	 * Change Submenu First Item Label
	 *
	 * Overwrite the label of the first submenu item of an admin menu item.
	 *
	 * Fired by `admin_menu` action.
	 *
	 * @since 3.1.0
	 *
	 * @param string $menu_slug
	 * @param string $new_label
	 * @access public
	 */
	public static function change_submenu_first_item_label( $menu_slug, $new_label ) {
		global $submenu;

		if ( isset( $submenu[ $menu_slug ] ) ) {
			// @codingStandardsIgnoreStart
			$submenu[ $menu_slug ][0][0] = $new_label;
			// @codingStandardsIgnoreEnd
		}
	}

	/**
	 * Validate an HTML tag against a safe allowed list.
	 *
	 * @param string $tag
	 *
	 * @return string
	 */
	public static function validate_html_tag( $tag ) {
		return $tag && in_array( strtolower( $tag ), self::ALLOWED_HTML_WRAPPER_TAGS ) ? $tag : 'div';
	}

	/**
	 * Safe print a validated HTML tag.
	 *
	 * @param string $tag
	 */
	public static function print_validated_html_tag( $tag ) {
		// PHPCS - the method validate_html_tag is safe.
		echo self::validate_html_tag( $tag ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Print internal content (not user input) without escaping.
	 */
	public static function print_unescaped_internal_string( $internal_string ) {
		echo $internal_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get recently edited posts query.
	 *
	 * Returns `WP_Query` of the recent edited posts.
	 * By default max posts ( $args['posts_per_page'] ) is 3.
	 *
	 * @param array $args
	 *
	 * @return \WP_Query
	 */
	public static function get_recently_edited_posts_query( $args = [] ) {
		$args = wp_parse_args( $args, [
			'no_found_rows' => true,
			'post_type' => 'any',
			'post_status' => [ 'publish', 'draft' ],
			'posts_per_page' => '3',
			'meta_key' => '_elementor_edit_mode',
			'meta_value' => 'builder',
			'orderby' => 'modified',
		] );

		return new \WP_Query( $args );
	}

	public static function print_wp_kses_extended( $text, array $tags ) {
		$allowed_html = wp_kses_allowed_html( 'post' );

		foreach ( $tags as $tag ) {
			if ( isset( self::EXTENDED_ALLOWED_HTML_TAGS[ $tag ] ) ) {
				$extended_tags = apply_filters( "elementor/extended_allowed_html_tags/{$tag}", self::EXTENDED_ALLOWED_HTML_TAGS[ $tag ] );
				$allowed_html = array_replace_recursive( $allowed_html, $extended_tags );
			}
		}

		echo wp_kses( $text, $allowed_html );
	}

	public static function is_elementor_path( $path ) {
		$path = wp_normalize_path( $path );

		/**
		 * Elementor related paths.
		 *
		 * Filters Elementor related paths.
		 *
		 * @param string[] $available_paths
		 */
		$available_paths = apply_filters( 'elementor/utils/elementor_related_paths', [ ELEMENTOR_PATH ] );

		return (bool) ( new Collection( $available_paths ) )
			->map( function ( $p ) {
				// `untrailingslashit` in order to include other plugins prefixed with elementor.
				return untrailingslashit( wp_normalize_path( $p ) );
			} )
			->find(function ( $p ) use ( $path ) {
				return false !== strpos( $path, $p );
			} );
	}

	/**
	 * @param string $file
	 * @param mixed  ...$args
	 * @return false|string
	 */
	public static function file_get_contents( $file, ...$args ) {
		if ( ! is_file( $file ) || ! is_readable( $file ) ) {
			return false;
		}
		return file_get_contents( $file, ...$args );
	}

	public static function get_super_global_value( $super_global, $key ) {
		if ( ! isset( $super_global[ $key ] ) ) {
			return null;
		}

		if ( $_FILES === $super_global ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return isset( $super_global[ $key ]['name'] ) ?
				self::sanitize_file_name( $super_global[ $key ] ) :
				self::sanitize_multi_upload( $super_global[ $key ] );
		}

		return wp_kses_post_deep( wp_unslash( $super_global[ $key ] ) );
	}

	private static function sanitize_multi_upload( $fields ) {
		return array_map( function( $field ) {
			return array_map( 'self::sanitize_file_name', $field );
		}, $fields );
	}

	private static function sanitize_file_name( $file ) {
		$file['name'] = sanitize_file_name( $file['name'] );

		return $file;
	}

	/**
	 * Return specific object property value if exist from array of keys.
	 *
	 * @param array $base_array
	 * @param array $keys
	 * @return mixed|null
	 */
	public static function get_array_value_by_keys( $base_array, $keys ) {
		$keys = (array) $keys;
		foreach ( $keys as $key ) {
			if ( ! isset( $base_array[ $key ] ) ) {
				return null;
			}
			$base_array = $base_array[ $key ];
		}
		return $base_array;
	}

	public static function get_cached_callback( $callback, $cache_key, $cache_time = 24 * HOUR_IN_SECONDS ) {
		$cache = get_site_transient( $cache_key );

		if ( ! $cache ) {
			$cache = call_user_func( $callback );

			if ( ! is_wp_error( $cache ) ) {
				set_site_transient( $cache_key, $cache, $cache_time );
			}
		}

		return $cache;
	}

	public static function is_sale_time(): bool {
		$sale_start_time = gmmktime( 12, 0, 0, 11, 25, 2025 );
		$sale_end_time = gmmktime( 3, 59, 0, 12, 3, 2025 );

		$now_time = gmdate( 'U' );

		return $now_time >= $sale_start_time && $now_time <= $sale_end_time;
	}

	public static function safe_throw( string $message ) {
		if ( ! static::is_elementor_debug() ) {
			return;
		}

		throw new \Exception( esc_html( $message ) );
	}

	public static function has_invalid_post_permissions( $post ): bool {
		$is_image_attachment = 'attachment' === $post->post_type && strpos( $post->post_mime_type, 'image/' ) === 0;

		if ( $is_image_attachment ) {
			return false;
		}

		$is_private = 'private' === $post->post_status
			&& ! current_user_can( 'read_private_posts', $post->ID );

		$not_allowed = 'publish' !== $post->post_status
			&& ! current_user_can( 'edit_post', $post->ID );

		$password_required = post_password_required( $post->ID )
			&& ! current_user_can( 'edit_post', $post->ID );

		return $is_private || $not_allowed || $password_required;
	}

	public static function is_custom_kit_applied() {
		return (bool) Plugin::$instance->kits_manager->get_previous_id();
	}

	public static function decode_string( string $encoded_string, ?string $fallback = '' ) {
		try {
			return base64_decode( $encoded_string, true ) ?? $fallback;
		} catch ( \Exception $e ) {
			return $fallback;
		}
	}

	public static function encode_string( string $decoded_string ): string {
		return base64_encode( $decoded_string );
	}
}
