<?php

namespace Elementor\Core\Base\Providers;

class Social_Network_Provider {

	private static array $social_networks = [];

	public const FACEBOOK = 'Facebook';
	public const TWITTER = 'X (Twitter)';
	public const INSTAGRAM = 'Instagram';
	public const LINKEDIN = 'LinkedIn';
	public const PINTEREST = 'Pinterest';
	public const YOUTUBE = 'YouTube';
	public const TIKTOK = 'TikTok';
	public const WHATSAPP = 'WhatsApp';
	public const APPLEMUSIC = 'Apple Music';
	public const SPOTIFY = 'Spotify';
	public const SOUNDCLOUD = 'SoundCloud';
	public const BEHANCE = 'Behance';
	public const DRIBBBLE = 'Dribbble';
	public const VIMEO = 'Vimeo';
	public const WAZE = 'Waze';
	public const MESSENGER = 'Messenger';
	public const TELEPHONE = 'Telephone';
	public const EMAIL = 'Email';
	public const URL = 'Url';
	public const FILE_DOWNLOAD = 'File Download';
	public const SMS = 'SMS';
	public const VIBER = 'VIBER';
	public const SKYPE = 'Skype';
	public const VCF = 'Save contact (vCard)';

	public static function get_social_networks_icons(): array {
		static::init_social_networks_array_if_empty();

		static $icons = [];

		if ( empty( $icons ) ) {
			foreach ( static::$social_networks as $network => $data ) {
				$icons[ $network ] = $data['icon'];
			}
		}

		return $icons;
	}

	public static function get_icon_mapping( string $platform ): string {
		static::init_social_networks_array_if_empty();

		if ( isset( self::$social_networks[ $platform ]['icon'] ) ) {
			return self::$social_networks[ $platform ]['icon'];
		}

		return '';
	}

	public static function get_name_mapping( string $platform ): string {
		static::init_social_networks_array_if_empty();

		if ( isset( self::$social_networks[ $platform ]['name'] ) ) {
			return self::$social_networks[ $platform ]['name'];
		}

		return '';
	}

	public static function get_text_mapping( string $platform ): string {
		static::init_social_networks_array_if_empty();

		if ( isset( self::$social_networks[ $platform ]['text'] ) ) {
			return self::$social_networks[ $platform ]['text'];
		}

		return '';
	}

	public static function get_social_networks_text( $providers = [] ): array {
		static::init_social_networks_array_if_empty();

		static $texts = [];

		if ( empty( $texts ) ) {
			foreach ( static::$social_networks as $network => $data ) {
				$texts[ $network ] = $data['text'];
			}
		}

		if ( $providers ) {
			return array_intersect_key( $texts, array_flip( $providers ) );
		}

		return $texts;
	}

