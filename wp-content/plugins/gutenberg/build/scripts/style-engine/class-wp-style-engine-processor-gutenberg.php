<?php
/**
 * WP_Style_Engine_Processor_Gutenberg
 *
 * Compiles styles from stores or collection of CSS rules.
 *
 * @package gutenberg
 */

if ( ! class_exists( 'WP_Style_Engine_Processor_Gutenberg' ) ) {

	/**
	 * Compiles styles from stores or collection of CSS rules.
	 *
	 * @access private
	 */
	class WP_Style_Engine_Processor_Gutenberg {

		/**
		 * A collection of Style Engine Store objects.
		 *
		 * @var WP_Style_Engine_CSS_Rules_Store_Gutenberg[]
		 */
		protected $stores = array();

		/**
		 * The set of CSS rules that this processor will work on.
		 *
		 * @var WP_Style_Engine_CSS_Rule_Gutenberg[]
		 */
		protected $css_rules = array();

		/**
		 * Add a store to the processor.
		 *
		 * @param WP_Style_Engine_CSS_Rules_Store_Gutenberg $store The store to add.
		 *
		 * @return WP_Style_Engine_Processor_Gutenberg Returns the object to allow chaining methods.
		 */
		public function add_store( $store ) {
			if ( ! $store instanceof WP_Style_Engine_CSS_Rules_Store_Gutenberg ) {
				_doing_it_wrong(
					__METHOD__,
					__( '$store must be an instance of WP_Style_Engine_CSS_Rules_Store_Gutenberg', 'default' ),
					'6.1.0'
				);
				return $this;
			}

			$this->stores[ $store->get_name() ] = $store;

			return $this;
		}

		/**
		 * Adds rules to be processed.
		 *
		 * @param WP_Style_Engine_CSS_Rule_Gutenberg|WP_Style_Engine_CSS_Rule_Gutenberg[] $css_rules A single, or an array of, WP_Style_Engine_CSS_Rule_Gutenberg objects from a store or otherwise.
		 *
		 * @return WP_Style_Engine_Processor_Gutenberg Returns the object to allow chaining methods.
		 */
		public function add_rules( $css_rules ) {
			if ( ! is_array( $css_rules ) ) {
				$css_rules = array( $css_rules );
			}

			foreach ( $css_rules as $rule ) {
				$selector    = $rule->get_selector();
				$rules_group = $rule->get_rules_group();

				/**
				 * If there is an at_rule and it already exists in the css_rules array,
				 * add the rule to it.
				 * Otherwise, create a new entry for the at_rule
				 */
				if ( ! empty( $rules_group ) ) {
					if ( isset( $this->css_rules[ "$rules_group $selector" ] ) ) {
						$this->css_rules[ "$rules_group $selector" ]->add_declarations( $rule->get_declarations() );
						continue;
					}
					$this->css_rules[ "$rules_group $selector" ] = $rule;
					continue;
				}

				// If the selector already exists, add the declarations to it.
				if ( isset( $this->css_rules[ $selector ] ) ) {
					$this->css_rules[ $selector ]->add_declarations( $rule->get_declarations() );
					continue;
				}
				$this->css_rules[ $rule->get_selector() ] = $rule;
			}
			return $this;
		}

		/**
		 * Get the CSS rules as a string.
		 *
		 * Since 6.4.0 Optimization is no longer the default.
		 *
		 * @param array $options   {
		 *     Optional. An array of options. Default empty array.
		 *
		 *     @type bool $optimize Whether to optimize the CSS output, e.g., combine rules. Default is `false`.
		 *     @type bool $prettify Whether to add new lines and indents to output. Default is to inherit the value of the global constant `SCRIPT_DEBUG`, if it is defined.
		 * }
		 *
		 * @return string The computed CSS.
		 */
		public function get_css( $options = array() ) {
			$defaults = array(
				'optimize' => false,
				'prettify' => SCRIPT_DEBUG,
			);
			$options  = wp_parse_args( $options, $defaults );

			// If we have stores, get the rules from them.
			foreach ( $this->stores as $store ) {
				$this->add_rules( $store->get_all_rules() );
			}

			// Combine CSS selectors that have identical declarations.
			if ( true === $options['optimize'] ) {
				$this->combine_rules_selectors();
			}

			// Build the CSS.
			$css = '';
			foreach ( $this->css_rules as $rule ) {
				// See class WP_Style_Engine_CSS_Rule_Gutenberg for the get_css method.
				$css .= $rule->get_css( $options['prettify'] );
				$css .= $options['prettify'] ? "\n" : '';
			}
			return $css;
		}

		/**
		 * Combines selectors from the rules store when they have the same styles.
		 *
		 * @return void
		 */
		private function combine_rules_selectors() {
			// Build an array of selectors along with the JSON-ified styles to make comparisons easier.
			$selectors_json = array();
			foreach ( $this->css_rules as $rule ) {
				$declarations = $rule->get_declarations()->get_declarations();
				ksort( $declarations );
				$selectors_json[ $rule->get_selector() ] = wp_json_encode( $declarations );
			}

			// Combine selectors that have the same styles.
			foreach ( $selectors_json as $selector => $json ) {
				// Get selectors that use the same styles.
				$duplicates = array_keys( $selectors_json, $json, true );
				// Skip if there are no duplicates.
				if ( 1 >= count( $duplicates ) ) {
					continue;
				}

				$declarations = $this->css_rules[ $selector ]->get_declarations();

				foreach ( $duplicates as $key ) {
					// Unset the duplicates from the $selectors_json array to avoid looping through them as well.
					unset( $selectors_json[ $key ] );
					// Remove the rules from the rules collection.
					unset( $this->css_rules[ $key ] );
				}
				// Create a new rule with the combined selectors.
				$duplicate_selectors                     = implode( ',', $duplicates );
				$this->css_rules[ $duplicate_selectors ] = new WP_Style_Engine_CSS_Rule_Gutenberg( $duplicate_selectors, $declarations );
			}
		}
	}
}
