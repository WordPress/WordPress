<?php

/**
 * Settings class for custom post types and taxonomies language and translation management
 *
 * @since 1.8
 */
class PLL_Settings_CPT extends PLL_Settings_Module {

	/**
	 * Constructor
	 *
	 * @since 1.8
	 *
	 * @param object $polylang polylang object
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang, array(
			'module'      => 'cpt',
			'title'       => __( 'Custom post types and Taxonomies', 'polylang' ),
			'description' => __( 'Activate the languages and translations management for the custom post types and taxonomies.', 'polylang' ),
		) );

		// FIXME should be OK when the modules will be loaded from the settings page
		$post_types = get_post_types( array( 'public' => true, '_builtin' => false ) );
		$post_types = array_diff( $post_types, get_post_types( array( '_pll' => true ) ) );
		/** This filter is documented in include/model.php */
		$this->post_types = array_unique( apply_filters( 'pll_get_post_types', $post_types, true ) );

		$taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => false ) );
		$taxonomies = array_diff( $taxonomies, get_taxonomies( array( '_pll' => true ) ) );
		/** This filter is documented in include/model.php */
		$this->taxonomies = array_unique( apply_filters( 'pll_get_taxonomies', $taxonomies, true ) );
	}

	/**
	 * Tells if the module is active
	 *
	 * @since 1.8
	 *
	 * @return bool
	 */
	public function is_active() {
		return ! empty( $this->post_types ) || ! empty( $this->taxonomies );
	}

	/**
	 * Displays the settings form
	 *
	 * @since 1.8
	 */
	protected function form() {
		if ( ! empty( $this->post_types ) ) {?>
			<h4><?php esc_html_e( 'Custom post types', 'polylang' ); ?></h4>
			<ul class="pll-inline-block-list">
				<?php
				foreach ( $this->post_types as $post_type ) {
					$pt = get_post_type_object( $post_type );
					if ( ! empty( $pt ) ) {
						printf(
							'<li><label><input name="post_types[%s]" type="checkbox" value="1" %s /> %s</label></li>',
							esc_attr( $post_type ),
							in_array( $post_type, $this->options['post_types'] ) ? 'checked="checked"' : '',
							esc_html( $pt->labels->name )
						);
					}
				}
				?>
			</ul>
			<p class="description"><?php esc_html_e( 'Activate languages and translations for custom post types.', 'polylang' ); ?></p>
			<?php
		}

		if ( ! empty( $this->taxonomies ) ) {
			?>
			<h4><?php esc_html_e( 'Custom taxonomies', 'polylang' ); ?></h4>
			<ul class="pll-inline-block-list">
				<?php
				foreach ( $this->taxonomies as $taxonomy ) {
					$tax = get_taxonomy( $taxonomy );
					if ( ! empty( $tax ) ) {
						printf(
							'<li><label><input name="taxonomies[%s]" type="checkbox" value="1" %s /> %s</label></li>',
							esc_attr( $taxonomy ),
							in_array( $taxonomy, $this->options['taxonomies'] ) ? 'checked="checked"' : '',
							esc_html( $tax->labels->name )
						);
					}
				}
				?>
			</ul>
			<p class="description"><?php esc_html_e( 'Activate languages and translations for custom taxonomies.', 'polylang' ); ?></p>
			<?php
		}
	}

	/**
	 * Sanitizes the settings before saving
	 *
	 * @since 1.8
	 *
	 * @param array $options
	 */
	protected function update( $options ) {
		foreach ( array( 'post_types', 'taxonomies' ) as $key ) {
			$newoptions[ $key ] = empty( $options[ $key ] ) ? array() : array_keys( $options[ $key ], 1 );
		}
		return $newoptions; // Take care to return only validated options
	}
}
