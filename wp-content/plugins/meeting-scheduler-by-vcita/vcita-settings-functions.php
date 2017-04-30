<?php

/**
 * Place a notification in the admin pages in one of two cases: 
 * 1. User available but didn't complete registration
 * 2. User not created and didn't request to dismiss the message
 */
function vcita_wp_add_admin_notices() {
	$vcita_widget = (array) get_option(VCITA_WIDGET_KEY);
	
	if (!isset($_GET['page']) || !preg_match('/'.VCITA_WIDGET_UNIQUE_ID.'\//',$_GET['page'])) {
	
		$vcita_section_url = admin_url("plugins.php?page=".plugin_basename(__FILE__));
		$vcita_dismiss_url = admin_url("plugins.php?page=".plugin_basename(__FILE__)."&dismiss=true");
		$prefix = "<p><b>".VCITA_WIDGET_PLUGIN_NAME." - </b>";
		$suffix = "</p>";
		$class = "error";
		$user_available = isset($vcita_widget["uid"]) && !empty($vcita_widget["uid"]);
		
		if ($user_available && !$vcita_widget['confirmed'] && !empty($vcita_widget['confirmation_token'])) {
			echo "<div class='".$class."'>".$prefix." <a href='".$vcita_section_url."'>Click here to configure your contact and meeting preferences</a>".$suffix."</div>";
			
		} else if (!$user_available && (!isset($vcita_widget["dismiss"]) || !$vcita_widget["dismiss"])) {
			echo "<div class='".$class."'>".$prefix."You still haven't completed your Meeting Scheduler settings. <a href='".$vcita_section_url."'>Click here to learn more</a>, or <a href='".$vcita_dismiss_url."'>Dismiss.</a>".$suffix."</div>";
		} 
	}
}

/**
 *  Add the vCita widget to the "Settings" Side Menu
 */
function vcita_admin_actions() {
	if (function_exists('add_menu_page')) {
		add_menu_page(__(VCITA_WIDGET_MENU_NAME, VCITA_WIDGET_MENU_NAME), __(VCITA_WIDGET_MENU_NAME, VCITA_WIDGET_SHORTCODE), 'edit_posts',  __FILE__, 'vcita_settings_menu', 
			plugins_url(VCITA_WIDGET_UNIQUE_ID.'/images/settings.jpg'));
		add_action('admin_notices', 'vcita_wp_add_admin_notices');
	}
	if (function_exists('add_submenu_page') && !vcita_is_demo_user()) {
		add_submenu_page(__FILE__, __('Contact Form', VCITA_WIDGET_MENU_NAME), __('Contact Form', VCITA_WIDGET_MENU_NAME), 'edit_posts', VCITA_WIDGET_UNIQUE_ID.'/vcita-contact-form-edit.php');
		add_submenu_page(__FILE__, __('Active Engage', VCITA_WIDGET_MENU_NAME), __('Active Engage', VCITA_WIDGET_MENU_NAME), 'edit_posts', VCITA_WIDGET_UNIQUE_ID.'/vcita-active-engage-edit.php');
		add_submenu_page(__FILE__, __('Sidebar', VCITA_WIDGET_MENU_NAME), __('Sidebar', VCITA_WIDGET_MENU_NAME), 'edit_posts', VCITA_WIDGET_UNIQUE_ID.'/vcita-sidebar-edit.php');
		add_submenu_page(__FILE__, __('Calendar', VCITA_WIDGET_MENU_NAME), __('Calendar', VCITA_WIDGET_MENU_NAME), 'edit_posts', VCITA_WIDGET_UNIQUE_ID.'/vcita-calendar-edit.php');
	}
	
	add_submenu_page(null, __('', VCITA_WIDGET_MENU_NAME), __('', VCITA_WIDGET_MENU_NAME), 'edit_posts', VCITA_WIDGET_UNIQUE_ID.'/vcita-callback.php');
	
}

