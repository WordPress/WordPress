<?php
/**
 * The design guide ability, which hands agents that work on the site through
 * the Abilities API what Ipsum is built with and how it is meant to be used.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

if ( ! function_exists( 'ipsum_register_abilities' ) ) :
	/**
	 * Registers the design guide ability, so agents that work on the site
	 * through the Abilities API, rather than this repository, can learn how
	 * Ipsum is meant to be built with.
	 *
	 * It needs `edit_theme_options`, the capability behind the Site Editor and
	 * Global Styles, since the guide is for whoever builds the site's design.
	 *
	 * @since Ipsum 1.0
	 *
	 * @return void
	 */
	function ipsum_register_abilities() {
		$output_schema = ipsum_get_design_guide_schema();

		wp_register_ability(
			'ipsum/get-design-guide',
			array(
				'label'                 => __( 'Get Design Guide', 'ipsum' ),
				'description'           => __( 'Returns the design guide of the Ipsum theme, read from the site as it is now: the color palette and what each color is for, whether it is light or dark, fonts, font sizes, spacing, layout widths, style variations, block styles, patterns, and the templates and template parts with the pattern that fills each. Read it before building pages, templates or styles. Pass fields to get only some sections.', 'ipsum' ),
				'category'              => 'site',
				'input_schema'          => array(
					'type'                 => 'object',
					'properties'           => array(
						'fields' => array(
							'type'        => 'array',
							'items'       => array(
								'type' => 'string',
								'enum' => array_keys( $output_schema['properties'] ),
							),
							'description' => __( 'Optional. Limits the guide to these sections. All sections are returned when it is omitted.', 'ipsum' ),
						),
					),
					'additionalProperties' => false,
					'default'              => array(),
				),
				'output_schema'         => $output_schema,
				'execute_callback'      => 'ipsum_get_design_guide',
				'required_capabilities' => array( 'edit_theme_options' ),
				'meta'                  => array(
					'annotations' => array(
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					),
					'public'      => true,
				),
			)
		);
	}
endif;
add_action( 'wp_abilities_api_init', 'ipsum_register_abilities' );

if ( ! function_exists( 'ipsum_get_color_roles' ) ) :
	/**
	 * Returns what each palette color is for, keyed by its slug.
	 *
	 * Every color variation redefines the same slugs, so the roles hold under
	 * whichever variation is active.
	 *
	 * @since Ipsum 1.0
	 *
	 * @return array<string, string> Role descriptions, keyed by palette slug.
	 */
	function ipsum_get_color_roles() {
		return array(
			'base'     => __( 'The page background.', 'ipsum' ),
			'contrast' => __( 'Body text, headings, links and buttons, on Base. It can be translucent, so on any other background use Strong Contrast.', 'ipsum' ),
			'accent-1' => __( 'An accent for small highlights. The theme uses it only for the arrival bar. Links and buttons use Contrast, not this. Use it sparingly.', 'ipsum' ),
			'accent-3' => __( 'The strongest contrast with the background, not an accent: hover states, and the background of inverted sections such as the navigation overlay, with Base for the text.', 'ipsum' ),
			'accent-5' => __( 'A subtle, translucent wash behind quotes, tags and table headers. It works on any background; keep Contrast text on it.', 'ipsum' ),
			'accent-6' => __( 'Faint, translucent lines, such as borders, list markers and table rules. Not for text.', 'ipsum' ),
		);
	}
endif;

