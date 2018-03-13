<?php
/**
 * Option API
 *
 * @package WordPress
 * @subpackage Option
 */

/**
 * Retrieves an option value based on an option name.
 *
 * If the option does not exist or does not have a value, then the return value
 * will be false. This is useful to check whether you need to install an option
 * and is commonly used during installation of plugin options and to test
 * whether upgrading is required.
 *
 * If the option was serialized then it will be unserialized when it is returned.
 *
 * Any scalar values will be returned as strings. You may coerce the return type of
 * a given option by registering an {@see 'option_$option'} filter callback.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $option  Name of option to retrieve. Expected to not be SQL-escaped.
 * @param mixed  $default Optional. Default value to return if the option does not exist.
 * @return mixed Value set for the option.
 */
function get_option( $option, $default = false ) {
	global $wpdb;

	$option = trim( $option );
	if ( empty( $option ) ) {
		return false;
	}

	/**
	 * Filters the value of an existing option before it is retrieved.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * Passing a truthy value to the filter will short-circuit retrieving
	 * the option value, returning the passed value instead.
	 *
	 * @since 1.5.0
	 * @since 4.4.0 The `$option` parameter was added.
	 * @since 4.9.0 The `$default` parameter was added.
	 *
	 * @param bool|mixed $pre_option The value to return instead of the option value. This differs from
	 *                               `$default`, which is used as the fallback value in the event the option
	 *                               doesn't exist elsewhere in get_option(). Default false (to skip past the
	 *                               short-circuit).
	 * @param string     $option     Option name.
	 * @param mixed      $default    The fallback value to return if the option does not exist.
	 *                               Default is false.
	 */
	$pre = apply_filters( "pre_option_{$option}", false, $option, $default );

	if ( false !== $pre ) {
		return $pre;
	}

	if ( defined( 'WP_SETUP_CONFIG' ) ) {
		return false;
	}

	// Distinguish between `false` as a default, and not passing one.
	$passed_default = func_num_args() > 1;

	if ( ! wp_installing() ) {
		// prevent non-existent options from triggering multiple queries
		$notoptions = wp_cache_get( 'notoptions', 'options' );
		if ( isset( $notoptions[ $option ] ) ) {
			/**
			 * Filters the default value for an option.
			 *
			 * The dynamic portion of the hook name, `$option`, refers to the option name.
			 *
			 * @since 3.4.0
			 * @since 4.4.0 The `$option` parameter was added.
			 * @since 4.7.0 The `$passed_default` parameter was added to distinguish between a `false` value and the default parameter value.
			 *
			 * @param mixed  $default The default value to return if the option does not exist
			 *                        in the database.
			 * @param string $option  Option name.
			 * @param bool   $passed_default Was `get_option()` passed a default value?
			 */
			return apply_filters( "default_option_{$option}", $default, $option, $passed_default );
		}

		$alloptions = wp_load_alloptions();

		if ( isset( $alloptions[ $option ] ) ) {
			$value = $alloptions[ $option ];
		} else {
			$value = wp_cache_get( $option, 'options' );

			if ( false === $value ) {
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option ) );

				// Has to be get_row instead of get_var because of funkiness with 0, false, null values
				if ( is_object( $row ) ) {
					$value = $row->option_value;
					wp_cache_add( $option, $value, 'options' );
				} else { // option does not exist, so we must cache its non-existence
					if ( ! is_array( $notoptions ) ) {
						$notoptions = array();
					}
					$notoptions[ $option ] = true;
					wp_cache_set( 'notoptions', $notoptions, 'options' );

					/** This filter is documented in wp-includes/option.php */
					return apply_filters( "default_option_{$option}", $default, $option, $passed_default );
				}
			}
		}
	} else {
		$suppress = $wpdb->suppress_errors();
		$row      = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option ) );
		$wpdb->suppress_errors( $suppress );
		if ( is_object( $row ) ) {
			$value = $row->option_value;
		} else {
			/** This filter is documented in wp-includes/option.php */
			return apply_filters( "default_option_{$option}", $default, $option, $passed_default );
		}
	}

	// If home is not set use siteurl.
	if ( 'home' == $option && '' == $value ) {
		return get_option( 'siteurl' );
	}

	if ( in_array( $option, array( 'siteurl', 'home', 'category_base', 'tag_base' ) ) ) {
		$value = untrailingslashit( $value );
	}

	/**
	 * Filters the value of an existing option.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * @since 1.5.0 As 'option_' . $setting
	 * @since 3.0.0
	 * @since 4.4.0 The `$option` parameter was added.
	 *
	 * @param mixed  $value  Value of the option. If stored serialized, it will be
	 *                       unserialized prior to being returned.
	 * @param string $option Option name.
	 */
	return apply_filters( "option_{$option}", maybe_unserialize( $value ), $option );
}

/**
 * Protect WordPress special option from being modified.
 *
 * Will die if $option is in protected list. Protected options are 'alloptions'
 * and 'notoptions' options.
 *
 * @since 2.2.0
 *
 * @param string $option Option name.
 */
function wp_protect_special_option( $option ) {
	if ( 'alloptions' === $option || 'notoptions' === $option ) {
		wp_die( sprintf( __( '%s is a protected WP option and may not be modified' ), esc_html( $option ) ) );
	}
}

/**
 * Print option value after sanitizing for forms.
 *
 * @since 1.5.0
 *
 * @param string $option Option name.
 */
function form_option( $option ) {
	echo esc_attr( get_option( $option ) );
}

/**
 * Loads and caches all autoloaded options, if available or all options.
 *
 * @since 2.2.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return array List of all options.
 */
function wp_load_alloptions() {
	global $wpdb;

	if ( ! wp_installing() || ! is_multisite() ) {
		$alloptions = wp_cache_get( 'alloptions', 'options' );
	} else {
		$alloptions = false;
	}

	if ( ! $alloptions ) {
		$suppress = $wpdb->suppress_errors();
		if ( ! $alloptions_db = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE autoload = 'yes'" ) ) {
			$alloptions_db = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options" );
		}
		$wpdb->suppress_errors( $suppress );

		$alloptions = array();
		foreach ( (array) $alloptions_db as $o ) {
			$alloptions[ $o->option_name ] = $o->option_value;
		}

		if ( ! wp_installing() || ! is_multisite() ) {
			/**
			 * Filters all options before caching them.
			 *
			 * @since 4.9.0
			 *
			 * @param array $alloptions Array with all options.
			 */
			$alloptions = apply_filters( 'pre_cache_alloptions', $alloptions );
			wp_cache_add( 'alloptions', $alloptions, 'options' );
		}
	}

	/**
	 * Filters all options after retrieving them.
	 *
	 * @since 4.9.0
	 *
	 * @param array $alloptions Array with all options.
	 */
	return apply_filters( 'alloptions', $alloptions );
}

/**
 * Loads and caches certain often requested site options if is_multisite() and a persistent cache is not being used.
 *
 * @since 3.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $network_id Optional site ID for which to query the options. Defaults to the current site.
 */
function wp_load_core_site_options( $network_id = null ) {
	global $wpdb;

	if ( ! is_multisite() || wp_using_ext_object_cache() || wp_installing() ) {
		return;
	}

	if ( empty( $network_id ) ) {
		$network_id = get_current_network_id();
	}

	$core_options = array( 'site_name', 'siteurl', 'active_sitewide_plugins', '_site_transient_timeout_theme_roots', '_site_transient_theme_roots', 'site_admins', 'can_compress_scripts', 'global_terms_enabled', 'ms_files_rewriting' );

	$core_options_in = "'" . implode( "', '", $core_options ) . "'";
	$options         = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->sitemeta WHERE meta_key IN ($core_options_in) AND site_id = %d", $network_id ) );

	foreach ( $options as $option ) {
		$key                = $option->meta_key;
		$cache_key          = "{$network_id}:$key";
		$option->meta_value = maybe_unserialize( $option->meta_value );

		wp_cache_set( $cache_key, $option->meta_value, 'site-options' );
	}
}

