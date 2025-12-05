<?php
namespace Elementor\Modules\Usage;

use Elementor\Core\Base\Document;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\DynamicTags\Manager;
use Elementor\Modules\System_Info\Module as System_Info;
use Elementor\Plugin;
use Elementor\Settings;
use Elementor\Tracker;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor usage module.
 *
 * Elementor usage module handler class is responsible for registering and
 * managing Elementor usage data.
 */
class Module extends BaseModule {
	const GENERAL_TAB = 'general';
	const META_KEY = '_elementor_controls_usage';
	const OPTION_NAME = 'elementor_controls_usage';

	/**
	 * @var bool
	 */
	private $is_document_saving = false;

	/**
	 * Get module name.
	 *
	 * Retrieve the usage module name.
	 *
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'usage';
	}

	/**
	 * Get doc type count.
	 *
	 * Get count of documents based on doc type
	 *
	 * Remove 'wp-' from $doc_type for BC, support doc type change since 2.7.0.
	 *
	 * @param \Elementor\Core\Documents_Manager $doc_class
	 * @param String                            $doc_type
	 *
	 * @return int
	 */
	public function get_doc_type_count( $doc_class, $doc_type ) {
		static $posts = null;
		static $library = null;

		if ( null === $posts ) {
			$posts = \Elementor\Tracker::get_posts_usage();
		}

		if ( null === $library ) {
			$library = \Elementor\Tracker::get_library_usage();
		}

		$posts_usage = $posts;

		if ( $doc_class::get_property( 'show_in_library' ) ) {
			$posts_usage = $library;
		}

		$doc_type_common = str_replace( 'wp-', '', $doc_type );

		$doc_usage = isset( $posts_usage[ $doc_type_common ] ) ? $posts_usage[ $doc_type_common ] : 0;

		return is_array( $doc_usage ) ? $doc_usage['publish'] : $doc_usage;
	}

	/**
	 * Get formatted usage.
	 *
	 * Retrieve formatted usage, for frontend.
	 *
	 * @param String $format Optional. Default is 'html'.
	 *
	 * @return array
	 */
	public function get_formatted_usage( $format = 'html' ) {
		$usage = [];

		foreach ( get_option( self::OPTION_NAME, [] ) as $doc_type => $elements ) {
			$doc_class = Plugin::$instance->documents->get_document_type( $doc_type );

			if ( 'html' === $format && $doc_class ) {
				$doc_title = $doc_class::get_title();
			} else {
				$doc_title = $doc_type;
			}

			$doc_count = $this->get_doc_type_count( $doc_class, $doc_type );

			$tab_group = $doc_class::get_property( 'admin_tab_group' );

			if ( 'html' === $format && $tab_group ) {
				$doc_title = ucwords( $tab_group ) . ' - ' . $doc_title;
			}

			// Replace element type with element title.
			foreach ( $elements as $element_type => $data ) {
				unset( $elements[ $element_type ] );

				if ( in_array( $element_type, [ 'section', 'column' ], true ) ) {
					continue;
				}

				$widget_instance = Plugin::$instance->widgets_manager->get_widget_types( $element_type );

				if ( 'html' === $format && $widget_instance ) {
					$widget_title = $widget_instance->get_title();
				} else {
					$widget_title = $element_type;
				}

				$widget_title = apply_filters( 'elementor/usage/elements/element_title', $widget_title, $element_type );

				$elements[ $widget_title ] = $data['count'];
			}

			// Sort elements by key.
			ksort( $elements );

			$usage[ $doc_type ] = [
				'title' => $doc_title,
				'elements' => $elements,
				'count' => $doc_count,
			];

			// ' ? 1 : 0;' In sorters is compatibility for PHP8.0.
			// Sort usage by title.
			uasort( $usage, function( $a, $b ) {
				return ( $a['title'] > $b['title'] ) ? 1 : 0;
			} );

			// If title includes '-' will have lower priority.
			uasort( $usage, function( $a ) {
				return strpos( $a['title'], '-' ) ? 1 : 0;
			} );
		}

		return $usage;
	}

	/**
	 * Before document Save.
	 *
	 * Called on elementor/document/before_save, remove document from global & set saving flag.
	 *
	 * @param Document $document
	 * @param array    $data new settings to save.
	 */
	public function before_document_save( $document, $data ) {
		$current_status = get_post_status( $document->get_post() );
		$new_status = isset( $data['settings']['post_status'] ) ? $data['settings']['post_status'] : '';

		if ( $current_status === $new_status ) {
			$this->remove_from_global( $document );
		}

		$this->is_document_saving = true;
	}

