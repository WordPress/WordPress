<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Workarounds\FluentSmtp;

class PHPMailerProxy {
	private $wrapped;

	public function __construct( $phpmailer ) {
		$this->wrapped = $phpmailer;
	}

	// @codingStandardsIgnoreStart

	public function __get( $name )
	{
		return $this->wrapped->$name;
	}

	public function __set( $name, $value )
	{
		$this->wrapped->$name = $value;
	}

	public function __call( $name , $arguments ) {
		if ( $name == 'addAttachment' ) {
			return;
		}

		return call_user_func_array([$this->wrapped, $name], $arguments);
	}

	// @codingStandardsIgnoreEnd
}
