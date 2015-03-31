/*globals wp */

/**
 * wp.media.view.UploaderStatusError
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var UploaderStatusError = wp.media.View.extend({
	className: 'upload-error',
	template:  wp.template('uploader-status-error')
});

module.exports = UploaderStatusError;
