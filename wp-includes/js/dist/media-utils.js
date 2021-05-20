this["wp"] = this["wp"] || {}; this["wp"]["mediaUtils"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "Lb+8");
/******/ })
/************************************************************************/
/******/ ({

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "Lb+8":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "MediaUpload", function() { return /* reexport */ media_upload; });
__webpack_require__.d(__webpack_exports__, "uploadMedia", function() { return /* reexport */ uploadMedia; });

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// CONCATENATED MODULE: ./node_modules/@wordpress/media-utils/build-module/components/media-upload/index.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



const {
  wp
} = window;
const DEFAULT_EMPTY_GALLERY = [];
/**
 * Prepares the Featured Image toolbars and frames.
 *
 * @return {wp.media.view.MediaFrame.Select} The default media workflow.
 */

const getFeaturedImageMediaFrame = () => {
  return wp.media.view.MediaFrame.Select.extend({
    /**
     * Enables the Set Featured Image Button.
     *
     * @param {Object} toolbar toolbar for featured image state
     * @return {void}
     */
    featuredImageToolbar(toolbar) {
      this.createSelectToolbar(toolbar, {
        text: wp.media.view.l10n.setFeaturedImage,
        state: this.options.state
      });
    },

    /**
     * Handle the edit state requirements of selected media item.
     *
     * @return {void}
     */
    editState() {
      const selection = this.state('featured-image').get('selection');
      const view = new wp.media.view.EditImage({
        model: selection.single(),
        controller: this
      }).render(); // Set the view to the EditImage frame using the selected image.

      this.content.set(view); // After bringing in the frame, load the actual editor via an ajax call.

      view.loadEditor();
    },

    /**
     * Create the default states.
     *
     * @return {void}
     */
    createStates: function createStates() {
      this.on('toolbar:create:featured-image', this.featuredImageToolbar, this);
      this.on('content:render:edit-image', this.editState, this);
      this.states.add([new wp.media.controller.FeaturedImage(), new wp.media.controller.EditImage({
        model: this.options.editImage
      })]);
    }
  });
};
/**
 * Prepares the Gallery toolbars and frames.
 *
 * @return {wp.media.view.MediaFrame.Post} The default media workflow.
 */


const getGalleryDetailsMediaFrame = () => {
  /**
   * Custom gallery details frame.
   *
   * @see https://github.com/xwp/wp-core-media-widgets/blob/905edbccfc2a623b73a93dac803c5335519d7837/wp-admin/js/widgets/media-gallery-widget.js
   * @class GalleryDetailsMediaFrame
   * @class
   */
  return wp.media.view.MediaFrame.Post.extend({
    /**
     * Set up gallery toolbar.
     *
     * @return {void}
     */
    galleryToolbar() {
      const editing = this.state().get('editing');
      this.toolbar.set(new wp.media.view.Toolbar({
        controller: this,
        items: {
          insert: {
            style: 'primary',
            text: editing ? wp.media.view.l10n.updateGallery : wp.media.view.l10n.insertGallery,
            priority: 80,
            requires: {
              library: true
            },

            /**
             * @fires wp.media.controller.State#update
             */
            click() {
              const controller = this.controller,
                    state = controller.state();
              controller.close();
              state.trigger('update', state.get('library')); // Restore and reset the default state.

              controller.setState(controller.options.state);
              controller.reset();
            }

          }
        }
      }));
    },

    /**
     * Handle the edit state requirements of selected media item.
     *
     * @return {void}
     */
    editState() {
      const selection = this.state('gallery').get('selection');
      const view = new wp.media.view.EditImage({
        model: selection.single(),
        controller: this
      }).render(); // Set the view to the EditImage frame using the selected image.

      this.content.set(view); // After bringing in the frame, load the actual editor via an ajax call.

      view.loadEditor();
    },

    /**
     * Create the default states.
     *
     * @return {void}
     */
    createStates: function createStates() {
      this.on('toolbar:create:main-gallery', this.galleryToolbar, this);
      this.on('content:render:edit-image', this.editState, this);
      this.states.add([new wp.media.controller.Library({
        id: 'gallery',
        title: wp.media.view.l10n.createGalleryTitle,
        priority: 40,
        toolbar: 'main-gallery',
        filterable: 'uploaded',
        multiple: 'add',
        editable: false,
        library: wp.media.query(Object(external_lodash_["defaults"])({
          type: 'image'
        }, this.options.library))
      }), new wp.media.controller.EditImage({
        model: this.options.editImage
      }), new wp.media.controller.GalleryEdit({
        library: this.options.selection,
        editing: this.options.editing,
        menu: 'gallery',
        displaySettings: false,
        multiple: true
      }), new wp.media.controller.GalleryAdd()]);
    }
  });
}; // the media library image object contains numerous attributes
// we only need this set to display the image in the library


const slimImageObject = img => {
  const attrSet = ['sizes', 'mime', 'type', 'subtype', 'id', 'url', 'alt', 'link', 'caption'];
  return Object(external_lodash_["pick"])(img, attrSet);
};

const getAttachmentsCollection = ids => {
  return wp.media.query({
    order: 'ASC',
    orderby: 'post__in',
    post__in: ids,
    posts_per_page: -1,
    query: true,
    type: 'image'
  });
};

class media_upload_MediaUpload extends external_wp_element_["Component"] {
  constructor({
    allowedTypes,
    gallery = false,
    unstableFeaturedImageFlow = false,
    modalClass,
    multiple = false,
    title = Object(external_wp_i18n_["__"])('Select or Upload Media')
  }) {
    super(...arguments);
    this.openModal = this.openModal.bind(this);
    this.onOpen = this.onOpen.bind(this);
    this.onSelect = this.onSelect.bind(this);
    this.onUpdate = this.onUpdate.bind(this);
    this.onClose = this.onClose.bind(this);

    if (gallery) {
      this.buildAndSetGalleryFrame();
    } else {
      const frameConfig = {
        title,
        multiple
      };

      if (!!allowedTypes) {
        frameConfig.library = {
          type: allowedTypes
        };
      }

      this.frame = wp.media(frameConfig);
    }

    if (modalClass) {
      this.frame.$el.addClass(modalClass);
    }

    if (unstableFeaturedImageFlow) {
      this.buildAndSetFeatureImageFrame();
    }

    this.initializeListeners();
  }

  initializeListeners() {
    // When an image is selected in the media frame...
    this.frame.on('select', this.onSelect);
    this.frame.on('update', this.onUpdate);
    this.frame.on('open', this.onOpen);
    this.frame.on('close', this.onClose);
  }
  /**
   * Sets the Gallery frame and initializes listeners.
   *
   * @return {void}
   */


  buildAndSetGalleryFrame() {
    const {
      addToGallery = false,
      allowedTypes,
      multiple = false,
      value = DEFAULT_EMPTY_GALLERY
    } = this.props; // If the value did not changed there is no need to rebuild the frame,
    // we can continue to use the existing one.

    if (value === this.lastGalleryValue) {
      return;
    }

    this.lastGalleryValue = value; // If a frame already existed remove it.

    if (this.frame) {
      this.frame.remove();
    }

    let currentState;

    if (addToGallery) {
      currentState = 'gallery-library';
    } else {
      currentState = value && value.length ? 'gallery-edit' : 'gallery';
    }

    if (!this.GalleryDetailsMediaFrame) {
      this.GalleryDetailsMediaFrame = getGalleryDetailsMediaFrame();
    }

    const attachments = getAttachmentsCollection(value);
    const selection = new wp.media.model.Selection(attachments.models, {
      props: attachments.props.toJSON(),
      multiple
    });
    this.frame = new this.GalleryDetailsMediaFrame({
      mimeType: allowedTypes,
      state: currentState,
      multiple,
      selection,
      editing: value && value.length ? true : false
    });
    wp.media.frame = this.frame;
    this.initializeListeners();
  }
  /**
   * Initializes the Media Library requirements for the featured image flow.
   *
   * @return {void}
   */


  buildAndSetFeatureImageFrame() {
    const featuredImageFrame = getFeaturedImageMediaFrame();
    const attachments = getAttachmentsCollection(this.props.value);
    const selection = new wp.media.model.Selection(attachments.models, {
      props: attachments.props.toJSON()
    });
    this.frame = new featuredImageFrame({
      mimeType: this.props.allowedTypes,
      state: 'featured-image',
      multiple: this.props.multiple,
      selection,
      editing: this.props.value ? true : false
    });
    wp.media.frame = this.frame;
  }

  componentWillUnmount() {
    this.frame.remove();
  }

  onUpdate(selections) {
    const {
      onSelect,
      multiple = false
    } = this.props;
    const state = this.frame.state();
    const selectedImages = selections || state.get('selection');

    if (!selectedImages || !selectedImages.models.length) {
      return;
    }

    if (multiple) {
      onSelect(selectedImages.models.map(model => slimImageObject(model.toJSON())));
    } else {
      onSelect(slimImageObject(selectedImages.models[0].toJSON()));
    }
  }

  onSelect() {
    const {
      onSelect,
      multiple = false
    } = this.props; // Get media attachment details from the frame state

    const attachment = this.frame.state().get('selection').toJSON();
    onSelect(multiple ? attachment : attachment[0]);
  }

  onOpen() {
    var _this$props$value;

    this.updateCollection(); // Handle both this.props.value being either (number[]) multiple ids
    // (for galleries) or a (number) singular id (e.g. image block).

    const hasMedia = Array.isArray(this.props.value) ? !!((_this$props$value = this.props.value) !== null && _this$props$value !== void 0 && _this$props$value.length) : !!this.props.value;

    if (!hasMedia) {
      return;
    }

    if (!this.props.gallery) {
      const selection = this.frame.state().get('selection');
      Object(external_lodash_["castArray"])(this.props.value).forEach(id => {
        selection.add(wp.media.attachment(id));
      });
    } // load the images so they are available in the media modal.


    getAttachmentsCollection(Object(external_lodash_["castArray"])(this.props.value)).more();
  }

  onClose() {
    const {
      onClose
    } = this.props;

    if (onClose) {
      onClose();
    }
  }

  updateCollection() {
    const frameContent = this.frame.content.get();

    if (frameContent && frameContent.collection) {
      const collection = frameContent.collection; // clean all attachments we have in memory.

      collection.toArray().forEach(model => model.trigger('destroy', model)); // reset has more flag, if library had small amount of items all items may have been loaded before.

      collection.mirroring._hasMore = true; // request items

      collection.more();
    }
  }

  openModal() {
    if (this.props.gallery) {
      this.buildAndSetGalleryFrame();
    }

    this.frame.open();
  }

  render() {
    return this.props.render({
      open: this.openModal
    });
  }

}

/* harmony default export */ var media_upload = (media_upload_MediaUpload);

// CONCATENATED MODULE: ./node_modules/@wordpress/media-utils/build-module/components/index.js


// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__("ywyh");
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);

