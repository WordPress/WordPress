<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Given it's a very specific case.
namespace Yoast\WP\SEO\Actions\Importing\Aioseo;

use Yoast\WP\SEO\Helpers\Image_Helper;
use Yoast\WP\SEO\Helpers\Import_Cursor_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Sanitization_Helper;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Replacevar_Service;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Robots_Provider_Service;
use Yoast\WP\SEO\Services\Importing\Aioseo\Aioseo_Robots_Transformer_Service;

/**
 * Importing action for AIOSEO general settings.
 */
class Aioseo_General_Settings_Importing_Action extends Abstract_Aioseo_Settings_Importing_Action {

	/**
	 * The plugin of the action.
	 */
	public const PLUGIN = 'aioseo';

	/**
	 * The type of the action.
	 */
	public const TYPE = 'general_settings';

	/**
	 * The option_name of the AIOSEO option that contains the settings.
	 */
	public const SOURCE_OPTION_NAME = 'aioseo_options';

	/**
	 * The map of aioseo_options to yoast settings.
	 *
	 * @var array
	 */
	protected $aioseo_options_to_yoast_map = [];

	/**
	 * The tab of the aioseo settings we're working with.
	 *
	 * @var string
	 */
	protected $settings_tab = 'global';

	/**
	 * The image helper.
	 *
	 * @var Image_Helper
	 */
	protected $image;

	/**
	 * Aioseo_General_Settings_Importing_Action constructor.
	 *
	 * @param Import_Cursor_Helper              $import_cursor      The import cursor helper.
	 * @param Options_Helper                    $options            The options helper.
	 * @param Sanitization_Helper               $sanitization       The sanitization helper.
	 * @param Image_Helper                      $image              The image helper.
	 * @param Aioseo_Replacevar_Service         $replacevar_handler The replacevar handler.
	 * @param Aioseo_Robots_Provider_Service    $robots_provider    The robots provider service.
	 * @param Aioseo_Robots_Transformer_Service $robots_transformer The robots transfomer service.
	 */
	public function __construct(
		Import_Cursor_Helper $import_cursor,
		Options_Helper $options,
		Sanitization_Helper $sanitization,
		Image_Helper $image,
		Aioseo_Replacevar_Service $replacevar_handler,
		Aioseo_Robots_Provider_Service $robots_provider,
		Aioseo_Robots_Transformer_Service $robots_transformer
	) {
		parent::__construct( $import_cursor, $options, $sanitization, $replacevar_handler, $robots_provider, $robots_transformer );

		$this->image = $image;
	}

	/**
	 * Builds the mapping that ties AOISEO option keys with Yoast ones and their data transformation method.
	 *
	 * @return void
	 */
	protected function build_mapping() {
		$this->aioseo_options_to_yoast_map = [
			'/separator'               => [
				'yoast_name'       => 'separator',
				'transform_method' => 'transform_separator',
			],
			'/siteTitle'               => [
				'yoast_name'       => 'title-home-wpseo',
				'transform_method' => 'simple_import',
			],
			'/metaDescription'         => [
				'yoast_name'       => 'metadesc-home-wpseo',
				'transform_method' => 'simple_import',
			],
			'/schema/siteRepresents'   => [
				'yoast_name'       => 'company_or_person',
				'transform_method' => 'transform_site_represents',
			],
			'/schema/person'           => [
				'yoast_name'       => 'company_or_person_user_id',
				'transform_method' => 'simple_import',
			],
			'/schema/organizationName' => [
				'yoast_name'       => 'company_name',
				'transform_method' => 'simple_import',
			],
			'/schema/organizationLogo' => [
				'yoast_name'       => 'company_logo',
				'transform_method' => 'import_company_logo',
			],
			'/schema/personLogo'       => [
				'yoast_name'       => 'person_logo',
				'transform_method' => 'import_person_logo',
			],
		];
	}

	/**
	 * Imports the organization logo while also accounting for the id of the log to be saved in the separate Yoast option.
	 *
	 * @param string $logo_url The company logo url coming from AIOSEO settings.
	 *
	 * @return string The transformed company logo url.
	 */
	public function import_company_logo( $logo_url ) {
		$logo_id = $this->image->get_attachment_by_url( $logo_url );
		$this->options->set( 'company_logo_id', $logo_id );

		$this->options->set( 'company_logo_meta', false );
		$logo_meta = $this->image->get_attachment_meta_from_settings( 'company_logo' );
		$this->options->set( 'company_logo_meta', $logo_meta );

		return $this->url_import( $logo_url );
	}

	/**
	 * Imports the person logo while also accounting for the id of the log to be saved in the separate Yoast option.
	 *
	 * @param string $logo_url The person logo url coming from AIOSEO settings.
	 *
	 * @return string The transformed person logo url.
	 */
	public function import_person_logo( $logo_url ) {
		$logo_id = $this->image->get_attachment_by_url( $logo_url );
		$this->options->set( 'person_logo_id', $logo_id );

		$this->options->set( 'person_logo_meta', false );
		$logo_meta = $this->image->get_attachment_meta_from_settings( 'person_logo' );
		$this->options->set( 'person_logo_meta', $logo_meta );

		return $this->url_import( $logo_url );
	}

	/**
	 * Transforms the site represents setting.
	 *
	 * @param string $site_represents The site represents setting.
	 *
	 * @return string The transformed site represents setting.
	 */
	public function transform_site_represents( $site_represents ) {
		switch ( $site_represents ) {
			case 'person':
				return 'person';

			case 'organization':
			default:
				return 'company';
		}
	}

	/**
	 * Transforms the separator setting.
	 *
	 * @param string $separator The separator setting.
	 *
	 * @return string The transformed separator.
	 */
	public function transform_separator( $separator ) {
		switch ( $separator ) {
			case '&#45;':
				return 'sc-dash';

			case '&ndash;':
				return 'sc-ndash';

			case '&mdash;':
				return 'sc-mdash';

			case '&raquo;':
				return 'sc-raquo';

			case '&laquo;':
				return 'sc-laquo';

			case '&gt;':
				return 'sc-gt';

			case '&bull;':
				return 'sc-bull';

			case '&#124;':
				return 'sc-pipe';

			default:
				return 'sc-dash';
		}
	}
}
