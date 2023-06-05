<?php
/**
 * WooCommerce Admin Input Parameter Exception Class
 *
 * Exception class thrown when user provides incorrect parameters.
 */

namespace Automattic\WooCommerce\Admin\API\Reports;

defined( 'ABSPATH' ) || exit;

/**
 * API\Reports\ParameterException class.
 */
class ParameterException extends \WC_Data_Exception {}
