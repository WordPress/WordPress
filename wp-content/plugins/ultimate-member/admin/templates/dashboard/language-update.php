<p><?php printf(__('You are currently using Ultimate Member in your language: <strong>%1$s (%2$s)</strong>.','ultimate-member'), $ultimatemember->available_languages[$locale], $locale); ?></p>

<p><a href="<?php echo add_query_arg( 'um_adm_action', 'um_language_downloader' ); ?>" class="button"><?php _e('Force Update Translation','ultimate-member'); ?></a></p>