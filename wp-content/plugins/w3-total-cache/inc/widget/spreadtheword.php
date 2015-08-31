<p><?php _e("We're working to make WordPress better. Please support us, here's how:", 'w3-total-cache') ?></p>
<ul>
    <li><label>Tweet: </label><input type="button" class="button button-tweet" value="Post to Twitter Now" /></li>
    <li><label><?php _e('Rate:', 'w3-total-cache')?> </label><input type="button" class="button button-rating" value="Vote &amp; Rate Now" /></li>
    <li><label><?php _e('Link:', 'w3-total-cache') ?></label>
        <select id="common_support" name="common.support" class="w3tc-ignore-change">
            <option value=""><?php esc_attr_e('select one', 'w3-total-cache')?></option>
            <?php foreach ($supports as $support_id => $support_name): ?>
            <option value="<?php echo esc_attr($support_id); ?>" <?php selected($support, $support_id); ?>><?php echo esc_attr($support_name); ?></option>
            <?php endforeach; ?>
        </select>
    </li>
</ul>

<p><?php _e('Or manually place a link, here is the code:', 'w3-total-cache') ?></p>
<div class="w3tc-manual-link widefat"><p><?php echo sprintf(__('Performance Optimization %s by W3 EDGE', 'w3-total-cache'), "&lt;a href=&quot;http://www.w3-edge.com/wordpress-plugins/&quot; rel=&quot;external nofollow&quot;&gt;WordPress Plugins&lt;/a&gt;")?></p></div>
