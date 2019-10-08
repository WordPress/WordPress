<?php
/**
 * The template for displaying Author info
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

if ( (bool) get_the_author_meta( 'description' ) ) : ?>
<div class="author-bio">
		<h2 class="author-title heading-size-4">
			<div class="author-avatar vcard">
				<?php echo get_avatar( get_the_author_meta( 'ID' ), 160 ); ?>
			</div>
			<span class="author-name">
				<?php
				printf(
					/* translators: %s: Author name */
					__( 'By %s', 'twentytwenty' ),
					esc_html( get_the_author() )
				);
				?>
			</span>
		</h2>
		<p class="author-description">
			<?php the_author_meta( 'description' ); ?>
			<a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
				<?php _e( 'View Archive &rarr;', 'twentytwenty' ); ?>
			</a>
		</p><!-- .author-description -->
</div><!-- .author-bio -->
<?php endif; ?>
