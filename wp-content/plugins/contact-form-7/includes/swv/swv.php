<?php
/**
 * Schema-Woven Validation API
 */

require_once WPCF7_PLUGIN_DIR . '/includes/swv/schema-holder.php';
require_once WPCF7_PLUGIN_DIR . '/includes/swv/script-loader.php';
require_once WPCF7_PLUGIN_DIR . '/includes/swv/php/abstract-rules.php';


/**
 * Returns an associative array of SWV rules.
 */
function wpcf7_swv_available_rules() {
	$rules = array(
		'required' => 'Contactable\SWV\RequiredRule',
		'requiredfile' => 'Contactable\SWV\RequiredFileRule',
		'email' => 'Contactable\SWV\EmailRule',
		'url' => 'Contactable\SWV\URLRule',
		'tel' => 'Contactable\SWV\TelRule',
		'number' => 'Contactable\SWV\NumberRule',
		'date' => 'Contactable\SWV\DateRule',
		'time' => 'Contactable\SWV\TimeRule',
		'file' => 'Contactable\SWV\FileRule',
		'enum' => 'Contactable\SWV\EnumRule',
		'dayofweek' => 'Contactable\SWV\DayofweekRule',
		'minitems' => 'Contactable\SWV\MinItemsRule',
		'maxitems' => 'Contactable\SWV\MaxItemsRule',
		'minlength' => 'Contactable\SWV\MinLengthRule',
		'maxlength' => 'Contactable\SWV\MaxLengthRule',
		'minnumber' => 'Contactable\SWV\MinNumberRule',
		'maxnumber' => 'Contactable\SWV\MaxNumberRule',
		'mindate' => 'Contactable\SWV\MinDateRule',
		'maxdate' => 'Contactable\SWV\MaxDateRule',
		'minfilesize' => 'Contactable\SWV\MinFileSizeRule',
		'maxfilesize' => 'Contactable\SWV\MaxFileSizeRule',
		'stepnumber' => 'Contactable\SWV\StepNumberRule',
		'all' => 'Contactable\SWV\AllRule',
		'any' => 'Contactable\SWV\AnyRule',
	);

	return apply_filters( 'wpcf7_swv_available_rules', $rules );
}


add_action( 'wpcf7_init', 'wpcf7_swv_load_rules', 10, 0 );

/**
 * Loads SWV fules.
 */
function wpcf7_swv_load_rules() {
	$rules = wpcf7_swv_available_rules();

	foreach ( array_keys( $rules ) as $rule ) {
		$file = sprintf( '%s.php', $rule );
		$path = path_join( WPCF7_PLUGIN_DIR . '/includes/swv/php/rules', $file );

		if ( file_exists( $path ) ) {
			include_once $path;
		}
	}
}


/**
 * Creates an SWV rule object.
 *
 * @param string $rule_name Rule name.
 * @param string|array $properties Optional. Rule properties.
 * @return \Contactable\SWV\Rule|null The rule object, or null if it failed.
 */
function wpcf7_swv_create_rule( $rule_name, $properties = '' ) {
	$rules = wpcf7_swv_available_rules();

	if ( isset( $rules[$rule_name] ) ) {
		return new $rules[$rule_name]( $properties );
	}
}


/**
 * Returns an associative array of JSON Schema for Contact Form 7 SWV.
 */
function wpcf7_swv_get_meta_schema() {
	return array(
		'$schema' => 'https://json-schema.org/draft/2020-12/schema',
		'title' => 'Contact Form 7 SWV',
		'description' => 'Contact Form 7 SWV meta-schema',
		'type' => 'object',
		'properties' => array(
			'version' => array(
				'type' => 'string',
			),
			'locale' => array(
				'type' => 'string',
			),
			'rules' => array(
				'type' => 'array',
				'items' => array(
					'type' => 'object',
					'properties' => array(
						'rule' => array(
							'type' => 'string',
							'enum' => array_keys( wpcf7_swv_available_rules() ),
						),
						'field' => array(
							'type' => 'string',
							'pattern' => '^[A-Za-z][-A-Za-z0-9_:]*$',
						),
						'error' => array(
							'type' => 'string',
						),
						'accept' => array(
							'type' => 'array',
							'items' => array(
								'type' => 'string',
							),
						),
						'base' => array(
							'type' => 'string',
						),
						'interval' => array(
							'type' => 'number',
							'minimum' => 0,
						),
						'threshold' => array(
							'type' => 'string',
						),
					),
					'required' => array( 'rule' ),
				),
			),
		),
	);
}


/**
 * The schema class as a composite rule.
 */
class WPCF7_SWV_Schema extends \Contactable\SWV\CompositeRule {

	/**
	 * The human-readable version of the schema.
	 */
	const version = 'Contact Form 7 SWV Schema 2024-10';


	/**
	 * Constructor.
	 */
	public function __construct( $properties = '' ) {
		$this->properties = wp_parse_args( $properties, array(
			'version' => self::version,
		) );
	}


	/**
	 * Validates with this schema.
	 *
	 * @param array $context Context.
	 */
	public function validate( $context ) {
		foreach ( $this->rules() as $rule ) {
			if ( $rule->matches( $context ) ) {
				yield $rule->validate( $context );
			}
		}
	}

}
