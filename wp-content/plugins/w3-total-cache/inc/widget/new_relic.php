<?php if (!defined('W3TC')) die(); ?>
<div id="new-relic-widget">
<?php if (!$new_relic_configured && $new_relic_enabled): ?>
<?php _e('You have not configured API key and Account Id.', 'w3-total-cache')?>
<?php else: ?>
    <?php if ($new_relic_configured && $new_relic_enabled): ?>
    <iframe width="100%" height="100px" scrolling="no" src="<?php echo wp_nonce_url(admin_url('admin.php?page=w3tc_general&w3tc_new_relic_view_new_relic_app&view_application=' . $view_application. '&timestamp=' . time()),'w3tc')?>">
    </iframe>
    <?php endif ?>
    <div id="new-relic-summary">
        <h4><?php _e('Overview', 'w3-total-cache')?></h4>
        <ul>
        <?php
            foreach($new_relic_summary as $name => $value): ?>
                <li><span><?php echo $name ?>: </span><?php echo $value ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
    <div id="new-relic-extra-metrics">
        <h4><?php _e('Average times', 'w3-total-cache')?></h4>
        <ul>
        <?php foreach ($metric_formatted as $name => $value): ?>
            <li><span><?php echo $name ?>: </span><?php echo $value ?></li>
        <?php endforeach; ?>
        </ul>
        <div style="clear:both"></div>
    </div>
    <div id="new-relic-top-list">
        <h4><?php _e('Top 5 slowest times','w3-total-cache')?></h4>
        <div class="wrapper">
        <h5><?php _e('Page load times', 'w3-total-cache')?><div class="handlediv open" title="Click to toggle"><br></div></h5>
        <div id="w3tc-page-load-times" class="top-five">
        <?php if ($slowest_page_loads): ?>
        <table class="slowest">
        <?php foreach($slowest_page_loads as $page => $time): ?>
            <tr><td><span><?php echo $page ?>:</span></td><td><?php echo $time ?></td></tr>
        <?php endforeach ?>
        </table>
        <?php else: ?>
            <?php if ($can_use_metrics): ?>
                <p><?php _e('Not enough data', 'w3-total-cache')?></p>
                <?php else: ?>
                <p><?php _e('Data not available to this subscription level.', 'w3-total-cache')?></p>
                <?php endif ?>
            <?php endif ?>
        </div>
        </div>
        <div class="wrapper">
        <h5><?php _e('Web Transaction times', 'w3-total-cache')?><div class="handlediv open" title="Click to toggle"><br></div></h5>
        <div id="w3tc-web-transaction-times" class="top-five">
        <?php if ($slowest_webtransaction): ?>

                <table class="slowest">
            <?php foreach($slowest_webtransaction as $transaction => $time): ?>
            <tr><td><span><?php echo $transaction ?>:</span></td><td><?php echo $time ?></td></tr>
            <?php endforeach ?>
        </table>
            <?php else: ?>
            <?php if ($can_use_metrics): ?>
                <p><?php _e('Not enough data', 'w3-total-cache')?></p>
                <?php else: ?>
                <p><?php _e('Data not available to this subscription level.', 'w3-total-cache')?></p>
                <?php endif ?>
            <?php endif ?>
        </div>
        </div>
        <div class="wrapper">
        <h5><?php _e('Database times', 'w3-total-cache')?><div class="handlediv open" title="Click to toggle"><br></div></h5>
        <div id="w3tc-database-times" class="top-five">
        <?php if ($slowest_database): ?>
            <table class="slowest">
            <?php foreach($slowest_database as $transaction => $time): ?>
            <tr><td><span><?php echo $transaction ?>:</span></td><td><?php echo $time ?></td></tr>
            <?php endforeach ?>
        </table>
            <?php else: ?>
            <?php if ($can_use_metrics): ?>
            <p><?php _e('Not enough data', 'w3-total-cache')?></p>
            <?php else: ?>
            <p><?php _e('Data not available to this subscription level.', 'w3-total-cache')?></p>
            <?php endif ?>
            <?php endif ?>
        </div>
        </div>
    </div>
    <div style="clear:both"></div>
    <hr>
    <p>
<?php _e('PHP agent:', 'w3-total-cache')?> <span class="w3tc-<?php if ($new_relic_running): ?>enabled"><?php _e('enabled', 'w3-total-cache')?><?php else: ?>disabled"><?php _e('disabled', 'w3-total-cache')?><?php endif; ?></span><br />
<?php _e('Subscription level:', 'w3-total-cache')?> <strong><?php echo $subscription_lvl ?></strong>
	</p>
<?php if (!$can_use_metrics): ?><p><a href="<?php echo esc_attr( NEWRELIC_SIGNUP_URL ); ?>" target="_blank"><?php _e('Upgrade your New Relic account to enable more metrics.', 'w3-total-cache')?></a></p><?php endif; ?>
<?php endif; ?>
</div>