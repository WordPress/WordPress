(function($) {
	$(document).ready(function(){
	
		var books = $(".real3dflipbook");
		
		$.each(books, function(){
			// console.log(this)
			var id = $(this).attr('id')
			// var optionsName = 
			// var options = window["options"+id]
			var options = $(this).find("#options").html()
			
			var json_str = options.replace(/&quot;/g, '"');
			json_str = json_str.replace(/“/g, '"');
			json_str = json_str.replace(/”/g, '"');
			json_str = json_str.replace(/″/g, '"');
			json_str = json_str.replace(/„/g, '"');
			
			json_str = json_str.replace(/«&nbsp;/g, '"');
			json_str = json_str.replace(/&nbsp;»/g, '"');


			options = jQuery.parseJSON(json_str);
			options.assets = {
				preloader:options.rootFolder+"images/preloader.jpg",
				left:options.rootFolder+"images/left.png",
				overlay:options.rootFolder+"images/overlay.jpg",
				flipMp3:options.rootFolder+"mp3/turnPage.mp3"
			};
			
			options.social = [];
			
			// if(options.facebook != "")
				// options.social.push({name:"facebook", icon:"fa-facebook", url:options.facebook});
			// if(options.twitter != "")
				// options.social.push({name:"twitter", icon:"fa-twitter", url:options.twitter});
			// if(options.googleplus != "")
				// options.social.push({name:"googleplus", icon:"fa-google-plus", url:options.googleplus});
			// if(options.linkedin != "")
				// options.social.push({name:"linkedin", icon:"fa-linkedin", url:options.linkedin});
			// if(options.youtube != "")
				// options.social.push({name:"youtube", icon:"fa-youtube", url:options.youtube});

				
			if(typeof(options.btnShare) == 'undefined' || !options.btnShare) options.btnShare = {enabled:false}
			if(typeof(options.btnNext) == 'undefined' || !options.btnNext) options.btnNext = {enabled:false}
			if(typeof(options.btnPrev) == 'undefined' || !options.btnPrev) options.btnPrev = {enabled:false}
			if(typeof(options.btnZoomIn) == 'undefined' || !options.btnZoomIn) options.btnZoomIn = {enabled:false}
			if(typeof(options.btnZoomOut) == 'undefined' || !options.btnZoomOut) options.btnZoomOut = {enabled:false}
			if(typeof(options.btnToc) == 'undefined' || !options.btnToc) options.btnToc = {enabled:false}
			if(typeof(options.btnThumbs) == 'undefined' || !options.btnThumbs) options.btnThumbs = {enabled:false}
			if(typeof(options.btnDownloadPages) == 'undefined' || !options.btnDownloadPages) options.btnDownloadPages = {enabled:false}
			if(typeof(options.btnDownloadPdf) == 'undefined' || !options.btnDownloadPdf) options.btnDownloadPdf = {enabled:false}
			if(typeof(options.btnExpand) == 'undefined' || !options.btnExpand) options.btnExpand = {enabled:false}
			if(typeof(options.btnExpandLightbox) == 'undefined' || !options.btnExpandLightbox) options.btnExpandLightbox = {enabled:false}
			if(typeof(options.btnSound) == 'undefined' || !options.btnSound) options.btnSound = {enabled:false}
				
				
			options.btnShare.enabled = (options.social.length > 0)
			if(typeof(options.btnShare.icon) == 'undefined' || options.btnShare.icon == '') options.btnShare.icon = "fa-share";
			if(typeof(options.btnShare.title) == 'undefined' || options.btnShare.title == '') options.btnShare.title = "Share";
						
			if(typeof(options.btnNext.icon) == 'undefined' || options.btnNext.icon == '') options.btnNext.icon = "fa-chevron-right";
			if(typeof(options.btnNext.title) == 'undefined' || options.btnNext.title == '') options.btnNext.title = "Next page";
			
			if(typeof(options.btnPrev.icon) == 'undefined' || options.btnPrev.icon == '') options.btnPrev.icon = "fa-chevron-left";
			if(typeof(options.btnPrev.title) == 'undefined' || options.btnPrev.title == '') options.btnPrev.title = "Previous page";
			
			if(typeof(options.btnZoomIn.icon) == 'undefined' || options.btnZoomIn.icon == '') options.btnZoomIn.icon = "fa-plus";
			if(typeof(options.btnZoomIn.title) == 'undefined' || options.btnZoomIn.title == '') options.btnZoomIn.title = "Zoom in";
			
			if(typeof(options.btnZoomOut.icon) == 'undefined' || options.btnZoomOut.icon == '') options.btnZoomOut.icon = "fa-minus";
			if(typeof(options.btnZoomOut.title) == 'undefined' || options.btnZoomOut.title == '') options.btnZoomOut.title = "Zoom out";
			
			if(typeof(options.btnToc.icon) == 'undefined' || options.btnToc.icon == '') options.btnToc.icon = "fa-list-ol";
			if(typeof(options.btnToc.title) == 'undefined' || options.btnToc.title == '') options.btnToc.title = "Table of content";
			
			if(typeof(options.btnThumbs.icon) == 'undefined' || options.btnThumbs.icon == '') options.btnThumbs.icon = "fa-th-large";
			if(typeof(options.btnThumbs.title) == 'undefined' || options.btnThumbs.title == '') options.btnThumbs.title = "Pages";
			
			if(typeof(options.btnDownloadPages.icon) == 'undefined' || options.btnDownloadPages.icon == '') options.btnDownloadPages.icon = "fa-download";
			if(typeof(options.btnDownloadPages.title) == 'undefined' || options.btnDownloadPages.title == '') options.btnDownloadPages.title = "Download pages";
			// if(options.downloadPagesUrl)
				// options.btnDownloadPages.url = options.downloadPagesUrl;
			
			if(typeof(options.btnDownloadPdf.icon) == 'undefined' || options.btnDownloadPdf.icon == '') options.btnDownloadPdf.icon = "fa-file";
			if(typeof(options.btnDownloadPdf.title) == 'undefined' || options.btnDownloadPdf.title == '') options.btnDownloadPdf.title = "Download PDF";
			// if(options.downloadPdfUrl)
				// options.btnDownloadPdf.url = options.downloadPdfUrl;
			
			if(typeof(options.btnExpand.icon) == 'undefined' || options.btnExpand.icon == '') options.btnExpand.icon = "fa-expand";
			if(typeof(options.btnExpand.iconAlt) == 'undefined' || options.btnExpand.iconAlt == '') options.btnExpand.iconAlt = "fa-compress";
			if(typeof(options.btnExpand.title) == 'undefined' || options.btnExpand.title == '') options.btnExpand.title = "Toggle fullscreen";
			
			if(typeof(options.btnExpandLightbox.icon) == 'undefined' || options.btnExpandLightbox.icon == '') options.btnExpandLightbox.icon = "fa-expand";
			if(typeof(options.btnExpandLightbox.iconAlt) == 'undefined' || options.btnExpandLightbox.iconAlt == '') options.btnExpandLightbox.iconAlt = "fa-compress";
			if(typeof(options.btnExpandLightbox.title) == 'undefined' || options.btnExpandLightbox.title == '') options.btnExpandLightbox.title = "Toggle fullscreen";
			
			if(typeof(options.btnSound.icon) == 'undefined' || options.btnSound.icon == '') options.btnSound.icon = "fa-volume-up";
			if(typeof(options.btnSound.title) == 'undefined' || options.btnSound.title == '') options.btnSound.title = "Sound";
			
			if(options.btnDownloadPages.url){
				options.btnDownloadPages.url = options.btnDownloadPages.url.replace(/\\/g, '/')
				options.btnDownloadPages.enabled = true
			}else
				options.btnDownloadPages.enabled = false
				
			if(options.btnDownloadPdf.url){
				options.btnDownloadPdf.url = options.btnDownloadPdf.url.replace(/\\/g, '/')
				options.btnDownloadPdf.enabled = true
			}else
				options.btnDownloadPdf.enabled = false
			
			var bookContainer = $(this);
			
			switch(options.mode){
				case "normal":
					var containerClass = bookContainer.attr("class")
					var containerId = bookContainer.attr("id")
					
					bookContainer.removeClass(containerClass).addClass(containerClass+"-"+containerId)
					options.lightBox = false;
					bookContainer
						.css("position","relative")
						.css("display","block")
						.css("height",String(options.height)+"px")
					bookContainer.flipBook(options);
					
					
					if(options.fitToWindow){
						window.onresize = function(event) {
							fitToWindow()
						};
						function fitToWindow(){
							bookContainer.css("width",String($(window).width())+"px");
							bookContainer.css("height",String($(window).height())+"px");
						}
						fitToWindow();
					}else if(options.fitToParent){
						window.onresize = function(event) {
							fitToParent()
						};
						function fitToParent(){
							//find parent that has width & height != 0
							var parent = findParent(bookContainer);
							
							bookContainer.css("width",String(parent.width())+"px")
							bookContainer.css("height",String(parent.height())+"px")
							
							function findParent(elem){
								if(elem.parent().width() > 0 && elem.parent().height() > 0)
									return elem.parent()
								else
									return findParent(elem.parent())
							}
						}
						fitToParent();
					}else if(options.fitToHeight){
						window.onresize = function(event) {
							fitToHeight()
						};
						function fitToHeight(){
							if($(window).height() < options.height + bookContainer.offset().top)
								bookContainer.css("height",String($(window).height() - bookContainer.offset().top)+"px")
							else
								bookContainer.css("height",String(options.height)+"px")
						}
						fitToHeight();
					}
					
					break;
				case "lightbox":
					bookContainer
						.css("display","inline")
					options.lightBox = true;
					
					var containerClass = "real3dflipbook-" + bookContainer.attr("id")
					
					if(options.lightboxCssClass != ""){
						if($("."+options.lightboxCssClass).length > 0)
							$("."+options.lightboxCssClass).addClass(containerClass)
					}
					else if(options.lightboxThumbnailUrl != ""){
						var img = $('<img></img>').attr('src', options.lightboxThumbnailUrl )
						.addClass(containerClass)
						;
						bookContainer.before(img)
						bookContainer.remove();
					}else{
						var text =$('<a/>').text(options.lightboxText)
						.addClass(containerClass)
						;
						bookContainer.before(text)
						bookContainer.remove();
					}		

					$("."+containerClass).flipBook(options);
					break;
				case "fullscreen":
					options.lightBox = false;
					bookContainer
						.appendTo('body')
						.css("position","fixed")
						// .css("top","0")
						.css("bottom","0")
						.css("left","0")
						.css("right","0")
						.css('top', options.offsetTop+'px')
						;
					bookContainer.flipBook(options);
					break;
			}
	
		})
		
	});
}(jQuery));