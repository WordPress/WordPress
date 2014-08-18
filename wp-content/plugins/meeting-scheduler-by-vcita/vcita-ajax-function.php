<?php
add_action( 'wp_ajax_vcita_ajax_toggle_ae', 'vcita_ajax_toggle_ae');
add_action( 'wp_ajax_vcita_ajax_toggle_contact', 'vcita_ajax_toggle_contact');
add_action( 'wp_ajax_vcita_ajax_toggle_calendar', 'vcita_ajax_toggle_calendar');

function vcita_ajax_toggle_ae() {
	$vcita_widget = (array) get_option(VCITA_WIDGET_KEY);
	$vcita_widget['engage_active'] = $_POST['activate'];
	update_option(VCITA_WIDGET_KEY, $vcita_widget);
}

function vcita_ajax_toggle_contact() {
	$vcita_widget = (array) get_option(VCITA_WIDGET_KEY);

	if(isset($vcita_widget['uid']) && !empty($vcita_widget['uid'])) {
		if ($_POST['activate'] == "true") {
			make_sure_page_published($vcita_widget);
		} else {
			vcita_trash_current_page($vcita_widget);
		}	
	}
}

function vcita_ajax_toggle_calendar() {
	$vcita_widget = (array) get_option(VCITA_WIDGET_KEY);
	
	if(isset($vcita_widget['uid']) && !empty($vcita_widget['uid'])) {
		if ($_POST['activate'] == "true") {
			make_sure_calendar_page_published($vcita_widget);
		} else {
			vcita_trash_current_calendar_page($vcita_widget);
		}
	}	
}
?>