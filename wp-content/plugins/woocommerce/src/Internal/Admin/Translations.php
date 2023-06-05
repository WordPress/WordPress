<?php
/**
 * Register the scripts, and handles items needed for managing translations within WooCommerce Admin.
 */

namespace Automattic\WooCommerce\Internal\Admin;

use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Internal\Admin\Loader;

/**
 * Translations Class.
 */
class Translations {

	/**
	 * Class instance.
	 *
	 * @var Translations instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Hooks added here should be removed in `wc_admin_initialize` via the feature plugin.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'potentially_load_translation_script_file' ), 15 );

		// Combine JSON translation files (from chunks) when language packs are updated.
		add_action( 'upgrader_process_complete', array( $this, 'combine_translation_chunk_files' ), 10, 2 );

		// Handler for WooCommerce and WooCommerce Admin plugin activation.
		add_action( 'woocommerce_activated_plugin', array( $this, 'potentially_generate_translation_strings' ) );
		add_action( 'activated_plugin', array( $this, 'potentially_generate_translation_strings' ) );
	}

	/**
	 * Generate a filename to cache translations from JS chunks.
	 *
	 * @param string $domain Text domain.
	 * @param string $locale Locale being retrieved.
	 * @return string Filename.
	 */
	private function get_combined_translation_filename( $domain, $locale ) {
		$filename = implode( '-', array( $domain, $locale, WC_ADMIN_APP ) ) . '.json';

		return $filename;
	}

	/**
	 * Combines data from translation chunk files based on officially downloaded file format.
	 *
	 * @param array $json_i18n_filenames List of JSON chunk files.
	 * @return array Combined translation chunk data.
	 */
	private function combine_official_translation_chunks( $json_i18n_filenames ) {
		// the filesystem object should be hooked up.
		global $wp_filesystem;
		$combined_translation_data = array();

		foreach ( $json_i18n_filenames as $json_filename ) {
			if ( ! $wp_filesystem->is_readable( $json_filename ) ) {
				continue;
			}

			$file_contents = $wp_filesystem->get_contents( $json_filename );
			$chunk_data    = \json_decode( $file_contents, true );

			if ( empty( $chunk_data ) ) {
				continue;
			}

			$reference_file = $chunk_data['comment']['reference'];

			// Only combine "app" files (not scripts registered with WP).
			if (
				false === strpos( $reference_file, WC_ADMIN_DIST_JS_FOLDER . 'app/index.js' ) &&
				false === strpos( $reference_file, WC_ADMIN_DIST_JS_FOLDER . 'chunks/' )
			) {
				continue;
			}

			if ( empty( $combined_translation_data ) ) {
				// Use the first translation file as the base structure.
				$combined_translation_data = $chunk_data;
			} else {
				// Combine all messages from all chunk files.
				$combined_translation_data['locale_data']['messages'] = array_merge(
					$combined_translation_data['locale_data']['messages'],
					$chunk_data['locale_data']['messages']
				);
			}
		}

		// Remove inaccurate reference comment.
		unset( $combined_translation_data['comment'] );
		return $combined_translation_data;
	}

	/**
	 * Combines data from translation chunk files based on user-generated file formats,
	 * such as wp-cli tool or Loco Translate plugin.
	 *
	 * @param array $json_i18n_filenames List of JSON chunk files.
	 * @return array Combined translation chunk data.
	 */
	private function combine_user_translation_chunks( $json_i18n_filenames ) {
		// the filesystem object should be hooked up.
		global $wp_filesystem;
		$combined_translation_data = array();

		foreach ( $json_i18n_filenames as $json_filename ) {
			if ( ! $wp_filesystem->is_readable( $json_filename ) ) {
				continue;
			}

			$file_contents = $wp_filesystem->get_contents( $json_filename );
			$chunk_data    = \json_decode( $file_contents, true );

			if ( empty( $chunk_data ) ) {
				continue;
			}

			$reference_file = $chunk_data['source'];

			// Only combine "app" files (not scripts registered with WP).
			if (
				false === strpos( $reference_file, WC_ADMIN_DIST_JS_FOLDER . 'app/index.js' ) &&
				false === strpos( $reference_file, WC_ADMIN_DIST_JS_FOLDER . 'chunks/' )
			) {
				continue;
			}

			if ( empty( $combined_translation_data ) ) {
				// Use the first translation file as the base structure.
				$combined_translation_data = $chunk_data;
			} else {
				// Combine all messages from all chunk files.
				$combined_translation_data['locale_data']['woocommerce'] = array_merge(
					$combined_translation_data['locale_data']['woocommerce'],
					$chunk_data['locale_data']['woocommerce']
				);
			}
		}

		// Remove inaccurate reference comment.
		unset( $combined_translation_data['source'] );
		return $combined_translation_data;
	}

