<?php

namespace Yoast\WP\SEO\Actions\Wincher;

use Exception;
use Yoast\WP\SEO\Config\Wincher_Client;
use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Class Wincher_Account_Action
 */
class Wincher_Account_Action {

	public const ACCOUNT_URL          = 'https://api.wincher.com/beta/account';
	public const UPGRADE_CAMPAIGN_URL = 'https://api.wincher.com/v1/yoast/upgrade-campaign';

	/**
	 * The Wincher_Client instance.
	 *
	 * @var Wincher_Client
	 */
	protected $client;

	/**
	 * The Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * Wincher_Account_Action constructor.
	 *
	 * @param Wincher_Client $client         The API client.
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Wincher_Client $client, Options_Helper $options_helper ) {
		$this->client         = $client;
		$this->options_helper = $options_helper;
	}

	/**
	 * Checks the account limit for tracking keyphrases.
	 *
	 * @return object The response object.
	 */
	public function check_limit() {
		// Code has already been validated at this point. No need to do that again.
		try {
			$results = $this->client->get( self::ACCOUNT_URL );

			$usage   = ( $results['limits']['keywords']['usage'] ?? null );
			$limit   = ( $results['limits']['keywords']['limit'] ?? null );
			$history = ( $results['limits']['history_days'] ?? null );

			return (object) [
				'canTrack'    => ( $limit === null || $usage < $limit ),
				'limit'       => $limit,
				'usage'       => $usage,
				'historyDays' => $history,
				'status'      => 200,
			];
		} catch ( Exception $e ) {
			return (object) [
				'status' => $e->getCode(),
				'error'  => $e->getMessage(),
			];
		}
	}

	/**
	 * Gets the upgrade campaign.
	 *
	 * @return object The response object.
	 */
	public function get_upgrade_campaign() {
		try {
			$result   = $this->client->get( self::UPGRADE_CAMPAIGN_URL );
			$type     = ( $result['type'] ?? null );
			$months   = ( $result['months'] ?? null );
			$discount = ( $result['value'] ?? null );

			// We display upgrade discount only if it's a rate discount and positive months/discount.
			if ( $type === 'RATE' && $months && $discount ) {

				return (object) [
					'discount'  => $discount,
					'months'    => $months,
					'status'    => 200,
				];
			}

			return (object) [
				'discount'  => null,
				'months'    => null,
				'status'    => 200,
			];
		} catch ( Exception $e ) {
			return (object) [
				'status' => $e->getCode(),
				'error'  => $e->getMessage(),
			];
		}
	}
}
