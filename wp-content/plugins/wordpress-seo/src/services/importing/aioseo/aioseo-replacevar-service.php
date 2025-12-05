<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Given it's a very specific case.
namespace Yoast\WP\SEO\Services\Importing\Aioseo;

/**
 * Replaces AISOEO replacevars with Yoast ones.
 */
class Aioseo_Replacevar_Service {

	/**
	 * Mapping between the AiOSEO replace vars and the Yoast replace vars.
	 *
	 * @see https://yoast.com/help/list-available-snippet-variables-yoast-seo/
	 *
	 * @var array
	 */
	protected $replace_vars_map = [
		// The key is the AiOSEO replace var, the value is the Yoast replace var (see class-wpseo-replace-vars).
		'#archive_title'             => '%%archive_title%%',
		'#archive_date'              => '%%date%%',
		'#attachment_caption'        => '%%caption%%',
		'#author_bio'                => '%%user_description%%',
		'#author_first_name'         => '%%author_first_name%%',
		'#author_last_name'          => '%%author_last_name%%',
		'#author_name'               => '%%name%%',
		'#blog_title'                => '%%sitename%%', // Same with #site_title.
		'#categories'                => '%%category%%',
		'#current_date'              => '%%currentdate%%',
		'#current_day'               => '%%currentday%%',
		'#current_month'             => '%%currentmonth%%',
		'#current_year'              => '%%currentyear%%',
		'#parent_title'              => '%%parent_title%%',
		'#page_number'               => '%%pagenumber%%',
		'#permalink'                 => '%%permalink%%',
		'#post_content'              => '%%post_content%%',
		'#post_date'                 => '%%date%%',
		'#post_day'                  => '%%post_day%%',
		'#post_month'                => '%%post_month%%',
		'#post_title'                => '%%title%%',
		'#post_year'                 => '%%post_year%%',
		'#post_excerpt_only'         => '%%excerpt_only%%',
		'#post_excerpt'              => '%%excerpt%%',
		'#search_term'               => '%%searchphrase%%',
		'#separator_sa'              => '%%sep%%',
		'#site_title'                => '%%sitename%%',
		'#tagline'                   => '%%sitedesc%%',
		'#taxonomy_title'            => '%%category_title%%',
		'#taxonomy_description'      => '%%term_description%%',
	];

	/**
	 * Edits the replace_vars map of the class.
	 *
	 * @param string $aioseo_var The AIOSEO replacevar.
	 * @param string $yoast_var  The Yoast replacevar.
	 *
	 * @return void
	 */
	public function compose_map( $aioseo_var, $yoast_var ) {
		$map = $this->replace_vars_map;

		$map[ $aioseo_var ] = $yoast_var;

		$this->replace_vars_map = $map;
	}

	/**
	 * Transforms AIOSEO replacevars into Yoast replacevars.
	 *
	 * @param string $aioseo_replacevar The AIOSEO replacevar.
	 *
	 * @return string The Yoast replacevar.
	 */
	public function transform( $aioseo_replacevar ) {
		$yoast_replacevar = \str_replace( \array_keys( $this->replace_vars_map ), \array_values( $this->replace_vars_map ), $aioseo_replacevar );

		// Transform the '#custom_field-<custom_field>' tags into '%%cf_<custom_field>%%' ones.
		$yoast_replacevar = \preg_replace_callback(
			'/#custom_field-([a-zA-Z0-9_-]+)/',
			static function ( $cf_matches ) {
				return '%%cf_' . $cf_matches[1] . '%%';
			},
			$yoast_replacevar
		);

		// Transform the '#tax_name-<custom-tax-name>' tags into '%%ct_<custom-tax-name>%%' ones.
		$yoast_replacevar = \preg_replace_callback(
			'/#tax_name-([a-zA-Z0-9_-]+)/',
			static function ( $ct_matches ) {
				return '%%ct_' . $ct_matches[1] . '%%';
			},
			$yoast_replacevar
		);

		return $yoast_replacevar;
	}
}