	/**
	 * Find and combine translation chunk files.
	 *
	 * Only targets files that aren't represented by a registered script (e.g. not passed to wp_register_script()).
	 *
	 * @param string $lang_dir Path to language files.
	 * @param string $domain Text domain.
	 * @param string $locale Locale being retrieved.
	 * @return array Combined translation chunk data.
	 */
	private function get_translation_chunk_data( $lang_dir, $domain, $locale ) {
		// So long as this function is called during the 'upgrader_process_complete' action,
		// the filesystem object should be hooked up.
		global $wp_filesystem;

		// Grab all JSON files in the current language pack.
		$json_i18n_filenames       = glob( $lang_dir . $domain . '-' . $locale . '-*.json' );
		$combined_translation_data = array();

		if ( false === $json_i18n_filenames ) {
			return $combined_translation_data;
		}

		// Use first JSON file to determine file format. This check is required due to
		// file format difference between official language files and user translated files.
		$format_determine_file = reset( $json_i18n_filenames );

		if ( ! $wp_filesystem->is_readable( $format_determine_file ) ) {
			return $combined_translation_data;
		}

		$file_contents         = $wp_filesystem->get_contents( $format_determine_file );
		$format_determine_data = \json_decode( $file_contents, true );

		if ( empty( $format_determine_data ) ) {
			return $combined_translation_data;
		}

		if ( isset( $format_determine_data['comment'] ) ) {
			return $this->combine_official_translation_chunks( $json_i18n_filenames );
		} elseif ( isset( $format_determine_data['source'] ) ) {
			return $this->combine_user_translation_chunks( $json_i18n_filenames );
		} else {
			return $combined_translation_data;
		}
	}

	/**
	 * Combine and save translations for a specific locale.
	 *
	 * Note that this assumes \WP_Filesystem is already initialized with write access.
	 *
	 * @param string $language_dir Path to language files.
	 * @param string $plugin_domain Text domain.
	 * @param string $locale Locale being retrieved.
	 */
	private function build_and_save_translations( $language_dir, $plugin_domain, $locale ) {
		global $wp_filesystem;
		$translations_from_chunks = $this->get_translation_chunk_data( $language_dir, $plugin_domain, $locale );

		if ( empty( $translations_from_chunks ) ) {
			return;
		}

		$cache_filename          = $this->get_combined_translation_filename( $plugin_domain, $locale );
		$chunk_translations_json = wp_json_encode( $translations_from_chunks );

		// Cache combined translations strings to a file.
		$wp_filesystem->put_contents( $language_dir . $cache_filename, $chunk_translations_json );
	}

