<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals
 * @since   1.5.0
 */

use Yoast\WP\SEO\Config\Schema_Types;
use Yoast\WP\SEO\Helpers\Schema\Article_Helper;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * This class implements defaults and value validation for all WPSEO Post Meta values.
 *
 * Some guidelines:
 * - To update a meta value, you can just use update_post_meta() with the full (prefixed) meta key
 *   or the convenience method WPSEO_Meta::set_value() with the internal key.
 *   All updates will be automatically validated.
 *   Meta values will only be saved to the database if they are *not* the same as the default to
 *   keep database load low.
 * - To retrieve a WPSEO meta value, you **must** use WPSEO_Meta::get_value() which will always return a
 *   string value, either the saved value or the default.
 *   This method can also retrieve a complete set of WPSEO meta values for one specific post, see
 *   the method documentation for the parameters.
 *
 * {@internal Unfortunately there isn't a filter available to hook into before returning the results
 *            for get_post_meta(), get_post_custom() and the likes. That would have been the
 *            preferred solution.}}
 *
 * {@internal All WP native get_meta() results get cached internally, so no need to cache locally.}}
 * {@internal Use $key when the key is the WPSEO internal name (without prefix), $meta_key when it
 *            includes the prefix.}}
 */
class WPSEO_Meta {

	/**
	 * Prefix for all WPSEO meta values in the database.
	 *
	 * {@internal If at any point this would change, quite apart from an upgrade routine,
	 *            this also will need to be changed in the wpml-config.xml file.}}
	 *
	 * @var string
	 */
	public static $meta_prefix = '_yoast_wpseo_';

	/**
	 * Prefix for all WPSEO meta value form field names and ids.
	 *
	 * @var string
	 */
	public static $form_prefix = 'yoast_wpseo_';

	/**
	 * Allowed length of the meta description.
	 *
	 * @var int
	 */
	public static $meta_length = 156;

	/**
	 * Reason the meta description is not the default length.
	 *
	 * @var string
	 */
	public static $meta_length_reason = '';

	/**
	 * Meta box field definitions for the meta box form.
	 *
	 * {@internal
	 * - Titles, help texts, description text and option labels are added via a translate_meta_boxes() method
	 *   in the relevant child classes (WPSEO_Metabox and WPSEO_Social_admin) as they are only needed there.
	 * - Beware: even though the meta keys are divided into subsets, they still have to be uniquely named!}}
	 *
	 * @var array
	 *            Array format:
	 *                (required)       'type'          => (string) field type. i.e. text / textarea / checkbox /
	 *                                                    radio / select / multiselect / upload etc.
	 *                (recommended)    'default_value' => (string|array) default value for the field.
	 *                                                    IMPORTANT:
	 *                                                    - if the field has options, the default has to be the
	 *                                                      key of one of the options.
	 *                                                    - if the field is a text field, the default **has** to be
	 *                                                      an empty string as otherwise the user can't save
	 *                                                      an empty value/delete the meta value.
	 *                                                    - if the field is a checkbox, the only valid values
	 *                                                      are 'on' or 'off'.
	 *                (semi-required)   'options'      => (array) options for used with (multi-)select and radio
	 *                                                    fields, required if that's the field type.
	 *                                                    key = (string) value which will be saved to db.
	 *                                                    value = (string) text label for the option.
	 *                (optional)        'autocomplete' => (bool) whether autocomplete is on for text fields,
	 *                                                    defaults to true.
	 *                (optional)        'class'        => (string) classname(s) to add to the actual <input> tag.
	 *                (optional)        'rows'         => (int) number of rows for a textarea, defaults to 3.
	 *                (optional)        'serialized'   => (bool) whether the value is expected to be serialized,
	 *                                                     i.e. an array or object, defaults to false.
	 *                                                     Currently only used by add-on plugins.
	 */
	public static $meta_fields = [
		'general'  => [
			'focuskw' => [
				'type'  => 'hidden',
				'title' => '',
			],
			'title' => [
				'type'          => 'hidden',
				'default_value' => '',
			],
			'metadesc' => [
				'type'          => 'hidden',
				'default_value' => '',
				'class'         => 'metadesc',
				'rows'          => 2,
			],
			'linkdex' => [
				'type'          => 'hidden',
				'default_value' => '0',
			],
			'content_score' => [
				'type'          => 'hidden',
				'default_value' => '0',
			],
			'inclusive_language_score' => [
				'type'          => 'hidden',
				'default_value' => '0',
			],
			'is_cornerstone' => [
				'type'          => 'hidden',
				'default_value' => 'false',
			],
		],
		'advanced' => [
			'meta-robots-noindex'  => [
				'type'          => 'hidden',
				'default_value' => '0', // = post-type default.
				'options'       => [
					'0' => '', // Post type default.
					'2' => '', // Index.
					'1' => '', // No-index.
				],
			],
			'meta-robots-nofollow' => [
				'type'          => 'hidden',
				'default_value' => '0', // = follow.
				'options'       => [
					'0' => '', // Follow.
					'1' => '', // No-follow.
				],
			],
			'meta-robots-adv'      => [
				'type'          => 'hidden',
				'default_value' => '',
				'options'       => [
					'noimageindex' => '',
					'noarchive'    => '',
					'nosnippet'    => '',
				],
			],
			'bctitle'              => [
				'type'          => 'hidden',
				'default_value' => '',
			],
			'canonical'            => [
				'type'          => 'hidden',
				'default_value' => '',
			],
			'redirect'             => [
				'type'          => 'url',
				'default_value' => '',
			],
		],
		'social'   => [],
		'schema'   => [
			'schema_page_type'    => [
				'type'    => 'hidden',
				'options' => Schema_Types::PAGE_TYPES,
			],
			'schema_article_type' => [
				'type'          => 'hidden',
				'hide_on_pages' => true,
				'options'       => Schema_Types::ARTICLE_TYPES,
			],
		],
		/* Fields we should validate & save, but not show on any form. */
		'non_form' => [
			'linkdex' => [
				'type'          => null,
				'default_value' => '0',
			],
		],
	];

