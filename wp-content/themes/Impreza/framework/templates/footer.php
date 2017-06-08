<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$us_layout = US_Layout::instance();
?>
</div>
<!-- /CANVAS -->

<?php if ( $us_layout->footer_show_top OR $us_layout->footer_show_bottom ) { ?>

<?php do_action( 'us_before_footer' ) ?>

<?php
$footer_classes = '';
$footer_layout = us_get_option( 'footer_layout' );
if ( $footer_layout != NULL ) {
	$footer_classes .= ' layout_' . $footer_layout;
}
?>
<!-- FOOTER -->
<footer class="l-footer<?php echo $footer_classes; ?>" itemscope="itemscope" itemtype="https://schema.org/WPFooter">

<?php if ( $us_layout->footer_show_top ): ?>
	<!-- subfooter: top -->
	<div class="l-subfooter at_top">
		<div class="l-subfooter-h i-cf">

			<?php do_action( 'us_top_subfooter_start' ) ?>

			<div class="g-cols offset_medium">
			<?php
			$columns_number = (int) us_get_option( 'footer_columns', 3 );
			if ( $columns_number < 1 OR $columns_number > 4 ) {
				$columns_number = 3;
			}
			$columns_classes = array (
				1 => 'vc_col-sm-12',
				2 => 'vc_col-sm-6',
				3 => 'vc_col-sm-4',
				4 => 'vc_col-sm-3',
			);
			$columns_class = $columns_classes[$columns_number];
			$widget_names = array (
				1 => 'footer_first',
				2 => 'footer_second',
				3 => 'footer_third',
				4 => 'footer_fourth',
			);
			for ( $i = 1; $i <= $columns_number; $i ++ ) {
				?>
				<div class="<?php echo $columns_class ?>">
					<?php dynamic_sidebar( $widget_names[ $i ] ) ?>
				</div>
				<?php
			}
			?>
			</div>

			<?php do_action( 'us_top_subfooter_end' ) ?>

		</div>
	</div>
<?php endif/*( $us_layout->footer_show_top )*/; ?>

<?php if ( $us_layout->footer_show_bottom ): ?>
	<!-- subfooter: bottom -->
	<div class="l-subfooter at_bottom">
		<div class="l-subfooter-h i-cf">

			<?php do_action( 'us_bottom_subfooter_start' ) ?>

			<?php
			if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ 'us_footer_menu' ] ) ) {
				us_load_template( 'templates/elements/additional_menu', array(
					'source' => $locations[ 'us_footer_menu' ],
					'text_size' => '',
					'indents' => '',
				) );
			}
			?>

			<div class="w-copyright"><?php echo us_get_option( 'footer_copyright', '' ) ?></div>

			<?php do_action( 'us_bottom_subfooter_end' ) ?>

		</div>
	</div>
<?php endif/*( $us_layout->footer_show_bottom )*/; ?>

</footer>
<!-- /FOOTER -->

<?php do_action( 'us_after_footer' ) ?>

<?php }/*( $us_layout->footer_show_top OR $us_layout->footer_show_bottom )*/; ?>

<a class="w-header-show" href="javascript:void(0);"></a>
<a class="w-toplink" href="#" title="<?php _e( 'Back to top', 'us'); ?>"></a>
<script type="text/javascript">
	if (window.$us === undefined) window.$us = {};
	$us.canvasOptions = ($us.canvasOptions || {});
	$us.canvasOptions.disableEffectsWidth = <?php echo intval( us_get_option( 'disable_effects_width', 900 ) ) ?>;
	$us.canvasOptions.responsive = <?php echo us_get_option( 'responsive_layout', TRUE ) ? 'true' : 'false' ?>;

	$us.langOptions = ($us.langOptions || {});
	$us.langOptions.magnificPopup = ($us.langOptions.magnificPopup || {});
	$us.langOptions.magnificPopup.tPrev = '<?php _e( 'Previous (Left arrow key)', 'us' ); ?>'; // Alt text on left arrow
	$us.langOptions.magnificPopup.tNext = '<?php _e( 'Next (Right arrow key)', 'us' ); ?>'; // Alt text on right arrow
	$us.langOptions.magnificPopup.tCounter = '<?php _ex( '%curr% of %total%', 'Example: 3 of 12' , 'us' ); ?>'; // Markup for "1 of 7" counter

	$us.navOptions = ($us.navOptions || {});
	$us.navOptions.mobileWidth = <?php echo intval( us_get_option( 'menu_mobile_width', 900 ) ) ?>;
	$us.navOptions.togglable = <?php echo us_get_option( 'menu_togglable_type', TRUE ) ? 'true' : 'false' ?>;
</script>
<?php wp_footer(); ?>
</body>
</html>
