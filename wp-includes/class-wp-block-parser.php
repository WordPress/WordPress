<?php
/**
 * Block Serialization Parser
 *
 * @package WordPress
 */

/**
 * Class WP_Block_Parser
 *
 * Parses a document and constructs a list of parsed block objects
 *
 * @since 5.0.0
 * @since 4.0.0 returns arrays not objects, all attributes are arrays
 */
class WP_Block_Parser {
	/**
	 * Input document being parsed
	 *
	 * @example "Pre-text\n<!-- wp:paragraph -->This is inside a block!<!-- /wp:paragraph -->"
	 *
	 * @since 5.0.0
	 * @var string
	 */
	public $document;

	/**
	 * Tracks parsing progress through document
	 *
	 * @since 5.0.0
	 * @var int
	 */
	public $offset;

	/**
	 * List of parsed blocks
	 *
	 * @since 5.0.0
	 * @var array[]
	 */
	public $output;

	/**
	 * Stack of partially-parsed structures in memory during parse
	 *
	 * @since 5.0.0
	 * @var WP_Block_Parser_Frame[]
	 * @phpstan-var list<WP_Block_Parser_Frame>
	 */
	public $stack;

	/**
	 * Internal block processor for parsing and scanning input document.
	 *
	 * @since 7.2.0
	 *
	 * @var WP_Block_Processor|null
	 */
	private $block_processor = null;

	/**
	 * Parses a document and returns a list of block structures
	 *
	 * When encountering an invalid parse will return a best-effort
	 * parse. In contrast to the specification parser this does not
	 * return an error on invalid inputs.
	 *
	 * @since 5.0.0
	 *
	 * @param string $document Input document being parsed.
	 * @return array[]
	 */
	public function parse( $document ) {
		$this->document = $document;
		$this->offset   = 0;
		$this->output   = array();
		$this->stack    = array();

		$this->block_processor = new WP_Block_Processor( $document );

		while ( $this->proceed() ) {
			continue;
		}

		return $this->output;
	}

	/**
	 * Processes the next token from the input document
	 * and returns whether to proceed eating more tokens
	 *
	 * This is the "next step" function that essentially
	 * takes a token as its input and decides what to do
	 * with that token before descending deeper into a
	 * nested block tree or continuing along the document
	 * or breaking out of a level of nesting.
	 *
	 * @internal
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	public function proceed() {
		// Ensure this is initialized, because some legacy code calls this method directly.
		$this->block_processor ??= new WP_Block_Processor( $this->document );

		$processor   = $this->block_processor;
		$stack_depth = count( $this->stack );

		// Handle the cases when no more tokens exist.
		if ( ! $processor->next_delimiter() ) {
			/*
			 * Most documents will end with all blocks properly closed.
			 * In these cases, all that’s necessary is to flush the output.
			 * There is probably a terminating newline.
			 */
			if ( 0 === $stack_depth ) {
				$this->add_freeform();
				return false;
			}

