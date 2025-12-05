<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Application\File;

use WPSEO_Shortlinker;
use Yoast\WP\SEO\Llms_Txt\Application\File\Commands\Populate_File_Command_Handler;
use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Class File_Failure_Notification_Presenter.
 */
class File_Failure_Notification_Presenter extends Abstract_Presenter {

	/**
	 * Returns the notification as an HTML string.
	 *
	 * @return string The notification in an HTML string representation.
	 */
	public function present() {
		$notification_text  = '<p>';
		$notification_text .= $this->get_message();
		$notification_text .= '</p>';

		return $notification_text;
	}

	/**
	 * Returns the message to show.
	 *
	 * @return string The message.
	 */
	protected function get_message() {
		$reason = \get_option( Populate_File_Command_Handler::GENERATION_FAILURE_OPTION, false );
		switch ( $reason ) {
			case 'not_managed_by_yoast_seo':
				$message = \sprintf(
				/* translators: 1: Link start tag to the WordPress Reading Settings page, 2: Link closing tag. */
					\esc_html__( 'An existing llms.txt file wasn\'t created by Yoast or has been edited manually. Yoast won\'t overwrite it. %1$sDelete it manually%2$s or turn off this feature.', 'wordpress-seo' ),
					'<a href="' . \esc_url( WPSEO_Shortlinker::get( 'https://yoa.st/llms-txt-file-deletion' ) ) . '" target="_blank" rel="noopener noreferrer">',
					'</a>'
				);
				break;
			case 'filesystem_permissions':
				$message =
					\__( 'You have activated the Yoast llms.txt feature, but we couldn\'t generate an llms.txt file. It looks like there aren\'t sufficient permissions on the web server\'s filesystem.', 'wordpress-seo' );
				break;
			default:
				$message = \__( 'You have activated the Yoast llms.txt feature, but we couldn\'t generate an llms.txt file, for unknown reasons.', 'wordpress-seo' );
				break;
		}

		return \sprintf(
			'<strong>%1$s</strong> %2$s',
			\esc_html__( 'Your llms.txt file couldn\'t be auto-generated', 'wordpress-seo' ),
			$message
		);
	}
}