/**
 * Update the value of an option that was already added.
 *
 * You do not need to serialize values. If the value needs to be serialized, then
 * it will be serialized before it is inserted into the database. Remember,
 * resources can not be serialized or added as an option.
 *
 * If the option does not exist, then the option will be added with the option value,
 * with an `$autoload` value of 'yes'.
 *
 * @since 1.0.0
 * @since 4.2.0 The `$autoload` parameter was added.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string      $option   Option name. Expected to not be SQL-escaped.
 * @param mixed       $value    Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
 * @param string|bool $autoload Optional. Whether to load the option when WordPress starts up. For existing options,
 *                              `$autoload` can only be updated using `update_option()` if `$value` is also changed.
 *                              Accepts 'yes'|true to enable or 'no'|false to disable. For non-existent options,
 *                              the default value is 'yes'. Default null.
 * @return bool False if value was not updated and true if value was updated.
 */
function update_option( $option, $value, $autoload = null ) {
	global $wpdb;

	$option = trim( $option );
	if ( empty( $option ) ) {
		return false;
	}

	wp_protect_special_option( $option );

	if ( is_object( $value ) ) {
		$value = clone $value;
	}

	$value     = sanitize_option( $option, $value );
	$old_value = get_option( $option );

	/**
	 * Filters a specific option before its value is (maybe) serialized and updated.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * @since 2.6.0
	 * @since 4.4.0 The `$option` parameter was added.
	 *
	 * @param mixed  $value     The new, unserialized option value.
	 * @param mixed  $old_value The old option value.
	 * @param string $option    Option name.
	 */
	$value = apply_filters( "pre_update_option_{$option}", $value, $old_value, $option );

	/**
	 * Filters an option before its value is (maybe) serialized and updated.
	 *
	 * @since 3.9.0
	 *
	 * @param mixed  $value     The new, unserialized option value.
	 * @param string $option    Name of the option.
	 * @param mixed  $old_value The old option value.
	 */
	$value = apply_filters( 'pre_update_option', $value, $option, $old_value );

	/*
	 * If the new and old values are the same, no need to update.
	 *
	 * Unserialized values will be adequate in most cases. If the unserialized
	 * data differs, the (maybe) serialized data is checked to avoid
	 * unnecessary database calls for otherwise identical object instances.
	 *
	 * See https://core.trac.wordpress.org/ticket/38903
	 */
	if ( $value === $old_value || maybe_serialize( $value ) === maybe_serialize( $old_value ) ) {
		return false;
	}

	/** This filter is documented in wp-includes/option.php */
	if ( apply_filters( "default_option_{$option}", false, $option, false ) === $old_value ) {
		// Default setting for new options is 'yes'.
		if ( null === $autoload ) {
			$autoload = 'yes';
		}

		return add_option( $option, $value, '', $autoload );
	}

	$serialized_value = maybe_serialize( $value );

	/**
	 * Fires immediately before an option value is updated.
	 *
	 * @since 2.9.0
	 *
	 * @param string $option    Name of the option to update.
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 */
	do_action( 'update_option', $option, $old_value, $value );

	$update_args = array(
		'option_value' => $serialized_value,
	);

	if ( null !== $autoload ) {
		$update_args['autoload'] = ( 'no' === $autoload || false === $autoload ) ? 'no' : 'yes';
	}

	$result = $wpdb->update( $wpdb->options, $update_args, array( 'option_name' => $option ) );
	if ( ! $result ) {
		return false;
	}

	$notoptions = wp_cache_get( 'notoptions', 'options' );
	if ( is_array( $notoptions ) && isset( $notoptions[ $option ] ) ) {
		unset( $notoptions[ $option ] );
		wp_cache_set( 'notoptions', $notoptions, 'options' );
	}

	if ( ! wp_installing() ) {
		$alloptions = wp_load_alloptions();
		if ( isset( $alloptions[ $option ] ) ) {
			$alloptions[ $option ] = $serialized_value;
			wp_cache_set( 'alloptions', $alloptions, 'options' );
		} else {
			wp_cache_set( $option, $serialized_value, 'options' );
		}
	}

	/**
	 * Fires after the value of a specific option has been successfully updated.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * @since 2.0.1
	 * @since 4.4.0 The `$option` parameter was added.
	 *
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 * @param string $option    Option name.
	 */
	do_action( "update_option_{$option}", $old_value, $value, $option );

	/**
	 * Fires after the value of an option has been successfully updated.
	 *
	 * @since 2.9.0
	 *
	 * @param string $option    Name of the updated option.
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 */
	do_action( 'updated_option', $option, $old_value, $value );
	return true;
}

/**
 * Add a new option.
 *
 * You do not need to serialize values. If the value needs to be serialized, then
 * it will be serialized before it is inserted into the database. Remember,
 * resources can not be serialized or added as an option.
 *
 * You can create options without values and then update the values later.
 * Existing options will not be updated and checks are performed to ensure that you
 * aren't adding a protected WordPress option. Care should be taken to not name
 * options the same as the ones which are protected.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string         $option      Name of option to add. Expected to not be SQL-escaped.
 * @param mixed          $value       Optional. Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
 * @param string         $deprecated  Optional. Description. Not used anymore.
 * @param string|bool    $autoload    Optional. Whether to load the option when WordPress starts up.
 *                                    Default is enabled. Accepts 'no' to disable for legacy reasons.
 * @return bool False if option was not added and true if option was added.
 */
function add_option( $option, $value = '', $deprecated = '', $autoload = 'yes' ) {
	global $wpdb;

	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.3.0' );
	}

	$option = trim( $option );
	if ( empty( $option ) ) {
		return false;
	}

	wp_protect_special_option( $option );

	if ( is_object( $value ) ) {
		$value = clone $value;
	}

	$value = sanitize_option( $option, $value );

	// Make sure the option doesn't already exist. We can check the 'notoptions' cache before we ask for a db query
	$notoptions = wp_cache_get( 'notoptions', 'options' );
	if ( ! is_array( $notoptions ) || ! isset( $notoptions[ $option ] ) ) {
		/** This filter is documented in wp-includes/option.php */
		if ( apply_filters( "default_option_{$option}", false, $option, false ) !== get_option( $option ) ) {
			return false;
		}
	}

	$serialized_value = maybe_serialize( $value );
	$autoload         = ( 'no' === $autoload || false === $autoload ) ? 'no' : 'yes';

	/**
	 * Fires before an option is added.
	 *
	 * @since 2.9.0
	 *
	 * @param string $option Name of the option to add.
	 * @param mixed  $value  Value of the option.
	 */
	do_action( 'add_option', $option, $value );

	$result = $wpdb->query( $wpdb->prepare( "INSERT INTO `$wpdb->options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)", $option, $serialized_value, $autoload ) );
	if ( ! $result ) {
		return false;
	}

	if ( ! wp_installing() ) {
		if ( 'yes' == $autoload ) {
			$alloptions            = wp_load_alloptions();
			$alloptions[ $option ] = $serialized_value;
			wp_cache_set( 'alloptions', $alloptions, 'options' );
		} else {
			wp_cache_set( $option, $serialized_value, 'options' );
		}
	}

	// This option exists now
	$notoptions = wp_cache_get( 'notoptions', 'options' ); // yes, again... we need it to be fresh
	if ( is_array( $notoptions ) && isset( $notoptions[ $option ] ) ) {
		unset( $notoptions[ $option ] );
		wp_cache_set( 'notoptions', $notoptions, 'options' );
	}

	/**
	 * Fires after a specific option has been added.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * @since 2.5.0 As "add_option_{$name}"
	 * @since 3.0.0
	 *
	 * @param string $option Name of the option to add.
	 * @param mixed  $value  Value of the option.
	 */
	do_action( "add_option_{$option}", $option, $value );

	/**
	 * Fires after an option has been added.
	 *
	 * @since 2.9.0
	 *
	 * @param string $option Name of the added option.
	 * @param mixed  $value  Value of the option.
	 */
	do_action( 'added_option', $option, $value );
	return true;
}

/**
 * Removes option by name. Prevents removal of protected WordPress options.
 *
 * @since 1.2.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $option Name of option to remove. Expected to not be SQL-escaped.
 * @return bool True, if option is successfully deleted. False on failure.
 */
