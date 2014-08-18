<?php
	$success = $_GET['success'];
	$uid = $_GET['uid'];
	$first_name = $_GET['first_name'];
	$last_name = $_GET['last_name'];
	$title = $_GET['title'];
	$confirmation_token = $_GET['confirmation_token'];
	$confirmed = $_GET['confirmed'];
	$engage_delay = $_GET['engage_delay'];
	$implementation_key = $_GET['implementation_key'];
	$email = $_GET['email'];
	$confirmed = 'true';
	vcita_uninstall();
	vcita_clean_expert_data();
	vcita_parse_expert_data(compact(
		'success',
		'uid',
		'first_name',
		'last_name',
		'email',
		'title',
		'confirmation_token',
		'confirmed',
		'engage_delay',
		'implementation_key',
		'confirmed'));
	
	
	$redirectURL = get_admin_url('', '', 'admin').'admin.php?page='.VCITA_WIDGET_UNIQUE_ID.'/vcita-settings-functions.php';
?>
<script type="text/javascript">
	window.location = "<?php echo($redirectURL) ?>";
</script>