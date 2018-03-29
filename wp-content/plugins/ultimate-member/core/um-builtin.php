<?php

class UM_Builtin {

	public $predefined_fields = array();

	function __construct() { 

		add_action('init',  array(&$this, 'set_core_fields'), 1);
		
		add_action('init',  array(&$this, 'set_predefined_fields'), 1);
		
		add_action('init',  array(&$this, 'set_custom_fields'), 1);
		
		$this->saved_fields = get_option('um_fields');

	}
	
	/***
	***	@regular or multi-select/options
	***/
	function is_dropdown_field( $field, $attrs ) {
		
		if ( isset( $attrs['options'] ) )
			return true;
		
		$fields = $this->all_user_fields;
		
		if ( isset($fields[$field]['options']) )
			return true;
		
		return false;
	}
	
	/***
	***	@get a field
	***/
	function get_a_field( $field ) {
		$fields = $this->all_user_fields;
		if ( isset( $fields[$field] ) ) {
			return $fields[$field];
		}
		return '';
	}
	
	/***
	***	@get specific fields
	***/
	function get_specific_fields( $fields ) {
		$fields = explode(',', $fields);
		$array=array();
		foreach ($fields as $field ) {
			if ( isset( $this->predefined_fields[$field] ) ) {
				$array[$field] = $this->predefined_fields[$field];
			}
		}
		return $array;
	}
	
	/***
	***	@get specific field
	***/
	function get_specific_field( $fields ) {
		$fields = explode(',', $fields);
		$array=array();
		foreach ($fields as $field ) {
			if ( isset( $this->predefined_fields[$field] ) ) {
				$array = $this->predefined_fields[$field];
			} else if ( isset( $this->saved_fields[$field] ) ) {
				$array = $this->saved_fields[$field];
			}
		}
		return $array;
	}
	
	/***
	***	@Checks for a unique field error
	***/
	function unique_field_err( $key ){
		global $ultimatemember;
		if ( empty( $key ) ) return 'Please provide a meta key';
		if ( isset( $this->core_fields[ $key ] ) ) return __('Your meta key is a reserved core field and cannot be used','ultimate-member');
		if ( isset( $this->predefined_fields[ $key ] ) ) return __('Your meta key is a predefined reserved key and cannot be used','ultimate-member');
		if ( isset( $this->saved_fields[ $key ] ) ) return __('Your meta key already exists in your fields list','ultimate-member');
		if ( !$ultimatemember->validation->safe_string( $key ) ) return __('Your meta key contains illegal characters. Please correct it.','ultimate-member');
		return 0;
	}
	
	/***
	***	@check date range errors (start date)
	***/
	function date_range_start_err( $date ) {
		global $ultimatemember;
		if ( empty( $date ) ) return __('Please provide a date range beginning','ultimate-member');
		if ( !$ultimatemember->validation->validate_date( $date ) ) return __('Please enter a valid start date in the date range','ultimate-member');
		return 0;
	}
	
	/***
	***	@check date range errors (end date)
	***/
	function date_range_end_err( $date, $start_date ) {
		global $ultimatemember;
		if ( empty( $date ) ) return __('Please provide a date range end','ultimate-member');
		if ( !$ultimatemember->validation->validate_date( $date ) ) return __('Please enter a valid end date in the date range','ultimate-member');
		if ( strtotime( $date ) <= strtotime( $start_date ) ) return __('The end of date range must be greater than the start of date range','ultimate-member');
		return 0;
	}
	
	/***
	***	@Get a core field attrs
	***/
	function get_core_field_attrs( $type ) {
		return ( isset( $this->core_fields[$type] ) ) ? $this->core_fields[$type] : array('');
	}
	
