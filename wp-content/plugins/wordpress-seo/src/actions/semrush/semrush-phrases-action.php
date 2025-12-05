<?php

namespace Yoast\WP\SEO\Actions\SEMrush;

use Exception;
use Yoast\WP\SEO\Config\SEMrush_Client;

/**
 * Class SEMrush_Phrases_Action
 */
class SEMrush_Phrases_Action {

	/**
	 * The transient cache key.
	 */
	public const TRANSIENT_CACHE_KEY = 'wpseo_semrush_related_keyphrases_%s_%s';

	/**
	 * The SEMrush keyphrase URL.
	 *
	 * @var string
	 */
	public const KEYPHRASES_URL = 'https://oauth.semrush.com/api/v1/keywords/phrase_fullsearch';

	/**
	 * The SEMrush_Client instance.
	 *
	 * @var SEMrush_Client
	 */
	protected $client;

	/**
	 * SEMrush_Phrases_Action constructor.
	 *
	 * @param SEMrush_Client $client The API client.
	 */
	public function __construct( SEMrush_Client $client ) {
		$this->client = $client;
	}

	/**
	 * Gets the related keyphrases and data based on the passed keyphrase and database country code.
	 *
	 * @param string $keyphrase The keyphrase to search for.
	 * @param string $database  The database's country code.
	 *
	 * @return object The response object.
	 */
	public function get_related_keyphrases( $keyphrase, $database ) {
		try {
			$transient_key = \sprintf( static::TRANSIENT_CACHE_KEY, $keyphrase, $database );
			$transient     = \get_transient( $transient_key );

			if ( $transient !== false && isset( $transient['data']['columnNames'] ) && \count( $transient['data']['columnNames'] ) === 5 ) {
				return $this->to_result_object( $transient );
			}

			$options = [
				'params' => [
					'phrase'         => $keyphrase,
					'database'       => $database,
					'export_columns' => 'Ph,Nq,Td,In,Kd',
					'display_limit'  => 10,
					'display_offset' => 0,
					'display_sort'   => 'nq_desc',
					'display_filter' => '%2B|Nq|Lt|1000',
				],
			];

			$results = $this->client->get( self::KEYPHRASES_URL, $options );

			\set_transient( $transient_key, $results, \DAY_IN_SECONDS );

			return $this->to_result_object( $results );
		} catch ( Exception $e ) {
			return (object) [
				'error'  => $e->getMessage(),
				'status' => $e->getCode(),
			];
		}
	}

	/**
	 * Converts the passed dataset to an object.
	 *
	 * @param array $result The result dataset to convert to an object.
	 *
	 * @return object The result object.
	 */
	protected function to_result_object( $result ) {
		return (object) [
			'results' => $result['data'],
			'status'  => $result['status'],
		];
	}
}
