<?php
/**
 * @package WPSEO\Internals\Options
 */

/**
 * Option: wpseo_titles
 */
class WPSEO_Option_Titles extends WPSEO_Option {

	/**
	 * @var  string  option name
	 */
	public $option_name = 'wpseo_titles';

	/**
	 * @var  array  Array of defaults for the option
	 *        Shouldn't be requested directly, use $this->get_defaults();
	 * @internal  Note: Some of the default values are added via the translate_defaults() method
	 */
	protected $defaults = array(
		// Non-form fields, set via (ajax) function.
		'title_test'             => 0,
		// Form fields.
		'forcerewritetitle'      => false,
		'separator'              => 'sc-dash',
		'noodp'                  => false,
		'noydir'                 => false,
		'usemetakeywords'        => false,
		'title-home-wpseo'       => '%%sitename%% %%page%% %%sep%% %%sitedesc%%', // Text field.
		'title-author-wpseo'     => '', // Text field.
		'title-archive-wpseo'    => '%%date%% %%page%% %%sep%% %%sitename%%', // Text field.
		'title-search-wpseo'     => '', // Text field.
		'title-404-wpseo'        => '', // Text field.

		'metadesc-home-wpseo'    => '', // Text area.
		'metadesc-author-wpseo'  => '', // Text area.
		'metadesc-archive-wpseo' => '', // Text area.

		'metakey-home-wpseo'     => '', // Text field.
		'metakey-author-wpseo'   => '', // Text field.

		'noindex-subpages-wpseo' => false,
		'noindex-author-wpseo'   => false,
		'noindex-archive-wpseo'  => true,
		'disable-author'         => false,
		'disable-date'           => false,


		/**
		 * Uses enrich_defaults to add more along the lines of:
		 * - 'title-' . $pt->name        => ''; // Text field.
		 * - 'metadesc-' . $pt->name      => ''; // Text field.
		 * - 'metakey-' . $pt->name        => ''; // Text field.
		 * - 'noindex-' . $pt->name        => false;
		 * - 'showdate-' . $pt->name      => false;
		 * - 'hideeditbox-' . $pt->name      => false;
		 *
		 * - 'title-ptarchive-' . $pt->name    => ''; // Text field.
		 * - 'metadesc-ptarchive-' . $pt->name  => ''; // Text field.
		 * - 'metakey-ptarchive-' . $pt->name  => ''; // Text field.
		 * - 'bctitle-ptarchive-' . $pt->name  => ''; // Text field.
		 * - 'noindex-ptarchive-' . $pt->name  => false;
		 *
		 * - 'title-tax-' . $tax->name      => '''; // Text field.
		 * - 'metadesc-tax-' . $tax->name    => ''; // Text field.
		 * - 'metakey-tax-' . $tax->name    => ''; // Text field.
		 * - 'noindex-tax-' . $tax->name    => false;
		 * - 'hideeditbox-tax-' . $tax->name  => false;
		 */
	);

	/**
	 * @var  array  Array of variable option name patterns for the option
	 */
	protected $variable_array_key_patterns = array(
		'title-',
		'metadesc-',
		'metakey-',
		'noindex-',
		'showdate-',
		'hideeditbox-',
		'bctitle-ptarchive-',
	);

	/**
	 * @var array  Array of sub-options which should not be overloaded with multi-site defaults
	 */
	public $ms_exclude = array(
		/* theme dependent */
		'title_test',
		'forcerewritetitle',
	);

	/**
	 * @var array Array of the separator options. To get these options use WPSEO_Option_Titles::get_instance()->get_separator_options()
	 */
	private $separator_options = array(
		'sc-dash'   => '-',
		'sc-ndash'  => '&ndash;',
		'sc-mdash'  => '&mdash;',
		'sc-middot' => '&middot;',
		'sc-bull'   => '&bull;',
		'sc-star'   => '*',
		'sc-smstar' => '&#8902;',
		'sc-pipe'   => '|',
		'sc-tilde'  => '~',
		'sc-laquo'  => '&laquo;',
		'sc-raquo'  => '&raquo;',
		'sc-lt'     => '&lt;',
		'sc-gt'     => '&gt;',
	);

