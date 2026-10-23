<?php
/**
 * View Transitions API.
 *
 * @package WordPress
 * @subpackage View Transitions
 * @since 7.0.0
 */

/**
 * Enqueues View Transitions CSS for the admin.
 *
 * To disable admin View Transitions, dequeue the `wp-view-transitions-admin` style at the
 * {@see 'admin_enqueue_scripts'} action, rather than unhooking this function.
 *
 * @since 7.0.0
 * @since 7.2.0 Called from wp-admin/admin-header.php instead of hooked to {@see 'admin_enqueue_scripts'}.
 */
function wp_enqueue_view_transitions_admin_css(): void {
	wp_enqueue_style( 'wp-view-transitions-admin' );
}

/**
 * Gets the CSS for View Transitions in the admin.
 *
 * @since 7.0.0
 *
 * @return string The CSS.
 */
function wp_get_view_transitions_admin_css(): string {
	$affix = SCRIPT_DEBUG ? '' : '.min';
	$path  = ABSPATH . "wp-admin/css/view-transitions{$affix}.css";
	return (string) file_get_contents( $path );
}

/**
 * Prints a render-blocking expectation so View Transitions capture a fully parsed admin menu.
 *
 * A cross-document view transition snapshots the incoming page at its first render, which can
 * happen before the `<body>` has finished parsing. The snapshot would then capture a partially
 * built page — for example an incomplete admin menu — and the transition animates to or from
 * the wrong state.
 *
 * The `expect` link blocks the first render until `#wpcontent` is parsed. Rendering is unblocked
 * as soon as the target's start tag is parsed, not its children, so the target is the element
 * right after the admin menu rather than the menu itself: once `#wpcontent` starts, the whole
 * menu has been parsed. Targeting an element near the end of the body would also work, but it
 * would delay the first render by the time it takes to parse all of the page content. The media
 * query limits the first-render delay to when view transitions can run, matching the
 * `@view-transition` rule in view-transitions.css. It does not detect whether a transition is
 * actually in progress, so a reload or a direct visit is also held until `#wpcontent` is parsed,
 * but since that element starts right after the admin menu, the delay is negligible.
 *
 * Nothing is printed unless the admin View Transitions style has already been printed, since
 * there is otherwise no transition to wait for. That style is only enqueued from
 * wp-admin/admin-header.php, so this also leaves out iframe screens such as Thickbox modals,
 * which have no `#wpcontent`: an `expect` link whose target never appears blocks the first
 * render until the whole document has been parsed.
 *
 * @since 7.2.0
 *
 * @link https://html.spec.whatwg.org/multipage/links.html#link-type-expect
 */
function wp_print_view_transitions_render_blocking_link(): void {
	if ( ! wp_style_is( 'wp-view-transitions-admin', 'done' ) ) {
		return;
	}

	echo '<link rel="expect" href="#wpcontent" blocking="render" media="(prefers-reduced-motion: no-preference)">' . "\n";
}
