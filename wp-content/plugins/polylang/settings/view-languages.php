<?php

/**
 * Displays the Languages admin panel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly
};

require ABSPATH . 'wp-admin/options-head.php'; // Displays the errors messages as when we were a child of options-general.php
?>
<div class="wrap">
	<h1><?php echo esc_html( $GLOBALS['title'] ); ?></h1>
	<?php
	switch ( $this->active_tab ) {
		case 'lang':     // Languages tab
		case 'strings':  // String translations tab
		case 'settings': // Settings tab
			include PLL_SETTINGS_INC . '/view-tab-' . $this->active_tab . '.php';
			break;

		default:
			/**
			 * Fires when loading the active Polylang settings tab
			 * Allows plugins to add their own tab
			 *
			 * @since 1.5.1
			 */
			do_action( 'pll_settings_active_tab_' . $this->active_tab );
			break;
	}
	?>
</div><!-- wrap -->
