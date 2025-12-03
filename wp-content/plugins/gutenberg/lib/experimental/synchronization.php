<?php
/**
 * Bootstraps synchronization (collaborative editing).
 *
 * @package gutenberg
 */

/**
 * Initializes the collaborative editing secret.
 */
function gutenberg_rest_api_init_collaborative_editing() {
	$gutenberg_experiments = get_option( 'gutenberg-experiments' );
	if ( ! $gutenberg_experiments || ! array_key_exists( 'gutenberg-sync-collaboration', $gutenberg_experiments ) ) {
		return;
	}
	$collaborative_editing_secret = get_site_option( 'collaborative_editing_secret' );
	if ( ! $collaborative_editing_secret ) {
		$collaborative_editing_secret = wp_generate_password( 64, false );
	}
	add_site_option( 'collaborative_editing_secret', $collaborative_editing_secret );

	wp_add_inline_script( 'wp-sync', 'window.__experimentalCollaborativeEditingSecret = "' . $collaborative_editing_secret . '";', 'before' );
}
add_action( 'admin_init', 'gutenberg_rest_api_init_collaborative_editing' );

/**
 * Registers post meta for persisting CRDT documents.
 */
function gutenberg_rest_api_crdt_post_meta() {
	$gutenberg_experiments = get_option( 'gutenberg-experiments' );
	if ( ! $gutenberg_experiments || ! array_key_exists( 'gutenberg-sync-collaboration', $gutenberg_experiments ) ) {
		return;
	}

	// This string must match WORDPRESS_META_KEY_FOR_CRDT_DOC_PERSISTENCE in @wordpress/sync.
	$persisted_crdt_post_meta_key = '_crdt_document';

	register_meta(
		'post',
		$persisted_crdt_post_meta_key,
		array(
			'auth_callback'     => function ( bool $_allowed, string $_meta_key, int $object_id, int $user_id ): bool {
				return user_can( $user_id, 'edit_post', $object_id );
			},
			// IMPORTANT: Revisions must be disabled because we always want to preserve
			// the latest persisted CRDT document, even when a revision is restored.
			// This ensures that we can continue to apply updates to a shared document
			// and peers can simply merge the restored revision like any other incoming
			// update.
			//
			// If we want to persist CRDT documents alongisde revisions in the
			// future, we should do so in a separate meta key.
			'revisions_enabled' => false,
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		)
	);
}
add_action( 'init', 'gutenberg_rest_api_crdt_post_meta' );
