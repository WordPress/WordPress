<?php

require_once WPCF7_PLUGIN_DIR . '/includes/l10n.php';
require_once WPCF7_PLUGIN_DIR . '/includes/capabilities.php';
require_once WPCF7_PLUGIN_DIR . '/includes/filesystem.php';
require_once WPCF7_PLUGIN_DIR . '/includes/functions.php';
require_once WPCF7_PLUGIN_DIR . '/includes/formatting.php';
require_once WPCF7_PLUGIN_DIR . '/includes/pipe.php';
require_once WPCF7_PLUGIN_DIR . '/includes/pocket-holder.php';
require_once WPCF7_PLUGIN_DIR . '/includes/form-tag.php';
require_once WPCF7_PLUGIN_DIR . '/includes/form-tags-manager.php';
require_once WPCF7_PLUGIN_DIR . '/includes/shortcodes.php';
require_once WPCF7_PLUGIN_DIR . '/includes/swv/swv.php';
require_once WPCF7_PLUGIN_DIR . '/includes/contact-form-functions.php';
require_once WPCF7_PLUGIN_DIR . '/includes/contact-form-template.php';
require_once WPCF7_PLUGIN_DIR . '/includes/contact-form.php';
require_once WPCF7_PLUGIN_DIR . '/includes/mail.php';
require_once WPCF7_PLUGIN_DIR . '/includes/mail-tag.php';
require_once WPCF7_PLUGIN_DIR . '/includes/special-mail-tags.php';
require_once WPCF7_PLUGIN_DIR . '/includes/file.php';
require_once WPCF7_PLUGIN_DIR . '/includes/validation-functions.php';
require_once WPCF7_PLUGIN_DIR . '/includes/validation.php';
require_once WPCF7_PLUGIN_DIR . '/includes/submission.php';
require_once WPCF7_PLUGIN_DIR . '/includes/upgrade.php';
require_once WPCF7_PLUGIN_DIR . '/includes/integration.php';
require_once WPCF7_PLUGIN_DIR . '/includes/config-validator/validator.php';
require_once WPCF7_PLUGIN_DIR . '/includes/rest-api.php';
require_once WPCF7_PLUGIN_DIR . '/includes/block-editor/block-editor.php';
require_once WPCF7_PLUGIN_DIR . '/includes/html-formatter.php';

if ( is_admin() ) {
	require_once WPCF7_PLUGIN_DIR . '/admin/admin.php';
} else {
	require_once WPCF7_PLUGIN_DIR . '/includes/controller.php';
}


class WPCF7 {

	/**
	 * Loads modules from the modules directory.
	 */
	public static function load_modules() {
		self::load_module( 'acceptance' );
		self::load_module( 'akismet' );
		self::load_module( 'checkbox' );
		self::load_module( 'constant-contact' );
		self::load_module( 'count' );
		self::load_module( 'date' );
		self::load_module( 'disallowed-list' );
		self::load_module( 'doi-helper' );
		self::load_module( 'file' );
		self::load_module( 'flamingo' );
		self::load_module( 'hidden' );
		self::load_module( 'listo' );
		self::load_module( 'number' );
		self::load_module( 'quiz' );
		self::load_module( 'really-simple-captcha' );
		self::load_module( 'recaptcha' );
		self::load_module( 'reflection' );
		self::load_module( 'response' );
		self::load_module( 'select' );
		self::load_module( 'sendinblue' );
		self::load_module( 'stripe' );
		self::load_module( 'submit' );
		self::load_module( 'text' );
		self::load_module( 'textarea' );
		self::load_module( 'turnstile' );
	}


	/**
	 * Loads the specified module.
	 *
	 * @param string $mod Name of module.
	 * @return bool True on success, false on failure.
	 */
	protected static function load_module( $mod ) {
		return false
			|| wpcf7_include_module_file( $mod . '/' . $mod . '.php' )
			|| wpcf7_include_module_file( $mod . '.php' );
	}


	/**
	 * Retrieves a named entry from the option array of Contact Form 7.
	 *
	 * @param string $name Array item key.
	 * @param mixed $default_value Optional. Default value to return if the entry
	 *                             does not exist. Default false.
	 * @return mixed Array value tied to the $name key. If nothing found,
	 *               the $default_value value will be returned.
	 */
	public static function get_option( $name, $default_value = false ) {
		$option = get_option( 'wpcf7' );

		if ( false === $option ) {
			return $default_value;
		}

		if ( isset( $option[$name] ) ) {
			return $option[$name];
		} else {
			return $default_value;
		}
	}


	/**
	 * Update an entry value on the option array of Contact Form 7.
	 *
	 * @param string $name Array item key.
	 * @param mixed $value Option value.
	 */
	public static function update_option( $name, $value ) {
		$old_option = get_option( 'wpcf7' );
		$old_option = ( false === $old_option ) ? array() : (array) $old_option;

		update_option( 'wpcf7',
			array_merge( $old_option, array( $name => $value ) )
		);

		do_action( 'wpcf7_update_option', $name, $value, $old_option );
	}
}


add_action( 'plugins_loaded', 'wpcf7', 10, 0 );

/**
 * Loads modules and registers WordPress shortcodes.
 */
function wpcf7() {
	WPCF7::load_modules();

	add_shortcode( 'contact-form-7', 'wpcf7_contact_form_tag_func' );
	add_shortcode( 'contact-form', 'wpcf7_contact_form_tag_func' );
}


add_action( 'init', 'wpcf7_init', 10, 0 );

/**
 * Registers post types for contact forms.
 */
function wpcf7_init() {
	wpcf7_get_request_uri();
	wpcf7_register_post_types();

	do_action( 'wpcf7_init' );
}


add_action( 'admin_init', 'wpcf7_upgrade', 10, 0 );

/**
 * Upgrades option data when necessary.
 */
function wpcf7_upgrade() {
	$old_ver = WPCF7::get_option( 'version', '0' );
	$new_ver = WPCF7_VERSION;

	if ( $old_ver === $new_ver ) {
		return;
	}

	do_action( 'wpcf7_upgrade', $new_ver, $old_ver );

	WPCF7::update_option( 'version', $new_ver );
}


add_action( 'activate_' . WPCF7_PLUGIN_BASENAME, 'wpcf7_install', 10, 0 );

/**
 * Callback tied to plugin activation action hook. Attempts to create
 * initial user dataset.
 */
function wpcf7_install() {
	if ( $opt = get_option( 'wpcf7' ) ) {
		return;
	}

	wpcf7_register_post_types();
	wpcf7_upgrade();

	if ( get_posts( array( 'post_type' => 'wpcf7_contact_form' ) ) ) {
		return;
	}

	$contact_form = WPCF7_ContactForm::get_template(
		array(
			'title' =>
				/* translators: title of your first contact form. %d: number fixed to '1' */
				sprintf( __( 'Contact form %d', 'contact-form-7' ), 1 ),
		)
	);

	$contact_form->save();

	WPCF7::update_option( 'bulk_validate',
		array(
			'timestamp' => time(),
			'version' => WPCF7_VERSION,
			'count_valid' => 1,
			'count_invalid' => 0,
		)
	);
}
