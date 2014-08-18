<?php
// function to display the GUI in the back-end
function wppb_userListing(){
?>
	<style>
	  slider {}
	</style>
	<style>
	  slider2 {}
	</style>

<?php
	//first thing we will have to do is create a default settings on first-time run of the addon
	$customUserListingSettings = get_option('customUserListingSettings','not_found');
	$wppbFetchArray = get_option('wppb_custom_fields');
	if ($customUserListingSettings == 'not_found'){
		$customUserListingSettingsArg = array(
										 'sortingCriteria'=> 'login',
										 'sortingOrder'=> 'asc',
										 'sortingNumber'=> '25',
										 'avatarSize' => 16,
										 /*'avatarSizeSingle' => 16,*/
										 'allUserlisting' => '',
										 'singleUserlisting' => '');
		add_option('customUserListingSettings', $customUserListingSettingsArg);
	}
?>
	<form method="post" action="options.php#wppb_userListing" name="userlistingForm">
		<?php $customUserListingSettings = get_option('customUserListingSettings'); ?>
		<?php settings_fields('customUserListingSettings'); ?>
		
		<h2><?php _e('User-Listing', 'profilebuilder');?></h2>
		<h3><?php _e('User-Listing', 'profilebuilder');?></h3>
		<p>
		<?php _e('To create a page containing the users registered to this current site/blog, insert the following shortcode in a (blank) page: ', 'profilebuilder');?><strong>[wppb-list-users]</strong>.<br/>
		<?php _e('For instance, to create a userlisting shortcode listing only the editors and authors, visible to only the users currently logged in, you would use:', 'profilebuilder');?> <strong>[wppb-list-users visibility="restricted" roles="editor,author"]</strong>.<br/>
		<?php _e('You can also create a userlisting page that displays users having a certain meta-value within a certain (extra) meta-field like so:', 'profilebuilder');?> <strong>[wppb-list-users meta_key="skill" meta_value="Photography"]</strong>. <?php _e('Remember though, that the field-value combination must exist in the database.', 'profilebuilder');?>
		</p><br/>
		
		<strong><?php _e('General Settings','profilebuilder');?></strong>
		<p>
		<?php _e('These settings are applied to the front-end userlisting.','profilebuilder');?>
		</p>
		
		
		<table class="sortingTable">
			<tr class="sortingTableRow">
				<td class="sortingTableCell1"><span style="padding-left:20px"> &rarr; <?php _e('Number of Users/Page: ', 'profilebuilder');?></span></td>
				<td class="sortingTableCell2">
					<select id="sortingNumberSelect" name="customUserListingSettings[sortingNumber]">
						<option <?php if ($customUserListingSettings['sortingNumber'] == '5'){ echo 'selected="yes" ';} ?> value="5">5</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '10'){ echo 'selected="yes" ';} ?> value="10">10</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '25'){ echo 'selected="yes" ';} ?> value="25">25</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '50'){ echo 'selected ="yes" ';} ?> value="50">50</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '100'){ echo 'selected ="yes" ';} ?> value="100">100</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '150'){ echo 'selected ="yes" ';} ?> value="150">150</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '200'){ echo 'selected ="yes" ';} ?> value="200">200</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '250'){ echo 'selected ="yes" ';} ?> value="250">250</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '500'){ echo 'selected ="yes" ';} ?> value="500">500</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '1000'){ echo 'selected ="yes" ';} ?> value="1000">1000</option>
					</select>
				</td>
			</tr>
			<tr class="sortingTableRow">
				<td class="sortingTableCell1"><span style="padding-left:20px"> &rarr; <?php _e('Default Sorting Criteria: ', 'profilebuilder');?></span></td>
				<td class="sortingTableCell2">
					<select id="sortingCriteriaSelect" name="customUserListingSettings[sortingCriteria]">
						<option></option>
						<optgroup label="Default WordPress Fields">
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'login'){ echo 'selected="yes" ';} ?> value="login"><?php _e('Username', 'profilebuilder');?></option>
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'email'){ echo 'selected="yes" ';} ?> value="email"><?php _e('Email', 'profilebuilder');?></option>
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'url'){ echo 'selected="yes" ';} ?> value="url"><?php _e('Website', 'profilebuilder');?></option>
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'bio'){ echo 'selected="yes" ';} ?> value="bio"><?php _e('Biographical Info', 'profilebuilder');?></option>
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'registered'){ echo 'selected="yes" ';} ?> value="registered"><?php _e('Registration Date', 'profilebuilder');?></option>
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'firstname'){ echo 'selected="yes" ';} ?> value="firstname"><?php _e('Firstname', 'profilebuilder');?></option>
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'lastname'){ echo 'selected="yes" ';} ?> value="lastname"><?php _e('Lastname', 'profilebuilder');?></option>
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'nicename'){ echo 'selected="yes" ';} ?> value="nicename"><?php _e('Display Name', 'profilebuilder');?></option>
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'post_count'){ echo 'selected="yes" ';} ?> value="post_count"><?php _e('Number of Posts', 'profilebuilder');?></option>
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'aim'){ echo 'selected="yes" ';} ?> value="aim"><?php _e('Aim', 'profilebuilder');?></option>
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'yim'){ echo 'selected="yes" ';} ?> value="yim"><?php _e('Yim', 'profilebuilder');?></option>
							<option <?php if ($customUserListingSettings['sortingCriteria'] == 'jabber'){ echo 'selected="yes" ';} ?> value="jabber"><?php _e('Jabber', 'profilebuilder');?></option>
						</optgroup>
						<optgroup label="<?php _e('Custom Fields', 'profilebuilder');?>">
							<?php
							foreach($wppbFetchArray as $key => $value){
								if ($value['item_type'] !== 'avatar'){
									echo '<option ';
									if ($customUserListingSettings['sortingCriteria'] == $value['item_metaName'])
										echo 'selected="yes" ';
									echo 'value="'.$value['item_metaName'].'">'.$value['item_metaName'].'</option>';
								}
							}
							?>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr class="sortingTableRow">
				<td class="sortingTableCell1"><span style="padding-left:20px"> &rarr; <?php _e('Default Sorting Order: ', 'profilebuilder');?></span></td>
				<td class="sortingTableCell2">
					<select id="sortingOrderSelect" name="customUserListingSettings[sortingOrder]">
						<option <?php if ($customUserListingSettings['sortingOrder'] == 'asc'){ echo 'selected="yes" ';} ?> value="asc"><?php _e('Ascending', 'profilebuilder');?></option>
						<option <?php if ($customUserListingSettings['sortingOrder'] == 'desc'){ echo 'selected ="yes" ';} ?> value="desc"><?php _e('Descending', 'profilebuilder');?></option>
					</select>
				</td>
			</tr>
		</table>
		<br/>
		
		<strong><?php _e('"All-Userlisting" Template','profilebuilder');?></strong>
		<p>
		<?php _e('With the userlisting templates you can customize the look, feel and information listed by the shortcode.','profilebuilder');?><br/>
		<?php _e('The "All Users Listing" template is used to list all users. It\'s displayed on each page access where the shortcode is present.','profilebuilder');?>
		</p>
		<table class="fieldTable">
			<tr class="sortingTableRow">
				<td class="sortingTableCell1"><span style="padding-left:20px"> &rarr; <?php _e('Avatar/Gravatar Size: ', 'profilebuilder');?></span></td>
				<td class="sortingTableCell2">
					<select id="sortingNumberSelect" name="customUserListingSettings[avatarSize]">
						<?php
							for($i=20; $i<=200; $i++){
								echo '<option ';
								if ($customUserListingSettings['avatarSize'] == $i)
									echo 'selected="yes" ';
							echo ' value="'.$i.'">'.$i.'</option>';
							}
						?>
					</select>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell1">
					<span style="padding-left:20px"> &rarr; <?php _e('Insert "Sort By" Field:', 'profilebuilder'); ?></span>
				</td>
				<td class="fieldTableCell2">
					<select id="insertSortField" onchange="wppb_insertAtCursor(allUserlisting,this.value)">
						<option></option>
						<optgroup label="<?php _e('Default WordPress Fields', 'profilebuilder');?>">
							<option value="%%sort_user_name%%">%%sort_user_name%%</option>
							<option value="%%sort_email%%">%%sort_email%%</option>
							<option value="%%sort_website%%">%%sort_website%%</option>
							<option value="%%sort_biographical_info%%">%%sort_biographical_info%%</option>
							<option value="%%sort_registration_date%%">%%sort_registration_date%%</option>
							<option value="%%sort_first_name%%">%%sort_first_name%%</option>
							<option value="%%sort_last_name%%">%%sort_last_name%%</option>
							<option value="%%sort_display_name%%">%%sort_display_name%%</option>
							<option value="%%sort_number_of_posts%%">%%sort_number_of_posts%%</option>
							<option value="%%sort_aim%%">%%sort_aim%%</option>
							<option value="%%sort_yim%%">%%sort_yim%%</option>
							<option value="%%sort_jabber%%">%%sort_jabber%%</option>
						</optgroup>
						<optgroup label="<?php _e('Custom Fields', 'profilebuilder');?>">
							<?php
							foreach($wppbFetchArray as $key => $value)
								if ($value['item_type'] !== 'avatar')
									echo '<option value="%%sort_'.$value['item_metaName'].'%%">%%sort_'.$value['item_metaName'].'%%</option>';
							?>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell1">
					<span style="padding-left:20px"> &rarr; <?php _e('Insert "User-Meta" Field:', 'profilebuilder'); ?></span>
				</td>
				<td class="fieldTableCell2">
					<select id="insertUserMetaField" onchange="wppb_insertAtCursor(allUserlisting,this.value)">
						<option></option>
						<optgroup label="Default WordPress Fields">
							<option value="%%meta_user_name%%">%%meta_user_name%%</option>
							<option value="%%meta_email%%">%%meta_email%%</option>
							<option value="%%meta_first_last_name%%">%%meta_first_last_name%%</option>
							<option value="%%meta_role%%">%%meta_role%%</option>
							<option value="%%meta_email%%">%%meta_email%%</option>
							<option value="%%meta_registration_date%%">%%meta_registration_date%%</option>
							<option value="%%meta_first_name%%">%%meta_first_name%%</option>
							<option value="%%meta_last_name%%">%%meta_last_name%%</option>
							<option value="%%meta_nickname%%">%%meta_nickname%%</option>
							<option value="%%meta_display_name%%">%%meta_display_name%%</option>
							<option value="%%meta_website%%">%%meta_website%%</option>
							<option value="%%meta_biographical_info%%">%%meta_biographical_info%%</option>
							<option value="%%meta_number_of_posts%%">%%meta_number_of_posts%%</option>
							<option value="%%meta_aim%%">%%meta_aim%%</option>
							<option value="%%meta_yim%%">%%meta_yim%%</option>
							<option value="%%meta_jabber%%">%%meta_jabber%%</option>
						</optgroup>
						<optgroup label="Custom Fields">
							<?php
							foreach($wppbFetchArray as $key => $value){
								//if ($value['item_type'] !== 'avatar')
									echo '<option value="%%meta_'.$value['item_metaName'].'%%">%%meta_'.$value['item_metaName'].'%%</option>';
								if ($value['item_type'] == 'upload')
									echo '<option value="%%meta_'.$value['item_metaName'].'_URL%%">%%meta_'.$value['item_metaName'].'_URL%%</option>';
							}
							?>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell1">
					<span style="padding-left:20px"> &rarr; <?php _e('Insert Extra Functions:', 'profilebuilder'); ?></span>
				</td>
				<td class="fieldTableCell2">
					<select id="insertExtraFunction" onchange="wppb_insertAtCursor(allUserlisting,this.value)">
						<option></option>
						<option value="%%extra_more_info_link%%">%%extra_more_info_link%%</option>
						<option value="%%extra_while_users%%">%%extra_while_users%%</option>
						<option value="%%extra_end_while_users%%">%%extra_end_while_users%%</option>
						<option value="%%extra_search_all_fields%%">%%extra_search_all_fields%%</option>
						<option value="%%extra_avatar_or_gravatar%%">%%extra_avatar_or_gravatar%%</option>
					</select>
				</td>
			</tr>
			<tr class="fieldTableExtraRow">
				<td class="fieldTableCell4" colspan="2">
					<?php echo '<button type="button" name="allUserlistingButton" id="allUserlistingButton" class="button">'. __('Show/Hide Default "All-Userlisting" Code','profilebuilder').'</button>';?>
					<slider><br/><br/>
						<?php echo '<b>'.__('If you wish to use a default userlisting, just copy the following code and paste it in the textarea below:', 'profilebuilder').'</b>';?><br/><br/>
						&lt;table id="userListingTable" cellspacing="0"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;thead&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading1" scope="col" colspan="2"&gt;&lt;span&gt;%%sort_user_name%%&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading2" scope="col"&gt;&lt;span&gt;%%sort_first_last_name%%&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading3" scope="col"&gt;&lt;span&gt;<?php _e('Role', 'profilebuilder');?>&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading4" scope="col"&gt;&lt;span&gt;%%sort_number_of_posts%%&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading5" scope="col"&gt;&lt;span&gt;%%sort_registration_date%%&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading6" scope="col"&gt;&lt;span&gt;<?php _e('More', 'profilebuilder');?>&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/thead&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tbody&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%%extra_while_users%%<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="tableRow" onmouseover="style.backgroundColor='grey'; style.color='white';" onmouseout="style.backgroundColor=''; style.color='';"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="avatarColumn"&gt;%%extra_avatar_or_gravatar%%&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="loginNameColumn"&gt;&lt;span&gt;%%meta_user_name%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="nameColumn"&gt;&lt;span&gt;%%meta_first_last_name%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="roleColumn"&gt;&lt;span&gt;%%meta_role%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="postsColumn"&gt;&lt;span&gt;%%meta_number_of_posts%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="signUpColumn"&gt;&lt;span&gt;%%meta_registration_date%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="moreInfoColumn"&gt;&lt;span&gt;%%extra_more_info_link%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%%extra_end_while_users%%<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tbody&gt;<br/>
						&lt;/table&gt;<br/>
					</slider>
					<script>
						jQuery("slider").hide();
						jQuery("#allUserlistingButton").click(function () {
						  jQuery("slider").slideToggle("slow");
						});
					</script>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell3" colspan="2">
					<textarea id="allUserlisting" name="customUserListingSettings[allUserlisting]" wrap="off" onkeydown="return wppb_catchTab(this,event)"><?php echo $customUserListingSettings['allUserlisting'];?></textarea>
				</td>
			</tr>
		</table>	
		<br/><br/><br/><br/>
		
		<strong><?php _e('"Single-Userlisting" Template','profilebuilder');?></strong>
		<p>
		<?php _e('With the userlisting templates you can customize the look, feel and information listed by the shortcode.','profilebuilder');?><br/>
		<?php _e('The "Single User Listing" template is used to list an individual user. It\'s displayed when clickin on the "more info" link.','profilebuilder');?>
		</p>
		<table class="sortingTable">
			<!--
			<tr class="sortingTableRow">
				<td class="sortingTableCell1"><span style="padding-left:20px"> &rarr; <?php _e('Avatar/Gravatar Size: ', 'profilebuilder');?></span></td>
				<td class="sortingTableCell2">
					<select id="sortingNumberSelect" name="customUserListingSettings[avatarSizeSingle]">
						<?php
							for($i=20; $i<=200; $i++){
								echo '<option ';
								if ($customUserListingSettings['avatarSizeSingle'] == $i)
									echo 'selected="yes" ';
							echo ' value="'.$i.'">'.$i.'</option>';
							}
						?>
					</select>
				</td>
			</tr>
			-->
			<tr class="fieldTableRow">
				<td class="fieldTableCell1">
					<span style="padding-left:20px"> &rarr; <?php _e('Insert "User-Meta" Field:', 'profilebuilder'); ?></span>
				</td>
				<td class="fieldTableCell2">
					<select id="insertUserMetaField" onchange="wppb_insertAtCursor(singleUserlisting,this.value)">
						<option></option>
						<optgroup label="<?php _e('Default WordPress Fields', 'profilebuilder');?>">
							<option value="%%meta_user_name%%">%%meta_user_name%%</option>
							<option value="%%meta_first_last_name%%">%%meta_first_last_name%%</option>
							<option value="%%meta_role%%">%%meta_role%%</option>
							<option value="%%meta_email%%">%%meta_email%%</option>
							<option value="%%meta_registration_date%%">%%meta_registration_date%%</option>
							<option value="%%meta_first_name%%">%%meta_first_name%%</option>
							<option value="%%meta_last_name%%">%%meta_last_name%%</option>
							<option value="%%meta_nickname%%">%%meta_nickname%%</option>
							<option value="%%meta_display_name%%">%%meta_display_name%%</option>
							<option value="%%meta_website%%">%%meta_website%%</option>
							<option value="%%meta_biographical_info%%">%%meta_biographical_info%%</option>
							<option value="%%meta_aim%%">%%meta_aim%%</option>
							<option value="%%meta_yim%%">%%meta_yim%%</option>
							<option value="%%meta_jabber%%">%%meta_jabber%%</option>
							<option value="%%meta_number_of_posts%%">%%meta_number_of_posts%%</option>
						</optgroup>
						<optgroup label="Custom Fields, 'profilebuilder');?>">
							<?php
							foreach($wppbFetchArray as $key => $value){
								//if ($value['item_type'] !== 'avatar'){
									echo '<option value="%%meta_'.$value['item_metaName'].'%%">%%meta_'.$value['item_metaName'].'%%</option>';
								//}
								if ($value['item_type'] === 'upload')
									echo '<option value="%%meta_'.$value['item_metaName'].'_URL%%">%%meta_'.$value['item_metaName'].'_URL%%</option>';
							}
							?>
						</optgroup>
						<optgroup label="Custom Fields(Description)">
							<?php
							foreach($wppbFetchArray as $key => $value)
								//if ($value['item_type'] !== 'avatar')
									echo '<option value="%%meta_description_'.$value['item_metaName'].'%%">%%meta_description_'.$value['item_metaName'].'%%</option>';
							?>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell1">
					<span style="padding-left:20px"> &rarr; <?php _e('Insert Extra Functions:', 'profilebuilder'); ?></span>
				</td>
				<td class="fieldTableCell2">
					<select id="insertExtraFunction" onchange="wppb_insertAtCursor(singleUserlisting,this.value)">
						<option></option>
						<option value="%%extra_go_back_link%%">%%extra_go_back_link%%</option>
						<!--<option value="%%extra_avatar_or_gravatar%%">%%extra_avatar_or_gravatar%%</option>-->
					</select>
				</td>
			</tr>
			<tr class="fieldTableExtraRow">
				<td class="fieldTableCell4" colspan="2">
					<?php echo '<button type="button" id="singleUserlistingButton" class="button">'. __('Show/Hide Default "Single-Userlisting" Code','profilebuilder').'</button>';?>
					<slider2><br/><br/>
						<?php echo '<b>'.__('If you wish to use a default userlisting, just copy the following code and paste it in the textarea below:', 'profilebuilder').'</b>';?><br/><br/>
						%%extra_go_back_link%%<br/>
						&lt;table id="userListingDisplayTable"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell1" colspan="2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="sHeader"&gt;&lt;strong&gt;<?php _e('Name', 'profilebuilder');?>&lt;/strong&gt;&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputName"&gt;<?php _e('Username', 'profilebuilder');?>:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputValue"&gt;%%meta_user_name%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputName"&gt;<?php _e('First Name', 'profilebuilder');?>:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputValue"&gt;%%meta_first_name%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputName"&gt;<?php _e('Last Name', 'profilebuilder');?>:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputValue"&gt;%%meta_last_name%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputName"&gt;<?php _e('Nickname', 'profilebuilder');?>:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputValue"&gt;%%meta_nickname%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputName"&gt;<?php _e('Display name publicly as', 'profilebuilder');?>:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputValue"&gt;%%meta_display_name%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell1" colspan="2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="sHeader"&gt;&lt;strong&gt;<?php _e('Contact Info', 'profilebuilder');?>&lt;/strong&gt;&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputName"&gt;<?php _e('Website', 'profilebuilder');?>:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputValue"&gt;%%meta_website%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell1" colspan="2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="sHeader"&gt;&lt;strong&gt;<?php _e('About Yourself', 'profilebuilder');?>&lt;/strong&gt;&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputName"&gt;<?php _e('Biographical Info', 'profilebuilder');?>:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="inputValue"&gt;%%meta_biographical_info%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&lt;/table&gt;<br/>
						%%extra_go_back_link%%<br/>
					</slider2>
					<script>
						jQuery("slider2").hide();
						jQuery("#singleUserlistingButton").click(function () {
						  jQuery("slider2").slideToggle("slow");
						});
					</script>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell3" colspan="2">
					<textarea id="singleUserlisting" name="customUserListingSettings[singleUserlisting]" wrap="off" onkeydown="return wppb_catchTab(this,event)"><?php echo $customUserListingSettings['singleUserlisting'];?></textarea> 
				</td>
			</tr>
		</table>
	<div align="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</form>
	</div>
	
