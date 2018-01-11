<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { // If uninstall not called from WordPress exit
	exit();
}

/**
 * Manages Polylang uninstallation
 * The goal is to remove ALL Polylang related data in db
 *
 * @since 0.5
 */
class PLL_Uninstall {

	/**
	 * Constructor: manages uninstall for multisite
	 *
	 * @since 0.5
	 */
	function __construct() {
		global $wpdb;

		// Check if it is a multisite uninstall - if so, run the uninstall function for each blog id
		if ( is_multisite() ) {
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->uninstall();
			}
			restore_current_blog();
		}
		else {
			$this->uninstall();
		}
	}

	/**
	 * Removes ALL plugin data
	 * only when the relevant option is active
	 *
	 * @since 0.5
	 */
	function uninstall() {
		$options = get_option( 'polylang' );

		if ( empty( $options['uninstall'] ) ) {
			return;
		}

		// Suppress data of the old model < 1.2
		// FIXME: to remove when support for v1.1.6 will be dropped
		global $wpdb;
		$wpdb->termmeta = $wpdb->prefix . 'termmeta'; // registers the termmeta table in wpdb

		// Do nothing if the termmeta table does not exists
		if ( count( $wpdb->get_results( "SHOW TABLES LIKE '$wpdb->termmeta'" ) ) ) {
			$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_translations'" );
			$wpdb->query( "DELETE FROM $wpdb->termmeta WHERE meta_key = '_language'" );
			$wpdb->query( "DELETE FROM $wpdb->termmeta WHERE meta_key = '_rtl'" );
			$wpdb->query( "DELETE FROM $wpdb->termmeta WHERE meta_key = '_translations'" );
		}

		// Need to register the taxonomies
		$pll_taxonomies = array( 'language', 'term_language', 'post_translations', 'term_translations' );
		foreach ( $pll_taxonomies as $taxonomy ) {
			register_taxonomy( $taxonomy, null, array( 'label' => false, 'public' => false, 'query_var' => false, 'rewrite' => false ) );
		}

		$languages = get_terms( 'language', array( 'hide_empty' => false ) );

		// Delete users options
		foreach ( get_users( array( 'fields' => 'ID' ) ) as $user_id ) {
			delete_user_meta( $user_id, 'pll_filter_content' );
			delete_user_meta( $user_id, 'pll_duplicate_content' );
			foreach ( $languages as $lang ) {
				delete_user_meta( $user_id, 'description_' . $lang->slug );
			}
		}

		// Delete menu language switchers
		$ids = get_posts( array(
			'post_type'   => 'nav_menu_item',
			'numberposts' => -1,
			'nopaging'    => true,
			'fields'      => 'ids',
			'meta_key'    => '_pll_menu_item',
		) );

		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}

		// Delete the strings translations ( <1.2 )
		// FIXME: to remove when support for v1.1.6 will be dropped
		foreach ( $languages as $lang ) {
			delete_option( 'polylang_mo' . $lang->term_id );
		}

		// Delete the strings translations 1.2+
		register_post_type( 'polylang_mo', array( 'rewrite' => false, 'query_var' => false ) );
		$ids = get_posts( array(
			'post_type'   => 'polylang_mo',
			'post_status' => 'any',
			'numberposts' => -1,
			'nopaging'    => true,
			'fields'      => 'ids',
		) );
		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}

		// Delete all what is related to languages and translations
		foreach ( get_terms( $pll_taxonomies, array( 'hide_empty' => false ) ) as $term ) {
			$term_ids[] = (int) $term->term_id;
			$tt_ids[] = (int) $term->term_taxonomy_id;
		}

		if ( ! empty( $term_ids ) ) {
			$term_ids = array_unique( $term_ids );
			$wpdb->query( "DELETE FROM $wpdb->terms WHERE term_id IN ( " . implode( ',', $term_ids ) . ' )' );
			$wpdb->query( "DELETE FROM $wpdb->term_taxonomy WHERE term_id IN ( " . implode( ',', $term_ids ) . ' )' );
		}

		if ( ! empty( $tt_ids ) ) {
			$tt_ids = array_unique( $tt_ids );
			$wpdb->query( "DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ( " . implode( ',', $tt_ids ) . ' )' );
		}

		// Delete options
		delete_option( 'polylang' );
		delete_option( 'widget_polylang' ); // Automatically created by WP
		delete_option( 'polylang_wpml_strings' ); // Strings registered with icl_register_string
		delete_option( 'polylang_licenses' );

		// Delete transients
		delete_transient( 'pll_languages_list' );
		delete_transient( 'pll_upgrade_1_4' );
		delete_transient( 'pll_translated_slugs' );
	}
}

new PLL_Uninstall();