	private static function init_social_networks_array_if_empty(): void {
		if ( ! empty( static::$social_networks ) ) {
			return;
		}

		static::$social_networks[ static::VCF ] = [
			'text' => esc_html__( 'Save contact (vCard)', 'elementor' ),
			'icon' => 'fab fa-outlook',
			'name' => 'vcf',
		];

		static::$social_networks[ static::FACEBOOK ] = [
			'text' => esc_html__( 'Facebook', 'elementor' ),
			'icon' => 'fab fa-facebook',
			'name' => 'facebook',
		];

		static::$social_networks[ static::TWITTER ] = [
			'text' => esc_html__( 'X (Twitter)', 'elementor' ),
			'icon' => 'fab fa-x-twitter',
			'name' => 'x-twitter',
		];

		static::$social_networks[ static::INSTAGRAM ] = [
			'text' => esc_html__( 'Instagram', 'elementor' ),
			'icon' => 'fab fa-instagram',
			'name' => 'instagram',
		];

		static::$social_networks[ static::LINKEDIN ] = [
			'text' => esc_html__( 'LinkedIn', 'elementor' ),
			'icon' => 'fab fa-linkedin-in',
			'name' => 'linkedin',
		];

		static::$social_networks[ static::PINTEREST ] = [
			'text' => esc_html__( 'Pinterest', 'elementor' ),
			'icon' => 'fab fa-pinterest',
			'name' => 'pinterest',
		];

		static::$social_networks[ static::YOUTUBE ] = [
			'text' => esc_html__( 'YouTube', 'elementor' ),
			'icon' => 'fab fa-youtube',
			'name' => 'youtube',
		];

		static::$social_networks[ static::TIKTOK ] = [
			'text' => esc_html__( 'TikTok', 'elementor' ),
			'icon' => 'fab fa-tiktok',
			'name' => 'tiktok',
		];

		static::$social_networks[ static::WHATSAPP ] = [
			'text' => esc_html__( 'WhatsApp', 'elementor' ),
			'icon' => 'fab fa-whatsapp',
			'name' => 'whatsapp',
		];

		static::$social_networks[ static::APPLEMUSIC ] = [
			'text' => esc_html__( 'Apple Music', 'elementor' ),
			'icon' => 'fa fa-music',
			'name' => 'apple-music',
		];

		static::$social_networks[ static::SPOTIFY ] = [
			'text' => esc_html__( 'Spotify', 'elementor' ),
			'icon' => 'fab fa-spotify',
			'name' => 'spotify',
		];

		static::$social_networks[ static::SOUNDCLOUD ] = [
			'text' => esc_html__( 'SoundCloud', 'elementor' ),
			'icon' => 'fab fa-soundcloud',
			'name' => 'soundcloud',
		];

		static::$social_networks[ static::BEHANCE ] = [
			'text' => esc_html__( 'Behance', 'elementor' ),
			'icon' => 'fab fa-behance',
			'name' => 'behance',
		];

		static::$social_networks[ static::DRIBBBLE ] = [
			'text' => esc_html__( 'Dribbble', 'elementor' ),
			'icon' => 'fab fa-dribbble',
			'name' => 'dribble',
		];

		static::$social_networks[ static::VIMEO ] = [
			'text' => esc_html__( 'Vimeo', 'elementor' ),
			'icon' => 'fab fa-vimeo-v',
			'name' => 'vimeo',
		];

		static::$social_networks[ static::WAZE ] = [
			'text' => esc_html__( 'Waze', 'elementor' ),
			'icon' => 'fab fa-waze',
			'name' => 'waze',
		];

		static::$social_networks[ static::MESSENGER ] = [
			'text' => esc_html__( 'Messenger', 'elementor' ),
			'icon' => 'fab fa-facebook-messenger',
			'name' => 'messenger',
		];

		static::$social_networks[ static::TELEPHONE ] = [
			'text' => esc_html__( 'Telephone', 'elementor' ),
			'icon' => 'fas fa-phone-alt',
			'name' => 'phone',
		];

		static::$social_networks[ static::EMAIL ] = [
			'text' => esc_html__( 'Email', 'elementor' ),
			'icon' => 'fas fa-envelope',
			'name' => 'email',
		];

		static::$social_networks[ static::URL ] = [
			'text' => esc_html__( 'URL', 'elementor' ),
			'icon' => 'fas fa-globe',
			'name' => 'url',
		];

		static::$social_networks[ static::FILE_DOWNLOAD ] = [
			'text' => esc_html__( 'File Download', 'elementor' ),
			'icon' => 'fas fa-download',
			'name' => 'download',
		];

		static::$social_networks[ static::SMS ] = [
			'text' => esc_html__( 'SMS', 'elementor' ),
			'icon' => 'fas fa-sms',
			'name' => 'sms',
		];

		static::$social_networks[ static::VIBER ] = [
			'text' => esc_html__( 'Viber', 'elementor' ),
			'icon' => 'fab fa-viber',
			'name' => 'viber',
		];

		static::$social_networks[ static::SKYPE ] = [
			'text' => esc_html__( 'Skype', 'elementor' ),
			'icon' => 'fab fa-skype',
			'name' => 'skype',
		];
	}

	public static function build_messenger_link( string $username ) {
		return 'https://m.me/' . $username;
	}

	public static function build_email_link( array $data, string $prefix ) {
		$email = $data[ $prefix . '_mail' ] ?? '';
		$subject = $data[ $prefix . '_mail_subject' ] ?? '';
		$body = $data[ $prefix . '_mail_body' ] ?? '';

		if ( ! $email ) {
			return '';
		}

		$link = 'mailto:' . $email;

		if ( $subject ) {
			$link .= '?subject=' . $subject;
		}

		if ( $body ) {
			$link .= $subject ? '&' : '?';
			$link .= 'body=' . $body;
		}

		return $link;
	}


	public static function build_viber_link( string $action, string $number ) {
		if ( empty( $number ) ) {
			return '';
		}

		return add_query_arg( [
			'number' => urlencode( $number ),
		], 'viber://' . $action );
	}
}
