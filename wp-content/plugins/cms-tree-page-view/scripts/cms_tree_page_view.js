
/*

Some docs so I remember how things work:

Timers:

	cmstpv_global_link_timer
	set when mouse over link. used to show the actions div.

*/


/**
 * Should have a module for all instead...
 */
var cms_tree_page_view = (function ($) {
	
	var my = {},
		privateVariable = 1;
	 
	function privateMethod() {
		// ...
	}
	 
	my.moduleProperty = 1;
	my.elements = {};
	
	my.selectors = {
		containers: "div.cms_tpv_container",
		action_div: "div.cms_tpv_page_actions",
		action_doit: "div.cms_tpv_action_add_doit"
	};

	my.init = function() {
		my.log("init cms tree page view");
		my.setup_elements();
		my.setup_listeners();
	};

	// this is wrong, the action div and doit should be for each container, not for all
	my.setup_elements = function() {
		for (var elm in my.selectors) {
			if (my.selectors.hasOwnProperty(elm))
				my.elements[elm] = $(my.selectors[elm]);
		}
	};

	my.setup_listeners = function() {
		
		// When something has been written in one of the page titles: show another row
		// Also: if more than one row are empty at the end, remove all but the last
		$(document).on("keyup", "ul.cms_tpv_action_add_doit_pages li:last-child input", function(e) {

			var $t = $(this);
			var $li = $t.closest("li");

			if ($.trim($t.val()) !== "") {
			
				var $new_li = $li.clone().hide();
				$new_li.find("input").val("");
				$li.after( $new_li );
				$new_li.slideDown();
			
			}

		});

		// Click on cancel-link = hide add-div
		jQuery(document).on("click", "a.cms_tpv_add_cancel", function(e) {

			e.preventDefault();
			var actions_div_doit = cms_tpv_get_page_actions_div_doit(this);
			actions_div_doit.slideUp("fast", function() {

				// Reset status back to draft
				$("input[name='cms_tpv_add_status'][value='draft']").attr("checked", true);

				// Remove all LIs except the last one
				$("ul.cms_tpv_action_add_doit_pages").find("li:not(:last)").remove();

			});

		});

		// Click on link to add pages
		jQuery(document).on("click", "a.cms_tpv_action_add_page_after, a.cms_tpv_action_add_page_inside", function(e) {

			e.preventDefault();
			var $this = jQuery(this);
			var post_type = cms_tpv_get_post_type(this);
			var selected_lang = cms_tpv_get_wpml_selected_lang(this);
			var actions_div = cms_tpv_get_page_actions_div(this);
			var actions_div_doit = cms_tpv_get_page_actions_div_doit(this);
			var post_status = actions_div.data("post_status");

			var add_type = "";
			if ($this.hasClass("cms_tpv_action_add_page_after")) {
				add_type = "after";
			} else if ($this.hasClass("cms_tpv_action_add_page_inside")) {
				add_type = "inside";
			}

			// not allowed when status is trash
			if (post_status === "trash" && add_type === "inside") {
				jAlert(cmstpv_l10n.Can_not_add_page_after_when_status_is_trash);
				return;
			}

			// if status is draft then it's not ok to add sub pages
			if (post_status === "draft" && add_type === "inside") {
				jAlert(cmstpv_l10n.Can_not_add_sub_page_when_status_is_draft);
				return false;
			}

			// Make the list sortable
			$("ul.cms_tpv_action_add_doit_pages").sortable({
				"axis": "y",
				"items": "> li:not(:last)",
				"containment": 'parent',
				"forceHelperSize": true,
				"forcePlaceholderSize": true,
				"handle": "span:first",
				"placeholder": "ui-state-highlight"
			});

			// Set up correct values for input fields and radio buttons and then show form/section
			actions_div_doit.find("[name='lang']").val(selected_lang);
			actions_div_doit.find("[name='cms_tpv_add_type'][value='"+add_type+"']").attr("checked", "checked");
			actions_div_doit.find("[name='ref_post_id']").val( actions_div.data("post_id") );
			actions_div_doit.slideDown("fast", function() {
				actions_div_doit.find("[name='cms_tpv_add_new_pages_names[]']").focus();
			});

			
		}); // click add page

		// submit form with new pages
		jQuery(document).on("submit", "div.cms_tpv_action_add_doit form", function(e) {

			//e.preventDefault();
			my.log("submitting form");
			var $form = $(this);
			$form.find("input[type='submit']").val( cmstpv_l10n.Adding ).attr("disabled", true);

		});

	};

	/**
	 * Log, but only if console.log is available
	 */
	my.log = function(what) {
		if (typeof(window.console) === "object" && typeof(window.console.log) === "function" ) {
			// console.log(what);
		}
	};
	 
	return my;

}(jQuery));

