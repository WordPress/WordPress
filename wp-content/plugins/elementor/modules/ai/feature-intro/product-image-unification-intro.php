<?php
namespace Elementor\Modules\Ai\Feature_Intro;

use Elementor\Core\Upgrade\Manager as Upgrade_Manager;
use Elementor\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Product_Image_Unification_Intro {

	const RELEASE_VERSION = '3.26.0';

	const CURRENT_POINTER_SLUG = 'e-ai-product-image-unification';

	public static function add_hooks() {
		add_action( 'admin_print_footer_scripts', [ __CLASS__, 'product_image_unification_intro_script' ] );
	}

	public static function product_image_unification_intro_script() {
		if ( static::is_dismissed() ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! isset( $screen->post_type ) || 'product' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_style( 'wp-pointer' );

		$pointer_content = '<h3>' . esc_html__( 'New! Unify pack-shots with Elementor AI', 'elementor' ) . '</h3>';
		$pointer_content .= '<p>' . esc_html__( 'Now you can process images in bulk and standardized the background and ratio - no manual editing required!', 'elementor' ) . '</p>';

		$pointer_content .= sprintf(
			'<p><button style="padding: 0; border: 0"><a class="button button-primary" href="%s" target="_blank">%s</a></button></p>',
			esc_js( 'https://go.elementor.com/wp-dash-unify-images-learn-more/' ),
			esc_html__( 'Learn more', 'elementor' )
		);

		?>
		<script>
			jQuery( document ).ready( function( $ ) {
				setTimeout( function () {
					$( '#bulk-action-selector-top' ).pointer( {
						content: '<?php echo $pointer_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>',
						position: {
							edge: <?php echo is_rtl() ? "'right'" : "'left'"; ?>,
							align: 'center'
						},
						pointerWidth: 360,
						close: function () {
							elementorCommon.ajax.addRequest( 'introduction_viewed', {
								data: {
									introductionKey: '<?php echo esc_attr( static::CURRENT_POINTER_SLUG ); ?>',
								},
							} );
						}
					} ).pointer( 'open' );
				}, 10 );
			} );
		</script>
		<?php
	}

	private static function is_dismissed() {
		return User::get_introduction_meta( static::CURRENT_POINTER_SLUG );
	}
}
