<?php

/**
 * Extends the PLL_Model class with methods needed only in Polylang settings pages
 *
 * @since 1.2
 */
class PLL_Admin_Model extends PLL_Model {

	/**
	 * Adds a new language
	 * Creates a default category for this language
	 *
	 * List of arguments that $args must contain:
	 * name           -> language name ( used only for display )
	 * slug           -> language code ( ideally 2-letters ISO 639-1 language code )
	 * locale         -> WordPress locale. If something wrong is used for the locale, the .mo files will not be loaded...
	 * rtl            -> 1 if rtl language, 0 otherwise
	 * term_group     -> language order when displayed
	 *
	 * Optional arguments that $args can contain:
	 * no_default_cat -> if set, no default category will be created for this language
	 * flag           -> country code, see flags.php
	 *
	 * @since 1.2
	 *
	 * @param array $args
	 * @return bool true if success / false if failed
	 */
	public function add_language( $args ) {
		if ( ! $this->validate_lang( $args ) ) {
			return false;
		}

		// First the language taxonomy
		$description = serialize( array( 'locale' => $args['locale'], 'rtl' => (int) $args['rtl'], 'flag_code' => empty( $args['flag'] ) ? '' : $args['flag'] ) );
		$r = wp_insert_term( $args['name'], 'language', array( 'slug' => $args['slug'], 'description' => $description ) );
		if ( is_wp_error( $r ) ) {
			// Avoid an ugly fatal error if something went wrong ( reported once in the forum )
			add_settings_error( 'general', 'pll_add_language', __( 'Impossible to add the language.', 'polylang' ) );
			return false;
		}
		wp_update_term( (int) $r['term_id'], 'language', array( 'term_group' => (int) $args['term_group'] ) ); // can't set the term group directly in wp_insert_term

		// The term_language taxonomy
		// Don't want shared terms so use a different slug
		wp_insert_term( $args['name'], 'term_language', array( 'slug' => 'pll_' . $args['slug'] ) );

		$this->clean_languages_cache(); // Update the languages list now !

		if ( ! isset( $this->options['default_lang'] ) ) {
			// If this is the first language created, set it as default language
			$this->options['default_lang'] = $args['slug'];
			update_option( 'polylang', $this->options );

			// And assign default language to default category
			$this->term->set_language( (int) get_option( 'default_category' ), (int) $r['term_id'] );
		} elseif ( empty( $args['no_default_cat'] ) ) {
			$this->create_default_category( $args['slug'] );
		}

		// Init a mo_id for this language
		$mo = new PLL_MO();
		$mo->export_to_db( $this->get_language( $args['slug'] ) );

		/**
		 * Fires when a language is added
		 *
		 * @since 1.9
		 *
		 * @param array $args arguments used to create the language
		 */
		do_action( 'pll_add_language', $args );

		$this->clean_languages_cache(); // Again to set add mo_id in the cached languages list
		flush_rewrite_rules(); // Refresh rewrite rules

		add_settings_error( 'general', 'pll_languages_created', __( 'Language added.', 'polylang' ), 'updated' );
		return true;
	}

