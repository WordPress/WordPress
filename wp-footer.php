<?php
// If a footer.php file exists in the WP root directory we
// use that, otherwise use this default wp-footer.php file.
if ( file_exists(ABSPATH . '/footer.php') ) :
	include_once(ABSPATH . '/footer.php');
else :
?>
</div>



<?php
// This code pulls in the sidebar:
include(ABSPATH . '/wp-sidebar.php');
?>

</div>

<p class="credit"><!--<?php echo $wpdb->num_queries; ?> queries. <?php timer_stop(1); ?> seconds. --> <cite><?php echo sprintf(__("Powered by <a href='http://wordpress.org' title='%s'><strong>WordPress</strong></a>"), __("Powered by WordPress, state-of-the-art semantic personal publishing platform.")); ?></cite></p>

<?php do_action('wp_footer', ''); ?>
</body>
</html>
<?php endif; ?>