	/**
	 * Helper property - reverse index of the definition array.
	 *
	 * Format: [full meta key including prefix]    => array
	 *         ['subset']    => (string) primary index
	 *         ['key']       => (string) internal key
	 *
	 * @var array
	 */
	public static $fields_index = [];

	/**
	 * Helper property - array containing only the defaults in the format:
	 * [full meta key including prefix]    => (string) default value
	 *
	 * @var array
	 */
	public static $defaults = [];

	/**
	 * Helper property to define the social network meta field definitions - networks.
	 *
	 * @var array
	 */
	private static $social_networks = [
		'opengraph'  => 'opengraph',
		'twitter'    => 'twitter',
	];

	/**
	 * Helper property to define the social network meta field definitions - fields and their type.
	 *
	 * @var array
	 */
	private static $social_fields = [
		'title'       => 'hidden',
		'description' => 'hidden',
		'image'       => 'hidden',
		'image-id'    => 'hidden',
	];

	/**
	 * Register our actions and filters.
	 *
	 * @return void
	 */
	public static function init() {
		foreach ( self::$social_networks as $option => $network ) {
			if ( WPSEO_Options::get( $option, false, [ 'wpseo_social' ] ) === true ) {
				foreach ( self::$social_fields as $box => $type ) {
					self::$meta_fields['social'][ $network . '-' . $box ] = [
						'type'          => $type,
						'default_value' => '',
					];
				}
			}
		}
		unset( $option, $network, $box, $type );

		/**
		 * Allow add-on plugins to register their meta fields for management by this class.
		 * Calls to add_filter() must be made before plugins_loaded prio 14.
		 */
		$extra_fields = apply_filters( 'add_extra_wpseo_meta_fields', [] );
		if ( is_array( $extra_fields ) ) {
			self::$meta_fields = self::array_merge_recursive_distinct( $extra_fields, self::$meta_fields );
		}
		unset( $extra_fields );

		foreach ( self::$meta_fields as $subset => $field_group ) {
			foreach ( $field_group as $key => $field_def ) {

				register_meta(
					'post',
					self::$meta_prefix . $key,
					[ 'sanitize_callback' => [ self::class, 'sanitize_post_meta' ] ]
				);

				// Set the $fields_index property for efficiency.
				self::$fields_index[ self::$meta_prefix . $key ] = [
					'subset' => $subset,
					'key'    => $key,
				];

				// Set the $defaults property for efficiency.
				if ( isset( $field_def['default_value'] ) ) {
					self::$defaults[ self::$meta_prefix . $key ] = $field_def['default_value'];
				}
				else {
					// Meta will always be a string, so let's make the meta meta default also a string.
					self::$defaults[ self::$meta_prefix . $key ] = '';
				}
			}
		}
		unset( $subset, $field_group, $key, $field_def );

		self::filter_schema_article_types();

		add_filter( 'update_post_metadata', [ self::class, 'remove_meta_if_default' ], 10, 5 );
		add_filter( 'add_post_metadata', [ self::class, 'dont_save_meta_if_default' ], 10, 4 );
	}