	/**
	 * Delete a language
	 *
	 * @since 1.2
	 *
	 * @param int $lang_id language term_id
	 */
	public function delete_language( $lang_id ) {
		$lang = $this->get_language( (int) $lang_id );

		if ( empty( $lang ) ) {
			return;
		}

		// Oops ! we are deleting the default language...
		// Need to do this before loosing the information for default category translations
		if ( $this->options['default_lang'] == $lang->slug ) {
			$slugs = $this->get_languages_list( array( 'fields' => 'slug' ) );
			$slugs = array_diff( $slugs, array( $lang->slug ) );

			if ( ! empty( $slugs ) ) {
				$this->update_default_lang( reset( $slugs ) ); // Arbitrary choice...
			} else {
				unset( $this->options['default_lang'] );
			}
		}

		// Delete the translations
		$this->update_translations( $lang->slug );

		// Delete language option in widgets
		foreach ( $GLOBALS['wp_registered_widgets'] as $widget ) {
			if ( ! empty( $widget['callback'][0] ) && ! empty( $widget['params'][0]['number'] ) ) {
				$obj = $widget['callback'][0];
				$number = $widget['params'][0]['number'];
				if ( is_object( $obj ) && method_exists( $obj, 'get_settings' ) && method_exists( $obj, 'save_settings' ) ) {
					$settings = $obj->get_settings();
					if ( isset( $settings[ $number ]['pll_lang'] ) && $settings[ $number ]['pll_lang'] == $lang->slug ) {
						unset( $settings[ $number ]['pll_lang'] );
						$obj->save_settings( $settings );
					}
				}
			}
		}

		// Delete menus locations
		if ( ! empty( $this->options['nav_menus'] ) ) {
			foreach ( $this->options['nav_menus'] as $theme => $locations ) {
				foreach ( $locations as $location => $languages ) {
					unset( $this->options['nav_menus'][ $theme ][ $location ][ $lang->slug ] );
				}
			}
		}

		// Delete users options
		foreach ( get_users( array( 'fields' => 'ID' ) ) as $user_id ) {
			delete_user_meta( $user_id, 'pll_filter_content', $lang->slug );
			delete_user_meta( $user_id, 'description_' . $lang->slug );
		}

		// Delete the string translations
		$post = wpcom_vip_get_page_by_title( 'polylang_mo_' . $lang->term_id, OBJECT, 'polylang_mo' );
		if ( ! empty( $post ) ) {
			wp_delete_post( $post->ID );
		}

		// Delete domain
		unset( $this->options['domains'][ $lang->slug ] );

		// Delete the language itself
		wp_delete_term( $lang->term_id, 'language' );
		wp_delete_term( $lang->tl_term_id, 'term_language' );

		// Update languages list
		$this->clean_languages_cache();

		update_option( 'polylang', $this->options );
		flush_rewrite_rules(); // refresh rewrite rules
		add_settings_error( 'general', 'pll_languages_deleted', __( 'Language deleted.', 'polylang' ), 'updated' );
	}

	/**
	 * Update language properties
	 *
	 * List of arguments that $args must contain:
	 * lang_id    -> term_id of the language to modify
	 * name       -> language name ( used only for display )
	 * slug       -> language code ( ideally 2-letters ISO 639-1 language code
	 * locale     -> WordPress locale. If something wrong is used for the locale, the .mo files will not be loaded...
	 * rtl        -> 1 if rtl language, 0 otherwise
	 * term_group -> language order when displayed
	 *
	 * Optional arguments that $args can contain:
	 * flag       -> country code, see flags.php
	 *
	 * @since 1.2
	 *
	 * @param array $args
	 * @return bool true if success / false if failed
	 */
	public function update_language( $args ) {
		$lang = $this->get_language( (int) $args['lang_id'] );
		if ( ! $this->validate_lang( $args, $lang ) ) {
			return false;
		}

		// Update links to this language in posts and terms in case the slug has been modified
		$slug = $args['slug'];
		$old_slug = $lang->slug;

		if ( $old_slug != $slug ) {
			// Update the language slug in translations
			$this->update_translations( $old_slug, $slug );

			// Update language option in widgets
			foreach ( $GLOBALS['wp_registered_widgets'] as $widget ) {
				if ( ! empty( $widget['callback'][0] ) && ! empty( $widget['params'][0]['number'] ) ) {
					$obj = $widget['callback'][0];
					$number = $widget['params'][0]['number'];
					if ( is_object( $obj ) && method_exists( $obj, 'get_settings' ) && method_exists( $obj, 'save_settings' ) ) {
						$settings = $obj->get_settings();
						if ( isset( $settings[ $number ]['pll_lang'] ) && $settings[ $number ]['pll_lang'] == $old_slug ) {
							$settings[ $number ]['pll_lang'] = $slug;
							$obj->save_settings( $settings );
						}
					}
				}
			}

			// Update menus locations
			if ( ! empty( $this->options['nav_menus'] ) ) {
				foreach ( $this->options['nav_menus'] as $theme => $locations ) {
					foreach ( $locations as $location => $languages ) {
						if ( ! empty( $this->options['nav_menus'][ $theme ][ $location ][ $old_slug ] ) ) {
							$this->options['nav_menus'][ $theme ][ $location ][ $slug ] = $this->options['nav_menus'][ $theme ][ $location ][ $old_slug ];
							unset( $this->options['nav_menus'][ $theme ][ $location ][ $old_slug ] );
						}
					}
				}
			}

			// Update domains
			if ( ! empty( $this->options['domains'][ $old_slug ] ) ) {
				$this->options['domains'][ $slug ] = $this->options['domains'][ $old_slug ];
				unset( $this->options['domains'][ $old_slug ] );
			}

			// Update the default language option if necessary
			if ( $this->options['default_lang'] == $old_slug ) {
				$this->options['default_lang'] = $slug;
			}
		}

		update_option( 'polylang', $this->options );

		// And finally update the language itself
		$description = serialize( array( 'locale' => $args['locale'], 'rtl' => (int) $args['rtl'], 'flag_code' => empty( $args['flag'] ) ? '' : $args['flag'] ) );
		wp_update_term( (int) $lang->term_id, 'language', array( 'slug' => $slug, 'name' => $args['name'], 'description' => $description, 'term_group' => (int) $args['term_group'] ) );
		wp_update_term( (int) $lang->tl_term_id, 'term_language', array( 'slug' => 'pll_' . $slug, 'name' => $args['name'] ) );

		/**
		 * Fires when a language is added
		 *
		 * @since 1.9
		 *
		 * @param array $args arguments used to modify the language
		 */
		do_action( 'pll_update_language', $args );

		$this->clean_languages_cache();
		flush_rewrite_rules(); // Refresh rewrite rules
		add_settings_error( 'general', 'pll_languages_updated', __( 'Language updated.', 'polylang' ), 'updated' );
		return true;
	}

