/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  MediaUpload: () => (/* reexport */ media_upload),
  transformAttachment: () => (/* reexport */ transformAttachment),
  uploadMedia: () => (/* reexport */ uploadMedia),
  validateFileSize: () => (/* reexport */ validateFileSize),
  validateMimeType: () => (/* reexport */ validateMimeType),
  validateMimeTypeForUser: () => (/* reexport */ validateMimeTypeForUser)
});

;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// ./node_modules/@wordpress/media-utils/build-module/components/media-upload/index.js
/**
 * WordPress dependencies
 */


const DEFAULT_EMPTY_GALLERY = [];

/**
 * Prepares the Featured Image toolbars and frames.
 *
 * @return {window.wp.media.view.MediaFrame.Select} The default media workflow.
 */
const getFeaturedImageMediaFrame = () => {
  const {
    wp
  } = window;
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
      }).render();

      // Set the view to the EditImage frame using the selected image.
      this.content.set(view);

      // After bringing in the frame, load the actual editor via an ajax call.
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
 * @return {window.wp.media.view.MediaFrame.Post} The default media workflow.
 */
const getGalleryDetailsMediaFrame = () => {
  const {
    wp
  } = window;
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
              state.trigger('update', state.get('library'));

              // Restore and reset the default state.
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
      }).render();

      // Set the view to the EditImage frame using the selected image.
      this.content.set(view);

      // After bringing in the frame, load the actual editor via an ajax call.
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
        library: wp.media.query({
          type: 'image',
          ...this.options.library
        })
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
};

// The media library image object contains numerous attributes
// we only need this set to display the image in the library.
const slimImageObject = img => {
  const attrSet = ['sizes', 'mime', 'type', 'subtype', 'id', 'url', 'alt', 'link', 'caption'];
  return attrSet.reduce((result, key) => {
    if (img?.hasOwnProperty(key)) {
      result[key] = img[key];
    }
    return result;
  }, {});
};
const getAttachmentsCollection = ids => {
  const {
    wp
  } = window;
  return wp.media.query({
    order: 'ASC',
    orderby: 'post__in',
    post__in: ids,
    posts_per_page: -1,
    query: true,
    type: 'image'
  });
};
class MediaUpload extends external_wp_element_namespaceObject.Component {
  constructor() {
    super(...arguments);
    this.openModal = this.openModal.bind(this);
    this.onOpen = this.onOpen.bind(this);
    this.onSelect = this.onSelect.bind(this);
    this.onUpdate = this.onUpdate.bind(this);
    this.onClose = this.onClose.bind(this);
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
    } = this.props;

    // If the value did not changed there is no need to rebuild the frame,
    // we can continue to use the existing one.
    if (value === this.lastGalleryValue) {
      return;
    }
    const {
      wp
    } = window;
    this.lastGalleryValue = value;

