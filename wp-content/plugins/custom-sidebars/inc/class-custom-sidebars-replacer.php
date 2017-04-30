<?php

add_action( 'cs_init', array( 'CustomSidebarsReplacer', 'instance' ) );

/**
 * This class actually replaces sidebars on the frontend.
 *
 * @since  2.0
 */
class CustomSidebarsReplacer extends CustomSidebars {

	private $original_post_id = 0;

	/**
	 * Returns the singleton object.
	 *
	 * @since  2.0
	 */
	public static function instance() {
		static $Inst = null;

		if ( null === $Inst ) {
			$Inst = new CustomSidebarsReplacer();
		}

		return $Inst;
	}

	/**
	 * Constructor is private -> singleton.
	 *
	 * @since  2.0
	 */
	private function __construct() {
		add_action(
			'widgets_init',
			array( $this, 'register_custom_sidebars')
		);

		if ( ! is_admin() ) {
			// Frontend hooks.
			add_action(
				'wp_head',
				array( $this, 'replace_sidebars' )
			);

			add_action(
				'wp',
				array( $this, 'store_original_post_id' )
			);
		}
	}

	/**
	 * Tell WordPress about the custom sidebars.
	 */
	public function register_custom_sidebars() {
		$sb = self::get_custom_sidebars();

		foreach ( $sb as $sidebar ) {
			/**
			 * i18n support for custom sidebars.
			 */
			$sidebar['name'] = __( $sidebar['name'], CSB_LANG );
			$sidebar['description'] = __( $sidebar['description'], CSB_LANG );
			$sidebar['before_widget'] = __( $sidebar['before_widget'], CSB_LANG );
			$sidebar['after_widget'] = __( $sidebar['after_widget'], CSB_LANG );
			$sidebar['before_title'] = __( $sidebar['before_title'], CSB_LANG );
			$sidebar['after_title'] = __( $sidebar['after_title'], CSB_LANG );

			/**
			 * Filter sidebar options for custom sidebars.
			 *
			 * @since  2.0
			 *
			 * @param  array $sidebar Options used by WordPress to display
			 *           the sidebar.
			 */
			$sidebar = apply_filters( 'cs_sidebar_params', $sidebar );

			register_sidebar( $sidebar );
		}
	}

	/**
	 * Stores the original post id before any plugin (buddypress) can modify this data, to show the proper sidebar.
	 */
	public function store_original_post_id() {
		global $post;

		if ( isset( $post->ID ) ) {
			$this->original_post_id = $post->ID;
		}
	}

	/**
	 * Replace the sidebars on current page with some custom sidebars.
	 * Sidebars are replaced by directly modifying the WordPress globals
	 * `$_wp_sidebars_widgets` and `$wp_registered_sidebars`
	 *
	 * What it really does it not replacing a specific *sidebar* but simply
	 * replacing all widgets inside the theme sidebars with the widgets of the
	 * custom defined sidebars.
	 */
	public function replace_sidebars() {
		global $_wp_sidebars_widgets,
			$wp_registered_sidebars,
			$wp_registered_widgets;

		/**
		 * Original sidebar configuration by WordPress:
		 * Lists sidebars and all widgets inside each sidebar.
		 */
		$original_widgets = $_wp_sidebars_widgets;

		$defaults = self::get_options();

		/**
		 * Fires before determining sidebar replacements.
		 *
		 * @param  array $defaults Array of the default sidebars for the page.
		 */
		do_action( 'cs_predetermine_replacements', $defaults );

		// Legacy handler with camelCase
		do_action( 'cs_predetermineReplacements', $defaults );

		$replacements = $this->determine_replacements( $defaults );

		foreach ( $replacements as $sb_id => $replace_info ) {
			if ( ! is_array( $replace_info ) || count( $replace_info ) < 3 ) {
				continue;
			}

			list( $replacement, $replacement_type, $extra_index ) = $replace_info;
			$check = $this->is_valid_replacement( $sb_id, $replacement, $replacement_type, $extra_index );

			if ( $check ) {
				if ( sizeof( $original_widgets[$replacement] ) == 0 ) {
					// No widgets on custom sidebar, show nothing.
					$wp_registered_widgets['csemptywidget'] = $this->get_empty_widget();
					$_wp_sidebars_widgets[$sb_id] = array( 'csemptywidget' );
				} else {
					$_wp_sidebars_widgets[$sb_id] = $original_widgets[$replacement];

					/**
					 * When custom sidebars use some wrapper code (before_title,
					 * after_title, ...) then we need to strip-slashes for this
					 * wrapper code to work properly
					 */
					$sidebar_for_replacing = $wp_registered_sidebars[$replacement];
					if ( $this->has_wrapper_code( $sidebar_for_replacing ) ) {
						$sidebar_for_replacing = $this->clean_wrapper_code( $sidebar_for_replacing );
						$wp_registered_sidebars[$sb_id] = $sidebar_for_replacing;
					}
				}
				$wp_registered_sidebars[$sb_id]['class'] = $replacement;
			}  // endif: is_valid_replacement

		} // endforeach
	}

