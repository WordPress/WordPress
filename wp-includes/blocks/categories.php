<?php
/**
 * Server-side rendering of the `core/categories` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/categories` block on server.
 *
 * @since 5.0.0
 * @since 6.7.0 Enable client-side rendering if enhancedPagination context is true.
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the categories list/dropdown markup.
 */
function render_block_core_categories( $attributes, $content, $block ) {
	static $block_id = 0;
	++$block_id;

	$taxonomy = get_taxonomy( $attributes['taxonomy'] );

	$args = array(
		'echo'         => false,
		'hierarchical' => ! empty( $attributes['showHierarchy'] ),
		'orderby'      => 'name',
		'show_count'   => ! empty( $attributes['showPostCounts'] ),
		'taxonomy'     => $attributes['taxonomy'],
		'title_li'     => '',
		'hide_empty'   => empty( $attributes['showEmpty'] ),
	);
	if ( ! empty( $attributes['showOnlyTopLevel'] ) && $attributes['showOnlyTopLevel'] ) {
		$args['parent'] = 0;
	}

	if ( ! empty( $attributes['displayAsDropdown'] ) ) {
		$id                       = 'wp-block-categories-' . $block_id;
		$args['id']               = $id;
		$args['name']             = $taxonomy->query_var;
		$args['value_field']      = 'slug';
		$args['show_option_none'] = sprintf(
			/* translators: %s: taxonomy's singular name */
			__( 'Select %s' ),
			$taxonomy->labels->singular_name
		);

		$show_label     = empty( $attributes['showLabel'] ) ? ' screen-reader-text' : '';
		$default_label  = $taxonomy->label;
		$label_text     = ! empty( $attributes['label'] ) ? wp_kses_post( $attributes['label'] ) : $default_label;
		$wrapper_markup = '<div %1$s><label class="wp-block-categories__label' . $show_label . '" for="' . esc_attr( $id ) . '">' . $label_text . '</label>%2$s</div>';
		$items_markup   = wp_dropdown_categories( $args );
		$type           = 'dropdown';

		if ( ! is_admin() ) {
			// Inject the dropdown script immediately after the select dropdown.
			$items_markup = preg_replace(
				'#(?<=</select>)#',
				build_dropdown_script_block_core_categories( $id ),
				$items_markup,
				1
			);
		}
	} else {
		$args['show_option_none'] = $taxonomy->labels->no_terms;

		$wrapper_markup = '<ul %1$s>%2$s</ul>';
		$items_markup   = wp_list_categories( $args );
		$type           = 'list';

		if ( ! empty( $block->context['enhancedPagination'] ) ) {
			$p = new WP_HTML_Tag_Processor( $items_markup );
			while ( $p->next_tag( 'a' ) ) {
				$p->set_attribute( 'data-wp-on--click', 'core/query::actions.navigate' );
			}
			$items_markup = $p->get_updated_html();
		}
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => "wp-block-categories-{$type}" ) );

	return sprintf(
		$wrapper_markup,
		$wrapper_attributes,
		$items_markup
	);
}

/**
 * Generates the inline script for a categories dropdown field.
 *
 * @since 5.0.0
 *
 * @param string $dropdown_id ID of the dropdown field.
 *
 * @return string Returns the dropdown onChange redirection script.
 */
function build_dropdown_script_block_core_categories( $dropdown_id ) {
	ob_start();
	?>
	<script>
	( function() {
		var dropdown = document.getElementById( '<?php echo esc_js( $dropdown_id ); ?>' );
		function onCatChange() {
			if ( dropdown.options[ dropdown.selectedIndex ].value !== -1 ) {
				location.href = "<?php echo esc_url( home_url() ); ?>/?" + dropdown.name + '=' + dropdown.options[ dropdown.selectedIndex ].value;
			}
		}
		dropdown.onchange = onCatChange;
	})();
	</script>
	<?php
	return wp_get_inline_script_tag( str_replace( array( '<script>', '</script>' ), '', ob_get_clean() ) );
}

/**
 * Registers the `core/categories` block on server.
 *
 * @since 5.0.0
 */
function register_block_core_categories() {
	register_block_type_from_metadata(
		__DIR__ . '/categories',
		array(
			'render_callback' => 'render_block_core_categories',
		)
	);
}
add_action( 'init', 'register_block_core_categories' );
