<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 * Code Based on
 * @author Andr&eacute; Br&auml;kling
 * https://github.com/braekling/WP-Matomo
 *
 */
/**
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */
use WpMatomo\Admin\TrackingSettings;
use WpMatomo\Paths;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var \WpMatomo\Settings $settings */
/** @var bool $was_updated */
/** @var array $matomo_default_tracking_code */
/** @var array $containers */
/** @var array $track_modes */
/** @var array $matomo_currencies */
/** @var string[] $settings_errors */
/** @var array $cookie_consent_modes */
/** @var string $matomo_exclusion_settings_url */
/** @var array $matomo_track_mode_descriptions $matomo_form */

$matomo_form  = new \WpMatomo\Admin\TrackingSettings\Forms( $settings );
$matomo_paths = new Paths();
?>

<?php
if ( $was_updated ) {
	include 'update_notice_clear_cache.php';
}
if ( count( $settings_errors ) ) {
	include 'settings_errors.php';
}

?>
<?php
$track_mode             = $settings->get_global_option( 'track_mode' );
$matomo_is_not_tracking = TrackingSettings::TRACK_MODE_DISABLED === $track_mode;

$matomo_is_not_generated_tracking     = $matomo_is_not_tracking || $settings->get_global_option( 'track_mode' ) === TrackingSettings::TRACK_MODE_MANUALLY;
$matomo_full_generated_tracking_group = 'matomo-track-option matomo-track-option-default  ';

$matomo_manually_network = '';
if ( $settings->is_network_enabled() ) {
	$matomo_manually_network = ' ' . sprintf( esc_html__( 'You can use these variables: %1$s. %2$sLearn more%3$s', 'matomo' ), '{MATOMO_IDSITE}, {MATOMO_API_ENDPOINT}, {MATOMO_JS_ENDPOINT}', '<a href="https://matomo.org/faq/wordpress/how-can-i-configure-the-tracking-code-manually-when-i-have-wordpress-network-enabled-in-multisite-mode/" target="_blank" rel="noreferrer noopener">', '</a>' );
}

$matomo_submit_button = '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . esc_attr__( 'Save Changes', 'matomo' ) . '" /></p>';
?>
<style>
	.post-types {
		display: flex;
		flex-direction: row;
		flex-wrap: wrap;
		align-items: center;
	}

	.post-types > * {
		margin-right: 8px;
	}

	.collapsible-settings > h2 {
		cursor: pointer;
	}

	.collapsible-settings:not(.expanded) > *:not(h2) {
		display: none;
	}

	.collapsible-settings > h2::before {
		display: inline-block;
		content: "›";
		margin-right: 4px;
		margin-left: 4px;
	}

	.collapsible-settings.expanded > h2::before {
		transform: rotate(90deg);
		margin-right: 6px;
	}

	#tracking-settings .widefat td p.description {
		margin-bottom: 0;
	}

	label[for="tagmanger_container_ids"] {
		display: block;
		margin-bottom: 1em;
	}

	#matomo-tagmanager-container-select {
		display: none;
	}

	#tracking-settings[data-track-mode="tagmanager"] #matomo-tagmanager-container-select {
		display: table-row;
	}

	#tagmanager-read-more-link {
		margin-left: 1.5em;
	}

	#tracking-settings .inactive-notice {
		display: none;
		text-transform: uppercase;
		font-weight: 400;
		font-size: 10px;
		margin-bottom: 2px;
	}

	#tracking-settings:not([data-track-mode="manually"]) #manual-tracking-settings > h2 {
		color: #888;
	}

	#tracking-settings:not([data-track-mode="default"]) #auto-tracking-settings > h2 {
		color: #888;
	}

	#tracking-settings:not([data-track-mode="manually"]) #manual-tracking-settings .inactive-notice {
		display: inline-block;
	}

	#tracking-settings:not([data-track-mode="default"]) #auto-tracking-settings .inactive-notice {
		display: inline-block;
	}

	#tracking-settings:not([data-track-mode="tagmanager"]) .tagmanager-container-select {
		display: none;
	}