// Bott it up on domready
jQuery(function() {
	cms_tree_page_view.init();
});


// @todo: add prefix to treeOptions, div_actions
var cms_tpv_tree, treeOptions, div_actions, cms_tpv_current_li_id = null;
jQuery(function($) {
	
	// Globals, don't "var" them! :)
	cms_tpv_tree = $("div.cms_tpv_container");
	div_actions = $("div.cms_tpv_page_actions");
	cms_tpv_message = $("div.cms_tpv_message");

	// try to override css
	var height = "20", height2 = "18", ins_height = "20";
	css_string = '' +
		'.jstree ul, .jstree li { display:block; margin:0 0 0 0; padding:0 0 0 0; list-style-type:none; } ' +
		'.jstree li { display:block; min-height:'+height+'px; line-height:'+height+'px; white-space:nowrap; margin-left:18px; min-width:18px; } ' +
		'.jstree-rtl li { margin-left:0; margin-right:18px; } ' +
		'.jstree > ul > li { margin-left:0px; } ' +
		'.jstree-rtl > ul > li { margin-right:0px; } ' +
		'.jstree ins { display:inline-block; text-decoration:none; width:18px; height:'+height+'px; margin:0 0 0 0; padding:0; } ' +
		'.jstree a { display:inline-block; line-height:'+height2+'px; height:'+height2+'px; color:black; white-space:nowrap; text-decoration:none; padding:1px 2px; margin:0; } ' +
		'.jstree a:focus { outline: none; } ' +
		'.jstree a > ins { height:'+ins_height+'px; width:16px; } ' +
		'.jstree a > .jstree-icon { margin-right:3px; } ' +
		'.jstree-rtl a > .jstree-icon { margin-left:3px; margin-right:0; } ' +
		'li.jstree-open > ul { display:block; } ' +
		'li.jstree-closed > ul { display:none; } ' +
		'#vakata-dragged { background-color: white; };' +
		'';
	$.vakata.css.add_sheet({
		str : css_string,
		title : "jstree_cms_tpv"
	});

	treeOptions = {
		//plugins: ["themes","json_data","cookies","search",CMS_TPV_CAN_DND, "types"],
		plugins: ["themes","json_data","cookies","search","dnd", "types"],
		core: {
			"html_titles": true
		},
		"json_data": {
			"ajax": {
				"url": ajaxurl + CMS_TPV_AJAXURL + CMS_TPV_VIEW,
				// this function is executed in the instance's scope (this refers to the tree instance)
				// the parameter is the node being loaded (may be -1, 0, or undefined when loading the root nodes)
				"data" : function (n) {
					// the result is fed to the AJAX request `data` option
					if (n.data) {
						var post_id = n.data("post_id");
						return {
							"id": post_id
						};
					}
				},
				"success": function(data, status) {
					
					// If data is null or empty = show message about no nodes
					if (data === null || !data) {
						cms_tpv_message.html( "<p>" + cmstpv_l10n["No posts found"] + "</p>" );
						cms_tpv_message.show();
					} /*else {
						cms_tpv_message.hide();
					}*/

				},
				"error": function(data, status) {
				}

			}

		},
		"themes": {
			"theme": "wordpress",
			"dots": false
		},
		"search": {
			"ajax" : {
				"url": ajaxurl + CMS_TPV_AJAXURL + CMS_TPV_VIEW
			},
			"case_insensitive": true
		},
		"dnd": {
		}
	};

	if (cms_tpv_tree.length > 0) {
		cms_tpv_bind_clean_node(); // don't remember why I run this here.. :/
	}
	cms_tpv_tree.each(function(i, elm) {

		var $elm = $(elm);

		// init tree, with settings specific for each post type
		var treeOptionsTmp = jQuery.extend(true, {}, treeOptions); // make copy of object
		var post_type = cms_tpv_get_post_type(elm);
		treeOptionsTmp.json_data.ajax.url = treeOptionsTmp.json_data.ajax.url + "&post_type=" + post_type + "&lang=" + cms_tpv_get_wpml_selected_lang(elm);
		treeOptionsTmp.json_data.data = cms_tpv_jsondata[post_type]; // get from js
		
		var isHierarchical = $(elm).closest(".cms_tpv_wrapper").find("[name=cms_tpv_meta_post_type_hierarchical]").val();
		if (isHierarchical === "0") {
			
			// no move to children if not hierarchical
			treeOptionsTmp.types = {
				"types": {
					"default" : {
						"valid_children" : [ "none" ]
					}
				}
			};

		}
		
		// set search url to include post type
		treeOptionsTmp.search.ajax.url = ajaxurl + CMS_TPV_AJAXURL + CMS_TPV_VIEW + "&post_type=" + cms_tpv_get_post_type(this);

		$elm.bind("search.jstree", function (event, data) {
			if (data.rslt.nodes.length === 0) {
				// no hits. doh.
				$(this).closest(".cms_tpv_wrapper").find(".cms_tree_view_search_form_no_hits").fadeIn("fast");
			}
		});
		
		// whole tre loaded
		$elm.bind("loaded.jstree", cms_tpv_tree_loaded);
		
		$elm.jstree(treeOptionsTmp);

	});

}); // end ondomready