	/**
	 * Retrieve the meta box form field definitions for the given tab and post type.
	 *
	 * @param string $tab       Tab for which to retrieve the field definitions.
	 * @param string $post_type Post type of the current post.
	 *
	 * @return array Array containing the meta box field definitions.
	 */
	public static function get_meta_field_defs( $tab, $post_type = 'post' ) {
		if ( ! isset( self::$meta_fields[ $tab ] ) ) {
			return [];
		}

		$field_defs = self::$meta_fields[ $tab ];

		switch ( $tab ) {
			case 'non-form':
				// Prevent non-form fields from being passed to forms.
				$field_defs = [];
				break;

			case 'advanced':
				global $post;

				if ( ! WPSEO_Capability_Utils::current_user_can( 'wpseo_edit_advanced_metadata' ) && WPSEO_Options::get( 'disableadvanced_meta' ) ) {
					return [];
				}

				$post_type = '';
				if ( isset( $post->post_type ) ) {
					$post_type = $post->post_type;
				}
				elseif ( ! isset( $post->post_type ) && isset( $_GET['post_type'] ) ) {
					$post_type = sanitize_text_field( $_GET['post_type'] );
				}

				if ( $post_type === '' ) {
					return [];
				}

				/* Don't show the breadcrumb title field if breadcrumbs aren't enabled. */
				if ( WPSEO_Options::get( 'breadcrumbs-enable', false ) !== true && ! current_theme_supports( 'yoast-seo-breadcrumbs' ) ) {
					unset( $field_defs['bctitle'] );
				}

				if ( empty( $post->ID ) || ( ! empty( $post->ID ) && self::get_value( 'redirect', $post->ID ) === '' ) ) {
					unset( $field_defs['redirect'] );
				}
				break;

			case 'schema':
				if ( ! WPSEO_Capability_Utils::current_user_can( 'wpseo_edit_advanced_metadata' ) && WPSEO_Options::get( 'disableadvanced_meta' ) ) {
					return [];
				}

				$field_defs['schema_page_type']['default'] = WPSEO_Options::get( 'schema-page-type-' . $post_type );

				$article_helper = new Article_Helper();
				if ( $article_helper->is_article_post_type( $post_type ) ) {
					$default_schema_article_type = WPSEO_Options::get( 'schema-article-type-' . $post_type );

					/** This filter is documented in inc/options/class-wpseo-option-titles.php */
					$allowed_article_types = apply_filters( 'wpseo_schema_article_types', Schema_Types::ARTICLE_TYPES );

					if ( ! array_key_exists( $default_schema_article_type, $allowed_article_types ) ) {
						$default_schema_article_type = WPSEO_Options::get_default( 'wpseo_titles', 'schema-article-type-' . $post_type );
					}
					$field_defs['schema_article_type']['default'] = $default_schema_article_type;
				}
				else {
					unset( $field_defs['schema_article_type'] );
				}

				break;
		}

		/**
		 * Filter the WPSEO metabox form field definitions for a tab.
		 * {tab} can be 'general', 'advanced' or 'social'.
		 *
		 * @param array  $field_defs Metabox form field definitions.
		 * @param string $post_type  Post type of the post the metabox is for, defaults to 'post'.
		 *
		 * @return array
		 */
		return apply_filters( 'wpseo_metabox_entries_' . $tab, $field_defs, $post_type );
	}

