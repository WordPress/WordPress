// packages/block-library/build-module/file/view.js
import { store } from "@wordpress/interactivity";

// packages/block-library/build-module/file/utils/index.js
var browserSupportsPdfs = () => {
  if (window.navigator.pdfViewerEnabled) {
    return true;
  }
  if (window.navigator.userAgent.indexOf("Mobi") > -1) {
    return false;
  }
  if (window.navigator.userAgent.indexOf("Android") > -1) {
    return false;
  }
  if (window.navigator.userAgent.indexOf("Macintosh") > -1 && window.navigator.maxTouchPoints && window.navigator.maxTouchPoints > 2) {
    return false;
  }
  if (!!(window.ActiveXObject || "ActiveXObject" in window) && !(createActiveXObject("AcroPDF.PDF") || createActiveXObject("PDF.PdfCtrl"))) {
    return false;
  }
  return true;
};
var createActiveXObject = (type) => {
  let ax;
  try {
    ax = new window.ActiveXObject(type);
  } catch (e) {
    ax = void 0;
  }
  return ax;
};

// packages/block-library/build-module/file/view.js
store(
  "core/file",
  {
    state: {
      get hasPdfPreview() {
        return browserSupportsPdfs();
      }
    }
  },
  { lock: true }
);
//# sourceMappingURL=view.js.map