</style>
<script>
	window.jQuery(document).ready(function ($) {
		// even spacing between post types
		function setPostTypeSpacing() {
			var postTypes = $('.post-types > div:visible');
			if (postTypes.length) {
				var postLengths = postTypes.toArray().map(function (e) {
					return $(e).width();
				});

				var maxPostLength = Math.max.apply(null, postLengths);
				postTypes.each(function () {
					$(this).width(maxPostLength);
				});
			}
		}

		$('#tracking-settings').on('click', '.collapsible-settings h2', function (e) {
			$(e.target).closest('.collapsible-settings').toggleClass('expanded');
			setPostTypeSpacing();
		});

		function onTrackModeChange() {
			var currentTrackMode = $('input[name="matomo[track_mode]"]:checked').val();

			// set the current track mode as an attribute on the <form> for css
			$(this)
				.closest('form')
				.attr('data-track-mode', currentTrackMode);

			// mark setting sections that do not apply to the currently selected track mode
			// as "inactive"
			$('h2[data-inactive-title]')
				.removeAttr('title')
				.each(function () {
					if (currentTrackMode !== $(this).closest('[data-settings-for]').attr('data-settings-for')) {
						$(this).attr('title', $(this).attr('data-inactive-title'));
					}
				});

			// if a settings section for the currently selected track mode, auto expand it, and
			// auto collapse other sections
			$('[data-settings-for]')
				.removeClass('expanded')
				.each(function () {
					$(this).toggleClass('expanded', $(this).attr('data-settings-for') === currentTrackMode);
				});
		}

		$('#track_mode').on('change', onTrackModeChange);
		onTrackModeChange();
	});
