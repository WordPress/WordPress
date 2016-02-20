<?php
/**
 * @package WordPress
 * @subpackage Theme_Compat
 * @deprecated 3.0
 *
 * This file is here for Backwards compatibility with old themes and will be removed in a future version
 *
 */
_deprecated_file(
	/* translators: %s: template name */
	sprintf( __( 'Theme without %s' ), basename( __FILE__ ) ),
	'3.0',
	null,
	/* translators: %s: template name */
	sprintf( __( 'Please include a %s template in your theme.' ), basename( __FILE__ ) )
);
?>
	<div id="sidebar" role="complementary">
		<ul>
			<?php 	/* Widgetized sidebar, if you have the plugin installed. */
					if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
			<li>
				<?php get_search_form(); ?>
			</li>

			<!-- Author information is disabled per default. Uncomment and fill in your details if you want to use it.
			<li><h2><?php _e('Author'); ?></h2>
			<p>A little something about you, the author. Nothing lengthy, just an overview.</p>
			</li>
			-->

			<?php if ( is_404() || is_category() || is_day() || is_month() ||
						is_year() || is_search() || is_paged() ) :
			?> <li>

			<?php if ( is_404() ) : /* If this is a 404 page */ ?>
			<?php elseif ( is_category() ) : /* If this is a category archive */ ?>
				<p><?php /* translators: %s: category name */
					printf( __( 'You are currently browsing the archives for the %s category.' ),
						single_cat_title( '', false )
					);
				?></p>

			<?php elseif ( is_day() ) : /* If this is a daily archive */ ?>
				<p><?php /* translators: 1: site link, 2: archive date */
					printf( __( 'You are currently browsing the %1$s blog archives for the day %2$s.' ),
						sprintf( '<a href="%1$s/">%2$s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) ),
						get_the_time( __( 'l, F jS, Y' ) )
					);
				?></p>

			<?php elseif ( is_month() ) : /* If this is a monthly archive */ ?>
				<p><?php /* translators: 1: site link, 2: archive month */
					printf( __( 'You are currently browsing the %1$s blog archives for %2$s.' ),
						sprintf( '<a href="%1$s/">%2$s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) ),
						get_the_time( __( 'F, Y' ) )
					);
				?></p>

			<?php elseif ( is_year() ) : /* If this is a yearly archive */ ?>
				<p><?php /* translators: 1: site link, 2: archive year */
					printf( __( 'You are currently browsing the %1$s blog archives for the year %2$s.' ),
						sprintf( '<a href="%1$s/">%2$s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) ),
						get_the_time( 'Y' )
					);
				?></p>

			<?php elseif ( is_search() ) : /* If this is a search result */ ?>
				<p><?php /* translators: 1: site link, 2: search query */
					printf( __( 'You have searched the %1$s blog archives for <strong>&#8216;%2$s&#8217;</strong>. If you are unable to find anything in these search results, you can try one of these links.' ),
						sprintf( '<a href="%1$s/">%2$s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) ),
						esc_html( get_search_query() )
					);
				?></p>

			<?php elseif ( isset( $_GET['paged'] ) && ! empty( $_GET['paged'] ) ) : /* If this set is paginated */ ?>
				<p><?php /* translators: %s: site link */
					printf( __( 'You are currently browsing the %s blog archives.' ),
						sprintf( '<a href="%1$s/">%2$s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) )
					);
				?></p>

			<?php endif; ?>

			</li>
		<?php endif; ?>
		</ul>
		<ul role="navigation">
			<?php wp_list_pages('title_li=<h2>' . __('Pages') . '</h2>' ); ?>

			<li><h2><?php _e('Archives'); ?></h2>
				<ul>
				<?php wp_get_archives(array('type' => 'monthly')); ?>
				</ul>
			</li>

			<?php wp_list_categories(array('show_count' => 1, 'title_li' => '<h2>' . __('Categories') . '</h2>')); ?>
		</ul>
		<ul>
			<?php if ( is_home() || is_page() ) { /* If this is the frontpage */ ?>
				<?php wp_list_bookmarks(); ?>

				<li><h2><?php _e('Meta'); ?></h2>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
				</li>
			<?php } ?>

			<?php endif; /* ! dynamic_sidebar() */ ?>
		</ul>
	</div>
