<?php
/**
 * Handle logic for the conditional loading of snippets.
 * You'll find here the available logic options and how they apply to snippets.
 *
 * @package WPCode
 */

/**
 * The main conditional logic class that loads all the types.
 */
class WPCode_Conditional_Logic {

	/**
	 * Types of conditional logic.
	 *
	 * @var WPCode_Conditional_Type[]
	 */
	private $types = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_types' ), 0 );
	}

	/**
	 * Load the different conditional logic types.
	 *
	 * @return void
	 */
	public function load_types() {
		require_once WPCODE_PLUGIN_PATH . 'includes/conditional-logic/class-wpcode-conditional-type.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/conditional-logic/class-wpcode-conditional-user.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/conditional-logic/class-wpcode-conditional-page.php';
	}

	/**
	 * Register an auto-insert type.
	 *
	 * @param WPCode_Conditional_Type $type The type to add to the available types.
	 *
	 * @return void
	 */
	public function register_type( $type ) {
		$this->types[ $type->name ] = $type;
	}

	/**
	 * Get all the admin options for the conditional logic form.
	 *
	 * @return array
	 */
	public function get_all_admin_options() {
		$options = array();
		foreach ( $this->types as $type ) {
			$type->load_type_options(); // Reload the options in case a global snippet made them get loaded before some post types were registered, for example.
			$options[ $type->get_name() ] = array(
				'label'   => $type->get_label(),
				'name'    => $type->get_name(),
				'options' => $type->get_type_options(),
			);
		}

		// Sort options so the unavailable ones are at the end.
		$available_options   = array();
		$unavailable_options = array();
		foreach ( $options as $key => $option ) {
			if ( empty( $option['options'] ) ) {
				continue;
			}
			$first_option = reset( $option['options'] );
			if ( ! empty( $first_option['upgrade'] ) ) {
				$unavailable_options[ $key ] = $option;
			} else {
				$available_options[ $key ] = $option;
			}
		}
		return array_merge( $available_options, $unavailable_options );
	}

	/**
	 * Goes through a list of snippets and filters out the ones
	 * that don't match the conditional logic rules.
	 *
	 * @param WPCode_Snippet[] $snippets An array of snippets.
	 *
	 * @return WPCode_Snippet[]
	 */
	public function check_snippets_conditions( $snippets ) {
		// If there's nothing to evaluate just return an empty array.
		if ( empty( $snippets ) ) {
			return array();
		}

		$filtered_snippets = array();
		foreach ( $snippets as $snippet ) {
			if ( ! $snippet->conditional_rules_enabled() ) {
				// If rules are disabled, ignore.
				$filtered_snippets[] = $snippet;
				continue;
			}
			$rules = $snippet->get_conditional_rules();
			if ( ! isset( $rules['show'] ) ) {
				continue;
			}
			$show          = 'show' === $rules['show'];
			$rules_are_met = $this->are_snippet_rules_met( $snippet );
			if ( $show && ! $rules_are_met ) {
				// If we should show based on conditions, and conditions
				// are not met, skip.
				continue;
			}
			if ( ! $show && $rules_are_met ) {
				// If we should hide based on conditions, and conditions
				// are met, skip.
				continue;
			}

			$filtered_snippets[] = $snippet;
		}

		return $filtered_snippets;
	}

	/**
	 * Takes a snippet and evaluates if conditional logic rules
	 * are met.
	 *
	 * @param WPCode_Snippet $snippet The snippet instance.
	 *
	 * @return bool
	 */
	public function are_snippet_rules_met( $snippet ) {
		$rules = $snippet->get_conditional_rules();
		if ( empty( $rules ) || ! is_array( $rules ) || empty( $rules['groups'] ) ) {
			return true;
		}

		// Go through all rule groups.
		foreach ( $rules['groups'] as $rule_group ) {
			// If any of the groups are met return true.
			if ( $this->are_group_rules_met( $rule_group ) ) {
				return true;
			}
		}

		// If no group rules satisfy the conditions, return false.
		return false;
	}

	/**
	 * Evaluate all the rows of rules in a group.
	 *
	 * @param array $rule_group An array of rows.
	 *
	 * @return bool
	 */
	public function are_group_rules_met( $rule_group ) {
		foreach ( $rule_group as $rule_row ) {
			if ( ! isset( $rule_row['type'] ) || ! isset( $this->types[ $rule_row['type'] ] ) ) {
				continue;
			}
			$rule_type = $this->types[ $rule_row['type'] ];
			if ( ! $rule_type->evaluate_rule_row( $rule_row ) ) {
				// If this row doesn't match, the whole group fails.
				return false;
			}
		}

		// If none of the rows failed the group matches.
		return true;
	}
}