	/**
	 * Validate the post meta values.
	 *
	 * @param mixed  $meta_value The new value.
	 * @param string $meta_key   The full meta key (including prefix).
	 *
	 * @return string Validated meta value.
	 */
	public static function sanitize_post_meta( $meta_value, $meta_key ) {
		$field_def = self::$meta_fields[ self::$fields_index[ $meta_key ]['subset'] ][ self::$fields_index[ $meta_key ]['key'] ];
		$clean     = self::$defaults[ $meta_key ];

		switch ( true ) {
			case ( $meta_key === self::$meta_prefix . 'linkdex' ):
				$int = WPSEO_Utils::validate_int( $meta_value );
				if ( $int !== false && $int >= 0 ) {
					$clean = strval( $int ); // Convert to string to make sure default check works.
				}
				break;

			case ( $field_def['type'] === 'checkbox' ):
				// Only allow value if it's one of the predefined options.
				if ( in_array( $meta_value, [ 'on', 'off' ], true ) ) {
					$clean = $meta_value;
				}
				break;

			case ( $field_def['type'] === 'select' || $field_def['type'] === 'radio' ):
				// Only allow value if it's one of the predefined options.
				if ( isset( $field_def['options'][ $meta_value ] ) ) {
					$clean = $meta_value;
				}
				break;

			case ( $field_def['type'] === 'hidden' && $meta_key === self::$meta_prefix . 'meta-robots-adv' ):
				$clean = self::validate_meta_robots_adv( $meta_value );
				break;

			case ( $field_def['type'] === 'url' || $meta_key === self::$meta_prefix . 'canonical' ):
				// Validate as url(-part).
				$url = WPSEO_Utils::sanitize_url( $meta_value );
				if ( $url !== '' ) {
					$clean = $url;
				}
				break;

			case ( $field_def['type'] === 'upload' && in_array( $meta_key, [ self::$meta_prefix . 'opengraph-image', self::$meta_prefix . 'twitter-image' ], true ) ):
				// Validate as url.
				$url = WPSEO_Utils::sanitize_url( $meta_value, [ 'http', 'https', 'ftp', 'ftps' ] );
				if ( $url !== '' ) {
					$clean = $url;
				}
				break;

			case ( $field_def['type'] === 'hidden' && $meta_key === self::$meta_prefix . 'is_cornerstone' ):
				$clean = $meta_value;

				/*
				 * This used to be a checkbox, then became a hidden input.
				 * To make sure the value remains consistent, we cast 'true' to '1'.
				 */
				if ( $meta_value === 'true' ) {
					$clean = '1';
				}
				break;

			case ( $field_def['type'] === 'hidden' && isset( $field_def['options'] ) ):
				// Only allow value if it's one of the predefined options.
				if ( isset( $field_def['options'][ $meta_value ] ) ) {
					$clean = $meta_value;
				}
				break;

			case ( $field_def['type'] === 'textarea' ):
				if ( is_string( $meta_value ) ) {
					// Remove line breaks and tabs.
					// @todo [JRF => Yoast] Verify that line breaks and the likes aren't allowed/recommended in meta header fields.
					$meta_value = str_replace( [ "\n", "\r", "\t", '  ' ], ' ', $meta_value );
					$clean      = WPSEO_Utils::sanitize_text_field( trim( $meta_value ) );
				}
				break;

			case ( $field_def['type'] === 'multiselect' ):
				$clean = $meta_value;
				break;

			case ( $field_def['type'] === 'text' ):
			default:
				if ( is_string( $meta_value ) ) {
					$clean = WPSEO_Utils::sanitize_text_field( trim( $meta_value ) );
				}

				break;
		}

		$clean = apply_filters( 'wpseo_sanitize_post_meta_' . $meta_key, $clean, $meta_value, $field_def, $meta_key );

		return $clean;
	}

	/**
	 * Validate a meta-robots-adv meta value.
	 *
	 * @todo [JRF => Yoast] Verify that this logic for the prioritisation is correct.
	 *
	 * @param array|string $meta_value The value to validate.
	 *
	 * @return string Clean value.
	 */
	public static function validate_meta_robots_adv( $meta_value ) {
		$clean   = self::$meta_fields['advanced']['meta-robots-adv']['default_value'];
		$options = self::$meta_fields['advanced']['meta-robots-adv']['options'];

		if ( is_string( $meta_value ) ) {
			$meta_value = explode( ',', $meta_value );
		}

		if ( is_array( $meta_value ) && $meta_value !== [] ) {
			$meta_value = array_map( 'trim', $meta_value );

			// Individual selected entries.
			$cleaning = [];
			foreach ( $meta_value as $value ) {
				if ( isset( $options[ $value ] ) ) {
					$cleaning[] = $value;
				}
			}

			if ( $cleaning !== [] ) {
				$clean = implode( ',', $cleaning );
			}
			unset( $cleaning, $value );
		}

		return $clean;
	}

