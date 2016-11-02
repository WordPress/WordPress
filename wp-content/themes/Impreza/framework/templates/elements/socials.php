<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output socials element
 *
 * @var $style string
 * @var $hover string Size: 'default' / 'none'
 * @var $facebook string
 * @var $twitter string
 * @var $google string
 * @var $linkedin string
 * @var $youtube string
 * @var $vimeo string
 * @var $flickr string
 * @var $behance string
 * @var $instagram string
 * @var $xing string
 * @var $pinterest string
 * @var $skype string
 * @var $dribbble string
 * @var $vk string
 * @var $tumblr string
 * @var $soundcloud string
 * @var $twitch string
 * @var $yelp string
 * @var $deviantart string
 * @var $foursquare string
 * @var $github string
 * @var $odnoklassniki string
 * @var $s500px string
 * @var $houzz string
 * @var $medium string
 * @var $tripadvisor string
 * @var $rss string
 * @var $custom_icon string
 * @var $custom_url string
 * @var $custom_color string
 * @var $size int
 * @var $size_tablets int
 * @var $size_mobiles int
 * @var $design_options array
 * @var $id string
 */

$socials = array(
	'facebook' => 'Facebook',
	'twitter' => 'Twitter',
	'google' => 'Google+',
	'linkedin' => 'LinkedIn',
	'youtube' => 'YouTube',
	'vimeo' => 'Vimeo',
	'flickr' => 'Flickr',
	'behance' => 'Behance',
	'instagram' => 'Instagram',
	'xing' => 'Xing',
	'pinterest' => 'Pinterest',
	'skype' => 'Skype',
	'dribbble' => 'Dribbble',
	'vk' => 'Vkontakte',
	'tumblr' => 'Tumblr',
	'soundcloud' => 'SoundCloud',
	'twitch' => 'Twitch',
	'yelp' => 'Yelp',
	'deviantart' => 'DeviantArt',
	'foursquare' => 'Foursquare',
	'github' => 'GitHub',
	'odnoklassniki' => 'Odnoklassniki',
	's500px' => '500px',
	'houzz' => 'Houzz',
	'medium' => '',
	'tripadvisor' => '',
	'rss' => 'RSS',
);

$output_inner = '';

foreach ( $socials as $social_key => $social ) {
	$social_url = $$social_key;
	if ( ! $social_url ) {
		continue;
	}

	if ( $social_key == 'skype' ) {
		// Skype link may be some http(s): or skype: link. If protocol is not set, adding "skype:"
		if ( strpos( $social_url, ':' ) === FALSE ) {
			$social_url = 'skype:' . esc_attr( $social_url );
		}
	} else {
		$social_url = esc_url( $social_url );
	}

	$output_inner .= '<div class="w-socials-item ' . $social_key . '">
		<a class="w-socials-item-link" target="_blank" href="' . $social_url . '">
			<span class="w-socials-item-link-hover"></span>
		</a>
		<div class="w-socials-item-popup">
			<span>' . $social . '</span>
		</div>
	</div>';
}

// Custom icon
if ( ! empty( $custom_icon ) AND ! empty( $custom_url ) ) {
	$output_inner .= '<div class="w-socials-item custom">';
	$output_inner .= '<a class="w-socials-item-link" target="_blank" href="' . esc_url( $custom_url ) . '">';
	$output_inner .= '<span class="w-socials-item-link-hover"></span>';
	$output_inner .= '<i class="' . us_prepare_icon_class( $custom_icon ) . '"></i>';
	$output_inner .= '</a></div>';
}

if ( ! empty( $output_inner ) ) {
	$classes = ' style_' . $style . ' hover_' . $hover;
	if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
		$classes .= ' hide-for-sticky';
	}
	if ( isset( $id ) AND ! empty( $id ) ) {
		$classes .= ' ush_' . str_replace( ':', '_', $id );
	}
	$output = '<div class="w-socials' . $classes . '"><div class="w-socials-list">' . $output_inner . '</div></div>';

	echo $output;
}
