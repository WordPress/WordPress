<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use WP_Term;
use WPSEO_Meta;
use WPSEO_Primary_Term;
use Yoast\WP\SEO\Builders\Primary_Term_Builder;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Helpers\Primary_Term_Helper;
use Yoast\WP\SEO\Helpers\Site_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Repositories\Primary_Term_Repository;

/**
 * Primary Term watcher.
 *
 * Watches Posts to save the primary term when set.
 */
class Primary_Term_Watcher implements Integration_Interface {

	/**
	 * The primary term repository.
	 *
	 * @var Primary_Term_Repository
	 */
	protected $repository;

	/**
	 * Represents the site helper.
	 *
	 * @var Site_Helper
	 */
	protected $site;

	/**
	 * The primary term helper.
	 *
	 * @var Primary_Term_Helper
	 */
	protected $primary_term;

	/**
	 * The primary term builder.
	 *
	 * @var Primary_Term_Builder
	 */
	protected $primary_term_builder;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Migrations_Conditional::class ];
	}

	/**
	 * Primary_Term_Watcher constructor.
	 *
	 * @codeCoverageIgnore It sets dependencies.
	 *
	 * @param Primary_Term_Repository $repository           The primary term repository.
	 * @param Site_Helper             $site                 The site helper.
	 * @param Primary_Term_Helper     $primary_term         The primary term helper.
	 * @param Primary_Term_Builder    $primary_term_builder The primary term builder.
	 */
	public function __construct(
		Primary_Term_Repository $repository,
		Site_Helper $site,
		Primary_Term_Helper $primary_term,
		Primary_Term_Builder $primary_term_builder
	) {
		$this->repository           = $repository;
		$this->site                 = $site;
		$this->primary_term         = $primary_term;
		$this->primary_term_builder = $primary_term_builder;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'save_post', [ $this, 'save_primary_terms' ], \PHP_INT_MAX );
		\add_action( 'delete_post', [ $this, 'delete_primary_terms' ] );
	}

	/**
	 * Saves all selected primary terms.
	 *
	 * @param int $post_id Post ID to save primary terms for.
	 *
	 * @return void
	 */
	public function save_primary_terms( $post_id ) {
		// Bail if this is a multisite installation and the site has been switched.
		if ( $this->site->is_multisite_and_switched() ) {
			return;
		}

		$taxonomies = $this->primary_term->get_primary_term_taxonomies( $post_id );

		foreach ( $taxonomies as $taxonomy ) {
			$this->save_primary_term( $post_id, $taxonomy );
		}

		$this->primary_term_builder->build( $post_id );
	}

	/**
	 * Saves the primary term for a specific taxonomy.
	 *
	 * @param int     $post_id  Post ID to save primary term for.
	 * @param WP_Term $taxonomy Taxonomy to save primary term for.
	 *
	 * @return void
	 */
	protected function save_primary_term( $post_id, $taxonomy ) {
		if ( isset( $_POST[ WPSEO_Meta::$form_prefix . 'primary_' . $taxonomy->name . '_term' ] ) && \is_string( $_POST[ WPSEO_Meta::$form_prefix . 'primary_' . $taxonomy->name . '_term' ] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are casting to an integer.
			$primary_term_id = (int) \wp_unslash( $_POST[ WPSEO_Meta::$form_prefix . 'primary_' . $taxonomy->name . '_term' ] );

			if ( $primary_term_id <= 0 ) {
				$primary_term = '';
			}
			else {
				$primary_term = (string) $primary_term_id;
			}

			// We accept an empty string here because we need to save that if no terms are selected.
			if ( \check_admin_referer( 'save-primary-term', WPSEO_Meta::$form_prefix . 'primary_' . $taxonomy->name . '_nonce' ) !== null ) {
				$primary_term_object = new WPSEO_Primary_Term( $taxonomy->name, $post_id );
				$primary_term_object->set_primary_term( $primary_term );
			}
		}
	}

	/**
	 * Deletes primary terms for a post.
	 *
	 * @param int $post_id The post to delete the terms of.
	 *
	 * @return void
	 */
	public function delete_primary_terms( $post_id ) {
		foreach ( $this->primary_term->get_primary_term_taxonomies( $post_id ) as $taxonomy ) {
			$primary_term_indexable = $this->repository->find_by_post_id_and_taxonomy( $post_id, $taxonomy->name, false );

			if ( ! $primary_term_indexable ) {
				continue;
			}

			$primary_term_indexable->delete();
		}
	}
}