<?php
}

// function to display an error message in the front end in case the shortcode was used but the userlisting wasn't activated
function wppb_list_all_users_display_error($atts){
	$userlistingFilterArray['addonNotActivated'] = '<p class="error">'. __('You need to activate the User-Listing feature from within the "Addons" tab!', 'profilebuilder') .'<br/>'. __('You can find it in Profile Builder\'s menu.', 'profilebuilder').'</p>';
	$userlistingFilterArray['addonNotActivated'] = apply_filters('wppb_not_addon_not_activated', $userlistingFilterArray['addonNotActivated']);
	return $userlistingFilterArray['addonNotActivated'];
}

function wppb_add_userlisting_global(){
	global $alreadyDisplayed;
	$alreadyDisplayed = false;
}	
add_action('wp_head','wppb_add_userlisting_global');

//the function for the user-listing
function wppb_list_all_users($atts){
	$userlistingFilterArray = array();

	global $wppbFetchArray;
	global $roles;
	
	$wppbFetchArray = get_option('wppb_custom_fields');

	//get value set in the shortcode as parameter, default to "public" if not set
	extract(shortcode_atts(array('visibility' => 'public', 'roles' => '*', 'meta_key' => '', 'meta_value' => ''), $atts));
	
	//if the visibility was set to "restricted" then we need to check if the current user browsing the site/blog is logged in or not
	if ($visibility == 'restricted'){
		if ( is_user_logged_in() ) {
			$retVal = wppb_custom_userlisting_contents($roles, $meta_key, $meta_value);
			return $retVal;
			
		}elseif ( !is_user_logged_in() ) {
			$userlistingFilterArray['notLoggedIn'] = '<p class="error">'. __('You need to be logged in to view the userlisting!', 'profilebuilder') .'</p>';
			$userlistingFilterArray['notLoggedIn'] = apply_filters('wppb_not_logged_in_error_message', $userlistingFilterArray['notLoggedIn']);
			return $userlistingFilterArray['notLoggedIn'];
			
		}
	}else{
		$retVal = wppb_custom_userlisting_contents($roles, $meta_key, $meta_value);
		return $retVal;
	}
	
}

