<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post types
 *
 * Registers post types and taxonomies
 *
 * @class 		EVO_post_types
 * @version		2.2.9
 * @package		Eventon/Classes/events
 * @category	Class
 * @author 		AJDE
 */

class EVO_post_types{

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );

	}

	/**
	 * Register eventon taxonomies.
	 */
	public static function register_taxonomies() {
		/**
		 * Taxonomies
		 **/
		do_action( 'eventon_register_taxonomy' );
		
		$evcal_opt1= get_option('evcal_options_evcal_1');
		
			
		
		$__capabilities = array(
					'manage_terms' 		=> 'manage_eventon_terms',
					'edit_terms' 		=> 'edit_eventon_terms',
					'delete_terms' 		=> 'delete_eventon_terms',
					'assign_terms' 		=> 'assign_eventon_terms',
				);

		register_taxonomy( 'event_location', 
			apply_filters( 'eventon_taxonomy_objects_event_location', array('ajde_events') ),
			apply_filters( 'eventon_taxonomy_args_event_location', array(
				'hierarchical' => false, 
				'label' => __('Event Location','eventon'), 
				'show_ui' => true,
				'query_var' => true,
				'capabilities'			=> $__capabilities,
				'rewrite' => array( 'slug' => 'event-type' ) 
			)) 
		);



		

		// Event type custom taxonomy NAMES
		$event_type_names = evo_get_ettNames($evcal_opt1);


		// for each activated event type category
		for($x=1; $x<evo_get_ett_count($evcal_opt1)+1; $x++){

			$ab = ($x==1)? '':'_'.$x;
			$ab2 = ($x==1)? '':'-'.$x;
			$evt_name = $event_type_names[$x];

			register_taxonomy( 'event_type'.$ab, 
				apply_filters( 'eventon_taxonomy_objects_event_type'.$ab, array('ajde_events') ),
				apply_filters( 'eventon_taxonomy_args_event_type'.$ab, array(
					'hierarchical' => true, 
					'labels' => array(
		                    'name' 				=> __( "$evt_name Categories", 'eventon' ),
		                    'singular_name' 	=> __( "$evt_name Category", 'eventon' ),
							'menu_name'			=> _x( $evt_name, 'Admin menu name', 'eventon' ),
		                    'search_items' 		=> __( "Search {$evt_name} Categories", 'eventon' ),
		                    'all_items' 		=> __( "All {$evt_name} Categories", 'eventon' ),
		                    'parent_item' 		=> __( "Parent {$evt_name} Category", 'eventon' ),
		                    'parent_item_colon' => __( "Parent {$evt_name} Category:", 'eventon' ),
		                    'edit_item' 		=> __( "Edit {$evt_name} Category", 'eventon' ),
		                    'update_item' 		=> __( "Update {$evt_name} Category", 'eventon' ),
		                    'add_new_item' 		=> __( "Add New {$evt_name} Category", 'eventon' ),
		                    'new_item_name' 	=> __( "New {$evt_name} Category Name", 'eventon' )
		            	),
					'show_ui' => true,
					'query_var' => true,
					'capabilities'			=> $__capabilities,
					'rewrite' => array( 'slug' => 'event-type'.$ab2 ) 
				)) 
			);
		}


		
		
	}

	
	/**
	 * Register core post types
	 */
	public static function register_post_types() {
		if ( post_type_exists('ajde_events') )
			return;

		do_action( 'eventon_register_post_type' );
		
		$labels = eventon_get_proper_labels('Event','Events');
		register_post_type('ajde_events', 
			apply_filters( 'eventon_register_post_type_ajde_events',
				array(
					'labels' => $labels,
					'description' 			=> __( 'This is where you can add new events to your calendar.', 'eventon' ),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'eventon',
					'publicly_queryable' 	=> true,
					'hierarchical' 			=> false,
					'rewrite' 				=> apply_filters('eventon_event_slug', array('slug'=>'events')),
					'query_var'		 		=> true,
					'supports' 				=> array('title','editor','custom-fields','thumbnail'),
					//'supports' 			=> array('title','editor','thumbnail'),
					'menu_position' 		=> 15, 
					'has_archive' 			=> true
				)
			)
		);
	}

}

new EVO_post_types();