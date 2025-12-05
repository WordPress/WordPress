<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor embed.
 *
 * Elementor embed handler class is responsible for Elementor embed functionality.
 * The class holds the supported providers with their embed patters, and handles
 * their custom properties to create custom HTML with the embedded content.
 *
 * @since 1.5.0
 */
class Embed {

	/**
	 * Provider match masks.
	 *
	 * Holds a list of supported providers with their URL structure in a regex format.
	 *
	 * @since 1.5.0
	 * @access private
	 * @static
	 *
	 * @var array Provider URL structure regex.
	 */
	private static $provider_match_masks = [
		'youtube' => '/^.*(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:(?:watch)?\?(?:.*&)?vi?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"\'>]+)/',
		'vimeo' => '/^.*vimeo\.com\/(?:[a-z]*\/)*([‌​0-9]{6,11})[?]?.*/',
		'dailymotion' => '/^.*dailymotion.com\/(?:video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/',
		'videopress' => [
			'/^(?:http(?:s)?:\/\/)?videos\.files\.wordpress\.com\/([a-zA-Z\d]{8,})\//i',
			'/^(?:http(?:s)?:\/\/)?(?:www\.)?video(?:\.word)?press\.com\/(?:v|embed)\/([a-zA-Z\d]{8,})(.+)?/i',
		],
	];

	/**
	 * Embed patterns.
	 *
	 * Holds a list of supported providers with their embed patters.
	 *
	 * @since 1.5.0
	 * @access private
	 * @static
	 *
	 * @var array Embed patters.
	 */
	private static $embed_patterns = [
		'youtube' => 'https://www.youtube{NO_COOKIE}.com/embed/{VIDEO_ID}?feature=oembed',
		'vimeo' => 'https://player.vimeo.com/video/{VIDEO_ID}#t={TIME}',
		'dailymotion' => 'https://dailymotion.com/embed/video/{VIDEO_ID}',
		'videopress' => 'https://videopress.com/embed/{VIDEO_ID}',
	];

	/**
	 * Get video properties.
	 *
	 * Retrieve the video properties for a given video URL.
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 *
	 * @param string $video_url Video URL.
	 *
	 * @return null|array The video properties, or null.
	 */
	public static function get_video_properties( $video_url ) {
		foreach ( self::$provider_match_masks as $provider => $match_mask ) {
			if ( ! is_array( $match_mask ) ) {
				$match_mask = [ $match_mask ];
			}

			foreach ( $match_mask as $mask ) {
				if ( preg_match( $mask, $video_url, $matches ) ) {
					return [
						'provider' => $provider,
						'video_id' => $matches[1],
					];
				}
			}
		}

		return null;
	}

	/**
	 * Get embed URL.
	 *
	 * Retrieve the embed URL for a given video.
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 *
	 * @param string $video_url        Video URL.
	 * @param array  $embed_url_params Optional. Embed parameters. Default is an
	 *                                 empty array.
	 * @param array  $options          Optional. Embed options. Default is an
	 *                                 empty array.
	 *
	 * @return null|array The video properties, or null.
	 */
	public static function get_embed_url( $video_url, array $embed_url_params = [], array $options = [] ) {
		$video_properties = self::get_video_properties( $video_url );

		if ( ! $video_properties ) {
			return null;
		}

		$embed_pattern = self::$embed_patterns[ $video_properties['provider'] ];

		$replacements = [
			'{VIDEO_ID}' => $video_properties['video_id'],
		];

		if ( 'youtube' === $video_properties['provider'] ) {
			$replacements['{NO_COOKIE}'] = ! empty( $options['privacy'] ) ? '-nocookie' : '';
		} elseif ( 'vimeo' === $video_properties['provider'] ) {
			$time_text = '';

			if ( ! empty( $options['start'] ) ) {
				$time_text = date( 'H\hi\ms\s', $options['start'] ); // PHPCS:Ignore WordPress.DateTime.RestrictedFunctions.date_date
			}

			$replacements['{TIME}'] = $time_text;

			/**
			 * Handle Vimeo private videos
			 *
			 * Vimeo requires an additional parameter when displaying private/unlisted videos. It has two ways of
			 * passing that parameter:
			 * * as an endpoint - vimeo.com/{video_id}/{privacy_token}
			 * OR
			 * * as a GET parameter named `h` - vimeo.com/{video_id}?h={privacy_token}
			 *
			 * The following regex match looks for either of these methods in the Vimeo URL, and if it finds a privacy
			 * token, it adds it to the embed params array as the `h` parameter (which is how Vimeo can receive it when
			 * using Oembed).
			 */
			$h_param = [];
			preg_match( '/(?|(?:[\?|\&]h={1})([\w]+)|\d\/([\w]+))/', $video_url, $h_param );

			if ( ! empty( $h_param ) ) {
				$embed_url_params['h'] = $h_param[1];
			}
		}

		$embed_pattern = str_replace( array_keys( $replacements ), $replacements, $embed_pattern );

		return add_query_arg( $embed_url_params, $embed_pattern );
	}

