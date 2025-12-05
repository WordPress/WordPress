<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Piwik\Common;
use Piwik\Mail;
use WP_Error;
use WpMatomo;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Email {

	/**
	 * @var WP_Error|null
	 */
	private $wp_email_error;

	private $wp_content_type = null;
	/**
	 * @var Mail
	 */
	private $mail;

	public function on_error( $error ) {
		$this->wp_email_error = $error;
	}

	public function set_content_type( $content_type ) {
		if ( ! empty( $this->wp_content_type ) ) {
			return $this->wp_content_type;
		}

		return $content_type;
	}

	public function send( Mail $mail ) {
		$this->wp_content_type = null;
		$this->wp_email_error  = null;

		$this->mail = $mail;

		if ( $mail->getBodyHtml() ) {
			$content               = $mail->getBodyHtml();
			$this->wp_content_type = 'text/html';
		} elseif ( $mail->getBodyText() ) {
			$content               = $mail->getBodyText();
			$this->wp_content_type = 'text/plain';
		} else {
			// seems no content...
			$content = '';
		}

		$attachments = $mail->getAttachments();

		$recipients = array_keys( $mail->getRecipients() );

		$this->send_mail_through_wordpress( $recipients, $mail->getSubject(), $content, $attachments );
	}

	private function remember_mail_sent() {
		$history = WpMatomo::$settings->get_global_option( 'mail_history' );
		if ( empty( $history ) || ! is_array( $history ) ) {
			$history = [];
		}

		// allows us to see if there is a WP Mail issue or a Matomo issue
		array_unshift( $history, gmdate( 'Y-m-d H:i:s', time() ) );
		$history = array_slice( $history, 0, 3 ); // keep only the last 3 versions
		WpMatomo::$settings->set_global_option( 'mail_history', $history );
		WpMatomo::$settings->save();
	}

	private function send_mail_through_wordpress( $recipients, $subject, $content, $attachments ) {
		$this->wp_email_error = null;

		add_action( 'wp_mail_failed', [ $this, 'on_error' ] );
		add_filter( 'wp_mail_content_type', [ $this, 'set_content_type' ] );

		$this->remember_mail_sent();

		$header = '';

		if ( ! empty( $attachments ) ) {
			$random_id       = Common::generateUniqId();
			$header          = 'X-Matomo: ' . $random_id;
			$executed_action = false;

			$fluent_smtp_workaround = new WpMatomo\Workarounds\FluentSmtp();

			add_action(
				'phpmailer_init',
				function ( &$phpmailer ) use ( $attachments, $subject, $random_id, &$executed_action, $fluent_smtp_workaround ) {
					/** @var PHPMailer $phpmailer */
					if ( $executed_action ) {
						return; // already done, do not execute another time
					}
					$executed_action = true;
					$match           = false;
					foreach ( $phpmailer->getCustomHeaders() as $header ) {
						if ( isset( $header[0] ) && isset( $header[1] ) &&
							 is_string( $header[0] ) && is_string( $header[1] ) &&
							 'x-matomo' === Common::mb_strtolower( $header[0] ) &&
							 trim( $header[1] ) === $random_id
						) {
							$match = true;
						}
					}
					if ( ! $match ) {
						return; // attachments aren't for this mail
					}

					$base64_encoding = class_exists( PHPMailer::class ) ? PHPMailer::ENCODING_BASE64 : 'base64';

					foreach ( $attachments as $attachment ) {
						if ( ! empty( $attachment['cid'] ) ) {
							$phpmailer->addStringEmbeddedImage(
								$attachment['content'],
								$attachment['cid'],
								$attachment['filename'],
								$base64_encoding,
								$attachment['mimetype']
							);
						} else {
							$phpmailer->addStringAttachment(
								$attachment['content'],
								$attachment['filename'],
								$base64_encoding,
								$attachment['mimetype']
							);

							// smtp2go does not correctly detect attachments add via PHPMailer::addStringAttachment.
							// addStringAttachment() will set the attachment's "cid" to `0`, but in SMTP2GOMailer.php
							// inline attachments are only detected if the cid is non-empty and is a string. further,
							// the cid is used as the filename.
							//
							// to workaround this, we manually set the cid to the attachment filename.
							if ( is_plugin_active( 'smtp2go/smtp2go-wordpress-plugin.php' ) ) {
								$attachments                                 = $phpmailer->getAttachments();
								$attachments[ count( $attachments ) - 1 ][7] = $attachment['filename'];

								$property = new \ReflectionProperty( get_class( $phpmailer ), 'attachment' );
								$property->setAccessible( true );
								$property->setValue( $phpmailer, $attachments );
							}
						}
					}

					$phpmailer = $fluent_smtp_workaround->make_php_mailer_proxy( $phpmailer );
				}
			);
		}

		$success = wp_mail( $recipients, $subject, $content, $header );

		remove_action( 'wp_mail_failed', [ $this, 'on_error' ] );
		remove_filter( 'wp_mail_content_type', [ $this, 'set_content_type' ] );

		if ( isset( $fluent_smtp_workaround ) ) {
			$fluent_smtp_workaround->reset_phpmailer();
		}

		if ( ! $success ) {
			$message = 'Error unknown.';
			if ( ! empty( $this->wp_email_error ) && is_object( $this->wp_email_error ) && $this->wp_email_error instanceof WP_Error ) {
				$message = $this->wp_email_error->get_error_message();
			}
			if ( $this->mail && $this->mail->getAttachments() ) {
				$message .= ' (has attachments)';
			}
			if ( $this->wp_content_type ) {
				$message .= ' (type ' . $this->wp_content_type . ')';
			}
			$logger = new Logger();
			$logger->log_exception( 'mail_error', new Exception( $message ) );
			$logger->log( 'Matomo mail failed with subject ' . $subject . ': ' . $message );
		}

		$this->wp_content_type = null;
		$this->wp_email_error  = null;
	}
}