function cms_tpv_mouseover(e) {

	var $this = jQuery(this);
	var $li = $this.closest("li");
	cms_tpv_mouseover_li(e, $li.get(0));
	return true;

}


/**
 * When tree is loaded: start hoverindenting stuff
 * Is only fired when tree was loaded and contained stuff
 */
function cms_tpv_tree_loaded(event, data) {
		
	var $container = jQuery(event.target);
	var actions_div_doit = cms_tpv_get_page_actions_div_doit(event.target);

	// init = clear up some things
	actions_div_doit.hide();
	$container.find("li.has-visible-actions").removeClass("has-visible-actions");
	$container.find("a.hover").removeClass("hover");
	$container.find("div.cms_tpv_page_actions").removeClass("cms_tpv_page_actions_visible");
	$container.find("div.cms_tpv_page_actions_visible").removeClass("cms_tpv_page_actions_visible");

	// when mouse enters a/link
	// start timer and if no other a/link has been moused over since it started it's ok to show this one
	jQuery($container).on("mouseenter", "a", function(e) {

		// Don't activate if entered on ins-tag. We use that only for drag and drop
		// if (e.relatedTarget && e.relatedTarget.tagName === "INS") return;

		cms_tree_page_view.log("mouseenter container");

		var global_timer = $container.data("cmstpv_global_link_timer");

		if (global_timer) {
			// global timer exists, so overwrite it with our new one
			// stop that timer before setting ours
			cms_tree_page_view.log("clear global timer");
			clearTimeout(global_timer);
		} else {
			// no timer exists, overwrite with ours
		}

		// create new timer to show action div, no matter if one exists already
		// but not if we are creating new pages
		cms_tree_page_view.log("add timer for mousover on link");

		if ( ! actions_div_doit.is(":visible") ) {

			var timeoutID = setTimeout((function() {
				cms_tpv_mouseover_li(e);
			}), 500, e);

			$container.data("cmstpv_global_link_timer", timeoutID);

		} else {
			//console.log("timer not added because doit visible");
		}

	});

	/**
	 * When mouse down we may want to drag and drop,
	 * so hide the action div and cancel the timer
	 */
	jQuery($container).on("mousedown", "a", function(e) {
		
		cms_tree_page_view.log("mousedown a");

		var $target = jQuery(e.target);
		var $container = $target.closest("div.cms_tpv_container");
		var $wrapper = $container.closest("div.cms_tpv_wrapper");

		$container.find("li.has-visible-actions").removeClass("has-visible-actions");
		$container.find("a.hover").removeClass("hover");
		$wrapper.find("div.cms_tpv_page_actions").removeClass("cms_tpv_page_actions_visible");

	});

}


