/*
 Plugin: jQuery Parallax
 Version 1.1.3
 Author: Ian Lunn
 Twitter: @IanLunn
 Author URL: http://www.ianlunn.co.uk/
 Plugin URL: http://www.ianlunn.co.uk/plugins/jquery-parallax/

 (Contains lots of UpSolution's modifications)

 Dual licensed under the MIT and GPL licenses:
 http://www.opensource.org/licenses/mit-license.php
 http://www.gnu.org/licenses/gpl.html
 */

(function( $ ){
	var $window = $(window),
		windowHeight = $window.height();

	$.fn.parallax = function(xposParam){
		this.each(function(){
			var $container = $(this),
				$this = $container.children('.l-section-img, .l-titlebar-img'),
				speedFactor,
				offsetFactor = 0,
				getHeight,
				topOffset = 0,
				containerHeight = 0,
				containerWidth = 0,
			// Disable parallax on certain screen/img ratios
				disableParallax = false,
				parallaxIsDisabled = false,
			// Base image width and height (if can be achieved)
				baseImgHeight = 0,
				baseImgWidth = 0,
			// Backgroud-size cover? and current image size (counted)
				isBgCover = ($this.css('background-size') == 'cover'),
				originalBgPos = $this.css('background-position'),
				curImgHeight = 0,
				reversed = $container.hasClass('parallaxdir_reversed'),
				baseSpeedFactor = reversed ? -0.1 : 0.61,
				xpos,
				outerHeight = true;
			if ($this.length == 0) return;

			// setup defaults if arguments aren't specified
			if (xposParam === undefined) {
				xpos = "50%";
			} else {
				xpos = xposParam;
			}

			if ($container.hasClass('parallax_xpos_right')) {
				xpos = "100%";
			} else if ($container.hasClass('parallax_xpos_left')) {
				xpos = "0%";
			}

			if (outerHeight){
				getHeight = function(jqo){
					return jqo.outerHeight(true);
				};
			} else {
				getHeight = function(jqo){
					return jqo.height();
				};
			}

			// Count background image size
			function getBackgroundSize(callback){
				var img = new Image(),
				// here we will place image's width and height
					width, height,
				// here we get the size of the background and split it to array
					backgroundSize = ($this.css('background-size') || ' ').split(' '),
					backgroundWidthAttr = $this.attr('data-img-width'),
					backgroundHeightAttr = $this.attr('data-img-height');

				if (backgroundWidthAttr != '') width = parseInt(backgroundWidthAttr);
				if (backgroundHeightAttr != '') height = parseInt(backgroundHeightAttr);

				if (width !== undefined && height !== undefined){
					// Image is not needed
					return callback({ width: width, height: height });
				}

				// checking if width was set to pixel value
				if (/px/.test(backgroundSize[0])) width = parseInt(backgroundSize[0]);
				// checking if width was set to percent value
				if (/%/.test(backgroundSize[0])) width = $this.parent().width() * (parseInt(backgroundSize[0]) / 100);
				// checking if height was set to pixel value
				if (/px/.test(backgroundSize[1])) height = parseInt(backgroundSize[1]);
				// checking if height was set to percent value
				if (/%/.test(backgroundSize[1])) height = $this.parent().height() * (parseInt(backgroundSize[0]) / 100);

				if (width !== undefined && height !== undefined){
					// Image is not needed
					return callback({ width: width, height: height });
				}

				// Image is needed
				img.onload = function () {
					// check if width was set earlier, if not then set it now
					if (typeof width == 'undefined') width = this.width;
					// do the same with height
					if (typeof height == 'undefined') height = this.height;
					// call the callback
					callback({ width: width, height: height });
				};
				// extract image source from css using one, simple regex
				// src should be set AFTER onload handler
				img.src = ($this.css('background-image') || '').replace(/url\(['"]*(.*?)['"]*\)/g, '$1');
			}
			function update(){
				if (disableParallax){
					if ( ! parallaxIsDisabled){
						$this.css('backgroundPosition', originalBgPos);
						$container.usMod('parallax', 'fixed');
						parallaxIsDisabled = true;
					}
					return;
				}else{
					if (parallaxIsDisabled){
						$container.usMod('parallax', 'ver');
						parallaxIsDisabled = false;
					}
				}
				if (isNaN(speedFactor))
					return;

				var pos = $window.scrollTop();
				// Check if totally above or totally below viewport
				if ((topOffset + containerHeight < pos) || (pos < topOffset - windowHeight)) return;
				$this.css('backgroundPosition', xpos + " " + (offsetFactor + speedFactor * (topOffset - pos)) + "px");


			}
			function resize(){
				setTimeout(function(){
					windowHeight = $window.height();
					containerHeight = getHeight($this);
					containerWidth = $this.width();


					if ($window.width() <= $us.canvasOptions.disableEffectsWidth) {
						disableParallax = true;
					} else {
						disableParallax = false;
						if (isBgCover){
							if (baseImgWidth / baseImgHeight <= containerWidth / containerHeight){
								// Resizing by width
								curImgHeight = baseImgHeight * ($this.width() / baseImgWidth);
								disableParallax = false;
							}
							else {
								disableParallax = true;
							}
						}
					}



					// Improving speed factor to prevent showing image limits
					if (curImgHeight !== 0){
						if (baseSpeedFactor >= 0) {
							speedFactor = Math.min(baseSpeedFactor, curImgHeight / windowHeight);
							offsetFactor = Math.min(0, .5 * (windowHeight - curImgHeight - speedFactor * (windowHeight - containerHeight)));
						} else {
							speedFactor = Math.min(baseSpeedFactor, (windowHeight - containerHeight) / (windowHeight + containerHeight));
							offsetFactor = Math.max(0, speedFactor * containerHeight);
						}
					}else{
						speedFactor = baseSpeedFactor;
						offsetFactor = 0;
					}
					topOffset = $this.offset().top;
					update();
				}, 10);
			}

			getBackgroundSize(function(sz){
				curImgHeight = baseImgHeight = sz.height;
				baseImgWidth = sz.width;
				resize();
			});

			$window.bind({scroll: update, load: resize, resize: resize});
			resize();
		});
	};

	//$(function(){
		jQuery('.parallax_ver').parallax('50%');
	//});

})(jQuery);
