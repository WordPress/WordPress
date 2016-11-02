<?php

add_action( 'load-post.php', 'us_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'us_post_meta_boxes_setup' );

function us_post_meta_boxes_setup() {

	$config = us_config( 'meta-boxes', array() );

	foreach ( $config as &$meta_box ) {
		new US_Meta_Box( $meta_box );
	}
}

class US_Meta_Box {

	public $meta_box;

	public function __construct( $meta_box ) {
		if ( ! is_admin() )
			return;

		$this->meta_box = $meta_box;

		// Add meta box
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// Save meta box
		foreach ( $this->meta_box['post_types'] as $post_type ) {
			add_action( 'save_post_' . $post_type, array( $this, 'save_meta_boxes' ) );
		}

	}

	public function add_meta_boxes() {
		foreach ( $this->meta_box['post_types'] as $post_type )
		{
			add_meta_box(
				$this->meta_box['id'],
				$this->meta_box['title'],
				array( $this, 'meta_box_body' ),
				$post_type,
				$this->meta_box['context'],
				$this->meta_box['priority']
			);
		}
	}

	public function meta_box_body() {

		echo '<div class="usof-metabox">';
		$post    = get_post();
		$post_id = isset( $post->ID ) ? $post->ID : 0;
		$values = array();

		foreach( $this->meta_box['fields'] as $field_id => $field ) {
			if ( $post_id ) {
				$values[$field_id] = get_post_meta( $post_id, $field_id, TRUE );
				// TODO: move it to config or other more universal way
				if ( $field['type'] == 'link' ) {
					$values[$field_id] = json_decode( $values[$field_id], TRUE );
				}
			}
			if ( isset( $field['options'] ) AND ( ! in_array( $values[$field_id], array_keys( $field['options'] ) ) ) ) {
				$values[$field_id] = $field['std'];
			}
		}

		foreach( $this->meta_box['fields'] as $field_id => $field ) {
			us_load_template( 'vendor/usof/templates/field', array(
				'name' => $field_id,
				'id' => 'usof_' . $field_id,
				'field' => $field,
				'values' => &$values,
			) );
		}

		echo '</div>';
	}

	public function save_meta_boxes( $post_id ) {
		// TODO: prevent duplicate calls and check if we can omit saving meta on AutoSave

		foreach( $this->meta_box['fields'] as $field_id => $field ) {
			$new_value = isset( $_POST[$field_id] ) ? $_POST[$field_id] : NULL;

			// Update post meta if it's not empty
			if ( $new_value !== NULL AND $new_value !== array() ) {
				update_post_meta( $post_id, $field_id, $new_value );
			}
		}
	}
}
