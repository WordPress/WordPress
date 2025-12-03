<?php
/**
 * Template part for displaying the entry content or summary
 * @package Twenty8teen
 */

$default = twenty8teen_default_booleans();

if ( is_singular() ||
	( get_theme_mod( 'show_full_content', $default['show_full_content'] ) && ! is_search() ) ) : ?>
<div <?php twenty8teen_attributes( 'div', array(
	'class' => twenty8teen_widget_get_classes( 'entry-content' )
	) ); ?>>
	<?php
		the_content( sprintf(
				/* translators: %s: Name of current post for Read More link, only visible to screen readers, with space before and after */
			esc_html__( 'Read more%s', 'twenty8teen' ),
			'<span class="screen-reader-text"> "'
				. wp_strip_all_tags( get_the_title() ) . '" </span>'
		) );
	?>
	<div class="clear"></div>
</div><!-- .entry-content -->

<?php
else : ?>
<div <?php twenty8teen_attributes( 'div', array(
	'class' => twenty8teen_widget_get_classes( 'entry-summary' )
	) ); ?>>
	<?php the_excerpt(); ?>
	<div class="clear"></div>
</div><!-- .entry-summary -->

<?php
endif;

wp_link_pages( array(
	'before' => '<div class="entry-page-links navigation">' . esc_html__( 'Pages:', 'twenty8teen' ),
	'after'	=> '</div>',
	'link_before' => '<span class="page-numbers">',
	'link_after' => '</span>',
	'separator' => ', ',
) );
