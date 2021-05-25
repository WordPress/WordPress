<?php
/**
 * Server-side rendering of the `core/legacy-widget` block.
 *
 * @package WordPress
 */

/**
 * Renders the 'core/legacy-widget' block.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Rendered block.
 */
function render_block_core_legacy_widget( $attributes ) {
	if ( isset( $attributes['id'] ) ) {
		$sidebar_id = gutenberg_find_widgets_sidebar( $attributes['id'] );
		return gutenberg_render_widget( $attributes['id'], $sidebar_id );
	}

	if ( ! isset( $attributes['idBase'] ) ) {
		return '';
	}

	$widget_object = gutenberg_get_widget_object( $attributes['idBase'] );

	if ( ! $widget_object ) {
		return '';
	}

	if ( isset( $attributes['instance']['encoded'], $attributes['instance']['hash'] ) ) {
		$serialized_instance = base64_decode( $attributes['instance']['encoded'] );
		if ( wp_hash( $serialized_instance ) !== $attributes['instance']['hash'] ) {
			return '';
		}
		$instance = unserialize( $serialized_instance );
	} else {
		$instance = array();
	}

	ob_start();
	the_widget( get_class( $widget_object ), $instance );
	return ob_get_clean();
}

/**
 * Registers the 'core/legacy-widget' block.
 */
function register_block_core_legacy_widget() {
	register_block_type_from_metadata(
		__DIR__ . '/legacy-widget',
		array(
			'render_callback' => 'render_block_core_legacy_widget',
		)
	);
}

add_action( 'init', 'register_block_core_legacy_widget', 20 );

/**
 * Intercepts any request with legacy-widget-preview in the query param and, if
 * set, renders a page containing a preview of the requested Legacy Widget
 * block.
 */
function handle_legacy_widget_preview_iframe() {
	if ( empty( $_GET['legacy-widget-preview'] ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	define( 'IFRAME_REQUEST', true );

	?>
	<!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="profile" href="https://gmpg.org/xfn/11" />
		<?php wp_head(); ?>
		<style>
			/* Reset theme styles */
			html, body, #page, #content {
				background: #FFF !important;
				padding: 0 !important;
				margin: 0 !important;
			}
		</style>
	</head>
	<body <?php body_class(); ?>>
		<div id="page" class="site">
			<div id="content" class="site-content">
				<?php
				$registry = WP_Block_Type_Registry::get_instance();
				$block    = $registry->get_registered( 'core/legacy-widget' );
				echo $block->render( $_GET['legacy-widget-preview'] );
				?>
			</div><!-- #content -->
		</div><!-- #page -->
		<?php wp_footer(); ?>
	</body>
	</html>
	<?php

	exit;
}

add_action( 'init', 'handle_legacy_widget_preview_iframe', 21 );
