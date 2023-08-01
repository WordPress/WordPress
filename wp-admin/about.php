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
		</div>

		<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
			<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			<a href="contribute.php" class="nav-tab"><?php _e( 'Get Involved' ); ?></a>
		</nav>
		
		<div class="about__section aligncenter">
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
					<?php _e( 'Create beautiful and compelling websites more efficiently than ever. Whether you want to build an entire site without coding or are a developer looking to customize every detail, WordPress 6.3 has something for you.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/1-site-editor.webp" alt="" height="436" width="436" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Do everything in the Site Editor' ); ?></h3>
				<p><?php _e( 'WordPress 6.3 brings your content, templates, and patterns together in the Site Editor for the first time. Add pages, browse style variations, create synced patterns, and enjoy fine-tuned control over your navigation menus. No more time wasted switching across different site areas—now you can focus on what matters most. Creation to completion, all in one place.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Create and sync patterns' ); ?></h3>
				<p><?php _e( 'Arrange blocks in unlimited ways and save them as Patterns for use throughout your site. You can even specify whether to sync your patterns (previously referred to as “reusable blocks”) so that one change applies to all parts of your site. Or, utilize patterns as a starting point with the ability to customize each instance.' ); ?></p>
			</div>
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/2-create-patterns.webp" alt="" height="436" width="436" />
				</div>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/3-command-palette.webp" alt="" height="436" width="436" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Work faster with the Command Palette' ); ?></h3>
				<p><?php _e( 'Switch to a specific template or open your editor preferences with a new tool that helps you quickly navigate expanded functionality. With simple keyboard shortcuts (⌘+k on Mac or Ctrl+k on Windows), clicking the sidebar search icon in Site View, or clicking the Title Bar, get where you need to go and do what you need to do in seconds.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Sharpen your designs with new tools' ); ?></h3>
				<p><?php _e( 'New design controls bring more versatility for fine-tuning designs, starting with the ability to customize your caption&#8217;s styles from the Styles Interface without coding. You can manage your duotone filters in Styles for supported blocks and pick from the options provided by your theme or disable them entirely. The Cover block gets added settings for text color, layout controls, and border options, making this powerful block even more handy.' ); ?></p>
			</div>
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/4-design-tools.webp" alt="" height="436" width="436" />
				</div>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/5-style-revisions.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Track design changes with Style Revisions' ); ?></h3>
				<p><?php _e( 'You can now see how your site looked at a specific time. Visualize these revisions in a timeline and access a one-click option to restore prior styles.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/6-footnotes-block.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Annotate with the Footnotes block' ); ?></h3>
				<p><?php _e( 'Footnotes add convenient annotations throughout your content. Now you can add and link footnotes for any paragraph.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.3/7-details-block.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Show or hide content with the Details block' ); ?></h3>
				<p><?php _e( 'Use the block to avoid spoiling a surprise, create an interactive Q&A section, or hide a long paragraph under a heading.' ); ?></p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M25.7781 16.8569L25.8 22.8573L28.9984 22.8572C29.805 22.8572 30.2796 23.6339 29.8204 24.2024L23.8213 31.6292C23.2633 32.3201 22.2013 31.9819 22.2013 31.1416L22.2 25.1481H19.0016C18.1961 25.1481 17.7212 24.3733 18.1782 23.8047L24.1496 16.3722C24.7055 15.6804 25.7749 16.0169 25.7781 16.8569Z" fill="white"/>
					</svg>
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Performance gets a boost' ); ?></h3>
				<p><?php _e( 'WordPress 6.3 has 170+ performance updates, including defer and async support for the Scripts API and fetchpriority support for images. These improvements can improve your website&#8217;s load time as perceived by visitors, along with block template resolution, image lazy-loading, and the emoji loader.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M24 18.285C23.55 18.285 23.1638 18.1237 22.8413 17.8012C22.5188 17.4788 22.3575 17.0925 22.3575 16.6425C22.3575 16.1925 22.5188 15.8062 22.8413 15.4837C23.1638 15.1612 23.55 15 24 15C24.45 15 24.8363 15.1612 25.1588 15.4837C25.4813 15.8062 25.6425 16.1925 25.6425 16.6425C25.6425 17.0925 25.4813 17.4788 25.1588 17.8012C24.8363 18.1237 24.45 18.285 24 18.285ZM21.5925 33V21.0075C20.5725 20.9325 19.5863 20.8275 18.6338 20.6925C17.6813 20.5575 16.77 20.385 15.9 20.175L16.2375 18.825C17.5125 19.125 18.78 19.3387 20.04 19.4662C21.3 19.5938 22.62 19.6575 24 19.6575C25.38 19.6575 26.7 19.5938 27.96 19.4662C29.22 19.3387 30.4875 19.125 31.7625 18.825L32.1 20.175C31.23 20.385 30.3187 20.5575 29.3663 20.6925C28.4137 20.8275 27.4275 20.9325 26.4075 21.0075V33H25.0575V27.15H22.9425V33H21.5925Z" fill="white"/>
					</svg>
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Accessibility remains a core focus' ); ?></h3>
				<p><?php _e( 'Incorporating more than 50 accessibility improvements across the platform, WordPress 6.3 is more accessible than ever. Improved labeling, optimized tab and arrow-key navigation, revised heading hierarchy, and new controls in the admin image editor allow those using assistive technologies to navigate more easily.' ); ?></p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M15.9996 25C16.6704 25.3354 16.6703 25.3357 16.6702 25.3359L16.673 25.3305C16.6762 25.3242 16.6818 25.3135 16.6899 25.2985C16.7059 25.2686 16.7316 25.2218 16.7669 25.1608C16.8377 25.0385 16.9469 24.8592 17.0954 24.6419C17.3931 24.2062 17.8444 23.624 18.4543 23.0431C19.6731 21.8824 21.4972 20.75 23.9996 20.75C26.502 20.75 28.3261 21.8824 29.5449 23.0431C30.1549 23.624 30.6061 24.2062 30.9038 24.6419C31.0523 24.8592 31.1615 25.0385 31.2323 25.1608C31.2676 25.2218 31.2933 25.2686 31.3093 25.2985C31.3174 25.3135 31.323 25.3242 31.3262 25.3305L31.3291 25.3359C31.3289 25.3357 31.3288 25.3354 31.9996 25C32.6704 24.6646 32.6703 24.6643 32.6701 24.664L32.6688 24.6614L32.6662 24.6563L32.6583 24.6408C32.6517 24.6282 32.6427 24.6108 32.631 24.5892C32.6078 24.5459 32.5744 24.4852 32.5306 24.4096C32.4432 24.2584 32.3141 24.0471 32.1423 23.7956C31.7994 23.2938 31.2819 22.626 30.5794 21.9569C29.1731 20.6176 26.9972 19.25 23.9996 19.25C21.002 19.25 18.8261 20.6176 17.4199 21.9569C16.7174 22.626 16.1998 23.2938 15.8569 23.7956C15.6851 24.0471 15.556 24.2584 15.4686 24.4096C15.4248 24.4852 15.3914 24.5459 15.3682 24.5892C15.3566 24.6108 15.3475 24.6282 15.3409 24.6408L15.333 24.6563L15.3304 24.6614L15.3295 24.6632C15.3293 24.6635 15.3288 24.6646 15.9996 25ZM23.9996 28C25.9326 28 27.4996 26.433 27.4996 24.5C27.4996 22.567 25.9326 21 23.9996 21C22.0666 21 20.4996 22.567 20.4996 24.5C20.4996 26.433 22.0666 28 23.9996 28Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Preview block themes' ); ?></h3>
				<p><?php _e( 'Experience block themes before you switch and preview the Site Editor, with options to customize directly before committing to a new theme.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M30.5 19H17.5C17.2239 19 17 19.2239 17 19.5V28.5C17 28.7761 17.2239 29 17.5 29H30.5C30.7761 29 31 28.7761 31 28.5V19.5C31 19.2239 30.7761 19 30.5 19ZM17.5 17.5H30.5C31.6046 17.5 32.5 18.3954 32.5 19.5V28.5C32.5 29.6046 31.6046 30.5 30.5 30.5H17.5C16.3954 30.5 15.5 29.6046 15.5 28.5V19.5C15.5 18.3954 16.3954 17.5 17.5 17.5ZM18.5 20.5H19.25H22V22H20V24H18.5V21.25V20.5ZM28.75 27.5H29.5V26.75V24H28V26L26 26V27.5L28.75 27.5Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Set aspect ratio on images' ); ?></h3>
				<p><?php _e( 'Specify your aspect ratios and ensure design integrity, especially when using images in patterns.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M29.7499 14.9862L29.7499 14.0059L28.7939 14.279L28.7939 14.279L28.7928 14.2793L28.7928 14.2793L28.7821 14.2824L28.7506 14.2915C28.7234 14.2994 28.6837 14.3111 28.633 14.3261C28.5317 14.3562 28.386 14.4001 28.2068 14.4559C27.8488 14.5673 27.3551 14.7265 26.8128 14.9179C25.7533 15.2918 24.4209 15.8181 23.5839 16.3761C21.9787 17.4462 21.2703 18.4251 20.8568 19.1143C20.6874 19.3967 20.4992 19.8705 20.3226 20.3793C20.1388 20.9091 19.9461 21.5414 19.7753 22.1819C19.6047 22.8215 19.453 23.4805 19.3543 24.0621C19.2758 24.5242 19.2192 24.9991 19.2374 25.387L18.3036 27.7216C18.1498 28.1062 18.3368 28.5427 18.7214 28.6965C19.106 28.8503 19.5425 28.6633 19.6963 28.2787L20.3141 26.7342C20.635 26.7063 21.0572 26.6435 21.5194 26.5579C22.173 26.4369 22.9606 26.26 23.7647 26.0349C24.567 25.8102 25.3994 25.5337 26.137 25.2105C26.8594 24.8939 27.5557 24.5051 28.0303 24.0305C28.539 23.5218 28.8442 22.8139 29.0434 22.0898C29.2457 21.3547 29.36 20.5222 29.4345 19.6922C29.5006 18.9555 29.5374 18.1945 29.5718 17.4814L29.585 17.2087C29.6156 16.5862 29.6469 16.0134 29.6961 15.5127C29.7299 15.3451 29.7487 15.1792 29.7499 15.0162C29.7501 15.0062 29.7501 14.9962 29.7499 14.9862ZM21.8114 24.9706L22.8071 23.7536L27.0303 19.5305C27.386 19.1747 27.7182 18.8155 28.0194 18.4588C27.998 18.8284 27.973 19.1965 27.9405 19.5581C27.8692 20.353 27.7647 21.0831 27.5972 21.6918C27.4267 22.3114 27.211 22.7285 26.9697 22.9698C26.6943 23.2451 26.2031 23.5438 25.5349 23.8366C24.8818 24.1228 24.1205 24.3775 23.3603 24.5904C22.8227 24.7409 22.2925 24.8686 21.8114 24.9706ZM18 32.0002H26V30.5002H18V32.0002Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Build your site distraction-free' ); ?></h3>
				<p><?php _e( 'Distraction-free designing is now available in the Site Editor.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path fill="#fff" fill-rule="evenodd" d="m20.014 21.168 3.988-3.418 3.988 3.418-.976 1.14-3.012-2.582-3.012 2.581-.976-1.139Z" clip-rule="evenodd"/>
						<path fill="#fff" d="M16 29h16v-1.5H16V29Z"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Rediscover the Top Toolbar' ); ?></h3>
				<p><?php _e( 'A revamped top toolbar offers parent selectors for nested blocks, options when selecting multiple blocks, and a new interface embedded into the title bar with new functionality in mind.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M15 17.5H26V19H15V17.5Z" fill="white"/>
						<path d="M18.5 23H29.5V24.5H18.5V23Z" fill="white"/>
						<path d="M33 28.5H22V30H33V28.5Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'List View improvements' ); ?></h3>
				<p><?php _e( 'Drag and drop to every content layer and delete any block you would like in the updated List View.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M27.7 17.2L33.3 22.8C34 23.4 34 24.6 33.2 25.3L27.6 30.9C27.3 31.2 26.8 31.4 26.4 31.4C26 31.4 25.5 31.2 25.2 30.9L19.6 25.3C18.9 24.6 18.9 23.5 19.6 22.8L25.2 17.2C25.9 16.5 27 16.5 27.7 17.2Z" fill="white"/>
						<path d="M22 17.5L15.7 23.8C15.6 23.9 15.6 24.1 15.8 24.1L22.1 30.4L21 31.5L14.7 25.3C14 24.6 14 23.5 14.7 22.8L21 16.5L22 17.5Z" fill="white"/>
					</svg>
				</div>
				<h3 class="is-smaller-heading" style="margin-top:calc(var(--gap) / 2);margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Build templates with Patterns' ); ?></h3>
				<p><?php _e( 'Create unique patterns to jumpstart template creation with a new modal enabling access to pattern selection.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="about__section has-3-columns">
			<div class="column about__image is-vertically-aligned-top">
				<img src="<?php echo esc_url( admin_url( 'images/about-release-badge.svg?ver=6.3' ) ); ?>" alt="" height="270" width="270" />
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
				<p>
					<?php
					printf(
						/* translators: 1: Learn WordPress link, 2: Workshops link. */
						__( '<a href="%1$s">Learn WordPress</a> is a free resource for new and experienced WordPress users. Learn is stocked with how-to videos on using various features in WordPress, <a href="%2$s">interactive events</a> for exploring topics in-depth, and lesson plans for diving deep into specific areas of WordPress.' ),
						'https://learn.wordpress.org/',
						'https://learn.wordpress.org/online-workshops/'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M23 34v-4h-5l-2.293-2.293a1 1 0 0 1 0-1.414L18 24h5v-2h-7v-6h7v-2h2v2h5l2.293 2.293a1 1 0 0 1 0 1.414L30 22h-5v2h7v6h-7v4h-2Zm-5-14h11.175l.646-.646a.5.5 0 0 0 0-.708L29.175 18H18v2Zm.825 8H30v-2H18.825l-.646.646a.5.5 0 0 0 0 .708l.646.646Z" fill="#fff"/>
					</svg>
				</div>
				<p style="margin-top:calc(var(--gap) / 2);">
					<?php
					printf(
						/* translators: %s: WordPress Field Guide link. */
						__( 'Check out the latest version of the <a href="%s">WordPress Field Guide</a>. It is overflowing with detailed developer notes to help you build with WordPress.' ),
						__( 'https://make.wordpress.org/core/2023/07/18/wordpress-6-3-field-guide/' )
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#151515"/>
						<path d="M28 19.75h-8v1.5h8v-1.5ZM20 23h8v1.5h-8V23ZM26 26.25h-6v1.5h6v-1.5Z" fill="#fff"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M29 16H19a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V18a2 2 0 0 0-2-2Zm-10 1.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H19a.5.5 0 0 1-.5-.5V18a.5.5 0 0 1 .5-.5Z" fill="#fff"/>
					</svg>
				</div>
				<p style="margin-top:calc(var(--gap) / 2);">
					<?php
					printf(
						/* translators: 1: WordPress Release Notes link, 2: WordPress version number. */
						__( '<a href="%1$s">Read the WordPress %2$s Release Notes</a> for more information on the included enhancements and issues fixed, installation information, developer notes and resources, release contributors, and the list of file changes in this release.' ),
						sprintf(
							/* translators: %s: WordPress version number. */
							esc_url( __( 'https://wordpress.org/documentation/wordpress-version/version-%s/' ) ),
							'6-3'
						),
						'6.3'
					);
					?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="return-to-dashboard">
			<?php
			if ( isset( $_GET['updated'] ) && current_user_can( 'update_core' ) ) {
				printf(
					'<a href="%1$s">%2$s</a> | ',
					esc_url( self_admin_url( 'update-core.php' ) ),
					is_multisite() ? __( 'Go to Updates' ) : __( 'Go to Dashboard &rarr; Updates' )
				);
			}

			printf(
				'<a href="%1$s">%2$s</a>',
				esc_url( self_admin_url() ),
				is_blog_admin() ? __( 'Go to Dashboard &rarr; Home' ) : __( 'Go to Dashboard' )
			);
			?>
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
