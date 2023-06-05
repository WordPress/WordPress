<?php
/**
 * Geo IP class
 *
 * This class is a fork of GeoIP class from MaxMind LLC.
 *
 * @package    WooCommerce\Classes
 * @version    2.4.0
 * @deprecated 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Geo_IP Class.
 *
 * @deprecated 3.4.0
 */
class WC_Geo_IP {

	const GEOIP_COUNTRY_BEGIN            = 16776960;
	const GEOIP_STATE_BEGIN_REV0         = 16700000;
	const GEOIP_STATE_BEGIN_REV1         = 16000000;
	const GEOIP_MEMORY_CACHE             = 1;
	const GEOIP_SHARED_MEMORY            = 2;
	const STRUCTURE_INFO_MAX_SIZE        = 20;
	const GEOIP_COUNTRY_EDITION          = 1;
	const GEOIP_PROXY_EDITION            = 8;
	const GEOIP_ASNUM_EDITION            = 9;
	const GEOIP_NETSPEED_EDITION         = 10;
	const GEOIP_REGION_EDITION_REV0      = 7;
	const GEOIP_REGION_EDITION_REV1      = 3;
	const GEOIP_CITY_EDITION_REV0        = 6;
	const GEOIP_CITY_EDITION_REV1        = 2;
	const GEOIP_ORG_EDITION              = 5;
	const GEOIP_ISP_EDITION              = 4;
	const SEGMENT_RECORD_LENGTH          = 3;
	const STANDARD_RECORD_LENGTH         = 3;
	const ORG_RECORD_LENGTH              = 4;
	const GEOIP_SHM_KEY                  = 0x4f415401;
	const GEOIP_DOMAIN_EDITION           = 11;
	const GEOIP_COUNTRY_EDITION_V6       = 12;
	const GEOIP_LOCATIONA_EDITION        = 13;
	const GEOIP_ACCURACYRADIUS_EDITION   = 14;
	const GEOIP_CITY_EDITION_REV1_V6     = 30;
	const GEOIP_CITY_EDITION_REV0_V6     = 31;
	const GEOIP_NETSPEED_EDITION_REV1    = 32;
	const GEOIP_NETSPEED_EDITION_REV1_V6 = 33;
	const GEOIP_USERTYPE_EDITION         = 28;
	const GEOIP_USERTYPE_EDITION_V6      = 29;
	const GEOIP_ASNUM_EDITION_V6         = 21;
	const GEOIP_ISP_EDITION_V6           = 22;
	const GEOIP_ORG_EDITION_V6           = 23;
	const GEOIP_DOMAIN_EDITION_V6        = 24;

	/**
	 * Flags.
	 *
	 * @var int
	 */
	public $flags;

	/**
	 * File handler.
	 *
	 * @var resource
	 */
	public $filehandle;

	/**
	 * Memory buffer.
	 *
	 * @var string
	 */
	public $memory_buffer;

	/**
	 * Database type.
	 *
	 * @var int
	 */
	public $databaseType;

	/**
	 * Database segments.
	 *
	 * @var int
	 */
	public $databaseSegments;

	/**
	 * Record length.
	 *
	 * @var int
	 */
	public $record_length;

	/**
	 * Shmid.
	 *
	 * @var string
	 */
	public $shmid;

	/**
	 * Two letters country codes.
	 *
	 * @var array
	 */
	public $GEOIP_COUNTRY_CODES = array(
		'',
		'AP',
		'EU',
		'AD',
		'AE',
		'AF',
		'AG',
		'AI',
		'AL',
		'AM',
		'CW',
		'AO',
		'AQ',
		'AR',
		'AS',
		'AT',
		'AU',
		'AW',
		'AZ',
		'BA',
		'BB',
		'BD',
		'BE',
		'BF',
		'BG',
		'BH',
		'BI',
		'BJ',
		'BM',
		'BN',
		'BO',
		'BR',
		'BS',
		'BT',
		'BV',
		'BW',
		'BY',
		'BZ',
		'CA',
		'CC',
		'CD',
		'CF',
		'CG',
		'CH',
		'CI',
		'CK',
		'CL',
		'CM',
		'CN',
		'CO',
		'CR',
		'CU',
		'CV',
		'CX',
		'CY',
		'CZ',
		'DE',
		'DJ',
		'DK',
		'DM',
		'DO',
		'DZ',
		'EC',
		'EE',
		'EG',
		'EH',
		'ER',
		'ES',
		'ET',
		'FI',
		'FJ',
		'FK',
		'FM',
		'FO',
		'FR',
		'SX',
		'GA',
		'GB',
		'GD',
		'GE',
		'GF',
		'GH',
		'GI',
		'GL',
		'GM',
		'GN',
		'GP',
		'GQ',
		'GR',
		'GS',
		'GT',
		'GU',
		'GW',
		'GY',
		'HK',
		'HM',
		'HN',
		'HR',
		'HT',
		'HU',
		'ID',
		'IE',
		'IL',
		'IN',
		'IO',
		'IQ',
		'IR',
		'IS',
		'IT',
		'JM',
		'JO',
		'JP',
		'KE',
		'KG',
		'KH',
		'KI',
		'KM',
		'KN',
		'KP',
		'KR',
		'KW',
		'KY',
		'KZ',
		'LA',
		'LB',
		'LC',
		'LI',
		'LK',
		'LR',
		'LS',
		'LT',
		'LU',
		'LV',
		'LY',
		'MA',
		'MC',
		'MD',
		'MG',
		'MH',
		'MK',
		'ML',
		'MM',
		'MN',
		'MO',
		'MP',
		'MQ',
		'MR',
		'MS',
		'MT',
		'MU',
		'MV',
		'MW',
		'MX',
		'MY',
		'MZ',
		'NA',
		'NC',
		'NE',
		'NF',
		'NG',
		'NI',
		'NL',
		'NO',
		'NP',
		'NR',
		'NU',
		'NZ',
		'OM',
		'PA',
		'PE',
		'PF',
		'PG',
		'PH',
		'PK',
		'PL',
		'PM',
		'PN',
		'PR',
		'PS',
		'PT',
		'PW',
		'PY',
		'QA',
		'RE',
		'RO',
		'RU',
		'RW',
		'SA',
		'SB',
		'SC',
		'SD',
		'SE',
		'SG',
		'SH',
		'SI',
		'SJ',
		'SK',
		'SL',
		'SM',
		'SN',
		'SO',
		'SR',
		'ST',
		'SV',
		'SY',
		'SZ',
		'TC',
		'TD',
		'TF',
		'TG',
		'TH',
		'TJ',
		'TK',
		'TM',
		'TN',
		'TO',
		'TL',
		'TR',
		'TT',
		'TV',
		'TW',
		'TZ',
		'UA',
		'UG',
		'UM',
		'US',
		'UY',
		'UZ',
		'VA',
		'VC',
		'VE',
		'VG',
		'VI',
		'VN',
		'VU',
		'WF',
		'WS',
		'YE',
		'YT',
		'RS',
		'ZA',
		'ZM',
		'ME',
		'ZW',
		'A1',
		'A2',
		'O1',
		'AX',
		'GG',
		'IM',
		'JE',
		'BL',
		'MF',
		'BQ',
		'SS',
		'O1',
	);

