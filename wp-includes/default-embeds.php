<?php

/**
 * Default Embed Handlers
 *
 * @package WordPress
 * @subpackage Embeds
 */

/**
 * The Google Video embed handler callback. Google Video does not support oEmbed.
 *
 * @see WP_Embed::register_handler()
 * @see WP_Embed::shortcode()
 *
 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
 * @param array $attr Embed attributes.
 * @param string $url The original URL that was matched by the regex.
 * @param array $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_googlevideo( $matches, $attr, $url, $rawattr ) {
	// If the user supplied a fixed width AND height, use it
	if ( !empty($rawattr['width']) && !empty($rawattr['height']) ) {
		$width  = (int) $rawattr['width'];
		$height = (int) $rawattr['height'];
	} else {
		list( $width, $height ) = wp_expand_dimensions( 425, 344, $attr['width'], $attr['height'] );
	}

	return apply_filters( 'embed_googlevideo', '<embed type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docid=' . esc_attr($matches[2]) . '&amp;hl=en&amp;fs=true" style="width:' . esc_attr($width) . 'px;height:' . esc_attr($height) . 'px" allowFullScreen="true" allowScriptAccess="always"></embed>', $matches, $attr, $url, $rawattr );
}
wp_embed_register_handler( 'googlevideo', '#http://video\.google\.([A-Za-z.]{2,5})/videoplay\?docid=([\d-]+)(.*?)#i', 'wp_embed_handler_googlevideo' );

/**
 * The PollDaddy.com embed handler callback. PollDaddy does not support oEmbed, at least not yet.
 *
 * @see WP_Embed::register_handler()
 * @see WP_Embed::shortcode()
 *
 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
 * @param array $attr Embed attributes.
 * @param string $url The original URL that was matched by the regex.
 * @param array $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_polldaddy( $matches, $attr, $url, $rawattr ) {
	return apply_filters( 'embed_polldaddy', '<script type="text/javascript" charset="utf8" src="http://s3.polldaddy.com/p/' . esc_attr($matches[1]) . '"></script>', $matches, $attr, $url, $rawattr );
}
wp_embed_register_handler( 'polldaddy', '#http://answers.polldaddy.com/poll/(\d+)(.*?)#i', 'wp_embed_handler_polldaddy' );

?>