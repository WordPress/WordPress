<?php
class wfStyle {
	/**
	 * Returns the classes for the main content body of the page, adjusting for the paid status.
	 * 
	 * @return string
	 */
	public static function contentClasses() {
		if (wfConfig::get('isPaid')) {
			return 'wf-col-xs-12';
		}
		return 'wf-col-xs-12';
	}
	
	/**
	 * Returns the classes for the right rail portion of the page when present.
	 * 
	 * @return string
	 */
	public static function rightRailClasses() {
		return 'wf-hidden-xs wf-col-sm-push-9 wf-col-sm-3';
	}
}