	/**
	 * Prevent saving of default values and remove potential old value from the database if replaced by a default.
	 *
	 * @param bool   $check      The current status to allow updating metadata for the given type.
	 * @param int    $object_id  ID of the current object for which the meta is being updated.
	 * @param string $meta_key   The full meta key (including prefix).
	 * @param string $meta_value New meta value.
	 * @param string $prev_value The old meta value.
	 *
	 * @return bool|null True = stop saving, null = continue saving.
	 */
	public static function remove_meta_if_default( $check, $object_id, $meta_key, $meta_value, $prev_value = '' ) {
		/* If it's one of our meta fields, check against default. */
		if ( isset( self::$fields_index[ $meta_key ] ) && self::meta_value_is_default( $meta_key, $meta_value ) === true ) {
			if ( $prev_value !== '' ) {
				delete_post_meta( $object_id, $meta_key, $prev_value );
			}
			else {
				delete_post_meta( $object_id, $meta_key );
			}

			return true; // Stop saving the value.
		}

		return $check; // Go on with the normal execution (update) in meta.php.
	}

	/**
	 * Prevent adding of default values to the database.
	 *
	 * @param bool   $check      The current status to allow adding metadata for the given type.
	 * @param int    $object_id  ID of the current object for which the meta is being added.
	 * @param string $meta_key   The full meta key (including prefix).
	 * @param string $meta_value New meta value.
	 *
	 * @return bool|null True = stop saving, null = continue saving.
	 */
	public static function dont_save_meta_if_default( $check, $object_id, $meta_key, $meta_value ) {
		/* If it's one of our meta fields, check against default. */
		if ( isset( self::$fields_index[ $meta_key ] ) && self::meta_value_is_default( $meta_key, $meta_value ) === true ) {
			return true; // Stop saving the value.
		}

		return $check; // Go on with the normal execution (add) in meta.php.
	}

	/**
	 * Is the given meta value the same as the default value ?
	 *
	 * @param string $meta_key   The full meta key (including prefix).
	 * @param mixed  $meta_value The value to check.
	 *
	 * @return bool
	 */
	public static function meta_value_is_default( $meta_key, $meta_value ) {
		return ( isset( self::$defaults[ $meta_key ] ) && $meta_value === self::$defaults[ $meta_key ] );
	}

	/**
	 * Get a custom post meta value.
	 *
	 * Returns the default value if the meta value has not been set.
	 *
	 * {@internal Unfortunately there isn't a filter available to hook into before returning
	 *            the results for get_post_meta(), get_post_custom() and the likes. That
	 *            would have been the preferred solution.}}
	 *
	 * @param string $key    Internal key of the value to get (without prefix).
	 * @param int    $postid Post ID of the post to get the value for.
	 *
	 * @return string All 'normal' values returned from get_post_meta() are strings.
	 *                Objects and arrays are possible, but not used by this plugin
	 *                and therefore discarted (except when the special 'serialized' field def
	 *                value is set to true - only used by add-on plugins for now).
	 *                Will return the default value if no value was found.
	 *                Will return empty string if no default was found (not one of our keys) or
	 *                if the post does not exist.
	 */
	public static function get_value( $key, $postid = 0 ) {
		global $post;

		$postid = absint( $postid );
		if ( $postid === 0 ) {
			if ( ( isset( $post ) && is_object( $post ) ) && ( isset( $post->post_status ) && $post->post_status !== 'auto-draft' ) ) {
				$postid = $post->ID;
			}
			else {
				return '';
			}
		}

		$custom    = get_post_custom( $postid ); // Array of strings or empty array.
		$table_key = self::$meta_prefix . $key;

		// Populate the field_def using the field_index lookup array.
		$field_def = [];
		if ( isset( self::$fields_index[ $table_key ] ) ) {
			$field_def = self::$meta_fields[ self::$fields_index[ $table_key ]['subset'] ][ self::$fields_index[ $table_key ]['key'] ];
		}

		// Check if we have a custom post meta entry.
		if ( isset( $custom[ $table_key ][0] ) ) {
			$unserialized = maybe_unserialize( $custom[ $table_key ][0] );

			// Check if it is already unserialized.
			if ( $custom[ $table_key ][0] === $unserialized ) {
				return $custom[ $table_key ][0];
			}

			// Check whether we need to unserialize it.
			if ( isset( $field_def['serialized'] ) && $field_def['serialized'] === true ) {
				// Ok, serialize value expected/allowed.
				return $unserialized;
			}
		}

		// Meta was either not found or found, but object/array while not allowed to be.
		if ( isset( self::$defaults[ self::$meta_prefix . $key ] ) ) {
			// Update the default value to the current post type.
			switch ( $key ) {
				case 'schema_page_type':
				case 'schema_article_type':
					return '';
			}

			return self::$defaults[ self::$meta_prefix . $key ];
		}

		/*
		 * Shouldn't ever happen, means not one of our keys as there will always be a default available
		 * for all our keys.
		 */
		return '';
	}

