<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
?>

<script type="text/html" id="tmpl-primary-term-ui">
	<?php
	/* translators: Hidden accessibility text; %1$s expands to the term title, %2$s to the taxonomy title. */
	$yoast_free_js_button_label = __( 'Make %1$s primary %2$s', 'wordpress-seo' );
	$yoast_free_js_button_label = sprintf(
		$yoast_free_js_button_label,
		'{{data.term}}',
		'{{data.taxonomy.title}}'
	);

	printf(
		'<button type="button" class="wpseo-make-primary-term" aria-label="%1$s">%2$s</button>',
		esc_attr( $yoast_free_js_button_label ),
		esc_html__( 'Make primary', 'wordpress-seo' )
	);
	?>

	<span class="wpseo-is-primary-term" aria-hidden="true"><?php esc_html_e( 'Primary', 'wordpress-seo' ); ?></span>
</script>

<script type="text/html" id="tmpl-primary-term-screen-reader">
	<?php
	/* translators: %s is the taxonomy title. This will be shown to screenreaders */
	$yoast_free_js_taxonomy_title = __( 'Primary %s', 'wordpress-seo' );
	$yoast_free_js_taxonomy_title = sprintf(
		'(' . $yoast_free_js_taxonomy_title . ')',
		'{{data.taxonomy.title}}'
	);
	?>
	<span class="screen-reader-text wpseo-primary-category-label"><?php echo esc_html( $yoast_free_js_taxonomy_title ); ?></span>
</script>
