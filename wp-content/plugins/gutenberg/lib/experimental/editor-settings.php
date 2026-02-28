<?php
/**
 * Utilities to manage editor settings.
 *
 * @package gutenberg
 */

/**
 * Sets a global JS variable used to trigger the availability of each Gutenberg Experiment.
 */
function gutenberg_enable_experiments() {
	$gutenberg_experiments = get_option( 'gutenberg-experiments' );
	if ( $gutenberg_experiments && array_key_exists( 'gutenberg-sync-collaboration', $gutenberg_experiments ) ) {
		wp_add_inline_script( 'wp-block-editor', 'window.__experimentalEnableSync = true', 'before' );
	}
	if ( $gutenberg_experiments && array_key_exists( 'gutenberg-color-randomizer', $gutenberg_experiments ) ) {
		wp_add_inline_script( 'wp-block-editor', 'window.__experimentalEnableColorRandomizer = true', 'before' );
	}
	if ( $gutenberg_experiments && array_key_exists( 'gutenberg-grid-interactivity', $gutenberg_experiments ) ) {
		wp_add_inline_script( 'wp-block-editor', 'window.__experimentalEnableGridInteractivity = true', 'before' );
	}
	if ( gutenberg_is_experiment_enabled( 'gutenberg-no-tinymce' ) ) {
		wp_add_inline_script( 'wp-block-library', 'window.__experimentalDisableTinymce = true', 'before' );
	}
	if ( $gutenberg_experiments && array_key_exists( 'gutenberg-quick-edit-dataviews', $gutenberg_experiments ) ) {
		wp_add_inline_script( 'wp-block-editor', 'window.__experimentalQuickEditDataViews = true', 'before' );
	}
	if ( $gutenberg_experiments && array_key_exists( 'gutenberg-dataviews-media-modal', $gutenberg_experiments ) ) {
		wp_add_inline_script( 'wp-block-editor', 'window.__experimentalDataViewsMediaModal = true', 'before' );
	}
	if ( $gutenberg_experiments && array_key_exists( 'gutenberg-media-processing', $gutenberg_experiments ) ) {
		wp_add_inline_script( 'wp-block-editor', 'window.__experimentalMediaProcessing = true', 'before' );
	}
	if ( $gutenberg_experiments && array_key_exists( 'gutenberg-content-only-pattern-insertion', $gutenberg_experiments ) ) {
		wp_add_inline_script( 'wp-block-editor', 'window.__experimentalContentOnlyPatternInsertion = true', 'before' );
	}
	if ( gutenberg_is_experiment_enabled( 'active_templates' ) ) {
		wp_add_inline_script( 'wp-block-editor', 'window.__experimentalTemplateActivate = true', 'before' );
	}
}

add_action( 'admin_init', 'gutenberg_enable_experiments' );

/**
 * Sets a global JS variable used to trigger the availability of form & input blocks.
 *
 * @deprecated 19.0.0 Use gutenberg_enable_block_experiments().
 */
function gutenberg_enable_form_input_blocks() {
	_deprecated_function( __FUNCTION__, 'Gutenberg 19.0.0', 'gutenberg_enable_block_experiments' );
}

/**
 * Sets global JS variables used to enable various block experiments.
 */
function gutenberg_enable_block_experiments() {
	$gutenberg_experiments = get_option( 'gutenberg-experiments' );

	// Experimental form blocks.
	if ( $gutenberg_experiments && array_key_exists( 'gutenberg-form-blocks', $gutenberg_experiments ) ) {
		wp_add_inline_script( 'wp-block-editor', 'window.__experimentalEnableFormBlocks = true', 'before' );
	}

	// General experimental blocks that are not in the default block library.
	if ( $gutenberg_experiments && array_key_exists( 'gutenberg-block-experiments', $gutenberg_experiments ) ) {
		wp_add_inline_script( 'wp-block-editor', 'window.__experimentalEnableBlockExperiments = true', 'before' );
	}
}

add_action( 'admin_init', 'gutenberg_enable_block_experiments' );
