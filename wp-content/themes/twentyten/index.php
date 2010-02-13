<?php get_header(); ?>

		<div id="container">
			<div id="content">

<?php global $wp_query; $total_pages = $wp_query->max_num_pages; if ( $total_pages > 1 ) { ?>
				<div id="nav-above" class="navigation">
					<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' )) ?></div>
					<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' )) ?></div>
				</div><!-- #nav-above -->
<?php } ?>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <?php if ( in_category( 'Gallery' ) ) { ?>
    	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'twentyten'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

			<div class="entry-meta">
				<span class="meta-prep meta-prep-author"><?php _e('Posted on ', 'twentyten'); ?></span>
				<a href="<?php
the_permalink(); ?>" title="<?php the_time('Y-m-d\TH:i:sO') ?>" rel="bookmark"><span class="entry-date"><?php echo get_the_date(); ?></span></a>
				<span class="meta-sep"> <?php _e('by ', 'twentyten'); ?> </span>
				<span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( $authordata->ID, $authordata->user_nicename ); ?>" title="<?php printf( esc_attr__( 'View all posts by %s', 'twentyten' ), $authordata->display_name ); ?>"><?php the_author(); ?></a></span>
			</div><!-- .entry-meta -->

			<div class="entry-content">
				<div class="gallery-thumb"><a class="size-thumbnail" href="<?php the_permalink() ?>"><?php
				$images =& get_children( array('post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 999) );
				$total_images = count($images);
				$image = array_shift($images);
				echo wp_get_attachment_image( $image->ID, 'thumbnail' );
				?></a></div>
				<p><em><?php printf( __('This gallery contains <a %1$s>%2$s photos</a>.', 'twentyten'), 'href="' . get_permalink() . '" title="' . sprintf( esc_attr__('Permalink to %s', 'twentyten'), the_title_attribute('echo=0') ) . '" rel="bookmark"', $total_images ); ?></em></p>

				<?php the_excerpt(''); ?>
			</div><!-- .entry-content -->

			<div class="entry-utility">
				<?php
				    $category_id = get_cat_ID( 'Gallery' );
				    $category_link = get_category_link( $category_id );
				?>
				<a href="<?php echo $category_link; ?>" title="<?php esc_attr_e('View posts in the Gallery category', 'twentyten'); ?>"><?php _e('More Galleries', 'twentyten'); ?></a>

				<span class="meta-sep"> | </span>

				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ) ?></span>

			<?php edit_post_link( __( 'Edit', 'twentyten' ), "<span class=\"meta-sep\">|</span>\n\t\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t\n" ) ?>
			</div><!-- #entry-utility -->
		</div>


	<?php } elseif ( in_category( 'asides' ) ) { ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-content">
<?php the_content( __( 'Continue&nbsp;reading&nbsp;<span class="meta-nav">&rarr;</span>', 'twentyten' )  ); ?>
			</div><!-- .entry-content -->

			<div class="entry-utility">
				<span class="meta-prep meta-prep-author"><?php _e('Posted on ', 'twentyten'); ?></span>
				<a href="<?php
the_permalink(); ?>" title="<?php the_time('Y-m-d\TH:i:sO') ?>" rel="bookmark"><span class="entry-date"><?php echo get_the_date(); ?></span></a>
				<span class="meta-sep"> <?php _e('by ', 'twentyten'); ?> </span>
				<span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( $authordata->ID, $authordata->user_nicename ); ?>" title="<?php printf( esc_attr__( 'View all posts by %s', 'twentyten' ), $authordata->display_name ); ?>"><?php the_author(); ?></a></span>
				<span class="meta-sep"> | </span>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ) ?></span>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), "<span class=\"meta-sep\">|</span>\n\t\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t\n" ) ?>
			</div><!-- #entry-utility -->
		</div><!-- #post-<?php the_ID(); ?> -->


    <?php } else { ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'twentyten'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

			<div class="entry-meta">
				<span class="meta-prep meta-prep-author"><?php _e('Posted on ', 'twentyten'); ?></span>
				<a href="<?php
the_permalink(); ?>" title="<?php the_time('Y-m-d\TH:i:sO') ?>" rel="bookmark"><span class="entry-date"><?php echo get_the_date(); ?></span></a>
				<span class="meta-sep"> <?php _e('by ', 'twentyten'); ?> </span>
				<span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( $authordata->ID, $authordata->user_nicename ); ?>" title="<?php printf( esc_attr__( 'View all posts by %s', 'twentyten' ), $authordata->display_name ); ?>"><?php the_author(); ?></a></span>
			</div><!-- .entry-meta -->

			<div class="entry-content">
<?php the_content( __( 'Continue&nbsp;reading&nbsp;<span class="meta-nav">&rarr;</span>', 'twentyten' )  ); ?>
<?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'twentyten' ) . '&after=</div>') ?>
			</div><!-- .entry-content -->

			<div class="entry-utility">
				<span class="cat-links"><span class="entry-utility-prep entry-utility-prep-cat-links"><?php _e( 'Posted in ', 'twentyten' ); ?></span><?php echo get_the_category_list(', '); ?></span>
				<span class="meta-sep"> | </span>
				<?php the_tags( '<span class="tag-links"><span class="entry-utility-prep entry-utility-prep-tag-links">' . __('Tagged ', 'twentyten' ) . '</span>', ", ", "</span>\n\t\t\t\t\t\t<span class=\"meta-sep\">|</span>\n" ) ?>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ) ?></span>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), "<span class=\"meta-sep\">|</span>\n\t\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t\n" ) ?>
			</div><!-- #entry-utility -->
		</div><!-- #post-<?php the_ID(); ?> -->

<?php comments_template(); ?>

    <?php } ?>
    <?php endwhile; ?>
    <?php else : ?>
		<h2><?php _e( 'Not Found', 'twentyten' ); ?></h2>
		<div class="entry-content">
			<p><?php _e( 'Apologies, but we were unable to find what you were looking for. Perhaps searching will help.', 'twentyten' ); ?></p>
		<?php get_search_form(); ?>
		</div>
    <?php endif; ?>

<?php global $wp_query; $total_pages = $wp_query->max_num_pages; if ( $total_pages > 1 ) { ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' )) ?></div>
					<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' )) ?></div>
				</div><!-- #nav-below -->
<?php } ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