	/**
	 * 3 letters country codes.
	 *
	 * @var array
	 */
	public $GEOIP_COUNTRY_CODES3 = array(
		'',
		'AP',
		'EU',
		'AND',
		'ARE',
		'AFG',
		'ATG',
		'AIA',
		'ALB',
		'ARM',
		'CUW',
		'AGO',
		'ATA',
		'ARG',
		'ASM',
		'AUT',
		'AUS',
		'ABW',
		'AZE',
		'BIH',
		'BRB',
		'BGD',
		'BEL',
		'BFA',
		'BGR',
		'BHR',
		'BDI',
		'BEN',
		'BMU',
		'BRN',
		'BOL',
		'BRA',
		'BHS',
		'BTN',
		'BVT',
		'BWA',
		'BLR',
		'BLZ',
		'CAN',
		'CCK',
		'COD',
		'CAF',
		'COG',
		'CHE',
		'CIV',
		'COK',
		'CHL',
		'CMR',
		'CHN',
		'COL',
		'CRI',
		'CUB',
		'CPV',
		'CXR',
		'CYP',
		'CZE',
		'DEU',
		'DJI',
		'DNK',
		'DMA',
		'DOM',
		'DZA',
		'ECU',
		'EST',
		'EGY',
		'ESH',
		'ERI',
		'ESP',
		'ETH',
		'FIN',
		'FJI',
		'FLK',
		'FSM',
		'FRO',
		'FRA',
		'SXM',
		'GAB',
		'GBR',
		'GRD',
		'GEO',
		'GUF',
		'GHA',
		'GIB',
		'GRL',
		'GMB',
		'GIN',
		'GLP',
		'GNQ',
		'GRC',
		'SGS',
		'GTM',
		'GUM',
		'GNB',
		'GUY',
		'HKG',
		'HMD',
		'HND',
		'HRV',
		'HTI',
		'HUN',
		'IDN',
		'IRL',
		'ISR',
		'IND',
		'IOT',
		'IRQ',
		'IRN',
		'ISL',
		'ITA',
		'JAM',
		'JOR',
		'JPN',
		'KEN',
		'KGZ',
		'KHM',
		'KIR',
		'COM',
		'KNA',
		'PRK',
		'KOR',
		'KWT',
		'CYM',
		'KAZ',
		'LAO',
		'LBN',
		'LCA',
		'LIE',
		'LKA',
		'LBR',
		'LSO',
		'LTU',
		'LUX',
		'LVA',
		'LBY',
		'MAR',
		'MCO',
		'MDA',
		'MDG',
		'MHL',
		'MKD',
		'MLI',
		'MMR',
		'MNG',
		'MAC',
		'MNP',
		'MTQ',
		'MRT',
		'MSR',
		'MLT',
		'MUS',
		'MDV',
		'MWI',
		'MEX',
		'MYS',
		'MOZ',
		'NAM',
		'NCL',
		'NER',
		'NFK',
		'NGA',
		'NIC',
		'NLD',
		'NOR',
		'NPL',
		'NRU',
		'NIU',
		'NZL',
		'OMN',
		'PAN',
		'PER',
		'PYF',
		'PNG',
		'PHL',
		'PAK',
		'POL',
		'SPM',
		'PCN',
		'PRI',
		'PSE',
		'PRT',
		'PLW',
		'PRY',
		'QAT',
		'REU',
		'ROU',
		'RUS',
		'RWA',
		'SAU',
		'SLB',
		'SYC',
		'SDN',
		'SWE',
		'SGP',
		'SHN',
		'SVN',
		'SJM',
		'SVK',
		'SLE',
		'SMR',
		'SEN',
		'SOM',
		'SUR',
		'STP',
		'SLV',
		'SYR',
		'SWZ',
		'TCA',
		'TCD',
		'ATF',
		'TGO',
		'THA',
		'TJK',
		'TKL',
		'TKM',
		'TUN',
		'TON',
		'TLS',
		'TUR',
		'TTO',
		'TUV',
		'TWN',
		'TZA',
		'UKR',
		'UGA',
		'UMI',
		'USA',
		'URY',
		'UZB',
		'VAT',
		'VCT',
		'VEN',
		'VGB',
		'VIR',
		'VNM',
		'VUT',
		'WLF',
		'WSM',
		'YEM',
		'MYT',
		'SRB',
		'ZAF',
		'ZMB',
		'MNE',
		'ZWE',
		'A1',
		'A2',
		'O1',
		'ALA',
		'GGY',
		'IMN',
		'JEY',
		'BLM',
		'MAF',
		'BES',
		'SSD',
		'O1',
	);

