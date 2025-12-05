<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Generates the HTML for an inline Help Button and Panel.
 */
class WPSEO_Admin_Help_Panel {

	/**
	 * Unique identifier of the element the inline help refers to, used as an identifier in the html.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The Help Button text. Needs a properly escaped string.
	 *
	 * @var string
	 */
	private $help_button_text;

	/**
	 * The Help Panel content. Needs a properly escaped string (might contain HTML).
	 *
	 * @var string
	 */
	private $help_content;

	/**
	 * Optional Whether to print out a container div element for the Help Panel, used for styling.
	 *
	 * @var string
	 */
	private $wrapper;

	/**
	 * Constructor.
	 *
	 * @param string $id               Unique identifier of the element the inline help refers to, used as
	 *                                 an identifier in the html.
	 * @param string $help_button_text The Help Button text. Needs a properly escaped string.
	 * @param string $help_content     The Help Panel content. Needs a properly escaped string (might contain HTML).
	 * @param string $wrapper          Optional Whether to print out a container div element for the Help Panel,
	 *                                 used for styling.
	 *                                 Pass a `has-wrapper` value to print out the container. Default: no container.
	 */
	public function __construct( $id, $help_button_text, $help_content, $wrapper = '' ) {
		$this->id               = $id;
		$this->help_button_text = $help_button_text;
		$this->help_content     = $help_content;
		$this->wrapper          = $wrapper;
	}

	/**
	 * Returns the html for the Help Button.
	 *
	 * @return string
	 */
	public function get_button_html() {

		if ( ! $this->id || ! $this->help_button_text || ! $this->help_content ) {
			return '';
		}

		return sprintf(
			' <button type="button" class="yoast_help yoast-help-button dashicons" id="%1$s-help-toggle" aria-expanded="false" aria-controls="%1$s-help"><span class="yoast-help-icon" aria-hidden="true"></span><span class="screen-reader-text">%2$s</span></button>',
			esc_attr( $this->id ),
			$this->help_button_text
		);
	}

	/**
	 * Returns the html for the Help Panel.
	 *
	 * @return string
	 */
	public function get_panel_html() {

		if ( ! $this->id || ! $this->help_button_text || ! $this->help_content ) {
			return '';
		}

		$wrapper_start = '';
		$wrapper_end   = '';

		if ( $this->wrapper === 'has-wrapper' ) {
			$wrapper_start = '<div class="yoast-seo-help-container">';
			$wrapper_end   = '</div>';
		}

		return sprintf(
			'%1$s<p id="%2$s-help" class="yoast-help-panel">%3$s</p>%4$s',
			$wrapper_start,
			esc_attr( $this->id ),
			$this->help_content,
			$wrapper_end
		);
	}
}
