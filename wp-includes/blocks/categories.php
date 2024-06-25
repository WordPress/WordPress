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
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the categories list/dropdown markup.
 */
function render_block_core_categories( $attributes ) {
	static $block_id = 0;
	++$block_id;

	$args = array(
		'echo'         => false,
		'hierarchical' => ! empty( $attributes['showHierarchy'] ),
		'orderby'      => 'name',
		'show_count'   => ! empty( $attributes['showPostCounts'] ),
		'title_li'     => '',
		'hide_empty'   => empty( $attributes['showEmpty'] ),
	);
	if ( ! empty( $attributes['showOnlyTopLevel'] ) && $attributes['showOnlyTopLevel'] ) {
		$args['parent'] = 0;
	}

	if ( ! empty( $attributes['displayAsDropdown'] ) ) {
		$id                       = 'wp-block-categories-' . $block_id;
		$args['id']               = $id;
		$args['show_option_none'] = __( 'Select Category' );
		$wrapper_markup           = '<div %1$s><label class="screen-reader-text" for="' . esc_attr( $id ) . '">' . __( 'Categories' ) . '</label>%2$s</div>';
		$items_markup             = wp_dropdown_categories( $args );
		$type                     = 'dropdown';

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
		$wrapper_markup = '<ul %1$s>%2$s</ul>';
		$items_markup   = wp_list_categories( $args );
		$type           = 'list';
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
			if ( dropdown.options[ dropdown.selectedIndex ].value > 0 ) {
				location.href = "<?php echo esc_url( home_url() ); ?>/?cat=" + dropdown.options[ dropdown.selectedIndex ].value;
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
