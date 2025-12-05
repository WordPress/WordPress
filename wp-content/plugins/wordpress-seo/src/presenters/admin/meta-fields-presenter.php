<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use WP_Post;
use WPSEO_Meta;
use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Presenter class for meta fields in the post editor.
 *
 * Outputs the hidden fields for a particular field group and post.
 */
class Meta_Fields_Presenter extends Abstract_Presenter {

	/**
	 * The meta fields for which we are going to output hidden input.
	 *
	 * @var array
	 */
	private $meta_fields;

	/**
	 * The metabox post.
	 *
	 * @var WP_Post The metabox post.
	 */
	private $post;

	/**
	 * Meta_Fields_Presenter constructor.
	 *
	 * @param WP_Post $post        The metabox post.
	 * @param string  $field_group The key under which a group of fields is grouped.
	 * @param string  $post_type   The post type.
	 */
	public function __construct( $post, $field_group, $post_type = 'post' ) {
		$this->post        = $post;
		$this->meta_fields = WPSEO_Meta::get_meta_field_defs( $field_group, $post_type );
	}

	/**
	 * Presents the Meta Fields.
	 *
	 * @return string The styled Alert.
	 */
	public function present() {
		$output = '';

		foreach ( $this->meta_fields as $key => $meta_field ) {
			$form_key   = \esc_attr( WPSEO_Meta::$form_prefix . $key );
			$meta_value = WPSEO_Meta::get_value( $key, $this->post->ID );

			$default = '';
			if ( isset( $meta_field['default'] ) ) {
				$default = \sprintf( ' data-default="%s"', \esc_attr( $meta_field['default'] ) );
			}

			$output .= '<input type="hidden" id="' . $form_key . '" name="' . $form_key . '" value="' . \esc_attr( $meta_value ) . '"' . $default . '/>' . "\n";
		}

		return $output;
	}
}
