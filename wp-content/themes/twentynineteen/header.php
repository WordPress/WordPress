<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'twentynineteen' ); ?></a>

		<header id="masthead" class="<?php echo is_singular() && twentynineteen_can_show_post_thumbnail() ? 'site-header featured-image' : 'site-header'; ?>">
			<div class="site-branding-container">
				<?php get_template_part( 'template-parts/header/site', 'branding' ); ?>
			</div><!-- .layout-wrap -->

			<?php if ( is_singular() && twentynineteen_can_show_post_thumbnail() ) : ?>
				<div class="hentry">
					<?php the_post(); ?>
					<div class="entry-header">
						<?php if ( ! is_page() ) : ?>
						<?php $discussion = twentynineteen_can_show_post_thumbnail() ? twentynineteen_get_discussion_data() : null; ?>
						<?php endif; ?>
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
						<?php if ( ! is_page() ) : ?>
						<div class="<?php echo ( ! empty( $discussion ) && count( $discussion->authors ) > 0 ) ? 'entry-meta has-discussion' : 'entry-meta'; ?>">
							<?php twentynineteen_posted_by(); ?>
							<?php twentynineteen_posted_on(); ?>
							<span class="comment-count">
								<?php
								if ( ! empty( $discussion ) ) {
								twentynineteen_discussion_avatars_list( $discussion->authors );}
								?>
								<?php twentynineteen_comment_count(); ?>
							</span>
							<?php
							// Edit post link.
								edit_post_link(
									sprintf(
										wp_kses(
											/* translators: %s: Name of current post. Only visible to screen readers. */
											__( 'Edit <span class="screen-reader-text">%s</span>', 'twentynineteen' ),
											array(
												'span' => array(
													'class' => array(),
												),
											)
										),
										get_the_title()
									),
									'<span class="edit-link">' . twentynineteen_get_icon_svg( 'edit', 16 ),
									'</span>'
								);
							?>
						</div><!-- .meta-info -->
						<?php endif; ?>
					</div><!-- .entry-header -->
					<?php rewind_posts(); ?>
				</div>
			<?php endif; ?>
		</header><!-- #masthead -->

	<div id="content" class="site-content">
