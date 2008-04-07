jQuery(function($) {
	$('.noscript-action').remove();

	var increment = 1;

	// Open or close widget control form
	var toggleWidget = function( li, disableFields ) {
		var width = li.find('input.widget-width').val();

		// it seems IE chokes on these animations because of the positioning/floating
		var widgetAnim = $.browser.msie ? function() {
			var t = $(this);
			if ( t.is(':visible') ) {
				if ( disableFields ) { t.find( ':input:enabled' ).not( '[name="widget-id[]"], [name*="[submit]"]' ).attr( 'disabled', 'disabled' ); }
				li.css( 'marginLeft', 0 );
				t.siblings('h4').children('a').text( widgetsL10n.edit );
			} else {
				t.find( ':disabled' ).attr( 'disabled', '' ); // always enable on open
				if ( width > 250 )
					li.css( 'marginLeft', ( width - 250 ) * -1 );
				t.siblings('h4').children('a').text( widgetsL10n.cancel );
			}
			t.toggle();
		} : function() {
			var t = $(this);

			if ( t.is(':visible') ) {
				if ( disableFields ) { t.find( ':input:enabled' ).not( '[name="widget-id[]"], [name*="[submit]"]' ).attr( 'disabled', 'disabled' ); }
				if ( width > 250 )
					li.animate( { marginLeft: 0 } );
				t.siblings('h4').children('a').text( widgetsL10n.edit );
			} else {
				t.find( ':disabled' ).attr( 'disabled', '' ); // always enable on open
				if ( width > 250 )
					li.animate( { marginLeft: ( width - 250 ) * -1 } );
				t.siblings('h4').children('a').text( widgetsL10n.cancel );
			}
			t.animate( { height: 'toggle' } );
		};

		return li.children('div.widget-control').each( widgetAnim ).end();
	};

	// onclick for edit/cancel links
	var editClick = function() {
		var q = wpAjax.unserialize( this.href );
		// if link is in available widgets list, make sure it points to the current sidebar
		if ( ( q.sidebar && q.sidebar == $('#sidebar').val() ) || q.add ) {
			var w = q.edit || q.add;
			toggleWidget( $('#current-sidebar .widget-control-list input[@name^="widget-id"][@value=' + w + ']').parents('li:first'), false ).blur();
			return false;
		} else if ( q.sidebar ) { // otherwise, redirect to correct page
			return true;
		}

		// If link is in current widgets list, just open the form
		toggleWidget( $(this).parents('li:first'), true ).blur();
		return false;
	};

	// onclick for add links
	var addClick = function() {
		var oldLi = $(this).parents('li:first').find('ul.widget-control-info li');
		var newLi = oldLi.clone();

		if ( newLi.html().match( /%i%/ ) ) {
			// supplid form is a template, replace %i% by unique id
			var i = $('#generated-time').val() + increment.toString();
			increment++;
			newLi.html( newLi.html().replace( /%i%/g, i ) );
		} else {
			$(this).text( widgetsL10n.edit ).unbind().click( editClick );
			// save form content in textarea so we don't have any conflicting HTML ids
			oldLi.html( '<textarea>' + oldLi.html() + '</textarea>' );
		}

		// add event handlers
		addWidgetControls( newLi );

		// add widget to sidebar sortable
		widgetSortable.append( newLi ).SortableAddItem( newLi[0] );

		// increment widget counter
		var n = parseInt( $('#widget-count').text(), 10 ) + 1;
		$('#widget-count').text( n.toString() )

		return false;
	};

	// add event handlers to all links found in context
	var addWidgetControls = function( context ) {
		if ( !context )
			context = document;

		$('a.widget-control-edit', context).click( editClick );

		// onclick for save links
		$('a.widget-control-save', context).click( function() {
			toggleWidget( $(this).parents('li:first'), false ).blur()
			return false;
		} );

		// onclick for remove links
		$('a.widget-control-remove', context).click( function() {
			var w = $(this).parents('li:first').find('input[@name^="widget-id"]').val();
			$(this).parents('li:first').remove();
			var t = $('#widget-list ul#widget-control-info-' + w + ' textarea');
			t.parent().html( t.text() ).parents('li.widget-list-item:first').children( 'h4' ).children('a.widget-action')
				.show().text( widgetsL10n.add ).unbind().click( addClick );
			var n = parseInt( $('#widget-count').text(), 10 ) - 1;
			$('#widget-count').text( n.toString() )
			return false;
		} );
	}

	addWidgetControls();

	$('a.widget-control-add').click( addClick );

	var widgetSortable;
	var widgetSortableInit = function() {
		try { // a hack to make sortables work in jQuery 1.2+ and IE7
			$('#current-sidebar .widget-control-list').SortableDestroy();
		} catch(e) {}
		widgetSortable = $('#current-sidebar .widget-control-list').Sortable( {
			accept: 'widget-sortable',
			helperclass: 'sorthelper',
			handle: 'h4.widget-title',
			onStop: widgetSortableInit
		} );
	}

	// initialize sortable
	widgetSortableInit();

});
