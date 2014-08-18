<?php

/* --- Wordpress Hooks Implementations --- */

/**
 * Add the JS code for the vCita Active Engage feature
 */
function vcita_add_active_engage() {
	$vcita_widget = (array) get_option(VCITA_WIDGET_KEY);
	
	if (!is_admin() && $vcita_widget['engage_active'] == 'true') {
		// Will be added to the head of the page
		?>
		 <script type="text/javascript">//<![CDATA[
			var vcHost = document.location.protocol == "https:" ? "https://" : "http://";
			var vcUrl = "<?php echo VCITA_SERVER_BASE; ?>" + "/" + "<?php echo vcita_get_uid() ?>" + '/loader.js';
			document.write(unescape("%3Cscript src='" + vcHost + vcUrl + "' type='text/javascript'%3E%3C/script%3E"));	
		//]]></script>
		
		<?php
	}
}

/**
 * Add the JS code for the Admin section vCita Active Engage feature
 */
function vcita_add_active_engage_admin() {
	$vcita_widget = (array) get_option(VCITA_WIDGET_KEY);
	
	if (isset($vcita_widget["uid"]) && !empty($vcita_widget['uid'])) {
		// Will be added to the head of the page
		// Currently disabled
	}
}

/**
 * Use the current settings and create the vCita widget. - simply call the main vcita_add_contact function with the required parameters
 */
function vcita_widget_content($args) {
    $vcita_widget = (array) get_option(VCITA_WIDGET_KEY);

    echo vcita_add_contact( array('type' => 'widget', 'title' => $vcita_widget['title'], 'height' => '430px'));
}

/**
 * Main function for creating the widget html representation.
 * Transforms the shorcode parameters to the desired iframe call.
 *
 * Syntax as follows:
 * shortcode name - VCITA_WIDGET_SHORTCODE
 *
 * Arguments:
 * @param  type - Type of widget, can be "contact" or "widget". default is "contact"
 * @param email - The associated expert email. default is the currently saved "UID"
 * @param first_name - The first name of the expert. default is using the name of the associated Expert's UID
 * @param last_name - The last name of the expert. default is using the name of the associated Expert's UID
 * @param uid - The Unique identification for the user - if this is used it overrides the email / first name / last name
 * @param title - The title which will be above the widget. default is empty
 * @param widget - The width of the widget. default is "100%"
 * @param height - The height of the widget. default is "450px"
 *
 */
function vcita_add_contact($atts) {
    $vcita_widget = (array) get_option(VCITA_WIDGET_KEY);

    extract(shortcode_atts(array(
        'type' => 'contact',
        'email' => '',
        'first_name' => '',
        'last_name' => '',
        'uid' => $vcita_widget['uid'],
        'id' => '',
        'title' => '',
        'width' => '100%',
        'height' => '450px',
    ), $atts));

	// If user isn't available - try and create one.
    if (!empty($email)) {
		$vcita_widget['email'] = $email;
		$vcita_widget['first_name'] = $first_name;
		$vcita_widget['last_name'] = $last_name;
		$vcita_widget['uid'] = '';
        $vcita_widget = generate_or_validate_user($vcita_widget);
		
		// Don't save the user as the widget user - just use it 
		$id = $vcita_widget["uid"]; 
		
    } else if (empty($id)) {
    	if (empty($uid)) { 
    		$id = vcita_get_uid();
    	} else {
			$id = $uid;
		}
	}

	return vcita_create_embed_code($type, $id, $width, $height);
	
}

function vcita_add_calendar($atts) {
    $vcita_widget = (array) get_option(VCITA_WIDGET_KEY);
    extract(shortcode_atts(array(
        'type' => 'scheduler',
        'email' => '',
        'first_name' => '',
        'last_name' => '',
        'uid' => $vcita_widget['uid'],
        'id' => '',
        'title' => '',
        'width' => '500px',
        'height' => '450px',
    ), $atts));

  // If user isn't available - try and create one.
    if (!empty($email)) {
    $vcita_widget['email'] = $email;
    $vcita_widget['first_name'] = $first_name;
    $vcita_widget['last_name'] = $last_name;
    $vcita_widget['uid'] = '';
        $vcita_widget = generate_or_validate_user($vcita_widget);
    
    // Don't save the user as the widget user - just use it 
    $id = $vcita_widget["uid"]; 
    
    } else if (empty($id)) {
      if (empty($uid)) { 
        $id = vcita_get_uid();
      } else {
      $id = $uid;
    }
  }

  return vcita_create_embed_code($type, $id, $width, $height);
  
}

/**
 * Create the The iframe HTML Tag according to the given paramters
 */
function vcita_create_embed_code($type, $uid, $width, $height) {
    // Only present if UID is available 
    if (isset($uid) && !empty($uid)) {        
		// Load embed code from the cache if possible
		if ( false === ( $code = get_transient( 'embed_code' . $type . $uid . $width . $height) ) ) {
			extract(vcita_get_contents("http://".VCITA_SERVER_BASE."/api/experts/" . urlencode($uid) . "/embed_code?type=" . $type . "&width=" . urlencode($width) . "&height=" . urlencode($height)));
			$data = json_decode($raw_data);
			if ($success) {
				$code = $data->{'code'};			
				// Set the embed code to be cached for an hour
				set_transient( 'embed_code' . $type . $uid . $width . $height, $code, 3600);
			}
			else {
				$code = "<iframe frameborder='0' src='http://".VCITA_SERVER_BASE."/" . urlencode($uid) . "/" . $type . "/' width='".$width."' height='".$height."'></iframe>";
			}
		}
    }
	return $code;
}

/**
 * Embed the function for toggling visibility of an item.
 */
function vcita_embed_toggle_visibility() {
	?>
	<script type='text/javascript'>
	    function vcita_toggle_visibility(id) {
		    document.getElementById(id).style.display = (document.getElementById(id).style.display == 'block') ? 'none' : 'block';
	    }
		
		function vcita_toggle_option_visibility(id) {
			vcita_toggle_visibility(id);
		    document.getElementById(id + "_marker").innerHTML = (document.getElementById(id).style.display == 'block') ? '-' : '+';
	    }
	</script>
	<?php
}

/**
 * Create a Javascript function which go over on all the given ids and for each, clear the field and enable it
 */
function vcita_embed_clear_names($ids) {
	?>
	<script type='text/javascript'>
		function vcita_clearNames() {

	<?php
		foreach ($ids as $id) {
			vcita_embed_clear_name($id);
		}
	?>

	    }
	</script>
	<?php
}

/**
 * Create a Javascript snippet which will take the id and will clear the field.
 * By clear it will do the following: erase the field content, enable the field and clear the title element.
 *
 * This only changes fields which are disabled
 */
function vcita_embed_clear_name($id) {
	?>
    element = document.getElementById("<?php echo $id ?>");

    if (element.disabled) {
        element.value = '';
        element.removeAttribute('disabled');
        element.removeAttribute('title');
    }

	<?php
}
?>