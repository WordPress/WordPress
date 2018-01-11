<?php

/**
 * adds the language column in posts and terms list tables
 * manages quick edit and bulk edit as well
 *
 * @since 1.2
 */
class PLL_Admin_Filters_Columns {
	public $links, $model, $filter_lang;

	/**
	 * constructor: setups filters and actions
	 *
	 * @since 1.2
	 *
	 * @param object $polylang
	 */
	public function __construct( &$polylang ) {
		$this->links = &$polylang->links;
		$this->model = &$polylang->model;
		$this->filter_lang = &$polylang->filter_lang;

		// add the language and translations columns in 'All Posts', 'All Pages' and 'Media library' panels
		foreach ( $this->model->get_translated_post_types() as $type ) {
			// use the latest filter late as some plugins purely overwrite what's done by others :(
			// specific case for media
			add_filter( 'manage_' . ( 'attachment' == $type ? 'upload' : 'edit-' . $type ) . '_columns', array( $this, 'add_post_column' ), 100 );
			add_action( 'manage_' . ( 'attachment' == $type ? 'media' : $type . '_posts' ) . '_custom_column', array( $this, 'post_column' ), 10, 2 );
		}

		// quick edit and bulk edit
		add_filter( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );
		add_filter( 'bulk_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );

		// adds the language column in the 'Categories' and 'Post Tags' tables
		foreach ( $this->model->get_translated_taxonomies() as $tax ) {
			add_filter( 'manage_edit-' . $tax . '_columns', array( $this, 'add_term_column' ) );
			add_filter( 'manage_' . $tax . '_custom_column', array( $this, 'term_column' ), 10, 3 );
		}

		// ajax responses to update list table rows
		add_action( 'wp_ajax_pll_update_post_rows', array( $this, 'ajax_update_post_rows' ) );
		add_action( 'wp_ajax_pll_update_term_rows', array( $this, 'ajax_update_term_rows' ) );
	}

	/**
	 * adds languages and translations columns in posts, pages, media, categories and tags tables
	 *
	 * @since 0.8.2
	 *
	 * @param array  $columns List of table columns
	 * @param string $before  The column before which we want to add our languages
	 * @return array modified list of columns
	 */
	protected function add_column( $columns, $before ) {
		if ( $n = array_search( $before, array_keys( $columns ) ) ) {
			$end = array_slice( $columns, $n );
			$columns = array_slice( $columns, 0, $n );
		}

		foreach ( $this->model->get_languages_list() as $language ) {
			// don't add the column for the filtered language
			if ( empty( $this->filter_lang ) || $language->slug != $this->filter_lang->slug ) {
				$columns[ 'language_' . $language->slug ] = $language->flag ? $language->flag . '<span class="screen-reader-text">' . esc_html( $language->name ) . '</span>' : esc_html( $language->slug );
			}
		}

		return isset( $end ) ? array_merge( $columns, $end ) : $columns;
	}

	/**
	 * returns the first language column in the posts, pages and media library tables
	 *
	 * @since 0.9
	 *
	 * @return string first language column name
	 */
	protected function get_first_language_column() {
		foreach ( $this->model->get_languages_list() as $language ) {
			if ( empty( $this->filter_lang ) || $language->slug != $this->filter_lang->slug ) {
				$columns[] = 'language_' . $language->slug;
			}
		}

		return empty( $columns ) ? '' : reset( $columns );
	}

	/**
	 * adds the language and translations columns ( before the comments column ) in the posts, pages and media library tables
	 *
	 * @since 0.1
	 *
	 * @param array $columns list of posts table columns
	 * @return array modified list of columns
	 */
	function add_post_column( $columns ) {
		return $this->add_column( $columns, 'comments' );
	}

	/**
	 * fills the language and translations columns in the posts, pages and media library tables
	 * take care that when doing ajax inline edit, the post may not be updated in database yet
	 *
	 * @since 0.1
	 *
	 * @param string $column  Column name
	 * @param int    $post_id
	 */
	public function post_column( $column, $post_id ) {
		$inline = wp_doing_ajax() && isset( $_REQUEST['action'], $_POST['inline_lang_choice'] ) && 'inline-save' === $_REQUEST['action'];
		$lang = $inline ? $this->model->get_language( $_POST['inline_lang_choice'] ) : $this->model->post->get_language( $post_id );

		if ( false === strpos( $column, 'language_' ) || ! $lang ) {
			return;
		}

		$language = $this->model->get_language( substr( $column, 9 ) );

		// hidden field containing the post language for quick edit
		if ( $column == $this->get_first_language_column() ) {
			printf( '<div class="hidden" id="lang_%d">%s</div>', intval( $post_id ), esc_html( $lang->slug ) );
		}

		$post_type_object = get_post_type_object( get_post_type( $post_id ) );

		// link to edit post ( or a translation )
		if ( $id = $this->model->post->get( $post_id, $language ) ) {
			// get_edit_post_link returns nothing if the user cannot edit the post
			// thanks to Solinx. See http://wordpress.org/support/topic/feature-request-incl-code-check-for-capabilities-in-admin-screens
			if ( $link = get_edit_post_link( $id ) ) {
				if ( $id === $post_id ) {
					$class = 'pll_icon_tick';
					/* translators: accessibility text, %s is a native language name */
					$s = sprintf( __( 'Edit this item in %s', 'polylang' ), $language->name );
				} else {
					$class = esc_attr( 'pll_icon_edit translation_' . $id );
					/* translators: accessibility text, %s is a native language name */
					$s = sprintf( __( 'Edit the translation in %s', 'polylang' ), $language->name );
				}
				printf(
					'<a class="%1$s" title="%2$s" href="%3$s"><span class="screen-reader-text">%4$s</span></a>',
					esc_attr( $class ), esc_attr( get_post( $id )->post_title ), esc_url( $link ), esc_html( $s )
				);
			} elseif ( $id === $post_id ) {
				printf(
					'<span class="pll_icon_tick"><span class="screen-reader-text">%s</span></span>',
					/* translators: accessibility text, %s is a native language name */
					esc_html( sprintf( __( 'This item is in %s', 'polylang' ), $language->name ) )
				);
			}
		}
		// link to add a new translation
		else {
			echo $this->links->new_post_translation_link( $post_id, $language );
		}
	}

	/**
	 * quick edit & bulk edit
	 *
	 * @since 0.9
	 *
	 * @param string $column column name
	 * @param string $type either 'edit-tags' for terms list table or post type for posts list table
	 * @return string unmodified $column
	 */
	public function quick_edit_custom_box( $column, $type ) {
		if ( $column == $this->get_first_language_column() ) {

			$elements = $this->model->get_languages_list();
			if ( current_filter() == 'bulk_edit_custom_box' ) {
				array_unshift( $elements, (object) array( 'slug' => -1, 'name' => __( '&mdash; No Change &mdash;' ) ) );
			}

			$dropdown = new PLL_Walker_Dropdown();
			// the hidden field 'old_lang' allows to pass the old language to ajax request
			printf(
				'<fieldset class="inline-edit-col-left">
					<div class="inline-edit-col">
						<label class="alignleft">
							<span class="title">%s</span>
							%s
						</label>
					</div>
				</fieldset>',
				esc_html__( 'Language', 'polylang' ),
				$dropdown->walk( $elements, array( 'name' => 'inline_lang_choice', 'id' => '' ) )
			);
		}
		return $column;
	}

	/**
	 * adds the language column ( before the posts column ) in the 'Categories' or 'Post Tags' table
	 *
	 * @since 0.1
	 *
	 * @param array $columns list of terms table columns
	 * @return array modified list of columns
	 */
	public function add_term_column( $columns ) {
		return $this->add_column( $columns, 'posts' );
	}

	/**
	 * fills the language column in the 'Categories' or 'Post Tags' table
	 *
	 * @since 0.1
	 *
	 * @param string $out
	 * @param string $column  Column name
	 * @param int    $term_id
	 */
	public function term_column( $out, $column, $term_id ) {
		$inline = wp_doing_ajax() && isset( $_REQUEST['action'], $_POST['inline_lang_choice'] ) && 'inline-save-tax' === $_REQUEST['action'];
		if ( false === strpos( $column, 'language_' ) || ! ( $lang = $inline ? $this->model->get_language( $_POST['inline_lang_choice'] ) : $this->model->term->get_language( $term_id ) ) ) {
			return $out;
		}

		$post_type = isset( $GLOBALS['post_type'] ) ? $GLOBALS['post_type'] : $_REQUEST['post_type']; // 2nd case for quick edit
		$taxonomy = isset( $GLOBALS['taxonomy'] ) ? $GLOBALS['taxonomy'] : $_REQUEST['taxonomy'];

		if ( ! post_type_exists( $post_type ) || ! taxonomy_exists( $taxonomy ) ) {
			return $out;
		}

		$term_id = (int) $term_id;
		$language = $this->model->get_language( substr( $column, 9 ) );

		if ( $column == $this->get_first_language_column() ) {
			$out = sprintf( '<div class="hidden" id="lang_%d">%s</div>', intval( $term_id ), esc_html( $lang->slug ) );

			// identify the default categories to disable the language dropdown in js
			if ( in_array( get_option( 'default_category' ), $this->model->term->get_translations( $term_id ) ) ) {
				$out .= sprintf( '<div class="hidden" id="default_cat_%1$d">%1$d</div>', intval( $term_id ) );
			}
		}

		// link to edit term ( or a translation )
		if ( ( $id = $this->model->term->get( $term_id, $language ) ) && $term = get_term( $id, $taxonomy ) ) {
			if ( $link = get_edit_term_link( $id, $taxonomy, $post_type ) ) {
				if ( $id === $term_id ) {
					$class = 'pll_icon_tick';
					/* translators: accessibility text, %s is a native language name */
					$s = sprintf( __( 'Edit this item in %s', 'polylang' ), $language->name );
				} else {
					$class = esc_attr( 'pll_icon_edit translation_' . $id );
					/* translators: accessibility text, %s is a native language name */
					$s = sprintf( __( 'Edit the translation in %s', 'polylang' ), $language->name );
				}
				$out .= sprintf(
					'<a class="%1$s" title="%2$s" href="%3$s"><span class="screen-reader-text">%4$s</span></a>',
					$class, esc_attr( $term->name ), esc_url( $link ), esc_html( $s )
				);
			} elseif ( $id === $term_id ) {
				$out .= printf(
					'<span class="pll_icon_tick"><span class="screen-reader-text">%s</span></span>',
					/* translators: accessibility text, %s is a native language name */
					esc_html( sprintf( __( 'This item is in %s', 'polylang' ), $language->name ) )
				);
			}
		}

		// link to add a new translation
		else {
			$out .= $this->links->new_term_translation_link( $term_id, $taxonomy, $post_type, $language );
		}

		return $out;
	}

	/**
	 * update rows of translated posts when the language is modified in quick edit
	 *
	 * @since 1.7
	 */
	public function ajax_update_post_rows() {
		global $wp_list_table;

		if ( ! post_type_exists( $post_type = $_POST['post_type'] ) || ! $this->model->is_translated_post_type( $post_type ) ) {
			die( 0 );
		}

		check_ajax_referer( 'inlineeditnonce', '_pll_nonce' );

		$x = new WP_Ajax_Response();
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table', array( 'screen' => $_POST['screen'] ) );

		$translations = empty( $_POST['translations'] ) ? array() : explode( ',', $_POST['translations'] ); // collect old translations
		$translations = array_merge( $translations, array( $_POST['post_id'] ) ); // add current post
		$translations = array_map( 'intval', $translations );

		foreach ( $translations as $post_id ) {
			$level = is_post_type_hierarchical( $post_type ) ? count( get_ancestors( $post_id, $post_type ) ) : 0;
			if ( $post = get_post( $post_id ) ) {
				ob_start();
				$wp_list_table->single_row( $post, $level );
				$data = ob_get_clean();
				$x->add( array( 'what' => 'row', 'data' => $data, 'supplemental' => array( 'post_id' => $post_id ) ) );
			}
		}

		$x->send();
	}

	/**
	 * update rows of translated terms when adding / deleting a translation or when the language is modified in quick edit
	 *
	 * @since 1.7
	 */
	public function ajax_update_term_rows() {
		global $wp_list_table;

		if ( ! taxonomy_exists( $taxonomy = $_POST['taxonomy'] ) || ! $this->model->is_translated_taxonomy( $taxonomy ) ) {
			die( 0 );
		}

		check_ajax_referer( 'pll_language', '_pll_nonce' );

		$x = new WP_Ajax_Response();
		$wp_list_table = _get_list_table( 'WP_Terms_List_Table', array( 'screen' => $_POST['screen'] ) );

		$translations = empty( $_POST['translations'] ) ? array() : explode( ',', $_POST['translations'] ); // collect old translations
		$translations = array_merge( $translations, $this->model->term->get_translations( (int) $_POST['term_id'] ) ); // add current translations
		$translations = array_unique( $translations ); // remove duplicates
		$translations = array_map( 'intval', $translations );

		foreach ( $translations as $term_id ) {
			$level = is_taxonomy_hierarchical( $taxonomy ) ? count( get_ancestors( $term_id, $taxonomy ) ) : 0;
			if ( $tag = get_term( $term_id, $taxonomy ) ) {
				ob_start();
				$wp_list_table->single_row( $tag, $level );
				$data = ob_get_clean();
				$x->add( array( 'what' => 'row', 'data' => $data, 'supplemental' => array( 'term_id' => $term_id ) ) );
			}
		}

		$x->send();
	}
}
