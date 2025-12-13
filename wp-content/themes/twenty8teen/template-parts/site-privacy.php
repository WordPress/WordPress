<?php
/**
 * Template part for displaying site privacy.
 * @package Twenty8teen
 */

if ( function_exists( 'get_privacy_policy_url' ) && get_privacy_policy_url() ) : ?>
<div <?php twenty8teen_widget_get_classes( 'site-privacy', true ); ?>>
  <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">
		<?php esc_html__( 'Privacy', 'twenty8teen' ); ?></a>
</div><!-- .site-privacy -->
<?php endif; ?>