// get post type
// elm must be within .cms_tpv_wrapper to work
function cms_tpv_get_post_type(elm) {
	return jQuery(elm).closest("div.cms_tpv_wrapper").find("[name=cms_tpv_meta_post_type]").val();
}
// get selected lang
function cms_tpv_get_wpml_selected_lang(elm) {
	return jQuery(elm).closest("div.cms_tpv_wrapper").find("[name=cms_tpv_meta_wpml_language]").val();
}

function cms_tpv_get_page_actions_div(elm) {
	return jQuery(elm).closest("div.cms_tpv_wrapper").find("div.cms_tpv_page_actions");
}

function cms_tpv_get_page_actions_div_doit(elm) {
	return jQuery(elm).closest("div.cms_tpv_wrapper").find("div.cms_tpv_action_add_doit");
}


function cms_tpv_get_wrapper(elm) {
	var $wrapper = jQuery(elm).closest("div.cms_tpv_wrapper");
	return $wrapper;
}


// check if tree is beging dragged
function cms_tpv_is_dragging() {
	var eDrag = jQuery("#vakata-dragged");
	return eDrag.is(":visible");
}

// fired when mouse is over li
// actually when over a, old name :/
function cms_tpv_mouseover_li(e) {

	var $target = jQuery(e.target);
	var $li = $target.closest("li");
	var div_actions_for_post_type = cms_tpv_get_page_actions_div($li);
	var actions_div_doit = cms_tpv_get_page_actions_div_doit($li);
	var $cms_tpv_container = $li.closest("div.cms_tpv_container");

	if (cms_tpv_is_dragging() === false) {
		
		var is_visible = div_actions_for_post_type.is(":visible");
		is_visible = false;

		if (is_visible) {
			// do nada
		} else {

			// Add info to the action div from the li
			div_actions_for_post_type.data("post_status", $li.data("post_status"));

			// Remove classes on all elements
			$cms_tpv_container.find("li.has-visible-actions").removeClass("has-visible-actions");
			$cms_tpv_container.find("a.hover").removeClass("hover");

			// Add classes to only our new
			$li.addClass("has-visible-actions");
			$cms_tpv_container.addClass("has-visible-actions");
			$li.find("a:first").addClass("hover");
			
			// setup link for view page
			$view = div_actions_for_post_type.find(".cms_tpv_action_view");
			var permalink = $li.data("permalink");
			$view.attr("href", permalink);

			// setup link for edit page
			$edit = div_actions_for_post_type.find(".cms_tpv_action_edit");
			var editlink = $li.data("editlink");
			$edit.attr("href", editlink);
			$edit.removeClass("hidden");

			// ..and some extras
			div_actions_for_post_type.find(".cms_tpv_page_actions_modified_time").text($li.data("modified_time"));
			div_actions_for_post_type.find(".cms_tpv_page_actions_modified_by").text($li.data("modified_author"));
			div_actions_for_post_type.find(".cms_tpv_page_actions_page_id").text($li.data("post_id"));
			div_actions_for_post_type.find(".cms_tpv_page_actions_columns").html( unescape($li.data("columns")) );

			// add post title as headline
			div_actions_for_post_type.find(".cms_tpv_page_actions_headline").html( $li.data("post_title") );

			// add post id to data
			div_actions_for_post_type.data("post_id", $li.data("post_id"));
			

			// check permissions, may the current user add page, after or inside
			// If page has status draft then no one is allowed to add page inside
			// div_actions_for_post_type.find(".cms_tpv_action_add_page_inside, .cms_tpv_action_add_page_inside").show();
			div_actions_for_post_type.find(".cms_tpv_action_add_page_inside, .cms_tpv_action_add_page_inside").removeClass("hidden");
			
			var inside_allowed = true;
			if ("draft" === $li.data("post_status")) {
				inside_allowed = false;
			}

			if (!inside_allowed) {
				//div_actions_for_post_type.find(".cms_tpv_action_add_page_inside").hide();
			}


			// position and show action div
			var $a = $li.find("a");
			var width = $a.outerWidth(true);
			var new_offset = div_actions_for_post_type.offset();
			var new_offset_left = e.pageX + 35;
		
			new_offset_left = $a.offset().left + $a.width() + 20;
			new_offset.left = new_offset_left;
			new_offset.top = $a.offset().top - 30;
			div_actions_for_post_type.offset(new_offset);

			// check if action div bottom is visible in browser window, if not move it up until it is
			var pos_diff = (div_actions_for_post_type.offset().top + div_actions_for_post_type.height()) - (jQuery(window).height() + jQuery(window).scrollTop());
			if (pos_diff > 0)  {
				
				// set action div to begin at bottom of link instead
				new_offset.top = $a.offset().top - div_actions_for_post_type.height() + 15; // <- ska bli botten på vår div
				div_actions_for_post_type.offset( new_offset );
				div_actions_for_post_type.addClass("cms_tpv_page_actions_visible_from_bottom");
				
			} else {
				div_actions_for_post_type.removeClass("cms_tpv_page_actions_visible_from_bottom");
			}
			
			div_actions_for_post_type.addClass("cms_tpv_page_actions_visible");
			
			// check if user is allowed to edit page
			var $cms_tpv_action_add_and_edit_page = div_actions_for_post_type.find(".cms_tpv_action_add_and_edit_page");
			if ($li.data("user_can_edit_page") === "0") {
				$edit.addClass("hidden");
			}

			$cms_tpv_add_position = div_actions_for_post_type.find(".cms_tpv_add_position");
			$cms_tpv_add_position.removeClass("hidden");

			$cms_tpv_action_add_page_after = div_actions_for_post_type.find(".cms_tpv_action_add_page_after");
			$cms_tpv_action_add_page_after.removeClass("hidden");
			if ($li.data("user_can_add_page_after") === "0") {
				$cms_tpv_action_add_page_after.addClass("hidden");
				$cms_tpv_add_position.addClass("hidden");
			}

			$cms_tpv_action_add_page_inside = div_actions_for_post_type.find(".cms_tpv_action_add_page_inside");
			$cms_tpv_action_add_page_inside.removeClass("hidden");
			if ($li.data("user_can_add_page_inside") === "0") {
				$cms_tpv_action_add_page_inside.addClass("hidden");
				$cms_tpv_add_position.addClass("hidden");
			}

		}
	}

}

