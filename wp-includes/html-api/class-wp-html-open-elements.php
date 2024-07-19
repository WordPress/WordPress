<?php
/**
 * HTML API: WP_HTML_Open_Elements class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.4.0
 */

/**
 * Core class used by the HTML processor during HTML parsing
 * for managing the stack of open elements.
 *
 * This class is designed for internal use by the HTML processor.
 *
 * > Initially, the stack of open elements is empty. The stack grows
 * > downwards; the topmost node on the stack is the first one added
 * > to the stack, and the bottommost node of the stack is the most
 * > recently added node in the stack (notwithstanding when the stack
 * > is manipulated in a random access fashion as part of the handling
 * > for misnested tags).
 *
 * @since 6.4.0
 *
 * @access private
 *
 * @see https://html.spec.whatwg.org/#stack-of-open-elements
 * @see WP_HTML_Processor
 */
class WP_HTML_Open_Elements {
	/**
	 * Holds the stack of open element references.
	 *
	 * @since 6.4.0
	 *
	 * @var WP_HTML_Token[]
	 */
	public $stack = array();

	/**
	 * Whether a P element is in button scope currently.
	 *
	 * This class optimizes scope lookup by pre-calculating
	 * this value when elements are added and removed to the
	 * stack of open elements which might change its value.
	 * This avoids frequent iteration over the stack.
	 *
	 * @since 6.4.0
	 *
	 * @var bool
	 */
	private $has_p_in_button_scope = false;

	/**
	 * A function that will be called when an item is popped off the stack of open elements.
	 *
	 * The function will be called with the popped item as its argument.
	 *
	 * @since 6.6.0
	 *
	 * @var Closure|null
	 */
	private $pop_handler = null;

	/**
	 * A function that will be called when an item is pushed onto the stack of open elements.
	 *
	 * The function will be called with the pushed item as its argument.
	 *
	 * @since 6.6.0
	 *
	 * @var Closure|null
	 */
	private $push_handler = null;

	/**
	 * Sets a pop handler that will be called when an item is popped off the stack of
	 * open elements.
	 *
	 * The function will be called with the pushed item as its argument.
	 *
	 * @since 6.6.0
	 *
	 * @param Closure $handler The handler function.
	 */
	public function set_pop_handler( Closure $handler ): void {
		$this->pop_handler = $handler;
	}

	/**
	 * Sets a push handler that will be called when an item is pushed onto the stack of
	 * open elements.
	 *
	 * The function will be called with the pushed item as its argument.
	 *
	 * @since 6.6.0
	 *
	 * @param Closure $handler The handler function.
	 */
	public function set_push_handler( Closure $handler ): void {
		$this->push_handler = $handler;
	}

