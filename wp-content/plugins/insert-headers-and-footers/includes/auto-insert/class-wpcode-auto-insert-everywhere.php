<?php
/**
 * Class to auto-insert snippets site-wide.
 *
 * @package wpcode
 */

/**
 * Class WPCode_Auto_Insert_Single.
 */
class WPCode_Auto_Insert_Everywhere extends WPCode_Auto_Insert_Type {

	/**
	 * The category of this type.
	 *
	 * @var string
	 */
	public $category = 'global';

	/**
	 * This should is only available for PHP scripts.
	 *
	 * @var string
	 */
	public $code_type = 'php';

	/**
	 * Load the available options and labels.
	 *
	 * @return void
	 */
	public function init() {
		$this->label     = __( 'PHP Snippets Only', 'insert-headers-and-footers' );
		$this->locations = array(
			'everywhere'    => array(
				'label'       => esc_html__( 'Run Everywhere', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Snippet gets executed everywhere on your website.', 'insert-headers-and-footers' ),
			),
			'frontend_only' => array(
				'label'       => esc_html__( 'Frontend Only', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Snippet gets executed only in the frontend of the website.', 'insert-headers-and-footers' ),
			),
			'admin_only'    => array(
				'label'       => esc_html__( 'Admin Only', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'The snippet only gets executed in the wp-admin area.', 'insert-headers-and-footers' ),
			),
			'frontend_cl'   => array(
				'label'       => esc_html__( 'Frontend Conditional Logic', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Ideal for running the snippet later with conditional logic rules in the frontend.', 'insert-headers-and-footers' ),
			),
		);
	}

	/**
	 * Execute snippets.
	 *
	 * @return void
	 */
	public function run_snippets() {
		$snippets = $this->get_snippets_for_location( 'everywhere' );
		foreach ( $snippets as $snippet ) {
			wpcode()->execute->get_snippet_output( $snippet );
		}
		$location = is_admin() ? 'admin_only' : 'frontend_only';
		$snippets = $this->get_snippets_for_location( $location );
		foreach ( $snippets as $snippet ) {
			wpcode()->execute->get_snippet_output( $snippet );
		}
	}

	/**
	 * Execute snippets on the init hook to allow using more Conditional Logic options.
	 *
	 * @return void
	 */
	public function run_init_snippets() {
		$snippets = $this->get_snippets_for_location( 'frontend_cl' );
		foreach ( $snippets as $snippet ) {
			wpcode()->execute->get_snippet_output( $snippet );
		}
	}

	/**
	 * Override the default hook and short-circuit any other conditions
	 * checks as these snippets will run everywhere.
	 *
	 * @return void
	 */
	protected function add_start_hook() {
		add_action( 'plugins_loaded', array( $this, 'run_snippets' ), 5 );
		add_action( 'wp', array( $this, 'run_init_snippets' ) );
	}
}

new WPCode_Auto_Insert_Everywhere();
