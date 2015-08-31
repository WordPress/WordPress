<?php if (!defined('W3TC')) die(); ?>
<?php
/**
 * @var int $zone_id
 * @var array $summary
 * @var array $popular_files
 * @var string $content_zone
 * @var string $account_status
 */
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
?>
<div id="netdna-widget" class="maxcdn-netdna-widget-base">
    <div class="wrapper">
        <div class="status area">
        <p>
            <span><?php echo sprintf(__('Status: %s', 'w3-total-cache'), '<span class="account_status">' . $account_status. '</span>') ?></span>
            <span style="display:inline-block;float:right"><?php echo sprintf(__('Content Zone: %s', 'w3-total-cache'),'<span class="content-zone">' . $content_zone . '</span>') ?></span>
        </p>

    </div>
        <div class="tools area">
            <ul>
                <li><a class="button" href="<?php echo "https://cp.netdna.com/zones/pull/{$zone_id}"?>"><?php _e('Manage', 'w3-total-cache')?></a></li>
                <li><a class="button" href="<?php echo "https://cp.netdna.com/reporting/{$zone_id}"?>"><?php _e('Reports', 'w3-total-cache')?></a></li>
                <li><a class="button" href="<?php echo wp_nonce_url(admin_url('admin.php?page=w3tc_cdn&amp;w3tc_cdn_purge'))?>" onclick="w3tc_popupadmin_bar(this.href); return false"><?php _e('Purge', 'w3-total-cache')?></a></li>
            </ul>
        </div>
        <div class="summary area">
            <h4><?php _e('Reports - 30 days', 'w3-total-cache') ?></h4>
            <ul>
                <li><?php echo sprintf(__('<span>Transferred:</span> %s', 'w3-total-cache'), w3_format_bytes($summary['size'])) ?></li>
                <li><?php echo sprintf(__('<span>Cache Hits:</span> %d (%d%%)', 'w3-total-cache'),
                        $summary['cache_hit'], $summary['hit'] ? ($summary['cache_hit']/$summary['hit'])*100:$summary['hit']) ?></li>
                <li class="large"><?php echo sprintf(__('<span>Cache Misses (non-cache hits):</span> %d (%d%%)', 'w3-total-cache'),
                        $summary['noncache_hit'], $summary['hit']?($summary['noncache_hit']/$summary['hit'])*100:$summary['hit']) ?></li>
            </ul>
        </div>
        <div class="charts area">
            <h4><?php _e('Requests', 'w3-total-cache') ?></h4>
            <div id="chart_div" style="width: 320px; height: 220px;margin-left: auto ;  margin-right: auto ;"></div>
            <h4><?php _e('Content Breakdown', 'w3-total-cache') ?></h4>
            <p>
                <span><?php _e('File', 'w3-total-cache')?></span>
                <span style="display:inline-block;float:right"><?php _e('Hits', 'w3-total-cache') ?></span>
            </p>
            <ul class="file_hits">
                <?php
                if ($popular_files) :
                $compare = $popular_files[0]['hit'];
                foreach($popular_files as $file): ?>
                <li><span style="display:inline-block; background-color: <?php echo NetDNAPresentation::get_file_group_color($file['group'])?>;width: <?php echo $file['hit']/$compare*100*0.9?>%; min-width:60%" title="<?php echo $file['title'] ?>"><?php echo '/', $file['group'], '/',$file['file']?></span> <span style="color:#000"><?php echo $file['hit']?></span></li>
                <?php endforeach;
                endif;
                ?>
            </ul>
        </div>
    </div>
</div>