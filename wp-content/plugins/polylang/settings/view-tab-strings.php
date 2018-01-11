<?php

/**
 * Displays the strings translations tab in Polylang settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly
};
?>
<div class="form-wrap">
	<form id="string-translation" method="post" action="<?php echo esc_url( add_query_arg( 'noheader', 'true' ) ); ?>">
		<input type="hidden" name="pll_action" value="string-translation" />
		<?php
		$string_table->search_box( __( 'Search translations', 'polylang' ), 'translations' );
		wp_nonce_field( 'string-translation', '_wpnonce_string-translation' );
		$string_table->display();
		printf( '<br /><label><input name="clean" type="checkbox" value="1" /> %s</label>', esc_html__( 'Clean strings translation database', 'polylang' ) );
		?>
		<p><?php esc_html_e( 'Use this to remove unused strings from database, for example after a plugin has been uninstalled.', 'polylang' ); ?></p>
		<?php
		submit_button(); // Since WP 3.1
		?>
	</form>
</div>
