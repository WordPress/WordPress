<?php if ( is_attachment() ) : // If viewing a single attachment. ?>

	<article <?php hybrid_attr( 'post' ); ?>>

		<?php hybrid_attachment(); // Function for handling non-image attachments. ?>

		<header class="entry-header">
			<h1 <?php hybrid_attr( 'entry-title' ); ?>><?php single_post_title(); ?></h1>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>
		</div><!-- .entry-content -->

		<footer class="entry-footer">
			<time <?php hybrid_attr( 'entry-published' ); ?>><?php echo get_the_date(); ?></time>
			<?php if ( function_exists( 'ev_post_views' ) ) ev_post_views( array( 'text' => '%s' ) ); ?>
			<?php edit_post_link(); ?>
		</footer><!-- .entry-footer -->

	</article><!-- .entry -->

	<div class="attachment-meta">

		<div class="media-info">

			<h3><?php _e( 'Audio Info', 'stargazer' ); ?></h3>

			<?php hybrid_media_meta(); ?>

		</div><!-- .media-info -->

	</div><!-- .attachment-meta -->

<?php else : // If not viewing a single attachment. ?>

	<article <?php hybrid_attr( 'post' ); ?>>

		<?php get_the_image( array( 'size' => 'stargazer-full', 'order' => array( 'featured', 'attachment' ) ) ); ?>

		<header class="entry-header">
			<?php the_title( '<h2 ' . hybrid_get_attr( 'entry-title' ) . '><a href="' . get_permalink() . '" rel="bookmark" itemprop="url">', '</a></h2>' ); ?>
		</header><!-- .entry-header -->

		<div <?php hybrid_attr( 'entry-summary' ); ?>>
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->

	</article><!-- .entry -->

<?php endif; // End single attachment check. ?>