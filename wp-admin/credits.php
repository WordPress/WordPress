<?php
/**
 * Credits administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';
require_once __DIR__ . '/includes/credits.php';

$title = __( 'Credits' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

require_once ABSPATH . 'wp-admin/admin-header.php';

$credits = wp_credits();
?>
<div class="wrap about__container">

	<div class="about__header">
		<div class="about__header-title">
			<h1>
				<?php _e( 'WordPress' ); ?>
				<span><?php echo $display_version; ?></span>
			</h1>
		</div>

		<div class="about__header-text">
			<p>
				<?php _e( 'Building more with blocks, faster and easier.' ); ?>
			</p>
		</div>

		<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
			<a href="about.php" class="nav-tab"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
		</nav>
	</div>

	<div class="about__section">
		<div class="column">
			<h2><?php _e( 'WordPress is created by a worldwide team of passionate individuals.' ); ?></h2>

			<p>
				<?php
				if ( ! $credits ) {
					printf(
						/* translators: 1: https://wordpress.org/about/, 2: https://make.wordpress.org/ */
						__( 'WordPress is created by a <a href="%1$s">worldwide team</a> of passionate individuals. <a href="%2$s">Get involved in WordPress</a>.' ),
						__( 'https://wordpress.org/about/' ),
						__( 'https://make.wordpress.org/' )
					);
				} else {
					printf(
						/* translators: %s: https://make.wordpress.org/ */
						__( 'Want to see your name in lights on this page? <a href="%s">Get involved in WordPress</a>.' ),
						__( 'https://make.wordpress.org/' )
					);
				}
				?>
			</p>
		</div>

		<div class="about__image aligncenter">
			<img src="data:image/svg+xml;charset=utf8,%3Csvg width='1000' height='300' viewbox='0 0 1000 300' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill='%23F3F4F5' d='M0 0h1000v300H0z'/%3E%3Cpath style='mix-blend-mode:multiply' d='M39.6 140.22l931.1 3.36.8 76.5-929.5 6.6-2.4-86.46z' fill='%23216DD2'/%3E%3Cpath style='mix-blend-mode:multiply' d='M963.7 275.14s-.9-59.58-1-64.14c-.1-4.2-932.3 1.74-932.3 1.74L29 268.48v8.4' fill='%237FCDE6'/%3E%3Cpath style='mix-blend-mode:multiply' d='M958 73.32L47.8 70.26l1.2 78.66 907.3 4.26 1.7-79.86z' fill='%23072CF0'/%3E%3Cpath style='mix-blend-mode:multiply' d='M34 91.32l910.4-2.16L939.2 21 33.3 23.82l.7 67.5z' fill='%230188D9'/%3E%3C/svg%3E" alt="" />
		</div>
	</div>

<?php
if ( ! $credits ) {
	echo '</div>';
	require_once ABSPATH . 'wp-admin/admin-footer.php';
	exit;
}
?>

	<hr />

	<div class="about__section">
		<div class="column has-subtle-background-color">
			<?php wp_credits_section_title( $credits['groups']['core-developers'] ); ?>
			<?php wp_credits_section_list( $credits, 'core-developers' ); ?>
			<?php wp_credits_section_list( $credits, 'contributing-developers' ); ?>
		</div>
	</div>

	<hr />

	<div class="about__section">
		<div class="column">
			<?php wp_credits_section_title( $credits['groups']['props'] ); ?>
			<?php wp_credits_section_list( $credits, 'props' ); ?>
		</div>
	</div>

	<hr />

	<?php if ( isset( $credits['groups']['translators'] ) || isset( $credits['groups']['validators'] ) ) : ?>
	<div class="about__section">
		<div class="column">
			<?php wp_credits_section_title( $credits['groups']['validators'] ); ?>
			<?php wp_credits_section_list( $credits, 'validators' ); ?>
			<?php wp_credits_section_list( $credits, 'translators' ); ?>
		</div>
	</div>

	<hr />
	<?php endif; ?>

	<div class="about__section">
		<div class="column">
			<?php wp_credits_section_title( $credits['groups']['libraries'] ); ?>
			<?php wp_credits_section_list( $credits, 'libraries' ); ?>
		</div>
	</div>
</div>
<?php

require_once ABSPATH . 'wp-admin/admin-footer.php';

return;

// These are strings returned by the API that we want to be translatable.
__( 'Project Leaders' );
/* translators: %s: The current WordPress version number. */
__( 'Core Contributors to WordPress %s' );
__( 'Noteworthy Contributors' );
__( 'Cofounder, Project Lead' );
__( 'Lead Developer' );
__( 'Release Lead' );
__( 'Release Design Lead' );
__( 'Release Deputy' );
__( 'Core Developer' );
__( 'External Libraries' );
