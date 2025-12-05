<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Class Settings_Conditional.
 */
class Settings_Conditional implements Conditional {

	/**
	 * Holds User_Can_Manage_Wpseo_Options_Conditional.
	 *
	 * @var User_Can_Manage_Wpseo_Options_Conditional
	 */
	protected $user_can_manage_wpseo_options_conditional;

	/**
	 * Constructs Settings_Conditional.
	 *
	 * @param User_Can_Manage_Wpseo_Options_Conditional $user_can_manage_wpseo_options_conditional The User_Can_Manage_Wpseo_Options_Conditional.
	 */
	public function __construct(
		User_Can_Manage_Wpseo_Options_Conditional $user_can_manage_wpseo_options_conditional
	) {
		$this->user_can_manage_wpseo_options_conditional = $user_can_manage_wpseo_options_conditional;
	}

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		if ( ! $this->user_can_manage_wpseo_options_conditional->is_met() ) {
			return false;
		}

		return true;
	}
}
