<?php
// function used by certain fields to detect parameter prefix (? or &)
function wppb_detect_prefix(){
	$conjure = '?';
	$pageURL2 = 'http';
	
	if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")) {
		$pageURL2 .= "s";
	}
	$pageURL2 .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL2 .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}else{
		$pageURL2 .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	$questionPos = strpos( (string)$pageURL2, '?' );
	
	if($questionPos !== false)
		$conjure = '&';
		
	return $conjure;
}


/* the function to handle the HEADING custom field */
function wppb_heading_handler($page, $item_title, $id, $filterArray, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);

	$extraContent11 = $extraContent12 = '';
	if ($page == 'back_end'){
		$extraContent11 = '<h3>'; 
		$extraContent12 = '</h3>'; 
	}
	$filterArray['headerCustomField'] = '
		<p class="extraFieldHeading" id="custom_field_'.$id.'">'.$extraContent11.'<strong>'.$item_title.'</strong>'.$extraContent12.'</p>';
	$filterArray['headerCustomField'] = apply_filters('wppb_'.$page.'_header_custom_field_'.$id, $filterArray['headerCustomField'], $id, $extraContent11, $item_title, $extraContent12);
	
	return $filterArray['headerCustomField'];
	
}
/* END the function to handle the HEADING custom field */

/* the function to handle the INPUT custom field */
function wppb_input_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);

	$inputValue = get_the_author_meta( $fieldName, $currentUser );
	if ($page == 'back_end'){
		$filterArray['inputCustomField'] = '
			<table class="form-table">
				<tr>
					<th><label for="'.$item_type.$id.'">'.$item_title.'</label></th>
					<td>
						<input class="text-input" size="45" name="'.$item_type.$id.'" type="text" id="'.$item_type.$id.'" value="'.$inputValue.'"';
						if ((trim($item_options) != '') || ($item_options != NULL))
							$filterArray['inputCustomField'] .= ' maxlength="'.trim($item_options).'"';
						$filterArray['inputCustomField'] .= ' /><span class="description">'.$item_desc.'</span>
					</td>
				</tr>
			</table>';
			$filterArray['inputCustomField'] = apply_filters('wppb_'.$page.'_input_custom_field_'.$id, $filterArray['inputCustomField'], $item_title, $id, $item_type, $inputValue, trim($item_options), $item_desc);		
	}else{
		$errorVar = '';
		$errorMark = '';
		if (isset($item_required))
			if ($item_required == 'yes')
				$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
		if (in_array($id, $extraFieldsErrorHolder)){
			$errorVar = ' errorHolder';
			$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)', 'profilebuilder') .'"/>';
		}

		if ($page == 'register'){
			if (isset($postData[$item_type.$id]))
				$inputValue = $postData[$item_type.$id];
			else $inputValue = '';
		}
		$filterArray['inputCustomField'] = '
			<p class="form-input'.$id.$errorVar.'">
				<label for="'.$item_type.$id.'">'.$item_title.$errorMark.'</label>
				<input class="text-input" name="'.$item_type.$id.'" type="text" id="'.$item_type.$id.'" value="'.$inputValue.'"';
					if (($item_options != '') || ($item_options != NULL))
						$filterArray['inputCustomField'] .= ' maxlength="'.$item_options.'"';
					$filterArray['inputCustomField'] .= ' /><span class="wppb-description-delimiter">'.$item_desc.'</span>					
			</p><!-- .form-input'.$id.' -->';
		$filterArray['inputCustomField'] = apply_filters('wppb_'.$page.'_input_custom_field_'.$id, $filterArray['inputCustomField'], $item_title, $item_type, $id, $inputValue, $item_options, $item_desc, $item_required, $errorMark, $errorVar);		
	}
			
	return $filterArray['inputCustomField'];
}
/* END the function to handle the INPUT custom field */

/* the function to handle the HIDDEN INPUT custom field */
function wppb_hidden_input_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);

	if ($page == 'back_end'){
		$inputType = 'text';
		$inputValue = get_the_author_meta( $fieldName, $currentUser );
		if (trim($inputValue) == '')
			$inputValue = $item_options;
		
		if (!current_user_can( 'manage_options' )){
			$inputType = 'hidden';
			$filterArray['hiddenInputCustomField'] .= '<!--';
		}
		$filterArray['hiddenInputCustomField'] .= '
			<table class="form-table">
				<tr>
					<th><label for="'.$item_type.$id.'">'.$item_title.'</label></th>
					<td><input class="text-input" name="'.$item_type.$id.'" type="'.$inputType.'" id="'.$item_type.$id.'" value="'.$inputValue.'"/>
						<span class="wppb-description-delimiter">'.$item_desc.'</span>					
					</td>
				</tr>
			</table>';
		if (!current_user_can( 'manage_options' ))
			$filterArray['hiddenInputCustomField'] .= '-->';
		$filterArray['hiddenInputCustomField'] = apply_filters('wppb_'.$page.'_hidden_input_custom_field_'.$id, $filterArray['hiddenInputCustomField'], $item_title, $item_type, $id, $item_options, $item_desc);		
	}else{
		$filterArray['hiddenInputCustomField'] = '
			<!-- form-input'.$id.$errorVar.'"-->
				<label for="'.$item_type.$id.'"><!--'.$item_title.'--></label>
				<input class="text-input" name="'.$item_type.$id.'" type="hidden" id="'.$item_type.$id.'" value="'.$item_options.'" />
				<span class="wppb-description-delimiter"><!--'.$item_desc.'--></span>					
			<!-- .form-input'.$id.' -->';
		$filterArray['hiddenInputCustomField'] = apply_filters('wppb_'.$page.'_hidden_input_custom_field_'.$id, $filterArray['hiddenInputCustomField'], $item_title, $item_type, $id, $item_options, $item_desc, $errorVar);		
	}
			
	return $filterArray['hiddenInputCustomField'];
}
/* END the function to handle the HIDDEN INPUT custom field */