// EXTERNAL MODULE: external ["wp","blob"]
var external_wp_blob_ = __webpack_require__("xTGt");

// CONCATENATED MODULE: ./node_modules/@wordpress/media-utils/build-module/utils/upload-media.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Browsers may use unexpected mime types, and they differ from browser to browser.
 * This function computes a flexible array of mime types from the mime type structured provided by the server.
 * Converts { jpg|jpeg|jpe: "image/jpeg" } into [ "image/jpeg", "image/jpg", "image/jpeg", "image/jpe" ]
 * The computation of this array instead of directly using the object,
 * solves the problem in chrome where mp3 files have audio/mp3 as mime type instead of audio/mpeg.
 * https://bugs.chromium.org/p/chromium/issues/detail?id=227004
 *
 * @param {?Object} wpMimeTypesObject Mime type object received from the server.
 *                                    Extensions are keys separated by '|' and values are mime types associated with an extension.
 *
 * @return {?Array} An array of mime types or the parameter passed if it was "falsy".
 */

function getMimeTypesArray(wpMimeTypesObject) {
  if (!wpMimeTypesObject) {
    return wpMimeTypesObject;
  }

  return Object(external_lodash_["flatMap"])(wpMimeTypesObject, (mime, extensionsString) => {
    const [type] = mime.split('/');
    const extensions = extensionsString.split('|');
    return [mime, ...Object(external_lodash_["map"])(extensions, extension => `${type}/${extension}`)];
  });
}
/**
 *	Media Upload is used by audio, image, gallery, video, and file blocks to
 *	handle uploading a media file when a file upload button is activated.
 *
 *	TODO: future enhancement to add an upload indicator.
 *
 * @param   {Object}   $0                    Parameters object passed to the function.
 * @param   {?Array}   $0.allowedTypes       Array with the types of media that can be uploaded, if unset all types are allowed.
 * @param   {?Object}  $0.additionalData     Additional data to include in the request.
 * @param   {Array}    $0.filesList          List of files.
 * @param   {?number}  $0.maxUploadFileSize  Maximum upload size in bytes allowed for the site.
 * @param   {Function} $0.onError            Function called when an error happens.
 * @param   {Function} $0.onFileChange       Function called each time a file or a temporary representation of the file is available.
 * @param   {?Object}  $0.wpAllowedMimeTypes List of allowed mime types and file extensions.
 */

