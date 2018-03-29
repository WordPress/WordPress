<?php

class wfWAFView {

	/**
	 * @var string
	 */
	protected $viewPath;

	/**
	 * @var string
	 */
	protected $viewFileExtension = '.php';

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
	 * @return wfWAFView
	 */
	public static function create($view, $data = array()) {
		return new self($view, $data);
	}

	/**
	 * @param string $view
	 * @param array  $data
	 */
	public function __construct($view, $data = array()) {
		$this->viewPath = WFWAF_VIEW_PATH;
		$this->view = $view;
		$this->data = $data;
	}

	/**
	 * @return string
	 * @throws wfWAFViewNotFoundException
	 */
	public function render() {
		$view = preg_replace('/\.{2,}/', '.', $this->view);
		$viewPath = $this->viewPath . '/' . $view . $this->viewFileExtension;
		if (!file_exists($viewPath)) {
			throw new wfWAFViewNotFoundException('The view ' . $viewPath . ' does not exist or is not readable.');
		}

		extract($this->data, EXTR_SKIP);

		ob_start();
		/** @noinspection PhpIncludeInspection */
		include $viewPath;
		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	public function __toString() {
		try {
			return $this->render();
		} catch (wfWAFViewNotFoundException $e) {
			return defined('WFWAF_DEBUG') && WFWAF_DEBUG ? $e->getMessage() : 'The view could not be loaded.';
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
		$this->viewPath = WFWAF_VIEW_PATH;
		$this->view = null;
		$this->data = array();
		$this->viewFileExtension = '.php';
	}
}

class wfWAFViewNotFoundException extends Exception {
}
