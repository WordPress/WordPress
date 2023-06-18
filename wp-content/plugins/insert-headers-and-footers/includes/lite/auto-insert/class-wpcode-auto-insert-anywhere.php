<?php
/**
 * Class to auto-insert snippets anywhere by a CSS selector.
 *
 * @package wpcode
 */

/**
 * Class WPCode_Auto_Insert_Anywhere.
 */
class WPCode_Auto_Insert_Anywhere_Lite extends WPCode_Auto_Insert_Type {

	/**
	 * The category of this type.
	 *
	 * @var string
	 */
	public $category = 'global';

	/**
	 * Not available to select.
	 *
	 * @var string
	 */
	public $code_type = 'pro';

	/**
	 * Text to display next to optgroup label.
	 *
	 * @var string
	 */
	public $label_pill = 'PRO';

	/**
	 * Load the available options and labels.
	 *
	 * @return void
	 */
	public function init() {
		$this->label         = __( 'Anywhere (CSS Selector)', 'insert-headers-and-footers' );
		$this->locations     = array(
			'before_css_selector' => array(
				'label'       => __( 'Before HTML Element', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the HTML element specified by the CSS selector.', 'insert-headers-and-footers' ),
			),
			'after_css_selector'  => array(
				'label'       => __( 'After HTML Element', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the HTML element specified by the CSS selector.', 'insert-headers-and-footers' ),
			),
			'start_css_selector'  => array(
				'label'       => __( 'At the start of HTML Element', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the content of the HTML element specified by CSS selector.', 'insert-headers-and-footers' ),
			),
			'end_css_selector'    => array(
				'label'       => __( 'At the end of HTML Element', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the content of the HTML element specified by CSS selector.', 'insert-headers-and-footers' ),
			),
		);
		$this->upgrade_title = __( 'Insert Anywhere by CSS Selector is a Premium feature', 'insert-headers-and-footers' );
		$this->upgrade_text  = __( 'Upgrade to PRO today and insert snippets anywhere on your site using CSS selectors to target any HTML element.', 'insert-headers-and-footers' );
		$this->upgrade_link  = wpcode_utm_url( 'https://wpcode.com/lite/', 'edit-snippet', 'auto-insert', 'anywhere' );
	}
}

new WPCode_Auto_Insert_Anywhere_Lite();