function delete_option( $option ) {
	global $wpdb;

	$option = trim( $option );
	if ( empty( $option ) ) {
		return false;
	}

	wp_protect_special_option( $option );

	// Get the ID, if no ID then return
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT autoload FROM $wpdb->options WHERE option_name = %s", $option ) );
	if ( is_null( $row ) ) {
		return false;
	}

	/**
	 * Fires immediately before an option is deleted.
	 *
	 * @since 2.9.0
	 *
	 * @param string $option Name of the option to delete.
	 */
	do_action( 'delete_option', $option );

	$result = $wpdb->delete( $wpdb->options, array( 'option_name' => $option ) );
	if ( ! wp_installing() ) {
		if ( 'yes' == $row->autoload ) {
			$alloptions = wp_load_alloptions();
			if ( is_array( $alloptions ) && isset( $alloptions[ $option ] ) ) {
				unset( $alloptions[ $option ] );
				wp_cache_set( 'alloptions', $alloptions, 'options' );
			}
		} else {
			wp_cache_delete( $option, 'options' );
		}
	}
	if ( $result ) {

		/**
		 * Fires after a specific option has been deleted.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 *
		 * @since 3.0.0
		 *
		 * @param string $option Name of the deleted option.
		 */
		do_action( "delete_option_{$option}", $option );

		/**
		 * Fires after an option has been deleted.
		 *
		 * @since 2.9.0
		 *
		 * @param string $option Name of the deleted option.
		 */
		do_action( 'deleted_option', $option );
		return true;
	}
	return false;
}

/**
 * Delete a transient.
 *
 * @since 2.8.0
 *
 * @param string $transient Transient name. Expected to not be SQL-escaped.
 * @return bool true if successful, false otherwise
 */
function delete_transient( $transient ) {

	/**
	 * Fires immediately before a specific transient is deleted.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * @since 3.0.0
	 *
	 * @param string $transient Transient name.
	 */
	do_action( "delete_transient_{$transient}", $transient );

	if ( wp_using_ext_object_cache() ) {
		$result = wp_cache_delete( $transient, 'transient' );
	} else {
		$option_timeout = '_transient_timeout_' . $transient;
		$option         = '_transient_' . $transient;
		$result         = delete_option( $option );
		if ( $result ) {
			delete_option( $option_timeout );
		}
	}

	if ( $result ) {

		/**
		 * Fires after a transient is deleted.
		 *
		 * @since 3.0.0
		 *
		 * @param string $transient Deleted transient name.
		 */
		do_action( 'deleted_transient', $transient );
	}

	return $result;
}

/**
 * Get the value of a transient.
 *
 * If the transient does not exist, does not have a value, or has expired,
 * then the return value will be false.
 *
 * @since 2.8.0
 *
 * @param string $transient Transient name. Expected to not be SQL-escaped.
 * @return mixed Value of transient.
 */
function get_transient( $transient ) {

	/**
	 * Filters the value of an existing transient.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * Passing a truthy value to the filter will effectively short-circuit retrieval
	 * of the transient, returning the passed value instead.
	 *
	 * @since 2.8.0
	 * @since 4.4.0 The `$transient` parameter was added
	 *
	 * @param mixed  $pre_transient The default value to return if the transient does not exist.
	 *                              Any value other than false will short-circuit the retrieval
	 *                              of the transient, and return the returned value.
	 * @param string $transient     Transient name.
	 */
	$pre = apply_filters( "pre_transient_{$transient}", false, $transient );
	if ( false !== $pre ) {
		return $pre;
	}

	if ( wp_using_ext_object_cache() ) {
		$value = wp_cache_get( $transient, 'transient' );
	} else {
		$transient_option = '_transient_' . $transient;
		if ( ! wp_installing() ) {
			// If option is not in alloptions, it is not autoloaded and thus has a timeout
			$alloptions = wp_load_alloptions();
			if ( ! isset( $alloptions[ $transient_option ] ) ) {
				$transient_timeout = '_transient_timeout_' . $transient;
				$timeout           = get_option( $transient_timeout );
				if ( false !== $timeout && $timeout < time() ) {
					delete_option( $transient_option );
					delete_option( $transient_timeout );
					$value = false;
				}
			}
		}

		if ( ! isset( $value ) ) {
			$value = get_option( $transient_option );
		}
	}

	/**
	 * Filters an existing transient's value.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * @since 2.8.0
	 * @since 4.4.0 The `$transient` parameter was added
	 *
	 * @param mixed  $value     Value of transient.
	 * @param string $transient Transient name.
	 */
	return apply_filters( "transient_{$transient}", $value, $transient );
}

/**
 * Set/update the value of a transient.
 *
 * You do not need to serialize values. If the value needs to be serialized, then
 * it will be serialized before it is set.
 *
 * @since 2.8.0
 *
 * @param string $transient  Transient name. Expected to not be SQL-escaped. Must be
 *                           172 characters or fewer in length.
 * @param mixed  $value      Transient value. Must be serializable if non-scalar.
 *                           Expected to not be SQL-escaped.
 * @param int    $expiration Optional. Time until expiration in seconds. Default 0 (no expiration).
 * @return bool False if value was not set and true if value was set.
 */
function set_transient( $transient, $value, $expiration = 0 ) {

	$expiration = (int) $expiration;

	/**
	 * Filters a specific transient before its value is set.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * @since 3.0.0
	 * @since 4.2.0 The `$expiration` parameter was added.
	 * @since 4.4.0 The `$transient` parameter was added.
	 *
	 * @param mixed  $value      New value of transient.
	 * @param int    $expiration Time until expiration in seconds.
	 * @param string $transient  Transient name.
	 */
	$value = apply_filters( "pre_set_transient_{$transient}", $value, $expiration, $transient );

	/**
	 * Filters the expiration for a transient before its value is set.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * @since 4.4.0
	 *
	 * @param int    $expiration Time until expiration in seconds. Use 0 for no expiration.
	 * @param mixed  $value      New value of transient.
	 * @param string $transient  Transient name.
	 */
	$expiration = apply_filters( "expiration_of_transient_{$transient}", $expiration, $value, $transient );

	if ( wp_using_ext_object_cache() ) {
		$result = wp_cache_set( $transient, $value, 'transient', $expiration );
	} else {
		$transient_timeout = '_transient_timeout_' . $transient;
		$transient_option  = '_transient_' . $transient;
		if ( false === get_option( $transient_option ) ) {
			$autoload = 'yes';
			if ( $expiration ) {
				$autoload = 'no';
				add_option( $transient_timeout, time() + $expiration, '', 'no' );
			}
			$result = add_option( $transient_option, $value, '', $autoload );
		} else {
			// If expiration is requested, but the transient has no timeout option,
			// delete, then re-create transient rather than update.
			$update = true;
			if ( $expiration ) {
				if ( false === get_option( $transient_timeout ) ) {
					delete_option( $transient_option );
					add_option( $transient_timeout, time() + $expiration, '', 'no' );
					$result = add_option( $transient_option, $value, '', 'no' );
					$update = false;
				} else {
					update_option( $transient_timeout, time() + $expiration );
				}
			}
			if ( $update ) {
				$result = update_option( $transient_option, $value );
			}
		}
	}

	if ( $result ) {

		/**
		 * Fires after the value for a specific transient has been set.
		 *
		 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
		 *
		 * @since 3.0.0
		 * @since 3.6.0 The `$value` and `$expiration` parameters were added.
		 * @since 4.4.0 The `$transient` parameter was added.
		 *
		 * @param mixed  $value      Transient value.
		 * @param int    $expiration Time until expiration in seconds.
		 * @param string $transient  The name of the transient.
		 */
		do_action( "set_transient_{$transient}", $value, $expiration, $transient );

		/**
		 * Fires after the value for a transient has been set.
		 *
		 * @since 3.0.0
		 * @since 3.6.0 The `$value` and `$expiration` parameters were added.
		 *
		 * @param string $transient  The name of the transient.
		 * @param mixed  $value      Transient value.
		 * @param int    $expiration Time until expiration in seconds.
		 */
		do_action( 'setted_transient', $transient, $value, $expiration );
	}
	return $result;
}

