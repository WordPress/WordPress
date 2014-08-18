<?php
function wppb_register_login_widget() {
	register_widget( 'wppb_login_widget' );
}
add_action( 'widgets_init', 'wppb_register_login_widget' );

class wppb_login_widget extends WP_Widget {

	function wppb_login_widget() {
		$widget_ops = array( 'classname' => 'login', 'description' => __('This login widget lets you add a login form in the sidebar.', 'profilebuilder') );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'wppb-login-widget' );
		
		do_action( 'wppb_login_widget_settings', $widget_ops, $control_ops);
		
		$this->WP_Widget( 'wppb-login-widget', __('Profile Builder Login Widget', 'profilebuilder'), $widget_ops, $control_ops );
		
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('wppb_login_widget_title', $instance['title'] );
		$redirect = trim($instance['redirect']);
		$register = trim($instance['register']);
		$lostpass = trim($instance['lostpass']);

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo do_shortcode('[wppb-login display="false" redirect="'.$redirect.'" submit="widget"]');
		

		if ( $register ){

			/* Check if users can register. */
			$registration = get_option( 'users_can_register' );
			
			if (( current_user_can( 'create_users' )|| $registration) && !is_user_logged_in() ){
				$link = '<a href="'.$register.'" alt="'. __('Register', 'profilebuilder') .'" title="'. __('Register', 'profilebuilder') .'">'. __('Register', 'profilebuilder') .'</a>';
				$registerLink = '<br/>'.__("Don't have an account?", "profilebuilder") . ' '. $link . '<br/>';
				echo $registerLink = apply_filters('wppb_login_widget_register', $registerLink, $link );
			}
		}
		

		if ( $lostpass && !is_user_logged_in() ){
			$link = '<br/><a href="'.$lostpass.'" alt="'. __('Lost Password', 'profilebuilder') .'" title="'. __('Lost Password', 'profilebuilder') .'">'. __('Lost Your Password?', 'profilebuilder') .'</a>';
			echo $link = apply_filters('wppb_login_widget_lost_password', $link, $lostpass );
		}

		do_action( 'wppb_login_widget_display', $args, $instance);	
			
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['redirect'] = strip_tags( $new_instance['redirect'] );
		$instance['register'] = strip_tags( $new_instance['register'] );
		$instance['lostpass'] = strip_tags( $new_instance['lostpass'] );

		do_action( 'wppb_login_widget_update_action', $new_instance, $old_instance);
		
		return $instance;
	
	}


	function form( $instance ) {

		$defaults = array( 'title' => __('Login', 'profilebuilder'), 'redirect' => '', 'register' => '', 'lostpass' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'redirect' ); ?>"><?php _e('After login redirect URL (optional):', 'profilebuilder'); ?></label>
			<input id="<?php echo $this->get_field_id( 'redirect' ); ?>" name="<?php echo $this->get_field_name( 'redirect' ); ?>" value="<?php echo $instance['redirect']; ?>" style="width:100%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'register' ); ?>"><?php _e('Register page URL (optional):', 'profilebuilder'); ?></label>
			<input id="<?php echo $this->get_field_id( 'register' ); ?>" name="<?php echo $this->get_field_name( 'register' ); ?>" value="<?php echo $instance['register']; ?>" style="width:100%;" />
		</p>		
		
		<p>
			<label for="<?php echo $this->get_field_id( 'lostpass' ); ?>"><?php _e('Password Recovery page URL (optional):', 'profilebuilder'); ?></label>
			<input id="<?php echo $this->get_field_id( 'lostpass' ); ?>" name="<?php echo $this->get_field_name( 'lostpass' ); ?>" value="<?php echo $instance['lostpass']; ?>" style="width:100%;" />
		</p>

	<?php
	
		do_action( 'wppb_login_widget_after_display', $instance);
	}
}