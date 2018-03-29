<?php

class UM_API {

	public $is_filtering;

	public $addons = null;

	function __construct() {

		$this->is_filtering = 0;

		require_once um_path . 'core/um-short-functions.php';

		if (is_admin()) {
			require_once um_path . 'admin/core/um-admin-upgrade.php';
			require_once um_path . 'admin/um-admin-init.php';
		}

		add_action('init', array(&$this, 'init'), 0);

		add_action('init', array(&$this, 'load_addons'));

		$this->honeypot = 'request';

		$this->available_languages = array(
			'en_US' => 'English (US)',
			'es_ES' => 'Español',
			'es_MX' => 'Español (México)',
			'fr_FR' => 'Français',
			'it_IT' => 'Italiano',
			'de_DE' => 'Deutsch',
			'nl_NL' => 'Nederlands',
			'pt_BR' => 'Português do Brasil',
			'fi_FI' => 'Suomi',
			'ro_RO' => 'Română',
			'da_DK' => 'Dansk',
			'sv_SE' => 'Svenska',
			'pl_PL' => 'Polski',
			'cs_CZ' => 'Czech',
			'el' => 'Greek',
			'id_ID' => 'Indonesian',
			'zh_CN' => '简体中文',
			'ru_RU' => 'Русский',
			'tr_TR' => 'Türkçe',
			'fa_IR' => 'Farsi',
			'he_IL' => 'Hebrew',
			'ar' => 'العربية',
		);

		$this->addons['bp_avatar_transfer'] = array(
			__('BuddyPress Avatar Transfer', 'ultimate-member'),
			__('This add-on enables you to migrate your custom user photos from BuddyPress to use with Ultimate Member.', 'ultimate-member'),
		);

		$this->addons['gravatar_transfer'] = array(
			__('Gravatar Transfer', 'ultimate-member'),
			__('This add-on enables you to link gravatar photos to user accounts with their email address.', 'ultimate-member'),
		);

		$this->addons['generate_random_users'] = array(
			__('Generate Dummies', 'ultimate-member'),
			__('This add-on enables you to generate dummies.', 'ultimate-member'),
		);

		$this->addons['install_info'] = array(
			__('System Info', 'ultimate-member'),
			__('This add-on enables you to download system information file.', 'ultimate-member'),
		);

		// include widgets
		require_once um_path . 'core/widgets/um-search-widget.php';

		// init widgets
		add_action( 'widgets_init', array(&$this, 'widgets_init' ) );

	}

	/***
		***	@Load add-ons
	*/
	function load_addons() {
		global $ultimatemember;
		if ( isset( $ultimatemember->addons ) && is_array( $ultimatemember->addons ) ) {
			foreach ( $ultimatemember->addons as $addon => $name ) {
				if ( um_get_option('addon_' . $addon) == 1 ) {
					if( file_exists( um_path . 'addons/' . $addon . '.php' ) ){
						include_once um_path . 'addons/' . $addon . '.php';
					}
				}
			}
		}
	}

