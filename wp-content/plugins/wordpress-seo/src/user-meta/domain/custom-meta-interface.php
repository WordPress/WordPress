<?php

namespace Yoast\WP\SEO\User_Meta\Domain;

/**
 * This interface describes a custom meta.
 */
interface Custom_Meta_Interface {

	/**
	 * Returns the priority which the custom meta's form field should be rendered with.
	 *
	 * @return int.
	 */
	public function get_render_priority(): int;

	/**
	 * Returns the db key of the custom meta.
	 *
	 * @return string
	 */
	public function get_key(): string;

	/**
	 * Returns the id of the custom meta's form field.
	 *
	 * @return string
	 */
	public function get_field_id(): string;

	/**
	 * Returns the meta value.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return string
	 */
	public function get_value( $user_id ): string;

	/**
	 * Returns whether the respective global setting is enabled.
	 *
	 * @return bool
	 */
	public function is_setting_enabled(): bool;

	/**
	 * Returns whether the custom meta is allowed to be empty.
	 *
	 * @return bool
	 */
	public function is_empty_allowed(): bool;

	/**
	 * Renders the custom meta's field in the user form.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return void
	 */
	public function render_field( $user_id ): void;
}
