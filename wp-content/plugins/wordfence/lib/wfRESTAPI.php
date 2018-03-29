<?php

class wfWP_REST_Users_Controller extends WP_REST_Users_Controller
{
	public static function wfGetURLBase() {
		$controller = new wfWP_REST_Users_Controller();
		return rtrim($controller->namespace . '/' . $controller->rest_base, '/');
	}
	
	public function _wfGetURLBase() {
		return rtrim($this->namespace, '/' . $this->rest_base, '/');
	}
}