/**
 * When mouse leaves the whole cms tree page view-area/div
 * hide actions div after moving mouse out of a page and not moving it on again for...a while
 */
jQuery(document).on("mouseleave", "div.cms_tpv_container", function(e) {

	cms_tree_page_view.log("mouseleave container");
	
	var $container = jQuery(e.target).closest("div.cms_tpv_container");
	var $wrapper = $container.closest("div.cms_tpv_wrapper");
	var t = this;
	
	// Reset global timer
	var global_timer = $container.data("cmstpv_global_link_timer");
	if (global_timer) {
		cms_tree_page_view.log("clear global timer");
		clearTimeout(global_timer);
	}

	// Don't hide if in add-pages-mode
	var actions_div_doit = cms_tpv_get_page_actions_div_doit(this);
	if ( actions_div_doit.is(":visible") )
		return;

	// Maybe hide popup after a short while
	var hideTimer = setTimeout(function() {
		
		cms_tree_page_view.log("maybe hide popup because outside container");

		// But don't hide if we are inside the popup
		var $relatedTarget = jQuery(e.relatedTarget);
		if ($relatedTarget.hasClass("cms_tpv_page_actions")) {
			// we are over the actions div, so don't hide
			cms_tree_page_view.log("cancel hide beacuse over actions div");
		} else {
			// somewhere else, do hide
			cms_tree_page_view.log("do hide");
			$container.find("li.has-visible-actions").removeClass("has-visible-actions");
			$container.find("a.hover").removeClass("hover");
			$wrapper.find("div.cms_tpv_page_actions").removeClass("cms_tpv_page_actions_visible");
		}

	}, 500);

	$container.data("cmstpv_global_link_timer", hideTimer);

});

