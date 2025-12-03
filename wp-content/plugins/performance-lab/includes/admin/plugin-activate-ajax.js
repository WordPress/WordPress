/**
 * Handles activation of Performance Features (Plugins) using AJAX.
 */

( function () {
	// @ts-ignore
	const { i18n, a11y, apiFetch } = wp;
	const { __ } = i18n;

	// Queue to hold pending activation requests.
	const activationQueue = [];
	let isProcessingActivation = false;

	/**
	 * Enqueues plugin activation requests and starts processing if not already in progress.
	 *
	 * @param {MouseEvent} event - The click event object.
	 */
	function enqueuePluginActivation( event ) {
		// Prevent the default link behavior.
		event.preventDefault();

		const target = /** @type {HTMLElement} */ ( event.target );

		if (
			target.classList.contains( 'updating-message' ) ||
			target.classList.contains( 'disabled' )
		) {
			return;
		}

		target.classList.add( 'updating-message' );
		target.textContent = __( 'Waiting…', 'performance-lab' );

		const pluginSlug = target.dataset.pluginSlug;

		activationQueue.push( { target, pluginSlug } );

		// Start processing the queue if not already doing so.
		if ( ! isProcessingActivation ) {
			handlePluginActivation();
		}
	}

	/**
	 * Handles activation of feature/plugin using queue based approach.
	 *
	 * @return {Promise<void>} The asynchronous function returns a promise that resolves to void.
	 */
	async function handlePluginActivation() {
		if ( 0 === activationQueue.length ) {
			isProcessingActivation = false;
			return;
		}

		isProcessingActivation = true;

		const { target, pluginSlug } = activationQueue.shift();

		target.textContent = __( 'Activating…', 'performance-lab' );

		a11y.speak( __( 'Activating…', 'performance-lab' ) );

		try {
			// Activate the plugin/feature via the REST API.
			await apiFetch( {
				path: `/performance-lab/v1/features/${ pluginSlug }:activate`,
				method: 'POST',
			} );

			// Fetch the plugin/feature information via the REST API.
			/** @type {{settingsUrl: string|null}} */
			const featureInfo = await apiFetch( {
				path: `/performance-lab/v1/features/${ pluginSlug }`,
				method: 'GET',
			} );

			if ( featureInfo.settingsUrl ) {
				const actionButtonList = document.querySelector(
					`.plugin-card-${ pluginSlug } .plugin-action-buttons`
				);

				const listItem = document.createElement( 'li' );
				const anchor = document.createElement( 'a' );

				anchor.href = featureInfo.settingsUrl;
				anchor.textContent = __( 'Settings', 'performance-lab' );

				listItem.appendChild( anchor );
				actionButtonList.appendChild( listItem );
			}

			a11y.speak( __( 'Plugin activated.', 'performance-lab' ) );

			target.textContent = __( 'Active', 'performance-lab' );
			target.classList.remove( 'updating-message' );
			target.classList.add( 'disabled' );
		} catch ( error ) {
			a11y.speak( __( 'Plugin failed to activate.', 'performance-lab' ) );

			target.classList.remove( 'updating-message' );
			target.textContent = __( 'Activate', 'performance-lab' );
		} finally {
			handlePluginActivation();
		}
	}

	// Attach the event listeners.
	document
		.querySelectorAll( '.perflab-install-active-plugin' )
		.forEach( ( item ) => {
			item.addEventListener( 'click', enqueuePluginActivation );
		} );
} )();
