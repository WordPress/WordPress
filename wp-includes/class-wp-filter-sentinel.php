<?php
/**
 * Filter Sentinel API.
 *
 * @package WordPress
 * @since 7.1.0
 */

declare( strict_types = 1 );

/**
 * Marker object used as a filter's default value when any user value — including
 * `null`, `false`, or arbitrary objects — must remain distinguishable from the
 * "no filter modified this" case.
 *
 * Each instance is unique by identity. Compare returned values with `===` against
 * the original sentinel to detect that no filter callback replaced it.
 *
 * Filter callbacks that want to pass through without modifying the value should
 * return the received value unchanged. Returning a freshly constructed
 * `WP_Filter_Sentinel` is treated as a replacement, not as pass-through.
 *
 * @since 7.1.0
 */
final class WP_Filter_Sentinel {}
