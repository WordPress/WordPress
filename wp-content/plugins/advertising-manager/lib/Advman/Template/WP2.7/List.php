<?php
class Advman_Template_List
{
	function display($target = null, $filter = null)
	{
		global $advman_engine;
		$ads = $advman_engine->getAds();
		$stats = $advman_engine->getStats();
		$date = date('Y-m-d');
		
		$adCount = 0;
		$activeAdCount = 0;
		$networks = array();
		if (!empty($ads)) {
			$adCount = sizeof($ads);
			foreach ($ads as $ad) {
				if ($ad->active) {
					$activeAdCount++;
				}
				$networks[strtolower(get_class($ad))] = $ad->network_name;
			}
		}
		$filterActive = !empty($filter['active']) ? $filter['active'] : null;
		$filterNetwork = !empty($filter['network']) ? $filter['network'] : null;
		
		$defaultAdName = $advman_engine->getSetting('default-ad');

        $action = isset($_POST['advman-action']) ? OX_Tools::sanitize($_POST['advman-action'], 'key') : '';
        $msg = false;
        switch ($action) {
            case 'copy' : $msg = __("Ad copied.", "advman"); break;
            case 'delete' : $msg = __("Ad deleted.", "advman"); break;
        }
		
        if ($msg) {
?>
            <div id="message" class="updated fade"><p><strong><?php echo $msg; ?></strong></p></div>
<?php
        }
?>

<div class="wrap">
	<div id="icon-edit" class="icon32"><br /></div>
<h2><?php _e('Manage Your Advertising', 'advman'); ?></h2>
<script type='text/javascript'>
/* <![CDATA[ */
function advman_setAction(action, id, name, network)
{
	submit = true;
	if (action == 'delete') {
		if ( confirm('You are about to permanently delete the ' + network + ' ad:\n\n  [' + id + '] ' + name + '\n\nAre you sure?\n(Press \'Cancel\' to keep, \'OK\' to delete)') ) {
			submit = true;
		} else {
			submit = false;
		}
	}
	
	if (submit) {
		document.getElementById('advman-action').value = action;
		document.getElementById('advman-target').value = id;
		document.getElementById('advman-form').submit();
	}
}
/* ]]> */
</script>

<form action="" method="post" id="advman-form" enctype="multipart/form-data">
<input type="hidden" id="advman-mode" name="advman-mode" value="list_ads" />
<input type="hidden" id="advman-action" name="advman-action" />
<input type="hidden" id="advman-target" name="advman-target" />

<div class="tablenav">

<div class="alignleft actions">
<select id="advman-bulk-top" name="action">
<option value="" selected="selected"><?php _e('Bulk Actions', 'advman'); ?></option>
<option value="copy"><?php _e('Copy', 'advman'); ?></option>
<option value="delete"><?php _e('Delete', 'advman'); ?></option>
</select>
<input type="submit" value="<?php _e('Apply', 'advman'); ?>" name="doaction" id="doaction" class="button-secondary action" onclick="document.getElementById('advman-action').value = document.getElementById('advman-bulk-top').value;" />

<select name='advman-filter-network' class='postform' >
	<option value='0'> <?php _e('View all ad types', 'advman'); ?> </option>
<?php foreach ($networks as $network => $networkName): ?>
	<option class="level-0"<?php echo ($filterNetwork == $network) ? ' selected' : '' ?> value="<?php echo $network ?>"> <?php printf(__('View only %s ads', 'advman'), $networkName); ?> </option>
<?php endforeach; ?>
</select>
<select name='advman-filter-active' class='postform' >
	<option value='0'> <?php _e('View all ad statuses', 'advman'); ?> </option>
	<option class="level-0"<?php echo ($filterActive == 'active') ? ' selected' : '' ?> value="active"> <?php _e('View active ads only', 'advman'); ?> </option>
	<option class="level-0"<?php echo ($filterActive == 'inactive') ? ' selected' : '' ?> value="inactive"> <?php _e('View paused ads only', 'advman'); ?> </option>
</select>
<input type="submit" id="post-query-submit" value="<?php _e('Filter', 'advman'); ?>" class="button-secondary" onclick="document.getElementById('advman-action').value = 'filter';" />
<?php if ( !empty($filterActive) || !empty($filterNetwork)) : ?>
<input type="submit" value="<?php _e('Clear', 'advman'); ?>" class="button-secondary" onclick="document.getElementById('advman-action').value = 'clear';" />
<?php endif ?>
</div>


<div class="clear"></div>
</div>

<div class="clear"></div>

<table class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col"  class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col"  class="manage-column column-title" style=""><?php _e('Name', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-type" style=""><?php _e('Type', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-format" style=""><?php _e('Format', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-active" style=""><?php _e('Active', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-default" style=""><?php _e('Default', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-stats" style=""><?php _e('Views Today', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-date" style=""><?php _e('Last Edit', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-notes" style=""><?php _e('Notes', 'advman'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
	<th scope="col"  class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col"  class="manage-column column-title" style=""><?php _e('Name', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-type" style=""><?php _e('Type', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-format" style=""><?php _e('Format', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-active" style=""><?php _e('Active', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-default" style=""><?php _e('Default', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-stats" style=""><?php _e('Views Today', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-date" style=""><?php _e('Last Edit', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-advman-notes" style=""><?php _e('Notes', 'advman'); ?></th>
	</tr>
	</tfoot>

	<tbody>
<?php foreach ($ads as $ad) : ?>
<?php if ( ($filterActive == 'active' && $ad->active) || ($filterActive == 'inactive' && !$ad->active) || empty($filterActive) ) : ?>
<?php if ( ($filterNetwork == strtolower(get_class($ad))) || empty($filterNetwork) ) : ?>
	<tr id='post-3' class='alternate author-self status-publish iedit' valign="top">
		<th scope="row" class="check-column"><input type="checkbox" name="advman-targets[]" value="<?php echo $ad->id; ?>" /></th>
		<td class="post-title column-title">
			<strong><a class="row-title" href="javascript:advman_setAction('edit','<?php echo $ad->id; ?>');" title="<?php printf(__('Edit the ad &quot;%s&quot;', 'advman'), $ad->name); ?>">[<?php echo $ad->id; ?>] <?php echo $ad->name; ?></a></strong>
			<div class="row-actions">
				<span class='edit'><a href="javascript:advman_setAction('edit','<?php echo $ad->id; ?>');" title="<?php printf(__('Edit the ad &quot;%s&quot;', 'advman'), $ad->name); ?>"><?php _e('Edit', 'advman'); ?></a> | </span>
				<span class='edit'><a class='submitdelete' title="<?php _e('Copy this ad', 'advman'); ?>" href="javascript:advman_setAction('copy','<?php echo $ad->id; ?>');"><?php _e('Copy', 'advman'); ?></a> | </span>
				<span class='edit'><a class='submitdelete' title="<?php _e('Delete this ad', 'advman'); ?>" href="javascript:advman_setAction('delete','<?php echo $ad->id; ?>', '<?php echo $ad->name; ?>', '<?php echo $ad->network_name; ?>');" onclick=""><?php _e('Delete', 'advman'); ?></a> | </span>
				<span class='edit'><a href="<?php echo $ad->get_preview_url(); ?>" target="wp-preview" id="post-preview" tabindex="4"><?php _e('Preview', 'advman'); ?></a></span>
			</div>
		</td>
		<td class="advman-type column-advman-type"><a href="javascript:advman_setAction('edit-network','<?php echo $ad->id; ?>');" title="<?php printf(__('Edit the ad network &quot;%s&quot;', 'advman'), $ad->network_name); ?>"><?php echo $ad->network_name; ?></a></td>
		<td class="advman-format column-advman-format"> <?php echo $this->displayFormat($ad); ?></td>
		<td class="advman-active column-advman-active"><a href="javascript:advman_setAction('<?php echo ($ad->active) ? 'deactivate' : 'activate'; ?>','<?php echo $ad->id; ?>');"> <?php echo ($ad->active) ? __('Yes', 'advman') : __('No', 'advman'); ?></a></td>
		<td class="advman-default column-advman-default"><a href="javascript:advman_setAction('default','<?php echo $ad->id; ?>');"> <?php echo ($ad->name == $defaultAdName) ? __('Yes', 'advman') : __('No', 'advman'); ?></a></td>
<?php
		list($last_user, $last_timestamp, $last_timestamp2) = Advman_Tools::get_last_edit($ad->get_property('revisions'));
?>		<td class="advman-stats column-advman-stats"><?php echo empty($stats[$date][$ad->id]) ? 0 : $stats[$date][$ad->id]; ?></td>
		<td class="date column-date"><abbr title="<?php echo $last_timestamp2 ?>"><?php echo $last_timestamp . __(' ago', 'advman'); ?></abbr><br /> <?php echo __('by', 'advman') . ' ' . $last_user; ?></td>
		<td class="advman-notes column-advman-notes"><abbr title="<?php echo $ad->get_property('notes'); ?>"><?php echo $ad->get_property('notes'); ?></abbr></td>
	</tr>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
	</tbody>
</table>
<div class="tablenav">
	<div class="alignleft actions">
		<select id="advman-bulk-bottom" name="action">
			<option value="" selected="selected"><?php _e('Bulk Actions', 'advman'); ?></option>
			<option value="copy"><?php _e('Copy', 'advman'); ?></option>
			<option value="delete"><?php _e('Delete', 'advman'); ?></option>
		</select>
		<input type="submit" value="<?php _e('Apply', 'advman'); ?>" name="doaction" id="doaction" class="button-secondary action" onclick="document.getElementById('advman-action').value = document.getElementById('advman-bulk-bottom').value;" />
		<br class="clear" />
	</div>
	<br class="clear" />
</div>
</form>

<div class="clear"></div></div><!-- wpbody-content -->
<div class="clear"></div></div><!-- wpbody -->
<div class="clear"></div></div><!-- wpcontent -->
</div><!-- wpwrap -->


<?php
	}
	
	/**
	 * Display the format field according to the following rules:
	 * 1.  If a format and type combination is set, fill it in
	 * 2.  If not, display the default in grey
	 */
	function displayFormat($ad)
	{
		$format = $ad->get_property('adformat');
		
		// If format is custom, format it like:  Custom (468x60)
		if ($format == 'custom') {
			$format = __('Custom', 'advman') . ' (' . $ad->get_property('width') . 'x' . $ad->get('height') . ')';
		}
		
		// Find a default if the format is not filled in
		if (empty($format)) {
			$format = $ad->get_network_property('adformat');
			if ($format == 'custom') {
				$format = __('Custom', 'advman') . ' (' . $ad->get_property('width') . 'x' . $ad->get('height') . ')';
			}
			if (!empty($format)) {
				$format = "<span style='color:gray;'>" . $format . "</span>";
			}
		}
		
		$type = $ad->get_property('adtype');
		
		// If there is an ad type, prefix it on to the format
		if (empty($type)) {
			$type = $ad->get_network_property('adtype');
			if (!empty($type)) {
				$types = array(
					'ad' => __('Ad Unit', 'advmgr'),
					'link' => __('Link Unit', 'advmgr'),
					'ref_text' => __('Text Referral', 'advmgr'),
					'ref_image' => __('Image Referral', 'advmgr'),
				);
				$type = "<span style='color:gray;'>" . $types[$type] . "</span>";
			}
		}
		
		if (!empty($format) && (!empty($type))) {
			return $type . '<br />' . $format;
		}
		
		return $type . $format;
	}
}
?>