/**
 * Deletes all expired transients.
 *
 * The multi-table delete syntax is used to delete the transient record
 * from table a, and the corresponding transient_timeout record from table b.
 *
 * @since 4.9.0
 *
 * @param bool $force_db Optional. Force cleanup to run against the database even when an external object cache is used.
 */
function delete_expired_transients( $force_db = false ) {
	global $wpdb;

	if ( ! $force_db && wp_using_ext_object_cache() ) {
		return;
	}

	$wpdb->query(
		$wpdb->prepare(
			"DELETE a, b FROM {$wpdb->options} a, {$wpdb->options} b
			WHERE a.option_name LIKE %s
			AND a.option_name NOT LIKE %s
			AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
			AND b.option_value < %d",
			$wpdb->esc_like( '_transient_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_' ) . '%',
			time()
		)
	);

	if ( ! is_multisite() ) {
		// non-Multisite stores site transients in the options table.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE a, b FROM {$wpdb->options} a, {$wpdb->options} b
				WHERE a.option_name LIKE %s
				AND a.option_name NOT LIKE %s
				AND b.option_name = CONCAT( '_site_transient_timeout_', SUBSTRING( a.option_name, 17 ) )
				AND b.option_value < %d",
				$wpdb->esc_like( '_site_transient_' ) . '%',
				$wpdb->esc_like( '_site_transient_timeout_' ) . '%',
				time()
			)
		);
	} elseif ( is_multisite() && is_main_site() && is_main_network() ) {
		// Multisite stores site transients in the sitemeta table.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE a, b FROM {$wpdb->sitemeta} a, {$wpdb->sitemeta} b
				WHERE a.meta_key LIKE %s
				AND a.meta_key NOT LIKE %s
				AND b.meta_key = CONCAT( '_site_transient_timeout_', SUBSTRING( a.meta_key, 17 ) )
				AND b.meta_value < %d",
				$wpdb->esc_like( '_site_transient_' ) . '%',
				$wpdb->esc_like( '_site_transient_timeout_' ) . '%',
				time()
			)
		);
	}
}

/**
 * Saves and restores user interface settings stored in a cookie.
 *
 * Checks if the current user-settings cookie is updated and stores it. When no
 * cookie exists (different browser used), adds the last saved cookie restoring
 * the settings.
 *
 * @since 2.7.0
 */
function wp_user_settings() {

	if ( ! is_admin() || wp_doing_ajax() ) {
		return;
	}

	if ( ! $user_id = get_current_user_id() ) {
		return;
	}

	if ( ! is_user_member_of_blog() ) {
		return;
	}

	$settings = (string) get_user_option( 'user-settings', $user_id );

	if ( isset( $_COOKIE[ 'wp-settings-' . $user_id ] ) ) {
		$cookie = preg_replace( '/[^A-Za-z0-9=&_]/', '', $_COOKIE[ 'wp-settings-' . $user_id ] );

		// No change or both empty
		if ( $cookie == $settings ) {
			return;
		}

		$last_saved = (int) get_user_option( 'user-settings-time', $user_id );
		$current    = isset( $_COOKIE[ 'wp-settings-time-' . $user_id ] ) ? preg_replace( '/[^0-9]/', '', $_COOKIE[ 'wp-settings-time-' . $user_id ] ) : 0;

		// The cookie is newer than the saved value. Update the user_option and leave the cookie as-is
		if ( $current > $last_saved ) {
			update_user_option( $user_id, 'user-settings', $cookie, false );
			update_user_option( $user_id, 'user-settings-time', time() - 5, false );
			return;
		}
	}

	// The cookie is not set in the current browser or the saved value is newer.
	$secure = ( 'https' === parse_url( admin_url(), PHP_URL_SCHEME ) );
	setcookie( 'wp-settings-' . $user_id, $settings, time() + YEAR_IN_SECONDS, SITECOOKIEPATH, null, $secure );
	setcookie( 'wp-settings-time-' . $user_id, time(), time() + YEAR_IN_SECONDS, SITECOOKIEPATH, null, $secure );
	$_COOKIE[ 'wp-settings-' . $user_id ] = $settings;
}

/**
 * Retrieve user interface setting value based on setting name.
 *
 * @since 2.7.0
 *
 * @param string $name    The name of the setting.
 * @param string $default Optional default value to return when $name is not set.
 * @return mixed the last saved user setting or the default value/false if it doesn't exist.
 */
function get_user_setting( $name, $default = false ) {
	$all_user_settings = get_all_user_settings();

	return isset( $all_user_settings[ $name ] ) ? $all_user_settings[ $name ] : $default;
}

/**
 * Add or update user interface setting.
 *
 * Both $name and $value can contain only ASCII letters, numbers and underscores.
 *
 * This function has to be used before any output has started as it calls setcookie().
 *
 * @since 2.8.0
 *
 * @param string $name  The name of the setting.
 * @param string $value The value for the setting.
 * @return bool|null True if set successfully, false if not. Null if the current user can't be established.
 */
function set_user_setting( $name, $value ) {
	if ( headers_sent() ) {
		return false;
	}

	$all_user_settings          = get_all_user_settings();
	$all_user_settings[ $name ] = $value;

	return wp_set_all_user_settings( $all_user_settings );
}

/**
 * Delete user interface settings.
 *
 * Deleting settings would reset them to the defaults.
 *
 * This function has to be used before any output has started as it calls setcookie().
 *
 * @since 2.7.0
 *
 * @param string $names The name or array of names of the setting to be deleted.
 * @return bool|null True if deleted successfully, false if not. Null if the current user can't be established.
 */
function delete_user_setting( $names ) {
	if ( headers_sent() ) {
		return false;
	}

	$all_user_settings = get_all_user_settings();
	$names             = (array) $names;
	$deleted           = false;

	foreach ( $names as $name ) {
		if ( isset( $all_user_settings[ $name ] ) ) {
			unset( $all_user_settings[ $name ] );
			$deleted = true;
		}
	}

	if ( $deleted ) {
		return wp_set_all_user_settings( $all_user_settings );
	}

	return false;
}

/**
 * Retrieve all user interface settings.
 *
 * @since 2.7.0
 *
 * @global array $_updated_user_settings
 *
 * @return array the last saved user settings or empty array.
 */
function get_all_user_settings() {
	global $_updated_user_settings;

	if ( ! $user_id = get_current_user_id() ) {
		return array();
	}

	if ( isset( $_updated_user_settings ) && is_array( $_updated_user_settings ) ) {
		return $_updated_user_settings;
	}

	$user_settings = array();

	if ( isset( $_COOKIE[ 'wp-settings-' . $user_id ] ) ) {
		$cookie = preg_replace( '/[^A-Za-z0-9=&_-]/', '', $_COOKIE[ 'wp-settings-' . $user_id ] );

		if ( strpos( $cookie, '=' ) ) { // '=' cannot be 1st char
			parse_str( $cookie, $user_settings );
		}
	} else {
		$option = get_user_option( 'user-settings', $user_id );

		if ( $option && is_string( $option ) ) {
			parse_str( $option, $user_settings );
		}
	}

	$_updated_user_settings = $user_settings;
	return $user_settings;
}

/**
 * Private. Set all user interface settings.
 *
 * @since 2.8.0
 * @access private
 *
 * @global array $_updated_user_settings
 *
 * @param array $user_settings User settings.
 * @return bool|null False if the current user can't be found, null if the current
 *                   user is not a super admin or a member of the site, otherwise true.
 */
function wp_set_all_user_settings( $user_settings ) {
	global $_updated_user_settings;

	if ( ! $user_id = get_current_user_id() ) {
		return false;
	}

	if ( ! is_user_member_of_blog() ) {
		return;
	}

	$settings = '';
	foreach ( $user_settings as $name => $value ) {
		$_name  = preg_replace( '/[^A-Za-z0-9_-]+/', '', $name );
		$_value = preg_replace( '/[^A-Za-z0-9_-]+/', '', $value );

		if ( ! empty( $_name ) ) {
			$settings .= $_name . '=' . $_value . '&';
		}
	}

	$settings = rtrim( $settings, '&' );
	parse_str( $settings, $_updated_user_settings );

	update_user_option( $user_id, 'user-settings', $settings, false );
	update_user_option( $user_id, 'user-settings-time', time(), false );

	return true;
}

