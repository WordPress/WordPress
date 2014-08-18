<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Easy_Instagram_Utils {
	public function __construct() {	
	}

	public function get_caption_text( $element, $caption_hashtags, $caption_char_limit ) {
		$caption_text = trim( $element['caption_text'] );

		// Remove only hashtags at the end of the caption
		$failsafe_count = 100;
		if ( 'false' == $caption_hashtags ) {
			do {
				$no_hashtags_text = $caption_text;
				$caption_text = preg_replace( '/\s+#[^\\s]+\s?$/', '', $no_hashtags_text );
				$failsafe_count--;
				if ( $failsafe_count < 0 ) {
					break;
				}
			} while ( $caption_text != $no_hashtags_text );

			$caption_text = trim( $caption_text );

			if ( preg_match( '/^#[^\\s]*$/', $caption_text ) ) {
				$caption_text = '';
			}
		}

		// Truncate caption
		if ( ( $caption_char_limit > 0 ) && ( strlen( $caption_text ) > $caption_char_limit ) ) {
			$caption_text = substr( $caption_text, 0, $caption_char_limit );
			$caption_text = substr( $caption_text, 0, strrpos( $caption_text, ' ' ) );
			if ( strlen( $caption_text ) > 0 ) {
				$caption_text .= ' ...';
			}
		}

		return $caption_text;
	}
    
    //================================================================

	public function get_usename_caption( $elem ) {
		$caption_from = '';
		$user_name = '';

		if ( isset( $elem->caption ) && isset( $elem->caption->from ) && isset( $elem->caption->from->username ) ) {
			$user_name = $elem->caption->from->username;
		}

		if ( isset( $elem->caption ) ) {
			$caption_text = isset( $elem->caption->text ) ? trim( $elem->caption->text ) : '';

			if ( isset( $elem->caption->from ) ) {
				if ( isset( $elem->caption->from->username ) ) {
					$user_name = $elem->caption->from->username;
				}

				if ( isset( $elem->caption->from->full_name ) ) {
					$caption_from = $elem->caption->from->full_name;
				}
				else {
					$caption_from = $user_name;
				}
			}

			if ( empty( $user_name ) ) {
				if ( isset( $elem->user ) && isset( $elem->user->username ) ) {
					$user_name = $elem->user->username;
				}
			}

			if ( empty( $caption_from ) ) {
				if ( isset( $elem->user ) ) {
					if ( isset( $elem->user->full_name ) ) {
						$caption_from = $elem->user->full_name;
					}

					if ( empty( $caption_from ) ) {
						$caption_from = $user_name;
					}
				}
			}

			$caption_created_time = $elem->caption->created_time;
		}
		else {
			$caption_text = '';
			if ( isset( $elem->user ) && isset( $elem->user->username ) ) {
				$user_name = $elem->user->username;
			}

			if ( isset( $elem->user ) ) {
				if ( isset( $elem->user->full_name ) ) {
					$caption_from = $elem->user->full_name;
				}

				if ( empty( $caption_from ) ) {
					$caption_from = $user_name;
				}
			}
			$caption_created_time = null;
		}

		return array( $user_name, $caption_from, $caption_text, $caption_created_time );
    }

	//================================================================

	public function relative_time( $timestamp ) {
		$periods = array( 
			__( '1 second ago', 'Easy_Instagram' ),
			__( '1 minute ago', 'Easy_Instagram' ),
			__( '1 hour ago', 'Easy_Instagram' ),
			__( '1 day ago', 'Easy_Instagram' ),
			__( '1 week ago', 'Easy_Instagram' ),
			__( '1 month ago', 'Easy_Instagram' ),
			__( '1 year ago', 'Easy_Instagram' ),
			__( '1 decade ago', 'Easy_Instagram' )
		);
		$periods_plural = array( 
			__( '%s seconds ago', 'Easy_Instagram' ),
			__( '%s minutes ago', 'Easy_Instagram' ),
			__( '%s hours ago', 'Easy_Instagram' ),
			__( '%s days ago', 'Easy_Instagram' ),
			__( '%s weeks ago', 'Easy_Instagram' ),
			__( '%s months ago', 'Easy_Instagram' ),
			__( '%s years ago', 'Easy_Instagram' ),
			__( '%s decades ago', 'Easy_Instagram' )
		);
		
		$difference = time() - $timestamp;		
		$lengths = array( '60', '60', '24', '7', '4.35', '12', '10' );
		
		for( $j = 0; $difference >= $lengths[$j]; $j++ ) {
			$difference /= $lengths[$j];
		}
		$difference = (int) round( $difference );

		$text = sprintf( _n( $periods[ $j ], $periods_plural[ $j ], $difference, 'Easy_Instagram' ), $difference );
		
		return $text;
	}
	
	private function get_hidden_video_element( $element, $video_wrapper_id, $video_id, $css_class = '' ) {
		$default_video_css_class = 'video-js vjs-default-skin vjs-big-play-centered';
		
		if ( empty( $css_class ) ) {
			$video_css_class = $default_video_css_class;
		}
		else {	
			$video_css_class = trim( sprintf( '%s %s', $default_video_css_class, $css_class ) );
		}
		
		$video_css_class = apply_filters( 'easy_instagram_video_class', $video_css_class );
		
		$video_width = $element['video_width'];
		$video_height = $element['video_height'];
		$window_width = $video_width;
		$window_height = $video_height;
		$video_large_url = $element['video_large_url'];
		ob_start(); ?>
		
		<div id="<?php echo esc_attr( $video_wrapper_id ); ?>" class="easy-instagram-hid-video-wrapper">
			<video id="<?php echo esc_attr( $video_id ); ?>" width="<?php echo absint( $video_width );?>" height="<?php echo absint( $video_height );?>" class="<?php echo esc_attr( $video_css_class );?>" controls poster=<?php echo esc_url( $video_large_url );?>>
				<source src="<?php echo esc_url( $element['video_url'] );?>" type="video/mp4" />
				<img src="<?php echo esc_url( $element['thumbnails_url'] );?>" alt="" />
			</video>
		</div>
		<?php
		return ob_get_clean();
	}

	private function get_thumbnail_html_video( $element ) {
		$out = '';
		$has_thumb_action = false;
		$show_play_icon = false;

		$md5 = md5( $element['video_id'] );
		$video_wrapper_id = 'video_wrapper_' . $md5;
		$video_id = 'video_' . $md5;
		
		$thumbnail_width = $element['thumbnail_width'];
		$thumbnail_height = $element['thumbnail_height'];
		$video_width = $element['video_width'];
		$video_height = $element['video_height'];
		
		$unique_rel = $element['unique_rel'];
		
		switch ( $element['thumbnail_click'] ) {
			case 'thickbox':
				$out .= $this->get_hidden_video_element( $element, $video_wrapper_id, $video_id );
			
				$link = sprintf( '<a href="#TB_inline?&width=%d&height=%d&inlineId=%s" class="%s" rel="%s" title="%s">',
				 			$video_width, $video_height, esc_attr( $video_wrapper_id ),
							'thickbox video', $unique_rel, esc_html( $element['thumbnail_link_title'] ) );

				$out .= apply_filters( 'easy_instagram_video_thumb_link', $link, $element['video_url'], 
							$video_width, $video_height );
				
				$has_thumb_action = true;
				$show_play_icon = true;
				break;
			
			case 'colorbox':
				$out .= $this->get_hidden_video_element( $element, $video_wrapper_id, $video_id );
				
				$link = sprintf( '<a href="#" class="colorbox-video" rel="%s" title="%s">', $unique_rel, esc_html( $element['thumbnail_link_title'] ) );

				$out .= apply_filters( 'easy_instagram_video_thumb_link', $link, $element['video_url'], 
							$video_width, $video_height );
			
				$has_thumb_action = true;
				$show_play_icon = true;
				break;
				
			case 'original':
				$link = sprintf( '<a href="%s" target="_blank" class="easy-instagram-original" title="%s">',
							esc_url( $element['thumbnail_link_url'] ), esc_html( $element['thumbnail_link_title'] ) );

				$out .= apply_filters( 'easy_instagram_video_thumb_link', $link, $element['video_url'], 
							$video_width, $video_height );
				
				$has_thumb_action = true;
				$show_play_icon = true;
				break;
				
			default:
				break;
		}

		$thumb = $this->get_thumbnail_image( $element );
		$out .= $thumb;
		
		if ( $show_play_icon ) {
			//$icon_size = $this->get_video_icon_size( $thumbnail_width, $thumbnail_height );
			
			$icon_filename = sprintf( 'assets/images/video-play-128.png' );
			$video_icon_url = apply_filters( 'video_icon', plugins_url( $icon_filename, dirname( __FILE__ ) ) ) ;
			
			//$icon_width = $icon_height = $icon_size;
			//$left = round( 0.5 * ( $thumbnail_width - $icon_width ) );
			//$top = round( 0.5 * ( $thumbnail_height - $icon_height ) );
			
			//$icon_style = sprintf( 'width: %dpx; height: %dpx; top: %dpx; left: %dpx;', 
							//$icon_width, $icon_height, $top, $left );
			
			$out .= sprintf( '<img class="video-icon" src="%s" alt="%s" />', $video_icon_url, '' );
		}
		
		if ( $has_thumb_action ) {
			$out .= '</a>';
		}
		
		return $out;
	}
	
	private function get_thumbnail_html_image( $element ) {
		$out = '';
		$has_thumb_action = false;
		$thumbnail_link_url = $element['thumbnail_link_url'];
		$thumbnail_link_title = $element['thumbnail_link_title'];
		
		$unique_rel = $element['unique_rel'];

		switch ( $element['thumbnail_click'] ) {
			case 'thickbox':
				$link = sprintf( '<a href="%s" class="thickbox" rel="%s" title="%s">', $thumbnail_link_url, $unique_rel, $thumbnail_link_title );
				$out .= apply_filters( 'easy_instagram_thumb_link', $link );
				$has_thumb_action = true;
				break;
			
			case 'colorbox':
				$link = sprintf( '<a href="%s" class="colorbox" rel="%s" title="%s">', $thumbnail_link_url, $unique_rel ,$thumbnail_link_title );
				$out .= apply_filters( 'easy_instagram_thumb_link', $link );
				$has_thumb_action = true;
				break;
				
			case 'original':
				$link = sprintf( '<a href="%s" target="_blank" title="%s">', $thumbnail_link_url, $thumbnail_link_title );
				$out .= apply_filters( 'easy_instagram_thumb_link', $link );
				$has_thumb_action = true;
				break;
				
			default:
				break;
		}
		
		$thumb = $this->get_thumbnail_image( $element );
		$out .= $thumb;
		
		if ( $has_thumb_action ) {
			$out .= '</a>';
		}
		
		return $out;
	}
	
	private function get_thumbnail_image( $element ) {
		if ( isset( $element['dynamic_thumb'] ) ) {
			switch( $element['dynamic_thumb'] ) {
				case 'dynamic_thumbnail':
					$thumb = sprintf( '<img src="%s"  class="easy-instagram-thumbnail" />', 
							esc_url_raw( $element['thumbnail_url'] ) );
				break;
				
				case 'dynamic_normal':
					$thumb = sprintf( '<img src="%s"  class="easy-instagram-thumbnail" />', 
							esc_url_raw( $element['thumbnail_normal_link_url'] ) );
				break;
				
				case 'dynamic_large':
					$thumb = sprintf( '<img src="%s"  class="easy-instagram-thumbnail" />', 
							esc_url_raw( $element['thumbnail_large_link_url'] ) );
				break;
				
				default:
					$thumb = sprintf( '<img src="%s" alt="" style="width: %dpx; height: %dpx;" class="easy-instagram-thumbnail" />',
						esc_url_raw( $element['thumbnail_url'] ), $element['thumbnail_width'], $element['thumbnail_height'] );
				break;
			}
		}
		else {
			$thumb = sprintf( '<img src="%s" alt="" style="width: %dpx; height: %dpx;" class="easy-instagram-thumbnail" />', 
					esc_url_raw( $element['thumbnail_url'] ), $element['thumbnail_width'], $element['thumbnail_height'] );
		}
		return $thumb;	
	}

	private function get_video_icon_size( $thumbnail_width, $thumbnail_height ) {
		$min_dimension = min( array( $thumbnail_width, $thumbnail_height ) );

		if ( $min_dimension < 50 ) {
			$icon_size = 16;
		}
		else if ( $min_dimension < 100 ) {
			$icon_size = 24;
		}
		else if ( $min_dimension < 150 ) {
			$icon_size = 32;
		}
		else if ( $min_dimension < 200 ) {
			$icon_size = 48;
		}
		else if ( $min_dimension < 300 ) {
			$icon_size = 64;
		}
		else {
			$icon_size = 128;
		}
		
		return $icon_size;
	}
	
	
	public function get_thumbnail_html( $element ) {
		if ( 'video' == $element['type'] ) {
			return $this->get_thumbnail_html_video( $element );
		}
		else {
			return $this->get_thumbnail_html_image( $element );
		}
	}
}