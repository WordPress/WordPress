<?php
namespace Automattic\WooCommerce\Blocks\Templates;

use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;

/**
 * ClassicTemplatesCompatibility class.
 *
 * To bridge the gap on compatibility with widget blocks and classic PHP core templates.
 *
 * @internal
 */
class ClassicTemplatesCompatibility {

	/**
	 * Instance of the asset data registry.
	 *
	 * @var AssetDataRegistry
	 */
	protected $asset_data_registry;

	/**
	 * Constructor.
	 *
	 * @param AssetDataRegistry $asset_data_registry Instance of the asset data registry.
	 */
	public function __construct( AssetDataRegistry $asset_data_registry ) {
		$this->asset_data_registry = $asset_data_registry;
		$this->init();
	}

	/**
	 * Initialization method.
	 */
	protected function init() {
		if ( ! wc_current_theme_is_fse_theme() ) {
			add_action( 'template_redirect', array( $this, 'set_classic_template_data' ) );
			// We need to set this data on the widgets screen so the filters render previews.
			add_action( 'load-widgets.php', array( $this, 'set_filterable_product_data' ) );
		}
	}

	/**
	 * Executes the methods which set the necessary data needed for filter blocks to work correctly as widgets in Classic templates.
	 *
	 * @return void
	 */
	public function set_classic_template_data() {
		$this->set_filterable_product_data();
		$this->set_php_template_data();
	}

	/**
	 * This method passes the value `has_filterable_products` to the front-end for product archive pages,
	 * so that widget product filter blocks are aware of the context they are in and can render accordingly.
	 *
	 * @return void
	 */
	public function set_filterable_product_data() {
		global $pagenow;

		if ( is_shop() || is_product_taxonomy() || 'widgets.php' === $pagenow ) {
			$this->asset_data_registry->add( 'has_filterable_products', true, true );
		}
	}

	/**
	 * This method passes the value `is_rendering_php_template` to the front-end of Classic themes,
	 * so that widget product filter blocks are aware of how to filter the products.
	 *
	 * This data only matters on WooCommerce product archive pages.
	 * On non-archive pages the merchant could be using the All Products block which is not a PHP template.
	 *
	 * @return void
	 */
	public function set_php_template_data() {
		if ( is_shop() || is_product_taxonomy() ) {
			$this->asset_data_registry->add( 'is_rendering_php_template', true, true );
		}
	}
}
