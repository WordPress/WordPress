<?php
namespace Automattic\WooCommerce\Blocks\Templates;

/**
 * SingleProductTemplateCompatibility class.
 *
 * To bridge the gap on compatibility with PHP hooks and Single Product templates.
 *
 * @internal
 */
class SingleProductTemplateCompatibility extends AbstractTemplateCompatibility {
	const IS_FIRST_BLOCK = '__wooCommerceIsFirstBlock';
	const IS_LAST_BLOCK  = '__wooCommerceIsLastBlock';


	/**
	 * Inject hooks to rendered content of corresponding blocks.
	 *
	 * @param mixed $block_content The rendered block content.
	 * @param mixed $block         The parsed block data.
	 * @return string
	 */
	public function inject_hooks( $block_content, $block ) {
		if ( ! is_product() ) {
			return $block_content;
		}

		$this->remove_default_hooks();

		$block_name = $block['blockName'];

		$block_hooks = array_filter(
			$this->hook_data,
			function( $hook ) use ( $block_name ) {
				return $hook['block_name'] === $block_name;
			}
		);

		$first_or_last_block_content = $this->inject_hook_to_first_and_last_blocks( $block_content, $block, $block_hooks );

		if ( isset( $first_or_last_block_content ) ) {
			return $first_or_last_block_content;
		}

		return sprintf(
			'%1$s%2$s%3$s',
			$this->get_hooks_buffer( $block_hooks, 'before' ),
			$block_content,
			$this->get_hooks_buffer( $block_hooks, 'after' )
		);
	}

	/**
	 * Inject custom hooks to the first and last blocks.
	 * Since that there is a custom logic for the first and last block, we have to inject the hooks manually.
	 * The first block supports the following hooks:
	 * woocommerce_before_single_product
	 * woocommerce_before_single_product_summary
	 * woocommerce_single_product_summary
	 *
	 * The last block supports the following hooks:
	 * woocommerce_after_single_product
	 *
	 * @param mixed $block_content The rendered block content.
	 * @param mixed $block         The parsed block data.
	 * @param array $block_hooks   The hooks that should be injected to the block.
	 * @return string
	 */
	private function inject_hook_to_first_and_last_blocks( $block_content, $block, $block_hooks ) {
		$first_block_hook = array(
			'before' => array(
				'woocommerce_before_main_content'    => $this->hook_data['woocommerce_before_main_content'],
				'woocommerce_before_single_product'  => $this->hook_data['woocommerce_before_single_product'],
				'woocommerce_before_single_product_summary' => $this->hook_data['woocommerce_before_single_product_summary'],
				'woocommerce_single_product_summary' => $this->hook_data['woocommerce_single_product_summary'],
			),
			'after'  => array(),
		);

		$last_block_hook = array(
			'before' => array(),
			'after'  => array(
				'woocommerce_after_single_product' => $this->hook_data['woocommerce_after_single_product'],
				'woocommerce_after_main_content'   => $this->hook_data['woocommerce_after_main_content'],
				'woocommerce_sidebar'              => $this->hook_data['woocommerce_sidebar'],
			),
		);

		if ( isset( $block['attrs'][ self::IS_FIRST_BLOCK ] ) && isset( $block['attrs'][ self::IS_LAST_BLOCK ] ) ) {
			return sprintf(
				'%1$s%2$s',
				$this->inject_hooks_after_the_wrapper(
					$block_content,
					array_merge(
						$first_block_hook['before'],
						$block_hooks,
						$last_block_hook['before']
					)
				),
				$this->get_hooks_buffer(
					array_merge(
						$first_block_hook['after'],
						$block_hooks,
						$last_block_hook['after']
					),
					'after'
				)
			);
		}

		if ( isset( $block['attrs'][ self::IS_FIRST_BLOCK ] ) ) {
			return sprintf(
				'%1$s%2$s',
				$this->inject_hooks_after_the_wrapper(
					$block_content,
					array_merge(
						$first_block_hook['before'],
						$block_hooks
					)
				),
				$this->get_hooks_buffer(
					array_merge(
						$first_block_hook['after'],
						$block_hooks
					),
					'after'
				)
			);
		}

		if ( isset( $block['attrs'][ self::IS_LAST_BLOCK ] ) ) {
			return sprintf(
				'%1$s%2$s%3$s',
				$this->get_hooks_buffer(
					array_merge(
						$last_block_hook['before'],
						$block_hooks
					),
					'before'
				),
				$block_content,
				$this->get_hooks_buffer(
					array_merge(
						$block_hooks,
						$last_block_hook['after']
					),
					'after'
				)
			);
		}
	}

	/**
	 * Update the render block data to inject our custom attribute needed to
	 * determine which is the first block of the Single Product Template.
	 *
	 * @param array         $parsed_block The block being rendered.
	 * @param array         $source_block An un-modified copy of $parsed_block, as it appeared in the source content.
	 * @param WP_Block|null $parent_block If this is a nested block, a reference to the parent block.
	 *
	 * @return array
	 */
	public function update_render_block_data( $parsed_block, $source_block, $parent_block ) {
		return $parsed_block;
	}

