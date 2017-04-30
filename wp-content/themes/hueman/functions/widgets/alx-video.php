<?php
/*
	AlxVideo Widget
	
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	
	Copyright: (c) 2013 Alexander "Alx" Agnarson - http://alxmedia.se
	
		@package AlxVideo
		@version 1.0
*/

class AlxVideo extends WP_Widget {

/*  Constructor
/* ------------------------------------ */
	function AlxVideo() {
		parent::__construct( false, 'AlxVideo', array('description' => 'Display a responsive video by adding a link or embed code.', 'classname' => 'widget_alx_video') );;	
	}
	
/*  Widget
/* ------------------------------------ */
	public function widget($args, $instance) {
		extract( $args );
		$instance['title']?NULL:$instance['title']='';
		$title = apply_filters('widget_title',$instance['title']);
		$output = $before_widget."\n";
		if($title)
			$output .= $before_title.$title.$after_title;
		ob_start();	

		
		// The widget
		if ( !empty($instance['video_url']) ) {
			// echo '<div class="video-container">'; - We have a filter adding this to embed shortcode
			global $wp_embed;
			$video = $wp_embed->run_shortcode('[embed]'.$instance['video_url'].'[/embed]');
			// echo '</div>';
		} 
		elseif ( !empty($instance['video_embed_code']) ) {
			echo '<div class="video-container">';
			$video = $instance['video_embed_code'];
			echo '</div>';
		} else {
			$video = '';
		}
		echo $video; 

		
		$output .= ob_get_clean();
		$output .= $after_widget."\n";
		echo $output;
	}
	
/*  Widget update
/* ------------------------------------ */
	public function update($new,$old) {
		$instance = $old;
		$instance['title'] = esc_attr($new['title']);
	// Video
		$instance['video_url'] = esc_url($new['video_url']);
		$instance['video_embed_code'] = $new['video_embed_code'];
		return $instance;
	}

/*  Widget form
/* ------------------------------------ */
	public function form($instance) {
		// Default widget settings
		$defaults = array(
			'title' 			=> '',
		// Video
			'video_url' 		=> '',
			'video_embed_code' 	=> '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
?>

	<style>
	.widget .widget-inside .alx-options-video .postform { width: 100%; }
	.widget .widget-inside .alx-options-video p { margin: 3px 0; }
	.widget .widget-inside .alx-options-video hr { margin: 20px 0 10px; }
	.widget .widget-inside .alx-options-video h4 { margin-bottom: 10px; }
	</style>
	
	<div class="alx-options-video">
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
		</p>
		
		<h4>Responsive Video</h4>
	
		<p>
			<label for="<?php echo $this->get_field_id("video_url"); ?>">Video URL</label>
			<input style="width:100%;" id="<?php echo $this->get_field_id("video_url"); ?>" name="<?php echo $this->get_field_name("video_url"); ?>" type="text" value="<?php echo esc_url($instance["video_url"]); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id("video_embed_code"); ?>">Video Embed Code</label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('video_embed_code'); ?>" name="<?php echo $this->get_field_name('video_embed_code'); ?>"><?php echo $instance["video_embed_code"]; ?></textarea>
		</p>
	</div>
<?php

}

}

/*  Register widget
/* ------------------------------------ */
if ( ! function_exists( 'alx_register_widget_video' ) ) {

	function alx_register_widget_video() { 
		register_widget( 'AlxVideo' );
	}
	
}
add_action( 'widgets_init', 'alx_register_widget_video' );
