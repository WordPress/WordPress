<?php
/**
 * Lite-specific admin notices.
 */

add_action( 'admin_init', 'wpcode_maybe_add_library_connect_notice' );
add_action( 'wpcode_admin_page', 'wpcode_maybe_add_lite_top_bar_notice', 4 );
add_action( 'wpcode_admin_page_content_wpcode-headers-footers', 'wpcode_headers_footers_bottom_notice', 250 );

/**
 * Show a prompt to connect to the WPCode Library to get access to more snippets.
 *
 * @return void
 */
function wpcode_maybe_add_library_connect_notice() {
	if ( wpcode()->library_auth->has_auth() || ! isset( $_GET['page'] ) || 0 !== strpos( $_GET['page'], 'wpcode' ) ) {
		return;
	}
	// Don't show if in headers & footers mode only.
	if ( wpcode()->settings->get_option( 'headers_footers_mode' ) ) {
		return;
	}

	$settings_url = add_query_arg(
		array(
			'page' => 'wpcode-settings',
		),
		admin_url( 'admin.php' )
	);

	$snippets_count = wpcode()->library->get_snippets_count();
	// Translators: more here is used in the sense of "get access to more snippets" and gets replaced with the number of snippets if the library items are loaded correctly.
	$more = $snippets_count > 0 ? $snippets_count : __( 'more', 'insert-headers-and-footers' );

	WPCode_Notice::info(
		sprintf(
		// Translators: %1$s and %2$s add a link to the settings page. %3$s and %4$s make the text bold. %6$s is replaced with the number of snippets and %5$s adds a "new" icon.
			__( '%5$s%1$sConnect to the WPCode Library%2$s to get access to %3$s%6$s FREE snippets%4$s!', 'insert-headers-and-footers' ),
			'<a href="' . $settings_url . '" class="wpcode-start-auth">',
			'</a>',
			'<strong>',
			'</strong>',
			'<span class="wpcode-icon-new">&nbsp;NEW!</span>',
			$more
		),
		array(
			'dismiss' => WPCode_Notice::DISMISS_GLOBAL,
			'slug'    => 'wpcode-library-connect-lite',
		)
	);
}

/**
 * Add a notice to consider more features with offer.
 *
 * @return void
 */
function wpcode_maybe_add_lite_top_bar_notice() {
	// Only add this to the WPCode pages.
	if ( ! isset( $_GET['page'] ) || 0 !== strpos( $_GET['page'], 'wpcode' ) ) {
		return;
	}
	// Don't show in H&F mode.
	if ( wpcode()->settings->get_option( 'headers_footers_mode' ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( isset( $screen->id ) && false !== strpos( $screen->id, 'code-snippets_page_wpcode-' ) ) {
		$screen = str_replace( 'code-snippets_page_wpcode-', '', $screen->id );
	} else {
		$screen = 'snippets-list';
	}

	$upgrade_url = wpcode_utm_url(
		'https://wpcode.com/lite',
		$screen,
		'top-notice'
	);

	WPCode_Notice::top(
		sprintf(
		// Translators: %1$s and %2$s add a link to the upgrade page. %3$s and %4$s make the text bold.
			__( '%3$sYou\'re using WPCode Lite%4$s. To unlock more features consider %1$supgrading to Pro%2$s.', 'insert-headers-and-footers' ),
			'<a href="' . $upgrade_url . '" target="_blank" rel="noopener noreferrer">',
			'</a>',
			'<strong>',
			'</strong>'
		),
		array(
			'dismiss' => WPCode_Notice::DISMISS_USER,
			'slug'    => 'consider-upgrading',
		)
	);
}

/**
 * Show a notice with more features at the bottom of the Headers & Footers page.
 *
 * @return void
 */
function wpcode_headers_footers_bottom_notice() {
	// Don't show if in headers & footers mode only.
	if ( wpcode()->settings->get_option( 'headers_footers_mode' ) ) {
		return;
	}
	// Don't show if other notices were already displayed on the page.
	if ( ! empty( wpcode()->notice->notices ) ) {
		return;
	}

	$html = '<h3>' . esc_html__( 'Get WPCode Pro and Unlock all the Powerful Features', 'insert-headers-and-footers' ) . '</h3>';
	$html .= '<div class="wpcode-features-list">';
	$html .= '<ul>';
	$html .= '<li>' . esc_html__( 'Save & Reuse snippets in your private Cloud Library', 'insert-headers-and-footers' ) . '</li>';
	$html .= '<li>' . esc_html__( 'Add page-specific scripts when editing a post/page.', 'insert-headers-and-footers' ) . '</li>';
	$html .= '<li>' . esc_html__( 'Track all snippet changes with Advanced Code Revisions', 'insert-headers-and-footers' ) . '</li>';
	$html .= '</ul>';
	$html .= '<ul>';
	$html .= '<li>' . esc_html__( 'Load snippets by device (mobile/desktop) with 1-click.', 'insert-headers-and-footers' ) . '</li>';
	$html .= '<li>' . esc_html__( 'Easily insert and reuse content with Custom Shortcodes.', 'insert-headers-and-footers' ) . '</li>';
	$html .= '<li>' . esc_html__( 'Precisely track eCommerce conversions for WooCommerce and EDD.', 'insert-headers-and-footers' ) . '</li>';
	$html .= '</ul>';
	$html .= '</div>';
	$html .= sprintf(
		'<p><a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a></p>',
		wpcode_utm_url( 'https://wpcode.com/lite/', 'headers-footers', 'notice', 'get-wpcode-pro' ),
		esc_html__( 'Get WPCode Pro Today and Unlock all the Powerful Features »', 'insert-headers-and-footers' )
	);
	$html .= '<p>';
	$html .= sprintf(
	// Translators: Placeholders make the text bold.
		esc_html__( '%1$sBonus:%2$s WPCode Lite users get %3$s$50 off regular price%4$s, automatically applied at checkout', 'insert-headers-and-footers' ),
		'<strong>',
		'</strong>',
		'<strong style="color:#59A56D;">',
		'</strong>'
	);
	$html .= '</p>';

	// Add our custom notice for this page.
	WPCode_Notice::info(
		$html,
		array(
			'slug'    => 'ihaf-snippets',
			'dismiss' => WPCode_Notice::DISMISS_USER,
		)
	);
	// Display notice we just added so that scripts are loaded.
	wpcode()->notice->display();
}
