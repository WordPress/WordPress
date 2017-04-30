<?php
require_once(ADVMAN_TEMPLATE_PATH . '/Edit.php');
require_once(ADVMAN_LIB . '/Template/Metabox.php');

class Advman_Template_Edit_Ad extends Advman_Template_Edit
{
	function display($ad)
	{
		// Main pane - default options
		$properties = $ad->get_network_property_defaults();
		
		// Account information
		$fields = array('account-id','slot','counter');
		foreach ($fields as $field) {
			if (isset($properties[$field])) {
				add_meta_box('advman_account', __('Account Details', 'advman'), array('Advman_Template_Metabox', 'display_account_ad'), 'advman', 'main');
				break;
			}
		}
		
		// Format information
		$fields = array('adformat','adtype');
		foreach ($fields as $field) {
			if (isset($properties[$field])) {
				add_meta_box('advman_format', __('Ad Format', 'advman'), array('Advman_Template_Metabox', 'display_format_ad'), 'advman', 'main');
				break;
			}
		}
		
		// Appearance information
		$fields = array('alt-text','color-bg','color-border','color-link','color-text','color-title','font-text','font-title','status');
		foreach ($fields as $field) {
			if (isset($properties[$field])) {
				add_meta_box('advman_appearance', __('Ad Appearance', 'advman'), array('Advman_Template_Metabox', 'display_appearance_ad'), 'advman', 'main');
				break;
			}
		}
		
		add_meta_box('advman_display_options', __('Website Display Options', 'advman'), array('Advman_Template_Metabox', 'display_options_ad'), 'advman', 'main');
		// Main pane - advanced options
		add_meta_box('advman_optimisation', __('Optimization', 'advman'), array('Advman_Template_Metabox', 'display_optimisation_ad'), 'advman', 'advanced');
		add_meta_box('advman_code', __('Code', 'advman'), array('Advman_Template_Metabox', 'display_code_ad'), 'advman', 'advanced');
		// Main pane - low priority options
		add_meta_box('advman_history', __('History', 'advman'), array('Advman_Template_Metabox', 'display_history_ad'), 'advman', 'advanced');
		
		parent::display($ad);
	}
}
?>