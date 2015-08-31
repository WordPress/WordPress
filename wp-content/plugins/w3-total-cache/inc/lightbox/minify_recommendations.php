<?php if (!defined('W3TC')) die(); ?>
<h3>Minify: Help Wizard</h3>

<p>
    <?php _e('To get started with minify, we\'ve identified the following external CSS and JS objects in the', 'w3-total-cache'); ?>
    <select id="recom_theme">
    <?php foreach ($themes as $_theme_key => $_theme_name): ?>
        <option value="<?php echo htmlspecialchars($_theme_key); ?>"<?php selected($_theme_key, $theme_key); ?>><?php echo htmlspecialchars($_theme_name); ?><?php if ($_theme_key == $theme_key): ?> (active)<?php endif; ?></option>
    <?php endforeach; ?>
    </select>
    <?php _e('theme. Select "add" the files you wish to minify, then click "apply &amp; close" to save the settings.', 'w3-total-cache'); ?>
</p>

<div id="recom_container">
    <h4 style="margin-top: 0;">JavaScript:</h4>
    <?php if (count($js_groups)) :?>
    <ul id="recom_js_files" class="minify-files">
        <?php $index = 0; foreach ($js_groups as $js_group => $js_files): ?>
        	<?php foreach ($js_files as $js_file): $index++; ?>
            <li>
            	<table>
            		<tr>
            			<th class="minify-files-add"><?php _e('Add:', 'w3-total-cache'); ?></th>
            			<th>&nbsp;</th>
            			<th><?php _e('File URI:', 'w3-total-cache'); ?></th>
            			<th><?php _e('Template:', 'w3-total-cache'); ?></th>
            			<th colspan="2"><?php _e('Embed Location:', 'w3-total-cache'); ?></th>
            		</tr>
            		<tr>
            			<td class="minify-files-add">
                			<input type="checkbox" name="recom_js_useit" value="1"<?php checked(isset($checked_js[$js_group][$js_file]), true); ?> />
            			</td>
            			<td><?php echo $index; ?>.</td>
            			<td>
    	                    <input type="text" name="recom_js_file" value="<?php echo esc_attr($js_file); ?>" size="70" />
            			</td>
            			<td>
                            <select name="recom_js_template">
                            <?php foreach ($templates as $template_key => $template_name): ?>
                                <option value="<?php echo esc_attr($template_key); ?>"<?php selected($template_key, $js_group); ?>><?php echo htmlspecialchars($template_name); ?></option>
                            <?php endforeach; ?>
                            </select>
            			</td>
            			<td>
            				<?php $selected = (isset($locations_js[$js_group][$js_file]) ? $locations_js[$js_group][$js_file] : ''); ?>
                            <select name="recom_js_location">
                                <option value="include"<?php selected($selected, 'include'); ?>><?php _e('Embed in &lt;head&gt;', 'w3-total-cache'); ?></option>
                                <option value="include-body"<?php selected($selected, 'include-body'); ?>><?php _e('Embed after &lt;body&gt;', 'w3-total-cache'); ?></option>
                                <option value="include-footer"<?php selected($selected, 'include-footer'); ?>><?php _e('Embed before &lt;/body&gt;', 'w3-total-cache'); ?></option>
                            </select>
            			</td>
            			<td>
			                <input class="js_file_verify button" type="button" value="<?php _e('Verify URI', 'w3-total-cache'); ?>" />
            			</td>
					</tr>
				</table>
            </li>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
    <p>
    	<a href="#" id="recom_js_check"><?php _e('Check / Uncheck All', 'w3-total-cache'); ?></a>
    </p>
    <?php else:?>
    <p><?php _e('No files found.', 'w3-total-cache'); ?></p>
    <?php endif;?>

    <h4><?php _e('Cascading Style Sheets:', 'w3-total-cache'); ?></h4>

    <?php if (count($css_groups)) :?>
    <ul id="recom_css_files" class="minify-files">
        <?php $index = 0; foreach ($css_groups as $css_group => $css_files): ?>
    		<?php foreach ($css_files as $css_file): $index++; ?>
            <li>
            	<table>
            		<tr>
            			<th class="minify-files-add"><?php _e('Add:', 'w3-total-cache'); ?></th>
            			<th>&nbsp;</th>
            			<th><?php _e('File URI:', 'w3-total-cache'); ?></th>
            			<th colspan="2"><?php _e('Template:', 'w3-total-cache'); ?></th>
            		</tr>
            		<tr>
            			<td class="minify-files-add">
                        	<input type="checkbox" name="recom_css_useit" value="1"<?php checked(isset($checked_css[$css_group][$css_file]), true); ?> />
                        </td>
            			<td><?php echo $index; ?>.</td>
                        <td>
                            <input type="text" name="recom_css_file" value="<?php echo esc_attr($css_file); ?>" size="70" />
						</td>
						<td>
                            <select name="recom_css_template">
                            <?php foreach ($templates as $template_key => $template_name): ?>
                            <option value="<?php echo esc_attr($template_key); ?>"<?php selected($template_key, $css_group); ?>><?php echo htmlspecialchars($template_name); ?></option>
                            <?php endforeach; ?>
                            </select>
						</td>
						<td>
			                <input class="css_file_verify button" type="button" value="<?php _e('Verify URI', 'w3-total-cache'); ?>" />
						</td>
					</tr>
				</table>
            </li>
    	    <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
    <p>
    	<a href="#" id="recom_css_check"><?php _e('Check / Uncheck All', 'w3-total-cache'); ?></a>
    </p>
    <?php else:?>
    <p>No files found.</p>
    <?php endif;?>
</div>

<div id="recom_container_bottom">
    <p>
        <input class="recom_apply button-primary" type="button" value="<?php _e('Apply &amp; close', 'w3-total-cache'); ?>" />
    </p>

    <fieldset>
        <legend><?php _e('Notes', 'w3-total-cache'); ?></legend>

        <ul>
            <li><?php _e('Typically minification of advertiser code, analytics/statistics or any other types of tracking code is not recommended.', 'w3-total-cache'); ?></li>
            <li><?php _e('Scripts that were not already detected above may require <a href="admin.php?page=w3tc_support&amp;request_type=plugin_config">professional consultation</a> to implement.', 'w3-total-cache'); ?></li>
        </ul>
    </fieldset>
</div>