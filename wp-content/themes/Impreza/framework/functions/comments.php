<?php

class Walker_Comments_US extends Walker_Comment {

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 1;

		$output .= '<div class="w-comments-childlist">'."\n";

	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 1;

		$output .= '</div>'."\n";
	}
}

function us_comment_start( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	$author_link_start = $author_link_end = '';
	$author_url = get_comment_author_url();
	if ($author_url != '') {
		$author_link_start = '<a href="'.$author_url.'" target="_blank" rel="nofollow">';
		$author_link_end = '</a>';
	}
?>

	<div class="w-comments-item" id="comment-<?php comment_ID() ?>">
		<div class="w-comments-item-meta">
			<div class="w-comments-item-icon">
				<?php echo get_avatar($comment, $size = '50'); ?>
			</div>
			<div class="w-comments-item-author"><?php echo $author_link_start.get_comment_author().$author_link_end; ?></div>
			<a class="w-comments-item-date" href="#comment-<?php comment_ID() ?>"><?php echo get_comment_date().' '.get_comment_time() ?></a>
		</div>
		<div class="w-comments-item-text"><?php comment_text() ?></div>
		<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => '<div class="w-comments-item-answer">', 'after' => '</div>'))) ?>
	</div>


<?php
}

function us_comment_end( $comment, $args, $depth ) {
	return;
}
