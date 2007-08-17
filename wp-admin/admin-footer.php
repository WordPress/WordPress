
<div id="footer">
<p><?php 

$upgrade = apply_filters( 'update_footer', '' );
printf( __( 'Thank you for creating with <a href="%s">WordPress</a> | <a href="%s">Documentation</a> | <a href="%s">Feedback</a> %s' ), 'http://wordpress.org/', 'http://codex.wordpress.org/', 'http://wordpress.org/support/forum/4', $upgrade ) 

?></p>
</div>
<?php do_action('admin_footer', ''); ?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>