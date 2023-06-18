<?php
/**
 * Upgrade Welcome screen.
 *
 * @package WPCode
 */

/**
 * This page is shown when the plugin is updated from IHAF to WPCode.
 */
class WPCode_Upgrade_Welcome {

	/**
	 * Hidden welcome page slug.
	 */
	const SLUG = 'wpcode-upgrade-welcome';

	/**
	 * Primary class constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'hooks' ) );
	}

	/**
	 * Register the pages to be used for the Welcome screen.
	 */
	public function register() {
		add_dashboard_page(
			esc_html__( 'Welcome to WPCode', 'insert-headers-and-footers' ),
			esc_html__( 'Welcome to WPCode', 'insert-headers-and-footers' ),
			'wpcode_edit_snippets',
			self::SLUG,
			array( $this, 'output' )
		);
	}

	/**
	 * Register all WP hooks.
	 */
	public function hooks() {

		// If user is in admin ajax or doing cron, return.
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		// If user did not update (or can't update) the plugin don't show the screen.
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register' ) );
		add_filter( 'parent_file', array( $this, 'hide_menu' ), 1020 );
		add_action( 'admin_init', array( $this, 'redirect' ), 9999 );
		add_action( 'admin_body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Remove the dashboard page from the admin menu.
	 * We're using the parent_file filter to improve compatibility with admin-menu-editor.
	 *
	 * @param string $parent_file The parent file.
	 *
	 * @return string
	 */
	public function hide_menu( $parent_file ) {

		remove_submenu_page( 'index.php', self::SLUG );

		return $parent_file;
	}

	/**
	 * Welcome screen redirect. Only redirect if the user was previously using IHAF 1.6.x.
	 */
	public function redirect() {

		$redirect = get_transient( 'wpcode_upgrade_redirect' );

		if ( false === $redirect ) {
			return;
		}

		// Only redirect once.
		delete_transient( 'wpcode_upgrade_redirect' );

		wp_safe_redirect( admin_url( 'index.php?page=' . self::SLUG ) );
		exit;
	}

	/**
	 * Add a body class for this page only.
	 *
	 * @param string $body_class The body class.
	 *
	 * @return string
	 */
	public function body_class( $body_class ) {
		$screen = get_current_screen();

		if ( ! empty( $screen->id ) && false !== strpos( $screen->id, self::SLUG ) ) {
			$body_class .= ' ' . self::SLUG;
		}

		return $body_class;
	}

	/**
	 * Output of the upgrade screen.
	 */
	public function output() {
		$settings_link   = add_query_arg(
			array(
				'page' => 'wpcode-settings',
			),
			admin_url( 'admin.php' )
		);
		$snippets_page   = add_query_arg(
			array(
				'page' => 'wpcode',
			),
			admin_url( 'admin.php' )
		);
		$image_generator = WPCODE_PLUGIN_URL . 'admin/images/upgrade-welcome-generator.jpg';
		$image_settings  = WPCODE_PLUGIN_URL . 'admin/images/upgrade-welcome-headers-footers.jpg';
		$image_cloud     = WPCODE_PLUGIN_URL . 'admin/images/upgrade-welcome-cloud.jpg';
		$features        = array(
			array(
				'icon'  => 'code',
				'title' => __( 'Header & Footer Scripts', 'insert-headers-and-footers' ),
				'desc'  => __( 'Effortlessly manage global headers & footers in a familiar interface.', 'insert-headers-and-footers' ),
			),
			array(
				'icon'  => 'filter',
				'title' => __( 'Conversion Pixels', 'insert-headers-and-footers' ),
				'desc'  => __( 'Easily target specific pages to track conversions reliably.', 'insert-headers-and-footers' ),
			),
			array(
				'icon'  => 'php',
				'title' => __( 'PHP Snippets', 'insert-headers-and-footers' ),
				'desc'  => __( 'Add or remove features with full confidence that your site will not break.', 'insert-headers-and-footers' ),
			),
			array(
				'icon'  => 'split',
				'title' => __( 'Conditional Logic', 'insert-headers-and-footers' ),
				'desc'  => __( 'Create advanced conditional logic rules in an easy-to-use interface.', 'insert-headers-and-footers' ),
			),
			array(
				'icon'  => 'error_badge',
				'title' => __( 'Error Handling', 'insert-headers-and-footers' ),
				'desc'  => __( 'Unique error handling capabilities ensure you will not get locked out of your site.', 'insert-headers-and-footers' ),
			),
			array(
				'icon'  => 'terminal',
				'title' => __( 'Snippets Library', 'insert-headers-and-footers' ),
				'desc'  => __( 'One-click install from our extensive library of commonly-used snippets.', 'insert-headers-and-footers' ),
			),
		);
		$logo_src        = WPCODE_PLUGIN_URL . 'admin/images/wpcode-logo.png';
		// Translators: This simply adds the plugin name before the logo text.
		$logo_alt     = sprintf( __( '%s logo', 'insert-headers-and-footers' ), 'WPCode' );
		$syed_photo   = WPCODE_PLUGIN_URL . 'admin/images/syed.png';
		$mircea_photo = WPCODE_PLUGIN_URL . 'admin/images/mircea.png';
		?>
		<div class="wpcode-welcome-content">
			<div class="wpcode-welcome-logo">
				<img src="<?php echo esc_url( $logo_src ); ?>" width="132" alt="<?php echo esc_attr( $logo_alt ); ?>"/>
			</div>
			<div class="wpcode-welcome-box">
				<h2><?php esc_html_e( 'Insert Headers and Footers is now WPCode', 'insert-headers-and-footers' ); ?></h2>
				<p><?php esc_html_e( 'When we first built Insert Headers and Footers over a decade ago, it was meant to do one very simple thing: add header and footer scripts to your site without editing theme files.', 'insert-headers-and-footers' ); ?></p>
				<p><?php esc_html_e( 'Since then, the plugin has grown to over 1 million active installs with an amazing user base. We have continued to receive feature requests to add more options like controlling which pages the scripts get loaded, allowing more types of code snippets, etc.', 'insert-headers-and-footers' ); ?></p>
				<p><?php esc_html_e( 'We listened to your feedback, and we are excited to present WPCode, the next generation of Insert Headers and Footers. We chose a new name because it was only fair considering the plugin is now 10x more powerful. Aside from adding global headers and footer snippets, you can also add multiple other types of code snippets, have granular control of where the snippets are output with conditional logic, and a whole lot more.', 'insert-headers-and-footers' ); ?></p>
				<p>
					<?php
					printf(
					// Translators: Placeholders 1 & 2 add a link to scroll down the page and 3 & 4 add a link to the suggestions form.
						esc_html__(
							'Please see the full list of features %1$sbelow%2$s and let us know what you\'d like us to add next by %3$ssharing your feedback%4$s.',
							'insert-headers-and-footers'
						),
						'<a href="#features" class="wpcode-scroll-to">',
						'</a>',
						'<a href="' . esc_url( wpcode_utm_url( 'https://wpcode.com/suggestions/', 'welcome', 'intro' ) ) . '" target="_blank">',
						'</a>'
					);
					?>
				</p>
				<p>
					<?php
					printf(
					// Translators: Placeholders add link to the details about settings.
						esc_html__(
							'For those of you who want to limit the functionality and switch back to the old interface, you can do so with one click. %1$sSee details here%2$s.',
							'insert-headers-and-footers'
						),
						'<a href="#old_interface" class="wpcode-scroll-to">',
						'</a>'
					);
					?>
				</p>
				<p><?php esc_html_e( 'We have an exciting roadmap ahead of us since you have shared tons of great ideas with us over the last several years. We truly appreciate your continued support and thank you for being an awesome user.', 'insert-headers-and-footers' ); ?></p>
				<p><?php esc_html_e( 'We truly appreciate your continued support and thank you for using WPCode.', 'insert-headers-and-footers' ); ?></p>
				<div class="wpcode-welcome-syed-mircea">
					<div class="wpcode-welcome-person">
						<div class="wpcode-welcome-person-image">
							<img src="<?php echo esc_attr( $syed_photo ); ?>" alt="Syed" width="48"/>
						</div>
						<div class="wpcode-welcome-person-text">
							<h4>Syed Balkhi</h4>
							<?php
							printf(
							// Translators: Placeholder for "WPBeginner".
								esc_html__( 'Founder of %s', 'insert-headers-and-footers' ),
								'WPBeginner'
							);
							?>
						</div>
					</div>
					<div class="wpcode-welcome-person">
						<div class="wpcode-welcome-person-image">
							<img src="<?php echo esc_attr( $mircea_photo ); ?>" alt="Mircea" width="48"/>
						</div>
						<div class="wpcode-welcome-person-text">
							<h4>Mircea Sandu</h4>
							<?php esc_html_e( 'Lead Developer', 'insert-headers-and-footers' ); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="wpcode-welcome-box" id="features">
				<h2><?php esc_html_e( 'What’s New in WPCode (Features & Highlights)', 'insert-headers-and-footers' ); ?></h2>
				<div class="wpcode-welcome-features">
					<?php foreach ( $features as $feature ) { ?>
						<div class="wpcode-welcome-feature">
							<div class="wpcode-welcome-feature-icon">
								<div class="wpcode-welcome-feature-icon-icon">
									<?php wpcode_icon( $feature['icon'], 30, 30, '0 0 48 48' ); ?>
								</div>
							</div>
							<div class="wpcode-welcome-feature-text">
								<h3><?php echo esc_html( $feature['title'] ); ?></h3>
								<p><?php echo esc_html( $feature['desc'] ); ?></p>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="wpcode-welcome-box">
				<div class="wpcode-welcome-highlight">
					<div class="wpcode-welcome-highlight-column">
						<img src="<?php echo esc_url( $image_generator ); ?>" alt="<?php esc_attr_e( 'WPCode Generator Screen capture', 'insert-headers-and-footers' ); ?>"/>
					</div>
					<div class="wpcode-welcome-highlight-column">
						<h3><?php esc_html_e( 'Snippet Generator', 'insert-headers-and-footers' ); ?></h3>
						<p><?php esc_html_e( 'WPCode now includes a snippet generator directly in the plugin.', 'insert-headers-and-footers' ); ?></p>
						<p><?php esc_html_e( 'Using the built-in generators, you can quickly add custom post types, custom post statuses, widgets, menus, build complex WP Queries and much more.', 'insert-headers-and-footers' ); ?></p>
						<p><?php esc_html_e( 'Simply fill in the fields in our guided wizard to generate a custom ready-to-use snippet for your website with 1 click. Try WordPress Snippet Generator.', 'insert-headers-and-footers' ); ?></p>
					</div>
				</div>
			</div>
			<div class="wpcode-welcome-box">
				<div class="wpcode-welcome-highlight">
					<div class="wpcode-welcome-highlight-column">
						<h3><?php esc_html_e( 'Store Snippets in Cloud', 'insert-headers-and-footers' ); ?></h3>
						<p><?php esc_html_e( 'A lot of you requested the ability to save and re-use snippets on multiple websites.', 'insert-headers-and-footers' ); ?></p>
						<p>
							<?php
							printf(
							// Translators: Placeholders add a link to the suggestions page.
								esc_html__(
									'This feature is now available in the %1$sPRO version of the plugin%2$s along with other powerful features.',
									'insert-headers-and-footers'
								),
								'<a href="' . esc_url( wpcode_utm_url( 'https://wpcode.com/lite/', 'upgrade-welcome', 'cloud-snippets' ) ) . '" target="_blank">',
								'</a>'
							);
							?>
						</p>
						<p>
							<?php
							printf(
							// Translators: Placeholders add a link to the suggestions page.
								esc_html__(
									'If you have specific ideas or feature requests, please let us know by %1$sfilling out this form%2$s.',
									'insert-headers-and-footers'
								),
								'<a href="' . esc_url( wpcode_utm_url( 'https://wpcode.com/suggestions/', 'welcome', 'cloud' ) ) . '" target="_blank">',
								'</a>'
							);
							?>
						</p>
					</div>
					<div class="wpcode-welcome-highlight-column">
						<img src="<?php echo esc_url( $image_cloud ); ?>" alt="<?php esc_attr_e( 'WPCode Cloud Screen capture', 'insert-headers-and-footers' ); ?>"/>
					</div>
				</div>
			</div>
			<div class="wpcode-welcome-box">
				<div class="wpcode-welcome-highlight" id="old_interface">
					<div class="wpcode-welcome-highlight-column">
						<img src="<?php echo esc_url( $image_settings ); ?>" alt="<?php esc_attr_e( 'WPCode Generator Screen capture', 'insert-headers-and-footers' ); ?>"/>
					</div>
					<div class="wpcode-welcome-highlight-column">
						<h3><?php esc_html_e( 'Not ready for the new interface?', 'insert-headers-and-footers' ); ?></h3>
						<p><?php esc_html_e( 'If you are not ready to switch to the new interface, or you simply want to use the plugin just for inserting headers and footers we\'ve got you covered.', 'insert-headers-and-footers' ); ?></p>
						<p>
							<?php
							printf(
							// Translators: Placeholders add a link to the settings page.
								esc_html__(
									'You can switch to the simple Headers & Footers interface at any time from the %1$ssettings page%2$s.',
									'insert-headers-and-footers'
								),
								'<a href="' . esc_url( $settings_link ) . '">',
								'</a>'
							);
							?>
						</p>
						<p><?php esc_html_e( 'And if you change your mind later and want to give the full plugin a shot, you can always switch back with just 2 clicks using the option at the top of the page.', 'insert-headers-and-footers' ); ?></p>
					</div>
				</div>
			</div>
			<div class="wpcode-buttons-row">
				<a href="<?php echo esc_url( $snippets_page ); ?>" class="wpcode-button wpcode-button-large"><?php esc_html_e( 'Add Your First Snippet', 'insert-headers-and-footers' ); ?></a>
			</div>
		</div>
		<?php
	}
}

new WPCode_Upgrade_Welcome();
