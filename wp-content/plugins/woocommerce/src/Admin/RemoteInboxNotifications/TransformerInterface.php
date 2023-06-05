<?php

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

use stdClass;

/**
 * An interface to define a transformer.
 *
 * Interface TransformerInterface
 *
 * @package Automattic\WooCommerce\Admin\RemoteInboxNotifications
 */
interface TransformerInterface {
	/**
	 * Transform given value to a different value.
	 *
	 * @param mixed         $value a value to transform.
	 * @param stdClass|null $arguments arguments.
	 * @param string|null   $default default value.
	 *
	 * @return mixed|null
	 */
	public function transform( $value, stdClass $arguments = null, $default = null);

	/**
	 * Validate Transformer arguments.
	 *
	 * @param stdClass|null $arguments arguments to validate.
	 *
	 * @return mixed
	 */
	public function validate( stdClass $arguments = null );
}
