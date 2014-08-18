<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Mijireh_Model {

  protected $_data = array();
  protected $_errors = array();

  /**
   * Set the value of one of the keys in the private $_data array.
   *
   * @param string $key The key in the $_data array
   * @param string $value The value to assign to the key
   * @return boolean
   */
  public function __set($key, $value) {
    $success = false;
    if(array_key_exists($key, $this->_data)) {
      $this->_data[$key] = $value;
      $success = true;
    }
    return $success;
  }

  /**
   * Get the value for the key from the private $_data array.
   *
   * Return false if the requested key does not exist
   *
   * @param string $key The key from the $_data array
   * @return mixed
   */
  public function __get($key) {
    $value = false;
    if(array_key_exists($key, $this->_data)) {
      $value = $this->_data[$key];
    }

    /*
    elseif(method_exists($this, $key)) {
      $value = call_user_func_array(array($this, $key), func_get_args());
    }
    */

    return $value;
  }

  /**
   * Return true if the given $key in the private $_data array is set
   *
   * @param string $key
   * @return boolean
   */
  public function __isset($key) {
    return isset($this->_data[$key]);
  }

  /**
   * Set the value of the $_data array to null for the given key.
   *
   * @param string $key
   * @return void
   */
  public function __unset($key) {
    if(array_key_exists($key, $this->_data)) {
      $this->_data[$key] = null;
    }
  }

  /**
   * Return the private $_data array
   *
   * @return mixed
   */
  public function get_data() {
    return $this->_data;
  }

  /**
   * Return true if the given $key exists in the private $_data array
   *
   * @param string $key
   * @return boolean
   */
  public function field_exists($key) {
    return array_key_exists($key, $this->_data);
  }

  public function copy_from(array $data) {
    foreach($data as $key => $value) {
      if(array_key_exists($key, $this->_data)) {
        $this->_data[$key] = $value;
      }
    }
  }

  public function clear() {
    foreach($this->_data as $key => $value) {
      if($key == 'id') {
        $this->_data[$key] = null;
      }
      else {
        $this->_data[$key] = '';
      }
    }
  }

  public function add_error($error_message) {
    if(!empty($error_message)) {
      $this->_errors[] = $error_message;
    }
  }

  public function clear_errors() {
    $this->_errors = array();
  }

  public function get_errors() {
    return $this->_errors;
  }

  public function get_error_lines($glue="\n") {
    $error_lines = '';
    if(count($this->_errors)) {
      $error_lines = implode($glue, $this->_errors);
    }
    return $error_lines;
  }

  public function is_valid() {
    return count($this->_errors) == 0;
  }

}
