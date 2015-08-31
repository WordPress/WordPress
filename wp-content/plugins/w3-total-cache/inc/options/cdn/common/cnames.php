<?php if (!defined('W3TC')) die(); ?>
<ol id="cdn_cnames">
<?php
if (! count($cnames)) {
    $cnames = array('');
}

$count = count($cnames);

foreach ($cnames as $index => $cname):
    $label = '';

    if ($count > 1):
    	switch ($index):
            case 0:
                $label = __('(reserved for CSS)', 'w3-total-cache');
                break;

            case 1:
                $label = __('(reserved for JS in <head>)', 'w3-total-cache');
                break;

            case 2:
                $label = __('(reserved for JS after <body>)', 'w3-total-cache');
                break;

            case 3:
                $label = __('(reserved for JS before </body>)', 'w3-total-cache');
                break;

            default:
                $label = '';
                break;
    	endswitch;
    endif;
?>
	<li>
		<input type="text" name="cdn_cnames[]"
                       <?php $this->sealing_disabled('cdn') ?> value="<?php echo esc_attr($cname); ?>" size="60" />
		<input class="button cdn_cname_delete" type="button"
                       <?php $this->sealing_disabled('cdn') ?> value="<?php _e('Delete', 'w3-total-cache'); ?>"<?php if (!$index): ?> style="display: none;"<?php endif; ?> />
		<span><?php echo htmlspecialchars($label); ?></span>
	</li>
<?php endforeach; ?>
</ol>
<input id="cdn_cname_add" class="button" type="button" value="<?php _e('Add CNAME', 'w3-total-cache'); ?>" 
    <?php $this->sealing_disabled('cdn') ?> />
