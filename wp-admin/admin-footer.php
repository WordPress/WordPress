
<div id="footer"><p><a href="http://wordpress.org/"><img src="../wp-images/wp-small.png" alt="WordPress" /></a><br />
<?php bloginfo('version'); ?> &#8212; <a href="http://wordpress.org/support/"><?php _e('Support Forums'); ?></a><br />
<?php printf(__('%s seconds'), number_format(timer_stop(), 2)); ?>
</p>
<p>	<a href="http://getfirefox.com/" title="<?php _e('WordPress recommends the open-source Firefox browser') ?>"><img src="http://www.mozilla.org/products/firefox/buttons/getfirefox_88x31.png" width="88" height="31" alt="Get Firefox"></a></p>
</div>
<?php do_action('admin_footer', ''); ?>
</body>
</html>