<?php

/**
 * Class ActionScheduler_ActionClaim
 */
class ActionScheduler_ActionClaim {
	private $id = '';
	private $action_ids = array();

	public function __construct( $id, array $action_ids ) {
		$this->id = $id;
		$this->action_ids = $action_ids;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_actions() {
		return $this->action_ids;
	}
}
 