// When mouse enters actions div then cancel possibly global hide timer
// If moved outside container and then back, cancel possibly global timer
jQuery(document).on("mouseenter", "div.cms_tpv_page_actions", function(e) {
	
	var $this = jQuery(this);
	var $wrapper = $this.closest("div.cms_tpv_wrapper");
	var $container = $wrapper.find("div.cms_tpv_container");
	var timer = $container.data("cmstpv_global_link_timer");
	
	clearTimeout(timer);

});


// When mouse enter the whole cms tree page view-area/div
jQuery(document).on("mouseenter", "div.cms_tpv_container", function(e) {

	var $container = jQuery(this);
	jQuery.data(this, "cmstpv_do_hide_after_timeout", false);

});


/**
 * add childcount and other things to each li
 */
function cms_tpv_bind_clean_node() {
	
	cms_tpv_tree.bind("move_node.jstree", function (event, data) {
		var nodeBeingMoved = data.rslt.o; // noden vi flyttar
		var nodeNewParent = data.rslt.np;
		var nodePosition = data.rslt.p;
		var nodeR = data.rslt.r;
		var nodeRef = data.rslt.or; // noden som positionen gäller versus
		var selected_lang = cms_tpv_get_wpml_selected_lang(nodeBeingMoved);
		/*

		// om ovanför
		o ovanför or
		
		// om efter
		o efter r
		
		// om inside
		o ovanför or
		

		drop_target		: ".jstree-drop",
		drop_check		: function (data) { return true; },
		drop_finish		: $.noop,
		drag_target		: ".jstree-draggable",
		drag_finish		: $.noop,
		drag_check		: function (data) { return { after : false, before : false, inside : true }; }
		
		Gets executed after a valid drop, you get one parameter, which is as follows:
		data.o - the object being dragged
		data.r - the drop target
		*/
		
		var node_id,
			ref_node_id;
		if (nodePosition == "before") {
			node_id = jQuery( nodeBeingMoved ).attr( "id" );
			ref_node_id = jQuery( nodeRef ).attr( "id" );
		} else if (nodePosition == "after") {
			node_id = jQuery( nodeBeingMoved ).attr( "id" );
			ref_node_id = jQuery( nodeR ).attr( "id" );
		} else if (nodePosition == "inside") {
			node_id = jQuery( nodeBeingMoved ).attr( "id" );
			ref_node_id = jQuery( nodeR ).attr( "id" );
		}
		
		// Update parent or menu order
		jQuery.post(ajaxurl, {
				action: "cms_tpv_move_page",
				"node_id": node_id,
				"ref_node_id": ref_node_id,
				"type": nodePosition,
				"icl_post_language": selected_lang
			}, function(data, textStatus) {
		});

	});
	
	cms_tpv_tree.bind("clean_node.jstree", function(event, data) {
		var obj = (data.rslt.obj);
		if (obj && obj != -1) {
			obj.each(function(i, elm) {

				var li = jQuery(elm);
				var aFirst = li.find("a:first");

				// check that we haven't added our stuff already
				if (li.data("done_cms_tpv_clean_node")) {
					return;
				} else {
					li.data("done_cms_tpv_clean_node", true);
				}

				var childCount = li.data("childCount");
				if (childCount > 0) {
					aFirst.append("<span title='" + childCount + " " + cmstpv_l10n.child_pages + "' class='child_count'>("+childCount+")</span>");
				}
				
				// add protection type
				var rel = li.data("rel");
				if(rel == "password") {
					aFirst.find("ins").after("<span class='post_protected' title='" + cmstpv_l10n.Password_protected_page + "'>&nbsp;</span>");
				}

				// add page type
				var post_status = li.data("post_status");
				// post_status can be any value because of plugins like Edit flow
				// check if we have an existing translation for the string, otherwise use the post status directly
				var post_status_to_show = "";
				if (post_status_to_show = cmstpv_l10n["Status_" + post_status + "_ucase"]) {
					// it's ok
				} else {
					post_status_to_show = post_status;
				}
				if (post_status != "publish") {
					aFirst.find("ins").first().after("<span class='post_type post_type_"+post_status+"'>" + post_status_to_show + "</span>");
				}

				// To make hoverindent work we must wrap something around the a bla bla bla
				//var div_wrap = jQuery("<div class='cmstpv-hoverIntent-wrap' />");
				//aFirst.wrap(div_wrap);

			});
		}
	});
}

