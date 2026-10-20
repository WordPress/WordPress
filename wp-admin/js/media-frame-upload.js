/**
 * Routes media frame uploads through the client-side media pipeline.
 *
 * Every wp.media frame - the Media Library grid on wp-admin/upload.php and
 * the media modal the block editor, the site editor and the block widgets
 * screen open - uploads via wp.Uploader / plupload to async-upload.php.
 * When the browser is cross-origin isolated and supports the client-side
 * pipeline, this script intercepts the uploader's FilesAdded handler and
 * hands files to wp.mediaUploadPipeline (media-upload-pipeline.js)
 * instead: the original image is uploaded via the REST API and thumbnails
 * are generated in the browser (wasm-vips), then sideloaded and finalized.
 *
 * The frame's own UI is reused: the same placeholder tiles, progress bars,
 * "Uploading n/m" status, and error list that wp-plupload.js drives.
 *
 * When client-side support is unavailable the script cleanly no-ops and
 * the classic plupload flow is left untouched.
 *
 * @output wp-admin/js/media-frame-upload.js
 */

/* global plupload */

( function () {
	// Guard against double execution (e.g. duplicate enqueues).
	if ( window.__wpMediaFrameUpload ) {
		return;
	}

	const pipeline = window.wp && wp.mediaUploadPipeline;

	// Bail unless the browser actually supports client-side processing. This
	// is the clean no-op: when the isolation headers did not land, classic
	// plupload keeps handling uploads.
	if (
		! pipeline ||
		typeof plupload === 'undefined' ||
		! wp.Uploader ||
		! wp.media ||
		! pipeline.configure()
	) {
		return;
	}

	window.__wpMediaFrameUpload = true;

	/**
	 * Resets the upload queue once every attachment has finished uploading.
	 *
	 * Parity with wp-plupload.js so browse mode flips back when done.
	 */
	function maybeResetQueue() {
		const complete = wp.Uploader.queue.all( function (
			/** @type {WPAttachment} */ attachment
		) {
			return ! attachment.get( 'uploading' );
		} );

		if ( complete ) {
			wp.Uploader.queue.reset();
		}
	}

	/**
	 * Handles a completed upload by syncing the grid tile with the server data.
	 *
	 * @param {WPUploader}         wpUploader The wp.Uploader instance that queued the file.
	 * @param {WPAttachment}       model      The placeholder Attachment model.
	 * @param {PipelineAttachment} attachment The finalized attachment from the pipeline.
	 */
	function handleSuccess( wpUploader, model, attachment ) {
		// Not silent: Attachments re-keys a model from its cid to its id when
		// it sees the change event. Without that the collection cannot find the
		// attachment by id, and refreshing the library duplicates its tile.
		model.set( { id: attachment.id } );

		// Register the model in Attachments.all (parity with wp-plupload.js).
		wp.media.model.Attachment.get( attachment.id, model );

		// Clear the uploading state before the refetch lands, not after. The
		// attachment details view re-renders on the refetch's changes, so it has
		// to see `uploading: false` by then or it keeps showing a progress bar.
		// wp-plupload.js sets both in one call for the same reason.
		[ 'file', 'loaded', 'size', 'percent' ].forEach( function ( key ) {
			model.unset( key, { silent: true } );
		} );
		model.set( { uploading: false }, { silent: true } );

		model
			.fetch()
			.fail( function () {
				// Fetch failed, but the upload succeeded: fill the tile with what
				// the pipeline gave us so it is not left empty.
				model.set( attachment );
			} )
			.always( function () {
				maybeResetQueue();

				// Parity with wp-plupload.js, which exposes this callback so
				// other code can react to a finished upload.
				wpUploader.success( model );
			} );
	}

	/**
	 * Handles an upload error by removing the tile and surfacing the message.
	 *
	 * The error goes into wp.Uploader.errors exactly like a classic upload
	 * error, so the grid's error sidebar renders it, announces it, and
	 * moves focus to its Dismiss button.
	 *
	 * @param {WPUploader}   wpUploader The wp.Uploader instance that queued the file.
	 * @param {WPAttachment} model      The placeholder Attachment model.
	 * @param {UploadError}  error      The upload error.
	 * @param {string}       fileName   Name of the file (for the error label).
	 */
	function handleError( wpUploader, model, error, fileName ) {
		const message = pipeline.getErrorText( error, fileName );
		const file = { name: fileName };

		model.destroy();

		wp.Uploader.errors.unshift( {
			message: message,
			data: {},
			file: file,
		} );

		maybeResetQueue();

		// Parity with wp-plupload.js, which exposes this callback so other
		// code can react to a failed upload.
		wpUploader.error( message, {}, file );
	}

	/**
	 * Whether a plupload uploader posts attachments the way wp.Uploader does
	 * by default.
	 *
	 * The init wrapper below reaches every wp.Uploader built on the page,
	 * and a plugin can build one for its own purposes. Core's defaults tell
	 * an attachment upload apart: it posts to async-upload.php with the
	 * `upload-attachment` action. Anything a plugin pointed elsewhere, or
	 * gave another action, is uploading something else and is left alone.
	 *
	 * @param {plupload.Uploader} up The plupload uploader instance.
	 * @return {boolean} True when the uploader posts attachments.
	 */
	function isAttachmentUploader( up ) {
		const settings = up.settings || {};
		const params = settings.multipart_params || {};
		const url = new URL( settings.url || '', location.href );

		return (
			'upload-attachment' === params.action &&
			url.pathname.endsWith( 'async-upload.php' )
		);
	}

	/**
	 * Intercepts files added to a plupload uploader.
	 *
	 * Builds the same placeholder tiles as wp-plupload for the files the
	 * pipeline takes (see wp.mediaUploadPipeline.routeBatch()). Returns
	 * undefined to leave the whole batch to plupload's built-in handler.
	 *
	 * @param {WPUploader}        wpUploader The wp.Uploader instance.
	 * @param {plupload.Uploader} up         The plupload uploader instance.
	 * @param {plupload.File[]}   files      Files added to the queue.
	 * @return {boolean|undefined} False to suppress the built-in handler.
	 */
	function handleFilesAdded( wpUploader, up, files ) {
		if ( ! isAttachmentUploader( up ) ) {
			return;
		}

		// The classic flow attaches to `post_id` the same way, and rejects
		// a post the user cannot edit just as the REST API does.
		const params = ( up.settings && up.settings.multipart_params ) || {};

		return pipeline.routeBatch( up, files, {
			postId: parseInt( params.post_id, 10 ) || 0,
			onQueue: function (
				/** @type {plupload.File} */ file,
				/** @type {File} */ nativeFile
			) {
				// Build the same placeholder attributes as wp-plupload.js so
				// the grid's progress tiles and "Uploading n/m" status work
				// unchanged.
				/** @type {PlaceholderAttributes} */
				const attributes = {
					file: file,
					uploading: true,
					date: new Date(),
					filename: file.name,
					menuOrder: 0,
					uploadedTo: wp.media.model.settings.post.id,
					loaded: file.loaded,
					size: file.size,
					percent: file.percent,
				};

				/*
				 * Early mime type scanning for images, as wp-plupload.js does,
				 * extended with the formats the client-side pipeline accepts.
				 */
				const image = /(?:jpe?g|png|gif|webp|avif|heic|heif)$/i.exec(
					file.name
				);
				if ( image ) {
					attributes.type = 'image';
					// `jpg` is not a valid subtype, so map it to `jpeg`.
					const subtype = image[ 0 ].toLowerCase();
					attributes.subtype = 'jpg' === subtype ? 'jpeg' : subtype;
				}

				const model = wp.media.model.Attachment.create( attributes );
				wp.Uploader.queue.add( model );
				wpUploader.added( model );

				return {
					onSuccess: function (
						/** @type {PipelineAttachment} */ attachment
					) {
						handleSuccess( wpUploader, model, attachment );
					},
					onError: function ( /** @type {UploadError} */ error ) {
						handleError( wpUploader, model, error, nativeFile.name );
					},
					// plupload builds its own tile for the file it took over.
					onHandOff: function () {
						model.destroy();
					},
					onProgress: function ( /** @type {number} */ percent ) {
						model.set( { percent: percent } );
						wpUploader.progress( model );
					},
				};
			},
		} );
	}

	// Wrap wp.Uploader.prototype.init (an empty stub called once per instance
	// after plupload is initialized) to bind a higher-priority FilesAdded
	// handler on every uploader instance: the grid's, and the one each media
	// modal creates when it opens.
	const originalInit = wp.Uploader.prototype.init;
	wp.Uploader.prototype.init = function () {
		originalInit.apply( this, arguments );

		const wpUploader = /** @type {WPUploader} */ ( this );
		const up = /** @type {plupload.Uploader|undefined} */ ( this.uploader );

		if ( ! up || up.__wpMediaFrameUploadBound ) {
			return;
		}
		up.__wpMediaFrameUploadBound = true;

		// plupload sorts handlers by priority (descending) and a `false`
		// return breaks the chain, so priority 100 runs before and suppresses
		// the built-in FilesAdded handler.
		up.bind(
			'FilesAdded',
			function ( uploader, files ) {
				return handleFilesAdded( wpUploader, uploader, files );
			},
			this,
			100
		);
	};
} )();
