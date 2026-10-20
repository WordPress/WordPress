/**
 * Shared glue for routing classic admin uploads through the client-side
 * media pipeline.
 *
 * The media frames (media-frame-upload.js) and the Add New Media
 * File screen (media-new-upload.js) both intercept plupload and hand files
 * to @wordpress/upload-media instead. Everything that is not specific to
 * one screen's UI lives here: feature detection, configuring the
 * upload-media store once, the REST sideload/finalize/delete helpers the
 * store needs, queueing a file with success, error, and progress
 * callbacks, the error text shown for a failed upload, and the guard that
 * warns before leaving while uploads are in flight.
 *
 * Exposed as wp.mediaUploadPipeline.
 *
 * @output wp-admin/js/media-upload-pipeline.js
 */

/* global plupload */

window.wp = window.wp || {};

( function ( wp ) {
	const __ = wp.i18n.__;
	const settings = window._wpMediaUploadPipelineSettings || {};
	/** @type {any} */
	let uploadStore;
	let configured = false;

	// Uploads in flight, keyed by the onSuccess callback queueFile() passes
	// to addItems(). The store keeps that callback on the queue item, so it
	// identifies the item even after HEIC conversion swaps its file.
	const entries = new Map();

	// Files handed back to classic plupload after the pipeline gave up on
	// them, so the interceptors let plupload's own handler take them.
	const classicFiles = new WeakSet();

	const imageSizeCount = Object.keys( settings.allImageSizes || {} ).length;

	/**
	 * Recursively appends data to a FormData object, supporting nested objects.
	 *
	 * Mirrors flattenFormData() in the media-utils package.
	 *
	 * @param {FormData} formData The form data to append to.
	 * @param {string}   key      The key to append under.
	 * @param {*}        data     The value to append.
	 */
	function flattenFormData( formData, key, data ) {
		if (
			data !== null &&
			typeof data === 'object' &&
			Object.getPrototypeOf( data ) === Object.prototype
		) {
			Object.keys( data ).forEach( function ( name ) {
				flattenFormData( formData, key + '[' + name + ']', data[ name ] );
			} );
		} else if ( data !== undefined ) {
			formData.append( key, String( data ) );
		}
	}

	/**
	 * Sideloads a client-generated thumbnail to an existing attachment.
	 *
	 * Reimplements the private sideloadMedia() helper from the media-utils
	 * package as a thin apiFetch wrapper.
	 *
	 * @param {Object}                      args                  The sideload arguments.
	 * @param {File}                        args.file             The sub-size to sideload.
	 * @param {number}                      args.attachmentId     The attachment to sideload it to.
	 * @param {Record<string, unknown>}     [args.additionalData] Extra fields to send with it.
	 * @param {AbortSignal}                 [args.signal]         Signal aborting the request.
	 * @param {( subSize: Object ) => void} [args.onSuccess]      Called with the sideloaded sub-size.
	 * @param {( error: Error ) => void}    [args.onError]        Called when the sideload failed.
	 */
	function mediaSideload( args ) {
		const file = args.file;
		const additionalData = args.additionalData || {};

		const data = new FormData();
		data.append( 'file', file, file.name || file.type.replace( '/', '.' ) );
		Object.keys( additionalData ).forEach( function ( key ) {
			flattenFormData( data, key, additionalData[ key ] );
		} );

		wp.apiFetch( {
			path: '/wp/v2/media/' + args.attachmentId + '/sideload',
			body: data,
			method: 'POST',
			signal: args.signal,
		} )
			.then( function ( /** @type {Object} */ subSize ) {
				if ( args.onSuccess ) {
					args.onSuccess( subSize );
				}
			} )
			.catch( function ( /** @type {unknown} */ error ) {
				if ( args.onError ) {
					let normalized = error;
					if ( ! ( error instanceof Error ) ) {
						const rest = /** @type {UploadError} */ ( error );
						normalized = new Error(
							rest && rest.message ? rest.message : String( error )
						);
					}
					args.onError( /** @type {Error} */ ( normalized ) );
				}
			} );
	}

	/**
	 * Finalizes an upload once all client-side processing is complete.
	 *
	 * Reimplements the private mediaFinalize() helper. The returned
	 * attachment is load-bearing: it carries the post-finalize (scaled)
	 * URL used for srcset.
	 *
	 * @param {number}   id       The parent attachment ID.
	 * @param {Object[]} subSizes Accumulated sub-size data.
	 * @return {Promise<PipelineAttachment|undefined>} Resolves with the transformed attachment.
	 */
	async function mediaFinalize( id, subSizes ) {
		/** @type {Object|undefined} */
		const response = await wp.apiFetch( {
			path: '/wp/v2/media/' + id + '/finalize',
			method: 'POST',
			data: { sub_sizes: subSizes || [] },
		} );
		if ( ! response ) {
			return undefined;
		}
		return wp.mediaUtils.transformAttachment( response );
	}

	/**
	 * Deletes an attachment whose client-side processing failed outright.
	 *
	 * The queue calls this when every sub-size sideload for an upload fails:
	 * without it the original file is left behind as an attachment with no
	 * metadata, visible in the Media Library after the next page load. The
	 * block editor passes the same setting.
	 *
	 * @param {number} id The attachment ID to delete.
	 * @return {Promise<unknown>} Resolves once the attachment is deleted.
	 */
	function mediaDelete( id ) {
		return wp.apiFetch( {
			path: '/wp/v2/media/' + id + '?force=true',
			method: 'DELETE',
		} );
	}

	/**
	 * Whether every script the pipeline relies on is present.
	 *
	 * @return {boolean} True when the dependencies loaded.
	 */
	function hasDependencies() {
		return Boolean(
			typeof plupload !== 'undefined' &&
				wp.uploadMedia &&
				wp.mediaUtils &&
				wp.data &&
				wp.element &&
				wp.apiFetch
		);
	}

	/**
	 * Whether the browser can run the client-side pipeline on this page.
	 *
	 * False when the isolation headers did not land (the page is not
	 * cross-origin isolated) or the browser lacks the required features,
	 * in which case classic plupload keeps handling uploads.
	 *
	 * @return {boolean} True when client-side processing is available.
	 */
	function isSupported() {
		return Boolean(
			hasDependencies() &&
				wp.uploadMedia.detectClientSideMediaSupport &&
				wp.uploadMedia.detectClientSideMediaSupport().supported
		);
	}

	/**
	 * Estimates the progress (0-100) of a queue item.
	 *
	 * The pipeline never reports a numeric `progress` on its queue items
	 * (nothing dispatches updateItemProgress), so estimate one from the
	 * item's operation queue instead: each finished operation (prepare,
	 * transcode, upload, thumbnails, finalize) advances the bar, and the
	 * sub-sizes sideloaded so far advance it within thumbnail generation.
	 * `item.progress` is preferred whenever it is present.
	 *
	 * @param {QueueItem}   item  The upload-media queue item.
	 * @param {UploadEntry} entry The pipeline's bookkeeping for the upload.
	 * @return {number} Estimated progress.
	 */
	function estimateProgress( item, entry ) {
		if ( typeof item.progress === 'number' ) {
			return item.progress;
		}

		const remaining = item.operations ? item.operations.length : 0;
		let totals = entry.totals;
		if ( ! totals ) {
			totals = { total: remaining, remaining: remaining };
			entry.totals = totals;
		}
		// Operations are appended after preparation, so grow the total.
		if ( remaining > totals.remaining ) {
			totals.total += remaining - totals.remaining;
		}
		totals.remaining = remaining;

		if ( totals.total === 0 ) {
			return 0;
		}

		const completed = totals.total - remaining;
		let fraction = 0;
		if (
			'THUMBNAIL_GENERATION' === item.currentOperation &&
			imageSizeCount > 0
		) {
			fraction = Math.min(
				1,
				( item.subSizes || [] ).length / imageSizeCount
			);
		}

		return ( ( completed + fraction ) / totals.total ) * 100;
	}

	/**
	 * Tracks queued uploads on every change to the upload-media store.
	 *
	 * Reports progress, which holds at 99 until the upload's success
	 * callback has run so nothing looks finished before the screen has
	 * synced the result, and notes the attachment once the original is
	 * uploaded. Sub-size children are skipped; only top-level items matter.
	 *
	 * The store can also remove an item without calling either of its
	 * callbacks (it only calls onSuccess when the item has an attachment),
	 * so an upload that was seen in the store and is gone fails here
	 * instead of staying in flight, and holding the unload guard, for good.
	 */
	function onStoreChange() {
		if ( entries.size === 0 ) {
			return;
		}

		const present = new Set();
		const items = wp.data.select( uploadStore ).getItems();
		items.forEach( function ( /** @type {QueueItem} */ item ) {
			if ( item.parentId ) {
				return;
			}

			const entry = entries.get( item.onSuccess );
			if ( ! entry ) {
				return;
			}

			present.add( item.onSuccess );
			entry.seen = true;
			if ( item.attachment && item.attachment.id ) {
				entry.attachmentId = item.attachment.id;
			}

			if ( ! entry.onProgress ) {
				return;
			}

			const percent = Math.min(
				99,
				Math.round( estimateProgress( item, entry ) )
			);
			if ( percent !== entry.lastPercent ) {
				entry.lastPercent = percent;
				entry.onProgress( percent );
			}
		} );

		entries.forEach( function ( entry, key ) {
			if ( entry.seen && ! present.has( key ) ) {
				entry.fail( {
					message: __( 'An error occurred while uploading the file.' ),
				} );
			}
		} );
	}

	/**
	 * Whether something else already configured the store on this page.
	 *
	 * The block editor renders its own MediaUploadProvider into the same
	 * store. Neither `mediaSideload` nor `mediaFinalize` has a default, so
	 * their presence proves a provider's settings landed, whichever one
	 * rendered them.
	 *
	 * @return {boolean} True when the store carries a provider's settings.
	 */
	function isStoreConfigured() {
		const storeSettings = wp.data.select( uploadStore ).getSettings();
		return Boolean(
			storeSettings &&
				storeSettings.mediaSideload &&
				storeSettings.mediaFinalize
		);
	}

	/**
	 * Configures the upload-media store for this page, once.
	 *
	 * Rendering the provider with useSubRegistry: false wires the settings
	 * into the store that wp.data.dispatch/select address. The block editor
	 * does the same for its own uploads, so on its screens no second
	 * provider is rendered: whichever committed last would win, and this
	 * one could replace the editor's mediaUpload. Uploads wait for the
	 * editor's settings instead (see isReady()).
	 *
	 * @return {boolean} True when the pipeline is configured and usable.
	 */
	function configure() {
		if ( configured ) {
			return true;
		}

		if ( ! isSupported() ) {
			return false;
		}

		configured = true;
		uploadStore = wp.uploadMedia.store;

		if ( settings.isBlockEditor || isStoreConfigured() ) {
			wp.data.subscribe( onStoreChange, uploadStore );
			addUnloadGuard();
			return true;
		}

		/*
		 * The media-utils package branches on this flag: without it
		 * uploadMedia() creates and revokes a throwaway blob URL per file
		 * and emits an extra onFileChange carrying it. The block editor
		 * sets the same flag before configuring the same pipeline.
		 */
		window.__clientSideMediaProcessing = true;

		wp.element
			.createRoot( document.createElement( 'div' ) )
			.render(
				wp.element.createElement( wp.uploadMedia.MediaUploadProvider, {
					settings: {
						mediaUpload: wp.mediaUtils.uploadMedia,
						mediaSideload: mediaSideload,
						mediaFinalize: mediaFinalize,
						mediaDelete: mediaDelete,
						maxUploadFileSize: settings.maxUploadFileSize,
						allowedMimeTypes: settings.allowedMimeTypes,
						allImageSizes: settings.allImageSizes,
						bigImageSizeThreshold: settings.bigImageSizeThreshold,
						imageStripMeta: settings.imageStripMeta,
						imageMaxBitDepth: settings.imageMaxBitDepth,
						generateAnimatedImageSubsizes:
							settings.generateAnimatedImageSubsizes,
					},
					useSubRegistry: false,
				} )
			);

		// Only the upload-media store can change what this listener reads.
		wp.data.subscribe( onStoreChange, uploadStore );
		addUnloadGuard();

		return true;
	}

	/**
	 * Warns before leaving while uploads are in flight.
	 *
	 * Thumbnails that have not been sideloaded yet are lost and the
	 * attachment is left unfinalized, unlike classic uploads that complete
	 * server-side once the bytes arrive.
	 */
	function addUnloadGuard() {
		window.addEventListener( 'beforeunload', function ( event ) {
			if ( entries.size > 0 ) {
				event.preventDefault();
			}
		} );
	}

	/**
	 * Whether the store has received a provider's settings.
	 *
	 * A provider renders asynchronously, so a file added before its
	 * settings land must be left to classic plupload - a degradation,
	 * never data loss. The store's default state already carries a no-op
	 * `mediaUpload`, so only settings a provider alone supplies prove the
	 * real ones have landed: a file queued before that would be handed to
	 * the no-op and never upload. On block editor screens only the
	 * editor's provider supplies them.
	 *
	 * @return {boolean} True when the store is ready to accept files.
	 */
	function isReady() {
		return configured && isStoreConfigured();
	}

	/**
	 * Splits a batch of plupload files between the pipeline and classic
	 * plupload.
	 *
	 * Audio files stay classic so they keep the title and description that
	 * media_handle_upload() derives from their ID3 tags, which the REST
	 * endpoint does not do. So do JPEG and PNG images while plupload's own
	 * client-side resize is enabled (the "Scale images" setting, or a
	 * `resize` passed through `plupload_init`): plupload scales those down
	 * before uploading them, the pipeline would not.
	 *
	 * The whole batch stays classic when a file cannot be re-queued on
	 * plupload or is already plupload's: plupload returns no native File
	 * for sources it cannot expose as one (the html4 runtime, say), and a
	 * file the pipeline handed back (see handOffToClassic()) is plupload's
	 * to upload.
	 *
	 * @param {plupload.Uploader} up    The plupload uploader the files were added to.
	 * @param {plupload.File[]}   files Files added to the plupload queue.
	 * @return {{ pipeline: plupload.File[], classic: plupload.File[] }|null} The split,
	 *         or null when the whole batch stays on the classic path.
	 */
	function partitionBatch( up, files ) {
		const resize = up.settings && up.settings.resize;
		const isResizing = Boolean( resize && resize.enabled );
		/** @type {{ pipeline: plupload.File[], classic: plupload.File[] }} */
		const batch = { pipeline: [], classic: [] };

		for ( const file of files ) {
			if ( plupload.FAILED === file.status ) {
				continue;
			}
			const nativeFile = file.getNative && file.getNative();
			if ( ! nativeFile || classicFiles.has( nativeFile ) ) {
				return null;
			}
			const type = file.type || '';
			if (
				/^audio\//i.test( type ) ||
				( isResizing && /^image\/(?:jpeg|png)$/i.test( type ) )
			) {
				batch.classic.push( file );
			} else {
				batch.pipeline.push( file );
			}
		}

		return batch.pipeline.length ? batch : null;
	}

	/**
	 * Hands a file to classic plupload, whose own FilesAdded handler queues
	 * and starts the upload.
	 *
	 * @param {plupload.Uploader} up         The plupload uploader.
	 * @param {File}              nativeFile The file to upload.
	 */
	function addClassicFile( up, nativeFile ) {
		classicFiles.add( nativeFile );
		up.addFile( nativeFile );
	}

	/**
	 * Re-queues the classic share of a batch on plupload.
	 *
	 * Suppressing plupload's built-in FilesAdded handler covers the whole
	 * batch, so the files partitionBatch() left to plupload are removed and
	 * added back on their own.
	 *
	 * @param {plupload.Uploader} up    The plupload uploader.
	 * @param {plupload.File[]}   files The files to upload classically.
	 */
	function handOffBatchToClassic( up, files ) {
		files.forEach( function ( file ) {
			// partitionBatch() already established that every file has one.
			const nativeFile = /** @type {File} */ ( file.getNative() );
			up.removeFile( file );
			addClassicFile( up, nativeFile );
		} );
	}

	/**
	 * Re-queues a file on classic plupload when the server can do what the
	 * browser could not.
	 *
	 * The pipeline converts HEIC in the browser and fails outright when the
	 * platform has no HEIC decoder (Linux, and Windows without the HEVC
	 * extension), whereas the classic upload lets the server convert it.
	 * When the server's image editor supports HEIC - plupload carries no
	 * `heic_upload_error` setting then - the file goes back to plupload,
	 * whose own FilesAdded handler queues and starts the classic upload.
	 * Nothing else can convert it, so any other failure stands.
	 *
	 * @param {plupload.Uploader} up         The plupload uploader the file came from.
	 * @param {UploadError}       error      Why the pipeline gave up on it.
	 * @param {File}              nativeFile The file the pipeline was given.
	 * @return {boolean} True when the file was handed to classic plupload.
	 */
	function handOffToClassic( up, error, nativeFile ) {
		const errorCodes = wp.uploadMedia.ErrorCode || {};
		if (
			! error ||
			! errorCodes.HEIC_DECODE_ERROR ||
			error.code !== errorCodes.HEIC_DECODE_ERROR ||
			! up ||
			typeof up.addFile !== 'function' ||
			( up.settings && up.settings.heic_upload_error )
		) {
			return false;
		}

		addClassicFile( up, nativeFile );
		return true;
	}

	/**
	 * Builds the extra fields to send with an upload from plupload's
	 * multipart params.
	 *
	 * Anything a plugin added through the `plupload_default_params` filter
	 * or wp.Uploader.param() reached the classic upload as $_POST fields,
	 * so it is forwarded to the REST request the same way. The classic
	 * transport's own fields are dropped: `action` and `_wpnonce` belong to
	 * async-upload.php, and `post_id` is spelled `post` by the REST API and
	 * passed separately after the screen has validated it.
	 *
	 * @param {Record<string, string>} params Plupload's multipart_params.
	 * @param {number}                 postId The validated post to attach the upload to, or 0.
	 * @return {Record<string, unknown>} Additional data for the upload.
	 */
	function additionalDataFromParams( params, postId ) {
		/** @type {Record<string, unknown>} */
		const additionalData = {};
		Object.keys( params || {} ).forEach( function ( key ) {
			if ( 'action' === key || '_wpnonce' === key || 'post_id' === key ) {
				return;
			}
			additionalData[ key ] = params[ key ];
		} );
		if ( postId ) {
			additionalData.post = postId;
		}
		return additionalData;
	}

	/**
	 * Queues a file for client-side processing and upload.
	 *
	 * @param {File}                    nativeFile     The file to upload.
	 * @param {Record<string, unknown>} additionalData Extra fields to send with the attachment.
	 * @param {QueueFileCallbacks}      [callbacks]    Lifecycle callbacks.
	 */
	function queueFile( nativeFile, additionalData, callbacks = {} ) {
		/**
		 * Handles successful upload.
		 *
		 * @param {PipelineAttachment[]} attachments The uploaded attachments.
		 */
		function onSuccess( attachments ) {
			entries.delete( onSuccess );
			if ( callbacks.onSuccess ) {
				callbacks.onSuccess( attachments[ 0 ] );
			}
		}

		/** @type {UploadEntry} */
		const entry = {
			onProgress: callbacks.onProgress,
			lastPercent: -1,
			totals: null,
			seen: false,
			attachmentId: 0,
			fail: function ( error ) {
				if ( ! entries.delete( onSuccess ) ) {
					return;
				}
				// The original already reached the server (finalize failed,
				// say), so delete it: otherwise a retry leaves a duplicate
				// next to an attachment whose metadata was never finalized.
				// The store only deletes it itself when every sub-size failed.
				if ( entry.attachmentId ) {
					mediaDelete( entry.attachmentId ).catch( function () {} );
				}
				if ( callbacks.onError ) {
					callbacks.onError( error );
				}
			},
		};
		entries.set( onSuccess, entry );

		wp.data.dispatch( uploadStore ).addItems( {
			files: [ nativeFile ],
			additionalData: additionalData || {},
			onSuccess: onSuccess,
			onError: function ( /** @type {UploadError|string} */ error ) {
				// Also reached for files addItems() rejects before queueing.
				// The block editor's mediaUpload() setting, which the store
				// uses once the editor has configured it, reports transport
				// failures as a bare message string.
				entry.fail(
					typeof error === 'string' ? { message: error } : error
				);
			},
		} );
	}

	/**
	 * Routes a batch of files added to a plupload uploader through the
	 * pipeline.
	 *
	 * Returns undefined (not false) when the pipeline cannot take the batch
	 * so plupload's built-in FilesAdded handler runs and uploads it
	 * server-side - a degradation, never data loss. Otherwise queues the
	 * files the pipeline can take, re-queues the rest on plupload, and
	 * returns false to suppress the built-in handler, which would otherwise
	 * start a classic upload of every file.
	 *
	 * The classic flow posts plupload's multipart params to
	 * async-upload.php, so they are forwarded to the REST request for
	 * plugins reading $_POST, with `post_id` replaced by `options.postId`.
	 *
	 * @param {plupload.Uploader} up      The plupload uploader the files were added to.
	 * @param {plupload.File[]}   files   Files added to the queue.
	 * @param {RouteBatchOptions} options The screen's hooks.
	 * @return {boolean|undefined} False to suppress the built-in handler.
	 */
	function routeBatch( up, files, options ) {
		if ( ! isReady() ) {
			return;
		}

		const batch = partitionBatch( up, files );
		if ( ! batch ) {
			return;
		}

		const params = ( up.settings && up.settings.multipart_params ) || {};
		const additionalData = additionalDataFromParams(
			params,
			options.postId
		);

		if ( options.onStart ) {
			options.onStart( batch.pipeline );
		}

		batch.pipeline.forEach( function ( /** @type {plupload.File} */ file ) {
			// partitionBatch() already established that every file has one.
			const nativeFile = /** @type {File} */ ( file.getNative() );
			const callbacks = options.onQueue( file, nativeFile );

			// Remove the file from plupload so it is not uploaded twice.
			up.removeFile( file );

			queueFile( nativeFile, additionalData, {
				onSuccess: callbacks.onSuccess,
				onProgress: callbacks.onProgress,
				onError: function ( /** @type {UploadError} */ error ) {
					// When the server can convert what the browser could
					// not, plupload takes the file from here and shows its
					// own progress.
					if ( handOffToClassic( up, error, nativeFile ) ) {
						callbacks.onHandOff();
						return;
					}
					callbacks.onError( error );
				},
			} );
		} );

		handOffBatchToClassic( up, batch.classic );

		up.refresh();

		return false;
	}

	/**
	 * Builds the display text for a failed upload.
	 *
	 * wp.uploadMedia.getErrorMessage() maps an error *code* and a file name
	 * to a { title, description, action } object, so it can neither be handed
	 * the Error itself nor used as a string. Only codes it actually maps are
	 * worth using: its fallback (and the GENERAL code) says nothing the
	 * error's own message does not, and preferring the message there keeps a
	 * server-supplied reason instead of replacing it with "Please try again."
	 *
	 * @param {UploadError} error    The upload error.
	 * @param {string}      fileName Name of the file that failed to upload.
	 * @return {string} A human-readable message.
	 */
	function getErrorText( error, fileName ) {
		const errorCodes = wp.uploadMedia.ErrorCode || {};
		const code = error && error.code;
		let details;

		if (
			code &&
			code !== errorCodes.GENERAL &&
			Object.prototype.hasOwnProperty.call( errorCodes, code ) &&
			wp.uploadMedia.getErrorMessage
		) {
			details = wp.uploadMedia.getErrorMessage( code, fileName );

			if ( details && details.description ) {
				return details.action ?
					details.description + ' ' + details.action :
					details.description;
			}
		}

		return (
			( error && error.message ) ||
			__( 'An error occurred while uploading the file.' )
		);
	}

	wp.mediaUploadPipeline = {
		isSupported: isSupported,
		configure: configure,
		isReady: isReady,
		routeBatch: routeBatch,
		getErrorText: getErrorText,
	};
} )( window.wp );