// Perform search when submiting form
jQuery(document).on("submit", "form.cms_tree_view_search_form", function(e) {
	
	var $wrapper = jQuery(this).closest(".cms_tpv_wrapper");
	$wrapper.find(".cms_tpv_search_no_hits").hide();
	var s = $wrapper.find(".cms_tree_view_search").attr("value");
	s = jQuery.trim( s );

	if (s) {
		$wrapper.find(".cms_tree_view_search_form_no_hits").fadeOut("fast");
		$wrapper.find(".cms_tree_view_search_form_working").fadeIn("fast");
		$wrapper.find(".cms_tree_view_search_form_reset");
		$wrapper.find(".cms_tpv_container").jstree("search", s);
		$wrapper.find(".cms_tree_view_search_form_reset").fadeIn("fast");
	} else {
		$wrapper.find(".cms_tree_view_search_form_no_hits").fadeOut("fast");
		$wrapper.find(".cms_tpv_container").jstree("clear_search");
		$wrapper.find(".cms_tree_view_search_form_reset").fadeOut("fast");
	}
	$wrapper.find(".cms_tree_view_search_form_working").fadeOut("fast");
	
	return false;

});

// Reset search when click on x-link
jQuery(document).on("click", "a.cms_tree_view_search_form_reset", function(e) {
	var $wrapper = jQuery(this).closest(".cms_tpv_wrapper");
	$wrapper.find(".cms_tree_view_search").val("");
	$wrapper.find(".cms_tpv_container").jstree("clear_search");
	$wrapper.find(".cms_tree_view_search_form_reset").fadeOut("fast");
	$wrapper.find(".cms_tree_view_search_form_no_hits").fadeOut("fast");
	return false;
});

// open/close links
jQuery(document).on("click", "a.cms_tpv_open_all", function(e) {
	var $wrapper = jQuery(this).closest(".cms_tpv_wrapper");
	$wrapper.find(".cms_tpv_container").jstree("open_all");
	return false;
});

jQuery(document).on("click", "a.cms_tpv_close_all", function(e) {
	var $wrapper = jQuery(this).closest(".cms_tpv_wrapper");
	$wrapper.find(".cms_tpv_container").jstree("close_all");
	return false;
});

// view all or public or trash
jQuery(document).on("click", "a.cms_tvp_view_all", function(e) {
	cms_tvp_set_view("all", this);
	return false;
});

jQuery(document).on("click", "a.cms_tvp_view_public", function(e) {
	cms_tvp_set_view("public", this);
	return false;
});

jQuery(document).on("click", "a.cms_tvp_view_trash", function() {
	cms_tvp_set_view("trash", this);
	return false;
});


// click on link to change WPML-language
jQuery(document).on("click", "a.cms_tvp_switch_lang", function(e) {
	
	$wrapper = cms_tpv_get_wrapper(this);

	// Mark clicked link as selected
	$wrapper.find("ul.cms_tvp_switch_langs a").removeClass("current");
	jQuery(this).addClass("current");

	// Determine selected language, based on classes on the link
	var re = /cms_tpv_switch_language_code_([\w-]+)/;
	var matches = re.exec( jQuery(this).attr("class") );
	var lang_code = matches[1];

	// Add seleted lang to hidden input
	$wrapper.find("[name=cms_tpv_meta_wpml_language]").val(lang_code);

	// Update post count
	// Post counts are stored on the links for all | public | trash
	var $ul_select_view = $wrapper.find(".cms-tpv-subsubsub-select-view");
	$ul_select_view.find("li.cms_tvp_view_is_status_view a").each(function(i, a_tag) {
		
		// check if this link has a data attr with count for the selected lang
		var $a = jQuery(a_tag);
		var link_count = $a.data("post-count-" + lang_code);
		if ("undefined" === typeof(link_count)) link_count = 0;

		$a.find(".count").text("(" + link_count + ")");

	});

	// Set the view = reload the tree
	var current_view = cms_tpv_get_current_view(this);
	cms_tvp_set_view(current_view, this);
	
	return false;

});

