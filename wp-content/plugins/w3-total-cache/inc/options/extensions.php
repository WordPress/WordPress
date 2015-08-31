<?php if (!defined('W3TC')) die();?>
<?php
/**
 * @var string $sub_view
 */
?>
<?php include W3TC_INC_OPTIONS_DIR . '/common/header.php'; ?>
<div id="w3tc_extensions">
    <?php
    if ($sub_view == 'list')
        printf('<p>'.__('Extension support is always %s','w3-total-cache'), '<span class="w3tc-enabled">' . __('enabled', 'w3-total-cache') . '</span>.' .'</p>')
    ?>
    <form action="admin.php?page=<?php echo $this->_page; ?><?php echo $extension ? "&extension={$extension}&action=view" : ''?>" method="post">
        <div class="metabox-holder <?php echo $extension?'extension-settings':''?>">
            <?php include W3TC_INC_OPTIONS_DIR . "/extensions/$sub_view.php"?>
        </div>
    </form>
</div>
<?php include W3TC_INC_OPTIONS_DIR . '/common/footer.php'; ?>
