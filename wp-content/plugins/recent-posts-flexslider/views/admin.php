<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title', 'recent-post-flexslider-locale' ) ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" type="text" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e( 'Filter by Category', 'recent-post-flexslider-locale' ) ?></label> 
    <?php wp_dropdown_categories(array('name' => $this->get_field_name('categories'), 'selected' => $instance['categories'], 'orderby' => 'Name' , 'hierarchical' => 1, 'show_option_all' => 'All Categories', 'hide_empty' => '0')); ?>
    <?php
    $post_types = get_post_types();
    unset($post_types['page'], $post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item']);
    ?>
</p>

<p>
    <label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e( 'Filter by Post Type', 'recent-post-flexslider-locale' ) ?></label> 
    <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" style="width:100%;">
    <?php
            foreach ($post_types as $post_type ) { ?>
                <option value="<?php echo $post_type ?>" <?php selected( $post_type, $instance['post_type'], true ); ?>>
                    <?php echo $post_type ?>
                </option>
            <?php } ?>
    </select>
</p>
        
<p>
    <label for="<?php echo $this->get_field_id('slider_duration'); ?>"><?php _e( 'Slider Duration - Length of time to change slides <em>(In milliseconds)</em>', 'recent-post-flexslider-locale' ) ?></label>
    <input style="width: 40px;" id="<?php echo $this->get_field_id('slider_duration'); ?>" name="<?php echo $this->get_field_name('slider_duration'); ?>" value="<?php echo $instance['slider_duration']; ?>" type="text" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('slider_pause'); ?>"><?php _e( 'Slider Pause - Length of time to pause on a slide <em>(In milliseconds)</em>', 'recent-post-flexslider-locale' ) ?></label>
    <input style="width: 40px;" id="<?php echo $this->get_field_id('slider_pause'); ?>" name="<?php echo $this->get_field_name('slider_pause'); ?>" value="<?php echo $instance['slider_pause']; ?>" type="text" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('slider_count'); ?>"><?php _e( 'Number of slides to display', 'recent-post-flexslider-locale' ) ?></label>
    <input style="width: 40px;" id="<?php echo $this->get_field_id('slider_count'); ?>" name="<?php echo $this->get_field_name('slider_count'); ?>" value="<?php echo $instance['slider_count']; ?>" type="text" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('slider_height'); ?>"><?php _e( 'Slider Height <em>(In pixels)</em>', 'recent-post-flexslider-locale' ) ?></label>
    <input style="width: 40px;" id="<?php echo $this->get_field_id('slider_height'); ?>" name="<?php echo $this->get_field_name('slider_height'); ?>" value="<?php echo $instance['slider_height']; ?>" type="text" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('slider_animate'); ?>"><?php _e( 'Slider Animation Style', 'recent-post-flexslider-locale' ) ?></label> 
    <select id="<?php echo $this->get_field_id('slider_animate'); ?>" name="<?php echo $this->get_field_name('slider_animate'); ?>" style="width:100%;">
        <option value="slide" <?php selected( 'slide', $instance['slider_animate'], true ); ?>>slide</option>
        <option value="fade" <?php selected( 'fade', $instance['slider_animate'], true ); ?>>fade</option>
    </select>
</p>
    
<p>
    <input class="checkbox" type="checkbox" <?php checked($instance['post_title'], 'on'); ?> id="<?php echo $this->get_field_id('post_title'); ?>" name="<?php echo $this->get_field_name('post_title'); ?>" /> 
    <label for="<?php echo $this->get_field_id('post_title'); ?>"><?php _e( 'Show Post Title', 'recent-post-flexslider-locale' ) ?></label>
</p>
	
<p>
    <input class="checkbox" type="checkbox" <?php checked($instance['post_excerpt'], 'on'); ?> id="<?php echo $this->get_field_id('post_excerpt'); ?>" name="<?php echo $this->get_field_name('post_excerpt'); ?>" /> 
    <label for="<?php echo $this->get_field_id('post_excerpt'); ?>"><?php _e( 'Post Excerpt &nbsp;&nbsp;&nbsp; Length<em>(words)</em>:', 'recent-post-flexslider-locale' ) ?></label>
    <input style="width: 40px;" id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" value="<?php echo $instance['excerpt_length']; ?>" type="text" />
</p>
    
<p>
    <input class="checkbox" type="checkbox" <?php checked($instance['post_link'], 'on'); ?> id="<?php echo $this->get_field_id('post_link'); ?>" name="<?php echo $this->get_field_name('post_link'); ?>" /> 
    <label for="<?php echo $this->get_field_id('post_link'); ?>"><?php _e( 'Link Slide to Post', 'recent-post-flexslider-locale' ) ?></label>
</p>