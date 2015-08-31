<?php if (!defined('W3TC')) die(); ?>
<?php echo $this->postbox_header('Required Information'); ?>
<table class="form-table">
    <tr>
        <th><?php _e('Request type:', 'w3-total-cache'); ?></th>
        <td><?php echo esc_attr($this->_request_types[$request_type]); ?></td>
    </tr>
    <tr>
        <th><label for="support_url"><?php _e('Blog <acronym title="Uniform Resource Locator">URL</acronym>:', 'w3-total-cache'); ?></label></th>
        <td><input id="support_url" type="text" name="url" value="<?php echo esc_attr($url); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_name"><?php _e('Name:', 'w3-total-cache'); ?></label></th>
        <td><input id="support_name" type="text" name="name" value="<?php echo esc_attr($name); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_email"><?php _e('E-Mail:', 'w3-total-cache'); ?></label></th>
        <td><input id="support_email" type="text" name="email" value="<?php echo esc_attr($email); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_twitter"><?php _e('Twitter ID:', 'w3-total-cache'); ?></label></th>
        <td><input id="support_twitter" type="text" name="twitter" value="<?php echo esc_attr($twitter); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_subject"><?php _e('Subject:', 'w3-total-cache'); ?></label></th>
        <td><input id="support_subject" type="text" name="subject" value="<?php echo esc_attr($subject); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_description"><?php _e('Issue description:', 'w3-total-cache'); ?></label></th>
        <td><textarea id="support_description" name="description" cols="70" rows="8"><?php echo esc_textarea($description); ?></textarea></td>
    </tr>
    <tr>
        <th><label for="support_template"><?php _e('Attach template:', 'w3-total-cache'); ?></label></th>
        <td><select id="support_template" name="templates[]" multiple="multiple" size="10" style="height: auto;">
            <?php foreach ($template_files as $template_file): ?>
            <option value="<?php echo esc_attr($template_file); ?>"<?php if (in_array($template_file, $templates)): ?> selected="selected"<?php endif; ?>><?php echo esc_attr($template_file); ?></option>
            <?php endforeach; ?>
        </select></td>
    </tr>
    <tr>
        <th><label for="support_file"><?php _e('Attach file:', 'w3-total-cache'); ?></label></th>
        <td>
            <input id="support_file" type="file" name="files[]" value="" /><br />
            <a href="#" id="support_more_files"><?php _e('Attach more files', 'w3-total-cache'); ?></a>
        </td>
    </tr>
    <tr>
        <th><label for="support_wp_login"><?php _e('<acronym title="WordPress">WP</acronym> Admin login:', 'w3-total-cache'); ?></label></th>
        <td><input id="support_wp_login" type="text" name="wp_login" value="<?php echo esc_attr($wp_login); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_wp_password"><?php _e('<acronym title="WordPress">WP</acronym> Admin password:', 'w3-total-cache'); ?></label></th>
        <td><input id="support_wp_password" type="text" name="wp_password" value="<?php echo esc_attr($wp_password); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_ftp_host"><?php _e('<acronym title="Secure Shell">SSH</acronym> / <acronym title="File Transfer Protocol">FTP</acronym> host:', 'w3-total-cache'); ?></label></th>
        <td><input id="support_ftp_host" type="text" name="ftp_host" value="<?php echo esc_attr($ftp_host); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_ftp_login"><?php _e('<acronym title="Secure Shell">SSH</acronym> / <acronym title="File Transfer Protocol">FTP</acronym> login:', 'w3-total-cache'); ?></label></th>
        <td><input id="support_ftp_login" type="text" name="ftp_login" value="<?php echo esc_attr($ftp_login); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_ftp_password"><?php _e('<acronym title="Secure Shell">SSH</acronym> / <acronym title="File Transfer Protocol">FTP</acronym> password:', 'w3-total-cache'); ?></label></th>
        <td><input id="support_ftp_password" type="text" name="ftp_password" value="<?php echo esc_attr($ftp_password); ?>" size="80" /></td>
    </tr>
</table>
<?php echo $this->postbox_footer(); ?>

<?php echo $this->postbox_header(__('Additional Information', 'w3-total-cache')); ?>
<table class="form-table">
    <tr>
        <th><label for="support_phone"><?php _e('Phone:', 'w3-total-cache'); ?></label></th>
        <td><input id="support_phone" type="text" name="phone" value="<?php echo esc_attr($phone); ?>" size="80" /></td>
    </tr>
    <tr>
        <th colspan="2">
            <label for="support_subscribe_customer"><?php _e('Would you like to be notified when products are announced and updated?', 'w3-total-cache'); ?></label>
        </th>
    </tr>
    <tr>
        <td colspan="2">
            <input id="support_subscribe_customer" name="subscribe_customer" type="checkbox" value="Yes" <?php checked($subscribe_customer, true) ?> /> <?php _e('Yes, please notify me.', 'w3-total-cache'); ?>
        </td>
    </tr>
</table>
<?php echo $this->postbox_footer(); ?>