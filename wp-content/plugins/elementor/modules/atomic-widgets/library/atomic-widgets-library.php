<?php

namespace Elementor\Modules\AtomicWidgets\Library;

use Elementor\Plugin;

class Atomic_Widgets_Library {
	public function register_hooks() {
		add_action( 'elementor/documents/register', fn() => $this->register_documents() );
	}

	public function register_documents() {
		Plugin::$instance->documents
			->register_document_type( 'e-div-block', Div_Block::get_class_full_name() )
			->register_document_type( 'e-flexbox', Flexbox::get_class_full_name() );
	}
}
