<?php
/**
 * A class for displaying various tree-like structures.
 *
 * Extend the Walker class to use it, see examples below. Child classes
 * do not need to implement all of the abstract methods in the class. The child
 * only needs to implement the methods that are needed.
 *
 * @since 2.1.0
 *
 * @package WordPress
 * @abstract
 */
class Walker {
	/**
	 * What the class handles.
	 *
	 * @since 2.1.0
	 * @var string
	 */
	public $tree_type;

	/**
	 * DB fields to use.
	 *
	 * @since 2.1.0
	 * @var array
	 */
	public $db_fields;

	/**
	 * Max number of pages walked by the paged walker
	 *
	 * @since 2.7.0
	 * @var int
	 */
	public $max_pages = 1;

	/**
	 * Whether the current element has children or not.
	 *
	 * To be used in start_el().
	 *
	 * @since 4.0.0
	 * @var bool
	 */
	public $has_children;

	/**
	 * Starts the list before the elements are added.
	 *
	 * The $args parameter holds additional values that may be used with the child
	 * class methods. This method is called at the start of the output list.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param int    $depth  Depth of the item.
	 * @param array  $args   An array of additional arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * The $args parameter holds additional values that may be used with the child
	 * class methods. This method finishes the list at the end of output of the elements.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param int    $depth  Depth of the item.
	 * @param array  $args   An array of additional arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {}

	/**
	 * Start the element output.
	 *
	 * The $args parameter holds additional values that may be used with the child
	 * class methods. Includes the element output also.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output            Used to append additional content (passed by reference).
	 * @param object $object            The data object.
	 * @param int    $depth             Depth of the item.
	 * @param array  $args              An array of additional arguments.
	 * @param int    $current_object_id ID of the current item.
	 */
	public function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {}

