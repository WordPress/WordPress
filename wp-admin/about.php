<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! wp_is_mobile() ) {
	wp_enqueue_style( 'wp-mediaelement' );
	wp_enqueue_script( 'mediaelement-vimeo' );
	wp_enqueue_script( 'wp-mediaelement' );
	wp_localize_script( 'mediaelement', '_wpmejsSettings', array(
		'pluginPath'        => includes_url( 'js/mediaelement/', 'relative' ),
		'classPrefix'       => 'mejs-',
		'stretching'        => 'responsive',
		'pauseOtherPlayers' => '',
	) );
}

$video_url = 'https://videopress.com/embed/AHz0Ca46?hd=true';
$lang_code = str_replace( '_', '-', get_user_locale() );
list( $lang_code ) = explode( '-', $lang_code );
if ( 'en' !== $lang_code ) {
	$video_url = add_query_arg( 'defaultLangCode', $lang_code, $video_url );
}

$title = __( 'About' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
	<div class="wrap about-wrap">
		<h1><?php printf( __( 'Welcome to WordPress&nbsp;%s' ), $display_version ); ?></h1>

		<p class="about-text"><?php printf( __( 'Thank you for updating to the latest version! WordPress %s will smooth your design workflow and keep you safe from coding errors.' ), $display_version ); ?></p>
		<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="about.php" class="nav-tab nav-tab-active"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
		</h2>

		<div class="feature-section one-col">
			<div class="col">
				<h2><?php _e( 'Major Customizer Improvements, Code Error Checking, and More!' ); ?></h2>
				<p><?php _e( 'Welcome to an improved Customizer workflow with design drafts,  locking, scheduling, and preview links.  What&#8217;s more, code syntax highlighting and error checking will make for a clean and smooth site building experience. Finally, if all that wasn&#8217;t pretty great, we&#8217;ve got a great new Gallery widget and improvements to theme browsing and switching.' ); ?></p>
			</div>
		</div>

		<hr />

		<h2><?php _e( 'Customizer Workflow Improved' ); ?></h2>

		<div class="headline-feature one-col">
			<div class="col">
				
			</div>
		</div>

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Draft and Schedule Site Design Customization' ); ?></h3>
				<p><?php _e( 'Yes, you read that right. Just like you can draft and revise posts and schedule them to go live on the date and time you choose, you can now tinker with your site&#8217;s design and schedule those design changes to go live as you please.' ); ?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Collaborate with Design Preview Links' ); ?></h3>
				<p><?php _e( 'Need to get some feedback on proposed site design changes? WordPress 4.9 gives you a preview link you can send to your team and customers so that you can collect and integrate feedback before you schedule the changes to go live. Can we say collaboration&#43;&#43;?' ); ?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'One Design Lock To Protect Them All' ); ?></h3>
				<p><?php _e( 'Ever encounter a scenario where two designers walk into a project and designer A overrides designer B&#8217;s beautiful changes? WordPress 4.9&#8217;s design lock feature (similar to post locking) secures your draft design so that no one can make changes to it or erase all your hard work.' );?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Protection for Unsaved Design Changes' ); ?></h3>
				<p><?php 
					/* Translators: Donuts are a deliciously irresistible fried treat. They can be somewhat like cake, where crumbs can get into places like your keyboard. Insert your favorite local pastry here. */ 
					_e( 'Those fresh donuts in the break room lured you away from your desk before you saved your new draft design. Fear not, when you return, WordPress 4.9 will politely ask whether or not you&#8217;d like to save your unsaved changes. We haven&#8217;t however, got a solution for donut crumbs in your keyboard. (Yet.)' ); 
					?></p>
			</div>
		</div>

		<hr />
		
		<h2><?php _e( 'Coding Enhancements for CSS Editing and the Custom HTML Widget' ); ?></h2>

		<div class="feature-section three-col">
			<div class="col">
				<h3><?php _e( 'Syntax Highlighting and Error Checking? Yes, Please!' ); ?></h3>
				<p><?php _e( 'You&#8217;ve got a display problem but can&#8217;t quite figure out exactly what went wrong in the CSS you lovingly wrote. With syntax highlighting and error checking for CSS editing and the Custom HTML widget introduced in WordPress 4.8.1, you&#8217;ll  pinpoint coding  errors quickly. Practically guaranteed to help you scan code more easily and suss out and fix code errors quickly.' ); ?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Sandbox for Safety' ); ?></h3>
				<p><?php _e( 'The dreaded white screen. You&#8217;ll avoid it when working on themes and plugin code because WordPress 4.9 will warn you about saving an error. You&#8217;ll sleep better at night.' ); ?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Warning: Potential Danger Ahead! ' ); ?></h3>
				<p><?php 
					/* Translators: Walking across the Grand Canyon on a tightrope is a very dangerous activity: https://www.usatoday.com/story/news/nation/2013/06/23/wallenda-tightrope-colorado-river-gorge/2450505/ 
						* If this concept doesn't translate well in your language, please insert a very dangerous, yet slightly humorous alternative. */
					_e( 'When you edit themes and plugins directly, WordPress 4.9 will politely warn you of that you&#8217;re walking the coding equivalent of a tightrope over the Grand Canyon, and let you know that you have a safety net available if you draft and test changes  before update your file. Take the safe route: You&#8217;ll thank you. Your team and customers will thank you.' )
					;?></p>
			</div>
		</div>

		<hr />

		<h2><?php _e( 'Oh Hey There, Gallery Widget!' ); ?></h2>

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Gallery? Gallerah, Rah!' ); ?></h3>
				<p><?php _e( 'An incremental improvement to the media changes hatched in WordPress 4.8, you can now add a gallery via widget. Yes!' ); ?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Press a Button, Add Media' ); ?></h3>
				<p><?php _e( 'Want to add media to your text widget? Embed images, video, and audio directly into the widget along with your text, with our decidedly unfancy, yet wholly utilitarian Add Media button. Woo!' ); ?></p>
			</div>
		</div>

		<hr />

		<h2><?php _e( 'Theme Improvements: Previews and Persistent Widgets and Menus' ); ?></h2>

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Oh Say, Can You Theme Switch?' ); ?></h3>
				<p><?php _e( 'When you switch themes, widgets sometimes think they can just up and move location. Improvements in WordPress 4.9 offer more persistent menu and widget placement when you decide it&#8217;s time for a new theme.  Additionally, you can preview installed themes or download, install, and preview new themes right. Nothing says handy like being able to preview before you deploy. ' ); ?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Better Menu Instructions Equal Less Confusion' ); ?></h3>
				<p><?php _e( 'Were you confused by the steps to create a new menu? Perhaps no longer! We&#8217;ve ironed out the UX for a smoother menu creation process.  Freshly pressed copy will guide you with grace and aplomb.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="changelog">
			<h2><?php
				printf(
					/* translators: %s: smiling face with smiling eyes emoji */
					__( 'Even More Developer Happiness %s' ),
					'&#x1F60A'
				);
			?></h2>

			<div class="under-the-hood three-col">
				<div class="col">
					<h3><a href="#"><?php _e( 'Heading' ); ?></a></h3>
					<p><?php _e( 'Paragraph' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="#"><?php _e( 'Heading' ); ?></a></h3>
					<p><?php _e( 'Paragraph' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="#"><?php _e( 'Heading' ); ?></a></h3>
					<p><?php _e( 'Paragraph' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="#"><?php _e( 'Heading' ); ?></a></h3>
					<p><?php _e( 'Paragraph' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="#"><?php _e( 'Heading' ); ?></a></h3>
					<p><?php _e( 'Paragraph' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="#"><?php _e( 'Heading' ); ?></a></h3>
					<p><?php _e( 'Paragraph' ); ?></p>
				</div>
			</div>
		</div>

		<hr />

		<div class="return-to-dashboard">
			<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
					<?php is_multisite() ? _e( 'Return to Updates' ) : _e( 'Return to Dashboard &rarr; Updates' ); ?>
				</a> |
			<?php endif; ?>
			<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home' ) : _e( 'Go to Dashboard' ); ?></a>
		</div>

	</div>
<?php

include( ABSPATH . 'wp-admin/admin-footer.php' );

// These are strings we may use to describe maintenance/security releases, where we aim for no new strings.
return;

__( 'Maintenance Release' );
__( 'Maintenance Releases' );

__( 'Security Release' );
__( 'Security Releases' );

__( 'Maintenance and Security Release' );
__( 'Maintenance and Security Releases' );

/* translators: %s: WordPress version number */
__( '<strong>Version %s</strong> addressed one security issue.' );
/* translators: %s: WordPress version number */
__( '<strong>Version %s</strong> addressed some security issues.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. */
_n_noop( '<strong>Version %1$s</strong> addressed %2$s bug.',
         '<strong>Version %1$s</strong> addressed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. Singular security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. More than one security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.' );

/* translators: %s: Codex URL */
__( 'For more information, see <a href="%s">the release notes</a>.' );
