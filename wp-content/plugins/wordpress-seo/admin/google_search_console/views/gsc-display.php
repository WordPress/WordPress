<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Google_Search_Console
 */

// Admin header.
Yoast_Form::get_instance()->admin_header( false, 'wpseo-gsc', false, 'yoast_wpseo_gsc_options' );

// GSC Error notification.
$gsc_url                 = 'https://search.google.com/search-console/index';
$gsc_post_url            = 'https://yoa.st/google-search-console-deprecated';
$gsc_style_alert         = '
	display: flex;
	align-items: baseline;
	position: relative;
	padding: 16px;
	border: 1px solid rgba(0, 0, 0, 0.2);
	font-size: 14px;
	font-weight: 400;
	line-height: 1.5;
	margin: 16px 0;
	color: #450c11;
	background: #f8d7da;
';
$gsc_style_alert_icon    = 'display: block; margin-right: 8px;';
$gsc_style_alert_content = 'max-width: 600px;';
$gsc_style_alert_link    = 'color: #004973;';
$gsc_notification        = sprintf(
	/* Translators: %1$s: expands to opening anchor tag, %2$s expands to closing anchor tag. */
	__( 'Google has discontinued its Crawl Errors API. Therefore, any possible crawl errors you might have cannot be displayed here anymore. %1$sRead our statement on this for further information%2$s.', 'wordpress-seo' ),
	'<a style="' . $gsc_style_alert_link . '" href="' . WPSEO_Shortlinker::get( $gsc_post_url ) . '" target="_blank" rel="noopener">',
	WPSEO_Admin_Utils::get_new_tab_message() . '</a>'
);
$gsc_notification .= '<br/><br/>';
$gsc_notification .= sprintf(
	/* Translators: %1$s: expands to opening anchor tag, %2$s expands to closing anchor tag. */
	__( 'To view your current crawl errors, %1$splease visit Google Search Console%2$s.', 'wordpress-seo' ),
	'<a style="' . $gsc_style_alert_link . '" href="' . $gsc_url . '" target="_blank" rel="noopener noreferrer">',
	WPSEO_Admin_Utils::get_new_tab_message() . '</a>'
);
?>
	<div style="<?php echo $gsc_style_alert; ?>">
	<span style="<?php echo $gsc_style_alert_icon; ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" role="img" aria-hidden="true"
			focusable="false" fill="#450c11">
			<path
				d="M6 1q1.6 0 3 .8T11.2 4t.8 3-.8 3T9 12.2 6 13t-3-.8T.8 10 0 7t.8-3T3 1.8 6 1zm1 9.7V9.3 9L6.7 9H5l-.1.3V10.9l.3.1h1.6l.1-.3zm0-2.6L7 3.2v-.1L6.8 3H5 5l-.1.2.1 4.9.3.2h1.4l.2-.1Q7 8 6.9 8z"></path>
		</svg>
	</span>
		<span style="<?php echo $gsc_style_alert_content; ?>"><?php echo $gsc_notification; ?></span>
	</div>
<?php
