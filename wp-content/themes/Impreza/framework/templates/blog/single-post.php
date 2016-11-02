<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Outputs one single post.
 *
 * (!) Should be called after the current $wp_query is already defined
 *
 * @var $metas array Meta data that should be shown: array('date', 'author', 'categories', 'comments')
 * @var $show_tags boolean Should we show tags?
 *
 * @action Before the template: 'us_before_template:templates/blog/single-post'
 * @action After the template: 'us_after_template:templates/blog/single-post'
 * @filter Template variables: 'us_template_vars:templates/blog/single-post'
 */

$us_layout = US_Layout::instance();

// Filling and filtering parameters
$default_metas = array( 'date', 'author', 'categories', 'comments' );
$metas = ( isset( $metas ) AND is_array( $metas ) ) ? array_intersect( $metas, $default_metas ) : $default_metas;
if ( ! isset( $show_tags ) ) {
	$show_tags = TRUE;
}

$post_format = get_post_format() ? get_post_format() : 'standard';

// Note: it should be filtered by 'the_content' before processing to output
$the_content = get_the_content();

$preview_type = usof_meta( 'us_post_preview_layout' );
if ( $preview_type == '' ) {
	$preview_type = us_get_option( 'post_preview_layout', 'basic' );
}

$preview_html = '';
$preview_bg = '';
if ( $preview_type != 'none' AND ! post_password_required() ) {
	$post_thumbnail_id = get_post_thumbnail_id();
	if ( $preview_type == 'basic' ) {
		if ( in_array( $post_format, array( 'video', 'gallery', 'audio' ) ) ) {
			$preview_html = us_get_post_preview( $the_content, TRUE );
			if ( $preview_html == '' AND $post_thumbnail_id ) {
				$preview_html = wp_get_attachment_image( $post_thumbnail_id, 'large' );
			}
		} else {
			if ( $post_thumbnail_id ) {
				$preview_html = wp_get_attachment_image( $post_thumbnail_id, 'large' );
			} else {
				// Retreiving preview HTML from the post content
				$preview_html = us_get_post_preview( $the_content, TRUE );
			}
		}
	} elseif ( $preview_type == 'modern' OR 'trendy' ) {
		if ( $post_thumbnail_id ) {
			$image = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
			$preview_bg = $image[0];
		} elseif ( $post_format == 'image' ) {
			// Retreiving image from post content to use it as preview background
			$preview_bg_html = us_get_post_preview( $the_content, TRUE );
			if ( preg_match( '~src=\"([^\"]+)\"~u', $preview_bg_html, $matches ) ) {
				$preview_bg = $matches[1];
			}
		}
	}
}

if ( ! post_password_required() ) {
	$the_content = apply_filters( 'the_content', $the_content );
}

// The post itself may be paginated via <!--nextpage--> tags
$pagination = us_wp_link_pages( array(
	'before' => '<div class="g-pagination"><nav class="navigation pagination">',
	'after' => '</nav></div>',
	'next_or_number' => 'next_and_number',
	'nextpagelink' => '>',
	'previouspagelink' => '<',
	'link_before' => '<span>',
	'link_after' => '</span>',
	'echo' => 0,
) );

// If content has no sections, we'll create them manually
$has_own_sections = ( strpos( $the_content, ' class="l-section' ) !== FALSE );
if ( ! $has_own_sections ) {
	$the_content = '<section class="l-section"><div class="l-section-h i-cf" itemprop="text">' . $the_content . $pagination . '</div></section>';
} elseif ( ! empty( $pagination ) ) {
	$the_content .= '<section class="l-section"><div class="l-section-h i-cf" itemprop="text">' . $pagination . '</div></section>';
}

// Meta => certain html in a proper order
$meta_html = array_fill_keys( $metas, '' );

// Preparing post metas separately because we might want to order them inside the .w-blog-post-meta in future
$meta_html['date'] = '<time class="w-blog-post-meta-date date updated';
if ( ! in_array( 'date', $metas ) ) {
	// Hiding from users but not from search engines
	$meta_html['date'] .= ' hidden';
}
$meta_html['date'] .= '" itemprop="datePublished">' . get_the_date() . '</time>';

$meta_html['author'] = '<span class="w-blog-post-meta-author vcard author';
if ( ! in_array( 'author', $metas ) ) {
	$meta_html['author'] .= ' hidden';
}
$meta_html['author'] .= '">';
$meta_html['author'] .= '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ) . '" class="fn">' . get_the_author() . '</a>';
$meta_html['author'] .= '</span>';

if ( in_array( 'categories', $metas ) ) {
	$meta_html['categories'] = get_the_category_list( ', ' );
	if ( ! empty( $meta_html['categories'] ) ) {
		$meta_html['categories'] = '<span class="w-blog-post-meta-category">' . $meta_html['categories'] . '</span>';
	}
}