	/**
	 * After document save.
	 *
	 * Called on elementor/document/after_save, adds document to global & clear saving flag.
	 *
	 * @param Document $document
	 */
	public function after_document_save( $document ) {
		if ( Document::STATUS_PUBLISH === $document->get_post()->post_status || Document::STATUS_PRIVATE === $document->get_post()->post_status ) {
			$this->save_document_usage( $document );
		}

		$this->is_document_saving = false;
	}

	/**
	 * On status change.
	 *
	 * Called on transition_post_status.
	 *
	 * @param string   $new_status
	 * @param string   $old_status
	 * @param \WP_Post $post
	 */
	public function on_status_change( $new_status, $old_status, $post ) {
		if ( wp_is_post_autosave( $post ) ) {
			return;
		}

		// If it's from elementor editor, the usage should be saved via `before_document_save`/`after_document_save`.
		if ( $this->is_document_saving ) {
			return;
		}

		$document = Plugin::$instance->documents->get( $post->ID );

		if ( ! $document ) {
			return;
		}

		$is_public_unpublish = 'publish' === $old_status && 'publish' !== $new_status;
		$is_private_unpublish = 'private' === $old_status && 'private' !== $new_status;

		if ( $is_public_unpublish || $is_private_unpublish ) {
			$this->remove_from_global( $document );
		}

		$is_public_publish = 'publish' !== $old_status && 'publish' === $new_status;
		$is_private_publish = 'private' !== $old_status && 'private' === $new_status;

		if ( $is_public_publish || $is_private_publish ) {
			$this->save_document_usage( $document );
		}
	}

	/**
	 * On before delete post.
	 *
	 * Called on on_before_delete_post.
	 *
	 * @param int $post_id
	 */
	public function on_before_delete_post( $post_id ) {
		$document = Plugin::$instance->documents->get( $post_id );

		if ( $document->get_id() !== $document->get_main_id() ) {
			return;
		}

		$this->remove_from_global( $document );
	}

	/**
	 * Add's tracking data.
	 *
	 * Called on elementor/tracker/send_tracking_data_params.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function add_tracking_data( $params ) {
		$params['usages']['elements'] = get_option( self::OPTION_NAME );

		return $params;
	}

	/**
	 * Recalculate usage.
	 *
	 * Recalculate usage for all elementor posts.
	 *
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return int
	 */
	public function recalc_usage( $limit = -1, $offset = 0 ) {
		// While requesting recalc_usage, data should be deleted.
		// if its in a batch the data should be deleted only on the first batch.
		if ( 0 === $offset ) {
			delete_option( self::OPTION_NAME );
		}

		$post_types = get_post_types( [ 'public' => true ] );

		$query = new \WP_Query( [
			'no_found_rows' => true,
			'meta_key' => '_elementor_data',
			'post_type' => $post_types,
			'post_status' => [ 'publish', 'private' ],
			'posts_per_page' => $limit,
			'offset' => $offset,
		] );

		foreach ( $query->posts as $post ) {
			$document = Plugin::$instance->documents->get( $post->ID );

			if ( ! $document ) {
				continue;
			}

			$this->after_document_save( $document );
		}

		// Clear query memory before leave.
		wp_cache_flush();

		return count( $query->posts );
	}

	/**
	 * Increase controls count.
	 *
	 * Increase controls count, for each element.
	 *
	 * @param array  &$element_ref
	 * @param string $tab
	 * @param string $section
	 * @param string $control
	 * @param int    $count
	 */
	private function increase_controls_count( &$element_ref, $tab, $section, $control, $count ) {
		if ( ! isset( $element_ref['controls'][ $tab ] ) ) {
			$element_ref['controls'][ $tab ] = [];
		}

		if ( ! isset( $element_ref['controls'][ $tab ][ $section ] ) ) {
			$element_ref['controls'][ $tab ][ $section ] = [];
		}

		if ( ! isset( $element_ref['controls'][ $tab ][ $section ][ $control ] ) ) {
			$element_ref['controls'][ $tab ][ $section ][ $control ] = 0;
		}

		$element_ref['controls'][ $tab ][ $section ][ $control ] += $count;
	}

	/**
	 * Add Controls
	 *
	 * Add's controls to this element_ref, returns changed controls count.
	 *
	 * @param array $settings_controls
	 * @param array $element_controls
	 * @param array &$element_ref
	 *
	 * @return int ($changed_controls_count).
	 */
	private function add_controls( $settings_controls, $element_controls, &$element_ref ) {
		$changed_controls_count = 0;

		// Loop over all element settings.
		foreach ( $settings_controls as $control => $value ) {
			if ( empty( $element_controls[ $control ] ) ) {
				continue;
			}

			$control_config = $element_controls[ $control ];

			if ( ! isset( $control_config['section'], $control_config['default'] ) ) {
				continue;
			}

			$tab = $control_config['tab'];
			$section = $control_config['section'];

			// If setting value is not the control default.
			if ( $value !== $control_config['default'] ) {
				$this->increase_controls_count( $element_ref, $tab, $section, $control, 1 );

				++$changed_controls_count;
			}
		}

		return $changed_controls_count;
	}