	/**
	 * Update a meta value for a post.
	 *
	 * @param string $key        The internal key of the meta value to change (without prefix).
	 * @param mixed  $meta_value The value to set the meta to.
	 * @param int    $post_id    The ID of the post to change the meta for.
	 *
	 * @return bool Whether the value was changed.
	 */
	public static function set_value( $key, $meta_value, $post_id ) {
		/*
		 * Slash the data, because `update_metadata` will unslash it and we have already unslashed it.
		 * Related issue: https://github.com/Yoast/YoastSEO.js/issues/2158
		 */
		$meta_value = wp_slash( $meta_value );

		return update_post_meta( $post_id, self::$meta_prefix . $key, $meta_value );
	}

	/**
	 * Deletes a meta value for a post.
	 *
	 * @param string $key     The internal key of the meta value to change (without prefix).
	 * @param int    $post_id The ID of the post to delete the meta for.
	 *
	 * @return bool Whether the delete was successful or not.
	 */
	public static function delete( $key, $post_id ) {
		return delete_post_meta( $post_id, self::$meta_prefix . $key );
	}

	/**
	 * Used for imports, this functions imports the value of $old_metakey into $new_metakey for those post
	 * where no WPSEO meta data has been set.
	 * Optionally deletes the $old_metakey values.
	 *
	 * @param string $old_metakey The old key of the meta value.
	 * @param string $new_metakey The new key, usually the WPSEO meta key (including prefix).
	 * @param bool   $delete_old  Whether to delete the old meta key/value-sets.
	 *
	 * @return void
	 */
	public static function replace_meta( $old_metakey, $new_metakey, $delete_old = false ) {
		global $wpdb;

		/*
		 * Get only those rows where no wpseo meta values exist for the same post
		 * (with the exception of linkdex as that will be set independently of whether the post has been edited).
		 *
		 * {@internal Query is pretty well optimized this way.}}
		 */
		$query  = $wpdb->prepare(
			"
				SELECT `a`.*
				FROM {$wpdb->postmeta} AS a
				WHERE `a`.`meta_key` = %s
					AND NOT	EXISTS (
						SELECT DISTINCT `post_id` , count( `meta_id` ) AS count
						FROM {$wpdb->postmeta} AS b
						WHERE `a`.`post_id` = `b`.`post_id`
							AND `meta_key` LIKE %s
							AND `meta_key` <> %s
						GROUP BY `post_id`
					)
				;",
			$old_metakey,
			$wpdb->esc_like( self::$meta_prefix . '%' ),
			self::$meta_prefix . 'linkdex'
		);
		$oldies = $wpdb->get_results( $query );

		if ( is_array( $oldies ) && $oldies !== [] ) {
			foreach ( $oldies as $old ) {
				update_post_meta( $old->post_id, $new_metakey, $old->meta_value );
			}
		}

		// Delete old keys.
		if ( $delete_old === true ) {
			delete_post_meta_by_key( $old_metakey );
		}
	}

