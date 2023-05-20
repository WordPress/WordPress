<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

// Used in the HTML title tag.
/* translators: Page title of the About WordPress page in the admin. */
$title = _x( 'About', 'page title' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

require_once ABSPATH . 'wp-admin/admin-header.php';
?>
	<div class="wrap about__container">

		<div class="about__header">
			<div class="about__header-title">
				<h1>
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'WordPress %s' ),
						str_replace( '.', '<span>.</span>', $display_version )
					);
					?>
				</h1>
			</div>

			<div class="about__header-text"></div>
		</div>

		<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
			<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
		</nav>

		<div class="about__section changelog">
			<div class="column">
				<h2><?php _e( 'Maintenance and Security Releases' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. More than one security issue. */
						_n(
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.',
							1
						),
						'6.2.2',
						'1'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '6.2.2' )
						)
					);
					?>
				</p>

				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. More than one security issue. */
						_n(
							'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
							'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.',
							30
						),
						'6.2.1',
						'30'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '6.2.1' )
						)
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section">
			<div class="column">
				<h2>
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'Welcome to WordPress %s' ),
						$display_version
					);
					?>
				</h2>
				<p class="is-subheading">
					<?php
					printf(
						/* translators: 1: Count of enhancements, 2: Count of bug fixes. */
						__( 'WordPress 6.2 includes more than %1$s enhancements and %2$s bug fixes. This page highlights the latest features since the November 2022 release of WordPress 6.1. From quick highlights to developer resources, there&#8217;s a lot to explore.' ),
						292, // Enhancements.
						394 // Bug fixes.
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.2/about-site-editor.png" alt="" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Explore and edit your site from the Site Editor' ); ?></h3>
				<p><?php _e( 'An updated interface gives you more control over your site editing experience. Browse through full previews of your templates and template parts, then jump into editing your site from wherever you choose.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Manage your menu in more ways with the Navigation block' ); ?></h3>
				<p><?php _e( 'A new sidebar experience makes it easier to edit your site&#8217;s navigation. Add, remove, and reorder menu items faster—no matter how complex your menus are.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.2/about-navigation.png" alt="" />
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.2/about-block-inserter.png" alt="" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Discover a smoother experience for the Block Inserter' ); ?></h3>
				<p><?php _e( 'A refreshed design gives you more visibility and easier access to the content you need. Use the Media tab to drag and drop content from your existing Media Library quickly. Find patterns faster with a split view that lets you navigate categories and see previews all at once.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Find the controls you want when you need them' ); ?></h3>
				<p><?php _e( 'Your block settings sidebar is better organized with tabs for Settings and Styles. So the tools you need are easy to identify and access.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.2/about-split-controls-fixed.png" alt="" />
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.2/about-headers.png" alt="" />
				</div>
				<h3><?php _e( 'Build faster with headers and footers for block themes' ); ?></h3>
				<p><?php _e( 'Discover a new collection of header and footer patterns to choose from. Use them with any block theme as a quick, high-quality starting point for your site&#8217;s templates.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.2/about-openverse.png" alt="" />
				</div>
				<h3><?php _e( 'Explore Openverse media right from the Editor' ); ?></h3>
				<p><?php _e( 'Openverse&#8217;s library catalogs over 600 million free, openly licensed stock images and audio—and now it&#8217;s directly integrated into the Editor.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.2/about-distraction-free.png" alt="" />
				</div>
				<h3><?php _e( 'Focus on writing with Distraction Free mode' ); ?></h3>
				<p><?php _e( 'For those times you want to be alone with your ideas. You can now hide all your panels and controls, leaving you free to bring your content to life.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.2/about-out-of-beta.png" alt="" />
				</div>
				<h3><?php _e( 'Experience the Site Editor, now out of beta' ); ?></h3>
				<p><?php _e( 'Stable and ready for you to dive in and explore: 6.2 is your personal invitation to discover what the next generation of WordPress—and Block themes—can do.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.2/about-style-book.png" alt="" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Style Book' ); ?></h3>
				<p><?php _e( 'Use the new Style Book to get a complete overview of how every block in your site&#8217;s library looks. All in one place, all at a glance.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.2/about-copy-paste.png" alt="" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Copy and paste styles' ); ?></h3>
				<p><?php _e( 'Perfect the design on one type of block, then copy and paste those styles to other blocks to get just the look you want.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.2/about-custom-css.png" alt="" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Custom CSS' ); ?></h3>
				<p><?php _e( 'Power up your site any way you wish. Add CSS to your site, or your blocks, for another level of control over your site&#8217;s look and feel.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1D35B4"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M19.138 20.765a6.012 6.012 0 0 1 3.614-.62l4.15-5.617 6.57 6.57-5.616 4.15a6.01 6.01 0 0 1-.772 3.886c-.252.427-.561.828-.927 1.195l-3.713-3.713-5.692 5.693h-1.06v-1.061l5.692-5.692-3.713-3.713a6.007 6.007 0 0 1 1.467-1.078Zm7.936-3.944 4.105 4.105-4.933 3.647.124.884c.122.87-.01 1.766-.394 2.57l-6.002-6.003a4.515 4.515 0 0 1 2.57-.394l.883.124 3.647-4.933Z" fill="#fff"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:0;margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Sticky positioning' ); ?></h3>
				<p><?php _e( 'Choose to keep top-level group blocks fixed to the top of a page as visitors scroll.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1D35B4"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M18 15h2v2h8v-2h2v2a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H18a2 2 0 0 1-2-2V19a2 2 0 0 1 2-2v-2Zm12 3.5H18a.5.5 0 0 0-.5.5v1h13v-1a.5.5 0 0 0-.5-.5Zm.5 3h-13V31a.5.5 0 0 0 .5.5h12a.5.5 0 0 0 .5-.5v-9.5ZM23 23h2v2h-2v-2Zm-4 0v2h2v-2h-2Zm8 2v-2h2v2h-2Z" fill="#fff"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:0;margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Importing widgets' ); ?></h3>
				<p><?php _e( 'Options to import your favorite widgets from Classic themes to Block themes.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1D35B4"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M17.681 27.075h4.076l.977 2.774h1.72L20.564 19h-1.69L15 29.849h1.705l.976-2.774Zm2.046-5.766 1.503 4.262h-3.006l1.503-4.262Zm6.755 8.064c.332.366.864.549 1.595.549.498 0 .963-.1 1.395-.3.443-.21.825-.586 1.147-1.13.01.377.11.71.299.998.2.288.603.432 1.212.432.366 0 .665-.056.898-.166.243-.111.482-.26.714-.449l-.166-.282c-.11.088-.222.166-.332.232a.697.697 0 0 1-.366.1c-.177 0-.299-.061-.365-.183-.067-.122-.1-.316-.1-.581v-4.586c0-.543-.044-1.002-.133-1.379a1.828 1.828 0 0 0-.548-.963 1.974 1.974 0 0 0-.88-.499c-.344-.11-.754-.166-1.23-.166-.51 0-.975.06-1.396.183-.41.122-.747.271-1.013.448a2.84 2.84 0 0 0-.598.532c-.144.188-.216.432-.216.731 0 .288.083.543.249.764.166.21.42.316.764.316.31 0 .565-.089.764-.266.21-.177.316-.42.316-.73a1.04 1.04 0 0 0-.25-.715 1.108 1.108 0 0 0-.597-.4c.166-.21.393-.348.681-.414.288-.078.56-.117.814-.117.3 0 .554.05.764.15.222.1.393.271.515.515.133.232.2.548.2.947v1.13c0 .254-.117.465-.35.63-.22.167-.509.317-.863.45-.343.121-.714.254-1.113.398-.388.133-.759.3-1.113.499a2.583 2.583 0 0 0-.848.73c-.221.3-.332.687-.332 1.164 0 .576.16 1.052.482 1.428Zm3.356-.481c-.277.155-.56.232-.848.232-.354 0-.647-.116-.88-.349-.233-.232-.349-.598-.349-1.096 0-.51.116-.908.349-1.196.233-.288.51-.515.83-.682.333-.177.654-.337.964-.481.322-.144.56-.333.715-.565v3.306c-.244.388-.504.665-.781.83Z" fill="#fff"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:0;margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Local fonts in themes' ); ?></h3>
				<p><?php _e( 'Default WordPress themes offer better privacy with Google Fonts now included.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="about__section has-3-columns">
			<div class="column about__image is-vertically-aligned-top">
				<img src="data:image/svg+xml,%3Csvg width='280' height='280' viewBox='0 0 280 280' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg clip-path='url(%23a)'%3E%3Cpath fill='%23fff' d='M0 0h280v280H0z'/%3E%3Cg clip-path='url(%23b)'%3E%3Cpath fill='%231D35B4' d='M0 0h294v294H0z'/%3E%3Cpath d='M481.83 466.968c41.746-43.822 74.081-94.951 96.101-151.939 21.264-55.026 32.042-113.169 32.042-172.787 0-59.618-10.778-117.76-32.042-172.786-22.02-57.002-54.355-108.117-96.101-151.94-41.747-43.823-90.454-77.765-144.742-100.88C284.669-305.686 229.28-317 172.487-317c-56.794 0-112.183 11.314-164.601 33.636-54.302 23.115-102.995 57.057-144.742 100.88-41.747 43.823-74.081 94.952-96.101 151.94C-254.222 24.48-265 82.624-265 142.256c0 59.632 10.778 117.761 32.043 172.787 22.02 57.001 54.354 108.116 96.101 151.939a457.117 457.117 0 0 0 9.518 9.686l56.277-59.075a376.997 376.997 0 0 1-9.558-9.658c-67.612-70.974-104.838-165.327-104.838-265.693 0-100.365 37.226-194.718 104.838-265.692 67.611-70.974 157.495-110.051 253.106-110.051 95.61 0 185.494 39.077 253.106 110.051C493.204-52.476 530.43 41.877 530.43 142.242c0 100.366-37.226 194.719-104.837 265.693l-318.159 333.98H32.438l355.65-373.336c57.589-60.453 89.314-140.834 89.314-226.323 0-85.488-31.711-165.87-89.314-226.323-57.589-60.453-134.162-93.755-215.601-93.755-81.44 0-158.012 33.288-215.602 93.755-57.589 60.467-89.313 140.835-89.313 226.323 0 85.489 31.711 165.87 89.313 226.323 3.129 3.284 6.337 6.471 9.585 9.616l56.317-59.117a236.726 236.726 0 0 1-9.665-9.546c-87.868-92.238-87.868-242.327 0-334.58 87.869-92.237 230.847-92.237 318.729 0 42.569 44.686 66.008 104.096 66.008 167.29 0 63.195-23.439 122.604-66.008 167.29l-411.9 432.383h-74.996L294.36 270.176c67.201-70.542 67.201-185.311 0-255.854C261.814-19.842 218.529-38.67 172.5-38.67c-46.029 0-89.314 18.815-121.86 52.993C18.094 48.487.157 93.924.157 142.242s17.923 93.755 50.483 127.92a176.57 176.57 0 0 0 9.73 9.463l56.569-59.381a95.934 95.934 0 0 1-10.049-9.129c-17.526-18.398-27.177-42.863-27.177-68.887 0-26.023 9.651-50.489 27.177-68.886 17.526-18.398 40.832-28.529 65.623-28.529 24.791 0 48.097 10.131 65.623 28.529 36.179 37.978 36.179 99.781 0 137.773l-503.109 528.128V825.4H610v-83.499H219.947l261.909-274.933h-.026Z' fill='%23213FD4'/%3E%3C/g%3E%3Cpath d='M237 173.237h-81.39v-4.11l1.098-.242H237v4.352ZM237 190.647h-81.39V195H237v-4.353ZM237 179.767h-81.39v4.352H237v-4.352Z' fill='%23fff'/%3E%3Cpath d='M216.676 96.287c-6.635-6.577-15.456-10.2-24.839-10.2-9.382 0-18.204 3.623-24.839 10.2-6.634 6.577-10.289 15.321-10.289 24.621 0 9.3 3.655 18.044 10.289 24.621.26.256.522.508.789.757l3.106-3.079a31.284 31.284 0 0 1-.79-.755c-5.806-5.754-9.003-13.406-9.003-21.544s3.198-15.789 9.003-21.544c5.805-5.755 13.524-8.924 21.734-8.924 8.21 0 15.929 3.169 21.735 8.924 5.806 5.755 9.002 13.406 9.002 21.544s-3.196 15.789-9.002 21.544l-26.97 26.733 3.106 3.078 26.969-26.733c6.635-6.576 10.29-15.321 10.29-24.621 0-9.3-3.655-18.044-10.29-24.621l-.001-.001Z' fill='%23fff'/%3E%3Cpath d='M208.914 103.982c-4.561-4.522-10.626-7.012-17.077-7.012-6.45 0-12.515 2.49-17.076 7.012-4.561 4.521-7.074 10.533-7.074 16.927 0 6.393 2.511 12.405 7.074 16.926.259.257.524.506.794.751l3.109-3.081a20.109 20.109 0 0 1-.798-.748c-7.704-7.636-7.704-20.063 0-27.699 7.704-7.637 20.241-7.637 27.945 0 7.704 7.636 7.704 20.063 0 27.699l-35.072 34.765 3.105 3.078 35.072-34.765c4.561-4.521 7.074-10.533 7.074-16.926 0-6.394-2.512-12.406-7.074-16.927h-.002Z' fill='%23fff'/%3E%3Cpath d='M201.151 111.675a13.15 13.15 0 0 0-9.315-3.824 13.144 13.144 0 0 0-9.314 3.824c-5.137 5.092-5.137 13.376 0 18.467.26.258.53.504.808.738l3.122-3.095a8.661 8.661 0 0 1-.826-13.031 8.765 8.765 0 0 1 6.21-2.55c2.346 0 4.551.906 6.21 2.55a8.661 8.661 0 0 1 0 12.311l-42.436 42.064 3.777 2.412 41.764-41.398c5.136-5.091 5.136-13.375 0-18.467v-.001Z' fill='%23fff'/%3E%3Cpath d='M138.677 195c-4.233 0-7.677-3.417-7.677-7.616 0-4.2 3.444-7.616 7.677-7.616s7.677 3.416 7.677 7.616c0 4.199-3.444 7.616-7.677 7.616Z' fill='%2333F078'/%3E%3Cpath d='m95.363 126.168-3.735 3.702a19.68 19.68 0 0 1 11.449 5.579c7.704 7.636 7.704 20.063 0 27.699-7.704 7.637-20.24 7.637-27.945 0-3.732-3.699-5.787-8.618-5.787-13.849 0-5.232 2.055-10.15 5.787-13.85L126.028 85h-6.21l-47.79 47.371c-4.561 4.521-7.074 10.533-7.074 16.927 0 6.393 2.512 12.405 7.074 16.926 4.56 4.522 10.626 7.012 17.076 7.012 6.45 0 12.516-2.49 17.077-7.012 4.561-4.521 7.074-10.533 7.074-16.926 0-6.394-2.512-12.406-7.074-16.927a24.055 24.055 0 0 0-10.82-6.203h.002Z' fill='%23fff'/%3E%3Cpath d='M124.234 149.299c0-9.301-3.655-18.045-10.289-24.621a35.277 35.277 0 0 0-10.049-6.967l-3.337 3.308a30.885 30.885 0 0 1 10.281 6.737c5.806 5.755 9.003 13.406 9.003 21.544s-3.197 15.788-9.003 21.544c-5.805 5.755-13.524 8.923-21.734 8.923-8.21 0-15.929-3.168-21.735-8.923-5.806-5.756-9.002-13.406-9.002-21.544s3.196-15.789 9.002-21.544L110.504 85h-6.21l-40.027 39.676c-6.635 6.577-10.29 15.321-10.29 24.622s3.655 18.044 10.289 24.622c6.634 6.576 15.456 10.199 24.838 10.199 9.383 0 18.205-3.623 24.841-10.199 6.634-6.577 10.289-15.321 10.289-24.621Z' fill='%23fff'/%3E%3Cpath d='M131.836 132.102c-2.32-5.67-5.727-10.757-10.128-15.12a46.043 46.043 0 0 0-9.669-7.342l-3.23 3.201a41.577 41.577 0 0 1 9.793 7.22c3.983 3.947 7.066 8.549 9.164 13.677a40.899 40.899 0 0 1 3.054 15.561 40.886 40.886 0 0 1-3.054 15.56c-2.098 5.127-5.181 9.73-9.164 13.678a41.479 41.479 0 0 1-13.798 9.083 41.902 41.902 0 0 1-15.698 3.027c-5.419 0-10.701-1.018-15.698-3.027a41.492 41.492 0 0 1-13.799-9.083c-3.983-3.948-7.066-8.55-9.164-13.678a40.9 40.9 0 0 1-3.054-15.56 40.905 40.905 0 0 1 3.054-15.561c2.098-5.127 5.181-9.73 9.164-13.677L94.979 85h-6.21l-32.266 31.982c-4.4 4.363-7.808 9.45-10.127 15.12A45.215 45.215 0 0 0 43 149.299c0 5.934 1.136 11.72 3.376 17.196 2.32 5.671 5.727 10.758 10.128 15.12a45.85 45.85 0 0 0 15.253 10.039 46.302 46.302 0 0 0 17.349 3.347 46.322 46.322 0 0 0 17.349-3.346 45.854 45.854 0 0 0 15.253-10.039c4.401-4.362 7.808-9.449 10.128-15.12a45.203 45.203 0 0 0 3.376-17.196 45.222 45.222 0 0 0-3.375-17.197l-.001-.001Z' fill='%23fff'/%3E%3C/g%3E%3Cdefs%3E%3CclipPath id='a'%3E%3Cpath fill='%23fff' d='M0 0h280v280H0z'/%3E%3C/clipPath%3E%3CclipPath id='b'%3E%3Cpath fill='%23fff' d='M0 0h294v294H0z'/%3E%3C/clipPath%3E%3C/defs%3E%3C/svg%3E%0A" alt="" />
			</div>
			<div class="column is-vertically-aligned-center" style="grid-column-end:span 2">
				<h3>
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'Learn more about WordPress %s' ),
						$display_version
					);
					?>
				</h3>
				<p class="is-subheading">
					<?php
					printf(
						/* translators: 1: Learn WordPress link. */
						__( 'Explore <a href="%s">learn.wordpress.org</a> for tutorial videos, online workshops, courses, and lesson plans for Meetup organizers, including new features in WordPress.' ),
						'https://learn.wordpress.org/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1D35B4"/>
						<path d="M23 34v-4h-5l-2.293-2.293a1 1 0 0 1 0-1.414L18 24h5v-2h-7v-6h7v-2h2v2h5l2.293 2.293a1 1 0 0 1 0 1.414L30 22h-5v2h7v6h-7v4h-2Zm-5-14h11.175l.646-.646a.5.5 0 0 0 0-.708L29.175 18H18v2Zm.825 8H30v-2H18.825l-.646.646a.5.5 0 0 0 0 .708l.646.646Z" fill="#fff"/>
					</svg>
				</div>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress Field Guide link. */
						__( 'Check out the latest version of the <a href="%s">WordPress Field Guide</a>. It is overflowing with detailed developer notes to help you build with WordPress.' ),
						__( 'https://make.wordpress.org/core/2023/03/09/wordpress-6-2-field-guide/' )
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1D35B4"/>
						<path d="M28 19.75h-8v1.5h8v-1.5ZM20 23h8v1.5h-8V23ZM26 26.25h-6v1.5h6v-1.5Z" fill="#fff"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M29 16H19a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V18a2 2 0 0 0-2-2Zm-10 1.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H19a.5.5 0 0 1-.5-.5V18a.5.5 0 0 1 .5-.5Z" fill="#fff"/>
					</svg>
				</div>
				<p>
					<?php
					printf(
						/* translators: 1: WordPress Release Notes link, 2: WordPress version number. */
						__( '<a href="%1$s">Read the WordPress %2$s Release Notes</a> for more information on the included enhancements and issues fixed, installation information, developer notes and resources, release contributors, and the list of file changes in this release.' ),
						sprintf(
							/* translators: %s: WordPress version number. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							'6-2'
						),
						'6.2'
					);
					?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="return-to-dashboard">
			<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
					<?php is_multisite() ? _e( 'Go to Updates' ) : _e( 'Go to Dashboard &rarr; Updates' ); ?>
				</a> |
			<?php endif; ?>
			<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home' ) : _e( 'Go to Dashboard' ); ?></a>
		</div>
	</div>

<?php require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>

<?php

// These are strings we may use to describe maintenance/security releases, where we aim for no new strings.
return;

__( 'Maintenance Release' );
__( 'Maintenance Releases' );

__( 'Security Release' );
__( 'Security Releases' );

__( 'Maintenance and Security Release' );
__( 'Maintenance and Security Releases' );

/* translators: %s: WordPress version number. */
__( '<strong>Version %s</strong> addressed one security issue.' );
/* translators: %s: WordPress version number. */
__( '<strong>Version %s</strong> addressed some security issues.' );

/* translators: 1: WordPress version number, 2: Plural number of bugs. */
_n_noop(
	'<strong>Version %1$s</strong> addressed %2$s bug.',
	'<strong>Version %1$s</strong> addressed %2$s bugs.'
);

/* translators: 1: WordPress version number, 2: Plural number of bugs. Singular security issue. */
_n_noop(
	'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
	'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.'
);

/* translators: 1: WordPress version number, 2: Plural number of bugs. More than one security issue. */
_n_noop(
	'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
	'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.'
);

/* translators: %s: Documentation URL. */
__( 'For more information, see <a href="%s">the release notes</a>.' );

/* translators: 1: WordPress version number, 2: Link to update WordPress */
__( 'Important! Your version of WordPress (%1$s) is no longer supported, you will not receive any security updates for your website. To keep your site secure, please <a href="%2$s">update to the latest version of WordPress</a>.' );

/* translators: 1: WordPress version number, 2: Link to update WordPress */
__( 'Important! Your version of WordPress (%1$s) will stop receiving security updates in the near future. To keep your site secure, please <a href="%2$s">update to the latest version of WordPress</a>.' );

/* translators: %s: The major version of WordPress for this branch. */
__( 'This is the final release of WordPress %s' );

/* translators: The localized WordPress download URL. */
__( 'https://wordpress.org/download/' );
