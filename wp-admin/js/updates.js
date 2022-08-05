/**
 * Functions for ajaxified updates, deletions and installs inside the WordPress admin.
 *
 * @version 4.2.0
 * @output wp-admin/js/updates.js
 */

/* global pagenow */

/**
 * @param {jQuery}  $                                        jQuery object.
 * @param {object}  wp                                       WP object.
 * @param {object}  settings                                 WP Updates settings.
 * @param {string}  settings.ajax_nonce                      Ajax nonce.
 * @param {object=} settings.plugins                         Base names of plugins in their different states.
 * @param {Array}   settings.plugins.all                     Base names of all plugins.
 * @param {Array}   settings.plugins.active                  Base names of active plugins.
 * @param {Array}   settings.plugins.inactive                Base names of inactive plugins.
 * @param {Array}   settings.plugins.upgrade                 Base names of plugins with updates available.
 * @param {Array}   settings.plugins.recently_activated      Base names of recently activated plugins.
 * @param {Array}   settings.plugins['auto-update-enabled']  Base names of plugins set to auto-update.
 * @param {Array}   settings.plugins['auto-update-disabled'] Base names of plugins set to not auto-update.
 * @param {object=} settings.themes                          Slugs of themes in their different states.
 * @param {Array}   settings.themes.all                      Slugs of all themes.
 * @param {Array}   settings.themes.upgrade                  Slugs of themes with updates available.
 * @param {Arrat}   settings.themes.disabled                 Slugs of disabled themes.
 * @param {Array}   settings.themes['auto-update-enabled']   Slugs of themes set to auto-update.
 * @param {Array}   settings.themes['auto-update-disabled']  Slugs of themes set to not auto-update.
 * @param {object=} settings.totals                          Combined information for available update counts.
 * @param {number}  settings.totals.count                    Holds the amount of available updates.
 */
