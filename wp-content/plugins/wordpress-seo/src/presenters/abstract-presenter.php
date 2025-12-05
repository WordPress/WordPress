<?php

namespace Yoast\WP\SEO\Presenters;

/**
 * Abstract_Presenter class.
 */
abstract class Abstract_Presenter {

	/**
	 * Returns the output as string.
	 *
	 * @return string The output.
	 */
	abstract public function present();

	/**
	 * Returns the output as string.
	 *
	 * @return string The output.
	 */
	public function __toString() {
		return $this->present();
	}
}
