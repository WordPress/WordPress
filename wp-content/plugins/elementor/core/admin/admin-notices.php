<?php
namespace Elementor\Core\Admin;

use Elementor\Api;
use Elementor\Core\Admin\UI\Components\Button;
use Elementor\Core\Base\Module;
use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Plugin;
use Elementor\Settings;
use Elementor\Tracker;
use Elementor\User;
use Elementor\Utils;
use Elementor\Core\Admin\Notices\Base_Notice;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Admin_Notices extends Module {

	const DEFAULT_EXCLUDED_PAGES = [ 'plugins.php', 'plugin-install.php', 'plugin-editor.php' ];
	const LOCAL_GOOGLE_FONTS_DISABLED_NOTICE_ID = 'local_google_fonts_disabled';

	const EXIT_EARLY_FOR_BACKWARD_COMPATIBILITY = false;

	private $plain_notices = [
		'api_notice',
		'api_upgrade_plugin',
		'tracker',
		'tracker_last_update',
		'rate_us_feedback',
		'role_manager_promote',
		'experiment_promotion',
		'send_app_promotion',
		'site_mailer_promotion',
		'plugin_image_optimization',
		'ally_pages_promotion',
		self::LOCAL_GOOGLE_FONTS_DISABLED_NOTICE_ID,
	];

	private $elementor_pages_count = null;

	private $install_time = null;

	private $current_screen_id = null;

	private function get_notices() {
		/**
		 * Admin notices.
		 *
		 * Filters Elementor admin notices.
		 *
		 * This hook can be used by external developers to manage existing
		 * admin notice or to add new notices for Elementor add-ons.
		 *
		 * @param array $notices A list of notice classes.
		 */
		$notices = apply_filters( 'elementor/core/admin/notices', [] );

		return $notices;
	}

	private function get_install_time() {
		if ( null === $this->install_time ) {
			$this->install_time = Plugin::$instance->get_install_time();
		}

		return $this->install_time;
	}

	private function get_elementor_pages_count() {
		if ( null === $this->elementor_pages_count ) {
			$elementor_pages = new \WP_Query( [
				'no_found_rows' => true,
				'post_type' => 'any',
				'post_status' => 'publish',
				'fields' => 'ids',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_key' => '_elementor_edit_mode',
				'meta_value' => 'builder',
			] );

			$this->elementor_pages_count = $elementor_pages->post_count;
		}

		return $this->elementor_pages_count;
	}

	private function notice_api_upgrade_plugin() {
		$upgrade_notice = Api::get_upgrade_notice();
		if ( empty( $upgrade_notice ) ) {
			return false;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return false;
		}

		if ( ! $this->is_elementor_admin_screen_with_system_info() ) {
			return false;
		}

		// Check for upgrades.
		$update_plugins = get_site_transient( 'update_plugins' );

		$has_remote_update_package = ! ( empty( $update_plugins ) || empty( $update_plugins->response[ ELEMENTOR_PLUGIN_BASE ] ) || empty( $update_plugins->response[ ELEMENTOR_PLUGIN_BASE ]->package ) );

		if ( ! $has_remote_update_package && empty( $upgrade_notice['update_link'] ) ) {
			return false;
		}

		if ( $has_remote_update_package ) {
			$product = $update_plugins->response[ ELEMENTOR_PLUGIN_BASE ];

			$details_url = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $product->slug . '&section=changelog&TB_iframe=true&width=600&height=800' );
			$upgrade_url = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . ELEMENTOR_PLUGIN_BASE ), 'upgrade-plugin_' . ELEMENTOR_PLUGIN_BASE );
			$new_version = $product->new_version;
		} else {
			$upgrade_url = $upgrade_notice['update_link'];
			$details_url = $upgrade_url;

			$new_version = $upgrade_notice['version'];
		}

		// Check if upgrade messages should be shown.
		if ( version_compare( ELEMENTOR_VERSION, $upgrade_notice['version'], '>=' ) ) {
			return false;
		}

		$notice_id = 'upgrade_notice_' . $upgrade_notice['version'];
		if ( User::is_user_notice_viewed( $notice_id ) ) {
			return false;
		}

		$message = sprintf(
			/* translators: 1: Details URL, 2: Accessibility text, 3: Version number, 4: Update URL, 5: Accessibility text. */
			__( 'There is a new version of Elementor Page Builder available. <a href="%1$s" class="thickbox open-plugin-details-modal" aria-label="%2$s">View version %3$s details</a> or <a href="%4$s" class="update-link" aria-label="%5$s">update now</a>.', 'elementor' ),
			esc_url( $details_url ),
			esc_attr( sprintf(
				/* translators: %s: Elementor version. */
				__( 'View Elementor version %s details', 'elementor' ),
				$new_version
			) ),
			$new_version,
			esc_url( $upgrade_url ),
			esc_attr( esc_html__( 'Update Now', 'elementor' ) )
		);

		$options = [
			'title' => esc_html__( 'Update Notification', 'elementor' ),
			'description' => $message,
			'button' => [
				'icon_classes' => 'dashicons dashicons-update',
				'text' => esc_html__( 'Update Now', 'elementor' ),
				'url' => $upgrade_url,
			],
			'id' => $notice_id,
		];

		$this->print_admin_notice( $options );

		return true;
	}

	private function notice_api_notice() {
		$admin_notice = Api::get_admin_notice();
		if ( empty( $admin_notice ) ) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( ! $this->is_elementor_admin_screen_with_system_info() ) {
			return false;
		}

		$notice_id = 'admin_notice_api_' . $admin_notice['notice_id'];
		if ( User::is_user_notice_viewed( $notice_id ) ) {
			return false;
		}

		$options = [
			'title' => esc_html__( 'Update Notification', 'elementor' ),
			'description' => $admin_notice['notice_text'],
			'id' => $notice_id,
		];

		$this->print_admin_notice( $options );

		return true;
	}

	private function notice_tracker() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		// Show tracker notice after 24 hours from installed time.
		if ( strtotime( '+24 hours', $this->get_install_time() ) > time() ) {
			return false;
		}

		if ( '1' === get_option( 'elementor_tracker_notice' ) ) {
			return false;
		}

		if ( Tracker::is_allow_track() ) {
			return false;
		}

		if ( 2 > $this->get_elementor_pages_count() ) {
			return false;
		}

		// TODO: Skip for development env.
		$optin_url = wp_nonce_url( add_query_arg( 'elementor_tracker', 'opt_into' ), 'opt_into' );
		$optout_url = wp_nonce_url( add_query_arg( 'elementor_tracker', 'opt_out' ), 'opt_out' );

		$tracker_description_text = esc_html__( 'Become a super contributor by helping us understand how you use our service to enhance your experience and improve our product.', 'elementor' );

		/**
		 * Tracker admin description text.
		 *
		 * Filters the admin notice text for non-sensitive data collection.
		 *
		 * @since 1.0.0
		 *
		 * @param string $tracker_description_text Description text displayed in admin notice.
		 */
		$tracker_description_text = apply_filters( 'elementor/tracker/admin_description_text', $tracker_description_text );

		$message = esc_html( $tracker_description_text ) . ' <a href="https://go.elementor.com/usage-data-tracking/" target="_blank">' . esc_html__( 'Learn more.', 'elementor' ) . '</a>';

		$options = [
			'title' => esc_html__( 'Want to shape the future of web creation?', 'elementor' ),
			'description' => $message,
			'dismissible' => false,
			'button' => [
				'text' => esc_html__( 'Sure! I\'d love to help', 'elementor' ),
				'url' => $optin_url,
				'type' => 'cta',
			],
			'button_secondary' => [
				'text' => esc_html__( 'No thanks', 'elementor' ),
				'url' => $optout_url,
				'variant' => 'outline',
				'type' => 'cta',
			],
		];

		$this->print_admin_notice( $options );

		return true;
	}

	private function notice_tracker_last_update() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( ! Tracker::has_terms_changed() ) {
			return false;
		}

		$notice_id = 'tracker_last_update_' . Tracker::LAST_TERMS_UPDATED;

		if ( User::is_user_notice_viewed( $notice_id ) ) {
			return false;
		}

		$optin_url = wp_nonce_url( add_query_arg( 'elementor_tracker', 'opt_into' ), 'opt_into' );

		$message = esc_html__( 'We\'re updating our Terms and Conditions to include the collection of usage and behavioral data. This information helps us understand how you use Elementor so we can make informed improvements to the product.', 'elementor' );

		$options = [
			'id' => $notice_id,
			'title' => esc_html__( 'Update regarding usage data collection', 'elementor' ),
			'description' => $message,
			'button' => [
				'text' => esc_html__( 'Opt in', 'elementor' ),
				'url' => $optin_url,
				'type' => 'cta',
			],
			'button_secondary' => [
				'text' => esc_html__( 'Learn more', 'elementor' ),
				'url' => 'https://go.elementor.com/wp-dash-update-usage-notice/',
				'new_tab' => true,
				'type' => 'cta',
			],
		];

		$this->print_admin_notice( $options );

		return true;
	}

	private function notice_rate_us_feedback() {
		$notice_id = 'rate_us_feedback';

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( 'dashboard' !== $this->current_screen_id || User::is_user_notice_viewed( $notice_id ) ) {
			return false;
		}

		if ( 10 >= $this->get_elementor_pages_count() ) {
			return false;
		}

		$dismiss_url = add_query_arg( [
			'action' => 'elementor_set_admin_notice_viewed',
			'notice_id' => esc_attr( $notice_id ),
			'_wpnonce' => wp_create_nonce( 'elementor_set_admin_notice_viewed' ),
		], admin_url( 'admin-post.php' ) );

		$options = [
			'title' => esc_html__( 'Congrats!', 'elementor' ),
			'description' => esc_html__( 'You created over 10 pages with Elementor. Great job! If you can spare a minute, please help us by leaving a five star review on WordPress.org.', 'elementor' ),
			'id' => $notice_id,
			'button' => [
				'text' => esc_html__( 'Happy To Help', 'elementor' ),
				'url' => 'https://go.elementor.com/admin-review/',
				'new_tab' => true,
				'type' => 'cta',
			],
			'button_secondary' => [
				'text' => esc_html__( 'Hide Notification', 'elementor' ),
				'classes' => [ 'e-notice-dismiss' ],
				'url' => esc_url_raw( $dismiss_url ),
				'new_tab' => true,
				'type' => 'cta',
			],
		];

		$this->print_admin_notice( $options );

		return true;
	}

	private function notice_role_manager_promote() {
		$notice_id = 'role_manager_promote';

		if ( Utils::has_pro() ) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( 'elementor_page_elementor-role-manager' !== $this->current_screen_id || User::is_user_notice_viewed( $notice_id ) ) {
			return false;
		}

		$users = new \WP_User_Query( [
			'fields' => 'ID',
			'number' => 10,
		] );

		if ( 5 > $users->get_total() ) {
			return false;
		}

		$options = [
			'title' => esc_html__( 'Managing a multi-user site?', 'elementor' ),
			'description' => esc_html__( 'With Elementor Pro, you can control user access and make sure no one messes up your design.', 'elementor' ),
			'id' => $notice_id,

			'button' => [
				'text' => esc_html__( 'Learn More', 'elementor' ),
				'url' => 'https://go.elementor.com/plugin-promotion-role-manager/',
				'new_tab' => true,
				'type' => 'cta',
			],
		];

		$options = Filtered_Promotions_Manager::get_filtered_promotion_data( $options, 'core/admin/notice_role_manager_promote', 'button', 'url' );

		$this->print_admin_notice( $options );

		return true;
	}

	private function notice_experiment_promotion() {
		$notice_id = 'experiment_promotion';

		if ( ! current_user_can( 'manage_options' ) || User::is_user_notice_viewed( $notice_id ) ) {
			return false;
		}

		$experiments = Plugin::$instance->experiments;
		$is_all_performance_features_active = (
			$experiments->is_feature_active( 'e_font_icon_svg' ) &&
			$experiments->is_feature_active( 'e_optimized_markup' )
		);

		if ( $is_all_performance_features_active ) {
			return false;
		}

		$options = [
			'title' => esc_html__( 'Improve your site’s performance score.', 'elementor' ),
			'description' => esc_html__( 'With our experimental speed boosting features you can go faster than ever before. Look for the Performance label on our Experiments page and activate those experiments to improve your site loading speed.', 'elementor' ),
			'id' => $notice_id,
			'button' => [
				'text' => esc_html__( 'Try it out', 'elementor' ),
				'url' => Settings::get_settings_tab_url( 'experiments' ),
				'type' => 'cta',
			],
			'button_secondary' => [
				'text' => esc_html__( 'Learn more', 'elementor' ),
				'url' => 'https://go.elementor.com/wp-dash-experiment-promotion/',
				'new_tab' => true,
				'type' => 'cta',
			],
		];

		$this->print_admin_notice( $options );

		return true;
	}

	private function site_has_forms_plugins() {
		return defined( 'WPFORMS_VERSION' ) || defined( 'WPCF7_VERSION' ) || defined( 'FLUENTFORM_VERSION' ) || class_exists( '\GFCommon' ) || class_exists( '\Ninja_Forms' ) || function_exists( 'load_formidable_forms' ) || did_action( 'metform/after_load' ) || defined( 'FORMINATOR_PLUGIN_BASENAME' );
	}

	private function site_has_woocommerce() {
		return class_exists( 'WooCommerce' );
	}

	private function get_installed_form_plugin_name() {
		static $detected_form_plugin = null;

		if ( null !== $detected_form_plugin ) {
			return $detected_form_plugin;
		}

		$form_plugins_constants_to_name_mapper = [
			'WPFORMS_VERSION' => 'WPForms',
			'WPCF7_VERSION' => 'Contact Form 7',
		];

		foreach ( $form_plugins_constants_to_name_mapper as $constant => $name ) {
			if ( defined( $constant ) ) {
				$detected_form_plugin = $name;
				return $detected_form_plugin;
			}
		}

		$form_plugins_classes_to_name_mapper = [
			'\GFCommon' => 'Gravity Forms',
			'\Ninja_Forms' => 'Ninja Forms',
		];

		foreach ( $form_plugins_classes_to_name_mapper as $class => $name ) {
			if ( class_exists( $class ) ) {
				$detected_form_plugin = $name;
				return $detected_form_plugin;
			}
		}

		$detected_form_plugin = false;
		return $detected_form_plugin;
	}

	private function notice_send_app_promotion() {
		return self::EXIT_EARLY_FOR_BACKWARD_COMPATIBILITY;

		$notice_id = 'send_app_promotion';

		if ( ! $this->is_elementor_page() && ! $this->is_elementor_admin_screen() ) {
			return false;
		}

		if ( time() < $this->get_install_time() + ( 60 * DAY_IN_SECONDS ) ) {
			return false;
		}

		if ( ! current_user_can( 'install_plugins' ) || User::is_user_notice_viewed( $notice_id ) ) {
			return false;
		}

		$plugin_file_path = 'send/send-app.php';
		$plugin_slug = 'send-app';

		$cta_data = $this->get_plugin_cta_data( $plugin_slug, $plugin_file_path );
		if ( empty( $cta_data ) ) {
			return false;
		}

		$title = sprintf( esc_html__( 'Turn leads into loyal shoppers', 'elementor' ) );

		$options = [
			'title' => $title,
			'description' => esc_html__( 'Collecting leads is just the beginning. With Send by Elementor, you can manage contacts, launch automations, and turn form submissions into sales.', 'elementor' ),
			'id' => $notice_id,
			'type' => 'cta',
			'button' => [
				'text' => $cta_data['text'],
				'url' => $cta_data['url'],
				'type' => 'cta',
			],
			'button_secondary' => [
				'text' => esc_html__( 'Learn more', 'elementor' ),
				'url' => 'https://go.elementor.com/Formslearnmore',
				'new_tab' => true,
				'type' => 'cta',
			],
		];

		$this->print_admin_notice( $options );

		return true;
	}

	private function notice_local_google_fonts_disabled() {

		if ( ! $this->is_elementor_page() && ! $this->is_elementor_admin_screen() ) {
			return false;
		}

		if ( User::is_user_notice_viewed( self::LOCAL_GOOGLE_FONTS_DISABLED_NOTICE_ID ) ) {
			return false;
		}

		$is_local_gf_enabled = (bool) get_option( 'elementor_local_google_fonts', '0' );

		if ( $is_local_gf_enabled ) {
			return false;
		}

		$options = [
			'title' => esc_html__( 'Important: Local Google Fonts Settings in Elementor', 'elementor' ),
			'description' => esc_html__( 'Please note: The "Load Google Fonts Locally" feature has been disabled by default on all websites. To turn it back on, go to Elementor → Settings → Performance → Enable Load Google Fonts Locally.', 'elementor' ),
			'id' => self::LOCAL_GOOGLE_FONTS_DISABLED_NOTICE_ID,
			'type' => '',
			'button' => [
				'text' => esc_html__( 'Take me there', 'elementor' ),
				'url' => '../wp-admin/admin.php?page=elementor-settings#tab-performance',
				'new_tab' => false,
				'type' => 'cta',
			],
			'button_secondary' => [
				'text' => esc_html__( 'Learn more', 'elementor' ),
				'url' => 'https://go.elementor.com/wp-dash-google-fonts-locally-notice/',
				'new_tab' => true,
				'type' => 'cta',
			],
		];

		$this->print_admin_notice( $options );

		return true;
	}

	private function notice_ally_pages_promotion() {
		global $pagenow;
		$notice_id = 'ally_pages_promotion';

		if ( 'edit.php' !== $pagenow || empty( $_GET['post_type'] ) || 'page' !== $_GET['post_type'] ) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) || User::is_user_notice_viewed( $notice_id ) ) {
			return false;
		}

		$plugin_file_path = 'pojo-accessibility/pojo-accessibility.php';
		$plugin_slug = 'pojo-accessibility';

		$cta_data = $this->get_plugin_cta_data( $plugin_slug, $plugin_file_path );
		if ( empty( $cta_data ) ) {
			return false;
		}

		$options = [
			'title' => esc_html__( 'Make sure your site has an accessibility statement page', 'elementor' ),
			'description' => esc_html__( 'Create a more inclusive site experience for all your visitors. With Ally, it\'s easy to add your statement page in just a few clicks.', 'elementor' ),
			'id' => $notice_id,
			'type' => 'cta',
			'button' => [
				'text' => $cta_data['text'],
				'url' => self::add_plg_campaign_data( $cta_data['url'], [
					'name' => 'elementor_ea11y_campaign',
					'campaign' => 'acc-statement-plg-pages',
					'source' => 'wp-pages',
					'medium' => 'wp-dash',
				] ),
				'type' => 'cta',
			],
			'button_secondary' => [
				'text' => esc_html__( 'Learn more', 'elementor' ),
				'url' => 'https://go.elementor.com/acc-plg-learn-more',
				'new_tab' => true,
				'type' => 'cta',
			],
		];

		$this->print_admin_notice( $options );

		return true;
	}

	private function notice_site_mailer_promotion() {
		$notice_id = 'site_mailer_promotion';
		$has_forms = $this->site_has_forms_plugins();
		$has_woocommerce = $this->site_has_woocommerce();

		if ( ! $has_forms && ! $has_woocommerce ) {
			return false;
		}

		if ( ! $this->is_elementor_page() && ! $this->is_elementor_admin_screen() ) {
			return false;
		}

		if ( ( Utils::has_pro() && ! $has_woocommerce ) || ! current_user_can( 'install_plugins' ) || User::is_user_notice_viewed( $notice_id ) ) {
			return false;
		}

		$plugin_file_path = 'site-mailer/site-mailer.php';
		$plugin_slug = 'site-mailer';

		$cta_data = $this->get_plugin_cta_data( $plugin_slug, $plugin_file_path );
		if ( empty( $cta_data ) ) {
			return false;
		}

		$options = [
			'title' => esc_html__( 'Ensure your form emails avoid the spam folder!', 'elementor' ),
			'description' => esc_html__( 'Use Site Mailer for improved email deliverability, detailed email logs, and an easy setup.', 'elementor' ),
			'id' => $notice_id,
			'type' => 'cta',
			'button' => [
				'text' => $cta_data['text'],
				'url' => $cta_data['url'],
				'type' => 'cta',
			],
			'button_secondary' => [
				'text' => esc_html__( 'Learn more', 'elementor' ),
				'url' => 'https://go.elementor.com/sm-core-form/',
				'new_tab' => true,
				'type' => 'cta',
			],
		];

		if ( $this->should_render_woocommerce_hint( $has_forms, $has_woocommerce ) ) {
			// We include WP's default notice class so it will be properly handled by WP's js handler
			// And add a new one to distinguish between the two types of notices
			$options['classes'] = [ 'notice', 'e-notice', 'sm-notice-wc' ];
			$options['title'] = esc_html__( 'Improve Transactional Email Deliverability', 'elementor' );
			$options['description'] = esc_html__( 'Use Elementor\'s Site Mailer to ensure your store emails like purchase confirmations, shipping updates and more are reliably delivered.', 'elementor' );
		}

		$this->print_admin_notice( $options );

		return true;
	}

	private function should_render_woocommerce_hint( $has_forms, $has_woocommerce ): bool {
		if ( ! $has_forms && ! $has_woocommerce ) {
			return false;
		}

		if ( ! $has_forms && $has_woocommerce ) {
			return true;
		}

		if ( $has_forms && $has_woocommerce && Utils::has_pro() ) {
			return true;
		}

		if ( ! $has_woocommerce ) {
			return false;
		}

		return (bool) wp_rand( 0, 1 );
	}

	private function is_elementor_page(): bool {
		return 0 === strpos( $this->current_screen_id, 'elementor_page' );
	}

	private function is_elementor_admin_screen(): bool {
		return in_array( $this->current_screen_id, [ 'toplevel_page_elementor', 'edit-elementor_library' ], true );
	}

	private function is_elementor_admin_screen_with_system_info(): bool {
		return in_array( $this->current_screen_id, [ 'toplevel_page_elementor', 'edit-elementor_library', 'elementor_page_elementor-system-info', 'dashboard' ], true );
	}

	private function get_plugin_cta_data( $plugin_slug, $plugin_file_path ) {
		if ( is_plugin_active( $plugin_file_path ) ) {
			return false;
		}

		if ( $this->is_plugin_installed( $plugin_file_path ) ) {
			$url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin_file_path . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin_file_path );
			$cta_text = esc_html__( 'Activate Plugin', 'elementor' );
		} else {
			$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
			$cta_text = esc_html__( 'Install Plugin', 'elementor' );
		}

		return [
			'url' => $url,
			'text' => $cta_text,
		];
	}

	/**
	 * For testing purposes
	 */
	public function get_elementor_version() {
		return ELEMENTOR_VERSION;
	}

	private function notice_plugin_image_optimization() {
		$notice_id = 'plugin_image_optimization';

		if ( 'upload' !== $this->current_screen_id ) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) || User::is_user_notice_viewed( $notice_id ) ) {
			return false;
		}

		$attachments = new \WP_Query( [
			'post_type' => 'attachment',
			'post_status' => 'any',
			'fields' => 'ids',
		] );

		if ( 1 > $attachments->found_posts ) {
			return false;
		}

		$plugin_file_path = 'image-optimization/image-optimization.php';
		$plugin_slug = 'image-optimization';

		$cta_data = $this->get_plugin_cta_data( $plugin_slug, $plugin_file_path );

		if ( empty( $cta_data ) ) {
			return false;
		}

		$options = [
			'title' => esc_html__( 'Speed up your website with Image Optimizer by Elementor', 'elementor' ),
			'description' => esc_html__( 'Automatically compress and optimize images, resize larger files, or convert to WebP. Optimize images individually, in bulk, or on upload.', 'elementor' ),
			'id' => $notice_id,
			'type' => 'cta',
			'button_secondary' => [
				'text' => $cta_data['text'],
				'url' => $cta_data['url'],
				'type' => 'cta',
			],
		];

		$this->print_admin_notice( $options );

		return true;
	}

	private function is_plugin_installed( $file_path ): bool {
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}

	public function print_admin_notice( array $options, $exclude_pages = self::DEFAULT_EXCLUDED_PAGES ) {
		global $pagenow;

		if ( in_array( $pagenow, $exclude_pages, true ) ) {
			return;
		}

		$default_options = [
			'id' => null,
			'title' => '',
			'description' => '',
			'classes' => [ 'notice', 'e-notice' ], // We include WP's default notice class so it will be properly handled by WP's js handler
			'type' => '',
			'dismissible' => true,
			'icon' => 'eicon-elementor',
			'button' => [],
			'button_secondary' => [],
		];

		$options = array_replace_recursive( $default_options, $options );

		$notice_classes = $options['classes'];
		$dismiss_button = '';
		$icon = '';

		if ( $options['type'] ) {
			$notice_classes[] = 'e-notice--' . $options['type'];
		}

		$wrapper_attributes = [];

		if ( $options['dismissible'] ) {
			$label = esc_html__( 'Dismiss this notice.', 'elementor' );
			$notice_classes[] = 'e-notice--dismissible';
			$dismiss_button = '<i class="e-notice__dismiss" role="button" aria-label="' . $label . '" tabindex="0"></i>';

			$wrapper_attributes['data-nonce'] = wp_create_nonce( 'elementor_set_admin_notice_viewed' );
		}

		if ( $options['icon'] ) {
			$notice_classes[] = 'e-notice--extended';
			$icon = '<div class="e-notice__icon-wrapper"><i class="' . esc_attr( $options['icon'] ) . '" aria-hidden="true"></i></div>';
		}

		$wrapper_attributes['class'] = $notice_classes;

		if ( $options['id'] ) {
			$wrapper_attributes['data-notice_id'] = $options['id'];
		}
		?>
		<div <?php Utils::print_html_attributes( $wrapper_attributes ); ?>>
			<?php echo $dismiss_button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="e-notice__aside">
				<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="e-notice__content">
				<?php if ( $options['title'] ) { ?>
					<h3><?php echo wp_kses_post( $options['title'] ); ?></h3>
				<?php } ?>

				<?php if ( $options['description'] ) { ?>
					<p><?php echo wp_kses_post( $options['description'] ); ?></p>
				<?php } ?>

				<?php if ( ! empty( $options['button']['text'] ) || ! empty( $options['button_secondary']['text'] ) ) { ?>
					<div class="e-notice__actions">
						<?php
						foreach ( [ $options['button'], $options['button_secondary'] ] as $index => $button_settings ) {
							if ( empty( $button_settings['variant'] ) && $index ) {
								$button_settings['variant'] = 'outline';
							}

							if ( empty( $button_settings['text'] ) ) {
								continue;
							}

							$button = new Button( $button_settings );
							$button->print_button();
						} ?>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php }

	public function admin_notices() {
		$this->install_time = Plugin::$instance->get_install_time();
		$this->current_screen_id = get_current_screen()->id;

		foreach ( $this->plain_notices as $notice ) {
			$method_callback = "notice_{$notice}";
			if ( $this->$method_callback() ) {
				return;
			}
		}

		/** @var Base_Notice $notice_instance */
		foreach ( $this->get_notices() as $notice_instance ) {
			if ( ! $notice_instance->should_print() ) {
				continue;
			}

			$this->print_admin_notice( $notice_instance->get_config() );

			// It exits the method to make sure it prints only one notice.
			return;
		}
	}

	public function maybe_log_campaign() {
		if ( empty( $_GET['plg_campaign'] ) || empty( $_GET['plg_campaign_name'] ) ) {
			return;
		}

		$allowed_plgs = [
			'elementor_image_optimization_campaign',
			'elementor_ea11y_campaign',
			'elementor_site_mailer_campaign',
		];

		if ( ! in_array( $_GET['plg_campaign_name'], $allowed_plgs, true ) ) {
			return;
		}

		if ( ! isset( $_GET['plg_campaign_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['plg_campaign_nonce'] ), sanitize_key( $_GET['plg_campaign_name'] ) ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( empty( $_GET['plg_source'] ) || empty( $_GET['plg_medium'] ) ) {
			return;
		}

		$campaign_data = [
			'source' => sanitize_key( $_GET['plg_source'] ),
			'campaign' => sanitize_key( $_GET['plg_campaign'] ),
			'medium' => sanitize_key( $_GET['plg_medium'] ),
		];

		set_transient( sanitize_key( $_GET['plg_campaign_name'] ), $campaign_data, 30 * DAY_IN_SECONDS );
	}

	public static function add_plg_campaign_data( $url, $campaign_data ) {

		foreach ( [ 'name', 'campaign' ] as $key ) {
			if ( empty( $campaign_data[ $key ] ) ) {
				return $url;
			}
		}

		return add_query_arg( [
			'plg_campaign_name' => $campaign_data['name'],
			'plg_campaign' => $campaign_data['campaign'],
			'plg_source' => empty( $campaign_data['source'] ) ? '' : $campaign_data['source'],
			'plg_medium' => empty( $campaign_data['medium'] ) ? '' : $campaign_data['medium'],
			'plg_campaign_nonce' => wp_create_nonce( $campaign_data['name'] ),
		], $url );
	}

	/**
	 * @since 2.9.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'admin_notices' ], 20 );
		add_action( 'admin_action_install-plugin', [ $this, 'maybe_log_campaign' ] );
	}

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @since  2.9.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'admin-notices';
	}
}
