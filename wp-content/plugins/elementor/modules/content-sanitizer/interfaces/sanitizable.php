<?php
namespace Elementor\Modules\ContentSanitizer\Interfaces;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

interface Sanitizable {
	public function sanitize( $content );
}