/* the function to handle the CHECKBOX custom field */
function wppb_checkbox_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);
	$item_options = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $item_options);

	$userData = get_user_meta($currentUser, $fieldName, true);
	$userDataArray = explode(',', $userData);
	$newValue = str_replace(' ', '#@space@#', $item_options);  //we need to escape the spaces in the options list, because it won't save
	$checkboxValue = explode(',', $item_options);
	$checkboxValue2 = explode(',', $newValue);
	
	if ($page == 'back_end'){
		$filterArray['checkboxCustomField'] = '
			<table class="form-table">
				<tr>
					<th><label for="'.$item_type.$id.'">'.$item_title.'</label></th>
					<td>';
						foreach($checkboxValue2 as $thisValue){
							$localValue = str_replace('#@space@#', ' ', $thisValue);
							$filterArray['checkboxCustomField'] .= '<input value="'.$thisValue.'" name="'.$thisValue.$id.'" type="checkbox"'; 
							if (in_array($localValue, $userDataArray, true)) 
								$filterArray['checkboxCustomField'] .= ' checked';									
							$filterArray['checkboxCustomField'] .= ' /><span style="padding-left:5px"></span>'.$localValue.'<span style="padding-left:20px"></span>';
						}
					$filterArray['checkboxCustomField'] .= '
						<br/><span class="description">'.$item_desc.'</span>
					</td>
				</tr>
		</table>';
		
		$filterArray['checkboxCustomField'] = apply_filters('wppb_'.$page.'_checkbox_custom_field_'.$id, $filterArray['checkboxCustomField'], $userData, $item_title, $checkboxValue, $checkboxValue2, $id, $item_desc);		
	}else{
		
		$errorVar = '';
		$errorMark = '';
		if (isset($item_required))
			if ($item_required == 'yes')
				$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
		if (in_array($id, $extraFieldsErrorHolder)){
			$errorVar = ' errorHolder';
			$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)', 'profilebuilder') .'"/>';
		}
		
		$filterArray['checkboxCustomField'] = '
			<p class="form-checkbox'.$id.$errorVar.'">
				<label for="'.$item_type.$id.'">'.$item_title.$errorMark.'</label>
				<span class="wppb-description-delimiter">';					
					foreach($checkboxValue2 as $thisValue){
						$localValue = str_replace('#@space@#', ' ', $thisValue);
						$filterArray['checkboxCustomField'] .= '<input value="'.$thisValue.'" name="'.$thisValue.$id.'" type="checkbox"'; 
						if ($page == 'register'){
							$chBoxValue = '';
							if (isset($_POST[$thisValue.$id]))
								$chBoxValue = $_POST[$thisValue.$id];
							if ( $error && ( $chBoxValue == $thisValue ) ) 
								$filterArray['checkboxCustomField'] .=  ' checked';
						}elseif (in_array($localValue, $userDataArray, true)) 
							$filterArray['checkboxCustomField'] .= ' checked';									
						$filterArray['checkboxCustomField'] .= ' /><span class="wppb-rc-value">'.$localValue.'</span>';
					}
					$filterArray['checkboxCustomField'] .='<br/>'.$item_desc .'
				</span>
			</p><!-- .form-checkbox'.$id.' -->';
			$filterArray['checkboxCustomField'] = apply_filters('wppb_'.$page.'_checkbox_custom_field_'.$id, $filterArray['checkboxCustomField'], $userData, $item_title, $checkboxValue, $checkboxValue2, $id, $item_desc, $item_required, $_POST[$thisValue.$id], $userDataArray, $errorMark, $errorVar);
		
	}
			
	return $filterArray['checkboxCustomField'];
}
/* END the function to handle the CHECKBOX custom field */


/* the function to handle the RADIO custom field */
function wppb_radio_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);
	$item_options = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $item_options);

	$userData = get_user_meta($currentUser, $fieldName, true);
	$radioValue = explode(',', $item_options);
	
	if ($page == 'back_end'){
		$filterArray['radioCustomField'] = '
			<table class="form-table">
				<tr>
					<th><label for="'.$item_type.$id.'">'.$item_title.'</label></th>
					<td>';
					foreach($radioValue as $thisValue){
							$filterArray['radioCustomField'] .= '<input value="'.$thisValue.'" name="'.$item_type.$id.'" type="radio"'; 
							if ((strpos( (string)$userData, $thisValue )) !== FALSE) 
								$filterArray['radioCustomField'] .= ' checked';
							$filterArray['radioCustomField'] .= ' /><span style="padding-left:5px"></span>'.$thisValue.'<span style="padding-left:20px"></span>';
					}
					$filterArray['radioCustomField'] .= '
					<br/><span class="description">'.$item_desc.'</span>
					</td>
				</tr>
			</table>';
		
		$filterArray['radioCustomField'] = apply_filters('wppb_'.$page.'_radio_custom_field_'.$id, $filterArray['radioCustomField'], $userData, $radioValue, $id, $item_desc);		
	}else{
		
		$errorVar = '';
		$errorMark = '';
		if (isset($item_required))
			if ($item_required == 'yes')
				$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
		if (in_array($id, $extraFieldsErrorHolder)){
			$errorVar = ' errorHolder';
			$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)', 'profilebuilder') .'"/>';
		}

		$filterArray['radioCustomField'] = '
			<p class="form-radio'.$id.$errorVar.'">
				<label for="'.$item_type.$id.'">'.$item_title.$errorMark.'</label>
				<span class="wppb-description-delimiter">';
					foreach($radioValue as $thisValue){
						$filterArray['radioCustomField'] .= '<input value="'.$thisValue.'" name="'.$item_type.$id.'" type="radio"';
						if ($page == 'register'){
							$selValue = '';
							if (isset($_POST[$item_type.$id]))
								$selValue = $_POST[$item_type.$id];
							if ( $error && ( $selValue == $thisValue ) )
								$filterArray['radioCustomField'] .=  ' checked';
						}elseif ((strpos( (string)$userData, $thisValue )) !== FALSE)
							$filterArray['radioCustomField'] .= ' checked';
						$filterArray['radioCustomField'] .= ' /><span class="wppb-rc-value">'.$thisValue.'</span>'; 
					}
					$filterArray['radioCustomField'] .= '<br/>'.$item_desc .'
				</span>
			</p><!-- .form-radio'.$id.' -->';
			$filterArray['radioCustomField'] = apply_filters('wppb_'.$page.'_radio_custom_field_'.$id, $filterArray['radioCustomField'], $userData, $radioValue, $id, $item_desc, $item_required, $_POST[$item_type.$id], $errorMark, $errorVar);
		
	}
			
	return $filterArray['radioCustomField'];
}
/* END the function to handle the RADIO custom field */

/* the function to handle the SELECT custom field */
function wppb_select_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);
	$item_options = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $item_options);

	$userData = get_user_meta($currentUser, $fieldName, true);
	$selectValue = explode(',', $item_options);	
	
	if ($page == 'back_end'){
		$filterArray['selectCustomField'] = '
			<table class="form-table">
				<tr>
					<th><label for="'.$item_type.$id.'">'.$item_title.'</label></th>
					<td>
					<select name="'.$item_type.$id.'" id="'.$item_type.$id.'">';
						foreach($selectValue as $thisValue){
							$filterArray['selectCustomField'] .= '<option value="'.$thisValue.'"'; 
							if ((strpos( (string)$userData, $thisValue )) !== FALSE) 
								$filterArray['selectCustomField'] .= ' selected';
							$filterArray['selectCustomField'] .= '>'.$thisValue.'</option>';
						}
					$filterArray['selectCustomField'] .= '</select><br/>
					<span class="description">'.$item_desc.'</span>
					</td>
				</tr>
			</table>';	
		
		$filterArray['selectCustomField'] = apply_filters('wppb_'.$page.'_select_custom_field_'.$id, $filterArray['selectCustomField'], $userData, $selectValue, $item_title, $item_desc);		
	}else{
		
		$errorVar = '';
		$errorMark = '';
		if (isset($item_required))
			if ($item_required == 'yes')
				$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
		if (in_array($id, $extraFieldsErrorHolder)){
			$errorVar = ' errorHolder';
			$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)', 'profilebuilder') .'"/>';
		}

		$filterArray['selectCustomField'] = '
			<p class="form-select'.$id.$errorVar.'">
				<label for="'.$item_type.$id.'">'.$item_title.$errorMark.'</label>
				<select name="'.$item_type.$id.'" id="'.$item_type.$id.'">';
				foreach($selectValue as $thisValue){
					$filterArray['selectCustomField'] .= '<option value="'.$thisValue.'"'; 
					if ($page == 'register'){
						if ( $error && ( $_POST[$item_type.$id] == $thisValue ) ) 
							$filterArray['selectCustomField'] .= ' selected ';
					}elseif ((strpos( (string)$userData, $thisValue )) !== FALSE) 
						$filterArray['selectCustomField'] .= ' selected';
					$filterArray['selectCustomField'] .= '>'.$thisValue.'</option>';
				}
				$filterArray['selectCustomField'] .= '</select><span class="wppb-description-delimiter">'.$item_desc.'</span>
			</p><!-- .form-select'.$id.' -->';
		$filterArray['selectCustomField'] = apply_filters('wppb_'.$page.'_select_custom_field_'.$id, $filterArray['selectCustomField'], $userData, $selectValue, $item_title, $item_desc, $item_required, $errorMark, $errorVar);
		
	}
			
	return $filterArray['selectCustomField'];
}
/* END the function to handle the SELECT custom field */

