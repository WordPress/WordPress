/**
 * WordPress Administration Custom Navigation
 * Interface JS functions
 *
 * @author Jeffikus <pearce.jp@gmail.com>
 * @version 1.1.0
 *
 * @package WordPress
 * @subpackage Administration
 */

/*
 * Removes menu items from current menu
 * @param int o - the id of the menu li to remove.
*/
function removeitem(o)
{

	var todelete = document.getElementById('menu-' + o);

	if (todelete)
	{
		var parenttodelete = document.getElementById('menu-' + o).parentNode;
        throwaway_node = parenttodelete.removeChild(todelete);
	}

	updatepostdata();
};

/*
 * Loads dialog window to edit menu items from current menu
 * @param int o - the id of the menu li to edit.
*/
function edititem(o)
{

		itemTitle = jQuery('#title' + o).attr('value');
		itemURL = jQuery('#linkurl' + o).attr('value');
		itemAnchorTitle = jQuery('#anchortitle' + o).attr('value');
		itemNewWindow = jQuery('#newwindow' + o).attr('value');
		itemDesc = jQuery('#description' + o).attr('value');

		jQuery('#dialog-confirm').dialog( 'option' , 'itemID' , o )

		jQuery('#dialog-confirm').dialog('open');

		jQuery('#edittitle').attr('value', itemTitle);
		jQuery('#editlink').attr('value', itemURL);
		jQuery('#editanchortitle').attr('value', itemAnchorTitle);
		jQuery("#editnewwindow option[value='" + itemNewWindow  + "']").attr('selected', 'selected');
		jQuery('#editdescription').attr('value', itemDesc);

};

/*
 * Prepares menu items for POST
*/
function updatepostdata()
{

	var i = 0;
	 jQuery("#custom-nav").find("li").each(function(i) {
		i = i + 1;
     	var j = jQuery(this).attr('value');

     	jQuery(this).find('#position' + j).attr('value', i);
     	jQuery(this).attr('id','menu-' + i);
     	jQuery(this).attr('value', i);

     	jQuery(this).find('#dbid' + j).attr('name','dbid' + i);
     	jQuery(this).find('#dbid' + j).attr('id','dbid' + i);

		jQuery(this).find('#postmenu' + j).attr('name','postmenu' + i);
     	jQuery(this).find('#postmenu' + j).attr('id','postmenu' + i);

     	var p = jQuery(this).find('#parent' + j).parent().parent().parent().attr('value');

     	jQuery(this).find('#parent' + j).attr('name','parent' + i);
     	jQuery(this).find('#parent' + j).attr('id','parent' + i);
     	if (p) {
     		//Do nothing
     	}
     	else {
     		//reset p to be top level
     		p = 0;
     	}

     	jQuery(this).find('#parent' + j).attr('value', p);

     	jQuery(this).find('#title' + j).attr('name','title' + i);
     	jQuery(this).find('#title' + j).attr('id','title' + i);

     	jQuery(this).find('#linkurl' + j).attr('name','linkurl' + i);
     	jQuery(this).find('#linkurl' + j).attr('id','linkurl' + i);

     	jQuery(this).find('#description' + j).attr('name','description' + i);
     	jQuery(this).find('#description' + j).attr('id','description' + i);

     	jQuery(this).find('#icon' + j).attr('name','icon' + i);
     	jQuery(this).find('#icon' + j).attr('id','icon' + i);

     	jQuery(this).find('#position' + j).attr('name','position' + i);
     	jQuery(this).find('#position' + j).attr('id','position' + i);

     	jQuery(this).find('#linktype' + j).attr('name','linktype' + i);
     	jQuery(this).find('#linktype' + j).attr('id','linktype' + i);

     	jQuery(this).find('#anchortitle' + j).attr('name','anchortitle' + i);
     	jQuery(this).find('#anchortitle' + j).attr('id','anchortitle' + i);

     	jQuery(this).find('#newwindow' + j).attr('name','newwindow' + i);
     	jQuery(this).find('#newwindow' + j).attr('id','newwindow' + i);

     	jQuery(this).find('dl > dt > span > #remove' + j).attr('value', i);
     	jQuery(this).find('dl > dt > span > #remove' + j).attr('onClick', 'removeitem(' + i + ')');
     	jQuery(this).find('dl > dt > span > #remove' + j).attr('id','remove' + i);

     	jQuery('#licount').attr('value',i);

   });



};

