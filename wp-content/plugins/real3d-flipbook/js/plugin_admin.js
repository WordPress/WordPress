(function ($) {
    $(document).ready(function () {
	
	// console.log(options);
	
	var json_str = options.replace(/&quot;/g, '"');
	options = jQuery.parseJSON(json_str);
	
	// console.log(options);
	
	addOption("general", "name", "text", "Flipbook name","");
	
	addOption("pdf", "pdfUrl", "selectFile", "PDF file url (if not defined, you need to import pages as jpg-s) ","");
	addOption("pdf", "pdfPageScale", "text", "PDF page scale (between 1 and 2) ",1.5);
	
	addOption("general", "sound", "checkbox", "Sounds",true);
	
	addOption("general", "mode", "dropdown", "Flipbook mode","normal", ["normal","lightbox","fullscreen"]);
	addOption("general", "skin", "dropdown", "Flipbook skin","light", ["light","dark"]);
	
	addOption("normal", "backgroundColor", "text", "Flipbook background color","#818181");
	addOption("normal", "backgroundPattern", "selectImage", "Background image pattern url","");
	
	addOption("normal", "height", "text", "Flipbook height",400);
	addOption("normal", "fitToWindow", "checkbox", "Fit to window",false);
	addOption("normal", "fitToParent", "checkbox", "Fit to parent div",false);
	addOption("normal", "fitToHeight", "checkbox", "Fit to height",false);
	addOption("normal", "offsetTop", "text", "Flipbook offset top (fullscreen)",0);
	
	addOption("lightbox", "lightboxThumbnailUrl", "selectImage", "Lightbox Thumbnail Url","");
	addOption("lightbox", "lightboxCssClass", "text", "Lightbox element CSS class","");
	addOption("lightbox", "lightboxText", "text", "Lightbox link text","");
	
	addOption("lightbox", "lightBoxOpened", "checkbox", "Lightbox openes on start",false);
	addOption("lightbox", "lightBoxFullscreen", "checkbox", "Lightbox openes in fullscreen",false);
	addOption("general", "thumbnailsOnStart", "checkbox", "Show thumbnails on start",false);
	addOption("general", "contentOnStart", "checkbox", "Show content on start",false);
	
	addOption("general", "rightToLeft", "checkbox", "Right to left mode",false);
	addOption("general", "loadAllPages", "checkbox", "Load all pages on start",false);
	addOption("general", "pageWidth", "text", "Page width",1000);
	addOption("general", "pageHeight", "text", "Page height",1414);
	addOption("general", "thumbnailWidth", "text", "Thumbnail width",100);
	addOption("general", "thumbnailHeight", "text", "Thumbnail height",141);
	addOption("general", "flipType", "dropdown", "Flip type","3d", ["2d","3d"]);
	
	addOption("general", "zoom", "text", "Zoom",0.8);
	addOption("general", "zoomLevels", "text", "Zoom levels","0.8, 1,1.5,2,3,4");
	addOption("general", "zoomDisabled", "checkbox", "Mouse wheel zoom disabled",false);
	
	addOption("menu", "btnNext[enabled]", "checkbox", "Button next page",true);
	addOption("menu", "btnNext[icon]", "text", "Button next page CSS class","fa-chevron-right");
	addOption("menu", "btnNext[title]", "text", "Button next page title","Next Page");
	
	addOption("menu", "btnPrev[enabled]", "checkbox", "Button previous page",true);
	addOption("menu", "btnPrev[icon]", "text", "Button previous page CSS class","fa-chevron-left");
	addOption("menu", "btnPrev[title]", "text", "Button previous page title","Next Page");
	
	addOption("menu", "btnZoomIn[enabled]", "checkbox", "Button zoom in",true);
	addOption("menu", "btnZoomIn[icon]", "text", "Button zoom in CSS class","fa-plus");
	addOption("menu", "btnZoomIn[title]", "text", "Button zoom in title","Zoom in");
	
	addOption("menu", "btnZoomOut[enabled]", "checkbox", "Button zoom out",true);
	addOption("menu", "btnZoomOut[icon]", "text", "Button zoom out CSS class","fa-minus");
	addOption("menu", "btnZoomOut[title]", "text", "Button zoom out title","Zoom out");
	
	addOption("menu", "btnToc[enabled]", "checkbox", "Button table of content",true);
	addOption("menu", "btnToc[icon]", "text", "Button table of content CSS class","fa-list-ol");
	addOption("menu", "btnToc[title]", "text", "Button table of content title","Table of content");
	
	addOption("menu", "btnThumbs[enabled]", "checkbox", "Button thumbnails",true);
	addOption("menu", "btnThumbs[icon]", "text", "Button thumbnails CSS class","fa-th-large");
	addOption("menu", "btnThumbs[title]", "text", "Button thumbnails title","Pages");
	
	addOption("menu", "btnShare[enabled]", "checkbox", "Button share",true);
	addOption("menu", "btnShare[icon]", "text", "Button share CSS class","fa-link");
	addOption("menu", "btnShare[title]", "text", "Button share title","Share");
	
	addOption("menu", "btnSound[enabled]", "checkbox", "Button sound",true);
	addOption("menu", "btnSound[icon]", "text", "Button sound CSS class","fa-volume-up");
	addOption("menu", "btnSound[title]", "text", "Button sound title","Sound");
	
	addOption("menu", "btnDownloadPages[enabled]", "checkbox", "Button download pages",false);
	addOption("menu", "btnDownloadPages[url]", "selectFile", "Url of zip file containing all pages","");
	addOption("menu", "btnDownloadPages[icon]", "text", "Button download pages CSS class","fa-download");
	addOption("menu", "btnDownloadPages[title]", "text", "Button download pages title","Download pages");
	
	addOption("menu", "btnDownloadPdf[enabled]", "checkbox", "Button download pdf",false);
	addOption("menu", "btnDownloadPdf[url]", "selectFile", "url of pdf file","");
	addOption("menu", "btnDownloadPdf[icon]", "text", "Button download pdf CSS class","fa-file");
	addOption("menu", "btnDownloadPdf[title]", "text", "Button download pdf title","Download pdf");
	
	addOption("menu", "btnExpand[enabled]", "checkbox", "Button expand",true);
	addOption("menu", "btnExpand[icon]", "text", "Button expand CSS class","fa-expand");
	addOption("menu", "btnExpand[iconAlt]", "text", "Button compress CSS class","fa-compress");
	addOption("menu", "btnExpand[title]", "text", "Button expand title","Toggle fullscreen");
	
	addOption("menu", "btnExpandLightbox[enabled]", "checkbox", "Button expand lightbox",true);
	addOption("menu", "btnExpandLightbox[icon]", "text", "Button expand lightbox CSS class","fa-expand");
	addOption("menu", "btnExpandLightbox[iconAlt]", "text", "Button compress lightbox CSS class","fa-compress");
	addOption("menu", "btnExpandLightbox[title]", "text", "Button expand lightbox title","Toggle fullscreen");
	
	addOption("general", "startPage", "text", "Start page",1);
	
	
	addOption("general", "deeplinking[enabled]", "checkbox", "Deep linking",false);
	addOption("general", "deeplinking[prefix]", "text", "Deep linking prefix","");
	// addOption("general", "time1", "text", "Duration of first half of the flip [ms]",500);
	// addOption("general", "transition1", "dropdown", "Transition","easeInQuad", ["easeInQuad","easeInQuad"]);
	// addOption("general", "time2", "text", "Duration of second half of the flip [ms]",600);
	// addOption("general", "transition2", "dropdown", "Transition","easeOutQuad", ["easeOutQuad","easeOutQuad"]);
	
	
	// addOption("lightboxTransparent", "checkbox", "Lightbox transparrent",true);
	// addOption("lightboxPadding", "text", "Lightbox padding",0);
	// addOption("lightboxMargin", "text", "Lightbox margin",20);
	// addOption("lightboxWidth", "text", "Lightbox width",'75%');
	// addOption("lightboxHeight", "text", "Lightbox height",600);
	// addOption("lightboxMinWidth", "text", "Lightbox minimum widht",400);
	// addOption("lightboxMinHeight", "text", "Lightbox minimum height",100);
	// addOption("lightboxMaxWidth", "text", "Lightbox maximum widht",9999);
	// addOption("lightboxMaxHeight", "text", "Lightbox maximum height",9999);
	// addOption("lightboxAutoSize", "checkbox", "Lightbox auto size",true);
	// addOption("lightboxAutoHeight", "checkbox", "Lightbox auto height",false);
	// addOption("lightboxAutoWidth", "checkbox", "Lightbox auto width",false);
	
	addOption("general", "webgl", "checkbox", "webgl",true);
	addOption("webgl", "cameraDistance", "text", "Camera Distance",2300);
	addOption("webgl", "pan", "text", "Camera pan angle",0);
	addOption("webgl", "panMax", "text", "Camera pan angle max",20);
	addOption("webgl", "panMin", "text", "Camera pan angle min",-20);
	addOption("webgl", "tilt", "text", "Camera tilt angle",0);
	addOption("webgl", "tiltMax", "text", "Camera tilt angle max",0);
	addOption("webgl", "tiltMin", "text", "Camera tilt angle min",-60);
	
	addOption("webgl", "bookX", "text", "Book x",0);
	addOption("webgl", "bookY", "text", "Book y",0);
	addOption("webgl", "bookZ", "text", "Book z",0);
	
	addOption("webgl", "pageMaterial", "dropdown", "Page material","phong", ["phong","lambert","basic"]);
	// addOption("pageShadow", "checkbox", "Page shadow",false);
	addOption("webgl", "pageHardness", "text", "Page hardness",1);
	addOption("webgl", "coverHardness", "text", "Cover hardness",4);
	addOption("webgl", "pageSegmentsW", "text", "Page segments W",8);
	addOption("webgl", "pageSegmentsH", "text", "Page segments H",1);
	addOption("webgl", "pageShininess", "text", "Page shininess",20);
	addOption("webgl", "pageFlipDuration", "text", "Page flip duration",1.5);

	// addOption("pointLight", "checkbox", "Point light",false);
	// addOption("pointLightX", "text", "Point light x",0);
	// addOption("pointLightY", "text", "Point light y",0);
	// addOption("pointLightZ", "text", "Point light z",2000);
	// addOption("pointLightColor", "text", "Point light color",0xffffff);
	// addOption("pointLightIntensity", "text", "Point light intensity",0.1);
	
	// addOption("directionalLight", "checkbox", "Directional light",true);
	// addOption("directionalLightX", "text", "Directional light x",0);
	// addOption("directionalLightY", "text", "Directional light y",0);
	// addOption("directionalLightZ", "text", "Directional light z",1000);
	// addOption("directionalLightColor", "text", "Directional light color",0xffffff);
	// addOption("directionalLightIntensity", "text", "Directional light intensity",0.1);
	
	// addOption("ambientLight", "checkbox", "Ambient light",true);
	// addOption("ambientLightColor", "text", "Ambient light color",0xeeeeee);
	// addOption("ambientLightIntensity", "text", "Ambient light intensity",0.1);

	// addOption("spotLight", "checkbox", "Spotlight",false);
	// addOption("spotLightX", "text", "Spotlight x",0);
	// addOption("spotLightY", "text", "Spotlight y",0);
	// addOption("spotLightZ", "text", "Spotlight z",1000);
	// addOption("spotLightColor", "text", "Spotlight color",0xffffff);
	// addOption("spotLightIntensity", "text", "Spotlight intensity",0.05);
	// addOption("spotLightShadowCameraNear", "text", "Spotlight camera near",0.1);
	// addOption("spotLightShadowCameraFar", "text", "Spotlight camera far",1000);
	// addOption("spotLightCastShadow", "checkbox", "Spotlight cast shasows",false);
	// addOption("spotLightShadowDarkness", "text", "Spotlight shadow darkness",0.5);
	
	$('.postbox .hndle').click(function(e){
		$(this).parent().toggleClass("closed")
	});
	$('.postbox .handlediv').click(function(e){
		$(this).parent().toggleClass("closed")
	});
	
	
	$("#flipbook-general-options").find("select").change(function () {
        if(this.name == "mode"){
			if(this.value == "lightbox"){
				$("#flipbook-lightbox-options").closest('.postbox').show();
				$("#flipbook-normal-options").closest('.postbox').hide();
			}
			else{
				$("#flipbook-lightbox-options").closest('.postbox').hide();
				$("#flipbook-normal-options").closest('.postbox').show();
			}
		}
    });
	
	$("#flipbook-general-options").find("select").change();
	
	$("#flipbook-general-options").find("input:checkbox").change(function () {
        if(this.name == "webgl"){
			if(this.checked)
				$("#flipbook-webgl-options").closest('.postbox').show();
			else
				$("#flipbook-webgl-options").closest('.postbox').hide();
		}
    });
	$("#flipbook-general-options").find("input:checkbox").change();
	
	
	function addOption(section, name,type,desc,defaultValue,values){
		
		var table = $("#flipbook-"+section+"-options");
		var tableBody = table.find('tbody');
		var row = $('<tr valign="top"  class="field-row"></tr>').appendTo(tableBody);
		var th = $('<th scope="row">'+desc+'</th>').appendTo(row);
		var td = $('<td></td>').appendTo(row);
		
		// var list = $("#flipbook-options-list");
		// var li = $('<li />').appendTo(list);
		// var label = $('<label />').appendTo(li);
		// label.text(desc);
		
		switch(type){
			case "text":
				var input = $('<input type="text" name="'+name+'"/>').appendTo(td);
				if(options[name] && typeof(options[name]) != 'undefined'){
					input.attr("value",options[name]);
				}else if (options[    name.split("[")[0]   ] && name.indexOf("[") != -1 && typeof(options[    name.split("[")[0]   ]) != 'undefined' ){
					input.attr("value",options[    name.split("[")[0]   ][   name.split("[")[1].split("]")[0]   ]);
				}else {
					input.attr('value',defaultValue);
				}
				break;
			case "checkbox":
				var inputHidden = $('<input type="hidden" name="'+name+'" value="false"/>').appendTo(td);
				var input = $('<input type="checkbox" name="'+name+'" value="true"/>').appendTo(td);
				if(options[name] && typeof(options[name]) != 'undefined' ){
					input.attr("checked",options[name]);
				}else if (options[    name.split("[")[0]   ] && name.indexOf("[") != -1 && typeof(options[    name.split("[")[0]   ]) != 'undefined' ){
					input.attr("checked",options[    name.split("[")[0]   ][   name.split("[")[1].split("]")[0]   ]);
				
				}else {
					input.attr('checked',defaultValue);
				}
				break;
			case "selectImage":
				var input = $('<input type="text" name="'+name+'"/><a class="select-image-button button-secondary button80" href="#">Select file</a>').appendTo(td);
				if(typeof(options[name]) != 'undefined') {
					input.attr("value",options[name]);
				}else if (name.indexOf("[") != -1 && typeof(options[    name.split("[")[0]   ]) != 'undefined' ){
					input.attr("value",options[    name.split("[")[0]   ][   name.split("[")[1].split("]")[0]   ]);
				}else {
					input.attr('value',defaultValue);
				}
				break;
			case "selectFile":
				var input = $('<input type="text" name="'+name+'"/><a class="select-image-button button-secondary button80" href="#">Select file</a>').appendTo(td);
				if(typeof(options[name]) != 'undefined'){
					input.attr("value",options[name]);
				}else if (name.indexOf("[") != -1 && typeof(options[    name.split("[")[0]   ]) != 'undefined' ){
					input.attr("value",options[    name.split("[")[0]   ][   name.split("[")[1].split("]")[0]   ]);
				}else {
					input.attr('value',defaultValue);
				}
				break;
			case "dropdown":
				var select = $('<select name="'+name+'">').appendTo(td);
				for ( var i = 0; i < values.length; i++ ) {
					var option = $('<option name="'+name+'" value="'+values[i]+'">'+values[i]+'</option>').appendTo(select);
					if(typeof(options[name]) != 'undefined'){
						if(options[name] == values[i]){
							option.attr('selected','true');
						}
					}else if(defaultValue == values[i]){
						option.attr('selected','true');
					}
				}
				break;
			
		}
	
	}
	// flipbook-options
	
	//for all pages in  options.pages create page 
	for(var i= 0; i < options.pages.length; i++){
		var page = options.pages[i];
		var pagesContainer = $("#pages-container");
		var pageItem = createPageHtml("pages["+i+"]", i, page.title, page.src, page.thumb, page.htmlContent);
		pageItem.appendTo(pagesContainer);
		// pageItem.find('.add-link-button').click(function(e){
			// e.preventDefault();
			// var links = $(this).parent().find(".page-links");
			// var pageID = $(this).closest(".page").attr("id");
			// var linksCount = links.find(".page-link").length;		
			// var link = createLinkHtml("pages["+pageID+"][links]["+linksCount+"]", "", "", "", "", "page" ,"","","","","");
			// link.appendTo(links);
			// link.hide().fadeIn();
			// addListeners();
		// });
		//add links
		// if(page.links){
			// for(var j= 0; j < page.links.length; j++){
				// var linkObj = page.links[j]
				// var links = pageItem.find(".page-links");
				// var link = createLinkHtml("pages["+i+"][links]["+j+"]",
											// linkObj["x"],
											// linkObj["y"],
											// linkObj["width"],
											// linkObj["height"],
											// linkObj["target"],
											// linkObj["url"],
											// linkObj["color"],
											// linkObj["alpha"],
											// linkObj["hoverColor"],
											// linkObj["hoverAlpha"]
				// );
				// link.appendTo(links);
			
			
			// }
		// }
		
		
	}
	
	if(options.socialShare == null) options.socialShare = [];
	
	for(var i= 0; i < options.socialShare.length; i++){
		var share = options.socialShare[i];
		var shareContainer = $("#share-container");
		var shareItem = createShareHtml("socialShare["+i+"]", i, share.name, share.icon, share.url, share.target);
		shareItem.appendTo(shareContainer);
		
	}
	
	
	if(options.tableOfContent == null) options.tableOfContent = [];
	for(var i= 0; i < options.tableOfContent.length; i++){
		var toc = options.tableOfContent[i];
		var tocContainer = $("#toc-container");
		var tocItem = createTocHtml("tableOfContent["+i+"]", i, toc.title, toc.page);
		tocItem.appendTo(tocContainer);
		
	}
	
	$(".tabs").tabs();
	$(".ui-sortable").sortable();
	addListeners();
	
	
	$('#add-share-button').click(function (e) {
	
		e.preventDefault()
		
		var shareContainer = $("#share-container");
		var shareCount = shareContainer.find(".share").length;
		var shareItem = createShareHtml("socialShare["+shareCount+"]", "", "", "", "", "_blank");
		shareItem.appendTo(shareContainer);
		
		addListeners();
		$(".tabs").tabs();
	});
	
	
	
	$('#delete-all-pages-button').click(function (e) {
		//open editor to select one or multiple images and create pages from them
		e.preventDefault();
		
		$('.page').remove();
		
	});
	
	$('#add-new-page-button').click(function (e) {
	
		var pagesContainer = $("#pages-container");
		var pagesCount = pagesContainer.find(".page").length;
		var pageItem = createPageHtml("pages["+pagesCount+"]", "","", "","","");
		pageItem.appendTo(pagesContainer);
		pageItem.hide().fadeIn();
		$(".tabs").tabs();
		addListeners();
		//add page listeners
		pageItem.find('.add-link-button').click(function(e){
			e.preventDefault();
			var links = $(this).parent().find(".page-links");
			var pageID = $(this).closest(".page").attr("id");
			var linksCount = links.find(".page-link").length;		
			var link = createLinkHtml("pages["+pageID+"][links]["+linksCount+"]");
			link.appendTo(links);
			link.hide().fadeIn();
			addListeners();
		});
				
				
	
	});
	
	$('#add-pages-button').click(function (e) {
		//open editor to select one or multiple images and create pages from them
		e.preventDefault();

		
		
		var custom_uploader = wp.media({
			title: 'Select pages',
			button: {
				text: 'Select'
			},
			multiple: true  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var arr = custom_uploader.state().get('selection');
			var pages = new Array();
			for(var i=0;i<arr.models.length;i++){
				var url = arr.models[i].attributes.sizes.full.url;
				var thumb = (typeof(arr.models[i].attributes.sizes.medium) != "undefined") ? arr.models[i].attributes.sizes.medium.url : url;
				var title = arr.models[i].attributes.title;
				pages.push({title:title, url:url, thumb:thumb});
			}
			
			var pagesContainer = $("#pages-container");
		
			for(var i=0;i<pages.length;i++){
					
				var pagesCount = pagesContainer.find(".page").length;
				var pageItem = createPageHtml("pages["+pagesCount+"]", "",pages[i].title, pages[i].url,pages[i].thumb,"");
				pageItem.appendTo(pagesContainer);
				pageItem.hide().fadeIn();
				$(".tabs").tabs();
				addListeners();
				//add page listeners
				pageItem.find('.add-link-button').click(function(e){
					e.preventDefault();
					var links = $(this).parent().find(".page-links");
					var pageID = $(this).closest(".page").attr("id");
					var linksCount = links.find(".page-link").length;		
					var link = createLinkHtml("pages["+pageID+"][links]["+linksCount+"]");
					link.appendTo(links);
					link.hide().fadeIn();
					addListeners();
				});
			
			}
		
		
			//here create page for each url in urls
			
			// var attachment = custom_uploader.state().get('selection').first().toJSON();
			// $('.custom_media_image').attr('src', attachment.url);
			// $('.custom_media_url').val(attachment.url);
			// $('.custom_media_id').val(attachment.id);
		})
		.open();
		
		
			
	});
		
	function addListeners(){
		$('.submitdelete').click(function () {
			$(this).parent().parent().parent().animate({
				'opacity': 0
			}, 100).slideUp(100, function () {
					$(this).remove();
				});
		});
		
		// $('.select-image-button').click(function (e) {
			// e.preventDefault();
			// var imageURLInput = $(this).parent().find("input");
			// tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
			// $("#TB_window,#TB_overlay,#TB_HideSelect").one("unload", function (e) {
				// e.stopPropagation();
				// e.stopImmediatePropagation();
				// return false;
			// });
			
			// window.send_to_editor = function (html) {
				// var imgurl = jQuery('img',html).attr('src');
				// imageURLInput.val(imgurl);
				// tb_remove();
			// };
		// });	
		
		
		$('.select-image-button').click(function(e) {
			e.preventDefault();
			var imageURLInput = $(this).parent().find("input");
			var custom_uploader = wp.media({
				title: 'Select pages',
				button: {
					text: 'Select'
				},
				multiple: false  // Set this to true to allow multiple files to be selected
			})
			.on('select', function() {
				var arr = custom_uploader.state().get('selection');
				// var urls = new Array();
				// for(var i=0;i<arr.models.length;i++){
					// var url = arr.models[i].attributes.url;
					// urls.push(url);
				// }
				var url = arr.models[0].attributes.url;
				imageURLInput.val(url);
				// var attachment = custom_uploader.state().get('selection').first().toJSON();
				// $('.custom_media_image').attr('src', attachment.url);
				// $('.custom_media_url').val(attachment.url);
				// $('.custom_media_id').val(attachment.id);
			})
			.open();
		});



	}
			
	function createPageHtml(prefix,id,title,src,thumb,htmlContent) {
		htmlContent = stripslashes(htmlContent);
		return $('<div id="'+id+'"class="page">'
					+'<h4>Page '+id+'</h4>'
					
					+'<div class="tabs settings-area">'
					+'<img src="'+thumb+'"/>'
					+ '<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">'
						+ '<li><a href="#tabs-1">Title</a></li>'
						+ '<li><a href="#tabs-2">Image</a></li>'
						+ '<li><a href="#tabs-3">Thumbnail</a></li>'
						// + '<li><a href="#tabs-4">HTML Content</a></li>'
						// + '<li><a href="#tabs-5">Links</a></li>'
					+ '</ul>'
					+ '<div id="tabs-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom">'
					   + '<div class="field-row">'
						+ '<input id="page-title" name="'+prefix+'[title]" type="text" placeholder="Enter page title" value="'+title+'" />'
					   + '</div>'
					+ '</div>'
					+ '<div id="tabs-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom">'
						+ '<div class="field-row">'
							+ '<input id="image-path" name="'+prefix+'[src]" type="text" placeholder="Image URL" value="'+src+'" />'
							+ '<a class="select-image-button button-secondary button80" href="#">Select image</a> '
						+ '</div>'
					+ '</div>'
					+ '<div id="tabs-3" class="ui-tabs-panel ui-widget-content ui-corner-bottom">'
						+ '<div class="field-row">'
							+ '<input id="image-path" name="'+prefix+'[thumb]" type="text" placeholder="Thumbnail URL" value="'+thumb+'" />'
							+ '<a class="select-image-button button-secondary button80" href="#">Select image</a> '
						+ '</div>'
					+ '</div>'
					// + '<div id="tabs-4" class="ui-tabs-panel ui-widget-content ui-corner-bottom">'
							// + '<textarea id="html-content" name="'+prefix+'[htmlContent]" type="text" cols="50" rows="3" placeholder="Static HTML content">'+stripslashes(htmlContent)+'</textarea>'
					// + '</div>'
					// + '<div id="tabs-5" class="ui-tabs-panel ui-widget-content ui-corner-bottom">'
						// + '<div class="page-links">'
						// + '</div>'
						// + '<br />'
						// + '<a class="alignRight add-link-button button-secondary button80" href="#">Add New Link</a> '
					// + '</div>'
					// + '<div class="button-secondary submitbox deletediv"><a class="submitdelete deletion">Delete</a></div>'
					+ '<div class="submitbox deletediv"><span class="submitdelete deletion">x</span></div>'
					+ '</div>'
					+ '</div>'
				+ '</div>'
			);
	}
	
	function createShareHtml(prefix, id, name, icon, url, target){
	
		if (typeof(target) == 'undefined' || target != "_self") 
			target = "_blank";

		var markup = $('<div id="'+id+'"class="share">'
					+'<h4>Share button '+id+'</h4>'
					
					+'<div class="tabs settings-area">'
					+ '<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">'
						+ '<li><a href="#tabs-1">Icon name</a></li>'
						+ '<li><a href="#tabs-2">Icon css class</a></li>'
						+ '<li><a href="#tabs-3">Link</a></li>'
						+ '<li><a href="#tabs-4">Target</a></li>'
					+ '</ul>'
					+ '<div id="tabs-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom">'
					   + '<div class="field-row">'
						+ '<input id="page-title" name="'+prefix+'[name]" type="text" placeholder="Enter icon name" value="'+name+'" />'
					   + '</div>'
					+ '</div>'
					+ '<div id="tabs-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom">'
						+ '<div class="field-row">'
							+ '<input id="image-path" name="'+prefix+'[icon]" type="text" placeholder="Enter icon CSS class" value="'+icon+'" />'
						+ '</div>'
					+ '</div>'
					+ '<div id="tabs-3" class="ui-tabs-panel ui-widget-content ui-corner-bottom">'
						+ '<div class="field-row">'
							+ '<input id="image-path" name="'+prefix+'[url]" type="text" placeholder="Enter link" value="'+url+'" />'
						+ '</div>'
					+ '</div>'
					+ '<div id="tabs-4" class="ui-tabs-panel ui-widget-content ui-corner-bottom">'
						+ '<div class="field-row">'
							// + '<input id="image-path" name="'+prefix+'[target]" type="text" placeholder="Enter link" value="'+target+'" />'
							
							+ '<select id="social-share" name="'+prefix+'[target]">'
								// + '<option name="'+prefix+'[target]" value="_self">_self</option>'
								// + '<option name="'+prefix+'[target]" value="_blank">_blank</option>'
							+ '</select>'
							
							
						+ '</div>'
					+ '</div>'
					+ '<div class="submitbox deletediv"><span class="submitdelete deletion">x</span></div>'
					+ '</div>'
					+ '</div>'
				+ '</div>'
			);
			
			var values = ["_self", "_blank"];
			var select = markup.find('select');
		
			for ( var i = 0; i < values.length; i++ ) {
				var option = $('<option name="'+prefix+'[target]" value="'+values[i]+'">'+values[i]+'</option>').appendTo(select);
				if(typeof(options["socialShare"][id]) != 'undefined'){
					if(options["socialShare"][id]["target"] == values[i]){
						option.attr('selected','true');
					}
				}
			}
			
			return markup;
	}
	
	function createLinkHtml(prefix,x,y,width,height,target,url,color,alpha,hoverColor,hoverAlpha){
		var res = $('<div class="page-link link-options">'
					+ '<div class="inside">'
						+ '<div class="field-row">'
							+ '<label for="" data-help="">x</label>'
							+ '<input id="link-x" name="'+prefix+'[x]" type="text" placeholder="link x position" value="'+x+'"/>'
						+ '</div> '
						+ '<div class="field-row">'
							+ '<label for="" data-help="">y</label>'
							+ '<input id="link-y" name="'+prefix+'[y]" type="text" placeholder="link y position"  value="'+y+'"/>'
						+ '</div> '
						+ '<div class="field-row">'
							+ '<label for="" data-help="">width</label>'
							+ '<input id="link-width" name="'+prefix+'[width]" type="text" placeholder="link width" value="'+width+'"/>'
						+ '</div> '
						+ '<div class="field-row">'
							+ '<label for="" data-help="">height</label>'
							+ '<input id="link-height" name="'+prefix+'[height]" type="text" placeholder="link height" value="'+height+'"/>'
						+ '</div> '
						+ '<div class="field-row">'
							+ '<label for="" data-help="">color</label>'
							+ '<input id="link-color" name="'+prefix+'[color]" type="text" placeholder="link color" value="'+color+'"/>'
						+ '</div> '
						+ '<div class="field-row">'
							+ '<label for="" data-help="">alpha</label>'
							+ '<input id="link-alpha" name="'+prefix+'[alpha]" type="text" placeholder="link alpha" value="'+alpha+'"/>'
						+ '</div> '
						+ '<div class="field-row">'
							+ '<label for="" data-help="">hoverColor</label>'
							+ '<input id="link-hoverColor" name="'+prefix+'[hoverColor]" type="text" placeholder="link hoverColor" value="'+hoverColor+'"/>'
						+ '</div> '
						+ '<div class="field-row">'
							+ '<label for="" data-help="">hoverAlpha</label>'
							+ '<input id="link-hoverAlpha" name="'+prefix+'[hoverAlpha]" type="text" placeholder="link hoverAlpha" value="'+hoverAlpha+'"/>'
						+ '</div> '
						+ '<div class="field-row">'
							+ '<label for="" data-help="">href</label>'
							+ '<input id="link-url" name="'+prefix+'[url]" type="text" placeholder="page or URL" value="'+url+'"/>'
							+ '<select id="link-target" name="'+prefix+'[target]">'
								+ '<option class="page" value="page">page</option>'
								+ '<option class="_self" value="_self">_self</option>'
								+ '<option class="_blank" value="_blank">_blank</option>'
							+ '</select>'
						+ '</div>'
						+ '<div class="button-secondary submitbox deletediv"><a class="submitdelete deletion">Delete</a></div>'
						+ '<br />'
					+ '</div>'
				+ '</div>'
			);
			res.find("."+target).attr('selected','true');
			
			// switch(target){
				// case "page":
					// res.("'."+target+"'").attr('selected','true');
					// break;
				// case "_self":
					// break;
				// case "_blank":
					// break;
			// }

			return res;
	}	
	
	});
})(jQuery);

function stripslashes (str) {
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Ates Goral (http://magnetiq.com)
  // +      fixed by: Mick@el
  // +   improved by: marrtins
  // +   bugfixed by: Onno Marsman
  // +   improved by: rezna
  // +   input by: Rick Waldron
  // +   reimplemented by: Brett Zamir (http://brett-zamir.me)
  // +   input by: Brant Messenger (http://www.brantmessenger.com/)
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // *     example 1: stripslashes('Kevin\'s code');
  // *     returns 1: "Kevin's code"
  // *     example 2: stripslashes('Kevin\\\'s code');
  // *     returns 2: "Kevin\'s code"
  return (str + '').replace(/\\(.?)/g, function (s, n1) {
	switch (n1) {
	case '\\':
	  return '\\';
	case '0':
	  return '\u0000';
	case '':
	  return '';
	default:
	  return n1;
	}
  });
}