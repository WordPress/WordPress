<?php
/**
 * The template part for displaying content.
 *
 * @package Theme Meme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
	<header class="entry-header">
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta">
			<?php themememe_posted_on(); ?>

			<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
			<span class="comments-link">
				<i class="fa fa-comments"></i>
				<?php comments_popup_link( __( '0 Comments', 'themememe' ), __( '1 Comment', 'themememe' ), __( '% Comments', 'themememe' ) ); ?>
			</span>
			<?php endif; ?>
		<!-- .entry-meta --></div>
		<?php endif; ?>
	<!-- .entry-header --></header>

	<?php if ( is_search() ) : ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	<!-- .entry-summary --></div>
	<?php else : ?>
	<div class="clearfix entry-content">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'themememe' ) ); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'themememe' ),
				'after'  => '</div>',
			) );
		?>
	<!-- .entry-content --></div>
	<?php endif; ?>

	<footer class="entry-meta entry-footer">
		<?php if ( 'post' == get_post_type() ) : ?>
			<?php
				$categories_list = get_the_category_list( __( ', ', 'themememe' ) );
				if ( $categories_list && themememe_categorized_blog() ) :
			?>
			<span class="cat-links">
				<i class="fa fa-folder-open"></i>
				<?php printf( __( '%1$s', 'themememe' ), $categories_list ); ?>
			</span>
			<?php endif; ?>

			<?php
				$tags_list = get_the_tag_list( '', __( ', ', 'themememe' ) );
				if ( $tags_list ) :
			?>
			<span class="tags-links">
				<i class="fa fa-tags"></i>
				<?php printf( __( '%1$s', 'themememe' ), $tags_list ); ?>
			</span>
			<?php endif; ?>
		<?php endif; ?>

		<?php edit_post_link( __( 'Edit', 'themememe' ), '<span class="edit-link"><i class="fa fa-pencil"></i>', '</span>' ); ?>
	<!-- .entry-footer --></footer>
<!-- #post-<?php the_ID(); ?> --></article>