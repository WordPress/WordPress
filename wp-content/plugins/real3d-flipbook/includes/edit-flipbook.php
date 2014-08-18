<div class='wrap'>
	<div id='real3dflipbook-admin'>
		<a href="admin.php?page=real3d_flipbook_admin" class="back-to-list-link">&larr; <?php _e('Back to flipbooks list', 'flipbook'); ?></a>
		<h2 id="edit-flipbook-text">Edit flipbook
		<?php
			if (isset($_GET['bookId']) && $_GET['bookId'] > -1) {
				echo ' ' . $_GET['bookId'];
			}
		?>
		</h2>
		
		<form method="post" enctype="multipart/form-data" action="admin.php?page=real3d_flipbook_admin&action=save_settings&bookId=<?php _e($current_id)?>">
			<p class="submit"><input type="submit" name="submit" id="submit" class="button save-button button-primary" value="Save Changes"></p>
			<div class="metabox-holder">
				<div class="meta-box-sortables">
				
					<div class="column-left">
					
						<div class="postbox">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle">PDF Options</h3>
							<div class="inside">
								<table class="form-table" id="flipbook-pdf-options">
									<tbody/>
								</table>
							</div>
						</div>
						
						<div class="postbox">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle">General Options</h3>
							<div class="inside">
								<table class="form-table" id="flipbook-general-options">
									<tbody/>
								</table>
							</div>
						</div>
						
						<div class="postbox">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle">Lightbox Mode Options</h3>
							<div class="inside">
								<table class="form-table" id="flipbook-lightbox-options">
									<tbody/>
								</table>
							</div>
						</div>
						
						<div class="postbox">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle">Normal & Fullscreen Mode Options</h3>
							<div class="inside">
								<table class="form-table" id="flipbook-normal-options">
									<tbody/>
								</table>
							</div>
						</div>
						
						<div class="postbox">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle">Menu Options</h3>
							<div class="inside">
								<table class="form-table" id="flipbook-menu-options">
									<tbody/>
								</table>
							</div>
						</div>
						
						<div class="postbox">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle">WebGl Options</h3>
							<div class="inside">
								<table class="form-table" id="flipbook-webgl-options">
									<tbody/>
								</table>
							</div>
						</div>
						
						
						
						<div class="postbox">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle"><span>Social Share Options</span></h3>
							<div class="inside">
								<div>
									<div class="ui-sortable">
										<div id="share-container" class="ui-sortable"></div>
										<div><a id="add-share-button" class="alignleft button-primary " href='#'>Add Share Button</a></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="column-right">
					
						<div class="postbox">
							<div class="handlediv" title="Click to toggle"></div>
							<h3 class="hndle"><span>Pages</span></h3>
							<div class="inside">
								<div>
									<div class="ui-sortable sortable-pages-body">
										<div id="pages-container" class="ui-sortable sortable-pages-container"></div>
										<div><a id="delete-all-pages-button" class="alignleft button-secondary " href='#'>Delete All Pages</a></div>
										<!--<div><a id="add-new-page-button" class="alignleft button-primary " href='#'>Add New Page</a></div>-->
										<div><a id="add-pages-button" class="alignleft button-primary " href='#'>Add Pages From Gallery</a></div>
									</div>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button save-button button-primary" value="Save Changes"></p>
		</form>
	</div>
</div>
<?php 
wp_enqueue_media();
wp_enqueue_script("read3d_flipbook_admin", plugins_url()."/real3d-flipbook/js/plugin_admin.js", array('jquery','jquery-ui-sortable','jquery-ui-resizable','jquery-ui-selectable','jquery-ui-tabs' ),REAL3D_FLIPBOOK_VERSION);
wp_enqueue_style( 'read3d_flipbook_admin_css', plugins_url()."/real3d-flipbook/css/flipbook-admin.css",array(), REAL3D_FLIPBOOK_VERSION );
wp_enqueue_style( 'jquery-ui-style', plugins_url()."/real3d-flipbook/css/jquery-ui.css",array(), REAL3D_FLIPBOOK_VERSION );
//pass $flipbooks to javascript
wp_localize_script( 'read3d_flipbook_admin', 'options', json_encode($flipbooks[$current_id]) );