	/**
	 * Validates data entered when creating or updating a language
	 * @see PLL_Admin_Model::add_language
	 *
	 * @since 0.4
	 *
	 * @param array  $args
	 * @param object $lang optional the language currently updated, the language is created if not set
	 * @return bool true if success / false if failed
	 */
	protected function validate_lang( $args, $lang = null ) {
		// Validate locale with the same pattern as WP 4.3. See #28303
		if ( ! preg_match( '#^[a-z]{2,3}(?:_[A-Z]{2})?(?:_[a-z0-9]+)?$#', $args['locale'], $matches ) ) {
			add_settings_error( 'general', 'pll_invalid_locale', __( 'Enter a valid WordPress locale', 'polylang' ) );
		}

		// Validate slug characters
		if ( ! preg_match( '#^[a-z_-]+$#', $args['slug'] ) ) {
			add_settings_error( 'general', 'pll_invalid_slug', __( 'The language code contains invalid characters', 'polylang' ) );
		}

		// Validate slug is unique
		if ( $this->get_language( $args['slug'] ) && ( null === $lang || ( isset( $lang ) && $lang->slug != $args['slug'] ) ) ) {
			add_settings_error( 'general', 'pll_non_unique_slug', __( 'The language code must be unique', 'polylang' ) );
		}

		// Validate name
		// No need to sanitize it as wp_insert_term will do it for us
		if ( empty( $args['name'] ) ) {
			add_settings_error( 'general', 'pll_invalid_name', __( 'The language must have a name', 'polylang' ) );
		}

		// Validate flag
		if ( ! empty( $args['flag'] ) && ! file_exists( POLYLANG_DIR . '/flags/' . $args['flag'] . '.png' ) ) {
			add_settings_error( 'general', 'pll_invalid_flag', __( 'The flag does not exist', 'polylang' ) );
		}

		return get_settings_errors() ? false : true;
	}

	/**
	 * Used to set the language of posts or terms in mass
	 *
	 * @since 1.2
	 *
	 * @param string        $type either 'post' or 'term'
	 * @param array         $ids  array of post ids or term ids
	 * @param object|string $lang object or slug
	 */
	public function set_language_in_mass( $type, $ids, $lang ) {
		global $wpdb;

		$ids = array_map( 'intval', $ids );
		$lang = $this->get_language( $lang );
		$tt_id = 'term' === $type ? $lang->tl_term_taxonomy_id : $lang->term_taxonomy_id;

		foreach ( $ids as $id ) {
			$values[] = $wpdb->prepare( '( %d, %d )', $id, $tt_id );
		}

		if ( ! empty( $values ) ) {
			$values = array_unique( $values );
			$wpdb->query( "INSERT INTO $wpdb->term_relationships ( object_id, term_taxonomy_id ) VALUES " . implode( ',', $values ) );
			$lang->update_count(); // Updating term count is mandatory ( thanks to AndyDeGroo )
		}

		if ( 'term' === $type ) {
			clean_term_cache( $ids, 'term_language' );

			foreach ( $ids as $id ) {
				$translations[] = array( $lang->slug => $id );
			}

			if ( ! empty( $translations ) ) {
				$this->set_translation_in_mass( 'term', $translations );
			}
		} else {
			clean_term_cache( $ids, 'language' );
		}
	}

