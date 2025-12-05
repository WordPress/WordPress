<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Alerts\User_Interface\Default_SEO_Data;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
/**
 * This handles the process of checking for non-default SEO in the latest content and updating the relevant options right away.
 */
class Default_SEO_Data_Watcher implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $indexable_repository;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Constructor.
	 *
	 * @param Indexable_Repository $indexable_repository The indexable repository.
	 * @param Options_Helper       $options_helper       The options helper.
	 */
	public function __construct(
		Indexable_Repository $indexable_repository,
		Options_Helper $options_helper
	) {
		$this->indexable_repository = $indexable_repository;
		$this->options_helper       = $options_helper;
	}

	/**
	 * Registers the hooks with WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wpseo_saved_indexable', [ $this, 'check_for_default_seo_data' ], 10, 1 );
	}

	/**
	 * Checks for default SEO data in the saved indexable and the most recently modified posts.
	 *
	 * @param Indexable $saved_indexable The saved indexable.
	 *
	 * @return void
	 */
	public function check_for_default_seo_data( $saved_indexable ): void {
		// We have activated this feature only for posts for now.
		if ( $saved_indexable->object_type !== 'post' || $saved_indexable->object_sub_type !== 'post' ) {
			return;
		}

		// If the title or description is null, it means it's not default SEO data so let's reset the options.
		if ( $saved_indexable->title !== null ) {
			$this->options_helper->set( 'default_seo_title', [] );
		}

		if ( $saved_indexable->description !== null ) {
			$this->options_helper->set( 'default_seo_meta_desc', [] );
		}
	}
}
