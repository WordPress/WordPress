<?php
/**
 * Class to auto-insert snippets site-wide.
 *
 * @package wpcode
 */

/**
 * Class WPCode_Auto_Insert_Single.
 */
class WPCode_Auto_Insert_Site_Wide extends WPCode_Auto_Insert_Type {

	/**
	 * The category of this type.
	 *
	 * @var string
	 */
	public $category = 'global';

	/**
	 * Load the available options and labels.
	 *
	 * @return void
	 */
	public function init() {
		$this->label     = __( 'Site wide', 'insert-headers-and-footers' );
		$this->locations = array(
			'site_wide_header' => array(
				'label'       => esc_html__( 'Site Wide Header', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Insert snippet between the head tags of your website on all pages.', 'insert-headers-and-footers' ),
			),
			'site_wide_body'   => array(
				'label'       => esc_html__( 'Site Wide Body', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Insert the snippet just after the opening body tag.', 'insert-headers-and-footers' ),
			),
			'site_wide_footer' => array(
				'label'       => esc_html__( 'Site Wide Footer', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Insert the snippet in the footer just before the closing body tag.', 'insert-headers-and-footers' ),
			),
		);
	}

	/**
	 * Add hooks specific to this type.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_head', array( $this, 'insert_header' ) );
		add_action( 'wp_footer', array( $this, 'insert_footer' ) );
		add_action( 'wp_body_open', array( $this, 'insert_body' ) );
	}

	/**
	 * Insert snippets in the header.
	 *
	 * @return void
	 */
	public function insert_header() {
		$this->output_location( 'site_wide_header' );
	}

	/**
	 * Insert snippets in the footer.
	 *
	 * @return void
	 */
	public function insert_footer() {
		$this->output_location( 'site_wide_footer' );
	}

	/**
	 * Insert snippets after the opening body tag.
	 *
	 * @return void
	 */
	public function insert_body() {
		$this->output_location( 'site_wide_body' );
	}
}

new WPCode_Auto_Insert_Site_Wide();
