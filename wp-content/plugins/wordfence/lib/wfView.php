<?php

class wfView {

	/**
	 * @var string
	 */
	protected $view_path;

	/**
	 * @var string
	 */
	protected $view_file_extension = '.php';

	/**
	 * @var string
	 */
	protected $view;

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * @param string $view
	 * @param array  $data
	 * @return wfView
	 */
	public static function create($view, $data = array()) {
		return new self($view, $data);
	}

	/**
	 * @param string $view
	 * @param array  $data
	 */
	public function __construct($view, $data = array()) {
		$this->view_path = WORDFENCE_PATH . 'views';
		$this->view = $view;
		$this->data = $data;
	}

	/**
	 * @return string
	 * @throws wfViewNotFoundException
	 */
	public function render() {
		$view = preg_replace('/\.{2,}/', '.', $this->view);
		$view_path = $this->view_path . '/' . $view . $this->view_file_extension;
		if (!file_exists($view_path)) {
			throw new wfViewNotFoundException('The view ' . $view_path . ' does not exist or is not readable.');
		}

		extract($this->data, EXTR_SKIP);

		ob_start();
		/** @noinspection PhpIncludeInspection */
		include $view_path;
		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	public function __toString() {
		try {
			return $this->render();
		} catch (wfViewNotFoundException $e) {
			return defined('WP_DEBUG') && WP_DEBUG ? $e->getMessage() : 'The view could not be loaded.';
		}
	}

	/**
	 * @param $data
	 * @return $this
	 */
	public function addData($data) {
		$this->data = array_merge($data, $this->data);
		return $this;
	}

	/**
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param array $data
	 * @return $this
	 */
	public function setData($data) {
		$this->data = $data;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getView() {
		return $this->view;
	}

	/**
	 * @param string $view
	 * @return $this
	 */
	public function setView($view) {
		$this->view = $view;
		return $this;
	}

	/**
	 * Prevent POP
	 */
	public function __wakeup() {
		$this->view_path = WORDFENCE_PATH . 'views';
		$this->view = null;
		$this->data = array();
		$this->view_file_extension = '.php';
	}
}

class wfViewNotFoundException extends Exception {
}
