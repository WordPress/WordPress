<?php

namespace Elementor\Modules\Variables\Classes;

use Elementor\Modules\AtomicWidgets\PropsResolver\Transformers_Registry;
use Elementor\Modules\Variables\PropTypes\Color_Variable_Prop_Type;
use Elementor\Modules\Variables\PropTypes\Font_Variable_Prop_Type;
use Elementor\Modules\Variables\Transformers\Global_Variable_Transformer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Style_Transformers {
	public function append_to( Transformers_Registry $transformers_registry ): self {
		$transformer = new Global_Variable_Transformer();

		$transformers_registry->register( Color_Variable_Prop_Type::get_key(), $transformer );
		$transformers_registry->register( Font_Variable_Prop_Type::get_key(), $transformer );

		return $this;
	}
}