	/**
	 * Add general controls.
	 *
	 * Extract general controls to element ref, return clean `$settings_control`.
	 *
	 * @param array $settings_controls
	 * @param array &$element_ref
	 *
	 * @return array ($settings_controls).
	 */
	private function add_general_controls( $settings_controls, &$element_ref ) {
		if ( ! empty( $settings_controls[ Manager::DYNAMIC_SETTING_KEY ] ) ) {
			$settings_controls = array_merge( $settings_controls, $settings_controls[ Manager::DYNAMIC_SETTING_KEY ] );

			// Add dynamic count to controls under `general` tab.
			$this->increase_controls_count(
				$element_ref,
				self::GENERAL_TAB,
				Manager::DYNAMIC_SETTING_KEY,
				'count',
				count( $settings_controls[ Manager::DYNAMIC_SETTING_KEY ] )
			);
		}

		return $settings_controls;
	}

	/**
	 * Add to global.
	 *
	 * Add's usage to global (update database).
	 *
	 * @param string $doc_name
	 * @param array  $doc_usage
	 */
	private function add_to_global( $doc_name, $doc_usage ) {
		$global_usage = get_option( self::OPTION_NAME, [] );

		foreach ( $doc_usage as $element_type => $element_data ) {
			if ( ! isset( $global_usage[ $doc_name ] ) ) {
				$global_usage[ $doc_name ] = [];
			}

			if ( ! isset( $global_usage[ $doc_name ][ $element_type ] ) ) {
				$global_usage[ $doc_name ][ $element_type ] = [
					'count' => 0,
					'controls' => [],
				];
			}

			$global_element_ref = &$global_usage[ $doc_name ][ $element_type ];
			$global_element_ref['count'] += $element_data['count'];

			if ( empty( $element_data['controls'] ) ) {
				continue;
			}

			foreach ( $element_data['controls'] as $tab => $sections ) {
				foreach ( $sections as $section => $controls ) {
					foreach ( $controls as $control => $count ) {
						$this->increase_controls_count( $global_element_ref, $tab, $section, $control, $count );
					}
				}
			}
		}

		update_option( self::OPTION_NAME, $global_usage, false );
	}

	/**
	 * Remove from global.
	 *
	 * Remove's usage from global (update database).
	 *
	 * @param Document $document
	 */
	private function remove_from_global( $document ) {
		$prev_usage = $document->get_meta( self::META_KEY );

		if ( empty( $prev_usage ) ) {
			return;
		}

		$doc_name = $document->get_name();

		$global_usage = get_option( self::OPTION_NAME, [] );

		foreach ( $prev_usage as $element_type => $doc_value ) {
			if ( isset( $global_usage[ $doc_name ][ $element_type ]['count'] ) ) {
				$global_usage[ $doc_name ][ $element_type ]['count'] -= $prev_usage[ $element_type ]['count'];

				if ( 0 === $global_usage[ $doc_name ][ $element_type ]['count'] ) {
					unset( $global_usage[ $doc_name ][ $element_type ] );

					if ( 0 === count( $global_usage[ $doc_name ] ) ) {
						unset( $global_usage[ $doc_name ] );
					}

					continue;
				}

				foreach ( $prev_usage[ $element_type ]['controls'] as $tab => $sections ) {
					foreach ( $sections as $section => $controls ) {
						foreach ( $controls as $control => $count ) {
							if ( isset( $global_usage[ $doc_name ][ $element_type ]['controls'][ $tab ][ $section ][ $control ] ) ) {
								$section_ref = &$global_usage[ $doc_name ][ $element_type ]['controls'][ $tab ][ $section ];

								$section_ref[ $control ] -= $count;

								if ( 0 === $section_ref[ $control ] ) {
									unset( $section_ref[ $control ] );
								}
							}
						}
					}
				}
			}
		}

		update_option( self::OPTION_NAME, $global_usage, false );

		$document->delete_meta( self::META_KEY );
	}

