<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

$title = __( 'About' );

list( $display_version ) = explode( '-', $wp_version );

include( './admin-header.php' );
?>
<div class="wrap about-wrap">

<h1><?php printf( __( 'Welcome to WordPress %s' ), $display_version ); ?></h1>

<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Using WordPress %s will improve your looks, personality, and web publishing experience. Okay, just the last one, but still. :)' ), $display_version ); ?></div>

<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

<h2 class="nav-tab-wrapper">
	<a href="about.php" class="nav-tab nav-tab-active">
		<?php printf( __( 'What&#8217;s New in %s' ), $display_version ); ?>
	</a><a href="credits.php" class="nav-tab">
		<?php _e( 'Credits' ); ?>
	</a><a href="freedoms.php" class="nav-tab">
		<?php _e( 'Freedoms' ); ?>
	</a>
</h2>

<div class="changelog">
	<h3><?php _e( 'Easier Uploading' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<div class="feature-images">
			<img src="images/screenshots/media-icon.png" width="200" class="angled-right" />
			<img src="images/screenshots/drag-and-drop.png" width="200" class="angled-left" />
		</div>
		<div class="left-feature">
			<h4><?php _e( 'File Type Detection' ); ?></h4>
			<p><?php _e( 'We&#8217;ve streamlined things! Instead of needing to click on a specific upload icon based on your file type, now there&#8217;s just one. Once your file is uploaded, the appropriate fields will be displayed for entering information based on the file type.' ); ?></p>

			<h4><?php _e( 'Drag-and-Drop Media Uploader' ); ?></h4>
			<p><?php _e( 'Adding photos or other files to posts and pages just got easier. Drag files from your desktop and drop them into the uploader. Add one file at a time, or many at once.' ); ?></p>

			<h4><?php _e( 'More File Formats' ); ?></h4>
			<p><?php _e( 'We&#8217;ve added the rar and 7z file formats to the list of allowed file types in the uploader.' ); ?></p>
		</div>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Dashboard Design' ); ?></h3>

	<div class="feature-section text-features">
		<h4><?php _e( 'Flyout Menus' ); ?></h4>
		<p><?php _e( 'Speed up navigating the dashboard and reduce repetitive clicking with our new flyout submenus. As you hover over each main menu item in your dashboard navigation, the submenus will magically appear, providing single-click access to any dashboard screen.' ); ?></p>

		<div>
		<h4><?php _e( 'Header + Admin Bar = Toolbar' ); ?></h4>
		<p><?php _e( 'To save space and increase efficiency, we&#8217;ve combined the admin bar and the old Dashboard header into one persistent toolbar. Hovering over the toolbar items will reveal submenus when available for quick access. ' ); ?></p>
		</div>
	</div>

	<div class="feature-section screenshot-features">
		<div class="angled-left">
			<img src="images/screenshots/admin-flyouts.png" />
			<h4><?php _e( 'Responsive Design' ); ?></h4>
			<p><?php _e( 'Certain dashboard screens have been updated to look better at various sizes, including improved iPad/tablet support.' ); ?></p>
		</div>
		<div class="angled-right">
			<img src="images/screenshots/help-screen.png" />
			<h4><?php _e( 'Help Tabs' ); ?></h4>
			<p><?php _e( 'The Help tabs located in the upper corner of the dashboard screens below your name have gotten a facelift. Help content is broken into smaller sections for easier access, with links to relevant documentation and the support forums always visible.' ); ?></p>
		</div>
	</div>
</div>

<div class="changelog">
	<h3><?php _e( 'Feels Like the First Time' ); ?></h3>

	<div class="feature-section images-stagger-right">
		<div class="feature-images">
			<img src="images/screenshots/new-feature-pointer.png" class="angled-right" />
			<img src="images/screenshots/welcome-screen.png" class="angled-left" />
		</div>
		<div class="left-feature">
			<h4><?php _e( 'New Feature Pointers' ); ?></h4>
			<p><?php _e( 'When we add new features, move navigation, or do anything else with the dashboard that might throw you for a loop when you update your WordPress site, we&#8217;ll let you know about it with new feature pointers explaining the change.' ); ?></p>

			<h4><?php _e( 'Post-update Changelog' ); ?></h4>
			<p><?php _e( 'This screen! From now on when you update WordPress, you&#8217;ll be brought to this screen &mdash; also accessible any time from the W logo in the corner of the toolbar &mdash; to get an overview of what&#8217;s changed.' ); ?></p>

			<h4><?php _e( 'Dashboard Welcome' ); ?></h4>
			<p><?php _e( 'The dashboard home screen will have a Welcome area that displays when a new WordPress installation is accessed for the first time, prompting the site owner to complete various setup tasks. Once dismissed, this welcome can be accessed via the dashboard home screen options tab.' ); ?></p>
		</div>
	</div>

</div>

<div class="changelog">
	<h3><?php _e( 'Content Tools' ); ?></h3>

	<div class="feature-section three-col">
		<div>
			<h4><?php _e( 'Better Co-Editing' ); ?></h4>
			<img src="images/screenshots/coediting.png" class="element-screenshot" />
			<p><?php _e( 'Have you ever gone to edit a post after someone else has finished with it, only to get an alert that tells you the other person is still editing the post? From now on, you&#8217;ll only get that alert if another person is still on the editing screen &mdash; no more time lag.' ); ?></p>
		</div>
		<div>
			<h4><?php _e( 'Tumblr Importer' ); ?></h4>
			<p><?php _e( 'Want to import content from Tumblr to WordPress? No problem! Go to <span class="no-break">Tools &rarr; Import</span> to get the new Tumblr Importer, which maps your Tumblog posts to the matching WordPress post formats. Tip: Choose a theme designed to display post formats to get the greatest benefit from the importer.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'Widget Improvements' ); ?></h4>
			<p><?php _e( 'Changing themes often requires widget re-configuration based on the number and position of sidebars. Now if you change back to a previous theme, the widgets will automatically go back to how you had them arranged in that theme. <em>Note: if you&#8217;ve added new widgets since the switch, you&#8217;ll need to rescue them from the Inactive Widgets area.</em>' ); ?></p>
		</div>
	</div>

</div>

<div class="changelog">
	<h3><?php _e( 'Under the Hood' ); ?></h3>

	<div class="feature-section three-col">
		<div>
			<h4><?php _e( 'Flexible Permalinks' ); ?></h4>
			<p><?php _e( 'You have more freedom when choosing a post permalink structure. Skip the date information or add a category slug without a performance penalty.' ); ?></p>
		</div>
		<div>
			<h4><?php _e( 'Post Slugs: Less Funky' ); ?></h4>
			<p><?php _e( 'Funky characters in post titles (e.g. curly quotes from a word processor) will no longer result in garbled post slugs.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'jQuery and jQuery UI' ); ?></h4>
			<p><?php printf( __( 'WordPress now includes the entire jQuery UI stack and the latest version of jQuery: %s.' ), '1.7.1' ); ?></p>
		</div>
	</div>

	<div class="feature-section three-col">
		<div>
			<h4 style="direction:ltr">is_main_query()</h4>
			<p><?php _e( 'This handy method will tell you if a <code>WP_Query</code> object is the main WordPress query or a secondary query.' ); ?></p>
		</div>
		<div>
			<h4><?php _e( 'WP_Screen API' ); ?></h4>
			<p><?php _e( 'WordPress has a nice new API for working with admin screens. Create rich screens, add help documentation, adapt to screen contexts, and more.' ); ?></p>
		</div>
		<div class="last-feature">
			<h4><?php _e( 'Editor API Overhaul' ); ?></h4>
			<p><?php _e( 'The new editor API automatically pulls in all the JS and CSS goodness for the editor. It even supports multiple editors on the same page.' ); ?></p>
		</div>
	</div>

</div>

<div class="return-to-dashboard">
	<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
	<a href="<?php echo esc_url( network_admin_url( 'update-core.php' ) ); ?>"><?php
		is_multisite() ? _e( 'Return to Updates' ) : _e( 'Return to Dashboard &rarr; Updates' );
	?></a> |
	<?php endif; ?>
	<a href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Go to Dashboard &rarr; Home' ); ?></a>
</div>

</div>
<?php

include( './admin-footer.php' );
