<?php 
	vcita_add_stylesheet();
	$vcita_widget = (array) get_option(VCITA_WIDGET_KEY);
	$key = $vcita_widget["implementation_key"];
?>
<div class="vcita-wrap wide">	
	<div id="vcita-head">
		Edit vCita Contact Form
		<div class="small-text">Gain More Clients from Your Website!</div>
	</div>
	<a class="right done-editing" href="<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page=' . VCITA_WIDGET_UNIQUE_ID . '/vcita-settings-functions.php' ?>">Done</a>
	<a class="right back-to-main" href="<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page=' . VCITA_WIDGET_UNIQUE_ID . '/vcita-settings-functions.php' ?>">Back to main page</a>
	<div class="clear"></div>
	<?php if(!empty($key)) { ?>
	<script type="text/javascript">	
		jQuery(function ($) {	
			$('#iframe-holder').html($('#iframe-template').html());
		});
	</script>
	<div id="iframe-holder"></div>
	<script type="text/html" id="iframe-template">
		<iframe src="http://<?php echo VCITA_SERVER_BASE ?>/widget_implementations?platform=wordpress&widget=contact_form&key=<?php echo $key ?>" width="100%" height="100%"/>
	</script>
	<?php } else { ?>
	<script type="text/javascript">	
		jQuery(function ($) {	
		    $('#start-login')
		    	.click(function(){
		        	var callbackURL = "<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page='.VCITA_WIDGET_UNIQUE_ID.'/vcita-callback.php' ?>";
		        	var emailInput = $('#vcita-email');
					var email = $('#vcita-email').val();
					if (email == emailInput.data('watermark')) {
						email = "";
					}
		        	var new_location = "http://" + "<?php echo VCITA_LOGIN_PATH.'?callback=' ?>" + encodeURIComponent(callbackURL) + "&invite="+"<?php echo VCITA_WIDGET_INVITE_CODE ?>"+"&lang="+"<?php echo get_locale() ?>"+"&email=" + email; 
		        	window.location = new_location;
		    	});
		    
			$('#vcita-email')
				.keypress(function(e){
					if (e.keyCode == 13) {
						$('#start-login').click();
					}
				});
				
    	});
	</script>
	<div class="section">
		<h3>Before editing, please login to vCita</h3>
		<input id="vcita-email" type="text" value=""/>
		<a href="javascript:void(0)" class="gray-button-style account" id="start-login"><span></span>Login</a>	    	   
	</div>
	<?php } ?>
</div>