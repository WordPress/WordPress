<?php
/**
 * WordPress Administration Template Footer
 *
 * @package WordPress
 * @subpackage Administration
 */
?>

<br class="clear" /></div><!-- wpbody-content -->
</div><!-- wpbody -->
<br class="clear" /></div><!-- wpcontent -->
</div><!-- wpwrap -->

<div id="footer">
<p><?php
do_action( 'in_admin_footer' );
$upgrade = apply_filters( 'update_footer', '' );
echo '<span id="footer-thankyou">' . __('Thank you for creating with <a href="http://wordpress.org/">WordPress</a>.').'</span> | '.__('<a href="http://codex.wordpress.org/">Documentation</a>').' | '.__('<a href="http://wordpress.org/support/forum/4">Feedback</a>').' <span id="footer-upgrade">'.$upgrade . '</span>';
?>
</p>
</div>
<?php do_action('admin_footer', ''); ?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>