/* the function to handle the COUNTRY SELECT custom field */
function wppb_country_select_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);

	/* the array holding the countries */
	$countryArray = array ('Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antarctica', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Bouvet Island', 'Brazil', 'British Indian Ocean Territory', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China', 'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo', 'Congo, The Democratic Republic of The', 'Cook Islands', 'Costa Rica', 'Cote D\'ivoire', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Falkland Islands (Malvinas)', 'Faroe Islands', 'Fiji', 'Finland', 'France', 'French Guiana', 'French Polynesia', 'French Southern Territories', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guinea', 'Guinea-bissau', 'Guyana', 'Haiti', 'Heard Island and Mcdonald Islands', 'Holy See (Vatican City State)', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran, Islamic Republic of', 'Iraq', 'Ireland', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Korea, Democratic People\'s Republic of', 'Korea, Republic of', 'Kuwait', 'Kyrgyzstan', 'Lao People\'s Democratic Republic', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libyan Arab Jamahiriya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Macao', 'Macedonia, The Former Yugoslav Republic of', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Micronesia, Federated States of', 'Moldova, Republic of', 'Monaco', 'Mongolia', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'The Netherlands', 'Netherlands Antilles', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk Island', 'Northern Mariana Islands', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Palestinian Territory, Occupied', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn', 'Poland', 'Portugal', 'Puerto Rico', 'Qatar', 'Reunion', 'Romania', 'Russian Federation', 'Rwanda', 'Saint Helena', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Pierre and Miquelon', 'Saint Vincent and The Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia and Montenegro', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Georgia and The South Sandwich Islands', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Svalbard and Jan Mayen', 'Swaziland', 'Sweden', 'Switzerland', 'Syrian Arab Republic', 'Taiwan, Province of China', 'Tajikistan', 'Tanzania, United Republic of', 'Thailand', 'Timor-leste', 'Togo', 'Tokelau', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks and Caicos Islands', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'United States Minor Outlying Islands', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Venezuela', 'Viet Nam', 'Virgin Islands, British', 'Virgin Islands, U.S.', 'Wallis and Futuna', 'Western Sahara', 'Yemen', 'Zambia', 'Zimbabwe');
	$countryArray = apply_filters('wppb_'.$page.'_country_select_array', $countryArray);
	

	$userData = get_user_meta($currentUser, $fieldName, true);
	
	if ($page == 'back_end'){
		$filterArray['countrySelectCustomField'] = '
			<table class="form-table">
				<tr>
					<th><label for="'.$item_type.$id.'">'.$item_title.'</label></th>
					<td>
					<select name="'.$item_type.$id.'" id="'.$item_type.$id.'">';
						foreach($countryArray as $thisValue){
							$filterArray['countrySelectCustomField'] .= '<option value="'.$thisValue.'"'; 
							if ((strpos( (string)$userData, $thisValue )) !== FALSE) 
								$filterArray['countrySelectCustomField'] .= ' selected';
							$filterArray['countrySelectCustomField'] .= '>'.$thisValue.'</option>';
						}
					$filterArray['countrySelectCustomField'] .= '</select><br/>
					<span class="description">'.$item_desc.'</span>
					</td>
				</tr>
			</table>';	
		
		$filterArray['countrySelectCustomField'] = apply_filters('wppb_'.$page.'_country_select_custom_field_'.$id, $filterArray['countrySelectCustomField'], $userData, $item_title, $id, $item_desc, $item_required, $countryArray, $errorMark, $errorVar, $error, $_POST[$item_type.$id]);		
	}else{
		
		$errorVar = '';
		$errorMark = '';
		if (isset($item_required))
			if ($item_required == 'yes')
				$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
		if (in_array($id, $extraFieldsErrorHolder)){
			$errorVar = ' errorHolder';
			$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)', 'profilebuilder') .'"/>';
		}

		$filterArray['countrySelectCustomField'] = '
			<p class="form-select'.$id.$errorVar.'">
				<label for="'.$item_type.$id.'">'.$item_title.$errorMark.'</label>
				<select name="'.$item_type.$id.'" id="'.$item_type.$id.'">';
				foreach($countryArray as $thisValue){
					$filterArray['countrySelectCustomField'] .= '<option value="'.$thisValue.'"'; 
					if ($page == 'register'){
						if ( $error && ( $_POST[$item_type.$id] == $thisValue ) ) 
							$filterArray['countrySelectCustomField'] .= ' selected ';
					}elseif ((strpos( (string)$userData, $thisValue )) !== FALSE) 
						$filterArray['countrySelectCustomField'] .= ' selected';
					$filterArray['countrySelectCustomField'] .= '>'.$thisValue.'</option>';
				}
				$filterArray['countrySelectCustomField'] .= '</select><span class="wppb-description-delimiter">'.$item_desc.'</span>
			</p><!-- .form-select'.$id.' -->';
		$filterArray['countrySelectCustomField'] = apply_filters('wppb_'.$page.'_country_select_custom_field_'.$id, $filterArray['countrySelectCustomField'], $userData, $item_title, $id, $item_desc, $item_required, $countryArray, $errorMark, $errorVar, $error, $_POST[$item_type.$id]);
		
	}
			
	return $filterArray['countrySelectCustomField'];
}
/* END the function to handle the COUNTRY SELECT custom field */


