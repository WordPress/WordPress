<?php

namespace HelloTheme\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Script {

	protected string $handle;
	protected array $dependencies;

	public function __construct( string $handle, array $dependencies = [] ) {
		$this->handle       = $handle;
		$this->dependencies = $dependencies;
	}

	public function enqueue() {
		$asset_path = HELLO_THEME_SCRIPTS_PATH . $this->handle . '.asset.php';
		$asset_url = HELLO_THEME_SCRIPTS_URL;

		if ( ! file_exists( $asset_path ) ) {
			throw new \Exception( $asset_path . ' - You need to run `npm run build` for the "hello-elementor" first.' );
		}

		$script_asset = require $asset_path;

		foreach ( $this->dependencies as $dependency ) {
			$script_asset['dependencies'][] = $dependency;
		}

		wp_enqueue_script(
			$this->handle,
			$asset_url . "$this->handle.js",
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		\wp_set_script_translations( $this->handle, 'hello-elementor' );
	}
}
