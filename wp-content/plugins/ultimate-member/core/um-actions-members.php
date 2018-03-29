<?php
	/**
	 * Member Directory Search
	 */
	add_action('um_members_directory_search', 'um_members_directory_search');
	function um_members_directory_search( $args ) {
		global $ultimatemember;
		
		$search_filters = array();
		
		if ( isset($args['search_fields']) ) {
			foreach( $args['search_fields'] as $k => $testfilter ){
				if ($testfilter && !in_array( $testfilter, (array)$search_filters ) ) {
					$search_filters[] = $testfilter;
				}
			}
		}
		
		$search_filters = apply_filters('um_frontend_member_search_filters',$search_filters);
			
		if ( $args['search'] == 1 && is_array( $search_filters ) ) { // search on
			
			if ( isset( $args['roles_can_search'] ) && !empty( $args['roles_can_search'] ) && !in_array( um_user('role'), $args['roles_can_search'] ) ){
				return;
			}
			
			$count = count( $search_filters );

			?>
			
			<div class="um-search um-search-<?php echo $count; ?>">
			
				<form method="get" action="" />
				
					<?php if ( isset( $_REQUEST['page_id'] ) && get_option('permalink_structure') == 0 ) { ?>
					
					<input type="hidden" name="page_id" id="page_id" value="<?php echo esc_attr( $_REQUEST['page_id']); ?>" />
					
					<?php }

					$i = 0;
					foreach( $search_filters as $filter ) {
					$i++;
						
						if ( $i % 2 == 0 ) {
							$add_class = 'um-search-filter-2';
						} else {
							$add_class = '';
						}
						
						echo '<div class="um-search-filter '. $add_class .'">'; $ultimatemember->members->show_filter( $filter ); echo '</div>';
					
					}
					
					?>
				
					<div class="um-clear"></div>
					
					<div class="um-search-submit">

						<input type="hidden" name="um_search" id="um_search" value="1" />
						
						<a href="#" class="um-button um-do-search"><?php _e('Search','ultimate-member'); ?></a><a href="<?php echo $ultimatemember->permalinks->get_current_url( true ); ?>" class="um-button um-alt"><?php _e('Reset','ultimate-member'); ?></a>
						
					</div><div class="um-clear"></div>
				
				</form>
			
			</div>
			
			<?php
		
		}
	}
	
	/**
	 * Pre-display Member Directory
	 */
	add_action('um_pre_directory_shortcode', 'um_pre_directory_shortcode');
	function um_pre_directory_shortcode($args) {
		global $ultimatemember;
		extract( $args );

		$ultimatemember->members->results = $ultimatemember->members->get_members( $args );

	}
	
	/**
	 * Member Directory Header
	 */
	add_action('um_members_directory_head', 'um_members_directory_head');
	function um_members_directory_head( $args ) {
		global $ultimatemember;
		extract( $args );
		
		if ( isset($_REQUEST['um_search']) ) {
			$is_filtering = 1;
		} else if ( $ultimatemember->is_filtering == 1 ) {
			$is_filtering = 1;
		} else {
			$is_filtering = 0;
		}
		
		if ( um_members('header') && $is_filtering && um_members('users_per_page') ) { ?>
		
			<div class="um-members-intro">
				
				<div class="um-members-total"><?php echo ( um_members('total_users') > 1 ) ? um_members('header') : um_members('header_single'); ?></div>
					
			</div>
			
		<?php }
		
	}
	
	/**
	 * Member Directory Pagination
	 */
	add_action('um_members_directory_footer', 'um_members_directory_pagination');
	function um_members_directory_pagination( $args ) {
		global $ultimatemember;
		extract( $args );


		if ( isset( $args['search'] ) && $args['search'] == 1 && isset( $args['must_search'] ) && $args['must_search'] == 1 && !isset( $_REQUEST['um_search'] ) )
			return;
		
		if ( um_members('total_pages') > 1 ) { // needs pagination
		
		?>
		
		<div class="um-members-pagidrop uimob340-show uimob500-show">
			
			<?php _e('Jump to page:','ultimate-member'); ?>
			
			<?php if ( um_members('pages_to_show') && is_array( um_members('pages_to_show') ) ) { ?>
			<select onChange="window.location.href=this.value" class="um-s2" style="width: 100px">
				<?php foreach( um_members('pages_to_show') as $i ) { ?>
				<option value="<?php echo $ultimatemember->permalinks->add_query( 'members_page', $i ); ?>" <?php selected($i, um_members('page')); ?>><?php printf(__('%s of %d','ultimate-member'), $i, um_members('total_pages') ); ?></option>
				<?php } ?>
			</select>
			<?php } ?>
		
		</div>
		
		<div class="um-members-pagi uimob340-hide uimob500-hide">
		
			<?php if ( um_members('page') != 1 ) { ?>
			<a href="<?php echo $ultimatemember->permalinks->add_query( 'members_page', 1 ); ?>" class="pagi pagi-arrow um-tip-n" title="<?php _e('First Page','ultimate-member'); ?>"><i class="um-faicon-angle-double-left"></i></a>
			<?php } else { ?>
			<span class="pagi pagi-arrow disabled"><i class="um-faicon-angle-double-left"></i></span>
			<?php } ?>
			
			<?php if ( um_members('page') > 1 ) { ?>
			<a href="<?php echo $ultimatemember->permalinks->add_query( 'members_page', um_members('page') - 1 ); ?>" class="pagi pagi-arrow um-tip-n" title="<?php _e('Previous','ultimate-member'); ?>"><i class="um-faicon-angle-left"></i></a>
			<?php } else { ?>
			<span class="pagi pagi-arrow disabled"><i class="um-faicon-angle-left"></i></span>
			<?php } ?>
			
			<?php if ( um_members('pages_to_show') && is_array( um_members('pages_to_show') ) ) { ?>
			<?php foreach( um_members('pages_to_show') as $i ) { ?>
		
				<?php if ( um_members('page') == $i ) { ?>
				<span class="pagi current"><?php echo $i; ?></span>
				<?php } else { ?>
				
				<a href="<?php echo $ultimatemember->permalinks->add_query( 'members_page', $i ); ?>" class="pagi"><?php echo $i; ?></a>
				
				<?php } ?>
			
			<?php } ?>
			<?php } ?>
			
			<?php if ( um_members('page') != um_members('total_pages') ) { ?>
			<a href="<?php echo $ultimatemember->permalinks->add_query( 'members_page', um_members('page') + 1 ); ?>" class="pagi pagi-arrow um-tip-n" title="<?php _e('Next','ultimate-member'); ?>"><i class="um-faicon-angle-right"></i></a>
			<?php } else { ?>
			<span class="pagi pagi-arrow disabled"><i class="um-faicon-angle-right"></i></span>
			<?php } ?>
			
			<?php if ( um_members('page') != um_members('total_pages') ) { ?>
			<a href="<?php echo $ultimatemember->permalinks->add_query( 'members_page', um_members('total_pages') ); ?>" class="pagi pagi-arrow um-tip-n" title="<?php _e('Last Page','ultimate-member'); ?>"><i class="um-faicon-angle-double-right"></i></a>
			<?php } else { ?>
			<span class="pagi pagi-arrow disabled"><i class="um-faicon-angle-double-right"></i></span>
			<?php } ?>
			
		</div>
			
		<?php
		
		}
		
	}
		
	/**
	 * Member Directory Display
	 */
	add_action('um_members_directory_display', 'um_members_directory_display');
	function um_members_directory_display( $args ) {
		global $ultimatemember;

		extract( $args );
		
		if ( isset( $args['search'] ) && $args['search'] == 1 && isset( $args['must_search'] ) && $args['must_search'] == 1 && !isset( $_REQUEST['um_search'] ) )
			return;
		
		if ( um_members('no_users') ) {
		
		?>
		
		<div class="um-members-none">
			<p><?php echo $args['no_users']; ?></p>
		</div>
			
		<?php

		}
		
		$file = um_path . 'templates/members-grid.php';
		$theme_file = get_stylesheet_directory() . '/ultimate-member/templates/members-grid.php';
		
		if ( file_exists( $theme_file )  ){
			$file = $theme_file;
		}

		include $file;

	}