/**
 * Create the Main vCita Settings form content.
 *
 * The form is constructed from a list of input fields and a preview for the result
 */
 function vcita_settings_menu() {
	vcita_add_stylesheet();
	// Disconnect should change the widget values before the prepare settings method is called.
	if (isset($_POST) && isset($_POST['Submit']) && $_POST['Submit'] == 'Disconnect') {
		$vcita_widget = (array) get_option(VCITA_WIDGET_KEY);
		vcita_trash_current_page($vcita_widget);
		vcita_trash_current_calendar_page($vcita_widget);
		
		$vcita_widget = create_initial_parameters();
		$vcita_widget["dismiss"] = "true"; // Make sure the notification won't appear 
		update_option(VCITA_WIDGET_KEY, $vcita_widget);
	}

	extract(vcita_prepare_widget_settings("settings"));

	// Check the dedicated page flag - If it is on, make sure a page is available, if not - Trash the page
	if ($update_made) {
		if ($_POST['Submit'] == "Disable Page") {
			vcita_trash_current_page($vcita_widget);
			vcita_trash_current_calendar_page($vcita_widget);
				
			$vcita_widget['contact_page_active'] = 'false';
			$vcita_widget['calendar_page_active'] = 'false';
			update_option(VCITA_WIDGET_KEY, $vcita_widget);
						
		// Make sure page is live if requested to or as default
		} else if ($_POST['Submit'] == "Activate Page" || $vcita_widget['contact_page_active'] == 'true' || $vcita_widget['calendar_page_active'] == 'true') {
			$vcita_widget = make_sure_page_published($vcita_widget);
			$vcita_widget = make_sure_calendar_page_published($vcita_widget);
		}
	}

	$vcita_dismissed = false;
	
	if (isset($_GET) && isset($_GET['dismiss']) && $_GET['dismiss'] == "true") {
		$vcita_widget["dismiss"] = true;
		$vcita_dismissed = true;
		update_option(VCITA_WIDGET_KEY, $vcita_widget);
	}
	
    ?>
    	<script type='text/javascript'>
			jQuery(function ($) {	
				$('.widgets-holder .type')
					.hover(function(){
						var currObject = $(this);
						var info = $('#widget-info');
						
						info
							.removeClass(info.data('type'))
							.data('curr_type', currObject.data('type'))
							.addClass(currObject.data('type'));
						
						window.setTimeout(function(){
							info
								.addClass('show');
						}, 1);
					}, function() {
						$('#widget-info')
							.attr('class', ' ');
					});
			
				$('#active-engage-switch')
					.change(function(){
						toggleSettingsAjax($(this), "vcita_ajax_toggle_ae");
					});
					
				$('#contact-form-switch')
					.change(function(){
						toggleSettingsAjax($(this), "vcita_ajax_toggle_contact");
					});

				$('#calendar-switch')
					.change(function(){
						toggleSettingsAjax($(this), "vcita_ajax_toggle_calendar");
					});
					
				var toggleSettingsAjax = function(currObject, action) {	
					$.post(ajaxurl, {action: action, activate: currObject.is(':checked')}, function(response) { });
				};
				
			   $('.shortcode')
		        	.click(function(){
			    		showContent($('#shortcode-template').html());    
			    	});
		        
		        $('#close-floating, #floating')
		        	.click(function(){
			        	hideContent();	
		        	});
		        
		        $('#content-holder')	
		        	.click(function(e){
			        	e.stopImmediatePropagation();
		        	});
		        
		        var showContent = function(contentToShow){
			    	if (contentToShow) {
				    	$('#content').html(contentToShow);  
				    	
			    		var contentHolder = $('#content-holder');
						var marginTop = ($(window).height() - contentHolder.outerHeight(true)) / 2;

						contentHolder.css({ 'margin-top' : marginTop });
				    	$('#floating').addClass('visible');
				    	$('#floating-holder').css({'opacity':1});
				    	$('#content-holder').css({'display':'block'});
				    }
		        };
		        
		        var hideContent = function(){
		        	$('#content').html(" ");  
				    	
			        $('#floating').removeClass('visible');
			        $('#floating-holder').css({'opacity':0});
			        $('#content-holder').css({'display':'none'});
		        };		         		         

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
				
				$('#switch-email')
		        	.click(function(){
	        			var callbackURL = "<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page='.VCITA_WIDGET_UNIQUE_ID.'/vcita-callback.php' ?>";
			        	var new_location = "http://" + "<?php echo VCITA_CHANGE_EMAIL_PATH.'?callback=' ?>" + encodeURIComponent(callbackURL) + "&invite="+"<?php echo VCITA_WIDGET_INVITE_CODE ?>"+"&lang="+"<?php echo get_locale() ?>"; 
			        	window.location = new_location;
	 	        	});			

	        	$('#scheduling-settings')
	        		.click(function(){
	        			var callbackURL = "<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page='.VCITA_WIDGET_UNIQUE_ID.'/vcita-callback.php' ?>";
			        	var new_location = "http://" + "<?php echo VCITA_SCHEDULING_PATH.'?callback=' ?>" + encodeURIComponent(callbackURL) + "&invite="+"<?php echo VCITA_WIDGET_INVITE_CODE ?>"+"&lang="+"<?php echo get_locale() ?>"; 
			        	window.open(new_location);
	        		});

	        	$('#test-drive')
	        		.click(function(){
    					var callbackURL = "<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page='.VCITA_WIDGET_UNIQUE_ID.'/vcita-callback.php' ?>";
    					if ($(this).data().demo) {
							var new_location = "http://" + "<?php echo VCITA_SCHEDULING_TEST_DRIVE_DEMO_PATH.'?callback=' ?>" + encodeURIComponent(callbackURL) + "&invite="+"<?php echo VCITA_WIDGET_INVITE_CODE ?>"+"&lang="+"<?php echo get_locale() ?>"; 
						}
        				else {
				        	var new_location = "http://" + "<?php echo VCITA_SCHEDULING_TEST_DRIVE_PATH.'?callback=' ?>" + encodeURIComponent(callbackURL) + "&invite="+"<?php echo VCITA_WIDGET_INVITE_CODE ?>"+"&lang="+"<?php echo get_locale() ?>"; 
			        	}

			        	window.open(new_location, '', 'height=740, width=1024');
	        		});

		        $('#switch-account')
		        	.click(function(){
			        	var callbackURL = "<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page='.VCITA_WIDGET_UNIQUE_ID.'/vcita-callback.php' ?>";
			        	var new_location = "http://" + "<?php echo VCITA_LOGIN_PATH.'?callback=' ?>" + encodeURIComponent(callbackURL) + "&invite="+"<?php echo VCITA_WIDGET_INVITE_CODE ?>"+"&lang="+"<?php echo get_locale() ?>"+"&login=true"; 
			        	window.location = new_location;
	 	        	});					
				
				$('#vcita-email')
					.keypress(function(e){
						if (e.keyCode == 13) {
							$('#start-login').click();
						}
					});
					
				$('a.preview')
					.bind('click', function(e){
				       var link = $(e.currentTarget);
				       var height = link.data().height ? link.data().height : 600;
				       var width = link.data().width ? link.data().width : 600;
				       var specs = 'directories=0, height=' + height + ', width=' + width + ', location=0, menubar=0, scrollbars=0, status=0, titlebar=0, toolbar=0';
				       window.open(link.attr('href'), '_blank', specs);
				       e.preventDefault();
				     });

				  function popupCenter(url, width, height, name) {
					  var left = (screen.width/2)-(width/2);
					  var top = (screen.height/2)-(height/2);
					  return window.open(url, name, "location=0,resizable=1,scrollbars=1,width="+width+",height="+height+",left="+left+",top="+top);
					}

					jQuery("a.show-in-popup").click(function(e ){
					  popupCenter(jQuery(this).attr('href'), 1100, 650, jQuery(this).data().popup_window);
					  e.stopPropagation();
					  e.preventDefault();
					});
				     				     
			    var handleWatermark = function(input){
					if(input.val().trim() != "") {
						input.removeClass('vcita-watermark');						
					} else {
						input.val(input.data('watermark'));
						input.addClass('vcita-watermark');
					}
				};	
				 
				$('input.watermark')
					.focus(function(){
						var input = $(this);
						if (input.data('watermark') == input.val()) {
							input.val("");
							input.removeClass('vcita-watermark');
						}
				 	})
				 	.each(function(){
				 		handleWatermark($(this));
				 	})
				 	.blur(function(){
				 		handleWatermark($(this));
				 	});				
			<?php 
				if (vcita_is_demo_user()) { ?>
				
				$('.gray-button-style.edit, .widgets-holder .type')
					.click(function(e){
						showContent($('#must-logged-in').html());    
						e.preventDefault();
						return false;
					});
				<?php if(get_option(VCITA_WIDGET_KEY.'init')) { ?>
				$('.vcita-wrap').append($('#settings-iframe').html())
				
				<?php 
					update_option(VCITA_WIDGET_KEY.'init', false);
				} ?>
			<?php } ?>
			});
			
		</script>
		<div class="vcita-wrap" dir="ltr">
			<div id="vcita-head">
	    		Welcome to Online Scheduling!
	    		<br>
	    		<a href="http://www.vcita.com/education_center?item=3-1" class="watch-video show-in-popup" id="play-vcita-video2">Watch Video</a>
	    	</div>
			<?php echo vcita_create_user_message($vcita_widget, $update_made); ?>
			<?php if ($vcita_dismissed) { ?>
				<div class='updated below-h2' ><p>vCita Meeting Scheduler notification has been dismissed</p></div>			
			<?php } ?>
	    	<div class="section">
	    	<?php if($first_time) {?>
	    		<h3>Settings</h3>
	    		<div class="left appointments_holder">
		    		<div class="title">Appointment requests will be sent to this email:</div>
		    		<input id="vcita-email" type="text" value="" class="watermark" data-watermark="Enter Your Email"/>
		    		<a href="javascript:void(0)" class="gray-button-style account" id="start-login"><span></span>OK</a>	    	    
	    		</div>
				<div class="left">			
					<div class="title">Scheduling settings:</div>
	    			<a href="javascript:void(0);" id="test-drive" data-demo="true">See online scheduling demo</a>
    			</div>
    			<div class="clear"></div>
	    	<?php } 
	    		else { ?>
	    		<h3>Settings</h3>
	    		<div class="left appointments_holder">
	    			<div class="title">Appointment requests will be sent to this email:</div>
		    		<label class="checked" for="user-email"></label>
		    		<input id="vcita-email" type="text" disabled="disabled" value="<?php echo($vcita_widget["email"]) ?>"/>
		    		<a href="javascript:void(0)" class="gray-button-style account" id="switch-email" ><span></span>Profile Settings</a>
				</div>
				<div class="left">
					<div class="title">Scheduling settings:</div>
	    			<a class="gray-button-style scheduling" id="scheduling-settings" href="javascript:void(0);"><span></span>Go to settings</a>
	    			<a href="javascript:void(0);" id="test-drive">Test drive your online scheduling</a>
    			</div>
    			<div class="clear"></div>
	    	<?php } ?>
	    	</div>
	    	<div class="section widgets-holder">
	    		<h3>How would you like to add scheduling to your website?</h3>
	    		<div class="widgets-management">
	    			<div class="widgets-management-head">
	    				<div class="left type">Type</div>
	    				<div class="left edit">Edit</div>
	    				<div class="left edit">Preview</div>
	    				<div class="left installation">Installation</div>
	    				<div class="clear"></div>
	    			</div>
	    			<div class="widget-object">
	    				<a class="left type active-engage" data-type="show-activeengage" href="<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page=' . VCITA_WIDGET_UNIQUE_ID . '/vcita-active-engage-edit.php' ?>">
	    					<span></span>
	    					<h4>Active Engage</h4>
	    				</a>
	    				<div class="left buttons">
	    					<a class="gray-button-style edit" href="<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page=' . VCITA_WIDGET_UNIQUE_ID . '/vcita-active-engage-edit.php' ?>"><span></span>Edit</a>
	    				</div>
	    				<div class="left buttons">
							<a class="gray-button-style preview" href="http://<?php echo VCITA_SERVER_BASE ?>/integrations/wordpress/active_engage_preview?uid=<?php echo vcita_get_uid() ?>&ver=2"><span></span>Preview</a>	    	
						</div>
	    				<div class="left installation">
	    					<div class="left">
	    						<div class="text">
		    						Enable on all pages:
	    						</div>
			    				<div class="onoffswitch">
								    <input type="checkbox" name="active-engage-switch" class="onoffswitch-checkbox" id="active-engage-switch" <?php echo($vcita_widget['engage_active'] == 'true' ? "checked" : "") ?>>
								      <label class="onoffswitch-label" for="active-engage-switch">
								        <div class="onoffswitch-inner"></div>
								        <div class="onoffswitch-switch"></div>
								    </label>
								</div>
	    					</div>
	    					<div class="clear"></div>
	    				</div>
	    				<div class="clear"></div>
	    			</div>
	    			<div class="widget-object">
	    				<a class="left type calendar" data-type="show-calendar" href="<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page=' . VCITA_WIDGET_UNIQUE_ID . '/vcita-calendar-edit.php' ?>">
	    					<span></span>
	    					<h4>Scheduling Calendar</h4>
	    				</a>
	    				<div class="left buttons">
	    					<a class="gray-button-style edit" href="<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page=' . VCITA_WIDGET_UNIQUE_ID . '/vcita-calendar-edit.php' ?>"><span></span>Edit</a>
	    				</div>
	    				<div class="left buttons">
							<a class="gray-button-style preview" href="http://<?php echo VCITA_SERVER_BASE ?>/widgets/scheduler?v=<?php echo vcita_get_uid() ?>&ver=2" data-width="700" data-height="500"><span></span>Preview</a>	    	
						</div>
	    				<div class="left installation">
	    					<div class="left">
	    						<div class="text">
		    						Add a Book Appointment page:
	    						</div>
			    				<div class="onoffswitch">
								    <input type="checkbox" name="calendar-switch" class="onoffswitch-checkbox" id="calendar-switch" <?php echo(is_calendar_page_available($vcita_widget) ? "checked" : "") ?>>
								      <label class="onoffswitch-label" for="calendar-switch">
								        <div class="onoffswitch-inner"></div>
								        <div class="onoffswitch-switch"></div>
								    </label>
								</div>
	    					</div>
	    					<div class="clear"></div>
	    				</div>
	    				<div class="clear"></div>
	    			</div>
	    			<div class="widget-object" id="contact-form-widget" >
	    				<a class="left type contact-form" data-type="show-contactform" href="<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page=' . VCITA_WIDGET_UNIQUE_ID . '/vcita-contact-form-edit.php' ?>" >
		    				<span></span>
	    					<h4>Contact Form</h4>
	    				</a>
	    				<div class="left buttons">
	    					<a class="gray-button-style edit" href="<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page=' . VCITA_WIDGET_UNIQUE_ID . '/vcita-contact-form-edit.php' ?>" ><span></span>Edit</a>
	    				</div>
	    				<div class="left buttons">
	    					<a class="gray-button-style preview" href="http://<?php echo VCITA_SERVER_BASE ?>/contact_form?v=<?php echo vcita_get_uid() ?>&ver=2" data-width="600" data-height="550"><span></span>Preview</a>
	    				</div>
	    				<div class="left installation">
	    					<div class="left">
	    						<div class="text">
			    					Add as a new contact page:
	    						</div>
	    						<div class="onoffswitch">
	    							<input type="checkbox" name="contact-form-switch" class="onoffswitch-checkbox" id="contact-form-switch" <?php echo(is_page_available($vcita_widget) ? "checked" : "") ?>>
	    							<label class="onoffswitch-label" for="contact-form-switch">
								        <div class="onoffswitch-inner"></div>
								        <div class="onoffswitch-switch"></div>
								    </label>
								</div>	  
	    					</div>
	    					<div class="clear"></div>
	    				</div>
						<div class="clear"></div>
	    			</div>
	    			<div class="widget-object" id="sidebar-widget">
	    				<a class="left type sidebar" data-type="show-sidebar" href="<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page=' . VCITA_WIDGET_UNIQUE_ID . '/vcita-sidebar-edit.php' ?>">
		    				<span></span>	    				
	    					<h4>Sidebar</h4>
	    				</a>
	    				<div class="left buttons">
	    					<a class="gray-button-style edit" href="<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page=' . VCITA_WIDGET_UNIQUE_ID . '/vcita-sidebar-edit.php' ?>"><span></span>Edit</a>
	    				</div>
	    				<div class="left buttons">
	    					<a class="gray-button-style preview" href="http://<?php echo VCITA_SERVER_BASE ?>/contact_widget?v=<?php echo vcita_get_uid() ?>&ver=2" data-width="200" data-height="500"><span></span>Preview</a>
	    				</div>
	    				<div class="left installation">
	    					<div class="left long-text">
		    					 Add sidebar using the <a href="<?php echo $url = get_admin_url('', '', 'admin') . 'widgets.php' ?>">Widgets Menu</a> or <a class="shortcode">Grab Shortcode</a>
	    					</div>
	    					<div class="clear"></div>
	    				</div>
	    				<div class="clear"></div>
	    			</div>
	    		</div>
	    		<div id="widget-info" class="">
				    <div class="content active-engage">
				      <h3>Active Engage</h3>
				      Active Engage has proven to <b>double</b> the amount of contact requests from your website.<br> The 'contact' label will pop up when a client visits your website and offer to contact you or schedule time with you.
				    </div>
				    <div class="content sidebar">
					  <h3>Sidebar</h3>
					  Add a contact box to the sidebar of your website!
					  <br>
					  Present your profile details, and offer you website visitors to set an appointment with you or send you a message.
					</div>
				    <div class="content contact-form">
					  <h3>Contact Form</h3>
					  Create a professional looking contact form customized to your preferences:
					  <br>
					  <br>
					  <ul>
					    <li>Choose your colors and fonts</li>
					    <li>Define custom fields</li>
					    <li>Write your own texts</li>
					    <li>Show your profile details and link to a public profile page</li>
					    <li>Offer appointments scheduling</li>
					  </ul>
					  All for FREE
					</div>
					<div class="content calendar">
					  <h3>Scheduling Calendar</h3>
					  Add a Scheduling Calendar to your website!
					  <br>
					  Present your profile details, and offer you website visitors to set an appointment with you or send you a message.
					</div>
				</div>
	    	</div>
	    	<div class="links-holder left">
		    	<a id="switch-account" target="_blank" href="javascript:void(0);">Switch to a different vCita account</a>
	    	</div>
	    	<div class="shortcode-holder right">
	    		To change widgets size: <a class="shortcode gray-button-style edit"><span></span>Grab Shortcodes</a>
	    	</div>
	    	<div class="clear"></div>
	    	<div class="vcita-footer">
	    		<a class="web-developers left" target="_blank" href="http://<?php echo VCITA_SERVER_BASE ?>/partners/web-professionals?invite=<?php echo VCITA_WIDGET_INVITE_CODE ?>"></a>
	    		<div class="more-offers left">
	    			<div class="green">vCita has a lot more to offer!</div>
	    			Visit <a target="_blank" href="http://<?php echo VCITA_SERVER_BASE ?>?invite=<?php echo VCITA_WIDGET_INVITE_CODE ?>">vCita</a> or <a href="http://www.vcita.com/education_center" id="not-play-vcita-video" class="show-in-popup">watch a video</a>
	    			<br>
	    			Any suggestions or questions? visit our <a target="_blank" href="http://support.vcita.com/forums">forum</a>
	    		</div>
	    		<div class="clear"></div>
	    	</div>
	    </div>
	    <div id="margin">
	    </div>
	    
	    <div id="floating">
	    	<div id="floating-holder">
		    	<div id="content-holder">
		    		<a id="close-floating"></a>
			    	<div id="content">
			    	</div>
		    	</div>
	    	</div>
	    </div>
	    
	    <script type="text/html" id="shortcode-template">
	    	<div class="short-code">
				<div>Contact Form:</div>
				<input readonly="" type="text" id="vcita_embed_widget_<?php echo $form_uid;?>" onclick="this.select();" value="[<?php echo VCITA_WIDGET_SHORTCODE; ?> type=contact width=500 height=450]">
				<div>Scheduling Calendar:</div>
				<input readonly="" type="text" id="vcita_embed_widget_<?php echo $form_uid;?>" onclick="this.select();" value="[<?php echo VCITA_WIDGET_SHORTCODE; ?> type=scheduler width=500 height=450]">
				<div>Vertical Sidebar:</div>
				<input readonly="" type="text" id="vcita_embed_widget_<?php echo $form_uid;?>" onclick="this.select();" value="[<?php echo VCITA_WIDGET_SHORTCODE; ?> type=widget height=400 width=200]">
				<div >Horizontal Widget:</div>
				<input readonly="" type="text" id="vcita_embed_widget_<?php echo $form_uid;?>" onclick="this.select();" value="[<?php echo VCITA_WIDGET_SHORTCODE; ?> type=widget height=200]">
				<div >Buttons only:</div>
				<input readonly="" type="text" id="vcita_embed_widget_<?php echo $form_uid;?>" onclick="this.select();" value="[<?php echo VCITA_WIDGET_SHORTCODE; ?> type=widget height=100]">
				
				Note: Changing the height and width will affect the widgets on your website but will not affect the preview.
	    	</div>
	    </script>
		
		<script type="text/html" id="must-logged-in">
			<div class="need-to-fill-email">		
				In order to edit the widget, please fill in the email to which contact requests should be sent.
			</div>
		</script>
		
		<script type="text/html" id="vcita-video">
			<iframe allowfullscreen="true" type="text/html" frameborder="0" height="363" src="http://www.youtube.com/embed/rv-O7gxwLbk" width="600" />
		</script>
		
		<script type="text/html" id="vcita-video2">
			<iframe allowfullscreen="true" type="text/html" frameborder="0" height="363" src="http://www.youtube.com/embed/zcPpfiwE41Q" width="600" />
		</script>

		<script type="text/html" id="settings-iframe">
			<iframe src="http://<?php echo VCITA_SERVER_BASE ?>/integrations/wordpress/settings" class="hidden" width="0" height="0"/>
		</script>
    <?php 
}

