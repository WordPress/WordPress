<?php

namespace Yoast\WP\SEO\Promotions\Domain;

/**
 * Class to manage the Black Friday checklist promotion.
 *
 * @deprecated 26.0
 * @codeCoverageIgnore
 */
class Black_Friday_Checklist_Promotion extends Abstract_Promotion implements Promotion_Interface {

	/**
	 * Class constructor.
	 *
	 * @deprecated 26.0
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 26.0' );
		parent::__construct(
			'black-friday-2023-checklist',
			new Time_Interval(
				\gmmktime( 11, 00, 00, 9, 19, 2023 ),
				\gmmktime( 11, 00, 00, 10, 31, 2023 )
			)
		);
	}
}
