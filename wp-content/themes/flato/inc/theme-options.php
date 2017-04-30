<?php

/*  Initialize the options before anything else. 
/* ------------------------------------ */
add_action( 'admin_init', 'themememe_theme_options', 1 );


/*  Build the custom settings & update OptionTree.
/* ------------------------------------ */
function themememe_theme_options() {
	
	// Get a copy of the saved settings array.
	$saved_settings = get_option( 'option_tree_settings', array() );

	// Custom settings array that will eventually be passed to the OptionTree Settings API Class.
	$custom_settings = array(

/*  Admin panel sections
/* ------------------------------------ */	
	'sections'        => array(
		array(
			'id'		=> 'general',
			'title'		=> 'General'
		),
	),
	
/*  Theme options
/* ------------------------------------ */
	'settings'        => array(
		
		// Header: Custom Logo
		array(
			'id'		=> 'custom-logo',
			'label'		=> 'Custom Logo',
			'desc'		=> 'Upload your logo image.',
			'type'		=> 'upload',
			'section'	=> 'general'
		),
		// Header: Site Description
		array(
			'id'		=> 'site-description',
			'label'		=> 'Site Description',
			'desc'		=> 'Hide your site description.',
			'type'		=> 'checkbox',
			'section'	=> 'general',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Disable'
				)
			)
		)
	)
);

/*  Update the DB
/* ------------------------------------ */
	if ( $saved_settings !== $custom_settings ) {
		update_option( 'option_tree_settings', $custom_settings ); 
	} 
}
