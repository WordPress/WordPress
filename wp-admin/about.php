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
						$display_version
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
				<p class="is-subheading">
					<?php _e( 'WordPress 6.1 includes more than 2,000 updates. This page highlights some of the most significant changes to the product since the May 2022 release of WordPress 6.0. You will also find resources for developers and anyone seeking a deeper understanding of WordPress.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column about__image is-edge-to-edge" style="background-color:#353535;">
				<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50%25' cy='50%25' r='30%25' fill='%23E26F56' /%3E%3C/svg%3E" alt="" />
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'A new default theme powered by 10 distinct style variations' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: 1: Variation announcement post URL, 2: Accessibility-ready handbook page. */
						__( 'Building on the foundational elements in the 5.9 and 6.0 releases for block themes and style variations, the new default theme, Twenty Twenty-Three, includes <a href="%1$s">10 different styles</a> and is &#147;<a href="%2$s">Accessibility Ready</a>&#148;.' ),
						'https://make.wordpress.org/design/2022/09/07/tt3-default-theme-announcing-style-variation-selections/',
						'https://make.wordpress.org/themes/handbook/review/accessibility/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'A better creator experience with refined and additional templates' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: 1: Link to template options dev note, 2: Link to template creation dev note. */
						__( '<a href="%1$s">New templates</a> include a custom template for posts and pages in the Site Editor. Search-and-replace tools speed up the design of <a href="%2$s">template parts</a>.' ),
						'https://make.wordpress.org/core/2022/07/21/core-editor-improvement-deeper-customization-with-more-template-options/',
						'https://make.wordpress.org/core/2022/08/25/core-editor-improvement-refining-the-template-creation-experience/'
					);
					?>
				</p>
			</div>
			<div class="column about__image is-edge-to-edge has-subtle-background-color">
				<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50%25' cy='50%25' r='30%25' fill='%23E26F56' /%3E%3C/svg%3E" alt="" />
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column about__image is-edge-to-edge has-subtle-background-color">
				<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50%25' cy='50%25' r='30%25' fill='%23E26F56' /%3E%3C/svg%3E" alt="" />
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'More consistency and control across design tools' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to layout support refactor dev note. */
						__( 'Upgrades to the <a href="%s">controls for design elements and blocks</a> make the layout and site-building process more consistent, complete, and intuitive.' ),
						'https://make.wordpress.org/core/2022/10/10/updated-editor-layout-support-in-6-1-after-refactor/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Menus just got easier to create and manage' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to navigation block fallback dev note. */
						__( '<a href="%s">New fallback options</a> in the navigation block mean you can edit the menu that’s open; no searching needed. Plus, the controls for choosing and working on menus have their own place in the block settings. The mobile menu system also gets an upgrade with new features, including different icon options, to make the menu yours.' ),
						'https://make.wordpress.org/core/2022/09/27/navigation-block-fallback-behavior-in-wp-6-1-dev-note/'
					);
					?>
				</p>
			</div>
			<div class="column about__image is-edge-to-edge has-subtle-background-color">
				<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50%25' cy='50%25' r='30%25' fill='%23E26F56' /%3E%3C/svg%3E" alt="" />
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image has-accent-background-color">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50%25' cy='50%25' r='30%25' fill='%23E26F56' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3><?php _e( 'Improved layout and visualization of document settings' ); ?></h3>
				<p><?php _e( 'A cleaner, better-organized display helps you easily view and manage important post and page settings, especially the template picker and scheduler.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image has-accent-background-color">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50%25' cy='50%25' r='30%25' fill='%23E26F56' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3><?php _e( 'One-click lock settings for all inner blocks' ); ?></h3>
				<p><?php _e( 'When locking blocks, a new toggle lets you apply your lock settings to all the blocks in a containing block like the group, cover, and column blocks.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="column about__image is-edge-to-edge has-accent-background-color">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50%25' cy='50%25' r='30%25' fill='%23E26F56' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Improved block placeholders' ); ?></h3>
				<p><?php _e( 'Various blocks have improved placeholders that reflect customization options to help you design your site and its content. For example, the Image block placeholder displays custom borders and duotone filters even before selecting an image.' ); ?></p>
			</div>
			<div class="column">
				<div class="column about__image is-edge-to-edge has-accent-background-color">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50%25' cy='50%25' r='30%25' fill='%23E26F56' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Compose richer lists and quotes with inner blocks' ); ?></h3>
				<p><?php _e( 'The List and Quote blocks now support inner blocks, allowing for more flexible and rich compositions like adding headings inside your Quote blocks.' ); ?></p>
			</div>
			<div class="column">
				<div class="column about__image is-edge-to-edge has-accent-background-color">
					<img src="data:image/svg+xml,%3Csvg width='436' height='436' viewbox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50%25' cy='50%25' r='30%25' fill='%23E26F56' /%3E%3C/svg%3E" alt="" />
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Header and footer patterns for every theme' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: 1: Link to tutorial for customizing a header, 2: Link to tutorial for customizing a footer. */
						__( 'Explore these block patterns, making <a href="%1$s">header</a> and <a href="%2$s">footer</a> creation more efficient.' ),
						'https://learn.wordpress.org/tutorial/customizing-a-header-with-patterns/',
						'https://learn.wordpress.org/tutorial/customizing-a-footer-with-patterns/'
					);
					?>
			</div>
		</div>

		<hr />

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M30.5 17.5V20H32V17.5H34.5V16H32V13.5H30.5V16H28V17.5H30.5ZM24 16H18C16.8954 16 16 16.8954 16 18V30C16 31.1046 16.8954 32 18 32H30C31.1046 32 32 31.1046 32 30V24H30.5V30C30.5 30.2761 30.2761 30.5 30 30.5H18C17.7239 30.5 17.5 30.2761 17.5 30V18C17.5 17.7239 17.7239 17.5 18 17.5H24V16Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Add starter patterns to any post type' ); ?></h3>
				<p><?php _e( 'In WordPress 6.0, when you created a new page, you would see suggested patterns so you did not have to start with a blank page. In 6.1, you will also see the starter patterns modal when you create a new instance of any post type.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M31.25 23.375C31.25 26.3435 28.8435 28.75 25.875 28.75C22.9065 28.75 20.5 26.3435 20.5 23.375C20.5 20.4065 22.9065 18 25.875 18C28.8435 18 31.25 20.4065 31.25 23.375ZM32.75 23.375C32.75 27.172 29.6719 30.25 25.875 30.25C24.0609 30.25 22.411 29.5474 21.1824 28.3995L16.9939 32.0644L16.0061 30.9356L20.2039 27.2625C19.4444 26.1568 19 24.8178 19 23.375C19 19.578 22.078 16.5 25.875 16.5C29.6719 16.5 32.75 19.578 32.75 23.375Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Find block themes faster' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to "full site editing" themes on WordPress.org. */
						__( 'The Themes Directory has <a href="%s">a filter for block themes</a>, and a pattern preview gives a better sense of what the theme might look like while exploring different themes and patterns.' ),
						__( 'https://wordpress.org/themes/tags/full-site-editing/' )
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M22.4373 15.0445C22.5378 14.4418 23.0593 14 23.6703 14H25.3025C25.9136 14 26.4351 14.4418 26.5355 15.0445L26.8409 16.877C27.8252 17.2022 28.7192 17.7257 29.4757 18.4002L31.217 17.7478C31.7892 17.5335 32.4325 17.7642 32.738 18.2934L33.5541 19.7069C33.8597 20.2361 33.7378 20.9086 33.2661 21.297L31.8318 22.4777C31.9332 22.9693 31.9864 23.4784 31.9864 24C31.9864 24.5214 31.9332 25.0304 31.8319 25.5218L33.2672 26.7033C33.739 27.0917 33.8608 27.7642 33.5553 28.2934L32.7392 29.7069C32.4337 30.2361 31.7904 30.4668 31.2182 30.2525L29.4758 29.5997C28.7193 30.2743 27.8252 30.7978 26.8409 31.123L26.5355 32.9555C26.4351 33.5582 25.9136 34 25.3025 34H23.6703C23.0593 34 22.5378 33.5582 22.4373 32.9555L22.1319 31.123C21.1476 30.7978 20.2535 30.2743 19.497 29.5997L17.7547 30.2525C17.1825 30.4668 16.5392 30.2361 16.2336 29.7069L15.4175 28.2934C15.112 27.7642 15.2339 27.0917 15.7056 26.7033L17.1409 25.5218C17.0396 25.0304 16.9864 24.5214 16.9864 24C16.9864 23.4784 17.0397 22.9693 17.141 22.4777L15.7068 21.297C15.235 20.9086 15.1132 20.2361 15.4187 19.7069L16.2348 18.2934C16.5403 17.7642 17.1837 17.5335 17.7559 17.7479L19.4971 18.4002C20.2536 17.7257 21.1476 17.2022 22.1319 16.877L22.4373 15.0445ZM28.2364 24C28.2364 26.0711 26.5575 27.75 24.4864 27.75C22.4154 27.75 20.7364 26.0711 20.7364 24C20.7364 21.9289 22.4154 20.25 24.4864 20.25C26.5575 20.25 28.2364 21.9289 28.2364 24Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Keep your Site Editor settings for later' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to block editor preferences dev note. */
						__( 'Site Editor settings are now <a href="%s">persistent for each user</a>. This means your settings will now be consistent across browsers and devices.' ),
						'https://make.wordpress.org/core/2022/10/10/changes-to-block-editor-preferences-in-wordpress-6-1/'
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M32.75 24C32.75 28.8325 28.8325 32.75 24 32.75V24V15.25C28.8325 15.25 32.75 19.1675 32.75 24ZM24 14C29.5228 14 34 18.4772 34 24C34 29.5228 29.5228 34 24 34C18.4772 34 14 29.5228 14 24C14 18.4772 18.4772 14 24 14Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'A streamlined style system' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link style engine dev note. */
						__( 'The CSS rules for margin, padding, typography, colors, and borders within the <a href="%s">styles engine</a> are now all in one place, reducing time spent on layout-specific tasks and helps to generate semantic class names.' ),
						'https://make.wordpress.org/core/2022/10/10/block-styles-generation-style-engine/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<path d="M23.8 15.38C23.2 15.38 22.685 15.165 22.255 14.735C21.825 14.305 21.61 13.79 21.61 13.19C21.61 12.59 21.825 12.075 22.255 11.645C22.685 11.215 23.2 11 23.8 11C24.4 11 24.915 11.215 25.345 11.645C25.775 12.075 25.99 12.59 25.99 13.19C25.99 13.79 25.775 14.305 25.345 14.735C24.915 15.165 24.4 15.38 23.8 15.38ZM20.59 35V19.01C19.23 18.91 17.915 18.77 16.645 18.59C15.375 18.41 14.16 18.18 13 17.9L13.45 16.1C15.15 16.5 16.84 16.785 18.52 16.955C20.2 17.125 21.96 17.21 23.8 17.21C25.64 17.21 27.4 17.125 29.08 16.955C30.76 16.785 32.45 16.5 34.15 16.1L34.6 17.9C33.44 18.18 32.225 18.41 30.955 18.59C29.685 18.77 28.37 18.91 27.01 19.01V35H25.21V27.2H22.39V35H20.59Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Improved admin and editor accessibility' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to WordPress.org accessibility statement. */
						__( 'More than 40 improvements in accessibility include resolving focus loss problems in the editor, improving form labels and audible messages, making alternative text easier to edit, and fixing the sub-menu overlap in the expanded admin side navigation at smaller screen sizes and higher zoom levels. Learn more about <a href="%s">accessibility in WordPress</a>.' ),
						'https://wordpress.org/about/accessibility/'
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M23.2639 25.5896L18.7107 30.2783L17.6346 29.2333L22.1471 24.5865L16.0056 24.5598L16.0121 23.0598L22.2329 23.0869L17.9693 18.6964L19.0454 17.6514L23.2449 21.9759L23.2136 16.0062L24.7136 15.9983L24.7446 21.9117L28.8818 17.6514L29.9579 18.6964L25.6798 23.1019L32.1563 23.13L32.1498 24.63L25.7955 24.6024L30.2926 29.2333L29.2165 30.2783L24.7644 25.6937L24.7983 32.1497L23.2983 32.1576L23.2639 25.5896Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Other notes of interest' ); ?></h3>
				<p><?php _e( '6.1 includes a new time-to-read feature showing content authors the approximate time-to-read values for pages, posts, and custom post types.' ); ?></p>
				<p>
					<?php
					printf(
						/* translators: %s: "General Settings" admin page title, linked to the page if the user can edit options. */
						__( 'The site tagline is empty by default in new sites but can be modified in %s.' ),
						current_user_can( 'manage_options' ) ? '<a href="' . esc_url( admin_url( 'options-general.php' ) ) . '">' . __( 'General Settings' ) . '</a>' : __( 'General Settings' )
					);
					?>
				</p>
				<p><?php _e( 'A new modal design offers a background blur effect, making it easier to focus on the task at hand.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<rect x="9" y="15" width="30" height="18" rx="9" fill="white"/>
						<circle cx="18" cy="24" r="5" fill="#1E1E1E"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Updated interface options and features' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to styling elements dev note. */
						__( 'Updates include <a href="%s">styling elements</a> like buttons, citations, and links globally; controlling hover, active, and focus states for links using theme.json (not available to control in the interface yet); and customizing outline support for blocks and elements, among other features.' ),
						'https://make.wordpress.org/core/2022/10/10/styling-elements-in-block-themes/'
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M16.5 14H31.5C32.8807 14 34 15.1193 34 16.5V31.5C34 32.8807 32.8807 34 31.5 34H16.5C15.1193 34 14 32.8807 14 31.5V16.5C14 15.1193 15.1193 14 16.5 14ZM31.5 15.875H16.5C16.1548 15.875 15.875 16.1548 15.875 16.5V20.25H32.125V16.5C32.125 16.1548 31.8452 15.875 31.5 15.875ZM32.125 22.125H21.5L21.5 32.125H31.5C31.8452 32.125 32.125 31.8452 32.125 31.5V22.125ZM19.625 22.125H15.875V31.5C15.875 31.8452 16.1548 32.125 16.5 32.125H19.625L19.625 22.125Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Continued evolution of layout options' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to layout support refactor dev note. */
						__( 'The default content dimensions provided by themes can now be overridden in the Styles Sidebar, giving site builders better control over full-width content. Developers have <a href="%s">fine-grained control over these controls</a>.' ),
						'https://make.wordpress.org/core/2022/10/10/updated-editor-layout-support-in-6-1-after-refactor/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<circle cx="50%" cy="50%" r="30%" fill="red"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Block template parts in classic themes' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: 1: Link to block-based template parts in classic themes dev note, 2: Folder name. */
						__( '<a href="%1$s">Block template parts can now be defined in classic themes</a> by adding the appropriate HTML files to the %2$s directory at the root of the theme.' ),
						'https://make.wordpress.org/core/2022/10/04/block-based-template-parts-in-traditional-themes/',
						'<code>parts</code>'
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M14.0628 24.4951C14.0628 22.2125 15.9132 20.3621 18.1958 20.3621C20.1204 20.3621 21.7397 21.6785 22.1986 23.4612L22.2137 23.4573L23.0646 25.9676C23.7156 28.4398 25.9654 30.2638 28.6433 30.2638C31.8292 30.2638 34.4118 27.6812 34.4118 24.4953C34.4118 21.3094 31.8292 18.7268 28.6433 18.7268C27.9035 18.7268 27.1943 18.8665 26.5421 19.1217L27.1381 20.6448C27.6034 20.4627 28.1107 20.3623 28.6433 20.3623C30.9259 20.3623 32.7763 22.2127 32.7763 24.4953C32.7763 26.7779 30.9259 28.6283 28.6433 28.6283C26.7186 28.6283 25.0994 27.3119 24.6405 25.5292L24.6329 25.4995L24.623 25.4705L23.714 22.7888L23.7081 22.7908C22.9812 20.4373 20.7892 18.7266 18.1958 18.7266C15.0099 18.7266 12.4272 21.3092 12.4272 24.4951C12.4272 27.6809 15.0099 30.2636 18.1958 30.2636C18.9374 30.2636 19.6482 30.1232 20.3017 29.8669L19.7044 28.3443C19.2381 28.5272 18.7297 28.628 18.1958 28.628C15.9132 28.628 14.0628 26.7777 14.0628 24.4951Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Expanded support for Query Loop blocks' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to query loop dev note. */
						__( '<a href="%s">New filters</a> let Query Block variations support custom queries for more powerful variations and advanced hierarchical post types filtering options.' ),
						'https://make.wordpress.org/core/2022/10/10/extending-the-query-loop-block/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<circle cx="50%" cy="50%" r="30%" fill="red"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Filters for all your styles' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to theme.json filters dev note. */
						__( '<a href="%s">Leverage filters</a> in the Styles sidebar to control settings at all four levels of your site—core, theme, user, or block, from less to more specific.' ),
						'https://make.wordpress.org/core/2022/10/10/filters-for-theme-json-data/'
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<path d="M17 36.5996H32.333V34.533H17V36.5996Z" fill="white"/>
						<path d="M17 13.0668L32.333 13.0668V11.0002L17 11.0002V13.0668Z" fill="white"/>
						<path d="M37.4662 16.1334H35.3996V31.4664H37.4662V16.1334Z" fill="white"/>
						<path d="M14.0666 31.4663H12L12 16.1333H14.0666L14.0666 31.4663Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Spacing presets for faster, consistent design' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to spacing presets dev note. */
						__( 'Save time and help avoid hard-coding a values into a theme with <a href="%s">preset margin and padding values for multiple blocks</a>.' ),
						'https://make.wordpress.org/core/2022/10/07/introduction-of-presets-across-padding-margin-and-block-gap/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<path d="M21.4667 28.8357C21.9575 29.3156 22.667 29.5399 23.5952 29.5086C24.5235 29.4773 25.201 29.1382 25.6278 28.4914L32.5415 17.8811L21.8188 24.7355C21.1786 25.1528 20.8372 25.8206 20.7945 26.7387C20.7518 27.6568 20.9759 28.3558 21.4667 28.8357V28.8357ZM23.7393 14C24.9556 14 26.2252 14.193 27.5483 14.579C28.8713 14.965 30.1409 15.6275 31.3572 16.5665L29.6928 17.7246C28.7326 17.0986 27.703 16.6343 26.604 16.3318C25.5051 16.0292 24.5502 15.8779 23.7393 15.8779C20.7518 15.8779 18.2019 16.9212 16.0893 19.0078C13.9768 21.0944 12.9205 23.6296 12.9205 26.6135C12.9205 27.5524 13.0539 28.5018 13.3206 29.4617C13.5873 30.4215 13.9661 31.3083 14.4569 32.1221H32.9897C33.4591 31.3709 33.8325 30.4945 34.1099 29.493C34.3873 28.4914 34.526 27.5107 34.526 26.5509C34.526 25.6745 34.3927 24.7303 34.1259 23.7183C33.8592 22.7063 33.3844 21.7726 32.7016 20.9171L33.9499 19.2895C34.7608 20.458 35.3689 21.6317 35.7744 22.8106C36.1798 23.9896 36.4039 25.1737 36.4465 26.3631C36.4892 27.615 36.3612 28.7939 36.0624 29.8998C35.7637 31.0057 35.3263 32.0282 34.7501 32.9671C34.494 33.4471 34.222 33.7392 33.9339 33.8435C33.6458 33.9478 33.2884 34 32.8616 34H14.5849C14.2222 34 13.8647 33.9113 13.5126 33.734C13.1606 33.5566 12.8992 33.301 12.7284 32.9671C12.1736 31.9656 11.7469 30.9484 11.4481 29.9155C11.1494 28.8826 11 27.782 11 26.6135C11 24.8816 11.3361 23.2488 12.0083 21.7152C12.6804 20.1815 13.5927 18.8461 14.745 17.7089C15.8973 16.5717 17.2469 15.6693 18.794 15.0016C20.3411 14.3339 21.9895 14 23.7393 14V14Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Performance highlights' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: 1: REST API performance dev note, 2: Multisite performance dev note, 3: code-formatted "WP_Query" linked to dev note, 4: Block registration performance dev note, 5: Site health checks dev note; 6: code-formatted "async", 7: Performance field guide. */
						__( 'WordPress 6.1 resolved more than 25 tickets dedicated to enhancing performance. From the  <a href="%1$s">REST API</a> to <a href="%2$s">multisite</a>, %3$s to <a href="%4$s">core block registration</a>, and <a href="%5$s">new Site Health checks</a> to the addition of the %6$s attribute to images, there are performance improvements for every type of site. A full breakdown can be found in the <a href="%7$s">Performance Field Guide</a>.' ),
						'https://make.wordpress.org/core/2022/10/10/performance-improvements-to-the-rest-api/',
						'https://make.wordpress.org/core/2022/10/10/multisite-improvements-in-wordpress-6-1/',
						'<a href="https://make.wordpress.org/core/2022/10/07/improvements-to-wp_query-performance-in-6-1/"><code>WP_Query</code></a>',
						'https://make.wordpress.org/core/2022/10/07/improved-php-performance-for-core-blocks-registration/',
						'https://make.wordpress.org/core/2022/10/06/new-cache-site-health-checks-in-wordpress-6-1/',
						'<code>async</code>',
						'https://make.wordpress.org/core/2022/10/11/performance-field-guide-for-wordpress-6-1/'
					);
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: %s: Link to install Performance Lab plugin if permitted, otherwise link to plugin on WordPress.org. */
						__( 'Be among the first to get the latest improvements by adding the <a href="%s">Performance Lab plugin</a> to your WordPress test site or sandbox.' ),
						current_user_can( 'install_plugins' ) ? admin_url( 'plugin-install.php?s=slug%253Aperformance-lab&tab=search&type=term' ) : 'https://wordpress.org/plugins/performance-lab/'
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M33.9426 16.4519L30.3191 12.9453L18.4578 24.9231L17.0032 29.8499L22.0592 28.4519L33.9426 16.4519ZM16 33.9453H24.934V32.2702H16V33.9453Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'Content-only editing support for container blocks' ); ?></h3>
				<p><?php _e( 'Thanks to content-only editing settings, layouts can be locked within container blocks. In a content-only block, its children are invisible to the List View and entirely uneditable. So you control the layout while your writers can focus on the content.' ); ?></p>
				<p><?php _e( 'Combine it with block locking options for even more advanced control over your blocks.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#1E1E1E"/>
						<circle cx="50%" cy="50%" r="30%" fill="red"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading"><?php _e( 'More responsive text with fluid typography' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to fluid typography demo. */
						__( '<a href="%s">Fluid typography</a> lets you define font sizes that adapt for easy reading in any screen size.' ),
						'https://make.wordpress.org/core/2022/08/04/whats-new-in-gutenberg-13-8-3-august/#fluid-typography-support'
					);
					?>
				</p>
			</div>
		</div>

		<hr />

		<div class="about__section has-2-columns is-wider-right">
			<div class="column about__image is-vertically-aligned-top">
				<a href="#">
					<img src="data:image/svg+xml,%3Csvg width='269' height='163' viewBox='0 0 269 163' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg clip-path='url(%23a)'%3E%3Crect width='269' height='163' rx='4' fill='%23FDFF85'/%3E%3Cpath d='M238.84 130.043a1 1 0 0 0-1.524.852v8.21a1 1 0 0 0 1.524.852l6.671-4.105a1 1 0 0 0 0-1.703l-6.671-4.106Z' fill='%231E1E1E'/%3E%3Crect x='226.25' y='120.25' width='29.5' height='29.5' rx='2.75' stroke='%231E1E1E' stroke-width='1.5'/%3E%3Cpath d='M99.597 127.44c-6.16 0-11.36-1.16-15.6-3.48-4.24-2.32-7.68-5.4-10.32-9.24-2.56-3.84-4.4-8.16-5.52-12.96A64.74 64.74 0 0 1 66.477 87c0-9.28 1.28-17.4 3.84-24.36 2.64-6.96 6.4-12.36 11.28-16.2 4.88-3.92 10.8-5.88 17.76-5.88 5.521 0 10.241 1.08 14.161 3.24s6.96 5.04 9.12 8.64c2.24 3.6 3.6 7.52 4.08 11.76h-11.88c-.72-4.16-2.44-7.36-5.16-9.6-2.72-2.24-6.2-3.36-10.44-3.36-5.84 0-10.68 2.76-14.52 8.28-3.76 5.44-5.76 13.84-6 25.2 1.92-3.52 4.88-6.52 8.88-9 4.08-2.48 8.76-3.72 14.04-3.72 4.72 0 9.12 1.12 13.2 3.36 4.16 2.24 7.52 5.4 10.08 9.48 2.64 4 3.96 8.76 3.96 14.28 0 4.88-1.2 9.48-3.6 13.8-2.4 4.32-5.8 7.84-10.2 10.56-4.32 2.64-9.48 3.96-15.48 3.96Zm-.72-11.04c3.361 0 6.361-.72 9.001-2.16 2.64-1.44 4.72-3.4 6.24-5.88 1.52-2.56 2.28-5.44 2.28-8.64 0-5.12-1.68-9.2-5.04-12.24-3.28-3.04-7.48-4.56-12.6-4.56-3.36 0-6.4.76-9.12 2.28-2.64 1.52-4.72 3.56-6.24 6.12-1.52 2.48-2.28 5.24-2.28 8.28 0 3.28.76 6.2 2.28 8.76 1.6 2.48 3.72 4.44 6.36 5.88 2.72 1.44 5.76 2.16 9.12 2.16Zm45.925 10.32c-2.4 0-4.4-.76-6-2.28-1.52-1.6-2.28-3.48-2.28-5.64 0-2.24.76-4.12 2.28-5.64 1.6-1.6 3.6-2.4 6-2.4s4.36.8 5.88 2.4c1.52 1.52 2.28 3.4 2.28 5.64 0 2.16-.76 4.04-2.28 5.64-1.52 1.52-3.48 2.28-5.88 2.28Zm26.814-.72V56.4l-13.56 3.12v-9.36l18.6-8.16h8.16v84h-13.2Z' fill='%231E1E1E'/%3E%3C/g%3E%3Cdefs%3E%3CclipPath id='a'%3E%3Crect width='269' height='163' rx='4' fill='%23fff'/%3E%3C/clipPath%3E%3C/defs%3E%3C/svg%3E%0A" alt="<?php echo esc_attr( __( 'Exploring WordPress 6.1 video' ) ); ?>" />
				</a>
			</div>
			<div class="column">
				<h3>
					<?php
					printf(
						/* translators: %s: Version number. */
						__( 'Learn more about WordPress %s' ),
						$display_version
					);
					?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %s: 6.1 overview video link. */
						__( 'See WordPress 6.1 in action! <a href="%s">Watch a brief overview video</a> highlighting some of the major features debuting in WordPress 6.1.' ),
						'#'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column" style="padding-top:0">
				<p>
					<?php
					printf(
						/* translators: 1: Learn WordPress workshops link, 2: Learn WordPress social learning link. */
						__( 'Explore <a href="%1$s">learn.wordpress.org/&#8203;workshops</a> for quick how-to videos and lots more on new features in WordPress. Or join a live <a href="%2$s">interactive online learning session</a> on a specific WordPress topic.' ),
						'https://learn.wordpress.org/workshops/',
						'https://learn.wordpress.org/social-learning/'
					);
					?>
				</p>
			</div>
			<div class="column" style="padding-top:0">
				<p>
					<?php
					printf(
						/* translators: %s: WordPress Field Guide link. */
						__( 'Check out the latest version of the <a href="%s">WordPress Field Guide</a>. It is overflowing with detailed developer notes to help you build with WordPress.' ),
						__( '#' )
					);
					?>
				</p>
			</div>
			<div class="column" style="padding-top:0">
				<p>
					<?php
					printf(
						/* translators: 1: WordPress Release Notes link, 2: WordPress version number. */
						__( '<a href="%1$s">Read the WordPress %2$s Release Notes</a> for more information on the included enhancements and issues fixed, installation information, developer notes and resources, release contributors, and the list of file changes in this release.' ),
						sprintf(
							/* translators: %s: WordPress version number. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							'6-1'
						),
						'6.1'
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
