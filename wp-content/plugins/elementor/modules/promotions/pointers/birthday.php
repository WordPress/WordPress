<?php

namespace Elementor\Modules\Promotions\Pointers;

use Elementor\User;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Birthday {
	const PROMOTION_URL = 'https://go.elementor.com/go-pro-wordpress-notice-birthday/';
	const ELEMENTOR_POINTER_ID = 'toplevel_page_elementor';
	const SEEN_TODAY_KEY = '_elementor-2025-birthday';
	const DISMISS_ACTION_KEY = 'birthday_pointer_2025';

	public function __construct() {
		add_action( 'admin_print_footer_scripts-index.php', [ $this, 'enqueue_notice' ] );
	}

	public function enqueue_notice() {
		if ( ! $this->should_display_notice() ) {
			return;
		}

		$this->set_seen_today();
		$this->enqueue_dependencies();

		$pointer_content = '<h3>' . esc_html__( 'Elementor’s 9th Birthday sale!', 'elementor' ) . '</h3>';
		$pointer_content .= '<p>' . esc_html__( 'Celebrate Elementor’s birthday with us—exclusive deals are available now.', 'elementor' );
		$pointer_content .= sprintf(
			'<p><a class="button button-primary" href="%s" target="_blank">%s</a></p>',
			self::PROMOTION_URL,
			esc_html__( 'View Deals', 'elementor' )
		);

		$allowed_tags = [
			'h3' => [],
			'p' => [],
			'a' => [
				'class' => [],
				'target' => [ '_blank' ],
				'href' => [],
			],
		];
		?>

		<script>
			jQuery( document ).ready( function( $ ) {
				$( "#<?php echo esc_attr( self::ELEMENTOR_POINTER_ID ); ?>" ).pointer( {
					content: '<?php echo wp_kses( $pointer_content, $allowed_tags ); ?>',
					position: {
						edge: <?php echo is_rtl() ? "'right'" : "'left'"; ?>,
						align: "center"
					},
					close: function() {
						elementorCommon.ajax.addRequest( "introduction_viewed", {
							data: {
								introductionKey: '<?php echo esc_attr( static::DISMISS_ACTION_KEY ); ?>'
							}
						} );
					}
				} ).pointer( "open" );
			} );
		</script>
		<?php
	}

	public static function should_display_notice(): bool {
		return self::is_user_allowed() &&
			! self::is_dismissed() &&
			self::is_campaign_time() &&
			! self::is_already_seen_today() &&
			! Utils::has_pro();
	}

	private static function is_user_allowed(): bool {
		return current_user_can( 'manage_options' ) || current_user_can( 'edit_pages' );
	}

	private static function is_campaign_time() {
		$start = new \DateTime( '2025-06-10 12:00:00', new \DateTimeZone( 'UTC' ) );
		$end = new \DateTime( '2025-06-17 03:59:00', new \DateTimeZone( 'UTC' ) );
		$now = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );

		return $now >= $start && $now <= $end;
	}

	private static function is_already_seen_today() {
		return get_transient( self::get_user_transient_id() );
	}

	private function set_seen_today() {
		$now = time();
		$midnight = strtotime( 'tomorrow midnight' );
		$seconds_until_midnight = $midnight - $now;

		set_transient( self::get_user_transient_id(), $now, $seconds_until_midnight );
	}

	private static function get_user_transient_id(): string {
		return self::SEEN_TODAY_KEY . '_' . get_current_user_id();
	}

	private function enqueue_dependencies() {
		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_style( 'wp-pointer' );
	}

	private static function is_dismissed(): bool {
		return User::get_introduction_meta( static::DISMISS_ACTION_KEY );
	}
}
