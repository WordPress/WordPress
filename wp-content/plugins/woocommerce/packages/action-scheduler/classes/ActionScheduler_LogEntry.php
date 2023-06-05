<?php

/**
 * Class ActionScheduler_LogEntry
 */
class ActionScheduler_LogEntry {

	/**
	 * @var int $action_id
	 */
	protected $action_id =  '';

	/**
	 * @var string $message
	 */
	protected $message =  '';

	/**
	 * @var Datetime $date
	 */
	protected $date;

	/**
	 * Constructor
	 *
	 * @param mixed  $action_id Action ID
	 * @param string $message   Message
	 * @param Datetime $date    Datetime object with the time when this log entry was created. If this parameter is
	 *                          not provided a new Datetime object (with current time) will be created.
	 */
	public function __construct( $action_id, $message, $date = null ) {

		/*
		 * ActionScheduler_wpCommentLogger::get_entry() previously passed a 3rd param of $comment->comment_type
		 * to ActionScheduler_LogEntry::__construct(), goodness knows why, and the Follow-up Emails plugin
		 * hard-codes loading its own version of ActionScheduler_wpCommentLogger with that out-dated method,
		 * goodness knows why, so we need to guard against that here instead of using a DateTime type declaration
		 * for the constructor's 3rd param of $date and causing a fatal error with older versions of FUE.
		 */
		if ( null !== $date && ! is_a( $date, 'DateTime' ) ) {
			_doing_it_wrong( __METHOD__, 'The third parameter must be a valid DateTime instance, or null.', '2.0.0' );
			$date = null;
		}

		$this->action_id = $action_id;
		$this->message   = $message;
		$this->date      = $date ? $date : new Datetime;
	}

	/**
	 * Returns the date when this log entry was created
	 *
	 * @return Datetime
	 */
	public function get_date() {
		return $this->date;
	}

	public function get_action_id() {
		return $this->action_id;
	}

	public function get_message() {
		return $this->message;
	}
}

