<?php if (!defined('W3TC')) die(); ?>
<form id="support_form" class="w3tc-ignore-change" action="admin.php?page=<?php echo $this->_page; ?>" method="post" enctype="multipart/form-data">
    <div class="metabox-holder">
        <?php include W3TC_INC_DIR . '/options/support/form/' . $request_type . '.php'; ?>

        <?php echo $this->postbox_header(__('Note(s):', 'w3-total-cache')); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <ul>
                        <li><?php _e('All submitted data will not be saved and is used solely for the purposes your support request. You will not be added to a mailing list, solicited without your permission, nor will your site be administered after this support case is closed.', 'w3-total-cache'); ?></li>
                        <li><?php _e('Instead of providing your primary administrative or <acronym title="Secure Shell">SSH</acronym> / <acronym title="File Transfer Protocol">FTP</acronym> accounts, create a new administrator account that can be disabled when the support case is closed.', 'w3-total-cache'); ?></li>
                        <li><?php _e('Please add the domain w3-edge.com to your <a href="http://en.wikipedia.org/wiki/Whitelist" target="_blank">email whitelist</a> as soon as possible.', 'w3-total-cache'); ?></li>
                    </ul>
                </th>
            </tr>
        </table>
        <?php echo $this->postbox_footer(); ?>

        <p>
            <input type="hidden" name="request_type" value="<?php echo $request_type; ?>" />
            <input type="hidden" name="request_id" value="<?php echo $request_id; ?>" />
            <input type="hidden" name="payment" value="<?php echo $payment; ?>" />
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_support_request" class="button-primary" value="<?php _e('Submit request', 'w3-total-cache'); ?>" />
            <input id="support_cancel" class="button-secondary {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Cancel', 'w3-total-cache'); ?>" />
        </p>
    </div>
</form>
