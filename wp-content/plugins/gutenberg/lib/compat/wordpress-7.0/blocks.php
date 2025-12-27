<?php
/**
 * Compatibility shims for block APIs for WordPress 7.0.
 *
 * @package gutenberg
 */

if ( ! function_exists( 'gutenberg_resolve_pattern_blocks' ) ) {
	/**
	 * Replaces patterns in a block tree with their content.
	 *
	 * @since 6.6.0
	 * @since 7.0.0 Adds metadata to attributes of single-pattern container blocks.
	 *
	 * @param array $blocks An array blocks.
	 *
	 * @return array An array of blocks with patterns replaced by their content.
	 */
	function gutenberg_resolve_pattern_blocks( $blocks ) {
		static $inner_content;
		// Keep track of seen references to avoid infinite loops.
		static $seen_refs = array();
		$i                = 0;
		while ( $i < count( $blocks ) ) {
			if ( 'core/pattern' === $blocks[ $i ]['blockName'] ) {
				$attrs = $blocks[ $i ]['attrs'];

				if ( empty( $attrs['slug'] ) ) {
					++$i;
					continue;
				}

				$slug = $attrs['slug'];

				if ( isset( $seen_refs[ $slug ] ) ) {
					// Skip recursive patterns.
					array_splice( $blocks, $i, 1 );
					continue;
				}

				$registry = WP_Block_Patterns_Registry::get_instance();
				$pattern  = $registry->get_registered( $slug );

				// Skip unknown patterns.
				if ( ! $pattern ) {
					++$i;
					continue;
				}
				//////////////////////////////
				// START CORE MODIFICATIONS //
				//////////////////////////////
				$blocks_to_insert = parse_blocks( trim( $pattern['content'] ) );

				/*
				* For single-root patterns, add the pattern name to make this a pattern instance in the editor.
				* If the pattern has metadata, merge it with the existing metadata.
				*/
				if ( count( $blocks_to_insert ) === 1 ) {
					$block_metadata                = $blocks_to_insert[0]['attrs']['metadata'] ?? array();
					$block_metadata['patternName'] = $slug;

					/*
					* Merge pattern metadata with existing block metadata.
					* Pattern metadata takes precedence, but existing block metadata
					* is preserved as a fallback when the pattern doesn't define that field.
					* Only the defined fields (name, description, categories) are updated;
					* other metadata keys are preserved.
					*/
					foreach ( array(
						'name'        => 'title', // 'title' is the field in the pattern object 'name' is the field in the block metadata.
						'description' => 'description',
						'categories'  => 'categories',
					) as $key => $pattern_key ) {
						$value = $pattern[ $pattern_key ] ?? $block_metadata[ $key ] ?? null;
						if ( $value ) {
							$block_metadata[ $key ] = 'categories' === $key && is_array( $value )
								? array_map( 'sanitize_text_field', $value )
								: sanitize_text_field( $value );
						}
					}

					$blocks_to_insert[0]['attrs']['metadata'] = $block_metadata;
				}
				//////////////////////////////
				// END CORE MODIFICATIONS //
				//////////////////////////////

				$seen_refs[ $slug ] = true;
				$prev_inner_content = $inner_content;
				$inner_content      = null;
				$blocks_to_insert   = gutenberg_resolve_pattern_blocks( $blocks_to_insert );
				$inner_content      = $prev_inner_content;
				unset( $seen_refs[ $slug ] );
				array_splice( $blocks, $i, 1, $blocks_to_insert );

				// If we have inner content, we need to insert nulls in the
				// inner content array, otherwise serialize_blocks will skip
				// blocks.
				if ( $inner_content ) {
					$null_indices  = array_keys( $inner_content, null, true );
					$content_index = $null_indices[ $i ];
					$nulls         = array_fill( 0, count( $blocks_to_insert ), null );
					array_splice( $inner_content, $content_index, 1, $nulls );
				}

				// Skip inserted blocks.
				$i += count( $blocks_to_insert );
			} else {
				if ( ! empty( $blocks[ $i ]['innerBlocks'] ) ) {
					$prev_inner_content           = $inner_content;
					$inner_content                = $blocks[ $i ]['innerContent'];
					$blocks[ $i ]['innerBlocks']  = gutenberg_resolve_pattern_blocks(
						$blocks[ $i ]['innerBlocks']
					);
					$blocks[ $i ]['innerContent'] = $inner_content;
					$inner_content                = $prev_inner_content;
				}
				++$i;
			}
		}
		return $blocks;
	}
}
