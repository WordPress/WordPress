<?php
/**
 * Contribute administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

// Used in the HTML title tag.
$title = __( 'Get Involved' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );
$header_alt_text         = sprintf(
	/* translators: %s: Version number. */
	__( 'WordPress %s' ),
	$display_version
);

require_once ABSPATH . 'wp-admin/admin-header.php';
?>
<div class="wrap about__container">

	<div class="about__header">
		<div class="about__header-image">
			<img src="images/about-release-logo.svg?ver=6.9" alt="<?php echo esc_attr( $header_alt_text ); ?>" />
		</div>

		<div class="about__header-title">
			<h1>
				<?php _e( 'Get Involved' ); ?>
			</h1>
		</div>

		<div class="about__header-text">
			<?php _e( 'Be the future of WordPress' ); ?>
		</div>
	</div>

	<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
		<a href="about.php" class="nav-tab"><?php _e( 'What&#8217;s New' ); ?></a>
		<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
		<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
		<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
		<a href="contribute.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'Get Involved' ); ?></a>
	</nav>

	<div class="about__section has-2-columns is-wider-right">
		<div class="column">
			<img src="<?php echo esc_url( admin_url( 'images/contribute-main.svg?ver=6.5' ) ); ?>" alt="" width="290" height="290" />
		</div>
		<div class="column is-vertically-aligned-center">
			<p><?php _e( 'Do you use WordPress for work, for personal projects, or even just for fun? You can help shape the long-term success of the open source project that powers millions of websites around the world.' ); ?></p>
			<p><?php _e( 'Join the diverse WordPress contributor community and connect with other people who are passionate about maintaining a free and open web.' ); ?></p>

			<ul>
				<li><?php _e( 'Be part of a global open source community.' ); ?></li>
				<li><?php _e( 'Apply your skills or learn new ones.' ); ?></li>
				<li><?php _e( 'Grow your network and make friends.' ); ?></li>
			</ul>
		</div>
	</div>

	<div class="about__section has-2-columns is-wider-left">
		<div class="column is-vertically-aligned-center">
			<h2 class="is-smaller-heading"><?php _e( 'No-code contribution' ); ?></h2>
			<p><?php _e( 'WordPress may thrive on technical contributions, but you don&#8217;t have to code to contribute. Here are some of the ways you can make an impact without writing a single line of code:' ); ?></p>
			<ul>
				<li><?php _e( '<strong>Share</strong> your knowledge in the WordPress support forums.' ); ?></li>
				<li><?php _e( '<strong>Write</strong> or improve documentation for WordPress.' ); ?></li>
				<li><?php _e( '<strong>Translate</strong> WordPress into your local language.' ); ?></li>
				<li><?php _e( '<strong>Create</strong> and improve WordPress educational materials.' ); ?></li>
				<li><?php _e( '<strong>Promote</strong> the WordPress project to your community.' ); ?></li>
				<li><?php _e( '<strong>Curate</strong> submissions or take photos for the Photo Directory.' ); ?></li>
				<li><?php _e( '<strong>Organize</strong> or participate in local Meetups and WordCamps.' ); ?></li>
				<li><?php _e( '<strong>Lend</strong> your creative imagination to the WordPress UI design.' ); ?></li>
				<li><?php _e( '<strong>Edit</strong> videos and add captions to WordPress.tv.' ); ?></li>
				<li><?php _e( '<strong>Explore</strong> ways to reduce the environmental impact of websites.' ); ?></li>
			</ul>
		</div>
		<div class="column">
			<img src="<?php echo esc_url( admin_url( 'images/contribute-no-code.svg?ver=6.5' ) ); ?>" alt="" width="290" height="290" />
		</div>
	</div>
	<div class="about__section has-2-columns is-wider-right">
		<div class="column">
			<img src="<?php echo esc_url( admin_url( 'images/contribute-code.svg?ver=6.5' ) ); ?>" alt="" width="290" height="290" />
		</div>
		<div class="column is-vertically-aligned-center">
			<h2 class="is-smaller-heading"><?php _e( 'Code-based contribution' ); ?></h2>
			<p><?php _e( 'If you do code, or want to learn how, you can contribute technically in numerous ways:' ); ?></p>
			<ul>
				<li><?php _e( '<strong>Find</strong> and report bugs in the WordPress core software.' ); ?></li>
				<li><?php _e( '<strong>Test</strong> new releases and proposed features for the Block Editor.' ); ?></li>
				<li><?php _e( '<strong>Write</strong> and submit patches to fix bugs or help build new features.' ); ?></li>
				<li><?php _e( '<strong>Contribute</strong> to the code, improve the UX, and test the WordPress app.' ); ?></li>
			</ul>
			<p><?php _e( 'WordPress embraces new technologies, while being committed to backward compatibility. The WordPress project uses the following languages and libraries:' ); ?></p>
			<ul>
				<li><?php _e( 'WordPress Core and Block Editor: HTML, CSS, PHP, SQL, JavaScript, and React.' ); ?></li>
				<li><?php _e( 'WordPress app: Kotlin, Java, Swift, Objective-C, Vue, Python, and TypeScript.' ); ?></li>
			</ul>
		</div>
	</div>

	<div class="about__section is-feature has-subtle-background-color">
		<div class="column">
			<h2><?php _e( 'Shape the future of the web with WordPress' ); ?></h2>
			<p><?php _e( 'Finding the area that aligns with your skills and interests is the first step toward meaningful contribution. With more than 20 Make WordPress teams working on different parts of the open source WordPress project, there&#8217;s a place for everyone, no matter what your skill set is.' ); ?></p>
			<p><a href="<?php echo esc_url( __( 'https://make.wordpress.org/contribute/' ) ); ?>"><?php _e( 'Find your team &rarr;' ); ?></a></p>
		</div>
	</div>

</div>
<?php
require_once ABSPATH . 'wp-admin/admin-footer.php';
