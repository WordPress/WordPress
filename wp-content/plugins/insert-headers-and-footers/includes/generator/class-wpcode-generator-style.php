<?php
/**
 * Generate a snippet to enqueue styles.
 *
 * @package WPCode
 */

/**
 * WPCode_Generator_Style class.
 */
class WPCode_Generator_Style extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'enqueue_style';

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
		$this->title       = __( 'Register Stylesheets', 'insert-headers-and-footers' );
		$this->description = __( 'Generate a snippet to load CSS stylesheets using wp_register_style.', 'insert-headers-and-footers' );
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
							'content' => __( 'Using this generator you can create a WordPress function to register and enqueue styles.', 'insert-headers-and-footers' ),
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
								__( 'You can use this to load external styles or even styles from a theme or plugin. For example, you could load %1$sfontawesome%2$s from a cdn.', 'insert-headers-and-footers' ),
								'<a href="https://fontawesome.com/" target="_blank">',
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
							'placeholder' => 'add_custom_style',
							'default'     => 'add_custom_style' . time(),
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
								__( 'Hook used to add the styles: %1$sfrontend%2$s, %3$sadmin%4$s, %5$slogin%6$s or %7$sembed%8$s.', 'insert-headers-and-footers' ),
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
			'styles'  => array(
				'label'   => __( 'Styles', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Style name', 'insert-headers-and-footers' ),
							'description' => __( 'This will be used as an identifier in the code, should be lowercase with no spaces.', 'insert-headers-and-footers' ),
							'id'          => 'style_name',
							'name'        => 'style_name[]',
							'default'     => '',
							'placeholder' => '',
							'repeater'    => 'style',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Stylesheet URL', 'insert-headers-and-footers' ),
							'description' => __( 'The full URL for the stylesheet e.g. https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css.', 'insert-headers-and-footers' ),
							'id'          => 'style_url',
							'name'        => 'style_url[]',
							'default'     => '',
							'placeholder' => '',
							'repeater'    => 'style',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Dependencies', 'insert-headers-and-footers' ),
							'description' => __( 'Comma-separated list of styles required for this style to load, e.g. jquery', 'insert-headers-and-footers' ),
							'id'          => 'style_dependencies',
							'name'        => 'style_dependencies[]',
							'default'     => '',
							'placeholder' => '',
							'repeater'    => 'style',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Style Version', 'insert-headers-and-footers' ),
							'description' => __( 'The style version.', 'insert-headers-and-footers' ),
							'id'          => 'style_version',
							'name'        => 'style_version[]',
							'default'     => '1.0.0',
							'placeholder' => '',
							'repeater'    => 'style',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Media', 'insert-headers-and-footers' ),
							'description' => sprintf(
							// Translators: placeholders add a link to the W3.org reference.
								__( 'Load the style %1$smedia type%2$s, usually "all".', 'insert-headers-and-footers' ),
								'<a href="https://www.w3.org/TR/CSS2/media.html#media-types" target="_blank">',
								'</a>'
							),
							'id'          => 'style_media',
							'name'        => 'style_media[]',
							'default'     => 'all',
							'repeater'    => 'style',
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Deregister style?', 'insert-headers-and-footers' ),
							'description' => sprintf(
							// Translators: Placeholders for wp.org docs link.
								__( 'Should the style be %1$sderegistered%2$s first? (for example, if you are replacing an existing style).', 'insert-headers-and-footers' ),
								'<a href="https://developer.wordpress.org/reference/functions/wp_deregister_style/" target="_blank">',
								'</a>'
							),
							'id'          => 'style_deregister',
							'name'        => 'style_deregister[]',
							'default'     => 'false',
							'options'     => array(
								'false' => __( 'No', 'insert-headers-and-footers' ),
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
							),
							'repeater'    => 'style',
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Enqueue style?', 'insert-headers-and-footers' ),
							'description' => sprintf(
							// Translators: Placeholders for wp.org docs link.
								__( 'Should the style be %1$senqueued%2$s or just registered? (select "No" only if you intend enqueueing it later.', 'insert-headers-and-footers' ),
								'<a href="https://developer.wordpress.org/reference/functions/wp_enqueue_style/" target="_blank">',
								'</a>'
							),
							'id'          => 'style_enqueue',
							'name'        => 'style_enqueue[]',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
							'repeater'    => 'style',
						),
						array(
							'type' => 'spacer',
						),
					),
					// Column 3.
					array(
						array(
							'type'    => 'description',
							'label'   => __( 'Add more styles', 'insert-headers-and-footers' ),
							'content' => __( 'Use the "Add style" button below to add multiple styles in this snippet.', 'insert-headers-and-footers' ),
						),
						array(
							'type'        => 'repeater_button',
							'button_text' => __( 'Add style', 'insert-headers-and-footers' ),
							'id'          => 'style', // Repeater to repeat when clicked.
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

		$styles            = $this->get_value( 'style_name' );
		$styles_urls       = $this->get_value( 'style_url' );
		$styles_deps       = $this->get_value( 'style_dependencies' );
		$styles_versions   = $this->get_value( 'style_version' );
		$styles_media      = $this->get_value( 'style_media' );
		$styles_deregister = $this->get_value( 'style_deregister' );
		$styles_enqueue    = $this->get_value( 'style_enqueue' );
		$code              = '';

		if ( ! empty( $styles ) ) {
			foreach ( $styles as $key => $style ) {
				if ( empty( $style ) ) {
					continue;
				}
				$style        = sanitize_title( $style );
				$dependencies = explode( ',', $styles_deps[ $key ] );
				$deregister   = 'true' === $styles_deregister[ $key ] ? "wp_deregister_style( '$style' );" : '';
				$enqueue      = 'true' === $styles_enqueue[ $key ] ? "wp_enqueue_style( '$style' );" : '';
				$media        = 'all' !== $styles_media[ $key ] ? ", '$styles_media[$key]'" : '';

				$code .= "
			$deregister
			wp_register_style( '$style', '$styles_urls[$key]', {$this->array_to_code_string($dependencies)}, '$styles_versions[$key]'$media );
			$enqueue
			";
			}
		}

		return <<<EOD
// Add custom styles
function {$this->get_value( 'function_name' )}() {
$code
}
add_action( '{$this->get_value( 'hook' )}', '{$this->get_value( 'function_name' )}' );
EOD;
	}

}
