<?php

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-canvas' );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title><?php echo wp_get_document_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></title>
	<?php endif; ?>
	<?php wp_head(); ?>
	<?php

	// Keep the following line after `wp_head()` call, to ensure it's not overridden by another templates.
	Utils::print_unescaped_internal_string( Utils::get_meta_viewport( 'canvas' ) );
	?>
</head>
<body <?php body_class(); ?>>
	<?php
	wp_body_open();

	/**
	 * Before canvas page template content.
	 *
	 * Fires before the content of Elementor canvas page template.
	 *
	 * @since 1.0.0
	 */
	do_action( 'elementor/page_templates/canvas/before_content' );

	$module = apply_filters( 'elementor/render_mode/module', 'page-templates' );

	\Elementor\Plugin::$instance->modules_manager->get_modules( $module )->print_content();

	/**
	 * After canvas page template content.
	 *
	 * Fires after the content of Elementor canvas page template.
	 *
	 * @since 1.0.0
	 */
	do_action( 'elementor/page_templates/canvas/after_content' );

	wp_footer();
	?>
	</body>
</html>