//function to return to the userlisting page without the search parameters
function wppb_clear_results(){
	$args = array('searchFor', 'setSortingOrder', 'setSortingCriteria');
	
	return remove_query_arg($args);
}

//function to return the links for the sortable headers
function wppb_get_address($criteria){
	$customUserListingSettings = get_option('customUserListingSettings','not_found');
	
	if (isset($_REQUEST['setSortingCriteria']) && ($_REQUEST['setSortingCriteria'] == $criteria))
		$setSortingCriteria = $_REQUEST['setSortingCriteria'];
	else
		$setSortingCriteria = $criteria;
		
	if (isset($_REQUEST['setSortingOrder'])){
		if ($_REQUEST['setSortingOrder'] == 'asc')
			$setSortingOrder = 'desc';
		else
			$setSortingOrder = 'asc';
			
	}else
		$setSortingOrder = $customUserListingSettings['sortingOrder'];
		
	$args = array('setSortingCriteria' => $setSortingCriteria, 'setSortingOrder' => $setSortingOrder);	
	
	$searchText = __('Search Users by All Fields', 'profilebuilder');
	$searchText = apply_filters('wppb_userlisting_search_field_text', $searchText);
	if ((isset($_REQUEST['searchFor'])) && (trim($_REQUEST['searchFor']) != $searchText))
		$args['searchFor'] = trim($_REQUEST['searchFor']);

	return add_query_arg($args);
}