(function( $, wp, settings ) {
	var $document = $( document ),
		__ = wp.i18n.__,
		_x = wp.i18n._x,
		_n = wp.i18n._n,
		_nx = wp.i18n._nx,
		sprintf = wp.i18n.sprintf;

	wp = wp || {};

	/**
	 * The WP Updates object.
	 *
	 * @since 4.2.0
	 *
	 * @namespace wp.updates
	 */
	wp.updates = {};

	/**
	 * Removed in 5.5.0, needed for back-compatibility.
	 *
	 * @since 4.2.0
	 * @deprecated 5.5.0
	 *
	 * @type {object}
	 */
	wp.updates.l10n = {
		searchResults: '',
		searchResultsLabel: '',
		noPlugins: '',
		noItemsSelected: '',
		updating: '',
		pluginUpdated: '',
		themeUpdated: '',
		update: '',
		updateNow: '',
		pluginUpdateNowLabel: '',
		updateFailedShort: '',
		updateFailed: '',
		pluginUpdatingLabel: '',
		pluginUpdatedLabel: '',
		pluginUpdateFailedLabel: '',
		updatingMsg: '',
		updatedMsg: '',
		updateCancel: '',
		beforeunload: '',
		installNow: '',
		pluginInstallNowLabel: '',
		installing: '',
		pluginInstalled: '',
		themeInstalled: '',
		installFailedShort: '',
		installFailed: '',
		pluginInstallingLabel: '',
		themeInstallingLabel: '',
		pluginInstalledLabel: '',
		themeInstalledLabel: '',
		pluginInstallFailedLabel: '',
		themeInstallFailedLabel: '',
		installingMsg: '',
		installedMsg: '',
		importerInstalledMsg: '',
		aysDelete: '',
		aysDeleteUninstall: '',
		aysBulkDelete: '',
		aysBulkDeleteThemes: '',
		deleting: '',
		deleteFailed: '',
		pluginDeleted: '',
		themeDeleted: '',
		livePreview: '',
		activatePlugin: '',
		activateTheme: '',
		activatePluginLabel: '',
		activateThemeLabel: '',
		activateImporter: '',
		activateImporterLabel: '',
		unknownError: '',
		connectionError: '',
		nonceError: '',
		pluginsFound: '',
		noPluginsFound: '',
		autoUpdatesEnable: '',
		autoUpdatesEnabling: '',
		autoUpdatesEnabled: '',
		autoUpdatesDisable: '',
		autoUpdatesDisabling: '',
		autoUpdatesDisabled: '',
		autoUpdatesError: ''
	};

	wp.updates.l10n = window.wp.deprecateL10nObject( 'wp.updates.l10n', wp.updates.l10n, '5.5.0' );

	/**
	 * User nonce for ajax calls.
	 *
	 * @since 4.2.0
	 *
	 * @type {string}
	 */
	wp.updates.ajaxNonce = settings.ajax_nonce;

	/**
	 * Current search term.
	 *
	 * @since 4.6.0
	 *
	 * @type {string}
	 */
	wp.updates.searchTerm = '';

	/**
	 * Whether filesystem credentials need to be requested from the user.
	 *
	 * @since 4.2.0
	 *
	 * @type {bool}
	 */
	wp.updates.shouldRequestFilesystemCredentials = false;

	/**
	 * Filesystem credentials to be packaged along with the request.
	 *
	 * @since 4.2.0
	 * @since 4.6.0 Added `available` property to indicate whether credentials have been provided.
	 *
	 * @type {Object}
	 * @property {Object} filesystemCredentials.ftp                Holds FTP credentials.
	 * @property {string} filesystemCredentials.ftp.host           FTP host. Default empty string.
	 * @property {string} filesystemCredentials.ftp.username       FTP user name. Default empty string.
	 * @property {string} filesystemCredentials.ftp.password       FTP password. Default empty string.
	 * @property {string} filesystemCredentials.ftp.connectionType Type of FTP connection. 'ssh', 'ftp', or 'ftps'.
	 *                                                             Default empty string.
	 * @property {Object} filesystemCredentials.ssh                Holds SSH credentials.
	 * @property {string} filesystemCredentials.ssh.publicKey      The public key. Default empty string.
	 * @property {string} filesystemCredentials.ssh.privateKey     The private key. Default empty string.
	 * @property {string} filesystemCredentials.fsNonce            Filesystem credentials form nonce.
	 * @property {bool}   filesystemCredentials.available          Whether filesystem credentials have been provided.
	 *                                                             Default 'false'.
	 */
	wp.updates.filesystemCredentials = {
		ftp:       {
			host:           '',
			username:       '',
			password:       '',
			connectionType: ''
		},
		ssh:       {
			publicKey:  '',
			privateKey: ''
		},
		fsNonce: '',
		available: false
	};

	/**
	 * Whether we're waiting for an Ajax request to complete.
	 *
	 * @since 4.2.0
	 * @since 4.6.0 More accurately named `ajaxLocked`.
	 *
	 * @type {bool}
	 */
	wp.updates.ajaxLocked = false;

	/**
	 * Admin notice template.
	 *
	 * @since 4.6.0
	 *
	 * @type {function}
	 */
	wp.updates.adminNotice = wp.template( 'wp-updates-admin-notice' );

	/**
	 * Update queue.
	 *
	 * If the user tries to update a plugin while an update is
	 * already happening, it can be placed in this queue to perform later.
	 *
	 * @since 4.2.0
	 * @since 4.6.0 More accurately named `queue`.
	 *
	 * @type {Array.object}
	 */
	wp.updates.queue = [];

	/**
	 * Holds a jQuery reference to return focus to when exiting the request credentials modal.
	 *
	 * @since 4.2.0
	 *
	 * @type {jQuery}
	 */
	wp.updates.$elToReturnFocusToFromCredentialsModal = undefined;

	/**
	 * Adds or updates an admin notice.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object}  data
	 * @param {*=}      data.selector      Optional. Selector of an element to be replaced with the admin notice.
	 * @param {string=} data.id            Optional. Unique id that will be used as the notice's id attribute.
	 * @param {string=} data.className     Optional. Class names that will be used in the admin notice.
	 * @param {string=} data.message       Optional. The message displayed in the notice.
	 * @param {number=} data.successes     Optional. The amount of successful operations.
	 * @param {number=} data.errors        Optional. The amount of failed operations.
	 * @param {Array=}  data.errorMessages Optional. Error messages of failed operations.
	 *
	 */
	wp.updates.addAdminNotice = function( data ) {
		var $notice = $( data.selector ),
			$headerEnd = $( '.wp-header-end' ),
			$adminNotice;

		delete data.selector;
		$adminNotice = wp.updates.adminNotice( data );

		// Check if this admin notice already exists.
		if ( ! $notice.length ) {
			$notice = $( '#' + data.id );
		}

		if ( $notice.length ) {
			$notice.replaceWith( $adminNotice );
		} else if ( $headerEnd.length ) {
			$headerEnd.after( $adminNotice );
		} else {
			if ( 'customize' === pagenow ) {
				$( '.customize-themes-notifications' ).append( $adminNotice );
			} else {
				$( '.wrap' ).find( '> h1' ).after( $adminNotice );
			}
		}

		$document.trigger( 'wp-updates-notice-added' );
	};

	/**
	 * Handles Ajax requests to WordPress.
	 *
	 * @since 4.6.0
	 *
	 * @param {string} action The type of Ajax request ('update-plugin', 'install-theme', etc).
	 * @param {Object} data   Data that needs to be passed to the ajax callback.
	 * @return {$.promise}    A jQuery promise that represents the request,
	 *                        decorated with an abort() method.
	 */
	wp.updates.ajax = function( action, data ) {
		var options = {};

		if ( wp.updates.ajaxLocked ) {
			wp.updates.queue.push( {
				action: action,
				data:   data
			} );

			// Return a Deferred object so callbacks can always be registered.
			return $.Deferred();
		}

		wp.updates.ajaxLocked = true;

		if ( data.success ) {
			options.success = data.success;
			delete data.success;
		}

		if ( data.error ) {
			options.error = data.error;
			delete data.error;
		}

		options.data = _.extend( data, {
			action:          action,
			_ajax_nonce:     wp.updates.ajaxNonce,
			_fs_nonce:       wp.updates.filesystemCredentials.fsNonce,
			username:        wp.updates.filesystemCredentials.ftp.username,
			password:        wp.updates.filesystemCredentials.ftp.password,
			hostname:        wp.updates.filesystemCredentials.ftp.hostname,
			connection_type: wp.updates.filesystemCredentials.ftp.connectionType,
			public_key:      wp.updates.filesystemCredentials.ssh.publicKey,
			private_key:     wp.updates.filesystemCredentials.ssh.privateKey
		} );

		return wp.ajax.send( options ).always( wp.updates.ajaxAlways );
	};

	/**
	 * Actions performed after every Ajax request.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object}  response
	 * @param {Array=}  response.debug     Optional. Debug information.
	 * @param {string=} response.errorCode Optional. Error code for an error that occurred.
	 */
	wp.updates.ajaxAlways = function( response ) {
		if ( ! response.errorCode || 'unable_to_connect_to_filesystem' !== response.errorCode ) {
			wp.updates.ajaxLocked = false;
			wp.updates.queueChecker();
		}

		if ( 'undefined' !== typeof response.debug && window.console && window.console.log ) {
			_.map( response.debug, function( message ) {
				// Remove all HTML tags and write a message to the console.
				window.console.log( wp.sanitize.stripTagsAndEncodeText( message ) );
			} );
		}
	};

	/**
	 * Refreshes update counts everywhere on the screen.
	 *
	 * @since 4.7.0
	 */
	wp.updates.refreshCount = function() {
		var $adminBarUpdates              = $( '#wp-admin-bar-updates' ),
			$dashboardNavMenuUpdateCount  = $( 'a[href="update-core.php"] .update-plugins' ),
			$pluginsNavMenuUpdateCount    = $( 'a[href="plugins.php"] .update-plugins' ),
			$appearanceNavMenuUpdateCount = $( 'a[href="themes.php"] .update-plugins' ),
			itemCount;

		$adminBarUpdates.find( '.ab-label' ).text( settings.totals.counts.total );
		$adminBarUpdates.find( '.updates-available-text' ).text(
			sprintf(
				/* translators: %s: Total number of updates available. */
				_n( '%s update available', '%s updates available', settings.totals.counts.total ),
				settings.totals.counts.total
			)
		);

		// Remove the update count from the toolbar if it's zero.
		if ( 0 === settings.totals.counts.total ) {
			$adminBarUpdates.find( '.ab-label' ).parents( 'li' ).remove();
		}

		// Update the "Updates" menu item.
		$dashboardNavMenuUpdateCount.each( function( index, element ) {
			element.className = element.className.replace( /count-\d+/, 'count-' + settings.totals.counts.total );
		} );
		if ( settings.totals.counts.total > 0 ) {
			$dashboardNavMenuUpdateCount.find( '.update-count' ).text( settings.totals.counts.total );
		} else {
			$dashboardNavMenuUpdateCount.remove();
		}

		// Update the "Plugins" menu item.
		$pluginsNavMenuUpdateCount.each( function( index, element ) {
			element.className = element.className.replace( /count-\d+/, 'count-' + settings.totals.counts.plugins );
		} );
		if ( settings.totals.counts.total > 0 ) {
			$pluginsNavMenuUpdateCount.find( '.plugin-count' ).text( settings.totals.counts.plugins );
		} else {
			$pluginsNavMenuUpdateCount.remove();
		}

		// Update the "Appearance" menu item.
		$appearanceNavMenuUpdateCount.each( function( index, element ) {
			element.className = element.className.replace( /count-\d+/, 'count-' + settings.totals.counts.themes );
		} );
		if ( settings.totals.counts.total > 0 ) {
			$appearanceNavMenuUpdateCount.find( '.theme-count' ).text( settings.totals.counts.themes );
		} else {
			$appearanceNavMenuUpdateCount.remove();
		}

		// Update list table filter navigation.
		if ( 'plugins' === pagenow || 'plugins-network' === pagenow ) {
			itemCount = settings.totals.counts.plugins;
		} else if ( 'themes' === pagenow || 'themes-network' === pagenow ) {
			itemCount = settings.totals.counts.themes;
		}

		if ( itemCount > 0 ) {
			$( '.subsubsub .upgrade .count' ).text( '(' + itemCount + ')' );
		} else {
			$( '.subsubsub .upgrade' ).remove();
			$( '.subsubsub li:last' ).html( function() { return $( this ).children(); } );
		}
	};

	/**
	 * Decrements the update counts throughout the various menus.
	 *
	 * This includes the toolbar, the "Updates" menu item and the menu items
	 * for plugins and themes.
	 *
	 * @since 3.9.0
	 *
	 * @param {string} type The type of item that was updated or deleted.
	 *                      Can be 'plugin', 'theme'.
	 */
	wp.updates.decrementCount = function( type ) {
		settings.totals.counts.total = Math.max( --settings.totals.counts.total, 0 );

		if ( 'plugin' === type ) {
			settings.totals.counts.plugins = Math.max( --settings.totals.counts.plugins, 0 );
		} else if ( 'theme' === type ) {
			settings.totals.counts.themes = Math.max( --settings.totals.counts.themes, 0 );
		}

		wp.updates.refreshCount( type );
	};

	/**
	 * Sends an Ajax request to the server to update a plugin.
	 *
	 * @since 4.2.0
	 * @since 4.6.0 More accurately named `updatePlugin`.
	 *
	 * @param {Object}               args         Arguments.
	 * @param {string}               args.plugin  Plugin basename.
	 * @param {string}               args.slug    Plugin slug.
	 * @param {updatePluginSuccess=} args.success Optional. Success callback. Default: wp.updates.updatePluginSuccess
	 * @param {updatePluginError=}   args.error   Optional. Error callback. Default: wp.updates.updatePluginError
	 * @return {$.promise} A jQuery promise that represents the request,
	 *                     decorated with an abort() method.
	 */
	wp.updates.updatePlugin = function( args ) {
		var $updateRow, $card, $message, message,
			$adminBarUpdates = $( '#wp-admin-bar-updates' );

		args = _.extend( {
			success: wp.updates.updatePluginSuccess,
			error: wp.updates.updatePluginError
		}, args );

		if ( 'plugins' === pagenow || 'plugins-network' === pagenow ) {
			$updateRow = $( 'tr[data-plugin="' + args.plugin + '"]' );
			$message   = $updateRow.find( '.update-message' ).removeClass( 'notice-error' ).addClass( 'updating-message notice-warning' ).find( 'p' );
			message    = sprintf(
				/* translators: %s: Plugin name and version. */
 				_x( 'Updating %s...', 'plugin' ),
				$updateRow.find( '.plugin-title strong' ).text()
			);
		} else if ( 'plugin-install' === pagenow || 'plugin-install-network' === pagenow ) {
			$card    = $( '.plugin-card-' + args.slug );
			$message = $card.find( '.update-now' ).addClass( 'updating-message' );
			message    = sprintf(
				/* translators: %s: Plugin name and version. */
 				_x( 'Updating %s...', 'plugin' ),
				$message.data( 'name' )
			);

			// Remove previous error messages, if any.
			$card.removeClass( 'plugin-card-update-failed' ).find( '.notice.notice-error' ).remove();
		}

		$adminBarUpdates.addClass( 'spin' );

		if ( $message.html() !== __( 'Updating...' ) ) {
			$message.data( 'originaltext', $message.html() );
		}

		$message
			.attr( 'aria-label', message )
			.text( __( 'Updating...' ) );

		$document.trigger( 'wp-plugin-updating', args );

		return wp.updates.ajax( 'update-plugin', args );
	};

	/**
	 * Updates the UI appropriately after a successful plugin update.
	 *
	 * @since 4.2.0
	 * @since 4.6.0 More accurately named `updatePluginSuccess`.
	 * @since 5.5.0 Auto-update "time to next update" text cleared.
	 *
	 * @param {Object} response            Response from the server.
	 * @param {string} response.slug       Slug of the plugin to be updated.
	 * @param {string} response.plugin     Basename of the plugin to be updated.
	 * @param {string} response.pluginName Name of the plugin to be updated.
	 * @param {string} response.oldVersion Old version of the plugin.
	 * @param {string} response.newVersion New version of the plugin.
	 */
	wp.updates.updatePluginSuccess = function( response ) {
		var $pluginRow, $updateMessage, newText,
			$adminBarUpdates = $( '#wp-admin-bar-updates' );

		if ( 'plugins' === pagenow || 'plugins-network' === pagenow ) {
			$pluginRow     = $( 'tr[data-plugin="' + response.plugin + '"]' )
				.removeClass( 'update' )
				.addClass( 'updated' );
			$updateMessage = $pluginRow.find( '.update-message' )
				.removeClass( 'updating-message notice-warning' )
				.addClass( 'updated-message notice-success' ).find( 'p' );

			// Update the version number in the row.
			newText = $pluginRow.find( '.plugin-version-author-uri' ).html().replace( response.oldVersion, response.newVersion );
			$pluginRow.find( '.plugin-version-author-uri' ).html( newText );

			// Clear the "time to next auto-update" text.
			$pluginRow.find( '.auto-update-time' ).empty();
		} else if ( 'plugin-install' === pagenow || 'plugin-install-network' === pagenow ) {
			$updateMessage = $( '.plugin-card-' + response.slug ).find( '.update-now' )
				.removeClass( 'updating-message' )
				.addClass( 'button-disabled updated-message' );
		}

		$adminBarUpdates.removeClass( 'spin' );

		$updateMessage
			.attr(
				'aria-label',
				sprintf(
					/* translators: %s: Plugin name and version. */
					_x( '%s updated!', 'plugin' ),
					response.pluginName
				)
			)
			.text( _x( 'Updated!', 'plugin' ) );

		wp.a11y.speak( __( 'Update completed successfully.' ) );

		wp.updates.decrementCount( 'plugin' );

		$document.trigger( 'wp-plugin-update-success', response );
	};

	/**
	 * Updates the UI appropriately after a failed plugin update.
	 *
	 * @since 4.2.0
	 * @since 4.6.0 More accurately named `updatePluginError`.
	 *
	 * @param {Object}  response              Response from the server.
	 * @param {string}  response.slug         Slug of the plugin to be updated.
	 * @param {string}  response.plugin       Basename of the plugin to be updated.
	 * @param {string=} response.pluginName   Optional. Name of the plugin to be updated.
	 * @param {string}  response.errorCode    Error code for the error that occurred.
	 * @param {string}  response.errorMessage The error that occurred.
	 */
	wp.updates.updatePluginError = function( response ) {
		var $card, $message, errorMessage,
			$adminBarUpdates = $( '#wp-admin-bar-updates' );

		if ( ! wp.updates.isValidResponse( response, 'update' ) ) {
			return;
		}

		if ( wp.updates.maybeHandleCredentialError( response, 'update-plugin' ) ) {
			return;
		}

		errorMessage = sprintf(
			/* translators: %s: Error string for a failed update. */
			__( 'Update failed: %s' ),
			response.errorMessage
		);

		if ( 'plugins' === pagenow || 'plugins-network' === pagenow ) {
			if ( response.plugin ) {
				$message = $( 'tr[data-plugin="' + response.plugin + '"]' ).find( '.update-message' );
			} else {
				$message = $( 'tr[data-slug="' + response.slug + '"]' ).find( '.update-message' );
			}
			$message.removeClass( 'updating-message notice-warning' ).addClass( 'notice-error' ).find( 'p' ).html( errorMessage );

			if ( response.pluginName ) {
				$message.find( 'p' )
					.attr(
						'aria-label',
						sprintf(
							/* translators: %s: Plugin name and version. */
							_x( '%s update failed.', 'plugin' ),
							response.pluginName
						)
					);
			} else {
				$message.find( 'p' ).removeAttr( 'aria-label' );
			}
		} else if ( 'plugin-install' === pagenow || 'plugin-install-network' === pagenow ) {
			$card = $( '.plugin-card-' + response.slug )
				.addClass( 'plugin-card-update-failed' )
				.append( wp.updates.adminNotice( {
					className: 'update-message notice-error notice-alt is-dismissible',
					message:   errorMessage
				} ) );

			$card.find( '.update-now' )
				.text(  __( 'Update failed.' ) )
				.removeClass( 'updating-message' );

			if ( response.pluginName ) {
				$card.find( '.update-now' )
					.attr(
						'aria-label',
						sprintf(
							/* translators: %s: Plugin name and version. */
							_x( '%s update failed.', 'plugin' ),
							response.pluginName
						)
					);
			} else {
				$card.find( '.update-now' ).removeAttr( 'aria-label' );
			}

			$card.on( 'click', '.notice.is-dismissible .notice-dismiss', function() {

				// Use same delay as the total duration of the notice fadeTo + slideUp animation.
				setTimeout( function() {
					$card
						.removeClass( 'plugin-card-update-failed' )
						.find( '.column-name a' ).trigger( 'focus' );

					$card.find( '.update-now' )
						.attr( 'aria-label', false )
						.text( __( 'Update Now' ) );
				}, 200 );
			} );
		}

		$adminBarUpdates.removeClass( 'spin' );

		wp.a11y.speak( errorMessage, 'assertive' );

		$document.trigger( 'wp-plugin-update-error', response );
	};

	/**
	 * Sends an Ajax request to the server to install a plugin.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object}                args         Arguments.
	 * @param {string}                args.slug    Plugin identifier in the WordPress.org Plugin repository.
	 * @param {installPluginSuccess=} args.success Optional. Success callback. Default: wp.updates.installPluginSuccess
	 * @param {installPluginError=}   args.error   Optional. Error callback. Default: wp.updates.installPluginError
	 * @return {$.promise} A jQuery promise that represents the request,
	 *                     decorated with an abort() method.
	 */
	wp.updates.installPlugin = function( args ) {
		var $card    = $( '.plugin-card-' + args.slug ),
			$message = $card.find( '.install-now' );

		args = _.extend( {
			success: wp.updates.installPluginSuccess,
			error: wp.updates.installPluginError
		}, args );

		if ( 'import' === pagenow ) {
			$message = $( '[data-slug="' + args.slug + '"]' );
		}

		if ( $message.html() !== __( 'Installing...' ) ) {
			$message.data( 'originaltext', $message.html() );
		}

		$message
			.addClass( 'updating-message' )
			.attr(
				'aria-label',
				sprintf(
					/* translators: %s: Plugin name and version. */
					_x( 'Installing %s...', 'plugin' ),
					$message.data( 'name' )
				)
			)
			.text( __( 'Installing...' ) );

		wp.a11y.speak( __( 'Installing... please wait.' ) );

		// Remove previous error messages, if any.
		$card.removeClass( 'plugin-card-install-failed' ).find( '.notice.notice-error' ).remove();

		$document.trigger( 'wp-plugin-installing', args );

		return wp.updates.ajax( 'install-plugin', args );
	};

	/**
	 * Updates the UI appropriately after a successful plugin install.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object} response             Response from the server.
	 * @param {string} response.slug        Slug of the installed plugin.
	 * @param {string} response.pluginName  Name of the installed plugin.
	 * @param {string} response.activateUrl URL to activate the just installed plugin.
	 */
	wp.updates.installPluginSuccess = function( response ) {
		var $message = $( '.plugin-card-' + response.slug ).find( '.install-now' );

		$message
			.removeClass( 'updating-message' )
			.addClass( 'updated-message installed button-disabled' )
			.attr(
				'aria-label',
				sprintf(
					/* translators: %s: Plugin name and version. */
					_x( '%s installed!', 'plugin' ),
					response.pluginName
				)
			)
			.text( _x( 'Installed!', 'plugin' ) );

		wp.a11y.speak( __( 'Installation completed successfully.' ) );

		$document.trigger( 'wp-plugin-install-success', response );

		if ( response.activateUrl ) {
			setTimeout( function() {

				// Transform the 'Install' button into an 'Activate' button.
				$message.removeClass( 'install-now installed button-disabled updated-message' )
					.addClass( 'activate-now button-primary' )
					.attr( 'href', response.activateUrl );

				if ( 'plugins-network' === pagenow ) {
					$message
						.attr(
							'aria-label',
							sprintf(
								/* translators: %s: Plugin name. */
								_x( 'Network Activate %s', 'plugin' ),
								response.pluginName
							)
						)
						.text( __( 'Network Activate' ) );
				} else {
					$message
						.attr(
							'aria-label',
							sprintf(
								/* translators: %s: Plugin name. */
								_x( 'Activate %s', 'plugin' ),
								response.pluginName
							)
						)
						.text( __( 'Activate' ) );
				}
			}, 1000 );
		}
	};

	/**
	 * Updates the UI appropriately after a failed plugin install.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object}  response              Response from the server.
	 * @param {string}  response.slug         Slug of the plugin to be installed.
	 * @param {string=} response.pluginName   Optional. Name of the plugin to be installed.
	 * @param {string}  response.errorCode    Error code for the error that occurred.
	 * @param {string}  response.errorMessage The error that occurred.
	 */
	wp.updates.installPluginError = function( response ) {
		var $card   = $( '.plugin-card-' + response.slug ),
			$button = $card.find( '.install-now' ),
			errorMessage;

		if ( ! wp.updates.isValidResponse( response, 'install' ) ) {
			return;
		}

		if ( wp.updates.maybeHandleCredentialError( response, 'install-plugin' ) ) {
			return;
		}

		errorMessage = sprintf(
			/* translators: %s: Error string for a failed installation. */
			__( 'Installation failed: %s' ),
			response.errorMessage
		);

		$card
			.addClass( 'plugin-card-update-failed' )
			.append( '<div class="notice notice-error notice-alt is-dismissible"><p>' + errorMessage + '</p></div>' );

		$card.on( 'click', '.notice.is-dismissible .notice-dismiss', function() {

			// Use same delay as the total duration of the notice fadeTo + slideUp animation.
			setTimeout( function() {
				$card
					.removeClass( 'plugin-card-update-failed' )
					.find( '.column-name a' ).trigger( 'focus' );
			}, 200 );
		} );

		$button
			.removeClass( 'updating-message' ).addClass( 'button-disabled' )
			.attr(
				'aria-label',
				sprintf(
					/* translators: %s: Plugin name and version. */
					_x( '%s installation failed', 'plugin' ),
					$button.data( 'name' )
				)
			)
			.text( __( 'Installation failed.' ) );

		wp.a11y.speak( errorMessage, 'assertive' );

		$document.trigger( 'wp-plugin-install-error', response );
	};

	/**
	 * Updates the UI appropriately after a successful importer install.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object} response             Response from the server.
	 * @param {string} response.slug        Slug of the installed plugin.
	 * @param {string} response.pluginName  Name of the installed plugin.
	 * @param {string} response.activateUrl URL to activate the just installed plugin.
	 */
	wp.updates.installImporterSuccess = function( response ) {
		wp.updates.addAdminNotice( {
			id:        'install-success',
			className: 'notice-success is-dismissible',
			message:   sprintf(
				/* translators: %s: Activation URL. */
				__( 'Importer installed successfully. <a href="%s">Run importer</a>' ),
				response.activateUrl + '&from=import'
			)
		} );

		$( '[data-slug="' + response.slug + '"]' )
			.removeClass( 'install-now updating-message' )
			.addClass( 'activate-now' )
			.attr({
				'href': response.activateUrl + '&from=import',
				'aria-label':sprintf(
					/* translators: %s: Importer name. */
					__( 'Run %s' ),
					response.pluginName
				)
			})
			.text( __( 'Run Importer' ) );

		wp.a11y.speak( __( 'Installation completed successfully.' ) );

		$document.trigger( 'wp-importer-install-success', response );
	};

	/**
	 * Updates the UI appropriately after a failed importer install.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object}  response              Response from the server.
	 * @param {string}  response.slug         Slug of the plugin to be installed.
	 * @param {string=} response.pluginName   Optional. Name of the plugin to be installed.
	 * @param {string}  response.errorCode    Error code for the error that occurred.
	 * @param {string}  response.errorMessage The error that occurred.
	 */
	wp.updates.installImporterError = function( response ) {
		var errorMessage = sprintf(
				/* translators: %s: Error string for a failed installation. */
				__( 'Installation failed: %s' ),
				response.errorMessage
			),
			$installLink = $( '[data-slug="' + response.slug + '"]' ),
			pluginName = $installLink.data( 'name' );

		if ( ! wp.updates.isValidResponse( response, 'install' ) ) {
			return;
		}

		if ( wp.updates.maybeHandleCredentialError( response, 'install-plugin' ) ) {
			return;
		}

		wp.updates.addAdminNotice( {
			id:        response.errorCode,
			className: 'notice-error is-dismissible',
			message:   errorMessage
		} );

		$installLink
			.removeClass( 'updating-message' )
			.attr(
				'aria-label',
				sprintf(
					/* translators: %s: Plugin name. */
					_x( 'Install %s now', 'plugin' ),
					pluginName
				)
			)
			.text( __( 'Install Now' ) );

		wp.a11y.speak( errorMessage, 'assertive' );

		$document.trigger( 'wp-importer-install-error', response );
	};

	/**
	 * Sends an Ajax request to the server to delete a plugin.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object}               args         Arguments.
	 * @param {string}               args.plugin  Basename of the plugin to be deleted.
	 * @param {string}               args.slug    Slug of the plugin to be deleted.
	 * @param {deletePluginSuccess=} args.success Optional. Success callback. Default: wp.updates.deletePluginSuccess
	 * @param {deletePluginError=}   args.error   Optional. Error callback. Default: wp.updates.deletePluginError
	 * @return {$.promise} A jQuery promise that represents the request,
	 *                     decorated with an abort() method.
	 */
	wp.updates.deletePlugin = function( args ) {
		var $link = $( '[data-plugin="' + args.plugin + '"]' ).find( '.row-actions a.delete' );

		args = _.extend( {
			success: wp.updates.deletePluginSuccess,
			error: wp.updates.deletePluginError
		}, args );

		if ( $link.html() !== __( 'Deleting...' ) ) {
			$link
				.data( 'originaltext', $link.html() )
				.text( __( 'Deleting...' ) );
		}

		wp.a11y.speak( __( 'Deleting...' ) );

		$document.trigger( 'wp-plugin-deleting', args );

		return wp.updates.ajax( 'delete-plugin', args );
	};

	/**
	 * Updates the UI appropriately after a successful plugin deletion.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object} response            Response from the server.
	 * @param {string} response.slug       Slug of the plugin that was deleted.
	 * @param {string} response.plugin     Base name of the plugin that was deleted.
	 * @param {string} response.pluginName Name of the plugin that was deleted.
	 */
	wp.updates.deletePluginSuccess = function( response ) {

		// Removes the plugin and updates rows.
		$( '[data-plugin="' + response.plugin + '"]' ).css( { backgroundColor: '#faafaa' } ).fadeOut( 350, function() {
			var $form            = $( '#bulk-action-form' ),
				$views           = $( '.subsubsub' ),
				$pluginRow       = $( this ),
				$currentView     = $views.find( '[aria-current="page"]' ),
				$itemsCount      = $( '.displaying-num' ),
				columnCount      = $form.find( 'thead th:not(.hidden), thead td' ).length,
				pluginDeletedRow = wp.template( 'item-deleted-row' ),
				/**
				 * Plugins Base names of plugins in their different states.
				 *
				 * @type {Object}
				 */
				plugins          = settings.plugins,
				remainingCount;

			// Add a success message after deleting a plugin.
			if ( ! $pluginRow.hasClass( 'plugin-update-tr' ) ) {
				$pluginRow.after(
					pluginDeletedRow( {
						slug:    response.slug,
						plugin:  response.plugin,
						colspan: columnCount,
						name:    response.pluginName
					} )
				);
			}

			$pluginRow.remove();

			// Remove plugin from update count.
			if ( -1 !== _.indexOf( plugins.upgrade, response.plugin ) ) {
				plugins.upgrade = _.without( plugins.upgrade, response.plugin );
				wp.updates.decrementCount( 'plugin' );
			}

			// Remove from views.
			if ( -1 !== _.indexOf( plugins.inactive, response.plugin ) ) {
				plugins.inactive = _.without( plugins.inactive, response.plugin );
				if ( plugins.inactive.length ) {
					$views.find( '.inactive .count' ).text( '(' + plugins.inactive.length + ')' );
				} else {
					$views.find( '.inactive' ).remove();
				}
			}

			if ( -1 !== _.indexOf( plugins.active, response.plugin ) ) {
				plugins.active = _.without( plugins.active, response.plugin );
				if ( plugins.active.length ) {
					$views.find( '.active .count' ).text( '(' + plugins.active.length + ')' );
				} else {
					$views.find( '.active' ).remove();
				}
			}

			if ( -1 !== _.indexOf( plugins.recently_activated, response.plugin ) ) {
				plugins.recently_activated = _.without( plugins.recently_activated, response.plugin );
				if ( plugins.recently_activated.length ) {
					$views.find( '.recently_activated .count' ).text( '(' + plugins.recently_activated.length + ')' );
				} else {
					$views.find( '.recently_activated' ).remove();
				}
			}

			if ( -1 !== _.indexOf( plugins['auto-update-enabled'], response.plugin ) ) {
				plugins['auto-update-enabled'] = _.without( plugins['auto-update-enabled'], response.plugin );
				if ( plugins['auto-update-enabled'].length ) {
					$views.find( '.auto-update-enabled .count' ).text( '(' + plugins['auto-update-enabled'].length + ')' );
				} else {
					$views.find( '.auto-update-enabled' ).remove();
				}
			}

			if ( -1 !== _.indexOf( plugins['auto-update-disabled'], response.plugin ) ) {
				plugins['auto-update-disabled'] = _.without( plugins['auto-update-disabled'], response.plugin );
				if ( plugins['auto-update-disabled'].length ) {
					$views.find( '.auto-update-disabled .count' ).text( '(' + plugins['auto-update-disabled'].length + ')' );
				} else {
					$views.find( '.auto-update-disabled' ).remove();
				}
			}

			plugins.all = _.without( plugins.all, response.plugin );

			if ( plugins.all.length ) {
				$views.find( '.all .count' ).text( '(' + plugins.all.length + ')' );
			} else {
				$form.find( '.tablenav' ).css( { visibility: 'hidden' } );
				$views.find( '.all' ).remove();

				if ( ! $form.find( 'tr.no-items' ).length ) {
					$form.find( '#the-list' ).append( '<tr class="no-items"><td class="colspanchange" colspan="' + columnCount + '">' + __( 'No plugins are currently available.' ) + '</td></tr>' );
				}
			}

			if ( $itemsCount.length && $currentView.length ) {
				remainingCount = plugins[ $currentView.parent( 'li' ).attr('class') ].length;
				$itemsCount.text(
					sprintf(
						/* translators: %s: The remaining number of plugins. */
						_nx( '%s item', '%s items', 'plugin/plugins', remainingCount ),
						remainingCount
					)
				);
			}
		} );

		wp.a11y.speak( _x( 'Deleted!', 'plugin' ) );

		$document.trigger( 'wp-plugin-delete-success', response );
	};

	/**
	 * Updates the UI appropriately after a failed plugin deletion.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object}  response              Response from the server.
	 * @param {string}  response.slug         Slug of the plugin to be deleted.
	 * @param {string}  response.plugin       Base name of the plugin to be deleted
	 * @param {string=} response.pluginName   Optional. Name of the plugin to be deleted.
	 * @param {string}  response.errorCode    Error code for the error that occurred.
	 * @param {string}  response.errorMessage The error that occurred.
	 */
	wp.updates.deletePluginError = function( response ) {
		var $plugin, $pluginUpdateRow,
			pluginUpdateRow  = wp.template( 'item-update-row' ),
			noticeContent    = wp.updates.adminNotice( {
				className: 'update-message notice-error notice-alt',
				message:   response.errorMessage
			} );

		if ( response.plugin ) {
			$plugin          = $( 'tr.inactive[data-plugin="' + response.plugin + '"]' );
			$pluginUpdateRow = $plugin.siblings( '[data-plugin="' + response.plugin + '"]' );
		} else {
			$plugin          = $( 'tr.inactive[data-slug="' + response.slug + '"]' );
			$pluginUpdateRow = $plugin.siblings( '[data-slug="' + response.slug + '"]' );
		}

		if ( ! wp.updates.isValidResponse( response, 'delete' ) ) {
			return;
		}

		if ( wp.updates.maybeHandleCredentialError( response, 'delete-plugin' ) ) {
			return;
		}

		// Add a plugin update row if it doesn't exist yet.
		if ( ! $pluginUpdateRow.length ) {
			$plugin.addClass( 'update' ).after(
				pluginUpdateRow( {
					slug:    response.slug,
					plugin:  response.plugin || response.slug,
					colspan: $( '#bulk-action-form' ).find( 'thead th:not(.hidden), thead td' ).length,
					content: noticeContent
				} )
			);
		} else {

			// Remove previous error messages, if any.
			$pluginUpdateRow.find( '.notice-error' ).remove();

			$pluginUpdateRow.find( '.plugin-update' ).append( noticeContent );
		}

		$document.trigger( 'wp-plugin-delete-error', response );
	};

	/**
	 * Sends an Ajax request to the server to update a theme.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object}              args         Arguments.
	 * @param {string}              args.slug    Theme stylesheet.
	 * @param {updateThemeSuccess=} args.success Optional. Success callback. Default: wp.updates.updateThemeSuccess
	 * @param {updateThemeError=}   args.error   Optional. Error callback. Default: wp.updates.updateThemeError
	 * @return {$.promise} A jQuery promise that represents the request,
	 *                     decorated with an abort() method.
	 */
	wp.updates.updateTheme = function( args ) {
		var $notice;

		args = _.extend( {
			success: wp.updates.updateThemeSuccess,
			error: wp.updates.updateThemeError
		}, args );

		if ( 'themes-network' === pagenow ) {
			$notice = $( '[data-slug="' + args.slug + '"]' ).find( '.update-message' ).removeClass( 'notice-error' ).addClass( 'updating-message notice-warning' ).find( 'p' );

		} else if ( 'customize' === pagenow ) {

			// Update the theme details UI.
			$notice = $( '[data-slug="' + args.slug + '"].notice' ).removeClass( 'notice-large' );

			$notice.find( 'h3' ).remove();

			// Add the top-level UI, and update both.
			$notice = $notice.add( $( '#customize-control-installed_theme_' + args.slug ).find( '.update-message' ) );
			$notice = $notice.addClass( 'updating-message' ).find( 'p' );

		} else {
			$notice = $( '#update-theme' ).closest( '.notice' ).removeClass( 'notice-large' );

			$notice.find( 'h3' ).remove();

			$notice = $notice.add( $( '[data-slug="' + args.slug + '"]' ).find( '.update-message' ) );
			$notice = $notice.addClass( 'updating-message' ).find( 'p' );
		}

		if ( $notice.html() !== __( 'Updating...' ) ) {
			$notice.data( 'originaltext', $notice.html() );
		}

		wp.a11y.speak( __( 'Updating... please wait.' ) );
		$notice.text( __( 'Updating...' ) );

		$document.trigger( 'wp-theme-updating', args );

		return wp.updates.ajax( 'update-theme', args );
	};

	/**
	 * Updates the UI appropriately after a successful theme update.
	 *
	 * @since 4.6.0
	 * @since 5.5.0 Auto-update "time to next update" text cleared.
	 *
	 * @param {Object} response
	 * @param {string} response.slug       Slug of the theme to be updated.
	 * @param {Object} response.theme      Updated theme.
	 * @param {string} response.oldVersion Old version of the theme.
	 * @param {string} response.newVersion New version of the theme.
	 */
	wp.updates.updateThemeSuccess = function( response ) {
		var isModalOpen    = $( 'body.modal-open' ).length,
			$theme         = $( '[data-slug="' + response.slug + '"]' ),
			updatedMessage = {
				className: 'updated-message notice-success notice-alt',
				message:   _x( 'Updated!', 'theme' )
			},
			$notice, newText;

		if ( 'customize' === pagenow ) {
			$theme = $( '.updating-message' ).siblings( '.theme-name' );

			if ( $theme.length ) {

				// Update the version number in the row.
				newText = $theme.html().replace( response.oldVersion, response.newVersion );
				$theme.html( newText );
			}

			$notice = $( '.theme-info .notice' ).add( wp.customize.control( 'installed_theme_' + response.slug ).container.find( '.theme' ).find( '.update-message' ) );
		} else if ( 'themes-network' === pagenow ) {
			$notice = $theme.find( '.update-message' );

			// Update the version number in the row.
			newText = $theme.find( '.theme-version-author-uri' ).html().replace( response.oldVersion, response.newVersion );
			$theme.find( '.theme-version-author-uri' ).html( newText );

			// Clear the "time to next auto-update" text.
			$theme.find( '.auto-update-time' ).empty();
		} else {
			$notice = $( '.theme-info .notice' ).add( $theme.find( '.update-message' ) );

			// Focus on Customize button after updating.
			if ( isModalOpen ) {
				$( '.load-customize:visible' ).trigger( 'focus' );
				$( '.theme-info .theme-autoupdate' ).find( '.auto-update-time' ).empty();
			} else {
				$theme.find( '.load-customize' ).trigger( 'focus' );
			}
		}

		wp.updates.addAdminNotice( _.extend( { selector: $notice }, updatedMessage ) );
		wp.a11y.speak( __( 'Update completed successfully.' ) );

		wp.updates.decrementCount( 'theme' );

		$document.trigger( 'wp-theme-update-success', response );

		// Show updated message after modal re-rendered.
		if ( isModalOpen && 'customize' !== pagenow ) {
			$( '.theme-info .theme-author' ).after( wp.updates.adminNotice( updatedMessage ) );
		}
	};

	/**
	 * Updates the UI appropriately after a failed theme update.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object} response              Response from the server.
	 * @param {string} response.slug         Slug of the theme to be updated.
	 * @param {string} response.errorCode    Error code for the error that occurred.
	 * @param {string} response.errorMessage The error that occurred.
	 */
	wp.updates.updateThemeError = function( response ) {
		var $theme       = $( '[data-slug="' + response.slug + '"]' ),
			errorMessage = sprintf(
				/* translators: %s: Error string for a failed update. */
				 __( 'Update failed: %s' ),
				response.errorMessage
			),
			$notice;

		if ( ! wp.updates.isValidResponse( response, 'update' ) ) {
			return;
		}

		if ( wp.updates.maybeHandleCredentialError( response, 'update-theme' ) ) {
			return;
		}

		if ( 'customize' === pagenow ) {
			$theme = wp.customize.control( 'installed_theme_' + response.slug ).container.find( '.theme' );
		}

		if ( 'themes-network' === pagenow ) {
			$notice = $theme.find( '.update-message ' );
		} else {
			$notice = $( '.theme-info .notice' ).add( $theme.find( '.notice' ) );

			$( 'body.modal-open' ).length ? $( '.load-customize:visible' ).trigger( 'focus' ) : $theme.find( '.load-customize' ).trigger( 'focus');
		}

		wp.updates.addAdminNotice( {
			selector:  $notice,
			className: 'update-message notice-error notice-alt is-dismissible',
			message:   errorMessage
		} );

		wp.a11y.speak( errorMessage );

		$document.trigger( 'wp-theme-update-error', response );
	};

	/**
	 * Sends an Ajax request to the server to install a theme.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object}               args
	 * @param {string}               args.slug    Theme stylesheet.
	 * @param {installThemeSuccess=} args.success Optional. Success callback. Default: wp.updates.installThemeSuccess
	 * @param {installThemeError=}   args.error   Optional. Error callback. Default: wp.updates.installThemeError
	 * @return {$.promise} A jQuery promise that represents the request,
	 *                     decorated with an abort() method.
	 */
	wp.updates.installTheme = function( args ) {
		var $message = $( '.theme-install[data-slug="' + args.slug + '"]' );

		args = _.extend( {
			success: wp.updates.installThemeSuccess,
			error: wp.updates.installThemeError
		}, args );

		$message.addClass( 'updating-message' );
		$message.parents( '.theme' ).addClass( 'focus' );
		if ( $message.html() !== __( 'Installing...' ) ) {
			$message.data( 'originaltext', $message.html() );
		}

		$message
			.attr(
				'aria-label',
				sprintf(
					/* translators: %s: Theme name and version. */
					_x( 'Installing %s...', 'theme' ),
					$message.data( 'name' )
				)
			)
			.text( __( 'Installing...' ) );

		wp.a11y.speak( __( 'Installing... please wait.' ) );

		// Remove previous error messages, if any.
		$( '.install-theme-info, [data-slug="' + args.slug + '"]' ).removeClass( 'theme-install-failed' ).find( '.notice.notice-error' ).remove();

		$document.trigger( 'wp-theme-installing', args );

		return wp.updates.ajax( 'install-theme', args );
	};

	/**
	 * Updates the UI appropriately after a successful theme install.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object} response              Response from the server.
	 * @param {string} response.slug         Slug of the theme to be installed.
	 * @param {string} response.customizeUrl URL to the Customizer for the just installed theme.
	 * @param {string} response.activateUrl  URL to activate the just installed theme.
	 */
	wp.updates.installThemeSuccess = function( response ) {
		var $card = $( '.wp-full-overlay-header, [data-slug=' + response.slug + ']' ),
			$message;

		$document.trigger( 'wp-theme-install-success', response );

		$message = $card.find( '.button-primary' )
			.removeClass( 'updating-message' )
			.addClass( 'updated-message disabled' )
			.attr(
				'aria-label',
				sprintf(
					/* translators: %s: Theme name and version. */
					_x( '%s installed!', 'theme' ),
					response.themeName
				)
			)
			.text( _x( 'Installed!', 'theme' ) );

		wp.a11y.speak( __( 'Installation completed successfully.' ) );

		setTimeout( function() {

			if ( response.activateUrl ) {

				// Transform the 'Install' button into an 'Activate' button.
				$message
					.attr( 'href', response.activateUrl )
					.removeClass( 'theme-install updated-message disabled' )
					.addClass( 'activate' );

				if ( 'themes-network' === pagenow ) {
					$message
						.attr(
							'aria-label',
							sprintf(
								/* translators: %s: Theme name. */
								_x( 'Network Activate %s', 'theme' ),
								response.themeName
							)
						)
						.text( __( 'Network Enable' ) );
				} else {
					$message
						.attr(
							'aria-label',
							sprintf(
								/* translators: %s: Theme name. */
								_x( 'Activate %s', 'theme' ),
								response.themeName
							)
						)
						.text( __( 'Activate' ) );
				}
			}

			if ( response.customizeUrl ) {

				// Transform the 'Preview' button into a 'Live Preview' button.
				$message.siblings( '.preview' ).replaceWith( function () {
					return $( '<a>' )
						.attr( 'href', response.customizeUrl )
						.addClass( 'button load-customize' )
						.text( __( 'Live Preview' ) );
				} );
			}
		}, 1000 );
	};

	/**
	 * Updates the UI appropriately after a failed theme install.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object} response              Response from the server.
	 * @param {string} response.slug         Slug of the theme to be installed.
	 * @param {string} response.errorCode    Error code for the error that occurred.
	 * @param {string} response.errorMessage The error that occurred.
	 */
	wp.updates.installThemeError = function( response ) {
		var $card, $button,
			errorMessage = sprintf(
				/* translators: %s: Error string for a failed installation. */
				__( 'Installation failed: %s' ),
				response.errorMessage
			),
			$message     = wp.updates.adminNotice( {
				className: 'update-message notice-error notice-alt',
				message:   errorMessage
			} );

		if ( ! wp.updates.isValidResponse( response, 'install' ) ) {
			return;
		}

		if ( wp.updates.maybeHandleCredentialError( response, 'install-theme' ) ) {
			return;
		}

		if ( 'customize' === pagenow ) {
			if ( $document.find( 'body' ).hasClass( 'modal-open' ) ) {
				$button = $( '.theme-install[data-slug="' + response.slug + '"]' );
				$card   = $( '.theme-overlay .theme-info' ).prepend( $message );
			} else {
				$button = $( '.theme-install[data-slug="' + response.slug + '"]' );
				$card   = $button.closest( '.theme' ).addClass( 'theme-install-failed' ).append( $message );
			}
			wp.customize.notifications.remove( 'theme_installing' );
		} else {
			if ( $document.find( 'body' ).hasClass( 'full-overlay-active' ) ) {
				$button = $( '.theme-install[data-slug="' + response.slug + '"]' );
				$card   = $( '.install-theme-info' ).prepend( $message );
			} else {
				$card   = $( '[data-slug="' + response.slug + '"]' ).removeClass( 'focus' ).addClass( 'theme-install-failed' ).append( $message );
				$button = $card.find( '.theme-install' );
			}
		}

		$button
			.removeClass( 'updating-message' )
			.attr(
				'aria-label',
				sprintf(
					/* translators: %s: Theme name and version. */
					_x( '%s installation failed', 'theme' ),
					$button.data( 'name' )
				)
			)
			.text( __( 'Installation failed.' ) );

		wp.a11y.speak( errorMessage, 'assertive' );

		$document.trigger( 'wp-theme-install-error', response );
	};

	/**
	 * Sends an Ajax request to the server to delete a theme.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object}              args
	 * @param {string}              args.slug    Theme stylesheet.
	 * @param {deleteThemeSuccess=} args.success Optional. Success callback. Default: wp.updates.deleteThemeSuccess
	 * @param {deleteThemeError=}   args.error   Optional. Error callback. Default: wp.updates.deleteThemeError
	 * @return {$.promise} A jQuery promise that represents the request,
	 *                     decorated with an abort() method.
	 */
	wp.updates.deleteTheme = function( args ) {
		var $button;

		if ( 'themes' === pagenow ) {
			$button = $( '.theme-actions .delete-theme' );
		} else if ( 'themes-network' === pagenow ) {
			$button = $( '[data-slug="' + args.slug + '"]' ).find( '.row-actions a.delete' );
		}

		args = _.extend( {
			success: wp.updates.deleteThemeSuccess,
			error: wp.updates.deleteThemeError
		}, args );

		if ( $button && $button.html() !== __( 'Deleting...' ) ) {
			$button
				.data( 'originaltext', $button.html() )
				.text( __( 'Deleting...' ) );
		}

		wp.a11y.speak( __( 'Deleting...' ) );

		// Remove previous error messages, if any.
		$( '.theme-info .update-message' ).remove();

		$document.trigger( 'wp-theme-deleting', args );

		return wp.updates.ajax( 'delete-theme', args );
	};

	/**
	 * Updates the UI appropriately after a successful theme deletion.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object} response      Response from the server.
	 * @param {string} response.slug Slug of the theme that was deleted.
	 */
	wp.updates.deleteThemeSuccess = function( response ) {
		var $themeRows = $( '[data-slug="' + response.slug + '"]' );

		if ( 'themes-network' === pagenow ) {

			// Removes the theme and updates rows.
			$themeRows.css( { backgroundColor: '#faafaa' } ).fadeOut( 350, function() {
				var $views     = $( '.subsubsub' ),
					$themeRow  = $( this ),
					themes     = settings.themes,
					deletedRow = wp.template( 'item-deleted-row' );

				if ( ! $themeRow.hasClass( 'plugin-update-tr' ) ) {
					$themeRow.after(
						deletedRow( {
							slug:    response.slug,
							colspan: $( '#bulk-action-form' ).find( 'thead th:not(.hidden), thead td' ).length,
							name:    $themeRow.find( '.theme-title strong' ).text()
						} )
					);
				}

				$themeRow.remove();

				// Remove theme from update count.
				if ( -1 !== _.indexOf( themes.upgrade, response.slug ) ) {
					themes.upgrade = _.without( themes.upgrade, response.slug );
					wp.updates.decrementCount( 'theme' );
				}

				// Remove from views.
				if ( -1 !== _.indexOf( themes.disabled, response.slug ) ) {
					themes.disabled = _.without( themes.disabled, response.slug );
					if ( themes.disabled.length ) {
						$views.find( '.disabled .count' ).text( '(' + themes.disabled.length + ')' );
					} else {
						$views.find( '.disabled' ).remove();
					}
				}

				if ( -1 !== _.indexOf( themes['auto-update-enabled'], response.slug ) ) {
					themes['auto-update-enabled'] = _.without( themes['auto-update-enabled'], response.slug );
					if ( themes['auto-update-enabled'].length ) {
						$views.find( '.auto-update-enabled .count' ).text( '(' + themes['auto-update-enabled'].length + ')' );
					} else {
						$views.find( '.auto-update-enabled' ).remove();
					}
				}

				if ( -1 !== _.indexOf( themes['auto-update-disabled'], response.slug ) ) {
					themes['auto-update-disabled'] = _.without( themes['auto-update-disabled'], response.slug );
					if ( themes['auto-update-disabled'].length ) {
						$views.find( '.auto-update-disabled .count' ).text( '(' + themes['auto-update-disabled'].length + ')' );
					} else {
						$views.find( '.auto-update-disabled' ).remove();
					}
				}

				themes.all = _.without( themes.all, response.slug );

				// There is always at least one theme available.
				$views.find( '.all .count' ).text( '(' + themes.all.length + ')' );
			} );
		}

		wp.a11y.speak( _x( 'Deleted!', 'theme' ) );

		$document.trigger( 'wp-theme-delete-success', response );
	};

	/**
	 * Updates the UI appropriately after a failed theme deletion.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object} response              Response from the server.
	 * @param {string} response.slug         Slug of the theme to be deleted.
	 * @param {string} response.errorCode    Error code for the error that occurred.
	 * @param {string} response.errorMessage The error that occurred.
	 */
	wp.updates.deleteThemeError = function( response ) {
		var $themeRow    = $( 'tr.inactive[data-slug="' + response.slug + '"]' ),
			$button      = $( '.theme-actions .delete-theme' ),
			updateRow    = wp.template( 'item-update-row' ),
			$updateRow   = $themeRow.siblings( '#' + response.slug + '-update' ),
			errorMessage = sprintf(
				/* translators: %s: Error string for a failed deletion. */
				__( 'Deletion failed: %s' ),
				response.errorMessage
			),
			$message     = wp.updates.adminNotice( {
				className: 'update-message notice-error notice-alt',
				message:   errorMessage
			} );

		if ( wp.updates.maybeHandleCredentialError( response, 'delete-theme' ) ) {
			return;
		}

		if ( 'themes-network' === pagenow ) {
			if ( ! $updateRow.length ) {
				$themeRow.addClass( 'update' ).after(
					updateRow( {
						slug: response.slug,
						colspan: $( '#bulk-action-form' ).find( 'thead th:not(.hidden), thead td' ).length,
						content: $message
					} )
				);
			} else {
				// Remove previous error messages, if any.
				$updateRow.find( '.notice-error' ).remove();
				$updateRow.find( '.plugin-update' ).append( $message );
			}
		} else {
			$( '.theme-info .theme-description' ).before( $message );
		}

		$button.html( $button.data( 'originaltext' ) );

		wp.a11y.speak( errorMessage, 'assertive' );

		$document.trigger( 'wp-theme-delete-error', response );
	};

	/**
	 * Adds the appropriate callback based on the type of action and the current page.
	 *
	 * @since 4.6.0
	 * @private
	 *
	 * @param {Object} data   Ajax payload.
	 * @param {string} action The type of request to perform.
	 * @return {Object} The Ajax payload with the appropriate callbacks.
	 */
	wp.updates._addCallbacks = function( data, action ) {
		if ( 'import' === pagenow && 'install-plugin' === action ) {
			data.success = wp.updates.installImporterSuccess;
			data.error   = wp.updates.installImporterError;
		}

		return data;
	};

	/**
	 * Pulls available jobs from the queue and runs them.
	 *
	 * @since 4.2.0
	 * @since 4.6.0 Can handle multiple job types.
	 */
	wp.updates.queueChecker = function() {
		var job;

		if ( wp.updates.ajaxLocked || ! wp.updates.queue.length ) {
			return;
		}

		job = wp.updates.queue.shift();

		// Handle a queue job.
		switch ( job.action ) {
			case 'install-plugin':
				wp.updates.installPlugin( job.data );
				break;

			case 'update-plugin':
				wp.updates.updatePlugin( job.data );
				break;

			case 'delete-plugin':
				wp.updates.deletePlugin( job.data );
				break;

			case 'install-theme':
				wp.updates.installTheme( job.data );
				break;

			case 'update-theme':
				wp.updates.updateTheme( job.data );
				break;

			case 'delete-theme':
				wp.updates.deleteTheme( job.data );
				break;

			default:
				break;
		}
	};

	/**
	 * Requests the users filesystem credentials if they aren't already known.
	 *
	 * @since 4.2.0
	 *
	 * @param {Event=} event Optional. Event interface.
	 */
	wp.updates.requestFilesystemCredentials = function( event ) {
		if ( false === wp.updates.filesystemCredentials.available ) {
			/*
			 * After exiting the credentials request modal,
			 * return the focus to the element triggering the request.
			 */
			if ( event && ! wp.updates.$elToReturnFocusToFromCredentialsModal ) {
				wp.updates.$elToReturnFocusToFromCredentialsModal = $( event.target );
			}

			wp.updates.ajaxLocked = true;
			wp.updates.requestForCredentialsModalOpen();
		}
	};

	/**
	 * Requests the users filesystem credentials if needed and there is no lock.
	 *
	 * @since 4.6.0
	 *
	 * @param {Event=} event Optional. Event interface.
	 */
	wp.updates.maybeRequestFilesystemCredentials = function( event ) {
		if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.ajaxLocked ) {
			wp.updates.requestFilesystemCredentials( event );
		}
	};

	/**
	 * Keydown handler for the request for credentials modal.
	 *
	 * Closes the modal when the escape key is pressed and
	 * constrains keyboard navigation to inside the modal.
	 *
	 * @since 4.2.0
	 *
	 * @param {Event} event Event interface.
	 */
	wp.updates.keydown = function( event ) {
		if ( 27 === event.keyCode ) {
			wp.updates.requestForCredentialsModalCancel();
		} else if ( 9 === event.keyCode ) {

			// #upgrade button must always be the last focus-able element in the dialog.
			if ( 'upgrade' === event.target.id && ! event.shiftKey ) {
				$( '#hostname' ).trigger( 'focus' );

				event.preventDefault();
			} else if ( 'hostname' === event.target.id && event.shiftKey ) {
				$( '#upgrade' ).trigger( 'focus' );

				event.preventDefault();
			}
		}
	};

	/**
	 * Opens the request for credentials modal.
	 *
	 * @since 4.2.0
	 */
	wp.updates.requestForCredentialsModalOpen = function() {
		var $modal = $( '#request-filesystem-credentials-dialog' );

		$( 'body' ).addClass( 'modal-open' );
		$modal.show();
		$modal.find( 'input:enabled:first' ).trigger( 'focus' );
		$modal.on( 'keydown', wp.updates.keydown );
	};

	/**
	 * Closes the request for credentials modal.
	 *
	 * @since 4.2.0
	 */
	wp.updates.requestForCredentialsModalClose = function() {
		$( '#request-filesystem-credentials-dialog' ).hide();
		$( 'body' ).removeClass( 'modal-open' );

		if ( wp.updates.$elToReturnFocusToFromCredentialsModal ) {
			wp.updates.$elToReturnFocusToFromCredentialsModal.trigger( 'focus' );
		}
	};

	/**
	 * Takes care of the steps that need to happen when the modal is canceled out.
	 *
	 * @since 4.2.0
	 * @since 4.6.0 Triggers an event for callbacks to listen to and add their actions.
	 */
	wp.updates.requestForCredentialsModalCancel = function() {

		// Not ajaxLocked and no queue means we already have cleared things up.
		if ( ! wp.updates.ajaxLocked && ! wp.updates.queue.length ) {
			return;
		}

		_.each( wp.updates.queue, function( job ) {
			$document.trigger( 'credential-modal-cancel', job );
		} );

		// Remove the lock, and clear the queue.
		wp.updates.ajaxLocked = false;
		wp.updates.queue = [];

		wp.updates.requestForCredentialsModalClose();
	};

	/**
	 * Displays an error message in the request for credentials form.
	 *
	 * @since 4.2.0
	 *
	 * @param {string} message Error message.
	 */
	wp.updates.showErrorInCredentialsForm = function( message ) {
		var $filesystemForm = $( '#request-filesystem-credentials-form' );

		// Remove any existing error.
		$filesystemForm.find( '.notice' ).remove();
		$filesystemForm.find( '#request-filesystem-credentials-title' ).after( '<div class="notice notice-alt notice-error"><p>' + message + '</p></div>' );
	};

	/**
	 * Handles credential errors and runs events that need to happen in that case.
	 *
	 * @since 4.2.0
	 *
	 * @param {Object} response Ajax response.
	 * @param {string} action   The type of request to perform.
	 */
	wp.updates.credentialError = function( response, action ) {

		// Restore callbacks.
		response = wp.updates._addCallbacks( response, action );

		wp.updates.queue.unshift( {
			action: action,

			/*
			 * Not cool that we're depending on response for this data.
			 * This would feel more whole in a view all tied together.
			 */
			data: response
		} );

		wp.updates.filesystemCredentials.available = false;
		wp.updates.showErrorInCredentialsForm( response.errorMessage );
		wp.updates.requestFilesystemCredentials();
	};

	/**
	 * Handles credentials errors if it could not connect to the filesystem.
	 *
	 * @since 4.6.0
	 *
	 * @param {Object} response              Response from the server.
	 * @param {string} response.errorCode    Error code for the error that occurred.
	 * @param {string} response.errorMessage The error that occurred.
	 * @param {string} action                The type of request to perform.
	 * @return {boolean} Whether there is an error that needs to be handled or not.
	 */
	wp.updates.maybeHandleCredentialError = function( response, action ) {
		if ( wp.updates.shouldRequestFilesystemCredentials && response.errorCode && 'unable_to_connect_to_filesystem' === response.errorCode ) {
			wp.updates.credentialError( response, action );
			return true;
		}

		return false;
	};

	/**
	 * Validates an Ajax response to ensure it's a proper object.
	 *
	 * If the response deems to be invalid, an admin notice is being displayed.
	 *
	 * @param {(Object|string)} response              Response from the server.
	 * @param {function=}       response.always       Optional. Callback for when the Deferred is resolved or rejected.
	 * @param {string=}         response.statusText   Optional. Status message corresponding to the status code.
	 * @param {string=}         response.responseText Optional. Request response as text.
	 * @param {string}          action                Type of action the response is referring to. Can be 'delete',
	 *                                                'update' or 'install'.
	 */
	wp.updates.isValidResponse = function( response, action ) {
		var error = __( 'Something went wrong.' ),
			errorMessage;

		// Make sure the response is a valid data object and not a Promise object.
		if ( _.isObject( response ) && ! _.isFunction( response.always ) ) {
			return true;
		}

		if ( _.isString( response ) && '-1' === response ) {
			error = __( 'An error has occurred. Please reload the page and try again.' );
		} else if ( _.isString( response ) ) {
			error = response;
		} else if ( 'undefined' !== typeof response.readyState && 0 === response.readyState ) {
			error = __( 'Connection lost or the server is busy. Please try again later.' );
		} else if ( _.isString( response.responseText ) && '' !== response.responseText ) {
			error = response.responseText;
		} else if ( _.isString( response.statusText ) ) {
			error = response.statusText;
		}

		switch ( action ) {
			case 'update':
				/* translators: %s: Error string for a failed update. */
				errorMessage = __( 'Update failed: %s' );
				break;

			case 'install':
				/* translators: %s: Error string for a failed installation. */
				errorMessage = __( 'Installation failed: %s' );
				break;

			case 'delete':
				/* translators: %s: Error string for a failed deletion. */
				errorMessage = __( 'Deletion failed: %s' );
				break;
		}

		// Messages are escaped, remove HTML tags to make them more readable.
		error = error.replace( /<[\/a-z][^<>]*>/gi, '' );
		errorMessage = errorMessage.replace( '%s', error );

		// Add admin notice.
		wp.updates.addAdminNotice( {
			id:        'unknown_error',
			className: 'notice-error is-dismissible',
			message:   _.escape( errorMessage )
		} );

		// Remove the lock, and clear the queue.
		wp.updates.ajaxLocked = false;
		wp.updates.queue      = [];

		// Change buttons of all running updates.
		$( '.button.updating-message' )
			.removeClass( 'updating-message' )
			.removeAttr( 'aria-label' )
			.prop( 'disabled', true )
			.text( __( 'Update failed.' ) );

		$( '.updating-message:not(.button):not(.thickbox)' )
			.removeClass( 'updating-message notice-warning' )
			.addClass( 'notice-error' )
			.find( 'p' )
				.removeAttr( 'aria-label' )
				.text( errorMessage );

		wp.a11y.speak( errorMessage, 'assertive' );

		return false;
	};

	/**
	 * Potentially adds an AYS to a user attempting to leave the page.
	 *
	 * If an update is on-going and a user attempts to leave the page,
	 * opens an "Are you sure?" alert.
	 *
	 * @since 4.2.0
	 */
	wp.updates.beforeunload = function() {
		if ( wp.updates.ajaxLocked ) {
			return __( 'Updates may not complete if you navigate away from this page.' );
		}
	};

	$( function() {
		var $pluginFilter        = $( '#plugin-filter' ),
			$bulkActionForm      = $( '#bulk-action-form' ),
			$filesystemForm      = $( '#request-filesystem-credentials-form' ),
			$filesystemModal     = $( '#request-filesystem-credentials-dialog' ),
			$pluginSearch        = $( '.plugins-php .wp-filter-search' ),
			$pluginInstallSearch = $( '.plugin-install-php .wp-filter-search' );

		settings = _.extend( settings, window._wpUpdatesItemCounts || {} );

		if ( settings.totals ) {
			wp.updates.refreshCount();
		}

		/*
		 * Whether a user needs to submit filesystem credentials.
		 *
		 * This is based on whether the form was output on the page server-side.
		 *
		 * @see {wp_print_request_filesystem_credentials_modal() in PHP}
		 */
		wp.updates.shouldRequestFilesystemCredentials = $filesystemModal.length > 0;

		/**
		 * File system credentials form submit noop-er / handler.
		 *
		 * @since 4.2.0
		 */
		$filesystemModal.on( 'submit', 'form', function( event ) {
			event.preventDefault();

			// Persist the credentials input by the user for the duration of the page load.
			wp.updates.filesystemCredentials.ftp.hostname       = $( '#hostname' ).val();
			wp.updates.filesystemCredentials.ftp.username       = $( '#username' ).val();
			wp.updates.filesystemCredentials.ftp.password       = $( '#password' ).val();
			wp.updates.filesystemCredentials.ftp.connectionType = $( 'input[name="connection_type"]:checked' ).val();
			wp.updates.filesystemCredentials.ssh.publicKey      = $( '#public_key' ).val();
			wp.updates.filesystemCredentials.ssh.privateKey     = $( '#private_key' ).val();
			wp.updates.filesystemCredentials.fsNonce            = $( '#_fs_nonce' ).val();
			wp.updates.filesystemCredentials.available          = true;

			// Unlock and invoke the queue.
			wp.updates.ajaxLocked = false;
			wp.updates.queueChecker();

			wp.updates.requestForCredentialsModalClose();
		} );

		/**
		 * Closes the request credentials modal when clicking the 'Cancel' button or outside of the modal.
		 *
		 * @since 4.2.0
		 */
		$filesystemModal.on( 'click', '[data-js-action="close"], .notification-dialog-background', wp.updates.requestForCredentialsModalCancel );

		/**
		 * Hide SSH fields when not selected.
		 *
		 * @since 4.2.0
		 */
		$filesystemForm.on( 'change', 'input[name="connection_type"]', function() {
			$( '#ssh-keys' ).toggleClass( 'hidden', ( 'ssh' !== $( this ).val() ) );
		} ).trigger( 'change' );

		/**
		 * Handles events after the credential modal was closed.
		 *
		 * @since 4.6.0
		 *
		 * @param {Event}  event Event interface.
		 * @param {string} job   The install/update.delete request.
		 */
		$document.on( 'credential-modal-cancel', function( event, job ) {
			var $updatingMessage = $( '.updating-message' ),
				$message, originalText;

			if ( 'import' === pagenow ) {
				$updatingMessage.removeClass( 'updating-message' );
			} else if ( 'plugins' === pagenow || 'plugins-network' === pagenow ) {
				if ( 'update-plugin' === job.action ) {
					$message = $( 'tr[data-plugin="' + job.data.plugin + '"]' ).find( '.update-message' );
				} else if ( 'delete-plugin' === job.action ) {
					$message = $( '[data-plugin="' + job.data.plugin + '"]' ).find( '.row-actions a.delete' );
				}
			} else if ( 'themes' === pagenow || 'themes-network' === pagenow ) {
				if ( 'update-theme' === job.action ) {
					$message = $( '[data-slug="' + job.data.slug + '"]' ).find( '.update-message' );
				} else if ( 'delete-theme' === job.action && 'themes-network' === pagenow ) {
					$message = $( '[data-slug="' + job.data.slug + '"]' ).find( '.row-actions a.delete' );
				} else if ( 'delete-theme' === job.action && 'themes' === pagenow ) {
					$message = $( '.theme-actions .delete-theme' );
				}
			} else {
				$message = $updatingMessage;
			}

			if ( $message && $message.hasClass( 'updating-message' ) ) {
				originalText = $message.data( 'originaltext' );

				if ( 'undefined' === typeof originalText ) {
					originalText = $( '<p>' ).html( $message.find( 'p' ).data( 'originaltext' ) );
				}

				$message
					.removeClass( 'updating-message' )
					.html( originalText );

				if ( 'plugin-install' === pagenow || 'plugin-install-network' === pagenow ) {
					if ( 'update-plugin' === job.action ) {
						$message.attr(
							'aria-label',
							sprintf(
								/* translators: %s: Plugin name and version. */
								_x( 'Update %s now', 'plugin' ),
								$message.data( 'name' )
							)
						);
					} else if ( 'install-plugin' === job.action ) {
						$message.attr(
							'aria-label',
							sprintf(
								/* translators: %s: Plugin name. */
								_x( 'Install %s now', 'plugin' ),
								$message.data( 'name' )
							)
						);
					}
				}
			}

			wp.a11y.speak( __( 'Update canceled.' ) );
		} );

		/**
		 * Click handler for plugin updates in List Table view.
		 *
		 * @since 4.2.0
		 *
		 * @param {Event} event Event interface.
		 */
		$bulkActionForm.on( 'click', '[data-plugin] .update-link', function( event ) {
			var $message   = $( event.target ),
				$pluginRow = $message.parents( 'tr' );

			event.preventDefault();

			if ( $message.hasClass( 'updating-message' ) || $message.hasClass( 'button-disabled' ) ) {
				return;
			}

			wp.updates.maybeRequestFilesystemCredentials( event );

			// Return the user to the input box of the plugin's table row after closing the modal.
			wp.updates.$elToReturnFocusToFromCredentialsModal = $pluginRow.find( '.check-column input' );
			wp.updates.updatePlugin( {
				plugin: $pluginRow.data( 'plugin' ),
				slug:   $pluginRow.data( 'slug' )
			} );
		} );

		/**
		 * Click handler for plugin updates in plugin install view.
		 *
		 * @since 4.2.0
		 *
		 * @param {Event} event Event interface.
		 */
		$pluginFilter.on( 'click', '.update-now', function( event ) {
			var $button = $( event.target );
			event.preventDefault();

			if ( $button.hasClass( 'updating-message' ) || $button.hasClass( 'button-disabled' ) ) {
				return;
			}

			wp.updates.maybeRequestFilesystemCredentials( event );

			wp.updates.updatePlugin( {
				plugin: $button.data( 'plugin' ),
				slug:   $button.data( 'slug' )
			} );
		} );

		/**
		 * Click handler for plugin installs in plugin install view.
		 *
		 * @since 4.6.0
		 *
		 * @param {Event} event Event interface.
		 */
		$pluginFilter.on( 'click', '.install-now', function( event ) {
			var $button = $( event.target );
			event.preventDefault();

			if ( $button.hasClass( 'updating-message' ) || $button.hasClass( 'button-disabled' ) ) {
				return;
			}

			if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.ajaxLocked ) {
				wp.updates.requestFilesystemCredentials( event );

				$document.on( 'credential-modal-cancel', function() {
					var $message = $( '.install-now.updating-message' );

					$message
						.removeClass( 'updating-message' )
						.text( __( 'Install Now' ) );

					wp.a11y.speak( __( 'Update canceled.' ) );
				} );
			}

			wp.updates.installPlugin( {
				slug: $button.data( 'slug' )
			} );
		} );

		/**
		 * Click handler for importer plugins installs in the Import screen.
		 *
		 * @since 4.6.0
		 *
		 * @param {Event} event Event interface.
		 */
		$document.on( 'click', '.importer-item .install-now', function( event ) {
			var $button = $( event.target ),
				pluginName = $( this ).data( 'name' );

			event.preventDefault();

			if ( $button.hasClass( 'updating-message' ) ) {
				return;
			}

			if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.ajaxLocked ) {
				wp.updates.requestFilesystemCredentials( event );

				$document.on( 'credential-modal-cancel', function() {

					$button
						.removeClass( 'updating-message' )
						.attr(
							'aria-label',
							sprintf(
								/* translators: %s: Plugin name. */
								_x( 'Install %s now', 'plugin' ),
								pluginName
							)
						)
						.text( __( 'Install Now' ) );

					wp.a11y.speak( __( 'Update canceled.' ) );
				} );
			}

			wp.updates.installPlugin( {
				slug:    $button.data( 'slug' ),
				pagenow: pagenow,
				success: wp.updates.installImporterSuccess,
				error:   wp.updates.installImporterError
			} );
		} );

		/**
		 * Click handler for plugin deletions.
		 *
		 * @since 4.6.0
		 *
		 * @param {Event} event Event interface.
		 */
		$bulkActionForm.on( 'click', '[data-plugin] a.delete', function( event ) {
			var $pluginRow = $( event.target ).parents( 'tr' ),
				confirmMessage;

			if ( $pluginRow.hasClass( 'is-uninstallable' ) ) {
				confirmMessage = sprintf(
					/* translators: %s: Plugin name. */
					__( 'Are you sure you want to delete %s and its data?' ),
					$pluginRow.find( '.plugin-title strong' ).text()
				);
			} else {
				confirmMessage = sprintf(
					/* translators: %s: Plugin name. */
					__( 'Are you sure you want to delete %s?' ),
					$pluginRow.find( '.plugin-title strong' ).text()
				);
			}

			event.preventDefault();

			if ( ! window.confirm( confirmMessage ) ) {
				return;
			}

			wp.updates.maybeRequestFilesystemCredentials( event );

			wp.updates.deletePlugin( {
				plugin: $pluginRow.data( 'plugin' ),
				slug:   $pluginRow.data( 'slug' )
			} );

		} );

		/**
		 * Click handler for theme updates.
		 *
		 * @since 4.6.0
		 *
		 * @param {Event} event Event interface.
		 */
		$document.on( 'click', '.themes-php.network-admin .update-link', function( event ) {
			var $message  = $( event.target ),
				$themeRow = $message.parents( 'tr' );

			event.preventDefault();

			if ( $message.hasClass( 'updating-message' ) || $message.hasClass( 'button-disabled' ) ) {
				return;
			}

			wp.updates.maybeRequestFilesystemCredentials( event );

			// Return the user to the input box of the theme's table row after closing the modal.
			wp.updates.$elToReturnFocusToFromCredentialsModal = $themeRow.find( '.check-column input' );
			wp.updates.updateTheme( {
				slug: $themeRow.data( 'slug' )
			} );
		} );

		/**
		 * Click handler for theme deletions.
		 *
		 * @since 4.6.0
		 *
		 * @param {Event} event Event interface.
		 */
		$document.on( 'click', '.themes-php.network-admin a.delete', function( event ) {
			var $themeRow = $( event.target ).parents( 'tr' ),
				confirmMessage = sprintf(
					/* translators: %s: Theme name. */
					__( 'Are you sure you want to delete %s?' ),
					$themeRow.find( '.theme-title strong' ).text()
				);

			event.preventDefault();

			if ( ! window.confirm( confirmMessage ) ) {
				return;
			}

			wp.updates.maybeRequestFilesystemCredentials( event );

			wp.updates.deleteTheme( {
				slug: $themeRow.data( 'slug' )
			} );
		} );

		/**
		 * Bulk action handler for plugins and themes.
		 *
		 * Handles both deletions and updates.
		 *
		 * @since 4.6.0
		 *
		 * @param {Event} event Event interface.
		 */
		$bulkActionForm.on( 'click', '[type="submit"]:not([name="clear-recent-list"])', function( event ) {
			var bulkAction    = $( event.target ).siblings( 'select' ).val(),
				itemsSelected = $bulkActionForm.find( 'input[name="checked[]"]:checked' ),
				success       = 0,
				error         = 0,
				errorMessages = [],
				type, action;

			// Determine which type of item we're dealing with.
			switch ( pagenow ) {
				case 'plugins':
				case 'plugins-network':
					type = 'plugin';
					break;

				case 'themes-network':
					type = 'theme';
					break;

				default:
					return;
			}

			// Bail if there were no items selected.
			if ( ! itemsSelected.length ) {
				event.preventDefault();
				$( 'html, body' ).animate( { scrollTop: 0 } );

				return wp.updates.addAdminNotice( {
					id:        'no-items-selected',
					className: 'notice-error is-dismissible',
					message:   __( 'Please select at least one item to perform this action on.' )
				} );
			}

			// Determine the type of request we're dealing with.
			switch ( bulkAction ) {
				case 'update-selected':
					action = bulkAction.replace( 'selected', type );
					break;

				case 'delete-selected':
					var confirmMessage = 'plugin' === type ?
						__( 'Are you sure you want to delete the selected plugins and their data?' ) :
						__( 'Caution: These themes may be active on other sites in the network. Are you sure you want to proceed?' );

					if ( ! window.confirm( confirmMessage ) ) {
						event.preventDefault();
						return;
					}

					action = bulkAction.replace( 'selected', type );
					break;

				default:
					return;
			}

			wp.updates.maybeRequestFilesystemCredentials( event );

			event.preventDefault();

			// Un-check the bulk checkboxes.
			$bulkActionForm.find( '.manage-column [type="checkbox"]' ).prop( 'checked', false );

			$document.trigger( 'wp-' + type + '-bulk-' + bulkAction, itemsSelected );

			// Find all the checkboxes which have been checked.
			itemsSelected.each( function( index, element ) {
				var $checkbox = $( element ),
					$itemRow = $checkbox.parents( 'tr' );

				// Only add update-able items to the update queue.
				if ( 'update-selected' === bulkAction && ( ! $itemRow.hasClass( 'update' ) || $itemRow.find( 'notice-error' ).length ) ) {

					// Un-check the box.
					$checkbox.prop( 'checked', false );
					return;
				}

				// Add it to the queue.
				wp.updates.queue.push( {
					action: action,
					data:   {
						plugin: $itemRow.data( 'plugin' ),
						slug:   $itemRow.data( 'slug' )
					}
				} );
			} );

			// Display bulk notification for updates of any kind.
			$document.on( 'wp-plugin-update-success wp-plugin-update-error wp-theme-update-success wp-theme-update-error', function( event, response ) {
				var $itemRow = $( '[data-slug="' + response.slug + '"]' ),
					$bulkActionNotice, itemName;

				if ( 'wp-' + response.update + '-update-success' === event.type ) {
					success++;
				} else {
					itemName = response.pluginName ? response.pluginName : $itemRow.find( '.column-primary strong' ).text();

					error++;
					errorMessages.push( itemName + ': ' + response.errorMessage );
				}

				$itemRow.find( 'input[name="checked[]"]:checked' ).prop( 'checked', false );

				wp.updates.adminNotice = wp.template( 'wp-bulk-updates-admin-notice' );

				wp.updates.addAdminNotice( {
					id:            'bulk-action-notice',
					className:     'bulk-action-notice',
					successes:     success,
					errors:        error,
					errorMessages: errorMessages,
					type:          response.update
				} );

				$bulkActionNotice = $( '#bulk-action-notice' ).on( 'click', 'button', function() {
					// $( this ) is the clicked button, no need to get it again.
					$( this )
						.toggleClass( 'bulk-action-errors-collapsed' )
						.attr( 'aria-expanded', ! $( this ).hasClass( 'bulk-action-errors-collapsed' ) );
					// Show the errors list.
					$bulkActionNotice.find( '.bulk-action-errors' ).toggleClass( 'hidden' );
				} );

				if ( error > 0 && ! wp.updates.queue.length ) {
					$( 'html, body' ).animate( { scrollTop: 0 } );
				}
			} );

			// Reset admin notice template after #bulk-action-notice was added.
			$document.on( 'wp-updates-notice-added', function() {
				wp.updates.adminNotice = wp.template( 'wp-updates-admin-notice' );
			} );

			// Check the queue, now that the event handlers have been added.
			wp.updates.queueChecker();
		} );

		if ( $pluginInstallSearch.length ) {
			$pluginInstallSearch.attr( 'aria-describedby', 'live-search-desc' );
		}

		/**
		 * Handles changes to the plugin search box on the new-plugin page,
		 * searching the repository dynamically.
		 *
		 * @since 4.6.0
		 */
		$pluginInstallSearch.on( 'keyup input', _.debounce( function( event, eventtype ) {
			var $searchTab = $( '.plugin-install-search' ), data, searchLocation;

			data = {
				_ajax_nonce: wp.updates.ajaxNonce,
				s:           encodeURIComponent( event.target.value ),
				tab:         'search',
				type:        $( '#typeselector' ).val(),
				pagenow:     pagenow
			};
			searchLocation = location.href.split( '?' )[ 0 ] + '?' + $.param( _.omit( data, [ '_ajax_nonce', 'pagenow' ] ) );

			// Clear on escape.
			if ( 'keyup' === event.type && 27 === event.which ) {
				event.target.value = '';
			}

			if ( wp.updates.searchTerm === data.s && 'typechange' !== eventtype ) {
				return;
			} else {
				$pluginFilter.empty();
				wp.updates.searchTerm = data.s;
			}

			if ( window.history && window.history.replaceState ) {
				window.history.replaceState( null, '', searchLocation );
			}

			if ( ! $searchTab.length ) {
				$searchTab = $( '<li class="plugin-install-search" />' )
					.append( $( '<a />', {
						'class': 'current',
						'href': searchLocation,
						'text': __( 'Search Results' )
					} ) );

				$( '.wp-filter .filter-links .current' )
					.removeClass( 'current' )
					.parents( '.filter-links' )
					.prepend( $searchTab );

				$pluginFilter.prev( 'p' ).remove();
				$( '.plugins-popular-tags-wrapper' ).remove();
			}

			if ( 'undefined' !== typeof wp.updates.searchRequest ) {
				wp.updates.searchRequest.abort();
			}
			$( 'body' ).addClass( 'loading-content' );

			wp.updates.searchRequest = wp.ajax.post( 'search-install-plugins', data ).done( function( response ) {
				$( 'body' ).removeClass( 'loading-content' );
				$pluginFilter.append( response.items );
				delete wp.updates.searchRequest;

				if ( 0 === response.count ) {
					wp.a11y.speak( __( 'You do not appear to have any plugins available at this time.' ) );
				} else {
					wp.a11y.speak(
						sprintf(
							/* translators: %s: Number of plugins. */
							__( 'Number of plugins found: %d' ),
							response.count
						)
					);
				}
			} );
		}, 1000 ) );

		if ( $pluginSearch.length ) {
			$pluginSearch.attr( 'aria-describedby', 'live-search-desc' );
		}

		/**
		 * Handles changes to the plugin search box on the Installed Plugins screen,
		 * searching the plugin list dynamically.
		 *
		 * @since 4.6.0
		 */
		$pluginSearch.on( 'keyup input', _.debounce( function( event ) {
			var data = {
				_ajax_nonce:   wp.updates.ajaxNonce,
				s:             encodeURIComponent( event.target.value ),
				pagenow:       pagenow,
				plugin_status: 'all'
			},
			queryArgs;

			// Clear on escape.
			if ( 'keyup' === event.type && 27 === event.which ) {
				event.target.value = '';
			}

			if ( wp.updates.searchTerm === data.s ) {
				return;
			} else {
				wp.updates.searchTerm = data.s;
			}

			queryArgs = _.object( _.compact( _.map( location.search.slice( 1 ).split( '&' ), function( item ) {
				if ( item ) return item.split( '=' );
			} ) ) );

			data.plugin_status = queryArgs.plugin_status || 'all';

			if ( window.history && window.history.replaceState ) {
				window.history.replaceState( null, '', location.href.split( '?' )[ 0 ] + '?s=' + data.s + '&plugin_status=' + data.plugin_status );
			}

			if ( 'undefined' !== typeof wp.updates.searchRequest ) {
				wp.updates.searchRequest.abort();
			}

			$bulkActionForm.empty();
			$( 'body' ).addClass( 'loading-content' );
			$( '.subsubsub .current' ).removeClass( 'current' );

			wp.updates.searchRequest = wp.ajax.post( 'search-plugins', data ).done( function( response ) {

				// Can we just ditch this whole subtitle business?
				var $subTitle    = $( '<span />' ).addClass( 'subtitle' ).html(
					sprintf(
						/* translators: %s: Search query. */
						__( 'Search results for: %s' ),
						'<strong>' + _.escape( data.s ) + '</strong>'
					) ),
					$oldSubTitle = $( '.wrap .subtitle' );

				if ( ! data.s.length ) {
					$oldSubTitle.remove();
					$( '.subsubsub .' + data.plugin_status + ' a' ).addClass( 'current' );
				} else if ( $oldSubTitle.length ) {
					$oldSubTitle.replaceWith( $subTitle );
				} else {
					$( '.wp-header-end' ).before( $subTitle );
				}

				$( 'body' ).removeClass( 'loading-content' );
				$bulkActionForm.append( response.items );
				delete wp.updates.searchRequest;

				if ( 0 === response.count ) {
					wp.a11y.speak( __( 'No plugins found. Try a different search.'  ) );
				} else {
					wp.a11y.speak(
						sprintf(
							/* translators: %s: Number of plugins. */
							__( 'Number of plugins found: %d' ),
							response.count
						)
					);
				}
			} );
		}, 500 ) );

		/**
		 * Trigger a search event when the search form gets submitted.
		 *
		 * @since 4.6.0
		 */
		$document.on( 'submit', '.search-plugins', function( event ) {
			event.preventDefault();

			$( 'input.wp-filter-search' ).trigger( 'input' );
		} );

		/**
		 * Trigger a search event when the "Try Again" button is clicked.
		 *
		 * @since 4.9.0
		 */
		$document.on( 'click', '.try-again', function( event ) {
			event.preventDefault();
			$pluginInstallSearch.trigger( 'input' );
		} );

		/**
		 * Trigger a search event when the search type gets changed.
		 *
		 * @since 4.6.0
		 */
		$( '#typeselector' ).on( 'change', function() {
			var $search = $( 'input[name="s"]' );

			if ( $search.val().length ) {
				$search.trigger( 'input', 'typechange' );
			}
		} );

		/**
		 * Click handler for updating a plugin from the details modal on `plugin-install.php`.
		 *
		 * @since 4.2.0
		 *
		 * @param {Event} event Event interface.
		 */
		$( '#plugin_update_from_iframe' ).on( 'click', function( event ) {
			var target = window.parent === window ? null : window.parent,
				update;

			$.support.postMessage = !! window.postMessage;

			if ( false === $.support.postMessage || null === target || -1 !== window.parent.location.pathname.indexOf( 'update-core.php' ) ) {
				return;
			}

			event.preventDefault();

			update = {
				action: 'update-plugin',
				data:   {
					plugin: $( this ).data( 'plugin' ),
					slug:   $( this ).data( 'slug' )
				}
			};

			target.postMessage( JSON.stringify( update ), window.location.origin );
		} );

		/**
		 * Click handler for installing a plugin from the details modal on `plugin-install.php`.
		 *
		 * @since 4.6.0
		 *
		 * @param {Event} event Event interface.
		 */
		$( '#plugin_install_from_iframe' ).on( 'click', function( event ) {
			var target = window.parent === window ? null : window.parent,
				install;

			$.support.postMessage = !! window.postMessage;

			if ( false === $.support.postMessage || null === target || -1 !== window.parent.location.pathname.indexOf( 'index.php' ) ) {
				return;
			}

			event.preventDefault();

			install = {
				action: 'install-plugin',
				data:   {
					slug: $( this ).data( 'slug' )
				}
			};

			target.postMessage( JSON.stringify( install ), window.location.origin );
		} );

		/**
		 * Handles postMessage events.
		 *
		 * @since 4.2.0
		 * @since 4.6.0 Switched `update-plugin` action to use the queue.
		 *
		 * @param {Event} event Event interface.
		 */
		$( window ).on( 'message', function( event ) {
			var originalEvent  = event.originalEvent,
				expectedOrigin = document.location.protocol + '//' + document.location.host,
				message;

			if ( originalEvent.origin !== expectedOrigin ) {
				return;
			}

			try {
				message = JSON.parse( originalEvent.data );
			} catch ( e ) {
				return;
			}

			if ( ! message || 'undefined' === typeof message.action ) {
				return;
			}

			switch ( message.action ) {

				// Called from `wp-admin/includes/class-wp-upgrader-skins.php`.
				case 'decrementUpdateCount':
					/** @property {string} message.upgradeType */
					wp.updates.decrementCount( message.upgradeType );
					break;

				case 'install-plugin':
				case 'update-plugin':
					/* jscs:disable requireCamelCaseOrUpperCaseIdentifiers */
					window.tb_remove();
					/* jscs:enable */

					message.data = wp.updates._addCallbacks( message.data, message.action );

					wp.updates.queue.push( message );
					wp.updates.queueChecker();
					break;
			}
		} );

		/**
		 * Adds a callback to display a warning before leaving the page.
		 *
		 * @since 4.2.0
		 */
		$( window ).on( 'beforeunload', wp.updates.beforeunload );

		/**
		 * Prevents the page form scrolling when activating auto-updates with the Spacebar key.
		 *
		 * @since 5.5.0
		 */
		$document.on( 'keydown', '.column-auto-updates .toggle-auto-update, .theme-overlay .toggle-auto-update', function( event ) {
			if ( 32 === event.which ) {
				event.preventDefault();
			}
		} );

		/**
		 * Click and keyup handler for enabling and disabling plugin and theme auto-updates.
		 *
		 * These controls can be either links or buttons. When JavaScript is enabled,
		 * we want them to behave like buttons. An ARIA role `button` is added via
		 * the JavaScript that targets elements with the CSS class `aria-button-if-js`.
		 *
		 * @since 5.5.0
		 */
		$document.on( 'click keyup', '.column-auto-updates .toggle-auto-update, .theme-overlay .toggle-auto-update', function( event ) {
			var data, asset, type, $parent,
				$toggler = $( this ),
				action = $toggler.attr( 'data-wp-action' ),
				$label = $toggler.find( '.label' );

			if ( 'keyup' === event.type && 32 !== event.which ) {
				return;
			}

			if ( 'themes' !== pagenow ) {
				$parent = $toggler.closest( '.column-auto-updates' );
			} else {
				$parent = $toggler.closest( '.theme-autoupdate' );
			}

			event.preventDefault();

			// Prevent multiple simultaneous requests.
			if ( $toggler.attr( 'data-doing-ajax' ) === 'yes' ) {
				return;
			}

			$toggler.attr( 'data-doing-ajax', 'yes' );

			switch ( pagenow ) {
				case 'plugins':
				case 'plugins-network':
					type = 'plugin';
					asset = $toggler.closest( 'tr' ).attr( 'data-plugin' );
					break;
				case 'themes-network':
					type = 'theme';
					asset = $toggler.closest( 'tr' ).attr( 'data-slug' );
					break;
				case 'themes':
					type = 'theme';
					asset = $toggler.attr( 'data-slug' );
					break;
			}

			// Clear any previous errors.
			$parent.find( '.notice.notice-error' ).addClass( 'hidden' );

			// Show loading status.
			if ( 'enable' === action ) {
				$label.text( __( 'Enabling...' ) );
			} else {
				$label.text( __( 'Disabling...' ) );
			}

			$toggler.find( '.dashicons-update' ).removeClass( 'hidden' );

			data = {
				action: 'toggle-auto-updates',
				_ajax_nonce: settings.ajax_nonce,
				state: action,
				type: type,
				asset: asset
			};

			$.post( window.ajaxurl, data )
				.done( function( response ) {
					var $enabled, $disabled, enabledNumber, disabledNumber, errorMessage,
						href = $toggler.attr( 'href' );

					if ( ! response.success ) {
						// if WP returns 0 for response (which can happen in a few cases),
						// output the general error message since we won't have response.data.error.
						if ( response.data && response.data.error ) {
							errorMessage = response.data.error;
						} else {
							errorMessage = __( 'The request could not be completed.' );
						}

						$parent.find( '.notice.notice-error' ).removeClass( 'hidden' ).find( 'p' ).text( errorMessage );
						wp.a11y.speak( errorMessage, 'assertive' );
						return;
					}

					// Update the counts in the enabled/disabled views if on a screen
					// with a list table.
					if ( 'themes' !== pagenow ) {
						$enabled       = $( '.auto-update-enabled span' );
						$disabled      = $( '.auto-update-disabled span' );
						enabledNumber  = parseInt( $enabled.text().replace( /[^\d]+/g, '' ), 10 ) || 0;
						disabledNumber = parseInt( $disabled.text().replace( /[^\d]+/g, '' ), 10 ) || 0;

						switch ( action ) {
							case 'enable':
								++enabledNumber;
								--disabledNumber;
								break;
							case 'disable':
								--enabledNumber;
								++disabledNumber;
								break;
						}

						enabledNumber = Math.max( 0, enabledNumber );
						disabledNumber = Math.max( 0, disabledNumber );

						$enabled.text( '(' + enabledNumber + ')' );
						$disabled.text( '(' + disabledNumber + ')' );
					}

					if ( 'enable' === action ) {
						// The toggler control can be either a link or a button.
						if ( $toggler[ 0 ].hasAttribute( 'href' ) ) {
							href = href.replace( 'action=enable-auto-update', 'action=disable-auto-update' );
							$toggler.attr( 'href', href );
						}
						$toggler.attr( 'data-wp-action', 'disable' );

						$label.text( __( 'Disable auto-updates' ) );
						$parent.find( '.auto-update-time' ).removeClass( 'hidden' );
						wp.a11y.speak( __( 'Auto-updates enabled' ) );
					} else {
						// The toggler control can be either a link or a button.
						if ( $toggler[ 0 ].hasAttribute( 'href' ) ) {
							href = href.replace( 'action=disable-auto-update', 'action=enable-auto-update' );
							$toggler.attr( 'href', href );
						}
						$toggler.attr( 'data-wp-action', 'enable' );

						$label.text( __( 'Enable auto-updates' ) );
						$parent.find( '.auto-update-time' ).addClass( 'hidden' );
						wp.a11y.speak( __( 'Auto-updates disabled' ) );
					}

					$document.trigger( 'wp-auto-update-setting-changed', { state: action, type: type, asset: asset } );
				} )
				.fail( function() {
					$parent.find( '.notice.notice-error' )
						.removeClass( 'hidden' )
						.find( 'p' )
						.text( __( 'The request could not be completed.' ) );

					wp.a11y.speak( __( 'The request could not be completed.' ), 'assertive' );
				} )
				.always( function() {
					$toggler.removeAttr( 'data-doing-ajax' ).find( '.dashicons-update' ).addClass( 'hidden' );
				} );
			}
		);
	} );
})( jQuery, window.wp, window._wpUpdatesSettings );
