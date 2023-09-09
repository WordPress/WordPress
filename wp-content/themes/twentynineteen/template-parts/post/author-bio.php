<?php
/**
 * The template for displaying Author info
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
<<<<<<< HEAD
 * @since Twenty Nineteen 1.0
 */

if ( (bool) get_the_author_meta( 'description' ) ) :
	?>
=======
 * @since 1.0.0
 */

if ( (bool) get_the_author_meta( 'description' ) ) : ?>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
<div class="author-bio">
	<h2 class="author-title">
		<span class="author-heading">
			<?php
			printf(
<<<<<<< HEAD
				/* translators: %s: Post author. */
=======
				/* translators: %s: post author */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				__( 'Published by %s', 'twentynineteen' ),
				esc_html( get_the_author() )
			);
			?>
		</span>
	</h2>
	<p class="author-description">
		<?php the_author_meta( 'description' ); ?>
		<a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
			<?php _e( 'View more posts', 'twentynineteen' ); ?>
		</a>
	</p><!-- .author-description -->
</div><!-- .author-bio -->
<<<<<<< HEAD
	<?php
endif;
=======
<?php endif; ?>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
