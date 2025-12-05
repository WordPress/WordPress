<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Represents the yoast columns.
 */
class WPSEO_Yoast_Columns implements WPSEO_WordPress_Integration {

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'load-edit.php', [ $this, 'add_help_tab' ] );
	}

	/**
	 * Adds the help tab to the help center for current screen.
	 *
	 * @return void
	 */
	public function add_help_tab() {
		$link_columns_present = $this->display_links();
		$meta_columns_present = $this->display_meta_columns();
		if ( ! ( $link_columns_present || $meta_columns_present ) ) {
			return;
		}

		$help_tab_content = sprintf(
			/* translators: %1$s: Yoast SEO */
			__( '%1$s adds several columns to this page.', 'wordpress-seo' ),
			'Yoast SEO'
		);

		if ( $meta_columns_present ) {
			$help_tab_content .= ' ' . sprintf(
				/* translators: %1$s: Link to article about content analysis, %2$s: Anchor closing */
				__( 'We\'ve written an article about %1$show to use the SEO score and Readability score%2$s.', 'wordpress-seo' ),
				'<a href="' . WPSEO_Shortlinker::get( 'https://yoa.st/16p' ) . '">',
				'</a>'
			);
		}

		if ( $link_columns_present ) {
			$help_tab_content .= ' ' . sprintf(
				/* translators: %1$s: Link to article about text links, %2$s: Anchor closing tag, %3$s: Emphasis open tag, %4$s: Emphasis close tag */
				__( 'The links columns show the number of articles on this site linking %3$sto%4$s this article and the number of URLs linked %3$sfrom%4$s this article. Learn more about %1$show to use these features to improve your internal linking%2$s, which greatly enhances your SEO.', 'wordpress-seo' ),
				'<a href="' . WPSEO_Shortlinker::get( 'https://yoa.st/16p' ) . '">',
				'</a>',
				'<em>',
				'</em>'
			);
		}

		$screen = get_current_screen();
		$screen->add_help_tab(
			[
				/* translators: %s expands to Yoast */
				'title'    => sprintf( __( '%s Columns', 'wordpress-seo' ), 'Yoast' ),
				'id'       => 'yst-columns',
				'content'  => '<p>' . $help_tab_content . '</p>',
				'priority' => 15,
			]
		);
	}

	/**
	 * Retrieves the post type from the $_GET variable.
	 *
	 * @return string The current post type.
	 */
	private function get_current_post_type() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['post_type'] ) && is_string( $_GET['post_type'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return sanitize_text_field( wp_unslash( $_GET['post_type'] ) );
		}
		return '';
	}

	/**
	 * Whether we are showing link columns on this overview page.
	 * This depends on the post being accessible or not.
	 *
	 * @return bool Whether the linking columns are shown
	 */
	private function display_links() {
		$current_post_type = $this->get_current_post_type();

		if ( empty( $current_post_type ) ) {
			return false;
		}

		return WPSEO_Post_Type::is_post_type_accessible( $current_post_type );
	}

	/**
	 * Wraps the WPSEO_Metabox check to determine whether the metabox should be displayed either by
	 * choice of the admin or because the post type is not a public post type.
	 *
	 * @return bool Whether the meta box (and associated columns etc) should be hidden.
	 */
	private function display_meta_columns() {
		$current_post_type = $this->get_current_post_type();

		if ( empty( $current_post_type ) ) {
			return false;
		}

		return WPSEO_Utils::is_metabox_active( $current_post_type, 'post_type' );
	}
}
