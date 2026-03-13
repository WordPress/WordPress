<?php
/**
 * Enqueues the assets required for the Command Palette.
 *
 * @global array  $menu
 * @global array  $submenu
 */
function gutenberg_enqueue_command_palette_assets() {
	global $menu, $submenu;

	$command_palette_settings = array(
		'is_network_admin' => is_network_admin(),
	);

	if ( $menu ) {
		$menu_commands = array();
		foreach ( $menu as $menu_item ) {
			if ( empty( $menu_item[0] ) || ! empty( $menu_item[1] ) && ! current_user_can( $menu_item[1] ) ) {
				continue;
			}

			// Remove all HTML tags and their contents.
			$menu_label = $menu_item[0];
			while ( preg_match( '/<[^>]*>/', $menu_label ) ) {
				$menu_label = preg_replace( '/<[^>]*>.*?<\/[^>]*>|<[^>]*\/>|<[^>]*>/s', '', $menu_label );
			}
			$menu_label = trim( $menu_label );
			$menu_url   = '';
			$menu_slug  = $menu_item[2];

			if ( preg_match( '/\.php($|\?)/', $menu_slug ) || wp_http_validate_url( $menu_slug ) ) {
				$menu_url = $menu_slug;
			} elseif ( ! empty( menu_page_url( $menu_slug, false ) ) ) {
				$menu_url = html_entity_decode( menu_page_url( $menu_slug, false ), ENT_QUOTES, get_bloginfo( 'charset' ) );
			}

			if ( $menu_url ) {
				$menu_commands[] = array(
					'label' => $menu_label,
					'url'   => $menu_url,
					'name'  => $menu_slug,
				);
			}

			if ( array_key_exists( $menu_slug, $submenu ) ) {
				foreach ( $submenu[ $menu_slug ] as $submenu_item ) {
					if ( empty( $submenu_item[0] ) || ! empty( $submenu_item[1] ) && ! current_user_can( $submenu_item[1] ) ) {
						continue;
					}

					// Remove all HTML tags and their contents.
					$submenu_label = $submenu_item[0];
					while ( preg_match( '/<[^>]*>/', $submenu_label ) ) {
						$submenu_label = preg_replace( '/<[^>]*>.*?<\/[^>]*>|<[^>]*\/>|<[^>]*>/s', '', $submenu_label );
					}
					$submenu_label = trim( $submenu_label );
					$submenu_url   = '';
					$submenu_slug  = $submenu_item[2];

					if ( preg_match( '/\.php($|\?)/', $submenu_slug ) || wp_http_validate_url( $submenu_slug ) ) {
						$submenu_url = $submenu_slug;
					} elseif ( ! empty( menu_page_url( $submenu_slug, false ) ) ) {
						$submenu_url = html_entity_decode( menu_page_url( $submenu_slug, false ), ENT_QUOTES, get_bloginfo( 'charset' ) );
					}

					if ( $submenu_url ) {
						$menu_commands[] = array(
							'label' => sprintf(
								/* translators: 1: Menu label, 2: Submenu label. */
								__( '%1$s > %2$s' ),
								$menu_label,
								$submenu_label
							),
							'url'   => $submenu_url,
							'name'  => $menu_slug . '-' . $submenu_item[2],
						);
					}
				}
			}
		}
		$command_palette_settings['menu_commands'] = $menu_commands;
	}

	wp_enqueue_script( 'wp-commands' );
	wp_enqueue_style( 'wp-commands' );
	wp_enqueue_script( 'wp-core-commands' );

	wp_add_inline_script(
		'wp-core-commands',
		sprintf(
			'wp.coreCommands.initializeCommandPalette( %s );',
			wp_json_encode( $command_palette_settings, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES )
		)
	);
}

if ( has_filter( 'admin_enqueue_scripts', 'wp_enqueue_command_palette_assets' ) ) {
	remove_filter( 'admin_enqueue_scripts', 'wp_enqueue_command_palette_assets' );
}
add_filter( 'admin_enqueue_scripts', 'gutenberg_enqueue_command_palette_assets', 9 );