$comments_number = get_comments_number();
if ( in_array( 'comments', $metas ) AND ! ( $comments_number == 0 AND ! comments_open() ) ) {
	$meta_html['comments'] .= '<span class="w-blog-post-meta-comments">';
	// TODO Replace with get_comments_popup_link() when https://core.trac.wordpress.org/ticket/17763 is resolved
	ob_start();
	$comments_label = sprintf( _n( '%s comment', '%s comments', $comments_number, 'us' ), $comments_number );
	comments_popup_link( us_translate_with_external_domain( 'No Comments' ), $comments_label, $comments_label );
	$meta_html['comments'] .= ob_get_clean();
	$meta_html['comments'] .= '</span>';
}

if ( us_get_option( 'post_nav' ) ) {
	$prevnext = us_get_post_prevnext();
}

if ( $show_tags ) {
	$the_tags = get_the_tag_list( '', ', ', '' );
}

$meta_html = apply_filters( 'us_single_post_meta_html', $meta_html, get_the_ID() );

?>
<article <?php post_class( 'l-section for_blogpost preview_' . $preview_type ) ?>>
	<div class="l-section-h i-cf">
		<div class="w-blog">
			<?php if ( ! empty( $preview_bg ) ): ?>
				<div class="w-blog-post-preview" style="background-image: url(<?php echo $preview_bg ?>)"></div>
			<?php elseif ( ! empty( $preview_html ) OR $preview_type == 'modern' ): ?>
				<div class="w-blog-post-preview">
					<?php echo $preview_html ?>
				</div>
			<?php endif; ?>
			<div class="w-blog-post-body">
				<h1 class="w-blog-post-title entry-title" itemprop="headline"><?php the_title() ?></h1>

				<div class="w-blog-post-meta<?php echo empty( $metas ) ? ' hidden' : '' ?>">
					<?php echo implode( '', $meta_html ) ?>
				</div>
			</div>
		</div>

		<?php if ( $preview_type == 'trendy' AND $us_layout->sidebar_pos == 'none' AND $us_layout->titlebar == 'none' ): ?>
			<script>
				(function( $ ){
					var $window = $(window),
						windowWidth = $window.width();

					$.fn.trendyPreviewParallax = function(){
						var $this = $(this),
							$postBody = $('.w-blog-post-body');

						function update(){
							if (windowWidth > 900){
								var scrollTop = $window.scrollTop(),
									thisPos = scrollTop*0.3,
									postBodyPos = scrollTop*0.4,
									postBodyOpacity = Math.max(0, 1-scrollTop/450);
								$this.css('transform', 'translateY('+thisPos+'px)');
								$postBody.css('transform', 'translateY('+postBodyPos+'px)');
								$postBody.css('opacity', postBodyOpacity);
							} else {
								$this.css('transform', '');
								$postBody.css('transform', '');
								$postBody.css('opacity', '');
							}
						}

						function resize(){
							windowWidth = $window.width();
							update();
						}

						$window.bind({scroll: update, load: resize, resize: resize});
						resize();
					};

					$('.l-section.for_blogpost.preview_trendy .w-blog-post-preview').trendyPreviewParallax();

				})(jQuery);
			</script>
		<?php endif; ?>
	</div>
</article>

<?php echo $the_content ?>

<?php if ( $show_tags AND ! empty( $the_tags ) ): ?>
	<section class="l-section for_tags">
		<div class="l-section-h i-cf">
			<div class="g-tags">
				<span class="g-tags-title"><?php _e( 'Tags', 'us' ) ?>:</span>
				<?php echo $the_tags ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( us_get_option( 'post_sharing' ) ) : ?>
	<section class="l-section for_sharing">
		<div class="l-section-h i-cf">
			<?php
			$sharing_providers = (array) us_get_option( 'post_sharing_providers' );
			$us_sharing_atts = array(
				'type' => us_get_option( 'post_sharing_type', 'simple' ),
			);
			foreach ( array( 'email', 'facebook', 'twitter', 'linkedin', 'gplus', 'pinterest', 'vk' ) as $provider ) {
				$us_sharing_atts[ $provider ] = in_array( $provider, $sharing_providers );
			}
			us_load_template( 'shortcodes/us_sharing', array( 'atts' => $us_sharing_atts ) );
			?>
		</div>
	</section>
<?php endif; ?>

<?php if ( us_get_option( 'post_author_box' ) ): ?>
	<?php us_load_template( 'templates/blog/single-post-author' ) ?>
<?php endif; ?>

<?php if ( us_get_option( 'post_nav' ) AND ! empty( $prevnext ) ): ?>
	<section class="l-section for_blognav">
		<div class="l-section-h i-cf">
			<div class="w-blognav">
				<?php foreach ( $prevnext as $key => $item ): ?>
					<a class="w-blognav-<?php echo $key ?>" href="<?php echo $item['link'] ?>">
						<span class="w-blognav-meta"><?php echo $item['meta'] ?></span>
						<span class="w-blognav-title"><?php echo $item['title'] ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( us_get_option( 'post_related', TRUE ) ): ?>
	<?php us_load_template( 'templates/blog/single-post-related' ) ?>
<?php endif; ?>

<?php if ( comments_open() OR get_comments_number() != '0' ): ?>
	<section class="l-section for_comments">
		<div class="l-section-h i-cf">
			<?php wp_enqueue_script( 'comment-reply' ) ?>
			<?php comments_template() ?>
		</div>
	</section>
<?php endif; ?>
