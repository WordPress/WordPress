<?php
/**
 * @package WPSEO\Admin
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Display a list of contributors
 *
 * @param array $contributors
 */
function wpseo_display_contributors( $contributors ) {
	foreach ( $contributors as $username => $dev ) {
		echo '<li class="wp-person" id="wp-person-', $username, '">';
		echo '<a href="https://github.com/', $username, '"><img	src="https://secure.gravatar.com/avatar/', $dev->gravatar, '?s=60" class="gravatar" alt="', $dev->name, '"></a>';
		echo '<a class="web" href="https://github.com/', $username, '">', $dev->name, '</a>';
		echo '<span class="title">', $dev->role, '</span></li>';
	}
}

?>

<div class="wrap about-wrap">

	<h1><?php
		/* translators: %1$s expands to Yoast SEO */
		printf( __( 'Thank you for updating %1$s!', 'wordpress-seo' ), 'Yoast SEO' );
		?></h1>

	<p class="about-text">
		Yoast SEO 2.3 helps you optimize your site showing you errors straight from Google Search Console, and pointing you at posts that need work.
	</p>

	<h2 class="nav-tab-wrapper" id="wpseo-tabs">
		<a class="nav-tab" href="#top#new" id="new-tab">
			<?php
			/* translators: %s: '2.3' version number */
			echo sprintf( __( 'What’s new in %s', 'wordpress-seo' ), '2.3' );
			?>
		</a>
		<a class="nav-tab" href="#top#credits" id="credits-tab"><?php _e( 'Credits', 'wordpress-seo' ); ?></a>
	</h2>

	<div id="new" class="wpseotab">
		<div class="headline-feature">
			<h2 style="margin:30px 0 0 0;">Some of the changes we're most proud of:</h2>

			<div class="feature-section">
				<div class="col">
					<h3 class="alignleft">Fully integrated with Google's new Search Console</h3>

					<div class="clear"></div>
					<p>
						Google has renamed its Webmaster Tools to Search Console recently and has also rolled out a new
						API for it. This release brings the power of Google's Search Console straight to your WordPress
						install. All the errors Google gets, you will be able to see in your WordPress install.
					</p>

					<p>
						If you have <a target="_blank" href="https://yoast.com/wordpress/plugins/seo-premium/#utm_source=wordpress-seo-config&utm_medium=textlink&utm_campaign=about-page">Yoast SEO Premium</a>, you'll even be able to fix those errors by redirecting the broken
						URLs. Through the integration, we'll mark them as fixed in Google Search Console too!
					</p>
				</div>
				<div class="col">
					<img class="aligncenter"
					     src="https://yoast-mercury.s3.amazonaws.com/uploads/2015/07/search-console-screenshot.png"
					     alt="Yoast SEO Search Console integration screenshot"/>
				</div>
			</div>
		</div>

		<div class="headline-feature">
			<div class="feature-section">
				<div class="col">
					<img src="https://yoast-mercury.s3.amazonaws.com/uploads/2015/07/dashboard-widget.png"
					     alt="Yoast SEO Dashboard widget"/>
				</div>
				<div class="col">
					<h3 class="alignleft">Point you at posts that need work</h3>

					<div class="clear"></div>
					<p>
						It's sometimes easy to just keep on writing new posts, when you actually have old posts that
						need work. The new dashboard widget we've added points you at exactly those post that need work.
					</p>
					<p>
						Don't know how to optimize your posts? Read our <a target="_blank" href="https://yoast.com/ebooks/content-seo/#utm_source=wordpress-seo-config&utm_medium=textlink&utm_campaign=about-page">Content SEO eBook</a>!
					</p>
				</div>
			</div>
		</div>

		<div class="headline-feature">
			<div class="feature-section">
				<div class="col">
					<h3 class="alignleft">Breadcrumbs in the Customizer</h3>

					<div class="clear"></div>
					<p>
						If your theme declares support for <code>yoast-seo-breadcrumbs</code>, you will get a Customizer
						panel for Yoast SEO breadcrumbs. Automagically.
					</p>

					<p>
						Are you a developer? Learn <a href='http://kb.yoast.com/article/274-add-theme-support-for-yoast-seo-breadcrumbs'>how
							to implement this feature</a> for your theme(s).
					</p>
				</div>
				<div class="col">
					<img src="https://yoast-mercury.s3.amazonaws.com/uploads/2015/07/breadcrumbs-customizer1.png"
					     alt="Breadcrumbs in the customizer"/>
				</div>
			</div>
		</div>

		<div class="changelog feature-list finer-points">
			<h2 class="alignleft">More in this release</h2>

			<div class="clear"></div>

			<div class="feature-section col two-col">
				<div>
					<span class="dashicons dashicons-megaphone"></span>
					<h4>Yoast SEO now truly Yoast SEO</h4>
					</ul>
					<p>
						Everyone already did it, so we finally caved. The plugin formerly known as WordPress SEO by
						Yoast, will know officially be called Yoast SEO. for WordPress.
					</p>
				</div>

				<div class="last-feature">
					<span class="dashicons dashicons-admin-appearance"></span>
					<h4>Less admin clutter</h4>

					<p>
						We've changed the default for the edit posts overview page. No longer will you see multiple
						columns from Yoast SEO, you'll now only see one small column with the SEO score image.
					</p>
				</div>

				<div>
					<span class="dashicons dashicons-tag"></span>
					<h4>Just another WordPress site??</h4>

					<p>
						If your site has the default WordPress tagline "just another WordPress site", we'll actually
						give you a warning, asking you to change it.
					</p>
				</div>

				<div class="last-feature">
					<span class="dashicons dashicons-translation"></span>
					<h4><?php _e( 'More Translations', 'wordpress-seo' ); ?></h4>

					<p>
						<?php
						/* translators: %1$s expands to Yoast SEO, %2$s and %3$s to the anchor tags to the translate.yoast.com link, %4$d to the number of translations, %5$d to the number of translations, */
						printf( __( '%1$s ships, at time of release, with %4$d translations, of which %5$d are complete. That\'s a huge improvement from last time, and we\'re improving every week. Join us at %2$stranslate.yoast.com%3$s!', 'wordpress-seo' ), 'Yoast SEO', '<a target="_blank" href="https://translate.yoast.com/projects/wordpress-seo">', '</a>', 32, 10 );
						?>
					</p>
				</div>
			</div>
		</div>

		<div class="return-to-dashboard">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpseo_dashboard' ) ); ?>"><?php _e( 'Go to The General settings page →', 'wordpress-seo' ); ?></a>
		</div>

	</div>

	<div id="credits" class="wpseotab">
		<p class="about-description">
			<?php
			/* translators: %1$s and %2$s expands to anchor tags, %3$s expands to Yoast SEO */
			printf( __( 'While most of the development team is at %1$sYoast%2$s in the Netherlands, %3$s is created by a worldwide team.', 'wordpress-seo' ), '<a target="_blank" href="https://yoast.com/">', '</a>', 'Yoast SEO' );
			echo ' ';
			printf( __( 'Want to help us develop? Read our %1$scontribution guidelines%2$s!', 'wordpress-seo' ), '<a target="_blank" href="http://yoa.st/wpseocontributionguidelines">', '</a>' );
			?>
		</p>

		<h4 class="wp-people-group"><?php _e( 'Project Leaders', 'wordpress-seo' ); ?></h4>
		<ul class="wp-people-group " id="wp-people-group-project-leaders">
			<?php
			$leaders = array(
				'jdevalk'   => (object) array(
					'name'     => 'Joost de Valk',
					'role'     => __( 'Project Lead', 'wordpress-seo' ),
					'gravatar' => 'f08c3c3253bf14b5616b4db53cea6b78',
				),
				'jrfnl'     => (object) array(
					'name'     => 'Juliette Reinders Folmer',
					'role'     => __( 'Lead Developer', 'wordpress-seo' ),
					'gravatar' => 'cbbac3e529102364dc3b026af3cc2988',
				),
				'omarreiss' => (object) array(
					'name'     => 'Omar Reiss',
					'role'     => __( 'Lead Developer', 'wordpress-seo' ),
					'gravatar' => '86aaa606a1904e7e0cf9857a663c376e',
				),
				'tacoverdo' => (object) array(
					'name'     => 'Taco Verdonschot',
					'role'     => __( 'QA & Translations Manager', 'wordpress-seo' ),
					'gravatar' => 'd2d3ecb38cacd521926979b5c678297b',
				),
			);

			wpseo_display_contributors( $leaders );
			?>
		</ul>
		<h4 class="wp-people-group"><?php _e( 'Recent Rockstars', 'wordpress-seo' ); ?></h4>
		<ul class="wp-people-group " id="wp-people-group-rockstars">
			<?php
			$contributors = array(
				'bhubbard'      => (object) array(
					'name'     => 'Brandon Hubbard',
					'role'     => 'New Feature Instigator',
					'gravatar' => '3596404cbc9ffb7d4f48524e08340d86',
				),
				'garyjones'         => (object) array(
					'name'     => 'Gary Jones',
					'role'     => 'Developer, QA & Accessibility',
					'gravatar' => 'f00cf4e7f02e10152f60ec3507fa8ba8',
				),
				'andizer'       => (object) array(
					'name'     => 'Andy Meerwaldt',
					'role'     => 'Developer of the Search Console integration',
					'gravatar' => 'a9b43e766915b48031eab78f9916ca8e',
				),
				'rarst'         => (object) array(
					'name'     => 'Andrey Savchenko',
					'role'     => 'For the 100+ fixes that didn\'t make the about page',
					'gravatar' => 'ab89ce39f47b327f1c85e4019e865a71',
				),
			);

			wpseo_display_contributors( $contributors );
			?>
		</ul>
		<h4 class="wp-people-group"><?php _e( 'Contributing Developers', 'wordpress-seo' ); ?></h4>
		<ul class="wp-people-group " id="wp-people-group-core-developers">
			<?php
			$contributors = array(
				'atimmer'       => (object) array(
					'name'     => 'Anton Timmermans',
					'role'     => __( 'Developer', 'wordpress-seo' ),
					'gravatar' => 'b3acbabfdd208ecbf950d864b86fe968',
				),
				'petervw'       => (object) array(
					'name'     => 'Peter van Wilderen',
					'role'     => __( 'Developer', 'wordpress-seo' ),
					'gravatar' => 'e4662ebd4b59d3c196e2ba721d8a1efc',
				),
				'CarolineGeven' => (object) array(
					'name'     => 'Caroline Geven',
					'role'     => __( 'Developer', 'wordpress-seo' ),
					'gravatar' => 'f2596a568c3974e35f051266a63d791f',
				),
			);

			wpseo_display_contributors( $contributors );
			?>
		</ul>
		<h4 class="wp-people-group"><?php _e( 'Contributors to this release', 'wordpress-seo' ); ?></h4>
		<?php
		$patches_from = array(
			'Pete Nelson'             => 'https://github.com/petenelson',
			'Ajay D\'Souza' => 'https://github.com/ajaydsouza',
			'Filippo Buratti' => 'https://github.com/fburatti',
			'Michael Nordmeyer' => 'https://github.com/michaelnordmeyer',
			'Lars Schenk' => 'https://github.com/larsschenk',
		);
		?>
		<p>We're always grateful for patches from non-regular contributors, in Yoast SEO 2.2 and 2.3, patches from the following people made it in:</p>
		<ul class="ul-square">
			<?php
			foreach ( $patches_from as $patcher => $link ) {
				echo '<li><a href="', esc_url( $link ), '">', $patcher, '</a></li>';
			}
			?>
		</ul>
	</div>
</div>