//function to decode each sort tagname
function decode_sortTag($tagName){
	
	global $wppbFetchArray;
	$customUserListingSettings = get_option('customUserListingSettings','not_found');	
	
	if ($tagName == 'extra_search_all_fields'){
		$searchText = __('Search Users by All Fields', 'profilebuilder');
		$searchText = apply_filters('wppb_userlisting_search_field_text', $searchText);
		
		if (isset($_REQUEST['searchFor']))
			if (trim($_REQUEST['searchFor']) != $searchText)
				$searchText = trim($_REQUEST['searchFor']);
		
		if (isset($_REQUEST['setSortingCriteria']))
			$setSortingCriteria = $_REQUEST['setSortingCriteria'];
		else
			$setSortingCriteria = $customUserListingSettings['sortingCriteria'];
			
		if (isset($_REQUEST['setSortingOrder']))
			$setSortingOrder = $_REQUEST['setSortingOrder'];
		else
			$setSortingOrder = $customUserListingSettings['sortingOrder'];
	
		return '
			<form method="post" action="'.add_query_arg(array('page' => 1, 'setSortingCriteria' => $setSortingCriteria, 'setSortingOrder' => $setSortingOrder)).'" id="userListingForm">
				<table id="searchTable">
					<tr id="searchTableRow">
						<td id="searchTableDataCell1" class="searchTableDataCell1">
							<input onfocus="if(this.value == \''.$searchText.'\'){this.value = \'\';}" type="text" onblur="if(this.value == \'\'){this.value=\''.$searchText.'\';}" id="searchAllFields" name="searchFor" title="'.__('Leave Blank and Press Search to List All Users', 'profilebuilder').'" value="'.$searchText.'" />
						</td>
						<td id="searchTableDataCell2" class="searchTableDataCell2">
							<input type="hidden" name="action" value="searchAllFields" />
							<input type="submit" name="searchButton" class="searchAllButton" value="' . __('Search', 'profilebuilder') .'" />
							<a class="clearResults" href="'.wppb_clear_results().'">'.__('Clear Results', 'profilebuilder').'</a>
						</td>
					</tr>
				</table>
			</form>';
	
    }elseif ($tagName == 'sort_user_name'){
		$headTitle = __('Username', 'profilebuilder');
		$headTitle = apply_filters('sort_user_name_filter', $headTitle);
		
        return '<a href="'.wppb_get_address('login').'" id="sortLink" class="sortLink1">'.$headTitle.'</a>';
	
	}elseif ($tagName == 'sort_first_last_name'){
		$headTitle = __('First/Lastname', 'profilebuilder');
		$headTitle = apply_filters('sort_first_last_name_filter', $headTitle);

		return $headTitle;
		
	}elseif ($tagName == 'sort_email'){
		$headTitle = __('Email', 'profilebuilder');
		$headTitle = apply_filters('sort_email_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('email').'" id="sortLink" class="sortLink2">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_registration_date'){
		$headTitle = __('Sign-up Date', 'profilebuilder');
		$headTitle = apply_filters('sort_registration_date_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('registered').'" id="sortLink" class="sortLink3">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_first_name'){
		$headTitle = __('Firstname', 'profilebuilder');
		$headTitle = apply_filters('sort_first_name_filter', $headTitle);
		
        return '<a href="'.wppb_get_address('firstname').'" id="sortLink" class="sortLink4">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_last_name'){
		$headTitle = __('Lastname', 'profilebuilder');
		$headTitle = apply_filters('sort_last_name_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('lastname').'" id="sortLink" class="sortLink5">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_display_name'){
		$headTitle = __('Display Name', 'profilebuilder');
		$headTitle = apply_filters('sort_display_name_filter', $headTitle);
		
        return '<a href="'.wppb_get_address('nicename').'" id="sortLink" class="sortLink6">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_website'){
		$headTitle = __('Website', 'profilebuilder');
		$headTitle = apply_filters('sort_website_filter', $headTitle);
		
        return '<a href="'.wppb_get_address('url').'" id="sortLink" class="sortLink7">'.$headTitle.'</a>';
	
	}elseif ($tagName == 'sort_biographical_info'){
		$headTitle = __('Biographical Info', 'profilebuilder');
		$headTitle = apply_filters('sort_biographical_info_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('bio').'" id="sortLink" class="sortLink8">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_number_of_posts'){
		$headTitle = __('Posts', 'profilebuilder');
		$headTitle = apply_filters('sort_number_of_posts_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('post_count').'" id="sortLink" class="sortLink9">'.$headTitle.'</a>';
	}elseif ($tagName == 'sort_aim'){
		$headTitle = __('Aim', 'profilebuilder');
		$headTitle = apply_filters('sort_aim_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('aim').'" id="sortLink" class="sortLink10">'.$headTitle.'</a>';
	}elseif ($tagName == 'sort_yim'){
		$headTitle = __('Yim', 'profilebuilder');
		$headTitle = apply_filters('sort_yim_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('yim').'" id="sortLink" class="sortLink11">'.$headTitle.'</a>';
	}elseif ($tagName == 'sort_jabber'){
		$headTitle = __('Jabber', 'profilebuilder');
		$headTitle = apply_filters('sort_jabber_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('jabber').'" id="sortLink" class="sortLink12">'.$headTitle.'</a>';
	}else{
		$i = 12;
		
		foreach($wppbFetchArray as $key => $value)
			if ($tagName == 'sort_'.$value['item_metaName']){
				$i++;
			
				return '<a href="'.wppb_get_address($value['item_metaName']).'" id="sortLink" class="sortLink'.$i.'">'.$value['item_title'].'</a>';
			}
	}		
}

//function to decode the meta tags
function wppb_decode_metaTag($tagName, $object){
	global $wppbFetchArray;
	
	//filter to get current user by either username or id(default); get user by username?
	$userlistingFilterArray['getUserByID'] = false;
	$userlistingFilterArray['getUserByID'] = apply_filters('wppb_userlisting_get_user_by_id', $userlistingFilterArray['getUserByID']);
	
	if ($tagName == 'extra_more_info_link'){
		$userData = get_the_author_meta( 'user_login', $object->data->ID );
		
		$more = '';
		$url = apply_filters( 'wppb_userlisting_more_base_url', $val = get_permalink() );
		if (isset($_GET['page_id'])){
			$more = $url.'&userID='.$object->data->ID;
			$more = apply_filters ('wppb_userlisting_more_info_link_structure1', $more, $url, $object);
		}else{
			//do we need to add an extra slash?
			$slash = '';
			if ($url[strlen($url)-1] != '/')
					$slash = '/';
			if ($userlistingFilterArray['getUserByID'] === false){
				$more = $url.$slash.'user/'.$object->data->ID;
				$more = apply_filters ('wppb_userlisting_more_info_link_structure2', $more, $url, $slash, $object);
			}else{
				$more = $url.$slash.'user/'.$userData;
				$more = apply_filters ('wppb_userlisting_more_info_link_structure3', $more, $url, $slash, $userData);
			}
		}
		
		
		$textLink1 = apply_filters('wbb_userlisting_extra_more_info_link_type', true);
		
		if ($textLink1)
			return $userlistingFilterArray['moreLink1'] = apply_filters('wppb_userlisting_more_info_link', '<span id="wppb-more-span" class="wppb-more-span"><a href="'.$more.'" class="wppb-more" id="wppb-more" title="'. __('Click here to see more information about this user', 'profilebuilder') .'" alt="'. __('More...', 'profilebuilder') .'">'. __('More...', 'profilebuilder') .'</a></span>', $more);
		else	
			return $userlistingFilterArray['moreLink2'] = apply_filters('wppb_userlisting_more_info_link_with_arrow', '<a href="'.$more.'" class="wppb-more"><img src="'.WPPB_PLUGIN_URL.'/assets/images/arrow_right.png" title="'. __('Click here to see more information about this user.', 'profilebuilder') .'" alt=">"></a>');
		
		
	}elseif($tagName == 'meta_user_name'){
		$userData = get_the_author_meta( 'user_login', $object->data->ID );
		if ($userData == '')
			$userData = '-';
			
		return $userData = apply_filters('wppb_userlisting_extra_meta_user_name', $userData, $object);
	
	}elseif ($tagName == 'meta_email'){
		$userData = get_the_author_meta( 'user_email', $object->data->ID );
		if ($userData == '')
			$userData = '-';
			
		return $userData = apply_filters('wppb_userlisting_extra_meta_email', $userData, $object);
			
	}elseif ($tagName == 'meta_first_last_name'){
		$userData1 = get_the_author_meta( 'first_name', $object->data->ID );
		$userData2 = get_the_author_meta( 'last_name', $object->data->ID );
		
		if (($userData1 != '') && ($userData2 != ''))
			$userData = $userData1 .' '. $userData2;
		elseif ($userData1 == '')
			$userData = $userData2;
		elseif ($userData2 == '')
			$userData = $userData1;
		else
			$userData = '-';
			
		return $userData = apply_filters('wppb_userlisting_extra_meta_first_last_name', $userData, $object);
			
	
	}elseif ($tagName == 'meta_first_name'){
		$userData = get_the_author_meta( 'first_name', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		return $userData = apply_filters('wppb_userlisting_extra_meta_first_name', $userData, $object);	
		
	}elseif ($tagName == 'meta_role'){
		if (isset($object->roles[0]))
			$role = ucfirst($object->roles[0]);
		else
			$role = '';
			
		return $userData = apply_filters('wppb_userlisting_extra_meta_role', $role, $object);
	
	}elseif ($tagName == 'meta_number_of_posts'){
		$args = array('author'=> $object->data->ID, 'numberposts'=> -1);
		$allPosts = get_posts($args);
		$postsNumber = count($allPosts);
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_number_of_posts', '<a href="'.get_author_posts_url($object->data->ID).'" id="postNumberLink" class="postNumberLink">'.$postsNumber.'</a>', $object, $postsNumber);
		
	}elseif ($tagName == 'meta_aim'){
		$userData = get_the_author_meta( 'aim', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_aim', $userData, $object);
		
	}elseif ($tagName == 'meta_yim'){
		$userData = get_the_author_meta( 'yim', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_yim', $userData, $object);
		
	}elseif ($tagName == 'meta_jabber'){
		$userData = get_the_author_meta( 'jabber', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_jabber', $userData, $object);
		
	}elseif ($tagName == 'meta_registration_date'){
		$time = '';
		for ($i=0; $i<strlen($object->data->user_registered); $i++){
			if ($object->data->user_registered[$i] == ' ')
				break;
			else
				$time .= $object->data->user_registered[$i];
		}
	
		return $userData = apply_filters('wppb_userlisting_extra_meta_registration_date', $time, $object);
	
	}elseif ($tagName == 'meta_last_name'){
		$userData = get_the_author_meta( 'last_name', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_last_name', $userData, $object);
			
	
	}elseif ($tagName == 'meta_nickname'){
		$userData = get_the_author_meta( 'nickname', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_nickname', $userData, $object);
			
	
	}elseif ($tagName == 'meta_display_name'){
		$userData = get_the_author_meta( 'display_name', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_display_name', $userData, $object);
			
	
	}elseif ($tagName == 'meta_website'){
		$userData = get_the_author_meta( 'user_url', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_website', $userData, $object);
			
	
	}elseif ($tagName == 'meta_biographical_info'){
		$userData = get_the_author_meta( 'description', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_biographical_info', $userData, $object);
		
	}elseif ($tagName == 'extra_avatar_or_gravatar'){
		$customUserListingSettings = get_option('customUserListingSettings','not_found');
		$avatarSize = apply_filters('wppb_userlisting_avatar_size', $customUserListingSettings['avatarSize']);
	
		$avatarImage = get_avatar($object->data->ID, $avatarSize );
		
		return $userData = apply_filters('wppb_userlisting_extra_avatar_or_gravatar', $avatarImage, $object, $avatarSize);
		
		
	}else{
		global $wppbFetchArray;	
	
		if (count($wppbFetchArray) >= 1){
			foreach($wppbFetchArray as $key => $value){
				if ('meta_'.$value['item_metaName'] == $tagName){
					switch ($value['item_type']) {
						case "input":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
								
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'], $userData, $object);
						}
						case "checkbox":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							
							if ($userData != ''){							
								$userDataArray = explode(',', $userData);
								$value['item_options'] = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$value['id'].'_options_translation', $value['item_options']);
								$newValue = str_replace(' ', '#@space@#', $value['item_options']);  //we need to escape the spaces in the options list, because it won't save
								$value['item_options'] = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$value['id'].'_options_translation', $value['item_options']);
								$checkboxValue = explode(',', $value['item_options']);
								$checkboxValue2 = explode(',', $newValue);
								$nr = count($userDataArray);
									
								$userData = '';
								
								for($i=0; $i<$nr-2; $i++)
									$userData .= $userDataArray[$i]. ', ';
									$userData .= $userDataArray[$nr-2];
									
								}else
									$userData = '-';
								
								
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'], $userData, $object);
						}
						case "radio":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'], $userData, $object);
						}
						case "select":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'], $userData, $object);
						}						
						case "countrySelect":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'], $userData, $object);
						}						
						case "timeZone":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'], $userData, $object);
						}						
						case "datepicker":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'], $userData, $object);
						}						
						case "textarea":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData != '')
								$userData = nl2br($userData);
							else
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'], $userData, $object);
						}
						case "upload":{
							$imgSource = WPPB_PLUGIN_URL . '/assets/images/';
							$script = WPPB_PLUGIN_URL . '/premium/functions/';
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							$fileName = str_replace ( get_bloginfo('home').'/wp-content/uploads/profile_builder/attachments/userID_'.$object->data->ID.'_attachment_', '', $userData );
							
							if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/attachments/'))
								$retUserData = __('No uploaded attachment', 'profilebuilder');
							else
								$retUserData = $fileName.'<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a>';
								
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'], $retUserData, $object);	
						}
						case "avatar":{
							$customUserListingSettings = get_option('customUserListingSettings','not_found');
							$avatarSize = apply_filters('wppb_userlisting_avatar_size', $customUserListingSettings['avatarSize']);
							$avatarImage = get_avatar($object->data->ID, $avatarSize );

							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'], $avatarImage, $object, $avatarSize);
						}
					}
				}elseif ('meta_'.$value['item_metaName'].'_URL' == $tagName){
					switch ($value['item_type']) {
						case "upload":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							$fileName = str_replace ( get_bloginfo('home').'/wp-content/uploads/profile_builder/attachments/userID_'.$object->data->ID.'_attachment_', '', $userData );
							
							if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/attachments/'))
								return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'].'_url_empty_all', $userData, $object);	
							else
								return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'].'_url_all', $userData, $object);	
						}
					}
					
				}elseif ('meta_'.$value['item_title'] == $tagName){    //fall-back
					switch ($value['item_type']) {
						case "input":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
								
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_title'], $userData, $object);
						}
						case "checkbox":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							
							if ($userData != ''){							
								$userDataArray = explode(',', $userData);
								$value['item_options'] = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $value['item_options']);
								$newValue = str_replace(' ', '#@space@#', $value['item_options']);  //we need to escape the spaces in the options list, because it won't save
								$checkboxValue = explode(',', $value['item_options']);
								$checkboxValue2 = explode(',', $newValue);
								$nr = count($userDataArray);
									
								$userData = '';
								
								for($i=0; $i<$nr-2; $i++)
									$userData .= $userDataArray[$i]. ', ';
									$userData .= $userDataArray[$nr-2];
									
								}else
									$userData = '-';
								
								
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_title'], $userData, $object);
						}
						case "radio":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_title'], $userData, $object);
						}
						case "select":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_title'], $userData, $object);
						}						
						case "countrySelect":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_title'], $userData, $object);
						}						
						case "timeZone":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_title'], $userData, $object);
						}						
						case "datepicker":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_title'], $userData, $object);
						}						
						case "textarea":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData != '')
								$userData = nl2br($userData);
							else
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_title'], $userData, $object);
						}
						case "upload":{
							$imgSource = WPPB_PLUGIN_URL . '/assets/images/';
							$script = WPPB_PLUGIN_URL . '/premium/functions/';
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							$fileName = str_replace ( get_bloginfo('home').'/wp-content/uploads/profile_builder/attachments/userID_'.$object->data->ID.'_attachment_', '', $userData );
							
							if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/attachments/'))
								$retUserData = __('No uploaded attachment', 'profilebuilder');
							else
								$retUserData = $fileName.'<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a>';
								
							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_title'], $retUserData, $object);	
						}
						case "avatar":{
							$customUserListingSettings = get_option('customUserListingSettings','not_found');
							$avatarSize = apply_filters('wppb_userlisting_avatar_size', $customUserListingSettings['avatarSize']);
							$avatarImage = get_avatar($object->data->ID, $avatarSize );

							return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_title'], $avatarImage, $object, $avatarSize);
						}
					}
				}
			}
		}
	}	
}

