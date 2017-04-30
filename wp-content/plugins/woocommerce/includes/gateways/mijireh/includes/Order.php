<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Mijireh_Order extends Mijireh_Model {

  private function _init() {
    $this->_data = array(
      'partner_id'       => null,
      'order_number'     => null,
      'mode'             => null,
      'status'           => null,
      'order_date'       => null,
      'ip_address'       => null,
      'checkout_url'     => null,
      'total'            => '',
      'return_url'       => '',
      'items'            => array(),
      'email'            => '',
      'first_name'       => '',
      'last_name'        => '',
      'meta_data'        => array(),
      'tax'              => '',
      'shipping'         => '',
      'discount'         => '',
      'shipping_address' => array(),
      'billing_address'  => array(),
      'show_tax'         => true
    );
  }

  public function __construct($order_number=null) {
    $this->_init();
    if(isset($order_number)) {
      $this->load($order_number);
    }
  }

  public function load($order_number) {
    if(strlen(Mijireh::$access_key) < 5) {
      throw new Mijireh_Exception('missing mijireh access key');
    }

    $rest = new Mijireh_RestJSON(Mijireh::$url);
    $rest->setupAuth(Mijireh::$access_key, '');
    try {
      $order_data = $rest->get("orders/$order_number");
      $this->copy_from($order_data);
      return $this;
    }
    catch(Mijireh_Rest_BadRequest $e) {
      throw new Mijireh_BadRequest($e->getMessage());
    }
    catch(Mijireh_Rest_Unauthorized $e) {
      throw new Mijireh_Unauthorized("Unauthorized. Please check your api access key");
    }
    catch(Mijireh_Rest_NotFound $e) {
      throw new Mijireh_NotFound("Mijireh resource not found: " . $rest->last_request['url']);
    }
    catch(Mijireh_Rest_ClientError $e) {
      throw new Mijireh_ClientError($e->getMessage());
    }
    catch(Mijireh_Rest_ServerError $e) {
      throw new Mijireh_ServerError($e->getMessage());
    }
  }

  public function copy_from($order_data) {
    foreach($order_data as $key => $value) {
      if($key == 'items') {
        if(is_array($value)) {
          $this->clear_items(); // Clear current items before adding new items.
          foreach($value as  $item_array) {
            $item = new Mijireh_Item();
            $item->copy_from($item_array);
            $this->add_item($item);
          }
        }
      }
      elseif($key == 'shipping_address') {
        if(is_array($value)) {
          $address = new Mijireh_Address();
          $address->copy_from($value);
          $this->set_shipping_address($address);
        }
      }
      elseif($key == 'billing_address') {
        if(is_array($value)) {
          $address = new Mijireh_Address();
          $address->copy_from($value);
          $this->set_billing_address($address);
        }
      }
      elseif($key == 'meta_data') {
        if(is_array($value)) {
          $this->clear_meta_data(); // Clear current meta data before adding new meta data
          $this->_data['meta_data'] = $value;
        }
      }
      else {
        $this->$key = $value;
      }
    }

    if(!$this->validate()) {
      throw new Mijireh_Exception('invalid order hydration: ' . $this->get_errors_lines());
    }

    return $this;
  }

  public function create() {
    if(strlen(Mijireh::$access_key) < 5) {
      throw new Mijireh_Exception('missing mijireh access key');
    }

    if(!$this->validate()) {
      $error_message = 'unable to create order: ' . $this->get_error_lines();
      throw new Mijireh_Exception($error_message);
    }

    $rest = new Mijireh_RestJSON(Mijireh::$url);
    $rest->setupAuth(Mijireh::$access_key, '');
    try {
      $result = $rest->post('orders', $this->get_data());
      $this->copy_from($result);
      return $this;
    }
    catch(Mijireh_Rest_BadRequest $e) {
      throw new Mijireh_BadRequest($e->getMessage());
    }
    catch(Mijireh_Rest_Unauthorized $e) {
      throw new Mijireh_Unauthorized("Unauthorized. Please check your api access key");
    }
    catch(Mijireh_Rest_NotFound $e) {
      throw new Mijireh_NotFound("Mijireh resource not found: " . $rest->last_request['url']);
    }
    catch(Mijireh_Rest_ClientError $e) {
      throw new Mijireh_ClientError($e->getMessage());
    }
    catch(Mijireh_Rest_ServerError $e) {
      throw new Mijireh_ServerError($e->getMessage());
    }
  }

  /**
   * If meta_data or shipping_address are empty, exclude them altogether.
   */
  public function get_data() {
    $data = parent::get_data();
    if(count($data['meta_data']) == 0) { unset($data['meta_data']); }
    if(count($data['shipping_address']) == 0) { unset($data['shipping_address']); }
    if(count($data['billing_address']) == 0) { unset($data['billing_address']); }
    return $data;
  }

	/**
	 * Add the specified item and price to the order.
	 * Return the total number of items in the order (including the one that was just added)
	 *
	 * @param Mijireh_Item|string $name
	 * @param int                 $price
	 * @param int                 $quantity
	 * @param string              $sku
	 * @throws Mijireh_Exception
	 * @return int
	 */
  public function add_item($name, $price=0, $quantity=1, $sku='') {
    $item = '';
    if(is_object($name) && get_class($name) == 'Mijireh_Item') {
      $item = $name;
    }
    else {
      $item = new Mijireh_Item();
      $item->name = $name;
      $item->price = $price;
      $item->quantity = $quantity;
      $item->sku = $sku;
    }

    if($item->validate()) {
      $this->_data['items'][] = $item->get_data();
      return $this->item_count();
    }
    else {
      $errors = implode(' ', $item->get_errors());
      throw new Mijireh_Exception('unable to add invalid item to order :: ' . $errors);
    }
  }

  public function add_meta_data($key, $value) {
    if(!is_array($this->_data['meta_data'])) {
      $this->_data['meta_data'] = array();
    }
    $this->_data['meta_data'][$key] = $value;
  }

  /**
   * Return the value associated with the given key in the order's meta data.
   *
   * If the key does not exist, return false.
   */
  public function get_meta_value($key) {
    $value = false;
    if(isset($this->_data['meta_data'][$key])) {
      $value = $this->_data['meta_data'][$key];
    }
    return $value;
  }

  public function item_count() {
    $item_count = 0;
    if(is_array($this->_data['items'])) {
      $item_count = count($this->_data['items']);
    }
    return $item_count;
  }

  public function get_items() {
    $items = array();
    foreach($this->_data['items'] as $item_data) {
      $item = new Mijireh_Item();
      $item->copy_from($item_data);
    }
  }

  public function clear_items() {
    $this->_data['items'] = array();
  }

  public function clear_meta_data() {
    $this->_data['meta_data'] = array();
  }

  public function validate() {
    $this->_check_total();
    $this->_check_return_url();
    $this->_check_items();
    return count($this->_errors) == 0;
  }

  /**
   * Alias for set_shipping_address()
   */
  public function set_address(Mijireh_Address $address){
    $this->set_shipping_address($address);
  }

  public function set_shipping_address(Mijireh_Address $address) {
    if($address->validate()) {
      $this->_data['shipping_address'] = $address->get_data();
    }
    else {
      throw new Mijireh_Exception('invalid shipping address');
    }
  }

  public function set_billing_address(Mijireh_Address $address) {
    if($address->validate()) {
      $this->_data['billing_address'] = $address->get_data();
    }
    else {
      throw new Mijireh_Exception('invalid shipping address');
    }
  }

  /**
   * Alias for get_shipping_address()
   */
  public function get_address() {
    return $this->get_shipping_address();
  }

  public function get_shipping_address() {
    $address = false;
    if(is_array($this->_data['shipping_address'])) {
      $address = new Mijireh_Address();
      $address->copy_from($this->_data['shipping_address']);
    }
    return $address;
  }

  public function get_billing_address() {
    $address = false;
    if(is_array($this->_data['billing_address'])) {
      $address = new Mijireh_Address();
      $address->copy_from($this->_data['billing_address']);
    }
    return $address;
  }

  /**
   * The order total must be greater than zero.
   *
   * Return true if valid, otherwise false.
   *
   * @return boolean
   */
  private function _check_total() {
    $is_valid = true;
    if($this->_data['total'] <= 0) {
      $this->add_error('order total must be greater than zero');
      $is_valid = false;
    }
    return $is_valid;
  }

  /**
   * The return url must be provided and must start with http.
   *
   * Return true if valid, otherwise false
   *
   * @return boolean
   */
  private function _check_return_url() {
    $is_valid = false;
    if(!empty($this->_data['return_url'])) {
      $url = $this->_data['return_url'];
      if('http' == strtolower(substr($url, 0, 4))) {
        $is_valid = true;
      }
      else {
        $this->add_error('return url is invalid');
      }
    }
    else {
      $this->add_error('return url is required');
    }
    return $is_valid;
  }

  /**
   * An order must contain at least one item
   *
   * Return true if the order has at least one item, otherwise false.
   *
   * @return boolean
   */
  private function _check_items() {
    $is_valid = true;
    if(count($this->_data['items']) <= 0) {
      $is_valid = false;
      $this->add_error('the order must contain at least one item');
    }
    return $is_valid;
  }

}
