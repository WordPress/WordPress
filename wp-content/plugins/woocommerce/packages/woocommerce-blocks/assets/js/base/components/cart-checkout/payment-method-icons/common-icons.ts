/**
 * External dependencies
 */
import { WC_BLOCKS_IMAGE_URL } from '@woocommerce/block-settings';
import type { PaymentMethodIcon } from '@woocommerce/types';

/**
 * Array of common assets.
 */
export const commonIcons: PaymentMethodIcon[] = [
	{
		id: 'alipay',
		alt: 'Alipay',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/alipay.svg',
	},
	{
		id: 'amex',
		alt: 'American Express',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/amex.svg',
	},
	{
		id: 'bancontact',
		alt: 'Bancontact',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/bancontact.svg',
	},
	{
		id: 'diners',
		alt: 'Diners Club',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/diners.svg',
	},
	{
		id: 'discover',
		alt: 'Discover',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/discover.svg',
	},
	{
		id: 'eps',
		alt: 'EPS',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/eps.svg',
	},
	{
		id: 'giropay',
		alt: 'Giropay',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/giropay.svg',
	},
	{
		id: 'ideal',
		alt: 'iDeal',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/ideal.svg',
	},
	{
		id: 'jcb',
		alt: 'JCB',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/jcb.svg',
	},
	{
		id: 'laser',
		alt: 'Laser',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/laser.svg',
	},
	{
		id: 'maestro',
		alt: 'Maestro',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/maestro.svg',
	},
	{
		id: 'mastercard',
		alt: 'Mastercard',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/mastercard.svg',
	},
	{
		id: 'multibanco',
		alt: 'Multibanco',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/multibanco.svg',
	},
	{
		id: 'p24',
		alt: 'Przelewy24',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/p24.svg',
	},
	{
		id: 'sepa',
		alt: 'Sepa',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/sepa.svg',
	},
	{
		id: 'sofort',
		alt: 'Sofort',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/sofort.svg',
	},
	{
		id: 'unionpay',
		alt: 'Union Pay',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/unionpay.svg',
	},
	{
		id: 'visa',
		alt: 'Visa',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/visa.svg',
	},
	{
		id: 'wechat',
		alt: 'WeChat',
		src: WC_BLOCKS_IMAGE_URL + 'payment-methods/wechat.svg',
	},
];

/**
 * For a given ID, see if a common icon exists and return it's props.
 *
 * @param {string} id Icon ID.
 */
export const getCommonIconProps = (
	id: string
): PaymentMethodIcon | Record< string, unknown > => {
	return (
		commonIcons.find( ( icon ) => {
			return icon.id === id;
		} ) || {}
	);
};
