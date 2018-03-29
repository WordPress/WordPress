<?php

class wfOnboardingController {
	const ONBOARDING_FIRST_EMAILS = 'emails'; //New install, first attempt onboarding, part 1 completed
	const ONBOARDING_FIRST_LICENSE = 'license'; //New install, first attempt onboarding, part 2 completed
	const ONBOARDING_FIRST_SKIPPED = 'skipped'; //New install, first attempt onboarding was skipped
	
	const ONBOARDING_SECOND_EMAILS = 'emails'; //New install, second attempt onboarding, part 1 completed
	const ONBOARDING_SECOND_LICENSE = 'license'; //New install, second attempt onboarding, part 2 completed
	const ONBOARDING_SECOND_SKIPPED = 'skipped'; //New install, second attempt onboarding was skipped
	
	const ONBOARDING_THIRD_EMAILS = 'emails'; //New install, third attempt onboarding, part 1 completed
	const ONBOARDING_THIRD_LICENSE = 'license'; //New install, third attempt onboarding, part 2 completed
	
	const TOUR_DASHBOARD = 'dashboard';
	const TOUR_FIREWALL = 'firewall';
	const TOUR_SCAN = 'scan';
	const TOUR_BLOCKING = 'blocking';
	const TOUR_LIVE_TRAFFIC = 'livetraffic';
	
	/**
	 * Sets the appropriate initial settings for an existing install so it's not forced through onboarding.
	 */
	public static function migrateOnboarding() {
		$alertEmails = wfConfig::get('alertEmails');
		$onboardingAttempt1 = wfConfig::get('onboardingAttempt1');
		if (!empty($alertEmails) && empty($onboardingAttempt1)) {
			wfConfig::set('onboardingAttempt1', self::ONBOARDING_FIRST_LICENSE); //Mark onboarding as done
			
			$keys = array(self::TOUR_DASHBOARD, self::TOUR_FIREWALL, self::TOUR_SCAN, self::TOUR_BLOCKING, self::TOUR_LIVE_TRAFFIC);
			foreach ($keys as $k) {
				wfConfig::set('needsNewTour_' . $k, 0);
				wfConfig::set('needsUpgradeTour_' . $k, 1);
			}
		}
	}
	
	/**
	 * Initializes the onboarding hooks.
	 * 
	 * Only called if (is_admin() && wfUtils::isAdmin()) is true.
	 */
	public static function initialize() {
		$willShowAnyTour = (self::shouldShowNewTour(self::TOUR_DASHBOARD) || self::shouldShowUpgradeTour(self::TOUR_DASHBOARD) ||
							self::shouldShowNewTour(self::TOUR_FIREWALL) || self::shouldShowUpgradeTour(self::TOUR_FIREWALL) ||
							self::shouldShowNewTour(self::TOUR_SCAN) || self::shouldShowUpgradeTour(self::TOUR_SCAN) ||
							self::shouldShowNewTour(self::TOUR_BLOCKING) || self::shouldShowUpgradeTour(self::TOUR_BLOCKING) ||
							self::shouldShowNewTour(self::TOUR_LIVE_TRAFFIC) || self::shouldShowUpgradeTour(self::TOUR_LIVE_TRAFFIC));
		if (!self::shouldShowAttempt1() && !self::shouldShowAttempt2() && !self::shouldShowAttempt3() && !$willShowAnyTour) {
			return;
		}
		
		add_action('in_admin_header', 'wfOnboardingController::_admin_header'); //Called immediately after <div id="wpcontent">
		add_action('pre_current_active_plugins', 'wfOnboardingController::_pre_plugins'); //Called immediately after <hr class="wp-header-end">
		add_action('admin_enqueue_scripts', 'wfOnboardingController::_enqueue_scripts');
	}
	
	/**
	 * Enqueues the scripts and styles we need globally on the backend for onboarding.
	 */
	public static function _enqueue_scripts() {
		wp_enqueue_style('wordfence-font', 'https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i', '', WORDFENCE_VERSION);
		wp_enqueue_style('wordfence-ionicons-style', wfUtils::getBaseURL() . wfUtils::versionedAsset('css/wf-ionicons.css'), '', WORDFENCE_VERSION);
		wp_enqueue_style('wordfenceOnboardingCSS', wfUtils::getBaseURL() . wfUtils::versionedAsset('css/wf-onboarding.css'), '', WORDFENCE_VERSION);
		wp_enqueue_style('wordfence-colorbox-style', wfUtils::getBaseURL() . wfUtils::versionedAsset('css/wf-colorbox.css'), '', WORDFENCE_VERSION);
		
		wp_enqueue_script('jquery.wfcolorbox', wfUtils::getBaseURL() . wfUtils::versionedAsset('js/jquery.colorbox-min.js'), array('jquery'), WORDFENCE_VERSION);
	}
	
