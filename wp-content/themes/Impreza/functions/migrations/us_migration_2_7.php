<?php

class us_migration_2_7 extends US_Migration_Translator {

	// Options
	public function translate_theme_options( &$options ) {
		$favicon_id = us_get_option('favicon');

		if ($favicon_id != ''){
			update_option('site_icon', $favicon_id);
		}
	}
}