//function to render 404 page in case a user doesn't exist
function wppb_set404(){
	global $wp_query;
	global $wpdb;
	
	$arrayID = array();
	$nrOfIDs = 0;
	
	//check if certain users want their profile hidden
	$extraField_meta_key = apply_filters('wppb_display_profile_meta_field_name', '');	//meta-name of the extra-field which checks if the user wants his profile hidden
	$extraField_meta_value = apply_filters('wppb_display_profile_meta_field_value', '');	//the value of the above parameter; the users with these 2 combinations will be excluded
	
	if ((trim($extraField_meta_key) != '') && (trim($extraField_meta_value) != '')){
		$result = mysql_query("SELECT wppb_t1.ID FROM $wpdb->users AS wppb_t1 LEFT OUTER JOIN $wpdb->usermeta AS wppb_t2 ON wppb_t1.ID = wppb_t2.user_id AND wppb_t2.meta_key = '".$extraField_meta_key."' WHERE wppb_t2.meta_value LIKE '%".mysql_real_escape_string(trim($extraField_meta_value))."%'");
		
		if (is_resource($result)){
			while ($row = mysql_fetch_assoc($result))
				array_push($arrayID, $row['ID']);
		}
	}
	
	//if admin approval is activated, then give 404 if the user was manually requested
	$wppb_generalSettings = get_option('wppb_general_settings', 'not_found');
	if($wppb_generalSettings != 'not_found')
		if($wppb_generalSettings['adminApproval'] == 'yes'){
		
			// Get term by name ''unapproved'' in user_status taxonomy.
			$user_statusTaxID = get_term_by('name', 'unapproved', 'user_status');
			$term_taxonomy_id = $user_statusTaxID->term_taxonomy_id;
			
			$result = mysql_query("SELECT ID FROM $wpdb->users AS t1 LEFT OUTER JOIN $wpdb->term_relationships AS t0 ON t1.ID = t0.object_id WHERE t0.term_taxonomy_id = $term_taxonomy_id");
			if (is_resource($result)){
				while ($row = mysql_fetch_assoc($result))
					array_push($arrayID, $row['ID']);
			}
		}
	
	$nrOfIDs=count($arrayID);
	
	//filter to get current user by either username or id(default); get user by username?
	$userlistingFilterArray['getUserByID'] = false;
	$userlistingFilterArray['getUserByID'] = apply_filters('wppb_userlisting_get_user_by_id', $userlistingFilterArray['getUserByID']);
	
	$invoke404 = false;
	
	//get user ID
	if (isset($_GET['userID'])){
		$userID = get_userdata($_GET['userID']);
		if (is_object($userID)){
			if ($nrOfIDs){
				if (in_array($userID->ID, $arrayID)) 
					$invoke404 = true;
			}else{
				$username = $userID->user_login;
				$user = get_user_by('login', $username);
				if (($user === false) || ($user == null))
					$invoke404 = true;
			}
		}
	}else{
		if ($userlistingFilterArray['getUserByID'] === false){
			$userID = get_query_var( 'username' );
			if ($nrOfIDs){
				if (in_array($userID, $arrayID))
					$invoke404 = true;
			}else{
				$user = get_userdata($userID);
				if (is_object($user)){
					$username = $user->user_login;
					$user = get_user_by('login', $username);
					if (($userID !== '') && ($user === false))
						$invoke404 = true;
				}
			}
			
		}else{
			$username = get_query_var( 'username' );
			$user = get_user_by('login', $username);
			if (is_object($user)){
				if ($nrOfIDs){
					if (in_array($user->ID, $arrayID))
						$invoke404 = true;
				}else{
					if (($username !== '') && ($user === false))
						$invoke404 = true;
				}
			}
		}
	}
	
	if ($invoke404)
		$wp_query->set_404(); 

}
add_action('template_redirect', 'wppb_set404');