	/**
	 * Outputs the onboarding overlay if it needs to be shown on the plugins page.
	 */
	public static function _admin_header() {
		$willShowAnyTour = (self::shouldShowNewTour(self::TOUR_DASHBOARD) || self::shouldShowUpgradeTour(self::TOUR_DASHBOARD) ||
							self::shouldShowNewTour(self::TOUR_FIREWALL) || self::shouldShowUpgradeTour(self::TOUR_FIREWALL) ||
							self::shouldShowNewTour(self::TOUR_SCAN) || self::shouldShowUpgradeTour(self::TOUR_SCAN) ||
							self::shouldShowNewTour(self::TOUR_BLOCKING) || self::shouldShowUpgradeTour(self::TOUR_BLOCKING) ||
							self::shouldShowNewTour(self::TOUR_LIVE_TRAFFIC) || self::shouldShowUpgradeTour(self::TOUR_LIVE_TRAFFIC));
		
		$screen = get_current_screen();
		if ($screen->base == 'plugins' && self::shouldShowAttempt1()) {
			register_shutdown_function('wfOnboardingController::_markAttempt1Shown');
			$freshInstall = wfView::create('onboarding/fresh-install')->render(); 
			
			echo wfView::create('onboarding/overlay', array(
				'contentHTML' => $freshInstall,
			))->render();
		}
		else if (preg_match('/wordfence/i', $screen->base) && $willShowAnyTour) {
			echo wfView::create('onboarding/tour-overlay')->render();
		}
	}
	
	public static function _markAttempt1Shown() {
		wfConfig::set('onboardingAttempt1', self::ONBOARDING_FIRST_SKIPPED); //Only show it once, default to skipped after outputting the first time
	}
	
	public static function shouldShowAttempt1() { //Overlay on plugin page
		if (wfConfig::get('onboardingAttempt3') == self::ONBOARDING_THIRD_LICENSE) {
			return false;
		}
		
		switch (wfConfig::get('onboardingAttempt1')) {
			case self::ONBOARDING_FIRST_LICENSE:
			case self::ONBOARDING_FIRST_SKIPPED:
				return false;
		}
		return true;
	}
	
	public static function _pre_plugins() {
		if (self::shouldShowAttempt2()) {
			echo wfView::create('onboarding/plugin-header')->render();
		}
	}
	
	public static function shouldShowAttempt2() { //Header on plugin page
		if (wfConfig::get('onboardingAttempt3') == self::ONBOARDING_THIRD_LICENSE) {
			return false;
		}
		
		$alertEmails = wfConfig::get('alertEmails');
		$show = !wfConfig::get('onboardingAttempt2') && empty($alertEmails); //Unset defaults to true, all others false
		return $show;
	}
	
	public static function shouldShowAttempt3() {
		if (isset($_GET['page']) && preg_match('/^Wordfence/', $_GET['page'])) {
			$alertEmails = wfConfig::get('alertEmails');
			return empty($alertEmails);
		}
		
		return false;
	}
	
	/**
	 * Whether or not to pop up attempt 3 at page load or wait for user interaction.
	 * 
	 * @return bool
	 */
	public static function shouldShowAttempt3Automatically() {
		static $_shouldShowAttempt3Automatically = null;
		if ($_shouldShowAttempt3Automatically !== null) { //We cache this so the answer remains the same for the whole request
			return $_shouldShowAttempt3Automatically;
		}
		
		if (!self::shouldShowAttempt3()) {
			$_shouldShowAttempt3Automatically = false;
			return false;
		}
		
		$_shouldShowAttempt3Automatically = (!wfConfig::get('onboardingAttempt3Initial'));
		return (!wfConfig::get('onboardingAttempt3Initial'));
	}
	
	public static function willShowNewTour($page) {
		$key = 'needsNewTour_' . $page;
		return wfConfig::get($key);
	}
	
	public static function shouldShowNewTour($page) {
		$key = 'needsNewTour_' . $page;
		return (!self::shouldShowAttempt3Automatically() && wfConfig::get($key));
	}
	
	public static function willShowUpgradeTour($page) {
		$key = 'needsUpgradeTour_' . $page;
		return wfConfig::get($key);
	}
	
	public static function shouldShowUpgradeTour($page) {
		$key = 'needsUpgradeTour_' . $page;
		return (!self::shouldShowAttempt3Automatically() && wfConfig::get($key));
	}
}
