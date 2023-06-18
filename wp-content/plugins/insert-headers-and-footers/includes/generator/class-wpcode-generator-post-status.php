<?php
/**
 * Generate a snippet to register a new post status.
 *
 * @package WPCode
 */

/**
 * The Post Status generator.
 */
class WPCode_Generator_Post_Status extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'post-status';

	/**
	 * The categories for this generator.
	 *
	 * @var string[]
	 */
	public $categories = array(
		'content',
	);

	/**
	 * Set the translatable strings.
	 *
	 * @return void
	 */
	protected function set_strings() {
		$this->title       = __( 'Post Status', 'insert-headers-and-footers' );
		$this->description = __( 'Use this tool to generate a custom post status for your posts.', 'insert-headers-and-footers' );
	}

	/**
	 * Load the generator tabs.
	 *
	 * @return void
	 */
	protected function load_tabs() {
		$this->tabs = array(
			'info'       => array(
				'label'   => __( 'Info', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						// Column 1 fields.
						array(
							'type'    => 'description',
							'label'   => __( 'Overview', 'insert-headers-and-footers' ),
							'content' => __( 'Generate custom post statuses for your posts to improve the way you manage content.', 'insert-headers-and-footers' ),
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
							'content' => __( 'You could add a new status called "Pending Review" that your authors can use before the content will be published', 'insert-headers-and-footers' ),
						),
					),
				),
			),
			'general'    => array(
				'label'   => __( 'General', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Function name', 'insert-headers-and-footers' ),
							'description' => __( 'Make this unique to avoid conflicts with other snippets', 'insert-headers-and-footers' ),
							'id'          => 'function_name',
							'placeholder' => 'custom_post_status',
							'default'     => 'custom_post_status' . time(),
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
			'status'     => array(
				'label'   => __( 'Status', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Post Status', 'insert-headers-and-footers' ),
							'description' => __( 'Name of status used in the code, lowercase maximum 32 characters.', 'insert-headers-and-footers' ),
							'id'          => 'post_status',
							'placeholder' => 'pending',
							'default'     => 'pending',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Name', 'insert-headers-and-footers' ),
							'description' => __( 'The singular name that will be displayed in the admin. ' ),
							'id'          => 'label',
							'placeholder' => 'Pending',
							'default'     => 'Pending',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Name (Plural)', 'insert-headers-and-footers' ),
							'description' => __( 'The post status plural name. For example: Drafts.' ),
							'id'          => 'label_count',
							'placeholder' => 'Pending',
							'default'     => 'Pending',
						),
					),
				),
			),
			'visibility' => array(
				'label'   => __( 'Visibility', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Public', 'insert-headers-and-footers' ),
							'description' => __( 'Should the posts with this status be visible in the frontend?', 'insert-headers-and-footers' ),
							'id'          => 'public',
							'options'     => array(
								'true'  => __( 'Yes - Default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
							'default'     => 'true',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Exclude from search results', 'insert-headers-and-footers' ),
							'description' => __( 'Should the posts with this status be visible in the frontend?', 'insert-headers-and-footers' ),
							'id'          => 'exclude_from_search',
							'options'     => array(
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
								'false' => __( 'No - Default', 'insert-headers-and-footers' ),
							),
							'default'     => 'false',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Show in admin all list', 'insert-headers-and-footers' ),
							'description' => __( 'Show statuses in the edit listing of the post.', 'insert-headers-and-footers' ),
							'id'          => 'show_in_admin_all_list',
							'options'     => array(
								'true'  => __( 'Yes - Default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
							'default'     => 'true',
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Show in admin status list', 'insert-headers-and-footers' ),
							'description' => __( 'Show statuses list at the top of the edit listings. e.g. Published (12) Custom Status (2)', 'insert-headers-and-footers' ),
							'id'          => 'show_in_admin_status_list',
							'options'     => array(
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
								'false' => __( 'No - Default', 'insert-headers-and-footers' ),
							),
							'default'     => 'false',
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
		return <<<EOD
// Register Custom Status
function {$this->get_value( 'function_name' )}() {

	\$args = array(
		'label'                     => _x( '{$this->get_value( 'label' )}', 'Post Status Name', '{$this->get_value( 'text_domain' )}' ),
		'label_count'               => _n_noop( '{$this->get_value( 'label' )} (%s)',  '{$this->get_value( 'label_count' )} (%s)', '{$this->get_value( 'text_domain' )}' ), 
		'public'                    => {$this->get_value( 'public' )},
		'show_in_admin_all_list'    => {$this->get_value( 'show_in_admin_all_list' )},
		'show_in_admin_status_list' => {$this->get_value( 'show_in_admin_status_list' )},
		'exclude_from_search'       => {$this->get_value( 'exclude_from_search' )},
	);
	register_post_status( '{$this->get_value( 'post_status' )}', \$args );

}
add_action( 'init', '{$this->get_value( 'function_name' )}', 5 );
EOD;
	}

}