	/**
	 * Get elements usage.
	 *
	 * Get's the current elements usage by passed elements array parameter.
	 *
	 * @param array $elements
	 *
	 * @return array
	 */
	private function get_elements_usage( $elements ) {
		$usage = [];

		Plugin::$instance->db->iterate_data( $elements, function ( $element ) use ( &$usage ) {
			if ( empty( $element['widgetType'] ) ) {
				$type = $element['elType'];
				$element_instance = Plugin::$instance->elements_manager->get_element_types( $type );
			} else {
				$type = $element['widgetType'];
				$element_instance = Plugin::$instance->widgets_manager->get_widget_types( $type );
			}

			if ( ! isset( $usage[ $type ] ) ) {
				$usage[ $type ] = [
					'count' => 0,
					'control_percent' => 0,
					'controls' => [],
				];
			}

			$usage[ $type ]['count']++;

			if ( ! $element_instance ) {
				return $element;
			}

			$element_controls = $element_instance->get_controls();

			if ( isset( $element['settings'] ) ) {
				$settings_controls = $element['settings'];
				$element_ref = &$usage[ $type ];

				// Add dynamic values.
				$settings_controls = $this->add_general_controls( $settings_controls, $element_ref );

				$changed_controls_count = $this->add_controls( $settings_controls, $element_controls, $element_ref );

				$percent = ! empty( $element_controls ) ? $changed_controls_count / ( count( $element_controls ) / 100 ) : 0;

				$usage[ $type ] ['control_percent'] = (int) round( $percent );
			}

			return $element;
		} );

		return $usage;
	}

	/**
	 * Save document usage.
	 *
	 * Save requested document usage, and update global.
	 *
	 * @param Document $document
	 */
	private function save_document_usage( Document $document ) {
		if ( ! $document::get_property( 'is_editable' ) && ! $document->is_built_with_elementor() ) {
			return;
		}

		// Get data manually to avoid conflict with `\Elementor\Core\Base\Document::get_elements_data... convert_to_elementor`.
		$data = $document->get_json_meta( '_elementor_data' );

		if ( ! empty( $data ) ) {
			try {
				$usage = $this->get_elements_usage( $document->get_elements_raw_data( $data ) );

				$document->update_meta( self::META_KEY, $usage );

				$this->add_to_global( $document->get_name(), $usage );
			} catch ( \Exception $exception ) {
				Plugin::$instance->logger->get_logger()->error( $exception->getMessage(), [
					'document_id' => $document->get_id(),
					'document_name' => $document->get_name(),
				] );

				return;
			}
		}
	}

	public static function get_settings_usage() {
		$usage = [];

		$settings_tab = Plugin::$instance->settings->get_tabs();
		$settings = array_merge(
			$settings_tab[ Settings::TAB_GENERAL ]['sections'],
			$settings_tab[ Settings::TAB_ADVANCED ]['sections']
		);

		foreach ( $settings as $setting_data ) {
			foreach ( $setting_data['fields'] as $field_name => $field_data ) {
				$is_hidden_field = ( empty( $field_data['field_args']['type'] ) || 'hidden' === $field_data['field_args']['type'] );

				if ( $is_hidden_field ) {
					continue;
				}

				$setting_value = get_option( 'elementor_' . $field_name );

				if ( empty( $setting_value ) ) {
					continue;
				}

				$is_default_value = ( ! empty( $field_data['field_args']['std'] ) && $setting_value === $field_data['field_args']['std'] );

				if ( $is_default_value ) {
					continue;
				}

				$usage[ $field_name ] = $setting_value;
			}
		}

		$usage = apply_filters( 'elementor/system-info/usage/settings', $usage );

		return $usage;
	}

	/**
	 * Add system info report.
	 */
	public function add_system_info_report() {
		System_Info::add_report( 'usage', [
			'file_name' => __DIR__ . '/usage-reporter.php',
			'class_name' => __NAMESPACE__ . '\Usage_Reporter',
		] );

		System_Info::add_report( 'settings', [
			'file_name' => __DIR__ . '/settings-reporter.php',
			'class_name' => __NAMESPACE__ . '\Settings_Reporter',
		] );
	}

	/**
	 * Usage module constructor.
	 *
	 * Initializing Elementor usage module.
	 *
	 * @access public
	 */
	public function __construct() {
		if ( ! Tracker::is_allow_track() ) {
			return;
		}

		add_action( 'transition_post_status', [ $this, 'on_status_change' ], 10, 3 );
		add_action( 'before_delete_post', [ $this, 'on_before_delete_post' ] );

		add_action( 'elementor/document/before_save', [ $this, 'before_document_save' ], 10, 2 );
		add_action( 'elementor/document/after_save', [ $this, 'after_document_save' ] );

		add_filter( 'elementor/tracker/send_tracking_data_params', [ $this, 'add_tracking_data' ] );

		add_action( 'admin_init', [ $this, 'add_system_info_report' ], 50 );
	}
}
