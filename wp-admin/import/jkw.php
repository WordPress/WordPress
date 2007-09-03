<?php

class JeromesKeyword_Import {

	function header()  {
		echo '<div class="wrap">';
		echo '<h2>'.__('Import Jerome&#8217;s Keywords').'</h2>';
		echo '<p>'.__('Steps may take a few minutes depending on the size of your database. Please be patient.').'<br /><br /></p>';
	}

	function footer() {
		echo '</div>';
	}

	function greet() {
		echo '<div class="narrow">';
		echo '<p>'.__('Howdy! This imports tags from an existing Jerome&#8217;s Keywords installation into this blog using the new WordPress native tagging structure.').'</p>';
		echo '<p>'.__('This is suitable for Jerome&#8217;s Keywords version 1.x and 2.0a.').'</p>';
		echo '<p><strong>'.__('All existing Jerome&#8217;s Keywords will be removed after import.').'</strong></p>';
		echo '<p><strong>'.__('Don&#8217;t be stupid - backup your database before proceeding!').'</strong></p>';
		echo '<form action="admin.php?import=jkw&amp;step=1" method="post">';
		wp_nonce_field('import-jkw');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Import Version 1.x &raquo;').'" /></p>';
		echo '</form>';
		echo '<form action="admin.php?import=jkw&amp;step=3" method="post">';
		wp_nonce_field('import-jkw');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Import Version 2.0a &raquo;').'" /></p>';
		echo '</form>';
		echo '</div>';
	}


	function dispatch () {
		if ( empty( $_GET['step'] ) ) {
			$step = 0;
		} else {
			$step = (int) $_GET['step'];
		}

		// load the header
		$this->header();

		switch ( $step ) {
			case 0 :
				$this->greet();
				break;
			case 1 :
				check_admin_referer('import-jkw');
				$this->check_V1_post_keyword( true );
				break;
			case 2 :
				check_admin_referer('import-jkw');
				$this->check_V1_post_keyword( false );
				break;
			case 3 :
				check_admin_referer('import-jkw');
				$this->check_V2_post_keyword( true );
				break;
			case 4 :
				check_admin_referer('import-jkw');
				$this->check_V2_post_keyword( false );
				break;
			case 5:
				check_admin_referer('import-jkw');
				$this->cleanup_V2_import();
				break;
			case 6:
				$this->done();
				break;
		}

		// load the footer
		$this->footer();
	}


	function check_V1_post_keyword ( $precheck = true ) {
		global $wpdb;

		echo '<div class="narrow">';
		echo '<p><h3>'.__('Reading Jerome&#8217;s Keywords Tags&#8230;').'</h3></p>';

		// import Jerome's Keywords tags 
		$qry = "SELECT post_id, meta_id, meta_key, meta_value FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = 'keywords'";
		$metakeys = $wpdb->get_results($qry);
		if ( !is_array($metakeys)) {
			echo '<p>' . __('No Tags Found!') . '</p>';
			return false;
		} else {
			$count = count($metakeys);
			echo '<p>' . sprintf( __('Done! <strong>%s</strong> posts with tags were read.'), $count ) . '<br /></p>';

			echo '<ul>';

			foreach ($metakeys as $post_meta) {
	                if ($post_meta->meta_value != '') {
	                    $post_keys = explode(',', $post_meta->meta_value);
	                    foreach($post_keys as $keyword) {
	                        $keyword = addslashes(trim($keyword));
	                        if ($keyword != ''){
	                            echo '<li>' . $post_meta->post_id . '&nbsp;-&nbsp;' . $keyword . '</li>';
	                            if( !$precheck ){
	                                wp_add_post_tags($post_meta->post_id, $keyword);
	                            }
	                        }
	                    }
	                }
	                if( !$precheck ){
	                    delete_post_meta($post_meta->post_id, 'keywords');
	                }
			}

		    echo '</ul>';

		}

		echo '<form action="admin.php?import=jkw&amp;step='.($precheck? 2:6).'" method="post">';
		wp_nonce_field('import-jkw');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Next &raquo;').'" /></p>';
		echo '</form>';
		echo '</div>';
	}