	/**
	 * General clean-up of the saved meta values.
	 * - Remove potentially lingering old meta keys;
	 * - Remove all default and invalid values.
	 *
	 * @return void
	 */
	public static function clean_up() {
		global $wpdb;

		/*
		 * Clean up '_yoast_wpseo_meta-robots'.
		 *
		 * Retrieve all '_yoast_wpseo_meta-robots' meta values and convert if no new values found.
		 *
		 * {@internal Query is pretty well optimized this way.}}
		 *
		 * @todo [JRF => Yoast] Find out all possible values which the old '_yoast_wpseo_meta-robots' could contain
		 * to convert the data correctly.
		 */
		$query  = $wpdb->prepare(
			"
				SELECT `a`.*
				FROM {$wpdb->postmeta} AS a
				WHERE `a`.`meta_key` = %s
					AND NOT	EXISTS (
						SELECT DISTINCT `post_id` , count( `meta_id` ) AS count
						FROM {$wpdb->postmeta} AS b
						WHERE `a`.`post_id` = `b`.`post_id`
							AND ( `meta_key` = %s
							OR `meta_key` = %s )
						GROUP BY `post_id`
					)
				;",
			self::$meta_prefix . 'meta-robots',
			self::$meta_prefix . 'meta-robots-noindex',
			self::$meta_prefix . 'meta-robots-nofollow'
		);
		$oldies = $wpdb->get_results( $query );

		if ( is_array( $oldies ) && $oldies !== [] ) {
			foreach ( $oldies as $old ) {
				$old_values = explode( ',', $old->meta_value );
				foreach ( $old_values as $value ) {
					if ( $value === 'noindex' ) {
						update_post_meta( $old->post_id, self::$meta_prefix . 'meta-robots-noindex', 1 );
					}
					elseif ( $value === 'nofollow' ) {
						update_post_meta( $old->post_id, self::$meta_prefix . 'meta-robots-nofollow', 1 );
					}
				}
			}
		}
		unset( $query, $oldies, $old, $old_values, $value );

		// Delete old keys.
		delete_post_meta_by_key( self::$meta_prefix . 'meta-robots' );

		/*
		 * Remove all default values and (most) invalid option values.
		 * Invalid option values for the multiselect (meta-robots-adv) field will be dealt with seperately.
		 *
		 * {@internal Some of the defaults have changed in v1.5, but as the defaults will
		 *            be removed and new defaults will now automatically be passed when no
		 *            data found, this update is automatic (as long as we remove the old
		 *            values which we do in the below routine).}}
		 *
		 * {@internal Unfortunately we can't use the normal delete_meta() with key/value combination
		 *            as '' (empty string) values will be ignored and would result in all metas
		 *            with that key being deleted, not just the empty fields.
		 *            Still, the below implementation is largely based on the delete_meta() function.}}
		 */
		$query = [];

		foreach ( self::$meta_fields as $subset => $field_group ) {
			foreach ( $field_group as $key => $field_def ) {
				if ( ! isset( $field_def['default_value'] ) ) {
					continue;
				}

				if ( isset( $field_def['options'] ) && is_array( $field_def['options'] ) && $field_def['options'] !== [] ) {
					$valid = $field_def['options'];
					// Remove the default value from the valid options.
					unset( $valid[ $field_def['default_value'] ] );
					$valid = array_keys( $valid );

					$query[] = $wpdb->prepare(
						"( meta_key = %s AND meta_value NOT IN ( '" . implode( "','", esc_sql( $valid ) ) . "' ) )",
						self::$meta_prefix . $key
					);
					unset( $valid );
				}
				elseif ( is_string( $field_def['default_value'] ) && $field_def['default_value'] !== '' ) {
					$query[] = $wpdb->prepare(
						'( meta_key = %s AND meta_value = %s )',
						self::$meta_prefix . $key,
						$field_def['default_value']
					);
				}
				else {
					$query[] = $wpdb->prepare(
						"( meta_key = %s AND meta_value = '' )",
						self::$meta_prefix . $key
					);
				}
			}
		}
		unset( $subset, $field_group, $key, $field_def );

		$query    = "SELECT meta_id FROM {$wpdb->postmeta} WHERE " . implode( ' OR ', $query ) . ';';
		$meta_ids = $wpdb->get_col( $query );

		if ( is_array( $meta_ids ) && $meta_ids !== [] ) {
			// WP native action.
			do_action( 'delete_post_meta', $meta_ids, null, null, null );

			$query = "DELETE FROM {$wpdb->postmeta} WHERE meta_id IN( " . implode( ',', $meta_ids ) . ' )';
			$count = $wpdb->query( $query );

			if ( $count ) {
				foreach ( $meta_ids as $object_id ) {
					wp_cache_delete( $object_id, 'post_meta' );
				}

				// WP native action.
				do_action( 'deleted_post_meta', $meta_ids, null, null, null );
			}
		}
		unset( $query, $meta_ids, $count, $object_id );

		/*
		 * Deal with the multiselect (meta-robots-adv) field.
		 *
		 * Removes invalid option combinations, such as 'none,noarchive'.
		 *
		 * Default values have already been removed, so we should have a small result set and
		 * (hopefully) even smaller set of invalid results.
		 */
		$query  = $wpdb->prepare(
			"SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s",
			self::$meta_prefix . 'meta-robots-adv'
		);
		$oldies = $wpdb->get_results( $query );

		if ( is_array( $oldies ) && $oldies !== [] ) {
			foreach ( $oldies as $old ) {
				$clean = self::validate_meta_robots_adv( $old->meta_value );

				if ( $clean !== $old->meta_value ) {
					if ( $clean !== self::$meta_fields['advanced']['meta-robots-adv']['default_value'] ) {
						update_metadata_by_mid( 'post', $old->meta_id, $clean );
					}
					else {
						delete_metadata_by_mid( 'post', $old->meta_id );
					}
				}
			}
		}
		unset( $query, $oldies, $old, $clean );

		do_action( 'wpseo_meta_clean_up' );
	}

