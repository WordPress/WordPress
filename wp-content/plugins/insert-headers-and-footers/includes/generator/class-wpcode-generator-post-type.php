<?php
/**
 * Generate a snippet to register a new post type.
 *
 * @package WPCode
 */

/**
 * The Post Type generator.
 */
class WPCode_Generator_Post_Type extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'post-type';

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
		$this->title       = __( 'Post Type', 'insert-headers-and-footers' );
		$this->description = __( 'Use this tool to generate a custom post type for your website.', 'insert-headers-and-footers' );
	}

	/**
	 * Load the generator tabs.
	 *
	 * @return void
	 */
	protected function load_tabs() {
		$this->tabs = array(
			'info'         => array(
				'label'   => __( 'Info', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						// Column 1 fields.
						array(
							'type'    => 'description',
							'label'   => __( 'Overview', 'insert-headers-and-footers' ),
							'content' => __( 'Generate custom post types for your website using a snippet.', 'insert-headers-and-footers' ),
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
							'content' => __( 'You can add custom post types for specific items that are not blog posts, for example, if your site is about music you can have post types for artists, albums or songs.', 'insert-headers-and-footers' ),
						),
					),
				),
			),
			'general'      => array(
				'label'   => __( 'General', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Function name', 'insert-headers-and-footers' ),
							'description' => __( 'Make this unique to avoid conflicts with other snippets', 'insert-headers-and-footers' ),
							'id'          => 'function_name',
							'placeholder' => 'custom_post_type',
							'default'     => 'custom_post_type' . time(),
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
			'post_type'    => array(
				'label'   => __( 'Post Type', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Post Type Key', 'insert-headers-and-footers' ),
							'description' => __( 'Name of post type used in the code, lowercase maximum 20 characters.', 'insert-headers-and-footers' ),
							'id'          => 'post_type',
							'placeholder' => 'post_type',
							'default'     => 'post_type',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Description', 'insert-headers-and-footers' ),
							'description' => __( 'A short description of the post type.', 'insert-headers-and-footers' ),
							'id'          => 'description',
							'placeholder' => 'Post type description',
							'default'     => 'Post type description',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Name', 'insert-headers-and-footers' ),
							'description' => __( 'The singular post type name (e.g. Artist, Album, Song).', 'insert-headers-and-footers' ),
							'id'          => 'label',
							'placeholder' => 'Post Type',
							'default'     => 'Post Type',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Name (Plural)', 'insert-headers-and-footers' ),
							'description' => __( 'The post type plural name (e.g. Artists, Albums, Songs).', 'insert-headers-and-footers' ),
							'id'          => 'label_count',
							'placeholder' => 'Post Types',
							'default'     => 'Post Types',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Link To Taxonomies', 'insert-headers-and-footers' ),
							'description' => __( 'Comma-separated list of Taxonomies (e.g. post_tag, category)', 'insert-headers-and-footers' ),
							'id'          => 'taxonomies',
							'placeholder' => 'category,post_tag',
							'default'     => '',
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Hierarchical', 'insert-headers-and-footers' ),
							'description' => __( 'Hierarchical post types can have parents/children.', 'insert-headers-and-footers' ),
							'id'          => 'hierarchical',
							'default'     => 'false',
							'options'     => array(
								'true'  => __( 'Yes, like pages', 'insert-headers-and-footers' ),
								'false' => __( 'No, like posts', 'insert-headers-and-footers' ),
							),
						),
					),
				),
			),
			'labels'       => array(
				'label'   => __( 'Labels', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Menu Name', 'insert-headers-and-footers' ),
							'id'          => 'label_menu_name',
							'placeholder' => 'Post Types',
							'default'     => 'Post Types',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Admin Bar Name', 'insert-headers-and-footers' ),
							'id'          => 'label_admin_bar_name',
							'placeholder' => 'Post Type',
							'default'     => 'Post Type',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Archives', 'insert-headers-and-footers' ),
							'id'          => 'label_archives',
							'placeholder' => 'Item Archives',
							'default'     => 'Item Archives',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Attributes', 'insert-headers-and-footers' ),
							'id'          => 'label_attributes',
							'placeholder' => 'Item Attributes',
							'default'     => 'Item Attributes',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Parent Item', 'insert-headers-and-footers' ),
							'id'          => 'label_parent_item',
							'placeholder' => 'Parent Item:',
							'default'     => 'Parent Item:',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'All Items', 'insert-headers-and-footers' ),
							'id'          => 'label_all_items',
							'placeholder' => 'All Items',
							'default'     => 'All Items',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Add New Item', 'insert-headers-and-footers' ),
							'id'          => 'label_add_new_item',
							'placeholder' => 'Add New Item',
							'default'     => 'Add New Item',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Add New', 'insert-headers-and-footers' ),
							'id'          => 'label_add_new',
							'placeholder' => 'Add New',
							'default'     => 'Add New',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'New Item', 'insert-headers-and-footers' ),
							'id'          => 'label_new_item',
							'placeholder' => 'New Item',
							'default'     => 'New Item',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Edit Item', 'insert-headers-and-footers' ),
							'id'          => 'label_edit_item',
							'placeholder' => 'Edit Item',
							'default'     => 'Edit Item',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Update Item', 'insert-headers-and-footers' ),
							'id'          => 'label_update_item',
							'placeholder' => 'Update Item',
							'default'     => 'Update Item',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'View Item', 'insert-headers-and-footers' ),
							'id'          => 'label_view_item',
							'placeholder' => 'View Item',
							'default'     => 'View Item',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'View Items', 'insert-headers-and-footers' ),
							'id'          => 'label_view_items',
							'placeholder' => 'View Items',
							'default'     => 'View Items',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Search Item', 'insert-headers-and-footers' ),
							'id'          => 'label_search_item',
							'placeholder' => 'Search Item',
							'default'     => 'Search Item',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Not Found', 'insert-headers-and-footers' ),
							'id'          => 'label_not_found',
							'placeholder' => 'Not Found',
							'default'     => 'Not Found',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Not Found in Trash', 'insert-headers-and-footers' ),
							'id'          => 'label_not_found_in_trash',
							'placeholder' => 'Not Found in Trash',
							'default'     => 'Not Found in Trash',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Featured Image', 'insert-headers-and-footers' ),
							'id'          => 'label_featured_image',
							'placeholder' => 'Featured Image',
							'default'     => 'Featured Image',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Set featured image', 'insert-headers-and-footers' ),
							'id'          => 'label_set_featured_image',
							'placeholder' => 'Set featured image',
							'default'     => 'Set featured image',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Remove featured image', 'insert-headers-and-footers' ),
							'id'          => 'label_remove_featured_image',
							'placeholder' => 'Remove featured image',
							'default'     => 'Remove featured image',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Use as featured image', 'insert-headers-and-footers' ),
							'id'          => 'label_use_as_featured_image',
							'placeholder' => 'Use as featured image',
							'default'     => 'Use as featured image',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Insert into item', 'insert-headers-and-footers' ),
							'id'          => 'label_label_insert_into_item',
							'placeholder' => 'Insert into item',
							'default'     => 'Insert into item',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Uploaded to this item', 'insert-headers-and-footers' ),
							'id'          => 'label_uploaded_to_this_item',
							'placeholder' => 'Uploaded to this item',
							'default'     => 'Uploaded to this item',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Items list', 'insert-headers-and-footers' ),
							'id'          => 'label_items_list',
							'placeholder' => 'Items list',
							'default'     => 'Items list',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Items list navigation', 'insert-headers-and-footers' ),
							'id'          => 'label_items_list_navigation',
							'placeholder' => 'Items list navigation',
							'default'     => 'Items list navigation',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Filter items list', 'insert-headers-and-footers' ),
							'id'          => 'label_filter_items_list',
							'placeholder' => 'Filter items list',
							'default'     => 'Filter items list',
						),
					),
				),
			),
			'options'      => array(
				'label'   => __( 'Options', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'checkbox_list',
							'label'       => __( 'Supports', 'insert-headers-and-footers' ),
							'description' => __( 'Select which features this post type should support', 'insert-headers-and-footers' ),
							'id'          => 'supports',
							'default'     => array( 'title', 'editor' ),
							'options'     => array(
								'title'           => __( 'Title', 'insert-headers-and-footers' ),
								'editor'          => __( 'Content Editor', 'insert-headers-and-footers' ),
								'author'          => __( 'Author', 'insert-headers-and-footers' ),
								'thumbnail'       => __( 'Featured image', 'insert-headers-and-footers' ),
								'excerpt'         => __( 'Excerpt', 'insert-headers-and-footers' ),
								'trackbacks'      => __( 'Trackbacks', 'insert-headers-and-footers' ),
								'custom-fields'   => __( 'Custom Fields', 'insert-headers-and-footers' ),
								'comments'        => __( 'Comments', 'insert-headers-and-footers' ),
								'revisions'       => __( 'Revisions', 'insert-headers-and-footers' ),
								'page-attributes' => __( 'Page Attributes', 'insert-headers-and-footers' ),
								'post-formats'    => __( 'Post Formats', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Exclude From Search', 'insert-headers-and-footers' ),
							'description' => __( 'Exclude the posts of this post type from search results?', 'insert-headers-and-footers' ),
							'id'          => 'exclude_from_search',
							'default'     => 'false',
							'options'     => array(
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
								'false' => __( 'No - default', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Enable Export', 'insert-headers-and-footers' ),
							'description' => __( 'Allow exporting posts of this post type in Tools > Export.', 'insert-headers-and-footers' ),
							'id'          => 'can_export',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Enable Archives', 'insert-headers-and-footers' ),
							'description' => __( 'Enables archives for this post type, the post type key is used as default.', 'insert-headers-and-footers' ),
							'id'          => 'has_archive',
							'default'     => 'true',
							'options'     => array(
								'true'   => __( 'Yes - default', 'insert-headers-and-footers' ),
								'custom' => __( 'Yes - using custom slug', 'insert-headers-and-footers' ),
								'false'  => __( 'No', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Custom Archive Slug', 'insert-headers-and-footers' ),
							'description' => __( 'Custom archive slug (if selected above).', 'insert-headers-and-footers' ),
							'id'          => 'custom_archive_slug',
							'placeholder' => '',
							'default'     => '',
						),
					),
				),
			),
			'visibility'   => array(
				'label'   => __( 'Visibility', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Public', 'insert-headers-and-footers' ),
							// Translators: Placeholders add a link to the wp.org documentation page.
							'description' => sprintf( __( 'Should this post type be %1$svisible to authors%2$s?', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/reference/functions/register_post_type/#public" target="_blank">', '</a>' ),
							'id'          => 'public',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Show UI', 'insert-headers-and-footers' ),
							'description' => __( 'Should this post type be visible in the Admin?', 'insert-headers-and-footers' ),
							'id'          => 'show_ui',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Show in Menu?', 'insert-headers-and-footers' ),
							'description' => __( 'Should this post type be visible in the admin menu?', 'insert-headers-and-footers' ),
							'id'          => 'show_in_menu',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Menu position', 'insert-headers-and-footers' ),
							'description' => __( 'Choose the admin menu position.', 'insert-headers-and-footers' ),
							'id'          => 'menu_position',
							'default'     => '5',
							'options'     => array(
								'5'   => __( 'Below Posts (5)', 'insert-headers-and-footers' ),
								'10'  => __( 'Below Media (10)', 'insert-headers-and-footers' ),
								'20'  => __( 'Below Pages (20)', 'insert-headers-and-footers' ),
								'30'  => __( 'Below Comments (30)', 'insert-headers-and-footers' ),
								'60'  => __( 'Below First Separator (60)', 'insert-headers-and-footers' ),
								'65'  => __( 'Below Plugins (65)', 'insert-headers-and-footers' ),
								'70'  => __( 'Below Users (70)', 'insert-headers-and-footers' ),
								'75'  => __( 'Below Tools (75)', 'insert-headers-and-footers' ),
								'80'  => __( 'Below Settings (80)', 'insert-headers-and-footers' ),
								'100' => __( 'Below Second Separator (100)', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Menu Icon', 'insert-headers-and-footers' ),
							// Translators: Placeholder adds a link to the dashicons page.
							'description' => sprintf( __( 'Icon used next to the post type label in the admin menu. Use either a %1$sdashicon%2$s name or a full URL to an image file.', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/resource/dashicons/" target="_blank">', '</a>' ),
							'id'          => 'menu_icon',
							'placeholder' => '',
							'default'     => '',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Show in Admin Bar?', 'insert-headers-and-footers' ),
							'description' => __( 'Should this post type be visible in the admin bar?', 'insert-headers-and-footers' ),
							'id'          => 'show_in_admin_bar',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Show in Navigation Menus?', 'insert-headers-and-footers' ),
							'description' => __( 'Should this post type be available for use in menus (Appearance > Menus)?', 'insert-headers-and-footers' ),
							'id'          => 'show_in_nav_menus',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
					),
				),
			),
			'query'        => array(
				'label'   => __( 'Query', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Publicly Queryable', 'insert-headers-and-footers' ),
							// Translators: Placeholders add link to wp.org docs.
							'description' => sprintf( __( 'Enable frontend requests using the query variable. %1$sSee Documentation.%2$s', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/reference/functions/register_post_type/#publicly_queryable" target="_blank">', '</a>' ),
							'id'          => 'publicly_queryable',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Query variable', 'insert-headers-and-footers' ),
							// Translators: Placeholders add link to wp.org docs.
							'description' => sprintf( __( 'Key used for querying posts in the frontend. %1$sSee Documentation.%2$s', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/reference/functions/register_post_type/#query_var" target="_blank">', '</a>' ),
							'id'          => 'query_var',
							'default'     => 'true',
							'options'     => array(
								'true'   => __( 'Default (post type key)', 'insert-headers-and-footers' ),
								'custom' => __( 'Custom variable', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Custom Query Variable', 'insert-headers-and-footers' ),
							// Translators: Placeholder adds a link to the dashicons page.
							'description' => sprintf( __( 'The custom query variable to use for this post type. %1$sSee documentation%2$s.', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/reference/functions/register_post_type/#query_var" target="_blank">', '</a>' ),
							'id'          => 'query_var',
							'placeholder' => '',
							'default'     => '',
						),
					),
				),
			),
			'permalinks'   => array(
				'label'   => __( 'Permalinks', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Rewrite Permalinks', 'insert-headers-and-footers' ),
							// Translators: Placeholders add link to wp.org docs.
							'description' => sprintf( __( 'Use the default permalink structure, disable permalinks for this post type or use custom options. %1$sSee Documentation.%2$s', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/reference/functions/register_post_type/#rewrite" target="_blank">', '</a>' ),
							'id'          => 'rewrite',
							'default'     => 'true',
							'options'     => array(
								'true'   => __( 'Default (post type key)', 'insert-headers-and-footers' ),
								'false'  => __( 'Disable permalink rewrites', 'insert-headers-and-footers' ),
								'custom' => __( 'Custom permalink structure', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'URL Slug', 'insert-headers-and-footers' ),
							'description' => __( 'The slug used for this post types base. (for example: artist in www.example.com/artist/ )', 'insert-headers-and-footers' ),
							'id'          => 'rewrite_slug',
							'placeholder' => '',
							'default'     => '',
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Use URL Slug?', 'insert-headers-and-footers' ),
							'description' => __( 'Use the post type name as URL slug base?', 'insert-headers-and-footers' ),
							'id'          => 'with_front',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Use pagination?', 'insert-headers-and-footers' ),
							'description' => __( 'Allow the post type to have pagination?', 'insert-headers-and-footers' ),
							'id'          => 'pages',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Use feeds?', 'insert-headers-and-footers' ),
							'description' => __( 'Allow the post type to have feeds?', 'insert-headers-and-footers' ),
							'id'          => 'feeds',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
					),
				),
			),
			'capabilities' => array(
				'label'   => __( 'Capabilities', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Capabilities', 'insert-headers-and-footers' ),
							// Translators: Placeholders add link to wp.org docs.
							'description' => sprintf( __( 'User capabilities in relation to this post type. %1$sSee Documentation.%2$s', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/reference/functions/register_post_type/#capability_type" target="_blank">', '</a>' ),
							'id'          => 'capabilities',
							'default'     => 'true',
							'options'     => array(
								'true'   => __( 'Base capabilities - default', 'insert-headers-and-footers' ),
								'custom' => __( 'Custom Capabilities', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Base Capablities Type', 'insert-headers-and-footers' ),
							'description' => __( 'Use base capabilities from a core post type.', 'insert-headers-and-footers' ),
							'id'          => 'capability_type',
							'default'     => 'post',
							'options'     => array(
								'post' => __( 'Posts', 'insert-headers-and-footers' ),
								'page' => __( 'Pages', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 2.
					array(
						array(
							'type'    => 'description',
							'label'   => __( 'Custom Capabilities', 'insert-headers-and-footers' ),
							'content' => __( 'Use the fields below to use custom capabilities for this post type.' ),
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Read Post', 'insert-headers-and-footers' ),
							'id'          => 'read_post',
							'default'     => 'read_post',
							'placeholder' => 'read_post',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Read Private Posts', 'insert-headers-and-footers' ),
							'id'          => 'read_private_posts',
							'default'     => 'read_private_posts',
							'placeholder' => 'read_private_posts',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Publish Posts', 'insert-headers-and-footers' ),
							'id'          => 'publish_posts',
							'default'     => 'publish_posts',
							'placeholder' => 'publish_posts',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Delete Posts', 'insert-headers-and-footers' ),
							'id'          => 'delete_post',
							'default'     => 'delete_post',
							'placeholder' => 'delete_post',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Edit Post', 'insert-headers-and-footers' ),
							'id'          => 'edit_post',
							'default'     => 'edit_post',
							'placeholder' => 'edit_post',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Edit Posts', 'insert-headers-and-footers' ),
							'id'          => 'edit_posts',
							'default'     => 'edit_posts',
							'placeholder' => 'edit_posts',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Edit Others Posts', 'insert-headers-and-footers' ),
							'id'          => 'edit_others_posts',
							'default'     => 'edit_others_posts',
							'placeholder' => 'edit_others_posts',
						),
					),
				),
			),
			'rest_api'     => array(
				'label'   => __( 'Rest API', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Show in Rest API?', 'insert-headers-and-footers' ),
							// Translators: Placeholders add link to wp.org docs.
							'description' => sprintf( __( 'Add the post type to the WordPress wp-json API. %1$sSee Documentation.%2$s', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/reference/functions/register_post_type/#show_in_rest" target="_blank">', '</a>' ),
							'id'          => 'show_in_rest',
							'default'     => 'false',
							'options'     => array(
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
								'false' => __( 'No - default', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Rest Base', 'insert-headers-and-footers' ),
							// Translators: Placeholders add link to wp.org docs.
							'description' => sprintf( __( 'The base slug that this post type will use in the REST API. %1$sSee Documentation.%2$s', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/reference/functions/register_post_type/#rest_base" target="_blank">', '</a>' ),
							'id'          => 'rest_base',
							'default'     => '',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Rest Controller Class', 'insert-headers-and-footers' ),
							// Translators: Placeholders add link to wp.org docs.
							'description' => sprintf( __( 'The name of a custom Rest Controller class instead of WP_REST_Posts_Controller. %1$sSee Documentation.%2$s', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/reference/functions/register_post_type/#rest_controller_class" target="_blank">', '</a>' ),
							'id'          => 'rest_controller_class',
							'default'     => '',
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
		$has_archive = $this->get_value( 'has_archive' );
		if ( 'custom' === $has_archive ) {
			$has_archive = "'{$this->get_value( 'custom_archive_slug' )}'";
		}

		$rewrite         = $this->get_value( 'rewrite' );
		$rewrite_options = '';
		if ( 'true' === $rewrite ) {
			$rewrite = '';
		} elseif ( 'custom' === $rewrite ) {
			$rewrite         = "\t\t'rewrite'               => \$rewrite_options,\n";
			$rewrite_options = "
	\$rewrite_options = array(
		'slug'       => '{$this->get_value('rewrite_slug')}',
		'with_front' => {$this->get_value( 'with_front')},
		'pages'      => {$this->get_value( 'pages')},
		'feeds'      => {$this->get_value( 'feeds')},
 	); 
			";
		} else {
			$rewrite = "\t\t'rewrite'               => $rewrite,\n";
		}

		$custom_capabilities = '';
		$capabilities        = $this->get_value( 'capabilities' );

		if ( 'custom' === $capabilities ) {
			$custom_capabilities = "
	\$capabilities = array(
		'read_post'          => '{$this->get_value( 'read_post')}',
		'read_private_posts' => '{$this->get_value( 'read_private_posts')}',
		'publish_posts'      => '{$this->get_value( 'publish_posts')}',
		'delete_post'        => '{$this->get_value( 'delete_post')}',
		'edit_post'          => '{$this->get_value( 'edit_post')}',
		'edit_posts'         => '{$this->get_value( 'edit_posts')}',
		'edit_others_posts'  => '{$this->get_value( 'edit_others_posts')}',
	);
			";
			$capabilities        = "'capabilities'          => \$capabilities,\n";
		} else {
			$capabilities = "'capability_type'       => '{$this->get_value('capability_type')}',\n";
		}

		return <<<EOD
// Register Custom Post Type
function {$this->get_value( 'function_name' )}() {

	\$labels = array(
		'name'                  => _x( '{$this->get_value( 'label_count' )}', 'Post Type General Name', '{$this->get_value( 'text_domain' )}' ),
		'singular_name'         => _x( '{$this->get_value( 'label' )}', 'Post Type Singular Name', '{$this->get_value( 'text_domain' )}' ),
		'menu_name'             => __( '{$this->get_value( 'label_menu_name' )}', '{$this->get_value( 'text_domain' )}' ),
		'name_admin_bar'        => __( '{$this->get_value( 'label_admin_bar_name' )}', '{$this->get_value( 'text_domain' )}' ),
		'archives'              => __( '{$this->get_value( 'label_archives' )}', '{$this->get_value( 'text_domain' )}' ),
		'attributes'            => __( '{$this->get_value( 'label_attributes' )}', '{$this->get_value( 'text_domain' )}' ),
		'parent_item_colon'     => __( '{$this->get_value( 'label_parent_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'all_items'             => __( '{$this->get_value( 'label_all_items' )}', '{$this->get_value( 'text_domain' )}' ),
		'add_new_item'          => __( '{$this->get_value( 'label_add_new_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'add_new'               => __( '{$this->get_value( 'label_add_new' )}', '{$this->get_value( 'text_domain' )}' ),
		'new_item'              => __( '{$this->get_value( 'label_new_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'edit_item'             => __( '{$this->get_value( 'label_edit_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'update_item'           => __( '{$this->get_value( 'label_update_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'view_item'             => __( '{$this->get_value( 'label_view_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'view_items'            => __( '{$this->get_value( 'label_view_items' )}', '{$this->get_value( 'text_domain' )}' ),
		'search_items'          => __( '{$this->get_value( 'label_search_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'not_found'             => __( '{$this->get_value( 'label_not_found' )}', '{$this->get_value( 'text_domain' )}' ),
		'not_found_in_trash'    => __( '{$this->get_value( 'label_not_found_in_trash' )}', '{$this->get_value( 'text_domain' )}' ),
		'featured_image'        => __( '{$this->get_value( 'label_featured_image' )}', '{$this->get_value( 'text_domain' )}' ),
		'set_featured_image'    => __( '{$this->get_value( 'label_set_featured_image' )}', '{$this->get_value( 'text_domain' )}' ),
		'remove_featured_image' => __( '{$this->get_value( 'label_remove_featured_image' )}', '{$this->get_value( 'text_domain' )}' ),
		'use_featured_image'    => __( '{$this->get_value( 'label_use_as_featured_image' )}', '{$this->get_value( 'text_domain' )}' ),
		'insert_into_item'      => __( '{$this->get_value( 'label_label_insert_into_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'uploaded_to_this_item' => __( '{$this->get_value( 'label_uploaded_to_this_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'items_list'            => __( '{$this->get_value( 'label_items_list' )}', '{$this->get_value( 'text_domain' )}' ),
		'items_list_navigation' => __( '{$this->get_value( 'label_items_list_navigation' )}', '{$this->get_value( 'text_domain' )}' ),
		'filter_items_list'     => __( '{$this->get_value( 'label_filter_items_list' )}', '{$this->get_value( 'text_domain' )}' ),
	);
	$custom_capabilities
	$rewrite_options
	\$args = array(
		'label'                 => __( '{$this->get_value( 'label' )}', '{$this->get_value( 'text_domain' )}' ),
		'description'           => __( '{$this->get_value( 'description' )}', '{$this->get_value( 'text_domain' )}' ),
		'labels'                => \$labels,
		'supports'              => {$this->get_array_value( 'supports' )},
		'taxonomies'            => {$this->get_value_comma_separated( 'taxonomies' )},
		'hierarchical'          => {$this->get_value( 'hierarchical' )},
		'public'                => {$this->get_value( 'public' )},
		'show_ui'               => {$this->get_value( 'show_ui' )},
		'show_in_menu'          => {$this->get_value( 'show_in_menu' )},
		'menu_position'         => {$this->get_value( 'menu_position' )},
		'show_in_admin_bar'     => {$this->get_value( 'show_in_admin_bar' )},
		'show_in_nav_menus'     => {$this->get_value( 'show_in_nav_menus' )},
		'can_export'            => {$this->get_value( 'can_export' )},
		'has_archive'           => $has_archive,
		'exclude_from_search'   => {$this->get_value( 'exclude_from_search' )},
		'publicly_queryable'    => {$this->get_value( 'publicly_queryable' )},
		$capabilities$rewrite{$this->get_optional_value( 'menu_icon', true )}{$this->get_optional_value( 'show_in_rest' )}{$this->get_optional_value( 'rest_base', true )}{$this->get_optional_value( 'rest_controller_class', true )}\t);
	register_post_type( '{$this->get_value( 'post_type' )}', \$args );

}
add_action( 'init', '{$this->get_value( 'function_name' )}', 0 );
EOD;
	}

}
