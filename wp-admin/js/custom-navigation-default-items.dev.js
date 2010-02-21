/**
 * WordPress Administration Custom Navigation
 * Interface JQuery functions
 *
 * @author Jeffikus <pearce.jp@gmail.com>
 * @version 1.1.0
 *
 * @package WordPress
 * @subpackage Administration
 */

/*
 * Init Functions
*/
jQuery(function($)
	{
		//Makes dialog box
		$("#dialog-confirm").dialog({
			autoOpen: false,
			resizable: false,
			height: 210,
			width: 400,
			modal: true,
			buttons: {
				'Save': function() {

					titletosave = $('#edittitle').attr('value');
					linktosave = $('#editlink').attr('value');
					anchortitletosave = $('#editanchortitle').attr('value');
					newwindowtosave = $('#editnewwindow').attr('value');
					desctosave = $('#editdescription').attr('value');

					$('#title' + $(this).dialog('option', 'itemID')).attr('value',titletosave);
					$('#linkurl' + $(this).dialog('option', 'itemID')).attr('value',linktosave);
					$('#anchortitle' + $(this).dialog('option', 'itemID')).attr('value',anchortitletosave);
					$('#newwindow' + $(this).dialog('option', 'itemID')).attr('value',newwindowtosave);
					$('#description' + $(this).dialog('option', 'itemID')).attr('value',desctosave);

					$('#menu-' + $(this).dialog('option', 'itemID') + ' > dl > dt > span.title').text(titletosave);

					$('#view' + + $(this).dialog('option', 'itemID')).attr('href', linktosave);

					$(this).dialog('close');

				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		});

		$('#message').animate({ opacity: 1.0 },2000).fadeOut(300, function(){ $(this).remove();});

		//Add dropzone
	    $('#custom-nav li').prepend('<div class="dropzone"></div>');

		//Make li items draggable
		$('#custom-nav li').draggable({
			    handle: ' > dl',
			    opacity: .8,
			    addClasses: false,
			    helper: 'clone',
			    zIndex: 100
		});

		//Make items droppable
		$('#custom-nav dl, #custom-nav .dropzone').droppable(
		{
	    	accept: '#custom-nav li',
		    tolerance: 'pointer',
	    	drop: function(e, ui)
	    	{
	        	var li = $(this).parent();
	        	var child = !$(this).hasClass('dropzone');
	        	//Add UL to first child
	        	if (child && li.children('ul').length == 0)
	        	{
	            	li.append('<ul id="sub-menu" />');
	        	}
	        	//Make it draggable
	        	if (child)
	        	{
	            	li.children('ul').append(ui.draggable);
	        	}
	        	else
	        	{
	            	li.before(ui.draggable);
	        	}

	        	li.find('dl,.dropzone').css({ backgroundColor: '', borderColor: '' });

	        	var draggablevalue = ui.draggable.attr('value');
	        	var droppablevalue = li.attr('value');
	        	li.find('#menu-' + draggablevalue).find('#parent' + draggablevalue).val(droppablevalue);
	        	$(this).parent().find("dt").removeAttr('style');
	        	$(this).parent().find("div:first").removeAttr('style');


	    	},
	    	over: function()
	    	{
	    		//Add child
	    		if ($(this).attr('class') == 'dropzone ui-droppable')
	    		{
	    			$(this).parent().find("div:first").css('background', 'none').css('height', '50px');
	    		}
	    		//Add above
	    		else if ($(this).attr('class') == 'ui-droppable')
	    		{
	    			$(this).parent().find("dt:first").css('background', '#d8d8d8');
	    		}
	    		//do nothing
	    		else {

	    		}
	    		var parentid = $(this).parent().attr('id');

	       	},
	    	out: function()
	    	{
	        	$(this).parent().find("dt").removeAttr('style');
	        	$(this).parent().find("div:first").removeAttr('style');
	        	$(this).filter('.dropzone').css({ borderColor: '' });
	    	},
	    	deactivate: function()
	    	{


	    	}


		});

		//Handle Save Button Clicks
		$('#save_top').click(function()
		{
			updatepostdata();
		});
		$('#save_bottom').click(function()
		{
			updatepostdata();
		});


	});