	/**
	 * Ends the element output, if needed.
	 *
	 * The $args parameter holds additional values that may be used with the child class methods.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param object $object The data object.
	 * @param int    $depth  Depth of the item.
	 * @param array  $args   An array of additional arguments.
	 */
	public function end_el( &$output, $object, $depth = 0, $args = array() ) {}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth. It is possible to set the
	 * max depth to include all depths, see walk() method.
	 *
	 * This method should not be called directly, use the walk() method instead.
	 *
	 * @since 2.5.0
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing (passed by reference).
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments.
	 * @param string $output            Used to append additional content (passed by reference).
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element ) {
			return;
		}

		$id_field = $this->db_fields['id'];
		$id       = $element->$id_field;

		// Display this element.
		$this->has_children = ! empty( $children_elements[ $id ] );
		if ( isset( $args[0] ) && is_array( $args[0] ) ) {
			$args[0]['has_children'] = $this->has_children; // Back-compat.
		}

		$this->start_el( $output, $element, $depth, ...array_values( $args ) );

		// Descend only when the depth is right and there are children for this element.
		if ( ( 0 == $max_depth || $max_depth > $depth + 1 ) && isset( $children_elements[ $id ] ) ) {

			foreach ( $children_elements[ $id ] as $child ) {

				if ( ! isset( $newlevel ) ) {
					$newlevel = true;
					// Start the child delimiter.
					$this->start_lvl( $output, $depth, ...array_values( $args ) );
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset( $newlevel ) && $newlevel ) {
			// End the child delimiter.
			$this->end_lvl( $output, $depth, ...array_values( $args ) );
		}

		// End this element.
		$this->end_el( $output, $element, $depth, ...array_values( $args ) );
	}

	/**
	 * Display array of elements hierarchically.
	 *
	 * Does not assume any existing order of elements.
	 *
	 * $max_depth = -1 means flatly display every element.
	 * $max_depth = 0 means display all levels.
	 * $max_depth > 0 specifies the number of display levels.
	 *
	 * @since 2.1.0
	 * @since 5.3.0 Formalized the existing `...$args` parameter by adding it
	 *              to the function signature.
	 *
	 * @param array $elements  An array of elements.
	 * @param int   $max_depth The maximum hierarchical depth.
	 * @param mixed ...$args   Optional additional arguments.
	 * @return string The hierarchical item output.
	 */
	public function walk( $elements, $max_depth, ...$args ) {
		$output = '';

		// Invalid parameter or nothing to walk.
		if ( $max_depth < -1 || empty( $elements ) ) {
			return $output;
		}

		$parent_field = $this->db_fields['parent'];

		// Flat display.
		if ( -1 == $max_depth ) {
			$empty_array = array();
			foreach ( $elements as $e ) {
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );
			}
			return $output;
		}

		/*
		 * Need to display in hierarchical order.
		 * Separate elements into two buckets: top level and children elements.
		 * Children_elements is two dimensional array, eg.
		 * Children_elements[10][] contains all sub-elements whose parent is 10.
		 */
		$top_level_elements = array();
		$children_elements  = array();
		foreach ( $elements as $e ) {
			if ( empty( $e->$parent_field ) ) {
				$top_level_elements[] = $e;
			} else {
				$children_elements[ $e->$parent_field ][] = $e;
			}
		}

		/*
		 * When none of the elements is top level.
		 * Assume the first one must be root of the sub elements.
		 */
		if ( empty( $top_level_elements ) ) {

			$first = array_slice( $elements, 0, 1 );
			$root  = $first[0];

			$top_level_elements = array();
			$children_elements  = array();
			foreach ( $elements as $e ) {
				if ( $root->$parent_field == $e->$parent_field ) {
					$top_level_elements[] = $e;
				} else {
					$children_elements[ $e->$parent_field ][] = $e;
				}
			}
		}

		foreach ( $top_level_elements as $e ) {
			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
		}

		/*
		 * If we are displaying all levels, and remaining children_elements is not empty,
		 * then we got orphans, which should be displayed regardless.
		 */
		if ( ( 0 == $max_depth ) && count( $children_elements ) > 0 ) {
			$empty_array = array();
			foreach ( $children_elements as $orphans ) {
				foreach ( $orphans as $op ) {
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
				}
			}
		}

		return $output;
	}

	/**
	 * paged_walk() - produce a page of nested elements
	 *
	 * Given an array of hierarchical elements, the maximum depth, a specific page number,
	 * and number of elements per page, this function first determines all top level root elements
	 * belonging to that page, then lists them and all of their children in hierarchical order.
	 *
	 * $max_depth = 0 means display all levels.
	 * $max_depth > 0 specifies the number of display levels.
	 *
	 * @since 2.7.0
	 * @since 5.3.0 Formalized the existing `...$args` parameter by adding it
	 *              to the function signature.
	 *
	 * @param array $elements
	 * @param int   $max_depth The maximum hierarchical depth.
	 * @param int   $page_num  The specific page number, beginning with 1.
	 * @param int   $per_page
	 * @param mixed ...$args   Optional additional arguments.
	 * @return string XHTML of the specified page of elements
	 */
	public function paged_walk( $elements, $max_depth, $page_num, $per_page, ...$args ) {
		if ( empty( $elements ) || $max_depth < -1 ) {
			return '';
		}

		$output = '';

		$parent_field = $this->db_fields['parent'];

		$count = -1;
		if ( -1 == $max_depth ) {
			$total_top = count( $elements );
		}
		if ( $page_num < 1 || $per_page < 0 ) {
			// No paging.
			$paging = false;
			$start  = 0;
			if ( -1 == $max_depth ) {
				$end = $total_top;
			}
			$this->max_pages = 1;
		} else {
			$paging = true;
			$start  = ( (int) $page_num - 1 ) * (int) $per_page;
			$end    = $start + $per_page;
			if ( -1 == $max_depth ) {
				$this->max_pages = ceil( $total_top / $per_page );
			}
		}

		// Flat display.
		if ( -1 == $max_depth ) {
			if ( ! empty( $args[0]['reverse_top_level'] ) ) {
				$elements = array_reverse( $elements );
				$oldstart = $start;
				$start    = $total_top - $end;
				$end      = $total_top - $oldstart;
			}

			$empty_array = array();
			foreach ( $elements as $e ) {
				$count++;
				if ( $count < $start ) {
					continue;
				}
				if ( $count >= $end ) {
					break;
				}
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );
			}
			return $output;
		}

		/*
		 * Separate elements into two buckets: top level and children elements.
		 * Children_elements is two dimensional array, e.g.
		 * $children_elements[10][] contains all sub-elements whose parent is 10.
		 */
		$top_level_elements = array();
		$children_elements  = array();
		foreach ( $elements as $e ) {
			if ( 0 == $e->$parent_field ) {
				$top_level_elements[] = $e;
			} else {
				$children_elements[ $e->$parent_field ][] = $e;
			}
		}

		$total_top = count( $top_level_elements );
		if ( $paging ) {
			$this->max_pages = ceil( $total_top / $per_page );
		} else {
			$end = $total_top;
		}

		if ( ! empty( $args[0]['reverse_top_level'] ) ) {
			$top_level_elements = array_reverse( $top_level_elements );
			$oldstart           = $start;
			$start              = $total_top - $end;
			$end                = $total_top - $oldstart;
		}
		if ( ! empty( $args[0]['reverse_children'] ) ) {
			foreach ( $children_elements as $parent => $children ) {
				$children_elements[ $parent ] = array_reverse( $children );
			}
		}

		foreach ( $top_level_elements as $e ) {
			$count++;

			// For the last page, need to unset earlier children in order to keep track of orphans.
			if ( $end >= $total_top && $count < $start ) {
					$this->unset_children( $e, $children_elements );
			}

			if ( $count < $start ) {
				continue;
			}

			if ( $count >= $end ) {
				break;
			}

			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
		}

		if ( $end >= $total_top && count( $children_elements ) > 0 ) {
			$empty_array = array();
			foreach ( $children_elements as $orphans ) {
				foreach ( $orphans as $op ) {
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
				}
			}
		}

		return $output;
	}

	/**
	 * Calculates the total number of root elements.
	 *
	 * @since 2.7.0
	 *
	 * @param array $elements Elements to list.
	 * @return int Number of root elements.
	 */
	public function get_number_of_root_elements( $elements ) {
		$num          = 0;
		$parent_field = $this->db_fields['parent'];

		foreach ( $elements as $e ) {
			if ( 0 == $e->$parent_field ) {
				$num++;
			}
		}
		return $num;
	}

	/**
	 * Unset all the children for a given top level element.
	 *
	 * @since 2.7.0
	 *
	 * @param object $e
	 * @param array  $children_elements
	 */
	public function unset_children( $e, &$children_elements ) {
		if ( ! $e || ! $children_elements ) {
			return;
		}

		$id_field = $this->db_fields['id'];
		$id       = $e->$id_field;

		if ( ! empty( $children_elements[ $id ] ) && is_array( $children_elements[ $id ] ) ) {
			foreach ( (array) $children_elements[ $id ] as $child ) {
				$this->unset_children( $child, $children_elements );
			}
		}

		unset( $children_elements[ $id ] );
	}

}
