<?php
/**
 * Block Serialization Parser
 *
 * @package WordPress
 */

/**
 * Class WP_Block_Parser_Frame
 *
 * Holds partial blocks in memory while parsing
 *
 * @internal
 * @since 5.0.0
 */
class WP_Block_Parser_Frame {
	/**
	 * Full or partial block
	 *
	 * @since 5.0.0
	 * @var WP_Block_Parser_Block
	 */
	public $block;

	/**
	 * Byte offset into document for start of parse token
	 *
	 * @since 5.0.0
	 * @var int
	 */
	public $token_start;

	/**
	 * Byte length of entire parse token string
	 *
	 * @since 5.0.0
	 * @var int
	 */
	public $token_length;

	/**
	 * Byte offset into document for after parse token ends
	 * (used during reconstruction of stack into parse production)
	 *
	 * @since 5.0.0
	 * @var int
	 */
	public $prev_offset;

	/**
	 * Byte offset into document where leading HTML before token starts
	 *
	 * @since 5.0.0
	 * @var int
	 */
	public $leading_html_start;

	/**
	 * Constructor
	 *
	 * Will populate object properties from the provided arguments.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_Block_Parser_Block $block              Full or partial block.
	 * @param int                   $token_start        Byte offset into document for start of parse token.
	 * @param int                   $token_length       Byte length of entire parse token string.
	 * @param int|null              $prev_offset        Optional. Byte offset into document for after parse token ends. Default null.
	 * @param int|null              $leading_html_start Optional. Byte offset into document where leading HTML before token starts.
	 *                                                  Default null.
	 */
	public function __construct( $block, $token_start, $token_length, $prev_offset = null, $leading_html_start = null ) {
		$this->block              = $block;
		$this->token_start        = $token_start;
		$this->token_length       = $token_length;
		$this->prev_offset        = isset( $prev_offset ) ? $prev_offset : $token_start + $token_length;
		$this->leading_html_start = $leading_html_start;
	}
}