    // If a frame already existed remove it.
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
    const {
      wp
    } = window;
    const {
      value: featuredImageId,
      multiple,
      allowedTypes
    } = this.props;
    const featuredImageFrame = getFeaturedImageMediaFrame();
    const attachments = getAttachmentsCollection(featuredImageId);
    const selection = new wp.media.model.Selection(attachments.models, {
      props: attachments.props.toJSON()
    });
    this.frame = new featuredImageFrame({
      mimeType: allowedTypes,
      state: 'featured-image',
      multiple,
      selection,
      editing: featuredImageId
    });
    wp.media.frame = this.frame;
    // In order to select the current featured image when opening
    // the media library we have to set the appropriate settings.
    // Currently they are set in php for the post editor, but
    // not for site editor.
    wp.media.view.settings.post = {
      ...wp.media.view.settings.post,
      featuredImageId: featuredImageId || -1
    };
  }
  componentWillUnmount() {
    this.frame?.remove();
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
    } = this.props;
    // Get media attachment details from the frame state.
    const attachment = this.frame.state().get('selection').toJSON();
    onSelect(multiple ? attachment : attachment[0]);
  }
  onOpen() {
    const {
      wp
    } = window;
    const {
      value
    } = this.props;
    this.updateCollection();

    //Handle active tab in media model on model open.
    if (this.props.mode) {
      this.frame.content.mode(this.props.mode);
    }

    // Handle both this.props.value being either (number[]) multiple ids
    // (for galleries) or a (number) singular id (e.g. image block).
    const hasMedia = Array.isArray(value) ? !!value?.length : !!value;
    if (!hasMedia) {
      return;
    }
    const isGallery = this.props.gallery;
    const selection = this.frame.state().get('selection');
    const valueArray = Array.isArray(value) ? value : [value];
    if (!isGallery) {
      valueArray.forEach(id => {
        selection.add(wp.media.attachment(id));
      });
    }

    // Load the images so they are available in the media modal.
    const attachments = getAttachmentsCollection(valueArray);

    // Once attachments are loaded, set the current selection.
    attachments.more().done(function () {
      if (isGallery && attachments?.models?.length) {
        selection.add(attachments.models);
      }
    });
  }
  onClose() {
    const {
      onClose
    } = this.props;
    if (onClose) {
      onClose();
    }
    this.frame.detach();
  }
  updateCollection() {
    const frameContent = this.frame.content.get();
    if (frameContent && frameContent.collection) {
      const collection = frameContent.collection;

      // Clean all attachments we have in memory.
      collection.toArray().forEach(model => model.trigger('destroy', model));

      // Reset has more flag, if library had small amount of items all items may have been loaded before.
      collection.mirroring._hasMore = true;

      // Request items.
      collection.more();
    }
  }
  openModal() {
    const {
      allowedTypes,
      gallery = false,
      unstableFeaturedImageFlow = false,
      modalClass,
      multiple = false,
      title = (0,external_wp_i18n_namespaceObject.__)('Select or Upload Media')
    } = this.props;
    const {
      wp
    } = window;
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
    this.frame.open();
  }
  render() {
    return this.props.render({
      open: this.openModal
    });
  }
}
/* harmony default export */ const media_upload = (MediaUpload);

;// ./node_modules/@wordpress/media-utils/build-module/components/index.js


;// external ["wp","blob"]
const external_wp_blob_namespaceObject = window["wp"]["blob"];
;// external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// ./node_modules/@wordpress/media-utils/build-module/utils/flatten-form-data.js
/**
 * Determines whether the passed argument appears to be a plain object.
 *
 * @param data The object to inspect.
 */
function isPlainObject(data) {
  return data !== null && typeof data === 'object' && Object.getPrototypeOf(data) === Object.prototype;
}

/**
 * Recursively flatten data passed to form data, to allow using multi-level objects.
 *
 * @param {FormData}      formData Form data object.
 * @param {string}        key      Key to amend to form data object
 * @param {string|Object} data     Data to be amended to form data.
 */
function flattenFormData(formData, key, data) {
  if (isPlainObject(data)) {
    for (const [name, value] of Object.entries(data)) {
      flattenFormData(formData, `${key}[${name}]`, value);
    }
  } else if (data !== undefined) {
    formData.append(key, String(data));
  }
}

;// ./node_modules/@wordpress/media-utils/build-module/utils/transform-attachment.js
/**
 * Internal dependencies
 */

/**
 * Transforms an attachment object from the REST API shape into the shape expected by the block editor and other consumers.
 *
 * @param attachment REST API attachment object.
 */
function transformAttachment(attachment) {
  var _attachment$caption$r;
  // eslint-disable-next-line camelcase
  const {
    alt_text,
    source_url,
    ...savedMediaProps
  } = attachment;
  return {
    ...savedMediaProps,
    alt: attachment.alt_text,
    caption: (_attachment$caption$r = attachment.caption?.raw) !== null && _attachment$caption$r !== void 0 ? _attachment$caption$r : '',
    title: attachment.title.raw,
    url: attachment.source_url,
    poster: attachment._embedded?.['wp:featuredmedia']?.[0]?.source_url || undefined
  };
}

;// ./node_modules/@wordpress/media-utils/build-module/utils/upload-to-server.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


async function uploadToServer(file, additionalData = {}, signal) {
  // Create upload payload.
  const data = new FormData();
  data.append('file', file, file.name || file.type.replace('/', '.'));
  for (const [key, value] of Object.entries(additionalData)) {
    flattenFormData(data, key, value);
  }
  return transformAttachment(await external_wp_apiFetch_default()({
    // This allows the video block to directly get a video's poster image.
    path: '/wp/v2/media?_embed=wp:featuredmedia',
    body: data,
    method: 'POST',
    signal
  }));
}

