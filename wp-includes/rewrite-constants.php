<?php
/**
 * WordPress Rewrite API
 *
 * @package WordPress
 * @subpackage Rewrite
 */

/**
 * Endpoint Mask for default, which is nothing.
 *
 * @since 2.1.0
 */
define('EP_NONE', 0);

/**
 * Endpoint Mask for Permalink.
 *
 * @since 2.1.0
 */
define('EP_PERMALINK', 1);

/**
 * Endpoint Mask for Attachment.
 *
 * @since 2.1.0
 */
define('EP_ATTACHMENT', 2);

/**
 * Endpoint Mask for date.
 *
 * @since 2.1.0
 */
define('EP_DATE', 4);

/**
 * Endpoint Mask for year
 *
 * @since 2.1.0
 */
define('EP_YEAR', 8);

/**
 * Endpoint Mask for month.
 *
 * @since 2.1.0
 */
define('EP_MONTH', 16);

/**
 * Endpoint Mask for day.
 *
 * @since 2.1.0
 */
define('EP_DAY', 32);

/**
 * Endpoint Mask for root.
 *
 * @since 2.1.0
 */
define('EP_ROOT', 64);

/**
 * Endpoint Mask for comments.
 *
 * @since 2.1.0
 */
define('EP_COMMENTS', 128);

/**
 * Endpoint Mask for searches.
 *
 * @since 2.1.0
 */
define('EP_SEARCH', 256);

/**
 * Endpoint Mask for categories.
 *
 * @since 2.1.0
 */
define('EP_CATEGORIES', 512);

/**
 * Endpoint Mask for tags.
 *
 * @since 2.3.0
 */
define('EP_TAGS', 1024);

/**
 * Endpoint Mask for authors.
 *
 * @since 2.1.0
 */
define('EP_AUTHORS', 2048);

/**
 * Endpoint Mask for pages.
 *
 * @since 2.1.0
 */
define('EP_PAGES', 4096);

/**
 * Endpoint Mask for all archive views.
 *
 * @since 3.7.0
 */
define( 'EP_ALL_ARCHIVES', EP_DATE | EP_YEAR | EP_MONTH | EP_DAY | EP_CATEGORIES | EP_TAGS | EP_AUTHORS );

/**
 * Endpoint Mask for everything.
 *
 * @since 2.1.0
 */
define( 'EP_ALL', EP_PERMALINK | EP_ATTACHMENT | EP_ROOT | EP_COMMENTS | EP_SEARCH | EP_PAGES | EP_ALL_ARCHIVES );
