<?php
/**
 * Test the user's current authorization state
 *
 * @package WordPress
 * @since 3.6.0
 */
class WP_Auth_Check {

	/**
	 * Holds the singleton instance of this object
	 */
	private static $_instance = null;

	/**
	 * Private constructor because we're a singleton
	 */
	private function __construct() {}

	/**
	 * Initialize the singleton
	 */
	public static function get_instance() {
		$this_class = get_called_class(); // gets the right class when this is extended
		if ( ! ( self::$_instance instanceof $this_class ) ) {
			self::$_instance = new $this_class;
			self::$_instance->_init();
		}

		return self::$_instance;
	}

	/**
	 * Object init, sets up hooks. Not done in the constructor so that the
	 * _init() method may be extended without breaking the singleton.
	 */
	protected function _init() {
		if ( is_admin() ) {
			add_action( 'admin_footer', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'footer_js' ) );
		} elseif ( is_user_logged_in() ) {
			add_action( 'wp_footer', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_print_footer_scripts', array( $this, 'footer_js' ) );
		}

		add_filter( 'heartbeat_received', array( $this, 'login' ), 10, 2 );
		add_filter( 'heartbeat_nopriv_received', array( $this, 'nopriv_login' ), 10, 2 );
	}

	/**
	 * Checks if the user is still logged in
	 */
	public function login( $response, $data ) {
		if ( array_key_exists('wp-auth-check', $data) && ( ! isset( $_COOKIE[LOGGED_IN_COOKIE] ) || ! wp_validate_auth_cookie() || ! empty( $GLOBALS['login_grace_period'] ) ) )
			$response['wp-auth-check-html'] = $this->notice();

		return $response;
	}

	/**
	 * Runs when a user is expected to be logged in
	 * but has logged out or cannot be validated
	 */
	public function nopriv_login( $response, $data ) {
		if ( array_key_exists('wp-auth-check', $data) )
			$response['wp-auth-check-html'] = $this->notice();

		return $response;
	}
	
	public function footer_js() {
		?>
		<script>
		(function($){
			$( document ).on( 'heartbeat-tick.wp-auth-check', function( e, data ) {
				var wrap = $('#wp-auth-check-notice-wrap');

				if ( data['wp-auth-check-html'] && ! wrap.length ) {
					$('body').append( data['wp-auth-check-html'] );
				} else if ( !data['wp-auth-check-html'] && wrap.length && ! wrap.data('logged-in') ) {
					wrap.remove();
				}
			}).on( 'heartbeat-send.wp-auth-check', function( e, data ) {
				data['wp-auth-check'] = 1;
			});
		}(jQuery));
		</script>
		<?php
	}

	public function enqueue_scripts() {
		// This will also enqueue jQuery
		wp_enqueue_script( 'heartbeat' );
	}

	/**
	 * Returns the login notice
	 */
	public function notice() {
		// Inline JS and CSS, keep the notice portable.
		return '
<div id="wp-auth-check-notice-wrap">
<style type="text/css" scoped>
#wp-auth-check {
	position: fixed;
	height: 90%;
	left: 50%;
	max-height: 415px;
	overflow: auto;
	top: 35px;
	width: 300px;
	margin: 0 0 0 -160px;
	padding: 12px 20px;
	border: 1px solid #ddd;
	background-color: #fbfbfb;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	z-index: 1000000000;
}
#wp-auth-check-form {
	background: url("' . admin_url('/images/wpspin_light-2x.gif') . '") no-repeat center center;
	background-size: 16px 16px;
}
#wp-auth-check-form iframe {
	height: 100%;
	overflow: hidden;
}
#wp-auth-check a.wp-auth-check-close {
	position: absolute;
	right: 8px;
	top: 8px;
	width: 24px;
	height: 24px;
	background: url("' . includes_url('images/uploader-icons.png') . '") no-repeat scroll -95px center transparent;
}
#wp-auth-check h3 {
	margin: 0 0 12px;
	padding: 0;
	font-size: 1.25em;
}
@media print,
  (-o-min-device-pixel-ratio: 5/4),
  (-webkit-min-device-pixel-ratio: 1.25),
  (min-resolution: 120dpi) {
	#wp-auth-check a.wp-auth-check-close {
		background-image: url("' . includes_url('images/uploader-icons-2x.png') . '");
		background-size: 134px 15px;
	}
}
</style>
<div id="wp-auth-check" tabindex="0">
<h3>' .  __('Session expired') . '</h3>
<a href="#" class="wp-auth-check-close"><span class="screen-reader-text">' . __('close') . '</span></a>
<div id="wp-auth-check-form">
	<iframe src="' . esc_url( add_query_arg( array( 'interim-login' => 1 ), wp_login_url() ) ) . '" frameborder="0"></iframe>
</div>
</div>
<script type="text/javascript">
(function($){
var el, wrap = $("#wp-auth-check-notice-wrap");
el = $("#wp-auth-check").focus().find("a.wp-auth-check-close").on("click", function(e){
	el.fadeOut(200, function(){ wrap.remove(); });
	e.preventDefault();
});
$("#wp-auth-check-form iframe").load(function(){
	var height;
	try { height = $(this.contentWindow.document).find("#login").height(); } catch(er){}
	if ( height ) {
		$("#wp-auth-check").css("max-height", height + 40 + "px");
		$(this).css("height", height + 5 + "px");
		if ( height < 200 ) {
			wrap.data("logged-in", true);
			setTimeout( function(){ wrap.fadeOut(200, function(){ wrap.remove(); }); }, 5000 );
		}
	}
});
}(jQuery));
</script>
</div>';

	}
}

