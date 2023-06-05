<?php
/**
 * Structured data's handler and generator using JSON-LD format.
 *
 * @package WooCommerce\Classes
 * @since   3.0.0
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Structured data class.
 */
class WC_Structured_Data {

	/**
	 * Stores the structured data.
	 *
	 * @var array $_data Array of structured data.
	 */
	private $_data = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Generate structured data.
		add_action( 'woocommerce_before_main_content', array( $this, 'generate_website_data' ), 30 );
		add_action( 'woocommerce_breadcrumb', array( $this, 'generate_breadcrumblist_data' ), 10 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'generate_product_data' ), 60 );
		add_action( 'woocommerce_email_order_details', array( $this, 'generate_order_data' ), 20, 3 );

		// Output structured data.
		add_action( 'woocommerce_email_order_details', array( $this, 'output_email_structured_data' ), 30, 3 );
		add_action( 'wp_footer', array( $this, 'output_structured_data' ), 10 );
	}

	/**
	 * Sets data.
	 *
	 * @param  array $data  Structured data.
	 * @param  bool  $reset Unset data (default: false).
	 * @return bool
	 */
	public function set_data( $data, $reset = false ) {
		if ( ! isset( $data['@type'] ) || ! preg_match( '|^[a-zA-Z]{1,20}$|', $data['@type'] ) ) {
			return false;
		}

		if ( $reset && isset( $this->_data ) ) {
			unset( $this->_data );
		}

		$this->_data[] = $data;

		return true;
	}

	/**
	 * Gets data.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->_data;
	}

	/**
	 * Structures and returns data.
	 *
	 * List of types available by default for specific request:
	 *
	 * 'product',
	 * 'review',
	 * 'breadcrumblist',
	 * 'website',
	 * 'order',
	 *
	 * @param  array $types Structured data types.
	 * @return array
	 */
	public function get_structured_data( $types ) {
		$data = array();

		// Put together the values of same type of structured data.
		foreach ( $this->get_data() as $value ) {
			$data[ strtolower( $value['@type'] ) ][] = $value;
		}

		// Wrap the multiple values of each type inside a graph... Then add context to each type.
		foreach ( $data as $type => $value ) {
			$data[ $type ] = count( $value ) > 1 ? array( '@graph' => $value ) : $value[0];
			$data[ $type ] = apply_filters( 'woocommerce_structured_data_context', array( '@context' => 'https://schema.org/' ), $data, $type, $value ) + $data[ $type ];
		}

		// If requested types, pick them up... Finally change the associative array to an indexed one.
		$data = $types ? array_values( array_intersect_key( $data, array_flip( $types ) ) ) : array_values( $data );

		if ( ! empty( $data ) ) {
			if ( 1 < count( $data ) ) {
				$data = apply_filters( 'woocommerce_structured_data_context', array( '@context' => 'https://schema.org/' ), $data, '', '' ) + array( '@graph' => $data );
			} else {
				$data = $data[0];
			}
		}

		return $data;
	}

	/**
	 * Get data types for pages.
	 *
	 * @return array
	 */
	protected function get_data_type_for_page() {
		$types   = array();
		$types[] = is_shop() || is_product_category() || is_product() ? 'product' : '';
		$types[] = is_shop() && is_front_page() ? 'website' : '';
		$types[] = is_product() ? 'review' : '';
		$types[] = 'breadcrumblist';
		$types[] = 'order';

		return array_filter( apply_filters( 'woocommerce_structured_data_type_for_page', $types ) );
	}

	/**
	 * Makes sure email structured data only outputs on non-plain text versions.
	 *
	 * @param WP_Order $order         Order data.
	 * @param bool     $sent_to_admin Send to admin (default: false).
	 * @param bool     $plain_text    Plain text email (default: false).
	 */
	public function output_email_structured_data( $order, $sent_to_admin = false, $plain_text = false ) {
		if ( $plain_text ) {
			return;
		}
		echo '<div style="display: none; font-size: 0; max-height: 0; line-height: 0; padding: 0; mso-hide: all;">';
		$this->output_structured_data();
		echo '</div>';
	}

	/**
	 * Sanitizes, encodes and outputs structured data.
	 *
	 * Hooked into `wp_footer` action hook.
	 * Hooked into `woocommerce_email_order_details` action hook.
	 */
	public function output_structured_data() {
		$types = $this->get_data_type_for_page();
		$data  = $this->get_structured_data( $types );

		if ( $data ) {
			echo '<script type="application/ld+json">' . wc_esc_json( wp_json_encode( $data ), true ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Generators
	|--------------------------------------------------------------------------
	|
	| Methods for generating specific structured data types:
	|
	| - Product
	| - Review
	| - BreadcrumbList
	| - WebSite
	| - Order
	|
	| The generated data is stored into `$this->_data`.
	| See the methods above for handling `$this->_data`.
	|
	*/

	/**
	 * Generates Product structured data.
	 *
	 * Hooked into `woocommerce_single_product_summary` action hook.
	 *
	 * @param WC_Product $product Product data (default: null).
	 */
	public function generate_product_data( $product = null ) {
		if ( ! is_object( $product ) ) {
			global $product;
		}

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$shop_name = get_bloginfo( 'name' );
		$shop_url  = home_url();
		$currency  = get_woocommerce_currency();
		$permalink = get_permalink( $product->get_id() );
		$image     = wp_get_attachment_url( $product->get_image_id() );

		$markup = array(
			'@type'       => 'Product',
			'@id'         => $permalink . '#product', // Append '#product' to differentiate between this @id and the @id generated for the Breadcrumblist.
			'name'        => wp_kses_post( $product->get_name() ),
			'url'         => $permalink,
			'description' => wp_strip_all_tags( do_shortcode( $product->get_short_description() ? $product->get_short_description() : $product->get_description() ) ),
		);

		if ( $image ) {
			$markup['image'] = $image;
		}

		// Declare SKU or fallback to ID.
		if ( $product->get_sku() ) {
			$markup['sku'] = $product->get_sku();
		} else {
			$markup['sku'] = $product->get_id();
		}

		if ( '' !== $product->get_price() ) {
			// Assume prices will be valid until the end of next year, unless on sale and there is an end date.
			$price_valid_until = gmdate( 'Y-12-31', time() + YEAR_IN_SECONDS );

			if ( $product->is_type( 'variable' ) ) {
				$lowest  = $product->get_variation_price( 'min', false );
				$highest = $product->get_variation_price( 'max', false );

				if ( $lowest === $highest ) {
					$markup_offer = array(
						'@type'              => 'Offer',
						'price'              => wc_format_decimal( $lowest, wc_get_price_decimals() ),
						'priceValidUntil'    => $price_valid_until,
						'priceSpecification' => array(
							'price'                 => wc_format_decimal( $lowest, wc_get_price_decimals() ),
							'priceCurrency'         => $currency,
							'valueAddedTaxIncluded' => wc_prices_include_tax() ? 'true' : 'false',
						),
					);
				} else {
					$markup_offer = array(
						'@type'      => 'AggregateOffer',
						'lowPrice'   => wc_format_decimal( $lowest, wc_get_price_decimals() ),
						'highPrice'  => wc_format_decimal( $highest, wc_get_price_decimals() ),
						'offerCount' => count( $product->get_children() ),
					);
				}
			} else {
				if ( $product->is_on_sale() && $product->get_date_on_sale_to() ) {
					$price_valid_until = gmdate( 'Y-m-d', $product->get_date_on_sale_to()->getTimestamp() );
				}
				$markup_offer = array(
					'@type'              => 'Offer',
					'price'              => wc_format_decimal( $product->get_price(), wc_get_price_decimals() ),
					'priceValidUntil'    => $price_valid_until,
					'priceSpecification' => array(
						'price'                 => wc_format_decimal( $product->get_price(), wc_get_price_decimals() ),
						'priceCurrency'         => $currency,
						'valueAddedTaxIncluded' => wc_prices_include_tax() ? 'true' : 'false',
					),
				);
			}

			$markup_offer += array(
				'priceCurrency' => $currency,
				'availability'  => 'http://schema.org/' . ( $product->is_in_stock() ? 'InStock' : 'OutOfStock' ),
				'url'           => $permalink,
				'seller'        => array(
					'@type' => 'Organization',
					'name'  => $shop_name,
					'url'   => $shop_url,
				),
			);

			$markup['offers'] = array( apply_filters( 'woocommerce_structured_data_product_offer', $markup_offer, $product ) );
		}

		if ( $product->get_rating_count() && wc_review_ratings_enabled() ) {
			$markup['aggregateRating'] = array(
				'@type'       => 'AggregateRating',
				'ratingValue' => $product->get_average_rating(),
				'reviewCount' => $product->get_review_count(),
			);

			// Markup 5 most recent rating/review.
			$comments = get_comments(
				array(
					'number'      => 5,
					'post_id'     => $product->get_id(),
					'status'      => 'approve',
					'post_status' => 'publish',
					'post_type'   => 'product',
					'parent'      => 0,
					'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						array(
							'key'     => 'rating',
							'type'    => 'NUMERIC',
							'compare' => '>',
							'value'   => 0,
						),
					),
				)
			);

			if ( $comments ) {
				$markup['review'] = array();
				foreach ( $comments as $comment ) {
					$markup['review'][] = array(
						'@type'         => 'Review',
						'reviewRating'  => array(
							'@type'       => 'Rating',
							'bestRating'  => '5',
							'ratingValue' => get_comment_meta( $comment->comment_ID, 'rating', true ),
							'worstRating' => '1',
						),
						'author'        => array(
							'@type' => 'Person',
							'name'  => get_comment_author( $comment ),
						),
						'reviewBody'    => get_comment_text( $comment ),
						'datePublished' => get_comment_date( 'c', $comment ),
					);
				}
			}
		}

		// Check we have required data.
		if ( empty( $markup['aggregateRating'] ) && empty( $markup['offers'] ) && empty( $markup['review'] ) ) {
			return;
		}

		$this->set_data( apply_filters( 'woocommerce_structured_data_product', $markup, $product ) );
	}

	/**
	 * Generates Review structured data.
	 *
	 * Hooked into `woocommerce_review_meta` action hook.
	 *
	 * @param WP_Comment $comment Comment data.
	 */
	public function generate_review_data( $comment ) {
		$markup                  = array();
		$markup['@type']         = 'Review';
		$markup['@id']           = get_comment_link( $comment->comment_ID );
		$markup['datePublished'] = get_comment_date( 'c', $comment->comment_ID );
		$markup['description']   = get_comment_text( $comment->comment_ID );
		$markup['itemReviewed']  = array(
			'@type' => 'Product',
			'name'  => get_the_title( $comment->comment_post_ID ),
		);

		// Skip replies unless they have a rating.
		$rating = get_comment_meta( $comment->comment_ID, 'rating', true );

		if ( $rating ) {
			$markup['reviewRating'] = array(
				'@type'       => 'Rating',
				'bestRating'  => '5',
				'ratingValue' => $rating,
				'worstRating' => '1',
			);
		} elseif ( $comment->comment_parent ) {
			return;
		}

		$markup['author'] = array(
			'@type' => 'Person',
			'name'  => get_comment_author( $comment->comment_ID ),
		);

		$this->set_data( apply_filters( 'woocommerce_structured_data_review', $markup, $comment ) );
	}

	/**
	 * Generates BreadcrumbList structured data.
	 *
	 * Hooked into `woocommerce_breadcrumb` action hook.
	 *
	 * @param WC_Breadcrumb $breadcrumbs Breadcrumb data.
	 */
	public function generate_breadcrumblist_data( $breadcrumbs ) {
		$crumbs = $breadcrumbs->get_breadcrumb();

		if ( empty( $crumbs ) || ! is_array( $crumbs ) ) {
			return;
		}

		$markup                    = array();
		$markup['@type']           = 'BreadcrumbList';
		$markup['itemListElement'] = array();

		foreach ( $crumbs as $key => $crumb ) {
			$markup['itemListElement'][ $key ] = array(
				'@type'    => 'ListItem',
				'position' => $key + 1,
				'item'     => array(
					'name' => $crumb[0],
				),
			);

			if ( ! empty( $crumb[1] ) ) {
				$markup['itemListElement'][ $key ]['item'] += array( '@id' => $crumb[1] );
			} elseif ( isset( $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] ) ) {
				$current_url = set_url_scheme( 'http://' . wp_unslash( $_SERVER['HTTP_HOST'] ) . wp_unslash( $_SERVER['REQUEST_URI'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				$markup['itemListElement'][ $key ]['item'] += array( '@id' => $current_url );
			}
		}

		$this->set_data( apply_filters( 'woocommerce_structured_data_breadcrumblist', $markup, $breadcrumbs ) );
	}

	/**
	 * Generates WebSite structured data.
	 *
	 * Hooked into `woocommerce_before_main_content` action hook.
	 */
	public function generate_website_data() {
		$markup                    = array();
		$markup['@type']           = 'WebSite';
		$markup['name']            = get_bloginfo( 'name' );
		$markup['url']             = home_url();
		$markup['potentialAction'] = array(
			'@type'       => 'SearchAction',
			'target'      => home_url( '?s={search_term_string}&post_type=product' ),
			'query-input' => 'required name=search_term_string',
		);

		$this->set_data( apply_filters( 'woocommerce_structured_data_website', $markup ) );
	}

	/**
	 * Generates Order structured data.
	 *
	 * Hooked into `woocommerce_email_order_details` action hook.
	 *
	 * @param WP_Order $order         Order data.
	 * @param bool     $sent_to_admin Send to admin (default: false).
	 * @param bool     $plain_text    Plain text email (default: false).
	 */
	public function generate_order_data( $order, $sent_to_admin = false, $plain_text = false ) {
		if ( $plain_text || ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		$shop_name      = get_bloginfo( 'name' );
		$shop_url       = home_url();
		$order_url      = $sent_to_admin ? $order->get_edit_order_url() : $order->get_view_order_url();
		$order_statuses = array(
			'pending'    => 'https://schema.org/OrderPaymentDue',
			'processing' => 'https://schema.org/OrderProcessing',
			'on-hold'    => 'https://schema.org/OrderProblem',
			'completed'  => 'https://schema.org/OrderDelivered',
			'cancelled'  => 'https://schema.org/OrderCancelled',
			'refunded'   => 'https://schema.org/OrderReturned',
			'failed'     => 'https://schema.org/OrderProblem',
		);

		$markup_offers = array();
		foreach ( $order->get_items() as $item ) {
			if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
				continue;
			}

			$product        = $item->get_product();
			$product_exists = is_object( $product );
			$is_visible     = $product_exists && $product->is_visible();

			$markup_offers[] = array(
				'@type'              => 'Offer',
				'price'              => $order->get_line_subtotal( $item ),
				'priceCurrency'      => $order->get_currency(),
				'priceSpecification' => array(
					'price'            => $order->get_line_subtotal( $item ),
					'priceCurrency'    => $order->get_currency(),
					'eligibleQuantity' => array(
						'@type' => 'QuantitativeValue',
						'value' => apply_filters( 'woocommerce_email_order_item_quantity', $item->get_quantity(), $item ),
					),
				),
				'itemOffered'        => array(
					'@type' => 'Product',
					'name'  => wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, $is_visible ) ),
					'sku'   => $product_exists ? $product->get_sku() : '',
					'image' => $product_exists ? wp_get_attachment_image_url( $product->get_image_id() ) : '',
					'url'   => $is_visible ? get_permalink( $product->get_id() ) : get_home_url(),
				),
				'seller'             => array(
					'@type' => 'Organization',
					'name'  => $shop_name,
					'url'   => $shop_url,
				),
			);
		}

		$markup                       = array();
		$markup['@type']              = 'Order';
		$markup['url']                = $order_url;
		$markup['orderStatus']        = isset( $order_statuses[ $order->get_status() ] ) ? $order_statuses[ $order->get_status() ] : '';
		$markup['orderNumber']        = $order->get_order_number();
		$markup['orderDate']          = $order->get_date_created()->format( 'c' );
		$markup['acceptedOffer']      = $markup_offers;
		$markup['discount']           = $order->get_total_discount();
		$markup['discountCurrency']   = $order->get_currency();
		$markup['price']              = $order->get_total();
		$markup['priceCurrency']      = $order->get_currency();
		$markup['priceSpecification'] = array(
			'price'                 => $order->get_total(),
			'priceCurrency'         => $order->get_currency(),
			'valueAddedTaxIncluded' => 'true',
		);
		$markup['billingAddress']     = array(
			'@type'           => 'PostalAddress',
			'name'            => $order->get_formatted_billing_full_name(),
			'streetAddress'   => $order->get_billing_address_1(),
			'postalCode'      => $order->get_billing_postcode(),
			'addressLocality' => $order->get_billing_city(),
			'addressRegion'   => $order->get_billing_state(),
			'addressCountry'  => $order->get_billing_country(),
			'email'           => $order->get_billing_email(),
			'telephone'       => $order->get_billing_phone(),
		);
		$markup['customer']           = array(
			'@type' => 'Person',
			'name'  => $order->get_formatted_billing_full_name(),
		);
		$markup['merchant']           = array(
			'@type' => 'Organization',
			'name'  => $shop_name,
			'url'   => $shop_url,
		);
		$markup['potentialAction']    = array(
			'@type'  => 'ViewAction',
			'name'   => 'View Order',
			'url'    => $order_url,
			'target' => $order_url,
		);

		$this->set_data( apply_filters( 'woocommerce_structured_data_order', $markup, $sent_to_admin, $order ), true );
	}
}