	/***
		***	@Init
	*/
	function init() {

		ob_start();

		require_once um_path . 'core/um-api.php';
		require_once um_path . 'core/um-rewrite.php';
		require_once um_path . 'core/um-setup.php';
		require_once um_path . 'core/um-uninstall.php';
		require_once um_path . 'core/um-fonticons.php';
		require_once um_path . 'core/um-login.php';
		require_once um_path . 'core/um-register.php';
		require_once um_path . 'core/um-enqueue.php';
		require_once um_path . 'core/um-shortcodes.php';
		require_once um_path . 'core/um-account.php';
		require_once um_path . 'core/um-password.php';
		require_once um_path . 'core/um-fields.php';
		require_once um_path . 'core/um-form.php';
		require_once um_path . 'core/um-user.php';
		require_once um_path . 'core/um-user-posts.php';
		require_once um_path . 'core/um-profile.php';
		require_once um_path . 'core/um-query.php';
		require_once um_path . 'core/um-datetime.php';
		require_once um_path . 'core/um-chart.php';
		require_once um_path . 'core/um-builtin.php';
		require_once um_path . 'core/um-files.php';
		require_once um_path . 'core/um-taxonomies.php';
		require_once um_path . 'core/um-validation.php';
		require_once um_path . 'core/um-navmenu.php';
		require_once um_path . 'core/um-menu.php';
		require_once um_path . 'core/um-access.php';
		require_once um_path . 'core/um-permalinks.php';
		require_once um_path . 'core/um-mail.php';
		require_once um_path . 'core/um-members.php';
		require_once um_path . 'core/um-logout.php';
		require_once um_path . 'core/um-modal.php';
		require_once um_path . 'core/um-cron.php';
		require_once um_path . 'core/um-tracking.php';

		if ( ! class_exists('Mobile_Detect') ) {
			require_once um_path . 'core/lib/mobiledetect/Mobile_Detect.php';
		}

		require_once um_path . 'core/um-actions-form.php';
		require_once um_path . 'core/um-actions-access.php';
		require_once um_path . 'core/um-actions-wpadmin.php';
		require_once um_path . 'core/um-actions-core.php';
		require_once um_path . 'core/um-actions-ajax.php';
		require_once um_path . 'core/um-actions-login.php';
		require_once um_path . 'core/um-actions-register.php';
		require_once um_path . 'core/um-actions-profile.php';
		require_once um_path . 'core/um-actions-account.php';
		require_once um_path . 'core/um-actions-password.php';
		require_once um_path . 'core/um-actions-members.php';
		require_once um_path . 'core/um-actions-global.php';
		require_once um_path . 'core/um-actions-user.php';
		require_once um_path . 'core/um-actions-save-profile.php';
		require_once um_path . 'core/um-actions-misc.php';

		require_once um_path . 'core/um-filters-language.php';
		require_once um_path . 'core/um-filters-login.php';
		require_once um_path . 'core/um-filters-fields.php';
		require_once um_path . 'core/um-filters-files.php';
		require_once um_path . 'core/um-filters-navmenu.php';
		require_once um_path . 'core/um-filters-avatars.php';
		require_once um_path . 'core/um-filters-arguments.php';
		require_once um_path . 'core/um-filters-user.php';
		require_once um_path . 'core/um-filters-members.php';
		require_once um_path . 'core/um-filters-profile.php';
		require_once um_path . 'core/um-filters-account.php';
		require_once um_path . 'core/um-filters-misc.php';
		require_once um_path . 'core/um-filters-addons.php';
		require_once um_path . 'core/um-filters-commenting.php';

		/* initialize UM */
		$this->api = new UM_REST_API();
		$this->rewrite = new UM_Rewrite();
		$this->setup = new UM_Setup();
		$this->uninstall = new UM_Uninstall();
		$this->icons = new UM_FontIcons();
		$this->styles = new UM_Enqueue();
		$this->shortcodes = new UM_Shortcodes();
		$this->account = new UM_Account();
		$this->password = new UM_Password();
		$this->login = new UM_Login();
		$this->register = new UM_Register();
		$this->fields = new UM_Fields();
		$this->user = new UM_User();
		$this->user_posts = new UM_User_Posts();
		$this->profile = new UM_Profile();
		$this->datetime = new UM_DateTime();
		$this->chart = new UM_Chart();
		$this->builtin = new UM_Builtin();
		$this->form = new UM_Form();
		$this->files = new UM_Files();
		$this->taxonomies = new UM_Taxonomies();
		$this->validation = new UM_Validation();
		$this->query = new UM_Query();
		$this->menu = new UM_Menu();
		$this->access = new UM_Access();
		$this->permalinks = new UM_Permalinks();
		$this->mail = new UM_Mail();
		$this->members = new UM_Members();
		$this->logout = new UM_Logout();
		$this->modal = new UM_Modal();
		$this->cron = new UM_Cron();
		$this->tracking = new UM_Tracking();

		$this->mobile = new Mobile_Detect;

		$this->options = get_option('um_options');

		$language_domain = 'ultimatemember';
		$language_domain = apply_filters("um_language_textdomain", $language_domain );

		$language_locale = ( get_locale() != '' ) ? get_locale() : 'en_US';
		$language_locale = apply_filters("um_language_locale", $language_locale );

		$language_file = WP_LANG_DIR . '/plugins/' . $language_domain . '-' . $language_locale . '.mo';
		$language_file = apply_filters("um_language_file", $language_file );

		load_textdomain( $language_domain, $language_file );

		if ( ! get_option('show_avatars') ) {
			update_option('show_avatars', 1);
		}

	}

	function widgets_init() {
		register_widget( 'um_search_widget' );
	}

}

global $ultimatemember;

$ultimatemember = new UM_API();