//function  to decode all the extra tags
function wppb_decode_extraTag($tagName){
	//filter to get current user by either username or id(default); get user by username?
	$userlistingFilterArray['getUserByID'] = false;
	$userlistingFilterArray['getUserByID'] = apply_filters('wppb_userlisting_get_user_by_id', $userlistingFilterArray['getUserByID']);

	//get user ID
	if (isset($_GET['userID'])){
		$user = get_userdata($_GET['userID']);
		$username = $user->user_login;
	}else{
		if ($userlistingFilterArray['getUserByID'] === false){
			$userID = get_query_var( 'username' );
			$user = get_userdata($userID);
			$username = $user->user_login;
		}else
			$username = get_query_var( 'username' );
	}
	
	$user = get_user_by('login', $username);

	if ($user->ID == null)
		return '';
	
	if ($tagName == 'extra_go_back_link'){
		$textLink2 = apply_filters('wppb_userlisting_go_back_link_type', true);
		
		if ($textLink2)
			return $userlistingFilterArray['backLink1'] = apply_filters('wppb_userlisting_go_back_link', '<div id="wppb-back-span" class="wppb-back-span"><a href=\'javascript:history.go(-1)\' class="wppb-back" id="wppb-back" title="'. __('Click here to go back', 'profilebuilder') .'" alt="'. __('Back', 'profilebuilder') .'">'. __('Back', 'profilebuilder') .'</a></div>');
		else	
			return $userlistingFilterArray['backLink2'] = apply_filters('wppb_userlisting_go_back_link_with_arrow', '<a href=\'javascript:history.go(-1)\' class="wppb-back"><img src="'.WPPB_PLUGIN_URL.'/assets/images/arrow_left.png" title="'. __('Click here to go back', 'profilebuilder') .'" alt="<"/></a>');
	
	}elseif ($tagName == 'extra_avatar_or_gravatar'){
		$customUserListingSettings = get_option('customUserListingSettings','not_found');
		$avatarSizeSingle = apply_filters('wppb_userlisting_avatar_size_single_userlisting', $customUserListingSettings['avatarSizeSingle']);
	
		$avatarImage = get_avatar($user->ID, $avatarSize );
		
		return $userData = apply_filters('wppb_userlisting_extra_avatar_or_gravatar_single_userlisting', $avatarImage, $user, $avatarSizeSingle);	

	}elseif($tagName == 'meta_user_name'){
		$userData = get_the_author_meta( 'user_login', $user->ID );
		if ($userData == '')
			$userData = '-';
			
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_user_name', $userData, $user);
	
	}elseif ($tagName == 'meta_email'){
		$userData = get_the_author_meta( 'user_email', $user->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_email', $userData, $user);
			
	
	}elseif ($tagName == 'meta_first_name'){
		$userData = get_the_author_meta( 'first_name', $user->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_first_name', $userData, $user);
			
	}elseif ($tagName == 'meta_first_last_name'){
		$userData1 = get_the_author_meta( 'first_name', $user->ID );
		$userData2 = get_the_author_meta( 'last_name', $user->ID );
		
		if (($userData1 != '') && ($userData2 != ''))
			$userData = $userData1 .' '. $userData2;
		elseif ($userData1 == '')
			$userData = $userData2;
		elseif ($userData2 == '')
			$userData = $userData1;
		else
			$userData = '-';
			
		return $userData = apply_filters('wppb_userlisting_extra_meta_first_last_name_single_userlisting', $userData, $user);
			
			
	}elseif ($tagName == 'meta_last_name'){
		$userData = get_the_author_meta( 'last_name', $user->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_last_name', $userData, $user);
			
	
	}elseif ($tagName == 'meta_nickname'){
		$userData = get_the_author_meta( 'nickname', $user->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_nickname', $userData, $user);
			
	
	}elseif ($tagName == 'meta_display_name'){
		$userData = get_the_author_meta( 'display_name', $user->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_display_name', $userData, $user);
			
	}elseif ($tagName == 'meta_number_of_posts'){
		$args = array('author'=> $user->ID, 'numberposts'=> -1);
		$allPosts = get_posts($args);
		$postsNumber = count($allPosts);
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_number_of_posts_single_userlisting', '<a href="'.get_author_posts_url($user->ID).'" id="postNumberLinkSingle" class="postNumberLinkSingle">'.$postsNumber.'</a>', $user, $postsNumber);
		
	}elseif ($tagName == 'meta_website'){
		$userData = get_the_author_meta( 'user_url', $user->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_website', $userData, $user);
			
	
	}elseif ($tagName == 'meta_aim'){
		$userData = get_the_author_meta( 'aim', $user->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_aim', $userData, $user);

	}elseif ($tagName == 'meta_yim'){
		$userData = get_the_author_meta( 'yim', $user->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_yim', $userData, $user);

	}elseif ($tagName == 'meta_jabber'){
		$userData = get_the_author_meta( 'jabber', $user->ID );
		if ($userData == '')
			$userData = '-';
			
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_jabber', $userData, $user);
		
		
	}elseif ($tagName == 'meta_role'){
		if (isset($GLOBALS['wp_roles']->roles[$user->roles[0]]))
			$role = $GLOBALS['wp_roles']->roles[$user->roles[0]]['name'];
		else
			$role = '-';
			
		return $userData = apply_filters('wppb_userlisting_extra_meta_role_single_userlisting', $role, $user);

		
	}elseif ($tagName == 'meta_biographical_info'){
		$userData = get_the_author_meta( 'description', $user->ID );
		if ($userData == '')
			$userData = '-';
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_biographical_info', $userData, $user);
		
	}elseif ($tagName == 'meta_registration_date'){
		$time = date("n/j/Y", strtotime($user->user_registered));
		
		return $userData = apply_filters('wppb_userlisting_extra_meta_tag_registration_date', $time, $user);
		
	}else{
		global $wppbFetchArray;
	
		if (count($wppbFetchArray) >= 1){
			foreach($wppbFetchArray as $key => $value){
				if ('meta_'.$value['item_metaName'] == $tagName){
					switch ($value['item_type']) {
						case "input":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
								
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_metaName'], $userData, $user);
						}
						case "checkbox":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							
							if ($userData != ''){							
								$userDataArray = explode(',', $userData);
								$value['item_options'] = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $value['item_options']);
								$newValue = str_replace(' ', '#@space@#', $value['item_options']);  //we need to escape the spaces in the options list, because it won't save
								$checkboxValue = explode(',', $value['item_options']);
								$checkboxValue2 = explode(',', $newValue);
								$nr = count($userDataArray);
									
								$userData = '';
								
								for($i=0; $i<$nr-2; $i++)
									$userData .= $userDataArray[$i]. ', ';
									$userData .= $userDataArray[$nr-2];
									
							}else
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_metaName'], $userData, $user);
							
						}
						case "radio":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_metaName'], $userData, $user);
						}
						case "select":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_metaName'], $userData, $user);
						}						
						case "countrySelect":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_metaName'], $userData, $user);
						}						
						case "timeZone":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_metaName'], $userData, $user);
						}						
						case "datepicker":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_metaName'], $userData, $user);
						}						
						case "textarea":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData != '')
								$userData = nl2br($userData);
							else
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_metaName'], $userData, $user);
						}
						case "upload":{
							$imgSource = WPPB_PLUGIN_URL . '/assets/images/';
							$script = WPPB_PLUGIN_URL . '/premium/functions/';
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							$fileName = str_replace ( get_bloginfo('home').'/wp-content/uploads/profile_builder/attachments/userID_'.$user->ID.'_attachment_', '', $userData );
							
							if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/attachments/'))
								$retUserData = '<span class="wppb-description-delimiter2"><u>'. __('Current file', 'profilebuilder') .'</u>: </span><span class="wppb-description-delimiter2">'. __('No uploaded attachment', 'profilebuilder') .'</span>';
							else
								$retUserData = '<span class="wppb-description-delimiter2"><u>'. __('Current file', 'profilebuilder') .'</u>: '.$fileName.'<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a></span>';
								
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_metaName'], $retUserData, $user);	
						}
						case "avatar":{
							$imgSource = WPPB_PLUGIN_URL . '/assets/images/';
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);  // to use for the link
							$userData2 = get_user_meta($user->ID, 'resized_avatar_'.$value['id'], true); 	//to use for the preview
							
							//this checks if it only has 1 component
							if (is_numeric($value['item_options'])){
								$width = $height = $value['item_options'];
							//this checks if the entered value has 2 components
							}else{
								$sentValue = explode(',',$value['item_options']);
								$width = $sentValue[0];
								$height = $sentValue[1];
							}

							if ($userData != ''){
								if ($userData2 == ''){
									wppb_resize_avatar($user->ID);
									$userData2 = get_user_meta($user->ID, 'resized_avatar_'.$value['id'], true); 	//to use for the preview
									
								}
								
								$imgRelativePath = get_user_meta($user->ID, 'resized_avatar_'.$value['id'].'_relative_path', true); //get relative path
								//get image info
								$info = getimagesize($imgRelativePath);

								
								//this checks if it only has 1 component
								if (is_numeric($item_options)){
									$width = $height = $item_options;
								//this checks if the entered value has 2 components
								}else{
									$sentValue = explode(',',$item_options);
									$width = $sentValue[0];
									$height = $sentValue[1];
								}
								
								//call the avatar resize function if needed
								if (($info[0] != $width) || ($info[1] != $height)){
									wppb_resize_avatar($user->ID);
									//re-fetch user-data
									$userData2 = get_user_meta($user->ID, 'resized_avatar_'.$value['id'], true); 	//to use for the preview
								}
								
								if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/avatars/'))
									return $avatarImage = get_avatar($user->ID, $value['item_options'] );
								else{
									
										
									// display the resized image
									$retUserData = '<span class="avatar-border"><IMG SRC="'.$userData2.'" TITLE="'. __('Avatar', 'profilebuilder') .'" ALT="'. __('Avatar', 'profilebuilder') .'" HEIGHT='.$info[1].' WIDTH='.$info[0].'></span>';
									// display a link to the bigger image to see it clearly
									return $retUserData .= '<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a>';
								}
								
							}else 
								return $avatarImage = get_avatar($user->ID, $width );						
						}
					}
					
				}elseif('meta_description_'.$value['item_metaName'] == $tagName){
					return $value['item_desc'];
					
				}elseif ('meta_'.$value['item_metaName'].'_URL' == $tagName){
					switch ($value['item_type']) {
						case "upload":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							$fileName = str_replace ( get_bloginfo('home').'/wp-content/uploads/profile_builder/attachments/userID_'.$user->ID.'_attachment_', '', $userData );
							
							if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/attachments/'))
								return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'].'_url_empty_single', $userData, $object);	
							else
								return $userData = apply_filters('wppb_userlisting_extra_meta_'.$value['item_metaName'].'_url_single', $userData, $object);	
						}
					}
					
				}elseif ('meta_'.$value['item_title'] == $tagName){    //fall-back
				
					switch ($value['item_type']) {
						case "input":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
								
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_title'], $userData, $user);
						}
						case "checkbox":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							
							if ($userData != ''){							
								$userDataArray = explode(',', $userData);
								$value['item_options'] = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $value['item_options']);
								$newValue = str_replace(' ', '#@space@#', $value['item_options']);  //we need to escape the spaces in the options list, because it won't save
								$checkboxValue = explode(',', $value['item_options']);
								$checkboxValue2 = explode(',', $newValue);
								$nr = count($userDataArray);
									
								$userData = '';
								
								for($i=0; $i<$nr-2; $i++)
									$userData .= $userDataArray[$i]. ', ';
									$userData .= $userDataArray[$nr-2];
									
							}else
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_title'], $userData, $user);
							
						}
						case "radio":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_title'], $userData, $user);
						}
						case "select":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_title'], $userData, $user);
						}						
						case "countrySelect":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_title'], $userData, $user);
						}						
						case "timeZone":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_title'], $userData, $user);
						}						
						case "datepicker":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_title'], $userData, $user);
						}						
						case "textarea":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData != '')
								$userData = nl2br($userData);
							else
								$userData = '-';
							
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_title'], $userData, $user);
						}
						case "upload":{
							$imgSource = WPPB_PLUGIN_URL . '/assets/images/';
							$script = WPPB_PLUGIN_URL . '/premium/functions/';
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							$fileName = str_replace ( get_bloginfo('home').'/wp-content/uploads/profile_builder/attachments/userID_'.$user->ID.'_attachment_', '', $userData );
							
							if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/attachments/'))
								$retUserData = '<span class="wppb-description-delimiter2"><u>'. __('Current file', 'profilebuilder') .'</u>: </span><span class="wppb-description-delimiter2">'. __('No uploaded attachment', 'profilebuilder') .'</span>';
							else
								$retUserData = '<span class="wppb-description-delimiter2"><u>'. __('Current file', 'profilebuilder') .'</u>: '.$fileName.'<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a></span>';
								
							return $userData = apply_filters('wppb_userlisting_extra_meta_tag_'.$value['item_title'], $retUserData, $user);	
						}
						case "avatar":{
							$imgSource = WPPB_PLUGIN_URL . '/assets/images/';
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);  // to use for the link
							$userData2 = get_user_meta($user->ID, 'resized_avatar_'.$value['id'], true); 	//to use for the preview
							
							//this checks if it only has 1 component
							if (is_numeric($value['item_options'])){
								$width = $height = $value['item_options'];
							//this checks if the entered value has 2 components
							}else{
								$sentValue = explode(',',$value['item_options']);
								$width = $sentValue[0];
								$height = $sentValue[1];
							}

							if ($userData != ''){
								if ($userData2 == ''){
									wppb_resize_avatar($user->ID);
									$userData2 = get_user_meta($user->ID, 'resized_avatar_'.$value['id'], true); 	//to use for the preview
									
								}
								
								$imgRelativePath = get_user_meta($user->ID, 'resized_avatar_'.$value['id'].'_relative_path', true); //get relative path
								//get image info
								$info = getimagesize($imgRelativePath);

								
								//this checks if it only has 1 component
								if (is_numeric($item_options)){
									$width = $height = $item_options;
								//this checks if the entered value has 2 components
								}else{
									$sentValue = explode(',',$item_options);
									$width = $sentValue[0];
									$height = $sentValue[1];
								}
								
								//call the avatar resize function if needed
								if (($info[0] != $width) || ($info[1] != $height)){
									wppb_resize_avatar($user->ID);
									//re-fetch user-data
									$userData2 = get_user_meta($user->ID, 'resized_avatar_'.$value['id'], true); 	//to use for the preview
								}
								
								if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/avatars/'))
									return $avatarImage = get_avatar($user->ID, $value['item_options'] );
								else{
									
										
									// display the resized image
									$retUserData = '<span class="avatar-border"><IMG SRC="'.$userData2.'" TITLE="'. __('Avatar', 'profilebuilder') .'" ALT="'. __('Avatar', 'profilebuilder') .'" HEIGHT='.$info[1].' WIDTH='.$info[0].'></span>';
									// display a link to the bigger image to see it clearly
									return $retUserData .= '<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a>';
								}
								
							}else 
								return $avatarImage = get_avatar($user->ID, $width );						
						}
					}
					
				}elseif('meta_description_'.$value['item_title'] == $tagName){
					return $value['item_desc'];
					
				}
				
			}
		}
	}	
}

