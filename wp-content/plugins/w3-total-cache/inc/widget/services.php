<form action="<?php echo W3TC_PAYPAL_URL; ?>" xmlns="http://www.w3.org/1999/html" method="get">
<ul>
<?php $id =0;
      foreach($this->_request_types as $key => $desc): $id++;?>
    <li><input id="service<?php echo $id?>"name="service" type="radio" class="w3tc-service w3tc-ignore-change {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="<?php esc_attr_e($key)?>" /><label for="service<?php echo $id?>"><?php echo $desc ?></label></li>
<?php endforeach; ?>
</ul>
<div id="buy-w3-service-area"></div>
<p>
    <input id="buy-w3-service" name="buy-w3-service" type="submit" class="button button-primary button-large" value="<?php _e('Buy now', 'w3-total-cache') ?>" disabled="disabled"/>
</p>
</form>