	/**
	 * Reports if a specific node is in the stack of open elements.
	 *
	 * @since 6.4.0
	 *
	 * @param WP_HTML_Token $token Look for this node in the stack.
	 * @return bool Whether the referenced node is in the stack of open elements.
	 */
	public function contains_node( WP_HTML_Token $token ): bool {
		foreach ( $this->walk_up() as $item ) {
			if ( $token->bookmark_name === $item->bookmark_name ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns how many nodes are currently in the stack of open elements.
	 *
	 * @since 6.4.0
	 *
	 * @return int How many node are in the stack of open elements.
	 */
	public function count(): int {
		return count( $this->stack );
	}

	/**
	 * Returns the node at the end of the stack of open elements,
	 * if one exists. If the stack is empty, returns null.
	 *
	 * @since 6.4.0
	 *
	 * @return WP_HTML_Token|null Last node in the stack of open elements, if one exists, otherwise null.
	 */
	public function current_node(): ?WP_HTML_Token {
		$current_node = end( $this->stack );

		return $current_node ? $current_node : null;
	}

	/**
	 * Indicates if the current node is of a given type or name.
	 *
	 * It's possible to pass either a node type or a node name to this function.
	 * In the case there is no current element it will always return `false`.
	 *
	 * Example:
	 *
	 *     // Is the current node a text node?
	 *     $stack->current_node_is( '#text' );
	 *
	 *     // Is the current node a DIV element?
	 *     $stack->current_node_is( 'DIV' );
	 *
	 *     // Is the current node any element/tag?
	 *     $stack->current_node_is( '#tag' );
	 *
	 * @see WP_HTML_Tag_Processor::get_token_type
	 * @see WP_HTML_Tag_Processor::get_token_name
	 *
	 * @since 6.7.0
	 *
	 * @access private
	 *
	 * @param string $identity Check if the current node has this name or type (depending on what is provided).
	 * @return bool Whether there is a current element that matches the given identity, whether a token name or type.
	 */
	public function current_node_is( string $identity ): bool {
		$current_node = end( $this->stack );
		if ( false === $current_node ) {
			return false;
		}

		$current_node_name = $current_node->node_name;

		return (
			$current_node_name === $identity ||
			( '#doctype' === $identity && 'html' === $current_node_name ) ||
			( '#tag' === $identity && ctype_upper( $current_node_name ) )
		);
	}

	/**
	 * Returns whether an element is in a specific scope.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#has-an-element-in-the-specific-scope
	 *
	 * @param string   $tag_name         Name of tag check.
	 * @param string[] $termination_list List of elements that terminate the search.
	 * @return bool Whether the element was found in a specific scope.
	 */
	public function has_element_in_specific_scope( string $tag_name, $termination_list ): bool {
		foreach ( $this->walk_up() as $node ) {
			if ( $node->node_name === $tag_name ) {
				return true;
			}

			if (
				'(internal: H1 through H6 - do not use)' === $tag_name &&
				in_array( $node->node_name, array( 'H1', 'H2', 'H3', 'H4', 'H5', 'H6' ), true )
			) {
				return true;
			}

			switch ( $node->node_name ) {
				case 'HTML':
					return false;
			}

			if ( in_array( $node->node_name, $termination_list, true ) ) {
				return false;
			}
		}

		return false;
	}

	/**
	 * Returns whether a particular element is in scope.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#has-an-element-in-scope
	 *
	 * @param string $tag_name Name of tag to check.
	 * @return bool Whether given element is in scope.
	 */
	public function has_element_in_scope( string $tag_name ): bool {
		return $this->has_element_in_specific_scope(
			$tag_name,
			array(

				/*
				 * Because it's not currently possible to encounter
				 * one of the termination elements, they don't need
				 * to be listed here. If they were, they would be
				 * unreachable and only waste CPU cycles while
				 * scanning through HTML.
				 */
			)
		);
	}

	/**
	 * Returns whether a particular element is in list item scope.
	 *
	 * @since 6.4.0
	 * @since 6.5.0 Implemented: no longer throws on every invocation.
	 *
	 * @see https://html.spec.whatwg.org/#has-an-element-in-list-item-scope
	 *
	 * @param string $tag_name Name of tag to check.
	 * @return bool Whether given element is in scope.
	 */
	public function has_element_in_list_item_scope( string $tag_name ): bool {
		return $this->has_element_in_specific_scope(
			$tag_name,
			array(
				// There are more elements that belong here which aren't currently supported.
				'OL',
				'UL',
			)
		);
	}

	/**
	 * Returns whether a particular element is in button scope.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#has-an-element-in-button-scope
	 *
	 * @param string $tag_name Name of tag to check.
	 * @return bool Whether given element is in scope.
	 */
	public function has_element_in_button_scope( string $tag_name ): bool {
		return $this->has_element_in_specific_scope( $tag_name, array( 'BUTTON' ) );
	}

	/**
	 * Returns whether a particular element is in table scope.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#has-an-element-in-table-scope
	 *
	 * @throws WP_HTML_Unsupported_Exception Always until this function is implemented.
	 *
	 * @param string $tag_name Name of tag to check.
	 * @return bool Whether given element is in scope.
	 */
	public function has_element_in_table_scope( string $tag_name ): bool {
		throw new WP_HTML_Unsupported_Exception( 'Cannot process elements depending on table scope.' );

		return false; // The linter requires this unreachable code until the function is implemented and can return.
	}

	/**
	 * Returns whether a particular element is in select scope.
	 *
	 * This test differs from the others like it, in that its rules are inverted.
	 * Instead of arriving at a match when one of any tag in a termination group
	 * is reached, this one terminates if any other tag is reached.
	 *
	 * > The stack of open elements is said to have a particular element in select scope when it has
	 * > that element in the specific scope consisting of all element types except the following:
	 * >   - optgroup in the HTML namespace
	 * >   - option in the HTML namespace
	 *
	 * @since 6.4.0 Stub implementation (throws).
	 * @since 6.7.0 Full implementation.
	 *
	 * @see https://html.spec.whatwg.org/#has-an-element-in-select-scope
	 *
	 * @param string $tag_name Name of tag to check.
	 * @return bool Whether the given element is in SELECT scope.
	 */
	public function has_element_in_select_scope( string $tag_name ): bool {
		foreach ( $this->walk_up() as $node ) {
			if ( $node->node_name === $tag_name ) {
				return true;
			}

			if (
				'OPTION' !== $node->node_name &&
				'OPTGROUP' !== $node->node_name
			) {
				return false;
			}
		}

		return false;
	}

	/**
	 * Returns whether a P is in BUTTON scope.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#has-an-element-in-button-scope
	 *
	 * @return bool Whether a P is in BUTTON scope.
	 */
	public function has_p_in_button_scope(): bool {
		return $this->has_p_in_button_scope;
	}

	/**
	 * Pops a node off of the stack of open elements.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#stack-of-open-elements
	 *
	 * @return bool Whether a node was popped off of the stack.
	 */
	public function pop(): bool {
		$item = array_pop( $this->stack );
		if ( null === $item ) {
			return false;
		}

		if ( 'context-node' === $item->bookmark_name ) {
			$this->stack[] = $item;
			return false;
		}

		$this->after_element_pop( $item );
		return true;
	}

	/**
	 * Pops nodes off of the stack of open elements until one with the given tag name has been popped.
	 *
	 * @since 6.4.0
	 *
	 * @see WP_HTML_Open_Elements::pop
	 *
	 * @param string $tag_name Name of tag that needs to be popped off of the stack of open elements.
	 * @return bool Whether a tag of the given name was found and popped off of the stack of open elements.
	 */
	public function pop_until( string $tag_name ): bool {
		foreach ( $this->walk_up() as $item ) {
			if ( 'context-node' === $item->bookmark_name ) {
				return true;
			}

			$this->pop();

			if (
				'(internal: H1 through H6 - do not use)' === $tag_name &&
				in_array( $item->node_name, array( 'H1', 'H2', 'H3', 'H4', 'H5', 'H6' ), true )
			) {
				return true;
			}

			if ( $tag_name === $item->node_name ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Pushes a node onto the stack of open elements.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#stack-of-open-elements
	 *
	 * @param WP_HTML_Token $stack_item Item to add onto stack.
	 */
	public function push( WP_HTML_Token $stack_item ): void {
		$this->stack[] = $stack_item;
		$this->after_element_push( $stack_item );
	}

	/**
	 * Removes a specific node from the stack of open elements.
	 *
	 * @since 6.4.0
	 *
	 * @param WP_HTML_Token $token The node to remove from the stack of open elements.
	 * @return bool Whether the node was found and removed from the stack of open elements.
	 */
	public function remove_node( WP_HTML_Token $token ): bool {
		if ( 'context-node' === $token->bookmark_name ) {
			return false;
		}

		foreach ( $this->walk_up() as $position_from_end => $item ) {
			if ( $token->bookmark_name !== $item->bookmark_name ) {
				continue;
			}

			$position_from_start = $this->count() - $position_from_end - 1;
			array_splice( $this->stack, $position_from_start, 1 );
			$this->after_element_pop( $item );
			return true;
		}

		return false;
	}


	/**
	 * Steps through the stack of open elements, starting with the top element
	 * (added first) and walking downwards to the one added last.
	 *
	 * This generator function is designed to be used inside a "foreach" loop.
	 *
	 * Example:
	 *
	 *     $html = '<em><strong><a>We are here';
	 *     foreach ( $stack->walk_down() as $node ) {
	 *         echo "{$node->node_name} -> ";
	 *     }
	 *     > EM -> STRONG -> A ->
	 *
	 * To start with the most-recently added element and walk towards the top,
	 * see WP_HTML_Open_Elements::walk_up().
	 *
	 * @since 6.4.0
	 */
	public function walk_down() {
		$count = count( $this->stack );

		for ( $i = 0; $i < $count; $i++ ) {
			yield $this->stack[ $i ];
		}
	}

	/**
	 * Steps through the stack of open elements, starting with the bottom element
	 * (added last) and walking upwards to the one added first.
	 *
	 * This generator function is designed to be used inside a "foreach" loop.
	 *
	 * Example:
	 *
	 *     $html = '<em><strong><a>We are here';
	 *     foreach ( $stack->walk_up() as $node ) {
	 *         echo "{$node->node_name} -> ";
	 *     }
	 *     > A -> STRONG -> EM ->
	 *
	 * To start with the first added element and walk towards the bottom,
	 * see WP_HTML_Open_Elements::walk_down().
	 *
	 * @since 6.4.0
	 * @since 6.5.0 Accepts $above_this_node to start traversal above a given node, if it exists.
	 *
	 * @param WP_HTML_Token|null $above_this_node Optional. Start traversing above this node,
	 *                                            if provided and if the node exists.
	 */
	public function walk_up( ?WP_HTML_Token $above_this_node = null ) {
		$has_found_node = null === $above_this_node;

		for ( $i = count( $this->stack ) - 1; $i >= 0; $i-- ) {
			$node = $this->stack[ $i ];

			if ( ! $has_found_node ) {
				$has_found_node = $node === $above_this_node;
				continue;
			}

			yield $node;
		}
	}

	/*
	 * Internal helpers.
	 */

	/**
	 * Updates internal flags after adding an element.
	 *
	 * Certain conditions (such as "has_p_in_button_scope") are maintained here as
	 * flags that are only modified when adding and removing elements. This allows
	 * the HTML Processor to quickly check for these conditions instead of iterating
	 * over the open stack elements upon each new tag it encounters. These flags,
	 * however, need to be maintained as items are added and removed from the stack.
	 *
	 * @since 6.4.0
	 *
	 * @param WP_HTML_Token $item Element that was added to the stack of open elements.
	 */
	public function after_element_push( WP_HTML_Token $item ): void {
		/*
		 * When adding support for new elements, expand this switch to trap
		 * cases where the precalculated value needs to change.
		 */
		switch ( $item->node_name ) {
			case 'BUTTON':
				$this->has_p_in_button_scope = false;
				break;

			case 'P':
				$this->has_p_in_button_scope = true;
				break;
		}

		if ( null !== $this->push_handler ) {
			( $this->push_handler )( $item );
		}
	}

	/**
	 * Updates internal flags after removing an element.
	 *
	 * Certain conditions (such as "has_p_in_button_scope") are maintained here as
	 * flags that are only modified when adding and removing elements. This allows
	 * the HTML Processor to quickly check for these conditions instead of iterating
	 * over the open stack elements upon each new tag it encounters. These flags,
	 * however, need to be maintained as items are added and removed from the stack.
	 *
	 * @since 6.4.0
	 *
	 * @param WP_HTML_Token $item Element that was removed from the stack of open elements.
	 */
	public function after_element_pop( WP_HTML_Token $item ): void {
		/*
		 * When adding support for new elements, expand this switch to trap
		 * cases where the precalculated value needs to change.
		 */
		switch ( $item->node_name ) {
			case 'BUTTON':
				$this->has_p_in_button_scope = $this->has_element_in_button_scope( 'P' );
				break;

			case 'P':
				$this->has_p_in_button_scope = $this->has_element_in_button_scope( 'P' );
				break;
		}

		if ( null !== $this->pop_handler ) {
			( $this->pop_handler )( $item );
		}
	}

	/**
	 * Wakeup magic method.
	 *
	 * @since 6.6.0
	 */
	public function __wakeup() {
		throw new \LogicException( __CLASS__ . ' should never be unserialized' );
	}
}
