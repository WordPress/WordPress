<?php

/**
 * AWeberResponse
 *
 * Base class for objects that represent a response from the AWeberAPI. 
 * Responses will exist as one of the two AWeberResponse subclasses:
 *  - AWeberEntry - a single instance of an AWeber resource
 *  - AWeberCollection - a collection of AWeber resources
 * @uses AWeberAPIBase
 * @package 
 * @version $id$
 */
class AWeberResponse extends AWeberAPIBase {

    public $adapter = false;
    public $data = array();
    public $_dynamicData = array();

    /**
     * __construct
     *
     * Creates a new AWeberRespones
     *
     * @param mixed $response       Data returned by the API servers
     * @param mixed $url            URL we hit to get the data
     * @param mixed $adapter        OAuth adapter used for future interactions
     * @access public
     * @return void
     */
    public function __construct($response, $url, $adapter) {
        $this->adapter = $adapter;
        $this->url     = $url;
        $this->data    = $response;
    }

    /**
     * __set
     * 
     * Manual re-implementation of __set, allows sub classes to access
     * the default behavior by using the parent:: format.
     *
     * @param mixed $key        Key of the attr being set
     * @param mixed $value      Value being set to the attr
     * @access public
     */
    public function __set($key, $value) {
        $this->{$key} = $value;
    }

    /**
     * __get
     *
     * PHP "MagicMethod" to allow for dynamic objects.  Defers first to the 
     * data in $this->data.
     *
     * @param String $value  Name of the attribute requested
     * @access public
     * @return mixed
     */
    public function __get($value) {
        if (in_array($value, $this->_privateData)) {
            return null;
        }
        if (array_key_exists($value, $this->data)) {
            return $this->data[$value];
        }
        if ($value == 'type') return $this->_type();
    }

}


