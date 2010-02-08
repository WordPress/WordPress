<?php get_header(); ?>

		<div id="container">
			<div id="content">

<?php if ( have_posts() ) : ?>

				<h1 class="page-title"><?php _e( 'Search Results for: ', 'twentyten' ); ?><span><?php the_search_query(); ?></span></h1>

<?php global $wp_query; $total_pages = $wp_query->max_num_pages; if ( $total_pages > 1 ) { ?>
				<div id="nav-above" class="navigation">
					<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' )) ?></div>
					<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' )) ?></div>
				</div><!-- #nav-above -->
<?php } ?>

<?php while ( have_posts() ) : the_post() ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( __('Permalink to %s', 'twentyten'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

<?php if ( $post->post_type == 'post' ) { ?>
					<div class="entry-meta">
						<span class="meta-prep meta-prep-author"><?php _e('Posted on ', 'twentyten'); ?></span>
						<a href="<?php
		the_permalink(); ?>" title="<?php the_time('Y-m-d\TH:i:sO') ?>" rel="bookmark"><span class="entry-date"><?php the_time( get_option( 'date_format' ) ); ?></span></a>
						<span class="meta-sep"> <?php _e('by ', 'twentyten'); ?> </span>
						<span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( $authordata->ID, $authordata->user_nicename ); ?>" title="<?php printf( __( 'View all posts by %s', 'twentyten' ), $authordata->display_name ); ?>"><?php the_author(); ?></a></span>
					</div><!-- .entry-meta -->
<?php } ?>

					<div class="entry-summary">
<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' )  ); ?>
<?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'twentyten' ) . '&after=</div>') ?>
					</div><!-- .entry-summary -->

<?php if ( $post->post_type == 'post' ) { ?>
					<div class="entry-utility">
						<span class="cat-links"><span class="entry-utility-prep entry-utility-prep-cat-links"><?php _e( 'Posted in ', 'twentyten' ); ?></span><?php echo get_the_category_list(', '); ?></span>
						<span class="meta-sep"> | </span>
						<?php the_tags( '<span class="tag-links"><span class="entry-utility-prep entry-utility-prep-tag-links">' . __('Tagged ', 'twentyten' ) . '</span>', ", ", "</span>\n\t\t\t\t\t\t<span class=\"meta-sep\">|</span>\n" ) ?>
						<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ) ?></span>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), "<span class=\"meta-sep\">|</span>\n\t\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t\n" ) ?>
					</div><!-- #entry-utility -->
<?php } ?>
				</div><!-- #post-<?php the_ID(); ?> -->

<?php endwhile; ?>

<?php global $wp_query; $total_pages = $wp_query->max_num_pages; if ( $total_pages > 1 ) { ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' )) ?></div>
					<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' )) ?></div>
				</div><!-- #nav-below -->
<?php } ?>

<?php else : ?>

				<div id="post-0" class="post no-results not-found">
					<h2 class="entry-title"><?php _e( 'Nothing Found', 'twentyten' ) ?></h2>
					<div class="entry-content">
						<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'twentyten' ); ?></p>
	<?php get_search_form(); ?>
					</div><!-- .entry-content -->
				</div>

<?php endif; ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>