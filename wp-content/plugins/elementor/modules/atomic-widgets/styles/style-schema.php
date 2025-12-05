<?php

namespace Elementor\Modules\AtomicWidgets\Styles;

use Elementor\Modules\AtomicWidgets\DynamicTags\Dynamic_Prop_Types_Mapping;
use Elementor\Modules\AtomicWidgets\PropTypes\Background_Image_Overlay_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Background_Overlay_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Background_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Box_Shadow_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Border_Radius_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Border_Width_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Color_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Dimensions_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Filters\Backdrop_Filter_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Filters\Filter_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Layout_Direction_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Position_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Stroke_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Transform\Transform_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Transition_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Flex_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Style_Schema {
	public static function get() {
		return apply_filters( 'elementor/atomic-widgets/styles/schema', static::get_style_schema() );
	}

	public static function get_style_schema(): array {
		return array_merge(
			self::get_size_props(),
			self::get_position_props(),
			self::get_typography_props(),
			self::get_spacing_props(),
			self::get_border_props(),
			self::get_background_props(),
			self::get_effects_props(),
			self::get_layout_props(),
			self::get_alignment_props(),
		);
	}

	private static function get_size_props() {
		return [
			'width' => Size_Prop_Type::make(),
			'height' => Size_Prop_Type::make(),
			'min-width' => Size_Prop_Type::make(),
			'min-height' => Size_Prop_Type::make(),
			'max-width' => Size_Prop_Type::make(),
			'max-height' => Size_Prop_Type::make(),
			'overflow' => String_Prop_Type::make()->enum( [
				'visible',
				'hidden',
				'auto',
			] ),
			'aspect-ratio' => String_Prop_Type::make(),
			'object-fit' => String_Prop_Type::make()->enum( [
				'fill',
				'cover',
				'contain',
				'none',
				'scale-down',
			] ),
			'object-position' => Union_Prop_Type::make()
				->add_prop_type( String_Prop_Type::make()->enum( Position_Prop_Type::get_position_enum_values() ) )
				->add_prop_type( Position_Prop_Type::make() )
				->set_dependencies(
					Dependency_Manager::make( Dependency_Manager::RELATION_AND )
					->where( [
						'operator' => 'ne',
						'path' => [ 'object-fit' ],
						'value' => 'fill',
					] )
					->where( [
						'operator' => 'exists',
						'path' => [ 'object-fit' ],
					] )
					->get()
				),
		];
	}

	private static function get_position_props() {
		return [
			'position' => String_Prop_Type::make()->enum( [
				'static',
				'relative',
				'absolute',
				'fixed',
				'sticky',
			] ),
			'inset-block-start' => Size_Prop_Type::make(),
			'inset-inline-end' => Size_Prop_Type::make(),
			'inset-block-end' => Size_Prop_Type::make(),
			'inset-inline-start' => Size_Prop_Type::make(),
			'z-index' => Number_Prop_Type::make(),
			'scroll-margin-top' => Size_Prop_Type::make()->units( Size_Constants::anchor_offset() ),
		];
	}

	private static function get_typography_props() {
		return [
			'font-family' => String_Prop_Type::make(),
			'font-weight' => String_Prop_Type::make()->enum( [
				'100',
				'200',
				'300',
				'400',
				'500',
				'600',
				'700',
				'800',
				'900',
				'normal',
				'bold',
				'bolder',
				'lighter',
			] ),
			'font-size' => Size_Prop_Type::make()->units( Size_Constants::typography() ),
			'color' => Color_Prop_Type::make(),
			'letter-spacing' => Size_Prop_Type::make()->units( Size_Constants::typography() ),
			'word-spacing' => Size_Prop_Type::make()->units( Size_Constants::typography() ),
			'column-count' => Number_Prop_Type::make(),
			'column-gap' => Size_Prop_Type::make()
				->set_dependencies(
					Dependency_Manager::make()
					->where( [
						'operator' => 'gte',
						'path' => [ 'column-count' ],
						'value' => 1,
					] )
					->get()
				),
			'line-height' => Size_Prop_Type::make()->units( Size_Constants::typography() ),
			'text-align' => String_Prop_Type::make()->enum( [
				'start',
				'center',
				'end',
				'justify',
			] ),
			'font-style' => String_Prop_Type::make()->enum( [
				'normal',
				'italic',
				'oblique',
			] ),
			// TODO: validate text-decoration in more specific way [EDS-524]
			'text-decoration' => String_Prop_Type::make(),
			'text-transform' => String_Prop_Type::make()->enum( [
				'none',
				'capitalize',
				'uppercase',
				'lowercase',
			] ),
			'direction' => String_Prop_Type::make()->enum( [
				'ltr',
				'rtl',
			] ),
			'stroke' => Stroke_Prop_Type::make(),
			'all' => String_Prop_Type::make()->enum( [
				'initial',
				'inherit',
				'unset',
				'revert',
				'revert-layer',
			] ),
			'cursor' => String_Prop_Type::make()->enum( [
				'pointer',
			] ),
		];
	}

	private static function get_spacing_props() {
		return [
			'padding' => Union_Prop_Type::make()
				->add_prop_type( Dimensions_Prop_Type::make_with_units( Size_Constants::spacing() ) )
				->add_prop_type( Size_Prop_Type::make()->units( Size_Constants::spacing() ) ),
			'margin' => Union_Prop_Type::make()
				->add_prop_type( Dimensions_Prop_Type::make() )
				->add_prop_type( Size_Prop_Type::make() ),
		];
	}

	private static function get_border_props() {
		return [
			'border-radius' => Union_Prop_Type::make()
				->add_prop_type( Size_Prop_Type::make()->units( Size_Constants::border() ) )
				->add_prop_type( Border_Radius_Prop_Type::make() ),
			'border-width' => Union_Prop_Type::make()
				->add_prop_type( Size_Prop_Type::make()->units( Size_Constants::border() ) )
				->add_prop_type( Border_Width_Prop_Type::make() ),
			'border-color' => Color_Prop_Type::make(),
			'border-style' => String_Prop_Type::make()->enum( [
				'none',
				'hidden',
				'dotted',
				'dashed',
				'solid',
				'double',
				'groove',
				'ridge',
				'inset',
				'outset',
			] ),
		];
	}

	private static function get_background_props() {
		// Background image overlay as an exception
		$background_prop_type = Background_Prop_Type::make();
		$bg_overlay_prop_type = $background_prop_type->get_shape_field( Background_Overlay_Prop_Type::get_key() );
		$bg_image_overlay_prop_type = $bg_overlay_prop_type->get_item_type()->get_prop_type( Background_Image_Overlay_Prop_Type::get_key() );
		Dynamic_Prop_Types_Mapping::make()->get_modified_prop_types( $bg_image_overlay_prop_type->get_shape() );
		return [
			'background' => $background_prop_type,
		];
	}

	private static function get_effects_props() {
		return [
			'mix-blend-mode' => String_Prop_Type::make()->enum( [
				'normal',
				'multiply',
				'screen',
				'overlay',
				'darken',
				'lighten',
				'color-dodge',
				'saturation',
				'color',
				'difference',
				'exclusion',
				'hue',
				'luminosity',
				'soft-light',
				'hard-light',
				'color-burn',
			] ),
			'box-shadow' => Box_Shadow_Prop_Type::make(),
			'opacity' => Size_Prop_Type::make()
				->units( Size_Constants::opacity() )
				->default_unit( Size_Constants::UNIT_PERCENT ),
			'filter' => Filter_Prop_Type::make(),
			'backdrop-filter' => Backdrop_Filter_Prop_Type::make(),
			'transform' => Transform_Prop_Type::make(),
			'transition' => Transition_Prop_Type::make(),
		];
	}

	private static function get_layout_props() {
		return [
			'display' => String_Prop_Type::make()->enum( [
				'block',
				'inline',
				'inline-block',
				'flex',
				'inline-flex',
				'grid',
				'inline-grid',
				'flow-root',
				'none',
				'contents',
			] ),
			'flex-direction' => String_Prop_Type::make()->enum( [
				'row',
				'row-reverse',
				'column',
				'column-reverse',
			] ),
			'gap' => Union_Prop_Type::make()
				->add_prop_type( Layout_Direction_Prop_Type::make() )
				->add_prop_type( Size_Prop_Type::make()->units( Size_Constants::layout() ) ),
			'flex-wrap' => String_Prop_Type::make()->enum( [
				'wrap',
				'nowrap',
				'wrap-reverse',
			] ),
			'flex' => Flex_Prop_Type::make(),
		];
	}

	private static function get_alignment_props() {
		return [
			'justify-content' => String_Prop_Type::make()->enum( [
				'center',
				'start',
				'end',
				'flex-start',
				'flex-end',
				'left',
				'right',
				'normal',
				'space-between',
				'space-around',
				'space-evenly',
				'stretch',
			] ),
			'align-content' => String_Prop_Type::make()->enum( [
				'center',
				'start',
				'end',
				'space-between',
				'space-around',
				'space-evenly',
			] ),
			'align-items' => String_Prop_Type::make()->enum( [
				'normal',
				'stretch',
				'center',
				'start',
				'end',
				'flex-start',
				'flex-end',
				'self-start',
				'self-end',
				'anchor-center',
			] ),
			'align-self' => String_Prop_Type::make()->enum( [
				'auto',
				'normal',
				'center',
				'start',
				'end',
				'self-start',
				'self-end',
				'flex-start',
				'flex-end',
				'anchor-center',
				'baseline',
				'first baseline',
				'last baseline',
				'stretch',
			] ),
			'order' => Number_Prop_Type::make(),
		];
	}
}
