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
			if ( thumbEl )
				this.currentImage.thumb = ( 0 == id ? '' : thumbEl.value );
			else
				this.currentImage.thumb = false;
			this.currentImage.src = ( 0 == id ? '' : $('attachment-url-' + id).value );
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
			h += "<div id='file-title'>"
			if ( !this.currentImage.thumb )
				h += "<h2><a href='" + this.currentImage.src + "' title='Direct link to file'>" + this.currentImage.title + "</a></h2>";
			else
				h += "<h2>" + this.currentImage.title + "</h2>";
			h += "<span>[&nbsp;";
			h += "<a href='#' onclick='theFileList.editView(" + id + ")'>edit</a>"
			h += '&nbsp;|&nbsp;';
			if ( this.ID ) {
				var params = $H(this.params);
				params.ID = '';
				params.action = '';
				h += "<a href='" + this.urlData[0] + '?' + params.toQueryString() + "'  title='Browse your files'>cancel</a>";
			} else {
				h += "<a href='#' onclick='theFileList.cancelView()'  title='Browse your files'>cancel</a>";
			}
			h += "&nbsp;]</span>";
			h += '</div>'
			h += "<div id='upload-file-view' class='alignleft'>";
			if ( this.currentImage.thumb )
				h += "<a href='" + this.currentImage.src + "' title='Direct link to file'><img src='" + this.currentImage.thumb + "' alt='" + this.currentImage.title + "' width='" + this.currentImage.width + "' height='" + this.currentImage.height + "' /></a>";
			else
				h += '&nbsp;';
			h += "</div>";

			h += "<form name='uploadoptions' id='uploadoptions' class='alignleft'>";
			h += "<table>";
			if ( this.currentImage.thumb ) {
				h += "<tr><th style='padding-bottom:.5em'>Show</th><td style='padding-bottom:.5em'>";
				h += "<label for='display-thumb'><input type='radio' name='display' id='display-thumb' value='thumb' checked='checked' /> thumbnail</label><br />";
				h += "<label for='display-full'><input type='radio' name='display' id='display-full' value='full' /> full size</label>";
				h += "</td></tr>";
			}

			h += "<tr><th>Link</th><td>";
			h += "<label for='link-file'><input type='radio' name='link' id='link-file' value='file' checked='checked'/> file</label><br />";
			h += "<label for='link-page'><input type='radio' name='link' id='link-page' value='page' /> page</label><br />";
			h += "<label for='link-none'><input type='radio' name='link' id='link-none' value='none' /> none</label>";
			h += "</td></tr>";

			h += "<tr><td colspan='2'>";
			h += "<input type='button' class='button' name='send' onclick='theFileList.sendToEditor(" + id + ")' value='Send to editor &raquo;' />";
			h += "</td></tr></table>";
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
			h += "<div id='file-title'>"
			if ( !this.currentImage.thumb )
				h += "<h2><a href='" + this.currentImage.src + "' title='Direct link to file'>" + this.currentImage.title + "</a></h2>";
			else
				h += "<h2>" + this.currentImage.title + "</h2>";
			h += "<span>[&nbsp;";
			h += "<a href='#' onclick='theFileList.imageView(" + id + ")'>options</a>"
			h += '&nbsp;|&nbsp;';
			if ( this.ID ) {
				var params = $H(this.params);
				params.ID = '';
				params.action = '';
				h += "<a href='" + this.urlData[0] + '?' + params.toQueryString() + "'  title='Browse your files'>cancel</a>";
			} else {
				h += "<a href='#' onclick='theFileList.cancelView()'  title='Browse your files'>cancel</a>";
			}
			h += "&nbsp;]</span>";
			h += '</div>'
			h += "<div id='upload-file-view' class='alignleft'>";
			if ( this.currentImage.thumb )
				h += "<a href='" + this.currentImage.src + "' title='Direct link to file'><img src='" + this.currentImage.thumb + "' alt='" + this.currentImage.title + "' width='" + this.currentImage.width + "' height='" + this.currentImage.height + "' /></a>";
			else
				h += '&nbsp;';
			h += "</div>";


			h += "<table><tr>"
			h += "<th scope='row'><label for='post_title'>Title:</label></th>";
			h += "<td><input type='text' id='post_title' name='post_title' value='" + this.currentImage.title + "' /></td>";
			h += "</tr><tr>";
			h += "<th scope='row'><label for='post_content'>Description:</label></th>";
			h += "<td><textarea name='post_content' id='post_content'>" + this.currentImage.description + "</textarea></td>";
			h += "</tr><tr id='buttons'><th></th><td>";
			h += "<input type='hidden' name='from_tab' value='" + this.tab + "' />";
			h += "<input type='hidden' name='action' id='action-value' value='save' />";
			h += "<input type='hidden' name='ID' value='" + id + "' />";
			h += "<input type='hidden' name='_wpnonce' value='" + this.nonce + "' />";
			h += "<div class='submit'><input type='submit' value='Save' />";
			h += "<input type='button' name='delete' class='delete' value='Delete' onclick='theFileList.deleteFile(" + id + ");' />";
			h += "</div></td></tr></table></form>";

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

			if ( 'none' != link )
				h += "<a href='" + ( 'file' == link ? this.currentImage.src : this.currentImage.page ) + "' title='" + this.currentImage.title + "'>";
			if ( display )
				h += "<img src='" + ( 'thumb' == display ? this.currentImage.thumb : this.currentImage.src ) + "' alt='" + this.currentImage.title + "' />";
			else
				h += this.currentImage.title;
			if ( 'none' != link )
				h += "</a>";

			var win = window.opener ? window.opener : window.dialogArguments;
			if ( !win )
				win = top;
			tinyMCE = win.tinyMCE;
			if ( typeof tinyMCE != 'undefined' && tinyMCE.getInstanceById('content') )
				win.tinyMCE.execCommand('mceInsertContent', false, h);
			else
				win.edInsertContent(win.edCanvas, h);
			this.cancelView();
			return false;
		},

		deleteFile: function(id) {
			if ( confirm("Are you sure you want to delete the file '" + this.currentImage.title + "'?\nClick ok to delete or cancel to go back.") ) {
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
