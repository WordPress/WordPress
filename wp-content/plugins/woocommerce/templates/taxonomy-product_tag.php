<?php
/**
 * The Template for displaying products in a product tag. Simply includes the archive template.
 *
 * Override this template by copying it to yourtheme/woocommerce/taxonomy-product_tag.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wc_get_template( 'archive-product.php' );