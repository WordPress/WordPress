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
		</div>

		<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
			<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			<a href="contribute.php" class="nav-tab"><?php _e( 'Get Involved' ); ?></a>
		</nav>

		<div class="about__section changelog has-subtle-background-color">
			<div class="column">
				<h2><?php _e( 'Maintenance and Security Release' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
							'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.',
							12
						),
						'6.5.2',
						'12'
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '6.5.2' )
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
					<?php _e( 'Take your site-building experience further with WordPress 6.5. Explore more avenues to make it your own, with new features and enhancements that will help fine-tune your creative work. Discover the latest additions to the developer experience, with fresh foundational tools poised to transform the future of blocks.' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.5/1-font-library.webp" alt="" height="436" width="436" />
				</div>
			</div>
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Add and manage fonts across your site' ); ?></h3>
				<p><?php _e( 'The new Font Library puts you in control of an essential piece of your site&#8217;s design—typography—without coding or extra steps. Effortlessly install, remove, and activate local and Google Fonts across your site for any block theme. The ability to include custom typography collections gives site creators and publishers even more choice.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3><?php _e( 'Get more details from your style revisions' ); ?></h3>
				<p><?php _e( 'Work through creative projects with a more comprehensive picture of what&#8217;s been done—and what you can fall back on. Get details like time stamps, quick summaries, and a paginated list of total revisions. View revisions from the Style Book to see changes outside of what you&#8217;re working on. Revisions are also now available for templates and template parts.' ); ?></p>
			</div>
			<div class="column is-vertically-aligned-center">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.5/3-style-revisions.webp" alt="" height="436" width="436" />
				</div>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.5/4-background-images.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Do more with background images in Group blocks' ); ?></h3>
				<p><?php _e( 'Control size, repeat, and focal point options so you can play around with subtle or splashy ways to add visual interest to layouts.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.5/5-cover-aspect-ratio.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Get more control over images in Cover blocks' ); ?></h3>
				<p><?php _e( 'Set aspect ratios for Cover block images and easily add color overlays that automatically source color from your chosen image.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.5/6-box-shadow.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Add box shadow supports to even more blocks' ); ?></h3>
				<p><?php _e( 'With shadow supports enabled, you can create layouts with visual depth or add a little personality to your design.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-3-columns">
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.5/7-data-views.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Discover new Data Views' ); ?></h3>
				<p><?php _e( 'Find and organize your data however you like with data views for pages, templates, patterns, and template parts. Arrange it in a table or grid view with the option to toggle fields and make bulk changes.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.5/8-drag-n-drop.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Smoother drag-and-drop' ); ?></h3>
				<p><?php _e( 'Feel the difference when you move things around, with helpful visual cues like displaced items in List View or frictionless dragging to anywhere in your workspace—from beginning to end.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<img src="https://s.w.org/images/core/6.5/9-link-controls.webp" alt="" height="270" width="270" />
				</div>
				<h3 class="is-smaller-heading" style="margin-bottom:calc(var(--gap) / 4);"><?php _e( 'Improved link controls' ); ?></h3>
				<p><?php _e( 'Create and manage links easily with a more intuitive link-building experience, like a streamlined UI and a shortcut for copying links.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="6" fill="#ededed"/>
						<path d="M18.9167 16.5C18.2757 16.5 17.661 16.7546 17.2078 17.2078C16.7546 17.661 16.5 18.2757 16.5 18.9167V21.3333H18.3125V18.9167C18.3125 18.7564 18.3762 18.6028 18.4895 18.4895C18.6028 18.3762 18.7564 18.3125 18.9167 18.3125H21.3333V16.5H18.9167ZM21.3333 29.1875H18.9167C18.7564 29.1875 18.6028 29.1238 18.4895 29.0105C18.3762 28.8972 18.3125 28.7436 18.3125 28.5833V26.1667H16.5V28.5833C16.5 29.2243 16.7546 29.839 17.2078 30.2922C17.661 30.7454 18.2757 31 18.9167 31H21.3333V29.1875ZM26.1667 31V29.1875H28.5833C28.7436 29.1875 28.8972 29.1238 29.0105 29.0105C29.1238 28.8972 29.1875 28.7436 29.1875 28.5833V26.1667H31V28.5833C31 29.2243 30.7454 29.839 30.2922 30.2922C29.839 30.7454 29.2243 31 28.5833 31H26.1667ZM28.5833 16.5C29.2243 16.5 29.839 16.7546 30.2922 17.2078C30.7454 17.661 31 18.2757 31 18.9167V21.3333H29.1875V18.9167C29.1875 18.7564 29.1238 18.6028 29.0105 18.4895C28.8972 18.3762 28.7436 18.3125 28.5833 18.3125H26.1667V16.5H28.5833Z" fill="#1e1e1e"/>
					</svg>
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Bring interactions to blocks with the Interactivity API' ); ?></h3>
				<p><?php _e( 'The Interactivity API offers developers a standardized method for building interactive front-end experiences with blocks. It simplifies the process, with fewer dependencies on external tooling, while maintaining optimal performance. Use it to create memorable user experiences, like fetching search results instantly or letting visitors interact with content in real time.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="6" fill="#ededed"/>
						<path d="M18.95 19.45H27.15L25.45 21.25L26.55 22.35L30.15 18.75L26.65 14.75L25.55 15.75L27.45 18.05H18.95C18.05 18.05 17.25 18.35 16.65 18.95C15.25 20.45 15.25 23.15 15.25 24.55V24.75H16.75V24.45C16.75 23.35 16.75 20.95 17.75 19.95C18.05 19.65 18.45 19.45 18.95 19.45ZM32.75 23.45V23.25H31.25V23.55C31.25 24.65 31.25 27.05 30.25 28.05C29.95 28.35 29.55 28.55 28.95 28.55H20.75L22.45 26.85L21.35 25.75L17.85 29.25L21.35 33.25L22.45 32.25L20.55 29.95H28.95C29.85 29.95 30.65 29.65 31.25 29.05C32.75 27.65 32.75 24.85 32.75 23.45Z" fill="#1e1e1e"/>
					</svg>
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Connect blocks to custom fields or other dynamic content' ); ?></h3>
				<p><?php _e( 'Link core block attributes to custom fields and use the value of custom fields without creating custom blocks. Powered by the Block Bindings API, developers can extend this capability further to connect blocks to any dynamic content—even beyond custom fields. If there&#8217;s data stored elsewhere, easily point blocks to that new source with only a few lines of code.' ); ?></p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="6" fill="#ededed"/>
						<path d="M33 18.75H23.1925C22.7954 17.7305 21.7239 17 20.4643 17C19.2047 17 18.1332 17.7305 17.736 18.75H15V20.5H17.736C18.1332 21.5195 19.2047 22.25 20.4643 22.25C21.7239 22.25 22.7954 21.5195 23.1925 20.5H33V18.75Z" fill="#1e1e1e"/>
						<path d="M33 27.5H30.264C29.8668 26.4805 28.7953 25.75 27.5357 25.75C26.2761 25.75 25.2046 26.4805 24.8075 27.5H15V29.25H24.8075C25.2046 30.2695 26.2761 31 27.5357 31C28.7953 31 29.8668 30.2695 30.264 29.25H33V27.5Z" fill="#1e1e1e"/>
					</svg>
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Add appearance tools to Classic themes' ); ?></h3>
				<p><?php _e( 'Give designers and creators using Classic themes access to an upgraded design experience. Opt in to support for spacing, border, typography, and color options, even without using theme.json. Once support is enabled, more tools will be automatically added as they become available.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="6" fill="#ededed"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M22.5 16L22.5 20H25.5V16H27V20H28.5C29.0523 20 29.5 20.4477 29.5 21V25L26.5 29V31C26.5 31.5523 26.0523 32 25.5 32H22.5C21.9477 32 21.5 31.5523 21.5 31V29L18.5 25V21C18.5 20.4477 18.9477 20 19.5 20H21L21 16H22.5ZM23 28.5V30.5H25V28.5L28 24.5V21.5H20V24.5L23 28.5Z" fill="#1e1e1e"/>
					</svg>
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Explore improvements to the plugin experience' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Requires Plugins */
						__( 'There&#8217;s now an easier way to manage plugin dependencies. Plugin authors can supply a new %s header with a comma-separated list of required plugin slugs, presenting users with links to install and activate those plugins first.' ),
						'<code lang="en">Requires Plugins</code>'
					);
					?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns">
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#ededed"/>
						<path d="M28.4287 20.6507C28.8387 20.8874 28.9791 21.4116 28.7424 21.8215L24.7424 28.7498C24.5057 29.1597 23.9815 29.3002 23.5715 29.0635C23.1616 28.8268 23.0211 28.3026 23.2578 27.8926L27.2578 20.9644C27.4945 20.5544 28.0187 20.414 28.4287 20.6507Z" fill="#1e1e1e"/>
						<path d="M18.6433 23.579C18.2333 23.3423 17.7091 23.4828 17.4724 23.8927C17.2357 24.3027 17.3761 24.8269 17.7861 25.0636L18.281 25.3493C18.691 25.586 19.2152 25.4456 19.4519 25.0356C19.6886 24.6256 19.5481 24.1014 19.1381 23.8647L18.6433 23.579Z" fill="#1e1e1e"/>
						<path d="M20.0358 20.6508C20.4458 20.4141 20.97 20.5546 21.2067 20.9645L21.4924 21.4594C21.7291 21.8694 21.5887 22.3936 21.1787 22.6303C20.7687 22.867 20.2445 22.7265 20.0078 22.3166L19.7221 21.8217C19.4854 21.4117 19.6259 20.8875 20.0358 20.6508Z" fill="#1e1e1e"/>
						<path d="M24.8571 20C24.8571 19.5266 24.4734 19.1429 24 19.1429C23.5266 19.1429 23.1429 19.5266 23.1429 20V20.5714C23.1429 21.0448 23.5266 21.4286 24 21.4286C24.4734 21.4286 24.8571 21.0448 24.8571 20.5714V20Z" fill="#1e1e1e"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M14 26C14 20.4772 18.4772 16 24 16C29.5228 16 34 20.4772 34 26C34 28.0846 33.3612 30.0225 32.2686 31.6256L32.0135 32H15.9865L15.7314 31.6256C14.6388 30.0225 14 28.0846 14 26ZM24 17.7143C19.4239 17.7143 15.7143 21.4239 15.7143 26C15.7143 27.5698 16.1501 29.0357 16.9072 30.2857H31.0928C31.8499 29.0357 32.2857 27.5698 32.2857 26C32.2857 21.4239 28.5761 17.7143 24 17.7143Z" fill="#1e1e1e"/>
					</svg>
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Performance updates' ); ?></h3>
				<p><?php _e( 'This release includes 110+ performance updates, with an impressive increase in speed and efficiency across the Post Editor and Site Editor. Loading is over two times faster than in 6.4, with input processing speed up to five times faster than the previous release. Translated sites see up to 25% improvement in load time for this release.' ); ?></p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#ededed"/>
						<path d="M24 18.285C23.55 18.285 23.1637 18.1237 22.8412 17.8012C22.5187 17.4788 22.3575 17.0925 22.3575 16.6425C22.3575 16.1925 22.5187 15.8062 22.8412 15.4837C23.1637 15.1612 23.55 15 24 15C24.45 15 24.8362 15.1612 25.1587 15.4837C25.4812 15.8062 25.6425 16.1925 25.6425 16.6425C25.6425 17.0925 25.4812 17.4788 25.1587 17.8012C24.8362 18.1237 24.45 18.285 24 18.285ZM21.5925 33V21.0075C20.5725 20.9325 19.5862 20.8275 18.6337 20.6925C17.6812 20.5575 16.77 20.385 15.9 20.175L16.2375 18.825C17.5125 19.125 18.78 19.3387 20.04 19.4662C21.3 19.5938 22.62 19.6575 24 19.6575C25.38 19.6575 26.7 19.5938 27.96 19.4662C29.22 19.3387 30.4875 19.125 31.7625 18.825L32.1 20.175C31.23 20.385 30.3187 20.5575 29.3662 20.6925C28.4137 20.8275 27.4275 20.9325 26.4075 21.0075V33H25.0575V27.15H22.9425V33H21.5925Z" fill="#1e1e1e"/>
					</svg>
				</div>
				<h3 style="margin-top:calc(var(--gap) * 0.75);margin-bottom:calc(var(--gap) * 0.5)"><?php _e( 'Accessibility improvements' ); ?></h3>
				<p><?php _e( 'This release includes more than 65 accessibility improvements across the platform, making it more accessible than ever. This release adds fixes to contrast settings, cursor focus, submenus, and positioning of elements, among many others, that help improve the WordPress experience for everyone.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="about__section has-3-columns">
			<div class="column about__image is-vertically-aligned-top">
				<img src="<?php echo esc_url( admin_url( 'images/about-release-badge.svg?ver=6.5' ) ); ?>" alt="" height="280" width="280" />
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
						__( '<a href="%1$s">Learn WordPress</a> is a free resource for new and experienced WordPress users. Learn is stocked with how-to videos on using various features in WordPress, <a href="%2$s">interactive workshops</a> for exploring topics in-depth, and lesson plans for diving deep into specific areas of WordPress.' ),
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
						<rect width="48" height="48" rx="4" fill="#ededed"/>
						<path d="M23 34v-4h-5l-2.293-2.293a1 1 0 0 1 0-1.414L18 24h5v-2h-7v-6h7v-2h2v2h5l2.293 2.293a1 1 0 0 1 0 1.414L30 22h-5v2h7v6h-7v4h-2Zm-5-14h11.175l.646-.646a.5.5 0 0 0 0-.708L29.175 18H18v2Zm.825 8H30v-2H18.825l-.646.646a.5.5 0 0 0 0 .708l.646.646Z" fill="#1e1e1e"/>
					</svg>
				</div>
				<p style="margin-top:calc(var(--gap) / 2);">
					<?php
					printf(
						/* translators: 1: WordPress Field Guide link, 2: WordPress version number. */
						__( 'Explore the <a href="%1$s">WordPress %2$s Field Guide</a>. Learn about the changes in this release with detailed developer notes to help you build with WordPress.' ),
						esc_url( __( 'https://make.wordpress.org/core/wordpress-6-5-field-guide/' ) ),
						'6.5'
					);
					?>
				</p>
			</div>
			<div class="column">
				<div class="about__image">
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<rect width="48" height="48" rx="4" fill="#ededed"/>
						<path d="M28 19.75h-8v1.5h8v-1.5ZM20 23h8v1.5h-8V23ZM26 26.25h-6v1.5h6v-1.5Z" fill="#151515"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M29 16H19a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V18a2 2 0 0 0-2-2Zm-10 1.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H19a.5.5 0 0 1-.5-.5V18a.5.5 0 0 1 .5-.5Z" fill="#1e1e1e"/>
					</svg>
				</div>
				<p style="margin-top:calc(var(--gap) / 2);">
					<?php
					printf(
						/* translators: 1: WordPress Release Notes link, 2: WordPress version number. */
						__( '<a href="%1$s">Read the WordPress %2$s Release Notes</a> for information on installation, enhancements, fixed issues, release contributors, learning resources, and the list of file changes.' ),
						sprintf(
							/* translators: %s: WordPress version number. */
							esc_url( __( 'https://wordpress.org/documentation/wordpress-version/version-%s/' ) ),
							'6-5'
						),
						'6.5'
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
