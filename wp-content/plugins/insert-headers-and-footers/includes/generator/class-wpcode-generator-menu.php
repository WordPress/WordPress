<?php
/**
 * Generate a snippet for adding a custom navigation menu.
 *
 * @package WPCode
 */

/**
 * WPCode_Generator_Menu class.
 */
class WPCode_Generator_Menu extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'menu';

	/**
	 * The categories for this generator.
	 *
	 * @var string[]
	 */
	public $categories = array(
		'design',
	);

	/**
	 * Set the translatable strings.
	 *
	 * @return void
	 */
	protected function set_strings() {
		$this->title       = __( 'Navigation Menu', 'insert-headers-and-footers' );
		$this->description = __( 'Generate a snippet to register new navigation menus for your website.', 'insert-headers-and-footers' );
	}

	/**
	 * Load the generator tabs.
	 *
	 * @return void
	 */
	protected function load_tabs() {
		$this->tabs = array(
			'info'     => array(
				'label'   => __( 'Info', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						// Column 1 fields.
						array(
							'type'    => 'description',
							'label'   => __( 'Overview', 'insert-headers-and-footers' ),
							'content' => sprintf(
							// Translators: Placeholders add links to the wordpress.org references.
								__( 'This generator makes it easy to add new navigation menus to your website using the "register_nav_menus" function.', 'insert-headers-and-footers' ),
								'<a href="https://developer.wordpress.org/reference/functions/register_nav_menus/" target="_blank">',
								'</a>'
							),
						),
					),
					// Column 2.
					array(
						// Column 2 fields.
						array(
							'type'    => 'list',
							'label'   => __( 'Usage', 'insert-headers-and-footers' ),
							'content' => array(
								__( 'Fill in the forms using the menu on the left.', 'insert-headers-and-footers' ),
								__( 'Click the "Update Code" button.', 'insert-headers-and-footers' ),
								__( 'Click on "Use Snippet" to create a new snippet with the generated code.', 'insert-headers-and-footers' ),
								__( 'Activate and save the snippet and you\'re ready to go', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 3.
					array(
						// Column 3 fields.
						array(
							'type'    => 'description',
							'label'   => __( 'Examples', 'insert-headers-and-footers' ),
							'content' => __( 'You can add a new navigation menu for your website to display in a flyout menu that is not part of the theme, for example.', 'insert-headers-and-footers' ),
						),
					),
				),
			),
			'general'  => array(
				'label'   => __( 'General', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Function name', 'insert-headers-and-footers' ),
							'description' => __( 'Make this unique to avoid conflicts with other snippets', 'insert-headers-and-footers' ),
							'id'          => 'function_name',
							'placeholder' => 'add_custom_navigation_menu',
							'default'     => 'add_custom_navigation_menu' . time(),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Text Domain', 'insert-headers-and-footers' ),
							'description' => __( 'Optional text domain for translations.', 'insert-headers-and-footers' ),
							'id'          => 'text_domain',
							'placeholder' => 'text_domain',
							'default'     => 'text_domain',
						),
					),
				),
			),
			'schedule' => array(
				'label'   => __( 'Menus', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Menu name', 'insert-headers-and-footers' ),
							'description' => __( 'This is the menu name slug, lowercase and no space.', 'insert-headers-and-footers' ),
							'id'          => 'menu_name',
							'name'        => 'menu_name[]',
							'repeater'    => 'menus',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Menu label', 'insert-headers-and-footers' ),
							'description' => __( 'Add a descriptive label for this menu in the admin.', 'insert-headers-and-footers' ),
							'id'          => 'menu_description',
							'name'        => 'menu_description[]',
							'repeater'    => 'menus',
						),
					),
					// Column 2.
					array(
						array(
							'type'    => 'description',
							'label'   => __( 'Add another menu', 'insert-headers-and-footers' ),
							'content' => __( 'Use the "Add menu" button below to add as many menu locations as you need.', 'insert-headers-and-footers' ),
						),
						array(
							'type'        => 'repeater_button',
							'button_text' => __( 'Add Menu', 'insert-headers-and-footers' ),
							'id'          => 'menus', // Repeater to repeat when clicked.
						),
					),
				),
			),
		);
	}

	/**
	 * Get the snippet code with dynamic values applied.
	 *
	 * @return string
	 */
	public function get_snippet_code() {

		$menus        = $this->get_value( 'menu_name' );
		$descriptions = $this->get_value( 'menu_description' );
		$menus_code   = '';
		$textdomain   = $this->get_value( 'text_domain' );

		if ( ! empty( $menus ) ) {
			$array_code = '';
			foreach ( $menus as $key => $menu ) {
				if ( empty( $menu ) ) {
					continue;
				}
				$array_code .= "'$menu' => __( '$descriptions[$key]', '$textdomain' ),\n\t\t";
			}
			if ( ! empty( $array_code ) ) {
				$menus_code = "\$menus = array( 
		$array_code);
	register_nav_menus( \$menus );
			";
			}
		}

		return <<<EOD
// Add Custom Navigation Menus
function {$this->get_value( 'function_name' )}() {
	$menus_code
}
add_action( 'init', '{$this->get_value( 'function_name' )}' );
EOD;
	}

}