/* the function to handle the COUNTRY SELECT custom field */
function wppb_timezone_select_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);

	/* the array holding the timezones */
	$timezoneArray = array ('(GMT -12:00) Eniwetok, Kwajalein', '(GMT -11:00) Midway Island, Samoa', '(GMT -10:00) Hawaii', '(GMT -9:00) Alaska', '(GMT -8:00) Pacific Time (US &amp; Canada)', '(GMT -7:00) Mountain Time (US &amp; Canada)', '(GMT -6:00) Central Time (US &amp; Canada), Mexico City', '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima', '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz', '(GMT -3:30) Newfoundland', '(GMT -3:00) Brazil, Buenos Aires, Georgetown', '(GMT -2:00) Mid-Atlantic', '(GMT -1:00) Azores, Cape Verde Islands', '(GMT) Western Europe Time, London, Lisbon, Casablanca', '(GMT +1:00) Brussels, Copenhagen, Madrid, Paris', '(GMT +2:00) Kaliningrad, South Africa', '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg', '(GMT +3:30) Tehran', '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi', '(GMT +4:30) Kabul', '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent', '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi', '(GMT +5:45) Kathmandu', '(GMT +6:00) Almaty, Dhaka, Colombo', '(GMT +7:00) Bangkok, Hanoi, Jakarta', '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong', '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk', '(GMT +9:30) Adelaide, Darwin', '(GMT +10:00) Eastern Australia, Guam, Vladivostok', '(GMT +11:00) Magadan, Solomon Islands, New Caledonia', '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka');
	$timezoneArray = apply_filters('wppb_'.$page.'_timezone_select_array', $timezoneArray);
	

	$userData = get_user_meta($currentUser, $fieldName, true);
	
	if ($page == 'back_end'){
		$filterArray['timezoneSelectCustomField'] = '
			<table class="form-table">
				<tr>
					<th><label for="'.$item_type.$id.'">'.$item_title.'</label></th>
					<td>
					<select name="'.$item_type.$id.'" id="'.$item_type.$id.'">';
						foreach($timezoneArray as $thisValue){
							$filterArray['timezoneSelectCustomField'] .= '<option value="'.$thisValue.'"'; 
							if ((strpos( (string)$userData, $thisValue )) !== FALSE) 
								$filterArray['timezoneSelectCustomField'] .= ' selected';
							$filterArray['timezoneSelectCustomField'] .= '>'.$thisValue.'</option>';
						}
					$filterArray['timezoneSelectCustomField'] .= '</select><br/>
					<span class="description">'.$item_desc.'</span>
					</td>
				</tr>
			</table>';	
		
		$filterArray['timezoneSelectCustomField'] = apply_filters('wppb_'.$page.'_timezone_select_custom_field_'.$id, $filterArray['timezoneSelectCustomField'], $userData, $item_title, $id, $item_desc);		
	}else{
		
		$errorVar = '';
		$errorMark = '';
		if (isset($item_required))
			if ($item_required == 'yes')
				$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
		if (in_array($id, $extraFieldsErrorHolder)){
			$errorVar = ' errorHolder';
			$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)', 'profilebuilder') .'"/>';
		}

		$filterArray['timezoneSelectCustomField'] = '
			<p class="form-select'.$id.$errorVar.'">
				<label for="'.$item_type.$id.'">'.$item_title.$errorMark.'</label>
				<select name="'.$item_type.$id.'" id="'.$item_type.$id.'">';
				foreach($timezoneArray as $thisValue){
					$filterArray['timezoneSelectCustomField'] .= '<option value="'.$thisValue.'"'; 
					if ($page == 'register'){
						if ( $error && ( $_POST[$item_type.$id] == $thisValue ) ) 
							$filterArray['timezoneSelectCustomField'] .= ' selected ';
					}elseif ((strpos( (string)$userData, $thisValue )) !== FALSE) 
						$filterArray['timezoneSelectCustomField'] .= ' selected';
					$filterArray['timezoneSelectCustomField'] .= '>'.$thisValue.'</option>';
				}
				$filterArray['timezoneSelectCustomField'] .= '</select><span class="wppb-description-delimiter">'.$item_desc.'</span>
			</p><!-- .form-select'.$id.' -->';
		$filterArray['timezoneSelectCustomField'] = apply_filters('wppb_'.$page.'_timezone_select_custom_field_'.$id, $filterArray['timezoneSelectCustomField'], $userData, $item_title, $id, $item_desc, $item_required, $error, $_POST[$item_type.$id], $timezoneArray, $errorMark, $errorVar);
		
	}
			
	return $filterArray['timezoneSelectCustomField'];
}
/* END the function to handle the COUNTRY SELECT custom field */

/* the function to handle the DATEPICKER custom field */
function wppb_datepicker_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);

	$userData = get_user_meta($currentUser, $fieldName, true);
	
	if ($page == 'back_end'){
		$filterArray['datepickerCustomField'] = '
			<table class="form-table">
				<tr>
					<th><label for="'.$item_type.$id.'">'.$item_title.'</label></th>
					<td>
						<input size="45" id="'.$item_type.$id.'" name="'.$item_type.$id.'" type="text" class="wppb_datepicker" value="'.$userData.'"/>
						<span class="description">'.$item_desc.'</span>
					</td>
				</tr>
			</table>';
		
		$filterArray['datepickerCustomField'] = apply_filters('wppb_'.$page.'_datepicker_custom_field_'.$id, $filterArray['datepickerCustomField'], $userData, $item_title, $id, $item_desc);		
	}else{
		
		$errorVar = '';
		$errorMark = '';
		if (isset($item_required))
			if ($item_required == 'yes')
				$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
		if (in_array($id, $extraFieldsErrorHolder)){
			$errorVar = ' errorHolder';
			$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)', 'profilebuilder') .'"/>';
		}
		
		if ($page == 'register'){
			$userData = '';
			if (isset($_POST[$item_type.$id]))
				$userData = $_POST[$item_type.$id];
		}
		
		$filterArray['datepickerCustomField'] = '
			<p class="form-input'.$id.$errorVar.'">
				<label for="'.$item_type.$id.'">'.$item_title.$errorMark.'</label>
				<input name="'.$item_type.$id.'" id="'.$item_type.$id.'" type="text" class="wppb_datepicker" value="'.$userData.'" />
				<span class="wppb-description-delimiter">'.$item_desc.'</span>					
			</p><!-- .form-input'.$id.' -->';
		$filterArray['datepickerCustomField'] = apply_filters('wppb_'.$page.'_datepicker_custom_field_'.$id, $filterArray['datepickerCustomField'], $userData, $item_title, $id, $item_desc, $item_required, $errorMark, $errorVar);
		
	}
			
	return $filterArray['datepickerCustomField'];
}
/* END the function to handle the DATEPICKER custom field */


