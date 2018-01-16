<?php
/** widgets */
if( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name' => 'Viedeo_list_classification',
		'id'  => 'sidebar-1',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h4>',
		'after_title' => '</h4>'
	));
}

/** 缩略图 */
if ( function_exists('add_theme_support') ){
	add_theme_support('post-thumbnails');
	add_image_size('thumbnail', 160, 120, true);
	add_image_size('show', 80, 60, true);
}

function post_thumbnail_src($size){
	global $post;
	if( $values = get_post_custom_values("show") ) {
		$values = get_post_custom_values("show"); //在文章中的自定义字段中show对应的图片地址
		$post_thumbnail_src = $values [0];
	} elseif( has_post_thumbnail() ){
		switch($size){
			case 'thumbnail':
				$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail');
				$post_thumbnail_src = $thumbnail_src [0];
				break;
			case 'show':
				$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'show');
				$post_thumbnail_src = $thumbnail_src [0];
				break;
			default:
				break;
		}
	} else {
		$post_thumbnail_src = ''; //如果没有缩略图获取随机图片
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		$post_thumbnail_src = $matches [1] [0];
		if(emptyempty($post_thumbnail_src)){
			$random = mt_rand(1, 10);
			echo get_bloginfo ( 'stylesheet_directory' );
			echo '/img/random/'.$random.'.jpg';
		}
	};
	echo $post_thumbnail_src;
}
function autoset_featured() {
	global $post;
	$already_has_thumb = has_post_thumbnail($post->ID);
	if (!$already_has_thumb)  {
		$attached_image = get_children( "post_parent=$post->ID&post_type=attachment&post_mime_type=image&numberposts=1" );
		if ($attached_image) {
			foreach ($attached_image as $attachment_id => $attachment) {
			set_post_thumbnail($post->ID, $attachment_id);
			}
		}
	}
}
add_action('the_post', 'autoset_featured');
add_action('save_post', 'autoset_featured');
add_action('draft_to_publish', 'autoset_featured');
add_action('new_to_publish', 'autoset_featured');
add_action('pending_to_publish', 'autoset_featured');
add_action('future_to_publish', 'autoset_featured');

/** 评论 */

if ( ! function_exists( 'simright_comment' ) ) :
	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own simright_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 * @since Twenty Twelve 1.0
	 */
	function simright_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback':
			case 'trackback':
				// Display trackbacks differently than normal comments.
		?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'simright' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'simright' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
				break;
			default:
				// Proceed with normal comments.
				global $post;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf(
						'<cite><b class="fn">%1$s</b> %2$s</cite>',
						get_comment_author_link(),
						// If current post author is also comment author, make it known visually.
						( $comment->user_id === $post->post_author ) ? '<span></span>' : ''
					);
					printf(
						'<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'simright' ), get_comment_date(), get_comment_time() )
					);
					?>
				</header><!-- .comment-meta -->
				<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'simright' ); ?></p>
			<?php endif; ?>
				<section class="comment-content comment">
				<?php comment_text(); ?>
				<div class="reply">
				<?php
					comment_reply_link(
						array_merge(
							$args, array(
								'reply_text' => '<img class="reply" src="https://oss.simright.com/images/reply.svg">',
								'depth'      => $depth,
								'max_depth'  => 2,
							)
						)
					);
				?>
				</div><!-- .reply -->
				</section><!-- .comment-content -->
			</article><!-- #comment-## -->
		<?php
				break;
		endswitch; // end comment_type check
	}
endif;

/** script */
function simright_scripts() {
	wp_enqueue_script( 'comment-reply' );
	wp_localize_script(
		'simright_scripts', 'screenReaderText', array(
			'expand'   => __( 'expand child menu', 'simright' ),
			'collapse' => __( 'collapse child menu', 'simright' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'simright_scripts' );

/** 注册字符串 */
if( function_exists('pll_register_string') ) {
	pll_register_string('Home','Home');
	pll_register_string('Products','Products');
	pll_register_string('Public Cloud Apps','Public Cloud Apps');
	pll_register_string('Private Cloud Solutions','Private Cloud Solutions');
	pll_register_string('Simulator - Structural Analysis','Simulator - Structural Analysis');
	pll_register_string('Toptimizer - Topology Optimization','Toptimizer - Topology Optimization');
	pll_register_string('CAE Converter - CAE model converter','CAE Converter - CAE model converter');
	pll_register_string('CAD Converter - CAD model converter','CAD Converter - CAD model converter');
	pll_register_string('Viewer - CAD/CAE model viewer','Viewer - CAD/CAE model viewer');
	pll_register_string('Resources','Resources');
	pll_register_string('Public Projects','Public Projects');
	pll_register_string('Model Library','Model Library');
	pll_register_string('Pricing','Pricing');
	pll_register_string('Blog','Blog');
	pll_register_string('About','About');
	pll_register_string('About Us','About Us');
	pll_register_string('Contact Us','Contact Us');
	pll_register_string('Security','Security');
	pll_register_string('Qualification','Qualification');
	pll_register_string('Sales','Sales:');
	pll_register_string('Technical Support:','Technical Support:');
	pll_register_string('Converter','Converter');
	pll_register_string('Viewer','Viewer');
	pll_register_string('Simulator','Simulator');
	pll_register_string('Toptimizer','Toptimizer');
	pll_register_string('Follow Us','Follow Us');
	pll_register_string('Back','Back');
}

/** 搜索表单 */
function my_search_form( $form ) {
	$form = '<form role="search" class="neck-bar-search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
    <div class="form-group input-group">
    <input type="text" name="s" id="search" placeholder="Search" class="form-control" />
    <button type="submit" class="input-group-addon"><i class="glyphicon glyphicon-search"></i></button>
    </div>
    </form>';
    return $form;
}
add_filter( 'get_search_form', 'my_search_form' );
?>
