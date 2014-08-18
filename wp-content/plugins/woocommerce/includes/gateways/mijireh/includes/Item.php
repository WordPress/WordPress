<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Mijireh_Item extends Mijireh_Model {

  private function _init() {
    $this->_data = array(
      'name' => null,
      'price' => null,
      'quantity' => 1,
      'sku' => null
    );
  }

  private function _check_required_fields() {
    if(empty($this->_data['name'])) {
      $this->add_error('item name is required.');
    }

    if(!is_numeric($this->_data['price'])) {
      $this->add_error('price must be a number.');
    }
  }

  private function _check_quantity() {
    if($this->_data['quantity'] < 1) {
      $this->add_error('quantity must be greater than or equal to 1');
    }
  }

  public function __construct() {
    $this->_init();
  }

  public function __get($key) {
    $value = false;
    if($key == 'total') {
      $value = $this->_data['price'] * $this->_data['quantity'];
      $value = number_format($value, 2, '.', '');
    }
    else {
      $value = parent::__get($key);
    }
    return $value;
  }

  public function get_data() {
    $data = parent::get_data();
    $data['total'] = $this->total;
    return $data;
  }

  public function validate() {
    $this->_check_required_fields();
    $this->_check_quantity();
    return count($this->_errors) == 0;
  }

}