<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use WP_Block;
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;
use Automattic\WooCommerce\Blocks\Assets\Api as AssetApi;
use Automattic\WooCommerce\Blocks\Integrations\IntegrationRegistry;

/**
 * AbstractBlock class.
 */
abstract class AbstractBlock {

	/**
	 * Block namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'woocommerce';

	/**
	 * Block name within this namespace.
	 *
	 * @var string
	 */
	protected $block_name = '';

	/**
	 * Tracks if assets have been enqueued.
	 *
	 * @var boolean
	 */
	protected $enqueued_assets = false;

	/**
	 * Instance of the asset API.
	 *
	 * @var AssetApi
	 */
	protected $asset_api;

	/**
	 * Instance of the asset data registry.
	 *
	 * @var AssetDataRegistry
	 */
	protected $asset_data_registry;

	/**
	 * Instance of the integration registry.
	 *
	 * @var IntegrationRegistry
	 */
	protected $integration_registry;

	/**
	 * Constructor.
	 *
	 * @param AssetApi            $asset_api Instance of the asset API.
	 * @param AssetDataRegistry   $asset_data_registry Instance of the asset data registry.
	 * @param IntegrationRegistry $integration_registry Instance of the integration registry.
	 * @param string              $block_name Optionally set block name during construct.
	 */
	public function __construct( AssetApi $asset_api, AssetDataRegistry $asset_data_registry, IntegrationRegistry $integration_registry, $block_name = '' ) {
		$this->asset_api            = $asset_api;
		$this->asset_data_registry  = $asset_data_registry;
		$this->integration_registry = $integration_registry;
		$this->block_name           = $block_name ? $block_name : $this->block_name;
		$this->initialize();
	}

	/**
	 * The default render_callback for all blocks. This will ensure assets are enqueued just in time, then render
	 * the block (if applicable).
	 *
	 * @param array|WP_Block $attributes Block attributes, or an instance of a WP_Block. Defaults to an empty array.
	 * @param string         $content    Block content. Default empty string.
	 * @param WP_Block|null  $block      Block instance.
	 * @return string Rendered block type output.
	 */
	public function render_callback( $attributes = [], $content = '', $block = null ) {

		$render_callback_attributes = $this->parse_render_callback_attributes( $attributes );
		if ( ! is_admin() && ! WC()->is_rest_api_request() ) {
			$this->enqueue_assets( $render_callback_attributes );
		}
		return $this->render( $render_callback_attributes, $content, $block );
	}

	/**
	 * Enqueue assets used for rendering the block in editor context.
	 *
	 * This is needed if a block is not yet within the post content--`render` and `enqueue_assets` may not have ran.
	 */
	public function enqueue_editor_assets() {
		if ( $this->enqueued_assets ) {
			return;
		}
		$this->enqueue_data();
	}

