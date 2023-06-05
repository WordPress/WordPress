export interface Currency {
	/**
	 * ISO 4217 Currency Code
	 */
	code: CurrencyCode;
	/**
	 * String which separates the decimals from the integer
	 */
	decimalSeparator: string;
	/**
	 * @todo Description of this currently unknown
	 */
	minorUnit: number;
	/**
	 * String to prefix the currency with.
	 *
	 * This property is generally exclusive with `suffix`.
	 */
	prefix: string;
	/**
	 * String to suffix the currency with.
	 *
	 * This property is generally exclusive with `prefix`.
	 */
	suffix: string;
	/**
	 * Currency symbol
	 */
	symbol: string; // @todo create a list of allowed currency symbols
	/**
	 * String which separates the thousands
	 */
	thousandSeparator: string;
}

export interface CurrencyResponse {
	currency_code: CurrencyCode;
	currency_symbol: string;
	currency_minor_unit: number;
	currency_decimal_separator: string;
	currency_thousand_separator: string;
	currency_prefix: string;
	currency_suffix: string;
}

export type SymbolPosition = 'left' | 'left_space' | 'right' | 'right_space';

export type CurrencyCode =
	| 'AED'
	| 'AFN'
	| 'ALL'
	| 'AMD'
	| 'ANG'
	| 'AOA'
	| 'ARS'
	| 'AUD'
	| 'AWG'
	| 'AZN'
	| 'BAM'
	| 'BBD'
	| 'BDT'
	| 'BGN'
	| 'BHD'
	| 'BIF'
	| 'BMD'
	| 'BND'
	| 'BOB'
	| 'BRL'
	| 'BSD'
	| 'BTC'
	| 'BTN'
	| 'BWP'
	| 'BYR'
	| 'BYN'
	| 'BZD'
	| 'CAD'
	| 'CDF'
	| 'CHF'
	| 'CLP'
	| 'CNY'
	| 'COP'
	| 'CRC'
	| 'CUC'
	| 'CUP'
	| 'CVE'
	| 'CZK'
	| 'DJF'
	| 'DKK'
	| 'DOP'
	| 'DZD'
	| 'EGP'
	| 'ERN'
	| 'ETB'
	| 'EUR'
	| 'FJD'
	| 'FKP'
	| 'GBP'
	| 'GEL'
	| 'GGP'
	| 'GHS'
	| 'GIP'
	| 'GMD'
	| 'GNF'
	| 'GTQ'
	| 'GYD'
	| 'HKD'
	| 'HNL'
	| 'HRK'
	| 'HTG'
	| 'HUF'
	| 'IDR'
	| 'ILS'
	| 'IMP'
	| 'INR'
	| 'IQD'
	| 'IRR'
	| 'IRT'
	| 'ISK'
	| 'JEP'
	| 'JMD'
	| 'JOD'
	| 'JPY'
	| 'KES'
	| 'KGS'
	| 'KHR'
	| 'KMF'
	| 'KPW'
	| 'KRW'
	| 'KWD'
	| 'KYD'
	| 'KZT'
	| 'LAK'
	| 'LBP'
	| 'LKR'
	| 'LRD'
	| 'LSL'
	| 'LYD'
	| 'MAD'
	| 'MDL'
	| 'MGA'
	| 'MKD'
	| 'MMK'
	| 'MNT'
	| 'MOP'
	| 'MRU'
	| 'MUR'
	| 'MVR'
	| 'MWK'
	| 'MXN'
	| 'MYR'
	| 'MZN'
	| 'NAD'
	| 'NGN'
	| 'NIO'
	| 'NOK'
	| 'NPR'
	| 'NZD'
	| 'OMR'
	| 'PAB'
	| 'PEN'
	| 'PGK'
	| 'PHP'
	| 'PKR'
	| 'PLN'
	| 'PRB'
	| 'PYG'
	| 'QAR'
	| 'RON'
	| 'RSD'
	| 'RUB'
	| 'RWF'
	| 'SAR'
	| 'SBD'
	| 'SCR'
	| 'SDG'
	| 'SEK'
	| 'SGD'
	| 'SHP'
	| 'SLL'
	| 'SOS'
	| 'SRD'
	| 'SSP'
	| 'STN'
	| 'SYP'
	| 'SZL'
	| 'THB'
	| 'TJS'
	| 'TMT'
	| 'TND'
	| 'TOP'
	| 'TRY'
	| 'TTD'
	| 'TWD'
	| 'TZS'
	| 'UAH'
	| 'UGX'
	| 'USD'
	| 'UYU'
	| 'UZS'
	| 'VEF'
	| 'VES'
	| 'VND'
	| 'VUV'
	| 'WST'
	| 'XAF'
	| 'XCD'
	| 'XOF'
	| 'XPF'
	| 'YER'
	| 'ZAR'
	| 'ZMW';
