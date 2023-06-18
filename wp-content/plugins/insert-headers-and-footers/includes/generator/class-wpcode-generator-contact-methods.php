<?php
/**
 * Generate a snippet to add a extra contact methods to user profiles.
 *
 * @package WPCode
 */

/**
 * The Contact Methods generator.
 */
class WPCode_Generator_Contact_Methods extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'contact-methods';

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
		$this->title       = __( 'Contact Methods', 'insert-headers-and-footers' );
		$this->description = __( 'Add additional contact methods to WordPress user profiles.', 'insert-headers-and-footers' );
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
							'content' => __( 'Use this generator to create a snippet which adds more contact methods to your WordPress users profiles.', 'insert-headers-and-footers' ),
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
							'content' => __( 'You can add extra fields for user profiles like their extended address, city, country, phone number, social media profiles (Facebook, Twitter, etc).', 'insert-headers-and-footers' ),
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
							'description' => __( 'Make this unique to avoid conflicts with other snippets.', 'insert-headers-and-footers' ),
							'id'          => 'function_name',
							'placeholder' => 'add_custom_contact_methods',
							'default'     => 'add_custom_contact_methods_' . time(),
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
			'methods' => array(
				'label'   => __( 'Contact Methods', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Contact Method Slug', 'insert-headers-and-footers' ),
							'description' => __( 'A lowercase with no spaces slug for usage in the code. For example: "facebook" or "telephone".', 'insert-headers-and-footers' ),
							'id'          => 'contact_method_name',
							'name'        => 'contact_method_name[]',
							'placeholder' => '',
							'repeater'    => 'contact_methods',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Contact Method Label', 'insert-headers-and-footers' ),
							'description' => __( 'This will show up as a label next to the contact method field. For example: "Facebook URL" or "Phone number".', 'insert-headers-and-footers' ),
							'id'          => 'contact_method_description',
							'name'        => 'contact_method_description[]',
							'placeholder' => '',
							'repeater'    => 'contact_methods',
						),
					),
					// Column 3.
					array(
						array(
							'type'    => 'description',
							'label'   => __( 'Add more contact methods', 'insert-headers-and-footers' ),
							'content' => __( 'Use the "Add contact method" button below to add as many contact methods as you wish.', 'insert-headers-and-footers' ),
						),
						array(
							'type'        => 'repeater_button',
							'button_text' => __( 'Add contact method', 'insert-headers-and-footers' ),
							'id'          => 'contact_methods', // Repeater to repeat when clicked.
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

		$contact_method_names        = $this->get_value( 'contact_method_name' );
		$contact_method_descriptions = $this->get_value( 'contact_method_description' );

		$contact_methods_code = '';

		if ( ! empty( $contact_method_names ) ) {
			foreach ( $contact_method_names as $key => $method_name ) {
				if ( empty( $method_name ) ) {
					continue;
				}
				$method_name = sanitize_title( $method_name );
				$description = empty( $contact_method_descriptions[ $key ] ) ? '' : $contact_method_descriptions[ $key ];

				$contact_methods_code .= "
	\$contact_methods[ '$method_name' ] = __( '$description', '{$this->get_value('text_domain')}' );";
			}
		}

		return <<<EOD
// Add custom contact methods

function {$this->get_value( 'function_name' )}( \$contact_methods ) {
	$contact_methods_code
	
	return \$contact_methods;
}

add_filter( 'user_contactmethods', '{$this->get_value( 'function_name' )}' );

EOD;
	}

}
