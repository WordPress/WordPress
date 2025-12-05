<?php


// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Application\Configuration;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Llms_Txt\Application\Health_Check\File_Runner;
/**
 * Responsible for the llms.txt configuration.
 */
class Llms_Txt_Configuration {

	/**
	 * Runs the health check.
	 *
	 * @var File_Runner
	 */
	private $runner;

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	private $post_type_helper;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The constructor.
	 *
	 * @param File_Runner      $runner           The File_Generation health check runner.
	 * @param Post_Type_Helper $post_type_helper The post type helper.
	 * @param Options_Helper   $options_helper   The options helper.
	 */
	public function __construct(
		File_Runner $runner,
		Post_Type_Helper $post_type_helper,
		Options_Helper $options_helper
	) {
		$this->runner           = $runner;
		$this->post_type_helper = $post_type_helper;
		$this->options_helper   = $options_helper;
	}

	/**
	 * Returns a configuration
	 *
	 * @return array<string, array<string>|array<string, string|array<string, array<string, int>>>>
	 */
	public function get_configuration(): array {
		$this->runner->run();

		$configuration = [
			'generationFailure'       => ! $this->runner->is_successful(),
			'generationFailureReason' => $this->runner->get_generation_failure_reason(),
			'llmsTxtUrl'              => \home_url( 'llms.txt' ),
			'disabledPageIndexables'  => ( $this->post_type_helper->is_of_indexable_post_type( 'page' ) === false ),
			'otherIncludedPagesLimit' => $this->options_helper->get_other_included_pages_limit(),
		];

		return $configuration;
	}
}
