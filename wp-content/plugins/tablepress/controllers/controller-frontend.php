<?php
/**
 * Frontend Controller for TablePress with functionality for the frontend
 *
 * @package TablePress
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Frontend Controller class, extends Base Controller Class
 * @package TablePress
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Frontend_Controller extends TablePress_Controller {

	/**
	 * List of tables that are shown for the current request.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $shown_tables = array();

	/**
	 * Initiate Frontend functionality.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();

		/**
		 * Filter whether the TablePress Default CSS code shall be loaded.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $use Whether the Default CSS shall be loaded. Default true.
		 */
		if ( apply_filters( 'tablepress_use_default_css', true ) || TablePress::$model_options->get( 'use_custom_css' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ) );
		}

		// Add DataTables invocation calls.
		add_action( 'wp_print_footer_scripts', array( $this, 'add_datatables_calls' ), 11 ); // after inclusion of files

		// Remove WP-Table Reloaded Shortcodes and CSS, and add TablePress Shortcodes.
		add_action( 'init', array( $this, 'init_shortcodes' ), 20 ); // run on priority 20 as WP-Table Reloaded Shortcodes are registered at priority 10

		/**
		 * Filter whether the WordPress search shall also search TablePress tables.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $search Whether the TablePress tables shall be searched. Default true.
		 */
		if ( apply_filters( 'tablepress_wp_search_integration', true ) ) {
			// Extend WordPress Search to also find posts/pages that have a table with the one of the search terms in title (if shown), description (if shown), or content.
			add_filter( 'posts_search', array( $this, 'posts_search_filter' ) );
		}

		/**
		 * Load TablePress Template Tag functions.
		 */
		require_once TABLEPRESS_ABSPATH . 'controllers/template-tag-functions.php';
	}

	/**
	 * Register TablePress Shortcodes, after removing WP-Table Reloaded Shortcodes.
	 *
	 * @since 1.0.0
	 */
	public function init_shortcodes() {
		// Remove previously registered [table /] Shortcodes (e.g. from WP-Table Reloaded), as these would otherwise be used instead of TablePress's Shortcodes.
		remove_shortcode( TablePress::$shortcode );
		remove_shortcode( TablePress::$shortcode_info );
		// Dequeue WP-Table Relaoded Default CSS, as it can influence TablePress table styling.
		if ( isset( $GLOBALS['WP_Table_Reloaded_Frontend'] ) ) {
			remove_action( 'wp_head', array( $GLOBALS['WP_Table_Reloaded_Frontend'], 'add_frontend_css' ) );
		}

		add_shortcode( TablePress::$shortcode, array( $this, 'shortcode_table' ) );
		add_shortcode( TablePress::$shortcode_info, array( $this, 'shortcode_table_info' ) );
	}

	/**
	 * Enqueue CSS files for default CSS and "Custom CSS" (if desired).
	 *
	 * @since 1.0.0
	 */
	public function enqueue_css() {
		/** This filter is documented in controllers/controller-frontend.php */
		$use_default_css = apply_filters( 'tablepress_use_default_css', true );
		$custom_css = TablePress::$model_options->get( 'custom_css' );
		$use_custom_css = ( TablePress::$model_options->get( 'use_custom_css' ) && '' !== $custom_css );
		$use_custom_css_file = ( $use_custom_css && TablePress::$model_options->get( 'use_custom_css_file' ) );
		/**
		 * Filter the "Custom CSS" version number that is appended to the enqueued CSS files
		 *
		 * @since 1.0.0
		 *
		 * @param int $version The "Custom CSS" version.
		 */
		$custom_css_version = apply_filters( 'tablepress_custom_css_version', TablePress::$model_options->get( 'custom_css_version' ) );

		$tablepress_css = TablePress::load_class( 'TablePress_CSS', 'class-css.php', 'classes' );

		// Determine Default CSS URL.
		$rtl = ( is_rtl() ) ? '-rtl' : '';
		$suffix = SCRIPT_DEBUG ? '' : '.min';
		$unfiltered_default_css_url = plugins_url( "css/default{$rtl}{$suffix}.css", TABLEPRESS__FILE__ );
		/**
		 * Filter the URL from which the TablePress Default CSS file is loaded.
		 *
		 * @since 1.0.0
		 *
		 * @param string $unfiltered_default_css_url URL of the TablePress Default CSS file.
		 */
		$default_css_url = apply_filters( 'tablepress_default_css_url', $unfiltered_default_css_url );

		$use_custom_css_combined_file = ( $use_default_css && $use_custom_css_file && ! SCRIPT_DEBUG && ! is_rtl() && $unfiltered_default_css_url === $default_css_url && $tablepress_css->load_custom_css_from_file( 'combined' ) );

		if ( $use_custom_css_combined_file ) {
			$custom_css_combined_url = $tablepress_css->get_custom_css_location( 'combined', 'url' );
			// Need to use 'tablepress-default' instead of 'tablepress-combined' to not break existing TablePress Extensions.
			wp_enqueue_style( 'tablepress-default', $custom_css_combined_url, array(), $custom_css_version );
		} else {
			$custom_css_dependencies = array();
			if ( $use_default_css ) {
				wp_enqueue_style( 'tablepress-default', $default_css_url, array(), TablePress::version );
				// Add dependency to make sure that Custom CSS is printed after Default CSS.
				$custom_css_dependencies[] = 'tablepress-default';
			}

			$use_custom_css_minified_file = ( $use_custom_css_file && ! SCRIPT_DEBUG && $tablepress_css->load_custom_css_from_file( 'minified' ) );
			if ( $use_custom_css_minified_file ) {
				$custom_css_minified_url = $tablepress_css->get_custom_css_location( 'minified', 'url' );
				wp_enqueue_style( 'tablepress-custom', $custom_css_minified_url, $custom_css_dependencies, $custom_css_version );
				return;
			}

			$use_custom_css_normal_file = ( $use_custom_css_file && $tablepress_css->load_custom_css_from_file( 'normal' ) );
			if ( $use_custom_css_normal_file ) {
				$custom_css_normal_url = $tablepress_css->get_custom_css_location( 'normal', 'url' );
				wp_enqueue_style( 'tablepress-custom', $custom_css_normal_url, $custom_css_dependencies, $custom_css_version );
				return;
			}

			if ( $use_custom_css ) {
				// Get "Custom CSS" from options, try minified Custom CSS first,
				$custom_css_minified = TablePress::$model_options->get( 'custom_css_minified' );
				if ( ! empty( $custom_css_minified ) ) {
					$custom_css = $custom_css_minified;
				}
				/**
				 * Filter the "Custom CSS" code that is to be loaded as inline CSS.
				 *
				 * @since 1.0.0
				 *
				 * @param string $custom_css The "Custom CSS" code.
				 */
				$custom_css = apply_filters( 'tablepress_custom_css', $custom_css );
				if ( ! empty( $custom_css ) ) {
					// wp_add_inline_style() requires a loaded CSS file, so we have to work around that if "Default CSS" is disabled,
					if ( $use_default_css ) {
						// Handle of the file to which the <style> shall be appended.
						wp_add_inline_style( 'tablepress-default', $custom_css );
					} else {
						add_action( 'wp_head', array( $this, '_print_custom_css' ), 8 ); // priority 8 to hook in right after WP_Styles has been processed
					}
				}
			}
		}
	}

	/**
	 * Print "Custom CSS" to "wp_head" inline.
	 *
	 *  This is necessary if "Default CSS" is off, and saving "Custom CSS" to a file is not possible.
	 *
	 * @since 1.0.0
	 */
	public function _print_custom_css() {
		// Get "Custom CSS" from options, try minified Custom CSS first.
		$custom_css = TablePress::$model_options->get( 'custom_css_minified' );
		if ( empty( $custom_css ) ) {
			$custom_css = TablePress::$model_options->get( 'custom_css' );
		}
		/** This filter is documented in controllers/controller-frontend.php */
		$custom_css = apply_filters( 'tablepress_custom_css', $custom_css );
		echo "<style type='text/css'>\n{$custom_css}\n</style>\n";
	}

	/**
	 * Enqueue the DataTables JavaScript library and its dependencies.
	 *
	 * @since 1.0.0
	 */
	protected function _enqueue_datatables() {
		$js_file = 'js/jquery.datatables.min.js';
		$js_url = plugins_url( $js_file, TABLEPRESS__FILE__ );
		/**
		 * Filter the URL from which the DataTables JavaScript library file is loaded.
		 *
		 * @since 1.0.0
		 *
		 * @param string $js_url  URL of the DataTables JS library file.
		 * @param string $js_file Path and file name of the DataTables JS library file.
		 */
		$js_url = apply_filters( 'tablepress_datatables_js_url', $js_url, $js_file );
		wp_enqueue_script( 'tablepress-datatables', $js_url, array( 'jquery-core' ), TablePress::version, true );
	}

	/**
	 * Add JS code for invocation of DataTables JS library.
	 *
	 * @since 1.0.0
	 */
	public function add_datatables_calls() {
		if ( empty( $this->shown_tables ) ) {
			// There are no tables with activated DataTables on the page that is currently rendered.
			return;
		}

		// Storage for the DataTables languages.
		$datatables_languages = array();
		// Generate the specific JS commands, depending on chosen features on the "Edit" screen and the Shortcode parameters.
		$commands = array();

		foreach ( $this->shown_tables as $table_id => $table_store ) {
			if ( empty( $table_store['instances'] ) ) {
				continue;
			}
			foreach ( $table_store['instances'] as $html_id => $js_options ) {
				$parameters = array();

				// Settle dependencies/conflicts between certain features.
				if ( false !== $js_options['datatables_scrolly'] ) { // not necessarily a boolean!
					// Vertical scrolling and pagination don't work together.
					$js_options['datatables_paginate'] = false;
				}
				// Sanitize, as it may come from a Shortcode attribute.
				$js_options['datatables_paginate_entries'] = intval( $js_options['datatables_paginate_entries'] );

				// DataTables language/translation handling.
				/**
				 * Filter the locale/language for the DataTables JavaScript library.
				 *
				 * @since 1.0.0
				 *
				 * @param string $locale   The DataTables JS library locale.
				 * @param string $table_id The current table ID.
				 */
				$datatables_locale = apply_filters( 'tablepress_datatables_locale', $js_options['datatables_locale'], $table_id );
				// Only do the expensive language file checks if they haven't been done yet.
				if ( ! isset( $datatables_languages[ $datatables_locale ] ) ) {
					$orig_language_file = TABLEPRESS_ABSPATH . "i18n/datatables/lang-{$datatables_locale}.json";
					/**
					 * Filter the language file for the DataTables JavaScript library.
					 *
					 * @since 1.0.0
					 *
					 * @param string $orig_language_file Language file for the DataTables JS library.
					 * @param string $datatables_locale  Current locale/language for the DataTables JS library.
					 * @param string $path               Path of the language file.
					 */
					$language_file = apply_filters( 'tablepress_datatables_language_file', $orig_language_file, $datatables_locale, TABLEPRESS_ABSPATH ); // Make sure to check file_exists( $new_file ) when using this filter!
					// Load translation if it's not "en_US" (included as the default in DataTables) and the language file exists, or if the filter was used to change the language file.
					if ( ( 'en_US' !== $datatables_locale && file_exists( $language_file ) )
						|| ( $orig_language_file !== $language_file ) ) {
						$datatables_languages[ $datatables_locale ] = $language_file;
					}
				}
				// If translation is registered to have its strings added to the JS, add corresponding parameter to DataTables call.
				if ( isset( $datatables_languages[ $datatables_locale ] ) ) {
					$parameters['language'] = '"language":DataTables_language["' . $datatables_locale . '"]';
				}
				// These parameters need to be added for performance gain or to overwrite unwanted default behavior.
				if ( $js_options['datatables_sort'] ) {
					// No initial sort.
					$parameters['order'] = '"order":[]';
					// Don't add additional classes, to speed up sorting.
					$parameters['orderClasses'] = '"orderClasses":false';
				}
				// Alternating row colors is default, so remove them if not wanted with [].
				$parameters['stripeClasses'] = '"stripeClasses":' . ( ( $js_options['alternating_row_colors'] ) ? '["even","odd"]' : '[]' );
				// The following options are activated by default, so we only need to "false" them if we don't want them, but don't need to "true" them if we do.
				if ( ! $js_options['datatables_sort'] ) {
					$parameters['ordering'] = '"ordering":false';
				}
				if ( $js_options['datatables_paginate'] ) {
					$parameters['pagingType'] = '"pagingType":"simple"';
					if ( $js_options['datatables_lengthchange'] ) {
						$length_menu = array( 10, 25, 50, 100 );
						if ( ! in_array( $js_options['datatables_paginate_entries'], $length_menu, true ) ) {
							$length_menu[] = $js_options['datatables_paginate_entries'];
							sort( $length_menu, SORT_NUMERIC );
							$parameters['lengthMenu'] = '"lengthMenu":[' . implode( ',', $length_menu ) . ']';
						}
					} else {
						$parameters['lengthChange'] = '"lengthChange":false';
					}
					if ( 10 !== $js_options['datatables_paginate_entries'] ) {
						$parameters['pageLength'] = '"pageLength":' . $js_options['datatables_paginate_entries'];
					}
				} else {
					$parameters['paging'] = '"paging":false';
				}
				if ( ! $js_options['datatables_filter'] ) {
					$parameters['searching'] = '"searching":false';
				}
				if ( ! $js_options['datatables_info'] ) {
					$parameters['info'] = '"info":false';
				}
				if ( $js_options['datatables_scrollx'] ) {
					$parameters['scrollX'] = '"scrollX":true';
				}
				if ( false !== $js_options['datatables_scrolly'] ) {
					$parameters['scrollY'] = '"scrollY":"' . preg_replace( '#[^0-9a-z.%]#', '', $js_options['datatables_scrolly'] ) . '"';
					$parameters['scrollCollapse'] = '"scrollCollapse":true';
				}
				if ( ! empty( $js_options['datatables_custom_commands'] ) ) {
					$parameters['custom_commands'] = $js_options['datatables_custom_commands'];
				}

				/**
				 * Filter the parameters that are passed to the DataTables JavaScript library.
				 *
				 * @since 1.0.0
				 *
				 * @param array  $parameters The parameters for the DataTables JS library.
				 * @param string $table_id   The current table ID.
				 * @param string $html_id    The ID of the table HTML element.
				 * @param array  $js_options The options for the JS library.
				 */
				$parameters = apply_filters( 'tablepress_datatables_parameters', $parameters, $table_id, $html_id, $js_options );

				// If an existing parameter (in the from `"parameter":`) is set in the "Custom Commands", remove its default value.
				if ( isset( $parameters['custom_commands'] ) ) {
					foreach ( array_keys( $parameters ) as $maybe_overwritten_parameter ) {
						if ( false !== strpos( $parameters['custom_commands'], "\"{$maybe_overwritten_parameter}\":" ) ) {
							unset( $parameters[ $maybe_overwritten_parameter ] );
						}
					}
				}

				$parameters = implode( ',', $parameters );
				$parameters = ( ! empty( $parameters ) ) ? '{' . $parameters . '}' : '';

				$command = "$('#{$html_id}').dataTable({$parameters});";
				/**
				 * Filter the JavaScript command that invokes the DataTables JavaScript library on one table.
				 *
				 * @since 1.0.0
				 *
				 * @param string $command    The JS command for the DataTables JS library.
				 * @param string $html_id    The ID of the table HTML element.
				 * @param array  $parameters The parameters for the DataTables JS library.
				 * @param string $table_id   The current table ID.
				 * @param array  $js_options The options for the JS library.
				 */
				$command = apply_filters( 'tablepress_datatables_command', $command, $html_id, $parameters, $table_id, $js_options );
				if ( ! empty( $command ) ) {
					$commands[] = $command;
				}
			}
		}

		$commands = implode( "\n", $commands );
		/**
		 * Filter the JavaScript commands that invoke the DataTables JavaScript library on all tables on the page.
		 *
		 * @since 1.0.0
		 *
		 * @param array $commands The JS commands for the DataTables JS library.
		 */
		$commands = apply_filters( 'tablepress_all_datatables_commands', $commands );
		if ( empty( $commands ) ) {
			return;
		}

		// DataTables language/translation handling.
		$datatables_strings = '';
		foreach ( $datatables_languages as $locale => $language_file ) {
			$strings = file_get_contents( $language_file );
			// Remove unnecessary white space.
			$strings = str_replace( array( "\n", "\r", "\t" ), '', $strings );
			$datatables_strings .= "DataTables_language[\"{$locale}\"]={$strings};\n";
		}
		if ( ! empty( $datatables_strings ) ) {
			$datatables_strings = "var DataTables_language={};\n" . $datatables_strings;
		}

		// Echo DataTables strings and JS calls.
		echo <<<JS
<script type="text/javascript">
jQuery(document).ready(function($){
{$datatables_strings}{$commands}
});
</script>
JS;
	}

	/**
	 * Handle Shortcode [table id=<ID> /] in `the_content()`.
	 *
	 * @since 1.0.0
	 *
	 * @param array $shortcode_atts List of attributes that where included in the Shortcode.
	 * @return string Resulting HTML code for the table with the ID <ID>.
	 */
	public function shortcode_table( $shortcode_atts ) {
		// For empty Shortcodes like [table] or [table /], an empty string is passed, see WP Core #26927.
		$shortcode_atts = (array) $shortcode_atts;

		$_render = TablePress::load_class( 'TablePress_Render', 'class-render.php', 'classes' );

		$default_shortcode_atts = $_render->get_default_render_options();
		/**
		 * Filter the available/default attributes for the [table] Shortcode.
		 *
		 * @since 1.0.0
		 *
		 * @param array $default_shortcode_atts The [table] Shortcode default attributes.
		 */
		$default_shortcode_atts = apply_filters( 'tablepress_shortcode_table_default_shortcode_atts', $default_shortcode_atts );
		// Parse Shortcode attributes, only allow those that are specified.
		$shortcode_atts = shortcode_atts( $default_shortcode_atts, $shortcode_atts ); // Optional third argument left out on purpose. Use filter in the next line instead.
		/**
		 * Filter the attributes that were passed to the [table] Shortcode.
		 *
		 * @since 1.0.0
		 *
		 * @param array $shortcode_atts The attributes passed to the [table] Shortcode.
		 */
		$shortcode_atts = apply_filters( 'tablepress_shortcode_table_shortcode_atts', $shortcode_atts );

		// Check, if a table with the given ID exists.
		$table_id = preg_replace( '/[^a-zA-Z0-9_-]/', '', $shortcode_atts['id'] );
		if ( ! TablePress::$model_table->table_exists( $table_id ) ) {
			$message = "[table &#8220;{$table_id}&#8221; not found /]<br />\n";
			/**
			 * Filter the "Table not found" message.
			 *
			 * @since 1.0.0
			 *
			 * @param string $message  The "Table not found" message.
			 * @param string $table_id The current table ID.
			 */
			$message = apply_filters( 'tablepress_table_not_found_message', $message, $table_id );
			return $message;
		}

		// Load table, with table data, options, and visibility settings.
		$table = TablePress::$model_table->load( $table_id, true, true );
		if ( is_wp_error( $table ) ) {
			$message = "[table &#8220;{$table_id}&#8221; could not be loaded /]<br />\n";
			/**
			 * Filter the "Table could not be loaded" message.
			 *
			 * @since 1.0.0
			 *
			 * @param string   $message  The "Table could not be loaded" message.
			 * @param string   $table_id The current table ID.
			 * @param WP_Error $table    The error object for the table.
			 */
			$message = apply_filters( 'tablepress_table_load_error_message', $message, $table_id, $table );
			return $message;
		}
		if ( isset( $table['is_corrupted'] ) && $table['is_corrupted'] ) {
			$message = "<div>Attention: The internal data of table &#8220;{$table_id}&#8221; is corrupted!</div>";
			/**
			 * Filter the "Table data is corrupted" message.
			 *
			 * @since 1.0.0
			 *
			 * @param string $message    The "Table data is corrupted" message.
			 * @param string $table_id   The current table ID.
			 * @param string $json_error The JSON error with information about the corrupted table.
			 */
			$message = apply_filters( 'tablepress_table_corrupted_message', $message, $table_id, $table['json_error'] );
			return $message;
		}

		/**
		 * Filter whether the "datatables_custom_commands" Shortcode parameter is disabled.
		 *
		 * By default, the "datatables_custom_commands" Shortcode parameter is disabled for security reasons.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $disable Whether to disable the "datatables_custom_commands" Shortcode parameter. Default true.
		 */
		if ( ! is_null( $shortcode_atts['datatables_custom_commands'] ) && apply_filters( 'tablepress_disable_custom_commands_shortcode_parameter', true ) ) {
			$shortcode_atts['datatables_custom_commands'] = null;
		}

		// Determine options to use (if set in Shortcode, use those, otherwise use stored options, from the "Edit" screen).
		$render_options = array();
		foreach ( $shortcode_atts as $key => $value ) {
			// We have to check this, because strings 'true' or 'false' are not recognized as boolean!
			if ( is_string( $value ) && 'true' === strtolower( $value ) ) {
				$render_options[ $key ] = true;
			} elseif ( is_string( $value ) && 'false' === strtolower( $value ) ) {
				$render_options[ $key ] = false;
			} elseif ( is_null( $value ) && isset( $table['options'][ $key ] ) ) {
				$render_options[ $key ] = $table['options'][ $key ];
			} else {
				$render_options[ $key ] = $value;
			}
		}

		// Generate unique HTML ID, depending on how often this table has already been shown on this page.
		if ( ! isset( $this->shown_tables[ $table_id ] ) ) {
			$this->shown_tables[ $table_id ] = array(
				'count'     => 0,
				'instances' => array(),
			);
		}
		$this->shown_tables[ $table_id ]['count']++;
		$count = $this->shown_tables[ $table_id ]['count'];
		$render_options['html_id'] = "tablepress-{$table_id}";
		if ( $count > 1 ) {
			$render_options['html_id'] .= "-no-{$count}";
		}
		/**
		 * Filter the ID of the table HTML element.
		 *
		 * @since 1.0.0
		 *
		 * @param string $html_id  The ID of the table HTML element.
		 * @param string $table_id The current table ID.
		 * @param string $count    Number of copies of the table with this table ID on the page.
		 */
		$render_options['html_id'] = apply_filters( 'tablepress_html_id', $render_options['html_id'], $table_id, $count );

		// Generate the "Edit Table" link.
		$render_options['edit_table_url'] = '';
		/**
		 * Filter whether the "Edit" link below the table shall be shown.
		 *
		 * The "Edit" link is only shown to logged-in users who possess the necessary capability to edit the table.
		 *
		 * @since 1.0.0
		 *
		 * @param bool   $show     Whether to show the "Edit" link below the table. Default true.
		 * @param string $table_id The current table ID.
		 */
		if ( is_user_logged_in() && apply_filters( 'tablepress_edit_link_below_table', true, $table['id'] ) && current_user_can( 'tablepress_edit_table', $table['id'] ) ) {
			$render_options['edit_table_url'] = TablePress::url( array( 'action' => 'edit', 'table_id' => $table['id'] ) );
		}

		/**
		 * Filter the render options for the table.
		 *
		 * The render options are determined from the settings on a table's "Edit" screen and the Shortcode parameters.
		 *
		 * @since 1.0.0
		 *
		 * @param array $render_options The render options for the table.
		 * @param array $table          The current table.
		 */
		$render_options = apply_filters( 'tablepress_table_render_options', $render_options, $table );

		// Eventually add this table to list of tables which have a JS library enabled and thus are to be included in the script's call in the footer.
		if ( $render_options['use_datatables'] && $render_options['table_head'] && count( $table['data'] ) > 1 ) {
			// Get options for the DataTables JavaScript library from the table's render options.
			$js_options = array();
			foreach ( array(
				'alternating_row_colors',
				'datatables_sort',
				'datatables_paginate',
				'datatables_paginate',
				'datatables_paginate_entries',
				'datatables_lengthchange',
				'datatables_filter',
				'datatables_info',
				'datatables_scrollx',
				'datatables_scrolly',
				'datatables_locale',
				'datatables_custom_commands',
			) as $option ) {
				$js_options[ $option ] = $render_options[ $option ];
			}
			/**
			 * Filter the JavaScript options for the table.
			 *
			 * The JavaScript options are determined from the settings on a table's "Edit" screen and the Shortcode parameters.
			 * They are part of the render options and can be overwritten with Shortcode parameters.
			 *
			 * @since 1.0.0
			 *
			 * @param array  $js_options     The JavaScript options for the table.
			 * @param string $table_id       The current table ID.
			 * @param array  $render_options The render options for the table.
			 */
			$js_options = apply_filters( 'tablepress_table_js_options', $js_options, $table_id, $render_options );
			$this->shown_tables[ $table_id ]['instances'][ $render_options['html_id'] ] = $js_options;
			$this->_enqueue_datatables();
		}

		// Check if table output shall and can be loaded from the transient cache, otherwise generate the output.
		if ( $render_options['cache_table_output'] && ! is_user_logged_in() ) {
			// Hash the Render Options array to get a unique cache identifier.
			$table_hash = md5( wp_json_encode( $render_options ) );
			$transient_name = 'tablepress_' . $table_hash; // Attention: This string must not be longer than 45 characters!
			$output = get_transient( $transient_name );
			if ( false === $output || '' === $output ) {
				// Render/generate the table HTML, as it was not found in the cache.
				$_render->set_input( $table, $render_options );
				$output = $_render->get_output();
				// Save render output in a transient, set cache timeout to 24 hours.
				set_transient( $transient_name, $output, DAY_IN_SECONDS );
				// Update output caches list transient (necessary for cache invalidation upon table saving).
				$caches_list_transient_name = 'tablepress_c_' . md5( $table_id );
				$caches_list = get_transient( $caches_list_transient_name );
				if ( false === $caches_list ) {
					$caches_list = array();
				} else {
					$caches_list = (array) json_decode( $caches_list, true );
				}
				if ( ! in_array( $transient_name, $caches_list, true ) ) {
					$caches_list[] = $transient_name;
				}
				set_transient( $caches_list_transient_name, wp_json_encode( $caches_list ), 2 * DAY_IN_SECONDS );
			} else {
				/**
				 * Filter the cache hit comment message.
				 *
				 * @since 1.0.0
				 *
				 * @param string $comment The cache hit comment message.
				 */
				$output .= apply_filters( 'tablepress_cache_hit_comment', "<!-- #{$render_options['html_id']} from cache -->" );
			}
		} else {
			// Render/generate the table HTML, as no cache is to be used.
			$_render->set_input( $table, $render_options );
			$output = $_render->get_output();
		}

		// Maybe print a list of used render options.
		if ( $render_options['shortcode_debug'] && is_user_logged_in() ) {
			$output .= '<pre>' . var_export( $render_options, true ) . '</pre>';
		}

		return $output;
	}

	/**
	 * Handle Shortcode [table-info id=<ID> field=<name> /] in the_content().
	 *
	 * @since 1.0.0
	 *
	 * @param array $shortcode_atts List of attributes that where included in the Shortcode.
	 * @return string Text that replaces the Shortcode (error message or asked-for information).
	 */
	public function shortcode_table_info( $shortcode_atts ) {
		// For empty Shortcodes like [table-info] or [table-info /], an empty string is passed, see Core #26927.
		$shortcode_atts = (array) $shortcode_atts;

		// Parse Shortcode attributes, only allow those that are specified.
		$default_shortcode_atts = array(
			'id'     => '',
			'field'  => '',
			'format' => '',
		);
		/**
		 * Filter the available/default attributes for the [table-info] Shortcode.
		 *
		 * @since 1.0.0
		 *
		 * @param array $default_shortcode_atts The [table-info] Shortcode default attributes.
		 */
		$default_shortcode_atts = apply_filters( 'tablepress_shortcode_table_info_default_shortcode_atts', $default_shortcode_atts );
		$shortcode_atts = shortcode_atts( $default_shortcode_atts, $shortcode_atts ); // Optional third argument left out on purpose. Use filter in the next line instead.
		/**
		 * Filter the attributes that were passed to the [table-info] Shortcode.
		 *
		 * @since 1.0.0
		 *
		 * @param array $shortcode_atts The attributes passed to the [table-info] Shortcode.
		 */
		$shortcode_atts = apply_filters( 'tablepress_shortcode_table_info_shortcode_atts', $shortcode_atts );

		/**
		 * Filter whether the output of the [table-info] Shortcode is overwritten/short-circuited.
		 *
		 * @since 1.0.0
		 *
		 * @param bool|string $overwrite      Whether the [table-info] output is overwritten. Return false for the regular content, and a string to overwrite the output.
		 * @param array       $shortcode_atts The attributes passed to the [table-info] Shortcode.
		 */
		$overwrite = apply_filters( 'tablepress_shortcode_table_info_overwrite', false, $shortcode_atts );
		if ( $overwrite ) {
			return $overwrite;
		}

		// Check, if a table with the given ID exists.
		$table_id = preg_replace( '/[^a-zA-Z0-9_-]/', '', $shortcode_atts['id'] );
		if ( ! TablePress::$model_table->table_exists( $table_id ) ) {
			$message = "[table &#8220;{$table_id}&#8221; not found /]<br />\n";
			/** This filter is documented in controllers/controller-frontend.php */
			$message = apply_filters( 'tablepress_table_not_found_message', $message, $table_id );
			return $message;
		}

		// Load table, with table data, options, and visibility settings.
		$table = TablePress::$model_table->load( $table_id, true, true );
		if ( is_wp_error( $table ) ) {
			$message = "[table &#8220;{$table_id}&#8221; could not be loaded /]<br />\n";
			/** This filter is documented in controllers/controller-frontend.php */
			$message = apply_filters( 'tablepress_table_load_error_message', $message, $table_id, $table );
			return $message;
		}

		$field = preg_replace( '/[^a-z_]/', '', strtolower( $shortcode_atts['field'] ) );
		$format = preg_replace( '/[^a-z]/', '', strtolower( $shortcode_atts['format'] ) );

		// Generate output, depending on what information (field) was asked for.
		switch ( $field ) {
			case 'name':
			case 'description':
				$output = $table[ $field ];
				break;
			case 'last_modified':
				switch ( $format ) {
					case 'raw':
						$output = $table['last_modified'];
						break;
					case 'human':
						$modified_timestamp = strtotime( $table['last_modified'] );
						$current_timestamp = current_time( 'timestamp' );
						$time_diff = $current_timestamp - $modified_timestamp;
						// Time difference is only shown up to one day.
						if ( $time_diff >= 0 && $time_diff < DAY_IN_SECONDS ) {
							$output = sprintf( __( '%s ago', 'default' ), human_time_diff( $modified_timestamp, $current_timestamp ) ); // No `tablepress` text domain as translations are not loaded.
						} else {
							$output = TablePress::format_datetime( $table['last_modified'], 'mysql', '<br />' );
						}
						break;
					case 'date':
						$modified_timestamp = strtotime( $table['last_modified'] );
						$output = date_i18n( get_option( 'date_format' ), $modified_timestamp );
						break;
					case 'time':
						$modified_timestamp = strtotime( $table['last_modified'] );
						$output = date_i18n( get_option( 'time_format' ), $modified_timestamp );
						break;
					case 'mysql':
					default:
						$output = TablePress::format_datetime( $table['last_modified'], 'mysql', ' ' );
						break;
				}
				break;
			case 'last_editor':
				$output = TablePress::get_user_display_name( $table['options']['last_editor'] );
				break;
			case 'author':
				$output = TablePress::get_user_display_name( $table['author'] );
				break;
			case 'number_rows':
				$output = count( $table['data'] );
				if ( 'raw' !== $format ) {
					if ( $table['options']['table_head'] ) {
						$output = $output - 1;
					}
					if ( $table['options']['table_foot'] ) {
						$output = $output - 1;
					}
				}
				break;
			case 'number_columns':
				$output = count( $table['data'][0] );
				break;
			default:
				$output = "[table-info field &#8220;{$field}&#8221; not found in table &#8220;{$table_id}&#8221; /]<br />\n";
				/**
				 * Filter the "table info field not found" message.
				 *
				 * @since 1.0.0
				 *
				 * @param string $output The "table info field not found" message.
				 * @param array  $table  The current table ID.
				 * @param string $field  The field that was not found.
				 * @param string $format The return format for the field.
				 */
				$output = apply_filters( 'tablepress_table_info_not_found_message', $output, $table, $field, $format );
		}

		/**
		 * Filter the output of the [table-info] Shortcode.
		 *
		 * @since 1.0.0
		 *
		 * @param string $output         The output of the [table-info] Shortcode.
		 * @param array  $table          The current table.
		 * @param array  $shortcode_atts The attributes passed to the [table-info] Shortcode.
		 */
		$output = apply_filters( 'tablepress_shortcode_table_info_output', $output, $table, $shortcode_atts );
		return $output;
	}

	/**
	 * Expand WP Search to also find posts and pages that have a search term in a table that is shown in them.
	 *
	 * This is done by looping through all search terms and TablePress tables and searching there for the search term,
	 * saving all tables's IDs that have a search term and then expanding the WP query to search for posts or pages that have the
	 * Shortcode for one of these tables in their content.
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $search_sql Current part of the "WHERE" clause of the SQL statement used to get posts/pages from the WP database that is related to searching.
	 * @return string Eventually extended SQL "WHERE" clause, to also find posts/pages with Shortcodes in them.
	 */
	public function posts_search_filter( $search_sql ) {
		global $wpdb;

		if ( ! is_search() || ! is_main_query() ) {
			return $search_sql;
		}

		// Get variable that contains all search terms, parsed from $_GET['s'] by WP.
		$search_terms = get_query_var( 'search_terms' );
		if ( empty( $search_terms ) || ! is_array( $search_terms ) ) {
			return $search_sql;
		}

		// Load all table IDs and prime post meta cache for cached access to options and visibility settings of the tables, don't run filter hook.
		$table_ids = TablePress::$model_table->load_all( true, false );
		// Array of all search words that were found, and the table IDs where they were found.
		$query_result = array();

		foreach ( $table_ids as $table_id ) {
			// Load table, with table data, options, and visibility settings.
			$table = TablePress::$model_table->load( $table_id, true, true );

			if ( isset( $table['is_corrupted'] ) && $table['is_corrupted'] ) {
				// Do not search in corrupted tables.
				continue;
			}

			foreach ( $search_terms as $search_term ) {
				if ( ( $table['options']['print_name'] && false !== stripos( $table['name'], $search_term ) )
					|| ( $table['options']['print_description'] && false !== stripos( $table['description'], $search_term ) ) ) {
					// Found the search term in the name or description (and they are shown).
					$query_result[ $search_term ][] = $table_id; // Add table ID to result list.
					// No need to continue searching this search term in this table.
					continue;
				}

				// Search search term in visible table cells (without taking Shortcode parameters into account!).
				foreach ( $table['data'] as $row_idx => $table_row ) {
					if ( 0 === $table['visibility']['rows'][ $row_idx ] ) {
						// Row is hidden, so don't search in it.
						continue;
					}
					foreach ( $table_row as $col_idx => $table_cell ) {
						if ( 0 === $table['visibility']['columns'][ $col_idx ] ) {
							// Column is hidden, so don't search in it.
							continue;
						}
						// @TODO: Cells are not evaluated here, so math formulas are searched.
						if ( false !== stripos( $table_cell, $search_term ) ) {
							// Found the  search term in the cell content.
							$query_result[ $search_term ][] = $table_id; // Add table ID to result list
							// No need to continue searching this search term in this table.
							continue 3;
						}
					}
				}
			}
		}

		// For all found table IDs for each search term, add additional OR statement to the SQL "WHERE" clause.

		// If $_GET['exact'] is set, WordPress doesn't use % in SQL LIKE clauses.
		$exact = get_query_var( 'exact' );
		$n = ( empty( $exact ) ) ? '%' : '';
		$search_sql = $wpdb->remove_placeholder_escape( $search_sql );
		foreach ( $query_result as $search_term => $table_ids ) {
			$search_term = esc_sql( $wpdb->esc_like( $search_term ) );
			$old_or = "OR ({$wpdb->posts}.post_content LIKE '{$n}{$search_term}{$n}')";
			$table_ids = implode( '|', $table_ids );
			$regexp = '\\\\[' . TablePress::$shortcode . ' id=(["\\\']?)(' . $table_ids . ')([\]"\\\' /])'; // ' needs to be single escaped, [ double escaped (with \\) in mySQL
			$new_or = $old_or . " OR ({$wpdb->posts}.post_content REGEXP '{$regexp}')";
			$search_sql = str_replace( $old_or, $new_or, $search_sql );
		}
		$search_sql = $wpdb->add_placeholder_escape( $search_sql );

		return $search_sql;
	}

} // class TablePress_Frontend_Controller
