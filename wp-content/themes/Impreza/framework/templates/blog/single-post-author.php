<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * Outputs summary about the post's author
 *
 * @action Before the template: 'us_before_template:templates/blog/single-post-author'
 * @action After the template: 'us_after_template:templates/blog/single-post-author'
 */

global $authordata;
$author_avatar = get_avatar( $authordata->ID );
$author_url = get_the_author_meta( 'url' );
if ( ! empty( $author_url ) ) {
	$author_avatar = '<a href="' . esc_url( $author_url ) . '" rel="author external" target="_blank">' . $author_avatar . '</a>';
}
?>

<section class="l-section for_author">
	<div class="l-section-h i-cf">
		<div class="w-author" itemscope="itemscope" itemtype="https://schema.org/Person" itemprop="author">
			<div class="w-author-img">
				<?php echo $author_avatar ?>
			</div>
			<div class="w-author-name" itemprop="name">
				<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?>"><?php the_author(); ?></a>
			</div>
			<div class="w-author-url" itemprop="url">
				<?php if ( get_the_author_meta( 'url' ) ) { ?>
				<a href="<?php echo esc_url( get_the_author_meta( 'url' ) ); ?>"><?php echo esc_url( get_the_author_meta( 'url' ) ); ?></a>
				<?php } ?>
			</div>
			<div class="w-author-desc" itemprop="description"><?php the_author_meta( 'description' ) ?></div>
		</div>
	</div>
</section>