	/**
	 * Initialize this block type.
	 *
	 * - Hook into WP lifecycle.
	 * - Register the block with WordPress.
	 */
	protected function initialize() {
		if ( empty( $this->block_name ) ) {
			_doing_it_wrong( __METHOD__, esc_html__( 'Block name is required.', 'woocommerce' ), '4.5.0' );
			return false;
		}
		$this->integration_registry->initialize( $this->block_name . '_block' );
		$this->register_block_type_assets();
		$this->register_block_type();
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );
	}

	/**
	 * Register script and style assets for the block type before it is registered.
	 *
	 * This registers the scripts; it does not enqueue them.
	 */
	protected function register_block_type_assets() {
		if ( null !== $this->get_block_type_editor_script() ) {
			$data     = $this->asset_api->get_script_data( $this->get_block_type_editor_script( 'path' ) );
			$has_i18n = in_array( 'wp-i18n', $data['dependencies'], true );

			$this->asset_api->register_script(
				$this->get_block_type_editor_script( 'handle' ),
				$this->get_block_type_editor_script( 'path' ),
				array_merge(
					$this->get_block_type_editor_script( 'dependencies' ),
					$this->integration_registry->get_all_registered_editor_script_handles()
				),
				$has_i18n
			);
		}
		if ( null !== $this->get_block_type_script() ) {
			$data     = $this->asset_api->get_script_data( $this->get_block_type_script( 'path' ) );
			$has_i18n = in_array( 'wp-i18n', $data['dependencies'], true );

			$this->asset_api->register_script(
				$this->get_block_type_script( 'handle' ),
				$this->get_block_type_script( 'path' ),
				array_merge(
					$this->get_block_type_script( 'dependencies' ),
					$this->integration_registry->get_all_registered_script_handles()
				),
				$has_i18n
			);
		}
	}

	/**
	 * Injects Chunk Translations into the page so translations work for lazy loaded components.
	 *
	 * The chunk names are defined when creating lazy loaded components using webpackChunkName.
	 *
	 * @param string[] $chunks Array of chunk names.
	 */
	protected function register_chunk_translations( $chunks ) {
		foreach ( $chunks as $chunk ) {
			$handle = 'wc-blocks-' . $chunk . '-chunk';
			$this->asset_api->register_script( $handle, $this->asset_api->get_block_asset_build_path( $chunk ), [], true );
			wp_add_inline_script(
				$this->get_block_type_script( 'handle' ),
				wp_scripts()->print_translations( $handle, false ),
				'before'
			);
			wp_deregister_script( $handle );
		}
	}

	/**
	 * Generate an array of chunks paths for loading translation.
	 *
	 * @param string $chunks_folder The folder to iterate over.
	 * @return string[] $chunks list of chunks to load.
	 */
	protected function get_chunks_paths( $chunks_folder ) {
		$build_path = \Automattic\WooCommerce\Blocks\Package::get_path() . 'build/';
		$blocks     = [];
		if ( ! is_dir( $build_path . $chunks_folder ) ) {
			return [];
		}
		foreach ( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $build_path . $chunks_folder ) ) as $block_name ) {
			$blocks[] = str_replace( $build_path, '', $block_name );
		}

		$chunks = preg_filter( '/.js/', '', $blocks );
		return $chunks;
	}
	/**
	 * Registers the block type with WordPress.
	 *
	 * @return string[] Chunks paths.
	 */
	protected function register_block_type() {
		$block_settings = [
			'render_callback' => $this->get_block_type_render_callback(),
			'editor_script'   => $this->get_block_type_editor_script( 'handle' ),
			'editor_style'    => $this->get_block_type_editor_style(),
			'style'           => $this->get_block_type_style(),
		];

		if ( isset( $this->api_version ) && '2' === $this->api_version ) {
			$block_settings['api_version'] = 2;
		}

		$metadata_path = $this->asset_api->get_block_metadata_path( $this->block_name );
		// Prefer to register with metadata if the path is set in the block's class.
		if ( ! empty( $metadata_path ) ) {
			register_block_type_from_metadata(
				$metadata_path,
				$block_settings
			);
			return;
		}

		/*
		 * Insert attributes and supports if we're not registering the block using metadata.
		 * These are left unset until now and only added here because if they were set when registering with metadata,
		 * the attributes and supports from $block_settings would override the values from metadata.
		 */
		$block_settings['attributes']   = $this->get_block_type_attributes();
		$block_settings['supports']     = $this->get_block_type_supports();
		$block_settings['uses_context'] = $this->get_block_type_uses_context();

		register_block_type(
			$this->get_block_type(),
			$block_settings
		);
	}

	/**
	 * Get the block type.
	 *
	 * @return string
	 */
	protected function get_block_type() {
		return $this->namespace . '/' . $this->block_name;
	}

	/**
	 * Get the render callback for this block type.
	 *
	 * Dynamic blocks should return a callback, for example, `return [ $this, 'render' ];`
	 *
	 * @see $this->register_block_type()
	 * @return callable|null;
	 */
	protected function get_block_type_render_callback() {
		return [ $this, 'render_callback' ];
	}

	/**
	 * Get the editor script data for this block type.
	 *
	 * @see $this->register_block_type()
	 * @param string $key Data to get, or default to everything.
	 * @return array|string
	 */
	protected function get_block_type_editor_script( $key = null ) {
		$script = [
			'handle'       => 'wc-' . $this->block_name . '-block',
			'path'         => $this->asset_api->get_block_asset_build_path( $this->block_name ),
			'dependencies' => [ 'wc-blocks' ],
		];
		return $key ? $script[ $key ] : $script;
	}

	/**
	 * Get the editor style handle for this block type.
	 *
	 * @see $this->register_block_type()
	 * @return string|null
	 */
	protected function get_block_type_editor_style() {
		return 'wc-blocks-editor-style';
	}

	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @see $this->register_block_type()
	 * @param string $key Data to get, or default to everything.
	 * @return array|string|null
	 */
	protected function get_block_type_script( $key = null ) {
		$script = [
			'handle'       => 'wc-' . $this->block_name . '-block-frontend',
			'path'         => $this->asset_api->get_block_asset_build_path( $this->block_name . '-frontend' ),
			'dependencies' => [],
		];
		return $key ? $script[ $key ] : $script;
	}

	/**
	 * Get the frontend style handle for this block type.
	 *
	 * @see $this->register_block_type()
	 * @return string|null
	 */
	protected function get_block_type_style() {
		return 'wc-blocks-style';
	}

	/**
	 * Get the supports array for this block type.
	 *
	 * @see $this->register_block_type()
	 * @return string;
	 */
	protected function get_block_type_supports() {
		return [];
	}

	/**
	 * Get block attributes.
	 *
	 * @return array;
	 */
	protected function get_block_type_attributes() {
		return [];
	}

	/**
	 * Get block usesContext.
	 *
	 * @return array;
	 */
	protected function get_block_type_uses_context() {
		return [];
	}

	/**
	 * Parses block attributes from the render_callback.
	 *
	 * @param array|WP_Block $attributes Block attributes, or an instance of a WP_Block. Defaults to an empty array.
	 * @return array
	 */
	protected function parse_render_callback_attributes( $attributes ) {
		return is_a( $attributes, 'WP_Block' ) ? $attributes->attributes : $attributes;
	}

	/**
	 * Render the block. Extended by children.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		return $content;
	}

	/**
	 * Enqueue frontend assets for this block, just in time for rendering.
	 *
	 * @internal This prevents the block script being enqueued on all pages. It is only enqueued as needed. Note that
	 * we intentionally do not pass 'script' to register_block_type.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 */
	protected function enqueue_assets( array $attributes ) {
		if ( $this->enqueued_assets ) {
			return;
		}
		$this->enqueue_data( $attributes );
		$this->enqueue_scripts( $attributes );
		$this->enqueued_assets = true;
	}

	/**
	 * Data passed through from server to client for block.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 *                           Note, this will be empty in the editor context when the block is
	 *                           not in the post content on editor load.
	 */
	protected function enqueue_data( array $attributes = [] ) {
		$registered_script_data = $this->integration_registry->get_all_registered_script_data();

		foreach ( $registered_script_data as $asset_data_key => $asset_data_value ) {
			if ( ! $this->asset_data_registry->exists( $asset_data_key ) ) {
				$this->asset_data_registry->add( $asset_data_key, $asset_data_value );
			}
		}

		if ( ! $this->asset_data_registry->exists( 'wcBlocksConfig' ) ) {
			$this->asset_data_registry->add(
				'wcBlocksConfig',
				[
					'buildPhase'    => Package::feature()->get_flag(),
					'pluginUrl'     => plugins_url( '/', dirname( __DIR__ ) ),
					'productCount'  => array_sum( (array) wp_count_posts( 'product' ) ),
					'restApiRoutes' => [
						'/wc/store/v1' => array_keys( $this->get_routes_from_namespace( 'wc/store/v1' ) ),
					],
					'defaultAvatar' => get_avatar_url( 0, [ 'force_default' => true ] ),

					/*
					 * translators: If your word count is based on single characters (e.g. East Asian characters),
					 * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
					 * Do not translate into your own language.
					 */
					'wordCountType' => _x( 'words', 'Word count type. Do not translate!', 'woocommerce' ),
				]
			);
		}
	}

	/**
	 * Get routes from a REST API namespace.
	 *
	 * @param string $namespace Namespace to retrieve.
	 * @return array
	 */
	protected function get_routes_from_namespace( $namespace ) {
		$rest_server     = rest_get_server();
		$namespace_index = $rest_server->get_namespace_index(
			[
				'namespace' => $namespace,
				'context'   => 'view',
			]
		);

		if ( is_wp_error( $namespace_index ) ) {
			return [];
		}

		$response_data = $namespace_index->get_data();

		return $response_data['routes'] ?? [];
	}

	/**
	 * Register/enqueue scripts used for this block on the frontend, during render.
	 *
	 * @param array $attributes Any attributes that currently are available from the block.
	 */
	protected function enqueue_scripts( array $attributes = [] ) {
		if ( null !== $this->get_block_type_script() ) {
			wp_enqueue_script( $this->get_block_type_script( 'handle' ) );
		}
	}
}
