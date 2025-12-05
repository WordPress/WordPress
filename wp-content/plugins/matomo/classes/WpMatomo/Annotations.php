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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Annotations {
	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @param Settings $settings
	 */
	public function __construct( $settings ) {
		$this->settings = $settings;
		$this->logger   = new Logger();
	}

	public function register_hooks() {
		add_action( 'transition_post_status', [ $this, 'add_annotation' ], 10, 3 );
	}

	/**
	 * Identify new posts if an annotation is required
	 * and create Piwik annotation
	 *
	 * @param string $new_status new post status
	 * @param string $old_status old post status
	 * @param object $post current post object
	 */
	public function add_annotation( $new_status, $old_status, $post ) {
		if ( ! $this->settings->is_tracking_enabled() ) {
			return;
		}

		$enabled_post_types = $this->settings->get_global_option( 'add_post_annotations' );

		if ( empty( $enabled_post_types[ $post->post_type ] ) ) {
			return;
		}

		if ( 'publish' === $new_status
			 && 'publish' !== $old_status ) {
			$site   = new Site();
			$idsite = $site->get_current_matomo_site_id();

			if ( ! $idsite ) {
				return; // no site we can add it to
			}

			try {
				Bootstrap::do_bootstrap();

				$logger = $this->logger;
				\Piwik\Access::doAsSuperUser(
					function () use ( $post, $logger, $idsite ) {
						$note = esc_html__( 'Published:', 'matomo' ) . ' ' . $post->post_title . ' - URL: ' . get_permalink( $post->ID );
						\Piwik\Plugins\Annotations\API::unsetInstance();// make sure latest instance will be loaded with all up to date dependencies... mainly needed for tests
						$id = \Piwik\Plugins\Annotations\API::getInstance()->add( $idsite, gmdate( 'Y-m-d' ), $note );
						$logger->log( 'Add post annotation. ' . $note . ' - ' . wp_json_encode( $id ) );
					}
				);
			} catch ( Exception $e ) {
				$this->logger->log( 'Add post annotation failed: ' . $e->getMessage() );

				return;
			}
		}
	}
}