	/**
	 * Contry names.
	 *
	 * @var array
	 */
	public $GEOIP_COUNTRY_NAMES = array(
		'',
		'Asia/Pacific Region',
		'Europe',
		'Andorra',
		'United Arab Emirates',
		'Afghanistan',
		'Antigua and Barbuda',
		'Anguilla',
		'Albania',
		'Armenia',
		'Curacao',
		'Angola',
		'Antarctica',
		'Argentina',
		'American Samoa',
		'Austria',
		'Australia',
		'Aruba',
		'Azerbaijan',
		'Bosnia and Herzegovina',
		'Barbados',
		'Bangladesh',
		'Belgium',
		'Burkina Faso',
		'Bulgaria',
		'Bahrain',
		'Burundi',
		'Benin',
		'Bermuda',
		'Brunei Darussalam',
		'Bolivia',
		'Brazil',
		'Bahamas',
		'Bhutan',
		'Bouvet Island',
		'Botswana',
		'Belarus',
		'Belize',
		'Canada',
		'Cocos (Keeling) Islands',
		'Congo, The Democratic Republic of the',
		'Central African Republic',
		'Congo',
		'Switzerland',
		"Cote D'Ivoire",
		'Cook Islands',
		'Chile',
		'Cameroon',
		'China',
		'Colombia',
		'Costa Rica',
		'Cuba',
		'Cape Verde',
		'Christmas Island',
		'Cyprus',
		'Czech Republic',
		'Germany',
		'Djibouti',
		'Denmark',
		'Dominica',
		'Dominican Republic',
		'Algeria',
		'Ecuador',
		'Estonia',
		'Egypt',
		'Western Sahara',
		'Eritrea',
		'Spain',
		'Ethiopia',
		'Finland',
		'Fiji',
		'Falkland Islands (Malvinas)',
		'Micronesia, Federated States of',
		'Faroe Islands',
		'France',
		'Sint Maarten (Dutch part)',
		'Gabon',
		'United Kingdom',
		'Grenada',
		'Georgia',
		'French Guiana',
		'Ghana',
		'Gibraltar',
		'Greenland',
		'Gambia',
		'Guinea',
		'Guadeloupe',
		'Equatorial Guinea',
		'Greece',
		'South Georgia and the South Sandwich Islands',
		'Guatemala',
		'Guam',
		'Guinea-Bissau',
		'Guyana',
		'Hong Kong',
		'Heard Island and McDonald Islands',
		'Honduras',
		'Croatia',
		'Haiti',
		'Hungary',
		'Indonesia',
		'Ireland',
		'Israel',
		'India',
		'British Indian Ocean Territory',
		'Iraq',
		'Iran, Islamic Republic of',
		'Iceland',
		'Italy',
		'Jamaica',
		'Jordan',
		'Japan',
		'Kenya',
		'Kyrgyzstan',
		'Cambodia',
		'Kiribati',
		'Comoros',
		'Saint Kitts and Nevis',
		"Korea, Democratic People's Republic of",
		'Korea, Republic of',
		'Kuwait',
		'Cayman Islands',
		'Kazakhstan',
		"Lao People's Democratic Republic",
		'Lebanon',
		'Saint Lucia',
		'Liechtenstein',
		'Sri Lanka',
		'Liberia',
		'Lesotho',
		'Lithuania',
		'Luxembourg',
		'Latvia',
		'Libya',
		'Morocco',
		'Monaco',
		'Moldova, Republic of',
		'Madagascar',
		'Marshall Islands',
		'Macedonia',
		'Mali',
		'Myanmar',
		'Mongolia',
		'Macau',
		'Northern Mariana Islands',
		'Martinique',
		'Mauritania',
		'Montserrat',
		'Malta',
		'Mauritius',
		'Maldives',
		'Malawi',
		'Mexico',
		'Malaysia',
		'Mozambique',
		'Namibia',
		'New Caledonia',
		'Niger',
		'Norfolk Island',
		'Nigeria',
		'Nicaragua',
		'Netherlands',
		'Norway',
		'Nepal',
		'Nauru',
		'Niue',
		'New Zealand',
		'Oman',
		'Panama',
		'Peru',
		'French Polynesia',
		'Papua New Guinea',
		'Philippines',
		'Pakistan',
		'Poland',
		'Saint Pierre and Miquelon',
		'Pitcairn Islands',
		'Puerto Rico',
		'Palestinian Territory',
		'Portugal',
		'Palau',
		'Paraguay',
		'Qatar',
		'Reunion',
		'Romania',
		'Russian Federation',
		'Rwanda',
		'Saudi Arabia',
		'Solomon Islands',
		'Seychelles',
		'Sudan',
		'Sweden',
		'Singapore',
		'Saint Helena',
		'Slovenia',
		'Svalbard and Jan Mayen',
		'Slovakia',
		'Sierra Leone',
		'San Marino',
		'Senegal',
		'Somalia',
		'Suriname',
		'Sao Tome and Principe',
		'El Salvador',
		'Syrian Arab Republic',
		'Eswatini',
		'Turks and Caicos Islands',
		'Chad',
		'French Southern Territories',
		'Togo',
		'Thailand',
		'Tajikistan',
		'Tokelau',
		'Turkmenistan',
		'Tunisia',
		'Tonga',
		'Timor-Leste',
		'Turkey',
		'Trinidad and Tobago',
		'Tuvalu',
		'Taiwan',
		'Tanzania, United Republic of',
		'Ukraine',
		'Uganda',
		'United States Minor Outlying Islands',
		'United States',
		'Uruguay',
		'Uzbekistan',
		'Holy See (Vatican City State)',
		'Saint Vincent and the Grenadines',
		'Venezuela',
		'Virgin Islands, British',
		'Virgin Islands, U.S.',
		'Vietnam',
		'Vanuatu',
		'Wallis and Futuna',
		'Samoa',
		'Yemen',
		'Mayotte',
		'Serbia',
		'South Africa',
		'Zambia',
		'Montenegro',
		'Zimbabwe',
		'Anonymous Proxy',
		'Satellite Provider',
		'Other',
		'Aland Islands',
		'Guernsey',
		'Isle of Man',
		'Jersey',
		'Saint Barthelemy',
		'Saint Martin',
		'Bonaire, Saint Eustatius and Saba',
		'South Sudan',
		'Other',
	);

