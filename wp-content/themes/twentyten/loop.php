<?php if ( $wp_query->max_num_pages > 1 ) { ?>
	<div id="nav-above" class="navigation">
		<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' )); ?></div>
		<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' )); ?></div>
	</div><!-- #nav-above -->
<?php } ?>

<?php if ( ! have_posts() ) : ?>
	<div id="post-0" class="post error404 not-found">
		<h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
		<div class="entry-content">
			<p><?php _e( 'Apologies, but no results were found for the requested Archive. Perhaps searching will help find a related post.', 'twentyten' ); ?></p>
			<?php get_search_form(); ?>
		</div><!-- .entry-content -->
	</div><!-- #post-0 -->
<?php endif; ?>

<?php while ( have_posts() ) : the_post(); ?>
<?php if ( in_category( 'Gallery' ) ) { ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'twentyten'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

		<div class="entry-meta">
			<span class="meta-prep meta-prep-author"><?php _e( 'Posted on ', 'twentyten' ); ?></span>
			<a href="<?php the_permalink(); ?>" title="<?php the_time(); ?>" rel="bookmark"><span class="entry-date"><?php echo get_the_date(); ?></span></a>
			<span class="meta-sep"> <?php _e( 'by ', 'twentyten' ); ?> </span>
			<span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( get_the_author_meta('ID') ); ?>" title="<?php printf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ); ?>"><?php the_author(); ?></a></span>
		</div><!-- .entry-meta -->

		<div class="entry-content">
			<div class="gallery-thumb">
				<a class="size-thumbnail" href="<?php the_permalink(); ?>"><?php
				$images =& get_children( array('post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 999) );
				$total_images = count($images);
				$image = array_shift($images);
				echo wp_get_attachment_image( $image->ID, 'thumbnail' );
				?></a>
			</div>
			<p><em><?php printf( __('This gallery contains <a %1$s>%2$s photos</a>.', 'twentyten'), 'href="' . get_permalink() . '" title="' . sprintf( esc_attr__('Permalink to %s', 'twentyten'), the_title_attribute('echo=0') ) . '" rel="bookmark"', $total_images ); ?></em></p>

			<?php the_excerpt( '' ); ?>
		</div><!-- .entry-content -->

		<div class="entry-utility">
			<?php
				$category_id = get_cat_ID( 'Gallery' );
				$category_link = get_category_link( $category_id );
			?>
			<a href="<?php echo $category_link; ?>" title="<?php esc_attr_e('View posts in the Gallery category', 'twentyten'); ?>"><?php _e('More Galleries', 'twentyten'); ?></a>
			<span class="meta-sep"><?php _e( ' | ', 'twentyten' ); ?></span>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
			<?php edit_post_link( __( 'Edit', 'twentyten' ), "<span class=\"meta-sep\">|</span>\n\t\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t\n" ); ?>
		</div><!-- #entry-utility -->
	</div>


<?php } elseif ( in_category( 'asides' ) ) { ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php if ( is_archive() || is_search() ) : //Only display Excerpts for archives & search ?>
		<div class="entry-summary">
			<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
		</div><!-- .entry-summary -->
<?php else : ?>
		<div class="entry-content">
			<?php the_content( __( 'Continue&nbsp;reading&nbsp;<span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
		</div><!-- .entry-content -->
<?php endif; ?>

		<div class="entry-utility">
			<span class="meta-prep meta-prep-author"><?php _e( 'Posted on ', 'twentyten' ); ?></span>
			<a href="<?php the_permalink(); ?>" title="<?php the_time(); ?>" rel="bookmark"><span class="entry-date"><?php echo get_the_date(); ?></span></a>
			<span class="meta-sep"> <?php _e( ' by ', 'twentyten' ); ?> </span>
			<span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( get_the_author_meta('ID') ); ?>" title="<?php printf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ); ?>"><?php the_author(); ?></a></span>
			<span class="meta-sep"><?php _e( ' | ', 'twentyten' ); ?></span>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
			<?php edit_post_link( __( 'Edit', 'twentyten' ), "<span class=\"meta-sep\">|</span>\n\t\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t\n" ); ?>
		</div><!-- #entry-utility -->
	</div><!-- #post-<?php the_ID(); ?> -->


<?php } else { ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'twentyten'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

		<div class="entry-meta">
			<span class="meta-prep meta-prep-author"><?php _e('Posted on ', 'twentyten'); ?></span>
			<a href="<?php
the_permalink(); ?>" title="<?php the_time(); ?>" rel="bookmark"><span class="entry-date"><?php echo get_the_date(); ?></span></a>
			<span class="meta-sep"><?php _e( ' by ', 'twentyten' ); ?></span>
			<span class="author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( get_the_author_meta('ID') ); ?>" title="<?php printf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ); ?>"><?php the_author(); ?></a></span>
		</div><!-- .entry-meta -->

<?php if ( is_archive() || is_search() ) : //Only display Excerpts for archives & search ?>
		<div class="entry-summary">
			<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
		</div><!-- .entry-summary -->
<?php else : ?>
		<div class="entry-content">
			<?php the_content( __( 'Continue&nbsp;reading&nbsp;<span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
			<?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'twentyten' ) . '&after=</div>'); ?>
		</div><!-- .entry-content -->
<?php endif; ?>

		<div class="entry-utility">
			<span class="cat-links"><span class="entry-utility-prep entry-utility-prep-cat-links"><?php echo twentyten_cat_list(); ?></span></span>
			<span class="meta-sep"><?php _e( ' | ', 'twentyten' ); ?></span>
			<?php $tags_text = twentyten_tag_list(); ?>
			<?php if ( !empty($tags_text) ) : ?>
			<span class="tag-links"><span class="entry-utility-prep entry-utility-prep-tag-links"><?php echo $tags_text; ?></span></span>
			<span class="meta-sep"><?php _e( ' | ', 'twentyten' ); ?></span>
			<?php endif; //$tags_text ?>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
			<?php edit_post_link( __( 'Edit', 'twentyten' ), "<span class=\"meta-sep\">|</span>\n\t\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t\n" ); ?>
		</div><!-- #entry-utility -->
	</div><!-- #post-<?php the_ID(); ?> -->

	<?php comments_template( '', true ); ?>

<?php } ?>
<?php endwhile; ?>

<?php if (  $wp_query->max_num_pages > 1 ) { ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' )); ?></div>
					<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' )); ?></div>
				</div><!-- #nav-below -->
<?php } ?>