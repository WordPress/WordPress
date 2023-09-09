<?php
/**
 * Displays the footer widget area
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
<<<<<<< HEAD
 * @since Twenty Nineteen 1.0
 */

if ( is_active_sidebar( 'sidebar-1' ) ) :
	?>

	<aside class="widget-area" aria-label="<?php esc_attr_e( 'Footer', 'twentynineteen' ); ?>">
		<?php
		if ( is_active_sidebar( 'sidebar-1' ) ) {
			?>
					<div class="widget-column footer-widget-1">
					<?php dynamic_sidebar( 'sidebar-1' ); ?>
					</div>
				<?php
		}
		?>
	</aside><!-- .widget-area -->

	<?php
endif;
=======
 * @since 1.0.0
 */

if ( is_active_sidebar( 'sidebar-1' ) ) : ?>

	<aside class="widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Footer', 'twentynineteen' ); ?>">
		<?php
			if ( is_active_sidebar( 'sidebar-1' ) ) {
				?>
					<div class="widget-column footer-widget-1">
						<?php dynamic_sidebar( 'sidebar-1' ); ?>
					</div>
				<?php
			}
		?>
	</aside><!-- .widget-area -->

<?php endif; ?>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
