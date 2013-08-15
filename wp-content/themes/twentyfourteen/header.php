<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
	$email_link = get_theme_mod( 'email_link' );
	$twitter_link = get_theme_mod( 'twitter_link' );
	$facebook_link = get_theme_mod( 'facebook_link' );
	$pinterest_link = get_theme_mod( 'pinterest_link' );
	$google_plus_link = get_theme_mod( 'google_plus_link' );
	$linkedin_link = get_theme_mod( 'linkedin_link' );
	$flickr_link = get_theme_mod( 'flickr_link' );
	$github_link = get_theme_mod( 'github_link' );
	$dribbble_link = get_theme_mod( 'dribbble_link' );
	$vimeo_link = get_theme_mod( 'vimeo_link' );
	$youtube_link = get_theme_mod( 'youtube_link' );
	$social_links = ( '' != $email_link
		|| '' != $twitter_link
		|| '' != $facebook_link
		|| '' != $pinterest_link
		|| '' != $google_plus_link
		|| '' != $linkedin_link
		|| '' != $flickr_link
		|| '' != $github_link
		|| '' != $dribbble_link
		|| '' != $vimeo_link
		|| '' != $youtube_link
	) ? true : false;
?>
<div id="page" class="hfeed site">
	<?php do_action( 'before' ); ?>

	<?php $header_image = get_header_image();
	if ( ! empty( $header_image ) ) : ?>
	<div id="site-header">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
			<img src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" />
		</a>
	</div>
	<?php endif; ?>

	<header id="masthead" class="site-header" role="banner">
		<div class="header-main clear">
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>

			<div class="header-extra">
				<?php if ( $social_links ) : ?>
				<div class="social-links-toggle">
					<span class="genericon"><?php _e( 'Connect', 'twentyfourteen' ); ?></span>
				</div>
				<?php endif; ?>

				<div class="search-toggle">
					<span class="genericon"><?php _e( 'Search', 'twentyfourteen' ); ?></span>
				</div>
			</div>

			<nav role="navigation" class="site-navigation primary-navigation">
				<h1 class="screen-reader-text"><?php _e( 'Primary Menu', 'twentyfourteen' ); ?></h1>
				<div class="screen-reader-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentyfourteen' ); ?>"><?php _e( 'Skip to content', 'twentyfourteen' ); ?></a></div>
				<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
			</nav>

		</div>

		<div id="mobile-navigations" class="hide"></div>

		<?php if ( $social_links ) : ?>
			<div class="social-links-wrapper hide">
				<ul class="social-links clear">
					<?php if ( is_email( $email_link ) ) : ?>
					<li class="email-link">
						<a href="mailto:<?php echo antispambot( sanitize_email( $email_link ) ); ?>" class="genericon" title="<?php esc_attr_e( 'Email', 'twentyfourteen' ); ?>" target="_blank">
							<?php _e( 'Email', 'twentyfourteen' ); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if ( '' != $twitter_link ) : ?>
					<li class="twitter-link">
						<a href="<?php echo esc_url( $twitter_link ); ?>" class="genericon" title="<?php esc_attr_e( 'Twitter', 'twentyfourteen' ); ?>" target="_blank">
							<?php _e( 'Twitter', 'twentyfourteen' ); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if ( '' != $facebook_link ) : ?>
					<li class="facebook-link">
						<a href="<?php echo esc_url( $facebook_link ); ?>" class="genericon" title="<?php esc_attr_e( 'Facebook', 'twentyfourteen' ); ?>" target="_blank">
							<?php _e( 'Facebook', 'twentyfourteen' ); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if ( '' != $pinterest_link ) : ?>
					<li class="pinterest-link">
						<a href="<?php echo esc_url( $pinterest_link ); ?>" class="genericon" title="<?php esc_attr_e( 'Pinterest', 'twentyfourteen' ); ?>" target="_blank">
							<?php _e( 'Pinterest', 'twentyfourteen' ); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if ( '' != $google_plus_link ) : ?>
					<li class="google-link">
						<a href="<?php echo esc_url( $google_plus_link ); ?>" class="genericon" title="<?php esc_attr_e( 'Google Plus', 'twentyfourteen' ); ?>" target="_blank">
							<?php _e( 'Google Plus', 'twentyfourteen' ); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if ( '' != $linkedin_link ) : ?>
					<li class="linkedin-link">
						<a href="<?php echo esc_url( $linkedin_link ); ?>" class="genericon" title="<?php esc_attr_e( 'LinkedIn', 'twentyfourteen' ); ?>" target="_blank">
							<?php _e( 'LinkedIn', 'twentyfourteen' ); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if ( '' != $flickr_link ) : ?>
					<li class="flickr-link">
						<a href="<?php echo esc_url( $flickr_link ); ?>" class="genericon" title="<?php esc_attr_e( 'Flickr', 'twentyfourteen' ); ?>" target="_blank">
							<?php _e( 'Flickr', 'twentyfourteen' ); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if ( '' != $github_link ) : ?>
					<li class="github-link">
						<a href="<?php echo esc_url( $github_link ); ?>" class="genericon" title="<?php esc_attr_e( 'Github', 'twentyfourteen' ); ?>" target="_blank">
							<?php _e( 'Github', 'twentyfourteen' ); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if ( '' != $dribbble_link ) : ?>
					<li class="dribbble-link">
						<a href="<?php echo esc_url( $dribbble_link ); ?>" class="genericon" title="<?php esc_attr_e( 'Dribbble', 'twentyfourteen' ); ?>" target="_blank">
							<?php _e( 'Dribbble', 'twentyfourteen' ); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if ( '' != $vimeo_link ) : ?>
					<li class="vimeo-link">
						<a href="<?php echo esc_url( $vimeo_link ); ?>" class="genericon" title="<?php esc_attr_e( 'Vimeo', 'twentyfourteen' ); ?>" target="_blank">
							<?php _e( 'Vimeo', 'twentyfourteen' ); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if ( '' != $youtube_link ) : ?>
					<li class="youtube-link">
						<a href="<?php echo esc_url( $youtube_link ); ?>" class="genericon" title="<?php esc_attr_e( 'YouTube', 'twentyfourteen' ); ?>" target="_blank">
							<?php _e( 'YouTube', 'twentyfourteen' ); ?>
						</a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="search-box-wrapper hide">
			<div class="search-box clear">
			<?php get_search_form(); ?>
			</div>
		</div>
	</header><!-- #masthead -->

	<div id="main" class="site-main">
