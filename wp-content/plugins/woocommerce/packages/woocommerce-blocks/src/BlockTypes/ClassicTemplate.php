<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Templates\ProductAttributeTemplate;
use Automattic\WooCommerce\Blocks\Templates\ProductSearchResultsTemplate;
use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;
use WC_Query;

/**
 * Classic Single Product class
 *
 * @internal
 */
class ClassicTemplate extends AbstractDynamicBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'legacy-template';

	/**
	 * API version.
	 *
	 * @var string
	 */
	protected $api_version = '2';

	const FILTER_PRODUCTS_BY_STOCK_QUERY_PARAM = 'filter_stock_status';

	/**
	 * Initialize this block.
	 */
	protected function initialize() {
		parent::initialize();
		add_filter( 'render_block', array( $this, 'add_alignment_class_to_wrapper' ), 10, 2 );
		add_filter( 'woocommerce_product_query_meta_query', array( $this, 'filter_products_by_stock' ) );

	}

	/**
	 * Render method for the Classic Template block. This method will determine which template to render.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 * @return string | void Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		if ( ! isset( $attributes['template'] ) ) {
			return;
		}

		/**
		 * We need to load the scripts here because when using block templates wp_head() gets run after the block
		 * template. As a result we are trying to enqueue required scripts before we have even registered them.
		 *
		 * @see https://github.com/woocommerce/woocommerce-gutenberg-products-block/issues/5328#issuecomment-989013447
		 */
		if ( class_exists( 'WC_Frontend_Scripts' ) ) {
			$frontend_scripts = new \WC_Frontend_Scripts();
			$frontend_scripts::load_scripts();
		}

		$archive_templates = array( 'archive-product', 'taxonomy-product_cat', 'taxonomy-product_tag', ProductAttributeTemplate::SLUG, ProductSearchResultsTemplate::SLUG );

		if ( 'single-product' === $attributes['template'] ) {
			return $this->render_single_product();
		} elseif ( in_array( $attributes['template'], $archive_templates, true ) ) {
			// Set this so that our product filters can detect if it's a PHP template.
			$this->asset_data_registry->add( 'is_rendering_php_template', true, true );

			// Set this so filter blocks being used as widgets know when to render.
			$this->asset_data_registry->add( 'has_filterable_products', true, true );

			$this->asset_data_registry->add(
				'page_url',
				html_entity_decode( get_pagenum_link() ),
				''
			);

			return $this->render_archive_product();
		} else {
			ob_start();

			echo "You're using the ClassicTemplate block";

			wp_reset_postdata();
			return ob_get_clean();
		}
	}

	/**
	 * Render method for the single product template and parts.
	 *
	 * @return string Rendered block type output.
	 */
	protected function render_single_product() {
		ob_start();

		/**
		 * Hook: woocommerce_before_main_content
		 *
		 * Called before rendering the main content for a product.
		 *
		 * @see woocommerce_output_content_wrapper() Outputs opening DIV for the content (priority 10)
		 * @see woocommerce_breadcrumb() Outputs breadcrumb trail to the current product (priority 20)
		 * @see WC_Structured_Data::generate_website_data() Outputs schema markup (priority 30)
		 *
		 * @since 6.3.0
		 */
		do_action( 'woocommerce_before_main_content' );

		while ( have_posts() ) :

			the_post();
			wc_get_template_part( 'content', 'single-product' );

		endwhile;

		/**
		 * Hook: woocommerce_after_main_content
		 *
		 * Called after rendering the main content for a product.
		 *
		 * @see woocommerce_output_content_wrapper_end() Outputs closing DIV for the content (priority 10)
		 *
		 * @since 6.3.0
		 */
		do_action( 'woocommerce_after_main_content' );

		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Render method for the archive product template and parts.
	 *
	 * @return string Rendered block type output.
	 */
	protected function render_archive_product() {
		ob_start();

		/**
		 * Hook: woocommerce_before_main_content
		 *
		 * Called before rendering the main content for a product.
		 *
		 * @see woocommerce_output_content_wrapper() Outputs opening DIV for the content (priority 10)
		 * @see woocommerce_breadcrumb() Outputs breadcrumb trail to the current product (priority 20)
		 * @see WC_Structured_Data::generate_website_data() Outputs schema markup (priority 30)
		 *
		 * @since 6.3.0
		 */
		do_action( 'woocommerce_before_main_content' );

		?>
		<header class="woocommerce-products-header">
			<?php
			/**
			 * Hook: woocommerce_show_page_title
			 *
			 * Allows controlling the display of the page title.
			 *
			 * @since 6.3.0
			 */
			if ( apply_filters( 'woocommerce_show_page_title', true ) ) {
				?>
				<h1 class="woocommerce-products-header__title page-title">
					<?php
						woocommerce_page_title();
					?>
				</h1>
				<?php
			}
			/**
			 * Hook: woocommerce_archive_description.
			 *
			 * @see woocommerce_taxonomy_archive_description() Renders the taxonomy archive description (priority 10)
			 * @see woocommerce_product_archive_description() Renders the product archive description (priority 10)
			 *
			 * @since 6.3.0
			 */
			do_action( 'woocommerce_archive_description' );
			?>
		</header>
		<?php
		if ( woocommerce_product_loop() ) {

			/**
			 * Hook: woocommerce_before_shop_loop.
			 *
			 * @see woocommerce_output_all_notices() Render error notices (priority 10)
			 * @see woocommerce_result_count() Show number of results found (priority 20)
			 * @see woocommerce_catalog_ordering() Show form to control sort order (priority 30)
			 *
			 * @since 6.3.0
			 */
			do_action( 'woocommerce_before_shop_loop' );

			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();

					/**
					 * Hook: woocommerce_shop_loop.
					 *
					 * @since 6.3.0
					 */
					do_action( 'woocommerce_shop_loop' );

					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();

			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @see woocommerce_pagination() Renders pagination (priority 10)
			 *
			 * @since 6.3.0
			 */
			do_action( 'woocommerce_after_shop_loop' );
		} else {
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @see wc_no_products_found() Default no products found content (priority 10)
			 *
			 * @since 6.3.0
			 */
			do_action( 'woocommerce_no_products_found' );
		}

		/**
		 * Hook: woocommerce_after_main_content
		 *
		 * Called after rendering the main content for a product.
		 *
		 * @see woocommerce_output_content_wrapper_end() Outputs closing DIV for the content (priority 10)
		 *
		 * @since 6.3.0
		 */
		do_action( 'woocommerce_after_main_content' );

		wp_reset_postdata();
		return ob_get_clean();
	}

	/**
	 * Get HTML markup with the right classes by attributes.
	 * This function appends the classname at the first element that have the class attribute.
	 * Based on the experience, all the wrapper elements have a class attribute.
	 *
	 * @param string $content Block content.
	 * @param array  $block Parsed block data.
	 * @return string Rendered block type output.
	 */
	public function add_alignment_class_to_wrapper( string $content, array $block ) {
		if ( ( 'woocommerce/' . $this->block_name ) !== $block['blockName'] ) {
			return $content;
		}

		$attributes = (array) $block['attrs'];

		// Set the default alignment to wide.
		if ( ! isset( $attributes['align'] ) ) {
			$attributes['align'] = 'wide';
		}

		$align_class_and_style = StyleAttributesUtils::get_align_class_and_style( $attributes );

		if ( ! isset( $align_class_and_style['class'] ) ) {
			return $content;
		}

		// Find the first tag.
		$first_tag = '<[^<>]+>';
		$matches   = array();
		preg_match( $first_tag, $content, $matches );

		// If there is a tag, but it doesn't have a class attribute, add the class attribute.
		if ( isset( $matches[0] ) && strpos( $matches[0], ' class=' ) === false ) {
			$pattern_before_tag_closing = '/.+?(?=>)/';
			return preg_replace( $pattern_before_tag_closing, '$0 class="' . $align_class_and_style['class'] . '"', $content, 1 );
		}

		// If there is a tag, and it has a class already, add the class attribute.
		$pattern_get_class = '/(?<=class=\"|\')[^"|\']+(?=\"|\')/';
		return preg_replace( $pattern_get_class, '$0 ' . $align_class_and_style['class'], $content, 1 );
	}


	/**
	 * Filter products by stock status when as query param there is "filter_stock_status"
	 *
	 * @param array $meta_query Meta query.
	 * @return array
	 */
	public function filter_products_by_stock( $meta_query ) {
		global $wp_query;

		if (
			is_admin() ||
			! $wp_query->is_main_query() ||
			! isset( $_GET[ self::FILTER_PRODUCTS_BY_STOCK_QUERY_PARAM ] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) {
			return $meta_query;
		}

		$stock_status = array_keys( wc_get_product_stock_status_options() );
		$values       = sanitize_text_field( wp_unslash( $_GET[ self::FILTER_PRODUCTS_BY_STOCK_QUERY_PARAM ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$values_to_array = explode( ',', $values );

		$filtered_values = array_filter(
			$values_to_array,
			function( $value ) use ( $stock_status ) {
				return in_array( $value, $stock_status, true );
			}
		);

		if ( ! empty( $filtered_values ) ) {

			$meta_query[] = array(
				'key'     => '_stock_status',
				'value'   => $filtered_values,
				'compare' => 'IN',
			);
		}
		return $meta_query;
	}

}