	/**
	 * Set supported hooks.
	 */
	protected function set_hook_data() {
		$this->hook_data = array(
			'woocommerce_before_main_content'           => array(
				'block_name' => '',
				'position'   => 'before',
				'hooked'     => array(
					'woocommerce_output_content_wrapper' => 10,
					'woocommerce_breadcrumb'             => 20,
				),
			),
			'woocommerce_after_main_content'            => array(
				'block_name' => '',
				'position'   => 'after',
				'hooked'     => array(
					'woocommerce_output_content_wrapper_end' => 10,
				),
			),
			'woocommerce_sidebar'                       => array(
				'block_name' => '',
				'position'   => 'after',
				'hooked'     => array(
					'woocommerce_get_sidebar' => 10,
				),
			),
			'woocommerce_before_single_product'         => array(
				'block_name' => '',
				'position'   => 'before',
				'hooked'     => array(
					'woocommerce_output_all_notices' => 10,
				),
			),
			'woocommerce_before_single_product_summary' => array(
				'block_name' => '',
				'position'   => 'before',
				'hooked'     => array(
					'woocommerce_show_product_sale_flash' => 10,
					'woocommerce_show_product_images'     => 20,
				),
			),
			'woocommerce_single_product_summary'        => array(
				'block_name' => '',
				'position'   => 'before',
				'hooked'     => array(
					'woocommerce_template_single_title'   => 5,
					'woocommerce_template_single_rating'  => 10,
					'woocommerce_template_single_price'   => 10,
					'woocommerce_template_single_excerpt' => 20,
					'woocommerce_template_single_add_to_cart' => 30,
					'woocommerce_template_single_meta'    => 40,
					'woocommerce_template_single_sharing' => 50,
				),
			),
			'woocommerce_after_single_product'          => array(
				'block_name' => '',
				'position'   => 'after',
				'hooked'     => array(),
			),
			'woocommerce_product_meta_start'            => array(
				'block_name' => 'woocommerce/product-meta',
				'position'   => 'before',
				'hooked'     => array(),
			),
			'woocommerce_product_meta_end'              => array(
				'block_name' => 'woocommerce/product-meta',
				'position'   => 'after',
				'hooked'     => array(),
			),
			'woocommerce_share'                         => array(
				'block_name' => 'woocommerce/product-details',
				'position'   => 'before',
				'hooked'     => array(),
			),
			'woocommerce_after_single_product_summary'  => array(
				'block_name' => 'woocommerce/product-details',
				'position'   => 'before',
				'hooked'     => array(
					'woocommerce_output_product_data_tabs' => 10,
					'woocommerce_upsell_display'           => 15,
					'woocommerce_output_related_products'  => 20,
				),
			),
		);
	}

	/**
	 * Add compatibility layer to the first and last block of the Single Product Template.
	 *
	 * @param string $template_content Template.
	 * @return string
	 */
	public static function add_compatibility_layer( $template_content ) {
		$wrapped_blocks = self::wrap_single_product_template( $template_content );
		$template       = self::inject_custom_attributes_to_first_and_last_block_single_product_template( $wrapped_blocks );

		return array_reduce(
			$template,
			function( $carry, $item ) {
				if ( is_array( $item ) ) {
					return $carry . serialize_blocks( $item );
				}
				return $carry . serialize_block( $item );
			},
			''
		);

	}

	/**
	 * For compatibility reason, we need to wrap the Single Product template in a div with specific class.
	 * For more details, see https://github.com/woocommerce/woocommerce-blocks/issues/8314.
	 *
	 * @param string $template_content Template Content.
	 * @return array Wrapped template content inside a div.
	 */
	private static function wrap_single_product_template( $template_content ) {
		$parsed_blocks  = parse_blocks( $template_content );
		$grouped_blocks = self::group_blocks( $parsed_blocks );

		$single_product_template_blocks = array( 'woocommerce/product-image-gallery', 'woocommerce/product-details', 'woocommerce/add-to-cart-form', 'woocommerce/product-meta', 'woocommerce/product-price', 'woocommerce/breadcrumbs' );

		$wrapped_blocks = array_map(
			function( $blocks ) use ( $single_product_template_blocks ) {
				if ( 'core/template-part' === $blocks[0]['blockName'] ) {
					return $blocks;
				}

				$has_single_product_template_blocks = self::has_single_product_template_blocks( $blocks, $single_product_template_blocks );

				if ( $has_single_product_template_blocks ) {
					$wrapped_block = self::create_wrap_block_group( $blocks );
					return array( $wrapped_block[0] );
				}
				return $blocks;
			},
			$grouped_blocks
		);
		return $wrapped_blocks;
	}