</script>
<form id="tracking-settings" method="post" data-track-mode="<?php echo esc_attr( $track_mode ); ?>" action="#">
	<?php wp_nonce_field( TrackingSettings::NONCE_NAME ); ?>
	<p>
		<?php esc_html_e( 'Here you can configure visit tracking to your liking. Alternatively, you can simply enable tracking, and just rely on the default settings.', 'matomo' ); ?>
	</p>

	<table class="matomo-tracking-form widefat">
		<tbody>
		<?php
		$matomo_form->show_radio(
			'track_mode',
			esc_html__( 'Tracking mode', 'matomo' ),
			$track_modes,
			$matomo_track_mode_descriptions,
			null,
			false,
			'track_mode'
		);
		?>
		</tbody>
	</table>

	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $matomo_submit_button;
	?>

	<hr/>

	<div id="general-settings" class="collapsible-settings">
		<h2><?php esc_html_e( 'General Tracking Settings', 'matomo' ); ?></h2>
		<p><?php esc_html_e( 'These settings apply to all tracking modes (unless otherwise stated).', 'matomo' ); ?></p>

		<h4><?php esc_html_e( 'Ecommerce Tracking', 'matomo' ); ?></h4>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			$matomo_form->show_checkbox(
				'track_ecommerce',
				esc_html__( 'Enable ecommerce', 'matomo' ),
				esc_html__( 'Enable to track Ecommerce orders, abandoned carts and product views for WooCommerce, Easy Digital Downloads, MemberPress, and more. Disabling this feature will stop tracking ecommerce data and remove Ecommerce reports from the Matomo display.', 'matomo' ),
				false,
				$matomo_full_generated_tracking_group . ' matomo-track-option-manually matomo-track-option-tagmanager'
			);

			$matomo_form->show_select(
				\WpMatomo\Settings::SITE_CURRENCY,
				esc_html__( 'Currency', 'matomo' ),
				$matomo_currencies,
				esc_html__( 'Choose the currency which will be used in reports. This currency will be used if you have an ecommerce store or if you are using the Matomo goals feature and assign a monetary value to a goal.', 'matomo' ),
				''
			);

			$matomo_server_side_visitor_id_desc =
				esc_html__( 'When enabled Matomo will force the use of a temporary server side generated ID if a Visitor ID cookie is not found. This allows ecommerce events to be properly attributed to visits when cookies are blocked or cookieless tracking is used. The visitor ID will only be valid for the duration of a single visit.', 'matomo' )
				. '<br/><br/>'
				. esc_html__( 'Note that using this setting may not be allowed by your local privacy regulations, as some regulations require consent to be given before using a visitor ID and/or tracking ecommerce events.', 'matomo' )
				. '<br/><br/>'
				. esc_html__( 'Also note: this setting will not work if you are using using Varnish or another caching mechanism as it starts the WooCommerce session early for visitors that do not have the visitor ID cookie.', 'matomo' );

			$matomo_form->show_checkbox(
				\WpMatomo\Settings::USE_SESSION_VISITOR_ID_OPTION_NAME,
				esc_html__( 'Use server generated Visitor ID', 'matomo' ),
				$matomo_server_side_visitor_id_desc,
				false,
				$matomo_full_generated_tracking_group . ' matomo-track-option-manually matomo-track-option-tagmanager',
				false,
				'',
				false
			);
			?>
			</tbody>
		</table>

		<h4><?php esc_html_e( 'Other Tracking', 'matomo' ); ?></h4>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			$matomo_form->show_checkbox(
				'track_admin',
				esc_html__( 'Track admin pages', 'matomo' ),
				sprintf(
					esc_html__( 'Enable to track users on WordPress admin pages (%1$sremember to configure the tracking filter appropriately%2$s).', 'matomo' ),
					'<a href="' . esc_attr( $matomo_exclusion_settings_url ) . '" />',
					'</a>'
				),
				false,
				$matomo_full_generated_tracking_group . ' matomo-track-option-manually matomo-track-option-tagmanager'
			);

			$matomo_form->show_select(
				'track_user_id',
				__( 'Track WordPress User ID', 'matomo' ),
				[
					'disabled'    => esc_html__( 'Disabled', 'matomo' ),
					'uid'         => esc_html__( 'WP User ID', 'matomo' ),
					'email'       => esc_html__( 'Email Address', 'matomo' ),
					'username'    => esc_html__( 'Username', 'matomo' ),
					'displayname' => esc_html__( 'Display Name (Not Recommended!)', 'matomo' ),
				],
				__( 'When a user is logged in to WordPress, track their &quot;User ID&quot;. You can select which field from the User\'s profile is tracked as the &quot;User ID&quot;.', 'matomo' ),
				'',
				false,
				$matomo_full_generated_tracking_group . ' matomo-track-option-tagmanager'
			);

			$matomo_form->show_checkbox(
				'track_search',
				esc_html__( 'Track search', 'matomo' ),
				esc_html__( 'Enable Matomo\'s advanced Site Search Analytics feature.', 'matomo' ) . ' ' .
				sprintf(
					esc_html__( 'See %1$sMatomo documentation%2$s.', 'matomo' ),
					'<a href="https://matomo.org/faq/reports/tracking-site-search-keywords/#track-site-search-using-the-tracking-api-advanced-users-only" rel="noreferrer noopener" target="_BLANK">',
					'</a>'
				),
				false,
				$matomo_full_generated_tracking_group . ' matomo-track-option-manually matomo-track-option-tagmanager'
			);

			$matomo_form->show_checkbox(
				'track_404',
				esc_html__( 'Track 404', 'matomo' ),
				esc_html__( 'Matomo can automatically add a 404-category to track 404-page-visits.', 'matomo' ) .
				' ' . sprintf(
					esc_html__( 'See %1$sMatomo FAQ%2$s.', 'matomo' ),
					'<a href="https://matomo.org/faq/how-to/faq_60/" rel="noreferrer noopener" target="_BLANK">',
					'</a>'
				),
				false,
				$matomo_full_generated_tracking_group . ' matomo-track-option-manually'
			);

			$matomo_form->show_checkbox(
				'track_feed',
				esc_html__( 'Track RSS feeds', 'matomo' ),
				esc_html__( 'Enable to use a tracking pixel to track visitors that view posts in RSS feed viewers.', 'matomo' ),
				false,
				$matomo_full_generated_tracking_group . ' matomo-track-option-manually matomo-track-option-tagmanager'
			);

			$matomo_form->show_checkbox(
				'track_feed_addcampaign',
				esc_html__( 'Track RSS feed links as campaign', 'matomo' ),
				esc_html__( 'This will add Matomo campaign parameters to RSS feed links.', 'matomo' ) . ' ' . sprintf( esc_html__( 'See %1$sMatomo documentation%2$s.', 'matomo' ), '<a href="https://matomo.org/docs/tracking-campaigns/" rel="noreferrer noopener" target="_BLANK">', '</a>' ),
				false,
				$matomo_full_generated_tracking_group . ' matomo-track-option-manually matomo-track-option-tagmanager',
				false,
				'jQuery(\'tr.matomo-feed_campaign-option\').toggle(\'hidden\');'
			);

			$matomo_form->show_input(
				'track_feed_campaign',
				esc_html__( 'RSS feed campaign name', 'matomo' ),
				esc_html__( 'The campaign name to use if RSS feed links are tracked as a campaign.', 'matomo' ),
				true,
				$matomo_full_generated_tracking_group . ' matomo-feed_campaign-option matomo-track-option-tagmanager'
			);
			?>
			</tbody>
		</table>

		<h4><?php esc_html_e( 'Create Matomo annotations on', 'matomo' ); ?></h4>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			echo '<tr class="' . esc_attr( $matomo_full_generated_tracking_group ) . ' matomo-track-option-manually">';
			echo '<th scope="row"><label for="add_post_annotations">' . esc_html__( 'On new post of type', 'matomo' ) . ':</label></th><td>';
			echo '<div class="post-types">';
			$matomo_filter = $settings->get_global_option( 'add_post_annotations' );
			foreach ( get_post_types( [], 'objects' ) as $object_post_type ) {
				echo '<div><input type="checkbox" ' . ( isset( $matomo_filter [ $object_post_type->name ] ) && $matomo_filter [ $object_post_type->name ] ? 'checked="checked" ' : '' ) . 'value="1" name="matomo[add_post_annotations][' . esc_attr( $object_post_type->name ) . ']" /> <span>' . esc_html( $object_post_type->label ) . '</span></div>';
			}
			echo '</div>';
			echo '<p class="description" id="add_post_annotations-desc">' . sprintf( esc_html__( 'See %1$sMatomo documentation%2$s.', 'matomo' ), '<a href="https://matomo.org/docs/annotations/" rel="noreferrer noopener" target="_BLANK">', '</a>' ) . '</p></td></tr>';
			?>
			</tbody>
		</table>

		<h4><?php esc_html_e( 'Advanced', 'matomo' ); ?></h4>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			$matomo_form->show_checkbox(
				'track_noscript',
				__( 'Add &lt;noscript&gt; to track visitors who disable JavaScript', 'matomo' ),
				__( 'Adds the &lt;noscript&gt; code to your footer. This code is either generated automatically or defined by you, based on which tracking mode you choose use. This can be useful if you have a lot of visitors that have JavaScript disabled.', 'matomo' )
				. '<br/><br/><em>' . esc_html__( 'This setting does not apply to the Tag Manager tracking mode.', 'matomo' ) . '</em>',
				false,
				'matomo-track-option matomo-track-option-default  matomo-track-option-manually'
			);
			?>
			</tbody>
		</table>
	</div>

	<hr/>

	<div id="auto-tracking-settings" class="collapsible-settings" data-settings-for="default">
		<h2 data-inactive-title="<?php esc_attr_e( 'Note: these settings will only apply if the Auto tracking mode is active.', 'matomo' ); ?>">
			<?php esc_html_e( 'Settings for Auto Tracking mode', 'matomo' ); ?>
			<span class="inactive-notice">(<?php esc_html_e( 'Inactive', 'matomo' ); ?>)</span>
		</h2>
		<p><?php esc_html_e( 'The Auto tracking mode automatically generates and embeds the Matomo tracking JavaScript based on the settings below. Pick and choose what you\'d like to track, and Matomo for WordPress will set everything else up for you.', 'matomo' ); ?></p>
		<p id="showGeneratedTrackingCode"><a href="#"><?php esc_html_e( 'Show generated tracking code', 'matomo' ); ?></a></p>
		<p id="hideGeneratedTrackingCode" style="display:none;"><a href="#"><?php esc_html_e( 'Hide generated tracking code', 'matomo' ); ?></a></p>
		<table class="matomo-tracking-form widefat" id="generatedTrackingCode" style="display:none;margin-bottom:1em;">
			<?php
			$matomo_form->show_textarea(
				'generated_tracking_code',
				esc_html__( 'Tracking code', 'matomo' ),
				15,
				sprintf(
					esc_html__( 'This is a preview of the tracking code generated based on the configuration below. You don\'t need to do anything with it and this is purely for your information. The tracking code is a piece of code that will be automatically embedded into your site and send information about your visitors to Matomo. Have a look at the system report to get a list of all available JS tracker and tracking API endpoints. Our plugin will automatically embed this code into your website. %s', 'matomo' ),
					$matomo_manually_network
				),
				false,
				'',
				false,
				'',
				true,
				false,
				true
			);

			$matomo_form->show_textarea(
				'generated_noscript_code',
				esc_html__( '<noscript> code', 'matomo' ),
				2,
				__( 'This is a preview of your &lt;noscript&gt; code which is part of your tracking code. This code will only be embedded into your website if the noscript feature is enabled.', 'matomo' ),
				false,
				'',
				false,
				'',
				true,
				false,
				true
			);
			?>
		</table>
		<script>
			jQuery('#showGeneratedTrackingCode,#hideGeneratedTrackingCode').on('click', 'a', function (e) {
				e.preventDefault();
				e.stopPropagation();
				jQuery('#generatedTrackingCode,#showGeneratedTrackingCode,#hideGeneratedTrackingCode').toggle();
			});
		</script>

		<h4><?php esc_html_e( 'Privacy', 'matomo' ); ?></h4>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			$matomo_form->show_checkbox(
				'disable_cookies',
				esc_html__( 'Disable cookies', 'matomo' ),
				esc_html__( 'Disable the use of tracking cookies entirely. Using this setting may make it easier to achieve privacy compliance, but Matomo will not be able to recognize returning visitors as effectively.', 'matomo' ),
				false,
				$matomo_full_generated_tracking_group
			);

			$matomo_form->show_checkbox(
				'limit_cookies',
				esc_html__( 'Limit cookie lifetime', 'matomo' ),
				esc_html__( 'If necessary, you can limit the cookie lifetime to avoid tracking your users over a longer period.', 'matomo' ),
				false,
				$matomo_full_generated_tracking_group,
				false,
				'jQuery(\'tr.matomo-cookielifetime-option\').toggleClass(\'hidden\');'
			);

			$matomo_form->show_input(
				'limit_cookies_visitor',
				esc_html__( 'Visitor timeout (seconds)', 'matomo' ),
				false,
				! $settings->get_global_option( 'limit_cookies' ),
				$matomo_full_generated_tracking_group . ' matomo-cookielifetime-option'
			);

			$matomo_form->show_input(
				'limit_cookies_session',
				esc_html__( 'Session timeout (seconds)', 'matomo' ),
				false,
				! $settings->get_global_option( 'limit_cookies' ),
				$matomo_full_generated_tracking_group . ' matomo-cookielifetime-option'
			);

			$matomo_form->show_input(
				'limit_cookies_referral',
				esc_html__( 'Referral timeout (seconds)', 'matomo' ),
				false,
				! $settings->get_global_option( 'limit_cookies' ),
				$matomo_full_generated_tracking_group . ' matomo-cookielifetime-option'
			);
			?>
			</tbody>
		</table>

		<h4><?php esc_html_e( 'Subdomains', 'matomo' ); ?></h4>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			$matomo_form->show_checkbox(
				'track_across',
				esc_html__( 'Track subdomains in the same website', 'matomo' ),
				esc_html__( 'Track visitors across subdomains by adding a *.-prefix to the cookie domain.', 'matomo' ) . ' ' .
				sprintf(
					esc_html__( 'See %1$sMatomo documentation%2$s.', 'matomo' ),
					'<a href="https://developer.matomo.org/guides/tracking-javascript-guide#tracking-subdomains-in-the-same-website" rel="noreferrer noopener" target="_BLANK">',
					'</a>'
				),
				false,
				$matomo_full_generated_tracking_group
			);

			$matomo_form->show_checkbox(
				'track_across_alias',
				esc_html__( 'Do not count subdomains as outlink', 'matomo' ),
				esc_html__( 'Treats all subdomains as part of the same website, by adding a *.-prefix to the tracked domain.', 'matomo' ) . ' ' .
				sprintf(
					esc_html__( 'See %1$sMatomo documentation%2$s.', 'matomo' ),
					'<a href="https://developer.matomo.org/guides/tracking-javascript-guide#outlink-tracking-exclusions" rel="noreferrer noopener" target="_BLANK">',
					'</a>'
				),
				false,
				$matomo_full_generated_tracking_group
			);

			$matomo_form->show_checkbox(
				'track_crossdomain_linking',
				esc_html__( 'Enable cross domain linking', 'matomo' ),
				esc_html__( 'When enabled, it will make sure to use the same visitor ID for the same visitor across several domains. This only works when this feature is enabled because the visitor ID is stored in a cookie and cookies cannot be read when on a different domain. When this feature is enabled, it will append a URL parameter "pk_vid" that contains the visitor ID when a user clicks on a URL that belongs to one of your domains. For this feature to work, you also have to configure which domains should be treated as local in your Matomo website settings.', 'matomo' ),
				false,
				$matomo_full_generated_tracking_group
			);
			?>
			</tbody>
		</table>

		<h4><?php esc_html_e( 'Link Tracking', 'matomo' ); ?></h4>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			$matomo_form->show_input(
				'add_download_extensions',
				esc_html__( 'Add new file types for download tracking', 'matomo' ),
				sprintf(
					esc_html__( 'Matomo automatically detects file downloads if the extension of the file is in %1$sthis list%2$s. If you\'d like to track other file types as downloads, you can add their extensions here, divided by a vertical bar (&#124;).', 'matomo' ),
					'<a href="https://developer.matomo.org/guides/tracking-javascript-guide#tracking-file-downloads" rel="noreferrer noopener" target="_BLANK">',
					'</a>'
				),
				false,
				$matomo_full_generated_tracking_group
			);

			$matomo_form->show_input(
				'set_download_extensions',
				esc_html__( 'Define all file types for download tracking', 'matomo' ),
				esc_html__( 'Replace Matomo\'s default file extensions for download tracking, divided by a vertical bar (&#124;). Leave blank to keep Matomo\'s default settings (recommended).', 'matomo' ) . ' ' .
				sprintf(
					esc_html__( 'See %1$sMatomo documentation%2$s.', 'matomo' ),
					'<a href="https://developer.matomo.org/guides/tracking-javascript-guide#file-extensions-for-tracking-downloads" target="_BLANK">',
					'</a>'
				),
				false,
				$matomo_full_generated_tracking_group
			);

			$matomo_form->show_input(
				'set_download_classes',
				esc_html__( 'Set CSS classes to be treated as downloads', 'matomo' ),
				esc_html__( 'Set the CSS classes of links that, when clicked, should be tracked as downloads (in addition to \'piwik_download\'). Enter the list of CSS classes divided by a vertical bar (&#124;). Leave blank to keep Matomo\'s default settings.', 'matomo' ) . ' ' .
				sprintf(
					esc_html__( 'See %1$sMatomo JavaScript Tracking Client reference%2$s.', 'matomo' ),
					'<a href="https://developer.matomo.org/api-reference/tracking-javascript" target="_BLANK">',
					'</a>'
				),
				false,
				$matomo_full_generated_tracking_group
			);

			$matomo_form->show_input(
				'set_link_classes',
				esc_html__( 'Set CSS classes to be treated as outlinks', 'matomo' ),
				esc_html__( 'Set CSS classes of links that, when clicked, should be treated as outlinks (in addition to piwik_link), divided by a vertical bar (&#124;). Leave blank to keep Matomo\'s default settings.', 'matomo' ) . ' ' .
				sprintf(
					esc_html__( 'See %1$sMatomo JavaScript Tracking Client reference%2$s.', 'matomo' ),
					'<a href="https://developer.matomo.org/api-reference/tracking-javascript" target="_BLANK">',
					'</a>'
				),
				false,
				$matomo_full_generated_tracking_group
			);
			?>
			</tbody>
		</table>

		<h4><?php esc_html_e( 'Other Tracking', 'matomo' ); ?></h4>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			$matomo_form->show_checkbox(
				'track_jserrors',
				esc_html__( 'Track JS errors', 'matomo' ),
				esc_html__( 'Enable to track JavaScript errors that occur on your website as Matomo events.', 'matomo' )
				. ' ' . sprintf( esc_html__( 'See %1$sMatomo FAQ%2$s.', 'matomo' ), '<a href="https://matomo.org/faq/how-to/how-do-i-enable-basic-javascript-error-tracking-and-reporting-in-matomo-browser-console-error-messages/" rel="noreferrer noopener" target="_BLANK">', '</a>' )
				. ' ' . sprintf( esc_html__( 'For more advanced reporting of crashes, check out our %1$sCrash Analytics premium feature%2$s.', 'matomo' ), '<a href="https://plugins.matomo.org/CrashAnalytics?wp=1" target="_blank" rel="noreferrer noopener">', '</a>' ),
				false,
				$matomo_full_generated_tracking_group
			);

			$matomo_form->show_select(
				'track_content',
				__( 'Enable content tracking', 'matomo' ),
				[
					'disabled' => esc_html__( 'Disabled', 'matomo' ),
					'all'      => esc_html__( 'Track all content blocks', 'matomo' ),
					'visible'  => esc_html__( 'Track only visible content blocks', 'matomo' ),
				],
				__( 'Content tracking allows you to track interactions with pieces of content within your website.', 'matomo' ) . ' ' . sprintf( esc_html__( 'See %1$sMatomo documentation%2$s.', 'matomo' ), '<a href="https://developer.matomo.org/guides/content-tracking" rel="noreferrer noopener" target="_BLANK">', '</a>' ),
				'',
				false,
				$matomo_full_generated_tracking_group
			);

			$matomo_form->show_input(
				'track_heartbeat',
				esc_html__( 'Enable heartbeat timer (enable with care)', 'matomo' ),
				__( 'Enable a heartbeat timer to get more accurate visit lengths by sending periodic HTTP ping requests while a visitor is viewing your website. Enter the time between the pings in seconds (Matomo default: 15) to enable or 0 to disable this feature. <strong>Note:</strong> This will multiply the HTTP requests your website receives, which may cause performance issues based on your infrastructure and website traffic. Enable this setting with care.', 'matomo' ),
				false,
				$matomo_full_generated_tracking_group
			);
			?>
			</tbody>
		</table>

		<h4><?php esc_html_e( 'Advanced', 'matomo' ); ?></h4>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			$matomo_form->show_checkbox(
				'force_post',
				esc_html__( 'Force POST requests', 'matomo' ),
				esc_html__( 'When enabled, Matomo will always use POST requests. This can be helpful should you experience HTTP 414 URI too long errors in your tracking code.', 'matomo' ),
				false,
				$matomo_full_generated_tracking_group
			);

			$matomo_form->show_select(
				'cookie_consent',
				esc_html__( 'Custom consent screen', 'matomo' ),
				$cookie_consent_modes,
				sprintf(
					esc_html__( 'Activates a specific Matomo consent mode. Only configure a consent mode if you are implementing a consent screen yourself. This requires a custom consent implementation. For more information please read this %1$sFAQ%2$s (this option will take care of step 1 for you). By default no consent mode is applied.', 'matomo' ),
					'<a href="https://developer.matomo.org/guides/tracking-consent" rel="noreferrer noopener" target="_blank">',
					'</a>'
				),
				'',
				false,
				$matomo_full_generated_tracking_group
			);

			$matomo_form->show_select(
				'track_api_endpoint',
				__( 'Endpoint for HTTP Tracking API', 'matomo' ),
				[
					'default' => esc_html__( 'Default', 'matomo' ),
					'restapi' => esc_html__( 'Through WordPress Rest API', 'matomo' ),
				],
				sprintf( __( 'By default the HTTP Tracking API points to your Matomo plugin directory "%1$s". You can choose to use the WP Rest API (%2$s) instead for example to hide matomo.php or if the other URL doesn\'t work for you. Note: If the "Tag Manager" tracking mode is selected, then this URL will only be used in feed tracking.', 'matomo' ), esc_html( $matomo_paths->get_tracker_api_url_in_matomo_dir() ), esc_html( $matomo_paths->get_tracker_api_rest_api_endpoint() ) ),
				'',
				false,
				$matomo_full_generated_tracking_group . ' matomo-track-option-manually matomo-track-option-tagmanager'
			);

			$matomo_form->show_select(
				'track_js_endpoint',
				__( 'Endpoint for JavaScript tracker', 'matomo' ),
				[
					'default' => esc_html__( 'Default', 'matomo' ),
					'restapi' => esc_html__( 'Through WordPress Rest API (slower)', 'matomo' ),
					'plugin'  => esc_html__( 'Plugin (an alternative JS file if the default is blocked by the webserver)', 'matomo' ),
				],
				sprintf( __( 'By default the JS tracking code will be loaded from "%1$s". You can choose to serve the JS file through the WP Rest API (%2$s) for example to hide matomo.js. Please note that this means every request to the JavaScript file will launch WordPress PHP and therefore will be slower compared to your webserver serving the JS file directly. Using the "Plugin" method will cause issues with our paid Heatmap and Session Recording, Form Analytics, and Media Analytics plugin.', 'matomo' ), esc_html( $matomo_paths->get_js_tracker_url_in_matomo_dir() ), esc_html( $matomo_paths->get_js_tracker_rest_api_endpoint() ) ),
				'',
				false,
				$matomo_full_generated_tracking_group
			);
			?>
			</tbody>
		</table>
	</div>

	<hr/>

	<div id="manual-tracking-settings" class="collapsible-settings" data-settings-for="manually">
		<h2 data-inactive-title="<?php esc_attr_e( 'Note: these settings will only apply if the Manual tracking mode is active.', 'matomo' ); ?>">
			<?php esc_html_e( 'Settings for Manual Tracking mode', 'matomo' ); ?>
			<span class="inactive-notice">(<?php esc_html_e( 'Inactive', 'matomo' ); ?>)</span>
		</h2>
		<p><?php esc_html_e( 'With the Manual tracking mode, you can write the JavaScript tracking code yourself, customizing it any way you need to. Matomo for WordPress will then embed this script into your website\'s HTML.', 'matomo' ); ?></p>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			$matomo_form->show_textarea(
				'tracking_code',
				esc_html__( 'Tracking code', 'matomo' ),
				15,
				sprintf(
					esc_html__( 'Enter your tracking code here. If you need a starting point, you can use the Auto tracking mode settings and view the generated code in the above section. Have a look at the system report to get a list of all available JS tracker and tracking API endpoints. Note: all you need to do is define the code here. You don\'t need to embed it into your website, our plugin does this automatically. %s', 'matomo' ),
					$matomo_manually_network
				),
				false,
				'matomo-track-option matomo-track-option-default matomo-track-option-tagmanager  matomo-track-option-manually',
				! $settings->is_network_enabled(),
				'',
				false,
				false
			);

			$matomo_form->show_textarea(
				'noscript_code',
				esc_html__( 'Noscript code', 'matomo' ),
				2,
				__( 'Enter your custom &lt;noscript&gt; code here. This will only show if the noscript feature is enabled.', 'matomo' ),
				false,
				'matomo-track-option matomo-track-option-default  matomo-track-option-manually',
				true,
				'',
				false,
				false
			);
			?>
			</tbody>
		</table>
	</div>

	<hr/>

	<div id="html-tag-settings" class="collapsible-settings">
		<h2><?php esc_html_e( '<script> Tag Settings', 'matomo' ); ?></h2>
		<p><?php esc_html_e( 'These options are available for most tracking modes and control exactly how Matomo for WordPress embeds <script> elements into your pages. Most users will not need to change these settings.', 'matomo' ); ?></p>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			$matomo_form->show_select(
				'force_protocol',
				__( 'Force Matomo to use a specific protocol', 'matomo' ),
				[
					'disabled' => esc_html__( 'Disabled (default)', 'matomo' ),
					'https'    => esc_html__( 'https (SSL)', 'matomo' ),
				],
				__( 'Choose if you want to force Matomo to use HTTP or HTTPS.', 'matomo' )
				. '<br/><br/><em>' . esc_html__( 'This setting does not apply to the Manual tracking mode.', 'matomo' ) . '</em>',
				'',
				false,
				$matomo_full_generated_tracking_group . ' matomo-track-option-tagmanager'
			);
			$matomo_form->show_select(
				'track_codeposition',
				__( 'JavaScript code position', 'matomo' ),
				[
					'footer' => esc_html__( 'Footer', 'matomo' ),
					'header' => esc_html__( 'Header', 'matomo' ),
				],
				__( 'Choose whether the JavaScript code is added to the footer or the header.', 'matomo' ),
				'',
				false,
				'matomo-track-option matomo-track-option-default  matomo-track-option-tagmanager matomo-track-option-manually'
			);

			$matomo_form->show_checkbox(
				'track_datacfasync',
				esc_html__( 'Add data-cfasync=false', 'matomo' ),
				esc_html__( 'Adds data-cfasync=false to the script tag, e.g., to ask Rocket Loader to ignore the script.', 'matomo' ) . ' ' .
				sprintf(
					esc_html__( 'See %1$sCloudFlare Knowledge Base%2$s.', 'matomo' ),
					'<a href="https://support.cloudflare.com/hc/en-us/articles/200169436-How-can-I-have-Rocket-Loader-ignore-my-script-s-in-Automatic-Mode-" rel="noreferrer noopener" target="_BLANK">',
					'</a>'
				) . '<br/><br/><em>' . esc_html__( 'This setting does not apply to the Manual tracking mode.', 'matomo' ) . '</em>',
				false,
				$matomo_full_generated_tracking_group . '  matomo-track-option-tagmanager'
			);
			?>
			</tbody>
		</table>
	</div>

	<hr/>

	<div id="developer-settings" class="collapsible-settings">
		<h2><?php esc_html_e( 'Developer Settings', 'matomo' ); ?></h2>
		<p><?php esc_html_e( 'If your tracking code is not working as expected, this setting may help you find out why. When enabled, the tracker debug mode will output diagnostic information in tracking requests. It is recommended to only enable it when/if you need it, and disable it immediately after.', 'matomo' ); ?></p>
		<table class="matomo-tracking-form widefat">
			<tbody>
			<?php
			$matomo_form->show_select(
				'tracker_debug',
				__( 'Tracker Debug Mode', 'matomo' ),
				[
					'disabled'  => esc_html__( 'Disabled (recommended)', 'matomo' ),
					'always'    => esc_html__( 'Always enabled', 'matomo' ),
					'on_demand' => esc_html__( 'Enabled on demand', 'matomo' ),
				],
				__( 'For security and privacy reasons you should only enable this setting for as short time of a time as possible.', 'matomo' )
				. '<br/>'
				. __( 'If enabling on demand, add \'&debug=1\' to tracker requests to trigger debug output.', 'matomo' ),
				'',
				false,
				$matomo_full_generated_tracking_group . ' matomo-track-option-disabled matomo-track-option-manually matomo-track-option-tagmanager'
			);
			?>
			</tbody>
		</table>
	</div>

	<p>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $matomo_submit_button;
		?>
	</p>
</form>

<?php if ( ! $settings->is_network_enabled() ) { // Can't show it for multisite as idsite and url is always different. ?>
	<div id="matomo_default_tracking_code">
		<h2><?php esc_html_e( 'JavaScript tracking code', 'matomo' ); ?></h2>
		<p>
			<?php echo sprintf( esc_html__( 'Want to embed the tracking code manually into your site or using a different plugin? No problem! Simply copy/paste below tracking code. Want to adjust it? %1$sCheck out our developer documentation.%2$s', 'matomo' ), '<a href="https://developer.matomo.org/guides/tracking-javascript-guide" target="_blank" rel="noreferrer noopener">', '</a>' ); ?>
		</p>
		<?php echo '<pre><textarea readonly="readonly">' . esc_html( preg_replace( '/\\n+/', "\n", implode( ";\n", explode( ';', $matomo_default_tracking_code['script'] ) ) ) ) . '</textarea></pre>'; ?>
		<h3><?php esc_html_e( '<noscript> tracking code', 'matomo' ); ?></h3>
		<?php echo '<pre><textarea readonly="readonly" class="no_script">' . esc_html( $matomo_default_tracking_code['noscript'] ) . '</textarea></pre>'; ?>
	</div>
<?php } ?>
