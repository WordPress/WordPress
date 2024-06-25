<?php
/**
 * Template for displaying content featured in the showcase.php page template
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

global $feature_class;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $feature_class ); ?>>
	<header class="entry-header">
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

		<div class="entry-meta">
			<?php twentyeleven_posted_on(); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
		<?php
		wp_link_pages(
			array(
				'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>',
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<footer class="entry-meta">
		<?php
		$tags_list = get_the_tag_list( '', wp_get_list_item_separator() );

		if ( $tags_list && ! is_wp_error( $tags_list ) ) {
			/* translators: 1: Category list, 2: Tag list, 3: Post permalink, 4: Post title. */
			$utility_text = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyeleven' );
		} else {
			/* translators: 1: Category list, 3: Post permalink, 4: Post title. */
			$utility_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyeleven' );
		}

		printf(
			$utility_text,
			get_the_category_list( wp_get_list_item_separator() ),
			$tags_list,
			esc_url( get_permalink() ),
			the_title_attribute( 'echo=0' )
		);
		?>

		<?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
