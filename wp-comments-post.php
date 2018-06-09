<?php
/**
 * '댓글쓰기' 기능을 처리하고 댓글이 복제되는 것을 막는다.
 *
 * @package WordPress
 */

if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0' ) ) ) {
		$protocol = 'HTTP/1.0';
	}

	header( 'Allow: POST' );
	header( "$protocol 405 Method Not Allowed" );
	header( 'Content-Type: text/plain' );
	exit;
}

/** 워드프레스 환경을 설정한다. */
require( dirname( __FILE__ ) . '/wp-load.php' );

nocache_headers();

$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
if ( is_wp_error( $comment ) ) {
	$data = intval( $comment->get_error_data() );
	if ( ! empty( $data ) ) {
		wp_die(
			'<p>' . $comment->get_error_message() . '</p>', __( 'Comment Submission Failure' ), array(
				'response'  => $data,
				'back_link' => true,
			)
		);
	} else {
		exit;
	}
}

$user = wp_get_current_user();
$cookies_consent = ( isset( $_POST['wp-comment-cookies-consent'] ) );

/**
 * comment cookie들이 설정되었을 때 다른 행동을 수행한다.
 *
 * @since 3.4.0
 * @since 4.9.6 `$cookies_consent` 파라미터가 추가되었음.
 *
 * @param WP_Comment $comment         Comment object.
 * @param WP_User    $user            Comment 작성자의 user object. 처음에는 아마 존재하지 않는다.
 * @param boolean    $cookies_consent Comment 작성자가 쿠키를 저장할지 안할지 여부
 */
do_action( 'set_comment_cookies', $comment, $user, $cookies_consent );

$location = empty( $_POST['redirect_to'] ) ? get_comment_link( $comment ) : $_POST['redirect_to'] . '#comment-' . $comment->comment_ID;

/**
 * 게시 후 주석을 보내도록 위치 URI를 필터링합니다.
 * 
 * @since 2.0.5
 *
 * @param string     $_POST를 통하여 'redirect_to' URI가 보내는 위치
 * @param WP_Comment $comment  Comment object.
 */
$location = apply_filters( 'comment_post_redirect', $location, $comment );

wp_safe_redirect( $location );
exit;