	/**
	 * Add the actions and filters for the option
	 *
	 * @todo [JRF => testers] Check if the extra actions below would run into problems if an option
	 * is updated early on and if so, change the call to schedule these for a later action on add/update
	 * instead of running them straight away
	 *
	 * @return \WPSEO_Option_Titles
	 */
	protected function __construct() {
		parent::__construct();
		add_action( 'update_option_' . $this->option_name, array( 'WPSEO_Utils', 'clear_cache' ) );
		add_action( 'init', array( $this, 'end_of_init' ), 999 );
	}


	/**
	 * Make sure we can recognize the right action for the double cleaning
	 */
	public function end_of_init() {
		do_action( 'wpseo_double_clean_titles' );
	}


	/**
	 * Get the singleton instance of this class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the available separator options
	 *
	 * @return array
	 */
	public function get_separator_options() {
		$separators = $this->separator_options;

		/**
		 * Allow altering the array with separator options
		 * @api  array  $separator_options  Array with the separator options
		 */
		$filtered_separators = apply_filters( 'wpseo_separator_options', $separators );

		if ( is_array( $filtered_separators ) && $filtered_separators !== array() ) {
			$separators = array_merge( $separators, $filtered_separators );
		}

		return $separators;
	}

	/**
	 * Translate strings used in the option defaults
	 *
	 * @return void
	 */
	public function translate_defaults() {
		$this->defaults['title-author-wpseo'] = sprintf( __( '%s, Author at %s', 'wordpress-seo' ), '%%name%%', '%%sitename%%' ) . ' %%page%% ';
		$this->defaults['title-search-wpseo'] = sprintf( __( 'You searched for %s', 'wordpress-seo' ), '%%searchphrase%%' ) . ' %%page%% %%sep%% %%sitename%%';
		$this->defaults['title-404-wpseo']    = __( 'Page not found', 'wordpress-seo' ) . ' %%sep%% %%sitename%%';
	}


	/**
	 * Add dynamically created default options based on available post types and taxonomies
	 *
	 * @return  void
	 */
	public function enrich_defaults() {

		// Retrieve all the relevant post type and taxonomy arrays.
		$post_type_names = get_post_types( array( 'public' => true ), 'names' );

		$post_type_objects_custom = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' );

		$taxonomy_names = get_taxonomies( array( 'public' => true ), 'names' );


		if ( $post_type_names !== array() ) {
			foreach ( $post_type_names as $pt ) {
				$this->defaults[ 'title-' . $pt ]       = '%%title%% %%page%% %%sep%% %%sitename%%'; // Text field.
				$this->defaults[ 'metadesc-' . $pt ]    = ''; // Text area.
				$this->defaults[ 'metakey-' . $pt ]     = ''; // Text field.
				$this->defaults[ 'noindex-' . $pt ]     = false;
				$this->defaults[ 'showdate-' . $pt ]    = false;
				$this->defaults[ 'hideeditbox-' . $pt ] = false;
			}
			unset( $pt );
		}

		if ( $post_type_objects_custom !== array() ) {
			$archive = sprintf( __( '%s Archive', 'wordpress-seo' ), '%%pt_plural%%' );
			foreach ( $post_type_objects_custom as $pt ) {
				if ( ! $pt->has_archive ) {
					continue;
				}

				$this->defaults[ 'title-ptarchive-' . $pt->name ]    = $archive . ' %%page%% %%sep%% %%sitename%%'; // Text field.
				$this->defaults[ 'metadesc-ptarchive-' . $pt->name ] = ''; // Text area.
				$this->defaults[ 'metakey-ptarchive-' . $pt->name ]  = ''; // Text field.
				$this->defaults[ 'bctitle-ptarchive-' . $pt->name ]  = ''; // Text field.
				$this->defaults[ 'noindex-ptarchive-' . $pt->name ]  = false;
			}
			unset( $pt );
		}

		if ( $taxonomy_names !== array() ) {
			$archives = sprintf( __( '%s Archives', 'wordpress-seo' ), '%%term_title%%' );
			foreach ( $taxonomy_names as $tax ) {
				$this->defaults[ 'title-tax-' . $tax ]       = $archives . ' %%page%% %%sep%% %%sitename%%'; // Text field.
				$this->defaults[ 'metadesc-tax-' . $tax ]    = ''; // Text area.
				$this->defaults[ 'metakey-tax-' . $tax ]     = ''; // Text field.
				$this->defaults[ 'hideeditbox-tax-' . $tax ] = false;

				if ( $tax !== 'post_format' ) {
					$this->defaults[ 'noindex-tax-' . $tax ] = false;
				}
				else {
					$this->defaults[ 'noindex-tax-' . $tax ] = true;
				}
			}
			unset( $tax );
		}
	}


