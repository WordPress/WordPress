<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php'; ?>

<?php if ($results): ?>
<h4><?php _e('Page Speed Score:', 'w3-total-cache'); ?> <?php echo $results['score']; ?>/100</h4>

<p>
    <input class="w3tc-widget-ps-nonce" type="hidden" value="<?php echo wp_create_nonce('w3tc'); ?>" />
    <input class="button ps-expand-all" type="button" value="<?php _e('Expand all', 'w3-total-cache'); ?>" />
    <input class="button ps-collapse-all" type="button" value="<?php _e('Collapse all', 'w3-total-cache'); ?>" />
    <input class="button ps-refresh" type="button" value="<?php _e('Refresh analysis', 'w3-total-cache'); ?>" />
</p>

<ul class="ps-rules">
    <?php foreach ($results['rules'] as $index => $rule): ?>
    <li class="ps-rule ps-priority-<?php echo $rule['priority']; ?>">
        <div class="ps-icon"><div></div></div>
        <div class="ps-expand"><?php if (count($rule['blocks'])): ?><a href="#">+</a><?php endif; ?></div>
        <p><?php echo $rule['name']; ?></p>

        <?php if (count($rule['blocks']) || count($rule['resolution'])): ?>
        <div class="ps-expander">
            <?php if (count($rule['blocks'])): ?>
            <ul class="ps-blocks">
                <?php foreach ($rule['blocks'] as $block): ?>
                <li class="ps-block">
                    <p><?php echo $block['header']; ?></p>

                    <?php if (count($block['urls'])): ?>
                    <ul class="ps-urls">
                        <?php foreach ($block['urls'] as $url): ?>
                        <li class="ps-url"><?php echo $url['result']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <?php if (count($rule['resolution'])): ?>
				<p><strong><?php _e('Resolution:', 'w3-total-cache'); ?></strong> <?php echo $rule['resolution']['header']; ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<p><?php _e('Unable to fetch Page Speed results.', 'w3-total-cache'); ?></p>
<p>
    <input class="button ps-refresh" type="button" value="<?php _e('Refresh Analysis', 'w3-total-cache'); ?>" />
</p>
<?php endif; ?>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>