;// ./node_modules/@wordpress/media-utils/build-module/utils/upload-error.js
/**
 * MediaError class.
 *
 * Small wrapper around the `Error` class
 * to hold an error code and a reference to a file object.
 */
class UploadError extends Error {
  constructor({
    code,
    message,
    file,
    cause
  }) {
    super(message, {
      cause
    });
    Object.setPrototypeOf(this, new.target.prototype);
    this.code = code;
    this.file = file;
  }
}

;// ./node_modules/@wordpress/media-utils/build-module/utils/validate-mime-type.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Verifies if the caller (e.g. a block) supports this mime type.
 *
 * @param file         File object.
 * @param allowedTypes List of allowed mime types.
 */
function validateMimeType(file, allowedTypes) {
  if (!allowedTypes) {
    return;
  }

  // Allowed type specified by consumer.
  const isAllowedType = allowedTypes.some(allowedType => {
    // If a complete mimetype is specified verify if it matches exactly the mime type of the file.
    if (allowedType.includes('/')) {
      return allowedType === file.type;
    }
    // Otherwise a general mime type is used, and we should verify if the file mimetype starts with it.
    return file.type.startsWith(`${allowedType}/`);
  });
  if (file.type && !isAllowedType) {
    throw new UploadError({
      code: 'MIME_TYPE_NOT_SUPPORTED',
      message: (0,external_wp_i18n_namespaceObject.sprintf)(
      // translators: %s: file name.
      (0,external_wp_i18n_namespaceObject.__)('%s: Sorry, this file type is not supported here.'), file.name),
      file
    });
  }
}

;// ./node_modules/@wordpress/media-utils/build-module/utils/get-mime-types-array.js
/**
 * Browsers may use unexpected mime types, and they differ from browser to browser.
 * This function computes a flexible array of mime types from the mime type structured provided by the server.
 * Converts { jpg|jpeg|jpe: "image/jpeg" } into [ "image/jpeg", "image/jpg", "image/jpeg", "image/jpe" ]
 *
 * @param {?Object} wpMimeTypesObject Mime type object received from the server.
 *                                    Extensions are keys separated by '|' and values are mime types associated with an extension.
 *
 * @return An array of mime types or null
 */
function getMimeTypesArray(wpMimeTypesObject) {
  if (!wpMimeTypesObject) {
    return null;
  }
  return Object.entries(wpMimeTypesObject).flatMap(([extensionsString, mime]) => {
    const [type] = mime.split('/');
    const extensions = extensionsString.split('|');
    return [mime, ...extensions.map(extension => `${type}/${extension}`)];
  });
}

;// ./node_modules/@wordpress/media-utils/build-module/utils/validate-mime-type-for-user.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



/**
 * Verifies if the user is allowed to upload this mime type.
 *
 * @param file               File object.
 * @param wpAllowedMimeTypes List of allowed mime types and file extensions.
 */
function validateMimeTypeForUser(file, wpAllowedMimeTypes) {
  // Allowed types for the current WP_User.
  const allowedMimeTypesForUser = getMimeTypesArray(wpAllowedMimeTypes);
  if (!allowedMimeTypesForUser) {
    return;
  }
  const isAllowedMimeTypeForUser = allowedMimeTypesForUser.includes(file.type);
  if (file.type && !isAllowedMimeTypeForUser) {
    throw new UploadError({
      code: 'MIME_TYPE_NOT_ALLOWED_FOR_USER',
      message: (0,external_wp_i18n_namespaceObject.sprintf)(
      // translators: %s: file name.
      (0,external_wp_i18n_namespaceObject.__)('%s: Sorry, you are not allowed to upload this file type.'), file.name),
      file
    });
  }
}

;// ./node_modules/@wordpress/media-utils/build-module/utils/validate-file-size.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Verifies whether the file is within the file upload size limits for the site.
 *
 * @param file              File object.
 * @param maxUploadFileSize Maximum upload size in bytes allowed for the site.
 */
