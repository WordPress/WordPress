<?php if (!defined('W3TC')) die(); ?>
<h3><?php _e('Create Pull Zone', 'w3-total-cache')?></h3>
<style type="text/css">.form-table th {width:auto}</style>
<div class="netdna-maxcdn-form">
    <div class="create-error w3tc-error"></div>
    <table class="form-table">
    <tr>
        <th><label for="name"><?php _e('Name:', 'w3-total-cache') ?></label></th>
        <td><input type="text" id="name" name="name"/><div class="name_message w3tc-error inline"></div>
        <p><span class="description"><?php _e('Pull Zone Name. Length: 3-32 chars; only letters, digits, and dash (-) accepted', 'w3-total-cache')?></span></p>
        </td>
    </tr>
    <tr>
        <th><label for="url"><?php _e('Origin <acronym title="Uniform Resource Indicator">URL</acronym>:', 'w3-total-cache') ?></label></th>
        <td><?php echo w3_get_home_url() ?>
        <p><span class="description"><?php _e('Your server\'s hostname or domain', 'w3-total-cache') ?></span></p>
        </td>
    </tr>
    <tr>
        <th><label for="label"><?php _e('Description:', 'w3-total-cache') ?></label></th>
        <td><textarea id="label" name="label" cols="40"></textarea><div class="label_message w3tc-error inline"></div>
        <p><span class="description"><?php _e('Something that describes your zone. Length: 1-255 chars', 'w3-total-cache')?></span></p>
        </td>
    </tr>
    <tr>
        <th></th>
        <td>
            <input type="hidden" name="type" id="type" value="<?php echo $type ?>" />
            <?php wp_nonce_field('w3tc') ?>
            <input id="create_pull_zone" id="create_pull_zone" type="button" value="<?php _e('Create Pull Zone', 'w3-total-cache')?>" class="button-primary" />
            <div id="pull-zone-loading" style="display:inline-block;"></div>
        </td>
    </tr>
</table>
</div>