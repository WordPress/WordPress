/*!
 * Masonry v2 shim
 * to maintain backwards compatibility
 * as of Masonry v3.1.2
 *
 * Cascading grid layout library
 * http://masonry.desandro.com
 * MIT License
 * by David DeSandro
 */
!function(){"use strict";var t=window.Masonry;t.prototype._remapV2Options=function(){this._remapOption("gutterWidth","gutter"),this._remapOption("isResizable","isResizeBound"),this._remapOption("isRTL","isOriginLeft",function(t){return!t});var t=this.options.isAnimated;void 0!==t&&(this.options.transitionDuration=t?this.options.transitionDuration:0),void 0!==t&&!t||(t=(t=this.options.animationOptions)&&t.duration)&&(this.options.transitionDuration="string"==typeof t?t:t+"ms")},t.prototype._remapOption=function(t,o,i){t=this.options[t];void 0!==t&&(this.options[o]=i?i(t):t)};var o=t.prototype._create;t.prototype._create=function(){var t=this;this._remapV2Options(),o.apply(this,arguments),setTimeout(function(){jQuery(t.element).addClass("masonry")},0)};var i=t.prototype.layout;t.prototype.layout=function(){this._remapV2Options(),i.apply(this,arguments)};var n=t.prototype.option;t.prototype.option=function(){n.apply(this,arguments),this._remapV2Options()};var s=t.prototype._itemize;t.prototype._itemize=function(t){var o=s.apply(this,arguments);return jQuery(t).addClass("masonry-brick"),o};var e=t.prototype.measureColumns;t.prototype.measureColumns=function(){var t=this.options.columnWidth;t&&"function"==typeof t&&(this.getContainerWidth(),this.columnWidth=t(this.containerWidth)),e.apply(this,arguments)},t.prototype.reload=function(){this.reloadItems.apply(this,arguments),this.layout.apply(this)};var p=t.prototype.destroy;t.prototype.destroy=function(){var t=this.getItemElements();jQuery(this.element).removeClass("masonry"),jQuery(t).removeClass("masonry-brick"),p.apply(this,arguments)}}();