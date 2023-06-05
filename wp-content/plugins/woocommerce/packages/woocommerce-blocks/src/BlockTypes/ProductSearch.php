<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * ProductSearch class.
 */
class ProductSearch extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-search';

	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @param string $key Data to get, or default to everything.
	 * @return null
	 */
	protected function get_block_type_script( $key = null ) {
		return null;
	}

	/**
	 * Render the block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		static $instance_id = 0;

		$attributes = wp_parse_args(
			$attributes,
			array(
				'hasLabel'    => true,
				'align'       => '',
				'className'   => '',
				'label'       => __( 'Search', 'woocommerce' ),
				'placeholder' => __( 'Search products…', 'woocommerce' ),
			)
		);

		/**
		 * Product Search event.
		 *
		 * Listens for product search form submission, and on submission fires a WP Hook named
		 * `experimental__woocommerce_blocks-product-search`. This can be used by tracking extensions such as Google
		 * Analytics to track searches.
		 */
		$this->asset_api->add_inline_script(
			'wp-hooks',
			"
			window.addEventListener( 'DOMContentLoaded', () => {
				const forms = document.querySelectorAll( '.wc-block-product-search form' );

				for ( const form of forms ) {
					form.addEventListener( 'submit', ( event ) => {
						const field = form.querySelector( '.wc-block-product-search__field' );

						if ( field && field.value ) {
							wp.hooks.doAction( 'experimental__woocommerce_blocks-product-search', { event: event, searchTerm: field.value } );
						}
					} );
				}
			} );
			",
			'after'
		);

		$input_id           = 'wc-block-search__input-' . ( ++$instance_id );
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => implode(
					' ',
					array_filter(
						[
							'wc-block-product-search',
							$attributes['align'] ? 'align' . $attributes['align'] : '',
						]
					)
				),
			)
		);

		$label_markup = $attributes['hasLabel'] ? sprintf(
			'<label for="%s" class="wc-block-product-search__label">%s</label>',
			esc_attr( $input_id ),
			esc_html( $attributes['label'] )
		) : sprintf(
			'<label for="%s" class="wc-block-product-search__label screen-reader-text">%s</label>',
			esc_attr( $input_id ),
			esc_html( $attributes['label'] )
		);

		$input_markup  = sprintf(
			'<input type="search" id="%s" class="wc-block-product-search__field" placeholder="%s" name="s" />',
			esc_attr( $input_id ),
			esc_attr( $attributes['placeholder'] )
		);
		$button_markup = sprintf(
			'<button type="submit" class="wc-block-product-search__button" aria-label="%s">
				<svg aria-hidden="true" role="img" focusable="false" class="dashicon dashicons-arrow-right-alt2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
					<path d="M6 15l5-5-5-5 1-2 7 7-7 7z" />
				</svg>
			</button>',
			esc_attr__( 'Search', 'woocommerce' )
		);

		$field_markup = '
			<div class="wc-block-product-search__fields">
				' . $input_markup . $button_markup . '
				<input type="hidden" name="post_type" value="product" />
			</div>
		';

		return sprintf(
			'<div %s><form role="search" method="get" action="%s">%s</form></div>',
			$wrapper_attributes,
			esc_url( home_url( '/' ) ),
			$label_markup . $field_markup
		);
	}

	/**
	 * Extra data passed through from server to client for block.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 *                           Note, this will be empty in the editor context when the block is
	 *                           not in the post content on editor load.
	 */
	protected function enqueue_data( array $attributes = [] ) {
		parent::enqueue_data( $attributes );

		$gutenberg_version = '';

		if ( is_plugin_active( 'gutenberg/gutenberg.php' ) ) {
			if ( defined( 'GUTENBERG_VERSION' ) ) {
				$gutenberg_version = GUTENBERG_VERSION;
			}

			if ( ! $gutenberg_version ) {
				$gutenberg_data    = get_file_data(
					WP_PLUGIN_DIR . '/gutenberg/gutenberg.php',
					array( 'Version' => 'Version' )
				);
				$gutenberg_version = $gutenberg_data['Version'];
			}
		}

		$this->asset_data_registry->add(
			'isBlockVariationAvailable',
			version_compare( get_bloginfo( 'version' ), '6.1', '>=' ) || version_compare( $gutenberg_version, '13.4', '>=' )
		);
	}
}
