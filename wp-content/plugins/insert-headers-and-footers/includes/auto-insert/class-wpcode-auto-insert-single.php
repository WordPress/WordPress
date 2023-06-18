<?php
/**
 * Class to auto-insert snippets on single posts.
 *
 * @package wpcode
 */

/**
 * Class WPCode_Auto_Insert_Single.
 */
class WPCode_Auto_Insert_Single extends WPCode_Auto_Insert_Type {

	/**
	 * The category of this type.
	 *
	 * @var string
	 */
	public $category = 'page';

	/**
	 * Used to make sure we only output the before post code once.
	 *
	 * @var bool
	 */
	private $did_before_post_output = false;

	/**
	 * Load the available options and labels.
	 *
	 * @return void
	 */
	public function init() {
		$this->label     = __( 'Page, Post, Custom Post Type', 'insert-headers-and-footers' );
		$this->locations = array(
			'before_post'      => array(
				'label'       => esc_html__( 'Insert Before Post', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Insert snippet at the beginning of a post.', 'insert-headers-and-footers' ),
			),
			'after_post'       => array(
				'label'       => esc_html__( 'Insert After Post', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Insert snippet at the end of a post.', 'insert-headers-and-footers' ),
			),
			'before_content'   => array(
				'label'       => esc_html__( 'Insert Before Content', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Insert snippet at the beginning of the post content.', 'insert-headers-and-footers' ),
			),
			'after_content'    => array(
				'label'       => esc_html__( 'Insert After Content', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Insert snippet at the end of the post content.', 'insert-headers-and-footers' ),
			),
			'before_paragraph' => array(
				'label'       => esc_html__( 'Insert Before Paragraph', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Insert snippet before paragraph # of the post content.', 'insert-headers-and-footers' ),
			),
			'after_paragraph'  => array(
				'label'       => esc_html__( 'Insert After Paragraph', 'insert-headers-and-footers' ),
				'description' => esc_html__( 'Insert snippet after paragraph # of the post content.', 'insert-headers-and-footers' ),
			),
		);
	}

	/**
	 * Checks if we are on a singular page and we should be executing hooks.
	 *
	 * @return bool
	 */
	public function conditions() {
		return is_singular();
	}

	/**
	 * Add hooks specific to single posts.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'the_post', array( $this, 'insert_before_post' ) );
		add_filter( 'render_block_core/template-part', array( $this, 'insert_before_post_fse' ), 15, 2 );
		add_action( 'the_content', array( $this, 'insert_after_post' ) );
		add_filter( 'the_content', array( $this, 'insert_before_content' ) );
		add_filter( 'the_content', array( $this, 'insert_after_content' ) );
		add_filter( 'the_content', array( $this, 'insert_after_before_paragraph' ) );
	}

	/**
	 * Insert snippet before the post.
	 *
	 * @param WP_Post $post_object The post object being loaded.
	 *
	 * @return void
	 */
	public function insert_before_post( $post_object ) {
		if ( ! did_action( 'get_header' ) || get_the_ID() !== $post_object->ID || $this->did_before_post_output ) {
			return;
		}
		$this->output_location( 'before_post' );
		$this->did_before_post_output = true;
	}

	/**
	 * In FSE themes there's no "get_header" action to check for, so we hook after the core template-part header block.
	 *
	 * @param string $block_content The normal block HTML that would be sent to the screen.
	 * @param array  $block An array of data about the block, and the way the user configured it.
	 *
	 * @return string
	 */
	public function insert_before_post_fse( $block_content, $block ) {
		// If the get_header action ran we use the classic output method above.
		if ( did_action( 'get_header' ) ) {
			return $block_content;
		}
		if ( ! isset( $block['attrs']['slug'] ) || 'header' !== $block['attrs']['slug'] ) {
			return $block_content;
		}
		$before_post = '';
		$snippets    = $this->get_snippets_for_location( 'before_post' );
		foreach ( $snippets as $snippet ) {
			$before_post .= wpcode()->execute->get_snippet_output( $snippet );
		}

		return $block_content . $before_post;
	}

	/**
	 * Insert snippet output after the content.
	 *
	 * @param string $content The content of the post.
	 *
	 * @return string
	 */
	public function insert_after_content( $content ) {
		$snippets = $this->get_snippets_for_location( 'after_content' );
		foreach ( $snippets as $snippet ) {
			$content .= wpcode()->execute->get_snippet_output( $snippet );
		}

		return $content;
	}

	/**
	 * Insert snippet after the post
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	public function insert_after_post( $content ) {
		$snippets = $this->get_snippets_for_location( 'after_post' );
		foreach ( $snippets as $snippet ) {
			$content .= wpcode()->execute->get_snippet_output( $snippet );
		}

		return $content;
	}

	/**
	 * Insert snippets before the content.
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	public function insert_before_content( $content ) {
		$snippets        = $this->get_snippets_for_location( 'before_content' );
		$snippets_output = '';
		foreach ( $snippets as $snippet ) {
			$snippets_output .= wpcode()->execute->get_snippet_output( $snippet );
		}

		return $snippets_output . $content;
	}

	/**
	 * Insert content before or after paragraphs based on settings.
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	public function insert_after_before_paragraph( $content ) {

		$snippets = $this->get_snippets_for_location( 'before_paragraph' );
		foreach ( $snippets as $snippet ) {
			$auto_insert_number = $snippet->get_auto_insert_number();
			$auto_insert_number = empty( $auto_insert_number ) ? 1 : absint( $auto_insert_number );
			$snippet_output     = wpcode()->execute->get_snippet_output( $snippet );
			$content            = $this->insert_between_paragraphs( $snippet_output, $auto_insert_number, $content, 'before' );
		}

		$snippets = $this->get_snippets_for_location( 'after_paragraph' );
		foreach ( $snippets as $snippet ) {
			$auto_insert_number = $snippet->get_auto_insert_number();
			$auto_insert_number = empty( $auto_insert_number ) ? 1 : absint( $auto_insert_number );
			$snippet_output     = wpcode()->execute->get_snippet_output( $snippet );
			$content            = $this->insert_between_paragraphs( $snippet_output, $auto_insert_number, $content, 'after' );
		}

		return $content;
	}


	/**
	 * Insert snippet code before or after paragraphs in a post.
	 *
	 * @param string $content_to_insert The content to insert (snippet code output).
	 * @param int    $p_number The paragraph number.
	 * @param string $content_to_add_to The content in which the content should be added.
	 * @param string $before_or_after Add it before or after the paragraph.
	 *
	 * @return string
	 */
	public function insert_between_paragraphs( $content_to_insert, $p_number, $content_to_add_to, $before_or_after = 'after' ) {
		if ( 'before' === $before_or_after ) {
			preg_match_all( '/<p(.*?)>/', $content_to_add_to, $matches );
		} else {
			preg_match_all( '/<\/p>/', $content_to_add_to, $matches );
		}
		$paragraphs = $matches[0];

		// We don't have enough paragraphs to add the snippet.
		if ( count( $paragraphs ) < $p_number ) {
			return $content_to_add_to;
		}

		$p_number = -- $p_number;
		$offset   = 0;
		foreach ( $paragraphs as $p_index => $p ) {
			$position = strpos( $content_to_add_to, $p, $offset );
			if ( $p_index === $p_number ) {
				if ( 'before' === $before_or_after ) {
					$content_to_add_to = substr( $content_to_add_to, 0, $position ) . $content_to_insert . substr( $content_to_add_to, $position );
				} else {
					$content_to_add_to = substr( $content_to_add_to, 0, $position + 4 ) . $content_to_insert . substr( $content_to_add_to, $position + 4 );
				}
				break;
			} else {
				$offset = $position + 1;
			}
		}

		return $content_to_add_to;
	}
}

new WPCode_Auto_Insert_Single();
