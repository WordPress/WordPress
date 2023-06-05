<?php
namespace Automattic\WooCommerce\Blocks\Templates;

/**
 * AbstractTemplateCompatibility class.
 *
 * To bridge the gap on compatibility with PHP hooks and blockified templates.
 *
 * @internal
 */
abstract class AbstractTemplateCompatibility {
	/**
	 * The data of supported hooks, containing the hook name, the block name,
	 * position, and the callbacks.
	 *
	 * @var array $hook_data The hook data.
	 */
	protected $hook_data;

	/**
	 * Initialization method.
	 */
	public function init() {
		if ( ! wc_current_theme_is_fse_theme() ) {
			return;
		}

		$this->set_hook_data();

		add_filter(
			'render_block_data',
			function( $parsed_block, $source_block, $parent_block ) {
				/**
				* Filter to disable the compatibility layer for the blockified templates.
				*
				* This hook allows to disable the compatibility layer for the blockified templates.
				*
				* @since TBD
				* @param boolean.
				*/
				$is_disabled_compatility_layer = apply_filters( 'woocommerce_disable_compatibility_layer', false );

				if ( $is_disabled_compatility_layer ) {
					return $parsed_block;
				}

				return $this->update_render_block_data( $parsed_block, $source_block, $parent_block );

			},
			10,
			3
		);

		add_filter(
			'render_block',
			function ( $block_content, $block ) {
				/**
				* Filter to disable the compatibility layer for the blockified templates.
				*
				* This hook allows to disable the compatibility layer for the blockified.
				*
				* @since TBD
				* @param boolean.
				*/
				$is_disabled_compatility_layer = apply_filters( 'woocommerce_disable_compatibility_layer', false );

				if ( $is_disabled_compatility_layer ) {
					return $block_content;
				}

				return $this->inject_hooks( $block_content, $block );
			},
			10,
			2
		);
	}

	/**
	 * Update the render block data to inject our custom attribute needed to
	 * determine which blocks belong to an inherited Products block.
	 *
	 * @param array         $parsed_block The block being rendered.
	 * @param array         $source_block An un-modified copy of $parsed_block, as it appeared in the source content.
	 * @param WP_Block|null $parent_block If this is a nested block, a reference to the parent block.
	 *
	 * @return array
	 */
	abstract public function update_render_block_data( $parsed_block, $source_block, $parent_block );

	/**
	 * Inject hooks to rendered content of corresponding blocks.
	 *
	 * @param mixed $block_content The rendered block content.
	 * @param mixed $block         The parsed block data.
	 * @return string
	 */
	abstract public function inject_hooks( $block_content, $block );

	/**
	 * The hook data to inject to the rendered content of blocks. This also
	 * contains hooked functions that will be removed by remove_default_hooks.
	 *
	 * The array format:
	 * [
	 *   <hook-name> => [
	 *     block_name => <block-name>,
	 *     position => before|after,
	 *     hooked => [
	 *       <function-name> => <priority>,
	 *        ...
	 *     ],
	 *  ],
	 * ]
	 * Where:
	 * - hook-name is the name of the hook that will be replaced.
	 * - block-name is the name of the block that will replace the hook.
	 * - position is the position of the block relative to the hook.
	 * - hooked is an array of functions hooked to the hook that will be
	 *   replaced. The key is the function name and the value is the
	 *   priority.
	 */
	abstract protected function set_hook_data();


	/**
	 * Remove the default callback added by WooCommerce. We replaced these
	 * callbacks by blocks so we have to remove them to prevent duplicated
	 * content.
	 */
	protected function remove_default_hooks() {
		foreach ( $this->hook_data as $hook => $data ) {
			if ( ! isset( $data['hooked'] ) ) {
				continue;
			}
			foreach ( $data['hooked'] as $callback => $priority ) {
				remove_action( $hook, $callback, $priority );
			}
		}

		/**
		 * When extensions implement their equivalent blocks of the template
		 * hook functions, they can use this filter to register their old hooked
		 * data here, so in the blockified template, the old hooked functions
		 * can be removed in favor of the new blocks while keeping the old
		 * hooked functions working in classic templates.
		 *
		 * Accepts an array of hooked data. The array should be in the following
		 * format:
		 * [
		 *   [
		 *     hook => <hook-name>,
		 *     function => <function-name>,
		 *     priority => <priority>,
		 *  ],
		 *  ...
		 * ]
		 * Where:
		 * - hook-name is the name of the hook that have the functions hooked to.
		 * - function-name is the hooked function name.
		 * - priority is the priority of the hooked function.
		 *
		 * @since 9.5.0
		 * @param array $data Additional hooked data. Default to empty
		 */
		$additional_hook_data = apply_filters( 'woocommerce_blocks_hook_compatibility_additional_data', array() );

		if ( empty( $additional_hook_data ) || ! is_array( $additional_hook_data ) ) {
			return;
		}

		foreach ( $additional_hook_data as $data ) {
			if ( ! isset( $data['hook'], $data['function'], $data['priority'] ) ) {
				continue;
			}
			remove_action( $data['hook'], $data['function'], $data['priority'] );
		}
	}

	/**
	 * Get the buffer content of the hooks to append/prepend to render content.
	 *
	 * @param array  $hooks    The hooks to be rendered.
	 * @param string $position The position of the hooks.
	 *
	 * @return string
	 */
	protected function get_hooks_buffer( $hooks, $position ) {
		ob_start();
		foreach ( $hooks as $hook => $data ) {
			if ( $data['position'] === $position ) {
				/**
				 * Action to render the content of a hook.
				 *
				 * @since 9.5.0
				 */
				do_action( $hook );
			}
		}
		return ob_get_clean();
	}
}
