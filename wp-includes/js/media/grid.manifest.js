/*globals wp */

var media = wp.media;

media.controller.EditAttachmentMetadata = require( './controllers/edit-attachment-metadata.js' );
media.view.MediaFrame.Manage = require( './views/frame/manage.js' );
media.view.Attachment.Details.TwoColumn = require( './views/attachment/details-two-column.js' );
media.view.MediaFrame.Manage.Router = require( './routers/manage.js' );
media.view.EditImage.Details = require( './views/edit-image-details.js' );
media.view.MediaFrame.EditAttachments = require( './views/frame/edit-attachments.js' );
media.view.SelectModeToggleButton = require( './views/button/select-mode-toggle.js' );
media.view.DeleteSelectedButton = require( './views/button/delete-selected.js' );
media.view.DeleteSelectedPermanentlyButton = require( './views/button/delete-selected-permanently.js' );
