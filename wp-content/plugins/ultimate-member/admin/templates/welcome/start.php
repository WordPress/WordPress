
	<?php global $ultimatemember; include_once um_path . 'admin/templates/welcome/about_header.php'; ?>

	<div class="changelog">
		<h3>Getting Started</h3>
		<div class="feature-section">

			<p>Ultimate Member has been designed to be as easy to use as possible and you shouldn’t run into any difficulties. However, the plugin contains lots of different elements so we have created the following page to help you get started with Ultimate Member.</p>
			
		</div>
	</div>

	<div class="changelog">
		
		<div class="feature-section under-the-hood two-col">
			
			<div class="col">
				<h4>Automatically installed pages</h4>
				<p>Upon activation the plugin will install 7 core pages. These pages are required for the plugin to function correctly and cannot be deleted.</p>
				<p>
					<ul>
						<li><a href="<?php echo um_get_core_page('register'); ?>" target="_blank">Register</a></li>
						<li><a href="<?php echo um_get_core_page('login'); ?>" target="_blank">Login</a></li>
						<li><a href="<?php echo um_get_core_page('user'); ?>" target="_blank">User</a></li>
						<li><a href="<?php echo um_get_core_page('members'); ?>" target="_blank">Members</a></li>
						<li><a href="<?php echo um_get_core_page('account'); ?>" target="_blank">Account</a></li>
						<li><a href="<?php echo admin_url('post.php?post=' . $ultimatemember->permalinks->core['logout'] . '&action=edit'); ?>" target="_blank">Logout</a></li>
						<li><a href="<?php echo um_get_core_page('password-reset'); ?>" target="_blank">Password Reset</a></li>
					</ul>
				</p>
			</div>

			<div class="col">
				<h4>Getting started</h4>
				<p>The plugin has several different elements in the WordPress admin that allow you to customize your community/membership site:</p>
				<p>
					<ul>
						<li><a href="<?php echo admin_url('admin.php?page=ultimatemember'); ?>" target="_blank">Dashboard</a></li>
						<li><a href="<?php echo admin_url('admin.php?page=um_options'); ?>" target="_blank">Settings</a></li>
						<li><a href="<?php echo admin_url('edit.php?post_type=um_form'); ?>" target="_blank">Forms</a></li>
						<li><a href="<?php echo admin_url('edit.php?post_type=um_role'); ?>" target="_blank">Member Levels</a></li>
						<li><a href="<?php echo admin_url('edit.php?post_type=um_directory'); ?>" target="_blank">Member Directories</a></li>
					</ul>
				</p>
			</div>
			
		</div>
		
	</div>
	
	<div class="changelog feature-list">
		<h2>Need more help?</h2>
		<div class="feature-section">

			<p>If you have run into an issue with Ultimate Member, you should first have a look at our documentation and perform a search of the docs. If, after performing a search of the docs you can not find anything related to your issue/question then you can submit a <a href="https://wordpress.org/support/plugin/ultimate-member" target="_blank">support ticket</a>.</p>
			
			<p class="um-admin-notice" style="text-align:center"><a href="http://docs.ultimatemember.com/" target="_blank" class="button button-primary">View Documentation</a></p>
			
		</div>
	</div>
	
	<?php include_once um_path . 'admin/templates/welcome/about_footer.php'; ?>