<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\User_Meta\Framework\Custom_Meta;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\User_Meta\Domain\Custom_Meta_Interface;

/**
 * The Author_Pronouns custom meta.
 */
class Author_Pronouns implements Custom_Meta_Interface {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The constructor.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Returns the priority which the custom meta's form field should be rendered with.
	 *
	 * @return int The priority which the custom meta's form field should be rendered with.
	 */
	public function get_render_priority(): int {
		return 300;
	}

	/**
	 * Returns the db key of the Author_Pronouns custom meta.
	 *
	 * @return string The db key of the Author_Pronouns custom meta.
	 */
	public function get_key(): string {
		return 'wpseo_pronouns';
	}

	/**
	 * Returns the id of the custom meta's form field.
	 *
	 * @return string The id of the custom meta's form field.
	 */
	public function get_field_id(): string {
		return 'wpseo_author_pronouns';
	}

	/**
	 * Returns the meta value.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return string The meta value.
	 */
	public function get_value( $user_id ): string {
		return \get_the_author_meta( $this->get_key(), $user_id );
	}

	/**
	 * Returns whether the respective global setting is enabled.
	 *
	 * @return bool Whether the respective global setting is enabled.
	 */
	public function is_setting_enabled(): bool {
		return ( ! $this->options_helper->get( 'disable-author' ) );
	}

	/**
	 * Returns whether the custom meta is allowed to be empty.
	 *
	 * @return bool Whether the custom meta is allowed to be empty.
	 */
	public function is_empty_allowed(): bool {
		return true;
	}

	/**
	 * Renders the custom meta's field in the user form.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return void
	 */
	public function render_field( $user_id ): void {
		echo '

		<label for="' . \esc_attr( $this->get_field_id() ) . '">'
			. \esc_html__( 'Pronouns to use', 'wordpress-seo' )
		. '</label>';

		echo '

		<input
			class="yoast-settings__text regular-text"
			type="text" id="' . \esc_attr( $this->get_field_id() ) . '"
			name="' . \esc_attr( $this->get_field_id() ) . '"
			value="' . \esc_attr( $this->get_value( $user_id ) )
		. '"/><br>'
		. '<p class="description">' . \esc_html__( 'Enter the pronouns for the author, for example "she/her", "he/him", or "they/them".', 'wordpress-seo' ) . '</p>';
	}
}