function validateFileSize(file, maxUploadFileSize) {
  // Don't allow empty files to be uploaded.
  if (file.size <= 0) {
    throw new UploadError({
      code: 'EMPTY_FILE',
      message: (0,external_wp_i18n_namespaceObject.sprintf)(
      // translators: %s: file name.
      (0,external_wp_i18n_namespaceObject.__)('%s: This file is empty.'), file.name),
      file
    });
  }
  if (maxUploadFileSize && file.size > maxUploadFileSize) {
    throw new UploadError({
      code: 'SIZE_ABOVE_LIMIT',
      message: (0,external_wp_i18n_namespaceObject.sprintf)(
      // translators: %s: file name.
      (0,external_wp_i18n_namespaceObject.__)('%s: This file exceeds the maximum upload size for this site.'), file.name),
      file
    });
  }
}

;// ./node_modules/@wordpress/media-utils/build-module/utils/upload-media.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */






/**
 * Upload a media file when the file upload button is activated
 * or when adding a file to the editor via drag & drop.
 *
 * @param $0                    Parameters object passed to the function.
 * @param $0.allowedTypes       Array with the types of media that can be uploaded, if unset all types are allowed.
 * @param $0.additionalData     Additional data to include in the request.
 * @param $0.filesList          List of files.
 * @param $0.maxUploadFileSize  Maximum upload size in bytes allowed for the site.
 * @param $0.onError            Function called when an error happens.
 * @param $0.onFileChange       Function called each time a file or a temporary representation of the file is available.
 * @param $0.wpAllowedMimeTypes List of allowed mime types and file extensions.
 * @param $0.signal             Abort signal.
 */
function uploadMedia({
  wpAllowedMimeTypes,
  allowedTypes,
  additionalData = {},
  filesList,
  maxUploadFileSize,
  onError,
  onFileChange,
  signal
}) {
  const validFiles = [];
  const filesSet = [];
  const setAndUpdateFiles = (index, value) => {
    if (filesSet[index]?.url) {
      (0,external_wp_blob_namespaceObject.revokeBlobURL)(filesSet[index].url);
    }
    filesSet[index] = value;
    onFileChange?.(filesSet.filter(attachment => attachment !== null));
  };
  for (const mediaFile of filesList) {
    // Verify if user is allowed to upload this mime type.
    // Defer to the server when type not detected.
    try {
      validateMimeTypeForUser(mediaFile, wpAllowedMimeTypes);
    } catch (error) {
      onError?.(error);
      continue;
    }

    // Check if the caller (e.g. a block) supports this mime type.
    // Defer to the server when type not detected.
    try {
      validateMimeType(mediaFile, allowedTypes);
    } catch (error) {
      onError?.(error);
      continue;
    }

    // Verify if file is greater than the maximum file upload size allowed for the site.
    try {
      validateFileSize(mediaFile, maxUploadFileSize);
    } catch (error) {
      onError?.(error);
      continue;
    }
    validFiles.push(mediaFile);

    // Set temporary URL to create placeholder media file, this is replaced
    // with final file from media gallery when upload is `done` below.
    filesSet.push({
      url: (0,external_wp_blob_namespaceObject.createBlobURL)(mediaFile)
    });
    onFileChange?.(filesSet);
  }
  validFiles.map(async (file, index) => {
    try {
      const attachment = await uploadToServer(file, additionalData, signal);
      setAndUpdateFiles(index, attachment);
    } catch (error) {
      // Reset to empty on failure.
      setAndUpdateFiles(index, null);
      let message;
      if (error instanceof Error) {
        message = error.message;
      } else {
        message = (0,external_wp_i18n_namespaceObject.sprintf)(
        // translators: %s: file name
        (0,external_wp_i18n_namespaceObject.__)('Error while uploading file %s to the media library.'), file.name);
      }
      onError?.(new UploadError({
        code: 'GENERAL',
        message,
        file,
        cause: error instanceof Error ? error : undefined
      }));
    }
  });
}

;// ./node_modules/@wordpress/media-utils/build-module/index.js







(window.wp = window.wp || {}).mediaUtils = __webpack_exports__;
/******/ })()
;