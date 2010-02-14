<?php get_header(); ?>

		<div id="container">
			<div id="content">

<?php the_post(); ?>

				<p class="page-title"><a href="<?php echo get_permalink($post->post_parent); ?>" title="<?php printf( esc_attr__( 'Return to %s', 'twentyten' ), esc_html( get_the_title($post->post_parent), 1 ) ); ?>" rel="gallery">&larr; <?php echo get_the_title($post->post_parent); ?></a></p>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>

					<div class="entry-meta">
						<span class="meta-prep meta-prep-author"><?php _e( 'By ', 'twentyten' ); ?></span>
						<span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( $authordata->ID, $authordata->user_nicename ); ?>" title="<?php printf( esc_attr__( 'View all posts by %s', 'twentyten' ), $authordata->display_name ); ?>"><?php the_author(); ?></a></span>
						<span class="meta-sep"><?php _e( ' | ', 'twentyten' ); ?></span>
						<span class="meta-prep meta-prep-entry-date"><?php _e( 'Published ', 'twentyten' ); ?></span>
						<span class="entry-date"><abbr class="published" title="<?php the_time() ?>"><?php echo get_the_date(); ?></abbr></span>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), "<span class=\"meta-sep\">|</span>\n\t\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t" ); ?>
					</div><!-- .entry-meta -->

					<div class="entry-content">
						<div class="entry-attachment">
<?php if ( wp_attachment_is_image( $post->ID ) ) : ?>
						<p class="attachment"><a href="<?php echo wp_get_attachment_url($post->ID); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php
							echo wp_get_attachment_image( $post->ID, array($content_width, $content_width) ); // max $content_width wide or high.
						?></a></p>


				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php previous_image_link( false ); ?></div>
					<div class="nav-next"><?php next_image_link( false ); ?></div>
				</div><!-- #nav-below -->
<?php else : ?>
						<a href="<?php echo wp_get_attachment_url($post->ID); ?>" title="<?php echo esc_attr( get_the_title($post->ID) ); ?>" rel="attachment"><?php echo basename(get_permalink()); ?></a>
<?php endif; ?>
						</div>
						<div class="entry-caption"><?php if ( !empty($post->post_excerpt) ) the_excerpt(); ?></div>

						<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' )  ); ?>
						<?php wp_link_pages( 'before=<div class="page-link">' . __( 'Pages:', 'twentyten' ) . '&after=</div>' ); ?>

					</div><!-- .entry-content -->

					<div class="entry-utility">
					<?php printf( __( 'This entry was posted in %1$s%2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>. Follow any comments here with the <a href="%5$s" title="Comments RSS to %4$s" rel="alternate" type="application/rss+xml">RSS feed for this post</a>.', 'twentyten' ),
						get_the_category_list(', '),
						get_the_tag_list( __( ' and tagged ', 'twentyten' ), ', ', '' ),
						get_permalink(),
						the_title_attribute('echo=0'),
						get_post_comments_feed_link() ); ?>

<?php if ( comments_open() && pings_open() ) : // Comments and trackbacks open ?>
						<?php printf( __( '<a class="comment-link" href="#respond" title="Post a comment">Post a comment</a> or leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'twentyten' ), get_trackback_url() ); ?>
<?php elseif ( !comments_open() && pings_open() ) : // Only trackbacks open ?>
						<?php printf( __( 'Comments are closed, but you can leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'twentyten' ), get_trackback_url() ); ?>
<?php elseif ( comments_open() && !pings_open() ) : // Only comments open ?>
						<?php _e( 'Trackbacks are closed, but you can <a class="comment-link" href="#respond" title="Post a comment">post a comment</a>.', 'twentyten' ); ?>
<?php elseif ( !comments_open() && !pings_open() ) : // Comments and trackbacks closed ?>
						<?php _e( 'Both comments and trackbacks are currently closed.', 'twentyten' ); ?>
<?php endif; ?>
<?php edit_post_link( __( 'Edit', 'twentyten' ), "\n\t\t\t\t\t<span class=\"edit-link\">", "</span>" ); ?>
					</div><!-- .entry-utility -->
				</div><!-- #post-<?php the_ID(); ?> -->

<?php comments_template(); ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>