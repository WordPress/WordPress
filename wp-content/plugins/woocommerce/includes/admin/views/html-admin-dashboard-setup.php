<?php
/**
 * Admin View: Dashboard - Finish Setup
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="dashboard-widget-finish-setup" data-current-step="<?php echo esc_html( $step_number - 1 ); ?>" data-total-steps="<?php echo esc_html( $tasks_count ); ?>">
	<span class='progress-wrapper'>
		<svg class="circle-progress" width="17" height="17" version="1.1" xmlns="http://www.w3.org/2000/svg">
		  <circle r="6.5" cx="10" cy="10" fill="transparent" stroke-dasharray="40.859" stroke-dashoffset="0"></circle>
		  <circle class="bar" r="6.5" cx="190" cy="10" fill="transparent" stroke-dasharray="40.859" stroke-dashoffset="<?php echo esc_attr( $circle_dashoffset ); ?>" transform='rotate(-90 100 100)'></circle>
		</svg>
		<span><?php echo esc_html_e( 'Step', 'woocommerce' ); ?> <?php echo esc_html( $step_number ); ?> <?php echo esc_html_e( 'of', 'woocommerce' ); ?> <?php echo esc_html( $tasks_count ); ?></span>
	</span>

	<div class="description">
		<div>
			<?php echo esc_html_e( 'You\'re almost there! Once you complete store setup you can start receiving orders.', 'woocommerce' ); ?>
			<div><a href='<?php echo esc_attr( $button_link ); ?>' class='button button-primary'><?php echo esc_html_e( 'Start selling', 'woocommerce' ); ?></a></div>
		</div>
		<img src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/dashboard-widget-setup.png" />
	</div>
	<div class="clear"></div>
</div>

<script type="text/javascript">
	/*global jQuery */
	(function( $ ) {
		const widget = $( '.dashboard-widget-finish-setup' );
		const currentStep = widget.data( 'current-step' );
		const totalSteps = widget.data( 'total-steps' );

		$( document ).on( 'ready', function() {
			window.wcTracks.recordEvent( 'wcadmin_setup_widget_view', {
				completed_tasks: currentStep,
				total_tasks: totalSteps,
			} );
		});


		$( '.dashboard-widget-finish-setup a' ).on( 'click', function() {
			window.wcTracks.recordEvent( 'wcadmin_setup_widget_click', {
				completed_tasks: currentStep,
				total_tasks: totalSteps,
			} );
		});
	})( jQuery );
</script>
