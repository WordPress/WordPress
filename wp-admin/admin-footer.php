

<p align="center" style="width: 100%" class="tabletoprow"><?php printf(__('<strong><a href="%s">WordPress</a></strong> %s &#8212; <a href="%s">Support Forums</a><br />'), 'http://wordpress.org', $wp_version, 'http://wordpress.org/support/') ?>
<?php
     printf(__('%s seconds'), number_format(timer_stop(), 2));
?>
</p>

</body>
</html>