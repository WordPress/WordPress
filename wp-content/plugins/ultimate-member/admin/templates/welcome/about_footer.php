
	<div class="return-to-dashboard">
		
		<a href="<?php echo admin_url('admin.php?page=ultimatemember'); ?>">Go to Plugin Dashboard &rarr;</a>
			
		<div class="alignright">
				
			<?php global $reduxConfig; foreach ( $reduxConfig->args['share_icons'] as $k => $arr ) { ?><a href="<?php echo $arr['url']; ?>" class="um-about-icon um-admin-tipsy-n" title="<?php echo $arr['title']; ?>" target="_blank"><i class="<?php echo $arr['icon']; ?>"></i></a><?php } ?>

		</div>
			
	</div>
	
</div>