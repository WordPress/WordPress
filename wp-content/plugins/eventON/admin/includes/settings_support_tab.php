<?php
	// EventON Settings tab - Documentation and support
	// version: 0.2
?>
<div id="evcal_5" class="postbox evcal_admin_meta">	
	<?php 	if($eventon->is_eventon_activated()): ?>
		<div id='chatbox' style='display:none'>
			<div id='chatIN'>
				<div id='chatcontent'></div>
			</div>
		</div>
		<div id='chatboxB' style='display:none'></div>
	<?php endif;?>
	<div class="inside eventon_settings_page">
		<a id='evo_livechat' class='evo_support_box special' href='http://www.myeventon.com/support/' target='_blank'>
			<h3>Real-time Chat Support</h3>
			<p>Yes! you can ask any support related questions from us using the live chat support. This is free of charge. Please do remember that our availability varies on this chat. Look forward to answering your questions!</p>
		</a>
		
		<a class='evo_support_box' href='http://codecanyon.net/item/eventon-wordpress-event-calendar-plugin/1211017/comments' target='_blank'>
			<h3>Codecanyon Message Board</h3>
			<p>When the chat is offline, this is the best place for you to post your questions regarding eventon.</p>
		</a>

		<a class='evo_support_box twitter' href='http://www.twitter.com/myeventon' target='_blank'>
			<h3>Follow us on twitter @myeventon</h3>
			<p>You can get the latest updates, other news, tips and tricks for eventON via our twitter stream. You can also use this to ask quick questions (specially when chat if offline)</p>
		</a>
		

		
		
		<h2 class='heading'>myEventON Documentation</h2>
		
		<h4>Getting Started</h4>		
		<p><a href='http://www.myeventon.com/documentation/getting-started-with-eventon-adding-events/' target='_blank'>How to add events to calendar</a></p>
		<p><a href='http://www.myeventon.com/documentation/adding-calendar-to-site/' target='_blank'>How to add eventON calendar to pages</a></p>
		<p><a href='http://www.myeventon.com/documentation/how-to-use-event-types-to-do-more/' target='_blank'>How to use event_type categories to do more with eventON</a></p>
		<p><a href='http://www.myeventon.com/documentation/shortcode-guide/' target='_blank'>eventON shortcode Guide</a></p>
		
		<p><a href='http://www.myeventon.com/documentation/' target='_blank'>Complete archive of documentation</a></p>
		
		
		<h2 class='heading'>myEventON Addon Guide</h2>
		
		<p>To view guide for myEventON addons go to <a href='<?php echo get_admin_url()?>admin.php?page=eventon&tab=evcal_4'>Addons & Licenses</a> tab and click "Guide" button next to the addon.</p>
		
		
	</div>
	
<?php 	if($eventon->is_eventon_activated()): ?>

<script type="text/javascript">
jQuery(document).ready(function($){
	$('#evo_livechat').on('click', function(){
		
		$('#chatbox').fadeIn();
		$('#chatboxB').fadeIn();

		var el = document.createElement("iframe");
		var ob = document.getElementById('chatcontent');
		el.setAttribute('id', 'ifrm');
		ob.appendChild(el);
		el.setAttribute('src', 'http://www.myeventon.com/support/chat/');
		el.style.scrolling= 'no';
		var el_ = $(el);
		el_.css({'border':'0', 'height':'405px','width':'413px','overflow':'hidden','background':'transparent','z-index':'10000','position':'relative'});

		return false;
	});


	$(document).mouseup(function (e){
		var container=$('#chatcontent');
		
			if (!container.is(e.target) // if the target of the click isn't the container...
				&& e.pageX < ($(window).width() - 30)
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
			{
				$('#chatbox').fadeOut();
				$('#chatboxB').fadeOut();
			}
		
	});

	
});
</script>	
<?php endif;?>
</div>