/* the function to handle the TEXTAREA custom field */
function wppb_textarea_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);

	$inputValue = get_user_meta($currentUser, $fieldName, true);
	if ($page == 'back_end'){
		$filterArray['textareaCustomField'] = '
			<table class="form-table">
				<tr>
					<th><label for="'.$item_type.$id.'">'.$item_title.'</label></th>
					<td>
						<textarea rows="'.$item_options.'" name="'.$item_type.$id.'" id="'.$item_type.$id.'" wrap="virtual">'.$inputValue.'</textarea><br/>
						<span class="description">'.$item_desc.'</span>
					</td>
				</tr>
			</table>';
		$filterArray['textareaCustomField'] = apply_filters('wppb_'.$page.'_textarea_custom_field_'.$id, $filterArray['textareaCustomField'], $inputValue, $item_title, $id, $item_desc);		
	}else{
		$errorVar = '';
		$errorMark = '';
		if (isset($item_required))
			if ($item_required == 'yes')
				$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
		if (in_array($id, $extraFieldsErrorHolder)){
			$errorVar = ' errorHolder';
			$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)', 'profilebuilder') .'"/>';
		}
		if ($page == 'register'){
			$inputValue = '';
			if (isset($_POST[$item_type.$id]))
				$inputValue = $_POST[$item_type.$id];
		}
		$filterArray['textareaCustomField'] = '
			<p class="form-textarea'.$id.$errorVar.'">
				<label for="'.$item_type.$id.'">'.$item_title.$errorMark.'</label>
				<textarea rows="'.$item_options.'" name="'.$item_type.$id.'" id="'.$item_type.$id.'" wrap="virtual">'.$inputValue.'</textarea>
				<br/><span class="wppb-description-delimiter">'.$item_desc.'</span>					
			</p><!-- .form-textarea'.$value['id'].' -->';
		$filterArray['textareaCustomField'] = apply_filters('wppb_'.$page.'_textarea_custom_field_'.$id, $filterArray['textareaCustomField'], $inputValue, $item_title, $id, $item_desc, $item_required, $errorMark, $errorVar);		
	}
			
	return $filterArray['textareaCustomField'];
}
/* END the function to handle the TEXTAREA custom field */


/* the function to handle the UPLOAD custom field */
function wppb_upload_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);
	
	//add the nonce field
	$wppb_nonce = wp_create_nonce( 'user'.$currentUser.'_nonce_upload' );
	
	if (trim($item_desc) != '')
		$item_desc .= '<br/>';
	$imgSource = WPPB_PLUGIN_URL . '/assets/images/';
	$userData = get_user_meta($currentUser, $fieldName, true);
	$postion = strpos ( (string)$userData , '_attachment_' );
	//$fileName = substr ( $userData, $postion+12); 
	$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
	$fileName = str_replace ( $wpUploadPath['baseurl'].'/profile_builder/attachments/userID_'.$currentUser.'_attachment_', '', $userData );	
	if ($page == 'back_end'){
		$filterArray['uploadCustomField'] = '
			<table class="form-table">
				<tr>
					<th><label for="'.$item_type.$id.'">'.$item_title.'</label></th>
					<td>
					<input name="'.$item_type.$id.'" id="'.$item_type.$id.'" size="40" type="file" /><font color="grey" size="1">('. __('max upload size', 'profilebuilder') .' '.WPPB_SERVER_MAX_UPLOAD_SIZE_MEGA.'b)</font>
					<br/><span class="wppb-description-delimiter">'.$item_desc;
					if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/attachments/')){
						$filterArray['uploadCustomField'] .= '</span><span class="wppb-description-delimiter"><u>'. __('Current file', 'profilebuilder') .'</u>: </span><span style="padding-left:5px"></span><span class="wppb-description-delimiter"><i>'. __('No uploaded attachment', 'profilebuilder') .'</i></span>';
					}
					else{
						$text = __('Are you sure you want to delete this attachment?', 'profilebuilder');
						$filterArray['uploadCustomField'] .= '</span><br/><span class="wppb-description-delimiter"><u>'. __('Current file', 'profilebuilder') .'</u>: <span style="padding-left:5px"></span>'.$fileName.'<span style="padding-left:15px"></span><a href="'.$userData.'" target="_blank"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a><span style="padding-left:15px"></span><a href="javascript:confirmDelete(\''.$wppb_nonce.'\',\''.$currentUser.'\',\''.$id.'\',\''.$fieldName.'\',\''.get_permalink().'\',\''.get_bloginfo('url').'/wp-admin/admin-ajax.php\',\'attachment\',\''.$text.'\')"><img src="'.$imgSource.'icon_delete.png" title="'. __('Click to delete the current attachment', 'profilebuilder') .'"></a></span>';
					}
					$filterArray['uploadCustomField'] .= '
					</td>
				</tr>
			</table>';
		$filterArray['uploadCustomField'] = apply_filters('wppb_'.$page.'_upload_custom_field_'.$id, $filterArray['uploadCustomField'], $userData, $item_title, $id, $item_desc);		
	}else{
		$errorVar = '';
		$errorMark = '';
		if (isset($item_required))
			if ($item_required == 'yes')
				$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
		if (in_array($id, $extraFieldsErrorHolder)){
			$errorVar = ' errorHolder';
			$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)', 'profilebuilder') .'"/>';
		}
		
		if ($page == 'register'){
			$userData = '';
		}

		$filterArray['uploadCustomField'] = '
			<p class="form-upload'.$id.$errorVar.'">
				<label for="'.$item_type.$id.'">'.$item_title.$errorMark.'</label>
				<input name="'.$item_type.$id.'" id="'.$item_type.$id.'" size="30" type="file" /><span class="wppb-max-upload">('. __('max upload size', 'profilebuilder') .' '.WPPB_SERVER_MAX_UPLOAD_SIZE_MEGA.'b)</span>
				<span class="wppb-description-delimiter">'.$item_desc;
				if ($page != 'register'){
					if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/attachments/')){
						$filterArray['uploadCustomField'] .= '</span><span class="wppb-description-delimiter"><u>'. __('Current file', 'profilebuilder') .'</u>: </span><span class="wppb-description-delimiter"><i>'. __('No uploaded attachment', 'profilebuilder') .'</i></span>';
					}
					else{
						if (($item_required !=null) && ($item_required == 'yes')){
							$filterArray['uploadCustomField'] .= '</span><br/><span class="wppb-description-delimiter"><u>'. __('Current file', 'profilebuilder') .'</u>: '.$fileName.'<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a><img src="'.$imgSource.'icon_delete_disabled.png" title="' . __('The attachment can\'t be deleted (It was marked as required by the administrator)', 'profilebuilder') .'"></span>';
						}else{
							$text = __('Are you sure you want to delete this attachment?', 'profilebuilder');
							$filterArray['uploadCustomField'] .= '</span><br/><span class="wppb-description-delimiter"><u>'. __('Current file', 'profilebuilder') .'</u>: '.$fileName.'<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a><a href="javascript:confirmDelete(\''.$wppb_nonce.'\',\''.$currentUser.'\',\''.$id.'\',\''.$fieldName.'\',\''.get_permalink().wppb_detect_prefix().'fileType=attachment&fileName='.$fileName.'\',\''.get_bloginfo('url').'/wp-admin/admin-ajax.php\',\'attachment\',\''.$text.'\')" class="wppb-dattachment"><img src="'.$imgSource.'icon_delete.png" title="'. __('Click to delete the current attachment', 'profilebuilder') .'"></a></span>';
						}
					}
				}elseif($page == 'register')
					$filterArray['uploadCustomField'] .= '</span>';
			$filterArray['uploadCustomField'] .= '</p><!-- .form-upload'.$id.' -->';
		$filterArray['uploadCustomField'] = apply_filters('wppb_'.$page.'_upload_custom_field_'.$id, $filterArray['uploadCustomField'], $userData, $item_title, $id, $item_desc, $item_required, $errorMark, $errorVar, $fileName);
	}
			
	return $filterArray['uploadCustomField'];
}
/* END the function to handle the UPLOAD custom field */