	/**
	 * 2 letters continent codes.
	 *
	 * @var array
	 */
	public $GEOIP_CONTINENT_CODES = array(
		'--',
		'AS',
		'EU',
		'EU',
		'AS',
		'AS',
		'NA',
		'NA',
		'EU',
		'AS',
		'NA',
		'AF',
		'AN',
		'SA',
		'OC',
		'EU',
		'OC',
		'NA',
		'AS',
		'EU',
		'NA',
		'AS',
		'EU',
		'AF',
		'EU',
		'AS',
		'AF',
		'AF',
		'NA',
		'AS',
		'SA',
		'SA',
		'NA',
		'AS',
		'AN',
		'AF',
		'EU',
		'NA',
		'NA',
		'AS',
		'AF',
		'AF',
		'AF',
		'EU',
		'AF',
		'OC',
		'SA',
		'AF',
		'AS',
		'SA',
		'NA',
		'NA',
		'AF',
		'AS',
		'AS',
		'EU',
		'EU',
		'AF',
		'EU',
		'NA',
		'NA',
		'AF',
		'SA',
		'EU',
		'AF',
		'AF',
		'AF',
		'EU',
		'AF',
		'EU',
		'OC',
		'SA',
		'OC',
		'EU',
		'EU',
		'NA',
		'AF',
		'EU',
		'NA',
		'AS',
		'SA',
		'AF',
		'EU',
		'NA',
		'AF',
		'AF',
		'NA',
		'AF',
		'EU',
		'AN',
		'NA',
		'OC',
		'AF',
		'SA',
		'AS',
		'AN',
		'NA',
		'EU',
		'NA',
		'EU',
		'AS',
		'EU',
		'AS',
		'AS',
		'AS',
		'AS',
		'AS',
		'EU',
		'EU',
		'NA',
		'AS',
		'AS',
		'AF',
		'AS',
		'AS',
		'OC',
		'AF',
		'NA',
		'AS',
		'AS',
		'AS',
		'NA',
		'AS',
		'AS',
		'AS',
		'NA',
		'EU',
		'AS',
		'AF',
		'AF',
		'EU',
		'EU',
		'EU',
		'AF',
		'AF',
		'EU',
		'EU',
		'AF',
		'OC',
		'EU',
		'AF',
		'AS',
		'AS',
		'AS',
		'OC',
		'NA',
		'AF',
		'NA',
		'EU',
		'AF',
		'AS',
		'AF',
		'NA',
		'AS',
		'AF',
		'AF',
		'OC',
		'AF',
		'OC',
		'AF',
		'NA',
		'EU',
		'EU',
		'AS',
		'OC',
		'OC',
		'OC',
		'AS',
		'NA',
		'SA',
		'OC',
		'OC',
		'AS',
		'AS',
		'EU',
		'NA',
		'OC',
		'NA',
		'AS',
		'EU',
		'OC',
		'SA',
		'AS',
		'AF',
		'EU',
		'EU',
		'AF',
		'AS',
		'OC',
		'AF',
		'AF',
		'EU',
		'AS',
		'AF',
		'EU',
		'EU',
		'EU',
		'AF',
		'EU',
		'AF',
		'AF',
		'SA',
		'AF',
		'NA',
		'AS',
		'AF',
		'NA',
		'AF',
		'AN',
		'AF',
		'AS',
		'AS',
		'OC',
		'AS',
		'AF',
		'OC',
		'AS',
		'EU',
		'NA',
		'OC',
		'AS',
		'AF',
		'EU',
		'AF',
		'OC',
		'NA',
		'SA',
		'AS',
		'EU',
		'NA',
		'SA',
		'NA',
		'NA',
		'AS',
		'OC',
		'OC',
		'OC',
		'AS',
		'AF',
		'EU',
		'AF',
		'AF',
		'EU',
		'AF',
		'--',
		'--',
		'--',
		'EU',
		'EU',
		'EU',
		'EU',
		'NA',
		'NA',
		'NA',
		'AF',
		'--',
	);

	/** @var WC_Logger Logger instance */
	public static $log = false;

