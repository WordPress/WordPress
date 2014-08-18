<?php
/**
 * Functions for registering and setting theme settings that tie into the WordPress theme customizer.  
 * This file loads additional classes and adds settings to the customizer for the built-in Hybrid Core 
 * settings.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Load custom control classes. */
add_action( 'customize_register', 'hybrid_load_customize_controls', 1 );

/**
 * Loads framework-specific customize control classes.  Customize control classes extend the WordPress 
 * WP_Customize_Control class to create unique classes that can be used within the framework.
 *
 * @since 1.4.0
 * @access private
 */
function hybrid_load_customize_controls() {

	/* Loads the textarea customize control class. */
	require_once( trailingslashit( HYBRID_CLASSES ) . 'customize-control-textarea.php' );

	/* Loads the background image customize control class. */
	require_once( trailingslashit( HYBRID_CLASSES ) . 'customize-control-background-image.php' );
}
