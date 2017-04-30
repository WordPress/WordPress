<?php
/**
 * Extends the WordPress background image customize control class, which allows a theme to register
 * multiple default backgrounds for the user to choose from.  To use this, the theme author 
 * should remove the 'background_image' control and add this control in its place.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Background image customize control class.
 *
 * @since 2.0.0
 */
class Hybrid_Customize_Control_Background_Image extends WP_Customize_Background_Image_Control {

	/**
	 * Array of default backgrounds.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $default_backgrounds = array();

	/**
	 * Set up our control.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function __construct( $manager ) {

		/* Let WP handle this. */
		parent::__construct( $manager );

		/* Allow themes to register custom backgrounds. */
		$this->default_backgrounds = apply_filters( 'hybrid_default_backgrounds', $this->default_backgrounds );

		/* WordPress will only output the 'default' tab if there's a default image. Make sure it gets added. */
		if ( !$this->setting->default && !empty( $this->default_backgrounds ) )
			$this->add_tab( 'default', _x( 'Default', 'theme customizer tab', 'hybrid-core' ), array( $this, 'tab_default_background' ) );
	}

	/**
	 * Displays the 'default' tab for selecting a background image.  This method plays nicely with the 
	 * 'default-image' argument for 'custom-background' as well as our custom backgrounds.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function tab_default_background() {

		/* If the theme added a 'default-image', make sure to output it. */
		if ( $this->setting->default )
			$this->print_tab_image( $this->setting->default );

		/* Check if the theme added an array of default backgrounds. */
		if ( !empty( $this->default_backgrounds ) ) {

			/* Get the template and stylesheet directory URIs. */
			$template   = get_template_directory_uri();
			$stylesheet = get_stylesheet_directory_uri();

			/* Loop through the backgrounds and print them. */
			foreach ( $this->default_backgrounds as $background ) {

				/* If no thumbnail was given, use the original. */
				if ( !isset( $background['thumbnail_url'] ) )
					$background['thumbnail_url'] = $background['url'];

				/* Use '%s' for parent themes and '%2$s' for child themes. */
				$url       = sprintf( $background['url'],           $template, $stylesheet );
				$thumb_url = sprintf( $background['thumbnail_url'], $template, $stylesheet );

				/* Print the image. */
				$this->print_tab_image( $url, $thumb_url );
			}
		}
	}
}
