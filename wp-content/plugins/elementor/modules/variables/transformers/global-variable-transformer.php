<?php

namespace Elementor\Modules\Variables\Transformers;

use Elementor\Modules\Variables\Classes\Variables;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Global_Variable_Transformer extends Transformer_Base {
	public function transform( $value, $key ) {
		$variable = Variables::by_id( $value );

		if ( ! $variable ) {
			return null;
		}

		if ( array_key_exists( 'deleted', $variable ) && $variable['deleted'] ) {
			return "var(--{$value})";
		}

		$identifier = $variable['label'];

		if ( ! trim( $identifier ) ) {
			return null;
		}

		return "var(--{$identifier})";
	}
}
