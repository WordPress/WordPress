<?php

namespace Elementor;

/**
 * Elementor has validation interface.
 *
 * @param array $control_data The value to validate.
 * @return bool True on valid, throws an exception on error.
 * @throws \Exception If validation fails.
 */
interface Has_Validation {
	public function validate( array $control_data ): bool;
}