function cms_tpv_hide_action_div() {
	
}


function cms_tpv_get_current_view(elm) {
	
	$wrapper = cms_tpv_get_wrapper(elm);
	
	if ($wrapper.find(".cms_tvp_view_all").hasClass("current")) {
		return "all";
	} else if ($wrapper.find(".cms_tvp_view_public").hasClass("current")) {
		return "public";
	} else {
		return false; // like unknown
	}

}

/**
 * Sets the view; load pages for the current lang + post type + status
 * @param view all | public | trash
 * @elm element
 */
function cms_tvp_set_view(view, elm) {

	var $wrapper = jQuery(elm).closest(".cms_tpv_wrapper"),
		div_actions_for_post_type = cms_tpv_get_page_actions_div(elm);

	cms_tpv_message.hide();

	$wrapper.append(div_actions_for_post_type);
	$wrapper.find(".cms_tvp_view_all, .cms_tvp_view_public, .cms_tvp_view_trash").removeClass("current");
	$wrapper.find(".cms_tpv_container").jstree("destroy").html("");
	$wrapper.find("div.cms_tpv_page_actions").removeClass("cms_tpv_page_actions_visible");

	cms_tpv_bind_clean_node();

	// Mark selected link
	if (view == "all") {
		$wrapper.find(".cms_tvp_view_all").addClass("current");
	} else if (view == "public") {
		$wrapper.find(".cms_tvp_view_public").addClass("current");
	} else if (view == "trash") {
		$wrapper.find(".cms_tvp_view_trash").addClass("current");
	} else {
		
	}
	
	// Reload tree
	var treeOptionsTmp = jQuery.extend(true, {}, treeOptions);
	treeOptionsTmp.json_data.ajax.url = ajaxurl + CMS_TPV_AJAXURL + view + "&post_type=" + cms_tpv_get_post_type(elm) + "&lang=" + cms_tpv_get_wpml_selected_lang(elm);

	$wrapper.find(".cms_tpv_container").bind("loaded.jstree open_node.jstree", cms_tpv_tree_loaded);
	$wrapper.find(".cms_tpv_container").jstree(treeOptionsTmp);

}



/**
 * Stuff for the posts overview setting
 */
jQuery(function($) {

	// Move tree link into position
	var tree_view_switch = $("#view-switch-tree"),
		tree_view_switch_a = tree_view_switch.closest("a"),
		cmstpv_postsoverview_wrap = $("div.cmstpv-postsoverview-wrap");

	// Check if view-switch exists and add it if it does not
	// It must exist because that's where we have our switch to tree-icon
	var view_switch = $("div.view-switch");
	if (! view_switch.length) {
		
		view_switch = $("<div class='view-switch'></div>");
		$("div.tablenav-pages:first").after(view_switch);

		var view_switch_list = $("#view-switch-list"),
			view_switch_list_a = view_switch_list.closest("a");

		//view_switch.append(view_switch_list_a);
		//view_switch.append(" ");

	}
	
	// Add our link inside view switch

	/*
	view_switch.append(tree_view_switch_a);
	view_switch.addClass("view-switch-cstpv-icon-added");
	
	// if in tree mode: add a class to wpbody so we can style things
	if (cmstpv_postsoverview_wrap.length) {

		$wp_body = $("#wpbody");
		$wp_body.addClass("cmstpv_postsoverview_enabled");

		// Move wordpress table with view etc above cms tree page view so the icons get the correct position
		var viewswitch = $("div.view-switch");
		viewswitch.appendTo(cmstpv_postsoverview_wrap);

	}
	*/

});