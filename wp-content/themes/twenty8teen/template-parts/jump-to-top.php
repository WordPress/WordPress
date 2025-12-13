<?php
/**
 * Template part for displaying the jump to top link
 * @package Twenty8teen
 */

?>
	<a <?php twenty8teen_widget_get_classes( 'jump-to-top', true ); ?>
		href="#" title="<?php esc_attr_e( 'Jump to top', 'twenty8teen' ); ?>">
		<span aria-hidden="true">&and;</span><span class="screen-reader-text"><?php esc_html_e( 'Jump to top', 'twenty8teen' ); ?></span>
	</a>