/**
 * Delete the user settings of the current user.
 *
 * @since 2.7.0
 */
function delete_all_user_settings() {
	if ( ! $user_id = get_current_user_id() ) {
		return;
	}

	update_user_option( $user_id, 'user-settings', '', false );
	setcookie( 'wp-settings-' . $user_id, ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH );
}

/**
 * Retrieve an option value for the current network based on name of option.
 *
 * @since 2.8.0
 * @since 4.4.0 The `$use_cache` parameter was deprecated.
 * @since 4.4.0 Modified into wrapper for get_network_option()
 *
 * @see get_network_option()
 *
 * @param string $option     Name of option to retrieve. Expected to not be SQL-escaped.
 * @param mixed  $default    Optional value to return if option doesn't exist. Default false.
 * @param bool   $deprecated Whether to use cache. Multisite only. Always set to true.
 * @return mixed Value set for the option.
 */
function get_site_option( $option, $default = false, $deprecated = true ) {
	return get_network_option( null, $option, $default );
}

/**
 * Add a new option for the current network.
 *
 * Existing options will not be updated. Note that prior to 3.3 this wasn't the case.
 *
 * @since 2.8.0
 * @since 4.4.0 Modified into wrapper for add_network_option()
 *
 * @see add_network_option()
 *
 * @param string $option Name of option to add. Expected to not be SQL-escaped.
 * @param mixed  $value  Option value, can be anything. Expected to not be SQL-escaped.
 * @return bool False if the option was not added. True if the option was added.
 */
function add_site_option( $option, $value ) {
	return add_network_option( null, $option, $value );
}

/**
 * Removes a option by name for the current network.
 *
 * @since 2.8.0
 * @since 4.4.0 Modified into wrapper for delete_network_option()
 *
 * @see delete_network_option()
 *
 * @param string $option Name of option to remove. Expected to not be SQL-escaped.
 * @return bool True, if succeed. False, if failure.
 */
function delete_site_option( $option ) {
	return delete_network_option( null, $option );
}

/**
 * Update the value of an option that was already added for the current network.
 *
 * @since 2.8.0
 * @since 4.4.0 Modified into wrapper for update_network_option()
 *
 * @see update_network_option()
 *
 * @param string $option Name of option. Expected to not be SQL-escaped.
 * @param mixed  $value  Option value. Expected to not be SQL-escaped.
 * @return bool False if value was not updated. True if value was updated.
 */
function update_site_option( $option, $value ) {
	return update_network_option( null, $option, $value );
}

/**
 * Retrieve a network's option value based on the option name.
 *
 * @since 4.4.0
 *
 * @see get_option()
 *
 * @global wpdb $wpdb
 *
 * @param int      $network_id ID of the network. Can be null to default to the current network ID.
 * @param string   $option     Name of option to retrieve. Expected to not be SQL-escaped.
 * @param mixed    $default    Optional. Value to return if the option doesn't exist. Default false.
 * @return mixed Value set for the option.
 */
function get_network_option( $network_id, $option, $default = false ) {
	global $wpdb;

	if ( $network_id && ! is_numeric( $network_id ) ) {
		return false;
	}

	$network_id = (int) $network_id;

	// Fallback to the current network if a network ID is not specified.
	if ( ! $network_id ) {
		$network_id = get_current_network_id();
	}

	/**
	 * Filters an existing network option before it is retrieved.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * Passing a truthy value to the filter will effectively short-circuit retrieval,
	 * returning the passed value instead.
	 *
	 * @since 2.9.0 As 'pre_site_option_' . $key
	 * @since 3.0.0
	 * @since 4.4.0 The `$option` parameter was added.
	 * @since 4.7.0 The `$network_id` parameter was added.
	 * @since 4.9.0 The `$default` parameter was added.
	 *
	 * @param mixed  $pre_option The value to return instead of the option value. This differs from
	 *                           `$default`, which is used as the fallback value in the event the
	 *                           option doesn't exist elsewhere in get_network_option(). Default
	 *                           is false (to skip past the short-circuit).
	 * @param string $option     Option name.
	 * @param int    $network_id ID of the network.
	 * @param mixed  $default    The fallback value to return if the option does not exist.
	 *                           Default is false.
	 */
	$pre = apply_filters( "pre_site_option_{$option}", false, $option, $network_id, $default );

	if ( false !== $pre ) {
		return $pre;
	}

	// prevent non-existent options from triggering multiple queries
	$notoptions_key = "$network_id:notoptions";
	$notoptions     = wp_cache_get( $notoptions_key, 'site-options' );

	if ( is_array( $notoptions ) && isset( $notoptions[ $option ] ) ) {

		/**
		 * Filters a specific default network option.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 *
		 * @since 3.4.0
		 * @since 4.4.0 The `$option` parameter was added.
		 * @since 4.7.0 The `$network_id` parameter was added.
		 *
		 * @param mixed  $default    The value to return if the site option does not exist
		 *                           in the database.
		 * @param string $option     Option name.
		 * @param int    $network_id ID of the network.
		 */
		return apply_filters( "default_site_option_{$option}", $default, $option, $network_id );
	}

	if ( ! is_multisite() ) {
		/** This filter is documented in wp-includes/option.php */
		$default = apply_filters( 'default_site_option_' . $option, $default, $option, $network_id );
		$value   = get_option( $option, $default );
	} else {
		$cache_key = "$network_id:$option";
		$value     = wp_cache_get( $cache_key, 'site-options' );

		if ( ! isset( $value ) || false === $value ) {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT meta_value FROM $wpdb->sitemeta WHERE meta_key = %s AND site_id = %d", $option, $network_id ) );

			// Has to be get_row instead of get_var because of funkiness with 0, false, null values
			if ( is_object( $row ) ) {
				$value = $row->meta_value;
				$value = maybe_unserialize( $value );
				wp_cache_set( $cache_key, $value, 'site-options' );
			} else {
				if ( ! is_array( $notoptions ) ) {
					$notoptions = array();
				}
				$notoptions[ $option ] = true;
				wp_cache_set( $notoptions_key, $notoptions, 'site-options' );

				/** This filter is documented in wp-includes/option.php */
				$value = apply_filters( 'default_site_option_' . $option, $default, $option, $network_id );
			}
		}
	}

	if ( ! is_array( $notoptions ) ) {
		$notoptions = array();
		wp_cache_set( $notoptions_key, $notoptions, 'site-options' );
	}

	/**
	 * Filters the value of an existing network option.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * @since 2.9.0 As 'site_option_' . $key
	 * @since 3.0.0
	 * @since 4.4.0 The `$option` parameter was added.
	 * @since 4.7.0 The `$network_id` parameter was added.
	 *
	 * @param mixed  $value      Value of network option.
	 * @param string $option     Option name.
	 * @param int    $network_id ID of the network.
	 */
	return apply_filters( "site_option_{$option}", $value, $option, $network_id );
}

/**
 * Add a new network option.
 *
 * Existing options will not be updated.
 *
 * @since 4.4.0
 *
 * @see add_option()
 *
 * @global wpdb $wpdb
 *
 * @param int    $network_id ID of the network. Can be null to default to the current network ID.
 * @param string $option     Name of option to add. Expected to not be SQL-escaped.
 * @param mixed  $value      Option value, can be anything. Expected to not be SQL-escaped.
 * @return bool False if option was not added and true if option was added.
 */
