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
	 * Reports if a specific node is in the stack of open elements.
	 *
	 * @since 6.4.0
	 *
	 * @param WP_HTML_Token $token Look for this node in the stack.
	 * @return bool Whether the referenced node is in the stack of open elements.
	 */
	public function contains_node( $token ) {
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
	public function count() {
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
	public function current_node() {
		$current_node = end( $this->stack );

		return $current_node ? $current_node : null;
	}

	/**
	 * Returns whether an element is in a specific scope.
	 *
	 * ## HTML Support
	 *
	 * This function skips checking for the termination list because there
	 * are no supported elements which appear in the termination list.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#has-an-element-in-the-specific-scope
	 *
	 * @param string   $tag_name         Name of tag check.
	 * @param string[] $termination_list List of elements that terminate the search.
	 * @return bool Whether the element was found in a specific scope.
	 */
	public function has_element_in_specific_scope( $tag_name, $termination_list ) {
		foreach ( $this->walk_up() as $node ) {
			if ( $node->node_name === $tag_name ) {
				return true;
			}

			switch ( $node->node_name ) {
				case 'HTML':
					return false;
			}

			if ( in_array( $node->node_name, $termination_list, true ) ) {
				return true;
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
	public function has_element_in_scope( $tag_name ) {
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
	 *
	 * @see https://html.spec.whatwg.org/#has-an-element-in-list-item-scope
	 *
	 * @throws WP_HTML_Unsupported_Exception Always until this function is implemented.
	 *
	 * @param string $tag_name Name of tag to check.
	 * @return bool Whether given element is in scope.
	 */
	public function has_element_in_list_item_scope( $tag_name ) {
		throw new WP_HTML_Unsupported_Exception( 'Cannot process elements depending on list item scope.' );

		return false; // The linter requires this unreachable code until the function is implemented and can return.
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
	public function has_element_in_button_scope( $tag_name ) {
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
	public function has_element_in_table_scope( $tag_name ) {
		throw new WP_HTML_Unsupported_Exception( 'Cannot process elements depending on table scope.' );

		return false; // The linter requires this unreachable code until the function is implemented and can return.
	}

	/**
	 * Returns whether a particular element is in select scope.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#has-an-element-in-select-scope
	 *
	 * @throws WP_HTML_Unsupported_Exception Always until this function is implemented.
	 *
	 * @param string $tag_name Name of tag to check.
	 * @return bool Whether given element is in scope.
	 */
	public function has_element_in_select_scope( $tag_name ) {
		throw new WP_HTML_Unsupported_Exception( 'Cannot process elements depending on select scope.' );

		return false; // The linter requires this unreachable code until the function is implemented and can return.
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
	public function has_p_in_button_scope() {
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
	public function pop() {
		$item = array_pop( $this->stack );

		if ( null === $item ) {
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
	public function pop_until( $tag_name ) {
		foreach ( $this->walk_up() as $item ) {
			$this->pop();

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
	public function push( $stack_item ) {
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
	public function remove_node( $token ) {
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
	 */
	public function walk_up() {
		for ( $i = count( $this->stack ) - 1; $i >= 0; $i-- ) {
			yield $this->stack[ $i ];
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
	public function after_element_push( $item ) {
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
	public function after_element_pop( $item ) {
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
	}
}