	/**
	 * Get embed HTML.
	 *
	 * Retrieve the final HTML of the embedded URL.
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 *
	 * @param string $video_url        Video URL.
	 * @param array  $embed_url_params Optional. Embed parameters. Default is an
	 *                                 empty array.
	 * @param array  $options          Optional. Embed options. Default is an
	 *                                 empty array.
	 * @param array  $frame_attributes Optional. IFrame attributes. Default is an
	 *                                 empty array.
	 *
	 * @return string The embed HTML.
	 */
	public static function get_embed_html( $video_url, array $embed_url_params = [], array $options = [], array $frame_attributes = [] ) {
		$video_properties = self::get_video_properties( $video_url );

		$default_frame_attributes = [
			'class' => 'elementor-video-iframe',
			'allowfullscreen',
			'allow' => 'clipboard-write',
			'title' => sprintf(
				/* translators: %s: Video provider */
				__( '%s Video Player', 'elementor' ),
				$video_properties['provider']
			),
		];

		$video_embed_url = self::get_embed_url( $video_url, $embed_url_params, $options );
		if ( ! $video_embed_url ) {
			return null;
		}
		if ( ! isset( $options['lazy_load'] ) || ! $options['lazy_load'] ) {
			$default_frame_attributes['src'] = $video_embed_url;
		} else {
			$default_frame_attributes['data-lazy-load'] = $video_embed_url;
		}

		if ( isset( $embed_url_params['autoplay'] ) ) {
			$default_frame_attributes['allow'] = 'autoplay';
		}

		$frame_attributes = array_merge( $default_frame_attributes, $frame_attributes );

		$attributes_for_print = [];

		foreach ( $frame_attributes as $attribute_key => $attribute_value ) {
			$attribute_value = esc_attr( $attribute_value );

			if ( is_numeric( $attribute_key ) ) {
				$attributes_for_print[] = $attribute_value;
			} else {
				$attributes_for_print[] = sprintf( '%1$s="%2$s"', $attribute_key, $attribute_value );
			}
		}

		$attributes_for_print = implode( ' ', $attributes_for_print );

		$iframe_html = "<iframe $attributes_for_print></iframe>";

		/** This filter is documented in wp-includes/class-oembed.php */
		return apply_filters( 'oembed_result', $iframe_html, $video_url, $frame_attributes );
	}

	/**
	 * Get oembed data from the cache.
	 * if not exists in the cache it will fetch from provider and then save to the cache.
	 *
	 * @param string $oembed_url
	 * @param string $cached_post_id
	 *
	 * @return array|null
	 */
	public static function get_oembed_data( $oembed_url, $cached_post_id ) {
		$cached_oembed_data = json_decode( get_post_meta( $cached_post_id, '_elementor_oembed_cache', true ), true );

		if ( isset( $cached_oembed_data[ $oembed_url ] ) ) {
			return $cached_oembed_data[ $oembed_url ];
		}

		$normalize_oembed_data = self::fetch_oembed_data( $oembed_url );

		if ( ! $cached_oembed_data ) {
			$cached_oembed_data = [];
		}

		update_post_meta( $cached_post_id, '_elementor_oembed_cache', wp_json_encode( array_merge(
			$cached_oembed_data,
			[
				$oembed_url => $normalize_oembed_data,
			]
		) ) );

		return $normalize_oembed_data;
	}

	/**
	 * Fetch oembed data from oembed provider.
	 *
	 * @param string $oembed_url
	 * @return array|null
	 */
	public static function fetch_oembed_data( $oembed_url ) {
		$oembed_data = _wp_oembed_get_object()->get_data( $oembed_url );

		if ( ! $oembed_data ) {
			return null;
		}

		return [
			'thumbnail_url' => $oembed_data->thumbnail_url,
			'title' => $oembed_data->title,
		];
	}

	/**
	 * @param string          $oembed_url
	 * @param null|string|int $cached_post_id
	 *
	 * @return string|null
	 */
	public static function get_embed_thumbnail_html( $oembed_url, $cached_post_id = null ) {
		$oembed_data = self::get_oembed_data( $oembed_url, $cached_post_id );

		if ( ! $oembed_data ) {
			return null;
		}

		return '<div class="elementor-image">' . sprintf( '<img src="%1$s" alt="%2$s" title="%2$s" width="%3$s" loading="lazy" />', $oembed_data['thumbnail_url'], esc_attr( $oembed_data['title'] ), '100%' ) . '</div>';
	}
}
