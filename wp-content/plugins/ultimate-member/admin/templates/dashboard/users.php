<div class="table">

		<table>

			<tr class="first">
				<td class="first b"><a href="<?php echo admin_url('users.php'); ?>"><?php echo $ultimatemember->query->count_users(); ?></a></td>
				<td class="t"><a href="<?php echo admin_url('users.php'); ?>"><?php _e('Users','ultimate-member'); ?></a></td>
			</tr>

			<tr>
				<td class="first b"><a href="<?php echo admin_url('users.php?status=approved'); ?>"><?php echo $ultimatemember->query->count_users_by_status('approved'); ?></a></td>
				<td class="t"><a href="<?php echo admin_url('users.php?status=approved'); ?>"><?php _e('Approved','ultimate-member'); ?></a></td>
			</tr>
			
			<tr>
				<td class="first b"><a href="<?php echo admin_url('users.php?status=rejected'); ?>"><?php echo $ultimatemember->query->count_users_by_status('rejected'); ?></a></td>
				<td class="t"><a href="<?php echo admin_url('users.php?status=rejected'); ?>"><?php _e('Rejected','ultimate-member'); ?></a></td>
			</tr>

		</table>

</div>

<div class="table table_right">

		<table>

			<tr class="first">
				<td class="b"><a href="<?php echo admin_url('users.php?status=awaiting_admin_review'); ?>"><?php echo $ultimatemember->query->count_users_by_status('awaiting_admin_review'); ?></a></td>
				<td class="last t"><a href="<?php echo admin_url('users.php?status=awaiting_admin_review'); ?>" class="warning"><?php _e('Pending Review','ultimate-member'); ?></a></td>
			</tr>

			<tr>
				<td class="b"><a href="<?php echo admin_url('users.php?status=awaiting_email_confirmation'); ?>"><?php echo $ultimatemember->query->count_users_by_status('awaiting_email_confirmation'); ?></a></td>
				<td class="last t"><a href="<?php echo admin_url('users.php?status=awaiting_email_confirmation'); ?>" class="warning"><?php _e('Awaiting E-mail Confirmation','ultimate-member'); ?></a></td>
			</tr>

			<tr>
				<td class="first b"><a href="<?php echo admin_url('users.php?status=inactive'); ?>"><?php echo $ultimatemember->query->count_users_by_status('inactive'); ?></a></td>
				<td class="t"><a href="<?php echo admin_url('users.php?status=inactive'); ?>"><?php _e('Inactive','ultimate-member'); ?></a></td>
			</tr>

		</table>

</div><div class="um-admin-clear"></div>