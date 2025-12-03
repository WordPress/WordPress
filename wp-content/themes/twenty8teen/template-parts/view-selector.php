<?php
/**
 * Template part for displaying the view selector
 * @package Twenty8teen
 */

if ( is_singular() ) {
	return;
}
wp_enqueue_script( 'twenty8teen-view-selector', get_template_directory_uri() .
	'/js/view-selector.js', array(), '20210629', true );
?>
	<div <?php twenty8teen_widget_get_classes( 'view-selector', true ); ?>>
		<button type="button" class="view-switch">
			<?php _e( 'Table view', 'twenty8teen' ); ?>
		</button>
	</div>
