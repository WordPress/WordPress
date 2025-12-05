<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Workarounds;

use WpMatomo\Workarounds\FluentSmtp\PHPMailerProxy;

/**
 * Workarounds for bugs in the Fluent SMTP WordPress plugin.
 */
class FluentSmtp {

	private $original_phpmailer = null;

	private $was_phpmailer_replaced = false;

	/**
	 * Worksaround this bug in Fluent SMTP: https://github.com/WPManageNinja/fluent-smtp/issues/180
	 * by creating a proxy to the global PHPMailer object that disables the addAttachment() method.
	 *
	 * Should be used in a phpmailer_init action after manually adding attachments to the original
	 * PHPMailer instance.
	 */
	public function make_php_mailer_proxy( $phpmailer ) {
		if ( ! is_plugin_active( 'fluent-smtp/fluent-smtp.php' ) ) {
			return $phpmailer;
		}

		$this->was_phpmailer_replaced = true;
		$this->original_phpmailer     = $phpmailer;

		return new PHPMailerProxy( $phpmailer );
	}

	/**
	 * FluentSMTP will check if the global phpmailer object's class is actually PHPMailer, and if not, abort
	 * so we need to make sure it doesn't see our wrapped proxy again.
	 */
	public function reset_phpmailer() {
		global $phpmailer;

		if ( ! $this->was_phpmailer_replaced ) {
			return;
		}

		if ( ! is_plugin_active( 'fluent-smtp/fluent-smtp.php' ) ) {
			return;
		}

		// @codingStandardsIgnoreStart
		$phpmailer = $this->original_phpmailer;
		// @codingStandardsIgnoreEnd
	}
}
