<?php wp_reset_vars(array('action', 'standalone', 'option_group_id')); ?>

<?php if (isset($_GET['updated'])) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Settings saved.') ?></strong></p></div>
<?php endif; ?>