/*
 * Adds item from Page, Category, or Custom options menu
 * @param string templatedir - directory of the add, edit, view images.
 * @param string additemtype - Page, Category, or Custom.
 * @param string itemtext - menu text.
 * @param string itemurl - url of the menu.
 * @param int itemid - menu id.
 * @param int itemparentid - default 0.
 * @param string itemdescription - the description of the menu item.
*/
function appendToList(templatedir,additemtype,itemtext,itemurl,itemid,itemparentid,itemdescription)
{
	var inputvaluevarname = '';
	var inputvaluevarurl = '';
	var inputitemid = '';
	var inputparentid= '';
	var inputdescription = '';
	var inputicon = '';

	if (additemtype == 'Custom')
	{
		inputvaluevarname = document.getElementById('custom_menu_item_name').value;
		inputvaluevarurl = document.getElementById('custom_menu_item_url').value;
		inputitemid = '';
		inputparentid = '';
		inputlinktype = 'custom';
		inputdescription = document.getElementById('custom_menu_item_description').value;
	}
	else if (additemtype == 'Page')
	{
		inputvaluevarname = htmlentities(itemtext.toString());
		inputvaluevarurl = itemurl.toString();
		inputitemid = itemid.toString();
		inputparentid = '0';
		inputlinktype = 'page';
		inputdescription = htmlentities(itemdescription.toString());

	}
	else if (additemtype == 'Category')
	{
		inputvaluevarname = htmlentities(itemtext.toString());
		inputvaluevarurl = itemurl.toString();
		inputitemid = itemid.toString();
		inputparentid = '0';
		inputlinktype = 'category';
		inputdescription = htmlentities(itemdescription.toString());
	}
	else
	{
		inputvaluevarname = '';
		inputvaluevarname = '';
		inputitemid = '';
		inputparentid = '';
		inputlinktype = 'custom';
		inputdescription = '';
	}

	var count=document.getElementById('custom-nav').getElementsByTagName('li').length + 1;

	var randomnumber = count;

	var validatetest = 0;

	try
	{
		var test=document.getElementById("menu-" + randomnumber.toString()).value;
	}
	catch (err)
	{
		validatetest = 1;
	}

	while (validatetest == 0)
	{
		randomnumber = randomnumber + 1;

		try
		{
			var test2=document.getElementById("menu-" + randomnumber.toString()).value;
		}
		catch (err)
		{
			validatetest = 1;
		}
	}

	//Notification Message
	jQuery('.maintitle').after('<div id="message" class="updated fade below-h2"><p>Menu Item added!</p></div>');
	jQuery('#message').animate({ opacity: 1.0 },2000).fadeOut(300, function(){ jQuery(this).remove();});

	//Appends HTML to the menu
	jQuery('#custom-nav').append('<li id="menu-' + randomnumber + '" value="' + randomnumber + '"><div class="dropzone ui-droppable"></div><dl class="ui-droppable"><dt><span class="title">' + inputvaluevarname + '</span><span class="controls"><span class="type">' + additemtype + '</span><a id="edit' + randomnumber + '" onclick="edititem(' + randomnumber + ')" value="' + randomnumber +'"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="' + templatedir + '/wp-admin/images/ico-edit.png" /></a> <a id="remove' + randomnumber + '" onclick="removeitem(' + randomnumber + ')" value="' + randomnumber +'"><img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="' + templatedir + '/wp-admin/images/ico-close.png" /></a> <a href="' + inputvaluevarurl + '" target="_blank"><img alt="View Custom Link" title="View Custom Link" src="' + templatedir + '/wp-admin/images/ico-viewpage.png" /></a></span></dt></dl><a class="hide" href="' + inputvaluevarurl + '">' + inputvaluevarname + '</a><input type="hidden" name="postmenu' + randomnumber + '" id="postmenu' + randomnumber + '" value="' + inputitemid + '" /><input type="hidden" name="parent' + randomnumber + '" id="parent' + randomnumber + '" value="' + inputparentid + '" /><input type="hidden" name="title' + randomnumber + '" id="title' + randomnumber + '" value="' + inputvaluevarname + '" /><input type="hidden" name="linkurl' + randomnumber + '" id="linkurl' + randomnumber + '" value="' + inputvaluevarurl + '" /><input type="hidden" name="description' + randomnumber + '" id="description' + randomnumber + '" value="' + inputdescription + '" /><input type="hidden" name="icon' + randomnumber + '" id="icon' + randomnumber + '" value="' + inputicon + '" /><input type="hidden" name="position' + randomnumber + '" id="position' + randomnumber + '" value="' + randomnumber + '" /><input type="hidden" name="linktype' + randomnumber + '" id="linktype' + randomnumber + '" value="' + inputlinktype + '" /><input type="hidden" name="anchortitle' + randomnumber + '" id="anchortitle' + randomnumber + '" value="' + inputvaluevarname + '" /><input type="hidden" name="newwindow' + randomnumber + '" id="newwindow' + randomnumber + '" value="0" /></li>');

	//make menu item draggable
	jQuery('#menu-' + randomnumber + '').draggable(
	{
		handle: ' > dl',
		opacity: .8,
		addClasses: false,
		helper: 'clone',
		zIndex: 100
	});

	//make menu item droppable
	jQuery('#menu-' + randomnumber + ' dl, #menu-' + randomnumber + ' .dropzone').droppable({
		accept: '#' + randomnumber + ', #custom-nav li',
		tolerance: 'pointer',
		drop: function(e, ui)
		{
			var li = jQuery(this).parent();
			var child = !jQuery(this).hasClass('dropzone');
			//Append UL to first child
			if (child && li.children('ul').length == 0)
			{
				li.append('<ul/>');
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
	        jQuery(this).parent().find("dt").removeAttr('style');
	        jQuery(this).parent().find("div:first").removeAttr('style');

		},
		over: function()
	    	{
	    		//Add child
	    		if (jQuery(this).attr('class') == 'dropzone ui-droppable')
	    		{
	    			jQuery(this).parent().find("div:first").css('background', 'none').css('height', '50px');
	    		}
	    		//Add above
	    		else if (jQuery(this).attr('class') == 'ui-droppable')
	    		{
	    			jQuery(this).parent().find("dt:first").css('background', '#d8d8d8');
	    		}
	    		//do nothing
	    		else {

	    		}
	    		var parentid = jQuery(this).parent().attr('id');

	       	},
	    	out: function()
	    	{
	        	jQuery(this).parent().find("dt").removeAttr('style');
	        	jQuery(this).parent().find("div:first").removeAttr('style');
	        	jQuery(this).filter('.dropzone').css({ borderColor: '' });
	    	}
	});

	updatepostdata();
};