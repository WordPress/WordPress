<?php
/**
 * Generate a snippet for a sidebar.
 *
 * @package WPCode
 */

/**
 * WPCode_Generator_Sidebar class.
 */
class WPCode_Generator_Sidebar extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'sidebar';

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
		$this->title       = __( 'Sidebar', 'insert-headers-and-footers' );
		$this->description = __( 'Generate a snippet to register a sidebar for your widgets.', 'insert-headers-and-footers' );
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
								__( 'This generator makes it easy to add sidebars to your website using the "register_sidebar" function.', 'insert-headers-and-footers' ),
								'<a href="https://developer.wordpress.org/reference/functions/register_sidebar/" target="_blank">',
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
							'content' => __( 'You can add multiple widget areas for your footer or post-type specific sidebars.', 'insert-headers-and-footers' ),
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
							'placeholder' => 'register_custom_sidebars',
							'default'     => 'register_custom_sidebars' . time(),
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
				'label'   => __( 'Sidebars', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Sidebar Id', 'insert-headers-and-footers' ),
							'description' => __( 'This is the sidebar unique id, used in the code, lowercase with no spaces.', 'insert-headers-and-footers' ),
							'id'          => 'sidebar_id',
							'name'        => 'sidebar_id[]',
							'repeater'    => 'sidebars',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Name', 'insert-headers-and-footers' ),
							'description' => __( 'Add a descriptive label for this sidebar to be used in the admin.', 'insert-headers-and-footers' ),
							'id'          => 'sidebar_name',
							'name'        => 'sidebar_name[]',
							'repeater'    => 'sidebars',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Description', 'insert-headers-and-footers' ),
							'description' => __( 'A short description for the the admin area..', 'insert-headers-and-footers' ),
							'id'          => 'sidebar_description',
							'name'        => 'sidebar_description[]',
							'repeater'    => 'sidebars',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'CSS Class', 'insert-headers-and-footers' ),
							'description' => __( 'Use an unique CSS class name for better control over this sidebar\'s styles in the admin.', 'insert-headers-and-footers' ),
							'id'          => 'sidebar_css_class',
							'name'        => 'sidebar_css_class[]',
							'repeater'    => 'sidebars',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'html',
							'label'       => __( 'Before Title', 'insert-headers-and-footers' ),
							'description' => __( 'HTML code to add before each widget title.', 'insert-headers-and-footers' ),
							'id'          => 'before_title',
							'name'        => 'before_title[]',
							'repeater'    => 'sidebars',
							'default'     => '<h2 class="widgettitle">',
						),
						array(
							'type'        => 'html',
							'label'       => __( 'After Title', 'insert-headers-and-footers' ),
							'description' => __( 'HTML code to add after each widget title.', 'insert-headers-and-footers' ),
							'id'          => 'after_title',
							'name'        => 'after_title[]',
							'repeater'    => 'sidebars',
							'default'     => '</h2>',
						),
						array(
							'type'        => 'html',
							'label'       => __( 'Before Widget', 'insert-headers-and-footers' ),
							'description' => __( 'HTML code to add before each widget.', 'insert-headers-and-footers' ),
							'id'          => 'before_widget',
							'name'        => 'before_widget[]',
							'repeater'    => 'sidebars',
							'default'     => '<li id="%1$s" class="widget %2$s">',
						),
						array(
							'type'        => 'html',
							'label'       => __( 'After Widget', 'insert-headers-and-footers' ),
							'description' => __( 'HTML code to add after each widget.', 'insert-headers-and-footers' ),
							'id'          => 'after_widget',
							'name'        => 'after_widget[]',
							'repeater'    => 'sidebars',
							'default'     => '</li>',
						),
						array(
							'type' => 'spacer',
						),
					),
					// Column 3.
					array(
						array(
							'type'    => 'description',
							'label'   => __( 'Add another sidebar', 'insert-headers-and-footers' ),
							'content' => __( 'Use the "Add Sidebar" button below to add as many sidebars as you need.', 'insert-headers-and-footers' ),
						),
						array(
							'type'        => 'repeater_button',
							'button_text' => __( 'Add Sidebar', 'insert-headers-and-footers' ),
							'id'          => 'sidebars', // Repeater to repeat when clicked.
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

		$sidebar_id   = $this->get_value( 'sidebar_id' );
		$sidebar_code = '';

		$values = array(
			'name'          => 'sidebar_name',
			'description'   => 'sidebar_description',
			'class'         => 'sidebar_css_class',
			'before_title'  => 'before_title',
			'after_title'   => 'after_title',
			'before_widget' => 'before_widget',
			'after_widget'  => 'after_widget',
		);

		if ( ! empty( $sidebar_id ) ) {
			foreach ( $sidebar_id as $key => $id ) {
				if ( empty( $id ) ) {
					continue;
				}
				$id        = sanitize_title( $id );
				$optionals = '';
				foreach ( $values as $arg_key => $form_key ) {
					$form_values = $this->get_value( $form_key );

					$optionals .= $this->get_optional_value_code( $form_values[ $key ], $this->get_default_value( $form_key ), $arg_key, true );
				}

				$sidebar_code .= "
\$args = array(
		'id'                    => '$id',
$optionals); 
register_sidebar( \$args );
				";
			}
		}

		return <<<EOD
// Add Sidebars
function {$this->get_value( 'function_name' )}() {
	$sidebar_code
}
add_action( 'widgets_init', '{$this->get_value( 'function_name' )}' );
EOD;
	}

}
