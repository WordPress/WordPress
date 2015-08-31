<?php if (!defined('W3TC')) die(); ?>
<?php if ($results): ?>
    <h4>Page Speed Score: <?php echo $results['score']; ?>/100</h4>

    <ul class="w3tc-widget-ps-rules">
        <?php foreach ($results['rules'] as $index => $rule): if ($index > 5) break; ?>
        <li class="w3tc-widget-ps-rule w3tc-widget-ps-priority-<?php echo $rule['priority']; ?>">
            <div class="w3tc-widget-ps-icon"><div></div></div>
            <p><?php echo $rule['name']; ?></p>
        </li>
        <?php endforeach; ?>
    </ul>

    <p>
        <input class="button w3tc-widget-ps-refresh" type="button" value="Refresh analysis" />
        <input class="button w3tc-widget-ps-view-all {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="View all results" />
    </p>
<?php else: ?>
    <?php if ($key): ?>
    <p>Unable to fetch Page Speed results.</p>
    <p>
        <input class="button w3tc-widget-ps-refresh" type="button" value="Refresh Analysis" />
    </p>
    <?php else: ?>
    <p>Google Page Speed score is not available. Please follow the directions found in the Miscellanous settings box on the <a href="admin.php?page=w3tc_general">General Settings</a> tab.</p>
    <?php endif; ?>
<?php endif; ?>
