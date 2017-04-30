<?php
/**
 * Functions used for the showing help/links to eventon resources in admin
 *
 * @author 		EventON
 * @category 	Admin
 * @package 	Eventon/Admin
 * @version     0.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Help Tab Content
 *
 * Shows some text about WooCommerce and links to docs.
 *
 * @access public
 * @return void
 */
function eventon_admin_help_tab_content() {
	$screen = get_current_screen();

	$screen->add_help_tab( array(
	    'id'	=> 'eventon_overview_tab',
	    'title'	=> __( 'Overview', 'eventon' ),
	    'content'	=>

	    	'<p>' . __( 'Thank you for using EventON WordPress Event Calendar plugin. ', 'eventon' ). '</p>'

	) );

	

	$screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:', 'eventon' ) . '</strong></p>' .
		'<p><a href="http://www.myeventon.com/" target="_blank">' . __( 'EventON', 'eventon' ) . '</a></p>' .
		'<p><a href="http://www.myeventon.com/faq/" target="_blank">' . __( 'FAQ Section', 'eventon' ) . '</a></p>' .
		'<p><a href="http://www.myeventon.com/changelog/" target="_blank">' . __( 'Changelog', 'eventon' ) . '</a></p>'.
		'<p><a href="http://www.myeventon.com/documentation/" target="_blank">' . __( 'Documentation', 'eventon' ) . '</a></p>'.
		'<p><a href="http://www.myeventon.com/addons/" target="_blank">' . __( 'Addons', 'eventon' ) . '</a></p>'
	);
}
?>