	/**
	 * Logging method.
	 *
	 * @param string $message Log message.
	 * @param string $level   Optional. Default 'info'.
	 *     emergency|alert|critical|error|warning|notice|info|debug
	 */
	public static function log( $message, $level = 'info' ) {
		if ( empty( self::$log ) ) {
			self::$log = wc_get_logger();
		}
		self::$log->log( $level, $message, array( 'source' => 'geoip' ) );
	}

	/**
	 * Open geoip file.
	 *
	 * @param string $filename
	 * @param int    $flags
	 */
	public function geoip_open( $filename, $flags ) {
		$this->flags = $flags;
		if ( $this->flags & self::GEOIP_SHARED_MEMORY ) {
			$this->shmid = @shmop_open( self::GEOIP_SHM_KEY, 'a', 0, 0 );
		} else {
			if ( $this->filehandle = fopen( $filename, 'rb' ) ) {
				if ( $this->flags & self::GEOIP_MEMORY_CACHE ) {
					$s_array = fstat( $this->filehandle );
					$this->memory_buffer = fread( $this->filehandle, $s_array['size'] );
				}
			} else {
				$this->log( 'GeoIP API: Can not open ' . $filename, 'error' );
			}
		}

		$this->_setup_segments();
	}

	/**
	 * Setup segments.
	 *
	 * @return WC_Geo_IP instance
	 */
	private function _setup_segments() {
		$this->databaseType  = self::GEOIP_COUNTRY_EDITION;
		$this->record_length = self::STANDARD_RECORD_LENGTH;

		if ( $this->flags & self::GEOIP_SHARED_MEMORY ) {
			$offset = @shmop_size( $this->shmid ) - 3;

			for ( $i = 0; $i < self::STRUCTURE_INFO_MAX_SIZE; $i++ ) {
				$delim   = @shmop_read( $this->shmid, $offset, 3 );
				$offset += 3;

				if ( ( chr( 255 ) . chr( 255 ) . chr( 255 ) ) == $delim ) {
					$this->databaseType = ord( @shmop_read( $this->shmid, $offset, 1 ) );

					if ( $this->databaseType >= 106 ) {
						$this->databaseType -= 105;
					}

					$offset++;

					if ( self::GEOIP_REGION_EDITION_REV0 == $this->databaseType ) {
						$this->databaseSegments = self::GEOIP_STATE_BEGIN_REV0;
					} elseif ( self::GEOIP_REGION_EDITION_REV1 == $this->databaseType ) {
						$this->databaseSegments = self::GEOIP_STATE_BEGIN_REV1;
					} elseif ( ( self::GEOIP_CITY_EDITION_REV0 == $this->databaseType )
						|| ( self::GEOIP_CITY_EDITION_REV1 == $this->databaseType )
						|| ( self::GEOIP_ORG_EDITION == $this->databaseType )
						|| ( self::GEOIP_ORG_EDITION_V6 == $this->databaseType )
						|| ( self::GEOIP_DOMAIN_EDITION == $this->databaseType )
						|| ( self::GEOIP_DOMAIN_EDITION_V6 == $this->databaseType )
						|| ( self::GEOIP_ISP_EDITION == $this->databaseType )
						|| ( self::GEOIP_ISP_EDITION_V6 == $this->databaseType )
						|| ( self::GEOIP_USERTYPE_EDITION == $this->databaseType )
						|| ( self::GEOIP_USERTYPE_EDITION_V6 == $this->databaseType )
						|| ( self::GEOIP_LOCATIONA_EDITION == $this->databaseType )
						|| ( self::GEOIP_ACCURACYRADIUS_EDITION == $this->databaseType )
						|| ( self::GEOIP_CITY_EDITION_REV0_V6 == $this->databaseType )
						|| ( self::GEOIP_CITY_EDITION_REV1_V6 == $this->databaseType )
						|| ( self::GEOIP_NETSPEED_EDITION_REV1 == $this->databaseType )
						|| ( self::GEOIP_NETSPEED_EDITION_REV1_V6 == $this->databaseType )
						|| ( self::GEOIP_ASNUM_EDITION == $this->databaseType )
						|| ( self::GEOIP_ASNUM_EDITION_V6 == $this->databaseType )
					) {
						$this->databaseSegments = 0;
						$buf                    = @shmop_read( $this->shmid, $offset, self::SEGMENT_RECORD_LENGTH );

						for ( $j = 0; $j < self::SEGMENT_RECORD_LENGTH; $j++ ) {
							$this->databaseSegments += ( ord( $buf[ $j ] ) << ( $j * 8 ) );
						}

						if ( ( self::GEOIP_ORG_EDITION == $this->databaseType )
							|| ( self::GEOIP_ORG_EDITION_V6 == $this->databaseType )
							|| ( self::GEOIP_DOMAIN_EDITION == $this->databaseType )
							|| ( self::GEOIP_DOMAIN_EDITION_V6 == $this->databaseType )
							|| ( self::GEOIP_ISP_EDITION == $this->databaseType )
							|| ( self::GEOIP_ISP_EDITION_V6 == $this->databaseType )
						) {
							$this->record_length = self::ORG_RECORD_LENGTH;
						}
					}

					break;
				} else {
					$offset -= 4;
				}
			}
			if ( ( self::GEOIP_COUNTRY_EDITION == $this->databaseType )
				|| ( self::GEOIP_COUNTRY_EDITION_V6 == $this->databaseType )
				|| ( self::GEOIP_PROXY_EDITION == $this->databaseType )
				|| ( self::GEOIP_NETSPEED_EDITION == $this->databaseType )
			) {
				$this->databaseSegments = self::GEOIP_COUNTRY_BEGIN;
			}
		} else {
			$filepos = ftell( $this->filehandle );
			fseek( $this->filehandle, -3, SEEK_END );

			for ( $i = 0; $i < self::STRUCTURE_INFO_MAX_SIZE; $i++ ) {

				$delim = fread( $this->filehandle, 3 );
				if ( ( chr( 255 ) . chr( 255 ) . chr( 255 ) ) == $delim ) {

					$this->databaseType = ord( fread( $this->filehandle, 1 ) );
					if ( $this->databaseType >= 106 ) {
						$this->databaseType -= 105;
					}

					if ( self::GEOIP_REGION_EDITION_REV0 == $this->databaseType ) {
						$this->databaseSegments = self::GEOIP_STATE_BEGIN_REV0;
					} elseif ( self::GEOIP_REGION_EDITION_REV1 == $this->databaseType ) {
						$this->databaseSegments = self::GEOIP_STATE_BEGIN_REV1;
					} elseif ( ( self::GEOIP_CITY_EDITION_REV0 == $this->databaseType )
						|| ( self::GEOIP_CITY_EDITION_REV1 == $this->databaseType )
						|| ( self::GEOIP_CITY_EDITION_REV0_V6 == $this->databaseType )
						|| ( self::GEOIP_CITY_EDITION_REV1_V6 == $this->databaseType )
						|| ( self::GEOIP_ORG_EDITION == $this->databaseType )
						|| ( self::GEOIP_DOMAIN_EDITION == $this->databaseType )
						|| ( self::GEOIP_ISP_EDITION == $this->databaseType )
						|| ( self::GEOIP_ORG_EDITION_V6 == $this->databaseType )
						|| ( self::GEOIP_DOMAIN_EDITION_V6 == $this->databaseType )
						|| ( self::GEOIP_ISP_EDITION_V6 == $this->databaseType )
						|| ( self::GEOIP_LOCATIONA_EDITION == $this->databaseType )
						|| ( self::GEOIP_ACCURACYRADIUS_EDITION == $this->databaseType )
						|| ( self::GEOIP_NETSPEED_EDITION_REV1 == $this->databaseType )
						|| ( self::GEOIP_NETSPEED_EDITION_REV1_V6 == $this->databaseType )
						|| ( self::GEOIP_USERTYPE_EDITION == $this->databaseType )
						|| ( self::GEOIP_USERTYPE_EDITION_V6 == $this->databaseType )
						|| ( self::GEOIP_ASNUM_EDITION == $this->databaseType )
						|| ( self::GEOIP_ASNUM_EDITION_V6 == $this->databaseType )
					) {
						$this->databaseSegments = 0;
						$buf = fread( $this->filehandle, self::SEGMENT_RECORD_LENGTH );

						for ( $j = 0; $j < self::SEGMENT_RECORD_LENGTH; $j++ ) {
							$this->databaseSegments += ( ord( $buf[ $j ] ) << ( $j * 8 ) );
						}

						if ( ( self::GEOIP_ORG_EDITION == $this->databaseType )
							|| ( self::GEOIP_DOMAIN_EDITION == $this->databaseType )
							|| ( self::GEOIP_ISP_EDITION == $this->databaseType )
							|| ( self::GEOIP_ORG_EDITION_V6 == $this->databaseType )
							|| ( self::GEOIP_DOMAIN_EDITION_V6 == $this->databaseType )
							|| ( self::GEOIP_ISP_EDITION_V6 == $this->databaseType )
						) {
							$this->record_length = self::ORG_RECORD_LENGTH;
						}
					}

					break;
				} else {
					fseek( $this->filehandle, -4, SEEK_CUR );
				}
			}

			if ( ( self::GEOIP_COUNTRY_EDITION == $this->databaseType )
				|| ( self::GEOIP_COUNTRY_EDITION_V6 == $this->databaseType )
				|| ( self::GEOIP_PROXY_EDITION == $this->databaseType )
				|| ( self::GEOIP_NETSPEED_EDITION == $this->databaseType )
			) {
				$this->databaseSegments = self::GEOIP_COUNTRY_BEGIN;
			}

			fseek( $this->filehandle, $filepos, SEEK_SET );
		}

		return $this;
	}