	/**
	 * Combine translation chunks when plugin is activated.
	 *
	 * This function combines JSON translation data auto-extracted by GlotPress
	 * from Webpack-generated JS chunks into a single file. This is necessary
	 * since the JS chunks are not known to WordPress via wp_register_script()
	 * and wp_set_script_translations().
	 */
	private function generate_translation_strings() {
		$plugin_domain = explode( '/', plugin_basename( __FILE__ ) )[0];
		$locale        = determine_locale();
		$lang_dir      = WP_LANG_DIR . '/plugins/';

		// Bail early if not localized.
		if ( 'en_US' === $locale ) {
			return;
		}

		if ( ! function_exists( 'get_filesystem_method' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$access_type = get_filesystem_method();
		if ( 'direct' === $access_type ) {
			\WP_Filesystem();
			$this->build_and_save_translations( $lang_dir, $plugin_domain, $locale );
		} else {
			// I'm reluctant to add support for other filesystems here as it would require
			// user's input on activating plugin - which I don't think is common.
			return;
		}
	}

	/**
	 * Loads the required translation scripts on the correct pages.
	 */
	public function potentially_load_translation_script_file() {
		if ( ! PageController::is_admin_or_embed_page() ) {
			return;
		}

		// Grab translation strings from Webpack-generated chunks.
		add_filter( 'load_script_translation_file', array( $this, 'load_script_translation_file' ), 10, 3 );
	}

	/**
	 * Load translation strings from language packs for dynamic imports.
	 *
	 * @param string $file File location for the script being translated.
	 * @param string $handle Script handle.
	 * @param string $domain Text domain.
	 *
	 * @return string New file location for the script being translated.
	 */
	public function load_script_translation_file( $file, $handle, $domain ) {
		// Make sure the main app script is being loaded.
		if ( WC_ADMIN_APP !== $handle ) {
			return $file;
		}

		// Make sure we're handing the correct domain (could be woocommerce or woocommerce-admin).
		$plugin_domain = explode( '/', plugin_basename( __FILE__ ) )[0];
		if ( $plugin_domain !== $domain ) {
			return $file;
		}

		$locale         = determine_locale();
		$cache_filename = $this->get_combined_translation_filename( $domain, $locale );

		return WP_LANG_DIR . '/plugins/' . $cache_filename;
	}

	/**
	 * Run when plugin is activated (can be WooCommerce or WooCommerce Admin).
	 *
	 * @param string $filename Activated plugin filename.
	 */
	public function potentially_generate_translation_strings( $filename ) {
		$plugin_domain           = explode( '/', plugin_basename( __FILE__ ) )[0];
		$activated_plugin_domain = explode( '/', $filename )[0];

		// Ensure we're only running only on activation hook that originates from our plugin.
		if ( $plugin_domain === $activated_plugin_domain ) {
			$this->generate_translation_strings();
		}
	}

	/**
	 * Combine translation chunks when files are updated.
	 *
	 * This function combines JSON translation data auto-extracted by GlotPress
	 * from Webpack-generated JS chunks into a single file that can be used in
	 * subsequent requests. This is necessary since the JS chunks are not known
	 * to WordPress via wp_register_script() and wp_set_script_translations().
	 *
	 * @param Language_Pack_Upgrader $instance Upgrader instance.
	 * @param array                  $hook_extra Info about the upgraded language packs.
	 */
	public function combine_translation_chunk_files( $instance, $hook_extra ) {
		if (
			! is_a( $instance, 'Language_Pack_Upgrader' ) ||
			! isset( $hook_extra['translations'] ) ||
			! is_array( $hook_extra['translations'] )
		) {
			return;
		}

		// Make sure we're handing the correct domain (could be woocommerce or woocommerce-admin).
		$plugin_domain = explode( '/', plugin_basename( __FILE__ ) )[0];
		$locales       = array();
		$language_dir  = WP_LANG_DIR . '/plugins/';

		// Gather the locales that were updated in this operation.
		foreach ( $hook_extra['translations'] as $translation ) {
			if (
				'plugin' === $translation['type'] &&
				$plugin_domain === $translation['slug']
			) {
				$locales[] = $translation['language'];
			}
		}

		// Build combined translation files for all updated locales.
		foreach ( $locales as $locale ) {
			// So long as this function is hooked to the 'upgrader_process_complete' action,
			// WP_Filesystem should be hooked up to be able to call build_and_save_translations.
			$this->build_and_save_translations( $language_dir, $plugin_domain, $locale );
		}
	}
}