	/**
	 * Validate the option
	 *
	 * @param  array $dirty New value for the option.
	 * @param  array $clean Clean value for the option, normally the defaults.
	 * @param  array $old   Old value of the option.
	 *
	 * @return  array      Validated clean value for the option to be saved to the database
	 */
	protected function validate_option( $dirty, $clean, $old ) {

		foreach ( $clean as $key => $value ) {
			$switch_key = $this->get_switch_key( $key );

			switch ( $switch_key ) {
				/*
				Text fields
				*/

				/*
				Covers:
					   'title-home-wpseo', 'title-author-wpseo', 'title-archive-wpseo',
					   'title-search-wpseo', 'title-404-wpseo'
					   'title-' . $pt->name
					   'title-ptarchive-' . $pt->name
					   'title-tax-' . $tax->name
				*/
				case 'title-':
					if ( isset( $dirty[ $key ] ) ) {
						$clean[ $key ] = WPSEO_Utils::sanitize_text_field( $dirty[ $key ] );
					}
					break;

				/*
				Covers:
					   'metadesc-home-wpseo', 'metadesc-author-wpseo', 'metadesc-archive-wpseo'
					   'metadesc-' . $pt->name
					   'metadesc-ptarchive-' . $pt->name
					   'metadesc-tax-' . $tax->name
				*/
				case 'metadesc-':
					/*
					Covers:
							 'metakey-home-wpseo', 'metakey-author-wpseo'
							 'metakey-' . $pt->name
							 'metakey-ptarchive-' . $pt->name
							 'metakey-tax-' . $tax->name
					*/
				case 'metakey-':
					/*
					Covers:
							 ''bctitle-ptarchive-' . $pt->name
					*/
				case 'bctitle-ptarchive-':
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
						$clean[ $key ] = WPSEO_Utils::sanitize_text_field( $dirty[ $key ] );
					}
					break;


				/* integer field - not in form*/
				case 'title_test':
					if ( isset( $dirty[ $key ] ) ) {
						$int = WPSEO_Utils::validate_int( $dirty[ $key ] );
						if ( $int !== false && $int >= 0 ) {
							$clean[ $key ] = $int;
						}
					}
					elseif ( isset( $old[ $key ] ) ) {
						$int = WPSEO_Utils::validate_int( $old[ $key ] );
						if ( $int !== false && $int >= 0 ) {
							$clean[ $key ] = $int;
						}
					}
					break;

				/* Separator field - Radio */
				case 'separator':
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {

						// Get separator fields.
						$separator_fields = $this->get_separator_options();

						// Check if the given separator is exists.
						if ( isset( $separator_fields[ $dirty[ $key ] ] ) ) {
							$clean[ $key ] = $dirty[ $key ];
						}
					}
					break;

				/*
				Boolean fields
				*/

				/*
				Covers:
				 *		'noindex-subpages-wpseo', 'noindex-author-wpseo', 'noindex-archive-wpseo'
				 *		'noindex-' . $pt->name
				 *		'noindex-ptarchive-' . $pt->name
				 *		'noindex-tax-' . $tax->name
				 *		'forcerewritetitle':
				 *		'usemetakeywords':
				 *		'noodp':
				 *		'noydir':
				 *		'disable-author':
				 *		'disable-date':
				 *		'noindex-'
				 *		'showdate-'
				 *		'showdate-'. $pt->name
				 *		'hideeditbox-'
				 *	 	'hideeditbox-'. $pt->name
				 *		'hideeditbox-tax-' . $tax->name
				 */
				default:
					$clean[ $key ] = ( isset( $dirty[ $key ] ) ? WPSEO_Utils::validate_bool( $dirty[ $key ] ) : false );
					break;
			}
		}

