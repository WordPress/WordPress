<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var \WpMatomo\Settings $settings */
$matomo_extra_url_params = '&' . http_build_query(
	[
		'php'        => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION,
		'matomo'     => $settings->get_global_option( 'core_version' ),
		'wp_version' => ! empty( $GLOBALS['wp_version'] ) ? $GLOBALS['wp_version'] : '',
	]
);
?>
<div class="wrap">

	<div id="icon-plugins" class="icon32"></div>
	<?php if ( ! empty( $valid_tabs ) ) { ?>
	<h2 class="nav-tab-wrapper">
		<?php if ( in_array( 'marketplace', $valid_tabs, true ) ) { ?>
			<a href="?page=matomo-marketplace&tab=marketplace"
			   class="nav-tab <?php echo ( 'marketplace' === $active_tab ) ? 'nav-tab-active' : ''; ?>"
			><?php esc_html_e( 'Overview', 'matomo' ); ?></a>
		<?php } ?>
		<?php if ( in_array( 'install', $valid_tabs, true ) ) { ?>
			<a href="?page=matomo-marketplace&tab=install"
			   class="nav-tab <?php echo ( 'install' === $active_tab ) ? 'nav-tab-active' : ''; ?>"
			><?php esc_html_e( 'Install Plugins', 'matomo' ); ?></a>
		<?php } ?>
		<?php if ( in_array( 'subscriptions', $valid_tabs, true ) ) { ?>
			<a href="?page=matomo-marketplace&tab=subscriptions"
			   class="nav-tab <?php echo 'subscriptions' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Subscriptions', 'matomo' ); ?></a>
		<?php } ?>
	</h2>
	<?php } ?>

	<?php if ( $settings->is_network_enabled() && ! is_network_admin() && is_super_admin() ) { ?>
		<div class="updated notice">
			<p><?php esc_html_e( 'Only WordPress network admins can see this page', 'matomo' ); ?></p>
		</div>
	<?php } ?>

	<?php
	if ( isset( $marketplace_setup_wizard ) ) {
		$marketplace_setup_wizard->show();
		return;
	}
	?>

	<h1><?php matomo_header_icon(); ?><?php esc_html_e( 'Discover new functionality for your Matomo', 'matomo' ); ?></h1>

	<?php
	function matomo_show_tables( $matomo_feature_sections, $matomo_version ) {
		foreach ( $matomo_feature_sections as $matomo_feature_section ) {
			$matomo_feature_section['features'] = array_filter( $matomo_feature_section['features'] );
			$matomo_num_features_in_block       = count( $matomo_feature_section['features'] );
			$matomo_feature_section_class       = isset( $matomo_feature_section['class'] ) ? $matomo_feature_section['class'] : '';
			$matomo_extra_card_html             = isset( $matomo_feature_section['extra_card_html'] ) ? $matomo_feature_section['extra_card_html'] : '';

			echo '<h2>' . esc_html( $matomo_feature_section['title'] ) . '</h2>';
			echo '<div class="wp-list-table widefat plugin-install matomo-plugin-list matomo-plugin-row-' . esc_html( $matomo_num_features_in_block ) . ' ' . esc_attr( $matomo_feature_section_class ) . '"><div id="the-list">';

			foreach ( $matomo_feature_section['features'] as $matomo_index => $matomo_feature ) {
				$matomo_style        = '';
				$matomo_is_3_columns = 3 === $matomo_num_features_in_block;
				if ( $matomo_is_3_columns ) {
					$matomo_style = 'width: calc(33% - 8px);min-width:282px;max-width:350px;';
					if ( 2 === $matomo_index % 3 ) {
						$matomo_style .= 'clear: inherit;margin-right: 0;margin-left: 16px;';
					}
				}
				$plugin_url = empty( $matomo_feature['url'] ) ? null : $matomo_feature['url'] . '&matomoversion=' . $matomo_version;
				?>
				<div class="plugin-card" style="<?php echo esc_attr( $matomo_style ); ?>">
					<?php
					if ( $matomo_is_3_columns && ! empty( $matomo_feature['image'] ) ) {
						?>
					<a
							href="<?php echo esc_url( $plugin_url ); ?>"
							rel="noreferrer noopener" target="_blank"
							class="thickbox open-plugin-details-modal"><img
								src="<?php echo esc_url( $matomo_feature['image'] ); ?>"
								style="height: 80px;width:100%;object-fit: cover;" alt=""></a>
								<?php
					}
					?>

					<div class="plugin-card-top">
						<div class="
					<?php
					if ( ! $matomo_is_3_columns ) {
						?>
						name column-name
						<?php
					}
					?>
						" style="margin-right: 0;
						<?php
						if ( empty( $matomo_feature['image'] ) ) {
							echo 'margin-left: 0;';
						}
						?>
								">
							<h3>
								<a href="<?php echo esc_url( ! empty( $matomo_feature['video'] ) ? $matomo_feature['video'] : $plugin_url ); ?>"
								   rel="noreferrer noopener" target="_blank"
								   class="thickbox open-plugin-details-modal">
									<?php echo esc_html( $matomo_feature['name'] ); ?>
								</a>
								<?php
								if ( ! $matomo_is_3_columns && ! empty( $matomo_feature['image'] ) ) {
									?>
								<a
										href="<?php echo esc_url( $plugin_url ); ?>"
										rel="noreferrer noopener" target="_blank"
										class="thickbox open-plugin-details-modal"><img
											src="<?php echo esc_url( $matomo_feature['image'] ); ?>" class="plugin-icon"
											style="object-fit: cover;"
											alt=""></a>
											<?php
								}
								?>
							</h3>
						</div>
						<div class="
					<?php
					if ( ! $matomo_is_3_columns ) {
						?>
						desc column-description
						<?php
					}
					?>
						"
							 style="margin-right: 0;
							 <?php
								if ( empty( $matomo_feature['image'] ) ) {
									echo 'margin-left: 0;';
								}
								?>
									 ">
							<p class="matomo-description"><?php echo esc_html( $matomo_feature['description'] ); ?>
								<?php
								if ( ! empty( $matomo_feature['video'] ) ) {
									echo ' <a target="_blank" rel="noreferrer noopener" style="white-space: nowrap;" href="' . esc_url( $matomo_feature['video'] ) . '"><span class="dashicons dashicons-video-alt3"></span> ' . esc_html__( 'Learn more', 'matomo' ) . '</a>';
								} elseif ( ! empty( $matomo_feature['url'] ) ) {
									echo ' <a target="_blank" rel="noreferrer noopener" style="white-space: nowrap;" href="' . esc_url( $plugin_url ) . '">' . esc_html__( 'Learn more', 'matomo' ) . '</a>';
								}
								?>
							</p>
							<?php
							if ( ! empty( $matomo_feature['price'] ) ) {
								?>
								<p class="authors"><a class="button-primary"
													  rel="noreferrer noopener" target="_blank"
													  href="<?php echo esc_url( ! empty( $matomo_feature['download_url'] ) ? $matomo_feature['download_url'] : $plugin_url ); ?>">
									<?php
									if ( 'free' === $matomo_feature['price'] ) {
										esc_html_e( 'Download', 'matomo' );
									} else {
										echo esc_html( $matomo_feature['price'] );
									}
									?>
								</a>
								</p>
								<?php
							}
							?>
						</div>
					</div>
					<?php
						// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $matomo_extra_card_html;
					?>
				</div>
				<?php
			}
			echo '';
			echo '</div><div style="clear: both"></div>';
			if ( ! empty( $matomo_feature_section['more_url'] ) ) {
				echo '<a target="_blank" rel="noreferrer noopener" href="' . esc_attr( $matomo_feature_section['more_url'] ) . '"><span class="dashicons dashicons-arrow-right-alt2"></span>' . esc_html( $matomo_feature_section['more_text'] ) . '</a>';
			}
			echo '</div>';
		}
	}

	$matomo_feature_sections = [
		[
			'title'           => 'What\'s New',
			'class'           => 'matomo-new-plugins',
			'extra_card_html' => '<span class="matomo-new-marker">' . esc_html__( 'New!', 'matomo' ) . '</span>',
			'features'        =>
				[
					[
						'name'        => 'Crash Analytics',
						'description' => 'Detect crashes to improve the user experience, increase conversions and recover revenue. Resolve them with insights to minimise developer hours.',
						'price'       => '69EUR / 79USD',
						'url'         => 'https://plugins.matomo.org/CrashAnalytics?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
				],
		],
		[
			'title'     => 'Top free plugins',
			'more_url'  => 'https://plugins.matomo.org/free?wp=1&pk_campaign=WP&pk_source=Plugin',
			'more_text' => 'Browse all free plugins',
			'features'  =>
				[
					[
						'name'         => 'Marketing Campaigns Reporting',
						'description'  => 'Measure the effectiveness of your marketing campaigns. Track up to five channels instead of two: campaign, source, medium, keyword, content.',
						'price'        => 'free',
						'download_url' => 'https://plugins.matomo.org/api/2.0/plugins/MarketingCampaignsReporting/download/latest?wp=1' . $matomo_extra_url_params,
						'url'          => 'https://plugins.matomo.org/MarketingCampaignsReporting?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'        => '',
					],
					[
						'name'         => 'Custom Alerts',
						'description'  => 'Create custom Alerts to be notified of important changes on your website or app!',
						'price'        => 'free',
						'download_url' => 'https://plugins.matomo.org/api/2.0/plugins/CustomAlerts/download/latest?wp=1' . $matomo_extra_url_params,
						'url'          => 'https://plugins.matomo.org/CustomAlerts?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'        => '',
					],
				],
		],
	];

	/** @var \WpMatomo\Settings $settings */
	$matomo_version = $settings->get_matomo_major_version();

	matomo_show_tables( $matomo_feature_sections, $matomo_version );

	echo '<br>';

	$matomo_feature_sections = [
		[
			'title'    => 'Most popular premium features',
			'features' =>
				[
					[
						'name'        => 'Heatmap & Session Recording',
						'description' => 'Truly understand your visitors by seeing where they click, hover, type and scroll. Replay their actions in a video and ultimately increase conversions.',
						'price'       => '99EUR / 119USD',
						'url'         => 'https://plugins.matomo.org/HeatmapSessionRecording?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
					[
						'name'        => 'Custom Reports',
						'description' => 'Pull out the information you need in order to be successful. Develop your custom strategy to meet your individualized goals while saving money & time.',
						'price'       => '99EUR / 119USD',
						'url'         => 'https://plugins.matomo.org/CustomReports?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],

					[
						'name'        => 'Premium Bundle',
						'description' => 'All premium features in one bundle, make the most out of your Matomo for WordPress and enjoy discounts of over 25%!',
						'price'       => '499EUR / 579USD',
						'url'         => 'https://plugins.matomo.org/WpPremiumBundle?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
				],
		],
		[
			'title'    => 'Most popular content engagement',
			'features' =>
				[
					[
						'name'        => 'Form Analytics',
						'description' => 'Increase conversions on your online forms and lose less visitors by learning everything about your users behavior and their pain points on your forms.',
						'price'       => '79EUR / 89USD',
						'url'         => 'https://plugins.matomo.org/FormAnalytics?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
					[
						'name'        => 'Video & Audio Analytics',
						'description' => 'Grow your business with advanced video & audio analytics. Get powerful insights into how your audience watches your videos and listens to your audio.',
						'price'       => '79EUR / 89USD',
						'url'         => 'https://plugins.matomo.org/MediaAnalytics?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
					[
						'name'        => 'Users Flow',
						'description' => 'Users Flow is a visual representation of the most popular paths your users take through your website & app which lets you understand your users needs.',
						'price'       => '39EUR / 39USD',
						'url'         => 'https://plugins.matomo.org/UsersFlow?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
				],
		],
		[
			'title'    => 'Most popular acquisition & SEO features',
			'features' =>
				[
					[
						'name'        => 'Search Engine Keywords Performance',
						'description' => 'All keywords searched by your users on search engines are now visible into your Referrers reports! The ultimate solution to \'Keyword not defined\'.',
						'price'       => '69EUR / 79USD',
						'url'         => 'https://plugins.matomo.org/SearchEngineKeywordsPerformance?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
					[
						'name'        => 'SEO Web Vitals',
						'description' => 'Improve your website performance, rank higher in search results and optimise your visitor experience with SEO Web Vitals.',
						'price'       => '39EUR / 39USD',
						'url'         => 'https://plugins.matomo.org/SEOWebVitals?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
				],
		],
		[
			'title'    => '',
			'features' =>
				[
					[
						'name'        => 'Advertising Conversion Export',
						'description' => 'Provides an export of attributed goal conversions for usage in ad networks like Google Ads so you no longer need a conversion pixel.',
						'price'       => '79EUR / 89USD',
						'url'         => 'https://plugins.matomo.org/AdvertisingConversionExport?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
					[
						'name'        => 'Multi Attribution',
						'description' => 'Get a clear understanding of how much credit each of your marketing channel is actually responsible for to shift your marketing efforts wisely.',
						'price'       => '39EUR / 39USD',
						'url'         => 'https://plugins.matomo.org/MultiChannelConversionAttribution?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
				],
		],
		[
			'title'    => 'Other premium features',
			'features' =>
				[
					[
						'name'        => 'Funnels',
						'description' => 'Identify and understand where your visitors drop off to increase your conversions, sales and revenue with your existing traffic.',
						'price'       => '89EUR / 99USD',
						'url'         => 'https://plugins.matomo.org/Funnels?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
					[
						'name'        => 'Cohorts',
						'description' => 'Track your retention efforts over time and keep your visitors engaged and coming back for more.',
						'price'       => '49EUR / 59USD',
						'url'         => 'https://plugins.matomo.org/Cohorts?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
					[
						'name'        => 'Crash Analytics',
						'description' => 'Detect crashes to improve the user experience, increase conversions and recover revenue. Resolve them with insights to minimise developer hours.',
						'price'       => '69EUR / 79USD',
						'url'         => 'https://plugins.matomo.org/CrashAnalytics?wp=1&pk_campaign=WP&pk_source=Plugin',
						'image'       => '',
					],
				],
		],
	];

	matomo_show_tables( $matomo_feature_sections, $matomo_version );

	?>

</div>