	/**
	 * Close geoip file.
	 *
	 * @return bool
	 */
	public function geoip_close() {
		if ( $this->flags & self::GEOIP_SHARED_MEMORY ) {
			return true;
		}

		return fclose( $this->filehandle );
	}

	/**
	 * Common get record.
	 *
	 * @param  string $seek_country
	 * @return WC_Geo_IP_Record instance
	 */
	private function _common_get_record( $seek_country ) {
		// workaround php's broken substr, strpos, etc handling with
		// mbstring.func_overload and mbstring.internal_encoding
		$mbExists = extension_loaded( 'mbstring' );
		if ( $mbExists ) {
			$enc = mb_internal_encoding();
			mb_internal_encoding( 'ISO-8859-1' );
		}

		$record_pointer = $seek_country + ( 2 * $this->record_length - 1 ) * $this->databaseSegments;

		if ( $this->flags & self::GEOIP_MEMORY_CACHE ) {
			$record_buf = substr( $this->memory_buffer, $record_pointer, FULL_RECORD_LENGTH );
		} elseif ( $this->flags & self::GEOIP_SHARED_MEMORY ) {
			$record_buf = @shmop_read( $this->shmid, $record_pointer, FULL_RECORD_LENGTH );
		} else {
			fseek( $this->filehandle, $record_pointer, SEEK_SET );
			$record_buf = fread( $this->filehandle, FULL_RECORD_LENGTH );
		}

		$record                 = new WC_Geo_IP_Record();
		$record_buf_pos         = 0;
		$char                   = ord( substr( $record_buf, $record_buf_pos, 1 ) );
		$record->country_code   = $this->GEOIP_COUNTRY_CODES[ $char ];
		$record->country_code3  = $this->GEOIP_COUNTRY_CODES3[ $char ];
		$record->country_name   = $this->GEOIP_COUNTRY_NAMES[ $char ];
		$record->continent_code = $this->GEOIP_CONTINENT_CODES[ $char ];
		$str_length             = 0;

		$record_buf_pos++;

		// Get region
		$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		while ( 0 != $char ) {
			$str_length++;
			$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		}

		if ( $str_length > 0 ) {
			$record->region = substr( $record_buf, $record_buf_pos, $str_length );
		}

		$record_buf_pos += $str_length + 1;
		$str_length      = 0;

		// Get city
		$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		while ( 0 != $char ) {
			$str_length++;
			$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		}

		if ( $str_length > 0 ) {
			$record->city = substr( $record_buf, $record_buf_pos, $str_length );
		}

		$record_buf_pos += $str_length + 1;
		$str_length      = 0;

		// Get postal code
		$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		while ( 0 != $char ) {
			$str_length++;
			$char = ord( substr( $record_buf, $record_buf_pos + $str_length, 1 ) );
		}

		if ( $str_length > 0 ) {
			$record->postal_code = substr( $record_buf, $record_buf_pos, $str_length );
		}

		$record_buf_pos += $str_length + 1;

		// Get latitude and longitude
		$latitude  = 0;
		$longitude = 0;
		for ( $j = 0; $j < 3; ++$j ) {
			$char      = ord( substr( $record_buf, $record_buf_pos++, 1 ) );
			$latitude += ( $char << ( $j * 8 ) );
		}

		$record->latitude = ( $latitude / 10000 ) - 180;

		for ( $j = 0; $j < 3; ++$j ) {
			$char       = ord( substr( $record_buf, $record_buf_pos++, 1 ) );
			$longitude += ( $char << ( $j * 8 ) );
		}

		$record->longitude = ( $longitude / 10000 ) - 180;

		if ( self::GEOIP_CITY_EDITION_REV1 == $this->databaseType ) {
			$metroarea_combo = 0;
			if ( 'US' === $record->country_code ) {
				for ( $j = 0; $j < 3; ++$j ) {
					$char             = ord( substr( $record_buf, $record_buf_pos++, 1 ) );
					$metroarea_combo += ( $char << ( $j * 8 ) );
				}

				$record->metro_code = $record->dma_code = floor( $metroarea_combo / 1000 );
				$record->area_code  = $metroarea_combo % 1000;
			}
		}

		if ( $mbExists ) {
			mb_internal_encoding( $enc );
		}

		return $record;
	}