	function check_V2_post_keyword ( $precheck = true ) {
		global $wpdb;

		echo '<div class="narrow">';
		echo '<p><h3>'.__('Reading Jerome&#8217;s Keywords Tags&#8230;').'</h3></p>';

	        // import Jerome's Keywords tags 
	        $tablename = $wpdb->prefix . substr(get_option('jkeywords_keywords_table'), 1, -1);
	        $qry = "SELECT post_id, tag_name FROM $tablename";
	        $metakeys = $wpdb->get_results($qry);
	        if ( !is_array($metakeys)) {
			echo '<p>' . __('No Tags Found!') . '</p>';
			return false;	        
	        }
	        else {
	            $count = count($metakeys);
		    echo '<p>' . sprintf( __('Done! <strong>%s</strong> tags were read.'), $count ) . '<br /></p>';

		    echo '<ul>';

	            foreach($metakeys as $post_meta) {
	                $keyword = addslashes(trim($post_meta->tag_name));

	                if ($keyword != ''){
	                    echo '<li>' . $post_meta->post_id . '&nbsp;-&nbsp;' . $keyword . '</li>';
	                    if( !$precheck ){
	                        wp_add_post_tags($post_meta->post_id, $keyword);
	                    }
	                }
	            }

		    echo '</ul>';

		}

		echo '<form action="admin.php?import=jkw&amp;step='.($precheck? 4:5).'" method="post">';
		wp_nonce_field('import-jkw');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Next &raquo;').'" /></p>';
		echo '</form>';
		echo '</div>';
	}


	function cleanup_V2_import ( ) {
		global $wpdb;

                /* options from V2.0a (jeromes-keywords.php) */
                $options = array(
                    'version'        => '2.0',          // keywords options version
                    'keywords_table' => 'jkeywords',    // table where keywords/tags are stored
                    'query_varname'  => 'tag',          // HTTP var name used for tag searches
                    'template'       => 'keywords.php', // template file to use for displaying tag queries

                    'meta_always_include' => '',        // meta keywords to always include
                    'meta_includecats' => 'default',    // default' => include cats in meta keywords only for home page
                                                        // all' => includes cats on every page, none' => never included

                    'meta_autoheader'    => '1',        // automatically output meta keywords in header
                    'search_strict'      => '1',        // returns only exact tag matches if true
                    'use_feed_cats'      => '1',        // insert tags into feeds as categories

                    /* post tag options */
                    'post_linkformat'    => '',         // post tag format (initialized to $link_localsearch)
                    'post_tagseparator'  => ', ',       // tag separator character(s)
                    'post_includecats'   => '0',        // include categories in post's tag list
                    'post_notagstext'    => 'none',     // text to display if no tags found

                    /* tag cloud options */
                    'cloud_linkformat'   => '',         // post tag format (initialized to $link_tagcloud)
                    'cloud_tagseparator' => ' ',        // tag separator character(s)
                    'cloud_includecats'  => '0',        // include categories in tag cloud
                    'cloud_sortorder'    => 'natural',  // tag sorting: natural, countup/asc, countdown/desc, alpha
                    'cloud_displaymax'   => '0',        // maximum # of tags to display (all if set to zero)
                    'cloud_displaymin'   => '0',        // minimum tag count to include in tag cloud
                    'cloud_scalemax'     => '0',        // maximum value for count scaling (no scaling if zero)
                    'cloud_scalemin'     => '0'         // minimum value for count scaling
                    );

	        $tablename = $wpdb->prefix . substr(get_option('jkeywords_keywords_table'), 1, -1);

		$wpdb->query('DROP TABLE IF EXISTS ' . $tablename);

                foreach($options as $optname => $optval) {
                    delete_option('jkeywords_' . $optname);
                }

		$this->done();
	}


	function done ( ) {
		echo '<div class="narrow">';
		echo '<p><h3>'.__('Import Complete!').'</h3></p>';		
		echo '</div>';
	}


	function JeromesKeyword_Import ( ) {

		// Nothing.

	}

}


// create the import object
$jkw_import = new JeromesKeyword_Import();

// add it to the import page!
register_importer('jkw', 'Jerome&#8217;s Keywords', __('Import Jerome&#8217;s Keywords into the new native tagging structure.'), array($jkw_import, 'dispatch'));

?>
