<?php
/**
 * Script Modules API: Module functions
 *
 * @since 6.5.0
 *
 * @package WordPress
 * @subpackage Script Modules
 */

/**
 * Retrieves the main WP_Script_Modules instance.
 *
 * This function provides access to the WP_Script_Modules instance, creating one
 * if it doesn't exist yet.
 *
 * @since 6.5.0
 *
 * @return WP_Script_Modules The main WP_Script_Modules instance.
 */
function wp_modules() {
	static $instance = null;
	if ( is_null( $instance ) ) {
		$instance = new WP_Script_Modules();
		$instance->add_hooks();
	}
	return $instance;
}

/**
 * Registers the module if no module with that module identifier has already
 * been registered.
 *
 * @since 6.5.0
 *
 * @param string                                                        $module_id The identifier of the module.
 *                                                                                 Should be unique. It will be used
 *                                                                                 in the final import map.
 * @param string                                                        $src       Full URL of the module, or path of
 *                                                                                 the module relative to the
 *                                                                                 WordPress root directory.
 * @param array<string|array{id: string, import?: 'static'|'dynamic' }> $deps      Optional. An array of module
 *                                                                                 identifiers of the dependencies of
 *                                                                                 this module. The dependencies can
 *                                                                                 be strings or arrays. If they are
 *                                                                                 arrays, they need an `id` key with
 *                                                                                 the module identifier, and can
 *                                                                                 contain an `import` key with either
 *                                                                                 `static` or `dynamic`. By default,
 *                                                                                 dependencies that don't contain an
 *                                                                                 `import` key are considered static.
 * @param string|false|null                                             $version   Optional. String specifying the
 *                                                                                 module version number. Defaults to
 *                                                                                 false. It is added to the URL as a
 *                                                                                 query string for cache busting
 *                                                                                 purposes. If $version is set to
 *                                                                                 false, the version number is the
 *                                                                                 currently installed WordPress
 *                                                                                 version. If $version is set to
 *                                                                                 null, no version is added.
 */
function wp_register_module( $module_id, $src, $deps = array(), $version = false ) {
	wp_modules()->register( $module_id, $src, $deps, $version );
}

/**
 * Marks the module to be enqueued in the page.
 *
 * If a src is provided and the module has not been registered yet, it will be
 * registered.
 *
 * @since 6.5.0
 *
 * @param string                                                        $module_id The identifier of the module.
 *                                                                                 Should be unique. It will be used
 *                                                                                 in the final import map.
 * @param string                                                        $src       Optional. Full URL of the module,
 *                                                                                 or path of the module relative to
 *                                                                                 the WordPress root directory. If
 *                                                                                 it is provided and the module has
 *                                                                                 not been registered yet, it will be
 *                                                                                 registered.
 * @param array<string|array{id: string, import?: 'static'|'dynamic' }> $deps      Optional. An array of module
 *                                                                                 identifiers of the dependencies of
 *                                                                                 this module. The dependencies can
 *                                                                                 be strings or arrays. If they are
 *                                                                                 arrays, they need an `id` key with
 *                                                                                 the module identifier, and can
 *                                                                                 contain an `import` key with either
 *                                                                                 `static` or `dynamic`. By default,
 *                                                                                 dependencies that don't contain an
 *                                                                                 `import` key are considered static.
 * @param string|false|null                                             $version   Optional. String specifying the
 *                                                                                 module version number. Defaults to
 *                                                                                 false. It is added to the URL as a
 *                                                                                 query string for cache busting
 *                                                                                 purposes. If $version is set to
 *                                                                                 false, the version number is the
 *                                                                                 currently installed WordPress
 *                                                                                 version. If $version is set to
 *                                                                                 null, no version is added.
 */
function wp_enqueue_module( $module_id, $src = '', $deps = array(), $version = false ) {
	wp_modules()->enqueue( $module_id, $src, $deps, $version );
}

/**
 * Unmarks the module so it is no longer enqueued in the page.
 *
 * @since 6.5.0
 *
 * @param string $module_id The identifier of the module.
 */
function wp_dequeue_module( $module_id ) {
	wp_modules()->dequeue( $module_id );
}
