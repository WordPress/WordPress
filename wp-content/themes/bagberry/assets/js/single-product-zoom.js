"use strict";

(function ($) {
  'use strict';

  $(function () {
    var ProductGallery = function ProductGallery($target) {
      this.$target = $target;
      this.$images = $('.woocommerce-product-gallery__image', $target);
      $target.data('product_gallery', this);
      this.initZoom = this.initZoom.bind(this);
      this.initZoomForTarget = this.initZoomForTarget.bind(this);
      this.initZoom();
      $target.on('woocommerce_gallery_init_zoom', this.initZoom);
    };
    ProductGallery.prototype.initZoom = function () {
      for (var i = 0; i < this.$images.length; i++) {
        var image = this.$images[i];
        this.initZoomForTarget($(image));
      }
    };
    ProductGallery.prototype.initZoomForTarget = function (zoomTarget) {
      var galleryWidth = this.$target.width(),
        zoomEnabled = false;
      $(zoomTarget).each(function (index, target) {
        var image = $(target).find('img');
        if (image.data('large_image_width') > galleryWidth) {
          zoomEnabled = true;
          return false;
        }
      });
      if (zoomEnabled) {
        var zoom_options = $.extend({
          touch: false
        }, wc_single_product_params.zoom_options);
        if ('ontouchstart' in document.documentElement) {
          zoom_options.on = 'click';
        }
        zoomTarget.trigger('zoom.destroy');
        zoomTarget.zoom(zoom_options);
        setTimeout(function () {
          if (zoomTarget.find(':hover').length) {
            zoomTarget.trigger('mouseover');
          }
        }, 100);
      }
    };
    $('.woocommerce-product-gallery').each(function () {
      new ProductGallery($(this));
    });
  });
})(jQuery);