/**
 * Create the vCita floatting widget Settings form content.
 *
 * This is based on Wordpress guidelines for creating a single widget.
 */
function vcita_widget_admin() {
	vcita_add_stylesheet();
    ?>
    <script type="text/javascript">
		jQuery(function ($) {	
		     $('.start-login')
		    	.on('click', function(){
		        	var callbackURL = "<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page='.VCITA_WIDGET_UNIQUE_ID.'/vcita-callback.php' ?>";
					var email = "";
					$('.vcita-email').each(function(){
						var tempMail = $(this).val();
						if (tempMail)
					 	email = tempMail;
						if (email == $(this).data('watermark')) {
							email = "";
						}
					});
		        	
		        	var new_location = "http://" + "<?php echo VCITA_LOGIN_PATH.'?callback=' ?>" + encodeURIComponent(callbackURL) + "&invite="+"<?php echo VCITA_WIDGET_INVITE_CODE ?>"+"&lang="+"<?php echo get_locale() ?>"+"&email=" + email; 
		        	window.location = new_location;
		    	});
		    	
		    $('.switch-account')
		    	.on('click', function(){
		        	var callbackURL = "<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page='.VCITA_WIDGET_UNIQUE_ID.'/vcita-callback.php' ?>";
		        	var new_location = "http://" + "<?php echo VCITA_LOGIN_PATH.'?callback=' ?>" + encodeURIComponent(callbackURL) + "&invite="+"<?php echo VCITA_WIDGET_INVITE_CODE ?>"+"&lang="+"<?php echo get_locale() ?>"+"&login=true"; 
		        	window.location = new_location;
	 	        });					
	 	        	
        	$('.vcita-email')
				.on('keypress', function(e){
					if (e.keyCode == 13) {
						$('.start-login').click();
					}
				});
				
			$('a.preview')
				.bind('click', function(e){
			       var link = $(e.currentTarget);
			       var height = link.data().height ? link.data().height : 600;
			       var width = link.data().width ? link.data().width : 600;
			       var specs = 'directories=0, height=' + height + ', width=' + width + ', location=0, menubar=0, scrollbars=0, status=0, titlebar=0, toolbar=0';
			       window.open(link.attr('href'), '_blank', specs);
			       e.preventDefault();
			     });
			     
    	});
    </script>
    <div id="vcita_config" dir="ltr">
		<?php if(vcita_is_demo_user()) {?>
    		<h3>Contact requests will be sent to this email:</h3>
    		<input class="vcita-email" type="text" value=""/>
    		<a href="javascript:void(0)" class="gray-button-style account start-login"><span></span>OK</a>	    	    			
    	<?php } 
    		else { 
	    	$vcita_widget = (array) get_option(VCITA_WIDGET_KEY);	
    		?>
    		<h3>Contact requests will be sent to this email:</h3>
    		<label class="checked" for="user-email"></label>
    		<input class="vcita-email" type="text" disabled="disabled" value="<?php echo($vcita_widget["email"]) ?>"/>
    		<br><br>
    		<a href="javascript:void(0)" class="gray-button-style account switch-account" ><span></span>Change Email</a>
    		<br><br>    		
    		<a class="gray-button-style edit" href="<?php echo $url = get_admin_url('', '', 'admin') . 'admin.php?page=' . VCITA_WIDGET_UNIQUE_ID . '/vcita-sidebar-edit.php' ?>"><span></span>Edit</a>
    		<br><br>
			<a class="gray-button-style preview" href="http://<?php echo VCITA_SERVER_BASE ?>/contact_widget?v=<?php echo vcita_get_uid() ?>&ver=2" data-width="200" data-height="500"><span></span>Preview</a>
    	<?php } ?>			
    </div>

    <?php
}