/* the function to handle the AVATAR custom field */
function wppb_avatar_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);

	//add the nonce field
	$wppb_nonce = wp_create_nonce( 'user'.$currentUser.'_nonce_avatar' );

	$imgSource = WPPB_PLUGIN_URL . '/assets/images/';
	
	/* get the needed userdatas */
	$userData = get_user_meta($currentUser, $fieldName, true);  // to use for the link
	$userData2 = get_user_meta($currentUser, 'resized_avatar_'.$id, true); 	//to use for the preview	

	if ($userData != ''){
		if ($userData2 == ''){
			wppb_resize_avatar($currentUser);
			$userData2 = get_user_meta($currentUser, 'resized_avatar_'.$id, true); 	//to use for the preview
		}

		//get image info
		$imgRelativePath = get_user_meta($currentUser, 'resized_avatar_'.$id.'_relative_path', true); //get relative path
		$info = getimagesize($imgRelativePath);

		
		//this checks if it only has 1 component
		if (is_numeric($item_options)){
			$width = $height = $item_options;
		//this checks if the entered value has 2 components
		}else{
			$sentValue = explode(',',$item_options);
			$width = $sentValue[0];
			$height = $sentValue[1];
		}
		
		//call the avatar resize function if needed
		if (($info[0] != $width) || ($info[1] != $height)){
			wppb_resize_avatar($currentUser);
			//re-fetch user-data
			$userData2 = get_user_meta($currentUser, 'resized_avatar_'.$id, true);
		}
	}
	
	$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
	$fileName = str_replace ( $wpUploadPath['baseurl'].'/profile_builder/attachments/userID_'.$currentUser.'_attachment_', '', $userData );	
	if ($page == 'back_end'){
		$filterArray['avatarCustomField'] = '
			<table class="form-table">
				<tr>
					<th><label for="'.$item_type.$id.'">'.$item_title.'</label></th>
					<td>
						<input name="'.$item_type.$id.'" id="'.$item_type.$id.'" size="40" type="file" /><font color="grey" size="1">('. __('max upload size', 'profilebuilder') .' '.WPPB_SERVER_MAX_UPLOAD_SIZE_MEGA.'b)</font>
						<br/><span class="wppb-description-delimiter">'.$item_desc;
						if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/avatars/')){
							$filterArray['avatarCustomField'] .= '</span><br/><span class="wppb-description-delimiter"><u>'. __('Current avatar', 'profilebuilder') .'</u>: </span><span style="padding-left:5px"></span><span class="wppb-description-delimiter"><i>'. __('No uploaded avatar', 'profilebuilder') .'</i></span>';
						}
						else{
							/* display the resized image*/
							$filterArray['avatarCustomField'] .= '<br/><span class="avatar-border"><IMG SRC="'.$userData2.'" TITLE="'. __('Avatar', 'profilebuilder') .'" ALT="'. __('Avatar', 'profilebuilder') .'" HEIGHT='.$info[1].' WIDTH='.$info[0].'></span>';
							/* display a link to the bigger image to see it clearly and a delete icon*/
							$text = __('Are you sure you want to delete this avatar?', 'profilebuilder');
							$filterArray['avatarCustomField'] .= '<span style="padding-left:10px"></span><span style="padding-left:5px"></span><a href="'.$userData.'" target="_blank"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a><span style="padding-left:15px"></span><a href="javascript:confirmDelete(\''.$wppb_nonce.'\',\''.$currentUser.'\',\''.$id.'\',\''.$fieldName.'\',\''.get_permalink().'\',\''.get_bloginfo('url').'/wp-admin/admin-ajax.php\',\'avatar\',\''.$text.'\')"><img src="'.$imgSource.'icon_delete.png" title="'. __('Click to delete the current attachment', 'profilebuilder') .'"></a>';
						}
					$filterArray['avatarCustomField'] .= '
					</td>
				</tr>
			</table>';
		$filterArray['avatarCustomField'] = apply_filters('wppb_'.$page.'_avatar_custom_field_'.$id, $filterArray['avatarCustomField'], $userData, $userData2, $item_title, $id, $item_desc);		
	}else{
		$errorVar = '';
		$errorMark = '';
		if (isset($item_required))
			if ($item_required == 'yes')
				$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
		if (in_array($id, $extraFieldsErrorHolder)){
			$errorVar = ' errorHolder';
			$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)', 'profilebuilder') .'"/>';
		}

		$filterArray['avatarCustomField'] = '
			<p class="form-upload'.$id.$errorVar.'">
				<label for="'.$item_type.$id.'">'.$item_title.$errorMark.'</label>
				<input name="'.$item_type.$id.'" id="'.$item_type.$id.'" size="30" type="file" /><span class="wppb-max-upload">('. __('max upload size', 'profilebuilder') .' '.WPPB_SERVER_MAX_UPLOAD_SIZE_MEGA.'b)</span>
				<span class="wppb-description-delimiter">'.$item_desc;
		if ($page == 'edit_profile'){			
			if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/avatars/')){
				$filterArray['avatarCustomField'] .= '</span><span class="wppb-description-delimiter"><u>'. __('Current avatar', 'profilebuilder') .'</u>: </span><span class="wppb-description-delimiter"><i>'. __('No uploaded avatar', 'profilebuilder') .'</i>';
			}
			else{
				/* display the resized image*/
				$filterArray['avatarCustomField'] .= '<br/><IMG SRC="'.$userData2.'" TITLE="'. __('Avatar', 'profilebuilder') .'" ALT="'. __('Avatar', 'profilebuilder') .'" HEIGHT='.$info[1].' WIDTH='.$info[0].'>';
				/* display a link to the bigger image to see it clearly and a delete icon*/
				if (($item_required !=null) && ($item_required == 'yes'))
					$filterArray['avatarCustomField'] .= '<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a><img src="'.$imgSource.'icon_delete_disabled.png" title="'. __('The avatar image can\'t be deleted (It was marked as required by the administrator).', 'profilebuilder') .'"></span>';
				else{
					$text = __('Are you sure you want to delete this avatar?', 'profilebuilder');
					$filterArray['avatarCustomField'] .= '<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current avatar', 'profilebuilder') .'"></a><a href="javascript:confirmDelete(\''.$wppb_nonce.'\',\''.$currentUser.'\',\''.$id.'\',\''.$fieldName.'\',\''.get_permalink().wppb_detect_prefix().'fileType=avatar'.'\',\''.get_bloginfo('url').'/wp-admin/admin-ajax.php\',\'avatar\',\''.$text.'\')" class="wppb-dattachment"><img src="'.$imgSource.'icon_delete.png" title="'. __('Click to delete the avatar', 'profilebuilder') .'"></a></span>';
				}
			}
		}
		$filterArray['avatarCustomField'] .= '	
			</span></p><!-- .form-upload'.$id.' -->';
		$filterArray['avatarCustomField'] = apply_filters('wppb_'.$page.'_avatar_custom_field_'.$id, $filterArray['avatarCustomField'], $userData, $userData2, $item_title, $id, $item_desc, $item_required, $errorMark, $errorVar);
	}
			
	return $filterArray['avatarCustomField'];
}
/* END the function to handle the AVATAR custom field */


