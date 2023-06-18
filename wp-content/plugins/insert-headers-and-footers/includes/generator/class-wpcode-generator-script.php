<?php
/**
 * Generate a snippet to enqueue scripts.
 *
 * @package WPCode
 */

/**
 * WPCode_Generator_Script class.
 */
class WPCode_Generator_Script extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'enqueue_script';

	/**
	 * The categories for this generator.
	 *
	 * @var string[]
	 */
	public $categories = array(
		'core',
	);

	/**
	 * Set the translatable strings.
	 *
	 * @return void
	 */
	protected function set_strings() {
		$this->title       = __( 'Register Scripts', 'insert-headers-and-footers' );
		$this->description = __( 'Generate a snippet to load JavaScript scripts using wp_register_script.', 'insert-headers-and-footers' );
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
							'content' => __( 'Using this generator you can create a WordPress function to register and enqueue scripts.', 'insert-headers-and-footers' ),
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
							'content' => sprintf(
							// Translators: the placeholders add a link to getboostrap.com.
								__( 'You can use this to load external scripts or even scripts from a theme or plugin. For example, you could load %1$sbootstrap%2$s from a cdn.', 'insert-headers-and-footers' ),
								'<a href="https://getbootstrap.com/" target="_blank">',
								'</a>'
							),
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
							'placeholder' => 'add_custom_script',
							'default'     => 'add_custom_script' . time(),
							// This makes it unique for people who don't want to customize.
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Action (hook)', 'insert-headers-and-footers' ),
							'description' => sprintf(
							// Translators: placeholders add links to documentation on wordpress.org.
								__( 'Hook used to add the scripts: %1$sfrontend%2$s, %3$sadmin%4$s, %5$slogin%6$s or %7$sembed%8$s.', 'insert-headers-and-footers' ),
								'<a href="https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/" target="_blank">',
								'</a>',
								'<a href="https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/" target="_blank">',
								'</a>',
								'<a href="https://developer.wordpress.org/reference/hooks/login_enqueue_scripts/" target="_blank">',
								'</a>',
								'<a href="https://developer.wordpress.org/reference/hooks/enqueue_embed_scripts/" target="_blank">',
								'</a>'
							),
							'id'          => 'hook',
							'default'     => 'wp_enqueue_scripts',
							'options'     => array(
								// Translators: placeholder adds the hook name.
								'wp_enqueue_scripts'    => sprintf( __( 'Frontend (%s)', 'insert-headers-and-footers' ), 'wp_enqueue_scripts' ),
								// Translators: placeholder adds the hook name.
								'admin_enqueue_scripts' => sprintf( __( 'Admin (%s)', 'insert-headers-and-footers' ), 'admin_enqueue_scripts' ),
								// Translators: placeholder adds the hook name.
								'login_enqueue_scripts' => sprintf( __( 'Login (%s)', 'insert-headers-and-footers' ), 'login_enqueue_scripts' ),
								// Translators: placeholder adds the hook name.
								'enqueue_embed_scripts' => sprintf( __( 'Embed (%s)', 'insert-headers-and-footers' ), 'enqueue_embed_scripts' ),
							),
						),
					),
				),
			),
			'scripts' => array(
				'label'   => __( 'Scripts', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Script name', 'insert-headers-and-footers' ),
							'description' => __( 'This will be used as an identifier in the code, should be lowercase with no spaces.', 'insert-headers-and-footers' ),
							'id'          => 'script_name',
							'name'        => 'script_name[]',
							'default'     => '',
							'placeholder' => '',
							'repeater'    => 'script',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Script URL', 'insert-headers-and-footers' ),
							'description' => __( 'The full URL for the script e.g. https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js.', 'insert-headers-and-footers' ),
							'id'          => 'script_url',
							'name'        => 'script_url[]',
							'default'     => '',
							'placeholder' => '',
							'repeater'    => 'script',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Dependencies', 'insert-headers-and-footers' ),
							'description' => __( 'Comma-separated list of scripts required for this script to load, e.g. jquery', 'insert-headers-and-footers' ),
							'id'          => 'script_dependencies',
							'name'        => 'script_dependencies[]',
							'default'     => '',
							'placeholder' => '',
							'repeater'    => 'script',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Script Version', 'insert-headers-and-footers' ),
							'description' => __( 'The script version.', 'insert-headers-and-footers' ),
							'id'          => 'script_version',
							'name'        => 'script_version[]',
							'default'     => '1.0.0',
							'placeholder' => '',
							'repeater'    => 'script',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Header or Footer?', 'insert-headers-and-footers' ),
							'description' => __( 'Load the script in the page head or in the footer.', 'insert-headers-and-footers' ),
							'id'          => 'script_location',
							'name'        => 'script_location[]',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Footer', 'insert-headers-and-footers' ),
								'false' => __( 'Header', 'insert-headers-and-footers' ),
							),
							'repeater'    => 'script',
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Deregister script?', 'insert-headers-and-footers' ),
							'description' => sprintf(
							// Translators: Placeholders for wp.org docs link.
								__( 'Should the script be %1$sderegistered%2$s first? (for example, if you are replacing an existing script).', 'insert-headers-and-footers' ),
								'<a href="https://developer.wordpress.org/reference/functions/wp_deregister_script/" target="_blank">',
								'</a>'
							),
							'id'          => 'script_deregister',
							'name'        => 'script_deregister[]',
							'default'     => 'false',
							'options'     => array(
								'false' => __( 'No', 'insert-headers-and-footers' ),
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
							),
							'repeater'    => 'script',
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Enqueue script?', 'insert-headers-and-footers' ),
							'description' => sprintf(
							// Translators: Placeholders for wp.org docs link.
								__( 'Should the script be %1$senqueued%2$s or just registered? (select "No" only if you intend enqueueing it later.', 'insert-headers-and-footers' ),
								'<a href="https://developer.wordpress.org/reference/functions/wp_enqueue_script/" target="_blank">',
								'</a>'
							),
							'id'          => 'script_enqueue',
							'name'        => 'script_enqueue[]',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
							'repeater'    => 'script',
						),
						array(
							'type' => 'spacer',
						),
					),
					// Column 3.
					array(
						array(
							'type'    => 'description',
							'label'   => __( 'Add more scripts', 'insert-headers-and-footers' ),
							'content' => __( 'Use the "Add script" button below to add multiple scripts in this snippet.', 'insert-headers-and-footers' ),
						),
						array(
							'type'        => 'repeater_button',
							'button_text' => __( 'Add script', 'insert-headers-and-footers' ),
							'id'          => 'script', // Repeater to repeat when clicked.
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

		$scripts            = $this->get_value( 'script_name' );
		$scripts_urls       = $this->get_value( 'script_url' );
		$scripts_deps       = $this->get_value( 'script_dependencies' );
		$scripts_versions   = $this->get_value( 'script_version' );
		$scripts_locations  = $this->get_value( 'script_location' );
		$scripts_deregister = $this->get_value( 'script_deregister' );
		$scripts_enqueue    = $this->get_value( 'script_enqueue' );
		$code               = '';

		if ( ! empty( $scripts ) ) {
			foreach ( $scripts as $key => $script_name ) {
				if ( empty( $script_name ) ) {
					continue;
				}
				$script_name  = sanitize_title( $script_name );
				$dependencies = explode( ',', $scripts_deps[ $key ] );
				$deregister   = 'true' === $scripts_deregister[ $key ] ? "wp_deregister_script( '$script_name' );" : '';
				$enqueue      = 'true' === $scripts_enqueue[ $key ] ? "wp_enqueue_script( '$script_name' );" : '';

				$code .= "
			$deregister
			wp_register_script( '$script_name', '$scripts_urls[$key]', {$this->array_to_code_string($dependencies)}, '$scripts_versions[$key]', $scripts_locations[$key] );
			$enqueue
			";
			}
		}

		return <<<EOD
// Add custom scripts
function {$this->get_value( 'function_name' )}() {
$code
}
add_action( '{$this->get_value( 'hook' )}', '{$this->get_value( 'function_name' )}' );
EOD;
	}

}
