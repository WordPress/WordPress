<?php

add_action(
	'wp_enqueue_scripts',
	static function () {
		$assets = array();
		$asset_file = wpcf7_plugin_path( 'includes/swv/js/index.asset.php' );

		if ( file_exists( $asset_file ) ) {
			$assets = include $asset_file;
		}

		$assets = wp_parse_args( $assets, array(
			'dependencies' => array(),
			'version' => WPCF7_VERSION,
		) );

		wp_register_script( 'swv',
			wpcf7_plugin_url( 'includes/swv/js/index.js' ),
			$assets['dependencies'],
			$assets['version'],
			array( 'in_footer' => true )
		);
	},
	10, 0
);