/* the function to handle the AGREE TO TERMS custom field */
function wppb_agree_to_terms_handler($page, $item_title, $id, $item_desc, $item_required, $item_type, $currentUser, $filterArray, $extraFieldsErrorHolder, $postData, $item_options, $error, $fieldName){
	$item_title = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
	$item_desc = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);

	if ($page == 'register'){
		$errorVar = '';
		$errorMark = '';
		if (isset($item_required))
			if ($item_required == 'yes')
				$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
		if (in_array($id, $extraFieldsErrorHolder))
			$errorVar = ' errorHolder';
		$filterArray['agreeToTermsCustomField'] = '
			<p class="form-checkbox'.$id.$errorVar.'">
					<label for="'.$item_type.$id.'">'.$item_title.$errorMark.'</label>';						
					$filterArray['agreeToTermsCustomField'] .= '<input value="agree" name="'.$item_type.$id.'" id="'.$item_type.$id.'" type="checkbox"'; 
					if ( $error && ( $_POST[$item_type.$id] == 'agree' ) )
						$filterArray['agreeToTermsCustomField'] .= ' checked="yes"';
					$filterArray['agreeToTermsCustomField'] .= ' />'; 
					
					$item_desc = html_entity_decode ($item_desc);

				$filterArray['agreeToTermsCustomField'] .= '<span class="agreeToTerms">'.trim($item_desc).'</span>
					</p><!-- .form-checkbox'.$id.' -->';
		$filterArray['agreeToTermsCustomField'] = apply_filters('wppb_'.$page.'_upload_custom_field_'.$id, $filterArray['agreeToTermsCustomField'], $item_title, $id, $item_desc, $item_required, $errorMark, $errorVar);		
	}
			
	return $filterArray['agreeToTermsCustomField'];
}
/* END the function to handle the AGREE TO TERMS custom field */

/* the function to display the custom fields on all the pages */
function wppb_extra_fields($current_user, $extraFieldsErrorHolder, $extraFieldsFilterArray, $page, $error, $postData){
?>
	<script>	
		function confirmDelete(nonceField, currentUser, customFieldID, customFieldName, returnTo, ajaxurl, what, text) {
		  if (confirm(text)) {
			jQuery.post( ajaxurl ,  { action:"hook_wppb_delete", currentUser:currentUser, customFieldID:customFieldID, customFieldName:customFieldName, what:what, _ajax_nonce:nonceField}, function(response) {
				if(jQuery.trim(response)=="done"){
					window.location=returnTo;
				}else{
					alert(jQuery.trim(response));
				}
			});			
		  }
		}
	</script>	

	<?php

	/* fetch a new custom-fields (only the types) array from the database */
	$wppbFetchArray = get_option('wppb_custom_fields');
	$customFieldsArray  = array();
	
	if (count($wppbFetchArray) >= 1){
		foreach($wppbFetchArray as $key => $value){
			switch ($value['item_type']) {
				case "heading":{
					$customFieldsArray[$value['item_metaName']] = wppb_heading_handler($page, $value['item_title'], $value['id'], $extraFieldsFilterArray, $value['item_metaName']);
					break;
				}
				case "input":{
					$customFieldsArray[$value['item_metaName']] = wppb_input_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
				case "hiddenInput":{
					$customFieldsArray[$value['item_metaName']] = wppb_hidden_input_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
				case "checkbox":{
					$customFieldsArray[$value['item_metaName']] = wppb_checkbox_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
				case "radio":{
					$customFieldsArray[$value['item_metaName']] = wppb_radio_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
				case "select":{
					$customFieldsArray[$value['item_metaName']] = wppb_select_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
				case "countrySelect":{
					$customFieldsArray[$value['item_metaName']] = wppb_country_select_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
				case "timeZone":{
					$customFieldsArray[$value['item_metaName']] = wppb_timezone_select_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
				case "datepicker":{
					$customFieldsArray[$value['item_metaName']] = wppb_datepicker_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
				case "textarea":{
					$customFieldsArray[$value['item_metaName']] = wppb_textarea_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
				case "upload":{
					$customFieldsArray[$value['item_metaName']] .= wppb_upload_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
				case "avatar":{
					$customFieldsArray[$value['item_metaName']] .= wppb_avatar_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
				case "agreeToTerms":{
					$customFieldsArray[$value['item_metaName']] .= wppb_agree_to_terms_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $current_user, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], $error, $value['item_metaName']);
					break;
				}
			}
		}
	}
	
	$customFieldsArray = apply_filters('wppb_'.$page.'_custom_fields', $customFieldsArray);
	return $customFieldsArray;
}
/* END the function to display the custom fields on all the pages */

/* the function to display the custom fields in the back-end */
function display_profile_extra_fields_in_admin($user){
?>
	<script>	
		function confirmDelete(nonceField, currentUser, customFieldID, customFieldName, returnTo, ajaxurl, what, text) {
		  if (confirm(text)) {
			jQuery.post( ajaxurl ,  { action:"hook_wppb_delete", currentUser:currentUser, customFieldID:customFieldID, customFieldName:customFieldName, what:what, _ajax_nonce:nonceField}, function(response) {
				if(response=="done"){
					window.location=returnTo;
				}else{
					alert(response);
				}
			});			
		  }
		}
	</script>	

	
	<script type="text/javascript">
		var form = document.getElementById('your-profile');
		form.encoding = "multipart/form-data"; //IE5.5
		form.setAttribute('enctype', 'multipart/form-data'); //required for IE6 (is interpreted into "encType")
	</script>
	
	<script type="text/javascript">
		jQuery(function(){

			// Datepicker
			jQuery('.wppb_datepicker').datepicker({
				inline: true,
				changeMonth: true,
				changeYear: true
			});
			
			//hover states on the static widgets
			jQuery('#dialog_link, ul#icons li').hover(
				function() { jQuery(this).addClass('ui-state-hover'); }, 
				function() { jQuery(this).removeClass('ui-state-hover'); }
			);
			
		});
	</script>
<?php
	/* fetch a new custom-fields (only the types) array from the database */
	$wppbFetchArray = get_option('wppb_custom_fields');
	$page = 'back_end';
	$customFieldsArray = array();
	
	?>
	<?php echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.WPPB_SERVER_MAX_UPLOAD_SIZE_BYTE.'" />'; ?> <!-- set the MAX_FILE_SIZE to the server's current max upload size in bytes -->
	<?php
	
	if (count($wppbFetchArray) >= 1){
		foreach($wppbFetchArray as $key => $value){
			switch ($value['item_type']) {
				case "heading":{
					$customFieldsArray[$value['item_metaName']] = wppb_heading_handler($page, $value['item_title'], '', '', $value['item_metaName']);
					break;
				}
				case "input":{
					$customFieldsArray[$value['item_metaName']] = wppb_input_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $user->ID, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], '',$value['item_metaName']);
					break;
				}
				case "hiddenInput":{
					$customFieldsArray[$value['item_metaName']] = wppb_hidden_input_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $user->ID, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], '', $value['item_metaName']);
					break;
				}
				case "checkbox":{
					$customFieldsArray[$value['item_metaName']] = wppb_checkbox_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $user->ID, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], '', $value['item_metaName']);
					break;
				}
				case "radio":{
					$customFieldsArray[$value['item_metaName']] = wppb_radio_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $user->ID, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], '', $value['item_metaName']);
					break;
				}
				case "select":{
					$customFieldsArray[$value['item_metaName']] = wppb_select_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $user->ID, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], '', $value['item_metaName']);
					break;
				}
				case "countrySelect":{
					$customFieldsArray[$value['item_metaName']] = wppb_country_select_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $user->ID, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], '', $value['item_metaName']);
					break;
				}
				case "timeZone":{
					$customFieldsArray[$value['item_metaName']] = wppb_timezone_select_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $user->ID, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], '', $value['item_metaName']);
					break;
				}
				case "datepicker":{
					$customFieldsArray[$value['item_metaName']] = wppb_datepicker_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $user->ID, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], '', $value['item_metaName']);
					break;
				}
				case "textarea":{
					$customFieldsArray[$value['item_metaName']] = wppb_textarea_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $user->ID, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], '', $value['item_metaName']);
					break;
				}
				case "upload":{
					$customFieldsArray[$value['item_metaName']] = wppb_upload_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $user->ID, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], '', $value['item_metaName']);
					break;
				}
				case "avatar":{
					$customFieldsArray[$value['item_metaName']] = wppb_avatar_handler($page, $value['item_title'], $value['id'], $value['item_desc'], $value['item_required'], $value['item_type'], $user->ID, $extraFieldsFilterArray, $extraFieldsErrorHolder, $postData, $value['item_options'], '', $value['item_metaName']);
					break;
				}
			}
		}
	}
	
	$customFieldsArray = apply_filters('wppb_display_admin', $customFieldsArray);
	foreach ($customFieldsArray as $key => $value)
		echo $value;
	
}
/* END the function to display the custom fields in the back-end */


