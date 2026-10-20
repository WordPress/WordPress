/**
 * Routes "Add New Media File" uploads through the client-side media pipeline.
 *
 * On wp-admin/media-new.php WordPress uploads via a raw plupload.Uploader
 * (created by plupload-handlers) posting to async-upload.php. When the
 * browser is cross-origin isolated and supports the client-side pipeline,
 * this script intercepts the uploader's FilesAdded handler and hands files
 * to wp.mediaUploadPipeline (media-upload-pipeline.js) instead: the
 * original image is uploaded via the REST API and thumbnails are generated
 * in the browser (wasm-vips), then sideloaded and finalized.
 *
 * The screen's existing UI helpers from plupload-handlers are reused:
 * fileQueued() builds the progress item, uploadSuccess() renders the
 * finished attachment row (via the async-upload.php markup endpoint), and
 * uploadComplete() runs when the queue drains. Failed uploads render the
 * same notice async-upload.php returns for a server-side failure, so the
 * screen looks and behaves unchanged.
 *
 * When client-side support is unavailable the script cleanly no-ops and
 * the classic plupload flow is left untouched.
 *
 * @output wp-admin/js/media-new-upload.js
 */

/* global plupload, pluploadL10n, wpUploaderInit, fileQueued, uploadStart, uploadSuccess, uploadComplete */

