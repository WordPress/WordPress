<?php
class Advman_Widget extends WP_Widget
{
    function Advman_Widget()
    {
        $widget_ops = array('classname' => 'Advman_Widget', 'description' => __('Place an Advertising Manager ad', 'advman'));
        parent::WP_Widget('advman', __('Advertisement', 'advman'), $widget_ops);
    }
    function widget($args, $instance)
    {
        // $args is an array of strings that help widgets to conform to
        // the active theme: before_widget, before_title, after_widget,
        // and after_title are the array keys. Default tags: li and h2.
        extract($args); //nb. $name comes out of this, hence the use of $n
        
        global $advman_engine;
        
        $n = $instance['name'] == '' ? $advman_engine->getSetting('default-ad') : $instance['name'];
        $ad = $advman_engine->selectAd($n);
        
        if (!empty($ad)) {
            $suppress = !empty($instance['suppress']);
            $title = apply_filters('widget_title', $instance['title']);
            
            $ad_code = $ad->display();
            echo $suppress ? ($title . $ad_code) : ($before_widget . $before_title . $title . $after_title . $ad_code . $after_widget);
            $advman_engine->incrementStats($ad);
        }
    }
    
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['suppress'] = !empty($new_instance['suppress']);
        $instance['name'] = $new_instance['name'];
        return $instance;
    }
    
    function form($instance)
    {
        global $advman_engine;
        
        $ads = $advman_engine->getAds();
        $names = array();
        foreach ($ads as $ad) {
            $names[$ad->name] = $ad->name;
        }
        
        $title = esc_attr($instance['title']);
	$checked = !empty($instance['suppress']) ? 'checked="checked"' : '';
?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'advman'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
            <p>
                <label for="<?php echo $this->get_field_id('name'); ?>"><?php _e('Select an ad to display:', 'advman'); ?>
                <select class="widefat" id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>">
                    <option value=""<?php echo $instance['name'] == "" ? " selected='selected'" : ''; ?>><?php _e('Default Ad', 'advman'); ?></option>
<?php foreach ($names as $name) : ?>
                    <option value="<?php echo $name; ?>"<?php echo $name == $instance['name'] ? " selected='selected'" : ''; ?>>#<?php echo $name; ?></option>
<?php endforeach; ?>
                </select>
                </label>
            </p>
            <p><label for="<?php echo $this->get_field_id('suppress'); ?>"><?php _e('Hide widget formatting', 'advman'); ?> <input class="checkbox" id="<?php echo $this->get_field_id('suppress'); ?>" name="<?php echo $this->get_field_name('suppress'); ?>" type="checkbox" <?php echo $checked; ?> /></label></p>
        <?php
    }
}
?>