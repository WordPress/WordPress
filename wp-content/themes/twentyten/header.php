<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <title><?php
        if ( is_single() ) { single_post_title(); print ' | '; bloginfo('name'); }
        elseif ( is_home() || is_front_page() ) { bloginfo('name'); print ' | '; bloginfo('description'); get_page_number(); }
        elseif ( is_page() ) { single_post_title(''); print ' | '; bloginfo('name'); }
        elseif ( is_search() ) { print 'Search results for ' . wp_specialchars($s); get_page_number(); print ' | '; bloginfo('name'); }
        elseif ( is_404() ) { print 'Not Found | '; bloginfo('name'); }
        else { bloginfo('name'); wp_title('|'); get_page_number(); }
    ?></title>

	<meta http-equiv="content-type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php bloginfo('stylesheet_directory'); ?>/print.css" />

	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

	<?php wp_head(); ?>

	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url'); ?>" title="<?php printf( __( '%s latest posts', 'twentyten' ), wp_specialchars( get_bloginfo('name'), 1 ) ); ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'twentyten' ), wp_specialchars( get_bloginfo('name'), 1 ) ); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
</head>

<body <?php body_class(); ?>>
<div id="wrapper" class="hfeed">

	<div id="header">
		<div id="masthead">

			<div id="branding">
				<div id="site-title"><span><a href="<?php bloginfo( 'url' ) ?>/" title="<?php bloginfo( 'name' ) ?>" rel="home"><?php bloginfo( 'name' ) ?></a></span></div>
				<div id="site-description"><?php bloginfo( 'description' ) ?></div>

				<?php
					global $post;
					if ( is_singular() && has_post_thumbnail( $post->ID ) ) {
						echo get_the_post_thumbnail( $post->ID, 'post-thumbnail' );
					} else { ?>
					<img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="" />
				<?php } ?>
			</div><!-- #branding -->

			<div id="access">
				<div class="skip-link screen-reader-text"><a href="#content" title="<?php _e( 'Skip to content', 'twentyten' ) ?>"><?php _e( 'Skip to content', 'twentyten' ) ?></a></div>
				<?php wp_page_menu( 'sort_column=menu_order' ); ?>
			</div><!-- #access -->

		</div><!-- #masthead -->
	</div><!-- #header -->

	<div id="main">
