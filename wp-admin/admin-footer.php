

<p align="center" style="width: 100%" class="tabletoprow"><?php printf(__('<strong><a href="%1$s">WordPress</a></strong> %2$s &#8212; <a href="%3$s">Support Forums</a><br />'), 'http://wordpress.org', $wp_version, 'http://wordpress.org/support/') ?>
<?php
     printf(__('%s seconds'), number_format(timer_stop(), 2));
?>
</p>

</body>
</html>