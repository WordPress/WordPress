<?php

namespace Yoast\WP\SEO\Conditionals;

use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Conditional that is only met when the 'Redirect attachment URLs to the attachment itself' setting is enabled.
 */
class Attachment_Redirections_Enabled_Conditional implements Conditional {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * Attachment_Redirections_Enabled_Conditional constructor.
	 *
	 * @param Options_Helper $options The options helper.
	 */
	public function __construct( Options_Helper $options ) {
		$this->options = $options;
	}

	/**
	 * Returns whether the 'Redirect attachment URLs to the attachment itself' setting has been enabled.
	 *
	 * @return bool `true` when the 'Redirect attachment URLs to the attachment itself' setting has been enabled.
	 */
	public function is_met() {
		return $this->options->get( 'disable-attachment' );
	}
}