//function to parse the interWhileContent
function wppb_parse_interWhileContent($string, $object){

	$stringLength = strlen($string);
	$partialContent = '';
	$nCount = 0;
	
	while ($nCount < $stringLength){
		if (($string[$nCount] == '%') && ($string[$nCount+1] == '%')){
			$nCount = $nCount+2;
			$tagName = '';
			
			while(($string[$nCount] != '%') && ($string[$nCount+1] != '%')){
				$tagName .= $string[$nCount];
				$nCount++;
			}
			$tagName .= $string[$nCount];
			$nCount = $nCount+3;
			
			$partialContent .= wppb_decode_metaTag($tagName, $object);
		}else{
			$partialContent .= $string[$nCount];
			$nCount++;
		}
	}
	
	return $partialContent;
}

//function to handle the case when a search was requested but there were no results
function no_results_found_handler($content){

	$retContent = '';
	$formEnd = strpos( (string)$content, '</form>' );
	
	for ($i=0; $i<$formEnd+7; $i++){
		$retContent .= $content[$i];
	}	
	
	$userlistingFilterArray['noResultsFound'] = '<p class="noResults" id="noResults">'. __('No results found!', 'profilebuilder') .'</p>';
	$userlistingFilterArray['noResultsFound'] = apply_filters('wppb_no_results_found_message', $userlistingFilterArray['noResultsFound']);
	
	return $retContent.$userlistingFilterArray['noResultsFound'];
}

