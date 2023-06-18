<?php
/**
 * Generate a snippet using WP_Query
 *
 * @package WPCode
 */

/**
 * WPCode_Generator_Query class.
 */
class WPCode_Generator_Query extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'query';

	/**
	 * The categories for this generator.
	 *
	 * @var string[]
	 */
	public $categories = array(
		'query',
	);

	/**
	 * This snippet should not be auto-inserted by default.
	 *
	 * @var bool
	 */
	public $auto_insert = false;

	/**
	 * Set the translatable strings.
	 *
	 * @return void
	 */
	protected function set_strings() {
		$this->title       = __( 'WP_Query', 'insert-headers-and-footers' );
		$this->description = __( 'Generate a snippet using WP_Query to load posts from your website.', 'insert-headers-and-footers' );
	}

	/**
	 * Get a list of available post types for autocomplete.
	 *
	 * @return array
	 */
	public function get_autocomplete_post_types() {
		return array_keys( get_post_types() );
	}

	/**
	 * Get a list of available post statuses for autocomplete.
	 *
	 * @return array
	 */
	public function get_autocomplete_post_statuses() {
		return array_keys( get_post_statuses() );
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
							'content' => sprintf(
							// Translators: Placeholders add links to the wordpress.org references.
								__( 'This generator makes it easy for you to create custom queries using %1$sWP_Query%2$s which you can then extend to display posts or similar.', 'insert-headers-and-footers' ),
								'<a href="https://developer.wordpress.org/reference/classes/wp_query/" target="_blank">',
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
							'content' => __( 'You can use this generator to get quickly started with a query for all the posts of an author and display them using the shortcode functionality of WPCode or automatically displaying the posts using the auto-insert option.', 'insert-headers-and-footers' ),
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
							'label'       => __( 'Query variable name', 'insert-headers-and-footers' ),
							'description' => __( 'If you want to use something more specific. The leading $ will be automatically added.', 'insert-headers-and-footers' ),
							'id'          => 'var_name',
							'placeholder' => '$query',
							'default'     => 'query',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Include loop', 'insert-headers-and-footers' ),
							'description' => __( 'Select yes if you want to include an empty loop of the results that you can fill in for output.', 'insert-headers-and-footers' ),
							'id'          => 'loop',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
					),
				),
			),
			'post'       => array(
				'label'   => __( 'IDs & Parents', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'            => 'text',
							'label'           => __( 'Post ID(s)', 'insert-headers-and-footers' ),
							'description'     => __( 'Query a specific post ID or comma-separated list of ids. Cannot be combined with "Post ID not in" below.', 'insert-headers-and-footers' ),
							'id'              => 'post__in',
							'default'         => '',
							'comma-separated' => true,
						),
						array(
							'type'            => 'text',
							'label'           => __( 'Post ID not in', 'insert-headers-and-footers' ),
							'description'     => __( 'Post ids to exclude from this query. Cannot be combined with "Post ID(s)" above.', 'insert-headers-and-footers' ),
							'id'              => 'post__not_in',
							'default'         => '',
							'comma-separated' => true,
						),
					),
					// Column 2.
					array(
						array(
							'type'            => 'text',
							'label'           => __( 'Post parent ID(s)', 'insert-headers-and-footers' ),
							'description'     => __( 'Comma-separated list of post parent ids if the post type is hierarchical (like pages).', 'insert-headers-and-footers' ),
							'id'              => 'post_parent__in',
							'default'         => '',
							'comma-separated' => true,
						),
						array(
							'type'            => 'text',
							'label'           => __( 'Post parent not in', 'insert-headers-and-footers' ),
							'description'     => __( 'Comma-separated list of post parent ids to exclude.', 'insert-headers-and-footers' ),
							'id'              => 'post_parent__not_in',
							'default'         => '',
							'comma-separated' => true,
						),
					),
					// Column 3.
					array(
						array(
							'type'            => 'text',
							'label'           => __( 'Post slugs', 'insert-headers-and-footers' ),
							'description'     => __( 'Comma-separated list of post slugs to query by.', 'insert-headers-and-footers' ),
							'id'              => 'post_name__in',
							'default'         => '',
							'comma-separated' => true,
						),
					),
				),
			),
			'status'     => array(
				'label'   => __( 'Type & Status', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'         => 'text',
							'label'        => __( 'Post type', 'insert-headers-and-footers' ),
							'description'  => __( 'Post type to query by, start typing to get suggestions.', 'insert-headers-and-footers' ),
							'id'           => 'post_type',
							'default'      => 'post',
							'autocomplete' => $this->get_autocomplete_post_types(),
						),
					),
					// Column 2.
					array(
						array(
							'type'         => 'text',
							'label'        => __( 'Post status', 'insert-headers-and-footers' ),
							'description'  => __( 'Post status to query by.', 'insert-headers-and-footers' ),
							'id'           => 'post_status',
							'default'      => 'publish',
							'autocomplete' => $this->get_autocomplete_post_statuses(),
						),
					),
				),
			),
			'author'     => array(
				'label'   => __( 'Author', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Author ID(s)', 'insert-headers-and-footers' ),
							'description' => __( 'Author ID or comma-separated list of ids.', 'insert-headers-and-footers' ),
							'id'          => 'author',
							'default'     => '',
						),
						array(
							'type'            => 'text',
							'label'           => __( 'Author not in', 'insert-headers-and-footers' ),
							'description'     => __( 'Comma-separated list of author ids to exclude from the query.', 'insert-headers-and-footers' ),
							'id'              => 'author__not_in',
							'default'         => '',
							'comma-separated' => true,
						),
					),
					// Column 2.
					array(

						array(
							'type'        => 'text',
							'label'       => __( 'Author name', 'insert-headers-and-footers' ),
							'description' => __( 'Use the "user_nicename" parameter to query by author.', 'insert-headers-and-footers' ),
							'id'          => 'author_name',
							'default'     => '',
						),
					),
				),
			),
			'search'     => array(
				'label'   => __( 'Search', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Search term', 'insert-headers-and-footers' ),
							'description' => __( 'Search for posts by this search term.', 'insert-headers-and-footers' ),
							'id'          => 's',
							'default'     => '',
						),
					),
				),
			),
			'order'      => array(
				'label'   => __( 'Order', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'    => 'select',
							'label'   => __( 'Results Order', 'insert-headers-and-footers' ),
							'id'      => 'order',
							'default' => 'DESC',
							'options' => array(
								'DESC' => __( 'Descending order (3, 2, 1; c, b, a)', 'insert-headers-and-footers' ),
								'ASC'  => __( 'Ascending order (1, 2, 3; a, b, c)', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 2.
					array(
						array(
							'type'    => 'select',
							'label'   => __( 'Order by', 'insert-headers-and-footers' ),
							'id'      => 'orderby',
							'default' => 'date',
							'options' => array(
								'none'            => __( 'No order (none)', 'insert-headers-and-footers' ),
								'ID'              => __( 'ID', 'insert-headers-and-footers' ),
								'author'          => __( 'Author', 'insert-headers-and-footers' ),
								'title'           => __( 'Title', 'insert-headers-and-footers' ),
								'name'            => __( 'Slug (name)', 'insert-headers-and-footers' ),
								'type'            => __( 'Post type (type)', 'insert-headers-and-footers' ),
								'date'            => __( 'Date (default)', 'insert-headers-and-footers' ),
								'modified'        => __( 'Modified date', 'insert-headers-and-footers' ),
								'parent'          => __( 'Parent id', 'insert-headers-and-footers' ),
								'rand'            => __( 'Random', 'insert-headers-and-footers' ),
								'comment_count'   => __( 'Comment count', 'insert-headers-and-footers' ),
								'relevance'       => __( 'Relevance (for search)', 'insert-headers-and-footers' ),
								'menu_order'      => __( 'Page Order (menu_order)', 'insert-headers-and-footers' ),
								//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
								'meta_value'      => __( 'Meta value', 'insert-headers-and-footers' ),
								'meta_value_num'  => __( 'Numerical meta value (meta_value_num)', 'insert-headers-and-footers' ),
								'post__in'        => __( 'Order of ids in post__in', 'insert-headers-and-footers' ),
								'post_name__in'   => __( 'Order of names in post_name__in', 'insert-headers-and-footers' ),
								'post_parent__in' => __( 'Order of ids in post_parent__in', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Meta Key', 'insert-headers-and-footers' ),
							'description' => __( 'Meta key to use if you choose to order by meta value.', 'insert-headers-and-footers' ),
							'id'          => 'meta_key',
							'default'     => '',
						),
					),
					// Column 3.
					array(),
				),
			),
			'pagination' => array(
				'label'   => __( 'Pagination', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Use Pagination', 'insert-headers-and-footers' ),
							'id'          => 'nopaging',
							'default'     => 'false',
							'description' => __( 'Choose no to display all posts (not recommended).', 'insert-headers-and-footers' ),
							'options'     => array(
								'true'  => __( 'No', 'insert-headers-and-footers' ),
								'false' => __( 'Yes (default)', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Page number', 'insert-headers-and-footers' ),
							'description' => __( 'Which page to show.', 'insert-headers-and-footers' ),
							'id'          => 'paged',
							'default'     => '',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Posts per page', 'insert-headers-and-footers' ),
							'description' => __( 'How many posts should be displayed per page.', 'insert-headers-and-footers' ),
							'id'          => 'posts_per_page',
							'default'     => '',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Offset', 'insert-headers-and-footers' ),
							'description' => __( 'Number of posts to skip.', 'insert-headers-and-footers' ),
							'id'          => 'offset',
							'default'     => '',
						),
					),
					// Column 3.
					array(
						array(
							'type'    => 'select',
							'label'   => __( 'Ignore sticky posts', 'insert-headers-and-footers' ),
							'id'      => 'ignore_sticky_posts',
							'default' => 'false',
							'options' => array(
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
								'false' => __( 'No (default)', 'insert-headers-and-footers' ),
							),
						),
					),
				),
			),
			'taxonomy'   => array(
				'label'   => __( 'Taxonomy', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Taxonomy', 'insert-headers-and-footers' ),
							'description' => __( 'Taxonomy slug that you want to query by.', 'insert-headers-and-footers' ),
							'id'          => 'taxonomy',
							'name'        => 'taxonomy[]',
							'default'     => '',
							'repeater'    => 'tax_query',
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Field', 'insert-headers-and-footers' ),
							'description' => __( 'Select taxonomy term by.', 'insert-headers-and-footers' ),
							'id'          => 'field',
							'name'        => 'field[]',
							'repeater'    => 'tax_query',
							'default'     => 'term_id',
							'options'     => array(
								'term_id'          => __( 'Term ID (default', 'insert-headers-and-footers' ),
								'name'             => __( 'Term Name', 'insert-headers-and-footers' ),
								'slug'             => __( 'Term Slug', 'insert-headers-and-footers' ),
								'term_taxonomy_id' => __( 'Term Taxonomy ID', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'            => 'text',
							'label'           => __( 'Terms', 'insert-headers-and-footers' ),
							'description'     => __( 'Comma-separated list of terms to query by.', 'insert-headers-and-footers' ),
							'id'              => 'terms',
							'name'            => 'terms[]',
							'default'         => '',
							'repeater'        => 'tax_query',
							'comma-separated' => true,
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Include Children', 'insert-headers-and-footers' ),
							'id'          => 'include_children',
							'name'        => 'include_children[]',
							'default'     => 'true',
							'description' => __( 'Whether or not to include children for hierarchical taxonomies.', 'insert-headers-and-footers' ),
							'options'     => array(
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
							'repeater'    => 'tax_query',
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Operator', 'insert-headers-and-footers' ),
							'id'          => 'operator',
							'name'        => 'operator[]',
							'default'     => 'IN',
							'description' => __( 'Operator to test relation by.', 'insert-headers-and-footers' ),
							'options'     => array(
								'IN'         => 'IN',
								'NOT IN'     => 'NOT IN',
								'AND'        => 'AND',
								'EXISTS'     => 'EXISTS',
								'NOT EXISTS' => 'NOT EXISTS',
							),
							'repeater'    => 'tax_query',
						),
						array(
							'type' => 'spacer',
						),
					),
					// Column 3.
					array(
						array(
							'type'    => 'description',
							'label'   => __( 'Add another taxonomy', 'insert-headers-and-footers' ),
							'content' => __( 'Use the "Add Taxonomy" button below to query multiple taxonomies.', 'insert-headers-and-footers' ),
						),
						array(
							'type'        => 'repeater_button',
							'button_text' => __( 'Add Taxonomy', 'insert-headers-and-footers' ),
							'id'          => 'tax_query', // Repeater to repeat when clicked.
						),
						array(
							'type'    => 'select',
							'label'   => __( 'Tax Relation', 'insert-headers-and-footers' ),
							'id'      => 'relation',
							'default' => 'AND',
							'options' => array(
								'AND' => __( 'AND (default)', 'insert-headers-and-footers' ),
								'OR'  => __( 'OR', 'insert-headers-and-footers' ),
							),
						),
					),
				),
			),
			'meta'       => array(
				'label'   => __( 'Custom Fields', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Meta Key', 'insert-headers-and-footers' ),
							'description' => __( 'The key of the custom field.', 'insert-headers-and-footers' ),
							'id'          => 'key',
							'name'        => 'key[]',
							'default'     => '',
							'repeater'    => 'meta_query',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Meta Value', 'insert-headers-and-footers' ),
							'description' => __( 'Value to query the meta by.', 'insert-headers-and-footers' ),
							'id'          => 'value',
							'name'        => 'value[]',
							'default'     => '',
							'repeater'    => 'meta_query',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Compare', 'insert-headers-and-footers' ),
							'description' => __( 'How to compare the value for querying by meta.', 'insert-headers-and-footers' ),
							'id'          => 'compare',
							'name'        => 'compare[]',
							'repeater'    => 'meta_query',
							'default'     => '=',
							'options'     => array(
								'='           => '=',
								'!='          => '!=',
								'>'           => '>',
								'>='          => '>=',
								'<'           => '<',
								'<='          => '<=',
								'LIKE'        => 'LIKE',
								'NOT LIKE'    => 'NOT LIKE',
								'IN'          => 'IN',
								'NOT IN'      => 'NOT IN',
								'BETWEEN'     => 'BETWEEN',
								'NOT BETWEEN' => 'NOT BETWEEN',
								'EXISTS'      => 'EXISTS',
								'NOT EXISTS'  => 'NOT EXISTS',
								'REGEXP'      => 'REGEXP',
								'NOT REGEXP'  => 'NOT REGEXP',
								'RLIKE'       => 'RLIKE',
							),
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Type', 'insert-headers-and-footers' ),
							'description' => __( 'Type of custom field.', 'insert-headers-and-footers' ),
							'id'          => 'meta_type',
							'name'        => 'meta_type[]',
							'repeater'    => 'meta_query',
							'default'     => 'CHAR',
							'options'     => array(
								'NUMERIC'  => 'NUMERIC',
								'BINARY'   => 'BINARY',
								'CHAR'     => 'CHAR',
								'DATE'     => 'DATE',
								'DATETIME' => 'DATETIME',
								'DECIMAL'  => 'DECIMAL',
								'SIGNED'   => 'SIGNED',
								'TIME'     => 'TIME',
								'UNSIGNED' => 'UNSIGNED',
							),
						),
					),
					// Column 3.
					array(
						array(
							'type'    => 'description',
							'label'   => __( 'Add another meta query', 'insert-headers-and-footers' ),
							'content' => __( 'Use the "Add Meta" button below to use multiple meta queries.', 'insert-headers-and-footers' ),
						),
						array(
							'type'        => 'repeater_button',
							'button_text' => __( 'Add Meta', 'insert-headers-and-footers' ),
							'id'          => 'meta_query', // Repeater to repeat when clicked.
						),
						array(
							'type'    => 'select',
							'label'   => __( 'Relation', 'insert-headers-and-footers' ),
							'id'      => 'meta_relation',
							'default' => 'AND',
							'options' => array(
								'AND' => __( 'AND (default)', 'insert-headers-and-footers' ),
								'OR'  => __( 'OR', 'insert-headers-and-footers' ),
							),
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

		$query_variable = '$' . str_replace( '$', '', $this->get_value( 'var_name' ) );
		$loop_code      = '';
		if ( 'true' === $this->get_value( 'loop' ) ) {
			$loop_code = "
if ( {$query_variable}->have_posts() ) {
	while ( {$query_variable}->have_posts() ) {
		{$query_variable}->the_post();
		// Add you post code here.
	}
} else {
	// Display a no posts found message here.
}

// Reset post data.
wp_reset_postdata();
			";
		}

		$optional_values = array(
			'post__in'            => false,
			'post__not_in'        => false,
			'post_parent__in'     => false,
			'post_parent__not_in' => false,
			'post_name__in'       => true,
			'post_type'           => true,
			'post_status'         => true,
			'author'              => true,
			'author__not_in'      => false,
			'author_name'         => true,
			's'                   => true,
			'order'               => true,
			'orderby'             => true,
			//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_key'            => true,
			'nopaging'            => false,
			'paged'               => false,
			'posts_per_page'      => false,
			'offset'              => false,
			'ignore_sticky_posts' => false,
		);

		$args = '';
		foreach ( $optional_values as $optional_value => $quotes ) {
			$args .= $this->get_optional_value( $optional_value, $quotes );
		}

		$tax_query        = '';
		$taxonomies       = $this->get_value( 'taxonomy' );
		$fields           = $this->get_value( 'field' );
		$terms            = $this->get_value( 'terms' );
		$include_children = $this->get_value( 'include_children' );
		$operator         = $this->get_value( 'operator' );

		if ( ! empty( $taxonomies ) ) {
			$tax_query_arrays = array();
			foreach ( $taxonomies as $key => $taxonomy ) {
				if ( empty( $taxonomy ) ) {
					continue;
				}
				$params = array(
					$this->get_optional_value_code( $fields[ $key ], $this->get_default_value( 'field' ), 'field', true ),
					$this->get_optional_value_code( $terms[ $key ], $this->get_default_value( 'terms' ), 'terms', true, true ),
					$this->get_optional_value_code( $include_children[ $key ], $this->get_default_value( 'include_children' ), 'include_children' ),
					$this->get_optional_value_code( $operator[ $key ], $this->get_default_value( 'operator' ), 'operator', true ),
				);
				$params = array_filter( $params );
				$params = str_replace( "\n", '', $params );
				$params = implode( "\n\t\t", $params );
				if ( ! empty( $params ) ) {
					$params = "\n\t\t" . $params;
				}
				$tax_query_arrays[] = "\n\t\t\tarray(
				'taxonomy'              => '$taxonomy',$params
			),";
			}
			if ( ! empty( $tax_query_arrays ) ) {
				$tax_query = "\t\t'tax_query' => array(";
				if ( count( $tax_query_arrays ) > 1 ) {
					$optional_relation = $this->get_optional_value( 'relation', true );
					if ( ! empty( $optional_relation ) ) {
						$tax_query .= "\n\t" . str_replace( "\n", '', $optional_relation );
					}
				}

				$tax_query .= implode( '', $tax_query_arrays );
				$tax_query .= '
		),';
			}
		}

		$meta_query    = '';
		$meta_keys     = $this->get_value( 'key' );
		$meta_values   = $this->get_value( 'value' );
		$meta_compares = $this->get_value( 'compare' );
		$meta_types    = $this->get_value( 'meta_type' );

		if ( ! empty( $meta_keys ) ) {
			$meta_query_arrays = array();
			foreach ( $meta_keys as $key => $meta_key ) {
				if ( empty( $meta_key ) ) {
					continue;
				}
				$params = array(
					$this->get_optional_value_code( $meta_values[ $key ], $this->get_default_value( 'value' ), 'value', true ),
					$this->get_optional_value_code( $meta_compares[ $key ], $this->get_default_value( 'compare' ), 'compare', true ),
					$this->get_optional_value_code( $meta_types[ $key ], $this->get_default_value( 'meta_type' ), 'type', true ),
				);
				$params = array_filter( $params );
				$params = str_replace( "\n", '', $params );
				$params = implode( "\n\t\t", $params );
				if ( ! empty( $params ) ) {
					$params = "\n\t\t" . $params;
				}
				$meta_query_arrays[] = "\n\t\t\tarray(
				'key'                   => '$meta_key',$params
			),";
			}

			if ( ! empty( $meta_query_arrays ) ) {
				$meta_query = "\n\t\t'meta_query' => array(";
				if ( count( $meta_query_arrays ) > 1 ) {
					$optional_relation = $this->get_optional_value( 'meta_relation', true, 'relation' );
					if ( ! empty( $optional_relation ) ) {
						$meta_query .= "\n\t" . str_replace( "\n", '', $optional_relation );
					}
				}

				$meta_query .= implode( '', $meta_query_arrays );
				$meta_query .= '
		),';
			}
		}

		$args = "\n" . $args;

		return <<<EOD
// Query Posts.

// Query arguments.
\$query_args = array($args$tax_query$meta_query
);

// Run the query.
$query_variable = new WP_Query( \$query_args );
$loop_code

EOD;
	}

}
