<?php
/**
 * Generate a snippet to add a custom menu to the admin bar.
 *
 * @package WPCode
 */

/**
 * The Post Status generator.
 */
class WPCode_Generator_Admin_Bar extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'admin-bar';

	/**
	 * The categories for this generator.
	 *
	 * @var string[]
	 */
	public $categories = array(
		'admin',
	);

	/**
	 * Set the translatable strings.
	 *
	 * @return void
	 */
	protected function set_strings() {
		$this->title       = __( 'Admin Bar Menu', 'insert-headers-and-footers' );
		$this->description = __( 'Add a custom admin bar menu with links or content.', 'insert-headers-and-footers' );
	}

	/**
	 * Load the generator tabs.
	 *
	 * @return void
	 */
	protected function load_tabs() {
		$this->tabs = array(
			'info'    => array(
				'label'   => __( 'Info', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						// Column 1 fields.
						array(
							'type'    => 'description',
							'label'   => __( 'Overview', 'insert-headers-and-footers' ),
							'content' => __( 'Generate a snippet to add a custom menu to the admin bar by filling in a simple form.', 'insert-headers-and-footers' ),
						),
					),
					// Column 2.
					array(
						// Column 2 fields.
						array(
							'type'    => 'list',
							'label'   => __( 'Usage', 'insert-headers-and-footers' ),
							'content' => array(
								__( 'Fill in the forms sections using the menu on the left.', 'insert-headers-and-footers' ),
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
							'content' => __( 'You could add a new admin bar menu for links you use often, a list of posts, a site you often visit when in the admin, etc.', 'insert-headers-and-footers' ),
						),
					),
				),
			),
			'general' => array(
				'label'   => __( 'General', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Function name', 'insert-headers-and-footers' ),
							'description' => __( 'Make this unique to avoid conflicts with other snippets', 'insert-headers-and-footers' ),
							'id'          => 'function_name',
							'placeholder' => 'add_admin_bar_item',
							'default'     => 'add_admin_bar_item' . time(),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Position', 'insert-headers-and-footers' ),
							'description' => __( 'Select where you want the menu item to be displayed on the admin bar.', 'insert-headers-and-footers' ),
							'id'          => 'priority',
							'default'     => '1100',
							'options'     => array(
								'1100' => __( 'Last item on the left', 'insert-headers-and-footers' ),
								'30'   => __( 'Before Site Name', 'insert-headers-and-footers' ),
								'50'   => __( 'After Site Name', 'insert-headers-and-footers' ),
								'70'   => __( 'Before "New" Button' ),
								'80'   => __( 'After "New" Button' ),
							),
						),
					),
				),
			),
			'menu'    => array(
				'label'   => __( 'Menu Structure', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Menu ID', 'insert-headers-and-footers' ),
							'description' => __( 'Unique menu id for the admin bar menu.', 'insert-headers-and-footers' ),
							'id'          => 'menu_id',
							'placeholder' => 'custom_menu_id',
							'default'     => 'custom_menu_id',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Menu Title', 'insert-headers-and-footers' ),
							'description' => __( 'Text or HTML that will show up in the admin bar top-level item. Use HTML if you want to display an image.', 'insert-headers-and-footers' ),
							'id'          => 'menu_title',
							'placeholder' => '',
							'default'     => '',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Menu item link', 'insert-headers-and-footers' ),
							'description' => __( 'If left empty, the top level menu item will not be a link, just text.', 'insert-headers-and-footers' ),
							'id'          => 'menu_href',
							'placeholder' => '',
							'default'     => '',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Menu item target', 'insert-headers-and-footers' ),
							'description' => __( 'The menu item is a link use this field to set the target attribute. Use "_blank" to open the link in a new tab.', 'insert-headers-and-footers' ),
							'id'          => 'menu_target',
							'placeholder' => '',
							'default'     => '',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Submenu Item Title', 'insert-headers-and-footers' ),
							'description' => __( 'Text or HTML for the submenu item.', 'insert-headers-and-footers' ),
							'id'          => 'submenu_title',
							'name'        => 'submenu_title[]',
							'placeholder' => '',
							'repeater'    => 'submenu',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Submenu item link', 'insert-headers-and-footers' ),
							'description' => __( 'If left empty, this menu item will not be a link, just text.', 'insert-headers-and-footers' ),
							'id'          => 'submenu_href',
							'name'        => 'submenu_href[]',
							'placeholder' => '',
							'repeater'    => 'submenu',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Submenu item target attribute', 'insert-headers-and-footers' ),
							'description' => __( 'If the menu item is a link use this for the target attribute. Use "_blank" to open in a new tab.', 'insert-headers-and-footers' ),
							'id'          => 'submenu_target',
							'name'        => 'submenu_target[]',
							'placeholder' => '',
							'repeater'    => 'submenu',
						),
					),
					// Column 3.
					array(
						array(
							'type'    => 'description',
							'label'   => __( 'Add more submenu items', 'insert-headers-and-footers' ),
							'content' => __( 'Use the "Add submenu item" button below to add multiple submenu items.', 'insert-headers-and-footers' ),
						),
						array(
							'type'        => 'repeater_button',
							'button_text' => __( 'Add submenu item', 'insert-headers-and-footers' ),
							'id'          => 'submenu', // Repeater to repeat when clicked.
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

		$submenu_titles  = $this->get_value( 'submenu_title' );
		$submenu_hrefs   = $this->get_value( 'submenu_href' );
		$submenu_targets = $this->get_value( 'submenu_target' );
		$submenus_code   = '';

		if ( ! empty( $submenu_titles ) ) {
			foreach ( $submenu_titles as $key => $submenu_title ) {
				if ( empty( $submenu_title ) ) {
					continue;
				}
				$submenu_href   = empty( $submenu_hrefs[ $key ] ) ? '' : $submenu_hrefs[ $key ];
				$submenu_target = empty( $submenu_targets[ $key ] ) ? '' : $submenu_targets[ $key ];

				$submenus_code .= "
	\$admin_bar->add_menu(
		array(
			'id'     => 'submenu_{$this->get_value( 'menu_id' )}_$key',
			'parent' => '{$this->get_value( 'menu_id' )}',
			'title'  => '$submenu_title',
			'href'   => '$submenu_href',
			'meta'   => array(
				'target' => '$submenu_target',
			),
 		)
	);
			";
			}
		}

		return <<<EOD
// Add a custom menu item.

function {$this->get_value( 'function_name' )}( \$admin_bar ) {
	\$admin_bar->add_menu(
		array(
			'id'     => '{$this->get_value( 'menu_id' )}',
			'title'  => '{$this->get_value( 'menu_title' )}',
			'href'   => '{$this->get_value( 'menu_href' )}',
			'meta'   => array(
				'target' => '{$this->get_value( 'menu_target' )}',
			),
		)
	);
	$submenus_code
}

add_action( 'admin_bar_menu', '{$this->get_value( 'function_name' )}', {$this->get_value( 'priority' )} );

EOD;
	}

}
