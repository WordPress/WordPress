<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Views
 *
 * @uses    string                 $paper_id                  The ID of the paper.
 * @uses    string                 $paper_id_prefix           The ID prefix of the paper.
 * @uses    bool                   $collapsible               Whether the collapsible should be rendered.
 * @uses    array                  $collapsible_config        Configuration for the collapsible.
 * @uses    string                 $collapsible_header_class  Class for the collapsible header.
 * @uses    string                 $title                     The title.
 * @uses    string                 $title_after               Additional content to render after the title.
 * @uses    string                 $view_file                 Path to the view file.
 * @uses    WPSEO_Admin_Help_Panel $help_text                 The help text.
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

?>
<div
	class="<?php echo esc_attr( 'paper tab-block ' . $class ); ?>"<?php echo ( $paper_id ) ? ' id="' . esc_attr( $paper_id_prefix . $paper_id ) . '"' : ''; ?>>

	<?php
	if ( ! empty( $title ) ) {

		if ( ! empty( $collapsible ) ) {

			$button_id_attr = '';
			if ( ! empty( $paper_id ) ) {
				$button_id_attr = sprintf( ' id="%s"', esc_attr( $paper_id_prefix . $paper_id . '-button' ) );
			}

			printf(
				'<h2 class="%1$s"><button%2$s type="button" class="toggleable-container-trigger" aria-expanded="%3$s">%4$s%5$s <span class="toggleable-container-icon dashicons %6$s" aria-hidden="true"></span></button></h2>',
				esc_attr( 'collapsible-header ' . $collapsible_header_class ),
				// phpcs:ignore WordPress.Security.EscapeOutput -- $button_id_attr is escaped above.
				$button_id_attr,
				esc_attr( $collapsible_config['expanded'] ),
				// phpcs:ignore WordPress.Security.EscapeOutput -- $help_text is an instance of WPSEO_Admin_Help_Panel, which escapes it's own output.
				$help_text->get_button_html(),
				esc_html( $title ) . wp_kses_post( $title_after ),
				wp_kses_post( $collapsible_config['toggle_icon'] )
			);
		}
		else {
			echo '<div class="paper-title"><h2 class="help-button-inline">',
				esc_html( $title ),
				wp_kses_post( $title_after ),
				// phpcs:ignore WordPress.Security.EscapeOutput -- $help_text is an instance of WPSEO_Admin_Help_Panel, which escapes it's own output.
				$help_text->get_button_html(),
				'</h2></div>';
		}
	}
	?>
	<?php

	// phpcs:ignore WordPress.Security.EscapeOutput -- $help_text is an instance of WPSEO_Admin_Help_Panel, which escapes it's own output.
	echo $help_text->get_panel_html();

	$container_id_attr = '';
	if ( ! empty( $paper_id ) ) {
		$container_id_attr = sprintf( ' id="%s"', esc_attr( $paper_id_prefix . $paper_id . '-container' ) );
	}

	printf(
		'<div%1$s class="%2$s">%3$s</div>',
		// phpcs:ignore WordPress.Security.EscapeOutput -- $container_id_attr is escaped above.
		$container_id_attr,
		esc_attr( 'paper-container ' . $collapsible_config['class'] ),
		$content
	);
	?>

</div>