		return $clean;
	}


	/**
	 * Clean a given option value
	 *
	 * @param  array  $option_value          Old (not merged with defaults or filtered) option value to
	 *                                       clean according to the rules for this option.
	 * @param  string $current_version       (optional) Version from which to upgrade, if not set,
	 *                                       version specific upgrades will be disregarded.
	 * @param  array  $all_old_option_values (optional) Only used when importing old options to have
	 *                                       access to the real old values, in contrast to the saved ones.
	 *
	 * @return  array            Cleaned option
	 */
	protected function clean_option( $option_value, $current_version = null, $all_old_option_values = null ) {
		static $original = null;

		// Double-run this function to ensure renaming of the taxonomy options will work.
		if ( ! isset( $original ) && has_action( 'wpseo_double_clean_titles', array(
				$this,
				'clean',
			) ) === false
		) {
			add_action( 'wpseo_double_clean_titles', array( $this, 'clean' ) );
			$original = $option_value;
		}

		/*
		Move options from very old option to this one
			   @internal Don't rename to the 'current' names straight away as that would prevent
			   the rename/unset combi below from working
			   @todo [JRF] maybe figure out a smarter way to deal with this
		*/
		$old_option = null;
		if ( isset( $all_old_option_values ) ) {
			// Ok, we have an import.
			if ( isset( $all_old_option_values['wpseo_indexation'] ) && is_array( $all_old_option_values['wpseo_indexation'] ) && $all_old_option_values['wpseo_indexation'] !== array() ) {
				$old_option = $all_old_option_values['wpseo_indexation'];
			}
		}
		else {
			$old_option = get_option( 'wpseo_indexation' );
		}
		if ( is_array( $old_option ) && $old_option !== array() ) {
			$move = array(
				'noindexauthor'     => 'noindex-author',
				'disableauthor'     => 'disable-author',
				'noindexdate'       => 'noindex-archive',
				'noindexcat'        => 'noindex-category',
				'noindextag'        => 'noindex-post_tag',
				'noindexpostformat' => 'noindex-post_format',
				'noindexsubpages'   => 'noindex-subpages',
				'hidersdlink'       => 'hide-rsdlink',
				'hidefeedlinks'     => 'hide-feedlinks',
				'hidewlwmanifest'   => 'hide-wlwmanifest',
				'hideshortlink'     => 'hide-shortlink',
			);
			foreach ( $move as $old => $new ) {
				if ( isset( $old_option[ $old ] ) && ! isset( $option_value[ $new ] ) ) {
					$option_value[ $new ] = $old_option[ $old ];
				}
			}
			unset( $move, $old, $new );
		}
		unset( $old_option );


		// Fix wrongness created by buggy version 1.2.2.
		if ( isset( $option_value['title-home'] ) && $option_value['title-home'] === '%%sitename%% - %%sitedesc%% - 12345' ) {
			$option_value['title-home-wpseo'] = '%%sitename%% - %%sitedesc%%';
		}


		/*
		Renaming these options to avoid ever overwritting these if a (bloody stupid) user /
			   programmer would use any of the following as a custom post type or custom taxonomy:
			   'home', 'author', 'archive', 'search', '404', 'subpages'

			   Similarly, renaming the tax options to avoid a custom post type and a taxonomy
			   with the same name occupying the same option
		*/
		$rename = array(
			'title-home'       => 'title-home-wpseo',
			'title-author'     => 'title-author-wpseo',
			'title-archive'    => 'title-archive-wpseo',
			'title-search'     => 'title-search-wpseo',
			'title-404'        => 'title-404-wpseo',
			'metadesc-home'    => 'metadesc-home-wpseo',
			'metadesc-author'  => 'metadesc-author-wpseo',
			'metadesc-archive' => 'metadesc-archive-wpseo',
			'metakey-home'     => 'metakey-home-wpseo',
			'metakey-author'   => 'metakey-author-wpseo',
			'noindex-subpages' => 'noindex-subpages-wpseo',
			'noindex-author'   => 'noindex-author-wpseo',
			'noindex-archive'  => 'noindex-archive-wpseo',
		);
		foreach ( $rename as $old => $new ) {
			if ( isset( $option_value[ $old ] ) && ! isset( $option_value[ $new ] ) ) {
				$option_value[ $new ] = $option_value[ $old ];
				unset( $option_value[ $old ] );
			}
		}
		unset( $rename, $old, $new );


		/**
		 * @internal This clean-up action can only be done effectively once the taxonomies and post_types
		 * have been registered, i.e. at the end of the init action.
		 */
		if ( isset( $original ) && current_filter() === 'wpseo_double_clean_titles' || did_action( 'wpseo_double_clean_titles' ) > 0 ) {
			$rename          = array(
				'title-'           => 'title-tax-',
				'metadesc-'        => 'metadesc-tax-',
				'metakey-'         => 'metakey-tax-',
				'noindex-'         => 'noindex-tax-',
				'tax-hideeditbox-' => 'hideeditbox-tax-',

			);
			$taxonomy_names  = get_taxonomies( array( 'public' => true ), 'names' );
			$post_type_names = get_post_types( array( 'public' => true ), 'names' );
			$defaults        = $this->get_defaults();
			if ( $taxonomy_names !== array() ) {
				foreach ( $taxonomy_names as $tax ) {
					foreach ( $rename as $old_prefix => $new_prefix ) {
						if (
							( isset( $original[ $old_prefix . $tax ] ) && ! isset( $original[ $new_prefix . $tax ] ) )
							&& ( ! isset( $option_value[ $new_prefix . $tax ] )
							     || ( isset( $option_value[ $new_prefix . $tax ] )
							          && $option_value[ $new_prefix . $tax ] === $defaults[ $new_prefix . $tax ] ) )
						) {
							$option_value[ $new_prefix . $tax ] = $original[ $old_prefix . $tax ];

							/*
							Check if there is a cpt with the same name as the tax,
								   if so, we should make sure that the old setting hasn't been removed
							*/
							if ( ! isset( $post_type_names[ $tax ] ) && isset( $option_value[ $old_prefix . $tax ] ) ) {
								unset( $option_value[ $old_prefix . $tax ] );
							}
							else {
								if ( isset( $post_type_names[ $tax ] ) && ! isset( $option_value[ $old_prefix . $tax ] ) ) {
									$option_value[ $old_prefix . $tax ] = $original[ $old_prefix . $tax ];
								}
							}

							if ( $old_prefix === 'tax-hideeditbox-' ) {
								unset( $option_value[ $old_prefix . $tax ] );
							}
						}
					}
				}
			}
			unset( $rename, $taxonomy_names, $post_type_names, $defaults, $tax, $old_prefix, $new_prefix );
		}


		/*
		Make sure the values of the variable option key options are cleaned as they
			   may be retained and would not be cleaned/validated then
		*/
		if ( is_array( $option_value ) && $option_value !== array() ) {
			foreach ( $option_value as $key => $value ) {
				$switch_key = $this->get_switch_key( $key );

				// Similar to validation routine - any changes made there should be made here too.
				switch ( $switch_key ) {
					/* text fields */
					case 'title-':
					case 'metadesc-':
					case 'metakey-':
					case 'bctitle-ptarchive-':
						$option_value[ $key ] = WPSEO_Utils::sanitize_text_field( $value );
						break;

					case 'separator':
						if ( ! array_key_exists( $value, $this->get_separator_options() ) ) {
							$option_value[ $key ] = false;
						}
						break;

					/*
					Boolean fields
					*/

					/*
					Covers:
					 * 		'noindex-'
					 * 		'showdate-'
					 * 		'hideeditbox-'
					 */
					default:
						$option_value[ $key ] = WPSEO_Utils::validate_bool( $value );
						break;
				}
			}
			unset( $key, $value, $switch_key );
		}

		return $option_value;
	}


	/**
	 * Make sure that any set option values relating to post_types and/or taxonomies are retained,
	 * even when that post_type or taxonomy may not yet have been registered.
	 *
	 * @internal Overrule the abstract class version of this to make sure one extra renamed variable key
	 * does not get removed. IMPORTANT: keep this method in line with the parent on which it is based!
	 *
	 * @param  array $dirty Original option as retrieved from the database.
	 * @param  array $clean Filtered option where any options which shouldn't be in our option
	 *                      have already been removed and any options which weren't set
	 *                      have been set to their defaults.
	 *
	 * @return  array
	 */
	protected function retain_variable_keys( $dirty, $clean ) {
		if ( ( is_array( $this->variable_array_key_patterns ) && $this->variable_array_key_patterns !== array() ) && ( is_array( $dirty ) && $dirty !== array() ) ) {

			// Add the extra pattern.
			$patterns   = $this->variable_array_key_patterns;
			$patterns[] = 'tax-hideeditbox-';

			/**
			 * Allow altering the array with variable array key patterns
			 * @api  array  $patterns  Array with the variable array key patterns
			 */
			$patterns = apply_filters( 'wpseo_option_titles_variable_array_key_patterns', $patterns );

			foreach ( $dirty as $key => $value ) {

				// Do nothing if already in filtered option array.
				if ( isset( $clean[ $key ] ) ) {
					continue;
				}

				foreach ( $patterns as $pattern ) {
					if ( strpos( $key, $pattern ) === 0 ) {
						$clean[ $key ] = $value;
						break;
					}
				}
			}
		}

		return $clean;
	}
}