	/**
	 * Add custom attributes to the first group block and last group block that wrap Single Product Template blocks.
	 *
	 * @param array $wrapped_blocks Wrapped blocks.
	 * @return array
	 */
	private static function inject_custom_attributes_to_first_and_last_block_single_product_template( $wrapped_blocks ) {
		$template_with_custom_attributes = array_reduce(
			$wrapped_blocks,
			function( $carry, $item ) {

				$index          = $carry['index'];
				$carry['index'] = $carry['index'] + 1;
				$block          = $item[0];

				if ( 'core/template-part' === $block['blockName'] || self::is_custom_html( $block ) ) {
					$carry['template'][] = $block;
					return $carry;
				}

				if ( '' === $carry['first_block']['index'] ) {
					$block['attrs'][ self::IS_FIRST_BLOCK ] = true;
					$carry['first_block']['index']          = $index;
				}

				if ( '' !== $carry['last_block']['index'] ) {
					$index_element                         = $carry['last_block']['index'];
					$carry['last_block']['index']          = $index;
					$block['attrs'][ self::IS_LAST_BLOCK ] = true;
					unset( $carry['template'][ $index_element ]['attrs'][ self::IS_LAST_BLOCK ] );

					$carry['template'][] = $block;

					return $carry;
				}

				$block['attrs'][ self::IS_LAST_BLOCK ] = true;
				$carry['last_block']['index']          = $index;

				$carry['template'][] = $block;

				return $carry;
			},
			array(
				'template'    => array(),
				'first_block' => array(
					'index' => '',
				),
				'last_block'  => array(
					'index' => '',
				),
				'index'       => 0,
			)
		);

		return array( $template_with_custom_attributes['template'] );
	}

	/**
	 * Wrap all the blocks inside the template in a group block.
	 *
	 * @param array $blocks Array of parsed block objects.
	 * @return array Group block with the blocks inside.
	 */
	private static function create_wrap_block_group( $blocks ) {
		$serialized_blocks = serialize_blocks( $blocks );

		$new_block = parse_blocks(
			sprintf(
				'<!-- wp:group {"className":"woocommerce product"} -->
				<div class="wp-block-group woocommerce product">
					%1$s
				</div>
			<!-- /wp:group -->',
				$serialized_blocks
			)
		);

		$new_block['innerBlocks'] = $blocks;

		return $new_block;

	}

	/**
	 * Check if the Single Product template has a single product template block:
	 * woocommerce/product-gallery-image, woocommerce/product-details, woocommerce/add-to-cart-form]
	 *
	 * @param array $parsed_blocks Array of parsed block objects.
	 * @param array $single_product_template_blocks Array of single product template blocks.
	 * @return bool True if the template has a single product template block, false otherwise.
	 */
	private static function has_single_product_template_blocks( $parsed_blocks, $single_product_template_blocks ) {
		$found = false;

		foreach ( $parsed_blocks as $block ) {
			if ( isset( $block['blockName'] ) && in_array( $block['blockName'], $single_product_template_blocks, true ) ) {
				$found = true;
				break;
			}
			$found = self::has_single_product_template_blocks( $block['innerBlocks'], $single_product_template_blocks );
			if ( $found ) {
				break;
			}
		}
		return $found;
	}


	/**
	 * Group blocks in this way:
	 * B1 + TP1 + B2 + B3 + B4 + TP2 + B5
	 * (B = Block, TP = Template Part)
	 * becomes:
	 * [[B1], [TP1], [B2, B3, B4], [TP2], [B5]]
	 *
	 * @param array $parsed_blocks Array of parsed block objects.
	 * @return array Array of blocks grouped by template part.
	 */
	private static function group_blocks( $parsed_blocks ) {
		return array_reduce(
			$parsed_blocks,
			function( $carry, $block ) {
				if ( 'core/template-part' === $block['blockName'] ) {
					$carry[] = array( $block );
					return $carry;
				}
				$last_element_index = count( $carry ) - 1;
				if ( isset( $carry[ $last_element_index ][0]['blockName'] ) && 'core/template-part' !== $carry[ $last_element_index ][0]['blockName'] ) {
					$carry[ $last_element_index ][] = $block;
					return $carry;
				}
				$carry[] = array( $block );
				return $carry;
			},
			array()
		);
	}

	/**
	 * Inject the hooks after the div wrapper.
	 *
	 * @param string $block_content Block Content.
	 * @param array  $hooks Hooks to inject.
	 * @return array
	 */
	private function inject_hooks_after_the_wrapper( $block_content, $hooks ) {
		$closing_tag_position = strpos( $block_content, '>' );

		return substr_replace(
			$block_content,
			$this->get_hooks_buffer(
				$hooks,
				'before'
			),
			// Add 1 to the position to inject the content after the closing tag.
			$closing_tag_position + 1,
			0
		);
	}


	/**
	 * Plain custom HTML block is parsed as block with an empty blockName with a filled innerHTML.
	 *
	 * @param array $block Parse block.
	 * @return bool
	 */
	private static function is_custom_html( $block ) {
		return empty( $block['blockName'] ) && ! empty( $block['innerHTML'] );
	}
}
