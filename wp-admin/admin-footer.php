
<div id="footer">
<p class="logo"><a href="http://wordpress.org/" id="wordpress-logo"><img src="images/wordpress-logo.png" alt="WordPress" /></a></p>
<p class="docs"><?php _e('<a href="http://codex.wordpress.org/">Documentation</a>'); ?> &#8212; <?php _e('<a href="http://wordpress.org/support/">Support Forums</a>'); ?><br />
<?php printf( __('Version %s'), get_bloginfo('version') ); ?></p>
</div>
<?php do_action('admin_footer', ''); ?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>