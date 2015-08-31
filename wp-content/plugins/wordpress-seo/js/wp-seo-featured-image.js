/* global wp */
/* global removeThumb:true */
/* global wpseoMetaboxL10n */
/* jshint -W097 */
/* jshint -W003 */
'use strict';
jQuery( document ).ready( function() {
		var featuredImage = wp.media.featuredImage.frame();
		featuredImage.on( 'select', function() {
				yst_checkFeaturedImage( featuredImage );
			}
		);
	}
);

/**
 * Check if image is smaller than 200x200 pixels. If this is the case, show a warning
 * @param {object} featuredImage
 */
function yst_checkFeaturedImage( featuredImage ) {
	var attachment = featuredImage.state().get( 'selection' ).first().toJSON();

	if ( attachment.width < 200 || attachment.height < 200 ) {
		//Show warning to user and do not add image to OG
		if ( !document.getElementById( 'yst_opengraph_image_warning' ) ) {
			jQuery( '<div id="yst_opengraph_image_warning"><p>' + wpseoMetaboxL10n.featured_image_notice + '</p></div>' ).insertBefore( '#postimagediv' );
			document.getElementById( 'postimagediv' ).style.border = 'solid #dd3d36';
			document.getElementById( 'postimagediv' ).style.borderWidth = '3px 4px 4px 4px';
		}
	}

	yst_overrideElemFunction();
}

/**
 * Counter to make sure we do not end up in an endless loop if there' no remove-post-thumbnail id
 * @type {number}
 */
var thumbIdCounter = 0;

/**
 * Variable to hold the onclick function for remove-post-thumbnail.
 * @type {function}
 */
var removeThumb;

/**
 * If there's a remove-post-thumbnail id, add an onclick. When this id is clicked, call yst_removeOpengraphWarning
 * If not, check again after 100ms. Do not do this for more than 10 times so we do not end up in an endless loop
 */
function yst_overrideElemFunction() {
	if ( document.getElementById( 'remove-post-thumbnail' ) != null ) {
		thumbIdCounter = 0;
		removeThumb = document.getElementById( 'remove-post-thumbnail' ).onclick; // This variable is needed for core functionality to work
		document.getElementById( 'remove-post-thumbnail' ).onclick = yst_removeOpengraphWarning;
	}
	else {
		thumbIdCounter++;
		if ( thumbIdCounter < 10 ) {
			setTimeout( yst_overrideElemFunction, 100 );
		}
	}
}

/**
 * Remove error message
 */
function yst_removeOpengraphWarning() {
	jQuery( '#yst_opengraph_image_warning' ).remove();
	document.getElementById( 'postimagediv' ).style.border = 'none';

	//Make sure the original function does its work
	removeThumb();
}
