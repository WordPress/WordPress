<?php require_once('admin.php'); cache_javascript_headers(); ?>
addLoadEvent( function() {
	theFileList = {
		currentImage: {ID: 0},
		nonce: '',
		tab: '',
		postID: 0,

		initializeVars: function() {
			this.urlData  = document.location.href.split('?');
			this.params = this.urlData[1].toQueryParams();
			this.postID = this.params['post_id'];
			this.tab = this.params['tab'];
			this.style = this.params['style'];
			this.ID = this.params['ID'];
			if ( !this.style )
				this.style = 'default';
			var nonceEl = $('nonce-value');
			if ( nonceEl )
				this.nonce = nonceEl.value;
			if ( this.ID ) {
				this.grabImageData( this.ID );
				this.imageView( this.ID );
			}
		},

		initializeLinks: function() {
			if ( this.ID )
				return;
			$$('a.file-link').each( function(i) {
				var id = i.id.split('-').pop();
				i.onclick = function(e) { theFileList[ 'inline' == theFileList.style ? 'imageView' : 'editView' ](id, e); }
			} );
		},

		grabImageData: function(id) {
			if ( id == this.currentImage.ID )
				return;
			var thumbEl = $('attachment-thumb-url-' + id);
			this.currentImage.isImage = true;
			if ( thumbEl ) {
				this.currentImage.thumb = ( 0 == id ? '' : thumbEl.value );
				this.currentImage.thumbBase = ( 0 == id ? '' : $('attachment-thumb-url-base-' + id).value );
			} else {
				this.currentImage.thumb = false;
				var isImageEl = $('attachment-is-image-' + id);
				if ( !isImageEl )
					this.currentImage.isImage = false;
			}
			this.currentImage.src = ( 0 == id ? '' : $('attachment-url-' + id).value );
			this.currentImage.srcBase = ( 0 == id ? '' : $('attachment-url-base-' + id).value );
			this.currentImage.page = ( 0 == id ? '' : $('attachment-page-url-' + id).value );
			this.currentImage.title = ( 0 == id ? '' : $('attachment-title-' + id).value );
			this.currentImage.description = ( 0 == id ? '' : $('attachment-description-' + id).value );
			var widthEl = $('attachment-width-' + id);
			if ( widthEl ) {
				this.currentImage.width = ( 0 == id ? '' : widthEl.value );
				this.currentImage.height = ( 0 == id ? '' : $('attachment-height-' + id).value );
			} else {
				this.currentImage.width = false;
				this.currentImage.height = false;
			}
			this.currentImage.ID = id;
		},

		imageView: function(id, e) {
			this.prepView(id);
			var h = '';

			h += "<div id='upload-file'>"
			if ( this.ID ) {
				var params = $H(this.params);
				params.ID = '';
				params.action = '';
				h += "<a href='" + this.urlData[0] + '?' + params.toQueryString() + "' title='<?php echo wp_specialchars(__('Browse your files'), 1); ?>' class='back'><?php echo wp_specialchars(__('&laquo; Back'), 1); ?></a>";
			} else {
				h += "<a href='#' onclick='return theFileList.cancelView();'  title='<?php echo wp_specialchars(__('Browse your files'), 1); ?>' class='back'><?php echo wp_specialchars(__('&laquo; Back'), 1) ?></a>";
			}
			h += "<div id='file-title'>"
			if ( !this.currentImage.isImage )
				h += "<h2><a href='" + this.currentImage.srcBase + this.currentImage.src + "' onclick='return false;' title='<?php echo wp_specialchars(__('Direct link to file'), 1); ?>'>" + this.currentImage.title + "</a></h2>";
			else
				h += "<h2>" + this.currentImage.title + "</h2>";
			h += " &#8212; <span>";
			h += "<a href='#' onclick='return theFileList.editView(" + id + ");'><?php echo wp_specialchars(__('Edit'), 1); ?></a>"
			h += "</span>";
			h += '</div>'
			h += "<div id='upload-file-view' class='alignleft'>";
			if ( this.currentImage.isImage ) {
				h += "<a href='" + this.currentImage.srcBase + this.currentImage.src + "' onclick='return false;' title='<?php echo wp_specialchars(__('Direct link to file'), 1); ?>'>";
				h += "<img src='" + ( this.currentImage.thumb ? this.currentImage.thumb : this.currentImage.src ) + "' alt='" + this.currentImage.title + "' width='" + this.currentImage.width + "' height='" + this.currentImage.height + "' />";
				h += "</a>";
			} else
				h += '&nbsp;';
			h += "</div>";

			h += "<form name='uploadoptions' id='uploadoptions' class='alignleft'>";
			h += "<table>";
			if ( this.currentImage.thumb ) {
				h += "<tr><th style='padding-bottom:.5em'><?php echo wp_specialchars(__('Show:'), 1); ?></th><td style='padding-bottom:.5em'>";
				h += "<label for='display-thumb'><input type='radio' name='display' id='display-thumb' value='thumb' checked='checked' /> <?php echo wp_specialchars(__('Thumbnail'), 1); ?></label><br />";
				h += "<label for='display-full'><input type='radio' name='display' id='display-full' value='full' /> <?php echo wp_specialchars(__('Full size'), 1); ?></label>";
				h += "</td></tr>";
			}

			h += "<tr><th><?php echo wp_specialchars(__('Link to:'), 1); ?></th><td>";
			h += "<label for='link-file'><input type='radio' name='link' id='link-file' value='file' checked='checked'/> <?php echo wp_specialchars(__('File'), 1); ?></label><br />";
			h += "<label for='link-page'><input type='radio' name='link' id='link-page' value='page' /> <?php echo wp_specialchars(__('Page'), 1); ?></label><br />";
			h += "<label for='link-none'><input type='radio' name='link' id='link-none' value='none' /> <?php echo wp_specialchars(__('None'), 1); ?></label>";
			h += "</td></tr>";

			h += "<tr><td colspan='2'><p class='submit'>";
			h += "<input type='button' class='button' name='send' onclick='theFileList.sendToEditor(" + id + ")' value='<?php echo wp_specialchars(__('Send to editor &raquo;'), 1); ?>' />";
			h += "</p></td></tr></table>";
			h += "</form>";

			h += "</div>";

			new Insertion.Top('upload-content', h);
			if (e) Event.stop(e);
			return false;
		},

		editView: function(id, e) {
			this.prepView(id);
			var h = '';

			var action = 'upload.php?style=' + this.style + '&amp;tab=upload';
			if ( this.postID )
				action += '&amp;post_id=' + this.postID;

			h += "<form id='upload-file' method='post' action='" + action + "'>";
			if ( this.ID ) {
				var params = $H(this.params);
				params.ID = '';
				params.action = '';
				h += "<a href='" + this.urlData[0] + '?' + params.toQueryString() + "'  title='<?php echo wp_specialchars(__('Browse your files'), 1); ?>' class='back'><?php echo wp_specialchars(__('&laquo; Back'), 1); ?></a>";
			} else {
				h += "<a href='#' onclick='return theFileList.cancelView();'  title='<?php echo wp_specialchars(__('Browse your files'), 1); ?>' class='back'><?php echo wp_specialchars(__('&laquo; Back'), 1); ?></a>";
			}
			h += "<div id='file-title'>"
			if ( !this.currentImage.isImage )
				h += "<h2><a href='" + this.currentImage.srcBase + this.currentImage.src + "' onclick='return false;' title='<?php echo wp_specialchars(__('Direct link to file'), 1); ?>'>" + this.currentImage.title + "</a></h2>";
			else
				h += "<h2>" + this.currentImage.title + "</h2>";
			h += " &#8212; <span>";
			h += "<a href='#' onclick='return theFileList.imageView(" + id + ");'><?php wp_specialchars(__('Insert'), 1); ?></a>"
			h += "</span>";
			h += '</div>'
			h += "<div id='upload-file-view' class='alignleft'>";
			if ( this.currentImage.isImage ) {
				h += "<a href='" + this.currentImage.srcBase + this.currentImage.src + "' onclick='return false;' title='<?php echo wp_specialchars(__('Direct link to file')); ?>'>";
				h += "<img src='" + ( this.currentImage.thumb ? this.currentImage.thumb : this.currentImage.src ) + "' alt='" + this.currentImage.title + "' width='" + this.currentImage.width + "' height='" + this.currentImage.height + "' />";
				h += "</a>";
			} else
				h += '&nbsp;';
			h += "</div>";


			h += "<table><col /><col class='widefat' /><tr>"
			h += "<th scope='row'><label for='url'><?php echo wp_specialchars(__('URL'), 1); ?></label></th>";
			h += "<td><input type='text' id='url' class='readonly' value='" + this.currentImage.srcBase + this.currentImage.src + "' readonly='readonly' /></td>";
			h += "</tr><tr>";
			h += "<th scope='row'><label for='post_title'><?php echo wp_specialchars(__('Title'), 1); ?></label></th>";
			h += "<td><input type='text' id='post_title' name='post_title' value='" + this.currentImage.title + "' /></td>";
			h += "</tr><tr>";
			h += "<th scope='row'><label for='post_content'><?php echo wp_specialchars(__('Description'), 1); ?></label></th>";
			h += "<td><textarea name='post_content' id='post_content'>" + this.currentImage.description + "</textarea></td>";
			h += "</tr><tr id='buttons' class='submit'><td colspan='2'><input type='button' id='delete' name='delete' class='delete alignleft' value='<?php echo wp_specialchars(__('Delete File'), 1); ?>' onclick='theFileList.deleteFile(" + id + ");' />";
			h += "<input type='hidden' name='from_tab' value='" + this.tab + "' />";
			h += "<input type='hidden' name='action' id='action-value' value='save' />";
			h += "<input type='hidden' name='ID' value='" + id + "' />";
			h += "<input type='hidden' name='_wpnonce' value='" + this.nonce + "' />";
			h += "<div class='submit'><input type='submit' value='<?php echo wp_specialchars(__('Save &raquo;'), 1); ?>' /></div>";
			h += "</td></tr></table></form>";

			new Insertion.Top('upload-content', h);
			if (e) Event.stop(e);
			return false;		
		},

		prepView: function(id) {
			this.cancelView( true );
			var filesEl = $('upload-files');
			if ( filesEl )
				filesEl.hide();
			var navEl = $('current-tab-nav');
			if ( navEl )
				navEl.hide();
			this.grabImageData(id);
		},

		cancelView: function( prep ) {
			if ( !prep ) {
				var filesEl = $('upload-files');
				if ( filesEl )
					filesEl.show();
				var navEl = $('current-tab-nav');
				if ( navEl )
					navEl.show();
			}
			if ( !this.ID )
				this.grabImageData(0);
			var div = $('upload-file');
			if ( div )
				div.remove();
			return false;
		},

		sendToEditor: function(id) {
			this.grabImageData(id);
			var link = '';
			var display = '';
			var h = '';

			link = $A(document.forms.uploadoptions.elements.link).detect( function(i) { return i.checked; } ).value;
			displayEl = $A(document.forms.uploadoptions.elements.display).detect( function(i) { return i.checked; } )
			if ( displayEl )
				display = displayEl.value;
			else if ( this.currentImage.isImage )
				display = 'full';

			if ( 'none' != link )
				h += "<a href='" + ( 'file' == link ? ( this.currentImage.srcBase + this.currentImage.src ) : ( this.currentImage.page + "' rel='attachment wp-att-" + this.currentImage.ID ) ) + "' title='" + this.currentImage.title + "'>";
			if ( display )
				h += "<img src='" + ( 'thumb' == display ? ( this.currentImage.thumbBase + this.currentImage.thumb ) : ( this.currentImage.srcBase + this.currentImage.src ) ) + "' alt='" + this.currentImage.title + "' />";
			else
				h += this.currentImage.title;
			if ( 'none' != link )
				h += "</a>";

			var win = window.opener ? window.opener : window.dialogArguments;
			if ( !win )
				win = top;
			tinyMCE = win.tinyMCE;
			if ( typeof tinyMCE != 'undefined' && tinyMCE.getInstanceById('content') ) {
				tinyMCE.selectedInstance.getWin().focus();
				tinyMCE.execCommand('mceInsertContent', false, h);
			} else
				win.edInsertContent(win.edCanvas, h);
			if ( !this.ID )
				this.cancelView();
			return false;
		},

		deleteFile: function(id) {
			if ( confirm("<?php printf(js_escape(__("Are you sure you want to delete the file '%s'?\nClick ok to delete or cancel to go back.")), '" + this.currentImage.title + "'); ?>") ) {
				$('action-value').value = 'delete';
				$('upload-file').submit();
				return true;
			}
			return false;
		}
			
	};
	theFileList.initializeVars();
	theFileList.initializeLinks();
} );
