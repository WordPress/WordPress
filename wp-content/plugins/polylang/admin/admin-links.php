<?php

/**
 * manages links related functions
 *
 * @since 1.8
 */
class PLL_Admin_Links extends PLL_Links {

	/**
	 * get the link to create a new post translation
	 *
	 * @since 1.5
	 *
	 * @param int    $post_id  the source post id
	 * @param object $language the language of the new translation
	 * @return string
	 */
	public function get_new_post_translation_link( $post_id, $language ) {
		$post_type = get_post_type( $post_id );
		$post_type_object = get_post_type_object( get_post_type( $post_id ) );
		if ( ! current_user_can( $post_type_object->cap->create_posts ) ) {
			return '';
		}

		if ( 'attachment' == $post_type ) {
			$args = array(
				'action'     => 'translate_media',
				'from_media' => $post_id,
				'new_lang'   => $language->slug,
			);

			// add nonce for media as we will directly publish a new attachment from a click on this link
			$link = wp_nonce_url( add_query_arg( $args, admin_url( 'admin.php' ) ), 'translate_media' );
		} else {
			$args = array(
				'post_type' => $post_type,
				'from_post' => $post_id,
				'new_lang'  => $language->slug,
			);

			$link = add_query_arg( $args, admin_url( 'post-new.php' ) );
		}

		/**
		 * Filter the new post translation link
		 *
		 * @since 1.8
		 *
		 * @param string $link     the new post translation link
		 * @param object $language the language of the new translation
		 * @param int    $post_id  the source post id
		 */
		return apply_filters( 'pll_get_new_post_translation_link', $link, $language, $post_id );
	}

	/**
	 * returns html markup for a new post translation link
	 *
	 * @since 1.8
	 *
	 * @param int    $post_id
	 * @param object $language
	 * @return string
	 */
	public function new_post_translation_link( $post_id, $language ) {
		$link = $this->get_new_post_translation_link( $post_id, $language );
		return $link ? sprintf(
			'<a href="%1$s" class="pll_icon_add"><span class="screen-reader-text">%2$s</span></a>',
			esc_url( $link ),
			/* translators: accessibility text, %s is a native language name */
			esc_html( sprintf( __( 'Add a translation in %s', 'polylang' ), $language->name ) )
		) : '';
	}

	/**
	 * returns html markup for a translation link
	 *
	 * @since 1.4
	 *
	 * @param int $post_id translation post id
	 * @return string
	 */
	public function edit_post_translation_link( $post_id ) {
		$link = get_edit_post_link( $post_id );
		$language = $this->model->post->get_language( $post_id );
		return $link ? sprintf(
			'<a href="%1$s" class="pll_icon_edit"><span class="screen-reader-text">%2$s</span></a>',
			esc_url( $link ),
			/* translators: accessibility text, %s is a native language name */
			esc_html( sprintf( __( 'Edit the translation in %s', 'polylang' ), $language->name ) )
		) : '';
	}

	/**
	 * get the link to create a new term translation
	 *
	 * @since 1.5
	 *
	 * @param int    $term_id
	 * @param string $taxonomy
	 * @param string $post_type
	 * @param object $language
	 * @return string
	 */
	public function get_new_term_translation_link( $term_id, $taxonomy, $post_type, $language ) {
		$tax = get_taxonomy( $taxonomy );
		if ( ! $tax || ! current_user_can( $tax->cap->edit_terms ) ) {
			return '';
		}

		$args = array(
			'taxonomy'  => $taxonomy,
			'post_type' => $post_type,
			'from_tag'  => $term_id,
			'new_lang'  => $language->slug,
		);

		$link = add_query_arg( $args, admin_url( 'edit-tags.php' ) );

		/**
		 * Filter the new term translation link
		 *
		 * @since 1.8
		 *
		 * @param string $link      the new term translation link
		 * @param object $language  the language of the new translation
		 * @param int    $term_id   the source term id
		 * @param string $taxonomy
		 * @param string $post_type
		 */
		return apply_filters( 'pll_get_new_term_translation_link', $link, $language, $term_id, $taxonomy, $post_type );
	}

	/**
	 * returns html markup for a new term translation
	 *
	 * @since 1.8
	 *
	 * @param int    $term_id
	 * @param string $taxonomy
	 * @param string $post_type
	 * @param object $language
	 * @return string
	 */
	public function new_term_translation_link( $term_id, $taxonomy, $post_type, $language ) {
		$link = $this->get_new_term_translation_link( $term_id, $taxonomy, $post_type, $language );
		return $link ? sprintf(
			'<a href="%1$s" class="pll_icon_add"><span class="screen-reader-text">%2$s</span></a>',
			esc_url( $link ),
			/* translators: accessibility text, %s is a native language name */
			esc_html( sprintf( __( 'Add a translation in %s', 'polylang' ), $language->name ) )
		) : '';
	}

	/**
	 * returns html markup for a term translation link
	 *
	 * @since 1.4
	 *
	 * @param object $term_id   translation term id
	 * @param string $taxonomy
	 * @param string $post_type
	 * @return string
	 */
	public function edit_term_translation_link( $term_id, $taxonomy, $post_type ) {
		$link = get_edit_term_link( $term_id, $taxonomy, $post_type );
		$language = $this->model->term->get_language( $term_id );
		return $link ? sprintf(
			'<a href="%1$s" class="pll_icon_edit"><span class="screen-reader-text">%2$s</span></a>',
			esc_url( $link ),
			/* translators: accessibility text, %s is a native language name */
			esc_html( sprintf( __( 'Edit the translation in %s', 'polylang' ), $language->name ) )
		) : '';
	}
}