if ( ! function_exists( 'ipsum_get_design_guide_schema' ) ) :
	/**
	 * Returns the JSON Schema of the design guide, with one property per section.
	 *
	 * @since Ipsum 1.0
	 *
	 * @return array The output schema of the design guide ability.
	 */
	function ipsum_get_design_guide_schema() {
		$string      = array( 'type' => 'string' );
		$nullable    = array( 'type' => array( 'string', 'null' ) );
		$strings     = array(
			'type'  => 'array',
			'items' => $string,
		);
		$scheme      = array(
			'type' => 'string',
			'enum' => array( 'light', 'dark' ),
		);
		$object_list = static function ( $description, $properties ) {
			return array(
				'type'        => 'array',
				'description' => $description,
				'items'       => array(
					'type'       => 'object',
					'properties' => $properties,
				),
			);
		};
		$preset_list = static function ( $description, $value_key, $value_schema ) use ( $object_list, $string ) {
			return $object_list(
				$description,
				array(
					'slug'     => $string,
					'name'     => $string,
					$value_key => $value_schema,
				)
			);
		};

		$colors = $preset_list( __( 'The color palette, with what each color is for. Every style variation redefines the same slugs. Colors added on the site have no role.', 'ipsum' ), 'color', $string );

		$colors['items']['properties']['role'] = $string;

		return array(
			'type'                 => 'object',
			'additionalProperties' => false,
			'properties'           => array(
				'theme'             => array(
					'type'        => 'object',
					'description' => __( 'The active theme. Its stylesheet is the one to use in REST routes such as /wp/v2/global-styles/themes/{stylesheet}.', 'ipsum' ),
					'properties'  => array(
						'name'       => $string,
						'stylesheet' => $string,
					),
				),
				'colorScheme'       => array_merge(
					$scheme,
					array( 'description' => __( 'Whether the active palette is light or dark.', 'ipsum' ) )
				),
				'colors'            => $colors,
				'gradients'         => $preset_list( __( 'The gradient presets.', 'ipsum' ), 'gradient', $string ),
				'duotone'           => $preset_list( __( 'The duotone filter presets.', 'ipsum' ), 'colors', $strings ),
				'typography'        => array(
					'type'        => 'object',
					'description' => __( 'The font families of body text and headings, by slug, and the font family and font size presets.', 'ipsum' ),
					'properties'  => array(
						'bodyFont'     => $nullable,
						'headingFont'  => $nullable,
						'blockFonts'   => array(
							'type'                 => 'object',
							'description'          => __( 'The blocks set in another font than body text, such as buttons, navigation and post meta, grouped by font family slug.', 'ipsum' ),
							'additionalProperties' => $strings,
						),
						'fontFamilies' => $preset_list( __( 'The font family presets.', 'ipsum' ), 'fontFamily', $string ),
						'fontSizes'    => $preset_list( __( 'The font size presets, smallest first.', 'ipsum' ), 'size', $string ),
					),
				),
				'spacing'           => $preset_list( __( 'The spacing scale, smallest first.', 'ipsum' ), 'size', $string ),
				'layout'            => array(
					'type'        => 'object',
					'description' => __( 'The width of the content, and of wide-aligned blocks.', 'ipsum' ),
					'properties'  => array(
						'contentSize' => $nullable,
						'wideSize'    => $nullable,
					),
				),
				'styleVariations'   => $object_list(
					__( 'The style variations. Each one lists only what it changes: colorScheme and colors for the palette, bodyFont and headingFont for the fonts, and blocks for the style properties it sets on each block. Their full data is at /wp/v2/global-styles/themes/{stylesheet}/variations.', 'ipsum' ),
					array(
						'title'       => $string,
						'colorScheme' => $scheme,
						'colors'      => array(
							'type'                 => 'object',
							'additionalProperties' => $string,
						),
						'bodyFont'    => $nullable,
						'headingFont' => $nullable,
						'blocks'      => array(
							'type'                 => 'object',
							'additionalProperties' => $strings,
						),
					)
				),
				'blockStyles'       => $object_list(
					__( 'The block styles registered on this site, beyond the ones blocks define themselves. Add className to a block of one of the blockTypes to apply it.', 'ipsum' ),
					array(
						'name'       => $string,
						'label'      => $string,
						'className'  => $string,
						'blockTypes' => $strings,
					)
				),
				'patterns'          => $object_list(
					__( 'The patterns of the theme, without their markup, which is at /wp/v2/block-patterns/patterns. Hidden patterns fill templates and template parts and are left out of the inserter.', 'ipsum' ),
					array(
						'slug'          => $string,
						'title'         => $string,
						'description'   => $string,
						'categories'    => $strings,
						'blockTypes'    => $strings,
						'templateTypes' => $strings,
						'postTypes'     => $strings,
						'hidden'        => array( 'type' => 'boolean' ),
					)
				),
				'templates'         => $object_list(
					__( 'The templates, with the pattern that fills each. The pattern is null when a template holds its own markup, as it does once it is customized.', 'ipsum' ),
					array(
						'slug'        => $string,
						'title'       => $string,
						'description' => $string,
						'pattern'     => $nullable,
						'customized'  => array( 'type' => 'boolean' ),
						'postTypes'   => $strings,
					)
				),
				'templateParts'     => $object_list(
					__( 'The template parts, with their area and the pattern that fills each.', 'ipsum' ),
					array(
						'slug'       => $string,
						'title'      => $string,
						'area'       => $string,
						'pattern'    => $nullable,
						'customized' => array( 'type' => 'boolean' ),
					)
				),
				'templatePartAreas' => $object_list(
					__( 'The areas a template part can belong to.', 'ipsum' ),
					array(
						'area'        => $string,
						'label'       => $string,
						'description' => $string,
					)
				),
				'guidelines'        => array_merge(
					$strings,
					array( 'description' => __( 'How the theme is meant to be built with.', 'ipsum' ) )
				),
			),
		);
	}