//the function to extract the raw html code (and more) from the back-end
function wppb_custom_userlisting_contents($allowedRoles, $meta_key, $meta_value){
	ob_start();

	global $alreadyDisplayed;
	
	$customUserListingSettings = get_option('customUserListingSettings','not_found');
	
	if ($customUserListingSettings != 'not_found'){
	
		$finalContent = '';
		$username = '';
		$username = get_query_var( 'username' );
		
		if (($username != '') || (isset($_GET['userID']))){
			if (!$alreadyDisplayed){
				$content = $customUserListingSettings['singleUserlisting'];
				$contentLength = strlen($content);
				
				$i = 0;
			
				while($i < $contentLength){
					if (($content[$i] == '%') && ($content[$i+1] == '%')){
						$i = $i+2;
						$tagName = '';
						
						while(($content[$i] != '%') && ($content[$i+1] != '%')){
							$tagName .= $content[$i];
							$i++;
						}
						$tagName .= $content[$i];
						$i = $i+3;
						
						$finalContent .= wppb_decode_extraTag($tagName);
					}else{
						$finalContent .= $content[$i];
						$i++;
					}
				}
				
				$alreadyDisplayed = true;
				
				echo html_entity_decode($finalContent);
			}
		}else{
			$content = $customUserListingSettings['allUserlisting'];
			$interWhileContent = '';
			$contentLength = strlen($content);
			$startWhileUsers = strpos( (string)$content, '%%extra_while_users%%' );
			$endWhileUsers = strpos( (string)$content, '%%extra_end_while_users%%' );

			$pageNum = get_query_var ('page');
			if ($pageNum > 0)
				$pageNum = $pageNum - 1;
			
			//set query args
			$args = array(
				'results_per_page'				=> $customUserListingSettings['sortingNumber'],
				'offset'						=> $pageNum*$customUserListingSettings['sortingNumber'],
				'role' 							=> $allowedRoles,
				'meta_key' 						=> $meta_key,
				'meta_value'					=> $meta_value,
				'meta_compare'					=> 'LIKE',
				'use_wildcard'					=> true				
			);
			
			// Check if some of the listing parameters have changed
			if ( isset($_REQUEST['setSortingCriteria']) && (trim($_REQUEST['setSortingCriteria']) !== '') )
				$args['sorting_criteria'] = $_REQUEST['setSortingCriteria'];
				
			if ( isset($_REQUEST['setSortingOrder']) && (trim($_REQUEST['setSortingOrder']) !== '') )
				$args['sorting_order'] = $_REQUEST['setSortingOrder'];
			
			if (isset($_REQUEST['searchFor'])){
				//was a valid string enterd in the search form?
				$searchText = __('Search Users by All Fields', 'profilebuilder');
				$searchText = apply_filters('wppb_userlisting_search_field_text', $searchText);
		
				if (trim($_REQUEST['searchFor']) !== $searchText)
					$args['search'] = $_REQUEST['searchFor'];
			}
			
			
			//use filters to change parameters if needed
			$args = apply_filters('wppb_userlisting_user_query_args', $args);
			
			//query users
			include_once (WPPB_PLUGIN_DIR . '/premium/classes/userlisting.class.php');
			$wp_user_search = new PB_WP_User_Query( $args );
			
			$thisPageOnly = $wp_user_search->get_results();			
			$totalUsers = $wp_user_search->get_total();
			
			//start creating the pagination
			include_once (WPPB_PLUGIN_DIR . '/premium/classes/pagination.class.php');
			if (($totalUsers != '0') || ($totalUsers != 0)){
				$pagination = new wppb_pagination;
				$first = __('&laquo;&laquo; First', 'profilebuilder');
				$prev = __('&laquo; Prev', 'profilebuilder');
				$next = __('Next &raquo; ', 'profilebuilder');
				$last = __('Last &raquo;&raquo;', 'profilebuilder');

				$currentPage = get_query_var ('page');

				if ($currentPage == 0)
					$currentPage = 1;
			}
			
			//specify results per page
			if (isset($_POST['searchFor'])){
				$searchText = __('Search Users by All Fields', 'profilebuilder');
				$searchText = apply_filters('wppb_userlisting_search_field_text', $searchText);
			
				if ((trim($_POST['searchFor']) == $searchText) || (trim($_POST['searchFor']) == '')){
					if (($totalUsers != '0') || ($totalUsers != 0))
						$userInfoPages = $pagination->generate($totalUsers, $customUserListingSettings['sortingNumber'], '', $first, $prev, $next, $last, $currentPage); 
				}else{
					if (($totalUsers != '0') || ($totalUsers != 0))
						$userInfoPages = $pagination->generate($totalUsers, $customUserListingSettings['sortingNumber'], trim($_POST['searchFor']), $first, $prev, $next, $last, $currentPage);
				}
			}elseif (isset($_GET['searchFor'])){
				if (($totalUsers != '0') || ($totalUsers != 0))
					$userInfoPages = $pagination->generate($totalUsers, $customUserListingSettings['sortingNumber'], trim($_GET['searchFor']), $first, $prev, $next, $last, $currentPage);
			}else{
				if (($totalUsers != '0') || ($totalUsers != 0))
					$userInfoPages = $pagination->generate($totalUsers, $customUserListingSettings['sortingNumber'], '', $first, $prev, $next, $last, $currentPage); 
			}
			
			$i = 0;
		
			while($i < $contentLength){
				if ($startWhileUsers == $i){
					$i = $i + 21;					
					
					while ($i < $endWhileUsers){
						$interWhileContent .= $content[$i];
						$i++;
					}

					foreach ($thisPageOnly as $localKey => $localValue)
						$finalContent .= wppb_parse_interWhileContent($interWhileContent, $localValue);
					
				}elseif (($content[$i] == '%') && ($content[$i+1] == '%')){
					$i = $i+2;
					$tagName = '';
					
					while(($content[$i] != '%') && ($content[$i+1] != '%')){
						$tagName .= $content[$i];
						$i++;
					}
					$tagName .= $content[$i];
					$i = $i+3;
					
					$finalContent .= decode_sortTag($tagName);
				}else{
					$finalContent .= $content[$i];
					$i++;
				}
			}
			
			if (($totalUsers != '0') || ($totalUsers != 0))
				echo html_entity_decode($finalContent);
			else{
				$finalContent = no_results_found_handler($finalContent);
				echo html_entity_decode($finalContent);
			}
			
			if (($totalUsers != '0') || ($totalUsers != 0)){
				$pageNumbers = '<br/><div class="pageNumberDisplay" id="pageNumberDisplay" align="right">'.$pagination->links().'</div>';
				$userlistingFilterArray['userlistingTablePagination'] = apply_filters('wppb_userlisting_userlisting_table_pagination', $pageNumbers);
				echo $userlistingFilterArray['userlistingTablePagination'];
			}
			
		}
	}
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

// Add the rewrite rule
function wppb_rrr_add_rules() {
	//add_rewrite_rule( '([^/]*)/user/([^/]+)','index.php?pagename=$matches[1]&username=$matches[2]', 'top' );
	add_rewrite_rule( '(.+?)/user/([^/]+)','index.php?pagename=$matches[1]&username=$matches[2]', 'top' );
}
add_action( 'admin_init', 'wppb_rrr_add_rules' );

// Add the store_id var so that WP recognizes it
function wppb_rrr_add_query_var( $vars ) {
	$vars[] = 'username';
	return $vars;
}
add_filter( 'query_vars', 'wppb_rrr_add_query_var' );

// Enqueue the userlisting javascript only on the needed page
function wppb_enqueue_userlisting_script($hook){

	if( $hook == 'users_page_ProfileBuilderOptionsAndSettings' )
		wp_enqueue_script('userlisting_script_handlder', WPPB_PLUGIN_URL.'/premium/assets/js/userlisting.scripts.js', '', PROFILE_BUILDER_VERSION);
}
add_action('admin_enqueue_scripts', 'wppb_enqueue_userlisting_script');