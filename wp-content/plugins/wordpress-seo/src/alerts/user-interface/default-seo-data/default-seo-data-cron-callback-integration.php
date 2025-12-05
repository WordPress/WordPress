<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Alerts\User_Interface\Default_SEO_Data;

use Yoast\WP\SEO\Alerts\User_Interface\Default_Seo_Data\Default_SEO_Data_Cron_Scheduler;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Cron Callback integration. This handles the actual process of detecting default SEO data in recent posts and updating the relevant options.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Default_SEO_Data_Cron_Callback_Integration implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The scheduler.
	 *
	 * @var Default_SEO_Data_Cron_Scheduler
	 */
	private $scheduler;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $indexable_repository;

	/**
	 * Constructor.
	 *
	 * @param Options_Helper                  $options_helper       The options helper.
	 * @param Default_SEO_Data_Cron_Scheduler $scheduler            The scheduler.
	 * @param Indexable_Repository            $indexable_repository The indexable repository.
	 */
	public function __construct(
		Options_Helper $options_helper,
		Default_SEO_Data_Cron_Scheduler $scheduler,
		Indexable_Repository $indexable_repository
	) {
		$this->options_helper       = $options_helper;
		$this->scheduler            = $scheduler;
		$this->indexable_repository = $indexable_repository;
	}

	/**
	 * Registers the hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action(
			Default_SEO_Data_Cron_Scheduler::CRON_HOOK,
			[
				$this,
				'detect_default_seo_data_in_recent',
			]
		);
	}

	/**
	 * Detects default SEO data in recent posts and updates the relevant options.
	 *
	 * @return void
	 */
	public function detect_default_seo_data_in_recent(): void {
		if ( ! \wp_doing_cron() ) {
			return;
		}

		$recent_posts = $this->indexable_repository->get_recently_modified_posts( 'post', 5, false );

		$recent_default_seo_title     = [];
		$recent_default_seo_meta_desc = [];
		foreach ( $recent_posts as $post ) {
			if ( $post->title === null ) {
				$recent_default_seo_title[] = $post->object_id;
			}

			if ( $post->description === null ) {
				$recent_default_seo_meta_desc[] = $post->object_id;
			}
		}

		$this->options_helper->set( 'default_seo_title', $recent_default_seo_title );
		$this->options_helper->set( 'default_seo_meta_desc', $recent_default_seo_meta_desc );
	}
}
