<?php

namespace Elementor\Modules\AtomicWidgets\DynamicTags;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Toggle_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Query_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Number_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Textarea_Control;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Transformable_Prop_Type;
use Elementor\Modules\AtomicWidgets\Query\Query_Builder;
use Elementor\Modules\AtomicWidgets\Query\Query_Builder_Factory;
use Elementor\Modules\WpRest\Base\Query as Query_Base;
use Elementor\Modules\WpRest\Classes\Post_Query;
use Elementor\Modules\WpRest\Classes\Term_Query;
use Elementor\Modules\WpRest\Classes\User_Query;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Dynamic_Tags_Editor_Config {

	private Dynamic_Tags_Schemas $schemas;

	private ?array $tags = null;

	public function __construct( Dynamic_Tags_Schemas $schemas ) {
		$this->schemas = $schemas;
	}

	public function get_tags(): array {
		if ( null !== $this->tags ) {
			return $this->tags;
		}

		$atomic_tags  = [];
		$dynamic_tags = Plugin::$instance->dynamic_tags->get_tags_config();

		foreach ( $dynamic_tags as $name => $tag ) {
			$atomic_tag = $this->convert_dynamic_tag_to_atomic( $tag );

			if ( $atomic_tag ) {
				$atomic_tags[ $name ] = $atomic_tag;
			}
		}

		$this->tags = $atomic_tags;

		return $this->tags;
	}

	/**
	 * @param string $name
	 *
	 * @return null|array{
	 *       name: string,
	 *       categories: string[],
	 *       label: string,
	 *       group: string,
	 *       atomic_controls: array,
	 *       props_schema: array<string, Transformable_Prop_Type>
	 *  }
	 */
	public function get_tag( string $name ): ?array {
		$tags = $this->get_tags();

		return $tags[ $name ] ?? null;
	}

	private function convert_dynamic_tag_to_atomic( $tag ) {
		if ( empty( $tag['name'] ) || empty( $tag['categories'] ) ) {
			return null;
		}

		$converted_tag = [
			'name'            => $tag['name'],
			'categories'      => $tag['categories'],
			'label'           => $tag['title'] ?? '',
			'group'           => $tag['group'] ?? '',
			'atomic_controls' => [],
			'props_schema'    => $this->schemas->get( $tag['name'] ),
		];

		if ( ! isset( $tag['controls'] ) ) {
			return $converted_tag;
		}

		try {
			$atomic_controls = $this->convert_controls_to_atomic( $tag );
		} catch ( \Exception $e ) {
			return null;
		}

		if ( null === $atomic_controls ) {
			return null;
		}

		$converted_tag['atomic_controls'] = $atomic_controls;

		return $converted_tag;
	}

	private function convert_controls_to_atomic( $tag ) {
		$atomic_controls = [];

		$controls = $tag['controls'] ?? null;
		$force = $tag['force_convert_to_atomic'] ?? false;

		if ( ! is_array( $controls ) ) {
			return null;
		}

		foreach ( $controls as $control ) {
			if ( 'section' === $control['type'] ) {
				continue;
			}

			$atomic_control = $this->convert_control_to_atomic( $control, $tag );

			if ( ! $atomic_control ) {
				if ( $force ) {
					continue;
				}

				return null;
			}

			$section_name = $control['section'];

			if ( ! isset( $atomic_controls[ $section_name ] ) ) {
				$atomic_controls[ $section_name ] = Section::make()
					->set_label( $controls[ $section_name ]['label'] );
			}

			$atomic_controls[ $section_name ] = $atomic_controls[ $section_name ]->add_item( $atomic_control );
		}

		return array_values( $atomic_controls );
	}

	private function convert_control_to_atomic( $control, $tag = [] ) {
		$map = [
			'select'   => fn( $control ) => $this->convert_select_control_to_atomic( $control, $tag ),
			'text'     => fn( $control ) => $this->convert_text_control_to_atomic( $control ),
			'textarea' => fn( $control ) => $this->convert_textarea_control_to_atomic( $control ),
			'switcher' => fn( $control ) => $this->convert_switch_control_to_atomic( $control ),
			'number'   => fn( $control ) => $this->convert_number_control_to_atomic( $control ),
			'query'   => fn( $control ) => $this->convert_autocomplete_control_to_atomic( $control ),
			'choose'   => fn( $control ) => $this->convert_choose_control_to_atomic( $control ),
			'media'   => fn( $control ) => $this->convert_media_control_to_atomic( $control ),
			'date_time' => fn( $control ) => $this->convert_text_control_to_atomic( $control ),
		];

		if ( ! isset( $map[ $control['type'] ] ) ) {
			return null;
		}

		$is_convertable = ! isset( $control['name'], $control['section'], $control['label'], $control['default'] );

		if ( $is_convertable ) {
			throw new \Exception( 'Control must have name, section, label, and default' );
		}

		return $map[ $control['type'] ]( $control );
	}

	/**
	 * @param $control
	 *
	 * @return Select_Control
	 * @throws \Exception If control is missing options.
	 */
	private function convert_select_control_to_atomic( $control, $tag = [] ) {
		$options = $this->extract_select_options_from_control( $control );

		if ( empty( $options ) ) {
			throw new \Exception( 'Select control must have options' );
		}

		$options = apply_filters( 'elementor/atomic/dynamic_tags/select_control_options', $options, $control, $tag );

		$options = array_map(
			fn( $key, $value ) => [
				'value' => $key,
				'label' => $value,
			],
			array_keys( $options ),
			$options
		);

		$select_control = Select_Control::bind_to( $control['name'] )
			->set_placeholder( $control['placeholder'] ?? '' )
			->set_options( $options )
			->set_label( $control['atomic_label'] ?? $control['label'] );

		if ( isset( $control['collection_id'] ) ) {
			$select_control->set_collection_id( $control['collection_id'] );
		}

		return $select_control;
	}

	private function extract_select_options_from_control( $control ): array {
		$options = $control['options'] ?? [];

		if ( ! empty( $options ) ) {
			return $options;
		}

		if ( empty( $control['groups'] ) || ! is_array( $control['groups'] ) ) {
			return $options;
		}

		foreach ( $control['groups'] as $group ) {
			if ( empty( $group['options'] ) || ! is_array( $group['options'] ) ) {
				continue;
			}

			$filtered = array_filter(
				$group['options'],
				static function ( $label, $key ) {
					return is_string( $key );
				},
				ARRAY_FILTER_USE_BOTH
			);

			$options = array_merge( $options, $filtered );
		}

		return $options;
	}

	/**
	 * @param $control
	 *
	 * @return Text_Control
	 */
	private function convert_text_control_to_atomic( $control ) {
		return Text_Control::bind_to( $control['name'] )
			->set_label( $control['label'] );
	}

	/**
	 * @param $control
	 *
	 * @return Switch_Control
	 */
	private function convert_switch_control_to_atomic( $control ) {
		return Switch_Control::bind_to( $control['name'] )
			->set_label( $control['atomic_label'] ?? $control['label'] );
	}

	/**
	 * @param $control
	 *
	 * @return Number_Control
	 */
	private function convert_number_control_to_atomic( $control ) {
		return Number_Control::bind_to( $control['name'] )
			->set_placeholder( $control['placeholder'] ?? '' )
			->set_max( $control['max'] ?? null )
			->set_min( $control['min'] ?? null )
			->set_step( $control['step'] ?? null )
			->set_should_force_int( $control['should_force_int'] ?? false )
			->set_label( $control['label'] );
	}

	private function convert_textarea_control_to_atomic( $control ) {
		return Textarea_Control::bind_to( $control['name'] )
			->set_placeholder( $control['placeholder'] ?? '' )
			->set_label( $control['label'] );
	}

	private function convert_autocomplete_control_to_atomic( $control ) {
		$query_config = [];
		$query_type = Post_Query::ENDPOINT;

		switch ( true ) {
			case $this->is_querying_wp_terms( $control ):
				$query_type = Term_Query::ENDPOINT;
				$included_types = null;
				$excluded_types = null;
				break;

			case $this->is_control_elementor_query( $control ):
				$included_types = [ Source_Local::CPT ];
				$excluded_types = [];
				break;

			case $this->is_querying_wp_media( $control ):
				$included_types = [ 'attachment' ];
				$excluded_types = [];
				$query_config[ Query_Base::IS_PUBLIC_KEY ] = false;
				break;

			case $this->is_querying_wp_users( $control ):
				$included_types = [ $control['autocomplete']['object'] ];
				$excluded_types = null;
				$query_type = User_Query::ENDPOINT;
				break;

			default:
				$included_types = isset( $control['autocomplete']['query']['post_type'] ) ? $control['autocomplete']['query']['post_type'] : [];
				$included_types = ! empty( $included_types ) && 'any' !== $included_types ? $included_types : null;
				$excluded_types = null;
		}

		$query_config[ Query_Base::ITEMS_COUNT_KEY ] = $this->extract_item_count_from_control( $control );
		$post_status[ Query_Base::IS_PUBLIC_KEY ] = $this->extract_post_status_from_control( $control );
		$query_config[ Query_Base::INCLUDED_TYPE_KEY ] = $included_types;
		$query_config[ Query_Base::EXCLUDED_TYPE_KEY ] = $excluded_types;
		$query_config[ Query_Builder_Factory::ENDPOINT_KEY ] = $query_type;
		$query_config[ Query_Base::META_QUERY_KEY ] = $this->extract_meta_query_from_control( $control );

		$query_control = Query_Control::bind_to( $control['name'] );
		$query_control->set_query_config( $query_config );
		$query_control->set_placeholder( $control['placeholder'] ?? '' );
		$query_control->set_label( $control['label'] );
		$query_control->set_allow_custom_values( false );

		return $query_control;
	}

	private function is_control_elementor_query( $control ): bool {
		return isset( $control['autocomplete']['object'] ) && 'library_template' === $control['autocomplete']['object'];
	}

	private function is_querying_wp_terms( $control ): bool {
		return isset( $control['autocomplete']['object'] ) && in_array( $control['autocomplete']['object'], [ 'tax', 'taxonomy', 'term' ], true );
	}

	private function is_querying_wp_media( $control ): bool {
		return isset( $control['autocomplete']['object'] ) && 'attachment' === $control['autocomplete']['object'];
	}

	private function is_querying_wp_users( $control ): bool {
		global $wp_roles;
		$roles = array_keys( $wp_roles->roles );

		return isset( $control['autocomplete']['object'] ) && in_array( $control['autocomplete']['object'], $roles, true );
	}

	private function convert_choose_control_to_atomic( $control ) {
		return Toggle_Control::bind_to( $control['name'] )
			->set_label( $control['atomic_label'] ?? $control['label'] )
			->add_options( $control['options'] )
			->set_size( 'tiny' )
			->set_exclusive( true )
			->set_convert_options( true );
	}

	private function convert_media_control_to_atomic( $control ) {
		return Image_Control::bind_to( $control['name'] )
			->set_show_mode( 'media' )
			->set_label( $control['label'] );
	}

	private function extract_post_status_from_control( $control ): ?bool {
		$status = $control['autocomplete']['query']['post_status'] ?? null;

		return isset( $status ) && in_array( 'private', $status )
			? false
			: null;
	}

	private function extract_item_count_from_control( $control ): ?int {
		$count = $control['autocomplete']['query']['posts_per_page'] ?? null;

		return isset( $count ) && is_numeric( $count )
			? $count
			: null;
	}

	private function extract_meta_query_from_control( $control ): ?array {
		return $control['autocomplete']['query']['meta_query'] ?? null;
	}
}
