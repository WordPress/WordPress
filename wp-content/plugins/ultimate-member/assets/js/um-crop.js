(function (factory) {
  if (typeof define === "function" && define.amd) {
    // AMD. Register as anonymous module.
    define(["jquery"], factory);
  } else if (typeof exports === "object") {
    // Node / CommonJS
    factory(require("jquery"));
  } else {
    // Browser globals.
    factory(jQuery);
  }
})(function ($) {

  "use strict";

  var $window = $(window),
      $document = $(document),
      location = window.location,

      // Constants
      TRUE = true,
      FALSE = false,
      NULL = null,
      NAN = NaN,
      INFINITY = Infinity,
      STRING_UNDEFINED = "undefined",
      STRING_DIRECTIVE = "directive",
      CROPPER_NAMESPACE = ".cropper",

      // RegExps
      REGEXP_DIRECTIVES = /^(e|n|w|s|ne|nw|sw|se|all|crop|move|zoom)$/,
      REGEXP_OPTIONS = /^(x|y|width|height)$/,
      REGEXP_PROPERTIES = /^(naturalWidth|naturalHeight|width|height|aspectRatio|ratio|rotate)$/,

      // Classes
      CLASS_MODAL = "cropper-modal",
      CLASS_HIDDEN = "cropper-hidden",
      CLASS_INVISIBLE = "cropper-invisible",
      CLASS_MOVE = "cropper-move",
      CLASS_CROP = "cropper-crop",
      CLASS_DISABLED = "cropper-disabled",

      // Events
      EVENT_MOUSE_DOWN = "mousedown touchstart",
      EVENT_MOUSE_MOVE = "mousemove touchmove",
      EVENT_MOUSE_UP = "mouseup mouseleave touchend touchleave touchcancel",
      EVENT_WHEEL = "wheel mousewheel DOMMouseScroll",
      EVENT_RESIZE = "resize" + CROPPER_NAMESPACE, // Bind to window with namespace
      EVENT_DBLCLICK = "dblclick",
      EVENT_BUILD = "build" + CROPPER_NAMESPACE,
      EVENT_BUILT = "built" + CROPPER_NAMESPACE,
      EVENT_DRAG_START = "dragstart" + CROPPER_NAMESPACE,
      EVENT_DRAG_MOVE = "dragmove" + CROPPER_NAMESPACE,
      EVENT_DRAG_END = "dragend" + CROPPER_NAMESPACE,

      // Functions
      isNumber = function (n) {
        return typeof n === "number";
      },

      toArray = function (obj, offset) {
        var args = [];

        if (typeof offset === "number") { // It's necessary for IE8
          args.push(offset);
        }

        return args.slice.apply(obj, args);
      },

      // Custom proxy to avoid jQuery's guid
      proxy = function (fn, context) {
        var args = toArray(arguments, 2);

        return function () {
          return fn.apply(context, args.concat(toArray(arguments)));
        };
      },

      // Constructor
      Cropper = function (element, options) {
        this.element = element;
        this.$element = $(element);
        this.defaults = $.extend({}, Cropper.DEFAULTS, $.isPlainObject(options) ? options : {});
        this.$original = NULL;
        this.ready = FALSE;
        this.built = FALSE;
        this.cropped = FALSE;
        this.rotated = FALSE;
        this.disabled = FALSE;
        this.replaced = FALSE;
        this.init();
      },

      // Others
      sqrt = Math.sqrt,
      min = Math.min,
      max = Math.max,
      abs = Math.abs,
      sin = Math.sin,
      cos = Math.cos,
      num = parseFloat;

  Cropper.prototype = {
    constructor: Cropper,

    support: {
      canvas: $.isFunction($("<canvas>")[0].getContext)
    },

    init: function () {
      var defaults = this.defaults;

      $.each(defaults, function (i, n) {
        switch (i) {
          case "aspectRatio":
            defaults[i] = abs(num(n)) || NAN; // 0 -> NaN
            break;

          case "autoCropArea":
            defaults[i] = abs(num(n)) || 0.8; // 0 | NaN -> 0.8
            break;

          case "minWidth":
          case "minHeight":
            defaults[i] = abs(num(n)) || 0; // NaN -> 0
            break;

          case "maxWidth":
          case "maxHeight":
            defaults[i] = abs(num(n)) || INFINITY; // 0 | NaN -> Infinity
            break;

          // No default
        }
      });

      // Set default image data
      this.image = {
        rotate: 0
      };

      this.load();
    },

    load: function () {
      var _this = this,
          $this = this.$element,
          element = this.element,
          image = this.image,
          crossOrigin = "",
          $clone,
          url;

      if ($this.is("img")) {
        url = $this.prop("src");
      } else if ($this.is("canvas") && this.support.canvas) {
        url = element.toDataURL();
      }

      if (!url) {
        return;
      }

      // Reset image rotate degree
      if (this.replaced) {
        image.rotate = 0;
      }

      if (this.defaults.checkImageOrigin) {
        if ($this.prop("crossOrigin") || this.isCrossOriginURL(url)) {
          crossOrigin = " crossOrigin";
        }
      }

      this.$clone = ($clone = $("<img" + crossOrigin + ' src="' + url + '">'));

      $clone.one("load", function () {
        image.naturalWidth = this.naturalWidth || $clone.width();
        image.naturalHeight = this.naturalHeight || $clone.height();
        image.aspectRatio = image.naturalWidth / image.naturalHeight;

        _this.url = url;
        _this.ready = TRUE;
        _this.build();
      });

      // Hide and prepend the clone iamge to the document body (Don't append to).
      $clone.addClass(CLASS_INVISIBLE).prependTo("body");
    },

    isCrossOriginURL: function (url) {
      var parts = url.match(/^(https?:)\/\/([^\:\/\?#]+):?(\d*)/i);

      if ((parts && (parts[1] !== location.protocol || parts[2] !== location.hostname || parts[3] !== location.port))) {
        return TRUE;
      }

      return FALSE;
    },

    build: function () {
      var $this = this.$element,
          defaults = this.defaults,
          buildEvent,
          $cropper;

      if (!this.ready) {
        return;
      }

      if (this.built) {
        this.unbuild();
      }

      $this.one(EVENT_BUILD, defaults.build); // Only trigger once
      buildEvent = $.Event(EVENT_BUILD);
      $this.trigger(buildEvent);

      if (buildEvent.isDefaultPrevented()) {
        return;
      }

      // Create cropper elements
      this.$cropper = ($cropper = $(Cropper.TEMPLATE));

      // Hide the original image
      $this.addClass(CLASS_HIDDEN);

      // Show and prepend the clone iamge to the cropper
      this.$clone.removeClass(CLASS_INVISIBLE).prependTo($cropper);

      // Save original image for rotation
      if (!this.rotated) {
        this.$original = this.$clone.clone();

        // Append the image to document to avoid "NS_ERROR_NOT_AVAILABLE" error on Firefox when call the "drawImage" method.
        this.$original.addClass(CLASS_HIDDEN).prependTo(this.$cropper);

        this.originalImage = $.extend({}, this.image);
      }

      this.$container = $this.parent();
      this.$container.append($cropper);

      this.$canvas = $cropper.find(".cropper-canvas");
      this.$dragger = $cropper.find(".cropper-dragger");
      this.$viewer = $cropper.find(".cropper-viewer");

      defaults.autoCrop ? (this.cropped = TRUE) : this.$dragger.addClass(CLASS_HIDDEN);
      defaults.dragCrop && this.setDragMode("crop");
      defaults.modal && this.$canvas.addClass(CLASS_MODAL);
      !defaults.dashed && this.$dragger.find(".cropper-dashed").addClass(CLASS_HIDDEN);
      !defaults.movable && this.$dragger.find(".cropper-face").data(STRING_DIRECTIVE, "move");
      !defaults.resizable && this.$dragger.find(".cropper-line, .cropper-point").addClass(CLASS_HIDDEN);

      this.addListeners();
      this.initPreview();

      this.built = TRUE; // Set `true` before update
      this.update();
      this.replaced = FALSE; // Reset to `false` after update

      $this.one(EVENT_BUILT, defaults.built); // Only trigger once
      $this.trigger(EVENT_BUILT);
    },

    unbuild: function () {
      if (!this.built) {
        return;
      }

      this.built = FALSE;
      this.removeListeners();

      this.$preview.empty();
      this.$preview = NULL;

      this.$dragger = NULL;
      this.$canvas = NULL;
      this.$container = NULL;

      this.$cropper.remove();
      this.$cropper = NULL;
    },

    update: function (data) {
      this.initContainer();
      this.initCropper();
      this.initImage();
      this.initDragger();

      if (data) {
        this.setData(data, TRUE);
        this.setDragMode("crop");
      } else {
        this.setData(this.defaults.data);
      }
    },

    resize: function () {
      clearTimeout(this.resizing);
      this.resizing = setTimeout($.proxy(this.update, this, this.getData()), 200);
    },

    preview: function () {
      var image = this.image,
          dragger = this.dragger,
          width = image.width,
          height = image.height,
          left = dragger.left - image.left,
          top = dragger.top - image.top;

      this.$viewer.find("img").css({
        width: width,
        height: height,
        marginLeft: -left,
        marginTop: -top
      });

      this.$preview.each(function () {
        var $this = $(this),
            ratio = $this.width() / dragger.width;

        $this.find("img").css({
          width: width * ratio,
          height: height * ratio,
          marginLeft: -left * ratio,
          marginTop: -top * ratio
        });
      });
    },

    addListeners: function () {
      var defaults = this.defaults;

      this.$element.on(EVENT_DRAG_START, defaults.dragstart).on(EVENT_DRAG_MOVE, defaults.dragmove).on(EVENT_DRAG_END, defaults.dragend);
      this.$cropper.on(EVENT_MOUSE_DOWN, $.proxy(this.dragstart, this)).on(EVENT_DBLCLICK, $.proxy(this.dblclick, this));

      if (defaults.zoomable) {
        this.$cropper.on(EVENT_WHEEL, $.proxy(this.wheel, this));
      }

      if (defaults.multiple) {
        this.$cropper.on(EVENT_MOUSE_MOVE, $.proxy(this.dragmove, this)).on(EVENT_MOUSE_UP, $.proxy(this.dragend, this));
      } else {
        $document.on(EVENT_MOUSE_MOVE, (this._dragmove = proxy(this.dragmove, this))).on(EVENT_MOUSE_UP, (this._dragend = proxy(this.dragend, this)));
      }

      $window.on(EVENT_RESIZE, (this._resize = proxy(this.resize, this)));
    },

    removeListeners: function () {
      var defaults = this.defaults;

      this.$element.off(EVENT_DRAG_START, defaults.dragstart).off(EVENT_DRAG_MOVE, defaults.dragmove).off(EVENT_DRAG_END, defaults.dragend);
      this.$cropper.off(EVENT_MOUSE_DOWN, this.dragstart).off(EVENT_DBLCLICK, this.dblclick);

      if (defaults.zoomable) {
        this.$cropper.off(EVENT_WHEEL, this.wheel);
      }

      if (defaults.multiple) {
        this.$cropper.off(EVENT_MOUSE_MOVE, this.dragmove).off(EVENT_MOUSE_UP, this.dragend);
      } else {
        $document.off(EVENT_MOUSE_MOVE, this._dragmove).off(EVENT_MOUSE_UP, this._dragend);
      }

      $window.off(EVENT_RESIZE, this._resize);
    },

    initPreview: function () {
      var img = '<img src="' + this.url + '">';

      this.$preview = $(this.defaults.preview);
      this.$viewer.html(img);
      this.$preview.html(img).find("img").css("cssText", "min-width:0!important;min-height:0!important;max-width:none!important;max-height:none!important;");
    },

    initContainer: function () {
      var $container = this.$container;
		if($container !== NULL){
		  this.container = {
			width: max($container.width(), 300),
			height: max($container.height(), 150)
		  };
		}
    },

    initCropper: function () {
      var container = this.container,
          image = this.image,
          cropper;

      if (((image.naturalWidth * container.height / image.naturalHeight) - container.width) >= 0) {
        cropper = {
          width: container.width,
          height: container.width / image.aspectRatio,
          left: 0
        };

        cropper.top = (container.height - cropper.height) / 2;
      } else {
        cropper = {
          width: container.height * image.aspectRatio,
          height: container.height,
          top: 0
        };

        cropper.left = (container.width - cropper.width) / 2;
      }

      this.$cropper.css({
        width: cropper.width,
        height: cropper.height,
        left: cropper.left,
        top: cropper.top
      });

      this.cropper = cropper;
    },

    initImage: function () {
      var image = this.image,
          cropper = this.cropper,
          defaultImage = {
            _width: cropper.width,
            _height: cropper.height,
            width: cropper.width,
            height: cropper.height,
            left: 0,
            top: 0,
            ratio: cropper.width / image.naturalWidth
          };

      this.defaultImage = $.extend({}, image, defaultImage);

      if (image._width !== cropper.width || image._height !== cropper.height) {
        $.extend(image, defaultImage);
      } else {
        image = $.extend({}, defaultImage, image);

        // Reset image ratio
        if (this.replaced) {
          image.ratio = defaultImage.ratio;
        }
      }

      this.image = image;
      this.renderImage();
    },

    renderImage: function (mode) {
      var image = this.image;

      if (mode === "zoom") {
        image.left -= (image.width - image.oldWidth) / 2;
        image.top -= (image.height - image.oldHeight) / 2;
      }

      image.left = min(max(image.left, image._width - image.width), 0);
      image.top = min(max(image.top, image._height - image.height), 0);

      this.$clone.css({
        width: image.width,
        height: image.height,
        marginLeft: image.left,
        marginTop: image.top
      });

      if (mode) {
        this.defaults.done(this.getData());
        this.preview();
      }
    },

    initDragger: function () {
      var defaults = this.defaults,
          cropper = this.cropper,
          // If not set, use the original aspect ratio of the image.
          aspectRatio = defaults.aspectRatio || this.image.aspectRatio,
          ratio = this.image.ratio,
          dragger;

      if (((cropper.height * aspectRatio) - cropper.width) >= 0) {
        dragger = {
          height: cropper.width / aspectRatio,
          width: cropper.width,
          left: 0,
          top: (cropper.height - (cropper.width / aspectRatio)) / 2,
          maxWidth: cropper.width,
          maxHeight: cropper.width / aspectRatio
        };
      } else {
        dragger = {
          height: cropper.height,
          width: cropper.height * aspectRatio,
          left: (cropper.width - (cropper.height * aspectRatio)) / 2,
          top: 0,
          maxWidth: cropper.height * aspectRatio,
          maxHeight: cropper.height
        };
      }

      dragger.minWidth = 0;
      dragger.minHeight = 0;

      if (defaults.aspectRatio) {
        if (isFinite(defaults.maxWidth)) {
          dragger.maxWidth = min(dragger.maxWidth, defaults.maxWidth * ratio);
          dragger.maxHeight = dragger.maxWidth / aspectRatio;
        } else if (isFinite(defaults.maxHeight)) {
          dragger.maxHeight = min(dragger.maxHeight, defaults.maxHeight * ratio);
          dragger.maxWidth = dragger.maxHeight * aspectRatio;
        }

        if (defaults.minWidth > 0) {
          dragger.minWidth = max(0, defaults.minWidth * ratio);
          dragger.minHeight = dragger.minWidth / aspectRatio;
        } else if (defaults.minHeight > 0) {
          dragger.minHeight = max(0, defaults.minHeight * ratio);
          dragger.minWidth = dragger.minHeight * aspectRatio;
        }
      } else {
        dragger.maxWidth = min(dragger.maxWidth, defaults.maxWidth * ratio);
        dragger.maxHeight = min(dragger.maxHeight, defaults.maxHeight * ratio);
        dragger.minWidth = max(0, defaults.minWidth * ratio);
        dragger.minHeight = max(0, defaults.minHeight * ratio);
      }

      // minWidth can't be greater than maxWidth, and minHeight too.
      dragger.minWidth = min(dragger.maxWidth, dragger.minWidth);
      dragger.minHeight = min(dragger.maxHeight, dragger.minHeight);

      // Center the dragger by default
      dragger.height *= defaults.autoCropArea;
      dragger.width *= defaults.autoCropArea;
      dragger.left = (cropper.width - dragger.width) / 2;
      dragger.top = (cropper.height - dragger.height) / 2;
      dragger.oldLeft = dragger.left;
      dragger.oldTop = dragger.top;

      this.defaultDragger = dragger;
      this.dragger = $.extend({}, dragger);
    },

    renderDragger: function () {
      var dragger = this.dragger,
          cropper = this.cropper;

      if (dragger.width > dragger.maxWidth) {
        dragger.width = dragger.maxWidth;
        dragger.left = dragger.oldLeft;
      } else if (dragger.width < dragger.minWidth) {
        dragger.width = dragger.minWidth;
        dragger.left = dragger.oldLeft;
      }

      if (dragger.height > dragger.maxHeight) {
        dragger.height = dragger.maxHeight;
        dragger.top = dragger.oldTop;
      } else if (dragger.height < dragger.minHeight) {
        dragger.height = dragger.minHeight;
        dragger.top = dragger.oldTop;
      }

      dragger.left = min(max(dragger.left, 0), cropper.width - dragger.width);
      dragger.top = min(max(dragger.top, 0), cropper.height - dragger.height);
      dragger.oldLeft = dragger.left;
      dragger.oldTop = dragger.top;

      // Re-render the dragger
      this.dragger = dragger;

      if (!this.disabled) {
        this.defaults.done(this.getData());
      }

      this.$dragger.css({
        width: dragger.width,
        height: dragger.height,
        left: dragger.left,
        top: dragger.top
      });

      this.preview();
    },

    reset: function (deep) {
      if (!this.cropped) {
        return;
      }

      if (deep) {
        this.defaults.data = {};
      }

      this.image = $.extend({}, this.defaultImage);
      this.renderImage();
      this.dragger = $.extend({}, this.defaultDragger);
      this.setData(this.defaults.data);
    },

    clear: function () {
      if (!this.cropped) {
        return;
      }

      this.cropped = FALSE;

      this.setData({
        x: 0,
        y: 0,
        width: 0,
        height: 0
      });

      this.$canvas.removeClass(CLASS_MODAL);
      this.$dragger.addClass(CLASS_HIDDEN);
    },

    destroy: function () {
      var $this = this.$element;

      if (!this.ready) {
        return;
      }

      this.unbuild();
      $this.removeClass(CLASS_HIDDEN).removeData("cropper");

      if (this.rotated) {
        $this.attr("src", this.$original.attr("src"));
      }
    },

    replace: function (url, /*INTERNAL*/ rotated) {
      var _this = this,
          $this = this.$element,
          element = this.element,
          context;

      if (url && url !== this.url && url !== $this.attr("src")) {
        if (!rotated) {
          this.rotated = FALSE;
          this.replaced = TRUE;
        }

        if ($this.is("img")) {
          $this.attr("src", url);
          this.load();
        } else if ($this.is("canvas") && this.support.canvas) {
          context = element.getContext("2d");

          $('<img src="' + url + '">').one("load", function () {
            element.width = this.width;
            element.height = this.height;
            context.clearRect(0, 0, element.width, element.height);
            context.drawImage(this, 0, 0);
            _this.load();
          });
        }
      }
    },

    setData: function (data, /*INTERNAL*/ once) {
      var cropper = this.cropper,
          dragger = this.dragger,
          image = this.image,
          aspectRatio = this.defaults.aspectRatio;

      if (!this.built || typeof data === STRING_UNDEFINED) {
        return;
      }

      if (data === NULL || $.isEmptyObject(data)) {
        dragger = $.extend({}, this.defaultDragger);
      }

      if ($.isPlainObject(data) && !$.isEmptyObject(data)) {

        if (!once) {
          this.defaults.data = data;
        }

        data = this.transformData(data);

        if (isNumber(data.x) && data.x <= cropper.width - image.left) {
          dragger.left = data.x + image.left;
        }

        if (isNumber(data.y) && data.y <= cropper.height - image.top) {
          dragger.top = data.y + image.top;
        }

        if (aspectRatio) {
          if (isNumber(data.width) && data.width <= dragger.maxWidth && data.width >= dragger.minWidth) {
            dragger.width = data.width;
            dragger.height = dragger.width / aspectRatio;
          } else if (isNumber(data.height) && data.height <= dragger.maxHeight && data.height >= dragger.minHeight) {
            dragger.height = data.height;
            dragger.width = dragger.height * aspectRatio;
          }
        } else {
          if (isNumber(data.width) && data.width <= dragger.maxWidth && data.width >= dragger.minWidth) {
            dragger.width = data.width;
          }

          if (isNumber(data.height) && data.height <= dragger.maxHeight && data.height >= dragger.minHeight) {
            dragger.height = data.height;
          }
        }
      }

      this.dragger = dragger;
      this.renderDragger();
    },

    getData: function (rounded) {
      var dragger = this.dragger,
          image = this.image,
          data = {};

      if (this.built) {
        data = {
          x: dragger.left - image.left,
          y: dragger.top - image.top,
          width: dragger.width,
          height: dragger.height
        };

        data = this.transformData(data, TRUE, rounded);
      }

      return data;
    },

    transformData: function (data, reversed, rounded) {
      var ratio = this.image.ratio,
          result = {};

      $.each(data, function (i, n) {
        n = num(n);

        if (REGEXP_OPTIONS.test(i) && !isNaN(n)) {
          result[i] = reversed ? (rounded ? Math.round(n / ratio) : n / ratio) : n * ratio;
        }
      });

      return result;
    },

    setAspectRatio: function (aspectRatio) {
      var freeRatio = aspectRatio === "auto";

      aspectRatio = num(aspectRatio);

      if (freeRatio || (!isNaN(aspectRatio) && aspectRatio > 0)) {
        this.defaults.aspectRatio = freeRatio ? NAN : aspectRatio;

        if (this.built) {
          this.initDragger();
          this.renderDragger();
        }
      }
    },

    getImageData: function () {
      var data = {};

      if (this.ready) {
        $.each(this.image, function (name, value) {
          if (REGEXP_PROPERTIES.test(name)) {
            data[name] = value;
          }
        });
      }

      return data;
    },

    getDataURL: function (options, type, quality) {
      var canvas = $("<canvas>")[0],
          data = this.getData(),
          dataURL = "",
          context;

      if (!$.isPlainObject(options)) {
        quality = type;
        type = options;
        options = {};
      }

      options = $.extend({
        width: data.width,
        height: data.height
      }, options);

      if (this.cropped && this.support.canvas) {
        canvas.width = options.width;
        canvas.height = options.height;
        context = canvas.getContext("2d");

        if (type === "image/jpeg") {
          context.fillStyle = "#fff";
          context.fillRect(0, 0, options.width, options.height);
        }

        context.drawImage(this.$clone[0], data.x, data.y, data.width, data.height, 0, 0, options.width, options.height);
        dataURL = canvas.toDataURL(type, quality);
      }

      return dataURL;
    },

    setDragMode: function (mode) {
      var $canvas = this.$canvas,
          defaults = this.defaults,
          cropable = FALSE,
          movable = FALSE;

      if (!this.built || this.disabled) {
        return;
      }

      switch (mode) {
        case "crop":
          if (defaults.dragCrop) {
            cropable = TRUE;
            $canvas.data(STRING_DIRECTIVE, mode);
          }

          break;

        case "move":
          movable = TRUE;
          $canvas.data(STRING_DIRECTIVE, mode);

          break;

        default:
          $canvas.removeData(STRING_DIRECTIVE);
      }

      $canvas.toggleClass(CLASS_CROP, cropable).toggleClass(CLASS_MOVE, movable);
    },

    enable: function () {
      if (this.built) {
        this.disabled = FALSE;
        this.$cropper.removeClass(CLASS_DISABLED);
      }
    },

    disable: function () {
      if (this.built) {
        this.disabled = TRUE;
        this.$cropper.addClass(CLASS_DISABLED);
      }
    },

    rotate: function (degree) {
      var image = this.image;

      degree = num(degree) || 0;

      if (!this.built || degree === 0 || this.disabled || !this.defaults.rotatable || !this.support.canvas) {
        return;
      }

      this.rotated = TRUE;
      degree = (image.rotate = (image.rotate + degree) % 360);

       // replace with "true" to prevent to override the original image
      this.replace(this.getRotatedDataURL(degree), true);
    },

    getRotatedDataURL: function (degree) {
      var canvas = $("<canvas>")[0],
          context = canvas.getContext("2d"),
          arc = degree * Math.PI / 180,
          deg = abs(degree) % 180,
          acuteAngle = deg > 90 ? (180 - deg) : deg,
          acuteAngleArc = acuteAngle * Math.PI / 180,
          originalImage = this.originalImage,
          naturalWidth = originalImage.naturalWidth,
          naturalHeight = originalImage.naturalHeight,
          width = abs(naturalWidth * cos(acuteAngleArc) + naturalHeight * sin(acuteAngleArc)),
          height = abs(naturalWidth * sin(acuteAngleArc) + naturalHeight * cos(acuteAngleArc));

      canvas.width = width;
      canvas.height = height;
      context.save();
      context.translate(width / 2, height / 2);
      context.rotate(arc);
      context.drawImage(this.$original[0], -naturalWidth / 2, -naturalHeight / 2, naturalWidth, naturalHeight);
      context.restore();

      return canvas.toDataURL();
    },

    zoom: function (delta) {
      var image = this.image,
          width,
          height,
          range;

      delta = num(delta);

      if (!this.built || !delta || this.disabled || !this.defaults.zoomable) {
        return;
      }

      width = image.width * (1 + delta);
      height = image.height * (1 + delta);
      range = width / image._width;

      if (range > 10) {
        return;
      }

      if (range < 1) {
        width = image._width;
        height = image._height;
      }

      if (range <= 1) {
        this.setDragMode("crop");
      } else {
        this.setDragMode("move");
      }

      image.oldWidth = image.width;
      image.oldHeight = image.height;

      image.width = width;
      image.height = height;
      image.ratio = image.width / image.naturalWidth;

      this.renderImage("zoom");
    },

    dblclick: function () {
      if (this.disabled) {
        return;
      }

      if (this.$canvas.hasClass(CLASS_CROP)) {
        this.setDragMode("move");
      } else {
        this.setDragMode("crop");
      }
    },

    wheel: function (event) {
      var e = event.originalEvent,
          msDeltaY = 117.25, // IE
          mozDelatY = 5, // Firefox
          webkitDelatY = 166.66665649414062, // Chrome, Opera
          zoomDelta = 0.1, // 10%
          delta;

      if (this.disabled) {
        return;
      }

      event.preventDefault();

      if (e.deltaY) {
        delta = e.deltaY;
        delta = delta % mozDelatY === 0 ? delta / mozDelatY : delta % msDeltaY === 0 ? delta / msDeltaY : delta / webkitDelatY;
      } else {
        delta = e.wheelDelta ? -e.wheelDelta / 120 : (e.detail ? e.detail / 3 : 0);
      }

      this.zoom(delta * zoomDelta);
    },

    dragstart: function (event) {
      var touches = event.originalEvent.touches,
          e = event,
          directive,
          dragStartEvent,
          touchesLength;

      if (this.disabled) {
        return;
      }

      if (touches) {
        touchesLength = touches.length;

        if (touchesLength > 1) {
          if (this.defaults.zoomable && touchesLength === 2) {
            e = touches[1];
            this.startX2 = e.pageX;
            this.startY2 = e.pageY;
            directive = "zoom";
          } else {
            return;
          }
        }

        e = touches[0];
      }

      directive = directive || $(e.target).data(STRING_DIRECTIVE);

      if (REGEXP_DIRECTIVES.test(directive)) {
        event.preventDefault();

        dragStartEvent = $.Event(EVENT_DRAG_START);
        this.$element.trigger(dragStartEvent);

        if (dragStartEvent.isDefaultPrevented()) {
          return;
        }

        this.directive = directive;
        this.cropping = FALSE;
        this.startX = e.pageX;
        this.startY = e.pageY;

        if (directive === "crop") {
          this.cropping = TRUE;
          this.$canvas.addClass(CLASS_MODAL);
        }
      }
    },

    dragmove: function (event) {
      var touches = event.originalEvent.touches,
          e = event,
          dragMoveEvent,
          touchesLength;

      if (this.disabled) {
        return;
      }

      if (touches) {
        touchesLength = touches.length;

        if (touchesLength > 1) {
          if (this.defaults.zoomable && touchesLength === 2) {
            e = touches[1];
            this.endX2 = e.pageX;
            this.endY2 = e.pageY;
          } else {
            return;
          }
        }

        e = touches[0];
      }

      if (this.directive) {
        event.preventDefault();

        dragMoveEvent = $.Event(EVENT_DRAG_MOVE);
        this.$element.trigger(dragMoveEvent);

        if (dragMoveEvent.isDefaultPrevented()) {
          return;
        }

        this.endX = e.pageX;
        this.endY = e.pageY;

        this.dragging();
      }
    },

    dragend: function (event) {
      var dragEndEvent;

      if (this.disabled) {
        return;
      }

      if (this.directive) {
        event.preventDefault();

        dragEndEvent = $.Event(EVENT_DRAG_END);
        this.$element.trigger(dragEndEvent);

        if (dragEndEvent.isDefaultPrevented()) {
          return;
        }

        if (this.cropping) {
          this.cropping = FALSE;
          this.$canvas.toggleClass(CLASS_MODAL, this.cropped && this.defaults.modal);
        }

        this.directive = "";
      }
    },

    dragging: function () {
      var directive = this.directive,
          image = this.image,
          cropper = this.cropper,
          maxWidth = cropper.width,
          maxHeight = cropper.height,
          dragger = this.dragger,
          width = dragger.width,
          height = dragger.height,
          left = dragger.left,
          top = dragger.top,
          right = left + width,
          bottom = top + height,
          renderable = TRUE,
          defaults = this.defaults,
          aspectRatio = defaults.aspectRatio,
          range = {
            x: this.endX - this.startX,
            y: this.endY - this.startY
          },
          offset;

      if (aspectRatio) {
        range.X = range.y * aspectRatio;
        range.Y = range.x / aspectRatio;
      }

      switch (directive) {
        // Move dragger
        case "all":
          left += range.x;
          top += range.y;

          break;

        // Resize dragger
        case "e":
          if (range.x >= 0 && (right >= maxWidth || aspectRatio && (top <= 0 || bottom >= maxHeight))) {
            renderable = FALSE;
            break;
          }

          width += range.x;

          if (aspectRatio) {
            height = width / aspectRatio;
            top -= range.Y / 2;
          }

          if (width < 0) {
            directive = "w";
            width = 0;
          }

          break;

        case "n":
          if (range.y <= 0 && (top <= 0 || aspectRatio && (left <= 0 || right >= maxWidth))) {
            renderable = FALSE;
            break;
          }

          height -= range.y;
          top += range.y;

          if (aspectRatio) {
            width = height * aspectRatio;
            left += range.X / 2;
          }

          if (height < 0) {
            directive = "s";
            height = 0;
          }

          break;

        case "w":
          if (range.x <= 0 && (left <= 0 || aspectRatio && (top <= 0 || bottom >= maxHeight))) {
            renderable = FALSE;
            break;
          }

          width -= range.x;
          left += range.x;

          if (aspectRatio) {
            height = width / aspectRatio;
            top += range.Y / 2;
          }

          if (width < 0) {
            directive = "e";
            width = 0;
          }

          break;

        case "s":
          if (range.y >= 0 && (bottom >= maxHeight || aspectRatio && (left <= 0 || right >= maxWidth))) {
            renderable = FALSE;
            break;
          }

          height += range.y;

          if (aspectRatio) {
            width = height * aspectRatio;
            left -= range.X / 2;
          }

          if (height < 0) {
            directive = "n";
            height = 0;
          }

          break;

        case "ne":
          if (aspectRatio) {
            if (range.y <= 0 && (top <= 0 || right >= maxWidth)) {
              renderable = FALSE;
              break;
            }

            height -= range.y;
            top += range.y;
            width = height * aspectRatio;
          } else {
            if (range.x >= 0) {
              if (right < maxWidth) {
                width += range.x;
              } else if (range.y <= 0 && top <= 0) {
                renderable = FALSE;
              }
            } else {
              width += range.x;
            }

            if (range.y <= 0) {
              if (top > 0) {
                height -= range.y;
                top += range.y;
              }
            } else {
              height -= range.y;
              top += range.y;
            }
          }

          if (height < 0) {
            directive = "sw";
            height = 0;
            width = 0;
          }

          break;

        case "nw":
          if (aspectRatio) {
            if (range.y <= 0 && (top <= 0 || left <= 0)) {
              renderable = FALSE;
              break;
            }

            height -= range.y;
            top += range.y;
            width = height * aspectRatio;
            left += range.X;
          } else {
            if (range.x <= 0) {
              if (left > 0) {
                width -= range.x;
                left += range.x;
              } else if (range.y <= 0 && top <= 0) {
                renderable = FALSE;
              }
            } else {
              width -= range.x;
              left += range.x;
            }

            if (range.y <= 0) {
              if (top > 0) {
                height -= range.y;
                top += range.y;
              }
            } else {
              height -= range.y;
              top += range.y;
            }
          }

          if (height < 0) {
            directive = "se";
            height = 0;
            width = 0;
          }

          break;

        case "sw":
          if (aspectRatio) {
            if (range.x <= 0 && (left <= 0 || bottom >= maxHeight)) {
              renderable = FALSE;
              break;
            }

            width -= range.x;
            left += range.x;
            height = width / aspectRatio;
          } else {
            if (range.x <= 0) {
              if (left > 0) {
                width -= range.x;
                left += range.x;
              } else if (range.y >= 0 && bottom >= maxHeight) {
                renderable = FALSE;
              }
            } else {
              width -= range.x;
              left += range.x;
            }

            if (range.y >= 0) {
              if (bottom < maxHeight) {
                height += range.y;
              }
            } else {
              height += range.y;
            }
          }

          if (width < 0) {
            directive = "ne";
            height = 0;
            width = 0;
          }

          break;

        case "se":
          if (aspectRatio) {
            if (range.x >= 0 && (right >= maxWidth || bottom >= maxHeight)) {
              renderable = FALSE;
              break;
            }

            width += range.x;
            height = width / aspectRatio;
          } else {
            if (range.x >= 0) {
              if (right < maxWidth) {
                width += range.x;
              } else if (range.y >= 0 && bottom >= maxHeight) {
                renderable = FALSE;
              }
            } else {
              width += range.x;
            }

            if (range.y >= 0) {
              if (bottom < maxHeight) {
                height += range.y;
              }
            } else {
              height += range.y;
            }
          }

          if (width < 0) {
            directive = "nw";
            height = 0;
            width = 0;
          }

          break;

        // Move image
        case "move":
          image.left += range.x;
          image.top += range.y;
          this.renderImage("move");
          renderable = FALSE;

          break;

        // Scale image
        case "zoom":
          if (defaults.zoomable) {
            this.zoom(function (x, y, x1, y1, x2, y2) {
              return (sqrt(x2 * x2 + y2 * y2) - sqrt(x1 * x1 + y1 * y1)) / sqrt(x * x + y * y);
            }(
              image.width,
              image.height,
              abs(this.startX - this.startX2),
              abs(this.startY - this.startY2),
              abs(this.endX - this.endX2),
              abs(this.endY - this.endY2)
            ));

            this.endX2 = this.startX2;
            this.endY2 = this.startY2;
          }

          break;

        // Crop image
        case "crop":
          if (range.x && range.y) {
            offset = this.$cropper.offset();
            left = this.startX - offset.left;
            top = this.startY - offset.top;
            width = dragger.minWidth;
            height = dragger.minHeight;

            if (range.x > 0) {
              if (range.y > 0) {
                directive = "se";
              } else {
                directive = "ne";
                top -= height;
              }
            } else {
              if (range.y > 0) {
                directive = "sw";
                left -= width;
              } else {
                directive = "nw";
                left -= width;
                top -= height;
              }
            }

            // Show the dragger if is hidden
            if (!this.cropped) {
              this.cropped = TRUE;
              this.$dragger.removeClass(CLASS_HIDDEN);
            }
          }

          break;

        // No default
      }

      if (renderable) {
        dragger.width = width;
        dragger.height = height;
        dragger.left = left;
        dragger.top = top;
        this.directive = directive;

        this.renderDragger();
      }

      // Override
      this.startX = this.endX;
      this.startY = this.endY;
    }
  };

  // Use the string compressor: Strmin (https://github.com/fengyuanchen/strmin)
  Cropper.TEMPLATE = (function (source, words) {
    words = words.split(",");
    return source.replace(/\d+/g, function (i) {
      return words[i];
    });
  })('<0 6="5-container"><0 6="5-canvas"></0><0 6="5-dragger"><1 6="5-viewer"></1><1 6="5-8 8-h"></1><1 6="5-8 8-v"></1><1 6="5-face" 3-2="all"></1><1 6="5-7 7-e" 3-2="e"></1><1 6="5-7 7-n" 3-2="n"></1><1 6="5-7 7-w" 3-2="w"></1><1 6="5-7 7-s" 3-2="s"></1><1 6="5-4 4-e" 3-2="e"></1><1 6="5-4 4-n" 3-2="n"></1><1 6="5-4 4-w" 3-2="w"></1><1 6="5-4 4-s" 3-2="s"></1><1 6="5-4 4-ne" 3-2="ne"></1><1 6="5-4 4-nw" 3-2="nw"></1><1 6="5-4 4-sw" 3-2="sw"></1><1 6="5-4 4-se" 3-2="se"></1></0></0>', "div,span,directive,data,point,cropper,class,line,dashed");

  /* Template source:
  <div class="cropper-container">
    <div class="cropper-canvas"></div>
    <div class="cropper-dragger">
      <span class="cropper-viewer"></span>
      <span class="cropper-dashed dashed-h"></span>
      <span class="cropper-dashed dashed-v"></span>
      <span class="cropper-face" data-directive="all"></span>
      <span class="cropper-line line-e" data-directive="e"></span>
      <span class="cropper-line line-n" data-directive="n"></span>
      <span class="cropper-line line-w" data-directive="w"></span>
      <span class="cropper-line line-s" data-directive="s"></span>
      <span class="cropper-point point-e" data-directive="e"></span>
      <span class="cropper-point point-n" data-directive="n"></span>
      <span class="cropper-point point-w" data-directive="w"></span>
      <span class="cropper-point point-s" data-directive="s"></span>
      <span class="cropper-point point-ne" data-directive="ne"></span>
      <span class="cropper-point point-nw" data-directive="nw"></span>
      <span class="cropper-point point-sw" data-directive="sw"></span>
      <span class="cropper-point point-se" data-directive="se"></span>
    </div>
  </div>
  */

  Cropper.DEFAULTS = {
    // Basic
    aspectRatio: "auto",
    autoCropArea: 0.8, // 80%
    data: {
      // x: 0,
      // y: 0,
      // width: 300,
      // height: 150
    },
    done: $.noop,
    preview: "",

    // Toggles
    multiple: FALSE,
    autoCrop: TRUE,
    dragCrop: TRUE,
    dashed: TRUE,
    modal: TRUE,
    movable: TRUE,
    resizable: TRUE,
    zoomable: TRUE,
    rotatable: TRUE,
    checkImageOrigin: TRUE,

    // Dimensions
    minWidth: 0,
    minHeight: 0,
    maxWidth: INFINITY,
    maxHeight: INFINITY,

    // Events
    build: NULL,
    built: NULL,
    dragstart: NULL,
    dragmove: NULL,
    dragend: NULL
  };

  Cropper.setDefaults = function (options) {
    $.extend(Cropper.DEFAULTS, options);
  };

  // Save the other cropper
  Cropper.other = $.fn.cropper;

  // Register as jQuery plugin
  $.fn.cropper = function (options) {
    var args = toArray(arguments, 1),
        result;

    this.each(function () {
      var $this = $(this),
          data = $this.data("cropper"),
          fn;

      if (!data) {
        $this.data("cropper", (data = new Cropper(this, options)));
      }

      if (typeof options === "string" && $.isFunction((fn = data[options]))) {
        result = fn.apply(data, args);
      }
    });

    return (typeof result !== STRING_UNDEFINED ? result : this);
  };

  $.fn.cropper.Constructor = Cropper;
  $.fn.cropper.setDefaults = Cropper.setDefaults;

  // No conflict
  $.fn.cropper.noConflict = function () {
    $.fn.cropper = Cropper.other;
    return this;
  };
});