	/**
	 * Used to create a translations groups in mass
	 *
	 * @since 1.6.3
	 *
	 * @param string $type         either 'post' or 'term'
	 * @param array  $translations array of translations arrays
	 */
	public function set_translation_in_mass( $type, $translations ) {
		global $wpdb;

		$taxonomy = $type . '_translations';

		foreach ( $translations as $t ) {
			$term = uniqid( 'pll_' ); // the term name
			$terms[] = $wpdb->prepare( '( %s, %s )', $term, $term );
			$slugs[] = $wpdb->prepare( '%s', $term );
			$description[ $term ] = serialize( $t );
			$count[ $term ] = count( $t );
		}

		// Insert terms
		if ( ! empty( $terms ) ) {
			$terms = array_unique( $terms );
			$wpdb->query( "INSERT INTO $wpdb->terms ( slug, name ) VALUES " . implode( ',', $terms ) );
		}

		// Get all terms with their term_id
		$terms = $wpdb->get_results( "SELECT term_id, slug FROM $wpdb->terms WHERE slug IN ( " . implode( ',', $slugs ) . ' )' );

		// Prepare terms taxonomy relationship
		foreach ( $terms as $term ) {
			$term_ids[] = $term->term_id;
			$tts[] = $wpdb->prepare( '( %d, %s, %s, %d )', $term->term_id, $taxonomy, $description[ $term->slug ], $count[ $term->slug ] );
		}

		// Insert term_taxonomy
		if ( ! empty( $tts ) ) {
			$tts = array_unique( $tts );
			$wpdb->query( "INSERT INTO $wpdb->term_taxonomy ( term_id, taxonomy, description, count ) VALUES " . implode( ',', $tts ) );
		}

		// Get all terms with term_taxonomy_id
		$terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );

		// Prepare objects relationships
		foreach ( $terms as $term ) {
			$t = unserialize( $term->description );
			if ( in_array( $t, $translations ) ) {
				foreach ( $t as $object_id ) {
					if ( ! empty( $object_id ) ) {
						$trs[] = $wpdb->prepare( '( %d, %d )', $object_id, $term->term_taxonomy_id );
					}
				}
			}
		}

		// Insert term_relationships
		if ( ! empty( $trs ) ) {
			$wpdb->query( "INSERT INTO $wpdb->term_relationships ( object_id, term_taxonomy_id ) VALUES " . implode( ',', $trs ) );
			$trs = array_unique( $trs );
		}

