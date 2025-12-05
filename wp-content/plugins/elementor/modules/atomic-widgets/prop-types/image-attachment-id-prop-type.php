<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Image_Attachment_Id_Prop_Type extends Plain_Prop_Type {
	public static function get_key(): string {
		return 'image-attachment-id';
	}

	protected function validate_value( $value ): bool {
		return is_numeric( $value );
	}

	protected function sanitize_value( $value ): int {
		return (int) $value;
	}
}
