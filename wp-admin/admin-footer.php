
<div id="footer"><p><a href="http://wordpress.org/"><img src="../wp-images/wp-small.png" alt="WordPress" /></a><br />
<?php bloginfo('version'); ?> <br /> 
<a href="http://codex.wordpress.org/"><?php _e('Documentation'); ?></a> &#8212; <a href="http://wordpress.org/support/"><?php _e('Support Forums'); ?></a> <br />
<?php printf(__('%s seconds'), number_format(timer_stop(), 2)); ?>
</p>

</div>

<?php do_action('admin_footer', ''); ?>

<?php if ( isset( $editing ) ) : ?>
<script type="text/javascript">
WhenLoaded();
</script> 
<?php endif; ?>

</body>
</html>