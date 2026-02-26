"use strict";
var wp;
(wp ||= {}).uploadMedia = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __require = /* @__PURE__ */ ((x) => typeof require !== "undefined" ? require : typeof Proxy !== "undefined" ? new Proxy(x, {
    get: (a, b) => (typeof require !== "undefined" ? require : a)[b]
  }) : x)(function(x) {
    if (typeof require !== "undefined") return require.apply(this, arguments);
    throw Error('Dynamic require of "' + x + '" is not supported');
  });
  var __commonJS = (cb, mod) => function __require2() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
    // If the importer is in node compatibility mode or this is not an ESM
    // file that has been converted to a CommonJS file using a Babel-
    // compatible transform (i.e. "__esModule" has not been set), then set
    // "default" to the CommonJS "module.exports" for node compatibility.
    isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
    mod
  ));
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/blob
  var require_blob = __commonJS({
    "package-external:@wordpress/blob"(exports, module) {
      module.exports = window.wp.blob;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // packages/upload-media/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    MediaUploadProvider: () => provider_default,
    UploadError: () => UploadError,
    clearFeatureDetectionCache: () => clearFeatureDetectionCache,
    detectClientSideMediaSupport: () => detectClientSideMediaSupport,
    isClientSideMediaSupported: () => isClientSideMediaSupported,
    store: () => store
  });

  // packages/upload-media/build-module/store/index.mjs
  var import_data = __toESM(require_data(), 1);

  // packages/upload-media/build-module/store/types.mjs
  var Type = /* @__PURE__ */ ((Type2) => {
    Type2["Unknown"] = "REDUX_UNKNOWN";
    Type2["Add"] = "ADD_ITEM";
    Type2["Prepare"] = "PREPARE_ITEM";
    Type2["Cancel"] = "CANCEL_ITEM";
    Type2["Remove"] = "REMOVE_ITEM";
    Type2["RetryItem"] = "RETRY_ITEM";
    Type2["PauseItem"] = "PAUSE_ITEM";
    Type2["ResumeItem"] = "RESUME_ITEM";
    Type2["PauseQueue"] = "PAUSE_QUEUE";
    Type2["ResumeQueue"] = "RESUME_QUEUE";
    Type2["OperationStart"] = "OPERATION_START";
    Type2["OperationFinish"] = "OPERATION_FINISH";
    Type2["AddOperations"] = "ADD_OPERATIONS";
    Type2["CacheBlobUrl"] = "CACHE_BLOB_URL";
    Type2["RevokeBlobUrls"] = "REVOKE_BLOB_URLS";
    Type2["UpdateProgress"] = "UPDATE_PROGRESS";
    Type2["UpdateSettings"] = "UPDATE_SETTINGS";
    return Type2;
  })(Type || {});
  var ItemStatus = /* @__PURE__ */ ((ItemStatus2) => {
    ItemStatus2["Queued"] = "QUEUED";
    ItemStatus2["Processing"] = "PROCESSING";
    ItemStatus2["Paused"] = "PAUSED";
    ItemStatus2["Uploaded"] = "UPLOADED";
    ItemStatus2["Error"] = "ERROR";
    return ItemStatus2;
  })(ItemStatus || {});
  var OperationType = /* @__PURE__ */ ((OperationType2) => {
    OperationType2["Prepare"] = "PREPARE";
    OperationType2["Upload"] = "UPLOAD";
    OperationType2["ResizeCrop"] = "RESIZE_CROP";
    OperationType2["Rotate"] = "ROTATE";
    OperationType2["TranscodeImage"] = "TRANSCODE_IMAGE";
    OperationType2["ThumbnailGeneration"] = "THUMBNAIL_GENERATION";
    return OperationType2;
  })(OperationType || {});

  // packages/upload-media/build-module/store/constants.mjs
  var STORE_NAME = "core/upload-media";
  var DEFAULT_MAX_CONCURRENT_UPLOADS = 5;
  var DEFAULT_MAX_CONCURRENT_IMAGE_PROCESSING = 2;
  var CLIENT_SIDE_SUPPORTED_MIME_TYPES = [
    "image/jpeg",
    "image/png",
    "image/gif",
    "image/webp",
    "image/avif"
  ];

  // packages/upload-media/build-module/store/reducer.mjs
  var noop = () => {
  };
  var DEFAULT_STATE = {
    queue: [],
    queueStatus: "active",
    blobUrls: {},
    settings: {
      mediaUpload: noop,
      maxConcurrentUploads: DEFAULT_MAX_CONCURRENT_UPLOADS,
      maxConcurrentImageProcessing: DEFAULT_MAX_CONCURRENT_IMAGE_PROCESSING
    }
  };
  function reducer(state = DEFAULT_STATE, action = { type: Type.Unknown }) {
    switch (action.type) {
      case Type.PauseQueue: {
        return {
          ...state,
          queueStatus: "paused"
        };
      }
      case Type.ResumeQueue: {
        return {
          ...state,
          queueStatus: "active"
        };
      }
      case Type.PauseItem:
        return {
          ...state,
          queue: state.queue.map(
            (item) => item.id === action.id ? {
              ...item,
              status: ItemStatus.Paused
            } : item
          )
        };
      case Type.ResumeItem:
        return {
          ...state,
          queue: state.queue.map(
            (item) => item.id === action.id ? {
              ...item,
              status: ItemStatus.Processing
            } : item
          )
        };
      case Type.Add:
        return {
          ...state,
          queue: [...state.queue, action.item]
        };
      case Type.Cancel:
        return {
          ...state,
          queue: state.queue.map(
            (item) => item.id === action.id ? {
              ...item,
              error: action.error
            } : item
          )
        };
      case Type.RetryItem:
        return {
          ...state,
          queue: state.queue.map(
            (item) => item.id === action.id ? {
              ...item,
              status: ItemStatus.Processing,
              error: void 0,
              retryCount: (item.retryCount ?? 0) + 1
            } : item
          )
        };
      case Type.Remove:
        return {
          ...state,
          queue: state.queue.filter((item) => item.id !== action.id)
        };
      case Type.OperationStart: {
        return {
          ...state,
          queue: state.queue.map(
            (item) => item.id === action.id ? {
              ...item,
              currentOperation: action.operation
            } : item
          )
        };
      }
      case Type.AddOperations:
        return {
          ...state,
          queue: state.queue.map((item) => {
            if (item.id !== action.id) {
              return item;
            }
            return {
              ...item,
              operations: [
                ...item.operations || [],
                ...action.operations
              ]
            };
          })
        };
      case Type.OperationFinish:
        return {
          ...state,
          queue: state.queue.map((item) => {
            if (item.id !== action.id) {
              return item;
            }
            const operations = item.operations ? item.operations.slice(1) : [];
            const attachment = item.attachment || action.item.attachment ? {
              ...item.attachment,
              ...action.item.attachment
            } : void 0;
            return {
              ...item,
              currentOperation: void 0,
              operations,
              ...action.item,
              attachment,
              additionalData: {
                ...item.additionalData,
                ...action.item.additionalData
              }
            };
          })
        };
      case Type.CacheBlobUrl: {
        const blobUrls = state.blobUrls[action.id] || [];
        return {
          ...state,
          blobUrls: {
            ...state.blobUrls,
            [action.id]: [...blobUrls, action.blobUrl]
          }
        };
      }
      case Type.RevokeBlobUrls: {
        const newBlobUrls = { ...state.blobUrls };
        delete newBlobUrls[action.id];
        return {
          ...state,
          blobUrls: newBlobUrls
        };
      }
      case Type.UpdateProgress:
        return {
          ...state,
          queue: state.queue.map(
            (item) => item.id === action.id ? {
              ...item,
              progress: action.progress
            } : item
          )
        };
      case Type.UpdateSettings: {
        return {
          ...state,
          settings: {
            ...state.settings,
            ...action.settings
          }
        };
      }
    }
    return state;
  }
  var reducer_default = reducer;

  // packages/upload-media/build-module/store/selectors.mjs
  var selectors_exports = {};
  __export(selectors_exports, {
    getItems: () => getItems,
    getSettings: () => getSettings,
    isUploading: () => isUploading,
    isUploadingById: () => isUploadingById,
    isUploadingByUrl: () => isUploadingByUrl
  });
  function getItems(state) {
    return state.queue;
  }
  function isUploading(state) {
    return state.queue.length >= 1;
  }
  function isUploadingByUrl(state, url) {
    return state.queue.some(
      (item) => item.attachment?.url === url || item.sourceUrl === url
    );
  }
  function isUploadingById(state, attachmentId) {
    return state.queue.some(
      (item) => item.attachment?.id === attachmentId || item.sourceAttachmentId === attachmentId
    );
  }
  function getSettings(state) {
    return state.settings;
  }

  // packages/upload-media/build-module/store/private-selectors.mjs
  var private_selectors_exports = {};
  __export(private_selectors_exports, {
    getActiveImageProcessingCount: () => getActiveImageProcessingCount,
    getActiveUploadCount: () => getActiveUploadCount,
    getAllItems: () => getAllItems,
    getBlobUrls: () => getBlobUrls,
    getFailedItems: () => getFailedItems,
    getItem: () => getItem,
    getItemProgress: () => getItemProgress,
    getPausedUploadForPost: () => getPausedUploadForPost,
    getPendingImageProcessing: () => getPendingImageProcessing,
    getPendingUploads: () => getPendingUploads,
    hasPendingItemsByParentId: () => hasPendingItemsByParentId,
    isBatchUploaded: () => isBatchUploaded,
    isPaused: () => isPaused,
    isUploadingToPost: () => isUploadingToPost
  });
  function getAllItems(state) {
    return state.queue;
  }
  function getItem(state, id) {
    return state.queue.find((item) => item.id === id);
  }
  function isBatchUploaded(state, batchId) {
    const batchItems = state.queue.filter(
      (item) => batchId === item.batchId
    );
    return batchItems.length === 0;
  }
  function isUploadingToPost(state, postOrAttachmentId) {
    return state.queue.some(
      (item) => item.currentOperation === OperationType.Upload && item.additionalData.post === postOrAttachmentId
    );
  }
  function getPausedUploadForPost(state, postOrAttachmentId) {
    return state.queue.find(
      (item) => item.status === ItemStatus.Paused && item.additionalData.post === postOrAttachmentId
    );
  }
  function isPaused(state) {
    return state.queueStatus === "paused";
  }
  function getBlobUrls(state, id) {
    return state.blobUrls[id] || [];
  }
  function getActiveUploadCount(state) {
    return state.queue.filter(
      (item) => item.currentOperation === OperationType.Upload
    ).length;
  }
  function getPendingUploads(state) {
    return state.queue.filter((item) => {
      const nextOperation = Array.isArray(item.operations?.[0]) ? item.operations[0][0] : item.operations?.[0];
      return nextOperation === OperationType.Upload && item.currentOperation !== OperationType.Upload;
    });
  }
  function getActiveImageProcessingCount(state) {
    return state.queue.filter(
      (item) => item.currentOperation === OperationType.ResizeCrop || item.currentOperation === OperationType.Rotate
    ).length;
  }
  function getPendingImageProcessing(state) {
    return state.queue.filter((item) => {
      const nextOperation = Array.isArray(item.operations?.[0]) ? item.operations[0][0] : item.operations?.[0];
      return (nextOperation === OperationType.ResizeCrop || nextOperation === OperationType.Rotate) && item.currentOperation !== OperationType.ResizeCrop && item.currentOperation !== OperationType.Rotate;
    });
  }
  function getFailedItems(state) {
    return state.queue.filter((item) => item.error !== void 0);
  }
  function hasPendingItemsByParentId(state, parentId) {
    return state.queue.some((item) => item.parentId === parentId);
  }
  function getItemProgress(state, id) {
    const item = state.queue.find((i) => i.id === id);
    return item?.progress;
  }

  // packages/upload-media/build-module/store/actions.mjs
  var actions_exports = {};
  __export(actions_exports, {
    addItems: () => addItems,
    cancelItem: () => cancelItem,
    retryItem: () => retryItem
  });

  // node_modules/uuid/dist/esm-browser/rng.js
  var getRandomValues;
  var rnds8 = new Uint8Array(16);
  function rng() {
    if (!getRandomValues) {
      getRandomValues = typeof crypto !== "undefined" && crypto.getRandomValues && crypto.getRandomValues.bind(crypto);
      if (!getRandomValues) {
        throw new Error("crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported");
      }
    }
    return getRandomValues(rnds8);
  }

  // node_modules/uuid/dist/esm-browser/stringify.js
  var byteToHex = [];
  for (let i = 0; i < 256; ++i) {
    byteToHex.push((i + 256).toString(16).slice(1));
  }
  function unsafeStringify(arr, offset = 0) {
    return byteToHex[arr[offset + 0]] + byteToHex[arr[offset + 1]] + byteToHex[arr[offset + 2]] + byteToHex[arr[offset + 3]] + "-" + byteToHex[arr[offset + 4]] + byteToHex[arr[offset + 5]] + "-" + byteToHex[arr[offset + 6]] + byteToHex[arr[offset + 7]] + "-" + byteToHex[arr[offset + 8]] + byteToHex[arr[offset + 9]] + "-" + byteToHex[arr[offset + 10]] + byteToHex[arr[offset + 11]] + byteToHex[arr[offset + 12]] + byteToHex[arr[offset + 13]] + byteToHex[arr[offset + 14]] + byteToHex[arr[offset + 15]];
  }

  // node_modules/uuid/dist/esm-browser/native.js
  var randomUUID = typeof crypto !== "undefined" && crypto.randomUUID && crypto.randomUUID.bind(crypto);
  var native_default = {
    randomUUID
  };

  // node_modules/uuid/dist/esm-browser/v4.js
  function v4(options, buf, offset) {
    if (native_default.randomUUID && !buf && !options) {
      return native_default.randomUUID();
    }
    options = options || {};
    const rnds = options.random || (options.rng || rng)();
    rnds[6] = rnds[6] & 15 | 64;
    rnds[8] = rnds[8] & 63 | 128;
    if (buf) {
      offset = offset || 0;
      for (let i = 0; i < 16; ++i) {
        buf[offset + i] = rnds[i];
      }
      return buf;
    }
    return unsafeStringify(rnds);
  }
  var v4_default = v4;

  // packages/upload-media/build-module/image-file.mjs
  var ImageFile = class extends File {
    width = 0;
    height = 0;
    originalWidth = 0;
    originalHeight = 0;
    get wasResized() {
      return (this.originalWidth || 0) > this.width || (this.originalHeight || 0) > this.height;
    }
    constructor(file, width, height, originalWidth, originalHeight) {
      super([file], file.name, {
        type: file.type,
        lastModified: file.lastModified
      });
      this.width = width;
      this.height = height;
      this.originalWidth = originalWidth;
      this.originalHeight = originalHeight;
    }
  };

  // packages/upload-media/build-module/utils.mjs
  var import_url = __toESM(require_url(), 1);
  var import_i18n = __toESM(require_i18n(), 1);
  function convertBlobToFile(fileOrBlob) {
    if (fileOrBlob instanceof File) {
      return fileOrBlob;
    }
    if ("name" in fileOrBlob && typeof fileOrBlob.name === "string") {
      return new File([fileOrBlob], fileOrBlob.name, {
        type: fileOrBlob.type,
        lastModified: fileOrBlob.lastModified
      });
    }
    const ext = fileOrBlob.type.split("/")[1];
    const mediaType = "application/pdf" === fileOrBlob.type ? "document" : fileOrBlob.type.split("/")[0];
    return new File([fileOrBlob], `${mediaType}.${ext}`, {
      type: fileOrBlob.type
    });
  }
  function renameFile(file, name) {
    return new File([file], name, {
      type: file.type,
      lastModified: file.lastModified
    });
  }
  function cloneFile(file) {
    return renameFile(file, file.name);
  }
  function getFileBasename(name) {
    return name.includes(".") ? name.split(".").slice(0, -1).join(".") : name;
  }

  // packages/upload-media/build-module/store/utils/index.mjs
  var vipsModulePromise;
  var vipsModule;
  function loadVipsModule() {
    if (!vipsModulePromise) {
      vipsModulePromise = import("@wordpress/vips/worker").then(
        (mod) => {
          vipsModule = mod;
          return mod;
        }
      );
    }
    return vipsModulePromise;
  }
  async function vipsConvertImageFormat(id, file, type, quality, interlaced) {
    const { vipsConvertImageFormat: convertImageFormat } = await loadVipsModule();
    const buffer = await convertImageFormat(
      id,
      await file.arrayBuffer(),
      file.type,
      type,
      quality,
      interlaced
    );
    const ext = type.split("/")[1];
    const fileName = `${getFileBasename(file.name)}.${ext}`;
    return new File([new Blob([buffer])], fileName, {
      type
    });
  }
  async function vipsHasTransparency(url) {
    const { vipsHasTransparency: hasTransparency } = await loadVipsModule();
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`Failed to fetch image: ${response.status}`);
    }
    return hasTransparency(await response.arrayBuffer());
  }
  async function vipsResizeImage(id, file, resize, smartCrop, addSuffix, signal, scaledSuffix) {
    if (signal?.aborted) {
      throw new Error("Operation aborted");
    }
    const { vipsResizeImage: resizeImage } = await loadVipsModule();
    const { buffer, width, height, originalWidth, originalHeight } = await resizeImage(
      id,
      await file.arrayBuffer(),
      file.type,
      resize,
      smartCrop
    );
    let fileName = file.name;
    const wasResized = originalWidth > width || originalHeight > height;
    if (wasResized) {
      const basename = getFileBasename(file.name);
      if (scaledSuffix) {
        fileName = file.name.replace(basename, `${basename}-scaled`);
      } else if (addSuffix) {
        fileName = file.name.replace(
          basename,
          `${basename}-${width}x${height}`
        );
      }
    }
    const resultFile = new ImageFile(
      new File(
        [new Blob([buffer], { type: file.type })],
        fileName,
        {
          type: file.type
        }
      ),
      width,
      height,
      originalWidth,
      originalHeight
    );
    return resultFile;
  }
  async function vipsRotateImage(id, file, orientation, signal) {
    if (signal?.aborted) {
      throw new Error("Operation aborted");
    }
    if (orientation === 1) {
      return file;
    }
    const { vipsRotateImage: rotateImage } = await loadVipsModule();
    const { buffer, width, height } = await rotateImage(
      id,
      await file.arrayBuffer(),
      file.type,
      orientation
    );
    const basename = getFileBasename(file.name);
    const fileName = file.name.replace(basename, `${basename}-rotated`);
    const resultFile = new ImageFile(
      new File(
        [new Blob([buffer], { type: file.type })],
        fileName,
        {
          type: file.type
        }
      ),
      width,
      height
    );
    return resultFile;
  }
  async function vipsCancelOperations(id) {
    if (!vipsModule) {
      return false;
    }
    return vipsModule.vipsCancelOperations(id);
  }
  function terminateVipsWorker() {
    if (vipsModule) {
      vipsModule.terminateVipsWorker();
    }
  }

  // packages/upload-media/build-module/validate-mime-type.mjs
  var import_i18n2 = __toESM(require_i18n(), 1);

  // packages/upload-media/build-module/upload-error.mjs
  var UploadError = class extends Error {
    code;
    file;
    constructor({ code, message, file, cause }) {
      super(message, { cause });
      Object.setPrototypeOf(this, new.target.prototype);
      this.code = code;
      this.file = file;
    }
  };

  // packages/upload-media/build-module/validate-mime-type.mjs
  function validateMimeType(file, allowedTypes) {
    if (!allowedTypes) {
      return;
    }
    const isAllowedType = allowedTypes.some((allowedType) => {
      if (allowedType.includes("/")) {
        return allowedType === file.type;
      }
      return file.type.startsWith(`${allowedType}/`);
    });
    if (file.type && !isAllowedType) {
      throw new UploadError({
        code: "MIME_TYPE_NOT_SUPPORTED",
        message: (0, import_i18n2.sprintf)(
          // translators: %s: file name.
          (0, import_i18n2.__)("%s: Sorry, this file type is not supported here."),
          file.name
        ),
        file
      });
    }
  }

  // packages/upload-media/build-module/validate-mime-type-for-user.mjs
  var import_i18n3 = __toESM(require_i18n(), 1);

  // packages/upload-media/build-module/get-mime-types-array.mjs
  function getMimeTypesArray(wpMimeTypesObject) {
    if (!wpMimeTypesObject) {
      return null;
    }
    return Object.entries(wpMimeTypesObject).flatMap(
      ([extensionsString, mime]) => {
        const [type] = mime.split("/");
        const extensions = extensionsString.split("|");
        return [
          mime,
          ...extensions.map(
            (extension) => `${type}/${extension}`
          )
        ];
      }
    );
  }

  // packages/upload-media/build-module/validate-mime-type-for-user.mjs
  function validateMimeTypeForUser(file, wpAllowedMimeTypes) {
    const allowedMimeTypesForUser = getMimeTypesArray(wpAllowedMimeTypes);
    if (!allowedMimeTypesForUser) {
      return;
    }
    const isAllowedMimeTypeForUser = allowedMimeTypesForUser.includes(
      file.type
    );
    if (file.type && !isAllowedMimeTypeForUser) {
      throw new UploadError({
        code: "MIME_TYPE_NOT_ALLOWED_FOR_USER",
        message: (0, import_i18n3.sprintf)(
          // translators: %s: file name.
          (0, import_i18n3.__)(
            "%s: Sorry, you are not allowed to upload this file type."
          ),
          file.name
        ),
        file
      });
    }
  }

  // packages/upload-media/build-module/validate-file-size.mjs
  var import_i18n4 = __toESM(require_i18n(), 1);
  function validateFileSize(file, maxUploadFileSize) {
    if (file.size <= 0) {
      throw new UploadError({
        code: "EMPTY_FILE",
        message: (0, import_i18n4.sprintf)(
          // translators: %s: file name.
          (0, import_i18n4.__)("%s: This file is empty."),
          file.name
        ),
        file
      });
    }
    if (maxUploadFileSize && file.size > maxUploadFileSize) {
      throw new UploadError({
        code: "SIZE_ABOVE_LIMIT",
        message: (0, import_i18n4.sprintf)(
          // translators: %s: file name.
          (0, import_i18n4.__)(
            "%s: This file exceeds the maximum upload size for this site."
          ),
          file.name
        ),
        file
      });
    }
  }

  // packages/upload-media/build-module/store/actions.mjs
  function addItems({
    files,
    onChange,
    onSuccess,
    onError,
    onBatchSuccess,
    additionalData,
    allowedTypes
  }) {
    return async ({ select: select2, dispatch }) => {
      const batchId = v4_default();
      for (const file of files) {
        try {
          validateMimeType(file, allowedTypes);
          validateMimeTypeForUser(
            file,
            select2.getSettings().allowedMimeTypes
          );
        } catch (error) {
          onError?.(error);
          continue;
        }
        try {
          validateFileSize(
            file,
            select2.getSettings().maxUploadFileSize
          );
        } catch (error) {
          onError?.(error);
          continue;
        }
        dispatch.addItem({
          file,
          batchId,
          onChange,
          onSuccess,
          onBatchSuccess,
          onError,
          additionalData
        });
      }
    };
  }
  function cancelItem(id, error, silent = false) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getItem(id);
      if (!item) {
        return;
      }
      item.abortController?.abort();
      await vipsCancelOperations(id);
      if (!silent) {
        const { onError } = item;
        onError?.(error ?? new Error("Upload cancelled"));
        if (!onError && error) {
          console.error("Upload cancelled", error);
        }
      }
      dispatch({
        type: Type.Cancel,
        id,
        error
      });
      dispatch.removeItem(id);
      dispatch.revokeBlobUrls(id);
      if (item.batchId && select2.isBatchUploaded(item.batchId)) {
        item.onBatchSuccess?.();
      }
    };
  }
  function retryItem(id) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getItem(id);
      if (!item) {
        return;
      }
      if (!item.error) {
        return;
      }
      dispatch({
        type: Type.RetryItem,
        id
      });
      dispatch.processItem(id);
    };
  }

  // packages/upload-media/build-module/store/private-actions.mjs
  var private_actions_exports = {};
  __export(private_actions_exports, {
    addItem: () => addItem,
    addSideloadItem: () => addSideloadItem,
    finishOperation: () => finishOperation,
    generateThumbnails: () => generateThumbnails,
    getTranscodeImageOperation: () => getTranscodeImageOperation,
    pauseItem: () => pauseItem,
    pauseQueue: () => pauseQueue,
    prepareItem: () => prepareItem,
    processItem: () => processItem,
    removeItem: () => removeItem,
    resizeCropItem: () => resizeCropItem,
    resumeItemByPostId: () => resumeItemByPostId,
    resumeQueue: () => resumeQueue,
    revokeBlobUrls: () => revokeBlobUrls,
    rotateItem: () => rotateItem,
    sideloadItem: () => sideloadItem,
    transcodeImageItem: () => transcodeImageItem,
    updateItemProgress: () => updateItemProgress,
    updateSettings: () => updateSettings,
    uploadItem: () => uploadItem
  });
  var import_blob = __toESM(require_blob(), 1);

  // packages/upload-media/build-module/stub-file.mjs
  var StubFile = class extends File {
    constructor(fileName = "stub-file") {
      super([], fileName);
    }
  };

  // packages/upload-media/build-module/store/private-actions.mjs
  var DEFAULT_OUTPUT_QUALITY = 0.82;
  function shouldPauseForSideload(item, operation, select2) {
    if (operation !== OperationType.Upload || !item.parentId || !item.additionalData.post) {
      return false;
    }
    return select2.isUploadingToPost(item.additionalData.post);
  }
  function addItem({
    file: fileOrBlob,
    batchId,
    onChange,
    onSuccess,
    onBatchSuccess,
    onError,
    additionalData = {},
    sourceUrl,
    sourceAttachmentId,
    abortController,
    operations
  }) {
    return async ({ dispatch }) => {
      const itemId = v4_default();
      const file = convertBlobToFile(fileOrBlob);
      let blobUrl;
      if (!(file instanceof StubFile)) {
        blobUrl = (0, import_blob.createBlobURL)(file);
        dispatch({
          type: Type.CacheBlobUrl,
          id: itemId,
          blobUrl
        });
      }
      dispatch({
        type: Type.Add,
        item: {
          id: itemId,
          batchId,
          status: ItemStatus.Processing,
          sourceFile: cloneFile(file),
          file,
          attachment: {
            url: blobUrl
          },
          additionalData: {
            convert_format: false,
            generate_sub_sizes: false,
            ...additionalData
          },
          onChange,
          onSuccess,
          onBatchSuccess,
          onError,
          sourceUrl,
          sourceAttachmentId,
          abortController: abortController || new AbortController(),
          operations: Array.isArray(operations) ? operations : [OperationType.Prepare]
        }
      });
      dispatch.processItem(itemId);
    };
  }
  function addSideloadItem({
    file,
    onChange,
    additionalData,
    operations,
    batchId,
    parentId
  }) {
    return ({ dispatch }) => {
      const itemId = v4_default();
      dispatch({
        type: Type.Add,
        item: {
          id: itemId,
          batchId,
          status: ItemStatus.Processing,
          sourceFile: cloneFile(file),
          file,
          onChange,
          additionalData: {
            ...additionalData
          },
          parentId,
          operations: Array.isArray(operations) ? operations : [OperationType.Prepare],
          abortController: new AbortController()
        }
      });
      dispatch.processItem(itemId);
    };
  }
  function processItem(id) {
    return async ({ select: select2, dispatch }) => {
      if (select2.isPaused()) {
        return;
      }
      const item = select2.getItem(id);
      if (!item) {
        return;
      }
      const {
        attachment,
        onChange,
        onSuccess,
        onBatchSuccess,
        batchId,
        parentId
      } = item;
      const operation = Array.isArray(item.operations?.[0]) ? item.operations[0][0] : item.operations?.[0];
      const operationArgs = Array.isArray(item.operations?.[0]) ? item.operations[0][1] : void 0;
      if (shouldPauseForSideload(item, operation, select2)) {
        dispatch({
          type: Type.PauseItem,
          id
        });
        return;
      }
      if (operation === OperationType.Upload) {
        const settings = select2.getSettings();
        const activeCount = select2.getActiveUploadCount();
        if (activeCount >= settings.maxConcurrentUploads) {
          return;
        }
      }
      if (operation === OperationType.ResizeCrop || operation === OperationType.Rotate) {
        const settings = select2.getSettings();
        const activeCount = select2.getActiveImageProcessingCount();
        if (activeCount >= settings.maxConcurrentImageProcessing) {
          return;
        }
      }
      if (attachment) {
        onChange?.([attachment]);
      }
      if (!operation) {
        if (parentId || !parentId && !select2.hasPendingItemsByParentId(id)) {
          if (attachment) {
            onSuccess?.([attachment]);
          }
          dispatch.removeItem(id);
          dispatch.revokeBlobUrls(id);
          if (batchId && select2.isBatchUploaded(batchId)) {
            onBatchSuccess?.();
          }
        }
        if (parentId && batchId && select2.isBatchUploaded(batchId)) {
          const parentItem = select2.getItem(parentId);
          if (!parentItem) {
            return;
          }
          if (attachment) {
            parentItem.onSuccess?.([attachment]);
          }
          dispatch.removeItem(parentId);
          dispatch.revokeBlobUrls(parentId);
          if (parentItem.batchId && select2.isBatchUploaded(parentItem.batchId)) {
            parentItem.onBatchSuccess?.();
          }
        }
        return;
      }
      dispatch({
        type: Type.OperationStart,
        id,
        operation
      });
      switch (operation) {
        case OperationType.Prepare:
          dispatch.prepareItem(item.id);
          break;
        case OperationType.ResizeCrop:
          dispatch.resizeCropItem(
            item.id,
            operationArgs
          );
          break;
        case OperationType.Rotate:
          dispatch.rotateItem(
            item.id,
            operationArgs
          );
          break;
        case OperationType.TranscodeImage:
          dispatch.transcodeImageItem(
            item.id,
            operationArgs
          );
          break;
        case OperationType.Upload:
          if (item.parentId) {
            dispatch.sideloadItem(id);
          } else {
            dispatch.uploadItem(id);
          }
          break;
        case OperationType.ThumbnailGeneration:
          dispatch.generateThumbnails(id);
          break;
      }
    };
  }
  function pauseQueue() {
    return {
      type: Type.PauseQueue
    };
  }
  function resumeQueue() {
    return async ({ select: select2, dispatch }) => {
      dispatch({
        type: Type.ResumeQueue
      });
      for (const item of select2.getAllItems()) {
        dispatch.processItem(item.id);
      }
    };
  }
  function pauseItem(id) {
    return async ({ dispatch }) => {
      dispatch({
        type: Type.PauseItem,
        id
      });
    };
  }
  function resumeItemByPostId(postOrAttachmentId) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getPausedUploadForPost(postOrAttachmentId);
      if (item) {
        dispatch({
          type: Type.ResumeItem,
          id: item.id
        });
        dispatch.processItem(item.id);
      }
    };
  }
  function removeItem(id) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getItem(id);
      if (!item) {
        return;
      }
      dispatch({
        type: Type.Remove,
        id
      });
      if (select2.getAllItems().length === 0) {
        terminateVipsWorker();
      }
    };
  }
  function finishOperation(id, updates) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getItem(id);
      const previousOperation = item?.currentOperation;
      dispatch({
        type: Type.OperationFinish,
        id,
        item: updates
      });
      dispatch.processItem(id);
      if (previousOperation === OperationType.Upload) {
        const pendingUploads = select2.getPendingUploads();
        for (const pendingItem of pendingUploads) {
          dispatch.processItem(pendingItem.id);
        }
      }
      if (previousOperation === OperationType.ResizeCrop || previousOperation === OperationType.Rotate) {
        const pendingItems = select2.getPendingImageProcessing();
        for (const pendingItem of pendingItems) {
          dispatch.processItem(pendingItem.id);
        }
      }
    };
  }
  var VALID_IMAGE_FORMATS = ["jpeg", "webp", "avif", "png", "gif"];
  function isValidImageFormat(format) {
    return VALID_IMAGE_FORMATS.includes(format);
  }
  function getInterlacedSetting(outputMimeType, settings) {
    switch (outputMimeType) {
      case "image/jpeg":
        return settings.jpegInterlaced ?? false;
      case "image/png":
        return settings.pngInterlaced ?? false;
      case "image/gif":
        return settings.gifInterlaced ?? false;
      default:
        return false;
    }
  }
  async function getTranscodeImageOperation(file, outputMimeType, settings) {
    if (file.type === "image/png" && outputMimeType === "image/jpeg") {
      const blobUrl = (0, import_blob.createBlobURL)(file);
      try {
        const hasAlpha = await vipsHasTransparency(blobUrl);
        if (hasAlpha) {
          return null;
        }
      } catch {
        return null;
      } finally {
        (0, import_blob.revokeBlobURL)(blobUrl);
      }
    }
    const formatPart = outputMimeType.split("/")[1];
    if (!isValidImageFormat(formatPart)) {
      return null;
    }
    return [
      OperationType.TranscodeImage,
      {
        outputFormat: formatPart,
        outputQuality: DEFAULT_OUTPUT_QUALITY,
        interlaced: getInterlacedSetting(outputMimeType, settings)
      }
    ];
  }
  function prepareItem(id) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getItem(id);
      if (!item) {
        return;
      }
      const { file } = item;
      const operations = [];
      const settings = select2.getSettings();
      const isImage = file.type.startsWith("image/");
      const isVipsSupported = CLIENT_SIDE_SUPPORTED_MIME_TYPES.includes(
        file.type
      );
      if (isImage && isVipsSupported) {
        const { imageOutputFormats } = settings;
        const outputMimeType = imageOutputFormats?.[file.type];
        if (outputMimeType && outputMimeType !== file.type) {
          const transcodeOperation = await getTranscodeImageOperation(
            file,
            outputMimeType,
            settings
          );
          if (transcodeOperation) {
            operations.push(transcodeOperation);
          }
        }
        operations.push(
          OperationType.Upload,
          OperationType.ThumbnailGeneration
        );
      } else {
        operations.push(OperationType.Upload);
      }
      dispatch({
        type: Type.AddOperations,
        id,
        operations
      });
      const updates = !isVipsSupported || !isImage ? {
        additionalData: {
          ...item.additionalData,
          generate_sub_sizes: true
        }
      } : {};
      dispatch.finishOperation(id, updates);
    };
  }
  function uploadItem(id) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getItem(id);
      if (!item) {
        return;
      }
      select2.getSettings().mediaUpload({
        filesList: [item.file],
        additionalData: item.additionalData,
        signal: item.abortController?.signal,
        onFileChange: ([attachment]) => {
          if (!(0, import_blob.isBlobURL)(attachment.url)) {
            dispatch.finishOperation(id, {
              attachment
            });
          }
        },
        onSuccess: ([attachment]) => {
          dispatch.finishOperation(id, {
            attachment
          });
        },
        onError: (error) => {
          dispatch.cancelItem(id, error);
        }
      });
    };
  }
  function sideloadItem(id) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getItem(id);
      if (!item) {
        return;
      }
      const { post, ...additionalData } = item.additionalData;
      const mediaSideload = select2.getSettings().mediaSideload;
      if (!mediaSideload) {
        dispatch.finishOperation(id, {});
        return;
      }
      mediaSideload({
        file: item.file,
        attachmentId: post,
        additionalData,
        signal: item.abortController?.signal,
        onFileChange: ([attachment]) => {
          dispatch.finishOperation(id, { attachment });
          dispatch.resumeItemByPostId(post);
        },
        onError: (error) => {
          dispatch.cancelItem(id, error);
          dispatch.resumeItemByPostId(post);
        }
      });
    };
  }
  function resizeCropItem(id, args) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getItem(id);
      if (!item) {
        return;
      }
      if (!args?.resize) {
        dispatch.finishOperation(id, {
          file: item.file
        });
        return;
      }
      const addSuffix = Boolean(item.parentId);
      const scaledSuffix = Boolean(args.isThresholdResize);
      try {
        const file = await vipsResizeImage(
          item.id,
          item.file,
          args.resize,
          false,
          // smartCrop
          addSuffix,
          item.abortController?.signal,
          scaledSuffix
        );
        const blobUrl = (0, import_blob.createBlobURL)(file);
        dispatch({
          type: Type.CacheBlobUrl,
          id,
          blobUrl
        });
        dispatch.finishOperation(id, {
          file,
          attachment: {
            url: blobUrl
          }
        });
      } catch (error) {
        dispatch.cancelItem(
          id,
          new UploadError({
            code: "IMAGE_TRANSCODING_ERROR",
            message: "File could not be uploaded",
            file: item.file,
            cause: error instanceof Error ? error : void 0
          })
        );
      }
    };
  }
  function rotateItem(id, args) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getItem(id);
      if (!item) {
        return;
      }
      if (!args?.orientation || args.orientation === 1) {
        dispatch.finishOperation(id, {
          file: item.file
        });
        return;
      }
      try {
        const file = await vipsRotateImage(
          item.id,
          item.file,
          args.orientation,
          item.abortController?.signal
        );
        const blobUrl = (0, import_blob.createBlobURL)(file);
        dispatch({
          type: Type.CacheBlobUrl,
          id,
          blobUrl
        });
        dispatch.finishOperation(id, {
          file,
          attachment: {
            url: blobUrl
          }
        });
      } catch (error) {
        dispatch.cancelItem(
          id,
          new UploadError({
            code: "IMAGE_ROTATION_ERROR",
            message: "Image could not be rotated",
            file: item.file,
            cause: error instanceof Error ? error : void 0
          })
        );
      }
    };
  }
  function transcodeImageItem(id, args) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getItem(id);
      if (!item) {
        return;
      }
      if (!args?.outputFormat) {
        dispatch.finishOperation(id, {
          file: item.file
        });
        return;
      }
      const outputMimeType = `image/${args.outputFormat}`;
      const quality = args.outputQuality ?? DEFAULT_OUTPUT_QUALITY;
      const interlaced = args.interlaced ?? false;
      try {
        const file = await vipsConvertImageFormat(
          item.id,
          item.file,
          outputMimeType,
          quality,
          interlaced
        );
        const blobUrl = (0, import_blob.createBlobURL)(file);
        dispatch({
          type: Type.CacheBlobUrl,
          id,
          blobUrl
        });
        dispatch.finishOperation(id, {
          file,
          attachment: {
            url: blobUrl
          }
        });
      } catch (error) {
        dispatch.cancelItem(
          id,
          new UploadError({
            code: "MEDIA_TRANSCODING_ERROR",
            message: "Image could not be transcoded to the target format",
            file: item.file,
            cause: error instanceof Error ? error : void 0
          })
        );
      }
    };
  }
  function generateThumbnails(id) {
    return async ({ select: select2, dispatch }) => {
      const item = select2.getItem(id);
      if (!item) {
        return;
      }
      if (!item.attachment) {
        dispatch.finishOperation(id, {});
        return;
      }
      const attachment = item.attachment;
      const needsRotation = attachment.exif_orientation && attachment.exif_orientation !== 1 && !item.file.name.includes("-scaled");
      if (needsRotation && attachment.id) {
        try {
          const rotatedFile = await vipsRotateImage(
            item.id,
            item.sourceFile,
            attachment.exif_orientation,
            item.abortController?.signal
          );
          dispatch.addSideloadItem({
            file: rotatedFile,
            batchId: v4_default(),
            parentId: item.id,
            additionalData: {
              post: attachment.id,
              image_size: "original",
              convert_format: false
            },
            operations: [OperationType.Upload]
          });
        } catch {
          console.warn(
            "Failed to rotate image, continuing with thumbnails"
          );
        }
      }
      if (!item.parentId && attachment.missing_image_sizes && attachment.missing_image_sizes.length > 0) {
        const file = attachment.filename ? renameFile(item.sourceFile, attachment.filename) : item.sourceFile;
        const batchId = v4_default();
        const settings = select2.getSettings();
        const allImageSizes = settings.allImageSizes || {};
        const { imageOutputFormats } = settings;
        const sourceType = item.sourceFile.type;
        const outputMimeType = imageOutputFormats?.[sourceType];
        let thumbnailTranscodeOperation = null;
        if (outputMimeType && outputMimeType !== sourceType) {
          thumbnailTranscodeOperation = await getTranscodeImageOperation(
            item.sourceFile,
            outputMimeType,
            settings
          );
        }
        for (const name of attachment.missing_image_sizes) {
          const imageSize = allImageSizes[name];
          if (!imageSize) {
            console.warn(
              `Image size "${name}" not found in configuration`
            );
            continue;
          }
          const thumbnailOperations = [
            [OperationType.ResizeCrop, { resize: imageSize }]
          ];
          if (thumbnailTranscodeOperation) {
            thumbnailOperations.push(thumbnailTranscodeOperation);
          }
          thumbnailOperations.push(OperationType.Upload);
          dispatch.addSideloadItem({
            file,
            onChange: ([updatedAttachment]) => {
              if ((0, import_blob.isBlobURL)(updatedAttachment.url)) {
                return;
              }
              item.onChange?.([updatedAttachment]);
            },
            batchId,
            parentId: item.id,
            additionalData: {
              // Sideloading does not use the parent post ID but the
              // attachment ID as the image sizes need to be added to it.
              post: attachment.id,
              image_size: name,
              convert_format: false
            },
            operations: thumbnailOperations
          });
        }
        const { bigImageSizeThreshold } = settings;
        if (bigImageSizeThreshold && attachment.id) {
          const sourceForScaled = attachment.filename ? renameFile(item.sourceFile, attachment.filename) : item.sourceFile;
          const scaledOperations = [
            [
              OperationType.ResizeCrop,
              {
                resize: {
                  width: bigImageSizeThreshold,
                  height: bigImageSizeThreshold
                },
                isThresholdResize: true
              }
            ]
          ];
          if (thumbnailTranscodeOperation) {
            scaledOperations.push(thumbnailTranscodeOperation);
          }
          scaledOperations.push(OperationType.Upload);
          dispatch.addSideloadItem({
            file: sourceForScaled,
            onChange: ([updatedAttachment]) => {
              if ((0, import_blob.isBlobURL)(updatedAttachment.url)) {
                return;
              }
              item.onChange?.([updatedAttachment]);
            },
            batchId,
            parentId: item.id,
            additionalData: {
              post: attachment.id,
              image_size: "scaled",
              convert_format: false
            },
            operations: scaledOperations
          });
        }
      }
      dispatch.finishOperation(id, {});
    };
  }
  function revokeBlobUrls(id) {
    return async ({ select: select2, dispatch }) => {
      const blobUrls = select2.getBlobUrls(id);
      for (const blobUrl of blobUrls) {
        (0, import_blob.revokeBlobURL)(blobUrl);
      }
      dispatch({
        type: Type.RevokeBlobUrls,
        id
      });
    };
  }
  function updateItemProgress(id, progress) {
    return async ({ dispatch }) => {
      dispatch({
        type: Type.UpdateProgress,
        id,
        progress
      });
    };
  }
  function updateSettings(settings) {
    return {
      type: Type.UpdateSettings,
      settings
    };
  }

  // packages/upload-media/build-module/lock-unlock.mjs
  var import_private_apis = __toESM(require_private_apis(), 1);
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/upload-media"
  );

  // packages/upload-media/build-module/store/index.mjs
  var storeConfig = {
    reducer: reducer_default,
    selectors: selectors_exports,
    actions: actions_exports
  };
  var store = (0, import_data.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    selectors: selectors_exports,
    actions: actions_exports
  });
  if (!(0, import_data.select)(store)) {
    (0, import_data.register)(store);
  }
  unlock(store).registerPrivateActions(private_actions_exports);
  unlock(store).registerPrivateSelectors(private_selectors_exports);

  // packages/upload-media/build-module/components/provider/index.mjs
  var import_element2 = __toESM(require_element(), 1);
  var import_data3 = __toESM(require_data(), 1);

  // packages/upload-media/build-module/components/provider/with-registry-provider.mjs
  var import_element = __toESM(require_element(), 1);
  var import_data2 = __toESM(require_data(), 1);
  var import_compose = __toESM(require_compose(), 1);
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  function getSubRegistry(subRegistries, registry, useSubRegistry) {
    if (!useSubRegistry) {
      return registry;
    }
    let subRegistry = subRegistries.get(registry);
    if (!subRegistry) {
      subRegistry = (0, import_data2.createRegistry)({}, registry);
      subRegistry.registerStore(STORE_NAME, storeConfig);
      subRegistries.set(registry, subRegistry);
    }
    return subRegistry;
  }
  var withRegistryProvider = (0, import_compose.createHigherOrderComponent)(
    (WrappedComponent) => ({ useSubRegistry = true, ...props }) => {
      const registry = (0, import_data2.useRegistry)();
      const [subRegistries] = (0, import_element.useState)(() => /* @__PURE__ */ new WeakMap());
      const subRegistry = getSubRegistry(
        subRegistries,
        registry,
        useSubRegistry
      );
      if (subRegistry === registry) {
        return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(WrappedComponent, { registry, ...props });
      }
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_data2.RegistryProvider, { value: subRegistry, children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(WrappedComponent, { registry: subRegistry, ...props }) });
    },
    "withRegistryProvider"
  );
  var with_registry_provider_default = withRegistryProvider;

  // packages/upload-media/build-module/components/provider/index.mjs
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
  var MediaUploadProvider = with_registry_provider_default((props) => {
    const { children, settings } = props;
    const { updateSettings: updateSettings2 } = unlock((0, import_data3.useDispatch)(store));
    (0, import_element2.useEffect)(() => {
      updateSettings2(settings);
    }, [settings, updateSettings2]);
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_jsx_runtime2.Fragment, { children });
  });
  var provider_default = MediaUploadProvider;

  // packages/upload-media/build-module/feature-detection.mjs
  var cachedResult = null;
  function detectClientSideMediaSupport() {
    if (cachedResult !== null) {
      return cachedResult;
    }
    if (typeof WebAssembly === "undefined") {
      cachedResult = {
        supported: false,
        reason: "WebAssembly is not supported in this browser."
      };
      return cachedResult;
    }
    if (typeof SharedArrayBuffer === "undefined") {
      cachedResult = {
        supported: false,
        reason: "SharedArrayBuffer is not available. This may be due to missing cross-origin isolation headers."
      };
      return cachedResult;
    }
    if (typeof Worker === "undefined") {
      cachedResult = {
        supported: false,
        reason: "Web Workers are not supported in this browser."
      };
      return cachedResult;
    }
    if (typeof window !== "undefined" && window.HTMLIFrameElement && !("credentialless" in window.HTMLIFrameElement.prototype)) {
      cachedResult = {
        supported: false,
        reason: "Browser does not support credentialless iframes. Cross-origin isolation would break third-party embeds"
      };
      return cachedResult;
    }
    if (typeof navigator !== "undefined" && "deviceMemory" in navigator && navigator.deviceMemory <= 2) {
      cachedResult = {
        supported: false,
        reason: "Device has insufficient memory for client-side media processing."
      };
      return cachedResult;
    }
    if (typeof navigator !== "undefined" && "hardwareConcurrency" in navigator && navigator.hardwareConcurrency < 4) {
      cachedResult = {
        supported: false,
        reason: "Device has insufficient CPU cores for client-side media processing."
      };
      return cachedResult;
    }
    if (typeof navigator !== "undefined") {
      const connection = navigator.connection;
      if (connection) {
        if (connection.saveData) {
          cachedResult = {
            supported: false,
            reason: "Data saver mode is enabled."
          };
          return cachedResult;
        }
        if (connection.effectiveType === "slow-2g" || connection.effectiveType === "2g" || connection.effectiveType === "3g") {
          cachedResult = {
            supported: false,
            reason: "Network connection is too slow for client-side media processing."
          };
          return cachedResult;
        }
      }
    }
    if (typeof window !== "undefined") {
      try {
        const testBlob = new Blob([""], {
          type: "application/javascript"
        });
        const testUrl = URL.createObjectURL(testBlob);
        try {
          const testWorker = new Worker(testUrl);
          testWorker.terminate();
        } finally {
          URL.revokeObjectURL(testUrl);
        }
      } catch {
        cachedResult = {
          supported: false,
          reason: "The site's Content Security Policy (CSP) does not allow blob: workers. The worker-src directive must include blob: to enable client-side media processing."
        };
        return cachedResult;
      }
    }
    cachedResult = { supported: true };
    return cachedResult;
  }
  function isClientSideMediaSupported() {
    return detectClientSideMediaSupport().supported;
  }
  function clearFeatureDetectionCache() {
    cachedResult = null;
  }
  return __toCommonJS(index_exports);
})();