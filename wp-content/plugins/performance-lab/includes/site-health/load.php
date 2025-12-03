<?php
/**
 * Site Health checks loader.
 *
 * @package performance-lab
 * @since 3.0.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

// Audit Autoloaded Options site health check.
require_once __DIR__ . '/audit-autoloaded-options/helper.php';
require_once __DIR__ . '/audit-autoloaded-options/hooks.php';

// Audit Enqueued Assets site health check.
require_once __DIR__ . '/audit-enqueued-assets/helper.php';
require_once __DIR__ . '/audit-enqueued-assets/hooks.php';

// WebP Support site health check.
require_once __DIR__ . '/webp-support/helper.php';
require_once __DIR__ . '/webp-support/hooks.php';

// AVIF Support site health check.
require_once __DIR__ . '/avif-support/helper.php';
require_once __DIR__ . '/avif-support/hooks.php';

// AVIF headers site health check.
require_once __DIR__ . '/avif-headers/helper.php';
require_once __DIR__ . '/avif-headers/hooks.php';

// Effective Asset Cache Headers site health check.
require_once __DIR__ . '/effective-asset-cache-headers/helper.php';
require_once __DIR__ . '/effective-asset-cache-headers/hooks.php';

// Cache-Control headers site health check.
require_once __DIR__ . '/bfcache-compatibility-headers/helper.php';
require_once __DIR__ . '/bfcache-compatibility-headers/hooks.php';
