<?php
/**
 * Templates registry functions.
 *
 * @package WordPress
 * @since 6.7.0
 */

/**
 * Core class used for interacting with templates.
 *
 * @since 6.7.0
 */
final class WP_Block_Templates_Registry {
	/**
	 * Registered templates, as `$name => $instance` pairs.
	 *
	 * @since 6.7.0
	 * @var WP_Block_Template[] $registered_block_templates Registered templates.
	 */
	private $registered_templates = array();

	/**
	 * Container for the main instance of the class.
	 *
	 * @since 6.7.0
	 * @var WP_Block_Templates_Registry|null
	 */
	private static $instance = null;

	/**
	 * Registers a template.
	 *
	 * @since 6.7.0
	 *
	 * @param string $template_name Template name including namespace.
	 * @param array  $args          Optional. Array of template arguments.
	 * @return WP_Block_Template|WP_Error The registered template on success, or WP_Error on failure.
	 */
	public function register( $template_name, $args = array() ) {

		$template = null;

		$error_message = '';
		$error_code    = '';

		if ( ! is_string( $template_name ) ) {
			$error_message = __( 'Template names must be strings.' );
			$error_code    = 'template_name_no_string';
		} elseif ( preg_match( '/[A-Z]+/', $template_name ) ) {
			$error_message = __( 'Template names must not contain uppercase characters.' );
			$error_code    = 'template_name_no_uppercase';
		} elseif ( ! preg_match( '/^[a-z0-9-]+\/\/[a-z0-9-]+$/', $template_name ) ) {
			$error_message = __( 'Template names must contain a namespace prefix. Example: my-plugin//my-custom-template' );
			$error_code    = 'template_no_prefix';
		} elseif ( $this->is_registered( $template_name ) ) {
			/* translators: %s: Template name. */
			$error_message = sprintf( __( 'Template "%s" is already registered.' ), $template_name );
			$error_code    = 'template_already_registered';
		}

		if ( $error_message ) {
			_doing_it_wrong(
				__METHOD__,
				$error_message,
				'6.7.0'
			);
			return new WP_Error( $error_code, $error_message );
		}

		if ( ! $template ) {
			$theme_name             = get_stylesheet();
			list( $plugin, $slug )  = explode( '//', $template_name );
			$default_template_types = get_default_block_template_types();

			$template              = new WP_Block_Template();
			$template->id          = $theme_name . '//' . $slug;
			$template->theme       = $theme_name;
			$template->plugin      = $plugin;
			$template->author      = null;
			$template->content     = isset( $args['content'] ) ? $args['content'] : '';
			$template->source      = 'plugin';
			$template->slug        = $slug;
			$template->type        = 'wp_template';
			$template->title       = isset( $args['title'] ) ? $args['title'] : $template_name;
			$template->description = isset( $args['description'] ) ? $args['description'] : '';
			$template->status      = 'publish';
			$template->origin      = 'plugin';
			$template->is_custom   = ! isset( $default_template_types[ $template_name ] );
			$template->post_types  = isset( $args['post_types'] ) ? $args['post_types'] : array();
		}

		$this->registered_templates[ $template_name ] = $template;

		return $template;
	}

	/**
	 * Retrieves all registered templates.
	 *
	 * @since 6.7.0
	 *
	 * @return WP_Block_Template[] Associative array of `$template_name => $template` pairs.
	 */
	public function get_all_registered() {
		return $this->registered_templates;
	}

	/**
	 * Retrieves a registered template by its name.
	 *
	 * @since 6.7.0
	 *
	 * @param string $template_name Template name including namespace.
	 * @return WP_Block_Template|null The registered template, or null if it is not registered.
	 */
	public function get_registered( $template_name ) {
		if ( ! $this->is_registered( $template_name ) ) {
			return null;
		}

		return $this->registered_templates[ $template_name ];
	}

	/**
	 * Retrieves a registered template by its slug.
	 *
	 * @since 6.7.0
	 *
	 * @param string $template_slug Slug of the template.
	 * @return WP_Block_Template|null The registered template, or null if it is not registered.
	 */
	public function get_by_slug( $template_slug ) {
		$all_templates = $this->get_all_registered();

		if ( ! $all_templates ) {
			return null;
		}

		foreach ( $all_templates as $template ) {
			if ( $template->slug === $template_slug ) {
				return $template;
			}
		}

		return null;
	}

	/**
	 * Retrieves registered templates matching a query.
	 *
	 * @since 6.7.0
	 *
	 * @param array  $query {
	 *     Arguments to retrieve templates. Optional, empty by default.
	 *
	 *     @type string[] $slug__in     List of slugs to include.
	 *     @type string[] $slug__not_in List of slugs to skip.
	 *     @type string   $post_type    Post type to get the templates for.
	 * }
	 * @return WP_Block_Template[] Associative array of `$template_name => $template` pairs.
	 */
	public function get_by_query( $query = array() ) {
		$all_templates = $this->get_all_registered();

		if ( ! $all_templates ) {
			return array();
		}

		$query            = wp_parse_args(
			$query,
			array(
				'slug__in'     => array(),
				'slug__not_in' => array(),
				'post_type'    => '',
			)
		);
		$slugs_to_include = $query['slug__in'];
		$slugs_to_skip    = $query['slug__not_in'];
		$post_type        = $query['post_type'];

		$matching_templates = array();
		foreach ( $all_templates as $template_name => $template ) {
			if ( $slugs_to_include && ! in_array( $template->slug, $slugs_to_include, true ) ) {
				continue;
			}

			if ( $slugs_to_skip && in_array( $template->slug, $slugs_to_skip, true ) ) {
				continue;
			}

			if ( $post_type && ! in_array( $post_type, $template->post_types, true ) ) {
				continue;
			}

			$matching_templates[ $template_name ] = $template;
		}

		return $matching_templates;
	}

	/**
	 * Checks if a template is registered.
	 *
	 * @since 6.7.0
	 *
	 * @param string $template_name Template name.
	 * @return bool True if the template is registered, false otherwise.
	 */
	public function is_registered( $template_name ) {
		return isset( $this->registered_templates[ $template_name ] );
	}

	/**
	 * Unregisters a template.
	 *
	 * @since 6.7.0
	 *
	 * @param string $template_name Template name including namespace.
	 * @return WP_Block_Template|WP_Error The unregistered template on success, or WP_Error on failure.
	 */
	public function unregister( $template_name ) {
		if ( ! $this->is_registered( $template_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Template name. */
				sprintf( __( 'Template "%s" is not registered.' ), $template_name ),
				'6.7.0'
			);
			/* translators: %s: Template name. */
			return new WP_Error( 'template_not_registered', __( 'Template "%s" is not registered.' ) );
		}

		$unregistered_template = $this->registered_templates[ $template_name ];
		unset( $this->registered_templates[ $template_name ] );

		return $unregistered_template;
	}

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 6.7.0
	 *
	 * @return WP_Block_Templates_Registry The main instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
