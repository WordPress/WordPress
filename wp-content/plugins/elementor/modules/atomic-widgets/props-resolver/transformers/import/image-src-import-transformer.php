<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Import;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;
use Elementor\Modules\AtomicWidgets\PropTypes\Image_Attachment_Id_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Image_Src_Prop_Type;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Image_Src_Import_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ) {
		if ( empty( $value['url']['value'] ) ) {
			return null;
		}

		$uploaded = Plugin::$instance->templates_manager->get_import_images_instance()->import( [
			'id' => $value['id']['value'] ?? null,
			'url' => $value['url']['value'],
		] );

		if ( ! $uploaded ) {
			return null;
		}

		return Image_Src_Prop_Type::generate( [
			'id'  => Image_Attachment_Id_Prop_Type::generate( $uploaded['id'] ),
			'url' => null,
		] );
	}
}
