<?php

/**
 * Class Sample_Product
 *
 * Our sample product class
 */
class Sample_Product extends Yoast_Product {

	public function __construct() {
		parent::__construct(
				'https://yoast.com',
				'Sample Product',
				'sample-product',
				'1.0',
				'https://yoast.com/wordpress/plugins/sample-product/',
				'admin.php?page=sample-product',
				'sample-product',
				'Yoast'
		);
	}

}