	/**
	 * THIS IS THE ACTUAL LOGIC OF THE PLUGIN
	 *
	 * Here we find out if some sidebars should be replaced, and if it is
	 * replaced we determine which custom sidebar to use.
	 *
	 * @param   array $options Plugin options with the replacement rules.
	 * @return  array List of the replaced sidebars.
	 */
	public function determine_replacements( $options ) {
		global $post,
			$sidebar_category;

		$sidebars = self::get_options( 'modifiable' );
		$replacements_todo = sizeof( $sidebars );
		$replacements = array();
		$branch = 0; // Debugging information

		foreach ( $sidebars as $sb ) {
			$replacements[ $sb ] = false;
		}

		// 1 |== Single posts/pages --------------------------------------------
		if ( is_single() ) {
			$branch = 1;

			$post_type = get_post_type();
			if ( ! self::supported_post_type( $post_type ) ) {
				return $options;
			}

			// 1.1 Check if replacements are defined in the post metadata.
			$reps = self::get_post_meta( $this->original_post_id );
			foreach ( $sidebars as $sb_id ) {
				if ( is_array( $reps ) && ! empty( $reps[$sb_id] ) ) {
					$replacements[$sb_id] = array(
						$reps[$sb_id],
						'particular',
						-1,
					);
					$replacements_todo -= 1;
				}
			}

			// 1.2 Try to use the parents metadata.
			if ( $post->post_parent != 0 && $replacements_todo > 0 ) {
				$reps = self::get_post_meta( $post->post_parent );
				foreach ( $sidebars as $sb_id ) {
					if ( $replacements[$sb_id] ) { continue; }
					if (
						is_array( $reps )
						&& ! empty( $reps[$sb_id] )
					) {
						$replacements[$sb_id] = array(
							$reps[$sb_id],
							'particular',
							-1,
						);
						$replacements_todo -= 1;
					}
				}
			}

			// 1.3 If no metadata set then use the category settings.
			if ( $replacements_todo > 0 ) {
				$categories = self::get_sorted_categories();
				$ind = sizeof( $categories ) -1;
				while ( $replacements_todo > 0 && $ind >= 0 ) {
					$cat_id = $categories[$ind]->cat_ID;
					foreach ( $sidebars as $sb_id ) {
						if ( $replacements[$sb_id] ) { continue; }
						if ( ! empty( $options['category_single'][$cat_id][$sb_id] ) ) {
							$replacements[$sb_id] = array(
								$options['category_single'][$cat_id][$sb_id],
								'category_single',
								$sidebar_category,
							);
							$replacements_todo -= 1;
						}
					}
					$ind -= 1;
				}
			}

			// 1.4 Look for post-type level replacements.
			if ( $replacements_todo > 0 ) {
				foreach ( $sidebars as $sb_id ) {
					if ( $replacements[$sb_id] ) { continue; }
					if (
						isset( $options['post_type_single'][$post_type] )
						&& ! empty( $options['post_type_single'][$post_type][$sb_id] )
					) {
						$replacements[$sb_id] = array(
							$options['post_type_single'][$post_type][$sb_id],
							'post_type_single',
							$post_type,
						);
						$replacements_todo -= 1;
					}
				}
			}
		} else

		// 2 |== Category archive ----------------------------------------------
		if ( is_category() ) {
			$branch = 2;

			// 2.1 Start at current category and travel up all parents
			$category_object = get_queried_object();
			$current_category = $category_object->term_id;
			while ( $current_category != 0 && $replacements_todo > 0 ) {
				foreach ( $sidebars as $sb_id ) {
					if ( $replacements[$sb_id] ) { continue; }
					if ( ! empty( $options['category_archive'][$current_category][$sb_id] ) ) {
						$replacements[$sb_id] = array(
							$options['category_archive'][$current_category][$sb_id],
							'category_archive',
							$current_category,
						);
						$replacements_todo -= 1;
					}
				}
				$current_category = $category_object->category_parent;
				if ( $current_category != 0 ) {
					$category_object = get_category( $current_category );
				}
			}
		} else

		// 3 |== Search --------------------------------------------------------
		// Must be before the post-type archive section; otherwise a search with
		// no results is recognized as post-type archive...
		if ( is_search() ) {
			$branch = 3;

			foreach ( $sidebars as $sb_id ) {
				if ( ! empty( $options['search'][$sb_id] ) ) {
					$replacements[$sb_id] = array(
						$options['search'][$sb_id],
						'search',
						-1,
					);
				}
			}
		} else

		// 4 |== Post-Tpe Archive ----------------------------------------------
		if ( ! is_category() && ! is_singular() && get_post_type() != 'post' ) {
			$branch = 4;

			$post_type = get_post_type();
			if ( ! self::supported_post_type( $post_type ) ) {
				return $options;
			}

			foreach ( $sidebars as $sb_id ) {
				if (
					isset( $options['post_type_archive'][$post_type] )
					&& ! empty( $options['post_type_archive'][$post_type][$sb_id] )
				) {
					$replacements[$sb_id] = array(
						$options['post_type_archive'][$post_type][$sb_id],
						'post_type_archive',
						$post_type,
					);
					$replacements_todo -= 1;
				}
			}
		} else

		// 5 |== Page ----------------------------------------------------------
		if ( is_page() ) {
			$branch = 5;

			$post_type = get_post_type();
			if ( ! self::supported_post_type( $post_type ) ) {
				return $options;
			}

			// 5.1 Check if replacements are defined in the post metadata.
			$reps = self::get_post_meta( $this->original_post_id );
			foreach ( $sidebars as $sb_id ) {
				if ( is_array( $reps ) && ! empty( $reps[$sb_id] ) ) {
					$replacements[$sb_id] = array(
						$reps[$sb_id],
						'particular',
						-1,
					);
					$replacements_todo -= 1;
				}
			}

			// 5.2 Try to use the parents metadata.
			if ( $post->post_parent != 0 && $replacements_todo > 0 ) {
				$reps = self::get_post_meta( $post->post_parent );
				foreach ( $sidebars as $sb_id ) {
					if ( $replacements[$sb_id] ) { continue; }
					if ( is_array( $reps )
						&& ! empty( $reps[$sb_id] )
					) {
						$replacements[$sb_id] = array(
							$reps[$sb_id],
							'particular',
							-1,
						);
						$replacements_todo -= 1;
					}
				}
			}

			// 5.3 Look for post-type level replacements.
			if ( $replacements_todo > 0 ) {
				foreach ( $sidebars as $sb_id ) {
					if ( $replacements[$sb_id] ) { continue; }
					if ( isset( $options['post_type_single'][$post_type] )
						&& ! empty( $options['post_type_single'][$post_type][$sb_id] )
					) {
						$replacements[$sb_id] = array(
							$options['post_type_single'][$post_type][$sb_id],
							'post_type_single',
							$post_type,
						);
						$replacements_todo -= 1;
					}
				}
			}
		} else

		// 6 |== Front Page ----------------------------------------------------
		if ( is_home() ) {
			$branch = 6;

			foreach ( $sidebars as $sb_id ) {
				if ( ! empty( $options['blog'][$sb_id] ) ) {
					$replacements[$sb_id] = array(
						$options['blog'][$sb_id],
						'blog',
						-1,
					);
				}
			}
		} else

		// 7 |== Tag archive ---------------------------------------------------
		if ( is_tag() ) {
			$branch = 7;

			foreach ( $sidebars as $sb_id ) {
				if ( ! empty( $options['tags'][$sb_id] ) ) {
					$replacements[$sb_id] = array(
						$options['tags'][$sb_id],
						'tags',
						-1,
					);
				}
			}
		} else

		// 8 |== Author archive ------------------------------------------------
		if ( is_author() ) {
			$branch = 8;

			foreach ( $sidebars as $sb_id ) {
				if ( ! empty( $options['authors'][$sb_id] ) ) {
					$replacements[$sb_id] = array(
						$options['authors'][$sb_id],
						'authors',
						-1,
					);
				}
			}
		} else

		// 9 |== Date archive --------------------------------------------------
		if ( is_date() ) {
			$branch = 9;

			foreach ( $sidebars as $sb_id ) {
				if ( ! empty( $options['date'][$sb_id] ) ) {
					$replacements[$sb_id] = array(
						$options['date'][$sb_id],
						'date',
						-1,
					);
				}
			}
		}

		/**
		 * Filter the replaced sidebars before they are processed by the plugin.
		 *
		 * @since  2.0
		 *
		 * @param  array $replacements List of the final/replaced sidebars.
		 */
		$replacements = apply_filters( 'cs_replace_sidebars', $replacements );

		return $replacements;
	}



