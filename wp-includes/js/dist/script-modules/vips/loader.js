// packages/vips/build-module/loader.mjs
function loader() {
  return import("@wordpress/vips/worker");
}
export {
  loader as default
};