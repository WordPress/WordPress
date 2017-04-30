<?php
/**
 * WooCommerce countries
 *
 * The WooCommerce countries class stores country/state data.
 *
 * @class 		WC_Countries
 * @version		1.6.4
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_Countries {

	/** @var array Array of countries */
	public $countries;

	/** @var array Array of states */
	public $states;

	/** @var array Array of locales */
	public $locale;

	/** @var array Array of address formats for locales */
	public $address_formats;

	/**
	 * Constructor for the counties class - defines all countries and states.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		global $woocommerce, $states;

		$this->countries = apply_filters( 'woocommerce_countries', array(
			'AF' => __( 'Afghanistan', 'woocommerce' ),
			'AX' => __( '&#197;land Islands', 'woocommerce' ),
			'AL' => __( 'Albania', 'woocommerce' ),
			'DZ' => __( 'Algeria', 'woocommerce' ),
			'AD' => __( 'Andorra', 'woocommerce' ),
			'AO' => __( 'Angola', 'woocommerce' ),
			'AI' => __( 'Anguilla', 'woocommerce' ),
			'AQ' => __( 'Antarctica', 'woocommerce' ),
			'AG' => __( 'Antigua and Barbuda', 'woocommerce' ),
			'AR' => __( 'Argentina', 'woocommerce' ),
			'AM' => __( 'Armenia', 'woocommerce' ),
			'AW' => __( 'Aruba', 'woocommerce' ),
			'AU' => __( 'Australia', 'woocommerce' ),
			'AT' => __( 'Austria', 'woocommerce' ),
			'AZ' => __( 'Azerbaijan', 'woocommerce' ),
			'BS' => __( 'Bahamas', 'woocommerce' ),
			'BH' => __( 'Bahrain', 'woocommerce' ),
			'BD' => __( 'Bangladesh', 'woocommerce' ),
			'BB' => __( 'Barbados', 'woocommerce' ),
			'BY' => __( 'Belarus', 'woocommerce' ),
			'BE' => __( 'Belgium', 'woocommerce' ),
			'PW' => __( 'Belau', 'woocommerce' ),
			'BZ' => __( 'Belize', 'woocommerce' ),
			'BJ' => __( 'Benin', 'woocommerce' ),
			'BM' => __( 'Bermuda', 'woocommerce' ),
			'BT' => __( 'Bhutan', 'woocommerce' ),
			'BO' => __( 'Bolivia', 'woocommerce' ),
			'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'woocommerce' ),
			'BA' => __( 'Bosnia and Herzegovina', 'woocommerce' ),
			'BW' => __( 'Botswana', 'woocommerce' ),
			'BV' => __( 'Bouvet Island', 'woocommerce' ),
			'BR' => __( 'Brazil', 'woocommerce' ),
			'IO' => __( 'British Indian Ocean Territory', 'woocommerce' ),
			'VG' => __( 'British Virgin Islands', 'woocommerce' ),
			'BN' => __( 'Brunei', 'woocommerce' ),
			'BG' => __( 'Bulgaria', 'woocommerce' ),
			'BF' => __( 'Burkina Faso', 'woocommerce' ),
			'BI' => __( 'Burundi', 'woocommerce' ),
			'KH' => __( 'Cambodia', 'woocommerce' ),
			'CM' => __( 'Cameroon', 'woocommerce' ),
			'CA' => __( 'Canada', 'woocommerce' ),
			'CV' => __( 'Cape Verde', 'woocommerce' ),
			'KY' => __( 'Cayman Islands', 'woocommerce' ),
			'CF' => __( 'Central African Republic', 'woocommerce' ),
			'TD' => __( 'Chad', 'woocommerce' ),
			'CL' => __( 'Chile', 'woocommerce' ),
			'CN' => __( 'China', 'woocommerce' ),
			'CX' => __( 'Christmas Island', 'woocommerce' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'woocommerce' ),
			'CO' => __( 'Colombia', 'woocommerce' ),
			'KM' => __( 'Comoros', 'woocommerce' ),
			'CG' => __( 'Congo (Brazzaville)', 'woocommerce' ),
			'CD' => __( 'Congo (Kinshasa)', 'woocommerce' ),
			'CK' => __( 'Cook Islands', 'woocommerce' ),
			'CR' => __( 'Costa Rica', 'woocommerce' ),
			'HR' => __( 'Croatia', 'woocommerce' ),
			'CU' => __( 'Cuba', 'woocommerce' ),
			'CW' => __( 'Cura&Ccedil;ao', 'woocommerce' ),
			'CY' => __( 'Cyprus', 'woocommerce' ),
			'CZ' => __( 'Czech Republic', 'woocommerce' ),
			'DK' => __( 'Denmark', 'woocommerce' ),
			'DJ' => __( 'Djibouti', 'woocommerce' ),
			'DM' => __( 'Dominica', 'woocommerce' ),
			'DO' => __( 'Dominican Republic', 'woocommerce' ),
			'EC' => __( 'Ecuador', 'woocommerce' ),
			'EG' => __( 'Egypt', 'woocommerce' ),
			'SV' => __( 'El Salvador', 'woocommerce' ),
			'GQ' => __( 'Equatorial Guinea', 'woocommerce' ),
			'ER' => __( 'Eritrea', 'woocommerce' ),
			'EE' => __( 'Estonia', 'woocommerce' ),
			'ET' => __( 'Ethiopia', 'woocommerce' ),
			'FK' => __( 'Falkland Islands', 'woocommerce' ),
			'FO' => __( 'Faroe Islands', 'woocommerce' ),
			'FJ' => __( 'Fiji', 'woocommerce' ),
			'FI' => __( 'Finland', 'woocommerce' ),
			'FR' => __( 'France', 'woocommerce' ),
			'GF' => __( 'French Guiana', 'woocommerce' ),
			'PF' => __( 'French Polynesia', 'woocommerce' ),
			'TF' => __( 'French Southern Territories', 'woocommerce' ),
			'GA' => __( 'Gabon', 'woocommerce' ),
			'GM' => __( 'Gambia', 'woocommerce' ),
			'GE' => __( 'Georgia', 'woocommerce' ),
			'DE' => __( 'Germany', 'woocommerce' ),
			'GH' => __( 'Ghana', 'woocommerce' ),
			'GI' => __( 'Gibraltar', 'woocommerce' ),
			'GR' => __( 'Greece', 'woocommerce' ),
			'GL' => __( 'Greenland', 'woocommerce' ),
			'GD' => __( 'Grenada', 'woocommerce' ),
			'GP' => __( 'Guadeloupe', 'woocommerce' ),
			'GT' => __( 'Guatemala', 'woocommerce' ),
			'GG' => __( 'Guernsey', 'woocommerce' ),
			'GN' => __( 'Guinea', 'woocommerce' ),
			'GW' => __( 'Guinea-Bissau', 'woocommerce' ),
			'GY' => __( 'Guyana', 'woocommerce' ),
			'HT' => __( 'Haiti', 'woocommerce' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'woocommerce' ),
			'HN' => __( 'Honduras', 'woocommerce' ),
			'HK' => __( 'Hong Kong', 'woocommerce' ),
			'HU' => __( 'Hungary', 'woocommerce' ),
			'IS' => __( 'Iceland', 'woocommerce' ),
			'IN' => __( 'India', 'woocommerce' ),
			'ID' => __( 'Indonesia', 'woocommerce' ),
			'IR' => __( 'Iran', 'woocommerce' ),
			'IQ' => __( 'Iraq', 'woocommerce' ),
			'IE' => __( 'Republic of Ireland', 'woocommerce' ),
			'IM' => __( 'Isle of Man', 'woocommerce' ),
			'IL' => __( 'Israel', 'woocommerce' ),
			'IT' => __( 'Italy', 'woocommerce' ),
			'CI' => __( 'Ivory Coast', 'woocommerce' ),
			'JM' => __( 'Jamaica', 'woocommerce' ),
			'JP' => __( 'Japan', 'woocommerce' ),
			'JE' => __( 'Jersey', 'woocommerce' ),
			'JO' => __( 'Jordan', 'woocommerce' ),
			'KZ' => __( 'Kazakhstan', 'woocommerce' ),
			'KE' => __( 'Kenya', 'woocommerce' ),
			'KI' => __( 'Kiribati', 'woocommerce' ),
			'KW' => __( 'Kuwait', 'woocommerce' ),
			'KG' => __( 'Kyrgyzstan', 'woocommerce' ),
			'LA' => __( 'Laos', 'woocommerce' ),
			'LV' => __( 'Latvia', 'woocommerce' ),
			'LB' => __( 'Lebanon', 'woocommerce' ),
			'LS' => __( 'Lesotho', 'woocommerce' ),
			'LR' => __( 'Liberia', 'woocommerce' ),
			'LY' => __( 'Libya', 'woocommerce' ),
			'LI' => __( 'Liechtenstein', 'woocommerce' ),
			'LT' => __( 'Lithuania', 'woocommerce' ),
			'LU' => __( 'Luxembourg', 'woocommerce' ),
			'MO' => __( 'Macao S.A.R., China', 'woocommerce' ),
			'MK' => __( 'Macedonia', 'woocommerce' ),
			'MG' => __( 'Madagascar', 'woocommerce' ),
			'MW' => __( 'Malawi', 'woocommerce' ),
			'MY' => __( 'Malaysia', 'woocommerce' ),
			'MV' => __( 'Maldives', 'woocommerce' ),
			'ML' => __( 'Mali', 'woocommerce' ),
			'MT' => __( 'Malta', 'woocommerce' ),
			'MH' => __( 'Marshall Islands', 'woocommerce' ),
			'MQ' => __( 'Martinique', 'woocommerce' ),
			'MR' => __( 'Mauritania', 'woocommerce' ),
			'MU' => __( 'Mauritius', 'woocommerce' ),
			'YT' => __( 'Mayotte', 'woocommerce' ),
			'MX' => __( 'Mexico', 'woocommerce' ),
			'FM' => __( 'Micronesia', 'woocommerce' ),
			'MD' => __( 'Moldova', 'woocommerce' ),
			'MC' => __( 'Monaco', 'woocommerce' ),
			'MN' => __( 'Mongolia', 'woocommerce' ),
			'ME' => __( 'Montenegro', 'woocommerce' ),
			'MS' => __( 'Montserrat', 'woocommerce' ),
			'MA' => __( 'Morocco', 'woocommerce' ),
			'MZ' => __( 'Mozambique', 'woocommerce' ),
			'MM' => __( 'Myanmar', 'woocommerce' ),
			'NA' => __( 'Namibia', 'woocommerce' ),
			'NR' => __( 'Nauru', 'woocommerce' ),
			'NP' => __( 'Nepal', 'woocommerce' ),
			'NL' => __( 'Netherlands', 'woocommerce' ),
			'AN' => __( 'Netherlands Antilles', 'woocommerce' ),
			'NC' => __( 'New Caledonia', 'woocommerce' ),
			'NZ' => __( 'New Zealand', 'woocommerce' ),
			'NI' => __( 'Nicaragua', 'woocommerce' ),
			'NE' => __( 'Niger', 'woocommerce' ),
			'NG' => __( 'Nigeria', 'woocommerce' ),
			'NU' => __( 'Niue', 'woocommerce' ),
			'NF' => __( 'Norfolk Island', 'woocommerce' ),
			'KP' => __( 'North Korea', 'woocommerce' ),
			'NO' => __( 'Norway', 'woocommerce' ),
			'OM' => __( 'Oman', 'woocommerce' ),
			'PK' => __( 'Pakistan', 'woocommerce' ),
			'PS' => __( 'Palestinian Territory', 'woocommerce' ),
			'PA' => __( 'Panama', 'woocommerce' ),
			'PG' => __( 'Papua New Guinea', 'woocommerce' ),
			'PY' => __( 'Paraguay', 'woocommerce' ),
			'PE' => __( 'Peru', 'woocommerce' ),
			'PH' => __( 'Philippines', 'woocommerce' ),
			'PN' => __( 'Pitcairn', 'woocommerce' ),
			'PL' => __( 'Poland', 'woocommerce' ),
			'PT' => __( 'Portugal', 'woocommerce' ),
			'QA' => __( 'Qatar', 'woocommerce' ),
			'RE' => __( 'Reunion', 'woocommerce' ),
			'RO' => __( 'Romania', 'woocommerce' ),
			'RU' => __( 'Russia', 'woocommerce' ),
			'RW' => __( 'Rwanda', 'woocommerce' ),
			'BL' => __( 'Saint Barth&eacute;lemy', 'woocommerce' ),
			'SH' => __( 'Saint Helena', 'woocommerce' ),
			'KN' => __( 'Saint Kitts and Nevis', 'woocommerce' ),
			'LC' => __( 'Saint Lucia', 'woocommerce' ),
			'MF' => __( 'Saint Martin (French part)', 'woocommerce' ),
			'SX' => __( 'Saint Martin (Dutch part)', 'woocommerce' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'woocommerce' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'woocommerce' ),
			'SM' => __( 'San Marino', 'woocommerce' ),
			'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'woocommerce' ),
			'SA' => __( 'Saudi Arabia', 'woocommerce' ),
			'SN' => __( 'Senegal', 'woocommerce' ),
			'RS' => __( 'Serbia', 'woocommerce' ),
			'SC' => __( 'Seychelles', 'woocommerce' ),
			'SL' => __( 'Sierra Leone', 'woocommerce' ),
			'SG' => __( 'Singapore', 'woocommerce' ),
			'SK' => __( 'Slovakia', 'woocommerce' ),
			'SI' => __( 'Slovenia', 'woocommerce' ),
			'SB' => __( 'Solomon Islands', 'woocommerce' ),
			'SO' => __( 'Somalia', 'woocommerce' ),
			'ZA' => __( 'South Africa', 'woocommerce' ),
			'GS' => __( 'South Georgia/Sandwich Islands', 'woocommerce' ),
			'KR' => __( 'South Korea', 'woocommerce' ),
			'SS' => __( 'South Sudan', 'woocommerce' ),
			'ES' => __( 'Spain', 'woocommerce' ),
			'LK' => __( 'Sri Lanka', 'woocommerce' ),
			'SD' => __( 'Sudan', 'woocommerce' ),
			'SR' => __( 'Suriname', 'woocommerce' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'woocommerce' ),
			'SZ' => __( 'Swaziland', 'woocommerce' ),
			'SE' => __( 'Sweden', 'woocommerce' ),
			'CH' => __( 'Switzerland', 'woocommerce' ),
			'SY' => __( 'Syria', 'woocommerce' ),
			'TW' => __( 'Taiwan', 'woocommerce' ),
			'TJ' => __( 'Tajikistan', 'woocommerce' ),
			'TZ' => __( 'Tanzania', 'woocommerce' ),
			'TH' => __( 'Thailand', 'woocommerce' ),
			'TL' => __( 'Timor-Leste', 'woocommerce' ),
			'TG' => __( 'Togo', 'woocommerce' ),
			'TK' => __( 'Tokelau', 'woocommerce' ),
			'TO' => __( 'Tonga', 'woocommerce' ),
			'TT' => __( 'Trinidad and Tobago', 'woocommerce' ),
			'TN' => __( 'Tunisia', 'woocommerce' ),
			'TR' => __( 'Turkey', 'woocommerce' ),
			'TM' => __( 'Turkmenistan', 'woocommerce' ),
			'TC' => __( 'Turks and Caicos Islands', 'woocommerce' ),
			'TV' => __( 'Tuvalu', 'woocommerce' ),
			'UG' => __( 'Uganda', 'woocommerce' ),
			'UA' => __( 'Ukraine', 'woocommerce' ),
			'AE' => __( 'United Arab Emirates', 'woocommerce' ),
			'GB' => __( 'United Kingdom (UK)', 'woocommerce' ),
			'US' => __( 'United States (US)', 'woocommerce' ),
			'UY' => __( 'Uruguay', 'woocommerce' ),
			'UZ' => __( 'Uzbekistan', 'woocommerce' ),
			'VU' => __( 'Vanuatu', 'woocommerce' ),
			'VA' => __( 'Vatican', 'woocommerce' ),
			'VE' => __( 'Venezuela', 'woocommerce' ),
			'VN' => __( 'Vietnam', 'woocommerce' ),
			'WF' => __( 'Wallis and Futuna', 'woocommerce' ),
			'EH' => __( 'Western Sahara', 'woocommerce' ),
			'WS' => __( 'Western Samoa', 'woocommerce' ),
			'YE' => __( 'Yemen', 'woocommerce' ),
			'ZM' => __( 'Zambia', 'woocommerce' ),
			'ZW' => __( 'Zimbabwe', 'woocommerce' )
		));

		// States set to array() are blank i.e. the country has no use for the state field.
		$states = array(
			'AF' => array(),
			'AT' => array(),
			'BE' => array(),
			'BI' => array(),
			'CZ' => array(),
			'DE' => array(),
			'DK' => array(),
			'EE' => array(),
			'FI' => array(),
			'FR' => array(),
			'IS' => array(),
			'IL' => array(),
			'KR' => array(),
			'NL' => array(),
			'NO' => array(),
			'PL' => array(),
			'PT' => array(),
			'SG' => array(),
			'SK' => array(),
			'SI' => array(),
			'LK' => array(),
			'SE' => array(),
			'VN' => array(),
		);

		// Load only the state files the shop owner wants/needs
		$allowed = array_merge( $this->get_allowed_countries(), $this->get_shipping_countries() );

		if ( $allowed )
			foreach ( $allowed as $CC => $country )
				if ( ! isset( $states[ $CC ] ) && file_exists( WC()->plugin_path() . '/i18n/states/' . $CC . '.php' ) )
					include( WC()->plugin_path() . '/i18n/states/' . $CC . '.php' );

		$this->states = apply_filters( 'woocommerce_states', $states );
	}

	/**
	 * Get the base country for the store.
	 *
	 * @access public
	 * @return string
	 */
	public function get_base_country() {
		$default = esc_attr( get_option('woocommerce_default_country') );
		$country = ( ( $pos = strrpos( $default, ':' ) ) === false ) ? $default : substr( $default, 0, $pos );

		return apply_filters( 'woocommerce_countries_base_country', $country );
	}

	/**
	 * Get the base state for the store.
	 *
	 * @access public
	 * @return string
	 */
	public function get_base_state() {
		$default = wc_clean( get_option( 'woocommerce_default_country' ) );
		$state   = ( ( $pos = strrpos( $default, ':' ) ) === false ) ? '' : substr( $default, $pos + 1 );

		return apply_filters( 'woocommerce_countries_base_state', $state );
	}

	/**
	 * Get the base city for the store.
	 *
	 * @access public
	 * @return string
	 */
	public function get_base_city() {
		return apply_filters( 'woocommerce_countries_base_city', '' );
	}

	/**
	 * Get the base postcode for the store.
	 *
	 * @access public
	 * @return string
	 */
	public function get_base_postcode() {
		return apply_filters( 'woocommerce_countries_base_postcode', '' );
	}

	/**
	 * Get the allowed countries for the store.
	 *
	 * @access public
	 * @return array
	 */
	public function get_allowed_countries() {

		if ( apply_filters('woocommerce_sort_countries', true ) )
			asort( $this->countries );

		if ( get_option('woocommerce_allowed_countries') !== 'specific' )
			return $this->countries;

		$countries = array();

		$raw_countries = get_option( 'woocommerce_specific_allowed_countries' );

		foreach ( $raw_countries as $country )
			$countries[ $country ] = $this->countries[ $country ];

		return apply_filters( 'woocommerce_countries_allowed_countries', $countries );
	}

	/**
	 * Get the countries you ship to.
	 *
	 * @access public
	 * @return array
	 */
	public function get_shipping_countries() {

		if ( apply_filters( 'woocommerce_sort_countries', true ) )
			asort( $this->countries );

		if ( get_option( 'woocommerce_ship_to_countries' ) == '' )
			return $this->get_allowed_countries();

		if ( get_option('woocommerce_ship_to_countries') !== 'specific' )
			return $this->countries;

		$countries = array();

		$raw_countries = get_option( 'woocommerce_specific_ship_to_countries' );

		foreach ( $raw_countries as $country )
			$countries[ $country ] = $this->countries[ $country ];

		return apply_filters( 'woocommerce_countries_shipping_countries', $countries );
	}

	/**
	 * get_allowed_country_states function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_allowed_country_states() {

		if ( get_option('woocommerce_allowed_countries') !== 'specific' )
			return $this->states;

		$states = array();

		$raw_countries = get_option( 'woocommerce_specific_allowed_countries' );

		foreach ( $raw_countries as $country )
			if ( isset( $this->states[ $country ] ) )
				$states[ $country ] = $this->states[ $country ];

		return apply_filters( 'woocommerce_countries_allowed_country_states', $states );
	}

	/**
	 * get_shipping_country_states function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_shipping_country_states() {

		if ( get_option( 'woocommerce_ship_to_countries' ) == '' )
			return $this->get_allowed_country_states();

		if ( get_option( 'woocommerce_ship_to_countries' ) !== 'specific' )
			return $this->states;

		$states = array();

		$raw_countries = get_option( 'woocommerce_specific_ship_to_countries' );

		foreach ( $raw_countries as $country )
			if ( ! empty( $this->states[ $country ] ) )
				$states[ $country ] = $this->states[ $country ];

		return apply_filters( 'woocommerce_countries_shipping_country_states', $states );
	}

	/**
	 * Gets an array of countries in the EU.
	 *
	 * @access public
	 * @return array
	 */
	public function get_european_union_countries() {
		return array( 'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'HR', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK' );
	}


	/**
	 * Gets the correct string for shipping - ether 'to the' or 'to'
	 *
	 * @access public
	 * @return string
	 */
	public function shipping_to_prefix() {
		$return = '';
		if (in_array(WC()->customer->get_shipping_country(), array( 'GB', 'US', 'AE', 'CZ', 'DO', 'NL', 'PH', 'USAF' ))) $return = __( 'to the', 'woocommerce' );
		else $return = __( 'to', 'woocommerce' );
		return apply_filters('woocommerce_countries_shipping_to_prefix', $return, WC()->customer->get_shipping_country());
	}


	/**
	 * Prefix certain countries with 'the'
	 *
	 * @access public
	 * @return string
	 */
	public function estimated_for_prefix() {
		$return = '';
		if (in_array($this->get_base_country(), array( 'GB', 'US', 'AE', 'CZ', 'DO', 'NL', 'PH', 'USAF' ))) $return = __( 'the', 'woocommerce' ) . ' ';
		return apply_filters('woocommerce_countries_estimated_for_prefix', $return, $this->get_base_country());
	}


	/**
	 * Correctly name tax in some countries VAT on the frontend
	 *
	 * @access public
	 * @return string
	 */
	public function tax_or_vat() {
		$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __( 'VAT', 'woocommerce' ) : __( 'Tax', 'woocommerce' );

		return apply_filters( 'woocommerce_countries_tax_or_vat', $return );
	}


	/**
	 * Include the Inc Tax label.
	 *
	 * @access public
	 * @return string
	 */
	public function inc_tax_or_vat() {
		$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __( '(incl. VAT)', 'woocommerce' ) : __( '(incl. tax)', 'woocommerce' );

		return apply_filters( 'woocommerce_countries_inc_tax_or_vat', $return );
	}


	/**
	 * Include the Ex Tax label.
	 *
	 * @access public
	 * @return string
	 */
	public function ex_tax_or_vat() {
		$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __( '(ex. VAT)', 'woocommerce' ) : __( '(ex. tax)', 'woocommerce' );

		return apply_filters( 'woocommerce_countries_ex_tax_or_vat', $return );
	}


	/**
	 * Get the states for a country.
	 *
	 * @access public
	 * @param string $cc country code
	 * @return array of states
	 */
	public function get_states( $cc ) {
		return ( isset( $this->states[ $cc ] ) ) ? $this->states[ $cc ] : false;
	}


	/**
	 * Outputs the list of countries and states for use in dropdown boxes.
	 *
	 * @access public
	 * @param string $selected_country (default: '')
	 * @param string $selected_state (default: '')
	 * @param bool $escape (default: false)
	 * @return void
	 */
	public function country_dropdown_options( $selected_country = '', $selected_state = '', $escape = false ) {

		if ( apply_filters('woocommerce_sort_countries', true ) )
			asort( $this->countries );

		if ( $this->countries ) foreach ( $this->countries as $key=>$value) :
			if ( $states =  $this->get_states($key) ) :
				echo '<optgroup label="' . esc_attr( $value ) . '">';
    				foreach ($states as $state_key=>$state_value) :
    					echo '<option value="' . esc_attr( $key ) . ':'.$state_key.'"';

    					if ($selected_country==$key && $selected_state==$state_key) echo ' selected="selected"';

    					echo '>'.$value.' &mdash; '. ($escape ? esc_js($state_value) : $state_value) .'</option>';
    				endforeach;
    			echo '</optgroup>';
			else :
    			echo '<option';
    			if ($selected_country==$key && $selected_state=='*') echo ' selected="selected"';
    			echo ' value="' . esc_attr( $key ) . '">'. ($escape ? esc_js( $value ) : $value) .'</option>';
			endif;
		endforeach;
	}


	/**
	 * Get country address formats
	 *
	 * @access public
	 * @return array
	 */
	public function get_address_formats() {

		if (!$this->address_formats) :

			// Common formats
			$postcode_before_city = "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}";

			// Define address formats
			$this->address_formats = apply_filters('woocommerce_localisation_address_formats', array(
				'default' => "{name}\n{company}\n{address_1}\n{address_2}\n{city}\n{state}\n{postcode}\n{country}",
				'AU' => "{name}\n{company}\n{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}",
				'AT' => $postcode_before_city,
				'BE' => $postcode_before_city,
				'CA' => "{company}\n{name}\n{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}",
				'CH' => $postcode_before_city,
				'CN' => "{country} {postcode}\n{state}, {city}, {address_2}, {address_1}\n{company}\n{name}",
				'CZ' => $postcode_before_city,
				'DE' => $postcode_before_city,
				'EE' => $postcode_before_city,
				'FI' => $postcode_before_city,
				'DK' => $postcode_before_city,
				'FR' => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city_upper}\n{country}",
				'HK' => "{company}\n{first_name} {last_name_upper}\n{address_1}\n{address_2}\n{city_upper}\n{state_upper}\n{country}",
				'HU' => "{name}\n{company}\n{city}\n{address_1}\n{address_2}\n{postcode}\n{country}",
				'IS' => $postcode_before_city,
				'IT' => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode}\n{city}\n{state_upper}\n{country}",
				'JP' => "{postcode}\n{state}{city}{address_2}\n{address_1}\n{company}\n{last_name} {first_name}\n {country}",
				'LI' => $postcode_before_city,
				'NL' => $postcode_before_city,
				'NZ' => "{name}\n{company}\n{address_1}\n{address_2}\n{city} {postcode}\n{country}",
				'NO' => $postcode_before_city,
				'PL' => $postcode_before_city,
				'SK' => $postcode_before_city,
				'SI' => $postcode_before_city,
				'ES' => "{name}\n{company}\n{address_1}\n{address_2}\n{postcode} {city}\n{state}\n{country}",
				'SE' => $postcode_before_city,
				'TR' => "{name}\n{company}\n{address_1}\n{address_2}\n{postcode} {city} {state}\n{country}",
				'US' => "{name}\n{company}\n{address_1}\n{address_2}\n{city}, {state} {postcode}\n{country}",
				'VN' => "{name}\n{company}\n{address_1}\n{city}\n{country}",
			));
		endif;

		return $this->address_formats;
	}


	/**
	 * Get country address format
	 *
	 * @access public
	 * @param array $args (default: array())
	 * @return string address
	 */
	public function get_formatted_address( $args = array() ) {

		$args = array_map( 'trim', $args );

		extract( $args );

		// Get all formats
		$formats 		= $this->get_address_formats();

		// Get format for the address' country
		$format			= ( $country && isset( $formats[ $country ] ) ) ? $formats[ $country ] : $formats['default'];

		// Handle full country name
		$full_country 	= ( isset( $this->countries[ $country ] ) ) ? $this->countries[ $country ] : $country;

		// Country is not needed if the same as base
		if ( $country == $this->get_base_country() && ! apply_filters( 'woocommerce_formatted_address_force_country_display', false ) )
			$format = str_replace( '{country}', '', $format );

		// Handle full state name
		$full_state		= ( $country && $state && isset( $this->states[ $country ][ $state ] ) ) ? $this->states[ $country ][ $state ] : $state;

		// Substitute address parts into the string
		$replace = array_map( 'esc_html', apply_filters( 'woocommerce_formatted_address_replacements', array(
			'{first_name}'       => $first_name,
			'{last_name}'        => $last_name,
			'{name}'             => $first_name . ' ' . $last_name,
			'{company}'          => $company,
			'{address_1}'        => $address_1,
			'{address_2}'        => $address_2,
			'{city}'             => $city,
			'{state}'            => $full_state,
			'{postcode}'         => $postcode,
			'{country}'          => $full_country,
			'{first_name_upper}' => strtoupper( $first_name ),
			'{last_name_upper}'  => strtoupper( $last_name ),
			'{name_upper}'       => strtoupper( $first_name . ' ' . $last_name ),
			'{company_upper}'    => strtoupper( $company ),
			'{address_1_upper}'  => strtoupper( $address_1 ),
			'{address_2_upper}'  => strtoupper( $address_2 ),
			'{city_upper}'       => strtoupper( $city ),
			'{state_upper}'      => strtoupper( $full_state ),
			'{postcode_upper}'   => strtoupper( $postcode ),
			'{country_upper}'    => strtoupper( $full_country ),
		), $args ) );

		$formatted_address = str_replace( array_keys( $replace ), $replace, $format );

		// Clean up white space
		$formatted_address = preg_replace( '/  +/', ' ', trim( $formatted_address ) );
		$formatted_address = preg_replace( '/\n\n+/', "\n", $formatted_address );

		// Break newlines apart and remove empty lines/trim commas and white space
		$formatted_address = array_filter( array_map( array( $this, 'trim_formatted_address_line' ), explode( "\n", $formatted_address ) ) );

		// Add html breaks
		$formatted_address = implode( '<br/>', $formatted_address );

		// We're done!
		return $formatted_address;
	}

	/**
	 * trim white space and commans off a line
	 * @param  string
	 * @return string
	 */
	private function trim_formatted_address_line( $line ) {
		return trim( $line, ", " );
	}


	/**
	 * Returns the fields we show by default. This can be filtered later on.
	 *
	 * @access public
	 * @return array
	 */
	public function get_default_address_fields() {
		$fields = array(
			'country'            => array(
				'type'     => 'country',
				'label'    => __( 'Country', 'woocommerce' ),
				'required' => true,
				'class'    => array( 'form-row-wide', 'address-field', 'update_totals_on_change' ),
			),
			'first_name'         => array(
				'label'    => __( 'First Name', 'woocommerce' ),
				'required' => true,
				'class'    => array( 'form-row-first' ),
			),
			'last_name'          => array(
				'label'    => __( 'Last Name', 'woocommerce' ),
				'required' => true,
				'class'    => array( 'form-row-last' ),
				'clear'    => true
			),
			'company'            => array(
				'label' => __( 'Company Name', 'woocommerce' ),
				'class' => array( 'form-row-wide' ),
			),
			'address_1'          => array(
				'label'       => __( 'Address', 'woocommerce' ),
				'placeholder' => _x( 'Street address', 'placeholder', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-wide', 'address-field' )
			),
			'address_2'          => array(
				'placeholder' => _x( 'Apartment, suite, unit etc. (optional)', 'placeholder', 'woocommerce' ),
				'class'       => array( 'form-row-wide', 'address-field' ),
				'required'    => false
			),
			'city'               => array(
				'label'       => __( 'Town / City', 'woocommerce' ),
				'placeholder' => __( 'Town / City', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-wide', 'address-field' )
			),
			'state'              => array(
				'type'        => 'state',
				'label'       => __( 'State / County', 'woocommerce' ),
				'placeholder' => __( 'State / County', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-first', 'address-field' ),
				'validate'    => array( 'state' )
			),
			'postcode'           => array(
				'label'       => __( 'Postcode / Zip', 'woocommerce' ),
				'placeholder' => __( 'Postcode / Zip', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-last', 'address-field' ),
				'clear'       => true,
				'validate'    => array( 'postcode' )
			),
		);

		return apply_filters( 'woocommerce_default_address_fields', $fields );
	}

	/**
	 * Get JS selectors for fields which are shown/hidden depending on the locale.
	 *
	 * @access public
	 * @return array
	 */
	public function get_country_locale_field_selectors() {
		$locale_fields = array (
			'address_1'	=> '#billing_address_1_field, #shipping_address_1_field',
			'address_2'	=> '#billing_address_2_field, #shipping_address_2_field',
			'state'		=> '#billing_state_field, #shipping_state_field',
			'postcode'	=> '#billing_postcode_field, #shipping_postcode_field',
			'city'		=> '#billing_city_field, #shipping_city_field'
		);
		
		return apply_filters( 'woocommerce_country_locale_field_selectors', $locale_fields );
	}

	/**
	 * Get country locale settings
	 *
	 * @access public
	 * @return array
	 */
	public function get_country_locale() {
		if ( ! $this->locale ) {

			// Locale information used by the checkout
			$this->locale = apply_filters('woocommerce_get_country_locale', array(
				'AE' => array(
					'postcode' => array(
						'required' 	=> false,
						'hidden'	=> true
					),
				),
				'AF' => array(
					'state' => array(
						'required' => false,
					),
				),
				'AT' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'BD' => array(
					'postcode' => array(
						'required' => false
					),
					'state'    => array(
						'label' => __( 'District', 'woocommerce' ),
					)
				),
				'BE' => array(
					'postcode_before_city' => true,
					'state' => array(
						'required' => false,
						'label'    => __( 'Province', 'woocommerce' ),
					),
				),
				'BI' => array(
					'state' => array(
						'required' => false,
					),
				),
				'BO' => array(
					'postcode' => array(
						'required' 	=> false,
						'hidden'	=> true
					),
				),
				'CA' => array(
					'state'	=> array(
						'label'			=> __( 'Province', 'woocommerce' ),
					)
				),
				'CH' => array(
                    'postcode_before_city' => true,
                    'state' => array(
                        'label'         => __( 'Canton', 'woocommerce' ),
                        'required'      => false
                    )
                ),
				'CL' => array(
					'city'		=> array(
						'required' 	=> false,
					),
					'state'		=> array(
						'label'			=> __( 'Municipality', 'woocommerce' ),
					)
				),
				'CN' => array(
					'state'	=> array(
						'label'			=> __( 'Province', 'woocommerce' ),
					)
				),
				'CO' => array(
					'postcode' => array(
						'required' 	=> false
					)
				),
				'CZ' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'DE' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'DK' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'EE' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'FI' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'FR' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'HK' => array(
					'postcode'	=> array(
						'required' => false
					),
					'city'	=> array(
						'label'				=> __( 'Town / District', 'woocommerce' ),
					),
					'state'		=> array(
						'label' 		=> __( 'Region', 'woocommerce' ),
					)
				),
				'HU' => array(
					'state' => array(
					    'label'         => __( 'County', 'woocommerce' ),
					)
				),
				'ID' => array(
	                'state' => array(
	                    'label'         => __( 'Province', 'woocommerce' ),
	                )
            	),
				'IS' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'IL' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'IT' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => true,
						'label'    => __( 'Province', 'woocommerce' ),
					)
				),
				'JP' => array(
					'state'		=> array(
						'label'    => __( 'Province', 'woocommerce' )
					)
				),
				'KR' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'NL' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false,
						'label'    => __( 'Province', 'woocommerce' ),
					)
				),
				'NZ' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'NO' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'PL' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'PT' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'RO' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'SG' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'SK' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'SI' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'ES' => array(
					'postcode_before_city' => true,
					'state'	=> array(
						'label'			=> __( 'Province', 'woocommerce' ),
					)
				),
				'LI' => array(
                    'postcode_before_city' => true,
                    'state' => array(
                        'label'         => __( 'Municipality', 'woocommerce' ),
                        'required'      => false
                    )
                ),
				'LK' => array(
					'state'	=> array(
						'required' => false
					)
				),
				'SE' => array(
					'postcode_before_city' => true,
					'state'	=> array(
						'required' => false
					)
				),
				'TR' => array(
					'postcode_before_city' => true,
					'state'	=> array(
						'label'			=> __( 'Province', 'woocommerce' ),
					)
				),
				'US' => array(
					'postcode'	=> array(
						'label' 		=> __( 'Zip', 'woocommerce' ),
					),
					'state'		=> array(
						'label' 		=> __( 'State', 'woocommerce' ),
					)
				),
				'GB' => array(
					'postcode'	=> array(
						'label' 		=> __( 'Postcode', 'woocommerce' ),
					),
					'state'		=> array(
						'label' 		=> __( 'County', 'woocommerce' ),
						'required' 		=> false
					)
				),
				'VN' => array(
					'state'		=> array(
						'required' => false
					),
					'postcode' => array(
						'required' 	=> false,
						'hidden'	=> true
					),
					'address_2' => array(
						'required' 	=> false,
						'hidden'	=> true
					)
				),
				'WS' => array(
					'postcode' => array(
						'required' 	=> false,
						'hidden'	=> true
					),
				),
				'ZA' => array(
					'state'	=> array(
						'label'			=> __( 'Province', 'woocommerce' ),
					)
				),
				'ZW' => array(
					'postcode' => array(
						'required' 	=> false,
						'hidden'	=> true
					),
				),
			));

			$this->locale = array_intersect_key( $this->locale, array_merge( $this->get_allowed_countries(), $this->get_shipping_countries() ) );

			// Default Locale Can be filters to override fields in get_address_fields().
			// Countries with no specific locale will use default.
			$this->locale['default'] = apply_filters('woocommerce_get_country_locale_default', $this->get_default_address_fields() );

			// Filter default AND shop base locales to allow overides via a single function. These will be used when changing countries on the checkout
			if ( ! isset( $this->locale[ $this->get_base_country() ] ) )
				$this->locale[ $this->get_base_country() ] = $this->locale['default'];

			$this->locale['default'] 					= apply_filters( 'woocommerce_get_country_locale_base', $this->locale['default'] );
			$this->locale[ $this->get_base_country() ] 	= apply_filters( 'woocommerce_get_country_locale_base', $this->locale[ $this->get_base_country() ] );
		}

		return $this->locale;
	}

	/**
	 * Apply locale and get address fields
	 *
	 * @access public
	 * @param mixed $country
	 * @param string $type (default: 'billing_')
	 * @return array
	 */
	public function get_address_fields( $country, $type = 'billing_' ) {

		if (!$country)
            $country = $this->get_base_country();

		$fields     = $this->get_default_address_fields();
		$locale		= $this->get_country_locale();

		if ( isset( $locale[ $country ] ) ) {

			$fields = wc_array_overlay( $fields, $locale[ $country ] );

			// If default country has postcode_before_city switch the fields round.
			// This is only done at this point, not if country changes on checkout.
			if ( isset( $locale[ $country ]['postcode_before_city'] ) ) {
				if ( isset( $fields['postcode'] ) ) {
					$fields['postcode']['class'] = array( 'form-row-wide', 'address-field' );

					$switch_fields = array();

					foreach ( $fields as $key => $value ) {
						if ( $key == 'city' ) {
							// Place postcode before city
							$switch_fields['postcode'] = '';
						}
						$switch_fields[$key] = $value;
					}

					$fields = $switch_fields;
				}
			}
		}

		// Prepend field keys
		$address_fields = array();

		foreach ( $fields as $key => $value ) {
			$address_fields[$type . $key] = $value;
		}

		// Billing/Shipping Specific
		if ( $type == 'billing_' ) {

			$address_fields['billing_email'] = array(
				'label' 		=> __( 'Email Address', 'woocommerce' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-first' ),
				'validate'		=> array( 'email' ),
			);
			$address_fields['billing_phone'] = array(
				'label' 		=> __( 'Phone', 'woocommerce' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-last' ),
				'clear'			=> true,
				'validate'		=> array( 'phone' ),
			);

		}

		$address_fields = apply_filters( 'woocommerce_' . $type . 'fields', $address_fields, $country );

		// Return
		return $address_fields;
	}
}