async function uploadMedia({
  allowedTypes,
  additionalData = {},
  filesList,
  maxUploadFileSize,
  onError = external_lodash_["noop"],
  onFileChange,
  wpAllowedMimeTypes = null
}) {
  // Cast filesList to array
  const files = [...filesList];
  const filesSet = [];

  const setAndUpdateFiles = (idx, value) => {
    Object(external_wp_blob_["revokeBlobURL"])(Object(external_lodash_["get"])(filesSet, [idx, 'url']));
    filesSet[idx] = value;
    onFileChange(Object(external_lodash_["compact"])(filesSet));
  }; // Allowed type specified by consumer


  const isAllowedType = fileType => {
    if (!allowedTypes) {
      return true;
    }

    return Object(external_lodash_["some"])(allowedTypes, allowedType => {
      // If a complete mimetype is specified verify if it matches exactly the mime type of the file.
      if (Object(external_lodash_["includes"])(allowedType, '/')) {
        return allowedType === fileType;
      } // Otherwise a general mime type is used and we should verify if the file mimetype starts with it.


      return Object(external_lodash_["startsWith"])(fileType, `${allowedType}/`);
    });
  }; // Allowed types for the current WP_User


  const allowedMimeTypesForUser = getMimeTypesArray(wpAllowedMimeTypes);

  const isAllowedMimeTypeForUser = fileType => {
    return Object(external_lodash_["includes"])(allowedMimeTypesForUser, fileType);
  }; // Build the error message including the filename


  const triggerError = error => {
    error.message = [Object(external_wp_element_["createElement"])("strong", {
      key: "filename"
    }, error.file.name), ': ', error.message];
    onError(error);
  };

  const validFiles = [];

  for (const mediaFile of files) {
    // Verify if user is allowed to upload this mime type.
    // Defer to the server when type not detected.
    if (allowedMimeTypesForUser && mediaFile.type && !isAllowedMimeTypeForUser(mediaFile.type)) {
      triggerError({
        code: 'MIME_TYPE_NOT_ALLOWED_FOR_USER',
        message: Object(external_wp_i18n_["__"])('Sorry, this file type is not permitted for security reasons.'),
        file: mediaFile
      });
      continue;
    } // Check if the block supports this mime type.
    // Defer to the server when type not detected.


    if (mediaFile.type && !isAllowedType(mediaFile.type)) {
      triggerError({
        code: 'MIME_TYPE_NOT_SUPPORTED',
        message: Object(external_wp_i18n_["__"])('Sorry, this file type is not supported here.'),
        file: mediaFile
      });
      continue;
    } // verify if file is greater than the maximum file upload size allowed for the site.


    if (maxUploadFileSize && mediaFile.size > maxUploadFileSize) {
      triggerError({
        code: 'SIZE_ABOVE_LIMIT',
        message: Object(external_wp_i18n_["__"])('This file exceeds the maximum upload size for this site.'),
        file: mediaFile
      });
      continue;
    } // Don't allow empty files to be uploaded.


    if (mediaFile.size <= 0) {
      triggerError({
        code: 'EMPTY_FILE',
        message: Object(external_wp_i18n_["__"])('This file is empty.'),
        file: mediaFile
      });
      continue;
    }

    validFiles.push(mediaFile); // Set temporary URL to create placeholder media file, this is replaced
    // with final file from media gallery when upload is `done` below

    filesSet.push({
      url: Object(external_wp_blob_["createBlobURL"])(mediaFile)
    });
    onFileChange(filesSet);
  }

  for (let idx = 0; idx < validFiles.length; ++idx) {
    const mediaFile = validFiles[idx];

    try {
      const savedMedia = await createMediaFromFile(mediaFile, additionalData);
      const mediaObject = { ...Object(external_lodash_["omit"])(savedMedia, ['alt_text', 'source_url']),
        alt: savedMedia.alt_text,
        caption: Object(external_lodash_["get"])(savedMedia, ['caption', 'raw'], ''),
        title: savedMedia.title.raw,
        url: savedMedia.source_url
      };
      setAndUpdateFiles(idx, mediaObject);
    } catch (error) {
      // Reset to empty on failure.
      setAndUpdateFiles(idx, null);
      let message;

      if (Object(external_lodash_["has"])(error, ['message'])) {
        message = Object(external_lodash_["get"])(error, ['message']);
      } else {
        message = Object(external_wp_i18n_["sprintf"])( // translators: %s: file name
        Object(external_wp_i18n_["__"])('Error while uploading file %s to the media library.'), mediaFile.name);
      }

      onError({
        code: 'GENERAL',
        message,
        file: mediaFile
      });
    }
  }
}
/**
 * @param {File}    file           Media File to Save.
 * @param {?Object} additionalData Additional data to include in the request.
 *
 * @return {Promise} Media Object Promise.
 */

function createMediaFromFile(file, additionalData) {
  // Create upload payload
  const data = new window.FormData();
  data.append('file', file, file.name || file.type.replace('/', '.'));
  Object(external_lodash_["forEach"])(additionalData, (value, key) => data.append(key, value));
  return external_wp_apiFetch_default()({
    path: '/wp/v2/media',
    body: data,
    method: 'POST'
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/media-utils/build-module/utils/index.js


// CONCATENATED MODULE: ./node_modules/@wordpress/media-utils/build-module/index.js




/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "xTGt":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blob"]; }());

/***/ }),

/***/ "ywyh":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["apiFetch"]; }());

/***/ })

/******/ });