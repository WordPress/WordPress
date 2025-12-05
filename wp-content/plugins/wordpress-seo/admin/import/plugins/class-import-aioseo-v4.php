<?php
/**
 * File with the class to handle data from All in One SEO Pack, versions 4 and up.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

use Yoast\WP\SEO\Actions\Importing\Aioseo\Aioseo_Cleanup_Action;
use Yoast\WP\SEO\Actions\Importing\Aioseo\Aioseo_Posts_Importing_Action;

/**
 * Class with functionality to import & clean All in One SEO Pack post metadata, versions 4 and up.
 */
class WPSEO_Import_AIOSEO_V4 extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'All In One SEO Pack';

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = '_aioseo_%';

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys = [
		[
			'old_key' => '_aioseo_title',
			'new_key' => 'title',
		],
		[
			'old_key' => '_aioseo_description',
			'new_key' => 'metadesc',
		],
		[
			'old_key' => '_aioseo_og_title',
			'new_key' => 'opengraph-title',
		],
		[
			'old_key' => '_aioseo_og_description',
			'new_key' => 'opengraph-description',
		],
		[
			'old_key' => '_aioseo_twitter_title',
			'new_key' => 'twitter-title',
		],
		[
			'old_key' => '_aioseo_twitter_description',
			'new_key' => 'twitter-description',
		],
	];

	/**
	 * Mapping between the AiOSEO replace vars and the Yoast replace vars.
	 *
	 * @see https://yoast.com/help/list-available-snippet-variables-yoast-seo/
	 *
	 * @var array
	 */
	protected $replace_vars = [
		// They key is the AiOSEO replace var, the value is the Yoast replace var (see class-wpseo-replace-vars).
		'#author_first_name' => '%%author_first_name%%',
		'#author_last_name'  => '%%author_last_name%%',
		'#author_name'       => '%%name%%',
		'#categories'        => '%%category%%',
		'#current_date'      => '%%currentdate%%',
		'#current_day'       => '%%currentday%%',
		'#current_month'     => '%%currentmonth%%',
		'#current_year'      => '%%currentyear%%',
		'#permalink'         => '%%permalink%%',
		'#post_content'      => '%%post_content%%',
		'#post_date'         => '%%date%%',
		'#post_day'          => '%%post_day%%',
		'#post_month'        => '%%post_month%%',
		'#post_title'        => '%%title%%',
		'#post_year'         => '%%post_year%%',
		'#post_excerpt_only' => '%%excerpt_only%%',
		'#post_excerpt'      => '%%excerpt%%',
		'#separator_sa'      => '%%sep%%',
		'#site_title'        => '%%sitename%%',
		'#tagline'           => '%%sitedesc%%',
		'#taxonomy_title'    => '%%category_title%%',
	];

	/**
	 * Replaces the AiOSEO variables in our temporary table with Yoast variables (replace vars).
	 *
	 * @param array $replace_values Key value pair of values to replace with other values. This is only used in the base class but not here.
	 *                              That is because this class doesn't have any `convert` keys in `$clone_keys`.
	 *                              For that reason, we're overwriting the base class' `meta_key_clone_replace()` function without executing that base functionality.
	 *
	 * @return void
	 */
	protected function meta_key_clone_replace( $replace_values ) {
		global $wpdb;

		// At this point we're already looping through all the $clone_keys (this happens in meta_keys_clone() in the abstract class).
		// Now, we'll also loop through the replace_vars array, which holds the mappings between the AiOSEO variables and the Yoast variables.
		// We'll replace all the AiOSEO variables in the temporary table with their Yoast equivalents.
		foreach ( $this->replace_vars as $aioseo_variable => $yoast_variable ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: We need this query and this is done at many other places as well, for example class-import-rankmath.
			$wpdb->query(
				$wpdb->prepare(
					'UPDATE tmp_meta_table SET meta_value = REPLACE( meta_value, %s, %s )',
					$aioseo_variable,
					$yoast_variable
				)
			);
		}

		// The AiOSEO custom fields take the form of `#custom_field-myfield`.
		// These should be mapped to %%cf_myfield%%.
		$meta_values_with_custom_fields = $this->get_meta_values_with_custom_field_or_taxonomy( $wpdb, 'custom_field' );
		$unique_custom_fields           = $this->get_unique_custom_fields_or_taxonomies( $meta_values_with_custom_fields, 'custom_field' );
		$this->replace_custom_field_or_taxonomy_replace_vars( $unique_custom_fields, $wpdb, 'custom_field', 'cf' );

		// Map `#tax_name-{tax-slug}` to `%%ct_{tax-slug}%%``.
		$meta_values_with_custom_taxonomies = $this->get_meta_values_with_custom_field_or_taxonomy( $wpdb, 'tax_name' );
		$unique_custom_taxonomies           = $this->get_unique_custom_fields_or_taxonomies( $meta_values_with_custom_taxonomies, 'tax_name' );
		$this->replace_custom_field_or_taxonomy_replace_vars( $unique_custom_taxonomies, $wpdb, 'tax_name', 'ct' );
	}

	/**
	 * Filters out all unique custom fields/taxonomies/etc. used in an AiOSEO replace var.
	 *
	 * @param string[] $meta_values   An array of all the meta values that
	 *                                contain one or more AIOSEO custom field replace vars
	 *                                (in the form `#custom_field-xyz`).
	 * @param string   $aioseo_prefix The AiOSEO prefix to use
	 *                                (e.g. `custom-field` for custom fields or `tax_name` for custom taxonomies).
	 *
	 * @return string[] An array of all the unique custom fields/taxonomies/etc. used in the replace vars.
	 *                  E.g. `xyz` in the above example.
	 */
	protected function get_unique_custom_fields_or_taxonomies( $meta_values, $aioseo_prefix ) {
		$unique_custom_fields_or_taxonomies = [];

		foreach ( $meta_values as $meta_value ) {
			// Find all custom field replace vars, store them in `$matches`.
			preg_match_all(
				"/#$aioseo_prefix-([\w-]+)/",
				$meta_value,
				$matches
			);

			/*
			 * `$matches[1]` contain the captured matches of the
			 * first capturing group (the `([\w-]+)` in the regex above).
			 */
			$custom_fields_or_taxonomies = $matches[1];

			foreach ( $custom_fields_or_taxonomies as $custom_field_or_taxonomy ) {
				$unique_custom_fields_or_taxonomies[ trim( $custom_field_or_taxonomy ) ] = 1;
			}
		}

		return array_keys( $unique_custom_fields_or_taxonomies );
	}

	/**
	 * Replaces every AIOSEO custom field/taxonomy/etc. replace var with the Yoast version.
	 *
	 * E.g. `#custom_field-xyz` becomes `%%cf_xyz%%`.
	 *
	 * @param string[] $unique_custom_fields_or_taxonomies An array of unique custom fields to replace the replace vars of.
	 * @param wpdb     $wpdb                               The WordPress database object.
	 * @param string   $aioseo_prefix                      The AiOSEO prefix to use
	 *                                                     (e.g. `custom-field` for custom fields or `tax_name` for custom taxonomies).
	 * @param string   $yoast_prefix                       The Yoast prefix to use (e.g. `cf` for custom fields).
	 *
	 * @return void
	 */
	protected function replace_custom_field_or_taxonomy_replace_vars( $unique_custom_fields_or_taxonomies, $wpdb, $aioseo_prefix, $yoast_prefix ) {
		foreach ( $unique_custom_fields_or_taxonomies as $unique_custom_field_or_taxonomy ) {
			$aioseo_variable = "#{$aioseo_prefix}-{$unique_custom_field_or_taxonomy}";
			$yoast_variable  = "%%{$yoast_prefix}_{$unique_custom_field_or_taxonomy}%%";

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->query(
				$wpdb->prepare(
					'UPDATE tmp_meta_table SET meta_value = REPLACE( meta_value, %s, %s )',
					$aioseo_variable,
					$yoast_variable
				)
			);
		}
	}

	// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

	/**
	 * Retrieve all the meta values from the temporary meta table that contain
	 * at least one AiOSEO custom field replace var.
	 *
	 * @param wpdb   $wpdb          The WordPress database object.
	 * @param string $aioseo_prefix The AiOSEO prefix to use
	 *                              (e.g. `custom-field` for custom fields or `tax_name` for custom taxonomies).
	 *
	 * @return string[] All meta values that contain at least one AioSEO custom field replace var.
	 */
	protected function get_meta_values_with_custom_field_or_taxonomy( $wpdb, $aioseo_prefix ) {
		return $wpdb->get_col(
			$wpdb->prepare(
				'SELECT meta_value FROM tmp_meta_table WHERE meta_value LIKE %s',
				"%#$aioseo_prefix-%"
			)
		);
	}

	// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

	/**
	 * Detects whether there is AIOSEO data to import by looking whether the AIOSEO data have been cleaned up.
	 *
	 * @return bool Boolean indicating whether there is something to import.
	 */
	protected function detect() {
		$aioseo_cleanup_action = YoastSEO()->classes->get( Aioseo_Cleanup_Action::class );
		return ( $aioseo_cleanup_action->get_total_unindexed() > 0 );
	}

	/**
	 * Import AIOSEO post data from their custom indexable table. Not currently used.
	 *
	 * @return void
	 */
	protected function import() {
		// This is overriden from the import.js and never run.
		$aioseo_posts_import_action = YoastSEO()->classes->get( Aioseo_Posts_Importing_Action::class );
		$aioseo_posts_import_action->index();
	}
}
