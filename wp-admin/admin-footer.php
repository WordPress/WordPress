
<div id="footer"><p><a href="http://wordpress.org/" id="wordpress-logo"><img src="images/wordpress-logo.png" alt="WordPress" /></a></p>
<p>
<a href="http://codex.wordpress.org/"><?php _e('Documentation'); ?></a> &#8212; <a href="http://wordpress.org/support/"><?php _e('Support Forums'); ?></a> <br />
<?php bloginfo('version'); ?> &#8212; <?php printf(__('%s seconds'), number_format(timer_stop(), 2)); ?>
</p>

</div>
<?php check_for_pings(); ?>
<?php do_action('admin_footer', ''); ?>

</body>
</html>