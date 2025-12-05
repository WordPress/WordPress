<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Given it's a very specific case.
namespace Yoast\WP\SEO\Actions\Importing\Aioseo;

/**
 * Importing action for AIOSEO taxonomies settings data.
 */
class Aioseo_Taxonomy_Settings_Importing_Action extends Abstract_Aioseo_Settings_Importing_Action {

	/**
	 * The plugin of the action.
	 */
	public const PLUGIN = 'aioseo';

	/**
	 * The type of the action.
	 */
	public const TYPE = 'taxonomy_settings';

	/**
	 * The option_name of the AIOSEO option that contains the settings.
	 */
	public const SOURCE_OPTION_NAME = 'aioseo_options_dynamic';

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
	protected $settings_tab = 'taxonomies';

	/**
	 * Additional mapping between AiOSEO replace vars and Yoast replace vars.
	 *
	 * @see https://yoast.com/help/list-available-snippet-variables-yoast-seo/
	 *
	 * @var array
	 */
	protected $replace_vars_edited_map = [
		'#breadcrumb_404_error_format'         => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#breadcrumb_archive_post_type_format' => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#breadcrumb_archive_post_type_name'   => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#breadcrumb_author_display_name'      => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#breadcrumb_author_first_name'        => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#breadcrumb_blog_page_title'          => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#breadcrumb_label'                    => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#breadcrumb_link'                     => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#breadcrumb_search_result_format'     => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#breadcrumb_search_string'            => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#breadcrumb_separator'                => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#breadcrumb_taxonomy_title'           => '', // Empty string, as AIOSEO shows nothing for that tag.
		'#taxonomy_title'                      => '%%term_title%%',
	];

	/**
	 * Builds the mapping that ties AOISEO option keys with Yoast ones and their data transformation method.
	 *
	 * @return void
	 */
	protected function build_mapping() {
		$taxonomy_objects = \get_taxonomies( [ 'public' => true ], 'object' );

		foreach ( $taxonomy_objects as $tax ) {
			// Use all the public taxonomies.
			$this->aioseo_options_to_yoast_map[ '/' . $tax->name . '/title' ]                       = [
				'yoast_name'       => 'title-tax-' . $tax->name,
				'transform_method' => 'simple_import',
			];
			$this->aioseo_options_to_yoast_map[ '/' . $tax->name . '/metaDescription' ]             = [
				'yoast_name'       => 'metadesc-tax-' . $tax->name,
				'transform_method' => 'simple_import',
			];
			$this->aioseo_options_to_yoast_map[ '/' . $tax->name . '/advanced/robotsMeta/noindex' ] = [
				'yoast_name'       => 'noindex-tax-' . $tax->name,
				'transform_method' => 'import_noindex',
				'type'             => 'taxonomies',
				'subtype'          => $tax->name,
				'option_name'      => 'aioseo_options_dynamic',
			];
		}
	}

	/**
	 * Returns a setting map of the robot setting for post category taxonomies.
	 *
	 * @return array The setting map of the robot setting for post category taxonomies.
	 */
	public function pluck_robot_setting_from_mapping() {
		$this->build_mapping();

		foreach ( $this->aioseo_options_to_yoast_map as $setting ) {
			// Return the first archive setting map.
			if ( $setting['transform_method'] === 'import_noindex' && isset( $setting['subtype'] ) && $setting['subtype'] === 'category' ) {
				return $setting;
			}
		}

		return [];
	}
}
