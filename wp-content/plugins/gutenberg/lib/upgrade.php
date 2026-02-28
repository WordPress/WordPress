<?php
/**
 * Upgrading Gutenberg's database.
 *
 * @package gutenberg
 */

if ( ! defined( '_GUTENBERG_VERSION_MIGRATION' ) ) {
	// It's necessary to update this version every time a new migration is needed.
	define( '_GUTENBERG_VERSION_MIGRATION', '9.8.0' );
}

/**
 * Migrate Gutenberg's database on upgrade.
 *
 * @access private
 * @internal
 */
function _gutenberg_migrate_database() {
	// The default value used here is the first version before migrations were added.
	$gutenberg_installed_version = get_option( 'gutenberg_version_migration', '9.7.0' );

	if ( _GUTENBERG_VERSION_MIGRATION !== $gutenberg_installed_version ) {
		if ( version_compare( $gutenberg_installed_version, '9.8.0', '<' ) ) {
			_gutenberg_migrate_remove_fse_drafts();
		}

		update_option( 'gutenberg_version_migration', _GUTENBERG_VERSION_MIGRATION );
	}
}

/**
 * Remove FSE auto drafts and associated terms.
 *
 * @access private
 * @internal
 */
function _gutenberg_migrate_remove_fse_drafts() {
	// Delete auto-draft templates and template parts.
	$delete_query = new WP_Query(
		array(
			'post_status'    => array( 'auto-draft' ),
			'post_type'      => array( 'wp_template', 'wp_template_part' ),
			'posts_per_page' => -1,
		)
	);
	foreach ( $delete_query->posts as $post ) {
		wp_delete_post( $post->ID, true );
	}

	// Delete _wp_file_based term.
	$term = get_term_by( 'name', '_wp_file_based', 'wp_theme' );
	if ( $term ) {
		wp_delete_term( $term->term_id, 'wp_theme' );
	}

	// Delete useless options.
	delete_option( 'gutenberg_last_synchronize_theme_template_checks' );
	delete_option( 'gutenberg_last_synchronize_theme_template-part_checks' );
}

// Deletion of the `_wp_file_based` term (in _gutenberg_migrate_remove_fse_drafts) must happen
// after its taxonomy (`wp_theme`) is registered. This happens in `gutenberg_register_wp_theme_taxonomy`,
// which is hooked into `init` (default priority, i.e. 10).
add_action( 'init', '_gutenberg_migrate_database', 20 );
