<?php
/**
 * Credits administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );
require_once( dirname( __FILE__ ) . '/includes/credits.php' );

$title = __( 'Credits' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

include( ABSPATH . 'wp-admin/admin-header.php' );

$credits = wp_credits();
?>
<div class="wrap about__container">

	<div class="about__header">
		<div class="about__header-title">
			<h1>
				<span><?php echo $display_version; ?></span>
				<?php _e( 'WordPress' ); ?>
			</h1>
		</div>

		<div class="about__header-badge"></div>

		<div class="about__header-text">
			<p>
				<?php
				printf(
					/* translators: %s: The current WordPress version number. */
					__( 'Introducing our most refined user experience with the improved block editor in WordPress %s!' ),
					$display_version
				);
				?>
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
			<img src="data:image/svg+xml;charset=utf8,%3Csvg width='1320' height='350' viewbox='0 0 1320 350' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='72' height='220' transform='matrix(1 0 0 -1 61 220)' fill='%23321017'/%3E%3Crect width='72' height='250' transform='matrix(1 0 0 -1 166 300)' fill='%23BD3854'/%3E%3Crect width='72' height='220' transform='matrix(1 0 0 -1 272 220)' fill='%23321017'/%3E%3Crect width='71' height='220' transform='matrix(1 0 0 -1 378 220)' fill='%235F1B29'/%3E%3Crect width='71' height='220' transform='matrix(1 0 0 -1 483 220)' fill='%23321017'/%3E%3Crect width='71' height='220' transform='matrix(1 0 0 -1 587 220)' fill='%235F1B29'/%3E%3Crect width='71.28' height='250' transform='matrix(1 0 0 -1 689 300)' fill='%23BD3854'/%3E%3Crect width='72' height='220' transform='matrix(1 0 0 -1 884 220)' fill='%235F1B29'/%3E%3Crect width='72' height='220' transform='matrix(1 0 0 -1 789 220)' fill='%23321017'/%3E%3Crect width='71' height='220' transform='matrix(1 0 0 -1 985 220)' fill='%23321017'/%3E%3Crect width='72' height='220' transform='matrix(1 0 0 -1 1084 220)' fill='%235F1B29'/%3E%3Crect width='72' height='220' transform='matrix(1 0 0 -1 1179 220)' fill='%233D0F19'/%3E%3C/svg%3E%0A" alt="" />
		</div>
	</div>

<?php
if ( ! $credits ) {
	echo '</div>';
	include( ABSPATH . 'wp-admin/admin-footer.php' );
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

include( ABSPATH . 'wp-admin/admin-footer.php' );

return;

// These are strings returned by the API that we want to be translatable
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