			/*
			 * However, if blocks remain open it means there were missing
			 * closing block delimiters. The post may have been truncated,
			 * for example, but some amount of corruption produced a malformed
			 * document.
			 *
			 * The spec parser considers this a parse error and returns a long
			 * freeform block containing the full span of text from the input
			 * document from the start of the un-closed block.
			 *
			 * This parser, however, is making a different pragmatic choice: it
			 * will implicitly close any remaining open blocks, as if the closers
			 * were provided. This ensures recovery of as much of the provided
			 * block structure as is possible.
			 */
			$this->implicitly_close_and_add_all_open_blocks();
			return false;
		}

		$token_type   = $processor->get_delimiter_type();
		$block_name   = $processor->get_block_type();
		$attrs        = $processor->allocate_and_return_parsed_attributes() ?? (
			JSON_ERROR_NONE === $processor->get_last_json_error()
				? array()
				: null
		);
		$span         = $processor->get_span();
		$start_offset = $span->start;
		$token_length = $span->length;

		// we may have some HTML soup before the next block.
		$leading_html_start = $start_offset > $this->offset ? $this->offset : null;

		switch ( $token_type ) {
			case WP_Block_Processor::VOID:
				/*
				 * easy case is if we stumbled upon a void block
				 * in the top-level of the document
				 */
				if ( 0 === $stack_depth ) {
					if ( isset( $leading_html_start ) ) {
						$this->output[] = (array) $this->freeform(
							substr(
								$this->document,
								$leading_html_start,
								$start_offset - $leading_html_start
							)
						);
					}

					$this->output[] = (array) new WP_Block_Parser_Block( $block_name, $attrs, array(), '', array() );
					$this->offset   = $start_offset + $token_length;
					return true;
				}

				// otherwise we found an inner block.
				$this->add_inner_block(
					new WP_Block_Parser_Block( $block_name, $attrs, array(), '', array() ),
					$start_offset,
					$token_length
				);
				$this->offset = $start_offset + $token_length;
				return true;

			case WP_Block_Processor::OPENER:
				// track all newly-opened blocks on the stack.
				$this->stack[] = new WP_Block_Parser_Frame(
					new WP_Block_Parser_Block( $block_name, $attrs, array(), '', array() ),
					$start_offset,
					$token_length,
					$start_offset + $token_length,
					$leading_html_start
				);
				$this->offset  = $start_offset + $token_length;
				return true;

			case WP_Block_Processor::CLOSER:
				/*
				 * if we're missing an opener we're in trouble
				 * This is an error
				 */
				if ( 0 === $stack_depth ) {
					/*
					 * we have options
					 * - assume an implicit opener
					 * - assume _this_ is the opener
					 * - give up and close out the document
					 */
					$this->add_freeform();
					return false;
				}

				// if we're not nesting then this is easy - close the block.
				if ( 1 === $stack_depth ) {
					$this->add_block_from_stack( $start_offset );
					$this->offset = $start_offset + $token_length;
					return true;
				}

				/*
				 * otherwise we're nested and we have to close out the current
				 * block and add it as a new innerBlock to the parent
				 */
				$stack_top              = array_pop( $this->stack );
				$html                   = substr( $this->document, $stack_top->prev_offset, $start_offset - $stack_top->prev_offset );
				$stack_top->prev_offset = $start_offset + $token_length;

				if ( '' !== $html ) {
					$stack_top->block->innerHTML     .= $html;
					$stack_top->block->innerContent[] = $html;
				}

				$this->add_inner_block(
					$stack_top->block,
					$stack_top->token_start,
					$stack_top->token_length,
					$start_offset + $token_length
				);
				$this->offset = $start_offset + $token_length;
				return true;

			default:
				// This is an error.
				$this->add_freeform();
				return false;
		}
	}

	/**
	 * Scans the document from where we last left off
	 * and finds the next valid token to parse if it exists
	 *
	 * Returns the type of the find: kind of find, block information, attributes
	 *
	 * @internal
	 * @since 5.0.0
	 * @since 4.6.1 fixed a bug in attribute parsing which caused catastrophic backtracking on invalid block comments
	 *
	 * @deprecated 7.2.0 Use {@see WP_Block_Processor} instead.
	 *
	 * @return array
	 */
	public function next_token() {
		// Ensure this is initialized, because some legacy code calls this method directly.
		$this->block_processor ??= new WP_Block_Processor( $this->document );

		$processor = $this->block_processor;

		if ( ! $processor->next_delimiter() ) {
			return array( 'no-more-tokens', null, null, null, null );
		}

		$span       = $processor->get_span();
		$started_at = $span->start;
		$length     = $span->length;
		$name       = $processor->get_block_type();
		$attrs      = $processor->allocate_and_return_parsed_attributes() ?? (
			JSON_ERROR_NONE === $processor->get_last_json_error()
				? array()
				: null
		);

		switch ( $processor->get_delimiter_type() ) {
			case WP_Block_Processor::VOID:
				return array( 'void-block', $name, $attrs, $started_at, $length );

			case WP_Block_Processor::CLOSER:
				return array( 'block-closer', $name, null, $started_at, $length );

			case WP_Block_Processor::OPENER:
				return array( 'block-opener', $name, $attrs, $started_at, $length );
		}

		// This should never be reachable.
		return $this->next_token();
	}

	/**
	 * Returns a new block object for freeform HTML
	 *
	 * @internal
	 * @since 5.0.0
	 *
	 * @param string $inner_html HTML content of block.
	 * @return WP_Block_Parser_Block freeform block object.
	 */
	public function freeform( $inner_html ) {
		return new WP_Block_Parser_Block( null, array(), array(), $inner_html, array( $inner_html ) );
	}

	/**
	 * Pushes a length of text from the input document
	 * to the output list as a freeform block.
	 *
	 * @internal
	 * @since 5.0.0
	 *
	 * @param null|int $length How many bytes of document text to output.
	 */
	public function add_freeform( $length = null ) {
		$length ??= strlen( $this->document ) - $this->offset;

		if ( 0 === $length ) {
			return;
		}

		$this->output[] = (array) $this->freeform( substr( $this->document, $this->offset, $length ) );
	}

	/**
	 * Given a block structure from memory pushes
	 * a new block to the output list.
	 *
	 * @internal
	 * @since 5.0.0
	 *
	 * @param WP_Block_Parser_Block $block        The block to add to the output.
	 * @param int                   $token_start  Byte offset into the document where the first token for the block starts.
	 * @param int                   $token_length Byte length of entire block from start of opening token to end of closing token.
	 * @param int|null              $last_offset  Last byte offset into document if continuing from earlier output.
	 */
	public function add_inner_block( WP_Block_Parser_Block $block, $token_start, $token_length, $last_offset = null ) {
		$parent                       = $this->stack[ count( $this->stack ) - 1 ];
		$parent->block->innerBlocks[] = (array) $block;
		$html                         = substr( $this->document, $parent->prev_offset, $token_start - $parent->prev_offset );

		if ( '' !== $html ) {
			$parent->block->innerHTML     .= $html;
			$parent->block->innerContent[] = $html;
		}

		$parent->block->innerContent[] = null;
		$parent->prev_offset           = $last_offset ?? $token_start + $token_length;
	}

	/**
	 * Pushes the top block from the parsing stack to the output list.
	 *
	 * @internal
	 * @since 5.0.0
	 *
	 * @param int|null $end_offset byte offset into document for where we should stop sending text output as HTML.
	 */
	public function add_block_from_stack( $end_offset = null ) {
		$stack_top   = array_pop( $this->stack );
		$prev_offset = $stack_top->prev_offset;

		$html = isset( $end_offset )
			? substr( $this->document, $prev_offset, $end_offset - $prev_offset )
			: substr( $this->document, $prev_offset );

		if ( '' !== $html ) {
			$stack_top->block->innerHTML     .= $html;
			$stack_top->block->innerContent[] = $html;
		}

		if ( isset( $stack_top->leading_html_start ) ) {
			$this->output[] = (array) $this->freeform(
				substr(
					$this->document,
					$stack_top->leading_html_start,
					$stack_top->token_start - $stack_top->leading_html_start
				)
			);
		}

		$this->output[] = (array) $stack_top->block;
	}

	/**
	 * Closes all open blocks and adds remaining blocks and freeform content to the output list.
	 *
	 * @internal
	 * @since 7.2.0
	 */
	public function implicitly_close_and_add_all_open_blocks(): void {
		$last_closed_block = null;
		$last_opener_at    = strlen( $this->document );
		$last_leading_html = null;

		/**
		 * There is a stack of open blocks, starting with a top-level block,
		 * and where each successive item in the stack is a child of that block.
		 * There are also spans of HTML content between the block delimiters.
		 *
		 * As the stack is collapsed, any HTML segments within that block must
		 * be collected into it, before closing and appending to its parent’s
		 * inner blocks array.
		 */
		while ( null !== ( $stack_top = array_pop( $this->stack ) ) ) {
			/*
			 * Since there is no open inner block on the top-most block on the stack,
			 * the first iteration’s remaining HTML span will be from the last `prev_offset`
			 * until the end of the document. For each containing parent, it will run from
			 * the `prev_offset` until the opening of the block that was just closed.
			 */
			$html_length = $last_opener_at - $stack_top->prev_offset;
			if ( 0 !== $html_length ) {
				$html = substr( $this->document, $stack_top->prev_offset, $html_length );

				$stack_top->block->innerHTML     .= $html;
				$stack_top->block->innerContent[] = $html;
			}

			if ( isset( $last_closed_block ) ) {
				$stack_top->block->innerContent[] = null;
				$stack_top->block->innerBlocks[]  = (array) $last_closed_block;
			}

			$last_closed_block = $stack_top->block;
			$last_opener_at    = $stack_top->token_start;
			$last_leading_html = $stack_top->leading_html_start;
		}

		// Collect any HTML preceding the outermost open block.
		if ( isset( $last_leading_html ) && $last_opener_at > $last_leading_html ) {
			$html           = substr( $this->document, $last_leading_html, $last_opener_at - $last_leading_html );
			$this->output[] = (array) $this->freeform( $html );
		}

		if ( isset( $last_closed_block ) ) {
			$this->output[] = (array) $last_closed_block;
		}

		$this->offset = strlen( $this->document );
	}
}

/**
 * WP_Block_Parser_Block class.
 *
 * Required for backward compatibility in WordPress Core.
 */
require_once __DIR__ . '/class-wp-block-parser-block.php';

/**
 * WP_Block_Parser_Frame class.
 *
 * Required for backward compatibility in WordPress Core.
 */
require_once __DIR__ . '/class-wp-block-parser-frame.php';