		clean_term_cache( $term_ids, $taxonomy );
	}

	/**
	 * Returns untranslated posts and terms ids ( used in settings )
	 *
	 * @since 0.9
	 * @since 2.2.6 Add the $limit argument
	 *
	 * @param in $limit Max number of posts or terms to return. Defaults to -1 (no limit).
	 * @return array Array made of an array of post ids and an array of term ids
	 */
	public function get_objects_with_no_lang( $limit = -1 ) {
		global $wpdb;

		/**
		 * Filters the max number of posts or terms to return when searching objects with no language
		 * This filter can be used to decrease the memory usage in case the number of objects
		 * without language is too big. Using a negative value is equivalent to have no limit.
		 *
		 * @since 2.2.6
		 *
		 * @param int $limit Max number of posts or terms to retrieve from the database
		 */
		$limit = (int) apply_filters( 'get_objects_with_no_lang_limit', $limit );

		$posts = get_posts( array(
			'numberposts' => $limit,
			'nopaging'    => $limit <= 0,
			'post_type'   => $this->get_translated_post_types(),
			'post_status' => 'any',
			'fields'      => 'ids',
			'tax_query'   => array(
				array(
					'taxonomy' => 'language',
					'terms'    => $this->get_languages_list( array( 'fields' => 'term_id' ) ),
					'operator' => 'NOT IN',
				),
			),
		) );

		$terms = $wpdb->get_col( sprintf( "
			SELECT {$wpdb->term_taxonomy}.term_id FROM {$wpdb->term_taxonomy}
			WHERE taxonomy IN ('%s')
			AND {$wpdb->term_taxonomy}.term_id NOT IN (
				SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (%s)
			)
			%s",
			implode( "','", array_map( 'esc_sql', $this->get_translated_taxonomies() ) ),
			implode( ',', array_map( 'intval', $this->get_languages_list( array( 'fields' => 'tl_term_taxonomy_id' ) ) ) ),
			$limit > 0 ? "LIMIT {$limit}" : ''
		) );

		/**
		 * Filter the list of untranslated posts ids and terms ids
		 *
		 * @since 0.9
		 *
		 * @param bool|array $objects false if no ids found, list of post and/or term ids otherwise
		 */
		return apply_filters( 'pll_get_objects_with_no_lang', empty( $posts ) && empty( $terms ) ? false : array( 'posts' => $posts, 'terms' => $terms ) );
	}

	/**
	 * Used to delete translations or update the translations when a language slug has been modified in settings
	 *
	 * @since 0.5
	 *
	 * @param string $old_slug the old language slug
	 * @param string $new_slug optional, the new language slug, if not set it means the correspondent has been deleted
	 */
	public function update_translations( $old_slug, $new_slug = '' ) {
		global $wpdb;

		$terms = get_terms( array( 'post_translations', 'term_translations' ) );

		foreach ( $terms as $term ) {
			$term_ids[ $term->taxonomy ][] = $term->term_id;
			$tr = unserialize( $term->description );
			if ( ! empty( $tr[ $old_slug ] ) ) {
				if ( $new_slug ) {
					$tr[ $new_slug ] = $tr[ $old_slug ]; // Suppress this for delete
				} else {
					$dr['id'][] = (int) $tr[ $old_slug ];
					$dr['tt'][] = (int) $term->term_taxonomy_id;
				}
				unset( $tr[ $old_slug ] );

				if ( empty( $tr ) || 1 == count( $tr ) ) {
					$dt['t'][] = (int) $term->term_id;
					$dt['tt'][] = (int) $term->term_taxonomy_id;
				} else {
					$ut['case'][] = $wpdb->prepare( 'WHEN %d THEN %s', $term->term_id, serialize( $tr ) );
					$ut['in'][] = (int) $term->term_id;
				}
			}
		}

		// Delete relationships
		if ( ! empty( $dr ) ) {
			$wpdb->query( "
				DELETE FROM $wpdb->term_relationships
				WHERE object_id IN ( " . implode( ',', $dr['id'] ) . ' )
				AND term_taxonomy_id IN ( ' . implode( ',', $dr['tt'] ) . ' )
			' );
		}

		// Delete terms
		if ( ! empty( $dt ) ) {
			$wpdb->query( "DELETE FROM $wpdb->terms WHERE term_id IN ( " . implode( ',', $dt['t'] ) . ' )' );
			$wpdb->query( "DELETE FROM $wpdb->term_taxonomy WHERE term_taxonomy_id IN ( " . implode( ',', $dt['tt'] ) . ' )' );
		}

		// Update terms
		if ( ! empty( $ut ) ) {
			$wpdb->query( "
				UPDATE $wpdb->term_taxonomy
				SET description = ( CASE term_id " . implode( ' ', $ut['case'] ) . ' END )
				WHERE term_id IN ( ' . implode( ',', $ut['in'] ) . ' )
			' );
		}

		if ( ! empty( $term_ids ) ) {
			foreach ( $term_ids as $taxonomy => $ids ) {
				clean_term_cache( $ids, $taxonomy );
			}
		}
	}

	/**
	 * Updates the default language
	 * taking care to update the default category & the nav menu locations
	 *
	 * @since 1.8
	 *
	 * @param string $slug new language slug
	 */
	public function update_default_lang( $slug ) {
		// The nav menus stored in theme locations should be in the default language
		$theme = get_stylesheet();
		if ( ! empty( $this->options['nav_menus'][ $theme ] ) ) {
			foreach ( $this->options['nav_menus'][ $theme ] as $key => $loc ) {
				$menus[ $key ] = empty( $loc[ $slug ] ) ? 0 : $loc[ $slug ];
			}
			set_theme_mod( 'nav_menu_locations', $menus );
		}

		// The default category should be in the default language
		$default_cats = $this->term->get_translations( get_option( 'default_category' ) );
		if ( isset( $default_cats[ $slug ] ) ) {
			update_option( 'default_category', $default_cats[ $slug ] );
		}

		// Update options
		$this->options['default_lang'] = $slug;
		update_option( 'polylang', $this->options );

		$this->clean_languages_cache();
		flush_rewrite_rules();
	}
}
