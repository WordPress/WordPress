<?php
// @phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- This namespace should reflect the namespace of the original class.
namespace Yoast\WP\SEO\Editors\Framework\Integrations;

use Yoast\WP\SEO\Editors\Domain\Integrations\Integration_Data_Provider_Interface;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Wincher_Helper;

/**
 * Describes if the Wincher integration is enabled.
 */
class Wincher implements Integration_Data_Provider_Interface {

	/**
	 * The Wincher helper.
	 *
	 * @var Wincher_Helper
	 */
	private $wincher_helper;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The constructor.
	 *
	 * @param Wincher_Helper $wincher_helper The Wincher helper.
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Wincher_Helper $wincher_helper, Options_Helper $options_helper ) {
		$this->wincher_helper = $wincher_helper;
		$this->options_helper = $options_helper;
	}

	/**
	 * If the integration is activated.
	 *
	 * @return bool If the integration is activated.
	 */
	public function is_enabled(): bool {
		return $this->wincher_helper->is_active();
	}

	/**
	 * Return this object represented by a key value array.
	 *
	 * @return array<string, bool> Returns the name and if the feature is enabled.
	 */
	public function to_array(): array {
		return [
			'active'            => $this->is_enabled(),
			'loginStatus'       => $this->is_enabled() && $this->wincher_helper->login_status(),
			'websiteId'         => $this->options_helper->get( 'wincher_website_id', '' ),
			'autoAddKeyphrases' => $this->options_helper->get( 'wincher_automatically_add_keyphrases', false ),
		];
	}

	/**
	 * Returns this object represented by a key value structure that is compliant with the script data array.
	 *
	 * @return array<string, bool> Returns the legacy key and if the feature is enabled.
	 */
	public function to_legacy_array(): array {
		return [
			'wincherIntegrationActive' => $this->is_enabled(),
			'wincherLoginStatus'       => $this->is_enabled() && $this->wincher_helper->login_status(),
			'wincherWebsiteId'         => $this->options_helper->get( 'wincher_website_id', '' ),
			'wincherAutoAddKeyphrases' => $this->options_helper->get( 'wincher_automatically_add_keyphrases', false ),
		];
	}
}
