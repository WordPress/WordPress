<?php
		
		// Random tweet - must be kept to 102 chars to "fit"
		$tweets        = array(
			'The easiest way to create powerful online communities and beautiful user profiles with #WordPress'
		);
		shuffle( $tweets );
		
?>

<div class="wrap about-wrap um-about-wrap">

	<h1>Welcome to Ultimate Member</h1>

	<div class="about-text"><?php _e('Thank you for installing! Ultimate Member is a powerful community and membership plugin that allows you to create beautiful community and membership sites with WordPress.','ultimate-member'); ?></div>

	<div class="wp-badge um-badge">Version <?php echo ultimatemember_version; ?></div>
	
	<p class="um-admin-notice ultimatemember-actions">
		<a href="<?php echo admin_url('admin.php?page=um_options'); ?>" class="button button-primary"><?php _e('Settings','ultimate-member'); ?></a>
		<a href="http://docs.ultimatemember.com/" class="button button-secondary" target="_blank"><?php _e('Docs','ultimate-member'); ?></a>
		<a href="https://wordpress.org/support/plugin/ultimate-member" class="button button-secondary" target="_blank"><?php _e('Support','ultimate-member'); ?></a>
		<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://ultimatemember.com/" data-text="<?php echo esc_attr( $tweets[0] ); ?>" data-via="umplugin" data-size="large">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	</p>

	<h2 class="nav-tab-wrapper">
	
		<?php foreach( $this->about_tabs as $k => $tab ) {
		
			if ( isset( $_REQUEST['page'] ) && 'ultimatemember-'.$k == $_REQUEST['page']  ) {
				$active = 'nav-tab-active';
			} else {
				$active = '';
			}
			
		?>
		
		<a href="<?php echo admin_url('admin.php?page=ultimatemember-' . $k); ?>" class="nav-tab <?php echo $active; ?>"><?php echo $tab; ?></a>
		
		<?php } ?>
		
	</h2>