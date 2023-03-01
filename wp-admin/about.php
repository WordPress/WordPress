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

			<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
				<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
				<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
				<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
				<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			</nav>
		</div>

		<div class="about__section">
			<div class="column">
				<h2 class="aligncenter">
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'Welcome to WordPress %s' ),
						$display_version
					);
					?>
				</h2>
				<p class="is-subheading aligncenter">
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
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='%231d35b4' /%3E%3C/svg%3E" alt="" />
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
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='%231d35b4' /%3E%3C/svg%3E" alt="" />
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='%231d35b4' /%3E%3C/svg%3E" alt="" />
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
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='%231d35b4' /%3E%3C/svg%3E" alt="" />
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='%231d35b4' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3><?php _e( 'Build faster with headers and footers for block themes' ); ?></h3>
				<p><?php _e( 'Discover a new collection of header and footer patterns to choose from. Use them with any block theme as a quick, high-quality starting point for your site&#8217;s templates.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='%231d35b4' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3><?php _e( 'Explore Openverse media right from the Editor' ); ?></h3>
				<p><?php _e( 'Openverse&#8217;s library catalogs over 600 million free, openly licensed stock images and audio—and now it&#8217;s directly integrated into the Editor.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='%231d35b4' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3><?php _e( 'Focus on writing with Distraction Free mode' ); ?></h3>
				<p><?php _e( 'For those times you want to be alone with your ideas. You can now hide all your panels and controls, leaving you free to bring your content to life.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='%231d35b4' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3><?php _e( 'Experience the Site Editor, now out of beta' ); ?></h3>
				<p><?php _e( 'Stable and ready for you to dive in and explore: 6.2 is your personal invitation to discover what the next generation of WordPress—and Block themes—can do.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='%231d35b4' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Style Book' ); ?></h3>
				<p><?php _e( 'Use the new Style Book to get a complete overview of how every block in your site&#8217;s library looks. All in one place, all at a glance.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='%231d35b4' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Copy and paste styles' ); ?></h3>
				<p><?php _e( 'Perfect the design on one type of block, then copy and paste those styles to other blocks to get just the look you want.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='100%25' height='100%25' fill='%231d35b4' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Custom CSS' ); ?></h3>
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
				<h3 class="is-smaller-heading"><?php _e( 'Sticky positioning' ); ?></h3>
				<p><?php _e( 'Choose to keep certain blocks fixed to the top of a page as visitors scroll.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1D35B4"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M18 15h2v2h8v-2h2v2a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H18a2 2 0 0 1-2-2V19a2 2 0 0 1 2-2v-2Zm12 3.5H18a.5.5 0 0 0-.5.5v1h13v-1a.5.5 0 0 0-.5-.5Zm.5 3h-13V31a.5.5 0 0 0 .5.5h12a.5.5 0 0 0 .5-.5v-9.5ZM23 23h2v2h-2v-2Zm-4 0v2h2v-2h-2Zm8 2v-2h2v2h-2Z" fill="#fff"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Importing widgets' ); ?></h3>
				<p><?php _e( 'Options to import your favorite widgets from Classic themes to Block themes.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1D35B4"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M17.681 27.075h4.076l.977 2.774h1.72L20.564 19h-1.69L15 29.849h1.705l.976-2.774Zm2.046-5.766 1.503 4.262h-3.006l1.503-4.262Zm6.755 8.064c.332.366.864.549 1.595.549.498 0 .963-.1 1.395-.3.443-.21.825-.586 1.147-1.13.01.377.11.71.299.998.2.288.603.432 1.212.432.366 0 .665-.056.898-.166.243-.111.482-.26.714-.449l-.166-.282c-.11.088-.222.166-.332.232a.697.697 0 0 1-.366.1c-.177 0-.299-.061-.365-.183-.067-.122-.1-.316-.1-.581v-4.586c0-.543-.044-1.002-.133-1.379a1.828 1.828 0 0 0-.548-.963 1.974 1.974 0 0 0-.88-.499c-.344-.11-.754-.166-1.23-.166-.51 0-.975.06-1.396.183-.41.122-.747.271-1.013.448a2.84 2.84 0 0 0-.598.532c-.144.188-.216.432-.216.731 0 .288.083.543.249.764.166.21.42.316.764.316.31 0 .565-.089.764-.266.21-.177.316-.42.316-.73a1.04 1.04 0 0 0-.25-.715 1.108 1.108 0 0 0-.597-.4c.166-.21.393-.348.681-.414.288-.078.56-.117.814-.117.3 0 .554.05.764.15.222.1.393.271.515.515.133.232.2.548.2.947v1.13c0 .254-.117.465-.35.63-.22.167-.509.317-.863.45-.343.121-.714.254-1.113.398-.388.133-.759.3-1.113.499a2.583 2.583 0 0 0-.848.73c-.221.3-.332.687-.332 1.164 0 .576.16 1.052.482 1.428Zm3.356-.481c-.277.155-.56.232-.848.232-.354 0-.647-.116-.88-.349-.233-.232-.349-.598-.349-1.096 0-.51.116-.908.349-1.196.233-.288.51-.515.83-.682.333-.177.654-.337.964-.481.322-.144.56-.333.715-.565v3.306c-.244.388-.504.665-.781.83Z" fill="#fff"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Local fonts in themes' ); ?></h3>
				<p><?php _e( 'Default WordPress themes offer better privacy with Google Fonts included.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="about__section has-2-columns is-wider-right">
			<div class="column about__image is-vertically-aligned-top">
				<a href="https://youtu.be/1w9oywSa6Hw">
					<img src="data:image/svg+xml,%3Csvg width='294' height='280' viewBox='0 0 294 280' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg clip-path='url(%23a)'%3E%3Cg clip-path='url(%23b)'%3E%3Cpath fill='%231D35B4' d='M0 0h280v280H0z'/%3E%3Cpath d='m178.94 801.802-59.946-94.723c45.365-71.684 70.344-166.987 70.344-268.362 0-101.375-24.979-196.678-70.344-268.362C73.629 98.671 13.316 59.202-50.84 59.202s-124.468 39.47-169.834 111.153c-45.365 71.684-70.344 166.987-70.344 268.362 0 101.375 24.979 196.678 70.344 268.362l-59.945 94.723c-61.373-96.978-95.181-225.924-95.181-363.063 0-137.139 33.794-266.085 95.181-363.062 61.372-96.977 142.976-150.4 229.765-150.4 86.789 0 168.393 53.4 229.765 150.4 61.373 96.977 95.181 225.923 95.181 363.062s-33.794 266.085-95.181 363.063h.029Z' fill='%23213FD4'/%3E%3Cpath d='m79.037 643.943-59.945-94.722c38.556-60.924 38.556-160.066 0-221.012-38.555-60.923-101.299-60.923-139.868 0-38.556 60.923-38.556 160.066 0 220.99l-59.946 94.722c-71.615-113.162-71.615-297.272 0-410.435 71.616-113.162 188.13-113.162 259.746 0 71.615 113.163 71.615 297.273 0 410.435l.013.022Z' fill='%23213FD4'/%3E%3Cpath d='m278.852 959.676-59.946-94.723c72.054-113.854 111.726-265.214 111.726-426.218 0-161.004-39.672-312.363-111.726-426.218C146.853-101.337 51.065-164.024-50.827-164.024s-197.681 62.687-269.734 176.541c-72.053 113.855-111.725 265.214-111.725 426.218 0 161.004 39.672 312.364 111.725 426.218l-59.946 94.723c-44.489-70.3-78.947-152.32-102.414-243.738-22.662-88.271-34.148-181.542-34.148-277.18s11.486-188.91 34.148-277.181c23.467-91.44 57.925-173.438 102.414-243.738 44.49-70.299 96.396-124.749 154.251-161.83 55.862-35.808 114.89-53.958 175.415-53.958 60.524 0 119.552 18.15 175.414 53.958 57.869 37.081 109.761 91.531 154.251 161.83 44.489 70.3 78.948 152.32 102.414 243.738 22.662 88.271 34.148 181.543 34.148 277.181 0 95.638-11.486 188.909-34.148 277.18-23.466 91.441-57.925 173.438-102.414 243.738h.028Z' fill='%23213FD4'/%3E%3Cpath d='m218.905 864.928-357.086 564.242 59.94 94.72 357.086-564.247-59.94-94.715ZM119.004 707.087-330.27 1417l59.94 94.72 449.274-709.918-59.94-94.715ZM19.094 549.203l-536.247 847.347 59.94 94.72L79.034 643.917l-59.94-94.714ZM2335.61 1839.71H-517.886v136.44H2335.61v-136.44ZM2335.61 1617.98H-517.886v136.45H2335.61v-136.45Z' fill='%23213FD4'/%3E%3Cpath d='M2335.61 1396.25H-517.886v130.76H2335.61v-130.76Z' fill='%23213FD4'/%3E%3C/g%3E%3Cpath d='m75.895 129.264-5.608 5.45a17.076 17.076 0 0 1 9.5 4.666 16.24 16.24 0 0 1 0 23.402c-3.216 3.126-7.492 4.847-12.04 4.847-4.548 0-8.824-1.721-12.04-4.847a16.239 16.239 0 0 1 0-23.402l-4.838-4.702c-9.306 9.044-9.306 23.76 0 32.805 4.509 4.382 10.503 6.794 16.878 6.794s12.369-2.412 16.877-6.794c9.307-9.045 9.307-23.761 0-32.805a23.893 23.893 0 0 0-8.73-5.414Z' fill='%2333F078'/%3E%3Cpath d='m84.501 120.9-5.106 4.962a28.488 28.488 0 0 1 8.455 5.682c5.37 5.218 8.327 12.157 8.327 19.537 0 7.381-2.958 14.319-8.327 19.538-5.37 5.218-12.51 8.093-20.103 8.093-7.594 0-14.733-2.875-20.103-8.093-5.37-5.218-8.327-12.157-8.327-19.538 0-7.38 2.957-14.318 8.327-19.537l-4.838-4.702c-6.662 6.475-10.33 15.083-10.33 24.238 0 9.156 3.669 17.765 10.33 24.239 6.66 6.473 15.52 10.039 24.94 10.039 9.421 0 18.278-3.566 24.94-10.039 6.662-6.475 10.33-15.083 10.33-24.239 0-9.155-3.67-17.764-10.33-24.238A35.356 35.356 0 0 0 84.5 120.9h.001Z' fill='%2333F078'/%3E%3Cpath d='M111.002 134.014c-2.349-5.629-5.798-10.678-10.253-15.007a46.57 46.57 0 0 0-7.927-6.195l-4.973 4.834a39.92 39.92 0 0 1 8.062 6.062c7.523 7.312 11.667 17.034 11.667 27.374s-4.144 20.061-11.667 27.373c-7.523 7.311-17.526 11.338-28.165 11.338-10.64 0-20.643-4.027-28.166-11.338-7.523-7.312-11.667-17.033-11.667-27.373s4.144-20.062 11.667-27.374l-4.838-4.701c-4.455 4.328-7.904 9.378-10.253 15.006a44.158 44.158 0 0 0-3.415 17.068c0 5.889 1.15 11.632 3.419 17.067 2.349 5.629 5.798 10.678 10.253 15.008a46.51 46.51 0 0 0 15.44 9.964 47.695 47.695 0 0 0 17.562 3.322c6.06 0 11.969-1.117 17.561-3.321a46.498 46.498 0 0 0 15.441-9.964c4.454-4.329 7.904-9.379 10.253-15.008a44.094 44.094 0 0 0 3.417-17.067c0-5.89-1.149-11.632-3.417-17.068h-.001Z' fill='%2333F078'/%3E%3Cpath d='m103.47 83.559-52.6 51.119 4.838 4.702 57.437-55.821h-9.675ZM87.344 83.559l-44.538 43.283 4.838 4.702L97.02 83.559h-9.675ZM71.219 83.559l-36.475 35.448 4.838 4.702 41.312-40.15H71.22ZM167.807 138.655l-4.765-4.661c3.606-3.527 5.592-8.216 5.592-13.205 0-4.988-1.986-9.678-5.592-13.205-3.607-3.527-8.401-5.47-13.501-5.47s-9.895 1.943-13.501 5.47c-3.607 3.527-5.592 8.217-5.592 13.205 0 4.989 1.985 9.678 5.592 13.205l-4.766 4.661c-4.879-4.771-7.566-11.117-7.566-17.865 0-6.748 2.686-13.093 7.566-17.865 4.879-4.772 11.366-7.4 18.266-7.4 6.899 0 13.386 2.627 18.265 7.4 4.879 4.772 7.566 11.117 7.566 17.865 0 6.748-2.686 13.094-7.566 17.865h.002Z' fill='%2333F078'/%3E%3Cpath d='m159.865 130.888-4.765-4.661a7.582 7.582 0 0 0 0-10.876c-3.065-2.997-8.053-2.997-11.119 0a7.58 7.58 0 0 0 0 10.875l-4.765 4.661a14.077 14.077 0 0 1 0-20.197c5.693-5.568 14.955-5.568 20.648 0a14.077 14.077 0 0 1 0 20.197l.001.001Z' fill='%2333F078'/%3E%3Cpath d='m175.75 146.424-4.766-4.661c5.728-5.602 8.882-13.05 8.882-20.973 0-7.922-3.154-15.37-8.882-20.973-5.728-5.602-13.342-8.687-21.442-8.687s-15.715 3.085-21.443 8.687c-5.728 5.603-8.882 13.051-8.882 20.973 0 7.923 3.154 15.371 8.882 20.973l-4.765 4.661c-3.537-3.459-6.276-7.495-8.142-11.994a35.432 35.432 0 0 1-2.714-13.639c0-4.706.913-9.295 2.714-13.639 1.866-4.5 4.605-8.534 8.142-11.994a36.91 36.91 0 0 1 12.262-7.963 37.647 37.647 0 0 1 13.945-2.655c4.811 0 9.503.893 13.944 2.655a36.89 36.89 0 0 1 12.262 7.963c3.537 3.46 6.276 7.496 8.142 11.994a35.436 35.436 0 0 1 2.715 13.639c0 4.706-.914 9.296-2.715 13.639-1.866 4.5-4.605 8.535-8.142 11.994h.003Z' fill='%2333F078'/%3E%3Cpath d='m170.984 141.762-28.387 27.765 4.765 4.66 28.387-27.765-4.765-4.66ZM163.042 133.995l-35.715 34.933 4.765 4.66 35.715-34.933-4.765-4.66ZM155.1 126.226l-42.629 41.695 4.765 4.661 42.629-41.696-4.765-4.66ZM339.253 189.728h-226.84v6.714h226.84v-6.714ZM339.253 178.817h-226.84v6.714h226.84v-6.714Z' fill='%2333F078'/%3E%3Cpath d='M339.253 167.907h-226.84v6.434h226.84v-6.434Z' fill='%2333F078'/%3E%3C/g%3E%3Cdefs%3E%3CclipPath id='a'%3E%3Cpath fill='%23fff' d='M0 0h294v280H0z'/%3E%3C/clipPath%3E%3CclipPath id='b'%3E%3Cpath fill='%23fff' d='M0 0h280v280H0z'/%3E%3C/clipPath%3E%3C/defs%3E%3C/svg%3E%0A" alt="<?php echo esc_attr( __( 'Exploring WordPress 6.2 video' ) ); ?>" />
				</a>
			</div>
			<div class="column is-vertically-aligned-center">
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
						'#'
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
