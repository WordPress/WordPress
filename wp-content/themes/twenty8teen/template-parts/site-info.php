<?php
/**
 * Template part for displaying site info.
 * @package Twenty8teen
 */

?>
<div <?php twenty8teen_widget_get_classes( 'site-info', true ); ?>>
  <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'twenty8teen' ) ); ?>"><?php
    /* translators: %s: CMS name, i.e. WordPress. */
    printf( esc_html__( 'Proudly powered by %s', 'twenty8teen' ), 'WordPress' );
  ?></a>
</div><!-- .site-info -->