	/***
	***	@Core Fields
	***/
	function set_core_fields(){
	
		$this->core_fields = array(
		
			'row' => array(
				'name' => 'Row',
				'in_fields' => false,
				'form_only' => true,
				'conditional_support' => 0,
				'icon' => 'um-faicon-pencil',
				'col1' => array('_id','_background','_text_color','_padding','_margin','_border','_borderradius','_borderstyle','_bordercolor'),
				'col2' => array('_heading','_heading_text','_heading_background_color','_heading_text_color','_icon','_icon_color','_css_class'),
			),
			
			'text' => array(
				'name' => 'Text Box',
				'col1' => array('_title','_metakey','_help','_default','_min_chars','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_validate','_custom_validate','_max_chars'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
			),
			
			'number' => array(
				'name' => __('Number','ultimate-member'),
				'col1' => array('_title','_metakey','_help','_default','_min','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_validate','_custom_validate','_max'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
			),
			
			'textarea' => array(
				'name' => 'Textarea',
				'col1' => array('_title','_metakey','_help','_height','_max_chars','_max_words','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_default','_html'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
			),
			
			'select' => array(
				'name' => 'Dropdown',
				'col1' => array('_title','_metakey','_help','_default','_options','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_custom_dropdown_options_source','_parent_dropdown_relationship'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required', 
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
					'_options' => array(
						'mode' => 'required',
						'error' => __('You have not added any choices yet.','ultimate-member')
					),
				)
			),
			
			'multiselect' => array(
				'name' => 'Multi-Select',
				'col1' => array('_title','_metakey','_help','_default','_options','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_min_selections','_max_selections','_custom_dropdown_options_source'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
					'_options' => array(
						'mode' => 'required',
						'error' => __('You have not added any choices yet.','ultimate-member')
					),
				)
			),
			
			'radio' => array(
				'name' => 'Radio',
				'col1' => array('_title','_metakey','_help','_default','_options','_visibility'),
				'col2' => array('_label','_public','_roles'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
					'_options' => array(
						'mode' => 'required',
						'error' => __('You have not added any choices yet.','ultimate-member')
					),
				)
			),
			
			'checkbox' => array(
				'name' => 'Checkbox',
				'col1' => array('_title','_metakey','_help','_default','_options','_visibility'),
				'col2' => array('_label','_public','_roles','_max_selections'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
					'_options' => array(
						'mode' => 'required',
						'error' => __('You have not added any choices yet.','ultimate-member')
					),
				)
			),
			
			'url' => array(
				'name' => 'URL',
				'col1' => array('_title','_metakey','_help','_default','_url_text','_visibility'),
				'col2' => array('_label','_placeholder','_url_target','_url_rel','_public','_roles','_validate','_custom_validate'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
			),
			
			'password' => array(
				'name' => 'Password',
				'col1' => array('_title','_metakey','_help','_min_chars','_max_chars','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_force_good_pass','_force_confirm_pass'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
			),
			
			'image' => array(
				'name' => 'Image Upload',
				'col1' => array('_title','_metakey','_help','_allowed_types','_max_size','_crop','_visibility'),
				'col2' => array('_label','_public','_roles','_upload_text','_upload_help_text','_button_text'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
					'_max_size' => array(
						'mode' => 'numeric',
						'error' => __('Please enter a valid size','ultimate-member')
					),
				)
			),
			
			'file' => array(
				'name' => 'File Upload',
				'col1' => array('_title','_metakey','_help','_allowed_types','_max_size','_visibility'),
				'col2' => array('_label','_public','_roles','_upload_text','_upload_help_text','_button_text'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
					'_max_size' => array(
						'mode' => 'numeric',
						'error' => __('Please enter a valid size','ultimate-member')
					),
				)
			),
			
			'date' => array(
				'name' => 'Date Picker',
				'col1' => array('_title','_metakey','_help','_range','_years','_years_x','_range_start','_range_end','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_format','_pretty_format','_disabled_weekdays'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
					'_years' => array(
						'mode' => 'numeric',
						'error' => __('Number of years is not valid','ultimate-member')
					),
					'_range_start' => array(
						'mode' => 'range-start',
					),
					'_range_end' => array(
						'mode' => 'range-end',
					),
				)
			),
			
			'time' => array(
				'name' => 'Time Picker',
				'col1' => array('_title','_metakey','_help','_format','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_intervals'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
			),
			
			'rating' => array(
				'name' => 'Rating',
				'col1' => array('_title','_metakey','_help','_visibility'),
				'col2' => array('_label','_public','_roles','_number','_default'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
			),
			
			'block' => array(
				'name' => 'Content Block',
				'col1' => array('_title','_visibility'),
				'col2' => array('_public','_roles'),
				'col_full' => array('_content'),
				'mce_content' => true,
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
				)
			),
			
			'shortcode' => array(
				'name' => 'Shortcode',
				'col1' => array('_title','_visibility'),
				'col2' => array('_public','_roles'),
				'col_full' => array('_content'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_content' => array(
						'mode' => 'required',
						'error' => __('You must add a shortcode to the content area','ultimate-member')
					),
				)
			),
			
			'spacing' => array(
				'name' => 'Spacing',
				'col1' => array('_title','_visibility'),
				'col2' => array('_spacing'),
				'form_only' => true,
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
				)
			),
			
			'divider' => array(
				'name' => 'Divider',
				'col1' => array('_title','_width','_divider_text','_visibility'),
				'col2' => array('_style','_color'),
				'form_only' => true,
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
				)
			),
			
			'googlemap' => array(
				'name' => 'Google Map',
				'col1' => array('_title','_metakey','_help','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_validate','_custom_validate'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
			),
			
			'youtube_video' => array(
				'name' => 'YouTube Video',
				'col1' => array('_title','_metakey','_help','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_validate','_custom_validate'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
			),
			
			'vimeo_video' => array(
				'name' => 'Vimeo Video',
				'col1' => array('_title','_metakey','_help','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_validate','_custom_validate'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
			),
			
			'soundcloud_track' => array(
				'name' => 'SoundCloud Track',
				'col1' => array('_title','_metakey','_help','_visibility'),
				'col2' => array('_label','_placeholder','_public','_roles','_validate','_custom_validate'),
				'col3' => array('_required','_editable','_icon'),
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => __('You must provide a title','ultimate-member')
					),
					'_metakey' => array(
						'mode' => 'unique',
					),
				)
			),
			
			/*'group' => array(
				'name' => 'Field Group',
				'col1' => array('_title','_max_entries'),
				'col2' => array('_label','_public','_roles'),
				'form_only' => true,
				'validate' => array(
					'_title' => array(
						'mode' => 'required',
						'error' => 'You must provide a title'
					),
					'_label' => array(
						'mode' => 'required',
						'error' => 'You must provide a label'
					),
				)
			),*/
		
		);
		
		$this->core_fields = apply_filters('um_core_fields_hook', $this->core_fields );
	
	}
	
	/***
	***	@Predefined Fields
	***/
	function set_predefined_fields(){
	
		global $ultimatemember;
		
		if ( !isset( $ultimatemember->query ) || ! method_exists( $ultimatemember->query, 'get_roles' ) ) {
			return;
		} else {
			//die('Method loaded!');
		}
		
		$um_roles = $ultimatemember->query->get_roles( false, array('admin') );
		
		$profile_privacy = apply_filters('um_profile_privacy_options', array( __('Everyone','ultimate-member'), __('Only me','ultimate-member') ) );
		
		$this->predefined_fields = array(
		
			'user_login' => array(
				'title' => __('Username','ultimate-member'),
				'metakey' => 'user_login',
				'type' => 'text',
				'label' => __('Username','ultimate-member'),
				'required' => 1,
				'public' => 1,
				'editable' => 0,
				'validate' => 'unique_username',
				'min_chars' => 3,
				'max_chars' => 24
			),
			
			'username' => array(
				'title' => __('Username or E-mail','ultimate-member'),
				'metakey' => 'username',
				'type' => 'text',
				'label' => __('Username or E-mail','ultimate-member'),
				'required' => 1,
				'public' => 1,
				'editable' => 0,
				'validate' => 'unique_username_or_email',
			),
			
			'user_password' => array(
				'title' => __('Password','ultimate-member'),
				'metakey' => 'user_password',
				'type' => 'password',
				'label' => __('Password','ultimate-member'),
				'required' => 1,
				'public' => 1,
				'editable' => 1,
				'min_chars' => 8,
				'max_chars' => 30,
				'force_good_pass' => 1,
				'force_confirm_pass' => 1,
			),
			
			'first_name' => array(
				'title' => __('First Name','ultimate-member'),
				'metakey' => 'first_name',
				'type' => 'text',
				'label' => __('First Name','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
			),
			
			'last_name' => array(
				'title' => __('Last Name','ultimate-member'),
				'metakey' => 'last_name',
				'type' => 'text',
				'label' => __('Last Name','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
			),
			
			'nickname' => array(
				'title' => __('Nickname','ultimate-member'),
				'metakey' => 'nickname',
				'type' => 'text',
				'label' => __('Nickname','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
			),

			'user_registered' => array(
				'title' => __('Registration Date','ultimate-member'),
				'metakey' => 'user_registered',
				'type' => 'text',
				'label' => __('Registration Date','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'edit_forbidden' => 1,
			),
			
			'last_login' => array(
				'title' => __('Last Login','ultimate-member'),
				'metakey' => '_um_last_login',
				'type' => 'text',
				'label' => __('Last Login','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'edit_forbidden' => 1,
			),
			
			'user_email' => array(
				'title' => __('E-mail Address','ultimate-member'),
				'metakey' => 'user_email',
				'type' => 'text',
				'label' => __('E-mail Address','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'validate' => 'unique_email',
				'autocomplete' => 'off'
			),

			'secondary_user_email' => array(
				'title' => __('Secondary E-mail Address','ultimate-member'),
				'metakey' => 'secondary_user_email',
				'type' => 'text',
				'label' => __('Secondary E-mail Address','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'validate' => 'unique_email',
				'autocomplete' => 'off'
			),
			
			'description' => array(
				'title' => __('Biography','ultimate-member'),
				'metakey' => 'description',
				'type' => 'textarea',
				'label' => __('Biography','ultimate-member'),
				'html' => 0,
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'max_words' => 40,
				'placeholder' => 'Enter a bit about yourself...',
			),
			
			'birth_date' => array(
				'title' => __('Birth Date','ultimate-member'),
				'metakey' => 'birth_date',
				'type' => 'date',
				'label' => __('Birth Date','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'pretty_format' => 1,
				'years' => 115,
				'years_x' => 'past',
				'icon' => 'um-faicon-calendar'
			),
			
			'gender' => array(
				'title' => __('Gender','ultimate-member'),
				'metakey' => 'gender',
				'type' => 'radio',
				'label' => __('Gender','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'options' => array( __('Male','ultimate-member'), __('Female','ultimate-member') )
			),
			
			'country' => array(
				'title' => __('Country','ultimate-member'),
				'metakey' => 'country',
				'type' => 'select',
				'label' => __('Country','ultimate-member'),
				'placeholder' => __('Choose a Country','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'options' => $this->get('countries')
			),
			
			'facebook' => array(
				'title' => __('Facebook','ultimate-member'),
				'metakey' => 'facebook',
				'type' => 'url',
				'label' => __('Facebook','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'url_target' => '_blank',
				'url_rel' => 'nofollow',
				'icon' => 'um-faicon-facebook',
				'validate' => 'facebook_url',
				'url_text' => 'Facebook',
				'advanced' => 'social',
				'color' => '#3B5999',
				'match' => 'https://facebook.com/',
			),
			
			'twitter' => array(
				'title' => __('Twitter','ultimate-member'),
				'metakey' => 'twitter',
				'type' => 'url',
				'label' => __('Twitter','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'url_target' => '_blank',
				'url_rel' => 'nofollow',
				'icon' => 'um-faicon-twitter',
				'validate' => 'twitter_url',
				'url_text' => 'Twitter',
				'advanced' => 'social',
				'color' => '#4099FF',
				'match' => 'https://twitter.com/',
			),
			
			'linkedin' => array(
				'title' => __('LinkedIn','ultimate-member'),
				'metakey' => 'linkedin',
				'type' => 'url',
				'label' => __('LinkedIn','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'url_target' => '_blank',
				'url_rel' => 'nofollow',
				'icon' => 'um-faicon-linkedin',
				'validate' => 'linkedin_url',
				'url_text' => 'LinkedIn',
				'advanced' => 'social',
				'color' => '#0976b4',
				'match' => 'https://linkedin.com/in/',
			),
			
			'googleplus' => array(
				'title' => __('Google+','ultimate-member'),
				'metakey' => 'googleplus',
				'type' => 'url',
				'label' => __('Google+','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'url_target' => '_blank',
				'url_rel' => 'nofollow',
				'icon' => 'um-faicon-google-plus',
				'validate' => 'google_url',
				'url_text' => 'Google+',
				'advanced' => 'social',
				'color' => '#dd4b39',
				'match' => 'https://google.com/+',
			),
			
			'instagram' => array(
				'title' => __('Instagram','ultimate-member'),
				'metakey' => 'instagram',
				'type' => 'url',
				'label' => __('Instagram','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'url_target' => '_blank',
				'url_rel' => 'nofollow',
				'icon' => 'um-faicon-instagram',
				'validate' => 'instagram_url',
				'url_text' => 'Instagram',
				'advanced' => 'social',
				'color' => '#3f729b',
				'match' => 'https://instagram.com/',
			),
			
			'skype' => array(
				'title' => __('Skype ID','ultimate-member'),
				'metakey' => 'skype',
				'type' => 'url',
				'label' => __('Skype ID','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'url_target' => '_blank',
				'url_rel' => 'nofollow',
				'icon' => 'um-faicon-skype',
				'validate' => 'skype',
				'url_text' => 'Skype',
			),
			
			'youtube' => array(
				'title' => __('YouTube','ultimate-member'),
				'metakey' => 'youtube',
				'type' => 'url',
				'label' => __('YouTube','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'url_target' => '_blank',
				'url_rel' => 'nofollow',
				'icon' => 'um-faicon-youtube',
				'validate' => 'youtube_url',
				'url_text' => 'YouTube',
				'advanced' => 'social',
				'color' => '#e52d27',
				'match' => 'https://youtube.com/',
			),
			
			'soundcloud' => array(
				'title' => __('SoundCloud','ultimate-member'),
				'metakey' => 'soundcloud',
				'type' => 'url',
				'label' => __('SoundCloud','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'url_target' => '_blank',
				'url_rel' => 'nofollow',
				'icon' => 'um-faicon-soundcloud',
				'validate' => 'soundcloud_url',
				'url_text' => 'SoundCloud',
				'advanced' => 'social',
				'color' => '#f50',
				'match' => 'https://soundcloud.com/',
			),

			'vk' => array(
				'title' => __('VKontakte','ultimate-member'),
				'metakey' => 'vkontakte',
				'type' => 'url',
				'label' => __('VKontakte','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'url_target' => '_blank',
				'url_rel' => 'nofollow',
				'icon' => 'um-faicon-vk',
				'validate' => 'vk_url',
				'url_text' => 'VKontakte',
				'advanced' => 'social',
				'color' => '#2B587A',
				'match' => 'https://vk.com/',
			),
			
			'role_select' => array(
				'title' => __('Roles (Dropdown)','ultimate-member'),
				'metakey' => 'role_select',
				'type' => 'select',
				'label' => __('Account Type','ultimate-member'),
				'placeholder' => 'Choose account type',
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'options' => $um_roles,
			),
			
			'role_radio' => array(
				'title' => __('Roles (Radio)','ultimate-member'),
				'metakey' => 'role_radio',
				'type' => 'radio',
				'label' => __('Account Type','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'options' => $um_roles,
			),
			
			'languages' => array(
				'title' => __('Languages','ultimate-member'),
				'metakey' => 'languages',
				'type' => 'multiselect',
				'label' => __('Languages Spoken','ultimate-member'),
				'placeholder' => __('Select languages','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'options' => $this->get('languages'),
			),
			
			'phone_number' => array(
				'title' => __('Phone Number','ultimate-member'),
				'metakey' => 'phone_number',
				'type' => 'text',
				'label' => __('Phone Number','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'validate' => 'phone_number',
				'icon' => 'um-faicon-phone',
			),
			
			'mobile_number' => array(
				'title' => __('Mobile Number','ultimate-member'),
				'metakey' => 'mobile_number',
				'type' => 'text',
				'label' => __('Mobile Number','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'validate' => 'phone_number',
				'icon' => 'um-faicon-mobile',
			),
			
			// private use ( not public list )
		
			'profile_photo' => array(
				'title' => __('Profile Photo','ultimate-member'),
				'metakey' => 'profile_photo',
				'type' => 'image',
				'label' => __('Change your profile photo','ultimate-member'),
				'upload_text' => __('Upload your photo here','ultimate-member'),
				'icon' => 'um-faicon-camera',
				'crop' => 1,
				'max_size' => ( um_get_option('profile_photo_max_size') ) ? um_get_option('profile_photo_max_size') : 999999999,
				'min_width' => str_replace('px','',um_get_option('profile_photosize')),
				'min_height' => str_replace('px','',um_get_option('profile_photosize')),
				'private_use' => true,
			),
			
			'cover_photo' => array(
				'title' => __('Cover Photo','ultimate-member'),
				'metakey' => 'cover_photo',
				'type' => 'image',
				'label' => __('Change your cover photo','ultimate-member'),
				'upload_text' => __('Upload profile cover here','ultimate-member'),
				'icon' => 'um-faicon-picture-o',
				'crop' => 2,
				'max_size' => ( um_get_option('cover_photo_max_size') ) ? um_get_option('cover_photo_max_size') : 999999999,
				'modal_size' => 'large',
				'ratio' => str_replace(':1','',um_get_option('profile_cover_ratio')),
				'min_width' => um_get_option('cover_min_width'),
				'private_use' => true,
			),
			
			'password_reset_text' => array(
				'title' => __('Password Reset','ultimate-member'),
				'type' => 'block',
				'content' => '<div style="text-align:center">' . __('To reset your password, please enter your email address or username below','ultimate-member'). '</div>',
				'private_use' => true,
			),
			
			'username_b' => array(
				'title' => __('Username or E-mail','ultimate-member'),
				'metakey' => 'username_b',
				'type' => 'text',
				'placeholder' => __('Enter your username or email','ultimate-member'),
				'required' => 1,
				'public' => 1,
				'editable' => 0,
				'private_use' => true,
			),
			
			// account page use ( not public )
			
			'profile_privacy' => array(
				'title' => __('Profile Privacy','ultimate-member'),
				'metakey' => 'profile_privacy',
				'type' => 'select',
				'label' => __('Profile Privacy','ultimate-member'),
				'help' => __('Who can see your public profile?','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'default' => __('Everyone','ultimate-member'),
				'options' => $profile_privacy,
				'allowclear' => 0,
				'account_only' => true,
				'required_perm' => 'can_make_private_profile',
			),
			
			'hide_in_members' => array(
				'title' => __('Hide my profile from directory','ultimate-member'),
				'metakey' => 'hide_in_members',
				'type' => 'radio',
				'label' => __('Hide my profile from directory','ultimate-member'),
				'help' => __('Here you can hide yourself from appearing in public directory','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'default' => __('No','ultimate-member'),
				'options' => array( __('No','ultimate-member'), __('Yes','ultimate-member') ),
				'account_only' => true,
				'required_opt' => array( 'members_page', 1 ),
			),
			
			'delete_account' => array(
				'title' => __('Delete Account','ultimate-member'),
				'metakey' => 'delete_account',
				'type' => 'radio',
				'label' => __('Delete Account','ultimate-member'),
				'help' => __('If you confirm, everything related to your profile will be deleted permanently from the site','ultimate-member'),
				'required' => 0,
				'public' => 1,
				'editable' => 1,
				'default' => __('No','ultimate-member'),
				'options' => array( __('Yes','ultimate-member') , __('No','ultimate-member') ),
				'account_only' => true,
			),
			
			'single_user_password' => array(
				'title' => __('Password','ultimate-member'),
				'metakey' => 'single_user_password',
				'type' => 'password',
				'label' => __('Password','ultimate-member'),
				'required' => 1,
				'public' => 1,
				'editable' => 1,
				'account_only' => true,
			),
			
		);
		
		$this->predefined_fields = apply_filters('um_predefined_fields_hook', $this->predefined_fields );
	
	}
	
	/***
	***	@Custom Fields
	***/
	function set_custom_fields(){

		if ( is_array( $this->saved_fields ) ) {
			
			$this->custom_fields = $this->saved_fields;
		
		} else {
			
			$this->custom_fields = '';
		
		}

		$custom = $this->custom_fields;
		$predefined = $this->predefined_fields;

		if ( is_array( $custom ) ){
			$this->all_user_fields = array_merge( $predefined, $custom );
		} else {
			$this->all_user_fields = $predefined;
		}

	}
	
	/***
	***	@may be used to show a dropdown, or source for user meta
	***/
	function all_user_fields( $exclude_types = null, $show_all = false ) {
	
		global $ultimatemember;
		
		$fields_without_metakey = array('block','shortcode','spacing','divider','group');
		remove_filter('um_fields_without_metakey', 'um_user_tags_requires_no_metakey');
		$fields_without_metakey = apply_filters('um_fields_without_metakey', $fields_without_metakey );
		
		if ( !$show_all ) {
			$this->fields_dropdown = array('image','file','password','rating');
			$this->fields_dropdown = array_merge( $this->fields_dropdown, $fields_without_metakey );
		} else {
			$this->fields_dropdown = $fields_without_metakey;
		}

		$custom = $this->custom_fields;
		$predefined = $this->predefined_fields;
		
		if ( $exclude_types ) {
			$exclude_types = explode(',', $exclude_types);
		}
		
		$all = array( 0 => '' );
		
		if ( is_array( $custom ) ){
		$all = $all + array_merge( $predefined, $custom );
		} else {
			$all = $all + $predefined;
		}
		
		foreach( $all as $k => $arr ) {
			
			if ( $k == 0 ) {
				unset($all[$k]);
			}
			
			if ( isset( $arr['title'] ) ){
				$all[$k]['title'] = stripslashes( $arr['title'] );
			}
			
			if ( $exclude_types && isset( $arr['type'] ) && in_array( $arr['type'], $exclude_types ) ) {
				unset( $all[$k] );
			}
			if ( isset( $arr['account_only'] ) || isset( $arr['private_use'] ) ) {
				if ( !$show_all ) {
					unset( $all[$k] );
				}
			}
			if ( isset( $arr['type'] ) && in_array( $arr['type'], $this->fields_dropdown ) ) {
				unset( $all[$k] );
			}
		}
		
		$all = $ultimatemember->fields->array_sort_by_column( $all, 'title');
		
		$all = array( 0 => '') + $all;

		return $all;
	}
	
	/***
	***	@Possible validation types for fields
	***/
	function validation_types(){
	
		$array[0] = __('None','ultimate-member');
		$array['alphabetic'] = __('Alphabetic value only','ultimate-member');
		$array['alpha_numeric'] = __('Alpha-numeric value','ultimate-member');
		$array['english'] = __('English letters only','ultimate-member');
		$array['facebook_url'] = __('Facebook URL','ultimate-member');
		$array['google_url'] = __('Google+ URL','ultimate-member');
		$array['instagram_url'] = __('Instagram URL','ultimate-member');
		$array['linkedin_url'] = __('LinkedIn URL','ultimate-member');
		$array['vk_url'] = __('VKontakte URL','ultimate-member');
		$array['lowercase'] = __('Lowercase only','ultimate-member');
		$array['numeric'] = __('Numeric value only','ultimate-member');
		$array['phone_number'] = __('Phone Number','ultimate-member');
		$array['skype'] = __('Skype ID','ultimate-member');
		$array['soundcloud'] = __('SoundCloud Profile','ultimate-member');
		$array['twitter_url'] = __('Twitter URL','ultimate-member');
		$array['unique_email'] = __('Unique E-mail','ultimate-member');
		$array['unique_value'] = __('Unique Metakey value','ultimate-member');
		$array['unique_username'] = __('Unique Username','ultimate-member');
		$array['unique_username_or_email'] = __('Unique Username/E-mail','ultimate-member');
		$array['url'] = __('Website URL','ultimate-member');
		$array['youtube_url'] = __('YouTube Profile','ultimate-member');
		$array['custom'] = __('Custom Validation','ultimate-member');
		
		$array = apply_filters('um_admin_field_validation_hook', $array );
		return $array;
	}
	
	/***
	***	@Get predefined options
	***/
	function get( $data ){
		switch($data) {
		
			case 'languages':
				$array = array(
							"aa" => __("Afar",'ultimate-member'),
							 "ab" => __("Abkhazian",'ultimate-member'),
							 "ae" => __("Avestan",'ultimate-member'),
							 "af" => __("Afrikaans",'ultimate-member'),
							 "ak" => __("Akan",'ultimate-member'),
							 "am" => __("Amharic",'ultimate-member'),
							 "an" => __("Aragonese",'ultimate-member'),
							 "ar" => __("Arabic",'ultimate-member'),
							 "as" => __("Assamese",'ultimate-member'),
							 "av" => __("Avaric",'ultimate-member'),
							 "ay" => __("Aymara",'ultimate-member'),
							 "az" => __("Azerbaijani",'ultimate-member'),
							 "ba" => __("Bashkir",'ultimate-member'),
							 "be" => __("Belarusian",'ultimate-member'),
							 "bg" => __("Bulgarian",'ultimate-member'),
							 "bh" => __("Bihari",'ultimate-member'),
							 "bi" => __("Bislama",'ultimate-member'),
							 "bm" => __("Bambara",'ultimate-member'),
							 "bn" => __("Bengali",'ultimate-member'),
							 "bo" => __("Tibetan",'ultimate-member'),
							 "br" => __("Breton",'ultimate-member'),
							 "bs" => __("Bosnian",'ultimate-member'),
							 "ca" => __("Catalan",'ultimate-member'),
							 "ce" => __("Chechen",'ultimate-member'),
							 "ch" => __("Chamorro",'ultimate-member'),
							 "co" => __("Corsican",'ultimate-member'),
							 "cr" => __("Cree",'ultimate-member'),
							 "cs" => __("Czech",'ultimate-member'),
							 "cu" => __("Church Slavic",'ultimate-member'),
							 "cv" => __("Chuvash",'ultimate-member'),
							 "cy" => __("Welsh",'ultimate-member'),
							 "da" => __("Danish",'ultimate-member'),
							 "de" => __("German",'ultimate-member'),
							 "dv" => __("Divehi",'ultimate-member'),
							 "dz" => __("Dzongkha",'ultimate-member'),
							 "ee" => __("Ewe",'ultimate-member'),
							 "el" => __("Greek",'ultimate-member'),
							 "en" => __("English",'ultimate-member'),
							 "eo" => __("Esperanto",'ultimate-member'),
							 "es" => __("Spanish",'ultimate-member'),
							 "et" => __("Estonian",'ultimate-member'),
							 "eu" => __("Basque",'ultimate-member'),
							 "fa" => __("Persian",'ultimate-member'),
							 "ff" => __("Fulah",'ultimate-member'),
							 "fi" => __("Finnish",'ultimate-member'),
							 "fj" => __("Fijian",'ultimate-member'),
							 "fo" => __("Faroese",'ultimate-member'),
							 "fr" => __("French",'ultimate-member'),
							 "fy" => __("Western Frisian",'ultimate-member'),
							 "ga" => __("Irish",'ultimate-member'),
							 "gd" => __("Scottish Gaelic",'ultimate-member'),
							 "gl" => __("Galician",'ultimate-member'),
							 "gn" => __("Guarani",'ultimate-member'),
							 "gu" => __("Gujarati",'ultimate-member'),
							 "gv" => __("Manx",'ultimate-member'),
							 "ha" => __("Hausa",'ultimate-member'),
							 "he" => __("Hebrew",'ultimate-member'),
							 "hi" => __("Hindi",'ultimate-member'),
							 "ho" => __("Hiri Motu",'ultimate-member'),
							 "hr" => __("Croatian",'ultimate-member'),
							 "ht" => __("Haitian",'ultimate-member'),
							 "hu" => __("Hungarian",'ultimate-member'),
							 "hy" => __("Armenian",'ultimate-member'),
							 "hz" => __("Herero",'ultimate-member'),
							 "ia" => __("Interlingua (International Auxiliary Language Association)",'ultimate-member'),
							 "id" => __("Indonesian",'ultimate-member'),
							 "ie" => __("Interlingue",'ultimate-member'),
							 "ig" => __("Igbo",'ultimate-member'),
							 "ii" => __("Sichuan Yi",'ultimate-member'),
							 "ik" => __("Inupiaq",'ultimate-member'),
							 "io" => __("Ido",'ultimate-member'),
							 "is" => __("Icelandic",'ultimate-member'),
							 "it" => __("Italian",'ultimate-member'),
							 "iu" => __("Inuktitut",'ultimate-member'),
							 "ja" => __("Japanese",'ultimate-member'),
							 "jv" => __("Javanese",'ultimate-member'),
							 "ka" => __("Georgian",'ultimate-member'),
							 "kg" => __("Kongo",'ultimate-member'),
							 "ki" => __("Kikuyu",'ultimate-member'),
							 "kj" => __("Kwanyama",'ultimate-member'),
							 "kk" => __("Kazakh",'ultimate-member'),
							 "kl" => __("Kalaallisut",'ultimate-member'),
							 "km" => __("Khmer",'ultimate-member'),
							 "kn" => __("Kannada",'ultimate-member'),
							 "ko" => __("Korean",'ultimate-member'),
							 "kr" => __("Kanuri",'ultimate-member'),
							 "ks" => __("Kashmiri",'ultimate-member'),
							 "ku" => __("Kurdish",'ultimate-member'),
							 "kv" => __("Komi",'ultimate-member'),
							 "kw" => __("Cornish",'ultimate-member'),
							 "ky" => __("Kirghiz",'ultimate-member'),
							 "la" => __("Latin",'ultimate-member'),
							 "lb" => __("Luxembourgish",'ultimate-member'),
							 "lg" => __("Ganda",'ultimate-member'),
							 "li" => __("Limburgish",'ultimate-member'),
							 "ln" => __("Lingala",'ultimate-member'),
							 "lo" => __("Lao",'ultimate-member'),
							 "lt" => __("Lithuanian",'ultimate-member'),
							 "lu" => __("Luba-Katanga",'ultimate-member'),
							 "lv" => __("Latvian",'ultimate-member'),
							 "mg" => __("Malagasy",'ultimate-member'),
							 "mh" => __("Marshallese",'ultimate-member'),
							 "mi" => __("Maori",'ultimate-member'),
							 "mk" => __("Macedonian",'ultimate-member'),
							 "ml" => __("Malayalam",'ultimate-member'),
							 "mn" => __("Mongolian",'ultimate-member'),
							 "mr" => __("Marathi",'ultimate-member'),
							 "ms" => __("Malay",'ultimate-member'),
							 "mt" => __("Maltese",'ultimate-member'),
							 "my" => __("Burmese",'ultimate-member'),
							 "na" => __("Nauru",'ultimate-member'),
							 "nb" => __("Norwegian Bokmal",'ultimate-member'),
							 "nd" => __("North Ndebele",'ultimate-member'),
							 "ne" => __("Nepali",'ultimate-member'),
							 "ng" => __("Ndonga",'ultimate-member'),
							 "nl" => __("Dutch",'ultimate-member'),
							 "nn" => __("Norwegian Nynorsk",'ultimate-member'),
							 "no" => __("Norwegian",'ultimate-member'),
							 "nr" => __("South Ndebele",'ultimate-member'),
							 "nv" => __("Navajo",'ultimate-member'),
							 "ny" => __("Chichewa",'ultimate-member'),
							 "oc" => __("Occitan",'ultimate-member'),
							 "oj" => __("Ojibwa",'ultimate-member'),
							 "om" => __("Oromo",'ultimate-member'),
							 "or" => __("Oriya",'ultimate-member'),
							 "os" => __("Ossetian",'ultimate-member'),
							 "pa" => __("Panjabi",'ultimate-member'),
							 "pi" => __("Pali",'ultimate-member'),
							 "pl" => __("Polish",'ultimate-member'),
							 "ps" => __("Pashto",'ultimate-member'),
							 "pt" => __("Portuguese",'ultimate-member'),
							 "qu" => __("Quechua",'ultimate-member'),
							 "rm" => __("Raeto-Romance",'ultimate-member'),
							 "rn" => __("Kirundi",'ultimate-member'),
							 "ro" => __("Romanian",'ultimate-member'),
							 "ru" => __("Russian",'ultimate-member'),
							 "rw" => __("Kinyarwanda",'ultimate-member'),
							 "sa" => __("Sanskrit",'ultimate-member'),
							 "sc" => __("Sardinian",'ultimate-member'),
							 "sd" => __("Sindhi",'ultimate-member'),
							 "se" => __("Northern Sami",'ultimate-member'),
							 "sg" => __("Sango",'ultimate-member'),
							 "si" => __("Sinhala",'ultimate-member'),
							 "sk" => __("Slovak",'ultimate-member'),
							 "sl" => __("Slovenian",'ultimate-member'),
							 "sm" => __("Samoan",'ultimate-member'),
							 "sn" => __("Shona",'ultimate-member'),
							 "so" => __("Somali",'ultimate-member'),
							 "sq" => __("Albanian",'ultimate-member'),
							 "sr" => __("Serbian",'ultimate-member'),
							 "ss" => __("Swati",'ultimate-member'),
							 "st" => __("Southern Sotho",'ultimate-member'),
							 "su" => __("Sundanese",'ultimate-member'),
							 "sv" => __("Swedish",'ultimate-member'),
							 "sw" => __("Swahili",'ultimate-member'),
							 "ta" => __("Tamil",'ultimate-member'),
							 "te" => __("Telugu",'ultimate-member'),
							 "tg" => __("Tajik",'ultimate-member'),
							 "th" => __("Thai",'ultimate-member'),
							 "ti" => __("Tigrinya",'ultimate-member'),
							 "tk" => __("Turkmen",'ultimate-member'),
							 "tl" => __("Tagalog",'ultimate-member'),
							 "tn" => __("Tswana",'ultimate-member'),
							 "to" => __("Tonga",'ultimate-member'),
							 "tr" => __("Turkish",'ultimate-member'),
							 "ts" => __("Tsonga",'ultimate-member'),
							 "tt" => __("Tatar",'ultimate-member'),
							 "tw" => __("Twi",'ultimate-member'),
							 "ty" => __("Tahitian",'ultimate-member'),
							 "ug" => __("Uighur",'ultimate-member'),
							 "uk" => __("Ukrainian",'ultimate-member'),
							 "ur" => __("Urdu",'ultimate-member'),
							 "uz" => __("Uzbek",'ultimate-member'),
							 "ve" => __("Venda",'ultimate-member'),
							 "vi" => __("Vietnamese",'ultimate-member'),
							 "vo" => __("Volapuk",'ultimate-member'),
							 "wa" => __("Walloon",'ultimate-member'),
							 "wo" => __("Wolof",'ultimate-member'),
							 "xh" => __("Xhosa",'ultimate-member'),
							 "yi" => __("Yiddish",'ultimate-member'),
							 "yo" => __("Yoruba",'ultimate-member'),
							 "za" => __("Zhuang",'ultimate-member'),
							 "zh" => __("Chinese",'ultimate-member'),
							 "zu" => __("Zulu",'ultimate-member')
			);
			break;

			case 'countries':
				$array = array (
							'AF' => __('Afghanistan','ultimate-member'),
							'AX' => __('Åland Islands','ultimate-member'),
							'AL' => __('Albania','ultimate-member'),
							'DZ' => __('Algeria','ultimate-member'),
							'AS' => __('American Samoa','ultimate-member'),
							'AD' => __('Andorra','ultimate-member'),
							'AO' => __('Angola','ultimate-member'),
							'AI' => __('Anguilla','ultimate-member'),
							'AQ' => __('Antarctica','ultimate-member'),
							'AG' => __('Antigua and Barbuda','ultimate-member'),
							'AR' => __('Argentina','ultimate-member'),
							'AM' => __('Armenia','ultimate-member'),
							'AW' => __('Aruba','ultimate-member'),
							'AU' => __('Australia','ultimate-member'),
							'AT' => __('Austria','ultimate-member'),
							'AZ' => __('Azerbaijan','ultimate-member'),
							'BS' => __('Bahamas','ultimate-member'),
							'BH' => __('Bahrain','ultimate-member'),
							'BD' => __('Bangladesh','ultimate-member'),
							'BB' => __('Barbados','ultimate-member'),
							'BY' => __('Belarus','ultimate-member'),
							'BE' => __('Belgium','ultimate-member'),
							'BZ' => __('Belize','ultimate-member'),
							'BJ' => __('Benin','ultimate-member'),
							'BM' => __('Bermuda','ultimate-member'),
							'BT' => __('Bhutan','ultimate-member'),
							'BO' => __('Bolivia, Plurinational State of','ultimate-member'),
							'BA' => __('Bosnia and Herzegovina','ultimate-member'),
							'BW' => __('Botswana','ultimate-member'),
							'BV' => __('Bouvet Island','ultimate-member'),
							'BR' => __('Brazil','ultimate-member'),
							'IO' => __('British Indian Ocean Territory','ultimate-member'),
							'BN' => __('Brunei Darussalam','ultimate-member'),
							'BG' => __('Bulgaria','ultimate-member'),
							'BF' => __('Burkina Faso','ultimate-member'),
							'BI' => __('Burundi','ultimate-member'),
							'KH' => __('Cambodia','ultimate-member'),
							'CM' => __('Cameroon','ultimate-member'),
							'CA' => __('Canada','ultimate-member'),
							'CV' => __('Cape Verde','ultimate-member'),
							'KY' => __('Cayman Islands','ultimate-member'),
							'CF' => __('Central African Republic','ultimate-member'),
							'TD' => __('Chad','ultimate-member'),
							'CL' => __('Chile','ultimate-member'),
							'CN' => __('China','ultimate-member'),
							'CX' => __('Christmas Island','ultimate-member'),
							'CC' => __('Cocos (Keeling) Islands','ultimate-member'),
							'CO' => __('Colombia','ultimate-member'),
							'KM' => __('Comoros','ultimate-member'),
							'CG' => __('Congo','ultimate-member'),
							'CD' => __('Congo, the Democratic Republic of the','ultimate-member'),
							'CK' => __('Cook Islands','ultimate-member'),
							'CR' => __('Costa Rica','ultimate-member'),
							'CI' => __("Côte d'Ivoire",'ultimate-member'),
							'HR' => __('Croatia','ultimate-member'),
							'CU' => __('Cuba','ultimate-member'),
							'CY' => __('Cyprus','ultimate-member'),
							'CZ' => __('Czech Republic','ultimate-member'),
							'DK' => __('Denmark','ultimate-member'),
							'DJ' => __('Djibouti','ultimate-member'),
							'DM' => __('Dominica','ultimate-member'),
							'DO' => __('Dominican Republic','ultimate-member'),
							'EC' => __('Ecuador','ultimate-member'),
							'EG' => __('Egypt','ultimate-member'),
							'SV' => __('El Salvador','ultimate-member'),
							'GQ' => __('Equatorial Guinea','ultimate-member'),
							'ER' => __('Eritrea','ultimate-member'),
							'EE' => __('Estonia','ultimate-member'),
							'ET' => __('Ethiopia','ultimate-member'),
							'FK' => __('Falkland Islands (Malvinas)','ultimate-member'),
							'FO' => __('Faroe Islands','ultimate-member'),
							'FJ' => __('Fiji','ultimate-member'),
							'FI' => __('Finland','ultimate-member'),
							'FR' => __('France','ultimate-member'),
							'GF' => __('French Guiana','ultimate-member'),
							'PF' => __('French Polynesia','ultimate-member'),
							'TF' => __('French Southern Territories','ultimate-member'),
							'GA' => __('Gabon','ultimate-member'),
							'GM' => __('Gambia','ultimate-member'),
							'GE' => __('Georgia','ultimate-member'),
							'DE' => __('Germany','ultimate-member'),
							'GH' => __('Ghana','ultimate-member'),
							'GI' => __('Gibraltar','ultimate-member'),
							'GR' => __('Greece','ultimate-member'),
							'GL' => __('Greenland','ultimate-member'),
							'GD' => __('Grenada','ultimate-member'),
							'GP' => __('Guadeloupe','ultimate-member'),
							'GU' => __('Guam','ultimate-member'),
							'GT' => __('Guatemala','ultimate-member'),
							'GG' => __('Guernsey','ultimate-member'),
							'GN' => __('Guinea','ultimate-member'),
							'GW' => __('Guinea-Bissau','ultimate-member'),
							'GY' => __('Guyana','ultimate-member'),
							'HT' => __('Haiti','ultimate-member'),
							'HM' => __('Heard Island and McDonald Islands','ultimate-member'),
							'VA' => __('Holy See (Vatican City State)','ultimate-member'),
							'HN' => __('Honduras','ultimate-member'),
							'HK' => __('Hong Kong','ultimate-member'),
							'HU' => __('Hungary','ultimate-member'),
							'IS' => __('Iceland','ultimate-member'),
							'IN' => __('India','ultimate-member'),
							'ID' => __('Indonesia','ultimate-member'),
							'IR' => __('Iran, Islamic Republic of','ultimate-member'),
							'IQ' => __('Iraq','ultimate-member'),
							'IE' => __('Ireland','ultimate-member'),
							'IM' => __('Isle of Man','ultimate-member'),
							'IL' => __('Israel','ultimate-member'),
							'IT' => __('Italy','ultimate-member'),
							'JM' => __('Jamaica','ultimate-member'),
							'JP' => __('Japan','ultimate-member'),
							'JE' => __('Jersey','ultimate-member'),
							'JO' => __('Jordan','ultimate-member'),
							'KZ' => __('Kazakhstan','ultimate-member'),
							'KE' => __('Kenya','ultimate-member'),
							'KI' => __('Kiribati','ultimate-member'),
							'KP' => __("Korea, Democratic People's Republic of",'ultimate-member'),
							'KR' => __('Korea, Republic of','ultimate-member'),
							'KW' => __('Kuwait','ultimate-member'),
							'KG' => __('Kyrgyzstan','ultimate-member'),
							'LA' => __("Lao People's Democratic Republic",'ultimate-member'),
							'LV' => __('Latvia','ultimate-member'),
							'LB' => __('Lebanon','ultimate-member'),
							'LS' => __('Lesotho','ultimate-member'),
							'LR' => __('Liberia','ultimate-member'),
							'LY' => __('Libyan Arab Jamahiriya','ultimate-member'),
							'LI' => __('Liechtenstein','ultimate-member'),
							'LT' => __('Lithuania','ultimate-member'),
							'LU' => __('Luxembourg','ultimate-member'),
							'MO' => __('Macao','ultimate-member'),
							'MK' => __('Macedonia, the former Yugoslav Republic of','ultimate-member'),
							'MG' => __('Madagascar','ultimate-member'),
							'MW' => __('Malawi','ultimate-member'),
							'MY' => __('Malaysia','ultimate-member'),
							'MV' => __('Maldives','ultimate-member'),
							'ML' => __('Mali','ultimate-member'),
							'MT' => __('Malta','ultimate-member'),
							'MH' => __('Marshall Islands','ultimate-member'),
							'MQ' => __('Martinique','ultimate-member'),
							'MR' => __('Mauritania','ultimate-member'),
							'MU' => __('Mauritius','ultimate-member'),
							'YT' => __('Mayotte','ultimate-member'),
							'MX' => __('Mexico','ultimate-member'),
							'FM' => __('Micronesia, Federated States of','ultimate-member'),
							'MD' => __('Moldova, Republic of','ultimate-member'),
							'MC' => __('Monaco','ultimate-member'),
							'MN' => __('Mongolia','ultimate-member'),
							'ME' => __('Montenegro','ultimate-member'),
							'MS' => __('Montserrat','ultimate-member'),
							'MA' => __('Morocco','ultimate-member'),
							'MZ' => __('Mozambique','ultimate-member'),
							'MM' => __('Myanmar','ultimate-member'),
							'NA' => __('Namibia','ultimate-member'),
							'NR' => __('Nauru','ultimate-member'),
							'NP' => __('Nepal','ultimate-member'),
							'NL' => __('Netherlands','ultimate-member'),
							'AN' => __('Netherlands Antilles','ultimate-member'),
							'NC' => __('New Caledonia','ultimate-member'),
							'NZ' => __('New Zealand','ultimate-member'),
							'NI' => __('Nicaragua','ultimate-member'),
							'NE' => __('Niger','ultimate-member'),
							'NG' => __('Nigeria','ultimate-member'),
							'NU' => __('Niue','ultimate-member'),
							'NF' => __('Norfolk Island','ultimate-member'),
							'MP' => __('Northern Mariana Islands','ultimate-member'),
							'NO' => __('Norway','ultimate-member'),
							'OM' => __('Oman','ultimate-member'),
							'PK' => __('Pakistan','ultimate-member'),
							'PW' => __('Palau','ultimate-member'),
							'PS' => __('Palestine','ultimate-member'),
							'PA' => __('Panama','ultimate-member'),
							'PG' => __('Papua New Guinea','ultimate-member'),
							'PY' => __('Paraguay','ultimate-member'),
							'PE' => __('Peru','ultimate-member'),
							'PH' => __('Philippines','ultimate-member'),
							'PN' => __('Pitcairn','ultimate-member'),
							'PL' => __('Poland','ultimate-member'),
							'PT' => __('Portugal','ultimate-member'),
							'PR' => __('Puerto Rico','ultimate-member'),
							'QA' => __('Qatar','ultimate-member'),
							'RE' => __('Réunion','ultimate-member'),
							'RO' => __('Romania','ultimate-member'),
							'RU' => __('Russian Federation','ultimate-member'),
							'RW' => __('Rwanda','ultimate-member'),
							'BL' => __('Saint Barthélemy','ultimate-member'),
							'SH' => __('Saint Helena','ultimate-member'),
							'KN' => __('Saint Kitts and Nevis','ultimate-member'),
							'LC' => __('Saint Lucia','ultimate-member'),
							'MF' => __('Saint Martin (French part)','ultimate-member'),
							'PM' => __('Saint Pierre and Miquelon','ultimate-member'),
							'VC' => __('Saint Vincent and the Grenadines','ultimate-member'),
							'WS' => __('Samoa','ultimate-member'),
							'SM' => __('San Marino','ultimate-member'),
							'ST' => __('Sao Tome and Principe','ultimate-member'),
							'SA' => __('Saudi Arabia','ultimate-member'),
							'SN' => __('Senegal','ultimate-member'),
							'RS' => __('Serbia','ultimate-member'),
							'SC' => __('Seychelles','ultimate-member'),
							'SL' => __('Sierra Leone','ultimate-member'),
							'SG' => __('Singapore','ultimate-member'),
							'SK' => __('Slovakia','ultimate-member'),
							'SI' => __('Slovenia','ultimate-member'),
							'SB' => __('Solomon Islands','ultimate-member'),
							'SO' => __('Somalia','ultimate-member'),
							'ZA' => __('South Africa','ultimate-member'),
							'GS' => __('South Georgia and the South Sandwich Islands','ultimate-member'),
							'SS' => __('South Sudan','ultimate-member'),
							'ES' => __('Spain','ultimate-member'),
							'LK' => __('Sri Lanka','ultimate-member'),
							'SD' => __('Sudan','ultimate-member'),
							'SR' => __('Suriname','ultimate-member'),
							'SJ' => __('Svalbard and Jan Mayen','ultimate-member'),
							'SZ' => __('Swaziland','ultimate-member'),
							'SE' => __('Sweden','ultimate-member'),
							'CH' => __('Switzerland','ultimate-member'),
							'SY' => __('Syrian Arab Republic','ultimate-member'),
							'TW' => __('Taiwan, Province of China','ultimate-member'),
							'TJ' => __('Tajikistan','ultimate-member'),
							'TZ' => __('Tanzania, United Republic of','ultimate-member'),
							'TH' => __('Thailand','ultimate-member'),
							'TL' => __('Timor-Leste','ultimate-member'),
							'TG' => __('Togo','ultimate-member'),
							'TK' => __('Tokelau','ultimate-member'),
							'TO' => __('Tonga','ultimate-member'),
							'TT' => __('Trinidad and Tobago','ultimate-member'),
							'TN' => __('Tunisia','ultimate-member'),
							'TR' => __('Turkey','ultimate-member'),
							'TM' => __('Turkmenistan','ultimate-member'),
							'TC' => __('Turks and Caicos Islands','ultimate-member'),
							'TV' => __('Tuvalu','ultimate-member'),
							'UG' => __('Uganda','ultimate-member'),
							'UA' => __('Ukraine','ultimate-member'),
							'AE' => __('United Arab Emirates','ultimate-member'),
							'GB' => __('United Kingdom','ultimate-member'),
							'US' => __('United States','ultimate-member'),
							'UM' => __('United States Minor Outlying Islands','ultimate-member'),
							'UY' => __('Uruguay','ultimate-member'),
							'UZ' => __('Uzbekistan','ultimate-member'),
							'VU' => __('Vanuatu','ultimate-member'),
							'VE' => __('Venezuela, Bolivarian Republic of','ultimate-member'),
							'VN' => __('Viet Nam','ultimate-member'),
							'VG' => __('Virgin Islands, British','ultimate-member'),
							'VI' => __('Virgin Islands, U.S.','ultimate-member'),
							'WF' => __('Wallis and Futuna','ultimate-member'),
							'EH' => __('Western Sahara','ultimate-member'),
							'YE' => __('Yemen','ultimate-member'),
							'ZM' => __('Zambia','ultimate-member'),
							'ZW' => __('Zimbabwe','ultimate-member'),
				);
				break;
	
		}
		
		$array = apply_filters("um_{$data}_predefined_field_options", $array);
		
		return $array;
		
	}

}
