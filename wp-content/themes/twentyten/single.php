<?php get_header(); ?>

		<div id="container">
			<div id="content">

<?php the_post(); ?>

				<div id="nav-above" class="navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">&larr;</span> %title' ) ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">&rarr;</span>' ) ?></div>
				</div><!-- #nav-above -->

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>

					<div class="entry-meta">
						<span class="meta-prep meta-prep-author"><?php _e('Posted by ', 'twentyten'); ?></span>
						<span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( $authordata->ID, $authordata->user_nicename ); ?>" title="<?php printf( esc_attr__( 'View all posts by %s', 'twentyten' ), $authordata->display_name ); ?>"><?php the_author(); ?></a></span>
						<span class="meta-sep"> <?php _e('on ', 'twentyten'); ?> </span>
						<a href="<?php
the_permalink(); ?>" title="<?php the_time('Y-m-d\TH:i:sO') ?>" rel="bookmark"><span class="entry-date"><?php echo get_the_date(); ?></span></a>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), "<span class=\"meta-sep\">|</span>\n\t\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t" ) ?>
					</div><!-- .entry-meta -->

					<div class="entry-content">
<?php the_content(); ?>
<?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'twentyten' ) . '&after=</div>') ?>
					</div><!-- .entry-content -->

<?php if ( get_the_author_meta('description') ) : // If a user has filled out their decscription show a bio on their entries  ?>
					<div id="entry-author-info">
						<div id="author-avatar">
<?php echo get_avatar( get_the_author_meta('user_email'), apply_filters('twentyten_author_bio_avatar_size', 60) ); ?>
						</div><!-- #author-avatar 	-->
						<div id="author-description">
							<h2><?php _e('About ', 'twentyten'); ?><?php the_author(); ?></h2>
<?php the_author_meta('description'); ?>
							<div id="author-link">
								<a href="<?php echo get_author_posts_url( $authordata->ID, $authordata->user_nicename ); ?>" title="<?php printf( esc_attr__( 'View all posts by %s', 'twentyten' ), $authordata->display_name ); ?>"><?php _e('View all posts by ', 'twentyten'); ?><?php the_author(); ?> &rarr;</a>
							</div><!-- #author-link	-->
						</div><!-- #author-description	-->
					</div><!-- .entry-author-info -->
<?php endif; ?>

					<div class="entry-utility">
					<?php printf( __( 'This entry was posted in %1$s%2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>. Follow any comments here with the <a href="%5$s" title="Comments RSS to %4$s" rel="alternate" type="application/rss+xml">RSS feed for this post</a>.', 'twentyten' ),
						get_the_category_list(', '),
						get_the_tag_list( __( ' and tagged ', 'twentyten' ), ', ', '' ),
						get_permalink(),
						the_title_attribute('echo=0'),
						get_post_comments_feed_link() ) ?>

<?php edit_post_link( __( 'Edit', 'twentyten' ), "\n\t\t\t\t\t<span class=\"edit-link\">", "</span>" ) ?>
					</div><!-- .entry-utility -->
				</div><!-- #post-<?php the_ID(); ?> -->

				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">&larr;</span> %title' ) ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">&rarr;</span>' ) ?></div>
				</div><!-- #nav-below -->

<?php comments_template('', true); ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>