<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Compatibility {
	public function register_hooks() {
		$this->ithemes_security();
	}

	/**
	 * refs https://github.com/matomo-org/matomo-for-wordpress/issues/131
	 * When user disables feature "Disable PHP in plugins" in system tweaks
	 * then the Matomo backend doesn't work
	 */
	private function ithemes_security() {
		if ( defined( 'MATOMO_COMPATIBIILITY_ITHEMES_SECURITY_DISABLE' ) && MATOMO_COMPATIBIILITY_ITHEMES_SECURITY_DISABLE ) {
			return;
		}
		add_filter(
			'itsec_filter_apache_server_config_modification',
			function ( $rules ) {
				// otherwise the path below won't be compatible
				// todo ideally we would make the plugins path relative and match the specific path...
				// like preg_quote(relative_wp_content_dir)...
				$is_wp_content_dir_compatible = defined( 'WP_CONTENT_DIR' )
												&& ABSPATH . 'wp-content' === rtrim( WP_CONTENT_DIR, '/' );
				if ( $rules
					 && $is_wp_content_dir_compatible
					 && is_string( $rules )
					 && strpos( $rules, 'RewriteEngine On' ) > 0
					 && strpos( $rules, 'content' ) > 0
					 && strpos( $rules, 'plugins' ) > 0 ) {
					$rules = '
	<IfModule mod_rewrite.c>
		RewriteEngine On

		# Allow Matomo Backend
		RewriteRule ^wp\-content/plugins/matomo/app/(index|piwik|matomo)\.php$ \$0 [NC,L]
	</IfModule>
' . $rules;
				}

				return $rules;
			},
			9999999991,
			$accepted_args = 1
		);
	}
}