function add_network_option( $network_id, $option, $value ) {
	global $wpdb;

	if ( $network_id && ! is_numeric( $network_id ) ) {
		return false;
	}

	$network_id = (int) $network_id;

	// Fallback to the current network if a network ID is not specified.
	if ( ! $network_id ) {
		$network_id = get_current_network_id();
	}

	wp_protect_special_option( $option );

	/**
	 * Filters the value of a specific network option before it is added.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * @since 2.9.0 As 'pre_add_site_option_' . $key
	 * @since 3.0.0
	 * @since 4.4.0 The `$option` parameter was added.
	 * @since 4.7.0 The `$network_id` parameter was added.
	 *
	 * @param mixed  $value      Value of network option.
	 * @param string $option     Option name.
	 * @param int    $network_id ID of the network.
	 */
	$value = apply_filters( "pre_add_site_option_{$option}", $value, $option, $network_id );

	$notoptions_key = "$network_id:notoptions";

	if ( ! is_multisite() ) {
		$result = add_option( $option, $value, '', 'no' );
	} else {
		$cache_key = "$network_id:$option";

		// Make sure the option doesn't already exist. We can check the 'notoptions' cache before we ask for a db query
		$notoptions = wp_cache_get( $notoptions_key, 'site-options' );
		if ( ! is_array( $notoptions ) || ! isset( $notoptions[ $option ] ) ) {
			if ( false !== get_network_option( $network_id, $option, false ) ) {
				return false;
			}
		}

		$value = sanitize_option( $option, $value );

		$serialized_value = maybe_serialize( $value );
		$result           = $wpdb->insert(
			$wpdb->sitemeta, array(
				'site_id'    => $network_id,
				'meta_key'   => $option,
				'meta_value' => $serialized_value,
			)
		);

		if ( ! $result ) {
			return false;
		}

		wp_cache_set( $cache_key, $value, 'site-options' );

		// This option exists now
		$notoptions = wp_cache_get( $notoptions_key, 'site-options' ); // yes, again... we need it to be fresh
		if ( is_array( $notoptions ) && isset( $notoptions[ $option ] ) ) {
			unset( $notoptions[ $option ] );
			wp_cache_set( $notoptions_key, $notoptions, 'site-options' );
		}
	}

	if ( $result ) {

		/**
		 * Fires after a specific network option has been successfully added.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 *
		 * @since 2.9.0 As "add_site_option_{$key}"
		 * @since 3.0.0
		 * @since 4.7.0 The `$network_id` parameter was added.
		 *
		 * @param string $option     Name of the network option.
		 * @param mixed  $value      Value of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( "add_site_option_{$option}", $option, $value, $network_id );

		/**
		 * Fires after a network option has been successfully added.
		 *
		 * @since 3.0.0
		 * @since 4.7.0 The `$network_id` parameter was added.
		 *
		 * @param string $option     Name of the network option.
		 * @param mixed  $value      Value of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( 'add_site_option', $option, $value, $network_id );

		return true;
	}

	return false;
}

/**
 * Removes a network option by name.
 *
 * @since 4.4.0
 *
 * @see delete_option()
 *
 * @global wpdb $wpdb
 *
 * @param int    $network_id ID of the network. Can be null to default to the current network ID.
 * @param string $option     Name of option to remove. Expected to not be SQL-escaped.
 * @return bool True, if succeed. False, if failure.
 */
function delete_network_option( $network_id, $option ) {
	global $wpdb;

	if ( $network_id && ! is_numeric( $network_id ) ) {
		return false;
	}

	$network_id = (int) $network_id;

	// Fallback to the current network if a network ID is not specified.
	if ( ! $network_id ) {
		$network_id = get_current_network_id();
	}

	/**
	 * Fires immediately before a specific network option is deleted.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * @since 3.0.0
	 * @since 4.4.0 The `$option` parameter was added.
	 * @since 4.7.0 The `$network_id` parameter was added.
	 *
	 * @param string $option     Option name.
	 * @param int    $network_id ID of the network.
	 */
	do_action( "pre_delete_site_option_{$option}", $option, $network_id );

	if ( ! is_multisite() ) {
		$result = delete_option( $option );
	} else {
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT meta_id FROM {$wpdb->sitemeta} WHERE meta_key = %s AND site_id = %d", $option, $network_id ) );
		if ( is_null( $row ) || ! $row->meta_id ) {
			return false;
		}
		$cache_key = "$network_id:$option";
		wp_cache_delete( $cache_key, 'site-options' );

		$result = $wpdb->delete(
			$wpdb->sitemeta, array(
				'meta_key' => $option,
				'site_id'  => $network_id,
			)
		);
	}

	if ( $result ) {

		/**
		 * Fires after a specific network option has been deleted.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 *
		 * @since 2.9.0 As "delete_site_option_{$key}"
		 * @since 3.0.0
		 * @since 4.7.0 The `$network_id` parameter was added.
		 *
		 * @param string $option     Name of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( "delete_site_option_{$option}", $option, $network_id );

		/**
		 * Fires after a network option has been deleted.
		 *
		 * @since 3.0.0
		 * @since 4.7.0 The `$network_id` parameter was added.
		 *
		 * @param string $option     Name of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( 'delete_site_option', $option, $network_id );

		return true;
	}

	return false;
}

/**
 * Update the value of a network option that was already added.
 *
 * @since 4.4.0
 *
 * @see update_option()
 *
 * @global wpdb $wpdb
 *
 * @param int      $network_id ID of the network. Can be null to default to the current network ID.
 * @param string   $option     Name of option. Expected to not be SQL-escaped.
 * @param mixed    $value      Option value. Expected to not be SQL-escaped.
 * @return bool False if value was not updated and true if value was updated.
 */
function update_network_option( $network_id, $option, $value ) {
	global $wpdb;

	if ( $network_id && ! is_numeric( $network_id ) ) {
		return false;
	}

	$network_id = (int) $network_id;

	// Fallback to the current network if a network ID is not specified.
	if ( ! $network_id ) {
		$network_id = get_current_network_id();
	}

	wp_protect_special_option( $option );

	$old_value = get_network_option( $network_id, $option, false );

	/**
	 * Filters a specific network option before its value is updated.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the option name.
	 *
	 * @since 2.9.0 As 'pre_update_site_option_' . $key
	 * @since 3.0.0
	 * @since 4.4.0 The `$option` parameter was added.
	 * @since 4.7.0 The `$network_id` parameter was added.
	 *
	 * @param mixed  $value      New value of the network option.
	 * @param mixed  $old_value  Old value of the network option.
	 * @param string $option     Option name.
	 * @param int    $network_id ID of the network.
	 */
	$value = apply_filters( "pre_update_site_option_{$option}", $value, $old_value, $option, $network_id );

	if ( $value === $old_value ) {
		return false;
	}

	if ( false === $old_value ) {
		return add_network_option( $network_id, $option, $value );
	}

	$notoptions_key = "$network_id:notoptions";
	$notoptions     = wp_cache_get( $notoptions_key, 'site-options' );
	if ( is_array( $notoptions ) && isset( $notoptions[ $option ] ) ) {
		unset( $notoptions[ $option ] );
		wp_cache_set( $notoptions_key, $notoptions, 'site-options' );
	}

	if ( ! is_multisite() ) {
		$result = update_option( $option, $value, 'no' );
	} else {
		$value = sanitize_option( $option, $value );

		$serialized_value = maybe_serialize( $value );
		$result           = $wpdb->update(
			$wpdb->sitemeta, array( 'meta_value' => $serialized_value ), array(
				'site_id'  => $network_id,
				'meta_key' => $option,
			)
		);

		if ( $result ) {
			$cache_key = "$network_id:$option";
			wp_cache_set( $cache_key, $value, 'site-options' );
		}
	}

	if ( $result ) {

		/**
		 * Fires after the value of a specific network option has been successfully updated.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 *
		 * @since 2.9.0 As "update_site_option_{$key}"
		 * @since 3.0.0
		 * @since 4.7.0 The `$network_id` parameter was added.
		 *
		 * @param string $option     Name of the network option.
		 * @param mixed  $value      Current value of the network option.
		 * @param mixed  $old_value  Old value of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( "update_site_option_{$option}", $option, $value, $old_value, $network_id );

		/**
		 * Fires after the value of a network option has been successfully updated.
		 *
		 * @since 3.0.0
		 * @since 4.7.0 The `$network_id` parameter was added.
		 *
		 * @param string $option     Name of the network option.
		 * @param mixed  $value      Current value of the network option.
		 * @param mixed  $old_value  Old value of the network option.
		 * @param int    $network_id ID of the network.
		 */
		do_action( 'update_site_option', $option, $value, $old_value, $network_id );

		return true;
	}

	return false;
}

/**
 * Delete a site transient.
 *
 * @since 2.9.0
 *
 * @param string $transient Transient name. Expected to not be SQL-escaped.
 * @return bool True if successful, false otherwise
 */
function delete_site_transient( $transient ) {

	/**
	 * Fires immediately before a specific site transient is deleted.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * @since 3.0.0
	 *
	 * @param string $transient Transient name.
	 */
	do_action( "delete_site_transient_{$transient}", $transient );

	if ( wp_using_ext_object_cache() ) {
		$result = wp_cache_delete( $transient, 'site-transient' );
	} else {
		$option_timeout = '_site_transient_timeout_' . $transient;
		$option         = '_site_transient_' . $transient;
		$result         = delete_site_option( $option );
		if ( $result ) {
			delete_site_option( $option_timeout );
		}
	}
	if ( $result ) {

		/**
		 * Fires after a transient is deleted.
		 *
		 * @since 3.0.0
		 *
		 * @param string $transient Deleted transient name.
		 */
		do_action( 'deleted_site_transient', $transient );
	}

	return $result;
}

/**
 * Get the value of a site transient.
 *
 * If the transient does not exist, does not have a value, or has expired,
 * then the return value will be false.
 *
 * @since 2.9.0
 *
 * @see get_transient()
 *
 * @param string $transient Transient name. Expected to not be SQL-escaped.
 * @return mixed Value of transient.
 */
function get_site_transient( $transient ) {

	/**
	 * Filters the value of an existing site transient.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * Passing a truthy value to the filter will effectively short-circuit retrieval,
	 * returning the passed value instead.
	 *
	 * @since 2.9.0
	 * @since 4.4.0 The `$transient` parameter was added.
	 *
	 * @param mixed  $pre_site_transient The default value to return if the site transient does not exist.
	 *                                   Any value other than false will short-circuit the retrieval
	 *                                   of the transient, and return the returned value.
	 * @param string $transient          Transient name.
	 */
	$pre = apply_filters( "pre_site_transient_{$transient}", false, $transient );

	if ( false !== $pre ) {
		return $pre;
	}

	if ( wp_using_ext_object_cache() ) {
		$value = wp_cache_get( $transient, 'site-transient' );
	} else {
		// Core transients that do not have a timeout. Listed here so querying timeouts can be avoided.
		$no_timeout       = array( 'update_core', 'update_plugins', 'update_themes' );
		$transient_option = '_site_transient_' . $transient;
		if ( ! in_array( $transient, $no_timeout ) ) {
			$transient_timeout = '_site_transient_timeout_' . $transient;
			$timeout           = get_site_option( $transient_timeout );
			if ( false !== $timeout && $timeout < time() ) {
				delete_site_option( $transient_option );
				delete_site_option( $transient_timeout );
				$value = false;
			}
		}

		if ( ! isset( $value ) ) {
			$value = get_site_option( $transient_option );
		}
	}

	/**
	 * Filters the value of an existing site transient.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * @since 2.9.0
	 * @since 4.4.0 The `$transient` parameter was added.
	 *
	 * @param mixed  $value     Value of site transient.
	 * @param string $transient Transient name.
	 */
	return apply_filters( "site_transient_{$transient}", $value, $transient );
}

/**
 * Set/update the value of a site transient.
 *
 * You do not need to serialize values, if the value needs to be serialize, then
 * it will be serialized before it is set.
 *
 * @since 2.9.0
 *
 * @see set_transient()
 *
 * @param string $transient  Transient name. Expected to not be SQL-escaped. Must be
 *                           167 characters or fewer in length.
 * @param mixed  $value      Transient value. Expected to not be SQL-escaped.
 * @param int    $expiration Optional. Time until expiration in seconds. Default 0 (no expiration).
 * @return bool False if value was not set and true if value was set.
 */
function set_site_transient( $transient, $value, $expiration = 0 ) {

	/**
	 * Filters the value of a specific site transient before it is set.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * @since 3.0.0
	 * @since 4.4.0 The `$transient` parameter was added.
	 *
	 * @param mixed  $value     New value of site transient.
	 * @param string $transient Transient name.
	 */
	$value = apply_filters( "pre_set_site_transient_{$transient}", $value, $transient );

	$expiration = (int) $expiration;

	/**
	 * Filters the expiration for a site transient before its value is set.
	 *
	 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
	 *
	 * @since 4.4.0
	 *
	 * @param int    $expiration Time until expiration in seconds. Use 0 for no expiration.
	 * @param mixed  $value      New value of site transient.
	 * @param string $transient  Transient name.
	 */
	$expiration = apply_filters( "expiration_of_site_transient_{$transient}", $expiration, $value, $transient );

	if ( wp_using_ext_object_cache() ) {
		$result = wp_cache_set( $transient, $value, 'site-transient', $expiration );
	} else {
		$transient_timeout = '_site_transient_timeout_' . $transient;
		$option            = '_site_transient_' . $transient;
		if ( false === get_site_option( $option ) ) {
			if ( $expiration ) {
				add_site_option( $transient_timeout, time() + $expiration );
			}
			$result = add_site_option( $option, $value );
		} else {
			if ( $expiration ) {
				update_site_option( $transient_timeout, time() + $expiration );
			}
			$result = update_site_option( $option, $value );
		}
	}
	if ( $result ) {

		/**
		 * Fires after the value for a specific site transient has been set.
		 *
		 * The dynamic portion of the hook name, `$transient`, refers to the transient name.
		 *
		 * @since 3.0.0
		 * @since 4.4.0 The `$transient` parameter was added
		 *
		 * @param mixed  $value      Site transient value.
		 * @param int    $expiration Time until expiration in seconds.
		 * @param string $transient  Transient name.
		 */
		do_action( "set_site_transient_{$transient}", $value, $expiration, $transient );

		/**
		 * Fires after the value for a site transient has been set.
		 *
		 * @since 3.0.0
		 *
		 * @param string $transient  The name of the site transient.
		 * @param mixed  $value      Site transient value.
		 * @param int    $expiration Time until expiration in seconds.
		 */
		do_action( 'setted_site_transient', $transient, $value, $expiration );
	}
	return $result;
}

/**
 * Register default settings available in WordPress.
 *
 * The settings registered here are primarily useful for the REST API, so this
 * does not encompass all settings available in WordPress.
 *
 * @since 4.7.0
 */
function register_initial_settings() {
	register_setting(
		'general', 'blogname', array(
			'show_in_rest' => array(
				'name' => 'title',
			),
			'type'         => 'string',
			'description'  => __( 'Site title.' ),
		)
	);

	register_setting(
		'general', 'blogdescription', array(
			'show_in_rest' => array(
				'name' => 'description',
			),
			'type'         => 'string',
			'description'  => __( 'Site tagline.' ),
		)
	);

	if ( ! is_multisite() ) {
		register_setting(
			'general', 'siteurl', array(
				'show_in_rest' => array(
					'name'   => 'url',
					'schema' => array(
						'format' => 'uri',
					),
				),
				'type'         => 'string',
				'description'  => __( 'Site URL.' ),
			)
		);
	}

	if ( ! is_multisite() ) {
		register_setting(
			'general', 'admin_email', array(
				'show_in_rest' => array(
					'name'   => 'email',
					'schema' => array(
						'format' => 'email',
					),
				),
				'type'         => 'string',
				'description'  => __( 'This address is used for admin purposes, like new user notification.' ),
			)
		);
	}

	register_setting(
		'general', 'timezone_string', array(
			'show_in_rest' => array(
				'name' => 'timezone',
			),
			'type'         => 'string',
			'description'  => __( 'A city in the same timezone as you.' ),
		)
	);

	register_setting(
		'general', 'date_format', array(
			'show_in_rest' => true,
			'type'         => 'string',
			'description'  => __( 'A date format for all date strings.' ),
		)
	);

	register_setting(
		'general', 'time_format', array(
			'show_in_rest' => true,
			'type'         => 'string',
			'description'  => __( 'A time format for all time strings.' ),
		)
	);

	register_setting(
		'general', 'start_of_week', array(
			'show_in_rest' => true,
			'type'         => 'integer',
			'description'  => __( 'A day number of the week that the week should start on.' ),
		)
	);

	register_setting(
		'general', 'WPLANG', array(
			'show_in_rest' => array(
				'name' => 'language',
			),
			'type'         => 'string',
			'description'  => __( 'WordPress locale code.' ),
			'default'      => 'en_US',
		)
	);

	register_setting(
		'writing', 'use_smilies', array(
			'show_in_rest' => true,
			'type'         => 'boolean',
			'description'  => __( 'Convert emoticons like :-) and :-P to graphics on display.' ),
			'default'      => true,
		)
	);

	register_setting(
		'writing', 'default_category', array(
			'show_in_rest' => true,
			'type'         => 'integer',
			'description'  => __( 'Default post category.' ),
		)
	);

	register_setting(
		'writing', 'default_post_format', array(
			'show_in_rest' => true,
			'type'         => 'string',
			'description'  => __( 'Default post format.' ),
		)
	);

	register_setting(
		'reading', 'posts_per_page', array(
			'show_in_rest' => true,
			'type'         => 'integer',
			'description'  => __( 'Blog pages show at most.' ),
			'default'      => 10,
		)
	);

	register_setting(
		'discussion', 'default_ping_status', array(
			'show_in_rest' => array(
				'schema' => array(
					'enum' => array( 'open', 'closed' ),
				),
			),
			'type'         => 'string',
			'description'  => __( 'Allow link notifications from other blogs (pingbacks and trackbacks) on new articles.' ),
		)
	);

	register_setting(
		'discussion', 'default_comment_status', array(
			'show_in_rest' => array(
				'schema' => array(
					'enum' => array( 'open', 'closed' ),
				),
			),
			'type'         => 'string',
			'description'  => __( 'Allow people to post comments on new articles.' ),
		)
	);

	register_setting( 'permalink', 'permalink_structure', array(
		'show_in_rest' => true,
		'type'         => 'string',
		'description'  => __( 'Custom URL structure for permalinks and archives.' ),
	) );
}

/**
 * Register a setting and its data.
 *
 * @since 2.7.0
 * @since 4.7.0 `$args` can be passed to set flags on the setting, similar to `register_meta()`.
 *
 * @global array $new_whitelist_options
 * @global array $wp_registered_settings
 *
 * @param string $option_group A settings group name. Should correspond to a whitelisted option key name.
 *  Default whitelisted option key names include "general," "discussion," and "reading," among others.
 * @param string $option_name The name of an option to sanitize and save.
 * @param array  $args {
 *     Data used to describe the setting when registered.
 *
 *     @type string   $type              The type of data associated with this setting.
 *                                       Valid values are 'string', 'boolean', 'integer', and 'number'.
 *     @type string   $description       A description of the data attached to this setting.
 *     @type callable $sanitize_callback A callback function that sanitizes the option's value.
 *     @type bool     $show_in_rest      Whether data associated with this setting should be included in the REST API.
 *     @type mixed    $default           Default value when calling `get_option()`.
 * }
 */
function register_setting( $option_group, $option_name, $args = array() ) {
	global $new_whitelist_options, $wp_registered_settings;

	$defaults = array(
		'type'              => 'string',
		'group'             => $option_group,
		'description'       => '',
		'sanitize_callback' => null,
		'show_in_rest'      => false,
	);

	// Back-compat: old sanitize callback is added.
	if ( is_callable( $args ) ) {
		$args = array(
			'sanitize_callback' => $args,
		);
	}

	/**
	 * Filters the registration arguments when registering a setting.
	 *
	 * @since 4.7.0
	 *
	 * @param array  $args         Array of setting registration arguments.
	 * @param array  $defaults     Array of default arguments.
	 * @param string $option_group Setting group.
	 * @param string $option_name  Setting name.
	 */
	$args = apply_filters( 'register_setting_args', $args, $defaults, $option_group, $option_name );
	$args = wp_parse_args( $args, $defaults );

	if ( ! is_array( $wp_registered_settings ) ) {
		$wp_registered_settings = array();
	}

	if ( 'misc' == $option_group ) {
		_deprecated_argument(
			__FUNCTION__, '3.0.0',
			/* translators: %s: misc */
			sprintf(
				__( 'The "%s" options group has been removed. Use another settings group.' ),
				'misc'
			)
		);
		$option_group = 'general';
	}

	if ( 'privacy' == $option_group ) {
		_deprecated_argument(
			__FUNCTION__, '3.5.0',
			/* translators: %s: privacy */
			sprintf(
				__( 'The "%s" options group has been removed. Use another settings group.' ),
				'privacy'
			)
		);
		$option_group = 'reading';
	}

	$new_whitelist_options[ $option_group ][] = $option_name;
	if ( ! empty( $args['sanitize_callback'] ) ) {
		add_filter( "sanitize_option_{$option_name}", $args['sanitize_callback'] );
	}
	if ( array_key_exists( 'default', $args ) ) {
		add_filter( "default_option_{$option_name}", 'filter_default_option', 10, 3 );
	}

	$wp_registered_settings[ $option_name ] = $args;
}

/**
 * Unregister a setting.
 *
 * @since 2.7.0
 * @since 4.7.0 `$sanitize_callback` was deprecated. The callback from `register_setting()` is now used instead.
 *
 * @global array $new_whitelist_options
 *
 * @param string   $option_group      The settings group name used during registration.
 * @param string   $option_name       The name of the option to unregister.
 * @param callable $deprecated        Deprecated.
 */
function unregister_setting( $option_group, $option_name, $deprecated = '' ) {
	global $new_whitelist_options, $wp_registered_settings;

	if ( 'misc' == $option_group ) {
		_deprecated_argument(
			__FUNCTION__, '3.0.0',
			/* translators: %s: misc */
			sprintf(
				__( 'The "%s" options group has been removed. Use another settings group.' ),
				'misc'
			)
		);
		$option_group = 'general';
	}

	if ( 'privacy' == $option_group ) {
		_deprecated_argument(
			__FUNCTION__, '3.5.0',
			/* translators: %s: privacy */
			sprintf(
				__( 'The "%s" options group has been removed. Use another settings group.' ),
				'privacy'
			)
		);
		$option_group = 'reading';
	}

	$pos = array_search( $option_name, (array) $new_whitelist_options[ $option_group ] );
	if ( $pos !== false ) {
		unset( $new_whitelist_options[ $option_group ][ $pos ] );
	}
	if ( '' !== $deprecated ) {
		_deprecated_argument(
			__FUNCTION__, '4.7.0',
			/* translators: 1: $sanitize_callback, 2: register_setting() */
			sprintf(
				__( '%1$s is deprecated. The callback from %2$s is used instead.' ),
				'<code>$sanitize_callback</code>',
				'<code>register_setting()</code>'
			)
		);
		remove_filter( "sanitize_option_{$option_name}", $deprecated );
	}

	if ( isset( $wp_registered_settings[ $option_name ] ) ) {
		// Remove the sanitize callback if one was set during registration.
		if ( ! empty( $wp_registered_settings[ $option_name ]['sanitize_callback'] ) ) {
			remove_filter( "sanitize_option_{$option_name}", $wp_registered_settings[ $option_name ]['sanitize_callback'] );
		}

		// Remove the default filter if a default was provided during registration.
		if ( array_key_exists( 'default', $wp_registered_settings[ $option_name ] ) ) {
			remove_filter( "default_option_{$option_name}", 'filter_default_option', 10 );
		}

		unset( $wp_registered_settings[ $option_name ] );
	}
}

/**
 * Retrieves an array of registered settings.
 *
 * @since 4.7.0
 *
 * @return array List of registered settings, keyed by option name.
 */
function get_registered_settings() {
	global $wp_registered_settings;

	if ( ! is_array( $wp_registered_settings ) ) {
		return array();
	}

	return $wp_registered_settings;
}

/**
 * Filter the default value for the option.
 *
 * For settings which register a default setting in `register_setting()`, this
 * function is added as a filter to `default_option_{$option}`.
 *
 * @since 4.7.0
 *
 * @param mixed $default Existing default value to return.
 * @param string $option Option name.
 * @param bool $passed_default Was `get_option()` passed a default value?
 * @return mixed Filtered default value.
 */
function filter_default_option( $default, $option, $passed_default ) {
	if ( $passed_default ) {
		return $default;
	}

	$registered = get_registered_settings();
	if ( empty( $registered[ $option ] ) ) {
		return $default;
	}

	return $registered[ $option ]['default'];
}
