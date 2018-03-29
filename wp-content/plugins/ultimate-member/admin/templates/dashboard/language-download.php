<p><?php printf(__('Ultimate Member is available in your language: <strong>%1$s (%2$s)</strong>.','ultimate-member'), $ultimatemember->available_languages[$locale], $locale); ?></p>

<p><a href="<?php echo add_query_arg( 'um_adm_action', 'um_language_downloader' ); ?>" class="button"><?php _e('Download Translation','ultimate-member'); ?></a></p>