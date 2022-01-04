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

// Used in the HTML title tag.
$title = __( 'Credits' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

require_once ABSPATH . 'wp-admin/admin-header.php';

$credits = wp_credits();
?>
<div class="wrap about__container">

	<div class="about__header">
		<div class="about__header-title">
			<h1>
				<?php _e( 'Contributors' ); ?>
			</h1>
		</div>

		<div class="about__header-text">
			<?php
			printf(
				/* translators: %s: Version number. */
				__( 'WordPress %s was created by a worldwide team of passionate individuals' ),
				$display_version
			);
			?>
		</div>

		<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
			<a href="about.php" class="nav-tab"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="credits.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
		</nav>
	</div>

	<div class="about__section has-1-column">
		<div class="column aligncenter">
			<?php if ( ! $credits ) : ?>

			<p>
				<?php
				printf(
					/* translators: 1: https://wordpress.org/about/ */
					__( 'WordPress is created by a <a href="%1$s">worldwide team</a> of passionate individuals.' ),
					__( 'https://wordpress.org/about/' )
				);
				?>
				<br />
				<a href="https://make.wordpress.org/"><?php _e( 'Get involved in WordPress.' ); ?></a>
			</p>

			<?php else : ?>

			<p>
				<?php _e( 'Want to see your name in lights on this page?' ); ?>
				<br />
				<a href="https://make.wordpress.org/"><?php _e( 'Get involved in WordPress.' ); ?></a>
			</p>

			<?php endif; ?>
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
		<div class="column is-edge-to-edge">
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
