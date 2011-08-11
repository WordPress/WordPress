<?php
/**
 * WordPress Administration Template Footer
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');
?>

<div class="clear"></div></div><!-- wpbody-content -->
<div class="clear"></div></div><!-- wpbody -->
<div class="clear"></div></div><!-- wpcontent -->

<div id="footer">
<?php do_action( 'in_admin_footer' ); ?>
<p id="footer-left" class="alignleft"><?php
$upgrade = apply_filters( 'update_footer', '' );
$footer_text = array(
	'<span id="footer-thankyou">' . __( 'Thank you for creating with <a href="http://wordpress.org/">WordPress</a>.' ) . '</span>',
	__( '<a href="http://codex.wordpress.org/">Documentation</a>' ),
	sprintf( '<a href="%s">' . __( 'Freedoms' ) . '</a>', admin_url( 'freedoms.php' ) ),
	__('<a href="http://wordpress.org/support/forum/4">Feedback</a>'),
	sprintf( '<a href="%s">' . __('Credits') . '</a>', admin_url('credits.php') ),
);
echo apply_filters( 'admin_footer_text', implode( ' &bull; ', $footer_text ) );
unset( $footer_text );
?></p>
<p id="footer-upgrade" class="alignright"><?php echo $upgrade; ?></p>
<div class="clear"></div>
</div>
<?php
do_action('admin_footer', '');
do_action('admin_print_footer_scripts');
do_action("admin_footer-" . $GLOBALS['hook_suffix']);

// get_site_option() won't exist when auto upgrading from <= 2.7
if ( function_exists('get_site_option') ) {
	if ( false === get_site_option('can_compress_scripts') )
		compression_test();
}

?>

<div class="clear"></div></div><!-- wpwrap -->
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>
