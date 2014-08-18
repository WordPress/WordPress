<?php

function slider_show($slider_id) {
    global $wp_slider;
    $slider = $wp_slider->get_slider($slider_id);
    $slider_elements = $wp_slider->get_slider_elements($slider_id, 'active');
?>
    <div id="slider-<?php echo $slider['key']; ?>" class="wp-slider">
        <a href="#" class="wp-slider-control wp-slider-prev" onclick="wp_slider_command('<?php echo $slider['key']; ?>', 'prev'); return false;"></a>
        <ul class="wp-slider-elements" style="<?php if($slider['options']['width']): ?>width:<?php echo $slider['options']['width']; ?>px;<?php endif; ?><?php if($slider['options']['height']): ?>height:<?php echo $slider['options']['height']; ?>px;<?php endif; ?>">
        <?php foreach ($slider_elements as $element): ?>
            <li class="wp-slider-element" id="wp-slide-<?php echo $element['id'];?>" style="cursor:<?php echo ($element['url'] == '') ? 'default' : 'pointer' ?>; background-image:url(<?php echo get_bloginfo('wpurl'); ?>/wp-content/slides/<?php echo stripslashes($element['filename']); ?>); width:<?php echo $slider['options']['width']; ?>px; height:<?php echo $slider['options']['height']; ?>px;">
                <div class="wp-slider-content">
                    <?php if($element['title'] != ''): ?>
                    <h1><?php echo apply_filters('the_title', $element['title']); ?></h1>
                    <?php endif; ?>
                    <?php if($element['description'] != ''): ?>
                    <div><?php echo stripslashes(apply_filters('the_excerpt', $element['description'])); ?></div>
                    <?php endif; ?>
                </div>
                <?php if($element['url'] != ''):?>
                <a class="wp-slider-link" style="width:<?php echo $slider['options']['width']; ?>px; height:<?php echo $slider['options']['height']; ?>px; position:absolute; top:0; left:0;" href="<?php echo $element['url']; ?>" target="<?php echo $element['target']; ?>"></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
        <a href="#" class="wp-slider-control wp-slider-next" onclick="wp_slider_command('<?php echo $slider['key']; ?>', 'next'); return false;"></a>
        <div class="wp-slider-buttons"></div>
        <a href="#" class="wp-slider-control wp-slider-play" onclick="wp_slider_command('<?php echo $slider['key']; ?>', 'play'); return false;"></a>
        <a href="#" class="wp-slider-control wp-slider-pause" onclick="wp_slider_command('<?php echo $slider['key']; ?>', 'pause'); return false;"></a>
    </div>
    <script type="text/javascript">
            wp_slider.<?php echo $slider['key']; ?> = {
                command: function(command){
                    jQuery('div#slider-<?php echo $slider['key']; ?> ul.wp-slider-elements').cycle(command);
                },
                args: {
                    fx: '<?php echo $slider['effect']['effect']; ?>',
                    timeout: <?php echo $slider['effect']['frecuency'] * 1000; ?>,
                    delay:  <?php echo $slider['effect']['delay'] * 1000; ?>,
                    <?php if($slider['effect']['easing']): ?>
                    easing: '<?php echo $slider['effect']['easing']; ?>',
                    <?php endif;?>
                    sync: 1,
                    <?php if($slider['effect']['before'] != ''): ?>
                    before: function(currSlideElement, nextSlideElement, options, forwardFlag){if(window.<?php echo $slider['effect']['before']; ?>){<?php echo $slider['effect']['before']; ?>(currSlideElement, nextSlideElement, options, forwardFlag);}},
                    <?php endif;?>
                    <?php if($slider['effect']['after'] != ''): ?>
                    after: function(currSlideElement, nextSlideElement, options, forwardFlag){if(window.<?php echo $slider['effect']['after']; ?>){<?php echo $slider['effect']['after']; ?>(currSlideElement, nextSlideElement, options, forwardFlag);}},
                    <?php endif;?>
                    pager:  'div#slider-<?php echo $slider['key']; ?> > .wp-slider-buttons',
                    activePagerClass: 'active',
                    pagerAnchorBuilder: function(idx, slide) {
                        return '<a href="#"><span>' + (idx+1) + '</span></a>';
                    }
                }
            }

        jQuery(document).ready(function(){
            jQuery('div#slider-<?php echo $slider['key']; ?> ul.wp-slider-elements').cycle(wp_slider.<?php echo $slider['key']; ?>.args);
        });
    </script>
<?php
}
?>