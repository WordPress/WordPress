<div class="wrap">
	<h2>Manage Flipbooks
		<a href='<?php echo admin_url( "admin.php?page=real3d_flipbook_admin&action=add_new" ); ?>' class='add-new-h2'>Add New</a>
	</h2>
	
	<table class='flipbooks-table wp-list-table widefat fixed'>
		<thead>
			<tr>
				<th width='5%'>ID</th>
				<th width='50%'>Name</th>
				<th width='30%'>Actions</th>
				<th width='20%'>Shortcode</th>						
			</tr>
		</thead>
		<tbody>
			<?php 
				
				$flipbooks = get_option("flipbooks");

				if (count($flipbooks) == 0) {
					echo '<tr>'.
							 '<td colspan="100%">No Flipbooks found.</td>'.
						 '</tr>';
				} else {
					$flipbook_display_name;
					foreach ($flipbooks as $flipbook) {
						$flipbook_display_name = $flipbook["name"];
						if(!$flipbook_display_name) {
							$flipbook_display_name = 'Flipbook #' . $flipbook["id"] . ' (no name)';
						}
						echo '<tr>'.
								'<td>' . $flipbook["id"] . '</td>'.								
								'<td>' . '<a href="' . admin_url('admin.php?page=real3d_flipbook_admin&action=edit&bookId=' . $flipbook["id"]) . '" title="Edit">'.$flipbook_display_name.'</a>' . '</td>'.
								'<td>' . 
									'<a href="' . admin_url('admin.php?page=real3d_flipbook_admin&action=edit&bookId=' . $flipbook["id"]) . '" title="Edit this item">Edit</a> | '.
									'<a href="' . admin_url('admin.php?page=real3d_flipbook_admin&action=delete&bookId='  . $flipbook["id"]) . '" title="Delete flipbook permanently" >Delete</a> | '.
									'<a href="' . admin_url('admin.php?page=real3d_flipbook_admin&action=duplicate&bookId='  . $flipbook["id"]) . '" title="Duplicate flipbook" >Duplicate</a>'.
									
								'</td>'.
								'<td>[real3dflipbook  id="' . $flipbook["id"] . '"]</td>'.
							'</tr>';
					}
				}
			?>
		</tbody>		 
	</table>

	<p>			
		<a class='button-primary' href='<?php echo admin_url( "admin.php?page=real3d_flipbook_admin&action=add_new" ); ?>'>Create New Flipook</a>       
		<a class='button-primary' href='<?php echo admin_url( "admin.php?page=real3d_flipbook_admin&action=delete_all" ); ?>'>Delete All Flipooks</a>  
	</p>    
	
	<p></p>
</div>