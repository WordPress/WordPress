<?php
// If a footer.php file exists in the WP root directory we
// use that, otherwise use this default wp-footer.php file.
if ( file_exists(ABSPATH . '/footer.php') ) :
	include(ABSPATH . '/footer.php');
else :
?>
</div>



<div id="menu">

<ul>
	<?php get_links_list(); ?>
 <li id="categories"><?php _e('Categories:'); ?>
	<ul>
	<?php wp_list_cats(); ?>
	</ul>
 </li>
 <li id="search">
   <label for="s"><?php _e('Search:'); ?></label>	
   <form id="searchform" method="get" action="<?php echo $PHP_SELF; ?>">
	<div>
		<input type="text" name="s" id="s" size="15" /><br />
		<input type="submit" name="submit" value="<?php _e('Search'); ?>" />
	</div>
	</form>
 </li>
 <li id="archives"><?php _e('Archives:'); ?>
 	<ul>
	 <?php wp_get_archives('type=monthly'); ?>
 	</ul>
 </li>
 <li id="calendar">
	<?php get_calendar(); ?>
 </li>
 <li id="meta"><?php _e('Meta:'); ?>
 	<ul>
		<li><?php wp_register(); ?></li>
		<li><?php wp_loginout(); ?></li>
		<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS'); ?>"><?php _e('<abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
		<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
		<li><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional'); ?>"><?php _e('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>'); ?></a></li>
		<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
		<li><a href="http://wordpress.org/" title="<?php _e('Powered by WordPress, state-of-the-art semantic personal publishing platform.'); ?>"><abbr title="WordPress">WP</abbr></a></li>
		<?php wp_meta(); ?>
	</ul>
 </li>

</ul>

</div>

</div>

<p class="credit"><!--<?php echo $wpdb->num_queries; ?> queries. <?php timer_stop(1); ?> seconds. --> <cite><?php echo sprintf(__("Powered by <a href='http://wordpress.org' title='%s'><strong>WordPress</strong></a>"), __("Powered by WordPress, state-of-the-art semantic personal publishing platform.")); ?></cite></p>
</body>
</html>
<?php endif; ?>