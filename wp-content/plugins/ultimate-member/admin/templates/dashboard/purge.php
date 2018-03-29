<?php if (  $this->dir_size('temp') > 0.1 ) { ?>

<p>You can free up <span class="red"><?php echo $this->dir_size('temp'); ?>MB</span> by purging your temp upload directory.</p>

<p><a href="<?php echo add_query_arg( 'um_adm_action', 'purge_temp' ); ?>" class="button">Purge Temp</a></p>

<?php } else { ?>

<p>Your temp uploads directory is <span class="ok">clean</span>. There is nothing to purge.</p>

<?php } ?>