/* the function to save the values from the custom fields in the back-end */
function save_profile_extra_fields_in_admin($user_id){
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	/* fetch a new custom-fields (only the types) array from the database */
	$wppbFetchArray = get_option('wppb_custom_fields');
	
	foreach ( $wppbFetchArray as $key => $value){
		switch ($value['item_type']) {
			case "input":{
				update_user_meta( $user_id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
				break;
			}			
			case "hiddenInput":{
				update_user_meta( $user_id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
				break;
			}
			case "checkbox":{
				$value['item_options'] = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $value['item_options']);
	
				$checkboxOption = '';
				$checkboxValue = explode(',', $value['item_options']);
				foreach($checkboxValue as $thisValue){
					$thisValue = str_replace(' ', '#@space@#', $thisValue); //we need to escape the space-codification we sent earlier in the post
					if (isset($_POST[$thisValue.$value['id']])){
						$localValue = str_replace('#@space@#', ' ', $_POST[$thisValue.$value['id']]);
						$checkboxOption = $checkboxOption.$localValue.',';
					}
				}
				
				update_user_meta( $user_id, $value['item_metaName'], esc_attr( $checkboxOption ) );
				break;
			}
			case "radio":{
				update_user_meta( $user_id, $value['item_metaName'], $_POST[$value['item_type'].$value['id']]  );
				break;
			}
			case "select":{
				update_user_meta( $user_id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
				break;
			}
			case "countrySelect":{
				update_user_meta( $user_id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
				break;
			}
			case "timeZone":{
				update_user_meta( $user_id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
				break;
			}
			case "datepicker":{
				update_user_meta( $user_id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
				break;
			}
			case "textarea":{
				update_user_meta( $user_id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
				break;
			}
			case "upload":{
					$uploadedfile = $value['item_type'].$value['id'];
					$target_path = "../wp-content/uploads/profile_builder/attachments/";
					
					$target_path = $target_path . 'userID_'.$user_id.'_attachment_'. basename( $_FILES[$uploadedfile]['name']); 	
					
					if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
						$upFile = get_bloginfo('home').'/'.$target_path;
						$upFile = str_replace ( '../' , '' , $upFile );
						update_user_meta( $user_id, $value['item_metaName'], $upFile);
					}
				break;
			}
			case "avatar":{
			
				$uploadedfile = $value['item_type'].$value['id'];
				
				$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
				$target_path_original = $wpUploadPath['basedir'].'/profile_builder/avatars/';
				
				$fileName = $_FILES[$uploadedfile]['name'];
				
				//replace all spaces with an underscore
				$finalFileName = '';
						
				for ($i=0; $i < strlen($fileName); $i++){
					if ($fileName[$i] == "'")
						$finalFileName .= '`';
					elseif ($fileName[$i] == ' ')
						$finalFileName .= '_';
					else $finalFileName .= $fileName[$i];
				}
				
				$fileName = $finalFileName;

				$target_path = $target_path_original . 'userID_'.$user_id.'_originalAvatar_'. $fileName; 	
				
				// when trying to upload file, be sure it's one of the accepted image file-types
				if ( (($_FILES[$uploadedfile]['type'] == 'image/jpeg') || ($_FILES[$uploadedfile]['type'] == 'image/jpg') || ($_FILES[$uploadedfile]['type'] == 'image/png') || ($_FILES[$uploadedfile]['type'] == 'image/bmp') || ($_FILES[$uploadedfile]['type'] == 'image/pjpeg') || ($_FILES[$uploadedfile]['type'] == 'image/x-png')) && (($_FILES[$uploadedfile]['size'] < WPPB_SERVER_MAX_UPLOAD_SIZE_BYTE) && ($_FILES[$uploadedfile]['size'] !=0)) ){
					$wp_filetype = wp_check_filetype(basename( $_FILES[$uploadedfile]['name']), null );
					$attachment = array('post_mime_type' => $wp_filetype['type'],
										'post_title' => $fileName,
										'post_content' => '',
										'post_status' => 'inherit'
										);


					$attach_id = wp_insert_attachment( $attachment, $target_path);
			
					$upFile = image_downsize( $attach_id, 'thumbnail' );
					$upFile = $upFile[0];
					
					//if file upload succeded			
					if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
						update_user_meta( $user_id, $value['item_metaName'], $upFile);
						update_user_meta( $user_id, 'resized_avatar_'.$value['id'], '');
					}
				}
				
			break;
			}
			
		}
	}
}
/* END the function to display the custom fields in the back-end */