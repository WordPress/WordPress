<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div class="container">
 *
 * @package Twenty Minutes
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php if ( function_exists( 'wp_body_open' ) ) {
  wp_body_open();
} else {
  do_action( 'wp_body_open' );
} ?>

<?php if ( get_theme_mod('twenty_minutes_preloader', false) != "") { ?>
  <div id="preloader">
    <div id="status">&nbsp;</div>
  </div>
<?php }?>

<a class="screen-reader-text skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'twenty-minutes' ); ?></a>

<div id="pageholder" <?php if( get_theme_mod( 'twenty_minutes_box_layout', false) != "" ) { echo 'class="boxlayout"'; } ?>>

<?php if ( get_theme_mod('twenty_minutes_topbar', false) != "") { ?>
  <div class="header-top">
    <div class="row m-0">
      <div class="col-lg-7 col-md-5">
        <div class="social-icons">
          <?php if ( get_theme_mod('twenty_minutes_fb_link') != "") { ?>
            <a title="<?php echo esc_attr('facebook', 'twenty-minutes'); ?>" target="_blank" href="<?php echo esc_url(get_theme_mod('twenty_minutes_fb_link')); ?>"><i class="fab fa-facebook-f"></i></a>
          <?php } ?>
          <?php if ( get_theme_mod('twenty_minutes_twitt_link') != "") { ?>
            <a title="<?php echo esc_attr('twitter', 'twenty-minutes'); ?>" target="_blank" href="<?php echo esc_url(get_theme_mod('twenty_minutes_twitt_link')); ?>"><i class="fab fa-twitter"></i></a>
          <?php } ?>
          <?php if ( get_theme_mod('twenty_minutes_linked_link') != "") { ?>
            <a title="<?php echo esc_attr('linkedin', 'twenty-minutes'); ?>" target="_blank" href="<?php echo esc_url(get_theme_mod('twenty_minutes_linked_link')); ?>"><i class="fab fa-linkedin-in"></i></a>
          <?php } ?>
          <?php if ( get_theme_mod('twenty_minutes_insta_link') != "") { ?>
            <a title="<?php echo esc_attr('instagram', 'twenty-minutes'); ?>" target="_blank" href="<?php echo esc_url(get_theme_mod('twenty_minutes_insta_link')); ?>"><i class="fab fa-instagram"></i></a>
          <?php } ?>
          <?php if ( get_theme_mod('twenty_minutes_youtube_link') != "") { ?>
            <a title="<?php echo esc_attr('youtube', 'twenty-minutes'); ?>" target="_blank" href="<?php echo esc_url(get_theme_mod('twenty_minutes_youtube_link')); ?>"><i class="fab fa-youtube"></i></a>
          <?php } ?>
        </div>
      </div>
      <div class="col-lg-5 col-md-7 p-0">
        <div class="info-box">
          <?php if ( get_theme_mod('twenty_minutes_phone_number') != "") { ?>
            <a class="phn" href="tel:<?php echo esc_url( get_theme_mod('twenty_minutes_phone_number','' )); ?>"><i class="fas fa-phone"></i><?php echo esc_html(get_theme_mod ('twenty_minutes_phone_number','')); ?></a>
          <?php } ?>
          <?php if ( get_theme_mod('twenty_minutes_email_address') != "") { ?>
            <a class="mail" href="mailto:<?php echo esc_attr( get_theme_mod('twenty_minutes_email_address','') ); ?>"><i class="far fa-envelope"></i><?php echo esc_html(get_theme_mod ('twenty_minutes_email_address','')); ?></a>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

<div class="header <?php echo esc_attr(twenty_minutes_sticky_menu()); ?>">
  <div class="container">
    <div class="row m-0">
      <div class="col-lg-3 col-md-8 align-self-center">
        <div class="logo">
          <?php twenty_minutes_the_custom_logo(); ?>
          <div class="site-branding-text">
            <?php if ( get_theme_mod('twenty_minutes_title_enable',false) != "") { ?>
              <?php if ( is_front_page() && is_home() ) : ?>
                <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
              <?php else : ?>
                <p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></p>
              <?php endif; ?>
            <?php } ?>
            <?php $twenty_minutes_description = get_bloginfo( 'description', 'display' );
            if ( $twenty_minutes_description || is_customize_preview() ) : ?>
              <?php if ( get_theme_mod('twenty_minutes_tagline_enable',false) != "") { ?>
              <span class="site-description"><?php echo esc_html( $twenty_minutes_description ); ?></span>
              <?php } ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-lg-9 col-md-4 align-self-center">
        <div class="toggle-nav">
          <button role="tab"><?php esc_html_e('MENU','twenty-minutes'); ?></button>
        </div>
        <div id="mySidenav" class="nav sidenav">
          <nav id="site-navigation" class="main-nav" role="navigation" aria-label="<?php esc_attr_e( 'Top Menu','twenty-minutes' ); ?>">
            <ul class="mobile_nav">
              <?php
                wp_nav_menu( array( 
                  'theme_location' => 'primary',
                  'container_class' => 'main-menu' ,
                  'items_wrap' => '%3$s',
                  'fallback_cb' => 'wp_page_menu',
                ) ); 
               ?>
            </ul>
            <a href="javascript:void(0)" class="close-button"><?php esc_html_e('CLOSE','twenty-minutes'); ?></a>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>
