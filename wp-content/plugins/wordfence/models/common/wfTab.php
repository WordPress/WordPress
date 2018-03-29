<?php

/**
 * Defines a UI tab.
 *
 * @property string $id
 * @property string $a
 * @property string $tabTitle
 * @property string $pageTitle
 * @property bool $active
 */
class wfTab {
	protected $_id;
	protected $_a;
	protected $_tabTitle;
	protected $_pageTitle;
	protected $_active;
	
	public function __construct($id, $a, $tabTitle, $pageTitle, $active = false) {
		$this->_id = $id;
		$this->_a = $a;
		$this->_tabTitle = $tabTitle;
		$this->_pageTitle = $pageTitle;
		$this->_active = $active;
	}
	
	public function __get($name) {
		switch ($name) {
			case 'id':
				return $this->_id;
			case 'a':
				return $this->_a;
			case 'tabTitle':
				return $this->_tabTitle;
			case 'pageTitle':
				return $this->_pageTitle;
			case 'active':
				return $this->_active;
		}
		
		throw new OutOfBoundsException('Invalid key: ' . $name);
	}
}