	/**
	 * Makes sure that the replacement sidebar exists.
	 * If the custom sidebar does not exist then the WordPress/Post options are
	 * updated to remove the invalid option.
	 *
	 * @since  1.0.0
	 * @param  string $sb_id The original sidebar (the one that is replaced).
	 * @param  string $replacement ID of the custom sidebar that should be used.
	 * @param  string $method Info where the replacement setting is saved.
	 * @param  int|string $extra_index Depends on $method - can be either one:
	 *                empty/post-type/category-ID
	 * @return bool
	 */
	public function is_valid_replacement( $sb_id, $replacement, $method, $extra_index ) {
		global $wp_registered_sidebars;
		$options = self::get_options();

		if ( isset( $wp_registered_sidebars[ $replacement ] ) ) {
			// Everything okay, we can use the replacement
			return true;
		}

		/*
		 * The replacement sidebar was not registered. Something's wrong, so we
		 * update the options and not try to replace this sidebar again.
		 */
		if ( $method == 'particular' ) {
			// Invalid replacement was found in post-meta data.
			$sidebars = self::get_post_meta( $this->original_post_id );
			if ( $sidebars && isset( $sidebars[$sb_id] ) ) {
				unset( $sidebars[$sb_id] );
				self::set_post_meta( $this->original_post_id, $sidebars );
			}
		} else {
			// Invalid replacement is defined in wordpress options table.
			if ( isset( $options[$method] ) ) {
				if (
					$extra_index != -1 &&
					isset( $options[$method][$extra_index] ) &&
					isset( $options[$method][$extra_index][$sb_id] )
				) {
					unset( $options[$method][$extra_index][$sb_id] );
					self::set_options( $options );
				}

				if (
					$extra_index == 1 &&
					isset( $options[$method] ) &&
					isset( $options[$method][$sb_id] )
				) {
					unset( $options[$method][$sb_id] );
					self::set_options( $options );
				}
			}
		}

		return false;
	}

