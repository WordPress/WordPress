<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

//for woof_meta_get_keys method
//another way is copy/paste!
class WOOF_PDS_CPT extends WC_Product_Data_Store_CPT {

    public function get_internal_meta_keys() {
        $exception = array('_height', '_width', '_length', '_weight', 'total_sales', '_stock');
        return array_diff($this->internal_meta_keys, $exception);
    }

}