	/**
	 * Get record.
	 *
	 * @param  int $ipnum
	 * @return WC_Geo_IP_Record instance
	 */
	private function _get_record( $ipnum ) {
		$seek_country = $this->_geoip_seek_country( $ipnum );
		if ( $seek_country == $this->databaseSegments ) {
			return null;
		}

		return $this->_common_get_record( $seek_country );
	}

	/**
	 * Seek country IPv6.
	 *
	 * @param  int $ipnum
	 * @return string
	 */
	public function _geoip_seek_country_v6( $ipnum ) {
		// arrays from unpack start with offset 1
		// yet another php mystery. array_merge work around
		// this broken behaviour
		$v6vec = array_merge( unpack( 'C16', $ipnum ) );

		$offset = 0;
		for ( $depth = 127; $depth >= 0; --$depth ) {
			if ( $this->flags & self::GEOIP_MEMORY_CACHE ) {
				$buf = $this->_safe_substr(
					$this->memory_buffer,
					2 * $this->record_length * $offset,
					2 * $this->record_length
				);
			} elseif ( $this->flags & self::GEOIP_SHARED_MEMORY ) {
				$buf = @shmop_read(
					$this->shmid,
					2 * $this->record_length * $offset,
					2 * $this->record_length
				);
			} else {
				if ( 0 != fseek( $this->filehandle, 2 * $this->record_length * $offset, SEEK_SET ) ) {
					break;
				}

				$buf = fread( $this->filehandle, 2 * $this->record_length );
			}
			$x = array( 0, 0 );
			for ( $i = 0; $i < 2; ++$i ) {
				for ( $j = 0; $j < $this->record_length; ++$j ) {
					$x[ $i ] += ord( $buf[ $this->record_length * $i + $j ] ) << ( $j * 8 );
				}
			}

			$bnum = 127 - $depth;
			$idx = $bnum >> 3;
			$b_mask = 1 << ( $bnum & 7 ^ 7 );
			if ( ( $v6vec[ $idx ] & $b_mask ) > 0 ) {
				if ( $x[1] >= $this->databaseSegments ) {
					return $x[1];
				}
				$offset = $x[1];
			} else {
				if ( $x[0] >= $this->databaseSegments ) {
					return $x[0];
				}
				$offset = $x[0];
			}
		}

		$this->log( 'GeoIP API: Error traversing database - perhaps it is corrupt?', 'error' );

		return false;
	}