	/**
	 * Recursively merge a variable number of arrays, using the left array as base,
	 * giving priority to the right array.
	 *
	 * Difference with native array_merge_recursive():
	 * array_merge_recursive converts values with duplicate keys to arrays rather than
	 * overwriting the value in the first array with the duplicate value in the second array.
	 *
	 * array_merge_recursive_distinct does not change the data types of the values in the arrays.
	 * Matching keys' values in the second array overwrite those in the first array, as is the
	 * case with array_merge.
	 *
	 * Freely based on information found on http://www.php.net/manual/en/function.array-merge-recursive.php
	 *
	 * {@internal Should be moved to a general utility class.}}
	 *
	 * @return array
	 */
	public static function array_merge_recursive_distinct() {

		$arrays = func_get_args();
		if ( count( $arrays ) < 2 ) {
			if ( $arrays === [] ) {
				return [];
			}
			else {
				return $arrays[0];
			}
		}

		$merged = array_shift( $arrays );

		foreach ( $arrays as $array ) {
			foreach ( $array as $key => $value ) {
				if ( is_array( $value ) && ( isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) ) {
					$merged[ $key ] = self::array_merge_recursive_distinct( $merged[ $key ], $value );
				}
				else {
					$merged[ $key ] = $value;
				}
			}
			unset( $key, $value );
		}

		return $merged;
	}

	/**
	 * Counts the total of all the keywords being used for posts except the given one.
	 *
	 * @param string $keyword The keyword to be counted.
	 * @param int    $post_id The id of the post to which the keyword belongs.
	 *
	 * @return array
	 */
	public static function keyword_usage( $keyword, $post_id ) {

		if ( empty( $keyword ) ) {
			return [];
		}

		/**
		 * The indexable repository.
		 *
		 * @var Indexable_Repository $repository
		 */
		$repository = YoastSEO()->classes->get( Indexable_Repository::class );

		$post_ids = $repository->query()
			->select( 'object_id' )
			->where( 'primary_focus_keyword', $keyword )
			->where( 'object_type', 'post' )
			->where_not_equal( 'object_id', $post_id )
			->where_not_equal( 'post_status', 'trash' )
			->limit( 2 )    // Limit to 2 results to save time and resources.
			->find_array();

		// Get object_id from each subarray in $post_ids.
		$post_ids = ( is_array( $post_ids ) ) ? array_column( $post_ids, 'object_id' ) : [];

		/*
		 * If Premium is installed, get the additional keywords as well.
		 * We only check for the additional keywords if we've not already found two.
		 * In that case there's no use for an additional query as we already know
		 * that the keyword has been used multiple times before.
		 */
		if ( count( $post_ids ) < 2 ) {
			/**
			 * Allows enhancing the array of posts' that share their focus keywords with the post's focus keywords.
			 *
			 * @param array  $post_ids The array of posts' ids that share their related keywords with the post.
			 * @param string $keyword  The keyword to search for.
			 * @param int    $post_id  The id of the post the keyword is associated to.
			 */
			$post_ids = apply_filters( 'wpseo_posts_for_focus_keyword', $post_ids, $keyword, $post_id );
		}

		return $post_ids;
	}

	/**
	 * Returns the post types for the given post ids.
	 *
	 * @param array $post_ids The post ids to get the post types for.
	 *
	 * @return array The post types.
	 */
	public static function post_types_for_ids( $post_ids ) {
		// Check if post ids is not empty.
		if ( ! empty( $post_ids ) ) {
			/**
			 * The indexable repository.
			 *
			 * @var Indexable_Repository $repository
			 */
			$repository = YoastSEO()->classes->get( Indexable_Repository::class );

			// Get the post subtypes for the posts that share the keyword.
			$post_types = $repository->query()
				->select( 'object_sub_type' )
				->where_in( 'object_id', $post_ids )
				->find_array();

			// Get object_sub_type from each subarray in $post_ids.
			$post_types = array_column( $post_types, 'object_sub_type' );
		}
		else {
			$post_types = [];
		}

		return $post_types;
	}

	/**
	 * Filter the schema article types.
	 *
	 * @return void
	 */
	public static function filter_schema_article_types() {
		/** This filter is documented in inc/options/class-wpseo-option-titles.php */
		self::$meta_fields['schema']['schema_article_type']['options'] = apply_filters( 'wpseo_schema_article_types', self::$meta_fields['schema']['schema_article_type']['options'] );
	}
}
