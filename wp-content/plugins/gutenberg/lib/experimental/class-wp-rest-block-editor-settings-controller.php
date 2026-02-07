<?php
/**
 * REST API: WP_REST_Block_Editor_Settings_Controller class
 *
 * @package    WordPress
 * @subpackage REST_API
 */

if ( ! class_exists( 'WP_REST_Block_Editor_Settings_Controller' ) ) {

	/**
	 * Core class used to retrieve the block editor settings via the REST API.
	 *
	 * @see WP_REST_Controller
	 */
	class WP_REST_Block_Editor_Settings_Controller extends WP_REST_Controller {
		/**
		 * Constructs the controller.
		 */
		public function __construct() {
			$this->namespace = 'wp-block-editor/v1';
			$this->rest_base = 'settings';
		}

		/**
		 * Registers the necessary REST API routes.
		 */
		public function register_routes() {
			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base,
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_items' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
					),
					'schema' => array( $this, 'get_public_item_schema' ),
				)
			);

			register_rest_route(
				$this->namespace,
				'/assets',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_assets' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
					),
					'schema' => array( $this, 'get_assets_schema' ),
				)
			);
		}

		/**
		 * Checks whether a given request has permission to read block editor settings
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_Error|bool True if the request has permission, WP_Error object otherwise.
		 */
		public function get_items_permissions_check( $request ) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			if ( current_user_can( 'edit_posts' ) ) {
				return true;
			}

			foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
				if ( current_user_can( $post_type->cap->edit_posts ) ) {
					return true;
				}
			}

			return new WP_Error(
				'rest_cannot_read_block_editor_settings',
				__( 'Sorry, you are not allowed to read the block editor settings.', 'gutenberg' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		/**
		 * Returns the block editor's settings
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error object on failure.
		 */
		public function get_items( $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis
			// Simplified context handling: mobile vs default.
			// Only 'mobile' context is special; everything else uses the default editor context.
			$context_param       = $request->get_param( 'context' );
			$editor_context_name = 'mobile' === $context_param ? 'core/mobile' : 'core/edit-post';

			// Apply mobile-specific settings filter only for mobile context.
			if ( 'mobile' === $context_param ) {
				add_filter( 'block_editor_settings_all', 'gutenberg_get_block_editor_settings_mobile', PHP_INT_MAX );
			}

			$editor_context = new WP_Block_Editor_Context( array( 'name' => $editor_context_name ) );
			$settings       = get_block_editor_settings( array(), $editor_context );

			if ( 'mobile' === $context_param ) {
				remove_filter( 'block_editor_settings_all', 'gutenberg_get_block_editor_settings_mobile', PHP_INT_MAX );
			}

			return rest_ensure_response( $settings );
		}

		/**
		 * Retrieves the block editor's settings schema, conforming to JSON Schema.
		 *
		 * @since 5.8.0
		 *
		 * @return array Item schema data.
		 */
		public function get_item_schema() {
			if ( $this->schema ) {
				return $this->add_additional_fields_schema( $this->schema );
			}

			$schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'block-editor-settings-item',
				'type'       => 'object',
				'properties' => array(
					'__unstableEnableFullSiteEditingBlocks' => array(
						'description' => __( 'Enables experimental Site Editor blocks', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'styles'                           => array(
						'description' => __( 'Editor styles', 'gutenberg' ),
						'type'        => 'array',
						'context'     => array( 'default' ),
					),

					'supportsTemplateMode'             => array(
						'description' => __( 'Indicates whether the current theme supports block-based templates.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'supportsLayout'                   => array(
						'description' => __( 'Enable/disable layouts support in container blocks.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'widgetTypesToHideFromLegacyWidgetBlock' => array(
						'description' => __( 'Widget types to hide from Legacy Widget block.', 'gutenberg' ),
						'type'        => 'array',
						'context'     => array( 'default' ),
					),

					'__experimentalFeatures'           => array(
						'description' => __( 'Settings consolidated from core, theme, and user origins.', 'gutenberg' ),
						'type'        => 'object',
						'context'     => array( 'default', 'mobile' ),
					),

					'__experimentalStyles'             => array(
						'description' => __( 'Styles consolidated from core, theme, and user origins.', 'gutenberg' ),
						'type'        => 'object',
						'context'     => array( 'mobile' ),
					),

					'__experimentalEnableQuoteBlockV2' => array(
						'description' => __( 'Whether the V2 of the quote block that uses inner blocks should be enabled.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'mobile' ),
					),

					'__experimentalEnableListBlockV2'  => array(
						'description' => __( 'Whether the V2 of the list block that uses inner blocks should be enabled.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'mobile' ),
					),

					'alignWide'                        => array(
						'description' => __( 'Enable/Disable Wide/Full Alignments.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'allowedBlockTypes'                => array(
						'description' => __( 'List of allowed block types.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'allowedMimeTypes'                 => array(
						'description' => __( 'List of allowed mime types and file extensions.', 'gutenberg' ),
						'type'        => 'object',
						'context'     => array( 'default' ),
					),

					'blockCategories'                  => array(
						'description' => __( 'Returns all the categories for block types that will be shown in the block editor.', 'gutenberg' ),
						'type'        => 'array',
						'context'     => array( 'default' ),
					),

					'blockInspectorTabs'               => array(
						'description' => __( 'Block inspector tab display overrides.', 'gutenberg' ),
						'type'        => 'object',
						'context'     => array( 'default' ),
					),

					'disableCustomColors'              => array(
						'description' => __( 'Disables custom colors.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'disableCustomFontSizes'           => array(
						'description' => __( 'Disables custom font size.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'disableCustomGradients'           => array(
						'description' => __( 'Disables custom font size.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'disableLayoutStyles'              => array(
						'description' => __( 'Disables output of layout styles.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'enableCustomLineHeight'           => array(
						'description' => __( 'Enables custom line height.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'enableCustomSpacing'              => array(
						'description' => __( 'Enables custom spacing.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'enableCustomUnits'                => array(
						'description' => __( 'Enables custom units.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'isRTL'                            => array(
						'description' => __( 'Determines whether the current locale is right-to-left (RTL).', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'imageDefaultSize'                 => array(
						'description' => __( 'Default size for images.', 'gutenberg' ),
						'type'        => 'string',
						'context'     => array( 'default' ),
					),

					'imageDimensions'                  => array(
						'description' => __( 'Available image dimensions.', 'gutenberg' ),
						'type'        => 'object',
						'context'     => array( 'default' ),
					),

					'imageEditing'                     => array(
						'description' => __( 'Determines whether the image editing feature is enabled.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),

					'imageSizes'                       => array(
						'description' => __( 'Available image sizes.', 'gutenberg' ),
						'type'        => 'array',
						'context'     => array( 'default' ),
					),

					'maxUploadFileSize'                => array(
						'description' => __( 'Maximum upload size in bytes allowed for the site.', 'gutenberg' ),
						'type'        => 'number',
						'context'     => array( 'default' ),
					),

					'colors'                           => array(
						'description' => __( 'Active theme color palette.', 'gutenberg' ),
						'type'        => 'array',
						'context'     => array( 'default' ),
					),

					'fontSizes'                        => array(
						'description' => __( 'Active theme font sizes.', 'gutenberg' ),
						'type'        => 'array',
						'context'     => array( 'default' ),
					),

					'gradients'                        => array(
						'description' => __( 'Active theme gradients.', 'gutenberg' ),
						'type'        => 'array',
						'context'     => array( 'default' ),
					),
					'spacingSizes'                     => array(
						'description' => __( 'Active theme spacing sizes.', 'gutenberg' ),
						'type'        => 'array',
						'context'     => array( 'default' ),
					),
					'spacingScale'                     => array(
						'description' => __( 'Active theme spacing scale.', 'gutenberg' ),
						'type'        => 'array',
						'context'     => array( 'default' ),
					),
					'disableCustomSpacingSizes'        => array(
						'description' => __( 'Disables custom spacing sizes.', 'gutenberg' ),
						'type'        => 'boolean',
						'context'     => array( 'default' ),
					),
				),
			);

			$this->schema = $schema;

			return $this->add_additional_fields_schema( $this->schema );
		}

		/**
		 * Retrieves editor assets (scripts, styles, and inline content).
		 *
		 * @since Gutenberg 5.8.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function get_assets( $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			// Load WordPress admin environment.
			$this->load_admin_environment();

			// Set up the block editor context.
			set_current_screen();
			$current_screen = get_current_screen();
			if ( $current_screen ) {
				$current_screen->is_block_editor( true );
			}

			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$hook_suffix = 'block-editor-assets';

			// Remove unwanted scripts/styles.
			remove_action( 'admin_enqueue_scripts', 'gutenberg_enqueue_command_palette_assets', 9 );
			remove_action( 'admin_enqueue_scripts', 'gutenberg_enqueue_command_palette_assets' );
			remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );

			// Preload blocks - this creates inline scripts.
			$server_block_settings = get_block_editor_server_block_settings();
			wp_add_inline_script(
				'wp-block-library',
				'wp.blocks.unstable__bootstrapServerSideBlockDefinitions(' . wp_json_encode( $server_block_settings, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ) . ');',
				'before'
			);

			// Preload server-registered block bindings sources.
			$registered_sources = get_all_registered_block_bindings_sources();
			if ( ! empty( $registered_sources ) ) {
				$filtered_sources = array();
				foreach ( $registered_sources as $source ) {
					$filtered_sources[] = array(
						'name'        => $source->name,
						'label'       => $source->label,
						'usesContext' => $source->uses_context,
					);
				}
				$script = sprintf( 'for ( const source of %s ) { wp.blocks.registerBlockBindingsSource( source ); }', wp_json_encode( $filtered_sources, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ) );
				wp_add_inline_script(
					'wp-block-library',
					$script,
					'before'
				);
			}

			$load_blocks_scripts = 'wp.blockLibrary.registerCoreBlocks( wp.blockLibrary.__experimentalGetCoreBlocks().filter( ( block ) => block.name !== "core/freeform" ) );';
			wp_add_inline_script(
				'wp-block-library',
				$load_blocks_scripts,
				'after'
			);

			// Enqueue editor assets.
			wp_enqueue_script( 'wp-format-library' );
			wp_enqueue_script( 'wp-block-library' );
			wp_enqueue_style( 'wp-editor' );
			wp_enqueue_style( 'wp-block-library' );
			wp_enqueue_style( 'wp-format-library' );

			do_action( 'enqueue_block_editor_assets' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

			// Never allow old admin styles as a dependency.
			wp_deregister_style( 'common' );
			wp_deregister_style( 'forms' );

			// Trigger rendering hooks to capture all assets.
			ob_start();
			do_action( 'admin_enqueue_scripts', $hook_suffix ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( "admin_print_styles-{$hook_suffix}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( 'admin_print_styles' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( "admin_print_scripts-{$hook_suffix}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( 'admin_print_scripts' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( "admin_head-{$hook_suffix}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( 'admin_head' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( 'admin_footer', '' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( 'wp_print_footer_scripts' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( "admin_print_footer_scripts-{$hook_suffix}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( 'admin_print_footer_scripts' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			do_action( "admin_footer-{$hook_suffix}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			ob_get_clean();

			// Process the captured assets.
			return rest_ensure_response( $this->process_assets() );
		}

		/**
		 * Process WordPress assets and return structured data.
		 *
		 * @since Gutenberg 5.8.0
		 *
		 * @return array Structured asset data.
		 */
		private function process_assets() {
			global $wp_scripts, $wp_styles;

			$styles_data    = array();
			$scripts_data   = array();
			$inline_styles  = array(
				'before' => array(),
				'after'  => array(),
			);
			$inline_scripts = array(
				'before' => array(),
				'after'  => array(),
			);

			// Get boot module asset file for dependencies.
			$boot_asset_file   = include __DIR__ . '/../../build/modules/boot/index.min.asset.php';
			$boot_dependencies = isset( $boot_asset_file['dependencies'] ) ? $boot_asset_file['dependencies'] : array();

			// Get all dependencies that should be excluded (boot dependencies + their deep dependencies).
			$excluded_scripts = $this->get_all_dependencies( $boot_dependencies, $wp_scripts );
			$excluded_styles  = $this->get_all_dependencies( array( 'wp-components', 'wp-commands', 'dashicons' ), $wp_styles );

			// Process styles - include all dependencies of queued styles.
			$all_needed_styles = array();
			foreach ( $wp_styles->queue as $handle ) {
				$all_needed_styles = array_merge( $all_needed_styles, $this->get_all_dependencies( array( $handle ), $wp_styles ) );
			}
			$all_needed_styles = array_unique( $all_needed_styles );

			foreach ( $all_needed_styles as $handle ) {
				// Skip styles that are already loaded by the boot module.
				if ( in_array( $handle, $excluded_styles, true ) ) {
					continue;
				}

				if ( isset( $wp_styles->registered[ $handle ] ) ) {
					$style                  = $wp_styles->registered[ $handle ];
					$styles_data[ $handle ] = array(
						'src'     => $style->src,
						'deps'    => $style->deps,
						'version' => $style->ver,
						'media'   => $style->args,
					);

					$inline_style = $wp_styles->get_data( $handle, 'after' );
					if ( ! empty( $inline_style ) ) {
						$inline_styles['after'][ $handle ] = $inline_style;
					}
					$inline_style = $wp_styles->get_data( $handle, 'before' );
					if ( ! empty( $inline_style ) ) {
						$inline_styles['before'][ $handle ] = $inline_style;
					}
				}
			}

			// Process scripts - include all dependencies of queued scripts.
			$all_needed_scripts = array();
			foreach ( $wp_scripts->queue as $handle ) {
				$all_needed_scripts = array_merge( $all_needed_scripts, $this->get_all_dependencies( array( $handle ), $wp_scripts ) );
			}
			$all_needed_scripts = array_unique( $all_needed_scripts );

			foreach ( $all_needed_scripts as $handle ) {
				// Skip scripts that are already loaded by the boot module.
				if ( in_array( $handle, $excluded_scripts, true ) ) {
					continue;
				}

				if ( isset( $wp_scripts->registered[ $handle ] ) ) {
					$script                  = $wp_scripts->registered[ $handle ];
					$scripts_data[ $handle ] = array(
						'src'       => $script->src,
						'deps'      => $script->deps,
						'version'   => $script->ver,
						'in_footer' => isset( $script->extra['group'] ) && 1 === $script->extra['group'],
					);

					// Get inline scripts using proper WordPress methods.
					$before_script = $wp_scripts->get_inline_script_data( $handle, 'before' );
					$after_script  = $wp_scripts->get_inline_script_data( $handle, 'after' );

					// Get extra script data (localized data).
					$extra_script = $wp_scripts->print_extra_script( $handle, false );

					// Store before scripts (includes extra/localized data).
					$before_content = '';
					if ( ! empty( $before_script ) ) {
						$before_content .= $before_script . "\n";
					}
					if ( ! empty( $extra_script ) ) {
						$before_content .= $extra_script . "\n";
					}

					if ( ! empty( $before_content ) ) {
						$inline_scripts['before'][ $handle ] = trim( $before_content );
					}

					// Store after scripts separately.
					if ( ! empty( $after_script ) ) {
						$inline_scripts['after'][ $handle ] = trim( $after_script );
					}
				}
			}

			return array(
				'scripts'        => $scripts_data,
				'styles'         => $styles_data,
				'inline_scripts' => $inline_scripts,
				'inline_styles'  => $inline_styles,
			);
		}

		/**
		 * Get all dependencies recursively (works for both scripts and styles).
		 *
		 * @since Gutenberg 5.8.0
		 *
		 * @param array|string    $handles   The handles to get dependencies for.
		 * @param WP_Dependencies $wp_assets The WordPress dependencies object.
		 * @return array Array of all dependencies.
		 */
		private function get_all_dependencies( $handles, $wp_assets ) {
			$all_deps = array();
			$handles  = (array) $handles;

			foreach ( $handles as $handle ) {
				if ( isset( $wp_assets->registered[ $handle ] ) ) {
					$asset      = $wp_assets->registered[ $handle ];
					$all_deps[] = $handle;

					if ( ! empty( $asset->deps ) ) {
						$child_deps = $this->get_all_dependencies( $asset->deps, $wp_assets );
						$all_deps   = array_merge( $all_deps, $child_deps );
					}
				}
			}

			return array_unique( $all_deps );
		}

		/**
		 * Load WordPress admin environment and required functions.
		 *
		 * This mirrors the initialization that happens in wp-admin context
		 * to ensure all necessary functions and hooks are available.
		 *
		 * @since Gutenberg 5.8.0
		 */
		private function load_admin_environment() {
			// Load core admin files that provide essential functions.
			require_once ABSPATH . 'wp-admin/includes/admin.php';
		}

		/**
		 * Retrieves the editor assets schema, conforming to JSON Schema.
		 *
		 * @since Gutenberg 5.8.0
		 *
		 * @return array Item schema data.
		 */
		public function get_assets_schema() {
			$schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'block-editor-assets',
				'type'       => 'object',
				'properties' => array(
					'scripts'        => array(
						'description' => __( 'Editor scripts data.', 'gutenberg' ),
						'type'        => 'object',
						'readonly'    => true,
					),
					'styles'         => array(
						'description' => __( 'Editor styles data.', 'gutenberg' ),
						'type'        => 'object',
						'readonly'    => true,
					),
					'inline_scripts' => array(
						'description' => __( 'Inline scripts for editor assets.', 'gutenberg' ),
						'type'        => 'object',
						'readonly'    => true,
					),
					'inline_styles'  => array(
						'description' => __( 'Inline styles for editor assets.', 'gutenberg' ),
						'type'        => 'object',
						'readonly'    => true,
					),
				),
			);

			return $this->add_additional_fields_schema( $schema );
		}
	}
}
