<?php
/*
 * Easy Instagram Widget
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Easy_Instagram_Widget extends WP_Widget {
	private $easy_instagram = null;
	private $defaults = array();

	public function __construct() {
		parent::__construct(
			'easy_instagram_widget_base',
			'Easy Instagram',
			array(
				'description' => __( 'Display one or more images from Instagram based on a tag or Instagram user id', 'Easy_Instagram' ),
				'class' => 'easy-instagram-widget'
			)
		);

		$this->easy_instagram = $GLOBALS['easy_instagram'];
		$this->defaults = $this->easy_instagram->get_defaults();
	}

	//==========================================================================

 	public function form( $instance ) {
		list( $username, $current_user_id ) = $this->easy_instagram->get_instagram_user_data();

		$template = isset( $instance['template'] ) ? $instance['template'] : 'default';

		$title = isset( $instance['title'] ) ? $instance['title'] : '';

		$type = ( isset( $instance['type'] ) ) ? $instance['type'] : 'tag';

		$value = ( isset( $instance['value'] ) ) ? $instance['value'] : '';

		$limit = ( isset( $instance['limit'] ) ) ? $instance['limit'] : 1;

		if ( $limit > $this->defaults['max_images'] ) {
			$limit = $this->defaults['max_images'];
		}

		$caption_hashtags = ( isset( $instance['caption_hashtags'] ) ) ? $instance['caption_hashtags'] : 'true';

		$caption_char_limit = ( isset( $instance['caption_char_limit'] ) ) 
			? $instance['caption_char_limit'] : $this->defaults['caption_char_limit'];

		$author_text = ( isset( $instance['author_text'] ) ) ? $instance['author_text'] : $this->defaults['author_text'];

		$author_full_name = ( isset( $instance['author_full_name'] ) ) ? $instance['author_full_name'] : 'false';

		$thumb_click = ( isset( $instance['thumb_click'] ) ) 
			? $instance['thumb_click'] : $this->defaults['thumb_click'];

		$time_text = ( isset( $instance['time_text'] ) ) ? $instance['time_text'] : $this->defaults['time_text'];

		$time_format = ( isset( $instance['time_format'] ) ) ? $instance['time_format'] : $this->defaults['time_format'];

		if ( isset( $instance['thumb_size'] ) ) {
			
			if ( 'dynamic_thumbnail' == trim( $instance['thumb_size'] ) ) {
				$thumb_size = 'dynamic_thumbnail';
			} else if ( 'dynamic_normal' == trim( $instance['thumb_size'] ) ) {
				$thumb_size = 'dynamic_normal';
			} else if ( 'dynamic_large' == trim( $instance['thumb_size'] ) ) {
				$thumb_size = 'dynamic_large';
			} else {
				list( $w, $h ) = $this->easy_instagram->get_thumb_size_from_params( $instance['thumb_size'] );
				if ( $w < $this->defaults['min_thumb_size'] || $h < $this->defaults['min_thumb_size'] ) {
					$thumb_size = $this->defaults['thumb_size'];
				}
				else {
					$thumb_size = trim( $instance['thumb_size'] );
				}
			}
		}
		else {
			$thumb_size = $this->defaults['thumb_size'];
		}

		$ajax = ( isset( $instance['ajax'] ) ) ? $instance['ajax'] : $this->defaults['ajax'];
		
		$type_options = array(
			'tag' => __( 'Tag', 'Easy_Instagram' ),
			'user_id' => __( 'User ID', 'Easy_Instagram' )
		);
		
		$caption_hashtags_options = 
			$author_fullname_hashtags_options = 
			$use_ajax_options = 
			array(
				'true' => __( 'Yes', 'Easy_Instagram' ),
				'false' => __( 'No', 'Easy_Instagram' )
			);
		
?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'Easy_Instagram' ); ?></label>
		<input type='text' class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php _e( $title ); ?> " />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Type:', 'Easy_Instagram' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
			<?php foreach ( $type_options as $option => $label ):
				printf( '<option value="%s"%s>%s</option>', esc_attr( $option ), selected( $type, $option, false ), esc_html( $label ) );
			endforeach; ?>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'value' ); ?>"><?php _e( 'ID/Hashtag Value:', 'Easy_Instagram' ); ?></label>
		<input type='text' class="widefat" id="<?php echo $this->get_field_id( 'value' ); ?>" name="<?php echo $this->get_field_name( 'value' ); ?>" value="<?php _e( $value ); ?> " />
		<span class='ei-field-info'><?php printf( __( 'Your User ID is: %s', 'Easy_Instagram' ), $current_user_id );?></span>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Images Count:', 'Easy_Instagram' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>">
		<?php for ( $i=1; $i<= $this->defaults['max_images']; $i++ ):
			printf( '<option value="%s"%s>%s</option>', $i, selected( $limit, $i, false ), $i );
		endfor; ?>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'thumb_size' ); ?>"><?php _e( 'Thumbnail Size (in pixels, leave blank for default):', 'Easy_Instagram' ); ?></label>
		<input type='text' class="widefat" id="<?php echo $this->get_field_id( 'thumb_size' ); ?>" name="<?php echo $this->get_field_name( 'thumb_size' ); ?>" value="<?php _e( $thumb_size ); ?> " />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'caption_hashtags' ); ?>"><?php _e( 'Show Caption Hashtags:', 'Easy_Instagram' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'caption_hashtags' ); ?>" name="<?php echo $this->get_field_name( 'caption_hashtags' ); ?>">
			<?php foreach ( $caption_hashtags_options as $option => $label ) :
				printf( '<option value="%s"%s>%s</option>', esc_attr( $option ), selected( $option, $caption_hashtags, false ), esc_html( $label ) );
			endforeach; ?>	
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'caption_char_limit' ); ?>"><?php _e( 'Caption Character Limit (0 for no caption):', 'Easy_Instagram' ); ?></label>
		<input type='text' class="widefat" id="<?php echo $this->get_field_id( 'caption_char_limit' ); ?>" name="<?php echo $this->get_field_name( 'caption_char_limit' ); ?>" value="<?php _e( $caption_char_limit ); ?> " />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'author_text' ); ?>"><?php _e( 'Author Text:', 'Easy_Instagram' ); ?></label>
		<input type='text' class="widefat" id="<?php echo $this->get_field_id( 'author_text' ); ?>" name="<?php echo $this->get_field_name( 'author_text' ); ?>" value="<?php _e( $author_text ); ?> " />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'author_full_name' ); ?>"><?php _e( 'Show Author\'s Full Name:', 'Easy_Instagram' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'author_full_name' ); ?>" name="<?php echo $this->get_field_name( 'author_full_name' ); ?>">
			<?php foreach ( $author_fullname_hashtags_options as $option => $label ) :
				printf( '<option value="%s"%s>%s</option>', esc_attr( $option), selected( $option, $author_full_name, false), esc_html( $label ) );
			endforeach; ?>	
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'thumb_click' ); ?>"><?php _e( 'On Thumbnail Click:', 'Easy_Instagram' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'thumb_click' ); ?>" name="<?php echo $this->get_field_name( 'thumb_click' ); ?>">
		<?php foreach ( $this->easy_instagram->get_thumb_click_options() as $key => $value ):
			printf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( $key, $thumb_click, false ), esc_html( $value ) );
		endforeach; ?>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'time_text' ); ?>"><?php _e( 'Time Text:', 'Easy_Instagram' ); ?></label>
		<input type='text' class="widefat" id="<?php echo $this->get_field_id( 'time_text' ); ?>" name="<?php echo $this->get_field_name( 'time_text' ); ?>" value="<?php _e( $time_text ); ?> " />
		</p>


		<p>
		<label for="<?php echo $this->get_field_id( 'time_format' ); ?>"><?php _e( 'Time Format:', 'Easy_Instagram' ); ?></label>
		<input type='text' class="widefat" id="<?php echo $this->get_field_id( 'time_format' ); ?>" name="<?php echo $this->get_field_name( 'time_format' ); ?>" value="<?php _e( $time_format ); ?> " />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Template:', 'Easy_Instagram' ); ?></label>
		<input type='text' class="widefat" id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" value="<?php _e( $template ); ?> " />
		<span class='ei-field-info'><?php _e( 'See Easy Instagram help for details.', 'Easy_Instagram' ); ?></span>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'ajax' ); ?>"><?php _e( 'Use AJAX:', 'Easy_Instagram' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'ajax' ); ?>" name="<?php echo $this->get_field_name( 'ajax' ); ?>">
			<?php foreach ( $use_ajax_options as $option => $label ) :
				printf( '<option value="%s"%s>%s</option>', esc_attr( $option ), selected( $option, $ajax, false ), esc_html( $label ) );
			endforeach; ?>
		</select>
		</p>
		
<?php

	}

	//==========================================================================

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title']				= strip_tags( $new_instance['title'] );
		$instance['type']				= strip_tags( $new_instance['type'] );
		$instance['value']				= trim( strip_tags( $new_instance['value'] ) );
		$instance['limit']				= strip_tags( $new_instance['limit'] );
		$instance['caption_hashtags'] 	= $new_instance['caption_hashtags'];
		$instance['caption_char_limit'] = (int) $new_instance['caption_char_limit'];
		$instance['author_text']		= strip_tags( $new_instance['author_text'] );
		$instance['author_full_name']	= $new_instance['author_full_name'];
		$instance['thumb_click']		= $new_instance['thumb_click'];
		$instance['time_text']			= strip_tags( $new_instance['time_text'] );
		$instance['time_format']		= strip_tags( $new_instance['time_format'] );
		$instance['thumb_size']			= strip_tags( $new_instance['thumb_size'] );
		$instance['template']			= trim( strip_tags( $new_instance['template'] ) );
		$instance['ajax']				= trim( strip_tags( $new_instance['ajax'] ) );
		return $instance;
	}

	//==========================================================================

	public function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		$tag = '';
		$user_id = '';
		$limit = 1;
		$caption_hashtags = 'true';
		$caption_char_limit = $this->defaults['caption_char_limit'];
		$author_text = $this->defaults['author_text'];
		$author_full_name = 'false';
		$thumb_click = '';
		$time_text = $this->defaults['time_text'];
		$time_format = $this->defaults['time_format'];
		$thumb_size = $this->defaults['thumb_size'];
		$template = $this->defaults['template'];
		$ajax = $this->defaults['ajax'];		

		if ( 'tag' == $instance['type'] ) {
			$tag = trim( $instance['value'] );
			$user_id = '';
		}
		else {
			$tag = '';
			$user_id = $instance['value'];
		}

		if ( isset( $instance['limit'] ) ) {
			$limit = (int) $instance['limit'];
			if ( $limit > $this->defaults['max_images'] ) {
				$limit = $this->defaults['max_images'];
			}
		}

		if ( isset( $instance['caption_hashtags'] ) ) {
			$caption_hashtags = $instance['caption_hashtags'];
		}

		if ( isset( $instance['caption_char_limit'] ) ) {
			$caption_char_limit = (int) $instance['caption_char_limit'];
		}

		if ( isset( $instance['author_text'] ) ) {
			$author_text = $instance['author_text'];
		}

		if ( isset( $instance['author_full_name'] ) ) {
			$author_full_name = $instance['author_full_name'];
		}

		if ( isset( $instance['thumb_click'] ) ) {
			$thumb_click = $instance['thumb_click'];
		}

		if ( isset( $instance['time_text'] ) ) {
			$time_text = $instance['time_text'];
		}

		if ( isset( $instance['time_format'] ) ) {
			$time_format = $instance['time_format'];
		}

		if ( isset( $instance['thumb_size'] ) ) {
			$thumb_size = $instance['thumb_size'];
		}
		
		if ( isset( $instance['template'] ) ) {
			$template = $instance['template'];
		}

		if ( isset( $instance['ajax'] ) ) {
			$ajax = $instance['ajax'];
		}


		$params = array(
			'tag'				 => $tag,
			'user_id'			 => $user_id,
			'limit'				 => $limit,
			'caption_hashtags'	 => $caption_hashtags,
			'caption_char_limit' => $caption_char_limit,
			'author_text'		 => $author_text,
			'author_full_name'	 => $author_full_name,
			'thumb_click'		 => $thumb_click,
			'time_text'			 => $time_text,
			'time_format'		 => $time_format,
			'thumb_size'		 => $thumb_size,
			'template'			 => $template,
			'ajax'               => $ajax
		);

		$content = $this->easy_instagram->generate_content( $params );

		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		echo $content;

		echo $after_widget;
	}

	//==========================================================================
}
