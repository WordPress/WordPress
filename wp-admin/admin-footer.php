
<div id="footer">
<p><?php

$upgrade = apply_filters( 'update_footer', '' );
echo __('Thank you for creating with <a href="http://wordpress.org/">WordPress</a>').' | '.__('<a href="http://codex.wordpress.org/">Documentation</a>').' | '.__('<a href="http://wordpress.org/support/forum/4">Feedback</a>').' '.$upgrade;

?></p>
</div>
<?php do_action('admin_footer', ''); ?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>