endif;

if ( ! function_exists( 'ipsum_get_design_guide_presets' ) ) :
	/**
	 * Lists the theme's presets of one kind, followed by any the site adds.
	 *
	 * @since Ipsum 1.0
	 *
	 * @param array    $presets The presets from the global settings, keyed by origin.
	 * @param string[] $keys    The preset properties to keep.
	 * @return array[] The presets, with only the given properties.
	 */
	function ipsum_get_design_guide_presets( $presets, $keys ) {
		$items = array();
		foreach ( array( 'theme', 'custom' ) as $origin ) {
			foreach ( $presets[ $origin ] ?? array() as $preset ) {
				$item = array();
				foreach ( $keys as $key ) {
					if ( isset( $preset[ $key ] ) ) {
						$item[ $key ] = $preset[ $key ];
					}
				}
				$items[] = $item;
			}
		}
		return $items;
	}
endif;

if ( ! function_exists( 'ipsum_get_design_guide_font_slug' ) ) :
	/**
	 * Returns the slug of the font family preset a style refers to.
	 *
	 * @since Ipsum 1.0
	 *
	 * @param mixed $font_family A fontFamily style value.
	 * @return string|null The preset slug, the value itself when it is not a
	 *                     preset, or null when there is no value.
	 */
	function ipsum_get_design_guide_font_slug( $font_family ) {
		if ( ! is_string( $font_family ) || '' === $font_family ) {
			return null;
		}
		if ( preg_match( '/^var(?::preset\|font-family\||\(--wp--preset--font-family--)([a-z0-9-]+)\)?$/', $font_family, $matches ) ) {
			return $matches[1];
		}
		return $font_family;
	}
endif;

if ( ! function_exists( 'ipsum_get_design_guide_pattern_slug' ) ) :
	/**
	 * Returns the pattern a template or template part delegates to.
	 *
	 * @since Ipsum 1.0
	 *
	 * @param string $content The template or template part markup.
	 * @return string|null The pattern slug, or null when the markup is not a
	 *                     single Pattern block.
	 */
	function ipsum_get_design_guide_pattern_slug( $content ) {
		$blocks = array_values(
			array_filter(
				parse_blocks( (string) $content ),
				static function ( $block ) {
					return null !== $block['blockName'];
				}
			)
		);
		if ( 1 === count( $blocks ) && 'core/pattern' === $blocks[0]['blockName'] && ! empty( $blocks[0]['attrs']['slug'] ) ) {
			return $blocks[0]['attrs']['slug'];
		}
		return null;
	}
endif;

