/**
 * @typedef {import('./shipping').ShippingAddress} CartShippingAddress
 */

/**
 * @typedef {Object} CartTotalItem
 *
 * @property {string} label        Label for total item
 * @property {number} value        The value of the total item (in subunits).
 * @property {number} valueWithTax The value of the total item with tax
 *                                 included (in subunits).
 */

/**
 * @typedef {Object} CartItemImage
 *
 * @property {number} id        Image id.
 * @property {string} src       Full size image URL.
 * @property {string} thumbnail Thumbnail URL.
 * @property {string} srcset    Thumbnail srcset for responsive image.
 * @property {string} sizes     Thumbnail sizes for responsive images.
 * @property {string} name      Image name.
 * @property {string} alt       Image alternative text.
 */

/**
 * @typedef {Object} CartItemVariation
 *
 * @property {string} attribute Variation attribute name.
 * @property {string} value     Variation attribute value.
 */

/**
 * @typedef {Object} CartItemTotals
 *
 * @property {string} currency_code               The ISO code for the currency.
 * @property {number} currency_minor_unit         The precision (decimal
 *                                                places).
 * @property {string} currency_symbol             The symbol for the currency
 *                                                (eg '$')
 * @property {string} currency_prefix             Price prefix for the currency
 *                                                which can be used to format
 *                                                returned prices.
 * @property {string} currency_suffix             Price suffix for the currency
 *                                                which can be used to format
 *                                                returned prices.
 * @property {string} currency_decimal_separator  The string used for the
 *                                                decimal separator.
 * @property {string} currency_thousand_separator The string used for the
 *                                                thousands separator.
 * @property {string} line_subtotal               Line subtotal (the price of
 *                                                the product before coupon
 *                                                discounts have been applied
 *                                                in subunits).
 * @property {string} line_subtotal_tax           Line subtotal tax (in
 *                                                subunits).
 * @property {string} line_total                  Line total (the price of the
 *                                                product after coupon
 *                                                discounts have been applied
 *                                                in subunits).
 * @property {string} line_total_tax              Line total tax (in subunits).
 */

/**
 * @typedef {Object} CartItemPriceRange
 *
 * @property {string} min_amount Price min amount in range.
 * @property {string} max_amount Price max amount in range.
 */

/**
 * @typedef {Object} CartItemPrices
 *
 * @property {string}                  currency_code               The ISO code for the
 *                                                                 currency.
 * @property {number}                  currency_minor_unit         The precision (decimal
 *                                                                 places).
 * @property {string}                  currency_symbol             The symbol for the
 *                                                                 currency (eg '$')
 * @property {string}                  currency_prefix             Price prefix for the
 *                                                                 currency which can be
 *                                                                 used to format returned
 *                                                                 prices.
 * @property {string}                  currency_suffix             Price suffix for the
 *                                                                 currency which can be
 *                                                                 used to format returned
 *                                                                 prices.
 * @property {string}                  currency_decimal_separator  The string used for the
 *                                                                 decimal separator.
 * @property {string}                  currency_thousand_separator The string used for the
 *                                                                 thousands separator.
 * @property {string}                  price                       Current product price
 *                                                                 in subunits.
 * @property {string}                  regular_price               Regular product price
 *                                                                 in subunits.
 * @property {string}                  sale_price                  Sale product price, if
 *                                                                 applicable (in subunits).
 * @property {CartItemPriceRange|null} price_range                 Price range, if
 *                                                                 applicable.
 *
 */

