<?php

abstract class WPCF7_WelcomePanelColumn {

	abstract protected function icon();
	abstract protected function title();
	abstract protected function content();

	public function print_content() {
		$content = $this->content();

		if ( is_array( $content ) ) {
			$content = implode( "\n\n", $content );
		}

		$content = wp_kses_post( $content );
		$content = wptexturize( $content );
		$content = convert_chars( $content );
		$content = wpautop( $content );

		$formatter = new WPCF7_HTMLFormatter();

		$formatter->append_start_tag( 'div', array(
			'class' => 'welcome-panel-column',
		) );

		$formatter->append_start_tag( 'h3' );

		$formatter->append_start_tag( 'span', array(
			'class' => sprintf( 'dashicons dashicons-%s', $this->icon() ),
			'aria-hidden' => 'true',
		) );

		$formatter->end_tag( 'span' );

		$formatter->append_whitespace();

		$formatter->append_preformatted( $this->title() );

		$formatter->end_tag( 'h3' );

		$formatter->append_preformatted( $content );

		$formatter->print();
	}
}


class WPCF7_WelcomePanelColumn_AntiSpam extends WPCF7_WelcomePanelColumn {

	protected function icon() {
		return 'shield';
	}

	protected function title() {
		return __( 'Getting spammed? You have protection.', 'contact-form-7' );
	}

	protected function content() {
		return array(
			__( 'Spammers target everything; your contact forms are not an exception. Before you get spammed, protect your contact forms with the powerful anti-spam features Contact Form 7 provides.', 'contact-form-7' ),
			sprintf(
				/* translators: 1: URL to support page about Akismet, 2: Cloudflare Turnstile, 3: Disallowed list */
				__( 'Contact Form 7 supports spam-filtering with <a href="%1$s">Akismet</a>. <a href="%2$s">Cloudflare Turnstile</a> blocks annoying spambots. Plus, using <a href="%3$s">disallowed list</a>, you can block messages containing specified keywords or those sent from specified IP addresses.', 'contact-form-7' ),
				__( 'https://contactform7.com/spam-filtering-with-akismet/', 'contact-form-7' ),
				__( 'https://contactform7.com/turnstile-integration/', 'contact-form-7' ),
				__( 'https://contactform7.com/comment-blacklist/', 'contact-form-7' )
			),
		);
	}
}


class WPCF7_WelcomePanelColumn_Donation extends WPCF7_WelcomePanelColumn {

	protected function icon() {
		return 'megaphone';
	}

	protected function title() {
		return __( 'Contact Form 7 needs your support.', 'contact-form-7' );
	}

	protected function content() {
		return array(
			__( 'It is hard to continue to maintain this plugin without support from users like you.', 'contact-form-7' ),
			sprintf(
				/* translators: %s: URL to support page about ways to contribute */
				__( 'There are several ways for you to <a href="%s">contribute to the project</a>: testing, coding, translating it into your local languages, helping other users, financial donations, etc, etc. We equally welcome you regardless of the way you contribute.', 'contact-form-7' ),
				__( 'https://contactform7.com/contributing/', 'contact-form-7' )
			),
		);
	}
}


class WPCF7_WelcomePanelColumn_Flamingo extends WPCF7_WelcomePanelColumn {

	protected function icon() {
		return 'editor-help';
	}

	protected function title() {
		return __( 'Before you cry over spilt mail&#8230;', 'contact-form-7' );
	}

	protected function content() {
		return array(
			__( 'Contact Form 7 does not store submitted messages anywhere. Therefore, you may lose important messages forever if your mail server has issues or you make a mistake in mail configuration.', 'contact-form-7' ),
			sprintf(
				/* translators: %s: URL to support page about the Flamingo plugin */
				__( 'Install a message storage plugin before this happens to you. <a href="%s">Flamingo</a> saves all messages through contact forms into the database. Flamingo is a free WordPress plugin created by the same author as Contact Form 7.', 'contact-form-7' ),
				__( 'https://contactform7.com/save-submitted-messages-with-flamingo/', 'contact-form-7' )
			),
		);
	}
}


class WPCF7_WelcomePanelColumn_Integration extends WPCF7_WelcomePanelColumn {

	protected function icon() {
		return 'superhero-alt';
	}

	protected function title() {
		return __( 'You have strong allies to back you up.', 'contact-form-7' );
	}

	protected function content() {
		return array(
			sprintf(
				/* translators: %s: URL to support page about the Brevo integration */
				__( 'Your contact forms will become more powerful and versatile by integrating them with external APIs. With CRM and email marketing services, you can build your own contact lists (<a href="%s">Brevo</a>).', 'contact-form-7' ),
				__( 'https://contactform7.com/sendinblue-integration/', 'contact-form-7' )
			),
			sprintf(
				/* translators: 1: URL to support page about Cloudflare Turnstile, 2: Stripe */
				__( 'With help from cloud-based machine learning, anti-spam services will protect your forms (<a href="%1$s">Cloudflare Turnstile</a>). Even payment services are natively supported (<a href="%2$s">Stripe</a>).', 'contact-form-7' ),
				__( 'https://contactform7.com/turnstile-integration/', 'contact-form-7' ),
				__( 'https://contactform7.com/stripe-integration/', 'contact-form-7' )
			),
		);
	}
}


