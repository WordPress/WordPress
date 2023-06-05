<?php
/**
 * WooCommerce Admin Notes Unavailable Exception Class
 *
 * Exception class thrown when an attempt to use notes is made but notes are unavailable.
 */

namespace Automattic\WooCommerce\Admin\Notes;

defined( 'ABSPATH' ) || exit;

/**
 * Notes\NotesUnavailableException class.
 */
class NotesUnavailableException extends \WC_Data_Exception {}
