<?php
namespace Elementor\Modules\Home\Transformations;

use Elementor\Core\DocumentTypes\Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Create_Site_Settings_Url extends Base\Transformations_Abstract {


	const SITE_SETTINGS_ITEMS = [ 'Site Settings', 'Site Logo', 'Global Colors', 'Global Fonts' ];

	public function transform( array $home_screen_data ): array {
		if ( empty( $home_screen_data['get_started'] ) ) {
			return $home_screen_data;
		}

		$site_settings_url_config = Page::get_site_settings_url_config();

		$home_screen_data['get_started']['repeater'] = array_map( function( $repeater_item ) use ( $site_settings_url_config ) {
			if ( ! in_array( $repeater_item['title'], static::SITE_SETTINGS_ITEMS, true ) ) {
				return $repeater_item;
			}

			if ( ! empty( $repeater_item['tab_id'] ) ) {
				$site_settings_url_config['url'] = add_query_arg( [ 'active-tab' => $repeater_item['tab_id'] ], $site_settings_url_config['url'] );
			}

			return array_merge( $repeater_item, $site_settings_url_config );
		}, $home_screen_data['get_started']['repeater'] );

		return $home_screen_data;
	}
}