if ( ! function_exists( 'ipsum_get_design_guide' ) ) :
	/**
	 * Builds the design guide from the site as it is, so it follows the active
	 * style variation and any change made in Global Styles or the Site Editor.
	 *
	 * @since Ipsum 1.0
	 *
	 * @param array $input {
	 *     The ability input.
	 *
	 *     @type string[] $fields Optional. The sections to return. Default all of them.
	 * }
	 * @return array The design guide.
	 */
	function ipsum_get_design_guide( $input = array() ) {
		$sections = array_keys( ipsum_get_design_guide_schema()['properties'] );
		if ( ! empty( $input['fields'] ) ) {
			$sections = array_intersect( $sections, $input['fields'] );
		}
		$sections = array_flip( $sections );

		// Resolving the merged data also registers the block styles in styles/blocks.
		$settings = wp_get_global_settings();
		$styles   = wp_get_global_styles();
		$guide    = array();

		if ( isset( $sections['theme'] ) ) {
			$guide['theme'] = array(
				'name'       => wp_get_theme()->get( 'Name' ),
				'stylesheet' => get_stylesheet(),
			);
		}

		if ( isset( $sections['colorScheme'] ) ) {
			// The audio player's CSS also reads a palette that sets no scheme as light.
			$guide['colorScheme'] = 'dark' === ( $settings['custom']['controlScheme'] ?? '' ) ? 'dark' : 'light';
		}

		if ( isset( $sections['colors'] ) ) {
			$roles           = ipsum_get_color_roles();
			$guide['colors'] = array();
			foreach ( ipsum_get_design_guide_presets( $settings['color']['palette'] ?? array(), array( 'slug', 'name', 'color' ) ) as $color ) {
				if ( isset( $color['slug'], $roles[ $color['slug'] ] ) ) {
					$color['role'] = $roles[ $color['slug'] ];
				}
				$guide['colors'][] = $color;
			}
		}

		if ( isset( $sections['gradients'] ) ) {
			$guide['gradients'] = ipsum_get_design_guide_presets( $settings['color']['gradients'] ?? array(), array( 'slug', 'name', 'gradient' ) );
		}

		if ( isset( $sections['duotone'] ) ) {
			$guide['duotone'] = ipsum_get_design_guide_presets( $settings['color']['duotone'] ?? array(), array( 'slug', 'name', 'colors' ) );
		}

		if ( isset( $sections['typography'] ) ) {
			$body_font   = ipsum_get_design_guide_font_slug( $styles['typography']['fontFamily'] ?? '' );
			$block_fonts = array();
			foreach ( $styles['blocks'] ?? array() as $block_type => $block_styles ) {
				$block_font = ipsum_get_design_guide_font_slug( $block_styles['typography']['fontFamily'] ?? '' );
				if ( null !== $block_font && $block_font !== $body_font ) {
					$block_fonts[ $block_font ][] = $block_type;
				}
			}
			$guide['typography'] = array(
				'bodyFont'     => $body_font,
				'headingFont'  => ipsum_get_design_guide_font_slug( $styles['elements']['heading']['typography']['fontFamily'] ?? '' ) ?? $body_font,
				'blockFonts'   => $block_fonts,
				'fontFamilies' => ipsum_get_design_guide_presets( $settings['typography']['fontFamilies'] ?? array(), array( 'slug', 'name', 'fontFamily' ) ),
				'fontSizes'    => ipsum_get_design_guide_presets( $settings['typography']['fontSizes'] ?? array(), array( 'slug', 'name', 'size' ) ),
			);
		}

		if ( isset( $sections['spacing'] ) ) {
			$guide['spacing'] = ipsum_get_design_guide_presets( $settings['spacing']['spacingSizes'] ?? array(), array( 'slug', 'name', 'size' ) );
		}

		if ( isset( $sections['layout'] ) ) {
			$guide['layout'] = array(
				'contentSize' => $settings['layout']['contentSize'] ?? null,
				'wideSize'    => $settings['layout']['wideSize'] ?? null,
			);
		}

		if ( isset( $sections['styleVariations'] ) ) {
			$guide['styleVariations'] = array();
			foreach ( WP_Theme_JSON_Resolver::get_style_variations() as $variation ) {
				$variation_settings = $variation['settings'] ?? array();
				$variation_styles   = $variation['styles'] ?? array();
				$item               = array( 'title' => $variation['title'] );

				if ( isset( $variation_settings['custom']['controlScheme'] ) ) {
					$item['colorScheme'] = 'dark' === $variation_settings['custom']['controlScheme'] ? 'dark' : 'light';
				}
				if ( ! empty( $variation_settings['color']['palette'] ) ) {
					$item['colors'] = array_column( ipsum_get_design_guide_presets( $variation_settings['color']['palette'], array( 'slug', 'color' ) ), 'color', 'slug' );
				}
				if ( ! empty( $variation_styles['typography']['fontFamily'] ) ) {
					$item['bodyFont'] = ipsum_get_design_guide_font_slug( $variation_styles['typography']['fontFamily'] );
				}
				if ( ! empty( $variation_styles['elements']['heading']['typography']['fontFamily'] ) ) {
					$item['headingFont'] = ipsum_get_design_guide_font_slug( $variation_styles['elements']['heading']['typography']['fontFamily'] );
				}
				if ( ! empty( $variation_styles['blocks'] ) ) {
					$item['blocks'] = array_map( 'array_keys', $variation_styles['blocks'] );
				}
				$guide['styleVariations'][] = $item;
			}
		}

		if ( isset( $sections['blockStyles'] ) ) {
			$block_styles = array();
			foreach ( WP_Block_Styles_Registry::get_instance()->get_all_registered() as $block_type => $block_type_styles ) {
				foreach ( $block_type_styles as $name => $style ) {
					if ( ! isset( $block_styles[ $name ] ) ) {
						$block_styles[ $name ] = array(
							'name'       => $name,
							'label'      => $style['label'] ?? $name,
							'className'  => 'is-style-' . $name,
							'blockTypes' => array(),
						);
					}
					$block_styles[ $name ]['blockTypes'][] = $block_type;
				}
			}
			$guide['blockStyles'] = array_values( $block_styles );
		}

		if ( isset( $sections['patterns'] ) ) {
			$theme_pattern_dirs = array(
				wp_normalize_path( get_stylesheet_directory() . '/patterns' ),
				wp_normalize_path( get_template_directory() . '/patterns' ),
			);
			$guide['patterns']  = array();
			foreach ( WP_Block_Patterns_Registry::get_instance()->get_all_registered() as $pattern ) {
				if ( empty( $pattern['filePath'] ) || ! in_array( wp_normalize_path( dirname( $pattern['filePath'] ) ), $theme_pattern_dirs, true ) ) {
					continue;
				}
				$item = array(
					'slug'        => $pattern['name'],
					'title'       => $pattern['title'],
					'description' => $pattern['description'] ?? '',
					'categories'  => $pattern['categories'] ?? array(),
				);
				foreach ( array( 'blockTypes', 'templateTypes', 'postTypes' ) as $key ) {
					if ( ! empty( $pattern[ $key ] ) ) {
						$item[ $key ] = $pattern[ $key ];
					}
				}
				$item['hidden']      = isset( $pattern['inserter'] ) && ! $pattern['inserter'];
				$guide['patterns'][] = $item;
			}
		}

		foreach ( array(
			'templates'     => 'wp_template',
			'templateParts' => 'wp_template_part',
		) as $section => $template_type ) {
			if ( ! isset( $sections[ $section ] ) ) {
				continue;
			}
			$guide[ $section ] = array();
			foreach ( get_block_templates( array(), $template_type ) as $template ) {
				$item = array(
					'slug'  => $template->slug,
					'title' => $template->title,
				);
				if ( 'wp_template' === $template_type ) {
					$item['description'] = (string) $template->description;
				} else {
					$item['area'] = $template->area;
				}
				$item['pattern']    = ipsum_get_design_guide_pattern_slug( $template->content );
				$item['customized'] = 'custom' === $template->source;
				if ( ! empty( $template->post_types ) ) {
					$item['postTypes'] = $template->post_types;
				}
				$guide[ $section ][] = $item;
			}
		}

		if ( isset( $sections['templatePartAreas'] ) ) {
			$guide['templatePartAreas'] = array();
			foreach ( get_allowed_block_template_part_areas() as $area ) {
				$guide['templatePartAreas'][] = array(
					'area'        => $area['area'],
					'label'       => $area['label'],
					'description' => $area['description'],
				);
			}
		}

		if ( isset( $sections['guidelines'] ) ) {
			$guide['guidelines'] = array(
				__( 'Style with the presets in this guide, by slug, rather than with literal values. Every style variation redefines the colors, so a literal color keeps the look of one variation under all the others.', 'ipsum' ),
				__( 'Each template and template part holds a single Pattern block, and that pattern holds the markup. To change one on this site, save new markup to the template or template part itself.', 'ipsum' ),
				__( 'Hidden patterns are there to fill templates and template parts, not to be inserted on their own.', 'ipsum' ),
			);
		}

		return $guide;
	}
endif;