	/**
	 * Seek country.
	 *
	 * @param  int $ipnum
	 * @return string
	 */
	private function _geoip_seek_country( $ipnum ) {
		$offset = 0;
		for ( $depth = 31; $depth >= 0; --$depth ) {
			if ( $this->flags & self::GEOIP_MEMORY_CACHE ) {
				$buf = $this->_safe_substr(
					$this->memory_buffer,
					2 * $this->record_length * $offset,
					2 * $this->record_length
				);
			} elseif ( $this->flags & self::GEOIP_SHARED_MEMORY ) {
				$buf = @shmop_read(
					$this->shmid,
					2 * $this->record_length * $offset,
					2 * $this->record_length
				);
			} else {
				if ( 0 != fseek( $this->filehandle, 2 * $this->record_length * $offset, SEEK_SET ) ) {
					break;
				}

				$buf = fread( $this->filehandle, 2 * $this->record_length );
			}

			$x = array( 0, 0 );
			for ( $i = 0; $i < 2; ++$i ) {
				for ( $j = 0; $j < $this->record_length; ++$j ) {
					$x[ $i ] += ord( $buf[ $this->record_length * $i + $j ] ) << ( $j * 8 );
				}
			}
			if ( $ipnum & ( 1 << $depth ) ) {
				if ( $x[1] >= $this->databaseSegments ) {
					return $x[1];
				}

				$offset = $x[1];
			} else {
				if ( $x[0] >= $this->databaseSegments ) {
					return $x[0];
				}

				$offset = $x[0];
			}
		}

		$this->log( 'GeoIP API: Error traversing database - perhaps it is corrupt?', 'error' );

		return false;
	}

	/**
	 * Record by addr.
	 *
	 * @param  string $addr
	 *
	 * @return WC_Geo_IP_Record
	 */
	public function geoip_record_by_addr( $addr ) {
		if ( null == $addr ) {
			return 0;
		}

		$ipnum = ip2long( $addr );
		return $this->_get_record( $ipnum );
	}

	/**
	 * Country ID by addr IPv6.
	 *
	 * @param  string $addr
	 * @return int|bool
	 */
	public function geoip_country_id_by_addr_v6( $addr ) {
		if ( ! defined( 'AF_INET6' ) ) {
			$this->log( 'GEOIP (geoip_country_id_by_addr_v6): PHP was compiled with --disable-ipv6 option' );
			return false;
		}
		$ipnum = inet_pton( $addr );
		return $this->_geoip_seek_country_v6( $ipnum ) - self::GEOIP_COUNTRY_BEGIN;
	}

	/**
	 * Country ID by addr.
	 *
	 * @param  string $addr
	 * @return int
	 */
	public function geoip_country_id_by_addr( $addr ) {
		$ipnum = ip2long( $addr );
		return $this->_geoip_seek_country( $ipnum ) - self::GEOIP_COUNTRY_BEGIN;
	}

	/**
	 * Country code by addr IPv6.
	 *
	 * @param  string $addr
	 * @return string
	 */
	public function geoip_country_code_by_addr_v6( $addr ) {
		$country_id = $this->geoip_country_id_by_addr_v6( $addr );
		if ( false !== $country_id && isset( $this->GEOIP_COUNTRY_CODES[ $country_id ] ) ) {
			return $this->GEOIP_COUNTRY_CODES[ $country_id ];
		}

		return false;
	}

	/**
	 * Country code by addr.
	 *
	 * @param  string $addr
	 * @return string
	 */
	public function geoip_country_code_by_addr( $addr ) {
		if ( self::GEOIP_CITY_EDITION_REV1 == $this->databaseType ) {
			$record = $this->geoip_record_by_addr( $addr );
			if ( false !== $record ) {
				return $record->country_code;
			}
		} else {
			$country_id = $this->geoip_country_id_by_addr( $addr );
			if ( false !== $country_id && isset( $this->GEOIP_COUNTRY_CODES[ $country_id ] ) ) {
				return $this->GEOIP_COUNTRY_CODES[ $country_id ];
			}
		}

		return false;
	}

	/**
	 * Encode string.
	 *
	 * @param  string $string
	 * @param  int    $start
	 * @param  int    $length
	 * @return string
	 */
	private function _safe_substr( $string, $start, $length ) {
		// workaround php's broken substr, strpos, etc handling with
		// mbstring.func_overload and mbstring.internal_encoding
		$mb_exists = extension_loaded( 'mbstring' );

		if ( $mb_exists ) {
			$enc = mb_internal_encoding();
			mb_internal_encoding( 'ISO-8859-1' );
		}

		$buf = substr( $string, $start, $length );

		if ( $mb_exists ) {
			mb_internal_encoding( $enc );
		}

		return $buf;
	}
}

/**
 * Geo IP Record class.
 */
class WC_Geo_IP_Record {

	/**
	 * Country code.
	 *
	 * @var string
	 */
	public $country_code;

	/**
	 * 3 letters country code.
	 *
	 * @var string
	 */
	public $country_code3;

	/**
	 * Country name.
	 *
	 * @var string
	 */
	public $country_name;

	/**
	 * Region.
	 *
	 * @var string
	 */
	public $region;

	/**
	 * City.
	 *
	 * @var string
	 */
	public $city;

	/**
	 * Postal code.
	 *
	 * @var string
	 */
	public $postal_code;

	/**
	 * Latitude
	 *
	 * @var int
	 */
	public $latitude;

	/**
	 * Longitude.
	 *
	 * @var int
	 */
	public $longitude;

	/**
	 * Area code.
	 *
	 * @var int
	 */
	public $area_code;

	/**
	 * DMA Code.
	 *
	 * Metro and DMA code are the same.
	 * Use metro code instead.
	 *
	 * @var float
	 */
	public $dma_code;

	/**
	 * Metro code.
	 *
	 * @var float
	 */
	public $metro_code;

	/**
	 * Continent code.
	 *
	 * @var string
	 */
	public $continent_code;
}