/**
 * Update the settings link to point to the correct location
 */
function vcita_add_settings_link($links, $file) {
	if ($file == plugin_basename(VCITA_WIDGET_UNIQUE_LOCATION)) {
		$settings_link = '<a href="' . admin_url("plugins.php?page=".plugin_basename(__FILE__)) . '">Settings</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
 }
 
/**
 * Create the message which will be displayed to the user after performing an update to the widget settings.
 * The message is created according to if an error had happen and if the user had finished the registration or not.
 */
function vcita_create_user_message($vcita_widget, $update_made) {

    if (!empty($vcita_widget['uid'])) {

        // If update wasn't made, keep the message without info about the last change
        if ($update_made) {
			if ($_POST['Submit'] == "Save Settings") {
				$message .= "<div>Account <b>".$vcita_widget['email']."</b> Saved.</div><br> ";
			} else {
				$message = "<b>Changes saved</b>";
			}
        } else {
            $message = "";
        }

        $message_type = "updated below-h2"; // Wordpress classes for showing a notification box
		
        if (!$vcita_widget['confirmed']) {
			if ($update_made) {
				$message .= "<br>";
			}
			
			$message .= "<div style='overflow:hidden'>";
            $prefix = "";

			if (!empty($vcita_widget['confirmation_token'])) {
				$message .= "<div style='float:left;'>Please <b>".vcita_create_link('configure your contact and meeting preferences', 'users/confirmation', 'confirmation_token='.$vcita_widget['confirmation_token'], array('style' => 'text-decoration:underline;'))."</b> or </div>";
			} else {
				$prefix = "Please";
			}
			
			$message .= "<div style='float:left;display:block;'>".$prefix."&nbsp;follow instructions sent to your email.</div>";
			
			if (empty($vcita_widget['confirmation_token'])) {
				$message .= "&nbsp;".vcita_create_link("Send email again", 'user/send_confirmation', 'email='.$vcita_widget['email'], array('style' => 'font-weight:bold;'));
			}
			
			$message .= "</div>";
        }

    } elseif (!empty($vcita_widget['last_error'])) {
        $message = "<b>".$vcita_widget['last_error']."</b>";
        $message_type = "error below-h2";
    }

    if (empty($message)) {
        return "";
    } else {
        return "<div class='".$message_type."' style='padding:5px;text-align:left;'>".$message."</div>";
    }
}