	/**
	 * Returns an empty dummy-widget. This dummy widget is used when a custom
	 * sidebar has no widgets.
	 *
	 * @since  1.0.0
	 */
	public function get_empty_widget() {
		$widget = new CustomSidebarsEmptyPlugin();
		return array(
			'name'        => 'CS Empty Widget',
			'id'          => 'csemptywidget',
			'callback'    => array( $widget, 'display_callback' ),
			'params'      => array( array( 'number' => 2 ) ),
			'classname'   => 'CustomSidebarsEmptyPlugin',
			'description' => 'CS dummy widget',
		);
	}

	/**
	 * Checks if the specified sidebar uses custom wrapper code.
	 *
	 * @since  1.2
	 * @return bool
	 */
	public function has_wrapper_code( $sidebar ) {
		return (
			strlen( trim( $sidebar['before_widget'] ) )
			OR strlen( trim( $sidebar['after_widget'] ) )
			OR strlen( trim( $sidebar['before_title'] ) )
			OR strlen( trim( $sidebar['after_title'] ) )
		);
	}

	/**
	 * Clean the slashes of the custom sidebar wrapper code.
	 *
	 * @since  1.2
	 */
	public function clean_wrapper_code( $sidebar ) {
		$sidebar['before_widget'] = stripslashes( $sidebar['before_widget'] );
		$sidebar['after_widget'] = stripslashes( $sidebar['after_widget'] );
		$sidebar['before_title'] = stripslashes( $sidebar['before_title'] );
		$sidebar['after_title'] = stripslashes( $sidebar['after_title'] );
		return $sidebar;
	}

};