( function () {
	// Guard against double execution (e.g. duplicate enqueues).
	if ( window.__wpMediaNewUpload ) {
		return;
	}

	const pipeline = window.wp && wp.mediaUploadPipeline;

	// Bail unless the browser actually supports client-side processing. This
	// is the clean no-op: when the isolation headers did not land, classic
	// plupload keeps handling uploads.
	if (
		! pipeline ||
		typeof plupload === 'undefined' ||
		! wp.a11y ||
		! pipeline.configure()
	) {
		return;
	}

	window.__wpMediaNewUpload = true;

	const __ = wp.i18n.__;
	const sprintf = wp.i18n.sprintf;

	// Number of pipeline uploads currently in flight, used to fire
	// uploadComplete() once both the pipeline and plupload are done.
	let inFlightCount = 0;

	/**
	 * Marks one pipeline upload as finished, firing uploadComplete() when
	 * nothing is left uploading.
	 *
	 * The built-in UploadComplete binding never fires for pipeline uploads
	 * because every file is removed from plupload before its queue starts.
	 * Files plupload is still uploading (a batch's classic share, a HEIC
	 * handed back) end with plupload's own UploadComplete instead.
	 *
	 * @param {plupload.Uploader} up The plupload uploader.
	 */
	function finishUpload( up ) {
		inFlightCount--;
		if ( inFlightCount === 0 && up.state !== plupload.STARTED ) {
			uploadComplete();
		}
	}

	/**
	 * Returns the post the screen attaches uploads to, or 0.
	 *
	 * media-new.php validates `post_id` (the post must exist and be
	 * editable by the user) before printing it into the form, whereas the
	 * raw request value is what plupload carries in its multipart params.
	 * async-upload.php re-validates that raw value and silently uploads
	 * unattached; the REST API rejects it instead, so read the validated one.
	 *
	 * @return {number} The parent post ID, or 0.
	 */
	function getParentPostId() {
		const input = /** @type {HTMLInputElement|null} */ (
			document.getElementById( 'post_id' )
		);
		const postId = input ? parseInt( input.value, 10 ) : 0;
		return postId > 0 ? postId : 0;
	}

	/**
	 * Returns the screen's progress item for a plupload file.
	 *
	 * @param {plupload.File} file The plupload file.
	 * @return {HTMLElement|null} The item fileQueued() built, if it is still on the page.
	 */
	function getItem( file ) {
		return document.getElementById( 'media-item-' + file.id );
	}

	/**
	 * Renders a failed upload the way async-upload.php does for a
	 * server-side failure: an error notice with a real Dismiss button
	 * described by the notice, a screen reader announcement, and focus
	 * returned to the browse button once dismissed.
	 *
	 * @param {plupload.File} file    The plupload file that failed.
	 * @param {string}        message The reason the upload failed.
	 */
	function renderError( file, message ) {
		const item = getItem( file );
		if ( ! item ) {
			return;
		}

		const descriptionId = 'error-description-' + file.id;

		const button = document.createElement( 'button' );
		button.type = 'button';
		button.id = 'dismiss-' + file.id;
		button.className = 'dismiss button-link';
		button.setAttribute( 'aria-describedby', descriptionId );
		button.textContent = pluploadL10n.dismiss;

		const heading = document.createElement( 'strong' );
		heading.textContent = pluploadL10n.error_uploading.replace(
			'%s',
			file.name
		);

		const notice = document.createElement( 'div' );
		notice.id = descriptionId;
		notice.className = 'notice notice-error error-div error';
		notice.append(
			button,
			' ',
			heading,
			document.createElement( 'br' ),
			message
		);

		item.replaceChildren( notice );
		// Read by itemAjaxError() in plupload-handlers so a later server
		// error for the same file is not rendered twice.
		item.dataset.lastErr = file.id;

		setTimeout( function () {
			wp.a11y.speak(
				sprintf(
					/* translators: %s: Name of the file that failed to upload. */
					__( '%s has failed to upload.' ),
					file.name
				)
			);
		}, 1500 );

		button.addEventListener( 'click', function () {
			item.remove();
			wp.a11y.speak( __( 'Error dismissed.' ) );
			const browseButton = document.getElementById(
				'plupload-browse-button'
			);
			if ( browseButton ) {
				browseButton.focus();
			}
		} );
	}

	/**
	 * Reflects pipeline progress onto the screen's progress bar for a file.
	 *
	 * The bar is 200px wide at 100%, matching uploadProgress() in
	 * plupload-handlers.
	 *
	 * @param {plupload.File} file    The plupload file being uploaded.
	 * @param {number}        percent Progress percentage.
	 */
	function renderProgress( file, percent ) {
		const item = getItem( file );
		if ( ! item ) {
			return;
		}

		const bar = /** @type {HTMLElement|null} */ (
			item.querySelector( '.bar' )
		);
		if ( bar ) {
			bar.style.width = 2 * percent + 'px';
		}

		const label = item.querySelector( '.percent' );
		if ( label ) {
			label.textContent = percent + '%';
		}
	}

	/**
	 * Intercepts files added to the plupload uploader.
	 *
	 * Builds the screen's progress items for the files the pipeline takes
	 * (see wp.mediaUploadPipeline.routeBatch()). Returns undefined to leave
	 * the whole batch to plupload's built-in handler.
	 *
	 * @param {plupload.Uploader} up    The plupload uploader instance.
	 * @param {plupload.File[]}   files Files added to the queue.
	 * @return {boolean|undefined} False to suppress the built-in handler.
	 */
	function handleFilesAdded( up, files ) {
		return pipeline.routeBatch( up, files, {
			// Without the parent post a file uploaded from
			// media-new.php?post_id=N would land unattached even though the
			// screen counts it against that post.
			postId: getParentPostId(),
			onStart: function ( /** @type {plupload.File[]} */ queued ) {
				// Parity with the built-in handler: clear stale queue errors
				// and run the shared upload-start housekeeping.
				const queueErrors = document.getElementById( 'media-upload-error' );
				if ( queueErrors ) {
					queueErrors.replaceChildren();
				}
				uploadStart();

				// Count the whole batch first: addItems() can reject a file
				// synchronously, which must not drain the count mid-batch.
				inFlightCount += queued.length;
			},
			onQueue: function (
				/** @type {plupload.File} */ file,
				/** @type {File} */ nativeFile
			) {
				// Build the screen's progress item for this file.
				fileQueued( file );

				return {
					onSuccess: function (
						/** @type {PipelineAttachment} */ attachment
					) {
						// uploadSuccess() renders the finished attachment row
						// via the existing async-upload.php markup endpoint;
						// the server normally returns the ID as a string.
						uploadSuccess( file, String( attachment.id ) );
						finishUpload( up );
					},
					onError: function ( /** @type {UploadError} */ error ) {
						renderError(
							file,
							pipeline.getErrorText( error, nativeFile.name )
						);
						finishUpload( up );
					},
					// plupload builds its own progress item for the file it
					// took over, so this one goes away.
					onHandOff: function () {
						const item = getItem( file );
						if ( item ) {
							item.remove();
						}
						finishUpload( up );
					},
					onProgress: function ( /** @type {number} */ percent ) {
						renderProgress( file, percent );
					},
				};
			},
		} );
	}

	/**
	 * Binds the interceptor to an uploader once it has initialized.
	 *
	 * plupload sorts handlers by priority (descending) and a `false` return
	 * breaks the chain, so priority 100 runs before and suppresses the
	 * built-in FilesAdded and UploadComplete handlers.
	 *
	 * @param {plupload.Uploader} up The uploader that initialized.
	 */
	function bindUploader( up ) {
		if ( up.__wpMediaNewUploadBound ) {
			return;
		}
		up.__wpMediaNewUploadBound = true;

		up.bind(
			'FilesAdded',
			function ( up, files ) {
				return handleFilesAdded( up, files );
			},
			null,
			100
		);

		// Hold back the built-in uploadComplete() while pipeline uploads
		// are still running; finishUpload() fires it once they are done.
		up.bind(
			'UploadComplete',
			function () {
				return inFlightCount > 0 ? false : undefined;
			},
			null,
			100
		);
	}

	// plupload-handlers creates its uploader from the `wpUploaderInit`
	// settings once the DOM is ready, after this script has run, and
	// plupload binds the handlers in a settings `init` map while the
	// uploader initializes. Hooking PostInit there reaches the uploader
	// without depending on ready-callback ordering. The settings are
	// missing when the browser uploader is disabled (the html-uploader
	// fallback), in which case there is nothing to intercept.
	if ( typeof wpUploaderInit !== 'object' || ! wpUploaderInit ) {
		return;
	}

	// Either shape may already hold another script's handlers, which keep
	// running ahead of the interceptor being bound.
	const init = wpUploaderInit.init;
	if ( typeof init === 'function' ) {
		wpUploaderInit.init = function ( up ) {
			init( up );
			bindUploader( up );
		};
	} else {
		const postInit = init && init.PostInit;
		wpUploaderInit.init = Object.assign( {}, init, {
			PostInit: function (
				/** @type {plupload.Uploader} */ up,
				/** @type {unknown[]} */ ...args
			) {
				if ( typeof postInit === 'function' ) {
					postInit.call( this, up, ...args );
				}
				bindUploader( up );
			},
		} );
	}
} )();
