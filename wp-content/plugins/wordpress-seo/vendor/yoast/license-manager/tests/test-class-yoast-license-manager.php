<?php

include( dirname( __FILE__ ) . '../../class-product.php' );
include( dirname( __FILE__ ) . '../../class-license-manager.php' );

class Yoast_Product_Double extends Yoast_Product {

	/**
	 * Construct the real Product class with our fake data
	 */
	public function __construct() {
		parent::__construct( get_site_url(), 'test-product', 'slug-test-product', '1.0.0' );
	}

}

class Yoast_License_Manager_Double extends Yoast_License_Manager {

	public $product;

	public function __construct() {
		$this->product = new Yoast_Product_Double();

		parent::__construct( $this->product );
	}

	public function specific_hooks() {
		return $this->specific_hooks();
	}

	public function setup_auto_updater() {
		return $this->setup_auto_updater();
	}

	/**
	 * Wrapper for get_curl_version()
	 *
	 * @return mixed
	 */
	public function double_get_curl_version(){
		return $this->get_curl_version();
	}

}

class Test_Yoast_License_Manager extends Yst_License_Manager_UnitTestCase {

	private $class;

	public function setUp() {
		$this->class = new Yoast_License_Manager_Double();
	}

	/**
	 * Make sure the API url is correct in the product
	 *
	 * @covers Yoast_License_Manager::get_api_url()
	 */
	public function test_get_api_url(){
		$this->assertEquals( $this->class->product->get_api_url(), get_site_url() );
	}

	/**
	 * Make sure the API url is correct in the product
	 *
	 * @covers Yoast_License_Manager::get_curl_version()
	 */
	public function test_get_curl_version_WITH_curl_installed_on_test_server(){
		$curl_result = $this->class->double_get_curl_version();

		if( function_exists('curl_version') ){
			$curl_version = curl_version();

			$this->assertEquals( $curl_result, $curl_version['version'] );
		}
		else{
			$this->assertFalse( $curl_result );
		}
	}

}