function wpcf7_welcome_panel() {
	$columns = array();

	$flamingo_is_active = defined( 'FLAMINGO_VERSION' );

	$sendinblue_is_active = false;

	if (
		class_exists( 'WPCF7_Sendinblue' ) and
		$sendinblue = WPCF7_Sendinblue::get_instance()
	) {
		$sendinblue_is_active = $sendinblue->is_active();
	}

	if ( $flamingo_is_active and $sendinblue_is_active ) {
		$columns[] = new WPCF7_WelcomePanelColumn_AntiSpam();
		$columns[] = new WPCF7_WelcomePanelColumn_Donation();
	} elseif ( $flamingo_is_active ) {
		$columns[] = new WPCF7_WelcomePanelColumn_Integration();
		$columns[] = new WPCF7_WelcomePanelColumn_AntiSpam();
	} elseif ( $sendinblue_is_active ) {
		$columns[] = new WPCF7_WelcomePanelColumn_Flamingo();
		$columns[] = new WPCF7_WelcomePanelColumn_AntiSpam();
	} else {
		$columns[] = new WPCF7_WelcomePanelColumn_Flamingo();
		$columns[] = new WPCF7_WelcomePanelColumn_Integration();
	}

	$classes = 'wpcf7-welcome-panel';

	$vers = (array) get_user_meta( get_current_user_id(),
		'wpcf7_hide_welcome_panel_on', true
	);

	if ( wpcf7_version_grep( wpcf7_version( 'only_major=1' ), $vers ) ) {
		$classes .= ' hidden';
	}

?>
<div id="wpcf7-welcome-panel" class="<?php echo esc_attr( $classes ); ?>">
	<?php wp_nonce_field( 'wpcf7-welcome-panel-nonce', 'welcomepanelnonce', false ); ?>
	<a class="welcome-panel-close" href="<?php echo esc_url( menu_page_url( 'wpcf7', false ) ); ?>"><?php echo esc_html( __( 'Dismiss', 'contact-form-7' ) ); ?></a>

	<div class="welcome-panel-content">
		<div class="welcome-panel-column-container">
<?php

	foreach ( $columns as $column ) {
		$column->print_content();
	}

?>
		</div>
	</div>
</div>
<?php
}


add_action(
	'wp_ajax_wpcf7-update-welcome-panel',
	'wpcf7_admin_ajax_welcome_panel',
	10, 0
);

function wpcf7_admin_ajax_welcome_panel() {
	check_ajax_referer( 'wpcf7-welcome-panel-nonce', 'welcomepanelnonce' );

	$vers = get_user_meta( get_current_user_id(),
		'wpcf7_hide_welcome_panel_on', true
	);

	if ( empty( $vers ) or ! is_array( $vers ) ) {
		$vers = array();
	}

	if ( empty( $_POST['visible'] ) ) {
		$vers[] = wpcf7_version( 'only_major=1' );
	} else {
		$vers = array_diff( $vers, array( wpcf7_version( 'only_major=1' ) ) );
	}

	$vers = array_unique( $vers );

	update_user_meta( get_current_user_id(),
		'wpcf7_hide_welcome_panel_on', $vers
	);

	wp_die( 1 );
}


add_filter(
	'screen_settings',
	'wpcf7_welcome_panel_screen_settings',
	10, 2
);

function wpcf7_welcome_panel_screen_settings( $screen_settings, $screen ) {

	if ( 'toplevel_page_wpcf7' !== $screen->id ) {
		return $screen_settings;
	}

	$vers = (array) get_user_meta( get_current_user_id(),
		'wpcf7_hide_welcome_panel_on', true
	);

	$checkbox_id = 'wpcf7-welcome-panel-show';
	$checked = ! in_array( wpcf7_version( 'only_major=1' ), $vers, true );

	$checkbox = sprintf(
		'<input %s />',
		wpcf7_format_atts( array(
			'id' => $checkbox_id,
			'type' => 'checkbox',
			'checked' => $checked,
		) )
	);

	$screen_settings .= sprintf( '
<fieldset class="wpcf7-welcome-panel-options">
<legend>%1$s</legend>
<label for="%2$s">%3$s %4$s</label>
</fieldset>',
		esc_html( __( 'Welcome panel', 'contact-form-7' ) ),
		esc_attr( $checkbox_id ),
		$checkbox,
		esc_html( __( 'Show welcome panel', 'contact-form-7' ) )
	);

	return $screen_settings;
}
