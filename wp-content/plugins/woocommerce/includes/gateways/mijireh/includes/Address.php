<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Mijireh_Address extends Mijireh_Model {

  public function __construct() {
    $this->init();
  }

  public function init() {
    $this->_data = array(
      'first_name' => '',
      'last_name' => '',
      'street' => '',
      'city' => '',
      'state_province' => '',
      'zip_code' => '',
      'country' => '',
      'company' => '',
      'apt_suite' => '',
      'phone' => ''
    );
  }

	/**
	 * Check required fields
	 * @return bool
	 */
	public function validate() {
    $is_valid = $this->_check_required_fields();
    return $is_valid;
  }

  /**
   * Return true if all of the required fields have a non-empty value
   *
   * @return boolean
   */
  private function _check_required_fields() {
    $pass = true;
    $fields = array('street', 'city', 'zip_code', 'country');
    foreach($fields as $f) {
      if(empty($this->_data[$f])) {
        $pass = false;
        $this->add_error("$f is required");
      }
    }
    return $pass;
  }

}