/**
 * @typedef {Object} CartItem
 *
 * @property {string}              key                  Unique identifier for the
 *                                                      item within the cart.
 * @property {number}              id                   The cart item product or
 *                                                      variation id.
 * @property {number}              quantity             The quantity of this item
 *                                                      in the cart.
 * @property {string}              name                 Product name.
 * @property {string}              summary              A short summary (or
 *                                                      excerpt from the full
 *                                                      description).
 * @property {string}              short_description    Product short description
 *                                                      in HTML format.
 * @property {string}              sku                  Stock keeping unit, if
 *                                                      applicable.
 * @property {number|null}         low_stock_remaining  Quantity left in stock if
 *                                                      stock is low, or null if
 *                                                      not applicable.
 * @property {boolean}             backorders_allowed   True if backorders are
 *                                                      allowed past stock
 *                                                      availability.
 * @property {boolean}             show_backorder_badge Whether a notification
 *                                                      should be shown about the
 *                                                      product being available on
 *                                                      backorder.
 * @property {boolean}             sold_individually    If true, only one item of
 *                                                      this product is allowed
 *                                                      for purchase in a single
 *                                                      order.
 * @property {string}              permalink            Product URL.
 * @property {CartItemImage[]}     images               List of images attached
 *                                                      to the cart item
 *                                                      product/variation.
 * @property {CartItemVariation[]} variation            Chosen attributes (for
 *                                                      variations).
 * @property {CartItemPrices}      prices               Item prices.
 * @property {CartItemTotals}      totals               Item total amounts
 *                                                      provided using the
 *                                                      smallest unit of the
 *                                                      currency.
 * @property {Object}              extensions           Extra data registered
 *                                                      by plugins.
 */

/**
 * @typedef {Object} CartData
 *
 * @property {Array}               coupons         Coupons applied to cart.
 * @property {Array}               shippingRates   Array of selected shipping
 *                                                 rates.
 * @property {CartShippingAddress} shippingAddress Shipping address for the
 *                                                 cart.
 * @property {Array}               items           Items in the cart.
 * @property {number}              itemsCount      Number of items in the cart.
 * @property {number}              itemsWeight     Weight of items in the cart.
 * @property {boolean}             needsShipping   True if the cart needs
 *                                                 shipping.
 * @property {CartTotals}          totals          Cart total amounts.
 */

/**
 * @typedef {Object} CartTotals
 *
 * @property {string} currency_code               Currency code (in ISO format)
 *                                                for returned prices.
 * @property {string} currency_symbol             Currency symbol for the
 *                                                currency which can be used to
 *                                                format returned prices.
 * @property {number} currency_minor_unit         Currency minor unit (number of
 *                                                digits after the decimal
 *                                                separator) for returned
 *                                                prices.
 * @property {string} currency_decimal_separator  Decimal separator for the
 *                                                currency which can be used to
 *                                                format returned prices.
 * @property {string} currency_thousand_separator Thousand separator for the
 *                                                currency which can be used to
 *                                                format returned prices.
 * @property {string} currency_prefix             Price prefix for the currency
 *                                                which can be used to format
 *                                                returned prices.
 * @property {string} currency_suffix             Price prefix for the currency
 *                                                which can be used to format
 *                                                returned prices.
 * @property {number} total_items                 Total price of items in the
 *                                                cart (in subunits).
 * @property {number} total_items_tax             Total tax on items in the
 *                                                cart (in subunits).
 * @property {number} total_fees                  Total price of any applied
 *                                                fees (in subunits).
 * @property {number} total_fees_tax              Total tax on fees (
 *                                                in subunits).
 * @property {number} total_discount              Total discount from applied
 *                                                coupons (in subunits).
 * @property {number} total_discount_tax          Total tax removed due to
 *                                                discount from applied coupons
 *                                                (in subunits).
 * @property {number} total_shipping              Total price of shipping
 *                                                (in subunits).
 * @property {number} total_shipping_tax          Total tax on shipping
 *                                                (in subunits).
 * @property {number} total_price                 Total price the customer will
 *                                                pay (in subunits).
 * @property {number} total_tax                   Total tax applied to items and
 *                                                shipping (in subunits).
 * @property {Array}  tax_lines                   Lines of taxes applied to
 *                                                items and shipping
 *                                                (in subunits).
 */

export {};
